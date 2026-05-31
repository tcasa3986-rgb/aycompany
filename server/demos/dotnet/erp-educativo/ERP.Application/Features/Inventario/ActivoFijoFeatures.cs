using ERP.Application.Interfaces;
using ERP.Domain.Entities.Biblioteca;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Inventario;

// ─── DTOS ──────────────────────────────────────────────────────────────────
public class ActivoFijoDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public TipoActivo TipoActivo { get; set; }
    public string? Marca { get; set; }
    public string? Modelo { get; set; }
    public string? NumeroSerie { get; set; }
    public decimal ValorAdquisicion { get; set; }
    public DateTime FechaAdquisicion { get; set; }
    public string? Estado { get; set; }
    public string? UbicacionActual { get; set; }
    public string? AsignadoA { get; set; }
}

// ─── QUERIES ────────────────────────────────────────────────────────────────
public record GetActivosFijosQuery : IRequest<List<ActivoFijoDto>>;

public class GetActivosFijosQueryHandler(IApplicationDbContext context) : IRequestHandler<GetActivosFijosQuery, List<ActivoFijoDto>>
{
    public async Task<List<ActivoFijoDto>> Handle(GetActivosFijosQuery request, CancellationToken cancellationToken)
    {
        return await context.ActivosFijos
            .OrderBy(a => a.Nombre)
            .Select(a => new ActivoFijoDto
            {
                Id = a.Id,
                Codigo = a.Codigo,
                Nombre = a.Nombre,
                TipoActivo = a.TipoActivo,
                Marca = a.Marca,
                Modelo = a.Modelo,
                NumeroSerie = a.NumeroSerie,
                ValorAdquisicion = a.ValorAdquisicion,
                FechaAdquisicion = a.FechaAdquisicion,
                Estado = a.Estado,
                UbicacionActual = a.UbicacionActual,
                AsignadoA = a.AsignadoA
            })
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ───────────────────────────────────────────────────────────────
public class UpsertActivoFijoCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public TipoActivo TipoActivo { get; set; }
    public string? Marca { get; set; }
    public string? Modelo { get; set; }
    public string? NumeroSerie { get; set; }
    public decimal ValorAdquisicion { get; set; }
    public DateTime FechaAdquisicion { get; set; } = DateTime.Now;
    public string? Estado { get; set; } = "Bueno";
    public string? UbicacionActual { get; set; }
    public string? AsignadoA { get; set; }
}

public class UpsertActivoFijoCommandHandler(IApplicationDbContext context) : IRequestHandler<UpsertActivoFijoCommand, int>
{
    public async Task<int> Handle(UpsertActivoFijoCommand request, CancellationToken cancellationToken)
    {
        ActivoFijo? entity;

        if (request.Id > 0)
        {
            entity = await context.ActivosFijos.FindAsync(new object[] { request.Id }, cancellationToken);
            if (entity == null) throw new Exception("Activo no encontrado");
        }
        else
        {
            entity = new ActivoFijo();
            context.ActivosFijos.Add(entity);
        }

        entity.Codigo = request.Codigo;
        entity.Nombre = request.Nombre;
        entity.TipoActivo = request.TipoActivo;
        entity.Marca = request.Marca;
        entity.Modelo = request.Modelo;
        entity.NumeroSerie = request.NumeroSerie;
        entity.ValorAdquisicion = request.ValorAdquisicion;
        entity.FechaAdquisicion = request.FechaAdquisicion;
        entity.Estado = request.Estado;
        entity.UbicacionActual = request.UbicacionActual;
        entity.AsignadoA = request.AsignadoA;

        await context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteActivoFijoCommand : IRequest<bool>
{
    public int Id { get; set; }
}

public class DeleteActivoFijoCommandHandler(IApplicationDbContext context) : IRequestHandler<DeleteActivoFijoCommand, bool>
{
    public async Task<bool> Handle(DeleteActivoFijoCommand request, CancellationToken cancellationToken)
    {
        var entity = await context.ActivosFijos.FindAsync(new object[] { request.Id }, cancellationToken);
        if (entity == null) return false;

        context.ActivosFijos.Remove(entity);
        await context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
