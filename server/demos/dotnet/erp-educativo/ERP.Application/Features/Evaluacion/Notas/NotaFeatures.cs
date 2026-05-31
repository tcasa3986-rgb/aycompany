using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Evaluacion.Notas;

// ─── DTOs ──────────────────────────────────────────────────────────────────

public class NotaDto
{
    public int EstudianteId { get; set; }
    public string EstudianteNombre { get; set; } = string.Empty;
    public string EstudianteCodigo { get; set; } = string.Empty;
    public decimal? Calificacion { get; set; }
    public string? Observacion { get; set; }
    public bool Ausente { get; set; }
}

// ─── QUERIES ───────────────────────────────────────────────────────────────

public class GetNotasPorEvaluacionQuery : IRequest<List<NotaDto>>
{
    public int EvaluacionId { get; set; }
}

public class GetNotasPorEvaluacionQueryHandler : IRequestHandler<GetNotasPorEvaluacionQuery, List<NotaDto>>
{
    private readonly IApplicationDbContext _context;

    public GetNotasPorEvaluacionQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<NotaDto>> Handle(GetNotasPorEvaluacionQuery request, CancellationToken cancellationToken)
    {
        var evaluacion = await _context.Evaluaciones.FindAsync(new object[] { request.EvaluacionId }, cancellationToken)
            ?? throw new Exception("Evaluación no encontrada.");

        // Obtenemos los alumnos de la sección
        var estudiantesMatriculados = await _context.DetallesMatricula
            .Include(d => d.Matricula)
            .ThenInclude(m => m!.Estudiante)
            .Where(d => d.SeccionId == evaluacion.SeccionId && d.Matricula!.Estado == ERP.Domain.Enums.EstadoMatricula.Confirmada)
            .Select(d => d.Matricula!.Estudiante!)
            .ToListAsync(cancellationToken);

        // Obtenemos las notas ya registradas
        var notasRegistradas = await _context.Notas
            .Where(n => n.EvaluacionId == request.EvaluacionId)
            .ToDictionaryAsync(n => n.EstudianteId, n => n, cancellationToken);

        var resultado = new List<NotaDto>();

        foreach (var estudiante in estudiantesMatriculados)
        {
            var dto = new NotaDto
            {
                EstudianteId = estudiante.Id,
                EstudianteCodigo = estudiante.Codigo,
                EstudianteNombre = $"{estudiante.ApellidoPaterno} {estudiante.ApellidoMaterno}, {estudiante.Nombres}"
            };

            if (notasRegistradas.TryGetValue(estudiante.Id, out var notaExistente))
            {
                dto.Calificacion = notaExistente.Calificacion;
                dto.Observacion = notaExistente.Observacion;
                dto.Ausente = notaExistente.Ausente;
            }

            resultado.Add(dto);
        }

        return resultado.OrderBy(r => r.EstudianteNombre).ToList();
    }
}

// ─── COMMANDS ──────────────────────────────────────────────────────────────

public class SaveNotasBatchCommand : IRequest<bool>
{
    public int EvaluacionId { get; set; }
    public List<NotaDto> Notas { get; set; } = new();
}

public class SaveNotasBatchCommandHandler : IRequestHandler<SaveNotasBatchCommand, bool>
{
    private readonly IApplicationDbContext _context;

    public SaveNotasBatchCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<bool> Handle(SaveNotasBatchCommand request, CancellationToken cancellationToken)
    {
        var evaluacion = await _context.Evaluaciones.FindAsync(new object[] { request.EvaluacionId }, cancellationToken)
            ?? throw new Exception("Evaluación no encontrada.");

        var notasExistentes = await _context.Notas
            .Where(n => n.EvaluacionId == request.EvaluacionId)
            .ToListAsync(cancellationToken);

        foreach (var dto in request.Notas)
        {
            if (dto.Calificacion > evaluacion.NotaMaxima)
            {
                throw new Exception($"La calificación del alumno {dto.EstudianteNombre} supera la nota máxima permitida ({evaluacion.NotaMaxima}).");
            }

            var existente = notasExistentes.FirstOrDefault(n => n.EstudianteId == dto.EstudianteId);

            if (existente != null)
            {
                existente.Calificacion = dto.Calificacion;
                existente.Ausente = dto.Ausente;
                existente.Observacion = dto.Observacion;
            }
            else
            {
                // Solo insertamos si el profesor le puso alguna nota o lo marcó ausente o puso observación.
                // Si todo está vacío, no creamos un registro innecesario en BD.
                if (dto.Calificacion.HasValue || dto.Ausente || !string.IsNullOrWhiteSpace(dto.Observacion))
                {
                    _context.Notas.Add(new Nota
                    {
                        EvaluacionId = request.EvaluacionId,
                        EstudianteId = dto.EstudianteId,
                        Calificacion = dto.Calificacion,
                        Ausente = dto.Ausente,
                        Observacion = dto.Observacion
                    });
                }
            }
        }

        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
