using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Models;

namespace SistemaGestionAcademias.Controllers
{
    [Authorize]
    public class InstructoresController : Controller
    {
        private readonly ApplicationDbContext _context;

        public InstructoresController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: Instructores
        public async Task<IActionResult> Index()
        {
            return View(await _context.Instructores.ToListAsync());
        }

        // GET: Instructores/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null) return NotFound();

            var instructor = await _context.Instructores
                .Include(i => i.Actividades)
                    .ThenInclude(a => a.Categoria)
                .Include(i => i.Actividades)
                    .ThenInclude(a => a.Inscripciones)
                .FirstOrDefaultAsync(m => m.Id == id);

            if (instructor == null) return NotFound();

            return View(instructor);
        }

        // GET: Instructores/Create
        public IActionResult Create()
        {
            return View();
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create([Bind("Id,Nombres,Apellidos,Especialidad,Telefono,Correo,Activo")] Instructor instructor)
        {
            if (ModelState.IsValid)
            {
                _context.Add(instructor);
                await _context.SaveChangesAsync();
                TempData["AlertTipo"] = "success";
                TempData["AlertMensaje"] = "Instructor registrado exitosamente.";
                return RedirectToAction(nameof(Index));
            }
            return View(instructor);
        }

        // GET: Instructores/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var instructor = await _context.Instructores.FindAsync(id);
            if (instructor == null) return NotFound();
            return View(instructor);
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, [Bind("Id,Nombres,Apellidos,Especialidad,Telefono,Correo,Activo")] Instructor instructor)
        {
            if (id != instructor.Id) return NotFound();

            if (ModelState.IsValid)
            {
                try
                {
                    _context.Update(instructor);
                    await _context.SaveChangesAsync();
                    TempData["AlertTipo"] = "success";
                    TempData["AlertMensaje"] = "Datos del instructor actualizados.";
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!InstructorExists(instructor.Id)) return NotFound();
                    else throw;
                }
                return RedirectToAction(nameof(Index));
            }
            return View(instructor);
        }

        // ---------------- ZONA RESTRINGIDA (SOLO ADMIN) ----------------

        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var instructor = await _context.Instructores.FirstOrDefaultAsync(m => m.Id == id);
            if (instructor == null) return NotFound();
            return View(instructor);
        }

        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var instructor = await _context.Instructores.FindAsync(id);
            if (instructor != null) _context.Instructores.Remove(instructor);
            await _context.SaveChangesAsync();
            TempData["AlertTipo"] = "success";
            TempData["AlertMensaje"] = "Instructor eliminado correctamente.";
            return RedirectToAction(nameof(Index));
        }

        // --- NUEVO MÉTODO: CAMBIAR ESTADO RÁPIDO ---
        public async Task<IActionResult> ToggleStatus(int? id)
        {
            if (id == null) return NotFound();

            var instructor = await _context.Instructores.FindAsync(id);
            if (instructor == null) return NotFound();

            // Invertimos el estado (Si es true pasa a false, y viceversa)
            instructor.Activo = !instructor.Activo;
            
            _context.Update(instructor);
            await _context.SaveChangesAsync();

            // Mensaje Feedback
            TempData["AlertTipo"] = "success";
            TempData["AlertMensaje"] = instructor.Activo 
                ? $"El instructor {instructor.Nombres} ahora está ACTIVO." 
                : $"El instructor {instructor.Nombres} ha sido DESACTIVADO.";

            return RedirectToAction(nameof(Index));
        }

        private bool InstructorExists(int id)
        {
            return _context.Instructores.Any(e => e.Id == id);
        }
    }
}