using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Application.Interfaces;
using ERP.TallerAutomotriz.Domain.Entities.Customers;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace ERP.TallerAutomotriz.Infrastructure.Services;

public class CustomerService : ICustomerService
{
    private readonly ApplicationDbContext _ctx;
    public CustomerService(ApplicationDbContext ctx) => _ctx = ctx;

    // ============ CLIENTES ============

    public async Task<PagedResult<ClienteListDto>> ListarClientesAsync(string? buscar, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _ctx.Clientes.AsNoTracking().AsQueryable();

        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(c =>
                c.NombreRazonSocial.ToLower().Contains(b) ||
                c.Codigo.ToLower().Contains(b) ||
                c.DocumentoIdentidad.Contains(b) ||
                (c.Email != null && c.Email.ToLower().Contains(b)) ||
                (c.TelefonoPrincipal != null && c.TelefonoPrincipal.Contains(b)));
        }

        var total = await query.CountAsync(ct);

        var items = await query
            .OrderBy(c => c.NombreRazonSocial)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .Select(c => new ClienteListDto
            {
                Id = c.Id,
                Codigo = c.Codigo,
                Tipo = c.Tipo,
                NombreRazonSocial = c.NombreRazonSocial,
                DocumentoIdentidad = c.DocumentoIdentidad,
                Email = c.Email,
                TelefonoPrincipal = c.TelefonoPrincipal,
                CantidadVehiculos = c.Vehiculos.Count,
                SaldoPendiente = c.SaldoPendiente,
                Activo = c.Activo
            })
            .ToListAsync(ct);

        return new PagedResult<ClienteListDto>
        {
            Items = items,
            Total = total,
            Page = page,
            PageSize = pageSize
        };
    }

    public async Task<List<ClienteListDto>> ListarTodosClientesAsync(CancellationToken ct = default)
    {
        return await _ctx.Clientes.AsNoTracking()
            .Where(c => c.Activo)
            .OrderBy(c => c.NombreRazonSocial)
            .Select(c => new ClienteListDto
            {
                Id = c.Id,
                Codigo = c.Codigo,
                Tipo = c.Tipo,
                NombreRazonSocial = c.NombreRazonSocial,
                DocumentoIdentidad = c.DocumentoIdentidad,
                Email = c.Email,
                TelefonoPrincipal = c.TelefonoPrincipal,
                Activo = c.Activo
            })
            .ToListAsync(ct);
    }

    public async Task<ClienteFormDto?> ObtenerClienteAsync(int id, CancellationToken ct = default)
    {
        return await _ctx.Clientes.AsNoTracking()
            .Where(c => c.Id == id)
            .Select(c => new ClienteFormDto
            {
                Id = c.Id,
                Codigo = c.Codigo,
                Tipo = c.Tipo,
                NombreRazonSocial = c.NombreRazonSocial,
                NombreComercial = c.NombreComercial,
                DocumentoIdentidad = c.DocumentoIdentidad,
                Direccion = c.Direccion,
                Ciudad = c.Ciudad,
                TelefonoPrincipal = c.TelefonoPrincipal,
                TelefonoSecundario = c.TelefonoSecundario,
                Email = c.Email,
                ContactoPrincipal = c.ContactoPrincipal,
                CargoContacto = c.CargoContacto,
                Notas = c.Notas,
                RecibeNotificaciones = c.RecibeNotificaciones,
                LimiteCredito = c.LimiteCredito
            })
            .FirstOrDefaultAsync(ct);
    }

    public async Task<int> CrearClienteAsync(ClienteFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var cliente = new Cliente
        {
            Codigo = string.IsNullOrWhiteSpace(dto.Codigo) ? await GenerarCodigoClienteAsync(ct) : dto.Codigo!,
            Tipo = dto.Tipo,
            NombreRazonSocial = dto.NombreRazonSocial,
            NombreComercial = dto.NombreComercial,
            DocumentoIdentidad = dto.DocumentoIdentidad,
            Direccion = dto.Direccion,
            Ciudad = dto.Ciudad,
            TelefonoPrincipal = dto.TelefonoPrincipal,
            TelefonoSecundario = dto.TelefonoSecundario,
            Email = dto.Email,
            ContactoPrincipal = dto.ContactoPrincipal,
            CargoContacto = dto.CargoContacto,
            Notas = dto.Notas,
            RecibeNotificaciones = dto.RecibeNotificaciones,
            LimiteCredito = dto.LimiteCredito,
            CreadoPor = usuario,
            Activo = true
        };

        _ctx.Clientes.Add(cliente);
        await _ctx.SaveChangesAsync(ct);
        return cliente.Id;
    }

    public async Task ActualizarClienteAsync(ClienteFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var cliente = await _ctx.Clientes.FirstOrDefaultAsync(c => c.Id == dto.Id, ct)
            ?? throw new InvalidOperationException("Cliente no encontrado");

        cliente.Tipo = dto.Tipo;
        cliente.NombreRazonSocial = dto.NombreRazonSocial;
        cliente.NombreComercial = dto.NombreComercial;
        cliente.DocumentoIdentidad = dto.DocumentoIdentidad;
        cliente.Direccion = dto.Direccion;
        cliente.Ciudad = dto.Ciudad;
        cliente.TelefonoPrincipal = dto.TelefonoPrincipal;
        cliente.TelefonoSecundario = dto.TelefonoSecundario;
        cliente.Email = dto.Email;
        cliente.ContactoPrincipal = dto.ContactoPrincipal;
        cliente.CargoContacto = dto.CargoContacto;
        cliente.Notas = dto.Notas;
        cliente.RecibeNotificaciones = dto.RecibeNotificaciones;
        cliente.LimiteCredito = dto.LimiteCredito;
        cliente.ModificadoPor = usuario;

        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarClienteAsync(int id, CancellationToken ct = default)
    {
        var cliente = await _ctx.Clientes.FirstOrDefaultAsync(c => c.Id == id, ct)
            ?? throw new InvalidOperationException("Cliente no encontrado");

        // Soft delete (desactivar). Si quieres hard delete, usa _ctx.Clientes.Remove(cliente).
        cliente.Activo = false;
        await _ctx.SaveChangesAsync(ct);
    }

    private async Task<string> GenerarCodigoClienteAsync(CancellationToken ct)
    {
        var ultimo = await _ctx.Clientes
            .Where(c => c.Codigo.StartsWith("CLI-"))
            .OrderByDescending(c => c.Codigo)
            .Select(c => c.Codigo)
            .FirstOrDefaultAsync(ct);

        if (ultimo == null) return "CLI-001";
        if (int.TryParse(ultimo[4..], out var n)) return $"CLI-{(n + 1):D3}";
        return $"CLI-{Random.Shared.Next(100, 999)}";
    }

    // ============ VEHÍCULOS ============

    public async Task<PagedResult<VehiculoListDto>> ListarVehiculosAsync(string? buscar, int? clienteId, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _ctx.Vehiculos.AsNoTracking().Include(v => v.Cliente).AsQueryable();

        if (clienteId.HasValue)
            query = query.Where(v => v.ClienteId == clienteId.Value);

        if (!string.IsNullOrWhiteSpace(buscar))
        {
            var b = buscar.Trim().ToLower();
            query = query.Where(v =>
                v.Placa.ToLower().Contains(b) ||
                v.Marca.ToLower().Contains(b) ||
                v.Modelo.ToLower().Contains(b) ||
                (v.VIN != null && v.VIN.ToLower().Contains(b)) ||
                v.Cliente!.NombreRazonSocial.ToLower().Contains(b));
        }

        var total = await query.CountAsync(ct);

        var items = await query
            .OrderBy(v => v.Placa)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .Select(v => new VehiculoListDto
            {
                Id = v.Id,
                ClienteId = v.ClienteId,
                ClienteNombre = v.Cliente!.NombreRazonSocial,
                Placa = v.Placa,
                Marca = v.Marca,
                Modelo = v.Modelo,
                Anio = v.Anio,
                Color = v.Color,
                KilometrajeActual = v.KilometrajeActual,
                Combustible = v.Combustible,
                Transmision = v.Transmision,
                Activo = v.Activo
            })
            .ToListAsync(ct);

        return new PagedResult<VehiculoListDto>
        {
            Items = items,
            Total = total,
            Page = page,
            PageSize = pageSize
        };
    }

    public async Task<VehiculoFormDto?> ObtenerVehiculoAsync(int id, CancellationToken ct = default)
    {
        return await _ctx.Vehiculos.AsNoTracking()
            .Where(v => v.Id == id)
            .Select(v => new VehiculoFormDto
            {
                Id = v.Id,
                ClienteId = v.ClienteId,
                Placa = v.Placa,
                VIN = v.VIN,
                Marca = v.Marca,
                Modelo = v.Modelo,
                Anio = v.Anio,
                Color = v.Color,
                KilometrajeActual = v.KilometrajeActual,
                Combustible = v.Combustible,
                Transmision = v.Transmision,
                Motor = v.Motor,
                NumeroChasis = v.NumeroChasis,
                Notas = v.Notas
            })
            .FirstOrDefaultAsync(ct);
    }

    public async Task<int> CrearVehiculoAsync(VehiculoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var vehiculo = new Vehiculo
        {
            ClienteId = dto.ClienteId,
            Placa = dto.Placa.ToUpper(),
            VIN = dto.VIN,
            Marca = dto.Marca,
            Modelo = dto.Modelo,
            Anio = dto.Anio,
            Color = dto.Color,
            KilometrajeActual = dto.KilometrajeActual,
            Combustible = dto.Combustible,
            Transmision = dto.Transmision,
            Motor = dto.Motor,
            NumeroChasis = dto.NumeroChasis,
            Notas = dto.Notas,
            CreadoPor = usuario,
            Activo = true
        };

        _ctx.Vehiculos.Add(vehiculo);
        await _ctx.SaveChangesAsync(ct);
        return vehiculo.Id;
    }

    public async Task ActualizarVehiculoAsync(VehiculoFormDto dto, string? usuario, CancellationToken ct = default)
    {
        var vehiculo = await _ctx.Vehiculos.FirstOrDefaultAsync(v => v.Id == dto.Id, ct)
            ?? throw new InvalidOperationException("Vehículo no encontrado");

        vehiculo.ClienteId = dto.ClienteId;
        vehiculo.Placa = dto.Placa.ToUpper();
        vehiculo.VIN = dto.VIN;
        vehiculo.Marca = dto.Marca;
        vehiculo.Modelo = dto.Modelo;
        vehiculo.Anio = dto.Anio;
        vehiculo.Color = dto.Color;
        vehiculo.KilometrajeActual = dto.KilometrajeActual;
        vehiculo.Combustible = dto.Combustible;
        vehiculo.Transmision = dto.Transmision;
        vehiculo.Motor = dto.Motor;
        vehiculo.NumeroChasis = dto.NumeroChasis;
        vehiculo.Notas = dto.Notas;
        vehiculo.ModificadoPor = usuario;

        await _ctx.SaveChangesAsync(ct);
    }

    public async Task EliminarVehiculoAsync(int id, CancellationToken ct = default)
    {
        var vehiculo = await _ctx.Vehiculos.FirstOrDefaultAsync(v => v.Id == id, ct)
            ?? throw new InvalidOperationException("Vehículo no encontrado");

        vehiculo.Activo = false;
        await _ctx.SaveChangesAsync(ct);
    }
}
