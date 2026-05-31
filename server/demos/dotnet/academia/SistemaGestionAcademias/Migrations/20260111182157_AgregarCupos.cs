using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace SistemaGestionAcademias.Migrations
{
    /// <inheritdoc />
    public partial class AgregarCupos : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<int>(
                name: "CupoMaximo",
                table: "Actividades",
                type: "int",
                nullable: false,
                defaultValue: 0);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CupoMaximo",
                table: "Actividades");
        }
    }
}
