using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Models;

namespace SistemaGestionAcademias.Data
{
    public class ApplicationDbContext : IdentityDbContext
    {
        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options)
            : base(options)
        {
        }

        protected override void OnModelCreating(ModelBuilder builder)
        {
            base.OnModelCreating(builder);
        }

        // --- TABLAS DEL SISTEMA ---
        public DbSet<Categoria> Categorias { get; set; }
        public DbSet<Instructor> Instructores { get; set; }
        public DbSet<Actividad> Actividades { get; set; }
        public DbSet<Alumno> Alumnos { get; set; }
        public DbSet<Inscripcion> Inscripciones { get; set; }
        public DbSet<Configuracion> Configuraciones { get; set; }
        public DbSet<CajaDiaria> CajasDiarias { get; set; }
        public DbSet<Gasto> Gastos { get; set; }

        // --- NUEVAS TABLAS DE ASISTENCIA ---
        public DbSet<SesionClase> SesionesClase { get; set; }
        public DbSet<Asistencia> Asistencias { get; set; }
    }
}