using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Models;

namespace SistemaGestionAcademias.Services
{
    public class ConfiguracionService
    {
        private readonly ApplicationDbContext _context;

        public ConfiguracionService(ApplicationDbContext context)
        {
            _context = context;
        }

        public async Task<Configuracion> ObtenerConfiguracion()
        {
            var config = await _context.Configuraciones.FirstOrDefaultAsync();
            
            // Si no existe configuración, devolvemos una por defecto para que el sistema no falle
            if (config == null)
            {
                return new Configuracion
                {
                    NombreEmpresa = "Mi Academia",
                    MonedaSimbolo = "$",
                    ZonaHorariaId = TimeZoneInfo.Local.Id, // Hora del servidor por defecto
                    LogoUrl = null
                };
            }
            return config;
        }
    }
}