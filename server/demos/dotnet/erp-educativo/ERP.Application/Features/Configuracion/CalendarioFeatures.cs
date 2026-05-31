using ERP.Application.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Configuracion;

public class EventoCalendarioDto
{
    public int Id { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Descripcion { get; set; }
    public DateTime FechaInicio { get; set; }
    public DateTime FechaFin { get; set; }
    public string Color { get; set; } = "#1A4B8C";
    public string Categoria { get; set; } = "General";
}

public record GetEventosCalendarioQuery : IRequest<List<EventoCalendarioDto>>;

public class GetEventosCalendarioQueryHandler : IRequestHandler<GetEventosCalendarioQuery, List<EventoCalendarioDto>>
{
    public async Task<List<EventoCalendarioDto>> Handle(GetEventosCalendarioQuery request, CancellationToken cancellationToken)
    {
        // Simulación de eventos académicos
        return new List<EventoCalendarioDto>
        {
            new() { Id = 1, Titulo = "Inicio de Clases 2026-I", FechaInicio = new DateTime(2026, 3, 15), FechaFin = new DateTime(2026, 3, 15), Color = "#10B981", Categoria = "Hito Académico" },
            new() { Id = 2, Titulo = "Semana de Exámenes Parciales", FechaInicio = new DateTime(2026, 5, 10), FechaFin = new DateTime(2026, 5, 15), Color = "#F59E0B", Categoria = "Evaluación" },
            new() { Id = 3, Titulo = "Aniversario Institucional", FechaInicio = new DateTime(2026, 6, 24), FechaFin = new DateTime(2026, 6, 24), Color = "#EF4444", Categoria = "Feriado/Evento" },
            new() { Id = 4, Titulo = "Cierre de Actas", FechaInicio = new DateTime(2026, 7, 15), FechaFin = new DateTime(2026, 7, 20), Color = "#6B21A8", Categoria = "Administrativo" }
        };
    }
}
