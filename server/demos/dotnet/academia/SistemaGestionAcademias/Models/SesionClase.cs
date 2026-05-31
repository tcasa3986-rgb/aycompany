using System.ComponentModel.DataAnnotations;

namespace SistemaGestionAcademias.Models
{
    public class SesionClase
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [Display(Name = "Fecha de Clase")]
        public DateTime Fecha { get; set; } // Ej: 12/01/2026

        [Display(Name = "Tema / Descripción")]
        public string? TemaDelDia { get; set; } // Ej: "Introducción al Balón"

        public bool AsistenciaTomada { get; set; } = false;

        // Relación con Actividad
        public int ActividadId { get; set; }
        public virtual Actividad? Actividad { get; set; }

        // Relación con las asistencias de los alumnos
        public virtual ICollection<Asistencia>? Asistencias { get; set; }
    }
}