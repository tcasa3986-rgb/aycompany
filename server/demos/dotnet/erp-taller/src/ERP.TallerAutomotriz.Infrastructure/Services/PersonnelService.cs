using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Application.Interfaces;
using ERP.TallerAutomotriz.Domain.Entities.Personnel;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace ERP.TallerAutomotriz.Infrastructure.Services;

public class PersonnelService : IPersonnelService
{
    private readonly ApplicationDbContext _ctx;
    public PersonnelService(ApplicationDbContext ctx) => _ctx = ctx;

    // ============ TÉCNICOS ============

    public async Task<List<TecnicoFullDto>> ListarTecnicosAsync(string? buscar = null, CancellationToken ct = default)
    {
        var query = _ctx.Tecnicos.AsNoTracking().AsQueryable();
        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(t =>
                t.Codigo.ToLower().Contains(b) ||
                t.Nombres.ToLower().Contains(b) ||
                t.Apellidos.ToLower().Contains(b) ||
                t.DocumentoIdentidad.Contains(b));
        }

        return await query
            .OrderBy(t => t.Nombres)
            .Select(t => new TecnicoFullDto
            {
                Id = t.Id,
                Codigo = t.Codigo,
                Nombres = t.Nombres,
                Apellidos = t.Apellidos,
                DocumentoIdentidad = t.DocumentoIdentidad,
                Telefono = t.Telefono,
                Email = t.Email,
                FechaIngreso = t.FechaIngreso,
                Nivel = t.Nivel,
                TarifaHora = t.TarifaHora,
                PorcentajeComision = t.PorcentajeComision,
                Especialidades = t.Especialidades,
                Activo = t.Activo,
                OTsAsignadas = _ctx.OrdenesTrabajo.Count(o => o.TecnicoPrincipalId == t.Id && o.Estado != Domain.Enums.EstadoOT.Entregado && o.Estado != Domain.Enums.EstadoOT.Cancelado)
            })
            .ToListAsync(ct);
    }

    public async Task<TecnicoFormDto?> ObtenerTecnicoAsync(int id, CancellationToken ct = default)
    {
        return await _ctx.Tecnicos.AsNoTracking()
            .Where(t => t.Id == id)
            .Select(t => new TecnicoFormDto
            {
                Id = t.Id,
                Codigo = t.Codigo,
                Nombres = t.Nombres,
                Apellidos = t.Apellidos,
                DocumentoIdentidad = t.DocumentoIdentidad,
                Telefono = t.Telefono,
                Email = t.Email,
                Direccion = t.Direccion,
                FechaIngreso = t.FechaIngreso,
                Nivel = t.Nivel,
                TarifaHora = t.TarifaHora,
                PorcentajeComision = t.PorcentajeComision,
                Especialidades = t.Especialidades
            })
            .FirstOrDefaultAsync(ct);
    }

    public async Task<int> CrearTecnicoAsync(TecnicoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var t = new Tecnico
        {
            Codigo = string.IsNullOrWhiteSpace(dto.Codigo) ? await GenerarCodigoAsync(ct) : dto.Codigo!,
            Nombres = dto.Nombres,
            Apellidos = dto.Apellidos,
            DocumentoIdentidad = dto.DocumentoIdentidad,
            Telefono = dto.Telefono,
            Email = dto.Email,
            Direccion = dto.Direccion,
            FechaIngreso = dto.FechaIngreso,
            Nivel = dto.Nivel,
            TarifaHora = dto.TarifaHora,
            PorcentajeComision = dto.PorcentajeComision,
            Especialidades = dto.Especialidades,
            CreadoPor = usuario,
            Activo = true
        };
        _ctx.Tecnicos.Add(t);
        await _ctx.SaveChangesAsync(ct);
        return t.Id;
    }

    public async Task ActualizarTecnicoAsync(TecnicoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var t = await _ctx.Tecnicos.FirstOrDefaultAsync(x => x.Id == dto.Id, ct)
            ?? throw new InvalidOperationException("Técnico no encontrado");
        t.Nombres = dto.Nombres;
        t.Apellidos = dto.Apellidos;
        t.DocumentoIdentidad = dto.DocumentoIdentidad;
        t.Telefono = dto.Telefono;
        t.Email = dto.Email;
        t.Direccion = dto.Direccion;
        t.FechaIngreso = dto.FechaIngreso;
        t.Nivel = dto.Nivel;
        t.TarifaHora = dto.TarifaHora;
        t.PorcentajeComision = dto.PorcentajeComision;
        t.Especialidades = dto.Especialidades;
        t.ModificadoPor = usuario;
        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarTecnicoAsync(int id, CancellationToken ct = default)
    {
        var t = await _ctx.Tecnicos.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("Técnico no encontrado");
        t.Activo = false;
        await _ctx.SaveChangesAsync(ct);
    }

    private async Task<string> GenerarCodigoAsync(CancellationToken ct)
    {
        var ult = await _ctx.Tecnicos
            .Where(t => t.Codigo.StartsWith("TEC-"))
            .OrderByDescending(t => t.Codigo)
            .Select(t => t.Codigo)
            .FirstOrDefaultAsync(ct);
        if (ult == null) return "TEC-001";
        if (int.TryParse(ult[4..], out var n)) return $"TEC-{(n + 1):D3}";
        return $"TEC-{Random.Shared.Next(100, 999)}";
    }

    // ============ ASISTENCIA ============

    public async Task<List<AsistenciaListDto>> ListarAsistenciasAsync(int? tecnicoId, DateTime desde, DateTime hasta, CancellationToken ct = default)
    {
        var query = _ctx.RegistrosAsistencia.AsNoTracking().Include(a => a.Tecnico).AsQueryable();
        if (tecnicoId.HasValue) query = query.Where(a => a.TecnicoId == tecnicoId.Value);
        query = query.Where(a => a.Fecha >= desde && a.Fecha < hasta.AddDays(1));

        return await query
            .OrderByDescending(a => a.Fecha)
            .Select(a => new AsistenciaListDto
            {
                Id = a.Id,
                TecnicoId = a.TecnicoId,
                Tecnico = a.Tecnico!.Nombres + " " + a.Tecnico.Apellidos,
                Fecha = a.Fecha,
                HoraEntrada = a.HoraEntrada,
                HoraSalida = a.HoraSalida,
                HorasTrabajadas = a.HorasTrabajadas,
                HorasExtras = a.HorasExtras,
                Observaciones = a.Observaciones
            })
            .ToListAsync(ct);
    }

    public async Task MarcarEntradaSalidaAsync(MarcarEntradaSalidaDto dto, CancellationToken ct = default)
    {
        var hoy = DateTime.Today;
        var registro = await _ctx.RegistrosAsistencia
            .FirstOrDefaultAsync(a => a.TecnicoId == dto.TecnicoId && a.Fecha == hoy, ct);

        if (registro == null)
        {
            // Primer marcado del día -> entrada
            registro = new RegistroAsistencia
            {
                TecnicoId = dto.TecnicoId,
                Fecha = hoy,
                HoraEntrada = DateTime.UtcNow,
                Observaciones = dto.Observaciones
            };
            _ctx.RegistrosAsistencia.Add(registro);
        }
        else if (registro.HoraSalida == null)
        {
            // Marcar salida y calcular horas
            registro.HoraSalida = DateTime.UtcNow;
            if (registro.HoraEntrada.HasValue)
            {
                var duracion = registro.HoraSalida.Value - registro.HoraEntrada.Value;
                var horas = (decimal)duracion.TotalHours;
                if (horas > 8)
                {
                    registro.HorasTrabajadas = 8;
                    registro.HorasExtras = horas - 8;
                }
                else
                {
                    registro.HorasTrabajadas = horas;
                    registro.HorasExtras = 0;
                }
            }
            if (!string.IsNullOrEmpty(dto.Observaciones)) registro.Observaciones = dto.Observaciones;
        }
        else
        {
            throw new InvalidOperationException("Ya hay entrada y salida registradas para hoy.");
        }

        await _ctx.SaveChangesAsync(ct);
    }

    // ============ COMISIONES ============

    public async Task<List<ComisionListDto>> ListarComisionesAsync(int? tecnicoId, bool? pagadas, DateTime? desde, DateTime? hasta, CancellationToken ct = default)
    {
        var query = _ctx.Comisiones.AsNoTracking().Include(c => c.Tecnico).AsQueryable();
        if (tecnicoId.HasValue) query = query.Where(c => c.TecnicoId == tecnicoId.Value);
        if (pagadas.HasValue) query = query.Where(c => c.Pagada == pagadas.Value);
        if (desde.HasValue) query = query.Where(c => c.Fecha >= desde.Value);
        if (hasta.HasValue) query = query.Where(c => c.Fecha < hasta.Value.AddDays(1));

        var items = await query.OrderByDescending(c => c.Fecha).ToListAsync(ct);
        var otIds = items.Where(c => c.OrdenTrabajoId.HasValue).Select(c => c.OrdenTrabajoId!.Value).Distinct().ToList();
        var otsDict = await _ctx.OrdenesTrabajo.AsNoTracking()
            .Where(o => otIds.Contains(o.Id))
            .Select(o => new { o.Id, o.Numero })
            .ToDictionaryAsync(o => o.Id, o => o.Numero, ct);

        return items.Select(c => new ComisionListDto
        {
            Id = c.Id,
            TecnicoId = c.TecnicoId,
            Tecnico = $"{c.Tecnico!.Nombres} {c.Tecnico.Apellidos}",
            Fecha = c.Fecha,
            OrdenTrabajoId = c.OrdenTrabajoId,
            NumeroOT = c.OrdenTrabajoId.HasValue && otsDict.TryGetValue(c.OrdenTrabajoId.Value, out var nro) ? nro : null,
            MontoBase = c.MontoBase,
            Porcentaje = c.Porcentaje,
            MontoComision = c.MontoComision,
            Pagada = c.Pagada,
            FechaPago = c.FechaPago
        }).ToList();
    }

    public async Task<int> RegistrarComisionAsync(ComisionFormDto dto, CancellationToken ct = default)
    {
        var c = new Comision
        {
            TecnicoId = dto.TecnicoId,
            OrdenTrabajoId = dto.OrdenTrabajoId,
            Fecha = dto.Fecha,
            MontoBase = dto.MontoBase,
            Porcentaje = dto.Porcentaje,
            MontoComision = dto.MontoBase * dto.Porcentaje / 100,
            Pagada = false
        };
        _ctx.Comisiones.Add(c);
        await _ctx.SaveChangesAsync(ct);
        return c.Id;
    }

    public async Task MarcarComisionPagadaAsync(int id, CancellationToken ct = default)
    {
        var c = await _ctx.Comisiones.FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new InvalidOperationException("Comisión no encontrada");
        c.Pagada = true;
        c.FechaPago = DateTime.UtcNow;
        await _ctx.SaveChangesAsync(ct);
    }
}
