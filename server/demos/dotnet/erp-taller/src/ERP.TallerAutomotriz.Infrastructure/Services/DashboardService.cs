using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Application.Interfaces;
using ERP.TallerAutomotriz.Domain.Enums;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace ERP.TallerAutomotriz.Infrastructure.Services;

public class DashboardService : IDashboardService
{
    private readonly ApplicationDbContext _ctx;
    public DashboardService(ApplicationDbContext ctx) => _ctx = ctx;

    public async Task<DashboardKpiDto> ObtenerKpisAsync(CancellationToken ct = default)
    {
        var hoy = DateTime.UtcNow.Date;
        var inicioMes = new DateTime(hoy.Year, hoy.Month, 1);

        var enTaller = await _ctx.OrdenesTrabajo.CountAsync(o =>
            o.Estado != EstadoOT.Entregado && o.Estado != EstadoOT.Cancelado, ct);

        var abiertas = await _ctx.OrdenesTrabajo.CountAsync(o =>
            o.Estado == EstadoOT.Recibido || o.Estado == EstadoOT.EnDiagnostico ||
            o.Estado == EstadoOT.EnReparacion || o.Estado == EstadoOT.EnEsperaRepuesto, ct);

        var terminadasHoy = await _ctx.OrdenesTrabajo.CountAsync(o =>
            o.Estado == EstadoOT.Entregado && o.FechaEntregaReal != null && o.FechaEntregaReal >= hoy, ct);

        var ingresosHoy = await _ctx.Pagos
            .Where(p => p.Fecha >= hoy)
            .SumAsync(p => (decimal?)p.Monto, ct) ?? 0;

        var tecnicosActivos = await _ctx.Tecnicos.CountAsync(t => t.Activo, ct);

        var citasHoy = await _ctx.Citas.CountAsync(c =>
            c.FechaHora >= hoy && c.FechaHora < hoy.AddDays(1), ct);

        var repuestosBajoStock = await _ctx.Repuestos.CountAsync(r =>
            r.Activo && r.StockActual <= r.StockMinimo, ct);

        var facturasVencidas = await _ctx.Facturas.Where(f =>
            f.Estado == EstadoFactura.Emitida || f.Estado == EstadoFactura.PagadaParcial)
            .Where(f => f.FechaVencimiento < hoy)
            .ToListAsync(ct);

        var ticketPromedio = await _ctx.Facturas
            .Where(f => f.Fecha >= inicioMes && f.Estado != EstadoFactura.Anulada && f.Estado != EstadoFactura.Borrador)
            .AverageAsync(f => (decimal?)f.Total, ct) ?? 0;

        var totalOTUlt30 = await _ctx.OrdenesTrabajo.CountAsync(o => o.FechaIngreso >= hoy.AddDays(-30), ct);
        var clientesUlt30 = await _ctx.OrdenesTrabajo.Where(o => o.FechaIngreso >= hoy.AddDays(-30))
            .Select(o => o.ClienteId).Distinct().CountAsync(ct);
        var clientesPrev30 = await _ctx.OrdenesTrabajo
            .Where(o => o.FechaIngreso >= hoy.AddDays(-60) && o.FechaIngreso < hoy.AddDays(-30))
            .Select(o => o.ClienteId).Distinct().CountAsync(ct);

        var retencion = clientesPrev30 == 0 ? 0d : (double)clientesUlt30 / clientesPrev30 * 100;

        return new DashboardKpiDto
        {
            VehiculosEnTaller = enTaller,
            OrdenesAbiertas = abiertas,
            OrdenesTerminadasHoy = terminadasHoy,
            IngresosHoy = ingresosHoy,
            TecnicosActivos = tecnicosActivos,
            CitasHoy = citasHoy,
            RepuestosBajoStock = repuestosBajoStock,
            FacturasVencidas = facturasVencidas.Count,
            MontoFacturasVencidas = facturasVencidas.Sum(f => f.SaldoPendiente),
            TicketPromedio = ticketPromedio,
            PorcentajeOcupacion = enTaller == 0 ? 0 : Math.Min(100, (double)enTaller / Math.Max(1, tecnicosActivos) * 50),
            TasaRetencion = Math.Min(100, retencion)
        };
    }

    public async Task<List<TendenciaItemDto>> ObtenerTendenciaSemanalAsync(CancellationToken ct = default)
    {
        var hoy = DateTime.UtcNow.Date;
        var lista = new List<TendenciaItemDto>();
        for (int i = 6; i >= 0; i--)
        {
            var dia = hoy.AddDays(-i);
            var siguiente = dia.AddDays(1);
            var monto = await _ctx.Pagos.Where(p => p.Fecha >= dia && p.Fecha < siguiente)
                .SumAsync(p => (decimal?)p.Monto, ct) ?? 0;
            var cant = await _ctx.OrdenesTrabajo.CountAsync(o => o.FechaIngreso >= dia && o.FechaIngreso < siguiente, ct);
            lista.Add(new TendenciaItemDto { Etiqueta = dia.ToString("ddd"), Valor = monto, Cantidad = cant });
        }
        return lista;
    }

    public async Task<List<TendenciaItemDto>> ObtenerTendenciaMensualAsync(CancellationToken ct = default)
    {
        var hoy = DateTime.UtcNow.Date;
        var lista = new List<TendenciaItemDto>();
        for (int i = 5; i >= 0; i--)
        {
            var primero = new DateTime(hoy.Year, hoy.Month, 1).AddMonths(-i);
            var siguiente = primero.AddMonths(1);
            var monto = await _ctx.Pagos.Where(p => p.Fecha >= primero && p.Fecha < siguiente)
                .SumAsync(p => (decimal?)p.Monto, ct) ?? 0;
            var cant = await _ctx.OrdenesTrabajo.CountAsync(o => o.FechaIngreso >= primero && o.FechaIngreso < siguiente, ct);
            lista.Add(new TendenciaItemDto { Etiqueta = primero.ToString("MMM"), Valor = monto, Cantidad = cant });
        }
        return lista;
    }

    public async Task<List<AlertaDto>> ObtenerAlertasAsync(CancellationToken ct = default)
    {
        var alertas = new List<AlertaDto>();
        var hoy = DateTime.UtcNow.Date;

        var bajoStock = await _ctx.Repuestos.Where(r => r.Activo && r.StockActual <= r.StockMinimo).Take(5).ToListAsync(ct);
        foreach (var r in bajoStock)
        {
            alertas.Add(new AlertaDto
            {
                Tipo = "StockBajo",
                Severidad = "warning",
                Titulo = "Stock bajo",
                Mensaje = $"{r.Descripcion} — quedan {r.StockActual} (mín {r.StockMinimo})",
                UrlAccion = "/inventario/repuestos"
            });
        }

        var vencidas = await _ctx.Facturas
            .Where(f => (f.Estado == EstadoFactura.Emitida || f.Estado == EstadoFactura.PagadaParcial) && f.FechaVencimiento < hoy)
            .Take(5).ToListAsync(ct);
        foreach (var f in vencidas)
        {
            alertas.Add(new AlertaDto
            {
                Tipo = "FacturaVencida",
                Severidad = "error",
                Titulo = $"Factura {f.Serie}-{f.Numero} vencida",
                Mensaje = $"Saldo pendiente S/. {f.SaldoPendiente:N2}",
                UrlAccion = "/facturacion/cuentas-cobrar"
            });
        }

        var citasSinTecnico = await _ctx.Citas
            .Where(c => c.FechaHora >= hoy && c.FechaHora < hoy.AddDays(1) && c.TecnicoPreferidoId == null)
            .Take(5).ToListAsync(ct);
        foreach (var c in citasSinTecnico)
        {
            alertas.Add(new AlertaDto
            {
                Tipo = "CitaSinTecnico",
                Severidad = "info",
                Titulo = "Cita sin técnico asignado",
                Mensaje = $"Cita a las {c.FechaHora:HH:mm}",
                UrlAccion = "/agenda"
            });
        }

        return alertas;
    }

    public async Task<List<OrdenTrabajoResumenDto>> ObtenerOrdenesRecientesAsync(int top = 6, CancellationToken ct = default)
    {
        return await _ctx.OrdenesTrabajo
            .Include(o => o.Cliente)
            .Include(o => o.Vehiculo)
            .Include(o => o.TecnicoPrincipal)
            .OrderByDescending(o => o.FechaIngreso)
            .Take(top)
            .Select(o => new OrdenTrabajoResumenDto
            {
                Id = o.Id,
                Numero = o.Numero,
                Cliente = o.Cliente!.NombreRazonSocial,
                Vehiculo = o.Vehiculo!.Marca + " " + o.Vehiculo.Modelo,
                Placa = o.Vehiculo.Placa,
                Estado = o.Estado.ToString(),
                Tecnico = o.TecnicoPrincipal != null ? o.TecnicoPrincipal.Nombres + " " + o.TecnicoPrincipal.Apellidos : null,
                FechaIngreso = o.FechaIngreso,
                Total = o.Total
            })
            .ToListAsync(ct);
    }
}
