<h1 align="center">🏪 Manual y Documentación Técnica - MiniMarket Pro</h1>

<p align="center">
  <img src="https://cdn-icons-png.flaticon.com/512/3081/3081840.png" width="100" />
</p>

## 🛠️ Tecnologías de Desarrollo

El sistema ha sido desarrollado con un stack moderno y escalable utilizando el ecosistema de Microsoft .NET:

* **Backend:** C# y **ASP.NET Core 10.0** (Arquitectura MVC).
* **Base de Datos:** SQL Server.
* **ORM (Mapeador Objeto-Relacional):** Entity Framework Core 10.0.
* **Seguridad y Autenticación:** ASP.NET Core Identity (Roles, Usuarios, Encriptación de Contraseñas).
* **Frontend / Diseño UI:** Razor Pages/Views, HTML5, Vanilla CSS interactivo, **Bootstrap 5.3**, DataTables (para reportes web), Select2 y FontAwesome para iconografía.
* **Tipografía:** *Outfit* (Google Fonts) para una apariencia corporativa premium.

---

## ⚡ Funcionalidades del Sistema

El sistema implementa un completo ERP / POS para la administración exhaustiva del MiniMarket:

1. **Dashboard y Analíticas:** Vista general de ventas del día, productos más vendidos e indicadores financieros de negocio.
2. **Punto de Venta (POS):** Pantalla de creación de ventas rápida tipo supermercado, búsqueda responsiva, cálculo automático de impuestos (IGV) y subtotales.
3. **Módulo de Caja y Turnos:** Apertura de caja, cierre, registro de saldo inicial y control de efectivo validado por cada cajero en su respectivo turno.
4. **Historial de Ventas y Compras:** Listados para auditoría con opción a anulación o visualización de tickets/facturas con reversión del stock de los productos.
5. **Inventario y Productos:** Control de stock, alertas visuales de stock mínimo o críticos, gestión de categorías/familias.
6. **Impresión de Etiquetas (Código de Barras):** Generador dinámico de códigos de barras basado en JsBarcode para productos, optimizado de copia múltiple para tiqueteras térmicas y A4.
7. **Control de Gastos:** Registro de movimientos y gastos operativos afectando directamente los balances del cierre de caja del turno.
8. **Kardex:** Monitorización detallada de movimientos, entradas y salidas de mercancía en tiempo real para análisis.
9. **Gestión de Clientes y Proveedores:** Base de datos relacional para guardar compradores y sus números de identificación o impuestos asociados para facturación rápida.
10. **Seguridad y Usuarios:** Control de roles restrictivos (`Administrador` con acceso total vs `Cajero` con pantallas restringidas), y módulo central para configurar negocio.

---

## 💻 Instalación y Configuración (Computadora Nueva)

### Paso 1: Descargar los Requisitos
Descargue e instale los siguientes programas obligatorios para el servidor/local:
1. **.NET 10.0 SDK:** Requerido para compilar y ejecutar la aplicación.
2. **SQL Server Developer Edition / SQL Express:** Motor de la base de datos empresarial.
3. **SQL Server Management Studio (SSMS):** Opcional, para visualizar las tablas de su BD.

### Paso 2: Configuración de la Base de Datos
1. Abra el archivo `appsettings.json` o `appsettings.Development.json` en la carpeta raíz `MiniMarket.Web` usando el bloc de notas o Visual Studio Code.
2. Busque la propiedad de conexión `DefaultConnection`.
3. Ingrese en el parámetro `Server=` el nombre local de su SQL Server.
   *Ejemplo:* `"Server=localhost\SQLEXPRESS;Database=BK_BD_MINIMARKET;Trusted_Connection=True;MultipleActiveResultSets=true;TrustServerCertificate=True"`

### Paso 3: Construcción de la Estructura de Datos (Migraciones)
1. Abra una terminal en su sistema Windows (PowerShell, CMD) situándose dentro de la ruta física de la carpeta `MiniMarket.Web`.
2. Ejecute el comando: `dotnet ef database update`
3. Este subprocedimiento construirá estructuralmente las tablas y vistas, cargando a la vez datos semilla, incluyendo los usuarios de prueba en su servidor SQL local.

### Paso 4: Ejecutar el Sistema en el Navegador
1. Manteniéndose en esa misma carpeta vía terminal, digite: `dotnet run` 
2. Cuando le diga que el servidor inicio correctamente, abra su navegador web (Chrome/Edge).
3. Ingrese a la dirección brindada (generalmente `http://localhost:5xxx`) para iniciar sesión en la aplicación.

---

## 🔐 Credenciales de Administrador

Una vez cargada la base de datos con las migraciones, utilice la siguiente clave generada:

* **Usuario Administrador:** `admin@minimarket.com`
* **Contraseña:** `Admin123!`

---

## 🌐 Recomendación de Hosting Web

**SmarterASP.NET** es nuestra recomendación preferencial para subir su aplicación de C#.
* **Por qué elegirlo:** Funciona íntegramente con ecosistemas y arquitecturas de Microsoft, tiene un gran soporte a `.NET 10.0`, y otorga una prueba sin tarjeta de crédito válida de 60 días para testear su App en un ambiente real.
* **Alternativas:** `InterServer.net` o virtualizadores como `Azure App Services` si dispone de personal preparado en infraestructuras Cloud.

---

## 🚀 Paso a Paso: Subir la Aplicación a SmarterASP.NET

### Fase 1: Compilar la Aplicación a Versión Productiva
1. Dentro de la carpeta `MiniMarket.Web`, ejecute el siguiente comando para generar la versión ligera o optimizada lista para producción: `dotnet publish -c Release -o ./publish`
2. El proceso originará una nueva carpeta denominada **`publish`**.
3. Ingrese ahí y comprima todo su contenido interior generándose un fichero como **`MiniMarket.zip`**.

### Fase 2: Configurar Instancias en SmarterASP.NET
1. Entrar o registrar su prueba gratuita en **SmarterASP.NET** > Control Panel.
2. Váyase a la sección "Websites", añada su nueva web y dígale que será con **ASP.NET Core**.
3. Dirigirse después a la pestaña o submenú lateral de navegacion  **"Databases"** > **"MS SQL"** para provisionar y crear una nueva base de datos.
4. Le otorgarán un string de credenciales (Servidor, BD Nombre, Usuario y Clave); reserve dicha información para el siguiente paso.

### Fase 3: Conexión y Migración
1. Descomprima temporalmente el archivo `appsettings.json` o sitúese en la configuración de la nube para modificar el atributo **`DefaultConnection`** empleando las claves que su host SQL le acababa de proveer.
2. *(Migración de Data)*: Conéctese con SSMS (SQL Management Studio) a la base de datos de SmarterASP.NET (vía nube) directamente usando sus claves, para ejecutar sobre ella el Script SQL de Migración originado por Entity Framework que replicará idénticamente sus tablas.

### Fase 4: Subir los Ficheros
1. En SmarterASP.NET presione **File Manager (Administrador de Archivos)** de tu sitio web asigando.
2. Elimine todo el contenido de inicio que provee el host por obvias razones y luego presione Upload para subir su fichero **`MiniMarket.zip`**.
3. Efectúe una descompresión o extracción ('Extract') de todo el Zip alojándolo directamente en el directorio *Root* publico de la web.
4. **¡Felicidades!** Abra el dominio en su navegador web, ¡la aplicación está ahora funcional por el internet global!

<br/><hr/><p align="center" style="color:Gray"><i>Documentación automática e instruccional del Sistema MiniMarket.</i></p>
