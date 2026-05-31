using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Academico.Cursos;

public class CursoDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public int Creditos { get; set; }
    public int HorasTeoricas { get; set; }
    public int HorasPracticas { get; set; }
    public bool EsElectivo { get; set; }
    public string? Descripcion { get; set; }
}

public class GetCursosQuery : IRequest<List<CursoDto>> { }
public class GetCursosQueryHandler : IRequestHandler<GetCursosQuery, List<CursoDto>>
{
    private readonly IApplicationDbContext _context;
    public GetCursosQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<CursoDto>> Handle(GetCursosQuery request, CancellationToken cancellationToken)
        => await _context.Cursos
            .Select(c => new CursoDto
            {
                Id = c.Id, Codigo = c.Codigo, Nombre = c.Nombre,
                Creditos = c.Creditos, HorasTeoricas = c.HorasTeoricas,
                HorasPracticas = c.HorasPracticas, EsElectivo = c.EsElectivo,
                Descripcion = c.Descripcion
            })
            .OrderBy(c => c.Nombre)
            .ToListAsync(cancellationToken);
}

public class UpsertCursoCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public int Creditos { get; set; }
    public int HorasTeoricas { get; set; }
    public int HorasPracticas { get; set; }
    public int HorasLaboratorio { get; set; }
    public bool EsElectivo { get; set; }
    public string? Descripcion { get; set; }
}
public class UpsertCursoCommandHandler : IRequestHandler<UpsertCursoCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertCursoCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<int> Handle(UpsertCursoCommand request, CancellationToken cancellationToken)
    {
        Curso entity;
        if (request.Id > 0)
            entity = await _context.Cursos.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Curso no encontrado");
        else { entity = new Curso(); _context.Cursos.Add(entity); }
        entity.Codigo = request.Codigo; entity.Nombre = request.Nombre;
        entity.Creditos = request.Creditos; entity.HorasTeoricas = request.HorasTeoricas;
        entity.HorasPracticas = request.HorasPracticas; entity.HorasLaboratorio = request.HorasLaboratorio;
        entity.EsElectivo = request.EsElectivo; entity.Descripcion = request.Descripcion;
        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteCursoCommand : IRequest<bool> { public int Id { get; set; } }
public class DeleteCursoCommandHandler : IRequestHandler<DeleteCursoCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteCursoCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<bool> Handle(DeleteCursoCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Cursos.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.Cursos.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
