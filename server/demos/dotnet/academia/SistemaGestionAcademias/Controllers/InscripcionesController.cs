using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.Rendering;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Models;

namespace SistemaGestionAcademias.Controllers
{
    [Authorize]
    public class InscripcionesController : Controller
    {
        private readonly ApplicationDbContext _context;

        public InscripcionesController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: Inscripciones
        public async Task<IActionResult> Index()
        {
            var applicationDbContext = _context.Inscripciones
                .Include(i => i.Actividad)
                .Include(i => i.Alumno)
                .OrderByDescending(i => i.FechaInscripcion);
            return View(await applicationDbContext.ToListAsync());
        }

        [HttpGet]
        public async Task<JsonResult> ObtenerInfoActividad(int id)
        {
            var actividad = await _context.Actividades.FindAsync(id);
            if (actividad == null) return Json(null);

            return Json(new { 
                costo = actividad.Costo,
                cupos = actividad.CupoMaximo
            });
        }

        // GET: Inscripciones/Create
        public IActionResult Create()
        {
            ViewData["ActividadId"] = new SelectList(_context.Actividades.Where(a => a.Activo), "Id", "Nombre");
            ViewData["AlumnoId"] = new SelectList(_context.Alumnos, "Id", "NombreCompleto");
            return View();
        }

        // POST: Inscripciones/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create([Bind("Id,FechaInscripcion,Estado,AlumnoId,ActividadId,DescuentoAplicado,MetodoPago")] Inscripcion inscripcion)
        {
            if (ModelState.IsValid)
            {
                bool yaExiste = await _context.Inscripciones
                    .AnyAsync(i => i.AlumnoId == inscripcion.AlumnoId && i.ActividadId == inscripcion.ActividadId);

                if (yaExiste)
                {
                    TempData["AlertTipo"] = "warning";
                    TempData["AlertMensaje"] = "Este alumno ya está inscrito en el taller seleccionado.";
                }
                else
                {
                    var actividad = await _context.Actividades.FindAsync(inscripcion.ActividadId);
                    
                    // CÁLCULO DE PRECIOS
                    inscripcion.MontoOriginal = actividad.Costo;
                    inscripcion.MontoFinal = actividad.Costo;

                    if (inscripcion.DescuentoAplicado == "Hermanos") inscripcion.MontoFinal = actividad.Costo * 0.90m;
                    else if (inscripcion.DescuentoAplicado == "Referido") inscripcion.MontoFinal = actividad.Costo * 0.95m;
                    else if (inscripcion.DescuentoAplicado == "Beca") 
                    {
                        inscripcion.MontoFinal = 0;
                        inscripcion.Estado = "Becado";
                        inscripcion.MetodoPago = null;
                    }

                    if (inscripcion.Estado == "Pagado" && string.IsNullOrEmpty(inscripcion.MetodoPago))
                    {
                        ModelState.AddModelError("MetodoPago", "Debe seleccionar un método de pago.");
                        TempData["AlertTipo"] = "error";
                        TempData["AlertMensaje"] = "Falta método de pago.";
                    }
                    else
                    {
                        int cantidadInscritos = await _context.Inscripciones.CountAsync(i => i.ActividadId == inscripcion.ActividadId);

                        if (cantidadInscritos >= actividad.CupoMaximo)
                        {
                            TempData["AlertTipo"] = "error";
                            TempData["AlertMensaje"] = "No hay cupos disponibles.";
                        }
                        else
                        {
                            _context.Add(inscripcion);
                            await _context.SaveChangesAsync();
                            TempData["AlertTipo"] = "success";
                            TempData["AlertMensaje"] = "Matrícula registrada correctamente.";
                            return RedirectToAction(nameof(Index));
                        }
                    }
                }
            }
            ViewData["ActividadId"] = new SelectList(_context.Actividades.Where(a => a.Activo), "Id", "Nombre", inscripcion.ActividadId);
            ViewData["AlumnoId"] = new SelectList(_context.Alumnos, "Id", "NombreCompleto", inscripcion.AlumnoId);
            return View(inscripcion);
        }

        // GET: Inscripciones/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();

            var inscripcion = await _context.Inscripciones.FindAsync(id);
            if (inscripcion == null) return NotFound();
            
            ViewData["ActividadId"] = new SelectList(_context.Actividades, "Id", "Nombre", inscripcion.ActividadId);
            ViewData["AlumnoId"] = new SelectList(_context.Alumnos, "Id", "NombreCompleto", inscripcion.AlumnoId);
            return View(inscripcion);
        }

        // POST: Inscripciones/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        // NOTA: Quitamos MontoFinal del Bind, lo calculamos aquí para seguridad
        public async Task<IActionResult> Edit(int id, [Bind("Id,FechaInscripcion,Estado,AlumnoId,ActividadId,DescuentoAplicado,MetodoPago")] Inscripcion inscripcion)
        {
            if (id != inscripcion.Id) return NotFound();

            if (ModelState.IsValid)
            {
                try
                {
                    // 1. BLINDAJE FINANCIERO: Recalcular precio real desde la base de datos
                    var actividad = await _context.Actividades.FindAsync(inscripcion.ActividadId);
                    
                    inscripcion.MontoOriginal = actividad.Costo; // Recuperamos costo real
                    inscripcion.MontoFinal = actividad.Costo;    // Reiniciamos cálculo

                    // Aplicamos descuento nuevamente
                    if (inscripcion.DescuentoAplicado == "Hermanos") inscripcion.MontoFinal = actividad.Costo * 0.90m;
                    else if (inscripcion.DescuentoAplicado == "Referido") inscripcion.MontoFinal = actividad.Costo * 0.95m;
                    else if (inscripcion.DescuentoAplicado == "Beca") 
                    {
                        inscripcion.MontoFinal = 0;
                        inscripcion.Estado = "Becado";
                        inscripcion.MetodoPago = null;
                    }

                    // Validación de método de pago en edición
                    if (inscripcion.Estado == "Pagado" && string.IsNullOrEmpty(inscripcion.MetodoPago))
                    {
                        ModelState.AddModelError("MetodoPago", "Debe seleccionar un método de pago.");
                        ViewData["ActividadId"] = new SelectList(_context.Actividades, "Id", "Nombre", inscripcion.ActividadId);
                        ViewData["AlumnoId"] = new SelectList(_context.Alumnos, "Id", "NombreCompleto", inscripcion.AlumnoId);
                        return View(inscripcion);
                    }

                    _context.Update(inscripcion);
                    await _context.SaveChangesAsync();
                    
                    TempData["AlertTipo"] = "success";
                    TempData["AlertMensaje"] = "Matrícula actualizada correctamente.";
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!InscripcionExists(inscripcion.Id)) return NotFound();
                    else throw;
                }
                return RedirectToAction(nameof(Index));
            }
            ViewData["ActividadId"] = new SelectList(_context.Actividades, "Id", "Nombre", inscripcion.ActividadId);
            ViewData["AlumnoId"] = new SelectList(_context.Alumnos, "Id", "NombreCompleto", inscripcion.AlumnoId);
            return View(inscripcion);
        }

        // DELETE y COMPROBANTE (Se mantienen igual)
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var inscripcion = await _context.Inscripciones.Include(i => i.Actividad).Include(i => i.Alumno).FirstOrDefaultAsync(m => m.Id == id);
            if (inscripcion == null) return NotFound();
            return View(inscripcion);
        }

        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var inscripcion = await _context.Inscripciones.FindAsync(id);
            if (inscripcion != null) _context.Inscripciones.Remove(inscripcion);
            await _context.SaveChangesAsync();
            TempData["AlertTipo"] = "success";
            TempData["AlertMensaje"] = "Matrícula eliminada.";
            return RedirectToAction(nameof(Index));
        }

        public async Task<IActionResult> Comprobante(int? id)
        {
            if (id == null) return NotFound();
            var inscripcion = await _context.Inscripciones
                .Include(i => i.Alumno)
                .Include(i => i.Actividad).ThenInclude(a => a.Instructor)
                .Include(i => i.Actividad).ThenInclude(a => a.Categoria)
                .FirstOrDefaultAsync(m => m.Id == id);
            if (inscripcion == null) return NotFound();
            return View(inscripcion);
        }

        private bool InscripcionExists(int id)
        {
            return _context.Inscripciones.Any(e => e.Id == id);
        }
    }
}