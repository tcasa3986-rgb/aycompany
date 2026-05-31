using ERP.Application.Interfaces;
using ERP.Domain.Entities.Biblioteca;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Biblioteca.Libros;

public class LibroDto
{
    public int Id { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Autor { get; set; }
    public string? ISBN { get; set; }
    public string? Editorial { get; set; }
    public int? AnioPublicacion { get; set; }
    public int TotalEjemplares { get; set; }
    public int EjemplaresDisponibles { get; set; }
    public string? Categoria { get; set; }
}

public class GetLibrosQuery : IRequest<List<LibroDto>> { }
public class GetLibrosQueryHandler : IRequestHandler<GetLibrosQuery, List<LibroDto>>
{
    private readonly IApplicationDbContext _context;
    public GetLibrosQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<LibroDto>> Handle(GetLibrosQuery request, CancellationToken cancellationToken)
        => await _context.Libros
            .Select(l => new LibroDto
            {
                Id = l.Id, Titulo = l.Titulo, Autor = l.Autor, ISBN = l.ISBN,
                Editorial = l.Editorial, AnioPublicacion = l.AnioPublicacion,
                TotalEjemplares = l.TotalEjemplares, EjemplaresDisponibles = l.EjemplaresDisponibles, Categoria = l.Categoria
            })
            .OrderBy(l => l.Titulo)
            .ToListAsync(cancellationToken);
}

public class UpsertLibroCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Autor { get; set; }
    public string? ISBN { get; set; }
    public string? Editorial { get; set; }
    public int? AnioPublicacion { get; set; }
    public int TotalEjemplares { get; set; }
    public int EjemplaresDisponibles { get; set; }
    public string? Categoria { get; set; }
    public string? Descripcion { get; set; }
}
public class UpsertLibroCommandHandler : IRequestHandler<UpsertLibroCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertLibroCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<int> Handle(UpsertLibroCommand request, CancellationToken cancellationToken)
    {
        Libro entity;
        if (request.Id > 0)
            entity = await _context.Libros.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Libro no encontrado");
        else { entity = new Libro(); _context.Libros.Add(entity); }
        entity.Titulo = request.Titulo; entity.Autor = request.Autor;
        entity.ISBN = request.ISBN; entity.Editorial = request.Editorial;
        entity.AnioPublicacion = request.AnioPublicacion;
        entity.TotalEjemplares = request.TotalEjemplares; entity.EjemplaresDisponibles = request.EjemplaresDisponibles;
        entity.Categoria = request.Categoria;
        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteLibroCommand : IRequest<bool> { public int Id { get; set; } }
public class DeleteLibroCommandHandler : IRequestHandler<DeleteLibroCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteLibroCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<bool> Handle(DeleteLibroCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Libros.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.Libros.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
