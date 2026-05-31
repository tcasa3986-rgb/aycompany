using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace SistemaGestionAcademias.Models
{
    public class Gasto
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [StringLength(200)]
        public string Descripcion { get; set; }

        [Required]
        [Column(TypeName = "decimal(18, 2)")]
        // Corrección: Range simplificado para evitar errores de compilación con decimales
        [Range(0, 999999)] 
        public decimal Monto { get; set; }

        [Required]
        public DateTime FechaHora { get; set; }

        public string? RegistradoPor { get; set; }
    }
}