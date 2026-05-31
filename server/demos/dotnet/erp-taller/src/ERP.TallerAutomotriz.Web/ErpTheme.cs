using MudBlazor;

namespace ERP.TallerAutomotriz.Web;

public static class ErpTheme
{
    public static MudTheme Theme = new()
    {
        PaletteLight = new PaletteLight
        {
            Primary = "#1F2C50",          // Azul marino oscuro (sidebar)
            Secondary = "#2BB59A",         // Verde teal (acento)
            Tertiary = "#F4A93C",          // Naranja (acento amarillo)
            Info = "#3FA9F5",
            Success = "#2BB59A",
            Warning = "#F4A93C",
            Error = "#E15A5A",
            AppbarBackground = "#FFFFFF",
            AppbarText = "#1F2C50",
            DrawerBackground = "#1F2C50",
            DrawerText = "#FFFFFF",
            DrawerIcon = "#FFFFFF",
            Background = "#F4F6FA",
            Surface = "#FFFFFF",
            TextPrimary = "#1F2C50",
            TextSecondary = "#5D6B82",
            ActionDefault = "#5D6B82",
            DividerLight = "#E3E7EE",
            LinesDefault = "#E3E7EE"
        },
        LayoutProperties = new LayoutProperties
        {
            DefaultBorderRadius = "10px",
            DrawerWidthLeft = "260px",
            AppbarHeight = "70px"
        }
    };
}
