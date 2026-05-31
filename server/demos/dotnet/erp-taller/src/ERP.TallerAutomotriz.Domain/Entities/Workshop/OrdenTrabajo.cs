using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Entities.Customers;
using ERP.TallerAutomotriz.Domain.Entities.Personnel;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Workshop;

public class OrdenTrabajo : AuditableEntity
{
    public string Numero { get; set; } = string.Empty; // Correlativo
    public DateTime FechaIngreso { get; set; } = DateTime.UtcNow;
    public DateTime? FechaEntregaEstimada { get; set; }
    public DateTime? FechaEntregaReal { get; set; }

    public int ClienteId { get; set; }
    public Cliente? Cliente { get; set; }

    public int VehiculoId { get; set; }
    public Vehiculo? Vehiculo { get; set; }

    public int KilometrajeIngreso { get; set; }
    public string? FallasReportadasCliente { get; set; }
    public string? SintomasDiagnosticados { get; set; }
    public string? ObservacionesIngreso { get; set; }

    public EstadoOT Estado { get; set; } = EstadoOT.Recibido;
    public PrioridadOT Prioridad { get; set; } = PrioridadOT.Normal;

    public int? TecnicoPrincipalId { get; set; }
    public Tecnico? TecnicoPrincipal { get; set; }

    public decimal SubtotalManoObra { get; set; }
    public decimal SubtotalRepuestos { get; set; }
    public decimal SubtotalServiciosExternos { get; set; }
    public decimal Descuento { get; set; }
    public decimal Impuesto { get; set; }
    public decimal Total { get; set; }

    public string? CodigoQR { get; set; }
    public bool PresupuestoAprobado { get; set; }
    public DateTime? FechaAprobacionPresupuesto { get; set; }
    public string? AprobadoPor { get; set; }

    public ICollection<DetalleOTServicio> Servicios { get; set; } = new List<DetalleOTServicio>();
    public ICollection<DetalleOTRepuesto> Repuestos { get; set; } = new List<DetalleOTRepuesto>();
    public ICollection<HistorialEstadoOT> Historial { get; set; } = new List<HistorialEstadoOT>();
    public ICollection<FotoOT> Fotos { get; set; } = new List<FotoOT>();
    public ICollection<ChecklistInspeccion> Checklist { get; set; } = new List<ChecklistInspeccion>();
    public ICollection<TecnicoOT> TecnicosAsignados { get; set; } = new List<TecnicoOT>();
}
