using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Customers;

public class Cliente : AuditableEntity
{
    public string Codigo { get; set; } = string.Empty;
    public TipoCliente Tipo { get; set; } = TipoCliente.PersonaNatural;
    public string NombreRazonSocial { get; set; } = string.Empty;
    public string? NombreComercial { get; set; }
    public string DocumentoIdentidad { get; set; } = string.Empty; // RUC/DNI
    public string? Direccion { get; set; }
    public string? Ciudad { get; set; }
    public string? TelefonoPrincipal { get; set; }
    public string? TelefonoSecundario { get; set; }
    public string? Email { get; set; }
    public string? ContactoPrincipal { get; set; } // Para empresas
    public string? CargoContacto { get; set; }
    public string? Notas { get; set; }
    public bool RecibeNotificaciones { get; set; } = true;
    public int? TecnicoConfianzaId { get; set; }
    public decimal SaldoPendiente { get; set; } = 0;
    public decimal LimiteCredito { get; set; } = 0;

    public ICollection<Vehiculo> Vehiculos { get; set; } = new List<Vehiculo>();
}
