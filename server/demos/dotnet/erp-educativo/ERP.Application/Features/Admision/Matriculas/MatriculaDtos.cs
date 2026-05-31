using ERP.Domain.Enums;

namespace ERP.Application.Features.Admision.Matriculas;

public class MatriculaListDto
{
    public int Id { get; set; }
    public string NumeroMatricula { get; set; } = string.Empty;
    public int EstudianteId { get; set; }
    public string Estudiante { get; set; } = string.Empty;
    public string CodigoEstudiante { get; set; } = string.Empty;
    public string Carrera { get; set; } = string.Empty;
    public string Periodo { get; set; } = string.Empty;
    public DateTime FechaMatricula { get; set; }
    public EstadoMatricula Estado { get; set; }
}

public class MatriculaCreateDto
{
    public int EstudianteId { get; set; }
    public int CarreraId { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public int Ciclo { get; set; }
    public TipoMatricula TipoMatricula { get; set; }
    public string? Observaciones { get; set; }
    public List<int> SeccionesIds { get; set; } = new();
}

public class MatriculaDto
{
    public int Id { get; set; }
    public string NumeroMatricula { get; set; } = string.Empty;
    public string EstudianteNombre { get; set; } = string.Empty;
    public string EstudianteCodigo { get; set; } = string.Empty;
    public string CarreraNombre { get; set; } = string.Empty;
    public int Ciclo { get; set; }
    public List<MatriculaCursoDto> Cursos { get; set; } = new();
}

public class MatriculaCursoDto
{
    public string Nombre { get; set; } = string.Empty;
    public string Seccion { get; set; } = string.Empty;
}
