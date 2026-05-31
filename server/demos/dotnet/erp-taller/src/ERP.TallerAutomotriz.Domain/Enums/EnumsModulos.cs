namespace ERP.TallerAutomotriz.Domain.Enums;

public enum TipoCliente
{
    PersonaNatural = 1,
    Empresa = 2
}

public enum TipoCombustible
{
    Gasolina = 1,
    Diesel = 2,
    Gas = 3,
    Electrico = 4,
    Hibrido = 5
}

public enum TipoTransmision
{
    Manual = 1,
    Automatica = 2,
    CVT = 3,
    SemiAutomatica = 4
}

public enum EstadoOT
{
    Recibido = 1,
    EnDiagnostico = 2,
    EnReparacion = 3,
    EnEsperaRepuesto = 4,
    ControlCalidad = 5,
    Listo = 6,
    Entregado = 7,
    Cancelado = 99
}

public enum PrioridadOT
{
    Baja = 1,
    Normal = 2,
    Alta = 3,
    Urgente = 4
}

public enum EstadoCita
{
    Pendiente = 1,
    Confirmada = 2,
    EnEspera = 3,
    Atendida = 4,
    Cancelada = 5,
    NoAsistio = 6
}

public enum TipoServicio
{
    MantenimientoPreventivo = 1,
    Correctivo = 2,
    Hojalateria = 3,
    Electrico = 4,
    AireAcondicionado = 5,
    AlineacionBalanceo = 6,
    Diagnostico = 7,
    Otro = 99
}

public enum TipoMovimientoInventario
{
    EntradaCompra = 1,
    SalidaConsumoOT = 2,
    DevolucionCliente = 3,
    DevolucionProveedor = 4,
    AjustePositivo = 5,
    AjusteNegativo = 6,
    TrasladoEntrada = 7,
    TrasladoSalida = 8
}

public enum MetodoCosteo
{
    PEPS = 1,
    PromedioPonderado = 2
}

public enum EstadoFactura
{
    Borrador = 1,
    Emitida = 2,
    PagadaParcial = 3,
    Pagada = 4,
    Vencida = 5,
    Anulada = 6
}

public enum TipoComprobante
{
    Factura = 1,
    Boleta = 2,
    NotaCredito = 3,
    NotaDebito = 4,
    Cotizacion = 5
}

public enum FormaPago
{
    Efectivo = 1,
    TarjetaCredito = 2,
    TarjetaDebito = 3,
    Transferencia = 4,
    PagoQR = 5,
    Cheque = 6
}

public enum EstadoOrdenCompra
{
    Borrador = 1,
    Enviada = 2,
    Aprobada = 3,
    RecibidaParcial = 4,
    Recibida = 5,
    Cancelada = 6
}

public enum EstadoCuentaPagar
{
    Pendiente = 1,
    PagadaParcial = 2,
    Pagada = 3,
    Vencida = 4
}

public enum NivelExperiencia
{
    Aprendiz = 1,
    Junior = 2,
    SemiSenior = 3,
    Senior = 4,
    Maestro = 5
}

public enum TipoNotificacion
{
    Email = 1,
    SMS = 2,
    WhatsApp = 3,
    Push = 4
}

public enum EstadoQC
{
    Pendiente = 1,
    Aprobado = 2,
    Rechazado = 3
}
