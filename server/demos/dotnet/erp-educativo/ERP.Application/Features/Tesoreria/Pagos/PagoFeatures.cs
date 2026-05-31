using ERP.Application.Interfaces;
using ERP.Domain.Entities.Financiero;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Tesoreria.Pagos;

public class DeudaListDto
{
    public int Id { get; set; }
    public string NumeroDeuda { get; set; } = string.Empty;
    public string Estudiante { get; set; } = string.Empty;
    public string Concepto { get; set; } = string.Empty;
    public decimal MontoOriginal { get; set; }
    public decimal MontoMora { get; set; }
    public DateTime FechaVencimiento { get; set; }
    public EstadoPago Estado { get; set; }
}

public class PagoListDto
{
    public int Id { get; set; }
    public string NumeroPago { get; set; } = string.Empty;
    public string Estudiante { get; set; } = string.Empty;
    public string Concepto { get; set; } = string.Empty;
    public decimal MontoPagado { get; set; }
    public DateTime FechaPago { get; set; }
    public TipoPago TipoPago { get; set; }
    public string? NumeroOperacion { get; set; }
    public bool Anulado { get; set; }
}

public class PagoDto
{
    public int Id { get; set; }
    public string NumeroPago { get; set; } = string.Empty;
    public string Estudiante { get; set; } = string.Empty;
    public string EstudianteEmail { get; set; } = string.Empty;
    public DateTime FechaPago { get; set; }
    public string Concepto { get; set; } = string.Empty;
    public string NumeroDeuda { get; set; } = string.Empty;
    public decimal MontoPagado { get; set; }
    public TipoPago TipoPago { get; set; }
    public string? NumeroOperacion { get; set; }
}

// ── QUERIES DEUDAS ────────────────────────────────────────────────────────

public class GetDeudasQuery : IRequest<List<DeudaListDto>> { }
public class GetDeudasQueryHandler : IRequestHandler<GetDeudasQuery, List<DeudaListDto>>
{
    private readonly IApplicationDbContext _context;
    public GetDeudasQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<DeudaListDto>> Handle(GetDeudasQuery request, CancellationToken cancellationToken)
        => await _context.Deudas
            .Include(d => d.Estudiante).Include(d => d.ConceptoPago)
            .OrderByDescending(d => d.FechaVencimiento)
            .Select(d => new DeudaListDto
            {
                Id = d.Id, NumeroDeuda = d.NumeroDeuda,
                Estudiante = d.Estudiante != null ? $"{d.Estudiante.ApellidoPaterno} {d.Estudiante.Nombres}" : "",
                Concepto = d.ConceptoPago != null ? d.ConceptoPago.Nombre : "",
                MontoOriginal = d.MontoOriginal, MontoMora = d.MontoMora,
                FechaVencimiento = d.FechaVencimiento, Estado = d.Estado
            })
            .ToListAsync(cancellationToken);
}

/// <summary>Solo deudas pendientes o vencidas (para el selector al registrar un pago).</summary>
public class GetDeudasPendientesQuery : IRequest<List<DeudaListDto>> { }
public class GetDeudasPendientesQueryHandler : IRequestHandler<GetDeudasPendientesQuery, List<DeudaListDto>>
{
    private readonly IApplicationDbContext _context;
    public GetDeudasPendientesQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<DeudaListDto>> Handle(GetDeudasPendientesQuery request, CancellationToken cancellationToken)
        => await _context.Deudas
            .Include(d => d.Estudiante).Include(d => d.ConceptoPago)
            .Where(d => d.Estado != EstadoPago.Pagado)
            .OrderBy(d => d.FechaVencimiento)
            .Select(d => new DeudaListDto
            {
                Id = d.Id, NumeroDeuda = d.NumeroDeuda,
                Estudiante = d.Estudiante != null ? $"{d.Estudiante.ApellidoPaterno} {d.Estudiante.Nombres}" : "",
                Concepto = d.ConceptoPago != null ? d.ConceptoPago.Nombre : "",
                MontoOriginal = d.MontoOriginal, MontoMora = d.MontoMora,
                FechaVencimiento = d.FechaVencimiento, Estado = d.Estado
            })
            .ToListAsync(cancellationToken);
}

// ── QUERIES PAGOS ─────────────────────────────────────────────────────────

public class GetPagosQuery : IRequest<List<PagoListDto>> { }
public class GetPagosQueryHandler : IRequestHandler<GetPagosQuery, List<PagoListDto>>
{
    private readonly IApplicationDbContext _context;
    public GetPagosQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<PagoListDto>> Handle(GetPagosQuery request, CancellationToken cancellationToken)
        => await _context.Pagos
            .Include(p => p.Deuda).ThenInclude(d => d!.Estudiante)
            .Include(p => p.Deuda).ThenInclude(d => d!.ConceptoPago)
            .OrderByDescending(p => p.FechaPago)
            .Select(p => new PagoListDto
            {
                Id = p.Id, NumeroPago = p.NumeroPago,
                Estudiante = p.Deuda != null && p.Deuda.Estudiante != null
                    ? $"{p.Deuda.Estudiante.ApellidoPaterno} {p.Deuda.Estudiante.Nombres}" : "",
                Concepto = p.Deuda != null && p.Deuda.ConceptoPago != null ? p.Deuda.ConceptoPago.Nombre : "",
                MontoPagado = p.MontoPagado, FechaPago = p.FechaPago,
                TipoPago = p.TipoPago, NumeroOperacion = p.NumeroOperacion, Anulado = p.Anulado
            })
            .ToListAsync(cancellationToken);
}

public class GetPagoByIdQuery : IRequest<PagoDto?> { public int Id { get; set; } }
public class GetPagoByIdQueryHandler : IRequestHandler<GetPagoByIdQuery, PagoDto?>
{
    private readonly IApplicationDbContext _context;
    public GetPagoByIdQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<PagoDto?> Handle(GetPagoByIdQuery request, CancellationToken cancellationToken)
        => await _context.Pagos
            .Include(p => p.Deuda).ThenInclude(d => d!.Estudiante)
            .Include(p => p.Deuda).ThenInclude(d => d!.ConceptoPago)
            .Where(p => p.Id == request.Id)
            .Select(p => new PagoDto
            {
                Id = p.Id,
                NumeroPago = p.NumeroPago,
                Estudiante = p.Deuda != null && p.Deuda.Estudiante != null ? $"{p.Deuda.Estudiante.ApellidoPaterno} {p.Deuda.Estudiante.Nombres}" : "",
                EstudianteEmail = p.Deuda != null && p.Deuda.Estudiante != null ? p.Deuda.Estudiante.Email ?? "" : "",
                FechaPago = p.FechaPago,
                Concepto = p.Deuda != null && p.Deuda.ConceptoPago != null ? p.Deuda.ConceptoPago.Nombre : "",
                NumeroDeuda = p.Deuda != null ? p.Deuda.NumeroDeuda : "",
                MontoPagado = p.MontoPagado,
                TipoPago = p.TipoPago,
                NumeroOperacion = p.NumeroOperacion
            })
            .FirstOrDefaultAsync(cancellationToken);
}

// ── CREAR PAGO ────────────────────────────────────────────────────────────

public class CreatePagoDto
{
    public int DeudaId { get; set; }
    public decimal MontoPagado { get; set; }
    public TipoPago TipoPago { get; set; } = TipoPago.Efectivo;
    public string? NumeroOperacion { get; set; }
    public string? Banco { get; set; }
    public string? Observaciones { get; set; }
}

public class CreatePagoCommand : IRequest<int>
{
    public CreatePagoDto Pago { get; set; } = new();
}

public class CreatePagoCommandHandler : IRequestHandler<CreatePagoCommand, int>
{
    private readonly IApplicationDbContext _context;
    public CreatePagoCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<int> Handle(CreatePagoCommand request, CancellationToken cancellationToken)
    {
        var dto = request.Pago;

        var deuda = await _context.Deudas
            .Include(d => d.Pagos)
            .FirstOrDefaultAsync(d => d.Id == dto.DeudaId, cancellationToken)
            ?? throw new Exception("Deuda no encontrada.");

        if (deuda.Estado == EstadoPago.Pagado)
            throw new Exception("Esta deuda ya se encuentra pagada.");

        if (dto.MontoPagado <= 0)
            throw new Exception("El monto del pago debe ser mayor a cero.");

        // Número correlativo
        var totalPagos = await _context.Pagos.CountAsync(cancellationToken);
        var numeroPago = $"PAG-{DateTime.Now.Year}-{(totalPagos + 1):D5}";

        var pago = new Pago
        {
            NumeroPago   = numeroPago,
            DeudaId      = dto.DeudaId,
            EstudianteId = deuda.EstudianteId,
            MontoPagado  = dto.MontoPagado,
            FechaPago    = DateTime.Now,
            TipoPago     = dto.TipoPago,
            NumeroOperacion = dto.NumeroOperacion,
            Banco        = dto.Banco,
            Observaciones = dto.Observaciones,
            Anulado      = false
        };
        _context.Pagos.Add(pago);

        // ¿La deuda queda saldada?
        var totalPagado = deuda.Pagos.Where(p => !p.Anulado).Sum(p => p.MontoPagado) + dto.MontoPagado;
        var montoDeuda  = deuda.MontoOriginal + deuda.MontoMora - deuda.MontoDescuento;
        if (totalPagado >= montoDeuda)
            deuda.Estado = EstadoPago.Pagado;

        await _context.SaveChangesAsync(cancellationToken);
        return pago.Id;
    }
}
