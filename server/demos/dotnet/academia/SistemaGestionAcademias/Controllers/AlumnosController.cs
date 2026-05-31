using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Helpers;
using SistemaGestionAcademias.Models;

namespace SistemaGestionAcademias.Controllers
{
    [Authorize]
    public class AlumnosController : Controller
    {
        private readonly ApplicationDbContext _context;
        private readonly IWebHostEnvironment _webHostEnvironment; // 1. Para manejar archivos

        // Inyectamos el entorno web (Environment)
        public AlumnosController(ApplicationDbContext context, IWebHostEnvironment webHostEnvironment)
        {
            _context = context;
            _webHostEnvironment = webHostEnvironment;
        }

        // GET: Alumnos
        public async Task<IActionResult> Index(string buscar, int? pageNumber)
        {
            if (buscar != null) { pageNumber = 1; }
            else { buscar = ViewData["BusquedaActual"] as string; }

            ViewData["BusquedaActual"] = buscar;

            var alumnos = from a in _context.Alumnos select a;

            if (!String.IsNullOrEmpty(buscar))
            {
                alumnos = alumnos.Where(s => s.Nombres.Contains(buscar) 
                                          || s.Apellidos.Contains(buscar) 
                                          || s.DocumentoIdentidad.Contains(buscar));
            }

            alumnos = alumnos.OrderBy(a => a.Apellidos);
            int pageSize = 5;
            return View(await PaginatedList<Alumno>.CreateAsync(alumnos.AsNoTracking(), pageNumber ?? 1, pageSize));
        }

        // GET: Alumnos/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null) return NotFound();

            var alumno = await _context.Alumnos
                .Include(a => a.Inscripciones)
                    .ThenInclude(i => i.Actividad)
                .FirstOrDefaultAsync(m => m.Id == id);

            if (alumno == null) return NotFound();

            return View(alumno);
        }

        // GET: Alumnos/Create
        public IActionResult Create()
        {
            return View();
        }

        // POST: Alumnos/Create
        // Recibimos 'foto' que viene del input type="file"
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create([Bind("Id,DocumentoIdentidad,Nombres,Apellidos,Telefono,Correo,FechaNacimiento")] Alumno alumno, IFormFile? foto)
        {
            if (ModelState.IsValid)
            {
                // LÓGICA DE GUARDADO DE FOTO
                if (foto != null)
                {
                    string carpetaFotos = Path.Combine(_webHostEnvironment.WebRootPath, "imagenes");
                    // Crear carpeta si no existe
                    if (!Directory.Exists(carpetaFotos)) Directory.CreateDirectory(carpetaFotos);

                    // Nombre único para evitar reemplazar fotos de otros con el mismo nombre
                    string nombreArchivo = Guid.NewGuid().ToString() + Path.GetExtension(foto.FileName);
                    string rutaCompleta = Path.Combine(carpetaFotos, nombreArchivo);

                    using (var fileStream = new FileStream(rutaCompleta, FileMode.Create))
                    {
                        await foto.CopyToAsync(fileStream);
                    }

                    alumno.FotoUrl = nombreArchivo; // Guardamos solo el nombre en BD
                }

                _context.Add(alumno);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(alumno);
        }

        // GET: Alumnos/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var alumno = await _context.Alumnos.FindAsync(id);
            if (alumno == null) return NotFound();
            return View(alumno);
        }

        // POST: Alumnos/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, [Bind("Id,DocumentoIdentidad,Nombres,Apellidos,Telefono,Correo,FechaNacimiento,FotoUrl")] Alumno alumno, IFormFile? foto)
        {
            if (id != alumno.Id) return NotFound();

            if (ModelState.IsValid)
            {
                try
                {
                    // LÓGICA DE ACTUALIZACIÓN DE FOTO
                    if (foto != null)
                    {
                        string carpetaFotos = Path.Combine(_webHostEnvironment.WebRootPath, "imagenes");
                        if (!Directory.Exists(carpetaFotos)) Directory.CreateDirectory(carpetaFotos);

                        // Si ya tenía foto, podríamos borrar la anterior para no llenar el disco (opcional)
                        // if (!string.IsNullOrEmpty(alumno.FotoUrl)) ... borrar archivo viejo ...

                        string nombreArchivo = Guid.NewGuid().ToString() + Path.GetExtension(foto.FileName);
                        string rutaCompleta = Path.Combine(carpetaFotos, nombreArchivo);

                        using (var fileStream = new FileStream(rutaCompleta, FileMode.Create))
                        {
                            await foto.CopyToAsync(fileStream);
                        }

                        alumno.FotoUrl = nombreArchivo;
                    }

                    _context.Update(alumno);
                    await _context.SaveChangesAsync();
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!AlumnoExists(alumno.Id)) return NotFound();
                    else throw;
                }
                return RedirectToAction(nameof(Index));
            }
            return View(alumno);
        }

        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var alumno = await _context.Alumnos.FirstOrDefaultAsync(m => m.Id == id);
            if (alumno == null) return NotFound();
            return View(alumno);
        }

        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var alumno = await _context.Alumnos.FindAsync(id);
            if (alumno != null) _context.Alumnos.Remove(alumno);
            await _context.SaveChangesAsync();
            return RedirectToAction(nameof(Index));
        }

        private bool AlumnoExists(int id)
        {
            return _context.Alumnos.Any(e => e.Id == id);
        }
    }
}