using ERP.TallerAutomotriz.Domain.Entities.Customers;
using ERP.TallerAutomotriz.Domain.Entities.Inventory;
using ERP.TallerAutomotriz.Domain.Entities.Personnel;
using ERP.TallerAutomotriz.Domain.Entities.Purchases;
using ERP.TallerAutomotriz.Domain.Entities.Sales;
using ERP.TallerAutomotriz.Domain.Entities.System;
using ERP.TallerAutomotriz.Domain.Entities.Workshop;
using ERP.TallerAutomotriz.Infrastructure.Identity;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;

namespace ERP.TallerAutomotriz.Infrastructure.Persistence;

public class ApplicationDbContext : IdentityDbContext<ApplicationUser, ApplicationRole, string>
{
    public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options) : base(options) { }

    // CRM
    public DbSet<Cliente> Clientes => Set<Cliente>();
    public DbSet<Vehiculo> Vehiculos => Set<Vehiculo>();

    // Workshop
    public DbSet<OrdenTrabajo> OrdenesTrabajo => Set<OrdenTrabajo>();
    public DbSet<Servicio> Servicios => Set<Servicio>();
    public DbSet<CategoriaServicio> CategoriasServicio => Set<CategoriaServicio>();
    public DbSet<DetalleOTServicio> DetalleOTServicios => Set<DetalleOTServicio>();
    public DbSet<DetalleOTRepuesto> DetalleOTRepuestos => Set<DetalleOTRepuesto>();
    public DbSet<HistorialEstadoOT> HistorialEstadosOT => Set<HistorialEstadoOT>();
    public DbSet<FotoOT> FotosOT => Set<FotoOT>();
    public DbSet<ChecklistInspeccion> ChecklistsInspeccion => Set<ChecklistInspeccion>();
    public DbSet<TecnicoOT> TecnicosOT => Set<TecnicoOT>();
    public DbSet<ControlCalidad> ControlesCalidad => Set<ControlCalidad>();
    public DbSet<Cita> Citas => Set<Cita>();

    // Inventory
    public DbSet<Repuesto> Repuestos => Set<Repuesto>();
    public DbSet<CategoriaRepuesto> CategoriasRepuesto => Set<CategoriaRepuesto>();
    public DbSet<CompatibilidadRepuesto> CompatibilidadesRepuesto => Set<CompatibilidadRepuesto>();
    public DbSet<Almacen> Almacenes => Set<Almacen>();
    public DbSet<StockAlmacen> StockAlmacenes => Set<StockAlmacen>();
    public DbSet<MovimientoInventario> MovimientosInventario => Set<MovimientoInventario>();

    // Sales
    public DbSet<Factura> Facturas => Set<Factura>();
    public DbSet<DetalleFactura> DetallesFactura => Set<DetalleFactura>();
    public DbSet<Pago> Pagos => Set<Pago>();
    public DbSet<Caja> Cajas => Set<Caja>();
    public DbSet<Cotizacion> Cotizaciones => Set<Cotizacion>();
    public DbSet<DetalleCotizacion> DetallesCotizacion => Set<DetalleCotizacion>();

    // Purchases
    public DbSet<Proveedor> Proveedores => Set<Proveedor>();
    public DbSet<OrdenCompra> OrdenesCompra => Set<OrdenCompra>();
    public DbSet<DetalleOrdenCompra> DetallesOrdenCompra => Set<DetalleOrdenCompra>();
    public DbSet<CuentaPagar> CuentasPagar => Set<CuentaPagar>();

    // Personnel
    public DbSet<Tecnico> Tecnicos => Set<Tecnico>();
    public DbSet<RegistroAsistencia> RegistrosAsistencia => Set<RegistroAsistencia>();
    public DbSet<Comision> Comisiones => Set<Comision>();

    // System
    public DbSet<Empresa> Empresas => Set<Empresa>();
    public DbSet<Sucursal> Sucursales => Set<Sucursal>();
    public DbSet<ParametroSistema> Parametros => Set<ParametroSistema>();
    public DbSet<LogAuditoria> LogsAuditoria => Set<LogAuditoria>();
    public DbSet<PlantillaNotificacion> PlantillasNotificacion => Set<PlantillaNotificacion>();
    public DbSet<NotificacionEnviada> NotificacionesEnviadas => Set<NotificacionEnviada>();

    protected override void OnModelCreating(ModelBuilder builder)
    {
        base.OnModelCreating(builder);

        // Esquemas por módulo
        builder.HasDefaultSchema("dbo");

        // Identity tables -> esquema seguridad
        builder.Entity<ApplicationUser>().ToTable("Usuarios", "seguridad");
        builder.Entity<ApplicationRole>().ToTable("Roles", "seguridad");
        builder.Entity<Microsoft.AspNetCore.Identity.IdentityUserRole<string>>().ToTable("UsuariosRoles", "seguridad");
        builder.Entity<Microsoft.AspNetCore.Identity.IdentityUserClaim<string>>().ToTable("UsuariosClaims", "seguridad");
        builder.Entity<Microsoft.AspNetCore.Identity.IdentityUserLogin<string>>().ToTable("UsuariosLogins", "seguridad");
        builder.Entity<Microsoft.AspNetCore.Identity.IdentityRoleClaim<string>>().ToTable("RolesClaims", "seguridad");
        builder.Entity<Microsoft.AspNetCore.Identity.IdentityUserToken<string>>().ToTable("UsuariosTokens", "seguridad");

        // CRM
        builder.Entity<Cliente>(b =>
        {
            b.ToTable("Clientes", "crm");
            b.HasIndex(x => x.Codigo).IsUnique();
            b.HasIndex(x => x.DocumentoIdentidad);
            b.Property(x => x.Codigo).HasMaxLength(20).IsRequired();
            b.Property(x => x.NombreRazonSocial).HasMaxLength(200).IsRequired();
            b.Property(x => x.DocumentoIdentidad).HasMaxLength(20).IsRequired();
            b.Property(x => x.Email).HasMaxLength(150);
            b.Property(x => x.SaldoPendiente).HasPrecision(18, 2);
            b.Property(x => x.LimiteCredito).HasPrecision(18, 2);
        });

        builder.Entity<Vehiculo>(b =>
        {
            b.ToTable("Vehiculos", "crm");
            b.HasIndex(x => x.Placa).IsUnique();
            b.HasIndex(x => x.VIN);
            b.Property(x => x.Placa).HasMaxLength(20).IsRequired();
            b.Property(x => x.Marca).HasMaxLength(50).IsRequired();
            b.Property(x => x.Modelo).HasMaxLength(50).IsRequired();
            b.HasOne(x => x.Cliente).WithMany(c => c.Vehiculos)
                .HasForeignKey(x => x.ClienteId).OnDelete(DeleteBehavior.Restrict);
        });

        // Workshop
        builder.Entity<OrdenTrabajo>(b =>
        {
            b.ToTable("OrdenesTrabajo", "taller");
            b.HasIndex(x => x.Numero).IsUnique();
            b.Property(x => x.Numero).HasMaxLength(20).IsRequired();
            b.Property(x => x.SubtotalManoObra).HasPrecision(18, 2);
            b.Property(x => x.SubtotalRepuestos).HasPrecision(18, 2);
            b.Property(x => x.SubtotalServiciosExternos).HasPrecision(18, 2);
            b.Property(x => x.Descuento).HasPrecision(18, 2);
            b.Property(x => x.Impuesto).HasPrecision(18, 2);
            b.Property(x => x.Total).HasPrecision(18, 2);
            b.HasOne(x => x.Cliente).WithMany().HasForeignKey(x => x.ClienteId).OnDelete(DeleteBehavior.Restrict);
            b.HasOne(x => x.Vehiculo).WithMany().HasForeignKey(x => x.VehiculoId).OnDelete(DeleteBehavior.Restrict);
            b.HasOne(x => x.TecnicoPrincipal).WithMany().HasForeignKey(x => x.TecnicoPrincipalId).OnDelete(DeleteBehavior.SetNull);
        });

        builder.Entity<Servicio>(b =>
        {
            b.ToTable("Servicios", "taller");
            b.HasIndex(x => x.Codigo).IsUnique();
            b.Property(x => x.Codigo).HasMaxLength(20).IsRequired();
            b.Property(x => x.Nombre).HasMaxLength(200).IsRequired();
            b.Property(x => x.PrecioEstandar).HasPrecision(18, 2);
            b.Property(x => x.CostoManoObra).HasPrecision(18, 2);
        });

        builder.Entity<CategoriaServicio>(b =>
        {
            b.ToTable("CategoriasServicio", "taller");
            b.Property(x => x.Nombre).HasMaxLength(100).IsRequired();
            b.HasOne(x => x.CategoriaPadre).WithMany().HasForeignKey(x => x.CategoriaPadreId).OnDelete(DeleteBehavior.Restrict);
        });

        builder.Entity<DetalleOTServicio>(b =>
        {
            b.ToTable("DetalleOTServicios", "taller");
            b.Property(x => x.PrecioUnitario).HasPrecision(18, 2);
            b.Property(x => x.Descuento).HasPrecision(18, 2);
            b.Property(x => x.Subtotal).HasPrecision(18, 2);
            b.HasOne(x => x.OrdenTrabajo).WithMany(o => o.Servicios).HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.Cascade);
            b.HasOne(x => x.Servicio).WithMany().HasForeignKey(x => x.ServicioId).OnDelete(DeleteBehavior.Restrict);
        });

        builder.Entity<DetalleOTRepuesto>(b =>
        {
            b.ToTable("DetalleOTRepuestos", "taller");
            b.Property(x => x.Cantidad).HasPrecision(18, 4);
            b.Property(x => x.PrecioUnitario).HasPrecision(18, 2);
            b.Property(x => x.CostoUnitario).HasPrecision(18, 2);
            b.Property(x => x.Descuento).HasPrecision(18, 2);
            b.Property(x => x.Subtotal).HasPrecision(18, 2);
            b.HasOne(x => x.OrdenTrabajo).WithMany(o => o.Repuestos).HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.Cascade);
            b.HasOne(x => x.Repuesto).WithMany().HasForeignKey(x => x.RepuestoId).OnDelete(DeleteBehavior.Restrict);
        });

        builder.Entity<HistorialEstadoOT>(b =>
        {
            b.ToTable("HistorialEstadoOT", "taller");
            b.HasOne(x => x.OrdenTrabajo).WithMany(o => o.Historial).HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<FotoOT>(b =>
        {
            b.ToTable("FotosOT", "taller");
            b.HasOne(x => x.OrdenTrabajo).WithMany(o => o.Fotos).HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<ChecklistInspeccion>(b =>
        {
            b.ToTable("ChecklistInspeccion", "taller");
            b.HasOne(x => x.OrdenTrabajo).WithMany(o => o.Checklist).HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<TecnicoOT>(b =>
        {
            b.ToTable("TecnicosOT", "taller");
            b.HasOne(x => x.OrdenTrabajo).WithMany(o => o.TecnicosAsignados).HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.Cascade);
            b.HasOne(x => x.Tecnico).WithMany().HasForeignKey(x => x.TecnicoId).OnDelete(DeleteBehavior.Restrict);
        });

        builder.Entity<ControlCalidad>(b =>
        {
            b.ToTable("ControlesCalidad", "taller");
            b.HasOne(x => x.OrdenTrabajo).WithMany().HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<Cita>(b =>
        {
            b.ToTable("Citas", "taller");
            b.HasOne(x => x.Cliente).WithMany().HasForeignKey(x => x.ClienteId).OnDelete(DeleteBehavior.Restrict);
            b.HasOne(x => x.Vehiculo).WithMany().HasForeignKey(x => x.VehiculoId).OnDelete(DeleteBehavior.SetNull);
            b.HasOne(x => x.Servicio).WithMany().HasForeignKey(x => x.ServicioId).OnDelete(DeleteBehavior.SetNull);
            b.HasOne(x => x.TecnicoPreferido).WithMany().HasForeignKey(x => x.TecnicoPreferidoId).OnDelete(DeleteBehavior.SetNull);
            b.HasOne(x => x.OrdenTrabajo).WithMany().HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.SetNull);
        });

        // Inventory
        builder.Entity<Repuesto>(b =>
        {
            b.ToTable("Repuestos", "inventario");
            b.HasIndex(x => x.CodigoInterno).IsUnique();
            b.HasIndex(x => x.CodigoOEM);
            b.HasIndex(x => x.CodigoBarras);
            b.Property(x => x.CodigoInterno).HasMaxLength(30).IsRequired();
            b.Property(x => x.Descripcion).HasMaxLength(200).IsRequired();
            b.Property(x => x.StockActual).HasPrecision(18, 4);
            b.Property(x => x.StockMinimo).HasPrecision(18, 4);
            b.Property(x => x.StockMaximo).HasPrecision(18, 4);
            b.Property(x => x.PrecioVenta).HasPrecision(18, 2);
            b.Property(x => x.CostoPromedio).HasPrecision(18, 4);
            b.Property(x => x.CostoUltimo).HasPrecision(18, 4);
            b.HasOne(x => x.Categoria).WithMany().HasForeignKey(x => x.CategoriaId).OnDelete(DeleteBehavior.SetNull);
        });

        builder.Entity<CategoriaRepuesto>(b =>
        {
            b.ToTable("CategoriasRepuesto", "inventario");
            b.Property(x => x.Nombre).HasMaxLength(100).IsRequired();
            b.HasOne(x => x.CategoriaPadre).WithMany().HasForeignKey(x => x.CategoriaPadreId).OnDelete(DeleteBehavior.Restrict);
        });

        builder.Entity<CompatibilidadRepuesto>(b =>
        {
            b.ToTable("CompatibilidadesRepuesto", "inventario");
            b.HasOne(x => x.Repuesto).WithMany(r => r.Compatibilidades).HasForeignKey(x => x.RepuestoId).OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<Almacen>(b =>
        {
            b.ToTable("Almacenes", "inventario");
            b.HasIndex(x => x.Codigo).IsUnique();
            b.Property(x => x.Codigo).HasMaxLength(20).IsRequired();
            b.Property(x => x.Nombre).HasMaxLength(100).IsRequired();
        });

        builder.Entity<StockAlmacen>(b =>
        {
            b.ToTable("StockAlmacenes", "inventario");
            b.HasIndex(x => new { x.RepuestoId, x.AlmacenId }).IsUnique();
            b.Property(x => x.Cantidad).HasPrecision(18, 4);
            b.HasOne(x => x.Repuesto).WithMany().HasForeignKey(x => x.RepuestoId).OnDelete(DeleteBehavior.Cascade);
            b.HasOne(x => x.Almacen).WithMany().HasForeignKey(x => x.AlmacenId).OnDelete(DeleteBehavior.Restrict);
        });

        builder.Entity<MovimientoInventario>(b =>
        {
            b.ToTable("MovimientosInventario", "inventario");
            b.Property(x => x.Cantidad).HasPrecision(18, 4);
            b.Property(x => x.CostoUnitario).HasPrecision(18, 4);
            b.Property(x => x.SaldoAnterior).HasPrecision(18, 4);
            b.Property(x => x.SaldoNuevo).HasPrecision(18, 4);
            b.HasOne(x => x.Repuesto).WithMany(r => r.Movimientos).HasForeignKey(x => x.RepuestoId).OnDelete(DeleteBehavior.Restrict);
            b.HasOne(x => x.Almacen).WithMany().HasForeignKey(x => x.AlmacenId).OnDelete(DeleteBehavior.Restrict);
        });

        // Sales
        builder.Entity<Factura>(b =>
        {
            b.ToTable("Facturas", "ventas");
            b.HasIndex(x => new { x.Serie, x.Numero }).IsUnique();
            b.Property(x => x.Serie).HasMaxLength(10).IsRequired();
            b.Property(x => x.Numero).HasMaxLength(20).IsRequired();
            b.Property(x => x.Subtotal).HasPrecision(18, 2);
            b.Property(x => x.Descuento).HasPrecision(18, 2);
            b.Property(x => x.BaseImponible).HasPrecision(18, 2);
            b.Property(x => x.PorcentajeImpuesto).HasPrecision(5, 2);
            b.Property(x => x.Impuesto).HasPrecision(18, 2);
            b.Property(x => x.Total).HasPrecision(18, 2);
            b.Property(x => x.MontoPagado).HasPrecision(18, 2);
            b.Property(x => x.SaldoPendiente).HasPrecision(18, 2);
            b.HasOne(x => x.Cliente).WithMany().HasForeignKey(x => x.ClienteId).OnDelete(DeleteBehavior.Restrict);
            b.HasOne(x => x.OrdenTrabajo).WithMany().HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.SetNull);
        });

        builder.Entity<DetalleFactura>(b =>
        {
            b.ToTable("DetallesFactura", "ventas");
            b.Property(x => x.Cantidad).HasPrecision(18, 4);
            b.Property(x => x.PrecioUnitario).HasPrecision(18, 2);
            b.Property(x => x.Descuento).HasPrecision(18, 2);
            b.Property(x => x.Subtotal).HasPrecision(18, 2);
            b.HasOne(x => x.Factura).WithMany(f => f.Detalles).HasForeignKey(x => x.FacturaId).OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<Pago>(b =>
        {
            b.ToTable("Pagos", "ventas");
            b.Property(x => x.Monto).HasPrecision(18, 2);
            b.HasOne(x => x.Factura).WithMany(f => f.Pagos).HasForeignKey(x => x.FacturaId).OnDelete(DeleteBehavior.Cascade);
            b.HasOne(x => x.Caja).WithMany().HasForeignKey(x => x.CajaId).OnDelete(DeleteBehavior.SetNull);
        });

        builder.Entity<Caja>(b =>
        {
            b.ToTable("Cajas", "ventas");
            b.HasIndex(x => x.Codigo).IsUnique();
            b.Property(x => x.Codigo).HasMaxLength(20).IsRequired();
            b.Property(x => x.Nombre).HasMaxLength(100).IsRequired();
            b.Property(x => x.MontoApertura).HasPrecision(18, 2);
            b.Property(x => x.MontoCierre).HasPrecision(18, 2);
        });

        builder.Entity<Cotizacion>(b =>
        {
            b.ToTable("Cotizaciones", "ventas");
            b.HasIndex(x => x.Numero).IsUnique();
            b.Property(x => x.Numero).HasMaxLength(20).IsRequired();
            b.Property(x => x.Subtotal).HasPrecision(18, 2);
            b.Property(x => x.Impuesto).HasPrecision(18, 2);
            b.Property(x => x.Total).HasPrecision(18, 2);
            b.HasOne(x => x.Cliente).WithMany().HasForeignKey(x => x.ClienteId).OnDelete(DeleteBehavior.Restrict);
            b.HasOne(x => x.OrdenTrabajo).WithMany().HasForeignKey(x => x.OrdenTrabajoId).OnDelete(DeleteBehavior.SetNull);
        });

        builder.Entity<DetalleCotizacion>(b =>
        {
            b.ToTable("DetallesCotizacion", "ventas");
            b.Property(x => x.Cantidad).HasPrecision(18, 4);
            b.Property(x => x.PrecioUnitario).HasPrecision(18, 2);
            b.Property(x => x.Subtotal).HasPrecision(18, 2);
            b.HasOne(x => x.Cotizacion).WithMany(c => c.Detalles).HasForeignKey(x => x.CotizacionId).OnDelete(DeleteBehavior.Cascade);
        });

        // Purchases
        builder.Entity<Proveedor>(b =>
        {
            b.ToTable("Proveedores", "compras");
            b.HasIndex(x => x.Codigo).IsUnique();
            b.Property(x => x.Codigo).HasMaxLength(20).IsRequired();
            b.Property(x => x.RazonSocial).HasMaxLength(200).IsRequired();
            b.Property(x => x.CalificacionPrecio).HasPrecision(3, 1);
            b.Property(x => x.CalificacionTiempo).HasPrecision(3, 1);
            b.Property(x => x.CalificacionCalidad).HasPrecision(3, 1);
        });

        builder.Entity<OrdenCompra>(b =>
        {
            b.ToTable("OrdenesCompra", "compras");
            b.HasIndex(x => x.Numero).IsUnique();
            b.Property(x => x.Numero).HasMaxLength(20).IsRequired();
            b.Property(x => x.Subtotal).HasPrecision(18, 2);
            b.Property(x => x.Impuesto).HasPrecision(18, 2);
            b.Property(x => x.Total).HasPrecision(18, 2);
            b.HasOne(x => x.Proveedor).WithMany().HasForeignKey(x => x.ProveedorId).OnDelete(DeleteBehavior.Restrict);
            b.HasOne(x => x.AlmacenDestino).WithMany().HasForeignKey(x => x.AlmacenDestinoId).OnDelete(DeleteBehavior.SetNull);
        });

        builder.Entity<DetalleOrdenCompra>(b =>
        {
            b.ToTable("DetallesOrdenCompra", "compras");
            b.Property(x => x.Cantidad).HasPrecision(18, 4);
            b.Property(x => x.CantidadRecibida).HasPrecision(18, 4);
            b.Property(x => x.PrecioUnitario).HasPrecision(18, 2);
            b.Property(x => x.Descuento).HasPrecision(18, 2);
            b.Property(x => x.Subtotal).HasPrecision(18, 2);
            b.HasOne(x => x.OrdenCompra).WithMany(o => o.Detalles).HasForeignKey(x => x.OrdenCompraId).OnDelete(DeleteBehavior.Cascade);
            b.HasOne(x => x.Repuesto).WithMany().HasForeignKey(x => x.RepuestoId).OnDelete(DeleteBehavior.Restrict);
        });

        builder.Entity<CuentaPagar>(b =>
        {
            b.ToTable("CuentasPagar", "compras");
            b.Property(x => x.Monto).HasPrecision(18, 2);
            b.Property(x => x.MontoPagado).HasPrecision(18, 2);
            b.Property(x => x.Saldo).HasPrecision(18, 2);
            b.HasOne(x => x.Proveedor).WithMany().HasForeignKey(x => x.ProveedorId).OnDelete(DeleteBehavior.Restrict);
            b.HasOne(x => x.OrdenCompra).WithMany().HasForeignKey(x => x.OrdenCompraId).OnDelete(DeleteBehavior.SetNull);
        });

        // Personnel
        builder.Entity<Tecnico>(b =>
        {
            b.ToTable("Tecnicos", "personal");
            b.HasIndex(x => x.Codigo).IsUnique();
            b.Property(x => x.Codigo).HasMaxLength(20).IsRequired();
            b.Property(x => x.Nombres).HasMaxLength(100).IsRequired();
            b.Property(x => x.Apellidos).HasMaxLength(100).IsRequired();
            b.Property(x => x.TarifaHora).HasPrecision(18, 2);
            b.Property(x => x.PorcentajeComision).HasPrecision(5, 2);
        });

        builder.Entity<RegistroAsistencia>(b =>
        {
            b.ToTable("RegistrosAsistencia", "personal");
            b.Property(x => x.HorasTrabajadas).HasPrecision(5, 2);
            b.Property(x => x.HorasExtras).HasPrecision(5, 2);
            b.HasOne(x => x.Tecnico).WithMany(t => t.Asistencias).HasForeignKey(x => x.TecnicoId).OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<Comision>(b =>
        {
            b.ToTable("Comisiones", "personal");
            b.Property(x => x.MontoBase).HasPrecision(18, 2);
            b.Property(x => x.Porcentaje).HasPrecision(5, 2);
            b.Property(x => x.MontoComision).HasPrecision(18, 2);
            b.HasOne(x => x.Tecnico).WithMany().HasForeignKey(x => x.TecnicoId).OnDelete(DeleteBehavior.Restrict);
        });

        // System
        builder.Entity<Empresa>(b =>
        {
            b.ToTable("Empresas", "sistema");
            b.Property(x => x.RazonSocial).HasMaxLength(200).IsRequired();
            b.Property(x => x.PorcentajeImpuesto).HasPrecision(5, 2);
        });

        builder.Entity<Sucursal>(b =>
        {
            b.ToTable("Sucursales", "sistema");
            b.HasIndex(x => x.Codigo).IsUnique();
            b.Property(x => x.Codigo).HasMaxLength(20).IsRequired();
            b.Property(x => x.Nombre).HasMaxLength(100).IsRequired();
        });

        builder.Entity<ParametroSistema>(b =>
        {
            b.ToTable("Parametros", "sistema");
            b.HasIndex(x => x.Clave).IsUnique();
            b.Property(x => x.Clave).HasMaxLength(100).IsRequired();
            b.Property(x => x.Valor).HasMaxLength(500).IsRequired();
        });

        builder.Entity<LogAuditoria>(b =>
        {
            b.ToTable("LogsAuditoria", "sistema");
            b.HasIndex(x => x.Fecha);
            b.Property(x => x.Usuario).HasMaxLength(100);
            b.Property(x => x.Accion).HasMaxLength(50);
            b.Property(x => x.Entidad).HasMaxLength(100);
        });

        builder.Entity<PlantillaNotificacion>(b =>
        {
            b.ToTable("PlantillasNotificacion", "sistema");
            b.HasIndex(x => x.Codigo).IsUnique();
            b.Property(x => x.Codigo).HasMaxLength(50).IsRequired();
            b.Property(x => x.Nombre).HasMaxLength(150).IsRequired();
            b.Property(x => x.Asunto).HasMaxLength(200);
        });

        builder.Entity<NotificacionEnviada>(b =>
        {
            b.ToTable("NotificacionesEnviadas", "sistema");
            b.HasIndex(x => x.Fecha);
        });
    }

    public override async Task<int> SaveChangesAsync(CancellationToken cancellationToken = default)
    {
        var entries = ChangeTracker.Entries<Domain.Common.AuditableEntity>();
        foreach (var entry in entries)
        {
            if (entry.State == EntityState.Added)
                entry.Entity.FechaCreacion = DateTime.UtcNow;
            if (entry.State == EntityState.Modified)
                entry.Entity.FechaModificacion = DateTime.UtcNow;
        }
        return await base.SaveChangesAsync(cancellationToken);
    }
}
