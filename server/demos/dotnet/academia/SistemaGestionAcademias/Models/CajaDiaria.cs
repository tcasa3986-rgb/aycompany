using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace SistemaGestionAcademias.Models
{
    public class CajaDiaria
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [DataType(DataType.Date)]
        public DateTime Fecha { get; set; }

        [Column(TypeName = "decimal(18, 2)")]
        [Display(Name = "Monto Inicial (Apertura)")]
        public decimal MontoInicial { get; set; }

        [Column(TypeName = "decimal(18, 2)")]
        [Display(Name = "Monto Final (Cierre)")]
        public decimal MontoFinalReal { get; set; } // Lo que cuenta el usuario

        [Column(TypeName = "decimal(18, 2)")]
        public decimal MontoFinalCalculado { get; set; } // Lo que dice el sistema

        [StringLength(20)]
        public string Estado { get; set; } = "Abierta"; // Abierta, Cerrada

        [StringLength(500)]
        public string? Observaciones { get; set; }
    }
}