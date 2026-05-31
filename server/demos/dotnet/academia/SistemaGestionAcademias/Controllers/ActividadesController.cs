using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.Rendering;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Models;
using ClosedXML.Excel; // Asegúrate de tener ClosedXML instalado

namespace SistemaGestionAcademias.Controllers
{
    [Authorize]
    public class ActividadesController : Controller
    {
        private readonly ApplicationDbContext _context;
        private readonly IWebHostEnvironment _webHostEnvironment;

        public ActividadesController(ApplicationDbContext context, IWebHostEnvironment webHostEnvironment)
        {
            _context = context;
            _webHostEnvironment = webHostEnvironment;
        }

        // GET: Actividades
        public async Task<IActionResult> Index(string buscar)
        {
            var query = _context.Actividades
                .Include(a => a.Categoria)
                .Include(a => a.Instructor)
                .AsQueryable();

            if (!String.IsNullOrEmpty(buscar))
            {
                query = query.Where(a => a.Nombre.Contains(buscar) || a.Categoria.Nombre.Contains(buscar));
            }

            ViewData["BusquedaActual"] = buscar;
            return View(await query.ToListAsync());
        }

        // GET: Actividades/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null) return NotFound();

            var actividad = await _context.Actividades
                .Include(a => a.Categoria)
                .Include(a => a.Instructor)
                .Include(a => a.Sesiones) // Incluir sesiones generadas
                .Include(a => a.Inscripciones).ThenInclude(i => i.Alumno)
                .FirstOrDefaultAsync(m => m.Id == id);

            if (actividad == null) return NotFound();

            return View(actividad);
        }

        public IActionResult Create()
        {
            ViewData["CategoriaId"] = new SelectList(_context.Categorias, "Id", "Nombre");
            ViewData["InstructorId"] = new SelectList(_context.Instructores, "Id", "Nombres");
            return View();
        }

        // POST: Actividades/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        // BIND ACTUALIZADO con nuevos campos de horario
        public async Task<IActionResult> Create([Bind("Id,Nombre,Descripcion,Silabus,Objetivos,Beneficios,Costo,FechaInicio,FechaFin,DiasSemana,HoraInicio,HoraFin,TotalHoras,CupoMaximo,Activo,CategoriaId,InstructorId")] Actividad actividad, IFormFile? imagen)
        {
            if (ModelState.IsValid)
            {
                if (imagen != null)
                {
                    string carpeta = Path.Combine(_webHostEnvironment.WebRootPath, "imagenes");
                    if (!Directory.Exists(carpeta)) Directory.CreateDirectory(carpeta);
                    string nombreArchivo = Guid.NewGuid().ToString() + Path.GetExtension(imagen.FileName);
                    string rutaCompleta = Path.Combine(carpeta, nombreArchivo);
                    using (var stream = new FileStream(rutaCompleta, FileMode.Create)) { await imagen.CopyToAsync(stream); }
                    actividad.ImagenUrl = nombreArchivo;
                }

                _context.Add(actividad);
                await _context.SaveChangesAsync(); // Guardamos primero para tener el ID

                // --- GENERACIÓN AUTOMÁTICA DE SESIONES ---
                if (!string.IsNullOrEmpty(actividad.DiasSemana))
                {
                    await GenerarSesiones(actividad);
                }

                TempData["AlertTipo"] = "success";
                TempData["AlertMensaje"] = "Actividad creada y cronograma generado.";
                return RedirectToAction(nameof(Index));
            }
            ViewData["CategoriaId"] = new SelectList(_context.Categorias, "Id", "Nombre", actividad.CategoriaId);
            ViewData["InstructorId"] = new SelectList(_context.Instructores, "Id", "Nombres", actividad.InstructorId);
            return View(actividad);
        }

        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var actividad = await _context.Actividades.FindAsync(id);
            if (actividad == null) return NotFound();
            ViewData["CategoriaId"] = new SelectList(_context.Categorias, "Id", "Nombre", actividad.CategoriaId);
            ViewData["InstructorId"] = new SelectList(_context.Instructores, "Id", "Nombres", actividad.InstructorId);
            return View(actividad);
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, [Bind("Id,Nombre,Descripcion,Silabus,Objetivos,Beneficios,Costo,FechaInicio,FechaFin,DiasSemana,HoraInicio,HoraFin,TotalHoras,CupoMaximo,Activo,CategoriaId,InstructorId,ImagenUrl")] Actividad actividad, IFormFile? imagen)
        {
            if (id != actividad.Id) return NotFound();

            if (ModelState.IsValid)
            {
                try
                {
                    if (imagen != null)
                    {
                        string carpeta = Path.Combine(_webHostEnvironment.WebRootPath, "imagenes");
                        if (!Directory.Exists(carpeta)) Directory.CreateDirectory(carpeta);
                        string nombreArchivo = Guid.NewGuid().ToString() + Path.GetExtension(imagen.FileName);
                        string rutaCompleta = Path.Combine(carpeta, nombreArchivo);
                        using (var stream = new FileStream(rutaCompleta, FileMode.Create)) { await imagen.CopyToAsync(stream); }
                        actividad.ImagenUrl = nombreArchivo;
                    }

                    _context.Update(actividad);
                    await _context.SaveChangesAsync();

                    // Opcional: Regenerar sesiones si cambian las fechas (lógica compleja, por ahora simple)
                    // Podrías agregar un botón específico para "Regenerar Cronograma"
                    
                    TempData["AlertTipo"] = "success";
                    TempData["AlertMensaje"] = "Actividad actualizada.";
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!ActividadExists(actividad.Id)) return NotFound(); else throw;
                }
                return RedirectToAction(nameof(Index));
            }
            ViewData["CategoriaId"] = new SelectList(_context.Categorias, "Id", "Nombre", actividad.CategoriaId);
            ViewData["InstructorId"] = new SelectList(_context.Instructores, "Id", "Nombres", actividad.InstructorId);
            return View(actividad);
        }

        // LÓGICA PARA CREAR SESIONES (CRONOGRAMA)
        private async Task GenerarSesiones(Actividad actividad)
        {
            var diasSeleccionados = actividad.DiasSemana.Split(',').ToList(); // ["Lunes", "Miércoles"]
            var fechaActual = actividad.FechaInicio;

            while (fechaActual <= actividad.FechaFin)
            {
                // Obtenemos el nombre del día en español (System.Globalization)
                var diaSemanaIngles = fechaActual.DayOfWeek.ToString();
                var diaSemanaEsp = TraducirDia(diaSemanaIngles);

                if (diasSeleccionados.Contains(diaSemanaEsp))
                {
                    var sesion = new SesionClase
                    {
                        ActividadId = actividad.Id,
                        Fecha = fechaActual,
                        TemaDelDia = "Clase Regular",
                        AsistenciaTomada = false
                    };
                    _context.Add(sesion);
                }
                fechaActual = fechaActual.AddDays(1);
            }
            await _context.SaveChangesAsync();
        }

        private string TraducirDia(string diaIngles)
        {
            return diaIngles switch
            {
                "Monday" => "Lunes",
                "Tuesday" => "Martes",
                "Wednesday" => "Miércoles",
                "Thursday" => "Jueves",
                "Friday" => "Viernes",
                "Saturday" => "Sábado",
                "Sunday" => "Domingo",
                _ => ""
            };
        }

        // DELETE y EXPORTAR se mantienen igual...
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var actividad = await _context.Actividades.Include(a => a.Categoria).Include(a => a.Instructor).FirstOrDefaultAsync(m => m.Id == id);
            if (actividad == null) return NotFound();
            return View(actividad);
        }

        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var actividad = await _context.Actividades.FindAsync(id);
            if (actividad != null) _context.Actividades.Remove(actividad);
            await _context.SaveChangesAsync();
            return RedirectToAction(nameof(Index));
        }

        public async Task<IActionResult> ExportarExcel(int id)
        {
            // Lógica existente de exportación
             var actividad = await _context.Actividades
                .Include(a => a.Categoria)
                .Include(a => a.Instructor)
                .Include(a => a.Inscripciones).ThenInclude(i => i.Alumno)
                .FirstOrDefaultAsync(m => m.Id == id);

            if (actividad == null) return NotFound();

            using (var workbook = new XLWorkbook())
            {
                var worksheet = workbook.Worksheets.Add("Alumnos");
                worksheet.Cell(1, 1).Value = "REPORTE - " + actividad.Nombre;
                var currentRow = 3;
                worksheet.Cell(currentRow, 1).Value = "Alumno";
                worksheet.Cell(currentRow, 2).Value = "Estado Pago";

                foreach (var item in actividad.Inscripciones)
                {
                    currentRow++;
                    worksheet.Cell(currentRow, 1).Value = item.Alumno.NombreCompleto;
                    worksheet.Cell(currentRow, 2).Value = item.Estado;
                }
                using (var stream = new MemoryStream())
                {
                    workbook.SaveAs(stream);
                    return File(stream.ToArray(), "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", $"Lista_{actividad.Nombre}.xlsx");
                }
            }
        }

        private bool ActividadExists(int id)
        {
            return _context.Actividades.Any(e => e.Id == id);
        }
    }
}