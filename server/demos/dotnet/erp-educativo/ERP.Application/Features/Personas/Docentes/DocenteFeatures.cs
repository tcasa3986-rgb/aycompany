using ERP.Application.Interfaces;
using ERP.Domain.Entities.Personas;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Personas.Docentes;

public class DocenteDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string NombreCompleto { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string DNI { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string? Telefono { get; set; }
    public string? GradoAcademico { get; set; }
    public string? Especialidad { get; set; }
    public TipoContrato TipoContrato { get; set; }
    public EstadoDocente Estado { get; set; }
}

public class GetDocentesQuery : IRequest<List<DocenteDto>> { }
public class GetDocentesQueryHandler : IRequestHandler<GetDocentesQuery, List<DocenteDto>>
{
    private readonly IApplicationDbContext _context;
    public GetDocentesQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<DocenteDto>> Handle(GetDocentesQuery request, CancellationToken cancellationToken)
        => await _context.Docentes
            .Select(d => new DocenteDto
            {
                Id = d.Id, Codigo = d.Codigo,
                NombreCompleto = $"{d.ApellidoPaterno} {d.ApellidoMaterno}, {d.Nombres}",
                Nombres = d.Nombres, ApellidoPaterno = d.ApellidoPaterno, ApellidoMaterno = d.ApellidoMaterno,
                DNI = d.DNI, Email = d.Email, Telefono = d.Telefono,
                GradoAcademico = d.GradoAcademico, Especialidad = d.Especialidad,
                TipoContrato = d.TipoContrato, Estado = d.Estado
            })
            .OrderBy(d => d.ApellidoPaterno)
            .ToListAsync(cancellationToken);
}

public class GetDocenteByIdQuery : IRequest<DocenteDto?> { public int Id { get; set; } }
public class GetDocenteByIdQueryHandler : IRequestHandler<GetDocenteByIdQuery, DocenteDto?>
{
    private readonly IApplicationDbContext _context;
    public GetDocenteByIdQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<DocenteDto?> Handle(GetDocenteByIdQuery request, CancellationToken cancellationToken)
        => await _context.Docentes
            .Where(d => d.Id == request.Id)
            .Select(d => new DocenteDto
            {
                Id = d.Id, Codigo = d.Codigo,
                NombreCompleto = $"{d.ApellidoPaterno} {d.ApellidoMaterno}, {d.Nombres}",
                Nombres = d.Nombres, ApellidoPaterno = d.ApellidoPaterno, ApellidoMaterno = d.ApellidoMaterno,
                DNI = d.DNI, Email = d.Email, Telefono = d.Telefono,
                GradoAcademico = d.GradoAcademico, Especialidad = d.Especialidad,
                TipoContrato = d.TipoContrato, Estado = d.Estado
            })
            .FirstOrDefaultAsync(cancellationToken);
}

public class UpsertDocenteCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string DNI { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string? Telefono { get; set; }
    public string? GradoAcademico { get; set; }
    public string? Especialidad { get; set; }
    public TipoContrato TipoContrato { get; set; }
    public EstadoDocente Estado { get; set; } = EstadoDocente.Activo;
    public DateTime FechaIngreso { get; set; } = DateTime.Today;
}
public class UpsertDocenteCommandHandler : IRequestHandler<UpsertDocenteCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertDocenteCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<int> Handle(UpsertDocenteCommand request, CancellationToken cancellationToken)
    {
        Docente entity;
        if (request.Id > 0)
            entity = await _context.Docentes.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Docente no encontrado");
        else { entity = new Docente(); _context.Docentes.Add(entity); }
        entity.Codigo = request.Codigo; entity.Nombres = request.Nombres;
        entity.ApellidoPaterno = request.ApellidoPaterno; entity.ApellidoMaterno = request.ApellidoMaterno;
        entity.DNI = request.DNI; entity.Email = request.Email; entity.Telefono = request.Telefono;
        entity.GradoAcademico = request.GradoAcademico; entity.Especialidad = request.Especialidad;
        entity.TipoContrato = request.TipoContrato; entity.Estado = request.Estado;
        entity.FechaIngreso = request.FechaIngreso;
        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteDocenteCommand : IRequest<bool> { public int Id { get; set; } }
public class DeleteDocenteCommandHandler : IRequestHandler<DeleteDocenteCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteDocenteCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<bool> Handle(DeleteDocenteCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Docentes.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.Docentes.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
