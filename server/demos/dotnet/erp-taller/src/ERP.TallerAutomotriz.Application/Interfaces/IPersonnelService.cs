using ERP.TallerAutomotriz.Application.DTOs;

namespace ERP.TallerAutomotriz.Application.Interfaces;

public interface IPersonnelService
{
    // Técnicos
    Task<List<TecnicoFullDto>> ListarTecnicosAsync(string? buscar = null, CancellationToken ct = default);
    Task<TecnicoFormDto?> ObtenerTecnicoAsync(int id, CancellationToken ct = default);
    Task<int> CrearTecnicoAsync(TecnicoFormDto dto, string? usuario, CancellationToken ct = default);
    Task ActualizarTecnicoAsync(TecnicoFormDto dto, string? usuario, CancellationToken ct = default);
    Task EliminarTecnicoAsync(int id, CancellationToken ct = default);

    // Asistencia
    Task<List<AsistenciaListDto>> ListarAsistenciasAsync(int? tecnicoId, DateTime desde, DateTime hasta, CancellationToken ct = default);
    Task MarcarEntradaSalidaAsync(MarcarEntradaSalidaDto dto, CancellationToken ct = default);

    // Comisiones
    Task<List<ComisionListDto>> ListarComisionesAsync(int? tecnicoId, bool? pagadas, DateTime? desde, DateTime? hasta, CancellationToken ct = default);
    Task<int> RegistrarComisionAsync(ComisionFormDto dto, CancellationToken ct = default);
    Task MarcarComisionPagadaAsync(int id, CancellationToken ct = default);
}
