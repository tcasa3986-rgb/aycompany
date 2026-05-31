using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Application.Interfaces;

public interface ISalesService
{
    // Facturas
    Task<PagedResult<FacturaListDto>> ListarFacturasAsync(string? buscar, EstadoFactura? estado, int page, int pageSize, CancellationToken ct = default);
    Task<FacturaDetalleDto?> ObtenerFacturaAsync(int id, CancellationToken ct = default);
    Task<int> CrearFacturaAsync(FacturaFormDto dto, string? usuario, CancellationToken ct = default);
    Task<int> CrearFacturaDesdeOTAsync(int ordenTrabajoId, string? usuario, CancellationToken ct = default);
    Task AnularFacturaAsync(int id, string? usuario, CancellationToken ct = default);

    // Pagos
    Task<int> RegistrarPagoAsync(PagoFormDto dto, string? usuario, CancellationToken ct = default);

    // Cotizaciones
    Task<List<CotizacionListDto>> ListarCotizacionesAsync(string? buscar = null, CancellationToken ct = default);
    Task<CotizacionFormDto?> ObtenerCotizacionAsync(int id, CancellationToken ct = default);
    Task<int> CrearCotizacionAsync(CotizacionFormDto dto, string? usuario, CancellationToken ct = default);
    Task<int> ConvertirCotizacionEnFacturaAsync(int cotizacionId, string? usuario, CancellationToken ct = default);

    // Caja
    Task<List<CajaEstadoDto>> ListarCajasAsync(CancellationToken ct = default);
    Task<CajaEstadoDto?> ObtenerEstadoCajaAsync(int cajaId, CancellationToken ct = default);
    Task AbrirCajaAsync(AperturaCajaDto dto, string? usuario, CancellationToken ct = default);
    Task CerrarCajaAsync(CierreCajaDto dto, string? usuario, CancellationToken ct = default);

    // Cuentas por cobrar
    Task<List<CuentaCobrarDto>> ListarCuentasCobrarAsync(bool soloVencidas = false, CancellationToken ct = default);
}
