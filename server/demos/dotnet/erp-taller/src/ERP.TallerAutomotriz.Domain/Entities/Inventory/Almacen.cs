using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Inventory;

public class Almacen : AuditableEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Direccion { get; set; }
    public string? Responsable { get; set; }
    public bool EsPrincipal { get; set; }
}

public class StockAlmacen : BaseEntity
{
    public int RepuestoId { get; set; }
    public Repuesto? Repuesto { get; set; }
    public int AlmacenId { get; set; }
    public Almacen? Almacen { get; set; }
    public decimal Cantidad { get; set; }
    public string? Ubicacion { get; set; }
}

public class MovimientoInventario : BaseEntity
{
    public int RepuestoId { get; set; }
    public Repuesto? Repuesto { get; set; }
    public int AlmacenId { get; set; }
    public Almacen? Almacen { get; set; }
    public TipoMovimientoInventario Tipo { get; set; }
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
    public decimal Cantidad { get; set; }
    public decimal CostoUnitario { get; set; }
    public decimal SaldoAnterior { get; set; }
    public decimal SaldoNuevo { get; set; }
    public string? NumeroDocumento { get; set; } // OC, OT, NC, etc.
    public string? TipoDocumento { get; set; }
    public int? DocumentoReferenciaId { get; set; }
    public string? Lote { get; set; }
    public string? NumeroSerie { get; set; }
    public DateTime? FechaVencimiento { get; set; }
    public string? Justificacion { get; set; }
    public string? Usuario { get; set; }
}
