using ERP.Application.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Configuracion.Institucion;

public class GetInstitucionQuery : IRequest<InstitucionDto>
{
}

public class GetInstitucionQueryHandler : IRequestHandler<GetInstitucionQuery, InstitucionDto>
{
    private readonly IApplicationDbContext _context;

    public GetInstitucionQueryHandler(IApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<InstitucionDto> Handle(GetInstitucionQuery request, CancellationToken cancellationToken)
    {
        var institucion = await _context.Instituciones.FirstOrDefaultAsync(cancellationToken);

        if (institucion == null)
        {
            return new InstitucionDto();
        }

        return new InstitucionDto
        {
            Id = institucion.Id,
            Nombre = institucion.Nombre,
            RUC = institucion.RUC,
            Direccion = institucion.Direccion,
            Telefono = institucion.Telefono,
            Email = institucion.Email,
            SitioWeb = institucion.SitioWeb,
            Slogan = institucion.Slogan,
            Logo = institucion.Logo
        };
    }
}
