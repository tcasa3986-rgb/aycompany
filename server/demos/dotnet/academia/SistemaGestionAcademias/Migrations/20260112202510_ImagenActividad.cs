using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace SistemaGestionAcademias.Migrations
{
    /// <inheritdoc />
    public partial class ImagenActividad : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "ImagenUrl",
                table: "Actividades",
                type: "nvarchar(max)",
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "ImagenUrl",
                table: "Actividades");
        }
    }
}
