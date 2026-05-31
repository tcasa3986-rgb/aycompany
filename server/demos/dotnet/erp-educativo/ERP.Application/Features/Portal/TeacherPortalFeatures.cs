using ERP.Application.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Portal;

public class TeacherDashboardDto
{
    public string DocenteNombre { get; set; } = string.Empty;
    public string Especialidad { get; set; } = string.Empty;
    public List<SeccionAsignadaDto> SeccionesAsignadas { get; set; } = new();
    public int TotalEstudiantes { get; set; }
    public int EvaluacionesPendientes { get; set; }
}

public class SeccionAsignadaDto
{
    public int Id { get; set; }
    public string CursoNombre { get; set; } = string.Empty;
    public string SeccionNombre { get; set; } = string.Empty;
    public int AlumnosContados { get; set; }
    public string Horario { get; set; } = string.Empty;
}

public record GetTeacherDashboardQuery(string UserEmail) : IRequest<TeacherDashboardDto>;

public class GetTeacherDashboardQueryHandler(IApplicationDbContext context) : IRequestHandler<GetTeacherDashboardQuery, TeacherDashboardDto>
{
    public async Task<TeacherDashboardDto> Handle(GetTeacherDashboardQuery request, CancellationToken cancellationToken)
    {
        var docente = await context.Docentes
            .FirstOrDefaultAsync(d => d.Email == request.UserEmail, cancellationToken);

        if (docente == null) return new TeacherDashboardDto();

        var secciones = await context.Secciones
            .Include(s => s.Curso)
            .Include(s => s.Horarios)
            .Where(s => s.Asignaciones.Any(a => a.DocenteId == docente.Id))
            .ToListAsync(cancellationToken);

        var dto = new TeacherDashboardDto
        {
            DocenteNombre = docente.NombreCompleto,
            Especialidad = docente.Especialidad ?? "General"
        };

        foreach (var s in secciones)
        {
            var count = await context.DetallesMatricula.CountAsync(d => d.SeccionId == s.Id, cancellationToken);
            dto.TotalEstudiantes += count;
            
            var schedule = s.Horarios.FirstOrDefault();
            
            dto.SeccionesAsignadas.Add(new SeccionAsignadaDto
            {
                Id = s.Id,
                CursoNombre = s.Curso?.Nombre ?? "N/A",
                SeccionNombre = s.Nombre,
                AlumnosContados = count,
                Horario = schedule != null ? $"{schedule.DiaSemana}: {schedule.HoraInicio:hh\\:mm} - {schedule.HoraFin:hh\\:mm}" : "Sin horario"
            });
        }

        // Evaluaciones pendientes (simulado: evaluaciones sin notas para sus secciones)
        dto.EvaluacionesPendientes = await context.Evaluaciones
            .Where(e => secciones.Select(s => s.Id).Contains(e.SeccionId))
            .CountAsync(e => !context.Notas.Any(n => n.EvaluacionId == e.Id), cancellationToken);

        return dto;
    }
}
