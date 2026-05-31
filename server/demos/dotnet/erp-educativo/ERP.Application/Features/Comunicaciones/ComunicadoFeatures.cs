using ERP.Application.Interfaces;
using ERP.Domain.Entities.Biblioteca;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Comunicaciones;

// ─── DTOS ──────────────────────────────────────────────────────────────────
public class ComunicadoDto
{
    public int Id { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string Contenido { get; set; } = string.Empty;
    public string? TipoDestinatario { get; set; }
    public DateTime FechaPublicacion { get; set; }
    public DateTime? FechaExpiracion { get; set; }
    public bool Destacado { get; set; }
    public string? ArchivoAdjunto { get; set; }
}

// ─── QUERIES ────────────────────────────────────────────────────────────────
public record GetComunicadosQuery : IRequest<List<ComunicadoDto>>;

public class GetComunicadosQueryHandler(IApplicationDbContext context) : IRequestHandler<GetComunicadosQuery, List<ComunicadoDto>>
{
    public async Task<List<ComunicadoDto>> Handle(GetComunicadosQuery request, CancellationToken cancellationToken)
    {
        return await context.Comunicados
            .OrderByDescending(c => c.Destacado).ThenByDescending(c => c.FechaPublicacion)
            .Select(c => new ComunicadoDto
            {
                Id = c.Id,
                Titulo = c.Titulo,
                Contenido = c.Contenido,
                TipoDestinatario = c.TipoDestinatario,
                FechaPublicacion = c.FechaPublicacion,
                FechaExpiracion = c.FechaExpiracion,
                Destacado = c.Destacado,
                ArchivoAdjunto = c.ArchivoAdjunto
            })
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ───────────────────────────────────────────────────────────────
public class UpsertComunicadoCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string Contenido { get; set; } = string.Empty;
    public string? TipoDestinatario { get; set; } = "Todos";
    public DateTime FechaPublicacion { get; set; } = DateTime.Now;
    public DateTime? FechaExpiracion { get; set; }
    public bool Destacado { get; set; }
}

public class UpsertComunicadoCommandHandler(IApplicationDbContext context) : IRequestHandler<UpsertComunicadoCommand, int>
{
    public async Task<int> Handle(UpsertComunicadoCommand request, CancellationToken cancellationToken)
    {
        Comunicado? entity;

        if (request.Id > 0)
        {
            entity = await context.Comunicados.FindAsync(new object[] { request.Id }, cancellationToken);
            if (entity == null) throw new Exception("Comunicado no encontrado");
        }
        else
        {
            entity = new Comunicado();
            context.Comunicados.Add(entity);
        }

        entity.Titulo = request.Titulo;
        entity.Contenido = request.Contenido;
        entity.TipoDestinatario = request.TipoDestinatario;
        entity.FechaPublicacion = request.FechaPublicacion;
        entity.FechaExpiracion = request.FechaExpiracion;
        entity.Destacado = request.Destacado;

        await context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteComunicadoCommand : IRequest<bool>
{
    public int Id { get; set; }
}

public class DeleteComunicadoCommandHandler(IApplicationDbContext context) : IRequestHandler<DeleteComunicadoCommand, bool>
{
    public async Task<bool> Handle(DeleteComunicadoCommand request, CancellationToken cancellationToken)
    {
        var entity = await context.Comunicados.FindAsync(new object[] { request.Id }, cancellationToken);
        if (entity == null) return false;

        context.Comunicados.Remove(entity);
        await context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
