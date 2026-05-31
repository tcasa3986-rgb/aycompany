using System.ComponentModel.DataAnnotations;

namespace SistemaGestionAcademias.Models
{
    public class Asistencia
    {
        [Key]
        public int Id { get; set; }

        [Required]
        public string Estado { get; set; } // Presente, Tardanza, Falta, Justificado

        public string? Observacion { get; set; }

        // Relaciones
        public int SesionClaseId { get; set; }
        public virtual SesionClase? SesionClase { get; set; }

        public int AlumnoId { get; set; }
        public virtual Alumno? Alumno { get; set; }
    }
}