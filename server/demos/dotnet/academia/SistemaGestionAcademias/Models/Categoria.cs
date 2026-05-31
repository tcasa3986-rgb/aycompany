using System.ComponentModel.DataAnnotations;

namespace SistemaGestionAcademias.Models
{
    public class Categoria
    {
        [Key]
        public int Id { get; set; }

        [Required(ErrorMessage = "El nombre es obligatorio")]
        [StringLength(50, ErrorMessage = "No puede exceder los 50 caracteres")]
        public string Nombre { get; set; } // Ej: Deportes, Danza

        [StringLength(255)]
        public string? Descripcion { get; set; }

        public bool Estado { get; set; } = true; // Para "eliminar" lógicamente sin borrar el dato
    }
}