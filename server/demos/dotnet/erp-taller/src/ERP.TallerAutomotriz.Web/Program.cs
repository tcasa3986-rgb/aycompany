using ERP.TallerAutomotriz.Application;
using ERP.TallerAutomotriz.Infrastructure;
using ERP.TallerAutomotriz.Infrastructure.Identity;
using ERP.TallerAutomotriz.Infrastructure.Persistence;
using ERP.TallerAutomotriz.Infrastructure.Persistence.Seeders;
using ERP.TallerAutomotriz.Web.Components;
using ERP.TallerAutomotriz.Web.Components.Account;
using Microsoft.AspNetCore.Components.Authorization;
using Microsoft.AspNetCore.Identity;
using MudBlazor.Services;

var builder = WebApplication.CreateBuilder(args);

// Razor + Blazor Server interactivo
builder.Services.AddRazorComponents()
    .AddInteractiveServerComponents();

// Capa Infrastructure (DbContext + servicios)
builder.Services.AddInfrastructure(builder.Configuration);
builder.Services.AddApplication();

// Identity / Auth (configurado en Web porque requiere shared framework AspNetCore.App)
builder.Services.AddCascadingAuthenticationState();
builder.Services.AddScoped<IdentityUserAccessor>();
builder.Services.AddScoped<IdentityRedirectManager>();
builder.Services.AddScoped<AuthenticationStateProvider, IdentityRevalidatingAuthenticationStateProvider>();

builder.Services.AddAuthentication(options =>
{
    options.DefaultScheme = IdentityConstants.ApplicationScheme;
    options.DefaultSignInScheme = IdentityConstants.ExternalScheme;
}).AddIdentityCookies();

builder.Services.AddIdentityCore<ApplicationUser>(opt =>
{
    opt.Password.RequireDigit = true;
    opt.Password.RequireLowercase = true;
    opt.Password.RequireUppercase = true;
    opt.Password.RequireNonAlphanumeric = true;
    opt.Password.RequiredLength = 8;
    opt.User.RequireUniqueEmail = true;
    opt.SignIn.RequireConfirmedAccount = false;
    opt.Lockout.MaxFailedAccessAttempts = 5;
    opt.Lockout.DefaultLockoutTimeSpan = TimeSpan.FromMinutes(15);
})
    .AddRoles<ApplicationRole>()
    .AddEntityFrameworkStores<ApplicationDbContext>()
    .AddSignInManager()
    .AddDefaultTokenProviders();

// MudBlazor
builder.Services.AddMudServices();

// SignalR (incluido por Blazor Server)
builder.Services.AddSignalR(opt => opt.MaximumReceiveMessageSize = 2 * 1024 * 1024);

builder.Services.AddHttpContextAccessor();

var app = builder.Build();

if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Error", createScopeForErrors: true);
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseStaticFiles();
app.UseAntiforgery();

app.UseAuthentication();
app.UseAuthorization();

app.MapRazorComponents<App>()
    .AddInteractiveServerRenderMode();

app.MapAdditionalIdentityEndpoints();

// Inicialización de BD + seed
using (var scope = app.Services.CreateScope())
{
    try
    {
        await DbInitializer.InitializeAsync(app.Services);
    }
    catch (Exception ex)
    {
        var logger = scope.ServiceProvider.GetRequiredService<ILoggerFactory>().CreateLogger("Startup");
        logger.LogError(ex, "Error inicializando BD. Asegúrese de que SQL Server esté accesible y la connection string sea correcta.");
    }
}

app.Run();
