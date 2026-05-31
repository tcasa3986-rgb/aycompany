using ERP.Application.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Configuracion.Institucion;

public class UpdateInstitucionCommand : IRequest<int>
{
    public InstitucionDto Institucion { get; set; } = new();
}

public class UpdateInstitucionCommandHandler : IRequestHandler<UpdateInstitucionCommand, int>
{
    private readonly IApplicationDbContext _context;

    public UpdateInstitucionCommandHandler(IApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<int> Handle(UpdateInstitucionCommand request, CancellationToken cancellationToken)
    {
        var institucion = await _context.Instituciones.FirstOrDefaultAsync(cancellationToken);

        if (institucion == null)
        {
            institucion = new ERP.Domain.Entities.Config.Institucion
            {
                Nombre = request.Institucion.Nombre,
                RUC = request.Institucion.RUC,
                Direccion = request.Institucion.Direccion,
                Telefono = request.Institucion.Telefono,
                Email = request.Institucion.Email,
                SitioWeb = request.Institucion.SitioWeb,
                Slogan = request.Institucion.Slogan,
                Logo = request.Institucion.Logo
            };
            _context.Instituciones.Add(institucion);
        }
        else
        {
            institucion.Nombre = request.Institucion.Nombre;
            institucion.RUC = request.Institucion.RUC;
            institucion.Direccion = request.Institucion.Direccion;
            institucion.Telefono = request.Institucion.Telefono;
            institucion.Email = request.Institucion.Email;
            institucion.SitioWeb = request.Institucion.SitioWeb;
            institucion.Slogan = request.Institucion.Slogan;
            institucion.Logo = request.Institucion.Logo;
        }

        await _context.SaveChangesAsync(cancellationToken);

        return institucion.Id;
    }
}
