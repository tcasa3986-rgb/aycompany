using ERP.TallerAutomotriz.Domain.Entities.Customers;
using ERP.TallerAutomotriz.Domain.Entities.Inventory;
using ERP.TallerAutomotriz.Domain.Entities.Personnel;
using ERP.TallerAutomotriz.Domain.Entities.Purchases;
using ERP.TallerAutomotriz.Domain.Entities.System;
using ERP.TallerAutomotriz.Domain.Entities.Workshop;
using ERP.TallerAutomotriz.Domain.Enums;
using ERP.TallerAutomotriz.Infrastructure.Identity;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Logging;

namespace ERP.TallerAutomotriz.Infrastructure.Persistence.Seeders;

public static class DbInitializer
{
    public static async Task InitializeAsync(IServiceProvider services)
    {
        using var scope = services.CreateScope();
        var sp = scope.ServiceProvider;
        var logger = sp.GetRequiredService<ILoggerFactory>().CreateLogger("DbInitializer");

        try
        {
            var ctx = sp.GetRequiredService<ApplicationDbContext>();
            await ctx.Database.MigrateAsync();

            var userMgr = sp.GetRequiredService<UserManager<ApplicationUser>>();
            var roleMgr = sp.GetRequiredService<RoleManager<ApplicationRole>>();

            // Roles
            foreach (var rol in RolesSistema.Todos)
            {
                if (!await roleMgr.RoleExistsAsync(rol))
                {
                    await roleMgr.CreateAsync(new ApplicationRole { Name = rol, Descripcion = $"Rol del sistema: {rol}" });
                }
            }

            // Usuario administrador por defecto
            const string adminEmail = "admin@taller.com";
            var admin = await userMgr.FindByEmailAsync(adminEmail);
            if (admin == null)
            {
                admin = new ApplicationUser
                {
                    UserName = adminEmail,
                    Email = adminEmail,
                    Nombres = "Administrador",
                    Apellidos = "Sistema",
                    EmailConfirmed = true,
                    Activo = true
                };
                var result = await userMgr.CreateAsync(admin, "Admin123$");
                if (result.Succeeded)
                    await userMgr.AddToRoleAsync(admin, RolesSistema.Administrador);
            }

            // Empresa por defecto
            if (!await ctx.Empresas.AnyAsync())
            {
                ctx.Empresas.Add(new Empresa
                {
                    RazonSocial = "Taller Automotriz Demo S.A.C.",
                    NombreComercial = "AutoTaller",
                    DocumentoFiscal = "20123456789",
                    Direccion = "Av. Principal 123, Lima",
                    Telefono = "+51 999 888 777",
                    Email = "contacto@autotaller.com",
                    Moneda = "PEN",
                    SimboloMoneda = "S/.",
                    PorcentajeImpuesto = 18,
                    NombreImpuesto = "IGV",
                    Pais = "Perú"
                });
            }

            // Sucursal principal
            if (!await ctx.Sucursales.AnyAsync())
            {
                ctx.Sucursales.Add(new Sucursal
                {
                    Codigo = "SUC001",
                    Nombre = "Sucursal Principal",
                    Direccion = "Av. Principal 123, Lima",
                    EsPrincipal = true
                });
            }

            // Almacén principal
            if (!await ctx.Almacenes.AnyAsync())
            {
                ctx.Almacenes.Add(new Almacen
                {
                    Codigo = "ALM001",
                    Nombre = "Almacén Principal",
                    Direccion = "Av. Principal 123, Lima",
                    EsPrincipal = true
                });
            }

            // Caja principal
            if (!await ctx.Cajas.AnyAsync())
            {
                ctx.Cajas.Add(new Domain.Entities.Sales.Caja
                {
                    Codigo = "CJA001",
                    Nombre = "Caja Principal",
                    Abierta = false
                });
            }

            // Categorías de servicio
            if (!await ctx.CategoriasServicio.AnyAsync())
            {
                ctx.CategoriasServicio.AddRange(
                    new CategoriaServicio { Nombre = "Mantenimiento Preventivo" },
                    new CategoriaServicio { Nombre = "Mantenimiento Correctivo" },
                    new CategoriaServicio { Nombre = "Sistema Eléctrico" },
                    new CategoriaServicio { Nombre = "Hojalatería y Pintura" },
                    new CategoriaServicio { Nombre = "Aire Acondicionado" },
                    new CategoriaServicio { Nombre = "Alineación y Balanceo" }
                );
            }

            // Categorías de repuesto
            if (!await ctx.CategoriasRepuesto.AnyAsync())
            {
                ctx.CategoriasRepuesto.AddRange(
                    new CategoriaRepuesto { Nombre = "Filtros" },
                    new CategoriaRepuesto { Nombre = "Aceites y Lubricantes" },
                    new CategoriaRepuesto { Nombre = "Frenos" },
                    new CategoriaRepuesto { Nombre = "Suspensión" },
                    new CategoriaRepuesto { Nombre = "Eléctrico" },
                    new CategoriaRepuesto { Nombre = "Motor" },
                    new CategoriaRepuesto { Nombre = "Llantas y Neumáticos" }
                );
            }

            await ctx.SaveChangesAsync();

            // Servicios demo
            if (!await ctx.Servicios.AnyAsync())
            {
                var catPrev = await ctx.CategoriasServicio.FirstOrDefaultAsync(c => c.Nombre == "Mantenimiento Preventivo");
                var catCorr = await ctx.CategoriasServicio.FirstOrDefaultAsync(c => c.Nombre == "Mantenimiento Correctivo");

                ctx.Servicios.AddRange(
                    new Servicio { Codigo = "SRV-001", Nombre = "Cambio de aceite y filtro", Tipo = TipoServicio.MantenimientoPreventivo, PrecioEstandar = 120, TiempoEstimadoMinutos = 30, CostoManoObra = 50, CategoriaId = catPrev?.Id },
                    new Servicio { Codigo = "SRV-002", Nombre = "Service 10,000 km", Tipo = TipoServicio.MantenimientoPreventivo, PrecioEstandar = 450, TiempoEstimadoMinutos = 120, CostoManoObra = 200, EsPaquete = true, CategoriaId = catPrev?.Id },
                    new Servicio { Codigo = "SRV-003", Nombre = "Cambio de pastillas de freno", Tipo = TipoServicio.Correctivo, PrecioEstandar = 180, TiempoEstimadoMinutos = 60, CostoManoObra = 80, CategoriaId = catCorr?.Id },
                    new Servicio { Codigo = "SRV-004", Nombre = "Diagnóstico computarizado OBD", Tipo = TipoServicio.Diagnostico, PrecioEstandar = 80, TiempoEstimadoMinutos = 45, CostoManoObra = 80 },
                    new Servicio { Codigo = "SRV-005", Nombre = "Alineación 4 ruedas", Tipo = TipoServicio.AlineacionBalanceo, PrecioEstandar = 90, TiempoEstimadoMinutos = 45, CostoManoObra = 90 }
                );
            }

            // Repuestos demo
            if (!await ctx.Repuestos.AnyAsync())
            {
                var catFiltros = await ctx.CategoriasRepuesto.FirstOrDefaultAsync(c => c.Nombre == "Filtros");
                var catAceite = await ctx.CategoriasRepuesto.FirstOrDefaultAsync(c => c.Nombre == "Aceites y Lubricantes");
                var catFrenos = await ctx.CategoriasRepuesto.FirstOrDefaultAsync(c => c.Nombre == "Frenos");

                ctx.Repuestos.AddRange(
                    new Repuesto { CodigoInterno = "REP-0001", CodigoOEM = "OEM-001", Descripcion = "Filtro de aceite Toyota Corolla", UnidadMedida = "UND", StockActual = 25, StockMinimo = 10, StockMaximo = 50, PrecioVenta = 25, CostoPromedio = 15, CategoriaId = catFiltros?.Id, Ubicacion = "A-1-2" },
                    new Repuesto { CodigoInterno = "REP-0002", Descripcion = "Aceite 5W30 Sintético 4L", UnidadMedida = "GLN", StockActual = 18, StockMinimo = 8, StockMaximo = 40, PrecioVenta = 95, CostoPromedio = 65, CategoriaId = catAceite?.Id, Ubicacion = "B-1-1" },
                    new Repuesto { CodigoInterno = "REP-0003", Descripcion = "Pastillas de freno delanteras", UnidadMedida = "JGO", StockActual = 5, StockMinimo = 6, StockMaximo = 20, PrecioVenta = 130, CostoPromedio = 75, CategoriaId = catFrenos?.Id, Ubicacion = "C-2-1" },
                    new Repuesto { CodigoInterno = "REP-0004", Descripcion = "Filtro de aire universal", UnidadMedida = "UND", StockActual = 32, StockMinimo = 10, StockMaximo = 60, PrecioVenta = 40, CostoPromedio = 22, CategoriaId = catFiltros?.Id, Ubicacion = "A-1-3" },
                    new Repuesto { CodigoInterno = "REP-0005", Descripcion = "Bujías de iridio (4 pzs)", UnidadMedida = "JGO", StockActual = 12, StockMinimo = 5, StockMaximo = 25, PrecioVenta = 220, CostoPromedio = 140, Ubicacion = "D-1-2" }
                );
            }

            // Técnicos demo
            if (!await ctx.Tecnicos.AnyAsync())
            {
                ctx.Tecnicos.AddRange(
                    new Tecnico { Codigo = "TEC-001", Nombres = "Juan", Apellidos = "Pérez García", DocumentoIdentidad = "12345678", FechaIngreso = DateTime.UtcNow.AddYears(-3), Nivel = NivelExperiencia.Senior, TarifaHora = 35, PorcentajeComision = 5, Especialidades = "Motor, Transmisión" },
                    new Tecnico { Codigo = "TEC-002", Nombres = "Carlos", Apellidos = "Mendoza Silva", DocumentoIdentidad = "87654321", FechaIngreso = DateTime.UtcNow.AddYears(-2), Nivel = NivelExperiencia.SemiSenior, TarifaHora = 28, PorcentajeComision = 4, Especialidades = "Eléctrico, Diagnóstico OBD" },
                    new Tecnico { Codigo = "TEC-003", Nombres = "Luis", Apellidos = "Quispe Ramos", DocumentoIdentidad = "11223344", FechaIngreso = DateTime.UtcNow.AddYears(-1), Nivel = NivelExperiencia.Junior, TarifaHora = 22, PorcentajeComision = 3, Especialidades = "Frenos, Suspensión" }
                );
            }

            // Proveedores demo
            if (!await ctx.Proveedores.AnyAsync())
            {
                ctx.Proveedores.AddRange(
                    new Proveedor { Codigo = "PROV-001", RazonSocial = "Repuestos del Norte S.A.", DocumentoIdentidad = "20111222333", Telefono = "999111222", Email = "ventas@repnorte.com", DiasCredito = 30, DiasEntrega = 3, CalificacionPrecio = 4, CalificacionTiempo = 5, CalificacionCalidad = 4 },
                    new Proveedor { Codigo = "PROV-002", RazonSocial = "Lubricantes Premium E.I.R.L.", DocumentoIdentidad = "20444555666", Telefono = "999333444", Email = "contacto@lubpremium.com", DiasCredito = 15, DiasEntrega = 2, CalificacionPrecio = 5, CalificacionTiempo = 4, CalificacionCalidad = 5 }
                );
            }

            // Clientes demo
            if (!await ctx.Clientes.AnyAsync())
            {
                var c1 = new Cliente { Codigo = "CLI-001", Tipo = TipoCliente.PersonaNatural, NombreRazonSocial = "Pedro Ramírez Loayza", DocumentoIdentidad = "44556677", Email = "pedro@gmail.com", TelefonoPrincipal = "987654321", Direccion = "Calle Las Flores 456, Lima" };
                var c2 = new Cliente { Codigo = "CLI-002", Tipo = TipoCliente.Empresa, NombreRazonSocial = "Transportes Veloz S.A.C.", NombreComercial = "Veloz", DocumentoIdentidad = "20123456789", Email = "flota@veloz.com", TelefonoPrincipal = "(01) 4567890", Direccion = "Av. Industrial 1500, Callao", ContactoPrincipal = "Ana Torres", CargoContacto = "Jefe de Flota" };
                var c3 = new Cliente { Codigo = "CLI-003", Tipo = TipoCliente.PersonaNatural, NombreRazonSocial = "María Fernández Castro", DocumentoIdentidad = "55667788", Email = "maria.f@hotmail.com", TelefonoPrincipal = "987112233", Direccion = "Av. Universitaria 800, Lima" };

                ctx.Clientes.AddRange(c1, c2, c3);
                await ctx.SaveChangesAsync();

                // Vehículos demo
                ctx.Vehiculos.AddRange(
                    new Vehiculo { ClienteId = c1.Id, Placa = "ABC-123", Marca = "Toyota", Modelo = "Corolla", Anio = 2018, Color = "Blanco", KilometrajeActual = 78500, Combustible = TipoCombustible.Gasolina, Transmision = TipoTransmision.Automatica },
                    new Vehiculo { ClienteId = c2.Id, Placa = "XYZ-456", Marca = "Hyundai", Modelo = "H100", Anio = 2020, Color = "Blanco", KilometrajeActual = 125000, Combustible = TipoCombustible.Diesel, Transmision = TipoTransmision.Manual },
                    new Vehiculo { ClienteId = c2.Id, Placa = "XYZ-789", Marca = "Hyundai", Modelo = "H100", Anio = 2021, Color = "Azul", KilometrajeActual = 98000, Combustible = TipoCombustible.Diesel, Transmision = TipoTransmision.Manual },
                    new Vehiculo { ClienteId = c3.Id, Placa = "DEF-321", Marca = "Kia", Modelo = "Picanto", Anio = 2022, Color = "Rojo", KilometrajeActual = 25000, Combustible = TipoCombustible.Gasolina, Transmision = TipoTransmision.Automatica }
                );
            }

            await ctx.SaveChangesAsync();
            logger.LogInformation("Base de datos inicializada con datos de ejemplo.");
        }
        catch (Exception ex)
        {
            logger.LogError(ex, "Error al inicializar la base de datos.");
            throw;
        }
    }
}
