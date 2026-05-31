using ERP.Application.Interfaces;
using ERP.Domain.Entities.Financiero;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Tesoreria.ConceptosPago;

public class ConceptoPagoDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public decimal MontoBase { get; set; }
    public bool AplicaMora { get; set; }
    public decimal? PorcentajeMora { get; set; }
    public int? DiasGraciaMora { get; set; }
    public bool EsRecurrente { get; set; }
    public int TotalDeudas { get; set; }
}

// ── GET ALL ───────────────────────────────────────────────────────────────

public class GetConceptosPagoQuery : IRequest<List<ConceptoPagoDto>> { }
public class GetConceptosPagoQueryHandler : IRequestHandler<GetConceptosPagoQuery, List<ConceptoPagoDto>>
{
    private readonly IApplicationDbContext _context;
    public GetConceptosPagoQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<ConceptoPagoDto>> Handle(GetConceptosPagoQuery request, CancellationToken cancellationToken)
        => await _context.ConceptosPago
            .Select(c => new ConceptoPagoDto
            {
                Id = c.Id, Codigo = c.Codigo, Nombre = c.Nombre,
                Descripcion = c.Descripcion, MontoBase = c.MontoBase,
                AplicaMora = c.AplicaMora, PorcentajeMora = c.PorcentajeMora,
                DiasGraciaMora = c.DiasGraciaMora, EsRecurrente = c.EsRecurrente,
                TotalDeudas = c.Deudas.Count
            })
            .OrderBy(c => c.Nombre)
            .ToListAsync(cancellationToken);
}

// ── UPSERT ────────────────────────────────────────────────────────────────

public class UpsertConceptoPagoCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public decimal MontoBase { get; set; }
    public bool AplicaMora { get; set; }
    public decimal? PorcentajeMora { get; set; }
    public int? DiasGraciaMora { get; set; }
    public bool EsRecurrente { get; set; }
}
public class UpsertConceptoPagoCommandHandler : IRequestHandler<UpsertConceptoPagoCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertConceptoPagoCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<int> Handle(UpsertConceptoPagoCommand request, CancellationToken cancellationToken)
    {
        ConceptoPago entity;
        if (request.Id > 0)
            entity = await _context.ConceptosPago.FindAsync([request.Id], cancellationToken)
                     ?? throw new Exception("Concepto de pago no encontrado.");
        else { entity = new ConceptoPago(); _context.ConceptosPago.Add(entity); }

        entity.Codigo          = request.Codigo;
        entity.Nombre          = request.Nombre;
        entity.Descripcion     = request.Descripcion;
        entity.MontoBase       = request.MontoBase;
        entity.AplicaMora      = request.AplicaMora;
        entity.PorcentajeMora  = request.PorcentajeMora;
        entity.DiasGraciaMora  = request.DiasGraciaMora;
        entity.EsRecurrente    = request.EsRecurrente;

        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

// ── DELETE ────────────────────────────────────────────────────────────────

public class DeleteConceptoPagoCommand : IRequest<bool> { public int Id { get; set; } }
public class DeleteConceptoPagoCommandHandler : IRequestHandler<DeleteConceptoPagoCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteConceptoPagoCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<bool> Handle(DeleteConceptoPagoCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.ConceptosPago.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.ConceptosPago.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
