using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Evaluacion.Evaluaciones;

// ─── DTOs ──────────────────────────────────────────────────────────────────

public class EvaluacionDto
{
    public int Id { get; set; }
    public int SeccionId { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public TipoEvaluacion Tipo { get; set; }
    public decimal PesoPromedio { get; set; }
    public DateTime FechaProgramada { get; set; }
    public decimal NotaMaxima { get; set; }
    public bool EsSubsanacion { get; set; }
    public int NotasRegistradas { get; set; }
}

// ─── QUERIES ───────────────────────────────────────────────────────────────

public class GetEvaluacionesPorSeccionQuery : IRequest<List<EvaluacionDto>>
{
    public int SeccionId { get; set; }
}

public class GetEvaluacionesPorSeccionQueryHandler : IRequestHandler<GetEvaluacionesPorSeccionQuery, List<EvaluacionDto>>
{
    private readonly IApplicationDbContext _context;

    public GetEvaluacionesPorSeccionQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<EvaluacionDto>> Handle(GetEvaluacionesPorSeccionQuery request, CancellationToken cancellationToken)
    {
        return await _context.Evaluaciones
            .Where(e => e.SeccionId == request.SeccionId)
            .Select(e => new EvaluacionDto
            {
                Id = e.Id,
                SeccionId = e.SeccionId,
                Nombre = e.Nombre,
                Tipo = e.Tipo,
                PesoPromedio = e.PesoPromedio,
                FechaProgramada = e.FechaProgramada,
                NotaMaxima = e.NotaMaxima,
                EsSubsanacion = e.EsSubsanacion,
                NotasRegistradas = _context.Notas.Count(n => n.EvaluacionId == e.Id && n.Calificacion.HasValue)
            })
            .OrderBy(e => e.FechaProgramada)
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ──────────────────────────────────────────────────────────────

public class UpsertEvaluacionCommand : IRequest<int>
{
    public int Id { get; set; }
    public int SeccionId { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public TipoEvaluacion Tipo { get; set; }
    public decimal PesoPromedio { get; set; }
    public DateTime? FechaProgramada { get; set; }
    public decimal NotaMaxima { get; set; } = 20;
}

public class UpsertEvaluacionCommandHandler : IRequestHandler<UpsertEvaluacionCommand, int>
{
    private readonly IApplicationDbContext _context;

    public UpsertEvaluacionCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<int> Handle(UpsertEvaluacionCommand request, CancellationToken cancellationToken)
    {
        ERP.Domain.Entities.Academico.Evaluacion entity;

        if (request.Id > 0)
        {
            entity = await _context.Evaluaciones.FindAsync(new object[] { request.Id }, cancellationToken)
                ?? throw new Exception("Evaluación no encontrada.");
        }
        else
        {
            entity = new ERP.Domain.Entities.Academico.Evaluacion { SeccionId = request.SeccionId };
            _context.Evaluaciones.Add(entity);
        }

        entity.Nombre = request.Nombre;
        entity.Tipo = request.Tipo;
        entity.PesoPromedio = request.PesoPromedio;
        entity.FechaProgramada = request.FechaProgramada ?? DateTime.Today;
        entity.NotaMaxima = request.NotaMaxima;

        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteEvaluacionCommand : IRequest<bool>
{
    public int Id { get; set; }
}

public class DeleteEvaluacionCommandHandler : IRequestHandler<DeleteEvaluacionCommand, bool>
{
    private readonly IApplicationDbContext _context;

    public DeleteEvaluacionCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<bool> Handle(DeleteEvaluacionCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Evaluaciones.FindAsync(new object[] { request.Id }, cancellationToken);
        if (entity == null) return false;

        var tieneNotas = await _context.Notas.AnyAsync(n => n.EvaluacionId == request.Id, cancellationToken);
        if (tieneNotas) throw new Exception("No se puede eliminar la evaluación porque ya tiene notas registradas.");

        _context.Evaluaciones.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
