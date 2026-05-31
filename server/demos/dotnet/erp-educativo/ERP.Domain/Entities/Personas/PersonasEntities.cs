using ERP.Domain.Common;
using ERP.Domain.Enums;
using ERP.Domain.Entities.Academico;

namespace ERP.Domain.Entities.Personas;

public class Estudiante : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string NombreCompleto => $"{ApellidoPaterno} {ApellidoMaterno}, {Nombres}";
    public string DNI { get; set; } = string.Empty;
    public DateTime FechaNacimiento { get; set; }
    public TipoSexo Sexo { get; set; }
    public string? Direccion { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public string? FotoPerfil { get; set; }
    public EstadoEstudiante Estado { get; set; } = EstadoEstudiante.Activo;
    public string? Nacionalidad { get; set; } = "Peruana";
    public string? LugarNacimiento { get; set; }
    public string? GrupoSanguineo { get; set; }
    public string? Alergias { get; set; }
    public string? Discapacidad { get; set; }
    public string? UsuarioId { get; set; }

    // Apoderado
    public string? NombreApoderado { get; set; }
    public string? DNIApoderado { get; set; }
    public string? TelefonoApoderado { get; set; }
    public string? EmailApoderado { get; set; }
    public string? ParentescoApoderado { get; set; }

    public ICollection<Matricula> Matriculas { get; set; } = new List<Matricula>();
    public ICollection<Documento> Documentos { get; set; } = new List<Documento>();
}

public class Docente : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string ApellidoPaterno { get; set; } = string.Empty;
    public string ApellidoMaterno { get; set; } = string.Empty;
    public string NombreCompleto => $"{ApellidoPaterno} {ApellidoMaterno}, {Nombres}";
    public string DNI { get; set; } = string.Empty;
    public DateTime FechaNacimiento { get; set; }
    public TipoSexo Sexo { get; set; }
    public string? Direccion { get; set; }
    public string? Telefono { get; set; }
    public string Email { get; set; } = string.Empty;
    public string? FotoPerfil { get; set; }
    public EstadoDocente Estado { get; set; } = EstadoDocente.Activo;
    public TipoContrato TipoContrato { get; set; }
    public string? GradoAcademico { get; set; }
    public string? Especialidad { get; set; }
    public string? ColegiaturaN { get; set; }
    public DateTime FechaIngreso { get; set; }
    public decimal? Sueldo { get; set; }
    public int? HorasMaximas { get; set; }
    public string? UsuarioId { get; set; }
    public ICollection<Documento> Documentos { get; set; } = new List<Documento>();
    public ICollection<AsignacionCurso> Asignaciones { get; set; } = new List<AsignacionCurso>();
}

public class PersonalAdministrativo : BaseEntity
{
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
    public DateTime FechaIngreso { get; set; }
    public string? UsuarioId { get; set; }
}

public class Documento : BaseEntity
{
    public string Nombre { get; set; } = string.Empty;
    public string? TipoDocumento { get; set; }
    public string RutaArchivo { get; set; } = string.Empty;
    public int? EstudianteId { get; set; }
    public Estudiante? Estudiante { get; set; }
    public int? DocenteId { get; set; }
    public Docente? Docente { get; set; }
}
