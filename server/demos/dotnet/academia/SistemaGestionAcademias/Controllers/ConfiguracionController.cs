using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.Rendering;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Models;

namespace SistemaGestionAcademias.Controllers
{
    [Authorize(Roles = "Admin")] // Solo el Admin puede tocar esto
    public class ConfiguracionController : Controller
    {
        private readonly ApplicationDbContext _context;
        private readonly IWebHostEnvironment _webHostEnvironment;

        public ConfiguracionController(ApplicationDbContext context, IWebHostEnvironment webHostEnvironment)
        {
            _context = context;
            _webHostEnvironment = webHostEnvironment;
        }

        // GET: Configuracion
        public async Task<IActionResult> Index()
        {
            // Solo debe haber 1 registro. Buscamos el primero.
            var configuracion = await _context.Configuraciones.FirstOrDefaultAsync();

            if (configuracion == null)
            {
                // Si no existe, creamos uno en memoria para mostrar el formulario vacío
                configuracion = new Configuracion 
                { 
                    MonedaSimbolo = "$",
                    ZonaHorariaId = TimeZoneInfo.Local.Id
                };
            }

            // Cargar lista de Zonas Horarias del Sistema Operativo
            ViewBag.ZonasHorarias = TimeZoneInfo.GetSystemTimeZones()
                .Select(z => new SelectListItem 
                { 
                    Value = z.Id, 
                    Text = z.DisplayName 
                }).ToList();

            return View(configuracion);
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Guardar(Configuracion modelo, IFormFile? logo)
        {
            // Validamos solo campos básicos (la imagen es opcional)
            if (ModelState.IsValid)
            {
                // 1. MANEJO DEL LOGO
                if (logo != null)
                {
                    string carpeta = Path.Combine(_webHostEnvironment.WebRootPath, "imagenes");
                    if (!Directory.Exists(carpeta)) Directory.CreateDirectory(carpeta);

                    string nombreArchivo = "logo_empresa" + Path.GetExtension(logo.FileName); // Siempre se llamará así para reemplazar el anterior
                    string rutaCompleta = Path.Combine(carpeta, nombreArchivo);

                    using (var stream = new FileStream(rutaCompleta, FileMode.Create))
                    {
                        await logo.CopyToAsync(stream);
                    }

                    modelo.LogoUrl = nombreArchivo;
                }
                else
                {
                    // Si no sube logo, mantenemos el anterior (necesitamos buscarlo si es edición)
                    var anterior = await _context.Configuraciones.AsNoTracking().FirstOrDefaultAsync(c => c.Id == modelo.Id);
                    if (anterior != null) modelo.LogoUrl = anterior.LogoUrl;
                }

                // 2. GUARDAR O ACTUALIZAR
                if (modelo.Id == 0)
                {
                    _context.Add(modelo); // Crear nuevo
                }
                else
                {
                    _context.Update(modelo); // Actualizar existente
                }

                await _context.SaveChangesAsync();
                
                TempData["AlertTipo"] = "success";
                TempData["AlertMensaje"] = "Configuración del sistema guardada correctamente.";
                
                return RedirectToAction(nameof(Index));
            }

            // Recargar zonas si falla
            ViewBag.ZonasHorarias = TimeZoneInfo.GetSystemTimeZones()
                .Select(z => new SelectListItem { Value = z.Id, Text = z.DisplayName }).ToList();

            return View("Index", modelo);
        }
    }
}