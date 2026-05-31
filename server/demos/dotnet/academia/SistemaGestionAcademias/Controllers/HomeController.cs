using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Models;
using System.Diagnostics;

namespace SistemaGestionAcademias.Controllers
{
    [Authorize]
    public class HomeController : Controller
    {
        private readonly ILogger<HomeController> _logger;
        private readonly ApplicationDbContext _context;

        public HomeController(ILogger<HomeController> logger, ApplicationDbContext context)
        {
            _logger = logger;
            _context = context;
        }

        public async Task<IActionResult> Index()
        {
            var dashboard = new DashboardViewModel();

            // 1. Contadores Generales
            dashboard.TotalAlumnos = await _context.Alumnos.CountAsync();
            dashboard.TotalInstructores = await _context.Instructores.CountAsync();
            dashboard.TotalActividades = await _context.Actividades.CountAsync();
            dashboard.ActividadesActivas = await _context.Actividades.Where(a => a.Activo).CountAsync();

            // 2. Tabla: Últimas 5 Inscripciones
            dashboard.UltimasInscripciones = await _context.Inscripciones
                .Include(i => i.Alumno)
                .Include(i => i.Actividad)
                .OrderByDescending(i => i.FechaInscripcion)
                .Take(5)
                .ToListAsync();

            // 3. DATOS PARA GRÁFICOS
            var actividadesData = await _context.Actividades
                .Include(a => a.Inscripciones)
                .Select(a => new { Nombre = a.Nombre, Cantidad = a.Inscripciones.Count })
                .OrderByDescending(x => x.Cantidad)
                .Take(5)
                .ToListAsync();

            foreach (var item in actividadesData)
            {
                dashboard.EtiquetasActividades.Add(item.Nombre);
                dashboard.ValoresInscritos.Add(item.Cantidad);
            }

            dashboard.TotalPagados = await _context.Inscripciones.CountAsync(i => i.Estado == "Pagado");
            dashboard.TotalPendientes = await _context.Inscripciones.CountAsync(i => i.Estado == "Pendiente");

            return View(dashboard);
        }

        // --- NUEVO: API PARA EL CALENDARIO ---
        [HttpGet]
        public async Task<JsonResult> ObtenerEventosCalendario()
        {
            var eventos = await _context.Actividades
                .Where(a => a.Activo)
                .Select(a => new {
                    title = a.Nombre,
                    start = a.FechaInicio.ToString("yyyy-MM-dd"), // Formato internacional
                    end = a.FechaFin.AddDays(1).ToString("yyyy-MM-dd"), // AddDays(1) porque el calendario es exclusivo en fecha fin
                    url = "/Actividades/Details/" + a.Id, // Al hacer clic, lleva al detalle
                    backgroundColor = "#ffc107", // Amarillo (color warning de bootstrap)
                    borderColor = "#ffc107",
                    textColor = "#000" // Texto negro
                })
                .ToListAsync();

            return Json(eventos);
        }

        [AllowAnonymous]
        public IActionResult Privacy()
        {
            return View();
        }

        [ResponseCache(Duration = 0, Location = ResponseCacheLocation.None, NoStore = true)]
        public IActionResult Error()
        {
            return View(new ErrorViewModel { RequestId = Activity.Current?.Id ?? HttpContext.TraceIdentifier });
        }
    }
}