using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Entities.Inventory;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Purchases;

public class Proveedor : AuditableEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string RazonSocial { get; set; } = string.Empty;
    public string? NombreComercial { get; set; }
    public string DocumentoIdentidad { get; set; } = string.Empty; // RUC
    public string? Direccion { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public string? Contacto { get; set; }
    public int DiasCredito { get; set; }
    public int DiasEntrega { get; set; }
    public decimal CalificacionPrecio { get; set; }
    public decimal CalificacionTiempo { get; set; }
    public decimal CalificacionCalidad { get; set; }
    public string? Notas { get; set; }
}

public class OrdenCompra : AuditableEntity
{
    public string Numero { get; set; } = string.Empty;
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
    public DateTime? FechaEntregaEsperada { get; set; }
    public int ProveedorId { get; set; }
    public Proveedor? Proveedor { get; set; }
    public int? AlmacenDestinoId { get; set; }
    public Almacen? AlmacenDestino { get; set; }
    public decimal Subtotal { get; set; }
    public decimal Impuesto { get; set; }
    public decimal Total { get; set; }
    public EstadoOrdenCompra Estado { get; set; } = EstadoOrdenCompra.Borrador;
    public string? AprobadaPor { get; set; }
    public DateTime? FechaAprobacion { get; set; }
    public string? Observaciones { get; set; }
    public ICollection<DetalleOrdenCompra> Detalles { get; set; } = new List<DetalleOrdenCompra>();
}

public class DetalleOrdenCompra : BaseEntity
{
    public int OrdenCompraId { get; set; }
    public OrdenCompra? OrdenCompra { get; set; }
    public int RepuestoId { get; set; }
    public Repuesto? Repuesto { get; set; }
    public decimal Cantidad { get; set; }
    public decimal CantidadRecibida { get; set; }
    public decimal PrecioUnitario { get; set; }
    public decimal Descuento { get; set; }
    public decimal Subtotal { get; set; }
}

public class CuentaPagar : AuditableEntity
{
    public int ProveedorId { get; set; }
    public Proveedor? Proveedor { get; set; }
    public int? OrdenCompraId { get; set; }
    public OrdenCompra? OrdenCompra { get; set; }
    public string NumeroFactura { get; set; } = string.Empty;
    public DateTime FechaEmision { get; set; }
    public DateTime FechaVencimiento { get; set; }
    public decimal Monto { get; set; }
    public decimal MontoPagado { get; set; }
    public decimal Saldo { get; set; }
    public EstadoCuentaPagar Estado { get; set; } = EstadoCuentaPagar.Pendiente;
    public string? Observaciones { get; set; }
}
