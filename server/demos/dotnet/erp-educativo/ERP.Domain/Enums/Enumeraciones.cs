namespace ERP.Domain.Enums;

public enum TipoSexo { Masculino = 1, Femenino = 2, Otro = 3 }

public enum EstadoEstudiante
{
    Activo = 1, Retirado = 2, Suspendido = 3,
    Egresado = 4, Graduado = 5, Reserva = 6
}

public enum EstadoDocente { Activo = 1, Inactivo = 2, Licencia = 3 }

public enum TipoContrato { TiempoCompleto = 1, MedioTiempo = 2, PorHoras = 3 }

public enum EstadoMatricula
{
    Pendiente = 1, Confirmada = 2, Condicional = 3,
    Anulada = 4, Trasladada = 5
}

public enum TipoMatricula { Nueva = 1, Regular = 2, Traslado = 3, Reingreso = 4 }

public enum EstadoPago { Pendiente = 1, Pagado = 2, Vencido = 3, Anulado = 4 }

public enum TipoPago
{
    Efectivo = 1, Transferencia = 2, Tarjeta = 3,
    QR = 4, Cheque = 5
}

public enum TipoEvaluacion
{
    Examen = 1, Practica = 2, Tarea = 3,
    Laboratorio = 4, Proyecto = 5, Participacion = 6
}

public enum EstadoAsistencia
{
    Presente = 1, Falta = 2, Tardanza = 3,
    JustificadoPresente = 4, JustificadoFalta = 5
}

public enum NivelEducativo
{
    Inicial = 1, Primaria = 2, Secundaria = 3,
    Tecnico = 4, Universitario = 5, Posgrado = 6
}

public enum Modalidad { Presencial = 1, SemiPresencial = 2, Virtual = 3 }

public enum TurnoAcademico { Manana = 1, Tarde = 2, Noche = 3 }

public enum EstadoSolicitudDocumento
{
    Pendiente = 1, EnProceso = 2, Listo = 3,
    Entregado = 4, Rechazado = 5
}

public enum TipoPersonal
{
    Administrativo = 1, Servicio = 2, Directivo = 3
}

public enum EstadoPostulante
{
    Registrado = 1, EnProceso = 2, Admitido = 3,
    NoAdmitido = 4, Matriculado = 5, Retirado = 6
}

public enum EstadoPrestamoBiblioteca
{
    Activo = 1, Devuelto = 2, Vencido = 3
}

public enum TipoActivo
{
    Mobiliario = 1, Equipo = 2, Vehiculo = 3, Herramienta = 4, Software = 5
}

public enum RolUsuario
{
    Administrador = 1, Docente = 2, Estudiante = 3,
    Apoderado = 4, Contador = 5, Recepcionista = 6, Directivo = 7
}
