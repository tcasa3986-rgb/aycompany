using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace SistemaGestionAcademias.Models
{
    public class Actividad
    {
        [Key]
        public int Id { get; set; }

        [Required(ErrorMessage = "El nombre es obligatorio")]
        [StringLength(100)]
        public string Nombre { get; set; }

        [Display(Name = "Sílabus / Temario")]
        public string? Silabus { get; set; }

        [Display(Name = "Objetivos de Logro")]
        public string? Objetivos { get; set; }

        [Display(Name = "Beneficios")]
        public string? Beneficios { get; set; }

        [Required]
        [Column(TypeName = "decimal(18, 2)")]
        [Display(Name = "Costo del Taller")]
        public decimal Costo { get; set; }

        [StringLength(500)]
        public string? Descripcion { get; set; }

        [Display(Name = "Imagen de Portada")]
        public string? ImagenUrl { get; set; }

        [Required]
        [DataType(DataType.Date)]
        public DateTime FechaInicio { get; set; }

        [Required]
        [DataType(DataType.Date)]
        public DateTime FechaFin { get; set; }

        // --- NUEVOS CAMPOS PARA CRONOGRAMA ---
        
        [Display(Name = "Días de Clase")]
        public string? DiasSemana { get; set; } // Ej: "Lunes,Miércoles,Viernes"

        [DataType(DataType.Time)]
        [Display(Name = "Hora Inicio")]
        public TimeSpan? HoraInicio { get; set; }

        [DataType(DataType.Time)]
        [Display(Name = "Hora Fin")]
        public TimeSpan? HoraFin { get; set; }

        [Display(Name = "Total Horas Académicas")]
        public int TotalHoras { get; set; }

        // -------------------------------------

        [Required]
        public int CupoMaximo { get; set; }

        public bool Activo { get; set; }

        // Relaciones
        public int CategoriaId { get; set; }
        public virtual Categoria? Categoria { get; set; }

        public int InstructorId { get; set; }
        public virtual Instructor? Instructor { get; set; }

        public virtual ICollection<Inscripcion>? Inscripciones { get; set; }
        
        // Relación con las sesiones generadas
        public virtual ICollection<SesionClase>? Sesiones { get; set; }
    }
}