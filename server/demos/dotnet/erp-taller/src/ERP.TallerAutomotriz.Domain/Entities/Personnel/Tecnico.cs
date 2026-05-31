using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Personnel;

public class Tecnico : AuditableEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string? UsuarioIdentityId { get; set; } // Vinculado a AspNetUsers
    public string Nombres { get; set; } = string.Empty;
    public string Apellidos { get; set; } = string.Empty;
    public string DocumentoIdentidad { get; set; } = string.Empty;
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public string? Direccion { get; set; }
    public DateTime FechaIngreso { get; set; }
    public NivelExperiencia Nivel { get; set; } = NivelExperiencia.Junior;
    public decimal TarifaHora { get; set; }
    public decimal PorcentajeComision { get; set; }
    public string? Especialidades { get; set; } // JSON o csv
    public string? UrlFoto { get; set; }

    public ICollection<RegistroAsistencia> Asistencias { get; set; } = new List<RegistroAsistencia>();
}

public class RegistroAsistencia : BaseEntity
{
    public int TecnicoId { get; set; }
    public Tecnico? Tecnico { get; set; }
    public DateTime Fecha { get; set; }
    public DateTime? HoraEntrada { get; set; }
    public DateTime? HoraSalida { get; set; }
    public decimal HorasTrabajadas { get; set; }
    public decimal HorasExtras { get; set; }
    public string? Observaciones { get; set; }
}

public class Comision : BaseEntity
{
    public int TecnicoId { get; set; }
    public Tecnico? Tecnico { get; set; }
    public DateTime Fecha { get; set; }
    public int? OrdenTrabajoId { get; set; }
    public decimal MontoBase { get; set; }
    public decimal Porcentaje { get; set; }
    public decimal MontoComision { get; set; }
    public bool Pagada { get; set; }
    public DateTime? FechaPago { get; set; }
}
