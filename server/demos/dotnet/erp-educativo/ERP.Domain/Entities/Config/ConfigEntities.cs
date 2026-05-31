using ERP.Domain.Common;

namespace ERP.Domain.Entities.Config;

public class Institucion : BaseEntity
{
    public string Nombre { get; set; } = string.Empty;
    public string? NombreCorto { get; set; }
    public string? RUC { get; set; }
    public string? Direccion { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public string? SitioWeb { get; set; }
    public string? Logo { get; set; }
    public string? Slogan { get; set; }
    public string? Departamento { get; set; }
    public string? Provincia { get; set; }
    public string? Distrito { get; set; }
    public string? Pais { get; set; } = "Perú";
    public string? Moneda { get; set; } = "PEN";
    public string? SimboloMoneda { get; set; } = "S/";
    public string? ColorPrimario { get; set; } = "#6B21A8";
    public string? ColorSecundario { get; set; } = "#F97316";
}

public class PeriodoAcademico : BaseEntity
{
    public string Nombre { get; set; } = string.Empty;
    public string Codigo { get; set; } = string.Empty;
    public DateTime FechaInicio { get; set; }
    public DateTime FechaFin { get; set; }
    public bool EsActual { get; set; }
    public int AnioAcademico { get; set; }
    public ICollection<CicloAcademico> Ciclos { get; set; } = new List<CicloAcademico>();
}

public class CicloAcademico : BaseEntity
{
    public int PeriodoAcademicoId { get; set; }
    public PeriodoAcademico? PeriodoAcademico { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public string Descripcion { get; set; } = string.Empty;
    public int OrdenNumero { get; set; }
}

public class Turno : BaseEntity
{
    public string Nombre { get; set; } = string.Empty;
    public TimeOnly HoraInicio { get; set; }
    public TimeOnly HoraFin { get; set; }
}

public class Parametro : BaseEntity
{
    public string Clave { get; set; } = string.Empty;
    public string Valor { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public string? Grupo { get; set; }
}

public class Aula : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public int Capacidad { get; set; }
    public string? Ubicacion { get; set; }
    public string? TipoAmbiente { get; set; }  // Aula, Laboratorio, Auditorio, etc.
    public bool TieneProyector { get; set; }
    public bool TieneInternet { get; set; }
}
