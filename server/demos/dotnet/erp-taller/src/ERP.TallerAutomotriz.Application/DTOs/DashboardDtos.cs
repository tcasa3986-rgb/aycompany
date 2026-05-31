namespace ERP.TallerAutomotriz.Application.DTOs;

public class DashboardKpiDto
{
    public int VehiculosEnTaller { get; set; }
    public int OrdenesAbiertas { get; set; }
    public int OrdenesTerminadasHoy { get; set; }
    public decimal IngresosHoy { get; set; }
    public int TecnicosActivos { get; set; }
    public int CitasHoy { get; set; }
    public int RepuestosBajoStock { get; set; }
    public int FacturasVencidas { get; set; }
    public decimal MontoFacturasVencidas { get; set; }
    public decimal TicketPromedio { get; set; }
    public double PorcentajeOcupacion { get; set; }
    public double TasaRetencion { get; set; }
}

public class TendenciaItemDto
{
    public string Etiqueta { get; set; } = string.Empty;
    public decimal Valor { get; set; }
    public int Cantidad { get; set; }
}

public class AlertaDto
{
    public string Tipo { get; set; } = string.Empty; // StockBajo, FacturaVencida, OTRetraso, CitaSinTecnico
    public string Severidad { get; set; } = "info"; // info, warning, error, success
    public string Titulo { get; set; } = string.Empty;
    public string Mensaje { get; set; } = string.Empty;
    public string? UrlAccion { get; set; }
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
}

public class OrdenTrabajoResumenDto
{
    public int Id { get; set; }
    public string Numero { get; set; } = string.Empty;
    public string Cliente { get; set; } = string.Empty;
    public string Vehiculo { get; set; } = string.Empty;
    public string Placa { get; set; } = string.Empty;
    public string Estado { get; set; } = string.Empty;
    public string? Tecnico { get; set; }
    public DateTime FechaIngreso { get; set; }
    public decimal Total { get; set; }
}
