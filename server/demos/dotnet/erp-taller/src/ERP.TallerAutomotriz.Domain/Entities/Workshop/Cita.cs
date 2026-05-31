using ERP.TallerAutomotriz.Domain.Common;
using ERP.TallerAutomotriz.Domain.Entities.Customers;
using ERP.TallerAutomotriz.Domain.Entities.Personnel;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Domain.Entities.Workshop;

public class Cita : AuditableEntity
{
    public int ClienteId { get; set; }
    public Cliente? Cliente { get; set; }
    public int? VehiculoId { get; set; }
    public Vehiculo? Vehiculo { get; set; }

    public DateTime FechaHora { get; set; }
    public int DuracionMinutos { get; set; } = 60;
    public int? ServicioId { get; set; }
    public Servicio? Servicio { get; set; }
    public int? TecnicoPreferidoId { get; set; }
    public Tecnico? TecnicoPreferido { get; set; }

    public EstadoCita Estado { get; set; } = EstadoCita.Pendiente;
    public string? Comentarios { get; set; }
    public bool RecordatorioEnviado { get; set; }
    public bool ConfirmacionEnviada { get; set; }
    public int? OrdenTrabajoId { get; set; }
    public OrdenTrabajo? OrdenTrabajo { get; set; }
}
