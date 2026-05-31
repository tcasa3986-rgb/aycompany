using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;
using SistemaGestionAcademias.Models;
using System;
using System.Collections.Generic;
using System.Linq; // VITAL para .Sum() y .Where()
using System.Threading.Tasks; // VITAL para async Task

namespace SistemaGestionAcademias.Controllers
{
    [Authorize]
    public class CajaController : Controller
    {
        private readonly ApplicationDbContext _context;

        public CajaController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: Caja (DASHBOARD)
        public async Task<IActionResult> Index()
        {
            var hoy = DateTime.Today;
            
            // 1. Verificar si hay caja del día
            var cajaHoy = await _context.CajasDiarias.FirstOrDefaultAsync(c => c.Fecha == hoy);

            // Si no existe, mandamos a Abrir
            if (cajaHoy == null)
            {
                return View("Abrir", new CajaDiaria { Fecha = hoy, MontoInicial = 0 });
            }

            // 2. Calcular montos
            var ingresos = await _context.Inscripciones
                .Include(i => i.Alumno)
                .Include(i => i.Actividad)
                .Where(i => i.FechaInscripcion.Date == hoy && i.Estado == "Pagado")
                .ToListAsync();

            var gastos = await _context.Gastos
                .Where(g => g.FechaHora.Date == hoy)
                .OrderByDescending(g => g.FechaHora)
                .ToListAsync();

            // Totales
            ViewBag.MontoInicial = cajaHoy.MontoInicial;
            ViewBag.TotalIngresos = ingresos.Sum(i => i.MontoFinal);
            
            // Desglose
            ViewBag.TotalEfectivo = ingresos.Where(i => i.MetodoPago == "Efectivo").Sum(i => i.MontoFinal);
            ViewBag.TotalDigital = ingresos.Where(i => i.MetodoPago != "Efectivo").Sum(i => i.MontoFinal);
            ViewBag.TotalGastos = gastos.Sum(g => g.Monto);
            
            // Saldo Teórico = (Inicial + Ingresos Efectivo) - Gastos
            // Nota: El cast (decimal) asegura que no falle si la suma es 0
            ViewBag.SaldoTeoricoEfectivo = (cajaHoy.MontoInicial + (decimal)ViewBag.TotalEfectivo) - (decimal)ViewBag.TotalGastos;

            ViewData["EstadoCaja"] = cajaHoy.Estado;
            ViewData["CajaId"] = cajaHoy.Id;
            
            // Listas para las tablas
            ViewBag.ListaIngresos = ingresos;
            ViewBag.ListaGastos = gastos;

            return View("Index", cajaHoy);
        }

        // POST: Abrir Caja
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Abrir(CajaDiaria caja)
        {
            caja.Fecha = DateTime.Today;
            caja.Estado = "Abierta";
            caja.MontoFinalReal = 0;
            caja.MontoFinalCalculado = 0;

            _context.Add(caja);
            await _context.SaveChangesAsync();
            
            TempData["AlertTipo"] = "success";
            TempData["AlertMensaje"] = "¡Caja Aperturada Correctamente!";
            
            return RedirectToAction(nameof(Index));
        }

        // POST: Registrar Gasto
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> RegistrarGasto(string descripcion, decimal monto)
        {
            var gasto = new Gasto
            {
                Descripcion = descripcion,
                Monto = monto,
                FechaHora = DateTime.Now,
                RegistradoPor = User.Identity?.Name ?? "Admin"
            };

            _context.Add(gasto);
            await _context.SaveChangesAsync();

            TempData["AlertTipo"] = "info";
            TempData["AlertMensaje"] = "Salida de dinero registrada.";
            
            return RedirectToAction(nameof(Index));
        }

        // GET: Vista Cerrar
        public async Task<IActionResult> Cerrar(int id)
        {
            var caja = await _context.CajasDiarias.FindAsync(id);
            if (caja == null || caja.Estado == "Cerrada") return RedirectToAction(nameof(Index));

            var hoy = caja.Fecha;
            
            // Recalcular montos para mostrar al usuario antes de cerrar
            var ingresosEfectivo = await _context.Inscripciones
                .Where(i => i.FechaInscripcion.Date == hoy && i.Estado == "Pagado" && i.MetodoPago == "Efectivo")
                .SumAsync(i => i.MontoFinal);
            
            var gastos = await _context.Gastos
                .Where(g => g.FechaHora.Date == hoy)
                .SumAsync(g => g.Monto);

            caja.MontoFinalCalculado = (caja.MontoInicial + ingresosEfectivo) - gastos;

            return View(caja);
        }

        // POST: Confirmar Cierre
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> ConfirmarCierre(int id, decimal MontoFinalReal, string Observaciones)
        {
            var caja = await _context.CajasDiarias.FindAsync(id);
            if (caja == null) return NotFound();

            // Guardamos datos de cierre
            caja.MontoFinalReal = MontoFinalReal;
            caja.Observaciones = Observaciones;
            caja.Estado = "Cerrada";

            // Guardamos también el cálculo final del sistema para historial
            var hoy = caja.Fecha;
            var ingresosEfectivo = await _context.Inscripciones
                .Where(i => i.FechaInscripcion.Date == hoy && i.Estado == "Pagado" && i.MetodoPago == "Efectivo")
                .SumAsync(i => i.MontoFinal);
            var gastos = await _context.Gastos.Where(g => g.FechaHora.Date == hoy).SumAsync(g => g.Monto);
            
            caja.MontoFinalCalculado = (caja.MontoInicial + ingresosEfectivo) - gastos;

            _context.Update(caja);
            await _context.SaveChangesAsync();

            TempData["AlertTipo"] = "success";
            TempData["AlertMensaje"] = "Caja Cerrada. ¡Buen trabajo!";

            return RedirectToAction(nameof(Index));
        }
    }
}