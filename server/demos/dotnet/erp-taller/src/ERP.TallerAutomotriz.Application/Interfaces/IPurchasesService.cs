using ERP.TallerAutomotriz.Application.DTOs;

namespace ERP.TallerAutomotriz.Application.Interfaces;

public interface IPurchasesService
{
    // Proveedores
    Task<List<ProveedorListDto>> ListarProveedoresAsync(string? buscar = null, CancellationToken ct = default);
    Task<ProveedorFormDto?> ObtenerProveedorAsync(int id, CancellationToken ct = default);
    Task<int> CrearProveedorAsync(ProveedorFormDto dto, string? usuario, CancellationToken ct = default);
    Task ActualizarProveedorAsync(ProveedorFormDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarProveedorAsync(int id, CancellationToken ct = default);

    // Órdenes de compra
    Task<List<OrdenCompraListDto>> ListarOrdenesCompraAsync(string? buscar = null, CancellationToken ct = default);
    Task<OrdenCompraDetalleDto?> ObtenerOrdenCompraAsync(int id, CancellationToken ct = default);
    Task<int> CrearOrdenCompraAsync(OrdenCompraFormDto dto, string? usuario, CancellationToken ct = default);
    Task RecibirMercaderiaAsync(RecepcionMercaderiaDto dto, string? usuario, CancellationToken ct = default);
    Task CancelarOrdenCompraAsync(int id, string? usuario, CancellationToken ct = default);

    // Cuentas por pagar
    Task<List<CuentaPagarDto>> ListarCuentasPagarAsync(bool soloVencidas = false, CancellationToken ct = default);
    Task<int> RegistrarCuentaPagarAsync(CuentaPagarFormDto dto, CancellationToken ct = default);
    Task RegistrarPagoCuentaAsync(int cuentaId, decimal monto, CancellationToken ct = default);
}
