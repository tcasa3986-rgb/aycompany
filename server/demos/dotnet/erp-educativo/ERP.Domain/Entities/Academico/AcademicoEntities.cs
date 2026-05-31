using ERP.Domain.Common;
using ERP.Domain.Enums;

namespace ERP.Domain.Entities.Academico;

public class Facultad : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public string? Decano { get; set; }
    public ICollection<Carrera> Carreras { get; set; } = new List<Carrera>();
}

public class Carrera : BaseEntity
{
    public int FacultadId { get; set; }
    public Facultad? Facultad { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public NivelEducativo NivelEducativo { get; set; }
    public Modalidad Modalidad { get; set; }
    public int DuracionSemestres { get; set; }
    public int TotalCreditos { get; set; }
    public string? Descripcion { get; set; }
    public string? DirectorId { get; set; }
    public ICollection<PlanEstudio> PlanesEstudio { get; set; } = new List<PlanEstudio>();
}

public class PlanEstudio : BaseEntity
{
    public int CarreraId { get; set; }
    public Carrera? Carrera { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public string Version { get; set; } = string.Empty;
    public int AnioVigencia { get; set; }
    public bool EsVigente { get; set; } = true;
    public ICollection<CursoMalla> CursosMalla { get; set; } = new List<CursoMalla>();
}

public class Curso : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public int Creditos { get; set; }
    public int HorasTeoricas { get; set; }
    public int HorasPracticas { get; set; }
    public int HorasLaboratorio { get; set; }
    public string? Descripcion { get; set; }
    public string? Syllabus { get; set; }
    public bool EsElectivo { get; set; }
    public ICollection<CursoMalla> CursosMalla { get; set; } = new List<CursoMalla>();
    public ICollection<Seccion> Secciones { get; set; } = new List<Seccion>();
}

public class CursoMalla : BaseEntity
{
    public int PlanEstudioId { get; set; }
    public PlanEstudio? PlanEstudio { get; set; }
    public int CursoId { get; set; }
    public Curso? Curso { get; set; }
    public int Ciclo { get; set; }
    public int? PrerrequisitoId { get; set; }
}

public class Seccion : BaseEntity
{
    public int CursoId { get; set; }
    public Curso? Curso { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public ERP.Domain.Entities.Config.PeriodoAcademico? PeriodoAcademico { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public int Capacidad { get; set; }
    public int? AulaId { get; set; }
    public int? TurnoId { get; set; }
    public ICollection<AsignacionCurso> Asignaciones { get; set; } = new List<AsignacionCurso>();
    public ICollection<Matricula> Matriculas { get; set; } = new List<Matricula>();
    public ICollection<Horario> Horarios { get; set; } = new List<Horario>();
    public ICollection<Asistencia> Asistencias { get; set; } = new List<Asistencia>();
    public ICollection<Evaluacion> Evaluaciones { get; set; } = new List<Evaluacion>();
}

public class AsignacionCurso : BaseEntity
{
    public int SeccionId { get; set; }
    public Seccion? Seccion { get; set; }
    public int DocenteId { get; set; }
    public ERP.Domain.Entities.Personas.Docente? Docente { get; set; }
    public bool EsPrincipal { get; set; } = true;
}

public class Horario : BaseEntity
{
    public int SeccionId { get; set; }
    public Seccion? Seccion { get; set; }
    public DayOfWeek DiaSemana { get; set; }
    public TimeOnly HoraInicio { get; set; }
    public TimeOnly HoraFin { get; set; }
    public int? AulaId { get; set; }
}

public class Matricula : BaseEntity
{
    public string NumeroMatricula { get; set; } = string.Empty;
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public int CarreraId { get; set; }
    public Carrera? Carrera { get; set; }
    public int Ciclo { get; set; }
    public TipoMatricula TipoMatricula { get; set; }
    public EstadoMatricula Estado { get; set; } = EstadoMatricula.Confirmada;
    public DateTime FechaMatricula { get; set; } = DateTime.Now;
    public string? Observaciones { get; set; }
    public ICollection<DetalleMatricula> Detalles { get; set; } = new List<DetalleMatricula>();
}

public class DetalleMatricula : BaseEntity
{
    public int MatriculaId { get; set; }
    public Matricula? Matricula { get; set; }
    public int SeccionId { get; set; }
    public Seccion? Seccion { get; set; }
}

public class Asistencia : BaseEntity
{
    public int SeccionId { get; set; }
    public Seccion? Seccion { get; set; }
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public DateTime FechaClase { get; set; }
    public EstadoAsistencia Estado { get; set; }
    public string? Observacion { get; set; }
    public bool Justificado { get; set; }
    public string? MotivoJustificacion { get; set; }
}

public class Evaluacion : BaseEntity
{
    public int SeccionId { get; set; }
    public Seccion? Seccion { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public TipoEvaluacion Tipo { get; set; }
    public decimal PesoPromedio { get; set; }  // porcentaje
    public DateTime FechaProgramada { get; set; }
    public decimal NotaMaxima { get; set; } = 20;
    public bool EsSubsanacion { get; set; }
    public int? EvaluacionBaseId { get; set; }
    public ICollection<Nota> Notas { get; set; } = new List<Nota>();
}

public class Nota : BaseEntity
{
    public int EvaluacionId { get; set; }
    public Evaluacion? Evaluacion { get; set; }
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public decimal? Calificacion { get; set; }
    public string? Observacion { get; set; }
    public bool Ausente { get; set; }
}

public class PromedioFinal : BaseEntity
{
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public int SeccionId { get; set; }
    public Seccion? Seccion { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public decimal? PromedioCalculado { get; set; }
    public bool Aprobado { get; set; }
    public string? Estado { get; set; }  // Aprobado, Desaprobado, Aplazado
}
