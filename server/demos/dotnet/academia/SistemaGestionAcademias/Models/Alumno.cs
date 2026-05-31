using System.ComponentModel.DataAnnotations;

namespace SistemaGestionAcademias.Models
{
    public class Alumno
    {
        [Key]
        public int Id { get; set; }

        [Required(ErrorMessage = "El documento es obligatorio")]
        [StringLength(20)]
        [Display(Name = "DNI / Documento")]
        public string DocumentoIdentidad { get; set; }

        [Required]
        [StringLength(100)]
        public string Nombres { get; set; }

        [Required]
        [StringLength(100)]
        public string Apellidos { get; set; }

        [Required]
        [Phone]
        [Display(Name = "Teléfono")]
        public string Telefono { get; set; }

        [EmailAddress]
        public string? Correo { get; set; }

        [DataType(DataType.Date)]
        [Display(Name = "Fecha Nacimiento")]
        public DateTime FechaNacimiento { get; set; }

        // CAMPO NUEVO: Ruta de la foto
        [Display(Name = "Foto de Perfil")]
        public string? FotoUrl { get; set; }

        public string NombreCompleto => $"{Nombres} {Apellidos}";

        public virtual ICollection<Inscripcion>? Inscripciones { get; set; }
    }
}