using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Customers;

public class Vehiculo : AuditableEntity
{
    public int ClienteId { get; set; }
    public Cliente? Cliente { get; set; }

    public string Placa { get; set; } = string.Empty;
    public string? VIN { get; set; }
    public string Marca { get; set; } = string.Empty;
    public string Modelo { get; set; } = string.Empty;
    public int Anio { get; set; }
    public string? Color { get; set; }
    public int KilometrajeActual { get; set; }
    public TipoCombustible Combustible { get; set; } = TipoCombustible.Gasolina;
    public TipoTransmision Transmision { get; set; } = TipoTransmision.Manual;
    public string? Motor { get; set; }
    public string? NumeroChasis { get; set; }
    public string? Notas { get; set; }
    public DateTime? UltimoServicio { get; set; }
    public int? KilometrajeUltimoServicio { get; set; }
    public DateTime? ProximoServicioFecha { get; set; }
    public int? ProximoServicioKm { get; set; }
}
