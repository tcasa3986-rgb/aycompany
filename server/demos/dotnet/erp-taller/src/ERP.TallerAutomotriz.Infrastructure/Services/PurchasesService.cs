using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Application.Interfaces;
using ERP.TallerAutomotriz.Domain.Entities.Inventory;
using ERP.TallerAutomotriz.Domain.Entities.Purchases;
using ERP.TallerAutomotriz.Domain.Enums;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace ERP.TallerAutomotriz.Infrastructure.Services;

public class PurchasesService : IPurchasesService
{
    private readonly ApplicationDbContext _ctx;
    public PurchasesService(ApplicationDbContext ctx) => _ctx = ctx;

    // ============ PROVEEDORES ============

    public async Task<List<ProveedorListDto>> ListarProveedoresAsync(string? buscar = null, CancellationToken ct = default)
    {
        var query = _ctx.Proveedores.AsNoTracking().AsQueryable();
        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(p =>
                p.Codigo.ToLower().Contains(b) ||
                p.RazonSocial.ToLower().Contains(b) ||
                p.DocumentoIdentidad.Contains(b));
        }

        return await query
            .OrderBy(p => p.RazonSocial)
            .Select(p => new ProveedorListDto
            {
                Id = p.Id,
                Codigo = p.Codigo,
                RazonSocial = p.RazonSocial,
                DocumentoIdentidad = p.DocumentoIdentidad,
                Telefono = p.Telefono,
                Email = p.Email,
                DiasCredito = p.DiasCredito,
                CalificacionPromedio = (p.CalificacionPrecio + p.CalificacionTiempo + p.CalificacionCalidad) / 3,
                Activo = p.Activo
            })
            .ToListAsync(ct);
    }

    public async Task<ProveedorFormDto?> ObtenerProveedorAsync(int id, CancellationToken ct = default)
    {
        return await _ctx.Proveedores.AsNoTracking()
            .Where(p => p.Id == id)
            .Select(p => new ProveedorFormDto
            {
                Id = p.Id, Codigo = p.Codigo, RazonSocial = p.RazonSocial,
                NombreComercial = p.NombreComercial, DocumentoIdentidad = p.DocumentoIdentidad,
                Direccion = p.Direccion, Telefono = p.Telefono, Email = p.Email,
                Contacto = p.Contacto, DiasCredito = p.DiasCredito, DiasEntrega = p.DiasEntrega,
                CalificacionPrecio = p.CalificacionPrecio, CalificacionTiempo = p.CalificacionTiempo,
                CalificacionCalidad = p.CalificacionCalidad, Notas = p.Notas
            })
            .FirstOrDefaultAsync(ct);
    }

    public async Task<int> CrearProveedorAsync(ProveedorFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var p = new Proveedor
        {
            Codigo = string.IsNullOrWhiteSpace(dto.Codigo) ? await GenerarCodigoProveedorAsync(ct) : dto.Codigo!,
            RazonSocial = dto.RazonSocial,
            NombreComercial = dto.NombreComercial,
            DocumentoIdentidad = dto.DocumentoIdentidad,
            Direccion = dto.Direccion, Telefono = dto.Telefono, Email = dto.Email,
            Contacto = dto.Contacto, DiasCredito = dto.DiasCredito, DiasEntrega = dto.DiasEntrega,
            CalificacionPrecio = dto.CalificacionPrecio, CalificacionTiempo = dto.CalificacionTiempo,
            CalificacionCalidad = dto.CalificacionCalidad, Notas = dto.Notas,
            CreadoPor = usuario, Activo = true
        };
        _ctx.Proveedores.Add(p);
        await _ctx.SaveChangesAsync(ct);
        return p.Id;
    }

    public async Task ActualizarProveedorAsync(ProveedorFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var p = await _ctx.Proveedores.FirstOrDefaultAsync(x => x.Id == dto.Id, ct)
            ?? throw new InvalidOperationException("Proveedor no encontrado");
        p.RazonSocial = dto.RazonSocial; p.NombreComercial = dto.NombreComercial;
        p.DocumentoIdentidad = dto.DocumentoIdentidad; p.Direccion = dto.Direccion;
        p.Telefono = dto.Telefono; p.Email = dto.Email; p.Contacto = dto.Contacto;
        p.DiasCredito = dto.DiasCredito; p.DiasEntrega = dto.DiasEntrega;
        p.CalificacionPrecio = dto.CalificacionPrecio; p.CalificacionTiempo = dto.CalificacionTiempo;
        p.CalificacionCalidad = dto.CalificacionCalidad; p.Notas = dto.Notas;
        p.ModificadoPor = usuario;
        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarProveedorAsync(int id, CancellationToken ct = default)
    {
        var p = await _ctx.Proveedores.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("Proveedor no encontrado");
        p.Activo = false;
        await _ctx.SaveChangesAsync(ct);
    }

    private async Task<string> GenerarCodigoProveedorAsync(CancellationToken ct)
    {
        var ult = await _ctx.Proveedores.Where(p => p.Codigo.StartsWith("PROV-"))
            .OrderByDescending(p => p.Codigo).Select(p => p.Codigo).FirstOrDefaultAsync(ct);
        if (ult == null) return "PROV-001";
        if (int.TryParse(ult[5..], out var n)) return $"PROV-{(n + 1):D3}";
        return $"PROV-{Random.Shared.Next(100, 999)}";
    }

    // ============ ÓRDENES DE COMPRA ============

    public async Task<List<OrdenCompraListDto>> ListarOrdenesCompraAsync(string? buscar = null, CancellationToken ct = default)
    {
        var query = _ctx.OrdenesCompra.AsNoTracking().Include(o => o.Proveedor).AsQueryable();
        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(o => o.Numero.ToLower().Contains(b) || o.Proveedor!.RazonSocial.ToLower().Contains(b));
        }
        return await query
            .OrderByDescending(o => o.Fecha)
            .Select(o => new OrdenCompraListDto
            {
                Id = o.Id, Numero = o.Numero, Fecha = o.Fecha,
                FechaEntregaEsperada = o.FechaEntregaEsperada,
                Proveedor = o.Proveedor!.RazonSocial,
                Total = o.Total, Estado = o.Estado
            })
            .ToListAsync(ct);
    }

    public async Task<OrdenCompraDetalleDto?> ObtenerOrdenCompraAsync(int id, CancellationToken ct = default)
    {
        var o = await _ctx.OrdenesCompra.AsNoTracking()
            .Include(x => x.Proveedor)
            .Include(x => x.AlmacenDestino)
            .Include(x => x.Detalles).ThenInclude(d => d.Repuesto)
            .FirstOrDefaultAsync(x => x.Id == id, ct);
        if (o == null) return null;
        return new OrdenCompraDetalleDto
        {
            Id = o.Id, Numero = o.Numero, Fecha = o.Fecha,
            FechaEntregaEsperada = o.FechaEntregaEsperada,
            ProveedorId = o.ProveedorId, Proveedor = o.Proveedor!.RazonSocial,
            DocumentoProveedor = o.Proveedor.DocumentoIdentidad,
            AlmacenDestinoId = o.AlmacenDestinoId,
            AlmacenDestino = o.AlmacenDestino?.Nombre,
            Subtotal = o.Subtotal, Impuesto = o.Impuesto, Total = o.Total,
            Estado = o.Estado, Observaciones = o.Observaciones,
            Items = o.Detalles.Select(d => new DetalleOCDto
            {
                Id = d.Id, RepuestoId = d.RepuestoId,
                CodigoRepuesto = d.Repuesto!.CodigoInterno,
                DescripcionRepuesto = d.Repuesto.Descripcion,
                Cantidad = d.Cantidad, CantidadRecibida = d.CantidadRecibida,
                PrecioUnitario = d.PrecioUnitario, Subtotal = d.Subtotal
            }).ToList()
        };
    }

    public async Task<int> CrearOrdenCompraAsync(OrdenCompraFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var numero = string.IsNullOrWhiteSpace(dto.Numero) ? await GenerarNumeroOCAsync(ct) : dto.Numero!;
        var almacenDest = dto.AlmacenDestinoId
            ?? (await _ctx.Almacenes.FirstOrDefaultAsync(a => a.EsPrincipal, ct))?.Id;

        decimal subtotal = dto.Items.Sum(i => i.Cantidad * i.PrecioUnitario);
        decimal impuesto = subtotal * 0.18m;
        decimal total = subtotal + impuesto;

        var oc = new OrdenCompra
        {
            Numero = numero, Fecha = dto.Fecha,
            FechaEntregaEsperada = dto.FechaEntregaEsperada,
            ProveedorId = dto.ProveedorId,
            AlmacenDestinoId = almacenDest,
            Subtotal = subtotal, Impuesto = impuesto, Total = total,
            Estado = EstadoOrdenCompra.Enviada,
            Observaciones = dto.Observaciones,
            CreadoPor = usuario, Activo = true
        };
        foreach (var i in dto.Items)
        {
            oc.Detalles.Add(new DetalleOrdenCompra
            {
                RepuestoId = i.RepuestoId,
                Cantidad = i.Cantidad,
                CantidadRecibida = 0,
                PrecioUnitario = i.PrecioUnitario,
                Subtotal = i.Cantidad * i.PrecioUnitario
            });
        }
        _ctx.OrdenesCompra.Add(oc);
        await _ctx.SaveChangesAsync(ct);
        return oc.Id;
    }

    public async Task RecibirMercaderiaAsync(RecepcionMercaderiaDto dto, string? usuario, CancellationToken ct = default)
    {
        var oc = await _ctx.OrdenesCompra
            .Include(x => x.Detalles).ThenInclude(d => d.Repuesto)
            .FirstOrDefaultAsync(x => x.Id == dto.OrdenCompraId, ct)
            ?? throw new InvalidOperationException("Orden de compra no encontrada");
        if (oc.Estado is EstadoOrdenCompra.Recibida or EstadoOrdenCompra.Cancelada)
            throw new InvalidOperationException("OC ya cerrada");
        if (!oc.AlmacenDestinoId.HasValue)
            throw new InvalidOperationException("OC sin almacén destino");

        foreach (var item in dto.Items)
        {
            var detalle = oc.Detalles.FirstOrDefault(d => d.Id == item.DetalleId);
            if (detalle == null) continue;
            if (item.CantidadRecibida <= 0) continue;
            var totalRecibida = detalle.CantidadRecibida + item.CantidadRecibida;
            if (totalRecibida > detalle.Cantidad)
                throw new InvalidOperationException($"Cantidad recibida excede la solicitada para {detalle.Repuesto?.Descripcion}");

            detalle.CantidadRecibida = totalRecibida;

            // Actualizar stock del repuesto
            if (detalle.Repuesto != null)
            {
                var saldoAnterior = detalle.Repuesto.StockActual;
                var saldoNuevo = saldoAnterior + item.CantidadRecibida;

                // Recalcular costo promedio ponderado
                if (detalle.PrecioUnitario > 0 && saldoNuevo > 0)
                {
                    var valorAnterior = saldoAnterior * detalle.Repuesto.CostoPromedio;
                    var valorEntrada = item.CantidadRecibida * detalle.PrecioUnitario;
                    detalle.Repuesto.CostoPromedio = (valorAnterior + valorEntrada) / saldoNuevo;
                    detalle.Repuesto.CostoUltimo = detalle.PrecioUnitario;
                }
                detalle.Repuesto.StockActual = saldoNuevo;

                // Crear movimiento
                _ctx.MovimientosInventario.Add(new MovimientoInventario
                {
                    RepuestoId = detalle.RepuestoId,
                    AlmacenId = oc.AlmacenDestinoId.Value,
                    Tipo = TipoMovimientoInventario.EntradaCompra,
                    Fecha = DateTime.UtcNow,
                    Cantidad = item.CantidadRecibida,
                    CostoUnitario = detalle.PrecioUnitario,
                    SaldoAnterior = saldoAnterior,
                    SaldoNuevo = saldoNuevo,
                    NumeroDocumento = oc.Numero,
                    TipoDocumento = "OC",
                    DocumentoReferenciaId = oc.Id,
                    Justificacion = dto.Observaciones,
                    Usuario = usuario
                });

                // Actualizar StockAlmacenes
                var stockAlm = await _ctx.StockAlmacenes
                    .FirstOrDefaultAsync(s => s.RepuestoId == detalle.RepuestoId && s.AlmacenId == oc.AlmacenDestinoId.Value, ct);
                if (stockAlm == null)
                {
                    _ctx.StockAlmacenes.Add(new StockAlmacen
                    {
                        RepuestoId = detalle.RepuestoId,
                        AlmacenId = oc.AlmacenDestinoId.Value,
                        Cantidad = item.CantidadRecibida
                    });
                }
                else
                {
                    stockAlm.Cantidad += item.CantidadRecibida;
                }
            }
        }

        // Actualizar estado de la OC
        bool todoRecibido = oc.Detalles.All(d => d.CantidadRecibida >= d.Cantidad);
        bool algoRecibido = oc.Detalles.Any(d => d.CantidadRecibida > 0);
        oc.Estado = todoRecibido ? EstadoOrdenCompra.Recibida
            : algoRecibido ? EstadoOrdenCompra.RecibidaParcial
            : oc.Estado;
        oc.ModificadoPor = usuario;

        await _ctx.SaveChangesAsync(ct);
    }

    public async Task CancelarOrdenCompraAsync(int id, string? usuario, CancellationToken ct = default)
    {
        var oc = await _ctx.OrdenesCompra.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("OC no encontrada");
        if (oc.Estado == EstadoOrdenCompra.Recibida)
            throw new InvalidOperationException("No se puede cancelar una OC ya recibida");
        oc.Estado = EstadoOrdenCompra.Cancelada;
        oc.ModificadoPor = usuario;
        await _ctx.SaveChangesAsync(ct);
    }

    private async Task<string> GenerarNumeroOCAsync(CancellationToken ct)
    {
        var year = DateTime.UtcNow.Year;
        var pre = $"OC-{year}-";
        var ult = await _ctx.OrdenesCompra.Where(o => o.Numero.StartsWith(pre))
            .OrderByDescending(o => o.Numero).Select(o => o.Numero).FirstOrDefaultAsync(ct);
        if (ult == null) return $"{pre}0001";
        var suf = ult[pre.Length..];
        if (int.TryParse(suf, out var n)) return $"{pre}{(n + 1):D4}";
        return $"{pre}{Random.Shared.Next(1000, 9999)}";
    }

    // ============ CUENTAS POR PAGAR ============

    public async Task<List<CuentaPagarDto>> ListarCuentasPagarAsync(bool soloVencidas = false, CancellationToken ct = default)
    {
        var hoy = DateTime.Today;
        var query = _ctx.CuentasPagar.AsNoTracking()
            .Include(c => c.Proveedor)
            .Include(c => c.OrdenCompra)
            .Where(c => c.Estado != EstadoCuentaPagar.Pagada);

        if (soloVencidas) query = query.Where(c => c.FechaVencimiento < hoy);

        return await query
            .OrderBy(c => c.FechaVencimiento)
            .Select(c => new CuentaPagarDto
            {
                Id = c.Id,
                ProveedorId = c.ProveedorId,
                Proveedor = c.Proveedor!.RazonSocial,
                OrdenCompraId = c.OrdenCompraId,
                NumeroOC = c.OrdenCompra != null ? c.OrdenCompra.Numero : null,
                NumeroFactura = c.NumeroFactura,
                FechaEmision = c.FechaEmision,
                FechaVencimiento = c.FechaVencimiento,
                Monto = c.Monto,
                MontoPagado = c.MontoPagado,
                Saldo = c.Saldo,
                Estado = c.Estado
            })
            .ToListAsync(ct);
    }

    public async Task<int> RegistrarCuentaPagarAsync(CuentaPagarFormDto dto, CancellationToken ct = default)
    {
        var c = new Domain.Entities.Purchases.CuentaPagar
        {
            ProveedorId = dto.ProveedorId,
            OrdenCompraId = dto.OrdenCompraId,
            NumeroFactura = dto.NumeroFactura,
            FechaEmision = dto.FechaEmision,
            FechaVencimiento = dto.FechaVencimiento,
            Monto = dto.Monto,
            MontoPagado = 0,
            Saldo = dto.Monto,
            Estado = EstadoCuentaPagar.Pendiente,
            Activo = true
        };
        _ctx.CuentasPagar.Add(c);
        await _ctx.SaveChangesAsync(ct);
        return c.Id;
    }

    public async Task RegistrarPagoCuentaAsync(int cuentaId, decimal monto, CancellationToken ct = default)
    {
        var c = await _ctx.CuentasPagar.FirstOrDefaultAsync(x => x.Id == cuentaId, ct)
            ?? throw new InvalidOperationException("Cuenta no encontrada");
        if (monto > c.Saldo) throw new InvalidOperationException($"Monto excede el saldo (S/. {c.Saldo:N2})");

        c.MontoPagado += monto;
        c.Saldo = c.Monto - c.MontoPagado;
        c.Estado = c.Saldo <= 0 ? EstadoCuentaPagar.Pagada : EstadoCuentaPagar.PagadaParcial;
        await _ctx.SaveChangesAsync(ct);
    }
}
