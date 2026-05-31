using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;
using System;
using System.Threading.Tasks;

namespace SistemaGestionAcademias.Data
{
    public static class SembradorDatos
    {
        public static async Task Inicializar(IServiceProvider serviceProvider)
        {
            var userManager = serviceProvider.GetRequiredService<UserManager<IdentityUser>>();
            var roleManager = serviceProvider.GetRequiredService<RoleManager<IdentityRole>>();

            // 1. CREAR ROLES DEL SISTEMA (Si no existen)
            string[] roles = { "Admin", "Secretaria", "Instructor" };

            foreach (var role in roles)
            {
                if (!await roleManager.RoleExistsAsync(role))
                {
                    await roleManager.CreateAsync(new IdentityRole(role));
                }
            }

            // 2. BUSCAR TU USUARIO PRINCIPAL
            // Asegúrate de que este sea EXACTAMENTE el correo con el que te logueas
            var emailAdmin = "admin@academia.com"; 
            var userAdmin = await userManager.FindByEmailAsync(emailAdmin);

            if (userAdmin != null)
            {
                // 3. ASIGNAR ROL DE ADMIN (Si aún no lo tiene)
                if (!await userManager.IsInRoleAsync(userAdmin, "Admin"))
                {
                    await userManager.AddToRoleAsync(userAdmin, "Admin");
                }
            }
        }
    }
}