using System.ComponentModel.DataAnnotations;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Application.DTOs;

// =============== PROVEEDORES ===============

public class ProveedorListDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string RazonSocial { get; set; } = string.Empty;
    public string DocumentoIdentidad { get; set; } = string.Empty;
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public int DiasCredito { get; set; }
    public decimal CalificacionPromedio { get; set; }
    public bool Activo { get; set; }
}

public class ProveedorFormDto
{
    public int Id { get; set; }
    public string? Codigo { get; set; }

    [Required, StringLength(200)]
    public string RazonSocial { get; set; } = string.Empty;

    [StringLength(150)]
    public string? NombreComercial { get; set; }

    [Required, StringLength(20)]
    public string DocumentoIdentidad { get; set; } = string.Empty;

    [StringLength(250)]
    public string? Direccion { get; set; }

    [StringLength(20)]
    public string? Telefono { get; set; }

    [EmailAddress, StringLength(150)]
    public string? Email { get; set; }

    [StringLength(150)]
    public string? Contacto { get; set; }

    [Range(0, int.MaxValue)] public int DiasCredito { get; set; }
    [Range(0, int.MaxValue)] public int DiasEntrega { get; set; }

    [Range(0, 5)] public decimal CalificacionPrecio { get; set; } = 3;
    [Range(0, 5)] public decimal CalificacionTiempo { get; set; } = 3;
    [Range(0, 5)] public decimal CalificacionCalidad { get; set; } = 3;

    public string? Notas { get; set; }
}

// =============== ÓRDENES DE COMPRA ===============

public class OrdenCompraListDto
{
    public int Id { get; set; }
    public string Numero { get; set; } = string.Empty;
    public DateTime Fecha { get; set; }
    public DateTime? FechaEntregaEsperada { get; set; }
    public string Proveedor { get; set; } = string.Empty;
    public decimal Total { get; set; }
    public EstadoOrdenCompra Estado { get; set; }
}

public class OrdenCompraDetalleDto
{
    public int Id { get; set; }
    public string Numero { get; set; } = string.Empty;
    public DateTime Fecha { get; set; }
    public DateTime? FechaEntregaEsperada { get; set; }
    public int ProveedorId { get; set; }
    public string Proveedor { get; set; } = string.Empty;
    public string DocumentoProveedor { get; set; } = string.Empty;
    public int? AlmacenDestinoId { get; set; }
    public string? AlmacenDestino { get; set; }
    public decimal Subtotal { get; set; }
    public decimal Impuesto { get; set; }
    public decimal Total { get; set; }
    public EstadoOrdenCompra Estado { get; set; }
    public string? Observaciones { get; set; }
    public List<DetalleOCDto> Items { get; set; } = new();
}

public class DetalleOCDto
{
    public int Id { get; set; }
    public int RepuestoId { get; set; }
    public string CodigoRepuesto { get; set; } = string.Empty;
    public string DescripcionRepuesto { get; set; } = string.Empty;
    public decimal Cantidad { get; set; }
    public decimal CantidadRecibida { get; set; }
    public decimal PrecioUnitario { get; set; }
    public decimal Subtotal { get; set; }
}

public class OrdenCompraFormDto
{
    public int Id { get; set; }
    public string? Numero { get; set; }
    public DateTime Fecha { get; set; } = DateTime.Today;
    public DateTime? FechaEntregaEsperada { get; set; } = DateTime.Today.AddDays(3);

    [Required] public int ProveedorId { get; set; }
    public int? AlmacenDestinoId { get; set; }
    public string? Observaciones { get; set; }

    public List<DetalleOCFormDto> Items { get; set; } = new();
}

public class DetalleOCFormDto
{
    [Required] public int RepuestoId { get; set; }
    public string? Descripcion { get; set; }

    [Range(0.01, double.MaxValue)]
    public decimal Cantidad { get; set; } = 1;

    [Range(0, double.MaxValue)]
    public decimal PrecioUnitario { get; set; }
}

public class RecepcionMercaderiaDto
{
    public int OrdenCompraId { get; set; }
    public List<RecepcionItemDto> Items { get; set; } = new();
    public string? Observaciones { get; set; }
}

public class RecepcionItemDto
{
    public int DetalleId { get; set; }
    public decimal CantidadRecibida { get; set; }
}

// =============== CUENTAS POR PAGAR ===============

public class CuentaPagarDto
{
    public int Id { get; set; }
    public int ProveedorId { get; set; }
    public string Proveedor { get; set; } = string.Empty;
    public int? OrdenCompraId { get; set; }
    public string? NumeroOC { get; set; }
    public string NumeroFactura { get; set; } = string.Empty;
    public DateTime FechaEmision { get; set; }
    public DateTime FechaVencimiento { get; set; }
    public decimal Monto { get; set; }
    public decimal MontoPagado { get; set; }
    public decimal Saldo { get; set; }
    public EstadoCuentaPagar Estado { get; set; }
    public bool Vencida => Estado != EstadoCuentaPagar.Pagada && FechaVencimiento.Date < DateTime.Today;
    public int DiasVencida => (DateTime.Today - FechaVencimiento.Date).Days;
}

public class CuentaPagarFormDto
{
    [Required] public int ProveedorId { get; set; }
    public int? OrdenCompraId { get; set; }

    [Required, StringLength(50)]
    public string NumeroFactura { get; set; } = string.Empty;

    public DateTime FechaEmision { get; set; } = DateTime.Today;
    public DateTime FechaVencimiento { get; set; } = DateTime.Today.AddDays(30);

    [Range(0.01, double.MaxValue)]
    public decimal Monto { get; set; }
}
