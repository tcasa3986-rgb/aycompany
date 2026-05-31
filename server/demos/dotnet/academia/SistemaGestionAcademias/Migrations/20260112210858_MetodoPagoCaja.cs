using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace SistemaGestionAcademias.Migrations
{
    /// <inheritdoc />
    public partial class MetodoPagoCaja : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "MetodoPago",
                table: "Inscripciones",
                type: "nvarchar(50)",
                maxLength: 50,
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "MetodoPago",
                table: "Inscripciones");
        }
    }
}
