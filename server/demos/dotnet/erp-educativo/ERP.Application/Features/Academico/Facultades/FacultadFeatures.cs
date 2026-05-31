using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Academico.Facultades;

public class FacultadDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public string? Decano { get; set; }
    public int TotalCarreras { get; set; }
}

// GET ALL
public class GetFacultadesQuery : IRequest<List<FacultadDto>> { }
public class GetFacultadesQueryHandler : IRequestHandler<GetFacultadesQuery, List<FacultadDto>>
{
    private readonly IApplicationDbContext _context;
    public GetFacultadesQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<FacultadDto>> Handle(GetFacultadesQuery request, CancellationToken cancellationToken)
        => await _context.Facultades
            .Select(f => new FacultadDto
            {
                Id = f.Id,
                Codigo = f.Codigo,
                Nombre = f.Nombre,
                Descripcion = f.Descripcion,
                Decano = f.Decano,
                TotalCarreras = f.Carreras.Count
            })
            .OrderBy(f => f.Nombre)
            .ToListAsync(cancellationToken);
}

// UPSERT
public class UpsertFacultadCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public string? Decano { get; set; }
}
public class UpsertFacultadCommandHandler : IRequestHandler<UpsertFacultadCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertFacultadCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<int> Handle(UpsertFacultadCommand request, CancellationToken cancellationToken)
    {
        Facultad entity;
        if (request.Id > 0)
        {
            entity = await _context.Facultades.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Facultad no encontrada");
        }
        else
        {
            entity = new Facultad();
            _context.Facultades.Add(entity);
        }
        entity.Codigo = request.Codigo;
        entity.Nombre = request.Nombre;
        entity.Descripcion = request.Descripcion;
        entity.Decano = request.Decano;
        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

// DELETE
public class DeleteFacultadCommand : IRequest<bool>
{
    public int Id { get; set; }
}
public class DeleteFacultadCommandHandler : IRequestHandler<DeleteFacultadCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteFacultadCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<bool> Handle(DeleteFacultadCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Facultades.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.Facultades.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
