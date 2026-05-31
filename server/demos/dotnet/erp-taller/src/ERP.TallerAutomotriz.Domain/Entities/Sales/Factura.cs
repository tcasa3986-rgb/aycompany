using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Entities.Customers;
using ERP.TallerAutomotriz.Domain.Entities.Workshop;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Sales;

public class Factura : AuditableEntity
{
    public TipoComprobante Tipo { get; set; } = TipoComprobante.Factura;
    public string Serie { get; set; } = "F001";
    public string Numero { get; set; } = string.Empty;
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
    public DateTime FechaVencimiento { get; set; }

    public int ClienteId { get; set; }
    public Cliente? Cliente { get; set; }

    public int? OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }

    public decimal Subtotal { get; set; }
    public decimal Descuento { get; set; }
    public decimal BaseImponible { get; set; }
    public decimal PorcentajeImpuesto { get; set; } = 18; // IGV/IVA
    public decimal Impuesto { get; set; }
    public decimal Total { get; set; }
    public decimal MontoPagado { get; set; }
    public decimal SaldoPendiente { get; set; }

    public EstadoFactura Estado { get; set; } = EstadoFactura.Borrador;
    public string? Observaciones { get; set; }
    public string? UrlPdf { get; set; }

    public ICollection<DetalleFactura> Detalles { get; set; } = new List<DetalleFactura>();
    public ICollection<Pago> Pagos { get; set; } = new List<Pago>();
}

public class DetalleFactura : BaseEntity
{
    public int FacturaId { get; set; }
    public Factura? Factura { get; set; }
    public string Descripcion { get; set; } = string.Empty;
    public string? CodigoItem { get; set; }
    public decimal Cantidad { get; set; }
    public decimal PrecioUnitario { get; set; }
    public decimal Descuento { get; set; }
    public decimal Subtotal { get; set; }
    public string Tipo { get; set; } = "Servicio"; // Servicio, Repuesto
}

public class Pago : AuditableEntity
{
    public int FacturaId { get; set; }
    public Factura? Factura { get; set; }
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
    public FormaPago FormaPago { get; set; }
    public decimal Monto { get; set; }
    public string? NumeroReferencia { get; set; }
    public string? Observaciones { get; set; }
    public int? CajaId { get; set; }
    public Caja? Caja { get; set; }
}

public class Caja : AuditableEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public bool Abierta { get; set; }
    public DateTime? FechaApertura { get; set; }
    public DateTime? FechaCierre { get; set; }
    public decimal MontoApertura { get; set; }
    public decimal MontoCierre { get; set; }
    public string? UsuarioApertura { get; set; }
    public string? UsuarioCierre { get; set; }
    public string? Observaciones { get; set; }
}

public class Cotizacion : AuditableEntity
{
    public string Numero { get; set; } = string.Empty;
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
    public DateTime ValidaHasta { get; set; }
    public int ClienteId { get; set; }
    public Cliente? Cliente { get; set; }
    public int? OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
    public decimal Subtotal { get; set; }
    public decimal Impuesto { get; set; }
    public decimal Total { get; set; }
    public string Estado { get; set; } = "Pendiente"; // Pendiente, Aprobada, Rechazada, Convertida
    public DateTime? FechaAprobacion { get; set; }
    public string? AprobadaPor { get; set; }
    public string? Observaciones { get; set; }
    public ICollection<DetalleCotizacion> Detalles { get; set; } = new List<DetalleCotizacion>();
}

public class DetalleCotizacion : BaseEntity
{
    public int CotizacionId { get; set; }
    public Cotizacion? Cotizacion { get; set; }
    public string Descripcion { get; set; } = string.Empty;
    public decimal Cantidad { get; set; }
    public decimal PrecioUnitario { get; set; }
    public decimal Subtotal { get; set; }
    public string Tipo { get; set; } = "Servicio";
}
