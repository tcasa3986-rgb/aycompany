using ERP.Application.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Admision.Matriculas;

public class GetMatriculasQuery : IRequest<List<MatriculaListDto>>
{
}

public class GetMatriculasQueryHandler : IRequestHandler<GetMatriculasQuery, List<MatriculaListDto>>
{
    private readonly IApplicationDbContext _context;

    public GetMatriculasQueryHandler(IApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<List<MatriculaListDto>> Handle(GetMatriculasQuery request, CancellationToken cancellationToken)
    {
        return await _context.Matriculas
            .Include(m => m.Estudiante)
            .Include(m => m.Carrera)
            .OrderByDescending(m => m.FechaMatricula)
            .Select(m => new MatriculaListDto
            {
                Id = m.Id,
                NumeroMatricula = m.NumeroMatricula,
                EstudianteId = m.EstudianteId,
                Estudiante = m.Estudiante != null ? m.Estudiante.NombreCompleto : "",
                CodigoEstudiante = m.Estudiante != null ? m.Estudiante.Codigo : "",
                Carrera = m.Carrera != null ? m.Carrera.Nombre : "",
                Periodo = "2026-I", // Simplificado para demo, deberíamos incluir PeriodoAcademico
                FechaMatricula = m.FechaMatricula,
                Estado = m.Estado
            })
            .ToListAsync(cancellationToken);
    }
}

public class GetMatriculaByIdQuery : IRequest<MatriculaDto?> { public int Id { get; set; } }
public class GetMatriculaByIdQueryHandler : IRequestHandler<GetMatriculaByIdQuery, MatriculaDto?>
{
    private readonly IApplicationDbContext _context;
    public GetMatriculaByIdQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<MatriculaDto?> Handle(GetMatriculaByIdQuery request, CancellationToken cancellationToken)
        => await _context.Matriculas
            .Include(m => m.Estudiante)
            .Include(m => m.Carrera)
            .Include(m => m.Detalles).ThenInclude(d => d.Seccion).ThenInclude(s => s.Curso)
            .Where(m => m.Id == request.Id)
            .Select(m => new MatriculaDto
            {
                Id = m.Id,
                NumeroMatricula = m.NumeroMatricula,
                EstudianteNombre = m.Estudiante != null ? m.Estudiante.NombreCompleto : "",
                EstudianteCodigo = m.Estudiante != null ? m.Estudiante.Codigo : "",
                CarreraNombre = m.Carrera != null ? m.Carrera.Nombre : "",
                Ciclo = m.Ciclo,
                Cursos = m.Detalles.Select(d => new MatriculaCursoDto
                {
                    Nombre = d.Seccion != null && d.Seccion.Curso != null ? d.Seccion.Curso.Nombre : "",
                    Seccion = d.Seccion != null ? d.Seccion.Nombre : ""
                }).ToList()
            })
            .FirstOrDefaultAsync(cancellationToken);
}
