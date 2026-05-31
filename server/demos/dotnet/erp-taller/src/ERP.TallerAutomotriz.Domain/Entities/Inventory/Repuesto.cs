using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Inventory;

public class Repuesto : AuditableEntity
{
    public string CodigoInterno { get; set; } = string.Empty;
    public string? CodigoOEM { get; set; }
    public string? CodigoBarras { get; set; }
    public string Descripcion { get; set; } = string.Empty;
    public string? DescripcionLarga { get; set; }
    public int? CategoriaId { get; set; }
    public CategoriaRepuesto? Categoria { get; set; }
    public string UnidadMedida { get; set; } = "UND";
    public decimal StockActual { get; set; }
    public decimal StockMinimo { get; set; }
    public decimal StockMaximo { get; set; }
    public decimal PrecioVenta { get; set; }
    public decimal CostoPromedio { get; set; }
    public decimal CostoUltimo { get; set; }
    public MetodoCosteo MetodoCosteo { get; set; } = MetodoCosteo.PromedioPonderado;
    public string? Ubicacion { get; set; } // Estante/Fila/Nivel
    public bool ManejaLote { get; set; }
    public bool ManejaSerie { get; set; }
    public bool TieneGarantia { get; set; }
    public int? MesesGarantia { get; set; }
    public string? UrlImagen { get; set; }

    public ICollection<CompatibilidadRepuesto> Compatibilidades { get; set; } = new List<CompatibilidadRepuesto>();
    public ICollection<MovimientoInventario> Movimientos { get; set; } = new List<MovimientoInventario>();
}

public class CategoriaRepuesto : AuditableEntity
{
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public int? CategoriaPadreId { get; set; }
    public CategoriaRepuesto? CategoriaPadre { get; set; }
}

public class CompatibilidadRepuesto : BaseEntity
{
    public int RepuestoId { get; set; }
    public Repuesto? Repuesto { get; set; }
    public string Marca { get; set; } = string.Empty;
    public string? Modelo { get; set; }
    public int? AnioDesde { get; set; }
    public int? AnioHasta { get; set; }
    public string? Notas { get; set; }
}
