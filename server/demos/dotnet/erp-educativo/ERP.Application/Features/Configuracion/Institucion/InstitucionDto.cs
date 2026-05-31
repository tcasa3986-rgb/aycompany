namespace ERP.Application.Features.Configuracion.Institucion;

public class InstitucionDto
{
    public int Id { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public string? RUC { get; set; }
    public string? Direccion { get; set; }
    public string? Telefono { get; set; }
    public string? Email { get; set; }
    public string? SitioWeb { get; set; }
    public string? Slogan { get; set; }
    public string? Logo { get; set; }
}
