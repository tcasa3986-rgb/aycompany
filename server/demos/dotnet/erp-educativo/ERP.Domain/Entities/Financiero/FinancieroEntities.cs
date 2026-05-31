using ERP.Domain.Common;
using ERP.Domain.Enums;

namespace ERP.Domain.Entities.Financiero;

public class ConceptoPago : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public decimal MontoBase { get; set; }
    public bool AplicaMora { get; set; }
    public decimal? PorcentajeMora { get; set; }
    public int? DiasGraciaMora { get; set; }
    public bool EsRecurrente { get; set; }
    public ICollection<Deuda> Deudas { get; set; } = new List<Deuda>();
}

public class Tarifario : BaseEntity
{
    public int CarreraId { get; set; }
    public int ConceptoPagoId { get; set; }
    public ConceptoPago? ConceptoPago { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public decimal Monto { get; set; }
    public string? Descripcion { get; set; }
}

public class Deuda : BaseEntity
{
    public string NumeroDeuda { get; set; } = string.Empty;
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public int ConceptoPagoId { get; set; }
    public ConceptoPago? ConceptoPago { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public decimal MontoOriginal { get; set; }
    public decimal MontoMora { get; set; }
    public decimal MontoDescuento { get; set; }
    public decimal MontoTotal => MontoOriginal + MontoMora - MontoDescuento;
    public DateTime FechaVencimiento { get; set; }
    public EstadoPago Estado { get; set; } = EstadoPago.Pendiente;
    public int? Cuota { get; set; }
    public int? TotalCuotas { get; set; }
    public ICollection<Pago> Pagos { get; set; } = new List<Pago>();
}

public class Pago : BaseEntity
{
    public string NumeroPago { get; set; } = string.Empty;
    public int DeudaId { get; set; }
    public Deuda? Deuda { get; set; }
    public int EstudianteId { get; set; }
    public decimal MontoPagado { get; set; }
    public DateTime FechaPago { get; set; } = DateTime.Now;
    public TipoPago TipoPago { get; set; }
    public string? NumeroOperacion { get; set; }
    public string? Banco { get; set; }
    public string? Observaciones { get; set; }
    public string? RutaComprobante { get; set; }
    public bool Anulado { get; set; }
    public string? MotivoAnulacion { get; set; }
}

public class Descuento : BaseEntity
{
    public string Nombre { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public decimal? PorcentajeDescuento { get; set; }
    public decimal? MontoFijo { get; set; }
    public bool EsBeca { get; set; }
    public DateTime? FechaInicio { get; set; }
    public DateTime? FechaFin { get; set; }
    public ICollection<DescuentoEstudiante> DescuentosEstudiante { get; set; } = new List<DescuentoEstudiante>();
}

public class DescuentoEstudiante : BaseEntity
{
    public int DescuentoId { get; set; }
    public Descuento? Descuento { get; set; }
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public int PeriodoAcademicoId { get; set; }
    public bool Aprobado { get; set; }
}

public class CajaMovimiento : BaseEntity
{
    public string Tipo { get; set; } = string.Empty; // Ingreso, Egreso
    public string Concepto { get; set; } = string.Empty;
    public decimal Monto { get; set; }
    public DateTime Fecha { get; set; } = DateTime.Now;
    public string? UsuarioCajero { get; set; }
    public string? Referencia { get; set; }
    public int? PagoId { get; set; }
    public Pago? Pago { get; set; }
}

public class SolicitudDocumento : BaseEntity
{
    public string NumeroSolicitud { get; set; } = string.Empty;
    public int EstudianteId { get; set; }
    public ERP.Domain.Entities.Personas.Estudiante? Estudiante { get; set; }
    public string TipoDocumento { get; set; } = string.Empty;
    public string? Motivo { get; set; }
    public EstadoSolicitudDocumento Estado { get; set; } = EstadoSolicitudDocumento.Pendiente;
    public DateTime FechaSolicitud { get; set; } = DateTime.Now;
    public DateTime? FechaEntrega { get; set; }
    public decimal CostoDerecho { get; set; }
    public bool Pagado { get; set; }
    public string? RutaDocumentoGenerado { get; set; }
}
