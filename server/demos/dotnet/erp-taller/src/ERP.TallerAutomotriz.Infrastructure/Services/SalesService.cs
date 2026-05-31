using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Application.Interfaces;
using ERP.TallerAutomotriz.Domain.Entities.Sales;
using ERP.TallerAutomotriz.Domain.Enums;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace ERP.TallerAutomotriz.Infrastructure.Services;

public class SalesService : ISalesService
{
    private readonly ApplicationDbContext _ctx;
    public SalesService(ApplicationDbContext ctx) => _ctx = ctx;

    // ============ FACTURAS ============

    public async Task<PagedResult<FacturaListDto>> ListarFacturasAsync(string? buscar, EstadoFactura? estado, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _ctx.Facturas.AsNoTracking().Include(f => f.Cliente).Include(f => f.OrdenTrabajo).AsQueryable();

        if (estado.HasValue) query = query.Where(f => f.Estado == estado.Value);

        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(f =>
                f.Numero.Contains(b) ||
                f.Serie.ToLower().Contains(b) ||
                f.Cliente!.NombreRazonSocial.ToLower().Contains(b));
        }

        var total = await query.CountAsync(ct);

        var items = await query
            .OrderByDescending(f => f.Fecha)
            .ThenByDescending(f => f.Id)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .Select(f => new FacturaListDto
            {
                Id = f.Id,
                Tipo = f.Tipo,
                Serie = f.Serie,
                Numero = f.Numero,
                Fecha = f.Fecha,
                FechaVencimiento = f.FechaVencimiento,
                Cliente = f.Cliente!.NombreRazonSocial,
                NumeroOT = f.OrdenTrabajo != null ? f.OrdenTrabajo.Numero : null,
                Total = f.Total,
                MontoPagado = f.MontoPagado,
                SaldoPendiente = f.SaldoPendiente,
                Estado = f.Estado
            })
            .ToListAsync(ct);

        return new PagedResult<FacturaListDto> { Items = items, Total = total, Page = page, PageSize = pageSize };
    }

    public async Task<FacturaDetalleDto?> ObtenerFacturaAsync(int id, CancellationToken ct = default)
    {
        var f = await _ctx.Facturas.AsNoTracking()
            .Include(x => x.Cliente)
            .Include(x => x.OrdenTrabajo)
            .Include(x => x.Detalles)
            .Include(x => x.Pagos)
            .FirstOrDefaultAsync(x => x.Id == id, ct);
        if (f == null) return null;

        return new FacturaDetalleDto
        {
            Id = f.Id,
            Tipo = f.Tipo,
            Serie = f.Serie,
            Numero = f.Numero,
            Fecha = f.Fecha,
            FechaVencimiento = f.FechaVencimiento,
            ClienteId = f.ClienteId,
            Cliente = f.Cliente!.NombreRazonSocial,
            DocumentoCliente = f.Cliente.DocumentoIdentidad,
            DireccionCliente = f.Cliente.Direccion,
            OrdenTrabajoId = f.OrdenTrabajoId,
            NumeroOT = f.OrdenTrabajo?.Numero,
            Subtotal = f.Subtotal,
            Descuento = f.Descuento,
            BaseImponible = f.BaseImponible,
            PorcentajeImpuesto = f.PorcentajeImpuesto,
            Impuesto = f.Impuesto,
            Total = f.Total,
            MontoPagado = f.MontoPagado,
            SaldoPendiente = f.SaldoPendiente,
            Estado = f.Estado,
            Observaciones = f.Observaciones,
            Detalles = f.Detalles.Select(d => new FacturaItemDto
            {
                Id = d.Id, Descripcion = d.Descripcion, CodigoItem = d.CodigoItem,
                Cantidad = d.Cantidad, PrecioUnitario = d.PrecioUnitario,
                Descuento = d.Descuento, Subtotal = d.Subtotal, Tipo = d.Tipo
            }).ToList(),
            Pagos = f.Pagos.OrderByDescending(p => p.Fecha).Select(p => new PagoListDto
            {
                Id = p.Id, Fecha = p.Fecha, FormaPago = p.FormaPago,
                Monto = p.Monto, NumeroReferencia = p.NumeroReferencia, Observaciones = p.Observaciones
            }).ToList()
        };
    }

    public async Task<int> CrearFacturaAsync(FacturaFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var numero = string.IsNullOrWhiteSpace(dto.Numero) ? await GenerarNumeroFacturaAsync(dto.Serie, ct) : dto.Numero!;

        decimal subtotal = dto.Items.Sum(i => i.Cantidad * i.PrecioUnitario - i.Descuento);
        decimal baseImp = subtotal - dto.Descuento;
        decimal impuesto = baseImp * dto.PorcentajeImpuesto / 100;
        decimal total = baseImp + impuesto;

        var f = new Factura
        {
            Tipo = dto.Tipo,
            Serie = dto.Serie,
            Numero = numero,
            Fecha = dto.Fecha,
            FechaVencimiento = dto.FechaVencimiento,
            ClienteId = dto.ClienteId,
            OrdenTrabajoId = dto.OrdenTrabajoId,
            Subtotal = subtotal,
            Descuento = dto.Descuento,
            BaseImponible = baseImp,
            PorcentajeImpuesto = dto.PorcentajeImpuesto,
            Impuesto = impuesto,
            Total = total,
            MontoPagado = 0,
            SaldoPendiente = total,
            Estado = EstadoFactura.Emitida,
            Observaciones = dto.Observaciones,
            CreadoPor = usuario,
            Activo = true
        };

        foreach (var i in dto.Items)
        {
            f.Detalles.Add(new DetalleFactura
            {
                Descripcion = i.Descripcion,
                CodigoItem = i.CodigoItem,
                Cantidad = i.Cantidad,
                PrecioUnitario = i.PrecioUnitario,
                Descuento = i.Descuento,
                Subtotal = i.Cantidad * i.PrecioUnitario - i.Descuento,
                Tipo = i.Tipo
            });
        }

        _ctx.Facturas.Add(f);

        // Actualiza saldo del cliente
        var cli = await _ctx.Clientes.FirstOrDefaultAsync(c => c.Id == dto.ClienteId, ct);
        if (cli != null) cli.SaldoPendiente += total;

        await _ctx.SaveChangesAsync(ct);
        return f.Id;
    }

    public async Task<int> CrearFacturaDesdeOTAsync(int ordenTrabajoId, string? usuario, CancellationToken ct = default)
    {
        var ot = await _ctx.OrdenesTrabajo.AsNoTracking()
            .Include(o => o.Servicios).ThenInclude(s => s.Servicio)
            .Include(o => o.Repuestos).ThenInclude(r => r.Repuesto)
            .FirstOrDefaultAsync(o => o.Id == ordenTrabajoId, ct)
            ?? throw new InvalidOperationException("OT no encontrada");

        // Verificar si ya existe factura para esta OT
        var existe = await _ctx.Facturas.AnyAsync(f => f.OrdenTrabajoId == ordenTrabajoId && f.Estado != EstadoFactura.Anulada, ct);
        if (existe) throw new InvalidOperationException("Esta OT ya tiene una factura emitida.");

        var dto = new FacturaFormDto
        {
            Tipo = TipoComprobante.Factura,
            Serie = "F001",
            Fecha = DateTime.Today,
            FechaVencimiento = DateTime.Today.AddDays(30),
            ClienteId = ot.ClienteId,
            OrdenTrabajoId = ot.Id,
            PorcentajeImpuesto = 18,
            Items = ot.Servicios.Select(s => new FacturaItemFormDto
            {
                Descripcion = s.Servicio?.Nombre ?? "Servicio",
                CodigoItem = s.Servicio?.Codigo,
                Cantidad = s.Cantidad,
                PrecioUnitario = s.PrecioUnitario,
                Descuento = s.Descuento,
                Tipo = "Servicio"
            }).Concat(ot.Repuestos.Select(r => new FacturaItemFormDto
            {
                Descripcion = r.Repuesto?.Descripcion ?? "Repuesto",
                CodigoItem = r.Repuesto?.CodigoInterno,
                Cantidad = r.Cantidad,
                PrecioUnitario = r.PrecioUnitario,
                Descuento = r.Descuento,
                Tipo = "Repuesto"
            })).ToList()
        };

        return await CrearFacturaAsync(dto, usuario, ct);
    }

    public async Task AnularFacturaAsync(int id, string? usuario, CancellationToken ct = default)
    {
        var f = await _ctx.Facturas.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("Factura no encontrada");
        if (f.Estado == EstadoFactura.Pagada || f.Estado == EstadoFactura.PagadaParcial)
            throw new InvalidOperationException("No se puede anular una factura con pagos. Anule los pagos primero.");

        f.Estado = EstadoFactura.Anulada;
        f.ModificadoPor = usuario;

        var cli = await _ctx.Clientes.FirstOrDefaultAsync(c => c.Id == f.ClienteId, ct);
        if (cli != null) cli.SaldoPendiente -= f.SaldoPendiente;

        await _ctx.SaveChangesAsync(ct);
    }

    private async Task<string> GenerarNumeroFacturaAsync(string serie, CancellationToken ct)
    {
        var ult = await _ctx.Facturas
            .Where(f => f.Serie == serie)
            .OrderByDescending(f => f.Numero)
            .Select(f => f.Numero)
            .FirstOrDefaultAsync(ct);
        if (ult == null) return "00000001";
        if (int.TryParse(ult, out var n)) return $"{(n + 1):D8}";
        return $"{Random.Shared.Next(10000000, 99999999)}";
    }

    // ============ PAGOS ============

    public async Task<int> RegistrarPagoAsync(PagoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var f = await _ctx.Facturas.FirstOrDefaultAsync(x => x.Id == dto.FacturaId, ct)
            ?? throw new InvalidOperationException("Factura no encontrada");
        if (f.Estado == EstadoFactura.Anulada) throw new InvalidOperationException("Factura anulada");
        if (dto.Monto > f.SaldoPendiente)
            throw new InvalidOperationException($"Monto excede el saldo pendiente (S/. {f.SaldoPendiente:N2})");

        // Buscar caja abierta si no se especificó
        var cajaId = dto.CajaId;
        if (!cajaId.HasValue)
        {
            var cajaAbierta = await _ctx.Cajas.FirstOrDefaultAsync(c => c.Abierta, ct);
            cajaId = cajaAbierta?.Id;
        }

        var pago = new Pago
        {
            FacturaId = dto.FacturaId,
            Fecha = DateTime.UtcNow,
            FormaPago = dto.FormaPago,
            Monto = dto.Monto,
            NumeroReferencia = dto.NumeroReferencia,
            Observaciones = dto.Observaciones,
            CajaId = cajaId,
            CreadoPor = usuario,
            Activo = true
        };

        _ctx.Pagos.Add(pago);

        f.MontoPagado += dto.Monto;
        f.SaldoPendiente = f.Total - f.MontoPagado;
        f.Estado = f.SaldoPendiente <= 0 ? EstadoFactura.Pagada : EstadoFactura.PagadaParcial;
        f.ModificadoPor = usuario;

        var cli = await _ctx.Clientes.FirstOrDefaultAsync(c => c.Id == f.ClienteId, ct);
        if (cli != null) cli.SaldoPendiente -= dto.Monto;

        await _ctx.SaveChangesAsync(ct);
        return pago.Id;
    }

    // ============ COTIZACIONES ============

    public async Task<List<CotizacionListDto>> ListarCotizacionesAsync(string? buscar = null, CancellationToken ct = default)
    {
        var query = _ctx.Cotizaciones.AsNoTracking().Include(c => c.Cliente).AsQueryable();
        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(c => c.Numero.ToLower().Contains(b) || c.Cliente!.NombreRazonSocial.ToLower().Contains(b));
        }

        return await query
            .OrderByDescending(c => c.Fecha)
            .Select(c => new CotizacionListDto
            {
                Id = c.Id,
                Numero = c.Numero,
                Fecha = c.Fecha,
                ValidaHasta = c.ValidaHasta,
                Cliente = c.Cliente!.NombreRazonSocial,
                Total = c.Total,
                Estado = c.Estado
            }).ToListAsync(ct);
    }

    public async Task<CotizacionFormDto?> ObtenerCotizacionAsync(int id, CancellationToken ct = default)
    {
        var c = await _ctx.Cotizaciones.AsNoTracking()
            .Include(x => x.Detalles)
            .FirstOrDefaultAsync(x => x.Id == id, ct);
        if (c == null) return null;
        return new CotizacionFormDto
        {
            Id = c.Id,
            Numero = c.Numero,
            Fecha = c.Fecha,
            ValidaHasta = c.ValidaHasta,
            ClienteId = c.ClienteId,
            OrdenTrabajoId = c.OrdenTrabajoId,
            Observaciones = c.Observaciones,
            Items = c.Detalles.Select(d => new FacturaItemFormDto
            {
                Descripcion = d.Descripcion,
                Cantidad = d.Cantidad,
                PrecioUnitario = d.PrecioUnitario,
                Tipo = d.Tipo
            }).ToList()
        };
    }

    public async Task<int> CrearCotizacionAsync(CotizacionFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var numero = string.IsNullOrWhiteSpace(dto.Numero) ? await GenerarNumeroCotizacionAsync(ct) : dto.Numero!;
        decimal subtotal = dto.Items.Sum(i => i.Cantidad * i.PrecioUnitario);
        decimal impuesto = subtotal * 0.18m;
        decimal total = subtotal + impuesto;

        var c = new Cotizacion
        {
            Numero = numero,
            Fecha = dto.Fecha,
            ValidaHasta = dto.ValidaHasta,
            ClienteId = dto.ClienteId,
            OrdenTrabajoId = dto.OrdenTrabajoId,
            Subtotal = subtotal,
            Impuesto = impuesto,
            Total = total,
            Estado = "Pendiente",
            Observaciones = dto.Observaciones,
            CreadoPor = usuario,
            Activo = true
        };
        foreach (var i in dto.Items)
        {
            c.Detalles.Add(new DetalleCotizacion
            {
                Descripcion = i.Descripcion,
                Cantidad = i.Cantidad,
                PrecioUnitario = i.PrecioUnitario,
                Subtotal = i.Cantidad * i.PrecioUnitario,
                Tipo = i.Tipo
            });
        }
        _ctx.Cotizaciones.Add(c);
        await _ctx.SaveChangesAsync(ct);
        return c.Id;
    }

    public async Task<int> ConvertirCotizacionEnFacturaAsync(int cotizacionId, string? usuario, CancellationToken ct = default)
    {
        var c = await _ctx.Cotizaciones.AsNoTracking()
            .Include(x => x.Detalles)
            .FirstOrDefaultAsync(x => x.Id == cotizacionId, ct)
            ?? throw new InvalidOperationException("Cotización no encontrada");
        if (c.Estado == "Convertida") throw new InvalidOperationException("Esta cotización ya fue convertida");

        var dto = new FacturaFormDto
        {
            Tipo = TipoComprobante.Factura,
            Serie = "F001",
            Fecha = DateTime.Today,
            FechaVencimiento = DateTime.Today.AddDays(30),
            ClienteId = c.ClienteId,
            OrdenTrabajoId = c.OrdenTrabajoId,
            PorcentajeImpuesto = 18,
            Observaciones = $"Generada desde cotización {c.Numero}",
            Items = c.Detalles.Select(d => new FacturaItemFormDto
            {
                Descripcion = d.Descripcion,
                Cantidad = d.Cantidad,
                PrecioUnitario = d.PrecioUnitario,
                Tipo = d.Tipo
            }).ToList()
        };

        var facturaId = await CrearFacturaAsync(dto, usuario, ct);

        // Marcar cotización como convertida
        var coti = await _ctx.Cotizaciones.FirstOrDefaultAsync(x => x.Id == cotizacionId, ct);
        if (coti != null)
        {
            coti.Estado = "Convertida";
            coti.ModificadoPor = usuario;
            await _ctx.SaveChangesAsync(ct);
        }
        return facturaId;
    }

    private async Task<string> GenerarNumeroCotizacionAsync(CancellationToken ct)
    {
        var year = DateTime.UtcNow.Year;
        var pre = $"COT-{year}-";
        var ult = await _ctx.Cotizaciones.Where(c => c.Numero.StartsWith(pre))
            .OrderByDescending(c => c.Numero).Select(c => c.Numero).FirstOrDefaultAsync(ct);
        if (ult == null) return $"{pre}0001";
        var suf = ult[pre.Length..];
        if (int.TryParse(suf, out var n)) return $"{pre}{(n + 1):D4}";
        return $"{pre}{Random.Shared.Next(1000, 9999)}";
    }

    // ============ CAJA ============

    public async Task<List<CajaEstadoDto>> ListarCajasAsync(CancellationToken ct = default)
    {
        var cajas = await _ctx.Cajas.AsNoTracking().OrderBy(c => c.Nombre).ToListAsync(ct);
        var resultado = new List<CajaEstadoDto>();
        foreach (var c in cajas)
        {
            resultado.Add(await BuildEstadoCajaAsync(c.Id, ct) ?? new CajaEstadoDto { Id = c.Id, Codigo = c.Codigo, Nombre = c.Nombre });
        }
        return resultado;
    }

    public async Task<CajaEstadoDto?> ObtenerEstadoCajaAsync(int cajaId, CancellationToken ct = default)
    {
        return await BuildEstadoCajaAsync(cajaId, ct);
    }

    private async Task<CajaEstadoDto?> BuildEstadoCajaAsync(int cajaId, CancellationToken ct)
    {
        var caja = await _ctx.Cajas.AsNoTracking().FirstOrDefaultAsync(c => c.Id == cajaId, ct);
        if (caja == null) return null;

        var desde = caja.FechaApertura ?? DateTime.Today;
        var pagos = await _ctx.Pagos.AsNoTracking()
            .Where(p => p.CajaId == cajaId && p.Fecha >= desde)
            .ToListAsync(ct);

        decimal efectivo = pagos.Where(p => p.FormaPago == FormaPago.Efectivo).Sum(p => p.Monto);
        decimal tarjeta = pagos.Where(p => p.FormaPago is FormaPago.TarjetaCredito or FormaPago.TarjetaDebito).Sum(p => p.Monto);
        decimal transf = pagos.Where(p => p.FormaPago == FormaPago.Transferencia).Sum(p => p.Monto);
        decimal otros = pagos.Where(p => p.FormaPago is FormaPago.PagoQR or FormaPago.Cheque).Sum(p => p.Monto);
        decimal totalIng = efectivo + tarjeta + transf + otros;

        return new CajaEstadoDto
        {
            Id = caja.Id,
            Codigo = caja.Codigo,
            Nombre = caja.Nombre,
            Abierta = caja.Abierta,
            FechaApertura = caja.FechaApertura,
            UsuarioApertura = caja.UsuarioApertura,
            MontoApertura = caja.MontoApertura,
            IngresosEfectivo = efectivo,
            IngresosTarjeta = tarjeta,
            IngresosTransferencia = transf,
            IngresosOtros = otros,
            TotalIngresos = totalIng,
            MontoEsperado = caja.MontoApertura + efectivo,
            CantidadPagos = pagos.Count
        };
    }

    public async Task AbrirCajaAsync(AperturaCajaDto dto, string? usuario, CancellationToken ct = default)
    {
        var c = await _ctx.Cajas.FirstOrDefaultAsync(x => x.Id == dto.CajaId, ct)
            ?? throw new InvalidOperationException("Caja no encontrada");
        if (c.Abierta) throw new InvalidOperationException("La caja ya está abierta");
        c.Abierta = true;
        c.FechaApertura = DateTime.UtcNow;
        c.UsuarioApertura = usuario;
        c.MontoApertura = dto.MontoApertura;
        c.MontoCierre = 0;
        c.Observaciones = dto.Observaciones;
        await _ctx.SaveChangesAsync(ct);
    }

    public async Task CerrarCajaAsync(CierreCajaDto dto, string? usuario, CancellationToken ct = default)
    {
        var c = await _ctx.Cajas.FirstOrDefaultAsync(x => x.Id == dto.CajaId, ct)
            ?? throw new InvalidOperationException("Caja no encontrada");
        if (!c.Abierta) throw new InvalidOperationException("La caja ya está cerrada");
        c.Abierta = false;
        c.FechaCierre = DateTime.UtcNow;
        c.UsuarioCierre = usuario;
        c.MontoCierre = dto.MontoCierreFisico;
        c.Observaciones = dto.Observaciones;
        await _ctx.SaveChangesAsync(ct);
    }

    // ============ CUENTAS POR COBRAR ============

    public async Task<List<CuentaCobrarDto>> ListarCuentasCobrarAsync(bool soloVencidas = false, CancellationToken ct = default)
    {
        var hoy = DateTime.Today;
        var query = _ctx.Facturas.AsNoTracking().Include(f => f.Cliente)
            .Where(f => f.Estado != EstadoFactura.Pagada && f.Estado != EstadoFactura.Anulada && f.SaldoPendiente > 0);

        if (soloVencidas) query = query.Where(f => f.FechaVencimiento < hoy);

        return await query
            .OrderBy(f => f.FechaVencimiento)
            .Select(f => new CuentaCobrarDto
            {
                FacturaId = f.Id,
                Comprobante = f.Serie + "-" + f.Numero,
                Fecha = f.Fecha,
                FechaVencimiento = f.FechaVencimiento,
                Cliente = f.Cliente!.NombreRazonSocial,
                Total = f.Total,
                SaldoPendiente = f.SaldoPendiente
            })
            .ToListAsync(ct);
    }
}
