using ERP.Application.Interfaces;
using ERP.Domain.Entities.Financiero;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Tesoreria.Caja;

// ─── DTOS ──────────────────────────────────────────────────────────────────
public class CajaMovimientoDto
{
    public int Id { get; set; }
    public string Tipo { get; set; } = string.Empty;
    public string Concepto { get; set; } = string.Empty;
    public decimal Monto { get; set; }
    public DateTime Fecha { get; set; }
    public string? UsuarioCajero { get; set; }
    public string? Referencia { get; set; }
    public int? PagoId { get; set; }
}

// ─── QUERIES ────────────────────────────────────────────────────────────────
public record GetCajaMovimientosQuery : IRequest<List<CajaMovimientoDto>>;

public class GetCajaMovimientosQueryHandler(IApplicationDbContext context) : IRequestHandler<GetCajaMovimientosQuery, List<CajaMovimientoDto>>
{
    public async Task<List<CajaMovimientoDto>> Handle(GetCajaMovimientosQuery request, CancellationToken cancellationToken)
    {
        return await context.CajaMovimientos
            .OrderByDescending(m => m.Fecha)
            .Select(m => new CajaMovimientoDto
            {
                Id = m.Id,
                Tipo = m.Tipo,
                Concepto = m.Concepto,
                Monto = m.Monto,
                Fecha = m.Fecha,
                UsuarioCajero = m.UsuarioCajero,
                Referencia = m.Referencia,
                PagoId = m.PagoId
            })
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ───────────────────────────────────────────────────────────────
public class RegistrarMovimientoCajaCommand : IRequest<int>
{
    public string Tipo { get; set; } = "Ingreso"; // Ingreso / Egreso
    public string Concepto { get; set; } = string.Empty;
    public decimal Monto { get; set; }
    public string? Referencia { get; set; }
    public string? UsuarioCajero { get; set; }
}

public class RegistrarMovimientoCajaCommandHandler(IApplicationDbContext context) : IRequestHandler<RegistrarMovimientoCajaCommand, int>
{
    public async Task<int> Handle(RegistrarMovimientoCajaCommand request, CancellationToken cancellationToken)
    {
        var entity = new CajaMovimiento
        {
            Tipo = request.Tipo,
            Concepto = request.Concepto,
            Monto = request.Monto,
            Fecha = DateTime.Now,
            Referencia = request.Referencia,
            UsuarioCajero = request.UsuarioCajero
        };

        context.CajaMovimientos.Add(entity);
        await context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}
