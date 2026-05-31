using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace SistemaGestionAcademias.Models
{
    public class Inscripcion
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [DataType(DataType.Date)]
        [Display(Name = "Fecha de Operación")]
        public DateTime FechaInscripcion { get; set; }

        [Required]
        public string Estado { get; set; } // Pendiente, Pagado, Becado

        // --- CAMPOS FINANCIEROS ---
        [Column(TypeName = "decimal(18, 2)")]
        public decimal MontoOriginal { get; set; }

        [Column(TypeName = "decimal(18, 2)")]
        public decimal MontoFinal { get; set; } // Lo que realmente pagó

        [StringLength(100)]
        public string? DescuentoAplicado { get; set; } // Ej: "Hermanos -10%"

        // --- NUEVO: PARA EL ARQUEO DE CAJA ---
        [Display(Name = "Método de Pago")]
        [StringLength(50)]
        public string? MetodoPago { get; set; } // Efectivo, Tarjeta, Yape/Plin, Transferencia

        // Relaciones
        public int AlumnoId { get; set; }
        public virtual Alumno? Alumno { get; set; }

        public int ActividadId { get; set; }
        public virtual Actividad? Actividad { get; set; }
    }
}