using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace SistemaGestionAcademias.Migrations
{
    /// <inheritdoc />
    public partial class ModuloAsistencia : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "DiasSemana",
                table: "Actividades",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<TimeSpan>(
                name: "HoraFin",
                table: "Actividades",
                type: "time",
                nullable: true);

            migrationBuilder.AddColumn<TimeSpan>(
                name: "HoraInicio",
                table: "Actividades",
                type: "time",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "TotalHoras",
                table: "Actividades",
                type: "int",
                nullable: false,
                defaultValue: 0);

            migrationBuilder.CreateTable(
                name: "SesionesClase",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    Fecha = table.Column<DateTime>(type: "datetime2", nullable: false),
                    TemaDelDia = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    AsistenciaTomada = table.Column<bool>(type: "bit", nullable: false),
                    ActividadId = table.Column<int>(type: "int", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_SesionesClase", x => x.Id);
                    table.ForeignKey(
                        name: "FK_SesionesClase_Actividades_ActividadId",
                        column: x => x.ActividadId,
                        principalTable: "Actividades",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Asistencias",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    Estado = table.Column<string>(type: "nvarchar(max)", nullable: false),
                    Observacion = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    SesionClaseId = table.Column<int>(type: "int", nullable: false),
                    AlumnoId = table.Column<int>(type: "int", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Asistencias", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Asistencias_Alumnos_AlumnoId",
                        column: x => x.AlumnoId,
                        principalTable: "Alumnos",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Asistencias_SesionesClase_SesionClaseId",
                        column: x => x.SesionClaseId,
                        principalTable: "SesionesClase",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_Asistencias_AlumnoId",
                table: "Asistencias",
                column: "AlumnoId");

            migrationBuilder.CreateIndex(
                name: "IX_Asistencias_SesionClaseId",
                table: "Asistencias",
                column: "SesionClaseId");

            migrationBuilder.CreateIndex(
                name: "IX_SesionesClase_ActividadId",
                table: "SesionesClase",
                column: "ActividadId");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "Asistencias");

            migrationBuilder.DropTable(
                name: "SesionesClase");

            migrationBuilder.DropColumn(
                name: "DiasSemana",
                table: "Actividades");

            migrationBuilder.DropColumn(
                name: "HoraFin",
                table: "Actividades");

            migrationBuilder.DropColumn(
                name: "HoraInicio",
                table: "Actividades");

            migrationBuilder.DropColumn(
                name: "TotalHoras",
                table: "Actividades");
        }
    }
}
