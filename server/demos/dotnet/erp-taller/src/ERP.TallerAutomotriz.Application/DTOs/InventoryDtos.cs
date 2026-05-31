using System.ComponentModel.DataAnnotations;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Application.DTOs;

// =================== REPUESTOS ===================

public class RepuestoListDto
{
    public int Id { get; set; }
    public string CodigoInterno { get; set; } = string.Empty;
    public string? CodigoOEM { get; set; }
    public string Descripcion { get; set; } = string.Empty;
    public string? Categoria { get; set; }
    public string UnidadMedida { get; set; } = "UND";
    public decimal StockActual { get; set; }
    public decimal StockMinimo { get; set; }
    public decimal PrecioVenta { get; set; }
    public decimal CostoPromedio { get; set; }
    public string? Ubicacion { get; set; }
    public bool Activo { get; set; }
    public bool BajoStock => StockActual <= StockMinimo;
}

public class RepuestoFormDto
{
    public int Id { get; set; }

    public string? CodigoInterno { get; set; } // null = autogenera

    [StringLength(30)]
    public string? CodigoOEM { get; set; }

    [StringLength(50)]
    public string? CodigoBarras { get; set; }

    [Required(ErrorMessage = "Descripción requerida")]
    [StringLength(200)]
    public string Descripcion { get; set; } = string.Empty;

    public string? DescripcionLarga { get; set; }

    public int? CategoriaId { get; set; }

    [StringLength(10)]
    public string UnidadMedida { get; set; } = "UND";

    public decimal StockMinimo { get; set; }
    public decimal StockMaximo { get; set; }

    [Required(ErrorMessage = "Precio de venta requerido")]
    public decimal PrecioVenta { get; set; }

    public decimal CostoPromedio { get; set; }

    [StringLength(50)]
    public string? Ubicacion { get; set; }

    public MetodoCosteo MetodoCosteo { get; set; } = MetodoCosteo.PromedioPonderado;

    public bool ManejaLote { get; set; }
    public bool ManejaSerie { get; set; }
    public bool TieneGarantia { get; set; }
    public int? MesesGarantia { get; set; }
}

public class CategoriaRepuestoDto
{
    public int Id { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public int? CategoriaPadreId { get; set; }
}

// =================== ALMACENES ===================

public class AlmacenDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Direccion { get; set; }
    public string? Responsable { get; set; }
    public bool EsPrincipal { get; set; }
    public bool Activo { get; set; }
}

public class AlmacenFormDto
{
    public int Id { get; set; }

    [Required(ErrorMessage = "Código requerido")]
    [StringLength(20)]
    public string Codigo { get; set; } = string.Empty;

    [Required(ErrorMessage = "Nombre requerido")]
    [StringLength(100)]
    public string Nombre { get; set; } = string.Empty;

    [StringLength(250)]
    public string? Direccion { get; set; }

    [StringLength(100)]
    public string? Responsable { get; set; }

    public bool EsPrincipal { get; set; }
}

// =================== MOVIMIENTOS ===================

public class MovimientoListDto
{
    public int Id { get; set; }
    public DateTime Fecha { get; set; }
    public TipoMovimientoInventario Tipo { get; set; }
    public int RepuestoId { get; set; }
    public string CodigoRepuesto { get; set; } = string.Empty;
    public string DescripcionRepuesto { get; set; } = string.Empty;
    public string Almacen { get; set; } = string.Empty;
    public decimal Cantidad { get; set; }
    public decimal CostoUnitario { get; set; }
    public decimal SaldoNuevo { get; set; }
    public string? NumeroDocumento { get; set; }
    public string? TipoDocumento { get; set; }
    public string? Justificacion { get; set; }
    public string? Usuario { get; set; }
}

public class MovimientoFormDto
{
    [Required] public int RepuestoId { get; set; }
    [Required] public int AlmacenId { get; set; }
    [Required] public TipoMovimientoInventario Tipo { get; set; } = TipoMovimientoInventario.AjustePositivo;

    [Range(0.0001, double.MaxValue, ErrorMessage = "Cantidad debe ser mayor a 0")]
    public decimal Cantidad { get; set; }

    public decimal CostoUnitario { get; set; }

    [StringLength(50)]
    public string? NumeroDocumento { get; set; }

    [StringLength(50)]
    public string? TipoDocumento { get; set; }

    public string? Justificacion { get; set; }
}
