using ERP.Application.Interfaces;
using ERP.Domain.Entities.Config;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Configuracion.Periodos;

public class PeriodoAcademicoDto
{
    public int Id { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public string Codigo { get; set; } = string.Empty;
    public DateTime FechaInicio { get; set; }
    public DateTime FechaFin { get; set; }
    public bool EsActual { get; set; }
    public int AnioAcademico { get; set; }
}

public class GetPeriodosQuery : IRequest<List<PeriodoAcademicoDto>> { }
public class GetPeriodosQueryHandler : IRequestHandler<GetPeriodosQuery, List<PeriodoAcademicoDto>>
{
    private readonly IApplicationDbContext _context;
    public GetPeriodosQueryHandler(IApplicationDbContext context) => _context = context;
    public async Task<List<PeriodoAcademicoDto>> Handle(GetPeriodosQuery request, CancellationToken cancellationToken)
        => await _context.PeriodosAcademicos
            .OrderByDescending(p => p.AnioAcademico).ThenByDescending(p => p.FechaInicio)
            .Select(p => new PeriodoAcademicoDto
            {
                Id = p.Id, Nombre = p.Nombre, Codigo = p.Codigo,
                FechaInicio = p.FechaInicio, FechaFin = p.FechaFin,
                EsActual = p.EsActual, AnioAcademico = p.AnioAcademico
            })
            .ToListAsync(cancellationToken);
}

public class UpsertPeriodoCommand : IRequest<int>
{
    public int Id { get; set; }
    public string Nombre { get; set; } = string.Empty;
    public string Codigo { get; set; } = string.Empty;
    public DateTime FechaInicio { get; set; }
    public DateTime FechaFin { get; set; }
    public bool EsActual { get; set; }
    public int AnioAcademico { get; set; }
}
public class UpsertPeriodoCommandHandler : IRequestHandler<UpsertPeriodoCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertPeriodoCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<int> Handle(UpsertPeriodoCommand request, CancellationToken cancellationToken)
    {
        PeriodoAcademico entity;
        if (request.Id > 0)
            entity = await _context.PeriodosAcademicos.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Período no encontrado");
        else { entity = new PeriodoAcademico(); _context.PeriodosAcademicos.Add(entity); }
        entity.Nombre = request.Nombre; entity.Codigo = request.Codigo;
        entity.FechaInicio = request.FechaInicio; entity.FechaFin = request.FechaFin;
        entity.EsActual = request.EsActual; entity.AnioAcademico = request.AnioAcademico;
        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeletePeriodoCommand : IRequest<bool> { public int Id { get; set; } }
public class DeletePeriodoCommandHandler : IRequestHandler<DeletePeriodoCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeletePeriodoCommandHandler(IApplicationDbContext context) => _context = context;
    public async Task<bool> Handle(DeletePeriodoCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.PeriodosAcademicos.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;
        _context.PeriodosAcademicos.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
