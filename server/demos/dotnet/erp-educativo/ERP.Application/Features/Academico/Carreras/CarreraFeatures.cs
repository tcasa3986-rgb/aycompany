using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Academico.Carreras;

public class CarreraDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string Facultad { get; set; } = string.Empty;
    public int FacultadId { get; set; }
    public NivelEducativo NivelEducativo { get; set; }
    public Modalidad Modalidad { get; set; }
    public int DuracionSemestres { get; set; }
    public int TotalCreditos { get; set; }
}

public class GetCarrerasQuery : IRequest<List<CarreraDto>> { }
public class GetCarrerasQueryHandler : IRequestHandler<GetCarrerasQuery, List<CarreraDto>>
{
    private readonly IApplicationDbContext _context;
    public GetCarrerasQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<CarreraDto>> Handle(GetCarrerasQuery request, CancellationToken cancellationToken)
        => await _context.Carreras
            .Include(c => c.Facultad)
            .Select(c => new CarreraDto
            {
                Id = c.Id,
                Codigo = c.Codigo,
                Nombre = c.Nombre,
                FacultadId = c.FacultadId,
                Facultad = c.Facultad != null ? c.Facultad.Nombre : "",
                NivelEducativo = c.NivelEducativo,
                Modalidad = c.Modalidad,
                DuracionSemestres = c.DuracionSemestres,
                TotalCreditos = c.TotalCreditos
            })
            .OrderBy(c => c.Nombre)
            .ToListAsync(cancellationToken);
}

public class UpsertCarreraCommand : IRequest<int>
{
    public int Id { get; set; }
    public int FacultadId { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public NivelEducativo NivelEducativo { get; set; }
    public Modalidad Modalidad { get; set; }
    public int DuracionSemestres { get; set; }
    public int TotalCreditos { get; set; }
}
public class UpsertCarreraCommandHandler : IRequestHandler<UpsertCarreraCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertCarreraCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<int> Handle(UpsertCarreraCommand request, CancellationToken cancellationToken)
    {
        Carrera entity;
        if (request.Id > 0)
            entity = await _context.Carreras.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Carrera no encontrada");
        else { entity = new Carrera(); _context.Carreras.Add(entity); }
        entity.Codigo = request.Codigo; entity.Nombre = request.Nombre;
        entity.FacultadId = request.FacultadId; entity.NivelEducativo = request.NivelEducativo;
        entity.Modalidad = request.Modalidad; entity.DuracionSemestres = request.DuracionSemestres;
        entity.TotalCreditos = request.TotalCreditos;
        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteCarreraCommand : IRequest<bool> { public int Id { get; set; } }
public class DeleteCarreraCommandHandler : IRequestHandler<DeleteCarreraCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteCarreraCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<bool> Handle(DeleteCarreraCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Carreras.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.Carreras.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
