using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace SistemaGestionAcademias.Migrations
{
    /// <inheritdoc />
    public partial class ActualizacionPagos : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "DescuentoAplicado",
                table: "Inscripciones",
                type: "nvarchar(100)",
                maxLength: 100,
                nullable: true);

            migrationBuilder.AddColumn<decimal>(
                name: "MontoFinal",
                table: "Inscripciones",
                type: "decimal(18,2)",
                nullable: false,
                defaultValue: 0m);

            migrationBuilder.AddColumn<decimal>(
                name: "MontoOriginal",
                table: "Inscripciones",
                type: "decimal(18,2)",
                nullable: false,
                defaultValue: 0m);

            migrationBuilder.AlterColumn<string>(
                name: "Descripcion",
                table: "Actividades",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(max)",
                oldNullable: true);

            migrationBuilder.AddColumn<string>(
                name: "Beneficios",
                table: "Actividades",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<decimal>(
                name: "Costo",
                table: "Actividades",
                type: "decimal(18,2)",
                nullable: false,
                defaultValue: 0m);

            migrationBuilder.AddColumn<string>(
                name: "Objetivos",
                table: "Actividades",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "Silabus",
                table: "Actividades",
                type: "nvarchar(max)",
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "DescuentoAplicado",
                table: "Inscripciones");

            migrationBuilder.DropColumn(
                name: "MontoFinal",
                table: "Inscripciones");

            migrationBuilder.DropColumn(
                name: "MontoOriginal",
                table: "Inscripciones");

            migrationBuilder.DropColumn(
                name: "Beneficios",
                table: "Actividades");

            migrationBuilder.DropColumn(
                name: "Costo",
                table: "Actividades");

            migrationBuilder.DropColumn(
                name: "Objetivos",
                table: "Actividades");

            migrationBuilder.DropColumn(
                name: "Silabus",
                table: "Actividades");

            migrationBuilder.AlterColumn<string>(
                name: "Descripcion",
                table: "Actividades",
                type: "nvarchar(max)",
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(500)",
                oldMaxLength: 500,
                oldNullable: true);
        }
    }
}
