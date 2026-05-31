using Microsoft.AspNetCore.Identity;

namespace ERP.Infrastructure.Data;

public class ApplicationUser : IdentityUser
{
    public string Nombres { get; set; } = string.Empty;
    public string Apellidos { get; set; } = string.Empty;
    public string NombreCompleto => $"{Nombres} {Apellidos}";
    public string? FotoPerfil { get; set; }
    public bool CambiarPassword { get; set; } = false;
    public DateTime? UltimoAcceso { get; set; }
    public bool Bloqueado { get; set; }
    public DateTime FechaCreacion { get; set; } = DateTime.UtcNow;
    public string? RolPrincipal { get; set; }
}
