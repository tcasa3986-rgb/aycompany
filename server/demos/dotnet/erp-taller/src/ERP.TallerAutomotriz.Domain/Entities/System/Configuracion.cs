using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.System;

public class Empresa : AuditableEntity
{
    public string RazonSocial { get; set; } = string.Empty;
    public string? NombreComercial { get; set; }
    public string DocumentoFiscal { get; set; } = string.Empty; // RUC
    public string? Direccion { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public string? Sitio { get; set; }
    public string? UrlLogo { get; set; }
    public string Moneda { get; set; } = "PEN";
    public string SimboloMoneda { get; set; } = "S/.";
    public decimal PorcentajeImpuesto { get; set; } = 18;
    public string NombreImpuesto { get; set; } = "IGV";
    public string Pais { get; set; } = "Perú";
}

public class Sucursal : AuditableEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public string? Direccion { get; set; }
    public string? Telefono { get; set; }
    public string? Responsable { get; set; }
    public bool EsPrincipal { get; set; }
}

public class ParametroSistema : AuditableEntity
{
    public string Clave { get; set; } = string.Empty;
    public string Valor { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public string Categoria { get; set; } = "General";
}

public class LogAuditoria : BaseEntity
{
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
    public string Usuario { get; set; } = string.Empty;
    public string Accion { get; set; } = string.Empty; // Crear, Editar, Eliminar
    public string Entidad { get; set; } = string.Empty;
    public string? EntidadId { get; set; }
    public string? Detalle { get; set; }
    public string? IpOrigen { get; set; }
}

public class PlantillaNotificacion : AuditableEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nombre { get; set; } = string.Empty;
    public TipoNotificacion Tipo { get; set; }
    public string Asunto { get; set; } = string.Empty;
    public string Cuerpo { get; set; } = string.Empty;
    public string? Variables { get; set; } // JSON con tokens disponibles
}

public class NotificacionEnviada : BaseEntity
{
    public DateTime Fecha { get; set; } = DateTime.UtcNow;
    public TipoNotificacion Tipo { get; set; }
    public string? Destinatario { get; set; }
    public string? Asunto { get; set; }
    public string? Cuerpo { get; set; }
    public bool Enviada { get; set; }
    public string? Error { get; set; }
    public string? PlantillaCodigo { get; set; }
    public string? ReferenciaTipo { get; set; } // OT, Cita, Factura
    public int? ReferenciaId { get; set; }
}
