using ERP.Application.Interfaces;
using ERP.Domain.Entities.Personas;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Personas.Estudiantes;

public class EstudianteDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string NombreCompleto { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string DNI { get; set; } = string.Empty;
    public DateTime FechaNacimiento { get; set; }
    public TipoSexo Sexo { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public EstadoEstudiante Estado { get; set; }
}

public class GetEstudiantesQuery : IRequest<List<EstudianteDto>> { }
public class GetEstudiantesQueryHandler : IRequestHandler<GetEstudiantesQuery, List<EstudianteDto>>
{
    private readonly IApplicationDbContext _context;
    public GetEstudiantesQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<EstudianteDto>> Handle(GetEstudiantesQuery request, CancellationToken cancellationToken)
        => await _context.Estudiantes
            .Select(e => new EstudianteDto
            {
                Id = e.Id, Codigo = e.Codigo,
                NombreCompleto = $"{e.ApellidoPaterno} {e.ApellidoMaterno}, {e.Nombres}",
                Nombres = e.Nombres, ApellidoPaterno = e.ApellidoPaterno, ApellidoMaterno = e.ApellidoMaterno,
                DNI = e.DNI, FechaNacimiento = e.FechaNacimiento, Sexo = e.Sexo,
                Telefono = e.Telefono, Email = e.Email, Estado = e.Estado
            })
            .OrderBy(e => e.ApellidoPaterno)
            .ToListAsync(cancellationToken);
}

public class GetEstudianteByIdQuery : IRequest<EstudianteDto?> { public int Id { get; set; } }
public class GetEstudianteByIdQueryHandler : IRequestHandler<GetEstudianteByIdQuery, EstudianteDto?>
{
    private readonly IApplicationDbContext _context;
    public GetEstudianteByIdQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<EstudianteDto?> Handle(GetEstudianteByIdQuery request, CancellationToken cancellationToken)
        => await _context.Estudiantes
            .Where(e => e.Id == request.Id)
            .Select(e => new EstudianteDto
            {
                Id = e.Id, Codigo = e.Codigo,
                NombreCompleto = $"{e.ApellidoPaterno} {e.ApellidoMaterno}, {e.Nombres}",
                Nombres = e.Nombres, ApellidoPaterno = e.ApellidoPaterno, ApellidoMaterno = e.ApellidoMaterno,
                DNI = e.DNI, FechaNacimiento = e.FechaNacimiento, Sexo = e.Sexo,
                Telefono = e.Telefono, Email = e.Email, Estado = e.Estado
            })
            .FirstOrDefaultAsync(cancellationToken);
}

public class UpsertEstudianteCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string DNI { get; set; } = string.Empty;
    public DateTime FechaNacimiento { get; set; }
    public TipoSexo Sexo { get; set; }
    public string? Direccion { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public EstadoEstudiante Estado { get; set; } = EstadoEstudiante.Activo;
    public string? NombreApoderado { get; set; }
    public string? TelefonoApoderado { get; set; }
}
public class UpsertEstudianteCommandHandler : IRequestHandler<UpsertEstudianteCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertEstudianteCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<int> Handle(UpsertEstudianteCommand request, CancellationToken cancellationToken)
    {
        Estudiante entity;
        if (request.Id > 0)
            entity = await _context.Estudiantes.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Estudiante no encontrado");
        else { entity = new Estudiante(); _context.Estudiantes.Add(entity); }
        entity.Codigo = request.Codigo; entity.Nombres = request.Nombres;
        entity.ApellidoPaterno = request.ApellidoPaterno; entity.ApellidoMaterno = request.ApellidoMaterno;
        entity.DNI = request.DNI; entity.FechaNacimiento = request.FechaNacimiento;
        entity.Sexo = request.Sexo; entity.Direccion = request.Direccion;
        entity.Telefono = request.Telefono; entity.Email = request.Email;
        entity.Estado = request.Estado; entity.NombreApoderado = request.NombreApoderado;
        entity.TelefonoApoderado = request.TelefonoApoderado;
        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteEstudianteCommand : IRequest<bool> { public int Id { get; set; } }
public class DeleteEstudianteCommandHandler : IRequestHandler<DeleteEstudianteCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteEstudianteCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<bool> Handle(DeleteEstudianteCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Estudiantes.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.Estudiantes.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
