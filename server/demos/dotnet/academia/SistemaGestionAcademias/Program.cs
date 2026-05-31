using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using SistemaGestionAcademias.Data;

var builder = WebApplication.CreateBuilder(args);

// 1. Configuración de la Base de Datos
var connectionString = builder.Configuration.GetConnectionString("DefaultConnection") 
    ?? throw new InvalidOperationException("Connection string 'DefaultConnection' not found.");

builder.Services.AddDbContext<ApplicationDbContext>(options =>
    options.UseSqlServer(connectionString));

// 2. Configuración de Identity (Usuarios y Roles)
// --- ¡ESTA ES LA CLAVE! ---
// .AddRoles<IdentityRole>() es OBLIGATORIO para que el login detecte que eres Admin
builder.Services.AddDefaultIdentity<IdentityUser>(options => options.SignIn.RequireConfirmedAccount = false)
    .AddRoles<IdentityRole>() 
    .AddEntityFrameworkStores<ApplicationDbContext>();
// --------------------------

// 3. Configuración de MVC
builder.Services.AddControllersWithViews();
builder.Services.AddRazorPages(); // Vital para el perfil

// Servicios propios
builder.Services.AddScoped<SistemaGestionAcademias.Services.ConfiguracionService>();

var app = builder.Build();

// Configuración del Pipeline
if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Home/Error");
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseStaticFiles();

app.UseRouting();

app.UseAuthentication();
app.UseAuthorization();

app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Home}/{action=Index}/{id?}");

app.MapRazorPages();

// 4. EJECUTAR SEMBRADOR DE DATOS AL INICIO
using (var scope = app.Services.CreateScope())
{
    var services = scope.ServiceProvider;
    await SembradorDatos.Inicializar(services);
}

app.Run();