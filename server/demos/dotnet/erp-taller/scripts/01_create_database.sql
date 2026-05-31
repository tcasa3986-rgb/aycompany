-- =====================================================================
-- AutoTaller ERP — Script opcional para crear la base de datos vacía.
-- Recomendado: usar EF Core migrations (`dotnet ef database update`)
-- en lugar de este script. Este archivo solo crea la BD si no existe.
-- =====================================================================

USE master;
GO

IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = N'TallerAutomotrizERP')
BEGIN
    CREATE DATABASE TallerAutomotrizERP
    COLLATE SQL_Latin1_General_CP1_CI_AS;
    PRINT 'Base de datos TallerAutomotrizERP creada.';
END
ELSE
BEGIN
    PRINT 'Base de datos TallerAutomotrizERP ya existe.';
END
GO

USE TallerAutomotrizERP;
GO

-- Esquemas por módulo (EF Core también los crea automáticamente)
IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'seguridad') EXEC('CREATE SCHEMA seguridad');
IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'crm')        EXEC('CREATE SCHEMA crm');
IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'taller')     EXEC('CREATE SCHEMA taller');
IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'inventario') EXEC('CREATE SCHEMA inventario');
IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'ventas')     EXEC('CREATE SCHEMA ventas');
IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'compras')    EXEC('CREATE SCHEMA compras');
IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'personal')   EXEC('CREATE SCHEMA personal');
IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'sistema')    EXEC('CREATE SCHEMA sistema');
GO

PRINT 'Esquemas creados. Ahora ejecute desde la raíz del proyecto:';
PRINT '   dotnet ef database update -p src/ERP.TallerAutomotriz.Infrastructure -s src/ERP.TallerAutomotriz.Web';
PRINT 'para crear todas las tablas y aplicar el modelo de datos.';
