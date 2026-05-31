using ERP.TallerAutomotriz.Application.DTOs;

namespace ERP.TallerAutomotriz.Application.Interfaces;

public interface ICustomerService
{
    // Clientes
    Task<PagedResult<ClienteListDto>> ListarClientesAsync(string? buscar, int page, int pageSize, CancellationToken ct = default);
    Task<List<ClienteListDto>> ListarTodosClientesAsync(CancellationToken ct = default);
    Task<ClienteFormDto?> ObtenerClienteAsync(int id, CancellationToken ct = default);
    Task<int> CrearClienteAsync(ClienteFormDto dto, string? usuario, CancellationToken ct = default);
    Task ActualizarClienteAsync(ClienteFormDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarClienteAsync(int id, CancellationToken ct = default);

    // Vehículos
    Task<PagedResult<VehiculoListDto>> ListarVehiculosAsync(string? buscar, int? clienteId, int page, int pageSize, CancellationToken ct = default);
    Task<VehiculoFormDto?> ObtenerVehiculoAsync(int id, CancellationToken ct = default);
    Task<int> CrearVehiculoAsync(VehiculoFormDto dto, string? usuario, CancellationToken ct = default);
    Task ActualizarVehiculoAsync(VehiculoFormDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarVehiculoAsync(int id, CancellationToken ct = default);
}
