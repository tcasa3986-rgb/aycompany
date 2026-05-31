using ERP.Application.Interfaces;
using ERP.Domain.Entities.Personas;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Personas.Personal;

// ─── DTOS ──────────────────────────────────────────────────────────────────
public class PersonalDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string NombreCompleto => $"{ApellidoPaterno} {ApellidoMaterno}, {Nombres}";
    public string DNI { get; set; } = string.Empty;
    public TipoSexo Sexo { get; set; }
    public string? Cargo { get; set; }
    public string? Area { get; set; }
    public TipoPersonal TipoPersonal { get; set; }
    public string Email { get; set; } = string.Empty;
    public string? Telefono { get; set; }
    public decimal? Sueldo { get; set; }
    public DateTime FechaIngreso { get; set; }
}

// ─── QUERIES ────────────────────────────────────────────────────────────────
public record GetPersonalQuery : IRequest<List<PersonalDto>>;

public class GetPersonalQueryHandler(IApplicationDbContext context) : IRequestHandler<GetPersonalQuery, List<PersonalDto>>
{
    public async Task<List<PersonalDto>> Handle(GetPersonalQuery request, CancellationToken cancellationToken)
    {
        return await context.PersonalAdministrativo
            .Select(p => new PersonalDto
            {
                Id = p.Id,
                Codigo = p.Codigo,
                Nombres = p.Nombres,
                ApellidoPaterno = p.ApellidoPaterno,
                ApellidoMaterno = p.ApellidoMaterno,
                DNI = p.DNI,
                Sexo = p.Sexo,
                Cargo = p.Cargo,
                Area = p.Area,
                TipoPersonal = p.TipoPersonal,
                Email = p.Email,
                Telefono = p.Telefono,
                Sueldo = p.Sueldo,
                FechaIngreso = p.FechaIngreso
            })
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ───────────────────────────────────────────────────────────────
public class UpsertPersonalCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string DNI { get; set; } = string.Empty;
    public TipoSexo Sexo { get; set; }
    public string? Cargo { get; set; }
    public string? Area { get; set; }
    public TipoPersonal TipoPersonal { get; set; }
    public string Email { get; set; } = string.Empty;
    public string? Telefono { get; set; }
    public decimal? Sueldo { get; set; }
    public DateTime FechaIngreso { get; set; } = DateTime.Now;
}

public class UpsertPersonalCommandHandler(IApplicationDbContext context) : IRequestHandler<UpsertPersonalCommand, int>
{
    public async Task<int> Handle(UpsertPersonalCommand request, CancellationToken cancellationToken)
    {
        PersonalAdministrativo? entity;

        if (request.Id > 0)
        {
            entity = await context.PersonalAdministrativo.FindAsync(new object[] { request.Id }, cancellationToken);
            if (entity == null) throw new Exception("Personal no encontrado");
        }
        else
        {
            entity = new PersonalAdministrativo();
            context.PersonalAdministrativo.Add(entity);
        }

        entity.Codigo = request.Codigo;
        entity.Nombres = request.Nombres;
        entity.ApellidoPaterno = request.ApellidoPaterno;
        entity.ApellidoMaterno = request.ApellidoMaterno;
        entity.DNI = request.DNI;
        entity.Sexo = request.Sexo;
        entity.Cargo = request.Cargo;
        entity.Area = request.Area;
        entity.TipoPersonal = request.TipoPersonal;
        entity.Email = request.Email;
        entity.Telefono = request.Telefono;
        entity.Sueldo = request.Sueldo;
        entity.FechaIngreso = request.FechaIngreso;

        await context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeletePersonalCommand : IRequest<bool>
{
    public int Id { get; set; }
}

public class DeletePersonalCommandHandler(IApplicationDbContext context) : IRequestHandler<DeletePersonalCommand, bool>
{
    public async Task<bool> Handle(DeletePersonalCommand request, CancellationToken cancellationToken)
    {
        var entity = await context.PersonalAdministrativo.FindAsync(new object[] { request.Id }, cancellationToken);
        if (entity == null) return false;

        context.PersonalAdministrativo.Remove(entity);
        await context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
