using ERP.Application.Interfaces;
using ERP.Domain.Entities.Personas;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Admision.Postulantes;

// ─── DTOS ──────────────────────────────────────────────────────────────────
public class PostulanteDto
{
    public int Id { get; set; }
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string NombreCompleto => $"{ApellidoPaterno} {ApellidoMaterno}, {Nombres}";
    public string DNI { get; set; } = string.Empty;
    public DateTime FechaNacimiento { get; set; }
    public TipoSexo Sexo { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public int CarreraId { get; set; }
    public string? CarreraNombre { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public string? PeriodoNombre { get; set; }
    public EstadoPostulante Estado { get; set; }
    public DateTime FechaRegistro { get; set; }
    public decimal? PuntajeExamen { get; set; }
}

// ─── QUERIES ────────────────────────────────────────────────────────────────
public record GetPostulantesQuery : IRequest<List<PostulanteDto>>;

public class GetPostulantesQueryHandler(IApplicationDbContext context) : IRequestHandler<GetPostulantesQuery, List<PostulanteDto>>
{
    public async Task<List<PostulanteDto>> Handle(GetPostulantesQuery request, CancellationToken cancellationToken)
    {
        return await context.Postulantes
            .Include(p => p.Carrera)
            .Include(p => p.PeriodoAcademico)
            .Select(p => new PostulanteDto
            {
                Id = p.Id,
                Nombres = p.Nombres,
                ApellidoPaterno = p.ApellidoPaterno,
                ApellidoMaterno = p.ApellidoMaterno,
                DNI = p.DNI,
                FechaNacimiento = p.FechaNacimiento,
                Sexo = p.Sexo,
                Telefono = p.Telefono,
                Email = p.Email,
                CarreraId = p.CarreraId,
                CarreraNombre = p.Carrera != null ? p.Carrera.Nombre : "",
                PeriodoAcademicoId = p.PeriodoAcademicoId,
                PeriodoNombre = p.PeriodoAcademico != null ? p.PeriodoAcademico.Nombre : "",
                Estado = p.Estado,
                FechaRegistro = p.FechaRegistro,
                PuntajeExamen = p.PuntajeExamen
            })
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ───────────────────────────────────────────────────────────────
public class UpsertPostulanteCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string DNI { get; set; } = string.Empty;
    public DateTime FechaNacimiento { get; set; } = DateTime.Now.AddYears(-18);
    public TipoSexo Sexo { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public int CarreraId { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public EstadoPostulante Estado { get; set; } = EstadoPostulante.Registrado;
    public decimal? PuntajeExamen { get; set; }
    public string? Observaciones { get; set; }
}

public class UpsertPostulanteCommandHandler(IApplicationDbContext context) : IRequestHandler<UpsertPostulanteCommand, int>
{
    public async Task<int> Handle(UpsertPostulanteCommand request, CancellationToken cancellationToken)
    {
        Postulante? entity;

        if (request.Id > 0)
        {
            entity = await context.Postulantes.FindAsync(new object[] { request.Id }, cancellationToken);
            if (entity == null) throw new Exception("Postulante no encontrado");
        }
        else
        {
            entity = new Postulante();
            context.Postulantes.Add(entity);
        }

        entity.Nombres = request.Nombres;
        entity.ApellidoPaterno = request.ApellidoPaterno;
        entity.ApellidoMaterno = request.ApellidoMaterno;
        entity.DNI = request.DNI;
        entity.FechaNacimiento = request.FechaNacimiento;
        entity.Sexo = request.Sexo;
        entity.Telefono = request.Telefono;
        entity.Email = request.Email;
        entity.CarreraId = request.CarreraId;
        entity.PeriodoAcademicoId = request.PeriodoAcademicoId;
        entity.Estado = request.Estado;
        entity.PuntajeExamen = request.PuntajeExamen;
        entity.Observaciones = request.Observaciones;

        await context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeletePostulanteCommand : IRequest<bool>
{
    public int Id { get; set; }
}

public class DeletePostulanteCommandHandler(IApplicationDbContext context) : IRequestHandler<DeletePostulanteCommand, bool>
{
    public async Task<bool> Handle(DeletePostulanteCommand request, CancellationToken cancellationToken)
    {
        var entity = await context.Postulantes.FindAsync(new object[] { request.Id }, cancellationToken);
        if (entity == null) return false;

        context.Postulantes.Remove(entity);
        await context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
