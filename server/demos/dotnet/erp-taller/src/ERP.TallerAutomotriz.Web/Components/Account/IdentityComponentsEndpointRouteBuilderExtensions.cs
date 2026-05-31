using ERP.TallerAutomotriz.Infrastructure.Identity;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Routing;

namespace ERP.TallerAutomotriz.Web.Components.Account;

internal static class IdentityComponentsEndpointRouteBuilderExtensions
{
    public static IEndpointConventionBuilder MapAdditionalIdentityEndpoints(this IEndpointRouteBuilder endpoints)
    {
        ArgumentNullException.ThrowIfNull(endpoints);

        var accountGroup = endpoints.MapGroup("/Account");

        // POST /Account/LoginPost — procesa el formulario de login
        accountGroup.MapPost("/LoginPost", async (
            HttpContext ctx,
            SignInManager<ApplicationUser> signInManager) =>
        {
            var form = await ctx.Request.ReadFormAsync();
            var email = form["Email"].ToString();
            var password = form["Password"].ToString();
            var rememberMe = form["RememberMe"].ToString() == "true";
            var returnUrl = form["ReturnUrl"].ToString();
            if (string.IsNullOrWhiteSpace(returnUrl)) returnUrl = "/";

            if (string.IsNullOrWhiteSpace(email) || string.IsNullOrWhiteSpace(password))
            {
                return Results.Redirect("/Account/Login?error=invalid");
            }

            var result = await signInManager.PasswordSignInAsync(email, password, rememberMe, lockoutOnFailure: true);
            if (result.Succeeded)
            {
                // Solo permitir redirecciones locales
                if (Uri.IsWellFormedUriString(returnUrl, UriKind.Relative))
                    return Results.Redirect(returnUrl);
                return Results.Redirect("/");
            }
            if (result.IsLockedOut)
            {
                return Results.Redirect("/Account/Login?error=locked");
            }

            return Results.Redirect("/Account/Login?error=invalid");
        });

        // GET /Account/Logout — cierra sesión y redirige al login
        accountGroup.MapGet("/Logout", async (
            HttpContext ctx,
            SignInManager<ApplicationUser> signInManager) =>
        {
            await signInManager.SignOutAsync();
            return Results.Redirect("/Account/Login");
        });

        // POST /Account/Logout (compatibilidad)
        accountGroup.MapPost("/Logout", async (
            HttpContext ctx,
            SignInManager<ApplicationUser> signInManager) =>
        {
            await signInManager.SignOutAsync();
            return Results.Redirect("/Account/Login");
        });

        return accountGroup;
    }
}
