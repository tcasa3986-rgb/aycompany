using ERP.TallerAutomotriz.Domain.Entities.Customers;
using ERP.TallerAutomotriz.Domain.Entities.Inventory;
using ERP.TallerAutomotriz.Domain.Entities.Personnel;
using ERP.TallerAutomotriz.Domain.Entities.Purchases;
using ERP.TallerAutomotriz.Domain.Entities.Sales;
using ERP.TallerAutomotriz.Domain.Entities.Workshop;
using ERP.TallerAutomotriz.Domain.Enums;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Logging;

namespace ERP.TallerAutomotriz.Infrastructure.Persistence.Seeders;

public static class DashboardSeeder
{
    public static async Task SeedAsync(IServiceProvider services)
    {
        using var scope = services.CreateScope();
        var ctx = scope.ServiceProvider.GetRequiredService<ApplicationDbContext>();
        var logger = scope.ServiceProvider.GetRequiredService<ILoggerFactory>().CreateLogger("DashboardSeeder");

        try
        {
            logger.LogInformation("Iniciando la siembra de datos adicionales para el dashboard...");

            // 1. Clientes Adicionales (7 para llegar a 10)
            if (await ctx.Clientes.CountAsync() < 10)
            {
                var nuevosClientes = new List<Cliente>
                {
                    new Cliente { Codigo = "CLI-004", Tipo = TipoCliente.PersonaNatural, NombreRazonSocial = "Roberto Gómez Bolaños", DocumentoIdentidad = "11112222", Email = "roberto@vecindad.com", TelefonoPrincipal = "955555555", Direccion = "Calle El Chavo 8" },
                    new Cliente { Codigo = "CLI-005", Tipo = TipoCliente.PersonaNatural, NombreRazonSocial = "Julia Roberts Smith", DocumentoIdentidad = "33334444", Email = "julia@gmail.com", TelefonoPrincipal = "944444444", Direccion = "Av. Hollywood 123" },
                    new Cliente { Codigo = "CLI-006", Tipo = TipoCliente.Empresa, NombreRazonSocial = "Logística Nacional S.A.", DocumentoIdentidad = "20555666777", Email = "mantenimiento@logistica.com", TelefonoPrincipal = "015556666", Direccion = "Zona Industrial Lurin" },
                    new Cliente { Codigo = "CLI-007", Tipo = TipoCliente.PersonaNatural, NombreRazonSocial = "Lionel Messi Cuccittini", DocumentoIdentidad = "10101010", Email = "leo@intermiami.com", TelefonoPrincipal = "910101010", Direccion = "Rosario, Argentina" },
                    new Cliente { Codigo = "CLI-008", Tipo = TipoCliente.PersonaNatural, NombreRazonSocial = "Shakira Ripoll", DocumentoIdentidad = "20202020", Email = "shakira@waka.com", TelefonoPrincipal = "920202020", Direccion = "Barranquilla, Colombia" },
                    new Cliente { Codigo = "CLI-009", Tipo = TipoCliente.Empresa, NombreRazonSocial = "Constructora Civil E.I.R.L.", DocumentoIdentidad = "20999888777", Email = "compras@constructora.com", TelefonoPrincipal = "016667777", Direccion = "Av. Javier Prado 450" },
                    new Cliente { Codigo = "CLI-010", Tipo = TipoCliente.PersonaNatural, NombreRazonSocial = "Bruce Wayne", DocumentoIdentidad = "99999999", Email = "bruce@waynecorp.com", TelefonoPrincipal = "999999999", Direccion = "Mansión Wayne, Gotham" }
                };
                ctx.Clientes.AddRange(nuevosClientes);
                await ctx.SaveChangesAsync();
            }

            // 2. Vehículos Adicionales
            if (await ctx.Vehiculos.CountAsync() < 10)
            {
                var clientes = await ctx.Clientes.OrderByDescending(c => c.Id).Take(7).ToListAsync();
                var nuevosVehiculos = new List<Vehiculo>
                {
                    new Vehiculo { ClienteId = clientes[0].Id, Placa = "CH-008", Marca = "Volkswagen", Modelo = "Escarabajo", Anio = 1970, Color = "Crema", KilometrajeActual = 500000, Combustible = TipoCombustible.Gasolina, Transmision = TipoTransmision.Manual },
                    new Vehiculo { ClienteId = clientes[1].Id, Placa = "JR-123", Marca = "Tesla", Modelo = "Model S", Anio = 2023, Color = "Negro", KilometrajeActual = 5000, Combustible = TipoCombustible.Electrico, Transmision = TipoTransmision.Automatica },
                    new Vehiculo { ClienteId = clientes[2].Id, Placa = "LN-999", Marca = "Volvo", Modelo = "FH16", Anio = 2021, Color = "Blanco", KilometrajeActual = 250000, Combustible = TipoCombustible.Diesel, Transmision = TipoTransmision.Manual },
                    new Vehiculo { ClienteId = clientes[3].Id, Placa = "GOAT-10", Marca = "Ferrari", Modelo = "488 Pista", Anio = 2022, Color = "Rojo", KilometrajeActual = 1200, Combustible = TipoCombustible.Gasolina, Transmision = TipoTransmision.Automatica },
                    new Vehiculo { ClienteId = clientes[4].Id, Placa = "WAKA-20", Marca = "Lamborghini", Modelo = "Urus", Anio = 2021, Color = "Amarillo", KilometrajeActual = 15000, Combustible = TipoCombustible.Gasolina, Transmision = TipoTransmision.Automatica },
                    new Vehiculo { ClienteId = clientes[5].Id, Placa = "CC-555", Marca = "Caterpillar", Modelo = "797F", Anio = 2019, Color = "Amarillo", KilometrajeActual = 85000, Combustible = TipoCombustible.Diesel, Transmision = TipoTransmision.Manual },
                    new Vehiculo { ClienteId = clientes[6].Id, Placa = "BAT-001", Marca = "WayneCorp", Modelo = "Tumbler", Anio = 2024, Color = "Negro Mate", KilometrajeActual = 100, Combustible = TipoCombustible.Gasolina, Transmision = TipoTransmision.Automatica }
                };
                ctx.Vehiculos.AddRange(nuevosVehiculos);
                await ctx.SaveChangesAsync();
            }

            // 3. Servicios Adicionales
            if (await ctx.Servicios.CountAsync() < 10)
            {
                var catElec = await ctx.CategoriasServicio.FirstOrDefaultAsync(c => c.Nombre == "Sistema Eléctrico");
                var catHoj = await ctx.CategoriasServicio.FirstOrDefaultAsync(c => c.Nombre == "Hojalatería y Pintura");
                
                ctx.Servicios.AddRange(
                    new Servicio { Codigo = "SRV-006", Nombre = "Cambio de Batería 13 placas", Tipo = TipoServicio.Electrico, PrecioEstandar = 350, TiempoEstimadoMinutos = 20, CostoManoObra = 30, CategoriaId = catElec?.Id },
                    new Servicio { Codigo = "SRV-007", Nombre = "Pintura de paño (Puerta/Tapabarro)", Tipo = TipoServicio.Hojalateria, PrecioEstandar = 250, TiempoEstimadoMinutos = 1440, CostoManoObra = 150, CategoriaId = catHoj?.Id },
                    new Servicio { Codigo = "SRV-008", Nombre = "Recarga de Aire Acondicionado", Tipo = TipoServicio.Otro, PrecioEstandar = 120, TiempoEstimadoMinutos = 60, CostoManoObra = 60 },
                    new Servicio { Codigo = "SRV-009", Nombre = "Limpieza de Inyectores", Tipo = TipoServicio.MantenimientoPreventivo, PrecioEstandar = 180, TiempoEstimadoMinutos = 90, CostoManoObra = 100 },
                    new Servicio { Codigo = "SRV-010", Nombre = "Lavado de Salón Premium", Tipo = TipoServicio.Otro, PrecioEstandar = 300, TiempoEstimadoMinutos = 360, CostoManoObra = 150 }
                );
                await ctx.SaveChangesAsync();
            }

            // 4. Repuestos Adicionales
            if (await ctx.Repuestos.CountAsync() < 10)
            {
                var catElec = await ctx.CategoriasRepuesto.FirstOrDefaultAsync(c => c.Nombre == "Eléctrico");
                var catSusp = await ctx.CategoriasRepuesto.FirstOrDefaultAsync(c => c.Nombre == "Suspensión");

                ctx.Repuestos.AddRange(
                    new Repuesto { CodigoInterno = "REP-0006", Descripcion = "Batería Bosch S4 12V", UnidadMedida = "UND", StockActual = 10, StockMinimo = 2, StockMaximo = 15, PrecioVenta = 380, CostoPromedio = 280, CategoriaId = catElec?.Id },
                    new Repuesto { CodigoInterno = "REP-0007", Descripcion = "Amortiguador Delantero Monroe", UnidadMedida = "UND", StockActual = 8, StockMinimo = 4, StockMaximo = 20, PrecioVenta = 220, CostoPromedio = 145, CategoriaId = catSusp?.Id },
                    new Repuesto { CodigoInterno = "REP-0008", Descripcion = "Kit de Embrague LUK", UnidadMedida = "JGO", StockActual = 3, StockMinimo = 2, StockMaximo = 10, PrecioVenta = 850, CostoPromedio = 620 },
                    new Repuesto { CodigoInterno = "REP-0009", Descripcion = "Faro Delantero LED Toyota", UnidadMedida = "UND", StockActual = 4, StockMinimo = 1, StockMaximo = 6, PrecioVenta = 1200, CostoPromedio = 850 },
                    new Repuesto { CodigoInterno = "REP-0010", Descripcion = "Radiador de Aluminio Universal", UnidadMedida = "UND", StockActual = 6, StockMinimo = 2, StockMaximo = 10, PrecioVenta = 450, CostoPromedio = 310 }
                );
                await ctx.SaveChangesAsync();
            }

            // 5. Técnicos Adicionales
            if (await ctx.Tecnicos.CountAsync() < 10)
            {
                ctx.Tecnicos.AddRange(
                    new Tecnico { Codigo = "TEC-004", Nombres = "Mario", Apellidos = "Bros", DocumentoIdentidad = "44445555", Nivel = NivelExperiencia.Senior, TarifaHora = 40, PorcentajeComision = 6, Especialidades = "Tuberías y Motores" },
                    new Tecnico { Codigo = "TEC-005", Nombres = "Luigi", Apellidos = "Bros", DocumentoIdentidad = "55556666", Nivel = NivelExperiencia.SemiSenior, TarifaHora = 32, PorcentajeComision = 5, Especialidades = "Electricidad" },
                    new Tecnico { Codigo = "TEC-006", Nombres = "Peter", Apellidos = "Parker", DocumentoIdentidad = "77778888", Nivel = NivelExperiencia.Junior, TarifaHora = 20, PorcentajeComision = 3, Especialidades = "Suspensión Arácnida" },
                    new Tecnico { Codigo = "TEC-007", Nombres = "Tony", Apellidos = "Stark", DocumentoIdentidad = "88889999", Nivel = NivelExperiencia.Senior, TarifaHora = 100, PorcentajeComision = 10, Especialidades = "Alta Tecnología y Robótica" },
                    new Tecnico { Codigo = "TEC-008", Nombres = "Wanda", Apellidos = "Maximoff", DocumentoIdentidad = "12121212", Nivel = NivelExperiencia.SemiSenior, TarifaHora = 35, PorcentajeComision = 5, Especialidades = "Diagnóstico Místico" },
                    new Tecnico { Codigo = "TEC-009", Nombres = "Steve", Apellidos = "Rogers", DocumentoIdentidad = "19401940", Nivel = NivelExperiencia.Senior, TarifaHora = 30, PorcentajeComision = 4, Especialidades = "Blindaje y Carrocería" },
                    new Tecnico { Codigo = "TEC-010", Nombres = "Natasha", Apellidos = "Romanoff", DocumentoIdentidad = "00700700", Nivel = NivelExperiencia.Senior, TarifaHora = 45, PorcentajeComision = 7, Especialidades = "Infiltración y Detalle" }
                );
                await ctx.SaveChangesAsync();
            }

            // 6. Proveedores Adicionales
            if (await ctx.Proveedores.CountAsync() < 10)
            {
                ctx.Proveedores.AddRange(
                    new Proveedor { Codigo = "PROV-003", RazonSocial = "Baterías al Toque S.A.", DocumentoIdentidad = "20999000111", Telefono = "911911911", Email = "ventas@bateriastoque.com" },
                    new Proveedor { Codigo = "PROV-004", RazonSocial = "Pinturas del Sur S.R.L.", DocumentoIdentidad = "20888000222", Telefono = "922922922", Email = "ventas@pinturassur.com" },
                    new Proveedor { Codigo = "PROV-005", RazonSocial = "Llantas América S.A.C.", DocumentoIdentidad = "20777000333", Telefono = "933933933", Email = "ventas@llantamerica.com" },
                    new Proveedor { Codigo = "PROV-006", RazonSocial = "Frenos Seguro E.I.R.L.", DocumentoIdentidad = "20666000444", Telefono = "944944944", Email = "ventas@frenoseguro.com" },
                    new Proveedor { Codigo = "PROV-007", RazonSocial = "Radiadores Martínez", DocumentoIdentidad = "20555000555", Telefono = "955955955", Email = "ventas@radiadoresmartinez.com" },
                    new Proveedor { Codigo = "PROV-008", RazonSocial = "Embragues del Perú", DocumentoIdentidad = "20444000666", Telefono = "966966966", Email = "ventas@embraguesperu.com" },
                    new Proveedor { Codigo = "PROV-009", RazonSocial = "Herramientas Pro S.A.", DocumentoIdentidad = "20333000777", Telefono = "977977977", Email = "ventas@herramientaspro.com" },
                    new Proveedor { Codigo = "PROV-010", RazonSocial = "Importaciones Globales", DocumentoIdentidad = "20222000888", Telefono = "988988988", Email = "ventas@importglobal.com" }
                );
                await ctx.SaveChangesAsync();
            }

            // 7. Ordenes de Trabajo (10)
            if (!await ctx.OrdenesTrabajo.AnyAsync())
            {
                var clientes = await ctx.Clientes.Take(10).ToListAsync();
                var vehiculos = await ctx.Vehiculos.Take(10).ToListAsync();
                var tecnicos = await ctx.Tecnicos.Take(10).ToListAsync();
                var servicios = await ctx.Servicios.Take(5).ToListAsync();

                for (int i = 1; i <= 10; i++)
                {
                    var ot = new OrdenTrabajo
                    {
                        Numero = $"OT-{i:D4}",
                        FechaIngreso = DateTime.UtcNow.AddDays(-i),
                        ClienteId = clientes[i-1].Id,
                        VehiculoId = vehiculos[i-1].Id,
                        KilometrajeIngreso = 10000 * i,
                        FallasReportadasCliente = "Falla de ejemplo " + i,
                        Estado = (EstadoOT)(i % 7 + 1),
                        Prioridad = (PrioridadOT)(i % 4 + 1),
                        TecnicoPrincipalId = tecnicos[i % tecnicos.Count].Id,
                        SubtotalManoObra = 100 * i,
                        SubtotalRepuestos = 50 * i,
                        Impuesto = 27 * i,
                        Total = 177 * i,
                        PresupuestoAprobado = true,
                        FechaAprobacionPresupuesto = DateTime.UtcNow.AddDays(-i).AddHours(1)
                    };
                    ctx.OrdenesTrabajo.Add(ot);
                }
                await ctx.SaveChangesAsync();
            }

            // 8. Citas (10)
            if (!await ctx.Citas.AnyAsync())
            {
                var clientes = await ctx.Clientes.Take(10).ToListAsync();
                var vehiculos = await ctx.Vehiculos.Take(10).ToListAsync();

                for (int i = 1; i <= 10; i++)
                {
                    ctx.Citas.Add(new Cita
                    {
                        ClienteId = clientes[i-1].Id,
                        VehiculoId = vehiculos[i-1].Id,
                        FechaHora = DateTime.UtcNow.AddDays(i-5).AddHours(9), // Algunas pasadas, algunas futuras
                        DuracionMinutos = 60,
                        Estado = i < 5 ? EstadoCita.Atendida : EstadoCita.Pendiente,
                        Comentarios = "Cita de prueba " + i
                    });
                }
                await ctx.SaveChangesAsync();
            }

            // 9. Facturas (10)
            if (!await ctx.Facturas.AnyAsync())
            {
                var clientes = await ctx.Clientes.Take(10).ToListAsync();
                var ots = await ctx.OrdenesTrabajo.Take(10).ToListAsync();

                for (int i = 1; i <= 10; i++)
                {
                    var total = 200 * i;
                    var saldo = i % 2 == 0 ? 0 : total; // Algunas pagadas, algunas pendientes
                    ctx.Facturas.Add(new Factura
                    {
                        Tipo = TipoComprobante.Factura,
                        Serie = "F001",
                        Numero = $"{i:D8}",
                        Fecha = DateTime.UtcNow.AddDays(-i),
                        FechaVencimiento = DateTime.UtcNow.AddDays(-i + 30),
                        ClienteId = clientes[i-1].Id,
                        OrdenTrabajoId = ots[i-1].Id,
                        Subtotal = total / 1.18m,
                        Impuesto = total - (total / 1.18m),
                        Total = total,
                        MontoPagado = total - saldo,
                        SaldoPendiente = saldo,
                        Estado = saldo == 0 ? EstadoFactura.Pagada : EstadoFactura.Emitida
                    });
                }
                await ctx.SaveChangesAsync();
            }

            // 10. Ordenes de Compra (10)
            if (!await ctx.OrdenesCompra.AnyAsync())
            {
                var proveedores = await ctx.Proveedores.Take(10).ToListAsync();
                var almacen = await ctx.Almacenes.FirstOrDefaultAsync();

                for (int i = 1; i <= 10; i++)
                {
                    ctx.OrdenesCompra.Add(new OrdenCompra
                    {
                        Numero = $"OC-{i:D4}",
                        Fecha = DateTime.UtcNow.AddDays(-i),
                        ProveedorId = proveedores[i-1].Id,
                        AlmacenDestinoId = almacen?.Id,
                        Estado = i % 3 == 0 ? EstadoOrdenCompra.Recibida : EstadoOrdenCompra.Enviada,
                        Subtotal = 500 * i,
                        Impuesto = 90 * i,
                        Total = 590 * i
                    });
                }
                await ctx.SaveChangesAsync();
            }

            logger.LogInformation("¡Siembra de datos para el dashboard completada exitosamente!");
        }
        catch (Exception ex)
        {
            logger.LogError(ex, "Error durante la siembra de datos del dashboard.");
            throw;
        }
    }
}
