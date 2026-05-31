using ERP.TallerAutomotriz.Application.Interfaces;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using ERP.TallerAutomotriz.Infrastructure.Services;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;

namespace ERP.TallerAutomotriz.Infrastructure;

public static class DependencyInjection
{
    public static IServiceCollection AddInfrastructure(this IServiceCollection services, IConfiguration configuration)
    {
        var connectionString = configuration.GetConnectionString("DefaultConnection")
            ?? throw new InvalidOperationException("Connection string 'DefaultConnection' not found.");

        services.AddDbContext<ApplicationDbContext>(options =>
            options.UseSqlServer(connectionString, sql =>
            {
                sql.EnableRetryOnFailure(3);
                sql.MigrationsAssembly(typeof(ApplicationDbContext).Assembly.FullName);
            }));

        // Servicios de aplicación
        services.AddScoped<IDashboardService, DashboardService>();
        services.AddScoped<ICustomerService, CustomerService>();
        services.AddScoped<IInventoryService, InventoryService>();
        services.AddScoped<IWorkshopService, WorkshopService>();
        services.AddScoped<IPersonnelService, PersonnelService>();
        services.AddScoped<ISalesService, SalesService>();

        return services;
    }
}
