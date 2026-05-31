using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Academico.Secciones;

public class SeccionDto
{
    public int Id { get; set; }
    public int CursoId { get; set; }
    public string Curso { get; set; } = string.Empty;
    public int PeriodoAcademicoId { get; set; }
    public string PeriodoAcademico { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public int Capacidad { get; set; }
    public int Inscritos { get; set; }
    public int? DocentePrincipalId { get; set; }
    public string? DocentePrincipal { get; set; }
}

public class GetSeccionesQuery : IRequest<List<SeccionDto>> 
{ 
    public int? PeriodoAcademicoId { get; set; }
}

public class GetSeccionesQueryHandler : IRequestHandler<GetSeccionesQuery, List<SeccionDto>>
{
    private readonly IApplicationDbContext _context;
    public GetSeccionesQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<SeccionDto>> Handle(GetSeccionesQuery request, CancellationToken cancellationToken)
    {
        var query = _context.Secciones
            .Include(s => s.Curso)
            .Include(s => s.PeriodoAcademico)
            .Include(s => s.Asignaciones).ThenInclude(a => a.Docente)
            .Include(s => s.Matriculas)
            .AsQueryable();

        if (request.PeriodoAcademicoId.HasValue)
            query = query.Where(s => s.PeriodoAcademicoId == request.PeriodoAcademicoId.Value);

        return await query
            .Select(s => new SeccionDto
            {
                Id = s.Id,
                CursoId = s.CursoId,
                Curso = s.Curso != null ? s.Curso.Nombre : "",
                PeriodoAcademicoId = s.PeriodoAcademicoId,
                PeriodoAcademico = s.PeriodoAcademico != null ? s.PeriodoAcademico.Nombre : "",
                Nombre = s.Nombre,
                Capacidad = s.Capacidad,
                Inscritos = s.Matriculas.Count,
                DocentePrincipalId = s.Asignaciones.FirstOrDefault(a => a.EsPrincipal) != null 
                                     ? s.Asignaciones.FirstOrDefault(a => a.EsPrincipal)!.DocenteId 
                                     : null,
                DocentePrincipal = s.Asignaciones.FirstOrDefault(a => a.EsPrincipal) != null 
                                   ? (s.Asignaciones.FirstOrDefault(a => a.EsPrincipal)!.Docente!.Nombres + " " + s.Asignaciones.FirstOrDefault(a => a.EsPrincipal)!.Docente!.ApellidoPaterno + " " + s.Asignaciones.FirstOrDefault(a => a.EsPrincipal)!.Docente!.ApellidoMaterno) 
                                   : "Sin Asignar"
            })
            .OrderByDescending(s => s.PeriodoAcademico)
            .ThenBy(s => s.Curso)
            .ThenBy(s => s.Nombre)
            .ToListAsync(cancellationToken);
    }
}

public class UpsertSeccionCommand : IRequest<int>
{
    public int Id { get; set; }
    public int CursoId { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public int Capacidad { get; set; }
    public int? DocentePrincipalId { get; set; }
}

public class UpsertSeccionCommandHandler : IRequestHandler<UpsertSeccionCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertSeccionCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<int> Handle(UpsertSeccionCommand request, CancellationToken cancellationToken)
    {
        Seccion entity;
        if (request.Id > 0)
        {
            entity = await _context.Secciones
                .Include(s => s.Asignaciones)
                .FirstOrDefaultAsync(s => s.Id == request.Id, cancellationToken) 
                ?? throw new Exception("Sección no encontrada");
        }
        else 
        { 
            entity = new Seccion(); 
            _context.Secciones.Add(entity); 
        }

        entity.CursoId = request.CursoId;
        entity.PeriodoAcademicoId = request.PeriodoAcademicoId;
        entity.Nombre = request.Nombre;
        entity.Capacidad = request.Capacidad;

        // Manejo de la asignación del docente principal
        if (request.DocentePrincipalId.HasValue)
        {
            var asignacion = entity.Asignaciones.FirstOrDefault(a => a.EsPrincipal);
            if (asignacion == null)
            {
                entity.Asignaciones.Add(new AsignacionCurso 
                { 
                    DocenteId = request.DocentePrincipalId.Value, 
                    EsPrincipal = true 
                });
            }
            else
            {
                asignacion.DocenteId = request.DocentePrincipalId.Value;
            }
        }
        else
        {
            var asignacion = entity.Asignaciones.FirstOrDefault(a => a.EsPrincipal);
            if (asignacion != null)
            {
                entity.Asignaciones.Remove(asignacion);
            }
        }

        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteSeccionCommand : IRequest<bool> { public int Id { get; set; } }
public class DeleteSeccionCommandHandler : IRequestHandler<DeleteSeccionCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteSeccionCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<bool> Handle(DeleteSeccionCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Secciones.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        
        // Verificar si tiene matrículas antes de eliminar
        var tieneMatriculas = await _context.DetallesMatricula.AnyAsync(d => d.SeccionId == request.Id, cancellationToken);
        if (tieneMatriculas) throw new Exception("No se puede eliminar la sección porque tiene alumnos matriculados.");

        _context.Secciones.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
