using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Entities.Inventory;
using ERP.TallerAutomotriz.Domain.Entities.Personnel;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Workshop;

public class DetalleOTServicio : BaseEntity
{
    public int OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
    public int ServicioId { get; set; }
    public Servicio? Servicio { get; set; }
    public string? Descripcion { get; set; }
    public int Cantidad { get; set; } = 1;
    public decimal PrecioUnitario { get; set; }
    public decimal Descuento { get; set; }
    public decimal Subtotal { get; set; }
    public int? TecnicoId { get; set; }
    public Tecnico? Tecnico { get; set; }
    public int TiempoRealMinutos { get; set; }
    public DateTime? FechaInicio { get; set; }
    public DateTime? FechaFin { get; set; }
    public bool Completado { get; set; }
    public string? Notas { get; set; }
}

public class DetalleOTRepuesto : BaseEntity
{
    public int OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
    public int RepuestoId { get; set; }
    public Repuesto? Repuesto { get; set; }
    public decimal Cantidad { get; set; }
    public decimal PrecioUnitario { get; set; }
    public decimal CostoUnitario { get; set; }
    public decimal Descuento { get; set; }
    public decimal Subtotal { get; set; }
    public bool Entregado { get; set; }
    public DateTime? FechaConsumo { get; set; }
    public string? Notas { get; set; }
}

public class HistorialEstadoOT : BaseEntity
{
    public int OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
    public EstadoOT EstadoAnterior { get; set; }
    public EstadoOT EstadoNuevo { get; set; }
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
    public string? Usuario { get; set; }
    public string? Comentario { get; set; }
}

public class FotoOT : BaseEntity
{
    public int OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
    public string Url { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public string Categoria { get; set; } = "Ingreso"; // Ingreso, Trabajo, Entrega
    public DateTime FechaCaptura { get; set; } = DateTime.UtcNow;
    public string? CapturadoPor { get; set; }
}

public class ChecklistInspeccion : BaseEntity
{
    public int OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
    public string Item { get; set; } = string.Empty; // Aceite, Frenos, Llantas, etc.
    public string Estado { get; set; } = "OK"; // OK, Atencion, Critico
    public string? Observacion { get; set; }
}

public class TecnicoOT : BaseEntity
{
    public int OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
    public int TecnicoId { get; set; }
    public Tecnico? Tecnico { get; set; }
    public string? Rol { get; set; } // Principal, Asistente, etc.
    public DateTime FechaAsignacion { get; set; } = DateTime.UtcNow;
}

public class ControlCalidad : AuditableEntity
{
    public int OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
    public DateTime FechaInspeccion { get; set; } = DateTime.UtcNow;
    public string? InspectorId { get; set; }
    public EstadoQC Estado { get; set; }
    public bool PruebaRutaRealizada { get; set; }
    public int KilometrajeSalida { get; set; }
    public int KilometrajeRegreso { get; set; }
    public string? Observaciones { get; set; }
}
