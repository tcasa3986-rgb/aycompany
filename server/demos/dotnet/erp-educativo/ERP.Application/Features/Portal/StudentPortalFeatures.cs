using ERP.Application.Interfaces;
using ERP.Application.Features.Evaluacion.Notas;
using ERP.Application.Features.Tesoreria;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Portal;

public class StudentDashboardDto
{
    public string EstudianteNombre { get; set; } = string.Empty;
    public string Carrera { get; set; } = string.Empty;
    public int Ciclo { get; set; }
    public decimal PromedioPonderado { get; set; }
    public List<CursoStatusDto> CursosActuales { get; set; } = new();
    public List<DeudaDto> DeudasPendientes { get; set; } = new();
}

public class DeudaDto
{
    public int Id { get; set; }
    public string NumeroDeuda { get; set; } = string.Empty;
    public string ConceptoNombre { get; set; } = string.Empty;
    public decimal MontoTotal { get; set; }
    public DateTime FechaVencimiento { get; set; }
    public EstadoPago Estado { get; set; }
}

public class CursoStatusDto
{
    public string CursoNombre { get; set; } = string.Empty;
    public decimal PromedioActual { get; set; }
    public decimal PorcentajeAsistencia { get; set; }
}

public record GetStudentDashboardQuery(string UserEmail) : IRequest<StudentDashboardDto>;

public class GetStudentDashboardQueryHandler(IApplicationDbContext context) : IRequestHandler<GetStudentDashboardQuery, StudentDashboardDto>
{
    public async Task<StudentDashboardDto> Handle(GetStudentDashboardQuery request, CancellationToken cancellationToken)
    {
        var estudiante = await context.Estudiantes
            .FirstOrDefaultAsync(e => e.Email == request.UserEmail, cancellationToken);

        if (estudiante == null) return new StudentDashboardDto();

        var matricula = await context.Matriculas
            .Include(m => m.Carrera)
            .OrderByDescending(m => m.FechaMatricula)
            .FirstOrDefaultAsync(m => m.EstudianteId == estudiante.Id, cancellationToken);

        var dto = new StudentDashboardDto
        {
            EstudianteNombre = estudiante.NombreCompleto,
            Carrera = matricula?.Carrera?.Nombre ?? "No Matriculado",
            Ciclo = matricula?.Ciclo ?? 0
        };

        if (matricula != null)
        {
            // Cargar cursos actuales desde DetallesMatricula
            var detalles = await context.DetallesMatricula
                .Include(d => d.Seccion)
                    .ThenInclude(s => s.Curso)
                .Where(d => d.MatriculaId == matricula.Id)
                .ToListAsync(cancellationToken);

            foreach (var d in detalles)
            {
                // Calcular promedio actual (simulado o desde tabla Promedios)
                var promedio = await context.PromediosFinales
                    .FirstOrDefaultAsync(p => p.EstudianteId == estudiante.Id && p.SeccionId == d.SeccionId, cancellationToken);
                
                // Calcular asistencia
                var asistencias = await context.Asistencias
                    .Where(a => a.EstudianteId == estudiante.Id && a.SeccionId == d.SeccionId)
                    .ToListAsync(cancellationToken);
                
                decimal pctAsistencia = asistencias.Any() 
                    ? (decimal)asistencias.Count(a => a.Estado == Domain.Enums.EstadoAsistencia.Presente || a.Estado == Domain.Enums.EstadoAsistencia.Tardanza) / asistencias.Count * 100
                    : 100;

                dto.CursosActuales.Add(new CursoStatusDto
                {
                    CursoNombre = d.Seccion?.Curso?.Nombre ?? "N/A",
                    PromedioActual = promedio?.PromedioCalculado ?? 0,
                    PorcentajeAsistencia = pctAsistencia
                });
            }
        }

        // Deudas
        dto.DeudasPendientes = await context.Deudas
            .Include(d => d.ConceptoPago)
            .Where(d => d.EstudianteId == estudiante.Id && d.Estado != Domain.Enums.EstadoPago.Pagado)
            .Select(d => new DeudaDto
            {
                Id = d.Id,
                NumeroDeuda = d.NumeroDeuda,
                ConceptoNombre = d.ConceptoPago != null ? d.ConceptoPago.Nombre : "",
                MontoTotal = d.MontoOriginal + d.MontoMora,
                FechaVencimiento = d.FechaVencimiento,
                Estado = d.Estado
            })
            .ToListAsync(cancellationToken);

        return dto;
    }
}
