using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Application.Interfaces;
using ERP.TallerAutomotriz.Domain.Entities.Inventory;
using ERP.TallerAutomotriz.Domain.Enums;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace ERP.TallerAutomotriz.Infrastructure.Services;

public class InventoryService : IInventoryService
{
    private readonly ApplicationDbContext _ctx;
    public InventoryService(ApplicationDbContext ctx) => _ctx = ctx;

    // ============ REPUESTOS ============

    public async Task<PagedResult<RepuestoListDto>> ListarRepuestosAsync(string? buscar, int? categoriaId, bool soloBajoStock, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _ctx.Repuestos.AsNoTracking().Include(r => r.Categoria).AsQueryable();

        if (categoriaId.HasValue)
            query = query.Where(r => r.CategoriaId == categoriaId.Value);

        if (soloBajoStock)
            query = query.Where(r => r.StockActual <= r.StockMinimo);

        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(r =>
                r.CodigoInterno.ToLower().Contains(b) ||
                r.Descripcion.ToLower().Contains(b) ||
                (r.CodigoOEM != null && r.CodigoOEM.ToLower().Contains(b)) ||
                (r.CodigoBarras != null && r.CodigoBarras.Contains(b)));
        }

        var total = await query.CountAsync(ct);

        var items = await query
            .OrderBy(r => r.Descripcion)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .Select(r => new RepuestoListDto
            {
                Id = r.Id,
                CodigoInterno = r.CodigoInterno,
                CodigoOEM = r.CodigoOEM,
                Descripcion = r.Descripcion,
                Categoria = r.Categoria != null ? r.Categoria.Nombre : null,
                UnidadMedida = r.UnidadMedida,
                StockActual = r.StockActual,
                StockMinimo = r.StockMinimo,
                PrecioVenta = r.PrecioVenta,
                CostoPromedio = r.CostoPromedio,
                Ubicacion = r.Ubicacion,
                Activo = r.Activo
            })
            .ToListAsync(ct);

        return new PagedResult<RepuestoListDto>
        {
            Items = items,
            Total = total,
            Page = page,
            PageSize = pageSize
        };
    }

    public async Task<List<RepuestoListDto>> ListarTodosRepuestosAsync(CancellationToken ct = default)
    {
        return await _ctx.Repuestos.AsNoTracking().Where(r => r.Activo)
            .OrderBy(r => r.Descripcion)
            .Select(r => new RepuestoListDto
            {
                Id = r.Id,
                CodigoInterno = r.CodigoInterno,
                Descripcion = r.Descripcion,
                UnidadMedida = r.UnidadMedida,
                StockActual = r.StockActual,
                PrecioVenta = r.PrecioVenta,
                Activo = r.Activo
            })
            .ToListAsync(ct);
    }

    public async Task<RepuestoFormDto?> ObtenerRepuestoAsync(int id, CancellationToken ct = default)
    {
        return await _ctx.Repuestos.AsNoTracking()
            .Where(r => r.Id == id)
            .Select(r => new RepuestoFormDto
            {
                Id = r.Id,
                CodigoInterno = r.CodigoInterno,
                CodigoOEM = r.CodigoOEM,
                CodigoBarras = r.CodigoBarras,
                Descripcion = r.Descripcion,
                DescripcionLarga = r.DescripcionLarga,
                CategoriaId = r.CategoriaId,
                UnidadMedida = r.UnidadMedida,
                StockMinimo = r.StockMinimo,
                StockMaximo = r.StockMaximo,
                PrecioVenta = r.PrecioVenta,
                CostoPromedio = r.CostoPromedio,
                Ubicacion = r.Ubicacion,
                MetodoCosteo = r.MetodoCosteo,
                ManejaLote = r.ManejaLote,
                ManejaSerie = r.ManejaSerie,
                TieneGarantia = r.TieneGarantia,
                MesesGarantia = r.MesesGarantia
            })
            .FirstOrDefaultAsync(ct);
    }

    public async Task<int> CrearRepuestoAsync(RepuestoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var repuesto = new Repuesto
        {
            CodigoInterno = string.IsNullOrWhiteSpace(dto.CodigoInterno) ? await GenerarCodigoRepuestoAsync(ct) : dto.CodigoInterno!,
            CodigoOEM = dto.CodigoOEM,
            CodigoBarras = dto.CodigoBarras,
            Descripcion = dto.Descripcion,
            DescripcionLarga = dto.DescripcionLarga,
            CategoriaId = dto.CategoriaId,
            UnidadMedida = dto.UnidadMedida,
            StockActual = 0,
            StockMinimo = dto.StockMinimo,
            StockMaximo = dto.StockMaximo,
            PrecioVenta = dto.PrecioVenta,
            CostoPromedio = dto.CostoPromedio,
            CostoUltimo = dto.CostoPromedio,
            Ubicacion = dto.Ubicacion,
            MetodoCosteo = dto.MetodoCosteo,
            ManejaLote = dto.ManejaLote,
            ManejaSerie = dto.ManejaSerie,
            TieneGarantia = dto.TieneGarantia,
            MesesGarantia = dto.MesesGarantia,
            CreadoPor = usuario,
            Activo = true
        };

        _ctx.Repuestos.Add(repuesto);
        await _ctx.SaveChangesAsync(ct);
        return repuesto.Id;
    }

    public async Task ActualizarRepuestoAsync(RepuestoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var r = await _ctx.Repuestos.FirstOrDefaultAsync(x => x.Id == dto.Id, ct)
            ?? throw new InvalidOperationException("Repuesto no encontrado");

        r.CodigoOEM = dto.CodigoOEM;
        r.CodigoBarras = dto.CodigoBarras;
        r.Descripcion = dto.Descripcion;
        r.DescripcionLarga = dto.DescripcionLarga;
        r.CategoriaId = dto.CategoriaId;
        r.UnidadMedida = dto.UnidadMedida;
        r.StockMinimo = dto.StockMinimo;
        r.StockMaximo = dto.StockMaximo;
        r.PrecioVenta = dto.PrecioVenta;
        r.CostoPromedio = dto.CostoPromedio;
        r.Ubicacion = dto.Ubicacion;
        r.MetodoCosteo = dto.MetodoCosteo;
        r.ManejaLote = dto.ManejaLote;
        r.ManejaSerie = dto.ManejaSerie;
        r.TieneGarantia = dto.TieneGarantia;
        r.MesesGarantia = dto.MesesGarantia;
        r.ModificadoPor = usuario;

        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarRepuestoAsync(int id, CancellationToken ct = default)
    {
        var r = await _ctx.Repuestos.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("Repuesto no encontrado");
        r.Activo = false;
        await _ctx.SaveChangesAsync(ct);
    }

    private async Task<string> GenerarCodigoRepuestoAsync(CancellationToken ct)
    {
        var ultimo = await _ctx.Repuestos
            .Where(r => r.CodigoInterno.StartsWith("REP-"))
            .OrderByDescending(r => r.CodigoInterno)
            .Select(r => r.CodigoInterno)
            .FirstOrDefaultAsync(ct);
        if (ultimo == null) return "REP-0001";
        if (int.TryParse(ultimo[4..], out var n)) return $"REP-{(n + 1):D4}";
        return $"REP-{Random.Shared.Next(1000, 9999)}";
    }

    // ============ CATEGORÍAS ============

    public async Task<List<CategoriaRepuestoDto>> ListarCategoriasAsync(CancellationToken ct = default)
    {
        return await _ctx.CategoriasRepuesto.AsNoTracking()
            .Where(c => c.Activo)
            .OrderBy(c => c.Nombre)
            .Select(c => new CategoriaRepuestoDto { Id = c.Id, Nombre = c.Nombre, CategoriaPadreId = c.CategoriaPadreId })
            .ToListAsync(ct);
    }

    // ============ ALMACENES ============

    public async Task<List<AlmacenDto>> ListarAlmacenesAsync(CancellationToken ct = default)
    {
        return await _ctx.Almacenes.AsNoTracking()
            .OrderBy(a => a.Nombre)
            .Select(a => new AlmacenDto
            {
                Id = a.Id,
                Codigo = a.Codigo,
                Nombre = a.Nombre,
                Direccion = a.Direccion,
                Responsable = a.Responsable,
                EsPrincipal = a.EsPrincipal,
                Activo = a.Activo
            })
            .ToListAsync(ct);
    }

    public async Task<AlmacenFormDto?> ObtenerAlmacenAsync(int id, CancellationToken ct = default)
    {
        return await _ctx.Almacenes.AsNoTracking()
            .Where(a => a.Id == id)
            .Select(a => new AlmacenFormDto
            {
                Id = a.Id,
                Codigo = a.Codigo,
                Nombre = a.Nombre,
                Direccion = a.Direccion,
                Responsable = a.Responsable,
                EsPrincipal = a.EsPrincipal
            })
            .FirstOrDefaultAsync(ct);
    }

    public async Task<int> CrearAlmacenAsync(AlmacenFormDto dto, string? usuario, CancellationToken ct = default)
    {
        // Si marcas como principal, desmarca los demás
        if (dto.EsPrincipal)
        {
            var actuales = await _ctx.Almacenes.Where(x => x.EsPrincipal).ToListAsync(ct);
            foreach (var a in actuales) a.EsPrincipal = false;
        }

        var almacen = new Almacen
        {
            Codigo = dto.Codigo,
            Nombre = dto.Nombre,
            Direccion = dto.Direccion,
            Responsable = dto.Responsable,
            EsPrincipal = dto.EsPrincipal,
            CreadoPor = usuario,
            Activo = true
        };
        _ctx.Almacenes.Add(almacen);
        await _ctx.SaveChangesAsync(ct);
        return almacen.Id;
    }

    public async Task ActualizarAlmacenAsync(AlmacenFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var a = await _ctx.Almacenes.FirstOrDefaultAsync(x => x.Id == dto.Id, ct)
            ?? throw new InvalidOperationException("Almacén no encontrado");

        if (dto.EsPrincipal && !a.EsPrincipal)
        {
            var otros = await _ctx.Almacenes.Where(x => x.EsPrincipal && x.Id != dto.Id).ToListAsync(ct);
            foreach (var o in otros) o.EsPrincipal = false;
        }

        a.Codigo = dto.Codigo;
        a.Nombre = dto.Nombre;
        a.Direccion = dto.Direccion;
        a.Responsable = dto.Responsable;
        a.EsPrincipal = dto.EsPrincipal;
        a.ModificadoPor = usuario;
        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarAlmacenAsync(int id, CancellationToken ct = default)
    {
        var a = await _ctx.Almacenes.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("Almacén no encontrado");
        if (a.EsPrincipal)
            throw new InvalidOperationException("No se puede desactivar el almacén principal.");
        a.Activo = false;
        await _ctx.SaveChangesAsync(ct);
    }

    // ============ MOVIMIENTOS ============

    public async Task<PagedResult<MovimientoListDto>> ListarMovimientosAsync(int? repuestoId, int? almacenId, DateTime? desde, DateTime? hasta, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _ctx.MovimientosInventario.AsNoTracking()
            .Include(m => m.Repuesto)
            .Include(m => m.Almacen)
            .AsQueryable();

        if (repuestoId.HasValue) query = query.Where(m => m.RepuestoId == repuestoId.Value);
        if (almacenId.HasValue) query = query.Where(m => m.AlmacenId == almacenId.Value);
        if (desde.HasValue) query = query.Where(m => m.Fecha >= desde.Value);
        if (hasta.HasValue) query = query.Where(m => m.Fecha < hasta.Value.AddDays(1));

        var total = await query.CountAsync(ct);

        var items = await query
            .OrderByDescending(m => m.Fecha)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .Select(m => new MovimientoListDto
            {
                Id = m.Id,
                Fecha = m.Fecha,
                Tipo = m.Tipo,
                RepuestoId = m.RepuestoId,
                CodigoRepuesto = m.Repuesto!.CodigoInterno,
                DescripcionRepuesto = m.Repuesto.Descripcion,
                Almacen = m.Almacen!.Nombre,
                Cantidad = m.Cantidad,
                CostoUnitario = m.CostoUnitario,
                SaldoNuevo = m.SaldoNuevo,
                NumeroDocumento = m.NumeroDocumento,
                TipoDocumento = m.TipoDocumento,
                Justificacion = m.Justificacion,
                Usuario = m.Usuario
            })
            .ToListAsync(ct);

        return new PagedResult<MovimientoListDto>
        {
            Items = items,
            Total = total,
            Page = page,
            PageSize = pageSize
        };
    }

    public async Task<int> RegistrarMovimientoAsync(MovimientoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var repuesto = await _ctx.Repuestos.FirstOrDefaultAsync(r => r.Id == dto.RepuestoId, ct)
            ?? throw new InvalidOperationException("Repuesto no encontrado");
        var almacen = await _ctx.Almacenes.FirstOrDefaultAsync(a => a.Id == dto.AlmacenId, ct)
            ?? throw new InvalidOperationException("Almacén no encontrado");

        var saldoAnterior = repuesto.StockActual;
        decimal saldoNuevo = saldoAnterior;

        bool esEntrada = dto.Tipo is TipoMovimientoInventario.EntradaCompra
                              or TipoMovimientoInventario.DevolucionCliente
                              or TipoMovimientoInventario.AjustePositivo
                              or TipoMovimientoInventario.TrasladoEntrada;

        if (esEntrada)
        {
            saldoNuevo = saldoAnterior + dto.Cantidad;

            // Recalcular costo promedio ponderado en entradas con costo
            if (dto.CostoUnitario > 0)
            {
                var valorAnterior = saldoAnterior * repuesto.CostoPromedio;
                var valorEntrada = dto.Cantidad * dto.CostoUnitario;
                if (saldoNuevo > 0)
                    repuesto.CostoPromedio = (valorAnterior + valorEntrada) / saldoNuevo;
                repuesto.CostoUltimo = dto.CostoUnitario;
            }
        }
        else
        {
            if (dto.Cantidad > saldoAnterior)
                throw new InvalidOperationException($"Stock insuficiente. Disponible: {saldoAnterior} {repuesto.UnidadMedida}");
            saldoNuevo = saldoAnterior - dto.Cantidad;
        }

        repuesto.StockActual = saldoNuevo;

        var mov = new MovimientoInventario
        {
            RepuestoId = dto.RepuestoId,
            AlmacenId = dto.AlmacenId,
            Tipo = dto.Tipo,
            Fecha = DateTime.UtcNow,
            Cantidad = dto.Cantidad,
            CostoUnitario = dto.CostoUnitario,
            SaldoAnterior = saldoAnterior,
            SaldoNuevo = saldoNuevo,
            NumeroDocumento = dto.NumeroDocumento,
            TipoDocumento = dto.TipoDocumento,
            Justificacion = dto.Justificacion,
            Usuario = usuario
        };

        _ctx.MovimientosInventario.Add(mov);

        // Mantener StockAlmacenes sincronizado
        var stockAlm = await _ctx.StockAlmacenes
            .FirstOrDefaultAsync(s => s.RepuestoId == dto.RepuestoId && s.AlmacenId == dto.AlmacenId, ct);
        if (stockAlm == null)
        {
            stockAlm = new StockAlmacen
            {
                RepuestoId = dto.RepuestoId,
                AlmacenId = dto.AlmacenId,
                Cantidad = esEntrada ? dto.Cantidad : -dto.Cantidad
            };
            _ctx.StockAlmacenes.Add(stockAlm);
        }
        else
        {
            stockAlm.Cantidad = esEntrada ? stockAlm.Cantidad + dto.Cantidad : stockAlm.Cantidad - dto.Cantidad;
        }

        await _ctx.SaveChangesAsync(ct);
        return mov.Id;
    }
}
