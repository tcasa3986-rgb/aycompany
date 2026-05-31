using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Models;
using SistemaGestionAcademias.ViewModels;

namespace SistemaGestionAcademias.Controllers
{
    [Authorize]
    public class AsistenciaController : Controller
    {
        private readonly ApplicationDbContext _context;

        public AsistenciaController(ApplicationDbContext context)
        {
            _context = context;
        }

        // 1. VER CRONOGRAMA DE LA ACTIVIDAD
        public async Task<IActionResult> Index(int actividadId)
        {
            var actividad = await _context.Actividades
                .Include(a => a.Sesiones)
                .FirstOrDefaultAsync(a => a.Id == actividadId);

            if (actividad == null) return NotFound();

            actividad.Sesiones = actividad.Sesiones.OrderBy(s => s.Fecha).ToList();

            return View(actividad);
        }

        // 2. PANTALLA PARA MARCAR ASISTENCIA (GET)
        [HttpGet]
        public async Task<IActionResult> Tomar(int sesionId)
        {
            var sesion = await _context.SesionesClase
                .Include(s => s.Actividad)
                .Include(s => s.Asistencias).ThenInclude(a => a.Alumno)
                .FirstOrDefaultAsync(s => s.Id == sesionId);

            if (sesion == null) return NotFound();

            var modelo = new AsistenciaPlanillaViewModel
            {
                SesionId = sesion.Id,
                
                // --- CORRECCIÓN: Asignamos el ID de la actividad aquí ---
                ActividadId = sesion.ActividadId, 
                
                NombreActividad = sesion.Actividad.Nombre,
                FechaSesion = sesion.Fecha.ToShortDateString(),
                Tema = sesion.TemaDelDia
            };

            // Lógica de carga (Igual que antes)
            if (sesion.AsistenciaTomada && sesion.Asistencias.Any())
            {
                modelo.Asistencias = sesion.Asistencias.Select(a => new AsistenciaItem
                {
                    AsistenciaId = a.Id,
                    AlumnoId = a.AlumnoId,
                    NombreAlumno = a.Alumno.NombreCompleto,
                    Estado = a.Estado,
                    Observacion = a.Observacion
                }).ToList();
            }
            else
            {
                var inscritos = await _context.Inscripciones
                    .Include(i => i.Alumno)
                    .Where(i => i.ActividadId == sesion.ActividadId)
                    .ToListAsync();

                modelo.Asistencias = inscritos.Select(i => new AsistenciaItem
                {
                    AlumnoId = i.AlumnoId,
                    NombreAlumno = i.Alumno.NombreCompleto,
                    Estado = "Presente",
                    Observacion = ""
                }).ToList();
            }

            return View(modelo);
        }

        // 3. GUARDAR LISTA (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Guardar(AsistenciaPlanillaViewModel modelo)
        {
            var sesion = await _context.SesionesClase.FindAsync(modelo.SesionId);
            if (sesion == null) return NotFound();

            sesion.TemaDelDia = modelo.Tema;
            sesion.AsistenciaTomada = true;

            foreach (var item in modelo.Asistencias)
            {
                if (item.AsistenciaId.HasValue && item.AsistenciaId > 0)
                {
                    var asistenciaExistente = await _context.Asistencias.FindAsync(item.AsistenciaId);
                    if (asistenciaExistente != null)
                    {
                        asistenciaExistente.Estado = item.Estado;
                        asistenciaExistente.Observacion = item.Observacion;
                        _context.Update(asistenciaExistente);
                    }
                }
                else
                {
                    var nuevaAsistencia = new Asistencia
                    {
                        SesionClaseId = modelo.SesionId,
                        AlumnoId = item.AlumnoId,
                        Estado = item.Estado,
                        Observacion = item.Observacion
                    };
                    _context.Add(nuevaAsistencia);
                }
            }

            await _context.SaveChangesAsync();

            TempData["AlertTipo"] = "success";
            TempData["AlertMensaje"] = "Asistencia registrada correctamente.";

            // Regresamos al índice usando el ID correcto obtenido de la base de datos
            return RedirectToAction("Index", new { actividadId = sesion.ActividadId });
        }
    }
}