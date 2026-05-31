using ERP.Application.Interfaces;
using ERP.Domain.Entities.Biblioteca;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Biblioteca.Prestamos;

// ─── DTOs ──────────────────────────────────────────────────────────────────

public class PrestamoDto
{
    public int Id { get; set; }
    public int LibroId { get; set; }
    public string LibroTitulo { get; set; } = string.Empty;
    public string LibroISBN { get; set; } = string.Empty;
    public int EstudianteId { get; set; }
    public string EstudianteNombre { get; set; } = string.Empty;
    public string EstudianteCodigo { get; set; } = string.Empty;
    public DateTime FechaPrestamo { get; set; }
    public DateTime FechaDevolucionEsperada { get; set; }
    public DateTime? FechaDevolucionReal { get; set; }
    public EstadoPrestamoBiblioteca Estado { get; set; }
    public decimal? MultaGenerada { get; set; }
    public bool MultaPagada { get; set; }
    public string? Observaciones { get; set; }
}

// ─── QUERIES ───────────────────────────────────────────────────────────────

public class GetPrestamosQuery : IRequest<List<PrestamoDto>> { }

public class GetPrestamosQueryHandler : IRequestHandler<GetPrestamosQuery, List<PrestamoDto>>
{
    private readonly IApplicationDbContext _context;

    public GetPrestamosQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<PrestamoDto>> Handle(GetPrestamosQuery request, CancellationToken cancellationToken)
    {
        return await _context.Prestamos
            .Include(p => p.Libro)
            .Include(p => p.Estudiante)
            .Select(p => new PrestamoDto
            {
                Id = p.Id,
                LibroId = p.LibroId,
                LibroTitulo = p.Libro!.Titulo,
                LibroISBN = p.Libro.ISBN,
                EstudianteId = p.EstudianteId,
                EstudianteNombre = $"{p.Estudiante!.ApellidoPaterno} {p.Estudiante.ApellidoMaterno}, {p.Estudiante.Nombres}",
                EstudianteCodigo = p.Estudiante.Codigo,
                FechaPrestamo = p.FechaPrestamo,
                FechaDevolucionEsperada = p.FechaDevolucionEsperada,
                FechaDevolucionReal = p.FechaDevolucionReal,
                Estado = p.Estado,
                MultaGenerada = p.MultaGenerada,
                MultaPagada = p.MultaPagada,
                Observaciones = p.Observaciones
            })
            .OrderByDescending(p => p.FechaPrestamo)
            .ToListAsync(cancellationToken);
    }
}

// ─── COMMANDS ──────────────────────────────────────────────────────────────

public class CreatePrestamoCommand : IRequest<int>
{
    public int LibroId { get; set; }
    public int EstudianteId { get; set; }
    public DateTime FechaDevolucionEsperada { get; set; }
    public string? Observaciones { get; set; }
}

public class CreatePrestamoCommandHandler : IRequestHandler<CreatePrestamoCommand, int>
{
    private readonly IApplicationDbContext _context;

    public CreatePrestamoCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<int> Handle(CreatePrestamoCommand request, CancellationToken cancellationToken)
    {
        var libro = await _context.Libros.FindAsync(new object[] { request.LibroId }, cancellationToken);
        if (libro == null) throw new Exception("El libro no existe.");
        if (libro.EjemplaresDisponibles <= 0) throw new Exception("No hay ejemplares disponibles para préstamo.");

        // Reducir stock
        libro.EjemplaresDisponibles -= 1;

        var prestamo = new Prestamo
        {
            LibroId = request.LibroId,
            EstudianteId = request.EstudianteId,
            FechaPrestamo = DateTime.Now,
            FechaDevolucionEsperada = request.FechaDevolucionEsperada,
            Estado = EstadoPrestamoBiblioteca.Activo,
            Observaciones = request.Observaciones
        };

        _context.Prestamos.Add(prestamo);
        await _context.SaveChangesAsync(cancellationToken);

        return prestamo.Id;
    }
}

public class DevolverLibroCommand : IRequest<bool>
{
    public int PrestamoId { get; set; }
    public decimal? MultaGenerada { get; set; }
    public string? ObservacionDevolucion { get; set; }
}

public class DevolverLibroCommandHandler : IRequestHandler<DevolverLibroCommand, bool>
{
    private readonly IApplicationDbContext _context;

    public DevolverLibroCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<bool> Handle(DevolverLibroCommand request, CancellationToken cancellationToken)
    {
        var prestamo = await _context.Prestamos.Include(p => p.Libro).FirstOrDefaultAsync(p => p.Id == request.PrestamoId, cancellationToken);
        if (prestamo == null) return false;
        
        if (prestamo.Estado == EstadoPrestamoBiblioteca.Devuelto) return true; // ya devuelto

        prestamo.FechaDevolucionReal = DateTime.Now;
        prestamo.Estado = EstadoPrestamoBiblioteca.Devuelto;
        prestamo.MultaGenerada = request.MultaGenerada;
        if (!string.IsNullOrEmpty(request.ObservacionDevolucion))
        {
            prestamo.Observaciones += $"\n[Devolución]: {request.ObservacionDevolucion}";
        }

        // Devolver stock al libro
        if (prestamo.Libro != null)
        {
            prestamo.Libro.EjemplaresDisponibles += 1;
        }

        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
