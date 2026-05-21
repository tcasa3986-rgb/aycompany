<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentación - CotizaPro</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #334155; line-height: 1.6; margin: 0; padding: 20px; font-size: 14px; }
        h1 { color: #0f172a; font-size: 28px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { color: #1e293b; font-size: 22px; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        h3 { color: #334155; font-size: 18px; margin-top: 20px; }
        p { margin-bottom: 15px; }
        ul, ol { margin-bottom: 15px; padding-left: 20px; }
        li { margin-bottom: 5px; }
        .highlight { background-color: #f1f5f9; padding: 2px 5px; border-radius: 4px; font-family: monospace; color: #ef4444; }
        .code-block { background-color: #1e293b; color: #f8fafc; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 13px; overflow-x: auto; margin-bottom: 15px; white-space: pre-wrap; }
        .step { background-color: #f8fafc; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 15px; border-radius: 0 8px 8px 0; }
        .step strong { color: #2563eb; }
        .cover { text-align: center; margin-top: 100px; margin-bottom: 100px; page-break-after: always; }
        .cover h1 { border: none; font-size: 40px; color: #0f172a; }
        .cover p { font-size: 18px; color: #64748b; }
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 50px; text-align: center; line-height: 35px; color: #94a3b8; font-size: 12px; border-top: 1px solid #e2e8f0; }
        .page-break { page-break-after: always; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border: 1px solid #e2e8f0; text-align: left; }
        th { background-color: #f8fafc; font-weight: bold; }
    </style>
</head>
<body>

    <div class="cover">
        <h1>Manual Técnico y de Despliegue</h1>
        <p>Sistema de Cotizaciones y Facturación (CotizaPro)</p>
        <div style="margin-top: 50px; color: #94a3b8; font-size: 14px;">
            Generado: {{ date('d/m/Y') }}
        </div>
    </div>

    <h2>1. Tecnologías del Sistema</h2>
    <p>El sistema <strong>CotizaPro</strong> ha sido desarrollado utilizando un stack tecnológico moderno, robusto y escalable, basado en PHP y el ecosistema Laravel.</p>
    <ul>
        <li><strong>Backend Framework:</strong> Laravel 11.x (PHP 8.3+)</li>
        <li><strong>Arquitectura:</strong> MVC (Modelo-Vista-Controlador)</li>
        <li><strong>Motor de Base de Datos:</strong> MySQL (recomendado para producción) / SQLite (soporte para desarrollo).</li>
        <li><strong>Frontend:</strong> Blade Templates (Motor de plantillas de Laravel).</li>
        <li><strong>Estilos (CSS):</strong> CSS nativo / variables CSS, optimizado sin dependencias pesadas de frameworks UI para mantenerlo ligero y personalizable.</li>
        <li><strong>JavaScript:</strong> Vanilla JS y Chart.js para visualización de gráficos en el Dashboard.</li>
        <li><strong>Generación de PDFs:</strong> <span class="highlight">barryvdh/laravel-dompdf</span>.</li>
        <li><strong>Gestor de Dependencias:</strong> Composer (PHP) y NPM (Frontend Assets / Vite).</li>
    </ul>

    <h2>2. Funcionalidades Principales</h2>
    <p>El sistema es un ERP simplificado centrado en la gestión ágil de cotizaciones comerciales.</p>
    <table>
        <thead>
            <tr>
                <th>Módulo</th>
                <th>Descripción / Funcionalidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Dashboard</strong></td>
                <td>Panel de control con métricas en tiempo real: total de cotizaciones emitidas/aprobadas, ingresos totales, ticket promedio, tasa de aprobación. Gráficos de ingresos mensuales y distribución por estado.</td>
            </tr>
            <tr>
                <td><strong>Clientes</strong></td>
                <td>CRUD completo de clientes. Historial de cotizaciones por cliente. Exportación de listados a Excel (CSV) y PDF.</td>
            </tr>
            <tr>
                <td><strong>Productos</strong></td>
                <td>Catálogo de productos o servicios. Precios y unidades configurables. Exportación a Excel y PDF.</td>
            </tr>
            <tr>
                <td><strong>Empresas</strong></td>
                <td>Gestión de empresas o entidades asociadas. Exportación a Excel y PDF.</td>
            </tr>
            <tr>
                <td><strong>Cotizaciones</strong></td>
                <td>Creación, edición y eliminación de cotizaciones. Cálculo automático de subtotales, descuentos e IGV. Conversión de estados (Borrador, Emitida, Aprobada, Rechazada). Duplicación rápida (Clonar). Generación y descarga de PDF con formato profesional. Envío directo por correo electrónico. Exportación de lista a Excel y PDF.</td>
            </tr>
            <tr>
                <td><strong>Reportes</strong></td>
                <td>Análisis detallado por año. KPIs anuales, desglose mensual y top de clientes. Exportación de resultados a Excel y PDF.</td>
            </tr>
            <tr>
                <td><strong>Configuración</strong></td>
                <td>Parámetros globales: Moneda por defecto (PEN, USD, EUR), IGV, logotipo de la empresa emisora, datos fiscales, términos y condiciones predeterminados, y credenciales SMTP para envíos de correo.</td>
            </tr>
            <tr>
                <td><strong>Mantenimiento</strong></td>
                <td>Generación de copias de seguridad (Backups SQL) y restauración de la base de datos completa directamente desde la interfaz web.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>3. Instalación Local (Paso a Paso)</h2>
    <p>Esta guía asume que utilizarás un entorno de desarrollo local como <strong>XAMPP</strong>, <strong>Laragon</strong> o <strong>Herd</strong> en Windows.</p>

    <div class="step">
        <strong>Paso 1: Requisitos Previos</strong>
        <p>Asegúrate de tener instalados los siguientes programas:</p>
        <ul>
            <li><strong>PHP:</strong> Versión 8.3 o superior.</li>
            <li><strong>Composer:</strong> Gestor de dependencias de PHP (<a href="https://getcomposer.org/">getcomposer.org</a>).</li>
            <li><strong>Node.js y NPM:</strong> Para compilar los assets del frontend (<a href="https://nodejs.org/">nodejs.org</a>).</li>
            <li><strong>Servidor de Base de Datos:</strong> MySQL (incluido en XAMPP/Laragon).</li>
            <li><strong>Git:</strong> (Opcional, pero recomendado).</li>
        </ul>
    </div>

    <div class="step">
        <strong>Paso 2: Obtener el Código y Dependencias</strong>
        <p>Abre tu terminal (Command Prompt, PowerShell o Git Bash) en la carpeta donde deseas alojar el proyecto (ej. <code>C:\xampp\htdocs\</code> o <code>C:\laragon\www\</code>).</p>
        <div class="code-block">git clone [URL_DEL_REPOSITORIO] cotizacion
cd cotizacion
composer install
npm install</div>
    </div>

    <div class="step">
        <strong>Paso 3: Configuración del Entorno (.env)</strong>
        <p>Copia el archivo de ejemplo y configura tu base de datos local:</p>
        <div class="code-block">cp .env.example .env
php artisan key:generate</div>
        <p>Abre el archivo <code>.env</code> generado con un editor de texto y configura la conexión a la base de datos (asegúrate de haber creado una base de datos vacía llamada <code>cotizapro</code> en MySQL):</p>
        <div class="code-block">DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cotizapro
DB_USERNAME=root
DB_PASSWORD=</div>
    </div>

    <div class="step">
        <strong>Paso 4: Migraciones y Compilación</strong>
        <p>Ejecuta las migraciones para crear las tablas en la base de datos y compila los assets CSS/JS:</p>
        <div class="code-block">php artisan migrate
npm run build</div>
    </div>

    <div class="step">
        <strong>Paso 5: Iniciar el Servidor Local</strong>
        <p>Levanta el servidor de desarrollo de Laravel:</p>
        <div class="code-block">php artisan serve</div>
        <p>El sistema estará disponible en tu navegador ingresando a: <code>http://127.0.0.1:8000</code>. Registra tu primer usuario administrador desde la opción "Register".</p>
    </div>

    <div class="page-break"></div>

    <h2>4. Hosting Web Recomendado</h2>
    <p>Para aplicaciones Laravel, es fundamental elegir un hosting que permita acceso SSH y soporte PHP 8.3+. A continuación se presentan las mejores opciones:</p>

    <h3>Recomendación Principal: Hostinger (Plan Premium o superior)</h3>
    <p><strong>¿Por qué Hostinger?</strong></p>
    <ul>
        <li>Excelente relación calidad-precio.</li>
        <li>Panel de control amigable (hPanel).</li>
        <li>Acceso SSH disponible (crucial para comandos de Laravel).</li>
        <li>Fácil cambio de versiones de PHP (soporta PHP 8.3).</li>
        <li>Bases de datos MySQL SSD.</li>
        <li>Certificados SSL gratuitos.</li>
    </ul>

    <h3>Otras Alternativas</h3>
    <ul>
        <li><strong>DigitalOcean / Linode:</strong> Servidores VPS no administrados (Requiere conocimientos avanzados de Linux, o usar Laravel Forge). Ideal para máxima escalabilidad.</li>
        <li><strong>SiteGround / A2 Hosting:</strong> Excelentes opciones de hosting compartido con soporte robusto para Laravel y SSH, aunque ligeramente más costosos que Hostinger.</li>
    </ul>

    <div class="page-break"></div>

    <h2>5. Guía de Despliegue (Hostinger)</h2>
    <p>Proceso paso a paso para subir CotizaPro a un hosting compartido estándar como Hostinger utilizando acceso SSH.</p>

    <div class="step">
        <strong>Paso 1: Preparación del Entorno en el Hosting</strong>
        <ul>
            <li>Inicia sesión en tu hPanel de Hostinger.</li>
            <li>En la sección <strong>Avanzado > Configuración de PHP</strong>, asegúrate de seleccionar <strong>PHP 8.3</strong>.</li>
            <li>En <strong>Avanzado > Acceso SSH</strong>, activa el acceso y anota tus credenciales (IP, Puerto, Usuario, Contraseña).</li>
            <li>En <strong>Bases de Datos > Bases de datos MySQL</strong>, crea una nueva base de datos, un usuario y una contraseña. Anota estos datos.</li>
        </ul>
    </div>

    <div class="step">
        <strong>Paso 2: Subir Archivos (Vía SSH/Git o Archivo ZIP)</strong>
        <p><strong>Opción A (Recomendada vía SSH):</strong> Conéctate por SSH a tu servidor y clona el repositorio en la carpeta raíz (usualmente fuera de <code>public_html</code> por seguridad, pero para este ejemplo usaremos una ruta estándar).</p>
        <div class="code-block">ssh -p PORT USUARIO@IP_SERVIDOR
cd domains/tudominio.com
# Eliminar la carpeta public_html por defecto si vas a clonar directamente
rm -rf public_html
git clone [URL_REPOSITORIO] public_html
cd public_html</div>
        
        <p><strong>Opción B (Archivo ZIP):</strong></p>
        <ol>
            <li>En tu computadora local, ejecuta <code>npm run build</code>.</li>
            <li>Comprime todo el proyecto en un archivo <code>proyecto.zip</code> (excluye <code>node_modules</code>, pero incluye <code>vendor</code> si no puedes correr composer en el server).</li>
            <li>En Hostinger, ve a <strong>Archivos > Administrador de Archivos</strong>.</li>
            <li>Sube el ZIP dentro de <code>public_html</code> (o en una carpeta superior por seguridad) y extráelo.</li>
        </ol>
    </div>

    <div class="step">
        <strong>Paso 3: Configurar .env en Producción</strong>
        <p>En el servidor, copia <code>.env.example</code> a <code>.env</code>. Edita el archivo con los datos de producción:</p>
        <div class="code-block">APP_NAME=CotizaPro
APP_ENV=production
APP_KEY=base64:.... (asegúrate de correr php artisan key:generate si está vacío)
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=[Nombre_DB_Hostinger]
DB_USERNAME=[Usuario_DB_Hostinger]
DB_PASSWORD=[Clave_DB_Hostinger]</div>
    </div>

    <div class="step">
        <strong>Paso 4: Comandos de Instalación (Vía SSH)</strong>
        <p>Conéctate por SSH y ejecuta los siguientes comandos dentro de la carpeta de tu proyecto:</p>
        <div class="code-block">composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache</div>
    </div>

    <div class="step">
        <strong>Paso 5: Enrutamiento Público (Importante en Hosting Compartido)</strong>
        <p>En Laravel, el directorio público es <code>/public</code>. En un hosting compartido, el dominio apunta a <code>/public_html</code>. Existen varias formas de resolver esto.</p>
        <p><strong>Método más seguro:</strong></p>
        <ol>
            <li>Sube los archivos del proyecto a una carpeta <em>fuera</em> de public_html (ej. <code>/domains/tudominio.com/cotizapro</code>).</li>
            <li>Copia el contenido de la carpeta <code>cotizapro/public/</code> al interior de la carpeta <code>/domains/tudominio.com/public_html/</code>.</li>
            <li>Edita el archivo <code>public_html/index.php</code>:
<pre style="background:#282c34;color:#abb2bf;padding:10px;border-radius:4px;font-size:12px;"><code>// Cambia estas líneas:
require __DIR__.'/../cotizapro/vendor/autoload.php';
$app = require_once __DIR__.'/../cotizapro/bootstrap/app.php';</code></pre>
            </li>
        </ol>
        <p><strong>Alternativa (Archivo .htaccess en la raíz):</strong> Si subiste todo dentro de <code>public_html</code>, crea un archivo <code>.htaccess</code> en <code>public_html</code> con el siguiente contenido para redirigir el tráfico a la carpeta public:</p>
        <div class="code-block"><IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule></div>
    </div>

    <div class="step">
        <strong>Paso 6: Permisos de Carpetas</strong>
        <p>Asegúrate de que las carpetas <code>storage</code> y <code>bootstrap/cache</code> tengan permisos de escritura (generalmente 755 o 775).</p>
        <div class="code-block">chmod -R 775 storage
chmod -R 775 bootstrap/cache</div>
    </div>

    <div class="footer">
        Sistema CotizaPro - Documentación Técnica
    </div>
</body>
</html>
