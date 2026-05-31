using ERP.Application.Interfaces;
using ERP.Domain.Enums;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Dashboard;

public class UltimaMatriculaDto
{
    public string Codigo { get; set; } = string.Empty;
    public string Estudiante { get; set; } = string.Empty;
    public string Carrera { get; set; } = string.Empty;
    public string Fecha { get; set; } = string.Empty;
    public string Estado { get; set; } = string.Empty;
}

public class DashboardDataDto
{
    public int EstudiantesActivos { get; set; }
    public int DocentesActivos { get; set; }
    public int MatriculasPeriodo { get; set; }
    public decimal IngresosMes { get; set; }
    public decimal DeudaPorCobrar { get; set; }
    public int CursosActivos { get; set; }
    
    // Lista de ultimas matriculas
    public List<UltimaMatriculaDto> UltimasMatriculas { get; set; } = new();
}

public class GetDashboardDataQuery : IRequest<DashboardDataDto> { }

public class GetDashboardDataQueryHandler : IRequestHandler<GetDashboardDataQuery, DashboardDataDto>
{
    private readonly IApplicationDbContext _context;

    public GetDashboardDataQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<DashboardDataDto> Handle(GetDashboardDataQuery request, CancellationToken cancellationToken)
    {
        var data = new DashboardDataDto();

        data.EstudiantesActivos = await _context.Estudiantes.CountAsync(e => e.Estado == EstadoEstudiante.Activo, cancellationToken);
        data.DocentesActivos = await _context.Docentes.CountAsync(d => d.Estado == EstadoDocente.Activo, cancellationToken);
        
        // Asumiendo que el periodo actual es el que tiene matriculas recientes
        data.MatriculasPeriodo = await _context.Matriculas.CountAsync(m => m.Estado != EstadoMatricula.Anulada, cancellationToken);
        
        // Ingresos: Pagos realizados en el mes actual (usamos año actual por los seeds si no hay en el mes)
        var mesActual = DateTime.Today.Month;
        var anioActual = DateTime.Today.Year;
        
        data.IngresosMes = await _context.Pagos
            .Where(p => p.FechaPago.Month == mesActual && p.FechaPago.Year == anioActual)
            .SumAsync(p => p.MontoPagado, cancellationToken);

        // Deuda total pendiente
        data.DeudaPorCobrar = await _context.Deudas
            .Where(d => d.Estado == EstadoPago.Pendiente || d.Estado == EstadoPago.Vencido)
            .SumAsync(d => d.MontoOriginal + d.MontoMora - d.MontoDescuento, cancellationToken);

        // Cursos activos (Secciones programadas)
        data.CursosActivos = await _context.Secciones.Select(s => s.CursoId).Distinct().CountAsync(cancellationToken);

        // Ultimas 5 matriculas
        data.UltimasMatriculas = await _context.Matriculas
            .Include(m => m.Estudiante)
            .Include(m => m.Carrera)
            .OrderByDescending(m => m.FechaMatricula)
            .Take(5)
            .Select(m => new UltimaMatriculaDto
            {
                Codigo = m.NumeroMatricula,
                Estudiante = $"{m.Estudiante!.ApellidoPaterno} {m.Estudiante.ApellidoMaterno}, {m.Estudiante.Nombres}",
                Carrera = m.Carrera!.Nombre,
                Fecha = m.FechaMatricula.ToString("dd/MM/yyyy"),
                Estado = m.Estado.ToString()
            })
            .ToListAsync(cancellationToken);

        return data;
    }
}
