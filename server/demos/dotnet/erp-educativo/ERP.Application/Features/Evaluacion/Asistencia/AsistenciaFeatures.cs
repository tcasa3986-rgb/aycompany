using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Evaluacion.Asistencia;

// ─── DTOs ──────────────────────────────────────────────────────────────────

public class SeccionDto
{
    public int Id { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public string CursoNombre { get; set; } = string.Empty;
    public string PeriodoNombre { get; set; } = string.Empty;
    public int Matriculados { get; set; }
}

public class AsistenciaDto
{
    public int EstudianteId { get; set; }
    public string EstudianteNombre { get; set; } = string.Empty;
    public string EstudianteCodigo { get; set; } = string.Empty;
    public EstadoAsistencia Estado { get; set; } = EstadoAsistencia.Presente;
    public string? Observacion { get; set; }
}

// ─── QUERIES ───────────────────────────────────────────────────────────────

public class GetSeccionesActivasQuery : IRequest<List<SeccionDto>> { }

public class GetSeccionesActivasQueryHandler : IRequestHandler<GetSeccionesActivasQuery, List<SeccionDto>>
{
    private readonly IApplicationDbContext _context;

    public GetSeccionesActivasQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<SeccionDto>> Handle(GetSeccionesActivasQuery request, CancellationToken cancellationToken)
    {
        return await _context.Secciones
            .Include(s => s.Curso)
            .Include(s => s.PeriodoAcademico)
            .Select(s => new SeccionDto
            {
                Id = s.Id,
                Nombre = s.Nombre,
                CursoNombre = s.Curso!.Nombre,
                PeriodoNombre = s.PeriodoAcademico!.Nombre,
                Matriculados = _context.DetallesMatricula.Count(d => d.SeccionId == s.Id && d.Matricula!.Estado == EstadoMatricula.Confirmada)
            })
            .OrderBy(s => s.CursoNombre).ThenBy(s => s.Nombre)
            .ToListAsync(cancellationToken);
    }
}

public class GetAsistenciaPorSeccionYFechaQuery : IRequest<List<AsistenciaDto>>
{
    public int SeccionId { get; set; }
    public DateTime Fecha { get; set; }
}

public class GetAsistenciaPorSeccionYFechaQueryHandler : IRequestHandler<GetAsistenciaPorSeccionYFechaQuery, List<AsistenciaDto>>
{
    private readonly IApplicationDbContext _context;

    public GetAsistenciaPorSeccionYFechaQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<AsistenciaDto>> Handle(GetAsistenciaPorSeccionYFechaQuery request, CancellationToken cancellationToken)
    {
        // Obtener todos los estudiantes matriculados en la sección
        var estudiantesMatriculados = await _context.DetallesMatricula
            .Include(d => d.Matricula)
            .ThenInclude(m => m!.Estudiante)
            .Where(d => d.SeccionId == request.SeccionId && d.Matricula!.Estado == EstadoMatricula.Confirmada)
            .Select(d => d.Matricula!.Estudiante!)
            .ToListAsync(cancellationToken);

        // Obtener asistencias ya registradas para esta fecha
        var asistenciasRegistradas = await _context.Asistencias
            .Where(a => a.SeccionId == request.SeccionId && a.FechaClase.Date == request.Fecha.Date)
            .ToDictionaryAsync(a => a.EstudianteId, a => a, cancellationToken);

        var resultado = new List<AsistenciaDto>();

        foreach (var estudiante in estudiantesMatriculados)
        {
            var dto = new AsistenciaDto
            {
                EstudianteId = estudiante.Id,
                EstudianteNombre = $"{estudiante.ApellidoPaterno} {estudiante.ApellidoMaterno}, {estudiante.Nombres}",
                EstudianteCodigo = estudiante.Codigo
            };

            if (asistenciasRegistradas.TryGetValue(estudiante.Id, out var asistenciaExistente))
            {
                dto.Estado = asistenciaExistente.Estado;
                dto.Observacion = asistenciaExistente.Observacion;
            }
            else
            {
                // Valor por defecto si no se ha tomado asistencia
                dto.Estado = EstadoAsistencia.Presente;
            }

            resultado.Add(dto);
        }

        return resultado.OrderBy(r => r.EstudianteNombre).ToList();
    }
}

// ─── COMMANDS ──────────────────────────────────────────────────────────────

public class SaveAsistenciaBatchCommand : IRequest<bool>
{
    public int SeccionId { get; set; }
    public DateTime Fecha { get; set; }
    public List<AsistenciaDto> Asistencias { get; set; } = new();
}

public class SaveAsistenciaBatchCommandHandler : IRequestHandler<SaveAsistenciaBatchCommand, bool>
{
    private readonly IApplicationDbContext _context;

    public SaveAsistenciaBatchCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<bool> Handle(SaveAsistenciaBatchCommand request, CancellationToken cancellationToken)
    {
        var asistenciasExistentes = await _context.Asistencias
            .Where(a => a.SeccionId == request.SeccionId && a.FechaClase.Date == request.Fecha.Date)
            .ToListAsync(cancellationToken);

        foreach (var dto in request.Asistencias)
        {
            var existente = asistenciasExistentes.FirstOrDefault(a => a.EstudianteId == dto.EstudianteId);

            if (existente != null)
            {
                existente.Estado = dto.Estado;
                existente.Observacion = dto.Observacion;
            }
            else
            {
                _context.Asistencias.Add(new ERP.Domain.Entities.Academico.Asistencia
                {
                    SeccionId = request.SeccionId,
                    EstudianteId = dto.EstudianteId,
                    FechaClase = request.Fecha.Date,
                    Estado = dto.Estado,
                    Observacion = dto.Observacion
                });
            }
        }

        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
