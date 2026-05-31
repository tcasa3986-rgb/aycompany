using ERP.Application.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Admision;

public class VacanteDetalleDto
{
    public string CarreraNombre { get; set; } = string.Empty;
    public int VacantesTotales { get; set; }
    public int Matriculados { get; set; }
    public int Disponibles => VacantesTotales - Matriculados;
    public decimal PorcentajeOcupacion => VacantesTotales > 0 ? (decimal)Matriculados / VacantesTotales * 100 : 0;
}

public record GetVacantesPorCarreraQuery : IRequest<List<VacanteDetalleDto>>;

public class GetVacantesPorCarreraQueryHandler(IApplicationDbContext context) : IRequestHandler<GetVacantesPorCarreraQuery, List<VacanteDetalleDto>>
{
    public async Task<List<VacanteDetalleDto>> Handle(GetVacantesPorCarreraQuery request, CancellationToken cancellationToken)
    {
        var carreras = await context.Carreras.ToListAsync(cancellationToken);
        var result = new List<VacanteDetalleDto>();

        foreach (var c in carreras)
        {
            var matriculados = await context.Matriculas.CountAsync(m => m.CarreraId == c.Id, cancellationToken);
            
            // Simulación de vacantes totales (pueden venir de una tabla de configuración)
            int vacantes = 40; // Default por carrera

            result.Add(new VacanteDetalleDto
            {
                CarreraNombre = c.Nombre,
                VacantesTotales = vacantes,
                Matriculados = matriculados
            });
        }

        return result;
    }
}
