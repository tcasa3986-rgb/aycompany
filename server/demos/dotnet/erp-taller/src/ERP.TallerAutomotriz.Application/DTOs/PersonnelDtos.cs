using System.ComponentModel.DataAnnotations;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Application.DTOs;

// =============== TÉCNICOS ===============

public class TecnicoFullDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombres { get; set; } = string.Empty;
    public string Apellidos { get; set; } = string.Empty;
    public string DocumentoIdentidad { get; set; } = string.Empty;
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public DateTime FechaIngreso { get; set; }
    public NivelExperiencia Nivel { get; set; }
    public decimal TarifaHora { get; set; }
    public decimal PorcentajeComision { get; set; }
    public string? Especialidades { get; set; }
    public bool Activo { get; set; }
    public int OTsAsignadas { get; set; }
    public string NombreCompleto => $"{Nombres} {Apellidos}";
}

public class TecnicoFormDto
{
    public int Id { get; set; }
    public string? Codigo { get; set; }

    [Required(ErrorMessage = "Nombres requerido")]
    [StringLength(100)]
    public string Nombres { get; set; } = string.Empty;

    [Required(ErrorMessage = "Apellidos requerido")]
    [StringLength(100)]
    public string Apellidos { get; set; } = string.Empty;

    [Required(ErrorMessage = "DNI requerido")]
    [StringLength(20)]
    public string DocumentoIdentidad { get; set; } = string.Empty;

    [StringLength(20)]
    public string? Telefono { get; set; }

    [EmailAddress]
    [StringLength(150)]
    public string? Email { get; set; }

    [StringLength(250)]
    public string? Direccion { get; set; }

    public DateTime FechaIngreso { get; set; } = DateTime.Today;
    public NivelExperiencia Nivel { get; set; } = NivelExperiencia.Junior;

    [Range(0, double.MaxValue)]
    public decimal TarifaHora { get; set; }

    [Range(0, 100)]
    public decimal PorcentajeComision { get; set; }

    public string? Especialidades { get; set; }
}

// =============== ASISTENCIA ===============

public class AsistenciaListDto
{
    public int Id { get; set; }
    public int TecnicoId { get; set; }
    public string Tecnico { get; set; } = string.Empty;
    public DateTime Fecha { get; set; }
    public DateTime? HoraEntrada { get; set; }
    public DateTime? HoraSalida { get; set; }
    public decimal HorasTrabajadas { get; set; }
    public decimal HorasExtras { get; set; }
    public string? Observaciones { get; set; }
}

public class MarcarEntradaSalidaDto
{
    public int TecnicoId { get; set; }
    public bool EsEntrada { get; set; }
    public string? Observaciones { get; set; }
}

// =============== COMISIONES ===============

public class ComisionListDto
{
    public int Id { get; set; }
    public int TecnicoId { get; set; }
    public string Tecnico { get; set; } = string.Empty;
    public DateTime Fecha { get; set; }
    public int? OrdenTrabajoId { get; set; }
    public string? NumeroOT { get; set; }
    public decimal MontoBase { get; set; }
    public decimal Porcentaje { get; set; }
    public decimal MontoComision { get; set; }
    public bool Pagada { get; set; }
    public DateTime? FechaPago { get; set; }
}

public class ComisionFormDto
{
    [Required] public int TecnicoId { get; set; }
    public int? OrdenTrabajoId { get; set; }
    public DateTime Fecha { get; set; } = DateTime.Today;

    [Range(0, double.MaxValue)]
    public decimal MontoBase { get; set; }

    [Range(0, 100)]
    public decimal Porcentaje { get; set; }
}
