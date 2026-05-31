using ERP.Application.Interfaces;
using ERP.Domain.Entities.Academico;
using MediatR;

namespace ERP.Application.Features.Admision.Matriculas;

public class CreateMatriculaCommand : IRequest<int>
{
    public MatriculaCreateDto Matricula { get; set; } = new();
}

public class CreateMatriculaCommandHandler : IRequestHandler<CreateMatriculaCommand, int>
{
    private readonly IApplicationDbContext _context;

    public CreateMatriculaCommandHandler(IApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<int> Handle(CreateMatriculaCommand request, CancellationToken cancellationToken)
    {
        var numeroAleatorio = new Random().Next(1000, 9999);
        var numeroMatricula = $"MAT-{DateTime.Now.Year}-{numeroAleatorio}";

        var nuevaMatricula = new Matricula
        {
            NumeroMatricula = numeroMatricula,
            EstudianteId = request.Matricula.EstudianteId,
            CarreraId = request.Matricula.CarreraId,
            PeriodoAcademicoId = request.Matricula.PeriodoAcademicoId,
            Ciclo = request.Matricula.Ciclo,
            TipoMatricula = request.Matricula.TipoMatricula,
            Observaciones = request.Matricula.Observaciones,
            Estado = ERP.Domain.Enums.EstadoMatricula.Confirmada,
            FechaMatricula = DateTime.Now
        };

        foreach (var seccionId in request.Matricula.SeccionesIds)
        {
            nuevaMatricula.Detalles.Add(new DetalleMatricula
            {
                SeccionId = seccionId
            });
        }

        _context.Matriculas.Add(nuevaMatricula);
        await _context.SaveChangesAsync(cancellationToken);

        return nuevaMatricula.Id;
    }
}
