using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Application.Interfaces;
using ERP.TallerAutomotriz.Domain.Entities.Workshop;
using ERP.TallerAutomotriz.Domain.Enums;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace ERP.TallerAutomotriz.Infrastructure.Services;

public class WorkshopService : IWorkshopService
{
    private readonly ApplicationDbContext _ctx;
    public WorkshopService(ApplicationDbContext ctx) => _ctx = ctx;

    // ============ SERVICIOS ============

    public async Task<List<ServicioListDto>> ListarServiciosAsync(string? buscar = null, CancellationToken ct = default)
    {
        var query = _ctx.Servicios.AsNoTracking().Include(s => s.Categoria).AsQueryable();
        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(s =>
                s.Codigo.ToLower().Contains(b) ||
                s.Nombre.ToLower().Contains(b));
        }

        return await query
            .OrderBy(s => s.Nombre)
            .Select(s => new ServicioListDto
            {
                Id = s.Id,
                Codigo = s.Codigo,
                Nombre = s.Nombre,
                Tipo = s.Tipo,
                Categoria = s.Categoria != null ? s.Categoria.Nombre : null,
                PrecioEstandar = s.PrecioEstandar,
                TiempoEstimadoMinutos = s.TiempoEstimadoMinutos,
                EsPaquete = s.EsPaquete,
                Activo = s.Activo
            })
            .ToListAsync(ct);
    }

    public async Task<ServicioFormDto?> ObtenerServicioAsync(int id, CancellationToken ct = default)
    {
        return await _ctx.Servicios.AsNoTracking()
            .Where(s => s.Id == id)
            .Select(s => new ServicioFormDto
            {
                Id = s.Id,
                Codigo = s.Codigo,
                Nombre = s.Nombre,
                Descripcion = s.Descripcion,
                Tipo = s.Tipo,
                CategoriaId = s.CategoriaId,
                PrecioEstandar = s.PrecioEstandar,
                TiempoEstimadoMinutos = s.TiempoEstimadoMinutos,
                CostoManoObra = s.CostoManoObra,
                EsPaquete = s.EsPaquete,
                Notas = s.Notas
            })
            .FirstOrDefaultAsync(ct);
    }

    public async Task<int> CrearServicioAsync(ServicioFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var s = new Servicio
        {
            Codigo = string.IsNullOrWhiteSpace(dto.Codigo) ? await GenerarCodigoServicioAsync(ct) : dto.Codigo!,
            Nombre = dto.Nombre,
            Descripcion = dto.Descripcion,
            Tipo = dto.Tipo,
            CategoriaId = dto.CategoriaId,
            PrecioEstandar = dto.PrecioEstandar,
            TiempoEstimadoMinutos = dto.TiempoEstimadoMinutos,
            CostoManoObra = dto.CostoManoObra,
            EsPaquete = dto.EsPaquete,
            Notas = dto.Notas,
            CreadoPor = usuario,
            Activo = true
        };
        _ctx.Servicios.Add(s);
        await _ctx.SaveChangesAsync(ct);
        return s.Id;
    }

    public async Task ActualizarServicioAsync(ServicioFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var s = await _ctx.Servicios.FirstOrDefaultAsync(x => x.Id == dto.Id, ct)
            ?? throw new InvalidOperationException("Servicio no encontrado");
        s.Nombre = dto.Nombre;
        s.Descripcion = dto.Descripcion;
        s.Tipo = dto.Tipo;
        s.CategoriaId = dto.CategoriaId;
        s.PrecioEstandar = dto.PrecioEstandar;
        s.TiempoEstimadoMinutos = dto.TiempoEstimadoMinutos;
        s.CostoManoObra = dto.CostoManoObra;
        s.EsPaquete = dto.EsPaquete;
        s.Notas = dto.Notas;
        s.ModificadoPor = usuario;
        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarServicioAsync(int id, CancellationToken ct = default)
    {
        var s = await _ctx.Servicios.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("Servicio no encontrado");
        s.Activo = false;
        await _ctx.SaveChangesAsync(ct);
    }

    public async Task<List<CategoriaServicioDto>> ListarCategoriasServicioAsync(CancellationToken ct = default)
    {
        return await _ctx.CategoriasServicio.AsNoTracking().Where(c => c.Activo)
            .OrderBy(c => c.Nombre)
            .Select(c => new CategoriaServicioDto { Id = c.Id, Nombre = c.Nombre })
            .ToListAsync(ct);
    }

    private async Task<string> GenerarCodigoServicioAsync(CancellationToken ct)
    {
        var ult = await _ctx.Servicios
            .Where(s => s.Codigo.StartsWith("SRV-"))
            .OrderByDescending(s => s.Codigo)
            .Select(s => s.Codigo)
            .FirstOrDefaultAsync(ct);
        if (ult == null) return "SRV-001";
        if (int.TryParse(ult[4..], out var n)) return $"SRV-{(n + 1):D3}";
        return $"SRV-{Random.Shared.Next(100, 999)}";
    }

    // ============ ÓRDENES DE TRABAJO ============

    public async Task<PagedResult<OrdenTrabajoListDto>> ListarOrdenesAsync(string? buscar, EstadoOT? estado, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _ctx.OrdenesTrabajo.AsNoTracking()
            .Include(o => o.Cliente)
            .Include(o => o.Vehiculo)
            .Include(o => o.TecnicoPrincipal)
            .AsQueryable();

        if (estado.HasValue)
            query = query.Where(o => o.Estado == estado.Value);

        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(o =>
                o.Numero.ToLower().Contains(b) ||
                o.Cliente!.NombreRazonSocial.ToLower().Contains(b) ||
                o.Vehiculo!.Placa.ToLower().Contains(b));
        }

        var total = await query.CountAsync(ct);

        var items = await query
            .OrderByDescending(o => o.FechaIngreso)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .Select(o => new OrdenTrabajoListDto
            {
                Id = o.Id,
                Numero = o.Numero,
                FechaIngreso = o.FechaIngreso,
                FechaEntregaEstimada = o.FechaEntregaEstimada,
                Cliente = o.Cliente!.NombreRazonSocial,
                Vehiculo = o.Vehiculo!.Marca + " " + o.Vehiculo.Modelo,
                Placa = o.Vehiculo.Placa,
                Estado = o.Estado,
                Prioridad = o.Prioridad,
                Tecnico = o.TecnicoPrincipal != null ? o.TecnicoPrincipal.Nombres + " " + o.TecnicoPrincipal.Apellidos : null,
                Total = o.Total
            })
            .ToListAsync(ct);

        return new PagedResult<OrdenTrabajoListDto>
        {
            Items = items, Total = total, Page = page, PageSize = pageSize
        };
    }

    public async Task<List<OrdenTrabajoListDto>> ListarTodasOrdenesAsync(CancellationToken ct = default)
    {
        return await _ctx.OrdenesTrabajo.AsNoTracking()
            .Include(o => o.Cliente).Include(o => o.Vehiculo).Include(o => o.TecnicoPrincipal)
            .Where(o => o.Estado != EstadoOT.Entregado && o.Estado != EstadoOT.Cancelado)
            .OrderByDescending(o => o.FechaIngreso)
            .Select(o => new OrdenTrabajoListDto
            {
                Id = o.Id,
                Numero = o.Numero,
                FechaIngreso = o.FechaIngreso,
                FechaEntregaEstimada = o.FechaEntregaEstimada,
                Cliente = o.Cliente!.NombreRazonSocial,
                Vehiculo = o.Vehiculo!.Marca + " " + o.Vehiculo.Modelo,
                Placa = o.Vehiculo.Placa,
                Estado = o.Estado,
                Prioridad = o.Prioridad,
                Tecnico = o.TecnicoPrincipal != null ? o.TecnicoPrincipal.Nombres + " " + o.TecnicoPrincipal.Apellidos : null,
                Total = o.Total
            })
            .ToListAsync(ct);
    }

    public async Task<OrdenTrabajoDetalleDto?> ObtenerOrdenAsync(int id, CancellationToken ct = default)
    {
        var ot = await _ctx.OrdenesTrabajo.AsNoTracking()
            .Include(o => o.Cliente)
            .Include(o => o.Vehiculo)
            .Include(o => o.TecnicoPrincipal)
            .Include(o => o.Servicios).ThenInclude(s => s.Servicio)
            .Include(o => o.Historial)
            .FirstOrDefaultAsync(o => o.Id == id, ct);
        if (ot == null) return null;

        return new OrdenTrabajoDetalleDto
        {
            Id = ot.Id,
            Numero = ot.Numero,
            FechaIngreso = ot.FechaIngreso,
            FechaEntregaEstimada = ot.FechaEntregaEstimada,
            FechaEntregaReal = ot.FechaEntregaReal,
            Estado = ot.Estado,
            Prioridad = ot.Prioridad,
            ClienteId = ot.ClienteId,
            Cliente = ot.Cliente!.NombreRazonSocial,
            DocumentoCliente = ot.Cliente.DocumentoIdentidad,
            TelefonoCliente = ot.Cliente.TelefonoPrincipal,
            VehiculoId = ot.VehiculoId,
            Placa = ot.Vehiculo!.Placa,
            Marca = ot.Vehiculo.Marca,
            Modelo = ot.Vehiculo.Modelo,
            Anio = ot.Vehiculo.Anio,
            KilometrajeIngreso = ot.KilometrajeIngreso,
            Tecnico = ot.TecnicoPrincipal != null ? $"{ot.TecnicoPrincipal.Nombres} {ot.TecnicoPrincipal.Apellidos}" : null,
            FallasReportadasCliente = ot.FallasReportadasCliente,
            SintomasDiagnosticados = ot.SintomasDiagnosticados,
            SubtotalManoObra = ot.SubtotalManoObra,
            SubtotalRepuestos = ot.SubtotalRepuestos,
            Descuento = ot.Descuento,
            Impuesto = ot.Impuesto,
            Total = ot.Total,
            Servicios = ot.Servicios.Select(s => new ServicioOTDto
            {
                Id = s.Id,
                ServicioId = s.ServicioId,
                Servicio = s.Servicio!.Nombre,
                Cantidad = s.Cantidad,
                PrecioUnitario = s.PrecioUnitario,
                Subtotal = s.Subtotal,
                Completado = s.Completado
            }).ToList(),
            Historial = ot.Historial.OrderByDescending(h => h.Fecha).Select(h => new HistorialEstadoDto
            {
                Fecha = h.Fecha,
                EstadoAnterior = h.EstadoAnterior,
                EstadoNuevo = h.EstadoNuevo,
                Usuario = h.Usuario,
                Comentario = h.Comentario
            }).ToList()
        };
    }

    public async Task<int> CrearOrdenAsync(OrdenTrabajoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var numero = string.IsNullOrWhiteSpace(dto.Numero) ? await GenerarNumeroOTAsync(ct) : dto.Numero!;

        var ot = new OrdenTrabajo
        {
            Numero = numero,
            FechaIngreso = DateTime.UtcNow,
            FechaEntregaEstimada = dto.FechaEntregaEstimada,
            ClienteId = dto.ClienteId,
            VehiculoId = dto.VehiculoId,
            KilometrajeIngreso = dto.KilometrajeIngreso,
            FallasReportadasCliente = dto.FallasReportadasCliente,
            SintomasDiagnosticados = dto.SintomasDiagnosticados,
            ObservacionesIngreso = dto.ObservacionesIngreso,
            Prioridad = dto.Prioridad,
            TecnicoPrincipalId = dto.TecnicoPrincipalId,
            Estado = EstadoOT.Recibido,
            CreadoPor = usuario,
            Activo = true,
            CodigoQR = $"OT|{numero}|{DateTime.UtcNow.Ticks}"
        };

        _ctx.OrdenesTrabajo.Add(ot);
        await _ctx.SaveChangesAsync(ct);

        // Agregar servicios seleccionados
        decimal totalManoObra = 0;
        if (dto.ServiciosIds.Any())
        {
            var servicios = await _ctx.Servicios.AsNoTracking()
                .Where(s => dto.ServiciosIds.Contains(s.Id))
                .ToListAsync(ct);
            foreach (var s in servicios)
            {
                _ctx.DetalleOTServicios.Add(new DetalleOTServicio
                {
                    OrdenTrabajoId = ot.Id,
                    ServicioId = s.Id,
                    Cantidad = 1,
                    PrecioUnitario = s.PrecioEstandar,
                    Subtotal = s.PrecioEstandar
                });
                totalManoObra += s.PrecioEstandar;
            }
        }

        ot.SubtotalManoObra = totalManoObra;
        ot.Total = totalManoObra; // Se recalculará al añadir repuestos

        // Historial inicial
        _ctx.HistorialEstadosOT.Add(new HistorialEstadoOT
        {
            OrdenTrabajoId = ot.Id,
            EstadoAnterior = EstadoOT.Recibido,
            EstadoNuevo = EstadoOT.Recibido,
            Fecha = DateTime.UtcNow,
            Usuario = usuario,
            Comentario = "OT creada"
        });

        // Actualizar kilometraje del vehículo si es mayor
        var vehiculo = await _ctx.Vehiculos.FirstOrDefaultAsync(v => v.Id == dto.VehiculoId, ct);
        if (vehiculo != null && dto.KilometrajeIngreso > vehiculo.KilometrajeActual)
        {
            vehiculo.KilometrajeActual = dto.KilometrajeIngreso;
        }

        await _ctx.SaveChangesAsync(ct);
        return ot.Id;
    }

    public async Task CambiarEstadoAsync(CambioEstadoDto dto, string? usuario, CancellationToken ct = default)
    {
        var ot = await _ctx.OrdenesTrabajo.FirstOrDefaultAsync(o => o.Id == dto.OrdenTrabajoId, ct)
            ?? throw new InvalidOperationException("OT no encontrada");

        var anterior = ot.Estado;
        ot.Estado = dto.NuevoEstado;
        ot.ModificadoPor = usuario;

        if (dto.NuevoEstado == EstadoOT.Entregado)
            ot.FechaEntregaReal = DateTime.UtcNow;

        _ctx.HistorialEstadosOT.Add(new HistorialEstadoOT
        {
            OrdenTrabajoId = ot.Id,
            EstadoAnterior = anterior,
            EstadoNuevo = dto.NuevoEstado,
            Fecha = DateTime.UtcNow,
            Usuario = usuario,
            Comentario = dto.Comentario
        });

        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarOrdenAsync(int id, CancellationToken ct = default)
    {
        var ot = await _ctx.OrdenesTrabajo.FirstOrDefaultAsync(o => o.Id == id, ct)
            ?? throw new InvalidOperationException("OT no encontrada");
        ot.Activo = false;
        ot.Estado = EstadoOT.Cancelado;
        await _ctx.SaveChangesAsync(ct);
    }

    private async Task<string> GenerarNumeroOTAsync(CancellationToken ct)
    {
        var year = DateTime.UtcNow.Year;
        var prefijo = $"OT-{year}-";
        var ult = await _ctx.OrdenesTrabajo
            .Where(o => o.Numero.StartsWith(prefijo))
            .OrderByDescending(o => o.Numero)
            .Select(o => o.Numero)
            .FirstOrDefaultAsync(ct);
        if (ult == null) return $"{prefijo}0001";
        var sufijo = ult[prefijo.Length..];
        if (int.TryParse(sufijo, out var n)) return $"{prefijo}{(n + 1):D4}";
        return $"{prefijo}{Random.Shared.Next(1000, 9999)}";
    }

    // ============ TÉCNICOS ============

    public async Task<List<TecnicoListDto>> ListarTecnicosAsync(CancellationToken ct = default)
    {
        return await _ctx.Tecnicos.AsNoTracking().Where(t => t.Activo)
            .OrderBy(t => t.Nombres)
            .Select(t => new TecnicoListDto
            {
                Id = t.Id,
                Codigo = t.Codigo,
                NombreCompleto = t.Nombres + " " + t.Apellidos,
                Nivel = t.Nivel,
                Especialidades = t.Especialidades,
                Activo = t.Activo
            })
            .ToListAsync(ct);
    }

    // ============ CITAS ============

    public async Task<List<CitaListDto>> ListarCitasAsync(DateTime desde, DateTime hasta, CancellationToken ct = default)
    {
        return await _ctx.Citas.AsNoTracking()
            .Include(c => c.Cliente).Include(c => c.Vehiculo).Include(c => c.Servicio).Include(c => c.TecnicoPreferido)
            .Where(c => c.FechaHora >= desde && c.FechaHora < hasta)
            .OrderBy(c => c.FechaHora)
            .Select(c => new CitaListDto
            {
                Id = c.Id,
                FechaHora = c.FechaHora,
                DuracionMinutos = c.DuracionMinutos,
                ClienteId = c.ClienteId,
                Cliente = c.Cliente!.NombreRazonSocial,
                VehiculoId = c.VehiculoId,
                Vehiculo = c.Vehiculo != null ? c.Vehiculo.Marca + " " + c.Vehiculo.Modelo : null,
                Placa = c.Vehiculo != null ? c.Vehiculo.Placa : null,
                Servicio = c.Servicio != null ? c.Servicio.Nombre : null,
                Tecnico = c.TecnicoPreferido != null ? c.TecnicoPreferido.Nombres + " " + c.TecnicoPreferido.Apellidos : null,
                Estado = c.Estado,
                Comentarios = c.Comentarios,
                OrdenTrabajoId = c.OrdenTrabajoId
            })
            .ToListAsync(ct);
    }

    public async Task<CitaFormDto?> ObtenerCitaAsync(int id, CancellationToken ct = default)
    {
        return await _ctx.Citas.AsNoTracking()
            .Where(c => c.Id == id)
            .Select(c => new CitaFormDto
            {
                Id = c.Id,
                ClienteId = c.ClienteId,
                VehiculoId = c.VehiculoId,
                FechaHora = c.FechaHora,
                DuracionMinutos = c.DuracionMinutos,
                ServicioId = c.ServicioId,
                TecnicoPreferidoId = c.TecnicoPreferidoId,
                Estado = c.Estado,
                Comentarios = c.Comentarios
            })
            .FirstOrDefaultAsync(ct);
    }

    public async Task<int> CrearCitaAsync(CitaFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var c = new Cita
        {
            ClienteId = dto.ClienteId,
            VehiculoId = dto.VehiculoId,
            FechaHora = dto.FechaHora,
            DuracionMinutos = dto.DuracionMinutos,
            ServicioId = dto.ServicioId,
            TecnicoPreferidoId = dto.TecnicoPreferidoId,
            Estado = dto.Estado,
            Comentarios = dto.Comentarios,
            CreadoPor = usuario,
            Activo = true
        };
        _ctx.Citas.Add(c);
        await _ctx.SaveChangesAsync(ct);
        return c.Id;
    }

    public async Task ActualizarCitaAsync(CitaFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var c = await _ctx.Citas.FirstOrDefaultAsync(x => x.Id == dto.Id, ct)
            ?? throw new InvalidOperationException("Cita no encontrada");
        c.ClienteId = dto.ClienteId;
        c.VehiculoId = dto.VehiculoId;
        c.FechaHora = dto.FechaHora;
        c.DuracionMinutos = dto.DuracionMinutos;
        c.ServicioId = dto.ServicioId;
        c.TecnicoPreferidoId = dto.TecnicoPreferidoId;
        c.Estado = dto.Estado;
        c.Comentarios = dto.Comentarios;
        c.ModificadoPor = usuario;
        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarCitaAsync(int id, CancellationToken ct = default)
    {
        var c = await _ctx.Citas.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("Cita no encontrada");
        c.Activo = false;
        c.Estado = EstadoCita.Cancelada;
        await _ctx.SaveChangesAsync(ct);
    }

    public async Task<int> ConvertirCitaEnOTAsync(int citaId, string? usuario, CancellationToken ct = default)
    {
        var cita = await _ctx.Citas.FirstOrDefaultAsync(c => c.Id == citaId, ct)
            ?? throw new InvalidOperationException("Cita no encontrada");
        if (cita.OrdenTrabajoId.HasValue)
            throw new InvalidOperationException("La cita ya fue convertida a OT");
        if (cita.VehiculoId == null)
            throw new InvalidOperationException("La cita no tiene vehículo asociado");

        var vehiculo = await _ctx.Vehiculos.AsNoTracking()
            .FirstOrDefaultAsync(v => v.Id == cita.VehiculoId.Value, ct)
            ?? throw new InvalidOperationException("Vehículo no encontrado");

        var formDto = new OrdenTrabajoFormDto
        {
            ClienteId = cita.ClienteId,
            VehiculoId = cita.VehiculoId.Value,
            FechaEntregaEstimada = cita.FechaHora.AddHours(2),
            KilometrajeIngreso = vehiculo.KilometrajeActual,
            ObservacionesIngreso = cita.Comentarios,
            TecnicoPrincipalId = cita.TecnicoPreferidoId,
            ServiciosIds = cita.ServicioId.HasValue ? new List<int> { cita.ServicioId.Value } : new()
        };

        var otId = await CrearOrdenAsync(formDto, usuario, ct);

        cita.OrdenTrabajoId = otId;
        cita.Estado = EstadoCita.Atendida;
        await _ctx.SaveChangesAsync(ct);
        return otId;
    }
}
