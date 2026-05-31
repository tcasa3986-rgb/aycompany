using System.ComponentModel.DataAnnotations;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Application.DTOs;

// =============== FACTURAS ===============

public class FacturaListDto
{
    public int Id { get; set; }
    public TipoComprobante Tipo { get; set; }
    public string Serie { get; set; } = string.Empty;
    public string Numero { get; set; } = string.Empty;
    public DateTime Fecha { get; set; }
    public DateTime FechaVencimiento { get; set; }
    public string Cliente { get; set; } = string.Empty;
    public string? NumeroOT { get; set; }
    public decimal Total { get; set; }
    public decimal MontoPagado { get; set; }
    public decimal SaldoPendiente { get; set; }
    public EstadoFactura Estado { get; set; }
    public bool Vencida => Estado != EstadoFactura.Pagada && Estado != EstadoFactura.Anulada && FechaVencimiento < DateTime.Today;
}

public class FacturaDetalleDto
{
    public int Id { get; set; }
    public TipoComprobante Tipo { get; set; }
    public string Serie { get; set; } = string.Empty;
    public string Numero { get; set; } = string.Empty;
    public DateTime Fecha { get; set; }
    public DateTime FechaVencimiento { get; set; }
    public int ClienteId { get; set; }
    public string Cliente { get; set; } = string.Empty;
    public string DocumentoCliente { get; set; } = string.Empty;
    public string? DireccionCliente { get; set; }
    public int? OrdenTrabajoId { get; set; }
    public string? NumeroOT { get; set; }
    public decimal Subtotal { get; set; }
    public decimal Descuento { get; set; }
    public decimal BaseImponible { get; set; }
    public decimal PorcentajeImpuesto { get; set; }
    public decimal Impuesto { get; set; }
    public decimal Total { get; set; }
    public decimal MontoPagado { get; set; }
    public decimal SaldoPendiente { get; set; }
    public EstadoFactura Estado { get; set; }
    public string? Observaciones { get; set; }
    public List<FacturaItemDto> Detalles { get; set; } = new();
    public List<PagoListDto> Pagos { get; set; } = new();
}

public class FacturaItemDto
{
    public int Id { get; set; }
    public string Descripcion { get; set; } = string.Empty;
    public string? CodigoItem { get; set; }
    public decimal Cantidad { get; set; }
    public decimal PrecioUnitario { get; set; }
    public decimal Descuento { get; set; }
    public decimal Subtotal { get; set; }
    public string Tipo { get; set; } = "Servicio";
}

public class FacturaFormDto
{
    public int Id { get; set; }
    public TipoComprobante Tipo { get; set; } = TipoComprobante.Factura;
    public string Serie { get; set; } = "F001";
    public string? Numero { get; set; }
    public DateTime Fecha { get; set; } = DateTime.Today;
    public DateTime FechaVencimiento { get; set; } = DateTime.Today.AddDays(30);

    [Required] public int ClienteId { get; set; }
    public int? OrdenTrabajoId { get; set; }

    public decimal PorcentajeImpuesto { get; set; } = 18;
    public decimal Descuento { get; set; }
    public string? Observaciones { get; set; }

    public List<FacturaItemFormDto> Items { get; set; } = new();
}

public class FacturaItemFormDto
{
    [Required, StringLength(200)]
    public string Descripcion { get; set; } = string.Empty;
    public string? CodigoItem { get; set; }

    [Range(0.0001, double.MaxValue)]
    public decimal Cantidad { get; set; } = 1;

    [Range(0, double.MaxValue)]
    public decimal PrecioUnitario { get; set; }

    [Range(0, double.MaxValue)]
    public decimal Descuento { get; set; }

    public string Tipo { get; set; } = "Servicio";
}

// =============== COTIZACIONES ===============

public class CotizacionListDto
{
    public int Id { get; set; }
    public string Numero { get; set; } = string.Empty;
    public DateTime Fecha { get; set; }
    public DateTime ValidaHasta { get; set; }
    public string Cliente { get; set; } = string.Empty;
    public decimal Total { get; set; }
    public string Estado { get; set; } = "Pendiente";
    public bool Vencida => Estado == "Pendiente" && ValidaHasta < DateTime.Today;
}

public class CotizacionFormDto
{
    public int Id { get; set; }
    public string? Numero { get; set; }
    public DateTime Fecha { get; set; } = DateTime.Today;
    public DateTime ValidaHasta { get; set; } = DateTime.Today.AddDays(15);

    [Required] public int ClienteId { get; set; }
    public int? OrdenTrabajoId { get; set; }
    public string? Observaciones { get; set; }

    public List<FacturaItemFormDto> Items { get; set; } = new();
}

// =============== PAGOS ===============

public class PagoListDto
{
    public int Id { get; set; }
    public DateTime Fecha { get; set; }
    public FormaPago FormaPago { get; set; }
    public decimal Monto { get; set; }
    public string? NumeroReferencia { get; set; }
    public string? Observaciones { get; set; }
}

public class PagoFormDto
{
    [Required] public int FacturaId { get; set; }

    [Range(0.01, double.MaxValue, ErrorMessage = "Monto debe ser mayor a 0")]
    public decimal Monto { get; set; }

    public FormaPago FormaPago { get; set; } = FormaPago.Efectivo;
    public string? NumeroReferencia { get; set; }
    public string? Observaciones { get; set; }
    public int? CajaId { get; set; }
}

// =============== CAJA ===============

public class CajaEstadoDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public bool Abierta { get; set; }
    public DateTime? FechaApertura { get; set; }
    public string? UsuarioApertura { get; set; }
    public decimal MontoApertura { get; set; }

    // Resumen del día (calculado)
    public decimal IngresosEfectivo { get; set; }
    public decimal IngresosTarjeta { get; set; }
    public decimal IngresosTransferencia { get; set; }
    public decimal IngresosOtros { get; set; }
    public decimal TotalIngresos { get; set; }
    public decimal MontoEsperado { get; set; }
    public int CantidadPagos { get; set; }
}

public class AperturaCajaDto
{
    [Required] public int CajaId { get; set; }
    [Range(0, double.MaxValue)] public decimal MontoApertura { get; set; }
    public string? Observaciones { get; set; }
}

public class CierreCajaDto
{
    [Required] public int CajaId { get; set; }
    [Range(0, double.MaxValue)] public decimal MontoCierreFisico { get; set; }
    public string? Observaciones { get; set; }
}

// =============== CUENTAS POR COBRAR ===============

public class CuentaCobrarDto
{
    public int FacturaId { get; set; }
    public string Comprobante { get; set; } = string.Empty;
    public DateTime Fecha { get; set; }
    public DateTime FechaVencimiento { get; set; }
    public string Cliente { get; set; } = string.Empty;
    public decimal Total { get; set; }
    public decimal SaldoPendiente { get; set; }
    public int DiasVencido => (DateTime.Today - FechaVencimiento.Date).Days;
    public bool Vencida => SaldoPendiente > 0 && FechaVencimiento.Date < DateTime.Today;
}
