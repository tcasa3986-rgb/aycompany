using ERP.Application.Interfaces;
using ERP.Domain.Entities.Config;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Configuracion.Parametros;

// ─── DTOS ──────────────────────────────────────────────────────────────────
public class ParametroDto
{
    public int Id { get; set; }
    public string Clave { get; set; } = string.Empty;
    public string Valor { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public string? Grupo { get; set; }
}

// ─── QUERIES ────────────────────────────────────────────────────────────────
public record GetParametrosQuery : IRequest<List<ParametroDto>>;

public class GetParametrosQueryHandler(IApplicationDbContext context) : IRequestHandler<GetParametrosQuery, List<ParametroDto>>
{
    public async Task<List<ParametroDto>> Handle(GetParametrosQuery request, CancellationToken cancellationToken)
    {
        return await context.Parametros
            .OrderBy(p => p.Grupo).ThenBy(p => p.Clave)
            .Select(p => new ParametroDto
            {
                Id = p.Id,
                Clave = p.Clave,
                Valor = p.Valor,
                Descripcion = p.Descripcion,
                Grupo = p.Grupo
            })
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ───────────────────────────────────────────────────────────────
public class UpsertParametroCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Clave { get; set; } = string.Empty;
    public string Valor { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public string? Grupo { get; set; }
}

public class UpsertParametroCommandHandler(IApplicationDbContext context) : IRequestHandler<UpsertParametroCommand, int>
{
    public async Task<int> Handle(UpsertParametroCommand request, CancellationToken cancellationToken)
    {
        Parametro? entity;

        if (request.Id > 0)
        {
            entity = await context.Parametros.FindAsync(new object[] { request.Id }, cancellationToken);
            if (entity == null) throw new Exception("Parámetro no encontrado");
        }
        else
        {
            entity = new Parametro();
            context.Parametros.Add(entity);
        }

        entity.Clave = request.Clave;
        entity.Valor = request.Valor;
        entity.Descripcion = request.Descripcion;
        entity.Grupo = request.Grupo;

        await context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteParametroCommand : IRequest<bool>
{
    public int Id { get; set; }
}

public class DeleteParametroCommandHandler(IApplicationDbContext context) : IRequestHandler<DeleteParametroCommand, bool>
{
    public async Task<bool> Handle(DeleteParametroCommand request, CancellationToken cancellationToken)
    {
        var entity = await context.Parametros.FindAsync(new object[] { request.Id }, cancellationToken);
        if (entity == null) return false;

        context.Parametros.Remove(entity);
        await context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
