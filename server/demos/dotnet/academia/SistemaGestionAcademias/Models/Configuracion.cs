using System.ComponentModel.DataAnnotations;

namespace SistemaGestionAcademias.Models
{
    public class Configuracion
    {
        [Key]
        public int Id { get; set; }

        [Required(ErrorMessage = "El nombre de la empresa es obligatorio")]
        [Display(Name = "Nombre de la Empresa")]
        public string NombreEmpresa { get; set; }

        [Display(Name = "Dirección")]
        public string? Direccion { get; set; }

        [Phone]
        public string? Telefono { get; set; }

        [EmailAddress]
        public string? CorreoContacto { get; set; }

        // --- CONFIGURACIÓN REGIONAL ---
        
        [Required]
        [Display(Name = "Símbolo de Moneda")]
        public string MonedaSimbolo { get; set; } // Ej: "$", "S/.", "€"

        [Display(Name = "Zona Horaria")]
        public string ZonaHorariaId { get; set; } // ID interno de Windows (Ej: "SA Pacific Standard Time")

        // --- IMAGEN ---
        [Display(Name = "Logo de la Empresa")]
        public string? LogoUrl { get; set; }
    }
}