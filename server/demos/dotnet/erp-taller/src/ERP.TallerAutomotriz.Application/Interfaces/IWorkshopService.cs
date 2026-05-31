using ERP.TallerAutomotriz.Application.DTOs;
using ERP.TallerAutomotriz.Domain.Enums;

namespace ERP.TallerAutomotriz.Application.Interfaces;

public interface IWorkshopService
{
    // Servicios
    Task<List<ServicioListDto>> ListarServiciosAsync(string? buscar = null, CancellationToken ct = default);
    Task<ServicioFormDto?> ObtenerServicioAsync(int id, CancellationToken ct = default);
    Task<int> CrearServicioAsync(ServicioFormDto dto, string? usuario, CancellationToken ct = default);
    Task ActualizarServicioAsync(ServicioFormDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarServicioAsync(int id, CancellationToken ct = default);
    Task<List<CategoriaServicioDto>> ListarCategoriasServicioAsync(CancellationToken ct = default);

    // Órdenes de trabajo
    Task<PagedResult<OrdenTrabajoListDto>> ListarOrdenesAsync(string? buscar, EstadoOT? estado, int page, int pageSize, CancellationToken ct = default);
    Task<List<OrdenTrabajoListDto>> ListarTodasOrdenesAsync(CancellationToken ct = default);
    Task<OrdenTrabajoDetalleDto?> ObtenerOrdenAsync(int id, CancellationToken ct = default);
    Task<int> CrearOrdenAsync(OrdenTrabajoFormDto dto, string? usuario, CancellationToken ct = default);
    Task CambiarEstadoAsync(CambioEstadoDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarOrdenAsync(int id, CancellationToken ct = default);

    // Técnicos (lectura ligera para asignación)
    Task<List<TecnicoListDto>> ListarTecnicosAsync(CancellationToken ct = default);

    // Citas
    Task<List<CitaListDto>> ListarCitasAsync(DateTime desde, DateTime hasta, CancellationToken ct = default);
    Task<CitaFormDto?> ObtenerCitaAsync(int id, CancellationToken ct = default);
    Task<int> CrearCitaAsync(CitaFormDto dto, string? usuario, CancellationToken ct = default);
    Task ActualizarCitaAsync(CitaFormDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarCitaAsync(int id, CancellationToken ct = default);
    Task<int> ConvertirCitaEnOTAsync(int citaId, string? usuario, CancellationToken ct = default);
}
