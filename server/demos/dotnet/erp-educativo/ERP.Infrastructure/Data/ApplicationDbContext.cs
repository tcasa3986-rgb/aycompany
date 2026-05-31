using ERP.Domain.Entities.Academico;
using ERP.Domain.Entities.Biblioteca;
using ERP.Domain.Entities.Config;
using ERP.Domain.Entities.Financiero;
using ERP.Domain.Entities.Personas;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;

using ERP.Application.Interfaces;

namespace ERP.Infrastructure.Data;

public class ApplicationDbContext : IdentityDbContext<ApplicationUser>, IApplicationDbContext
{
    public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options) : base(options) { }

    // ─── CONFIGURACIÓN ───────────────────────────────────────────────
    public DbSet<Institucion> Instituciones => Set<Institucion>();
    public DbSet<PeriodoAcademico> PeriodosAcademicos => Set<PeriodoAcademico>();
    public DbSet<CicloAcademico> CiclosAcademicos => Set<CicloAcademico>();
    public DbSet<Turno> Turnos => Set<Turno>();
    public DbSet<Parametro> Parametros => Set<Parametro>();
    public DbSet<Aula> Aulas => Set<Aula>();

    // ─── PERSONAS ────────────────────────────────────────────────────
    public DbSet<Estudiante> Estudiantes => Set<Estudiante>();
    public DbSet<Docente> Docentes => Set<Docente>();
    public DbSet<PersonalAdministrativo> PersonalAdministrativo => Set<PersonalAdministrativo>();
    public DbSet<Postulante> Postulantes => Set<Postulante>();
    public DbSet<Documento> Documentos => Set<Documento>();

    // ─── ACADÉMICO ───────────────────────────────────────────────────
    public DbSet<Facultad> Facultades => Set<Facultad>();
    public DbSet<Carrera> Carreras => Set<Carrera>();
    public DbSet<PlanEstudio> PlanesEstudio => Set<PlanEstudio>();
    public DbSet<Curso> Cursos => Set<Curso>();
    public DbSet<CursoMalla> CursosMalla => Set<CursoMalla>();
    public DbSet<Seccion> Secciones => Set<Seccion>();
    public DbSet<AsignacionCurso> AsignacionesCurso => Set<AsignacionCurso>();
    public DbSet<Horario> Horarios => Set<Horario>();
    public DbSet<Matricula> Matriculas => Set<Matricula>();
    public DbSet<DetalleMatricula> DetallesMatricula => Set<DetalleMatricula>();
    public DbSet<Asistencia> Asistencias => Set<Asistencia>();
    public DbSet<Evaluacion> Evaluaciones => Set<Evaluacion>();
    public DbSet<Nota> Notas => Set<Nota>();
    public DbSet<PromedioFinal> PromediosFinales => Set<PromedioFinal>();

    // ─── FINANCIERO ──────────────────────────────────────────────────
    public DbSet<ConceptoPago> ConceptosPago => Set<ConceptoPago>();
    public DbSet<Tarifario> Tarifarios => Set<Tarifario>();
    public DbSet<Deuda> Deudas => Set<Deuda>();
    public DbSet<Pago> Pagos => Set<Pago>();
    public DbSet<Descuento> Descuentos => Set<Descuento>();
    public DbSet<DescuentoEstudiante> DescuentosEstudiante => Set<DescuentoEstudiante>();
    public DbSet<CajaMovimiento> CajaMovimientos => Set<CajaMovimiento>();
    public DbSet<SolicitudDocumento> SolicitudesDocumento => Set<SolicitudDocumento>();

    // ─── BIBLIOTECA Y OTROS ──────────────────────────────────────────
    public DbSet<Libro> Libros => Set<Libro>();
    public DbSet<Prestamo> Prestamos => Set<Prestamo>();
    public DbSet<ActivoFijo> ActivosFijos => Set<ActivoFijo>();
    public DbSet<Comunicado> Comunicados => Set<Comunicado>();
    public DbSet<Notificacion> Notificaciones => Set<Notificacion>();
    public DbSet<Tesis> Tesis => Set<Tesis>();

    protected override void OnModelCreating(ModelBuilder builder)
    {
        base.OnModelCreating(builder);

        // Renombrar tablas Identity
        builder.Entity<ApplicationUser>().ToTable("Usuarios");
        builder.Entity<IdentityRole>().ToTable("Roles");
        builder.Entity<IdentityUserRole<string>>().ToTable("UsuarioRoles");
        builder.Entity<IdentityUserClaim<string>>().ToTable("UsuarioClaims");
        builder.Entity<IdentityUserLogin<string>>().ToTable("UsuarioLogins");
        builder.Entity<IdentityRoleClaim<string>>().ToTable("RolClaims");
        builder.Entity<IdentityUserToken<string>>().ToTable("UsuarioTokens");

        // Configuraciones de precisión decimal
        builder.Entity<Deuda>().Property(d => d.MontoOriginal).HasPrecision(18, 2);
        builder.Entity<Deuda>().Property(d => d.MontoMora).HasPrecision(18, 2);
        builder.Entity<Deuda>().Property(d => d.MontoDescuento).HasPrecision(18, 2);
        builder.Entity<Pago>().Property(p => p.MontoPagado).HasPrecision(18, 2);
        builder.Entity<ConceptoPago>().Property(c => c.MontoBase).HasPrecision(18, 2);
        builder.Entity<Nota>().Property(n => n.Calificacion).HasPrecision(5, 2);
        builder.Entity<PromedioFinal>().Property(p => p.PromedioCalculado).HasPrecision(5, 2);
        builder.Entity<Evaluacion>().Property(e => e.PesoPromedio).HasPrecision(5, 2);
        builder.Entity<Evaluacion>().Property(e => e.NotaMaxima).HasPrecision(5, 2);

        // Índices útiles
        builder.Entity<Estudiante>().HasIndex(e => e.DNI).IsUnique();
        builder.Entity<Estudiante>().HasIndex(e => e.Codigo).IsUnique();
        builder.Entity<Docente>().HasIndex(d => d.DNI).IsUnique();
        builder.Entity<Docente>().HasIndex(d => d.Codigo).IsUnique();
        builder.Entity<Matricula>().HasIndex(m => m.NumeroMatricula).IsUnique();
        builder.Entity<Pago>().HasIndex(p => p.NumeroPago).IsUnique();
        builder.Entity<Libro>().HasIndex(l => l.ISBN);
        builder.Entity<Parametro>().HasIndex(p => p.Clave).IsUnique();

        // Propiedad ignorada (computed)
        builder.Entity<Deuda>().Ignore(d => d.MontoTotal);
        builder.Entity<Estudiante>().Ignore(e => e.NombreCompleto);
        builder.Entity<Docente>().Ignore(d => d.NombreCompleto);
    }
}
