using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Academico.PlanesEstudio;

public class PlanEstudioDto
{
    public int Id { get; set; }
    public int CarreraId { get; set; }
    public string Carrera { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string Version { get; set; } = string.Empty;
    public int AnioVigencia { get; set; }
    public bool EsVigente { get; set; }
}

public class GetPlanesEstudioQuery : IRequest<List<PlanEstudioDto>> { }
public class GetPlanesEstudioQueryHandler : IRequestHandler<GetPlanesEstudioQuery, List<PlanEstudioDto>>
{
    private readonly IApplicationDbContext _context;
    public GetPlanesEstudioQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<PlanEstudioDto>> Handle(GetPlanesEstudioQuery request, CancellationToken cancellationToken)
        => await _context.PlanesEstudio
            .Include(p => p.Carrera)
            .Select(p => new PlanEstudioDto
            {
                Id = p.Id,
                CarreraId = p.CarreraId,
                Carrera = p.Carrera != null ? p.Carrera.Nombre : "",
                Nombre = p.Nombre,
                Version = p.Version,
                AnioVigencia = p.AnioVigencia,
                EsVigente = p.EsVigente
            })
            .OrderByDescending(p => p.AnioVigencia)
            .ToListAsync(cancellationToken);
}

public class UpsertPlanEstudioCommand : IRequest<int>
{
    public int Id { get; set; }
    public int CarreraId { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public string Version { get; set; } = string.Empty;
    public int AnioVigencia { get; set; }
    public bool EsVigente { get; set; } = true;
}

public class UpsertPlanEstudioCommandHandler : IRequestHandler<UpsertPlanEstudioCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertPlanEstudioCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<int> Handle(UpsertPlanEstudioCommand request, CancellationToken cancellationToken)
    {
        PlanEstudio entity;
        if (request.Id > 0)
            entity = await _context.PlanesEstudio.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Plan de Estudio no encontrado");
        else 
        { 
            entity = new PlanEstudio(); 
            _context.PlanesEstudio.Add(entity); 
        }

        entity.CarreraId = request.CarreraId;
        entity.Nombre = request.Nombre;
        entity.Version = request.Version;
        entity.AnioVigencia = request.AnioVigencia;
        entity.EsVigente = request.EsVigente;

        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeletePlanEstudioCommand : IRequest<bool> { public int Id { get; set; } }
public class DeletePlanEstudioCommandHandler : IRequestHandler<DeletePlanEstudioCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeletePlanEstudioCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<bool> Handle(DeletePlanEstudioCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.PlanesEstudio.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.PlanesEstudio.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
