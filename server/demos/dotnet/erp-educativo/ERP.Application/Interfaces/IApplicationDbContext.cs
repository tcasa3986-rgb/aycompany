using ERP.Domain.Entities.Academico;
using ERP.Domain.Entities.Biblioteca;
using ERP.Domain.Entities.Config;
using ERP.Domain.Entities.Financiero;
using ERP.Domain.Entities.Personas;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Interfaces;

public interface IApplicationDbContext
{
    DbSet<Institucion> Instituciones { get; }
    DbSet<PeriodoAcademico> PeriodosAcademicos { get; }
    DbSet<Parametro> Parametros { get; }
    DbSet<Aula> Aulas { get; }
    DbSet<Matricula> Matriculas { get; }
    DbSet<ERP.Domain.Entities.Personas.Estudiante> Estudiantes { get; }
    DbSet<ERP.Domain.Entities.Personas.Docente> Docentes { get; }
    DbSet<Carrera> Carreras { get; }
    DbSet<Facultad> Facultades { get; }
    DbSet<Curso> Cursos { get; }
    DbSet<Seccion> Secciones { get; }
    DbSet<ERP.Domain.Entities.Financiero.Deuda> Deudas { get; }
    DbSet<ERP.Domain.Entities.Financiero.Pago> Pagos { get; }
    DbSet<ERP.Domain.Entities.Financiero.ConceptoPago> ConceptosPago { get; }
    DbSet<ERP.Domain.Entities.Biblioteca.Libro> Libros { get; }
    DbSet<ERP.Domain.Entities.Academico.DetalleMatricula> DetallesMatricula { get; }
    DbSet<ERP.Domain.Entities.Academico.Asistencia> Asistencias { get; }
    DbSet<ERP.Domain.Entities.Biblioteca.Prestamo> Prestamos { get; }
    DbSet<ERP.Domain.Entities.Academico.Evaluacion> Evaluaciones { get; }
    DbSet<ERP.Domain.Entities.Academico.Nota> Notas { get; }
    DbSet<ERP.Domain.Entities.Academico.PromedioFinal> PromediosFinales { get; }
    DbSet<ERP.Domain.Entities.Academico.PlanEstudio> PlanesEstudio { get; }
    DbSet<ERP.Domain.Entities.Academico.Horario> Horarios { get; }
    DbSet<PersonalAdministrativo> PersonalAdministrativo { get; }
    DbSet<Postulante> Postulantes { get; }
    DbSet<CajaMovimiento> CajaMovimientos { get; }
    DbSet<ActivoFijo> ActivosFijos { get; }
    DbSet<Comunicado> Comunicados { get; }
    DbSet<Notificacion> Notificaciones { get; }
    DbSet<Tesis> Tesis { get; }
    DbSet<SolicitudDocumento> SolicitudesDocumento { get; }
    Task<int> SaveChangesAsync(CancellationToken cancellationToken);
}
