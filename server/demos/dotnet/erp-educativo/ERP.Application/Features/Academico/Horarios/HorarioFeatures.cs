using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Academico.Horarios;

public class HorarioDto
{
    public int Id { get; set; }
    public int SeccionId { get; set; }
    public DayOfWeek DiaSemana { get; set; }
    public TimeSpan HoraInicio { get; set; }
    public TimeSpan HoraFin { get; set; }
    public int? AulaId { get; set; }
    // Nombres legibles para mostrar en UI
    public string NombreDia => DiaSemana switch
    {
        DayOfWeek.Monday => "Lunes",
        DayOfWeek.Tuesday => "Martes",
        DayOfWeek.Wednesday => "Miércoles",
        DayOfWeek.Thursday => "Jueves",
        DayOfWeek.Friday => "Viernes",
        DayOfWeek.Saturday => "Sábado",
        DayOfWeek.Sunday => "Domingo",
        _ => ""
    };
    public string HorarioTexto => $"{HoraInicio:hh\\:mm} - {HoraFin:hh\\:mm}";
}

public class GetHorariosQuery : IRequest<List<HorarioDto>>
{
    public int SeccionId { get; set; }
}

public class GetHorariosQueryHandler : IRequestHandler<GetHorariosQuery, List<HorarioDto>>
{
    private readonly IApplicationDbContext _context;
    public GetHorariosQueryHandler(IApplicationDbContext context) => _context = context;

    public async Task<List<HorarioDto>> Handle(GetHorariosQuery request, CancellationToken cancellationToken)
    {
        return await _context.Horarios
            .Where(h => h.SeccionId == request.SeccionId)
            .OrderBy(h => h.DiaSemana).ThenBy(h => h.HoraInicio)
            .Select(h => new HorarioDto
            {
                Id = h.Id,
                SeccionId = h.SeccionId,
                DiaSemana = h.DiaSemana,
                HoraInicio = h.HoraInicio.ToTimeSpan(),
                HoraFin = h.HoraFin.ToTimeSpan(),
                AulaId = h.AulaId
            })
            .ToListAsync(cancellationToken);
    }
}

public class UpsertHorarioCommand : IRequest<int>
{
    public int Id { get; set; }
    public int SeccionId { get; set; }
    public DayOfWeek DiaSemana { get; set; }
    public TimeSpan HoraInicio { get; set; }
    public TimeSpan HoraFin { get; set; }
    public int? AulaId { get; set; }
}

public class UpsertHorarioCommandHandler : IRequestHandler<UpsertHorarioCommand, int>
{
    private readonly IApplicationDbContext _context;
    public UpsertHorarioCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<int> Handle(UpsertHorarioCommand request, CancellationToken cancellationToken)
    {
        if (request.HoraFin <= request.HoraInicio)
            throw new Exception("La hora de fin debe ser mayor a la hora de inicio.");

        // Validar cruce de horarios para la misma sección
        var cruces = await _context.Horarios
            .Where(h => h.SeccionId == request.SeccionId && h.DiaSemana == request.DiaSemana && h.Id != request.Id)
            .ToListAsync(cancellationToken);

        var nuevaHoraInicio = TimeOnly.FromTimeSpan(request.HoraInicio);
        var nuevaHoraFin = TimeOnly.FromTimeSpan(request.HoraFin);

        foreach (var h in cruces)
        {
            if ((nuevaHoraInicio >= h.HoraInicio && nuevaHoraInicio < h.HoraFin) ||
                (nuevaHoraFin > h.HoraInicio && nuevaHoraFin <= h.HoraFin) ||
                (nuevaHoraInicio <= h.HoraInicio && nuevaHoraFin >= h.HoraFin))
            {
                throw new Exception("Existe un cruce de horario con otra clase de esta sección.");
            }
        }

        Horario entity;
        if (request.Id > 0)
        {
            entity = await _context.Horarios.FindAsync([request.Id], cancellationToken) ?? throw new Exception("Horario no encontrado");
        }
        else
        {
            entity = new Horario();
            _context.Horarios.Add(entity);
        }

        entity.SeccionId = request.SeccionId;
        entity.DiaSemana = request.DiaSemana;
        entity.HoraInicio = nuevaHoraInicio;
        entity.HoraFin = nuevaHoraFin;
        entity.AulaId = request.AulaId;

        await _context.SaveChangesAsync(cancellationToken);
        return entity.Id;
    }
}

public class DeleteHorarioCommand : IRequest<bool> { public int Id { get; set; } }
public class DeleteHorarioCommandHandler : IRequestHandler<DeleteHorarioCommand, bool>
{
    private readonly IApplicationDbContext _context;
    public DeleteHorarioCommandHandler(IApplicationDbContext context) => _context = context;

    public async Task<bool> Handle(DeleteHorarioCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Horarios.FindAsync([request.Id], cancellationToken);
        if (entity == null) return false;

        _context.Horarios.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);
        return true;
    }
}
