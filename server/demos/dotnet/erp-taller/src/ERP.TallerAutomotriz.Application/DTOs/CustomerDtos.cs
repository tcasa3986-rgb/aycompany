using System.ComponentModel.DataAnnotations;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Application.DTOs;

public class ClienteListDto
{
    public int Id { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public TipoCliente Tipo { get; set; }
    public string NombreRazonSocial { get; set; } = string.Empty;
    public string DocumentoIdentidad { get; set; } = string.Empty;
    public string? Email { get; set; }
    public string? TelefonoPrincipal { get; set; }
    public int CantidadVehiculos { get; set; }
    public decimal SaldoPendiente { get; set; }
    public bool Activo { get; set; }
}

public class ClienteFormDto
{
    public int Id { get; set; }

    public string? Codigo { get; set; } // si es null, se autogenera

    public TipoCliente Tipo { get; set; } = TipoCliente.PersonaNatural;

    [Required(ErrorMessage = "Nombre o razón social requerido")]
    [StringLength(200)]
    public string NombreRazonSocial { get; set; } = string.Empty;

    [StringLength(150)]
    public string? NombreComercial { get; set; }

    [Required(ErrorMessage = "Documento de identidad requerido")]
    [StringLength(20)]
    public string DocumentoIdentidad { get; set; } = string.Empty;

    [StringLength(250)]
    public string? Direccion { get; set; }

    [StringLength(100)]
    public string? Ciudad { get; set; }

    [StringLength(20)]
    public string? TelefonoPrincipal { get; set; }

    [StringLength(20)]
    public string? TelefonoSecundario { get; set; }

    [EmailAddress(ErrorMessage = "Formato de email inválido")]
    [StringLength(150)]
    public string? Email { get; set; }

    [StringLength(150)]
    public string? ContactoPrincipal { get; set; }

    [StringLength(100)]
    public string? CargoContacto { get; set; }

    public string? Notas { get; set; }

    public bool RecibeNotificaciones { get; set; } = true;

    public decimal LimiteCredito { get; set; }
}

public class VehiculoListDto
{
    public int Id { get; set; }
    public int ClienteId { get; set; }
    public string ClienteNombre { get; set; } = string.Empty;
    public string Placa { get; set; } = string.Empty;
    public string Marca { get; set; } = string.Empty;
    public string Modelo { get; set; } = string.Empty;
    public int Anio { get; set; }
    public string? Color { get; set; }
    public int KilometrajeActual { get; set; }
    public TipoCombustible Combustible { get; set; }
    public TipoTransmision Transmision { get; set; }
    public bool Activo { get; set; }
}

public class VehiculoFormDto
{
    public int Id { get; set; }

    [Required(ErrorMessage = "Cliente requerido")]
    public int ClienteId { get; set; }

    [Required(ErrorMessage = "Placa requerida")]
    [StringLength(20)]
    public string Placa { get; set; } = string.Empty;

    [StringLength(20)]
    public string? VIN { get; set; }

    [Required(ErrorMessage = "Marca requerida")]
    [StringLength(50)]
    public string Marca { get; set; } = string.Empty;

    [Required(ErrorMessage = "Modelo requerido")]
    [StringLength(50)]
    public string Modelo { get; set; } = string.Empty;

    [Range(1900, 2100, ErrorMessage = "Año inválido")]
    public int Anio { get; set; } = DateTime.Now.Year;

    [StringLength(30)]
    public string? Color { get; set; }

    [Range(0, int.MaxValue, ErrorMessage = "Kilometraje inválido")]
    public int KilometrajeActual { get; set; }

    public TipoCombustible Combustible { get; set; } = TipoCombustible.Gasolina;
    public TipoTransmision Transmision { get; set; } = TipoTransmision.Manual;

    [StringLength(50)]
    public string? Motor { get; set; }

    [StringLength(50)]
    public string? NumeroChasis { get; set; }

    public string? Notas { get; set; }
}

public class PagedResult<T>
{
    public List<T> Items { get; set; } = new();
    public int Total { get; set; }
    public int Page { get; set; }
    public int PageSize { get; set; }
    public int TotalPages => PageSize == 0 ? 0 : (int)Math.Ceiling((double)Total / PageSize);
}
