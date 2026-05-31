using ERP.Application.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Evaluacion;

public class EncuestaDto
{
    public int Id { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string Descripcion { get; set; } = string.Empty;
    public DateTime FechaFin { get; set; }
    public int TotalRespondidos { get; set; }
    public bool Activa => DateTime.Now <= FechaFin;
}

public record GetEncuestasQuery : IRequest<List<EncuestaDto>>;

public class GetEncuestasQueryHandler : IRequestHandler<GetEncuestasQuery, List<EncuestaDto>>
{
    public async Task<List<EncuestaDto>> Handle(GetEncuestasQuery request, CancellationToken cancellationToken)
    {
        // Simulación de encuestas
        return new List<EncuestaDto>
        {
            new() { Id = 1, Titulo = "Evaluación de Desempeño Docente 2026-I", Descripcion = "Califica el desempeño de tus profesores en el presente semestre.", FechaFin = DateTime.Now.AddDays(15), TotalRespondidos = 124 },
            new() { Id = 2, Titulo = "Encuesta de Satisfacción de Servicios", Descripcion = "Tu opinión sobre biblioteca, laboratorios y cafetería.", FechaFin = DateTime.Now.AddDays(7), TotalRespondidos = 85 },
            new() { Id = 3, Titulo = "Clima Institucional (Docentes)", Descripcion = "Solo para personal docente y administrativo.", FechaFin = DateTime.Now.AddDays(-2), TotalRespondidos = 45 }
        };
    }
}
