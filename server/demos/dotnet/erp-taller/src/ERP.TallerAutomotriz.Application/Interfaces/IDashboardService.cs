using ERP.TallerAutomotriz.Application.DTOs;

namespace ERP.TallerAutomotriz.Application.Interfaces;

public interface IDashboardService
{
    Task<DashboardKpiDto> ObtenerKpisAsync(CancellationToken ct = default);
    Task<List<TendenciaItemDto>> ObtenerTendenciaSemanalAsync(CancellationToken ct = default);
    Task<List<TendenciaItemDto>> ObtenerTendenciaMensualAsync(CancellationToken ct = default);
    Task<List<AlertaDto>> ObtenerAlertasAsync(CancellationToken ct = default);
    Task<List<OrdenTrabajoResumenDto>> ObtenerOrdenesRecientesAsync(int top = 6, CancellationToken ct = default);
}
