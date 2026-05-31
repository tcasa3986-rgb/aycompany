using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Workshop;

public class Servicio : AuditableEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public TipoServicio Tipo { get; set; }
    public int? CategoriaId { get; set; }
    public CategoriaServicio? Categoria { get; set; }
    public decimal PrecioEstandar { get; set; }
    public int TiempoEstimadoMinutos { get; set; }
    public decimal CostoManoObra { get; set; }
    public bool EsPaquete { get; set; }
    public string? Notas { get; set; }
}

public class CategoriaServicio : AuditableEntity
{
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public int? CategoriaPadreId { get; set; }
    public CategoriaServicio? CategoriaPadre { get; set; }
    public ICollection<Servicio> Servicios { get; set; } = new List<Servicio>();
}
