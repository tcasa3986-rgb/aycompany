using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Academico.Promedios;

// ─── DTOs ──────────────────────────────────────────────────────────────────

public class PromedioDto
{
    public int EstudianteId { get; set; }
    public string EstudianteNombre { get; set; } = string.Empty;
    public string EstudianteCodigo { get; set; } = string.Empty;
    public decimal? PromedioCalculado { get; set; }
    public bool Aprobado { get; set; }
    public string Estado { get; set; } = string.Empty;
}

// ─── QUERIES ───────────────────────────────────────────────────────────────

public class GetPromediosPorSeccionQuery : IRequest<List<PromedioDto>>
{
    public int SeccionId { get; set; }
}

public class GetPromediosPorSeccionQueryHandler : IRequestHandler<GetPromediosPorSeccionQuery, List<PromedioDto>>
{
    private readonly IApplicationDbContext _context;

    public GetPromediosPorSeccionQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<PromedioDto>> Handle(GetPromediosPorSeccionQuery request, CancellationToken cancellationToken)
    {
        var promedios = await _context.PromediosFinales
            .Include(p => p.Estudiante)
            .Where(p => p.SeccionId == request.SeccionId)
            .ToListAsync(cancellationToken);

        if (!promedios.Any())
        {
            return new List<PromedioDto>();
        }

        return promedios.Select(p => new PromedioDto
        {
            EstudianteId = p.EstudianteId,
            EstudianteNombre = $"{p.Estudiante!.ApellidoPaterno} {p.Estudiante.ApellidoMaterno}, {p.Estudiante.Nombres}",
            EstudianteCodigo = p.Estudiante.Codigo,
            PromedioCalculado = p.PromedioCalculado,
            Aprobado = p.Aprobado,
            Estado = p.Estado ?? (p.Aprobado ? "Aprobado" : "Desaprobado")
        }).OrderBy(p => p.EstudianteNombre).ToList();
    }
}

// ─── COMMANDS ──────────────────────────────────────────────────────────────

public class CalcularYGuardarPromediosCommand : IRequest<bool>
{
    public int SeccionId { get; set; }
}

public class CalcularYGuardarPromediosCommandHandler : IRequestHandler<CalcularYGuardarPromediosCommand, bool>
{
    private readonly IApplicationDbContext _context;
    private const decimal NOTA_APROBATORIA_MINIMA = 11m;

    public CalcularYGuardarPromediosCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<bool> Handle(CalcularYGuardarPromediosCommand request, CancellationToken cancellationToken)
    {
        // 1. Obtener la sección y su periodo
        var seccion = await _context.Secciones
            .FirstOrDefaultAsync(s => s.Id == request.SeccionId, cancellationToken);
            
        if (seccion == null) throw new Exception("Sección no encontrada.");

        // 2. Obtener todas las evaluaciones de la sección y verificar si suman 100% (opcional pero recomendado)
        var evaluaciones = await _context.Evaluaciones
            .Where(e => e.SeccionId == request.SeccionId)
            .ToListAsync(cancellationToken);

        if (!evaluaciones.Any()) throw new Exception("La sección no tiene evaluaciones configuradas para calcular un promedio.");

        var sumaPesos = evaluaciones.Sum(e => e.PesoPromedio);
        if (sumaPesos == 0) throw new Exception("El peso total de las evaluaciones es 0%, no se puede calcular un promedio ponderado.");

        // 3. Obtener alumnos matriculados en la sección
        var matriculasConfirmadas = await _context.DetallesMatricula
            .Where(d => d.SeccionId == request.SeccionId && d.Matricula!.Estado == EstadoMatricula.Confirmada)
            .Select(d => d.Matricula!.EstudianteId)
            .ToListAsync(cancellationToken);

        // 4. Obtener todas las notas de esta sección
        var notasSeccion = await _context.Notas
            .Where(n => n.Evaluacion!.SeccionId == request.SeccionId)
            .ToListAsync(cancellationToken);

        // 5. Obtener los registros de PromediosFinales existentes para actualizarlos (Upsert)
        var promediosExistentes = await _context.PromediosFinales
            .Where(p => p.SeccionId == request.SeccionId)
            .ToDictionaryAsync(p => p.EstudianteId, p => p, cancellationToken);

        // 6. Cálculo iterativo por alumno
        foreach (var estudianteId in matriculasConfirmadas)
        {
            decimal sumaPonderada = 0m;
            
            foreach (var eval in evaluaciones)
            {
                var notaEstudiante = notasSeccion.FirstOrDefault(n => n.EstudianteId == estudianteId && n.EvaluacionId == eval.Id);
                
                // Si el alumno no tiene nota o estuvo ausente, equivale a 0
                decimal valorNota = 0m;
                if (notaEstudiante != null && notaEstudiante.Calificacion.HasValue && !notaEstudiante.Ausente)
                {
                    valorNota = notaEstudiante.Calificacion.Value;
                }

                // Cálculo ponderado: (Nota * Peso) / 100
                // Si los pesos no suman 100, la fórmula estricta es: (Nota * Peso) / SumaTotalPesos
                sumaPonderada += (valorNota * eval.PesoPromedio) / sumaPesos;
            }

            // Redondear a 2 decimales
            sumaPonderada = Math.Round(sumaPonderada, 2);
            bool aprobado = sumaPonderada >= NOTA_APROBATORIA_MINIMA;

            if (promediosExistentes.TryGetValue(estudianteId, out var promedioRecord))
            {
                promedioRecord.PromedioCalculado = sumaPonderada;
                promedioRecord.Aprobado = aprobado;
                promedioRecord.Estado = aprobado ? "Aprobado" : "Desaprobado";
            }
            else
            {
                _context.PromediosFinales.Add(new PromedioFinal
                {
                    EstudianteId = estudianteId,
                    SeccionId = request.SeccionId,
                    PeriodoAcademicoId = seccion.PeriodoAcademicoId,
                    PromedioCalculado = sumaPonderada,
                    Aprobado = aprobado,
                    Estado = aprobado ? "Aprobado" : "Desaprobado"
                });
            }
        }

        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
