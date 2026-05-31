using ERP.TallerAutomotriz.Infrastructure.Identity;
using Microsoft.AspNetCore.Identity;

namespace ERP.TallerAutomotriz.Web.Components.Account;

internal sealed class IdentityUserAccessor(UserManager<ApplicationUser> userManager, IdentityRedirectManager redirectManager)
{
    public async Task<ApplicationUser> GetRequiredUserAsync(HttpContext context)
    {
        var user = await userManager.GetUserAsync(context.User);
        if (user is null)
        {
            redirectManager.RedirectToWithStatus("Account/InvalidUser",
                $"Error: No se pudo cargar el usuario con Id '{userManager.GetUserId(context.User)}'.", context);
        }
        return user!;
    }
}
