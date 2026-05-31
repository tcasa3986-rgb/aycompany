using ERP.Domain.Common;
using ERP.Domain.Enums;

namespace ERP.Domain.Entities.Biblioteca;

public class Libro : BaseEntity
{
    public string ISBN { get; set; } = string.Empty;
    public string Titulo { get; set; } = string.Empty;
    public string Autor { get; set; } = string.Empty;
    public string? Editorial { get; set; }
    public int? AnioPublicacion { get; set; }
    public string? Edicion { get; set; }
    public string? Idioma { get; set; } = "Español";
    public string? Categoria { get; set; }
    public string? SubCategoria { get; set; }
    public string? Ubicacion { get; set; }
    public string? PortadaUrl { get; set; }
    public string? Resumen { get; set; }
    public int TotalEjemplares { get; set; }
    public int EjemplaresDisponibles { get; set; }
    public ICollection<Prestamo> Prestamos { get; set; } = new List<Prestamo>();
}

public class Prestamo : BaseEntity
{
    public int LibroId { get; set; }
    public Libro? Libro { get; set; }
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public DateTime FechaPrestamo { get; set; } = DateTime.Now;
    public DateTime FechaDevolucionEsperada { get; set; }
    public DateTime? FechaDevolucionReal { get; set; }
    public EstadoPrestamoBiblioteca Estado { get; set; } = EstadoPrestamoBiblioteca.Activo;
    public decimal? MultaGenerada { get; set; }
    public bool MultaPagada { get; set; }
    public string? Observaciones { get; set; }
}

public class ActivoFijo : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public TipoActivo TipoActivo { get; set; }
    public string? Marca { get; set; }
    public string? Modelo { get; set; }
    public string? NumeroSerie { get; set; }
    public decimal ValorAdquisicion { get; set; }
    public DateTime FechaAdquisicion { get; set; }
    public string? Estado { get; set; } = "Bueno"; // Bueno, Regular, Malo, Dado de baja
    public string? UbicacionActual { get; set; }
    public string? AsignadoA { get; set; }
    public string? Observaciones { get; set; }
}

public class Comunicado : BaseEntity
{
    public string Titulo { get; set; } = string.Empty;
    public string Contenido { get; set; } = string.Empty;
    public string? TipoDestinatario { get; set; } // Todos, Estudiantes, Docentes, Administrativos
    public DateTime FechaPublicacion { get; set; } = DateTime.Now;
    public DateTime? FechaExpiracion { get; set; }
    public bool Destacado { get; set; }
    public string? AutorId { get; set; }
    public string? ArchivoAdjunto { get; set; }
}

public class Notificacion : BaseEntity
{
    public string UsuarioId { get; set; } = string.Empty;
    public string Titulo { get; set; } = string.Empty;
    public string Mensaje { get; set; } = string.Empty;
    public string? Tipo { get; set; } // Info, Advertencia, Error, Éxito
    public string? Enlace { get; set; }
    public bool Leida { get; set; }
    public DateTime FechaEnvio { get; set; } = DateTime.Now;
}

public class Tesis : BaseEntity
{
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Resumen { get; set; }
    public string? AsesorId { get; set; }
    public string? Estado { get; set; } // Propuesta, En Desarrollo, Sustentada, Aprobada
    public DateTime? FechaSustentacion { get; set; }
    public decimal? NotaSustentacion { get; set; }
    public string? RutaDocumento { get; set; }
    public string? Observaciones { get; set; }
}
