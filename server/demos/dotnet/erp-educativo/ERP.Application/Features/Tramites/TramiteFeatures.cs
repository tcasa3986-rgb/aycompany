using ERP.Application.Interfaces;
using ERP.Domain.Entities.Financiero;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Tramites;

// ─── DTOS ──────────────────────────────────────────────────────────────────
public class SolicitudDocumentoDto
{
    public int Id { get; set; }
    public string NumeroSolicitud { get; set; } = string.Empty;
    public int EstudianteId { get; set; }
    public string EstudianteNombre { get; set; } = string.Empty;
    public string TipoDocumento { get; set; } = string.Empty;
    public string? Motivo { get; set; }
    public EstadoSolicitudDocumento Estado { get; set; }
    public DateTime FechaSolicitud { get; set; }
    public DateTime? FechaEntrega { get; set; }
    public decimal CostoDerecho { get; set; }
    public bool Pagado { get; set; }
}

// ─── QUERIES ────────────────────────────────────────────────────────────────
public record GetSolicitudesQuery : IRequest<List<SolicitudDocumentoDto>>;

public class GetSolicitudesQueryHandler(IApplicationDbContext context) : IRequestHandler<GetSolicitudesQuery, List<SolicitudDocumentoDto>>
{
    public async Task<List<SolicitudDocumentoDto>> Handle(GetSolicitudesQuery request, CancellationToken cancellationToken)
    {
        return await context.SolicitudesDocumento
            .Include(s => s.Estudiante)
            .OrderByDescending(s => s.FechaSolicitud)
            .Select(s => new SolicitudDocumentoDto
            {
                Id = s.Id,
                NumeroSolicitud = s.NumeroSolicitud,
                EstudianteId = s.EstudianteId,
                EstudianteNombre = s.Estudiante != null ? s.Estudiante.NombreCompleto : "",
                TipoDocumento = s.TipoDocumento,
                Motivo = s.Motivo,
                Estado = s.Estado,
                FechaSolicitud = s.FechaSolicitud,
                FechaEntrega = s.FechaEntrega,
                CostoDerecho = s.CostoDerecho,
                Pagado = s.Pagado
            })
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ───────────────────────────────────────────────────────────────
public class CambiarEstadoSolicitudCommand : IRequest<bool>
{
    public int Id { get; set; }
    public EstadoSolicitudDocumento NuevoEstado { get; set; }
    public DateTime? FechaEntrega { get; set; }
}

public class CambiarEstadoSolicitudCommandHandler(IApplicationDbContext context) : IRequestHandler<CambiarEstadoSolicitudCommand, bool>
{
    public async Task<bool> Handle(CambiarEstadoSolicitudCommand request, CancellationToken cancellationToken)
    {
        var entity = await context.SolicitudesDocumento.FindAsync(new object[] { request.Id }, cancellationToken);
        if (entity == null) return false;

        entity.Estado = request.NuevoEstado;
        if (request.FechaEntrega.HasValue)
            entity.FechaEntrega = request.FechaEntrega;
        
        if (request.NuevoEstado == EstadoSolicitudDocumento.Entregado && !entity.FechaEntrega.HasValue)
            entity.FechaEntrega = DateTime.Now;

        await context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
