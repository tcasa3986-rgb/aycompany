using Microsoft.Extensions.DependencyInjection;

namespace ERP.TallerAutomotriz.Application;

public static class DependencyInjection
{
    public static IServiceCollection AddApplication(this IServiceCollection services)
    {
        // Aquí se registrarán los servicios de aplicación cuando se implementen
        return services;
    }
}
