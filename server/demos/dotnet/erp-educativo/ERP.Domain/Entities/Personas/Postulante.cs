using ERP.Domain.Common;
using ERP.Domain.Enums;
using ERP.Domain.Entities.Academico;

namespace ERP.Domain.Entities.Personas;

public class Postulante : BaseEntity
{
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
    
    // Datos de Postulación
    public int CarreraId { get; set; }
    public Carrera? Carrera { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public ERP.Domain.Entities.Config.PeriodoAcademico? PeriodoAcademico { get; set; }
    
    public EstadoPostulante Estado { get; set; } = EstadoPostulante.Registrado;
    public DateTime FechaRegistro { get; set; } = DateTime.Now;
    
    public decimal? PuntajeExamen { get; set; }
    public string? Observaciones { get; set; }
}
