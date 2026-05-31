using ERP.Domain.Entities.Academico;
using ERP.Domain.Entities.Config;
using ERP.Domain.Entities.Financiero;
using ERP.Domain.Entities.Personas;
using ERP.Domain.Entities.Biblioteca;
using ERP.Domain.Enums;
using ERP.Infrastructure.Data;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;

namespace ERP.Infrastructure.Seeds;

public static class DatabaseSeeder
{
    public static async Task SeedAsync(ApplicationDbContext context, UserManager<ApplicationUser> userManager, RoleManager<IdentityRole> roleManager)
    {
        await context.Database.EnsureCreatedAsync();

        // ── Roles y usuario admin ────────────────────────────────────────
        string[] roles = { "Administrador", "Docente", "Estudiante" };
        foreach (var role in roles)
            if (!await roleManager.RoleExistsAsync(role))
                await roleManager.CreateAsync(new IdentityRole(role));

        var adminEmail = "admin@erpeducativo.com";
        if (await userManager.FindByEmailAsync(adminEmail) == null)
        {
            var adminUser = new ApplicationUser { UserName = adminEmail, Email = adminEmail, EmailConfirmed = true };
            var result = await userManager.CreateAsync(adminUser, "Admin123!");
            if (result.Succeeded) await userManager.AddToRoleAsync(adminUser, "Administrador");
        }

        // ── Institución ──────────────────────────────────────────────────
        if (!await context.Instituciones.AnyAsync())
        {
            context.Instituciones.Add(new Institucion
            {
                Nombre = "Instituto Superior Tecnológico ERP",
                NombreCorto = "IST ERP",
                RUC = "20123456789",
                Direccion = "Av. La Cultura 1234, Cusco",
                Telefono = "084-234567",
                Email = "info@isterp.edu.pe",
                SitioWeb = "www.isterp.edu.pe",
                Slogan = "Formando líderes del futuro",
                Departamento = "Cusco",
                Provincia = "Cusco",
                Distrito = "Cusco",
                Pais = "Perú"
            });
            await context.SaveChangesAsync();
        }

        // ── Períodos Académicos ──────────────────────────────────────────
        if (!await context.PeriodosAcademicos.AnyAsync())
        {
            context.PeriodosAcademicos.AddRange(
                new PeriodoAcademico { Nombre = "2024 - I Semestre", Codigo = "2024-I", AnioAcademico = 2024, FechaInicio = new DateTime(2024, 3, 1), FechaFin = new DateTime(2024, 7, 31), EsActual = false },
                new PeriodoAcademico { Nombre = "2024 - II Semestre", Codigo = "2024-II", AnioAcademico = 2024, FechaInicio = new DateTime(2024, 8, 1), FechaFin = new DateTime(2024, 12, 20), EsActual = false },
                new PeriodoAcademico { Nombre = "2025 - I Semestre", Codigo = "2025-I", AnioAcademico = 2025, FechaInicio = new DateTime(2025, 3, 1), FechaFin = new DateTime(2025, 7, 31), EsActual = true }
            );
            await context.SaveChangesAsync();
        }

        // ── Facultades ───────────────────────────────────────────────────
        if (!await context.Facultades.AnyAsync())
        {
            context.Facultades.AddRange(
                new Facultad { Codigo = "FING", Nombre = "Facultad de Ingeniería", Descripcion = "Carreras de tecnología e ingeniería", Decano = "Dr. Carlos Quispe" },
                new Facultad { Codigo = "FADM", Nombre = "Facultad de Administración", Descripcion = "Gestión empresarial y comercio", Decano = "Mg. Ana Mamani" },
                new Facultad { Codigo = "FSAL", Nombre = "Facultad de Ciencias de la Salud", Descripcion = "Enfermería, farmacia y afines", Decano = "Dr. Luis Condori" }
            );
            await context.SaveChangesAsync();
        }

        // ── Carreras ─────────────────────────────────────────────────────
        if (!await context.Carreras.AnyAsync())
        {
            var fac = await context.Facultades.ToListAsync();
            var fIng = fac.First(f => f.Codigo == "FING").Id;
            var fAdm = fac.First(f => f.Codigo == "FADM").Id;
            var fSal = fac.First(f => f.Codigo == "FSAL").Id;

            context.Carreras.AddRange(
                new Carrera { FacultadId = fIng, Codigo = "ISW", Nombre = "Ingeniería de Software", NivelEducativo = NivelEducativo.Universitario, Modalidad = Modalidad.Presencial, DuracionSemestres = 10, TotalCreditos = 220 },
                new Carrera { FacultadId = fIng, Codigo = "ISI", Nombre = "Sistemas de Información", NivelEducativo = NivelEducativo.Universitario, Modalidad = Modalidad.Presencial, DuracionSemestres = 10, TotalCreditos = 210 },
                new Carrera { FacultadId = fAdm, Codigo = "ADM", Nombre = "Administración de Empresas", NivelEducativo = NivelEducativo.Universitario, Modalidad = Modalidad.Presencial, DuracionSemestres = 10, TotalCreditos = 200 },
                new Carrera { FacultadId = fAdm, Codigo = "CON", Nombre = "Contabilidad y Finanzas", NivelEducativo = NivelEducativo.Universitario, Modalidad = Modalidad.Presencial, DuracionSemestres = 10, TotalCreditos = 200 },
                new Carrera { FacultadId = fSal, Codigo = "ENF", Nombre = "Enfermería Técnica", NivelEducativo = NivelEducativo.Tecnico, Modalidad = Modalidad.Presencial, DuracionSemestres = 6, TotalCreditos = 120 }
            );
            await context.SaveChangesAsync();
        }

        // ── Cursos ───────────────────────────────────────────────────────
        if (!await context.Cursos.AnyAsync())
        {
            context.Cursos.AddRange(
                new Curso { Codigo = "MAT101", Nombre = "Matemáticas I", Creditos = 4, HorasTeoricas = 3, HorasPracticas = 2 },
                new Curso { Codigo = "ALG101", Nombre = "Álgebra Lineal", Creditos = 3, HorasTeoricas = 2, HorasPracticas = 2 },
                new Curso { Codigo = "PRG101", Nombre = "Fundamentos de Programación", Creditos = 4, HorasTeoricas = 2, HorasPracticas = 4 },
                new Curso { Codigo = "BDT101", Nombre = "Base de Datos I", Creditos = 4, HorasTeoricas = 2, HorasPracticas = 4 },
                new Curso { Codigo = "FIS101", Nombre = "Física I", Creditos = 4, HorasTeoricas = 3, HorasPracticas = 2 },
                new Curso { Codigo = "ADM101", Nombre = "Introducción a la Administración", Creditos = 3, HorasTeoricas = 3, HorasPracticas = 0 },
                new Curso { Codigo = "CON101", Nombre = "Contabilidad General", Creditos = 4, HorasTeoricas = 3, HorasPracticas = 2 },
                new Curso { Codigo = "ECO101", Nombre = "Economía I", Creditos = 3, HorasTeoricas = 3, HorasPracticas = 0 },
                new Curso { Codigo = "LEN101", Nombre = "Lenguaje y Comunicación", Creditos = 2, HorasTeoricas = 2, HorasPracticas = 0 },
                new Curso { Codigo = "ETI101", Nombre = "Ética Profesional", Creditos = 2, HorasTeoricas = 2, HorasPracticas = 0 }
            );
            await context.SaveChangesAsync();
        }

        // ── Conceptos de Pago ────────────────────────────────────────────
        if (!await context.ConceptosPago.AnyAsync())
        {
            context.ConceptosPago.AddRange(
                new ConceptoPago { Codigo = "MAT", Nombre = "Matrícula", MontoBase = 250, AplicaMora = false, EsRecurrente = false },
                new ConceptoPago { Codigo = "PEN1", Nombre = "Pensión 1er Mes", MontoBase = 450, AplicaMora = true, PorcentajeMora = 1.5m, DiasGraciaMora = 5, EsRecurrente = true },
                new ConceptoPago { Codigo = "PEN2", Nombre = "Pensión 2do Mes", MontoBase = 450, AplicaMora = true, PorcentajeMora = 1.5m, DiasGraciaMora = 5, EsRecurrente = true },
                new ConceptoPago { Codigo = "PEN3", Nombre = "Pensión 3er Mes", MontoBase = 450, AplicaMora = true, PorcentajeMora = 1.5m, DiasGraciaMora = 5, EsRecurrente = true },
                new ConceptoPago { Codigo = "EXAM", Nombre = "Examen de Rezagados", MontoBase = 80, AplicaMora = false, EsRecurrente = false },
                new ConceptoPago { Codigo = "CERT", Nombre = "Certificado de Estudios", MontoBase = 30, AplicaMora = false, EsRecurrente = false }
            );
            await context.SaveChangesAsync();
        }

        // ── Docentes ─────────────────────────────────────────────────────
        if (!await context.Docentes.AnyAsync())
        {
            context.Docentes.AddRange(
                new Docente { Codigo = "DOC001", Nombres = "Carlos", ApellidoPaterno = "Quispe", ApellidoMaterno = "Huanca", DNI = "40123456", FechaNacimiento = new DateTime(1975, 5, 10), Sexo = TipoSexo.Masculino, Email = "cquispe@isterp.edu.pe", GradoAcademico = "Magíster", Especialidad = "Ingeniería de Software", TipoContrato = TipoContrato.TiempoCompleto, FechaIngreso = new DateTime(2018, 3, 1), Sueldo = 3500 },
                new Docente { Codigo = "DOC002", Nombres = "María", ApellidoPaterno = "Flores", ApellidoMaterno = "Salas", DNI = "41234567", FechaNacimiento = new DateTime(1980, 8, 20), Sexo = TipoSexo.Femenino, Email = "mflores@isterp.edu.pe", GradoAcademico = "Doctora", Especialidad = "Matemáticas", TipoContrato = TipoContrato.TiempoCompleto, FechaIngreso = new DateTime(2015, 1, 15), Sueldo = 4200 },
                new Docente { Codigo = "DOC003", Nombres = "Juan", ApellidoPaterno = "Mamani", ApellidoMaterno = "Ccopa", DNI = "42345678", FechaNacimiento = new DateTime(1985, 3, 12), Sexo = TipoSexo.Masculino, Email = "jmamani@isterp.edu.pe", GradoAcademico = "Magíster", Especialidad = "Base de Datos", TipoContrato = TipoContrato.PorHoras, FechaIngreso = new DateTime(2020, 8, 1), Sueldo = 1800 },
                new Docente { Codigo = "DOC004", Nombres = "Rosa", ApellidoPaterno = "Condori", ApellidoMaterno = "Apaza", DNI = "43456789", FechaNacimiento = new DateTime(1978, 11, 5), Sexo = TipoSexo.Femenino, Email = "rcondori@isterp.edu.pe", GradoAcademico = "Licenciada", Especialidad = "Contabilidad", TipoContrato = TipoContrato.MedioTiempo, FechaIngreso = new DateTime(2019, 3, 1), Sueldo = 2500 },
                new Docente { Codigo = "DOC005", Nombres = "Pedro", ApellidoPaterno = "Vargas", ApellidoMaterno = "Tito", DNI = "44567890", FechaNacimiento = new DateTime(1982, 7, 25), Sexo = TipoSexo.Masculino, Email = "pvargas@isterp.edu.pe", GradoAcademico = "Magíster", Especialidad = "Economía", TipoContrato = TipoContrato.TiempoCompleto, FechaIngreso = new DateTime(2017, 3, 1), Sueldo = 3200 }
            );
            await context.SaveChangesAsync();
        }

        // ── Estudiantes ──────────────────────────────────────────────────
        if (!await context.Estudiantes.AnyAsync())
        {
            var estudiantes = new List<Estudiante>
            {
                new() { Codigo = "EST2024001", Nombres = "Luis Alberto", ApellidoPaterno = "García", ApellidoMaterno = "López", DNI = "76123401", FechaNacimiento = new DateTime(2003, 4, 15), Sexo = TipoSexo.Masculino, Email = "lgarcia@est.isterp.edu.pe", Telefono = "987001001", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024002", Nombres = "Ana Lucía", ApellidoPaterno = "Mamani", ApellidoMaterno = "Quispe", DNI = "76123402", FechaNacimiento = new DateTime(2004, 7, 22), Sexo = TipoSexo.Femenino, Email = "amamani@est.isterp.edu.pe", Telefono = "987001002", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024003", Nombres = "Marco Antonio", ApellidoPaterno = "Flores", ApellidoMaterno = "Huanca", DNI = "76123403", FechaNacimiento = new DateTime(2002, 11, 8), Sexo = TipoSexo.Masculino, Email = "mflores@est.isterp.edu.pe", Telefono = "987001003", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024004", Nombres = "Carla Beatriz", ApellidoPaterno = "Ramos", ApellidoMaterno = "Ccopa", DNI = "76123404", FechaNacimiento = new DateTime(2003, 2, 14), Sexo = TipoSexo.Femenino, Email = "cramos@est.isterp.edu.pe", Telefono = "987001004", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024005", Nombres = "Jorge Luis", ApellidoPaterno = "Condori", ApellidoMaterno = "Apaza", DNI = "76123405", FechaNacimiento = new DateTime(2001, 9, 30), Sexo = TipoSexo.Masculino, Email = "jcondori@est.isterp.edu.pe", Telefono = "987001005", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024006", Nombres = "Sofía", ApellidoPaterno = "Vargas", ApellidoMaterno = "Tito", DNI = "76123406", FechaNacimiento = new DateTime(2004, 6, 18), Sexo = TipoSexo.Femenino, Email = "svargas@est.isterp.edu.pe", Telefono = "987001006", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024007", Nombres = "David Raúl", ApellidoPaterno = "Cusi", ApellidoMaterno = "Marca", DNI = "76123407", FechaNacimiento = new DateTime(2003, 1, 5), Sexo = TipoSexo.Masculino, Email = "dcusi@est.isterp.edu.pe", Telefono = "987001007", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024008", Nombres = "Patricia", ApellidoPaterno = "Huallpa", ApellidoMaterno = "Sinca", DNI = "76123408", FechaNacimiento = new DateTime(2002, 10, 27), Sexo = TipoSexo.Femenino, Email = "phuallpa@est.isterp.edu.pe", Telefono = "987001008", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024009", Nombres = "Kevin Alexander", ApellidoPaterno = "Ttito", ApellidoMaterno = "Quispe", DNI = "76123409", FechaNacimiento = new DateTime(2003, 8, 12), Sexo = TipoSexo.Masculino, Email = "kttito@est.isterp.edu.pe", Telefono = "987001009", Estado = EstadoEstudiante.Retirado },
                new() { Codigo = "EST2024010", Nombres = "Milagros", ApellidoPaterno = "Catunta", ApellidoMaterno = "Puma", DNI = "76123410", FechaNacimiento = new DateTime(2004, 3, 20), Sexo = TipoSexo.Femenino, Email = "mcatunta@est.isterp.edu.pe", Telefono = "987001010", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024011", Nombres = "Rodrigo", ApellidoPaterno = "Zamalloa", ApellidoMaterno = "Cruz", DNI = "76123411", FechaNacimiento = new DateTime(2002, 5, 3), Sexo = TipoSexo.Masculino, Email = "rzamalloa@est.isterp.edu.pe", Telefono = "987001011", Estado = EstadoEstudiante.Activo },
                new() { Codigo = "EST2024012", Nombres = "Valeria", ApellidoPaterno = "Palomino", ApellidoMaterno = "Góngora", DNI = "76123412", FechaNacimiento = new DateTime(2003, 12, 9), Sexo = TipoSexo.Femenino, Email = "vpalomino@est.isterp.edu.pe", Telefono = "987001012", Estado = EstadoEstudiante.Activo }
            };
            context.Estudiantes.AddRange(estudiantes);
            await context.SaveChangesAsync();
        }

        // ── Matrículas ───────────────────────────────────────────────────
        if (!await context.Matriculas.AnyAsync())
        {
            var periodo = await context.PeriodosAcademicos.FirstAsync(p => p.Codigo == "2025-I");
            var carreras = await context.Carreras.ToListAsync();
            var estudiantes = await context.Estudiantes.Take(10).ToListAsync();
            var carISW = carreras.First(c => c.Codigo == "ISW").Id;
            var carADM = carreras.First(c => c.Codigo == "ADM").Id;

            for (int i = 0; i < estudiantes.Count; i++)
            {
                context.Matriculas.Add(new Matricula
                {
                    NumeroMatricula = $"MAT-2025-{(i + 1):D4}",
                    EstudianteId = estudiantes[i].Id,
                    PeriodoAcademicoId = periodo.Id,
                    CarreraId = i % 2 == 0 ? carISW : carADM,
                    Ciclo = (i % 5) + 1,
                    TipoMatricula = i == 0 ? TipoMatricula.Nueva : TipoMatricula.Regular,
                    Estado = i == 8 ? EstadoMatricula.Anulada : EstadoMatricula.Confirmada,
                    FechaMatricula = DateTime.Now.AddDays(-(30 - i))
                });
            }
            await context.SaveChangesAsync();
        }

        // ── Secciones y Detalles (Matriculación de alumnos a cursos) ─────
        if (!await context.Secciones.AnyAsync())
        {
            var periodo = await context.PeriodosAcademicos.FirstAsync(p => p.Codigo == "2025-I");
            var cursos = await context.Cursos.Take(3).ToListAsync();
            var docentes = await context.Docentes.Take(3).ToListAsync();
            
            var secciones = new List<Seccion>
            {
                new Seccion { CursoId = cursos[0].Id, PeriodoAcademicoId = periodo.Id, Nombre = "A1", Capacidad = 30 },
                new Seccion { CursoId = cursos[1].Id, PeriodoAcademicoId = periodo.Id, Nombre = "B1", Capacidad = 30 },
                new Seccion { CursoId = cursos[2].Id, PeriodoAcademicoId = periodo.Id, Nombre = "A1", Capacidad = 30 }
            };
            context.Secciones.AddRange(secciones);
            await context.SaveChangesAsync();

            // Asignar alumnos matriculados a las secciones (simulación de carga académica)
            var matriculas = await context.Matriculas.Include(m => m.Estudiante).ToListAsync();
            foreach (var matricula in matriculas)
            {
                // Cada alumno se inscribe en las 3 secciones
                context.DetallesMatricula.AddRange(
                    new DetalleMatricula { MatriculaId = matricula.Id, SeccionId = secciones[0].Id },
                    new DetalleMatricula { MatriculaId = matricula.Id, SeccionId = secciones[1].Id },
                    new DetalleMatricula { MatriculaId = matricula.Id, SeccionId = secciones[2].Id }
                );
            }
            await context.SaveChangesAsync();
        }

        // ── Libros (Biblioteca) ──────────────────────────────────────────
        if (!await context.Libros.AnyAsync())
        {
            context.Libros.AddRange(
                new Libro { Titulo = "Ingeniería de Software: Un enfoque práctico", Autor = "Roger S. Pressman", Editorial = "McGraw-Hill", AnioPublicacion = 2010, ISBN = "978-607-15-0314-5", Categoria = "Tecnología", TotalEjemplares = 5, EjemplaresDisponibles = 5 },
                new Libro { Titulo = "Administración: Una perspectiva global", Autor = "Harold Koontz", Editorial = "McGraw-Hill", AnioPublicacion = 2012, ISBN = "978-607-15-0759-4", Categoria = "Negocios", TotalEjemplares = 3, EjemplaresDisponibles = 3 },
                new Libro { Titulo = "Fundamentos de Bases de Datos", Autor = "Abraham Silberschatz", Editorial = "McGraw-Hill", AnioPublicacion = 2014, ISBN = "978-84-481-9033-0", Categoria = "Tecnología", TotalEjemplares = 4, EjemplaresDisponibles = 4 }
            );
            await context.SaveChangesAsync();
        }

        // ── Deudas y Pagos ───────────────────────────────────────────────
        if (!await context.Deudas.AnyAsync())
        {
            var conceptos = await context.ConceptosPago.ToListAsync();
            var estudiantes = await context.Estudiantes.Take(8).ToListAsync();
            var periodo = await context.PeriodosAcademicos.FirstAsync(p => p.Codigo == "2025-I");

            var cMat  = conceptos.First(c => c.Codigo == "MAT").Id;
            var cPen1 = conceptos.First(c => c.Codigo == "PEN1").Id;
            var cPen2 = conceptos.First(c => c.Codigo == "PEN2").Id;
            var cPen3 = conceptos.First(c => c.Codigo == "PEN3").Id;

            var deudas = new List<Deuda>
            {
                new() { NumeroDeuda = "DEU-2025-00001", EstudianteId = estudiantes[0].Id, ConceptoPagoId = cMat,  PeriodoAcademicoId = periodo.Id, MontoOriginal = 250, FechaVencimiento = DateTime.Today.AddDays(-60), Estado = EstadoPago.Pagado },
                new() { NumeroDeuda = "DEU-2025-00002", EstudianteId = estudiantes[0].Id, ConceptoPagoId = cPen1, PeriodoAcademicoId = periodo.Id, MontoOriginal = 450, FechaVencimiento = DateTime.Today.AddDays(-30), Estado = EstadoPago.Pagado },
                new() { NumeroDeuda = "DEU-2025-00003", EstudianteId = estudiantes[0].Id, ConceptoPagoId = cPen2, PeriodoAcademicoId = periodo.Id, MontoOriginal = 450, FechaVencimiento = DateTime.Today.AddDays(15), Estado = EstadoPago.Pendiente },
                new() { NumeroDeuda = "DEU-2025-00004", EstudianteId = estudiantes[1].Id, ConceptoPagoId = cMat,  PeriodoAcademicoId = periodo.Id, MontoOriginal = 250, FechaVencimiento = DateTime.Today.AddDays(-55), Estado = EstadoPago.Pagado },
                new() { NumeroDeuda = "DEU-2025-00005", EstudianteId = estudiantes[1].Id, ConceptoPagoId = cPen1, PeriodoAcademicoId = periodo.Id, MontoOriginal = 450, MontoMora = 22.5m, FechaVencimiento = DateTime.Today.AddDays(-15), Estado = EstadoPago.Vencido },
                new() { NumeroDeuda = "DEU-2025-00006", EstudianteId = estudiantes[2].Id, ConceptoPagoId = cMat,  PeriodoAcademicoId = periodo.Id, MontoOriginal = 250, FechaVencimiento = DateTime.Today.AddDays(-50), Estado = EstadoPago.Pagado },
                new() { NumeroDeuda = "DEU-2025-00007", EstudianteId = estudiantes[2].Id, ConceptoPagoId = cPen1, PeriodoAcademicoId = periodo.Id, MontoOriginal = 450, FechaVencimiento = DateTime.Today.AddDays(-20), Estado = EstadoPago.Pendiente },
                new() { NumeroDeuda = "DEU-2025-00008", EstudianteId = estudiantes[3].Id, ConceptoPagoId = cPen2, PeriodoAcademicoId = periodo.Id, MontoOriginal = 450, MontoMora = 45m, FechaVencimiento = DateTime.Today.AddDays(-40), Estado = EstadoPago.Vencido },
                new() { NumeroDeuda = "DEU-2025-00009", EstudianteId = estudiantes[4].Id, ConceptoPagoId = cPen3, PeriodoAcademicoId = periodo.Id, MontoOriginal = 450, FechaVencimiento = DateTime.Today.AddDays(30), Estado = EstadoPago.Pendiente },
                new() { NumeroDeuda = "DEU-2025-00010", EstudianteId = estudiantes[5].Id, ConceptoPagoId = cPen1, PeriodoAcademicoId = periodo.Id, MontoOriginal = 450, FechaVencimiento = DateTime.Today.AddDays(10), Estado = EstadoPago.Pendiente },
                new() { NumeroDeuda = "DEU-2025-00011", EstudianteId = estudiantes[6].Id, ConceptoPagoId = cMat,  PeriodoAcademicoId = periodo.Id, MontoOriginal = 250, MontoMora = 25m, FechaVencimiento = DateTime.Today.AddDays(-45), Estado = EstadoPago.Vencido },
                new() { NumeroDeuda = "DEU-2025-00012", EstudianteId = estudiantes[7].Id, ConceptoPagoId = cPen2, PeriodoAcademicoId = periodo.Id, MontoOriginal = 450, FechaVencimiento = DateTime.Today.AddDays(5), Estado = EstadoPago.Pendiente }
            };
            context.Deudas.AddRange(deudas);
            await context.SaveChangesAsync();

            // Pagos para las deudas marcadas como Pagado
            var deudasPagadas = deudas.Where(d => d.Estado == EstadoPago.Pagado).ToList();
            int nPago = 1;
            foreach (var d in deudasPagadas)
            {
                context.Pagos.Add(new Pago
                {
                    NumeroPago = $"PAG-2025-{nPago++:D5}",
                    DeudaId = d.Id,
                    EstudianteId = d.EstudianteId,
                    MontoPagado = d.MontoOriginal,
                    FechaPago = d.FechaVencimiento.AddDays(-3),
                    TipoPago = nPago % 2 == 0 ? TipoPago.Transferencia : TipoPago.Efectivo,
                    NumeroOperacion = nPago % 2 == 0 ? $"TRF{nPago:D6}" : null,
                    Anulado = false
                });
            }
            await context.SaveChangesAsync();
        }
    }
}
