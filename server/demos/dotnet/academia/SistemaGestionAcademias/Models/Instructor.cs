using System.ComponentModel.DataAnnotations;

namespace SistemaGestionAcademias.Models
{
    public class Instructor
    {
        [Key]
        public int Id { get; set; }

        [Required(ErrorMessage = "El nombre es obligatorio")]
        [StringLength(100)]
        public string Nombres { get; set; }

        [Required(ErrorMessage = "El apellido es obligatorio")]
        [StringLength(100)]
        public string Apellidos { get; set; }

        [Required]
        [Display(Name = "Especialidad / Título")]
        public string Especialidad { get; set; }

        [Phone]
        public string? Telefono { get; set; }

        [EmailAddress]
        public string? Correo { get; set; }

        public bool Activo { get; set; } = true;

        // Propiedad calculada
        public string NombreCompleto => $"{Nombres} {Apellidos}";

        // NUEVA RELACIÓN: Las clases que dicta este profesor
        public virtual ICollection<Actividad>? Actividades { get; set; }
    }
}