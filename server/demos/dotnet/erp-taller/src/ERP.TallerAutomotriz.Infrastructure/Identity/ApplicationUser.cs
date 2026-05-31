using Microsoft.AspNetCore.Identity;

namespace ERP.TallerAutomotriz.Infrastructure.Identity;

public class ApplicationUser : IdentityUser
{
    public string? Nombres { get; set; }
    public string? Apellidos { get; set; }
    public string? UrlAvatar { get; set; }
    public bool Activo { get; set; } = true;
    public DateTime FechaCreacion { get; set; } = DateTime.UtcNow;
    public DateTime? UltimoAcceso { get; set; }
    public int? TecnicoId { get; set; }
    public int? SucursalId { get; set; }

    public string NombreCompleto => $"{Nombres} {Apellidos}".Trim();
}

public class ApplicationRole : IdentityRole
{
    public string? Descripcion { get; set; }
}

public static class RolesSistema
{
    public const string Administrador = "Administrador";
    public const string JefeTaller = "Jefe de Taller";
    public const string Recepcionista = "Recepcionista";
    public const string Tecnico = "Tecnico";
    public const string Cajero = "Cajero";
    public const string Contador = "Contador";

    public static readonly string[] Todos = new[]
    {
        Administrador, JefeTaller, Recepcionista, Tecnico, Cajero, Contador
    };
}
