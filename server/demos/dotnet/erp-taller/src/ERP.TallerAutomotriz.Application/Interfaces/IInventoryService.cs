using ERP.TallerAutomotriz.Application.DTOs;

namespace ERP.TallerAutomotriz.Application.Interfaces;

public interface IInventoryService
{
    // Repuestos
    Task<PagedResult<RepuestoListDto>> ListarRepuestosAsync(string? buscar, int? categoriaId, bool soloBajoStock, int page, int pageSize, CancellationToken ct = default);
    Task<List<RepuestoListDto>> ListarTodosRepuestosAsync(CancellationToken ct = default);
    Task<RepuestoFormDto?> ObtenerRepuestoAsync(int id, CancellationToken ct = default);
    Task<int> CrearRepuestoAsync(RepuestoFormDto dto, string? usuario, CancellationToken ct = default);
    Task ActualizarRepuestoAsync(RepuestoFormDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarRepuestoAsync(int id, CancellationToken ct = default);

    // Categorías
    Task<List<CategoriaRepuestoDto>> ListarCategoriasAsync(CancellationToken ct = default);

    // Almacenes
    Task<List<AlmacenDto>> ListarAlmacenesAsync(CancellationToken ct = default);
    Task<AlmacenFormDto?> ObtenerAlmacenAsync(int id, CancellationToken ct = default);
    Task<int> CrearAlmacenAsync(AlmacenFormDto dto, string? usuario, CancellationToken ct = default);
    Task ActualizarAlmacenAsync(AlmacenFormDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarAlmacenAsync(int id, CancellationToken ct = default);

    // Movimientos
    Task<PagedResult<MovimientoListDto>> ListarMovimientosAsync(int? repuestoId, int? almacenId, DateTime? desde, DateTime? hasta, int page, int pageSize, CancellationToken ct = default);
    Task<int> RegistrarMovimientoAsync(MovimientoFormDto dto, string? usuario, CancellationToken ct = default);
}
