using System.ComponentModel.DataAnnotations;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Application.DTOs;

// =============== SERVICIOS ===============

public class ServicioListDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public TipoServicio Tipo { get; set; }
    public string? Categoria { get; set; }
    public decimal PrecioEstandar { get; set; }
    public int TiempoEstimadoMinutos { get; set; }
    public bool EsPaquete { get; set; }
    public bool Activo { get; set; }
}

public class ServicioFormDto
{
    public int Id { get; set; }
    public string? Codigo { get; set; }

    [Required(ErrorMessage = "Nombre requerido")]
    [StringLength(200)]
    public string Nombre { get; set; } = string.Empty;

    public string? Descripcion { get; set; }
    public TipoServicio Tipo { get; set; } = TipoServicio.MantenimientoPreventivo;
    public int? CategoriaId { get; set; }

    [Range(0, double.MaxValue)]
    public decimal PrecioEstandar { get; set; }

    [Range(0, int.MaxValue)]
    public int TiempoEstimadoMinutos { get; set; } = 60;

    [Range(0, double.MaxValue)]
    public decimal CostoManoObra { get; set; }

    public bool EsPaquete { get; set; }
    public string? Notas { get; set; }
}

public class CategoriaServicioDto
{
    public int Id { get; set; }
    public string Nombre { get; set; } = string.Empty;
}

// =============== ÓRDENES DE TRABAJO ===============

public class OrdenTrabajoListDto
{
    public int Id { get; set; }
    public string Numero { get; set; } = string.Empty;
    public DateTime FechaIngreso { get; set; }
    public DateTime? FechaEntregaEstimada { get; set; }
    public string Cliente { get; set; } = string.Empty;
    public string Vehiculo { get; set; } = string.Empty;
    public string Placa { get; set; } = string.Empty;
    public EstadoOT Estado { get; set; }
    public PrioridadOT Prioridad { get; set; }
    public string? Tecnico { get; set; }
    public decimal Total { get; set; }
}

public class OrdenTrabajoFormDto
{
    public int Id { get; set; }
    public string? Numero { get; set; }

    [Required] public int ClienteId { get; set; }
    [Required] public int VehiculoId { get; set; }

    public DateTime? FechaEntregaEstimada { get; set; } = DateTime.Now.AddDays(1);
    public int KilometrajeIngreso { get; set; }
    public string? FallasReportadasCliente { get; set; }
    public string? SintomasDiagnosticados { get; set; }
    public string? ObservacionesIngreso { get; set; }
    public PrioridadOT Prioridad { get; set; } = PrioridadOT.Normal;
    public int? TecnicoPrincipalId { get; set; }

    public List<int> ServiciosIds { get; set; } = new();
}

public class OrdenTrabajoDetalleDto
{
    public int Id { get; set; }
    public string Numero { get; set; } = string.Empty;
    public DateTime FechaIngreso { get; set; }
    public DateTime? FechaEntregaEstimada { get; set; }
    public DateTime? FechaEntregaReal { get; set; }
    public EstadoOT Estado { get; set; }
    public PrioridadOT Prioridad { get; set; }

    public int ClienteId { get; set; }
    public string Cliente { get; set; } = string.Empty;
    public string DocumentoCliente { get; set; } = string.Empty;
    public string? TelefonoCliente { get; set; }

    public int VehiculoId { get; set; }
    public string Placa { get; set; } = string.Empty;
    public string Marca { get; set; } = string.Empty;
    public string Modelo { get; set; } = string.Empty;
    public int Anio { get; set; }
    public int KilometrajeIngreso { get; set; }

    public string? Tecnico { get; set; }
    public string? FallasReportadasCliente { get; set; }
    public string? SintomasDiagnosticados { get; set; }

    public decimal SubtotalManoObra { get; set; }
    public decimal SubtotalRepuestos { get; set; }
    public decimal Descuento { get; set; }
    public decimal Impuesto { get; set; }
    public decimal Total { get; set; }

    public List<ServicioOTDto> Servicios { get; set; } = new();
    public List<HistorialEstadoDto> Historial { get; set; } = new();
}

public class ServicioOTDto
{
    public int Id { get; set; }
    public int ServicioId { get; set; }
    public string Servicio { get; set; } = string.Empty;
    public int Cantidad { get; set; }
    public decimal PrecioUnitario { get; set; }
    public decimal Subtotal { get; set; }
    public bool Completado { get; set; }
}

public class HistorialEstadoDto
{
    public DateTime Fecha { get; set; }
    public EstadoOT EstadoAnterior { get; set; }
    public EstadoOT EstadoNuevo { get; set; }
    public string? Usuario { get; set; }
    public string? Comentario { get; set; }
}

public class CambioEstadoDto
{
    public int OrdenTrabajoId { get; set; }
    public EstadoOT NuevoEstado { get; set; }
    public string? Comentario { get; set; }
}

// =============== TÉCNICOS (lite) ===============

public class TecnicoListDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string NombreCompleto { get; set; } = string.Empty;
    public NivelExperiencia Nivel { get; set; }
    public string? Especialidades { get; set; }
    public bool Activo { get; set; }
}

// =============== CITAS ===============

public class CitaListDto
{
    public int Id { get; set; }
    public DateTime FechaHora { get; set; }
    public int DuracionMinutos { get; set; }
    public int ClienteId { get; set; }
    public string Cliente { get; set; } = string.Empty;
    public int? VehiculoId { get; set; }
    public string? Vehiculo { get; set; }
    public string? Placa { get; set; }
    public string? Servicio { get; set; }
    public string? Tecnico { get; set; }
    public EstadoCita Estado { get; set; }
    public string? Comentarios { get; set; }
    public int? OrdenTrabajoId { get; set; }
}

public class CitaFormDto
{
    public int Id { get; set; }

    [Required] public int ClienteId { get; set; }
    public int? VehiculoId { get; set; }

    [Required] public DateTime FechaHora { get; set; } = DateTime.Now.AddDays(1).Date.AddHours(9);

    [Range(15, 480)]
    public int DuracionMinutos { get; set; } = 60;

    public int? ServicioId { get; set; }
    public int? TecnicoPreferidoId { get; set; }
    public EstadoCita Estado { get; set; } = EstadoCita.Pendiente;
    public string? Comentarios { get; set; }
}
