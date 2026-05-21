<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual Técnico - LabSalud</title>
    <style>
        @page { margin: 2cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 11pt;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28pt;
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        h1, h2, h3 { color: #00325b; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        h1 { font-size: 20pt; margin-top: 40px; }
        h2 { font-size: 16pt; margin-top: 30px; color: #0284c7; }
        h3 { font-size: 13pt; margin-top: 20px; border: none; }
        
        .section { margin-bottom: 25px; }
        .tech-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .tech-grid td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .label { font-weight: bold; color: #0284c7; width: 30%; }
        
        .highlight-box {
            background: #f0f9ff;
            border-left: 5px solid #0284c7;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-box {
            background: #fff7ed;
            border-left: 5px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        code {
            background: #f1f5f9;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
        }
        .step {
            margin-bottom: 15px;
            padding-left: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .page-break { page-break-after: always; }
        ul { padding-left: 20px; }
        li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="footer">
        LabSalud - Sistema de Gestión de Laboratorio Clínico | Documentación técnica confidencial
    </div>

    <div class="header">
        <div class="logo">LabSalud</div>
        <div class="subtitle">Manual de Implementación y Usuario</div>
        <p>Fecha de generación: {{ date('d/m/Y') }}</p>
    </div>

    <div class="section">
        <h1>1. Tecnologías de Desarrollo</h1>
        <p>El sistema LabSalud ha sido construido utilizando los estándares más modernos de la industria para garantizar escalabilidad, seguridad y una experiencia de usuario premium.</p>
        <table class="tech-grid">
            <tr>
                <td class="label">Backend Framework</td>
                <td><strong>Laravel 11</strong> (PHP 8.3+) - El framework más robusto para aplicaciones empresariales.</td>
            </tr>
            <tr>
                <td class="label">Base de Datos</td>
                <td><strong>MySQL 8.0+</strong> - Motor relacional optimizado para integridad de datos.</td>
            </tr>
            <tr>
                <td class="label">Estilizado (CSS)</td>
                <td><strong>CSS Vanilla Premium</strong> con variables HSL, gradientes avanzados y diseño responsive nativo.</td>
            </tr>
            <tr>
                <td class="label">Frontend Logic</td>
                <td><strong>JavaScript (ES6+)</strong> nativo y Axios para comunicaciones asíncronas.</td>
            </tr>
            <tr>
                <td class="label">Seguridad</td>
                <td><strong>Spatie Laravel Permission</strong> para gestión granular de roles y permisos.</td>
            </tr>
            <tr>
                <td class="label">Gráficos</td>
                <td><strong>Chart.js</strong> para visualización estadística dinámica.</td>
            </tr>
            <tr>
                <td class="label">Reportes</td>
                <td><strong>Barryvdh DomPDF</strong> para generación de documentos y resultados clínicos.</td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h1>2. Funcionalidades del Sistema</h1>
        
        <h2>Módulo de Recepción</h2>
        <ul>
            <li><strong>Gestión de Pacientes:</strong> Registro completo, historial clínico y búsqueda avanzada.</li>
            <li><strong>Órdenes Médicas:</strong> Creación de peticiones, prioridad, selección de médicos referidores y convenios.</li>
            <li><strong>Agenda y Citas:</strong> Control de flujo de pacientes por horario.</li>
        </ul>

        <h2>Módulo de Laboratorio</h2>
        <ul>
            <li><strong>Toma de Muestras:</strong> Control de estados, impresión de etiquetas y fecha de toma.</li>
            <li><strong>Resultados Clínicos:</strong> Ingreso de valores, validación por tecnólogo y envío automático por email.</li>
            <li><strong>Inventario de Reactivos:</strong> Control de stock actual, mínimo y alertas de agotamiento.</li>
        </ul>

        <h2>Administración y Finanzas</h2>
        <ul>
            <li><strong>Facturación:</strong> Generación de comprobantes, control de pagos y deudas.</li>
            <li><strong>Catálogo de Pruebas:</strong> Gestión de áreas, tipos de examen y valores de referencia.</li>
            <li><strong>Médicos y Convenios:</strong> Directorio de alianzas estratégicas.</li>
        </ul>

        <h2>Sistema y Seguridad</h2>
        <ul>
            <li><strong>Roles de Usuario:</strong> Administrador, Tecnólogo, Recepcionista y Médico.</li>
            <li><strong>Mantenimiento:</strong> Copias de seguridad automáticas, restauración y reseteo integral.</li>
        </ul>
    </div>

    <div class="highlight-box">
        <h3>Credenciales de acceso predeterminadas:</h3>
        <p><strong>Usuario:</strong> <code>admin@lab.com</code></p>
        <p><strong>Contraseña:</strong> <code>password</code></p>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h1>3. Instalación en Computadora Nueva</h1>
        <p>Para poner en marcha el sistema en un entorno local de Windows, siga estos pasos estrictamente:</p>
        
        <div class="step">
            <h3>Paso 1: Descargar Software Base</h3>
            <ul>
                <li><strong>XAMPP (con PHP 8.3):</strong> <a href="https://www.apachefriends.org/es/index.html">Descargar aquí</a>. Instale e inicie los módulos Apache y MySQL.</li>
                <li><strong>Composer (Gestor de dependencias PHP):</strong> <a href="https://getcomposer.org/download/">Descargar aquí</a>.</li>
                <li><strong>Node.js:</strong> <a href="https://nodejs.org/">Descargar aquí</a>.</li>
            </ul>
        </div>

        <div class="step">
            <h3>Paso 2: Preparar la Aplicación</h3>
            <p>Copie la carpeta del proyecto a <code>C:\xampp\htdocs\laboratorio-clinico</code>.</p>
        </div>

        <div class="step">
            <h3>Paso 3: Configuración del Entorno</h3>
            <p>Renombre el archivo <code>.env.example</code> a <code>.env</code> y configure la base de datos:</p>
            <code>DB_DATABASE=laboratorio_clinico<br>DB_USERNAME=root<br>DB_PASSWORD=</code>
        </div>

        <div class="step">
            <h3>Paso 4: Comandos de Inicialización</h3>
            <p>Abra una terminal en la carpeta del proyecto y ejecute:</p>
            <ul>
                <li>Instalar dependencias: <code>composer install</code></li>
                <li>Generar llave de seguridad: <code>php artisan key:generate</code></li>
                <li>Migrar y poblar base de datos: <code>php artisan migrate --seed</code></li>
                <li>Instalar frontend: <code>npm install && npm run build</code></li>
            </ul>
        </div>

        <div class="step">
            <h3>Paso 5: Acceso</h3>
            <p>Inicie Apache y MySQL desde XAMPP, luego ejecute: <code>php artisan serve</code>. Acceda a <code>http://localhost:8000</code>.</p>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h1>4. Hosting y Despliegue Web</h1>
        
        <h2>Hosting Recomendado: DigitalOcean (via Laravel Forge)</h2>
        <p>Esta es la opción más profesional y escalable para una aplicación de salud.</p>
        
        <div class="highlight-box">
            <h3>¿Por qué elegir DigitalOcean?</h3>
            <ul>
                <li><strong>Ubicación de datos:</strong> Se puede elegir el servidor más cercano a su ciudad.</li>
                <li><strong>Velocidad SSD:</strong> Acceso instantáneo a resultados y registros.</li>
                <li><strong>SSL Gratis:</strong> Certificado de seguridad incluido para proteger datos de pacientes.</li>
            </ul>
        </div>

        <h2>Pasos para el Despliegue:</h2>
        <div class="step">
            <h3>1. Preparación del Servidor</h3>
            <p>Cree un Droplet en DigitalOcean con Ubuntu 22.04 LTS. Puede usar <strong>Laravel Forge</strong> para automatizar toda la instalación de PHP 8.3, MySQL y Nginx con un par de clics.</p>
        </div>

        <div class="step">
            <h3>2. Gestión de Código</h3>
            <p>Suba su proyecto a un repositorio privado en GitHub o GitLab. Conéctelo a su servidor para despliegues automáticos.</p>
        </div>

        <div class="step">
            <h3>3. Configuración de Producción</h3>
            <p>Configure el archivo <code>.env</code> en el servidor con <code>APP_ENV=production</code> y <code>APP_DEBUG=false</code>. Configure las credenciales de la base de datos del servidor.</p>
        </div>

        <div class="step">
            <h3>4. Optimización</h3>
            <p>Ejecute los comandos de optimización en el servidor:</p>
            <code>php artisan config:cache</code><br>
            <code>php artisan route:cache</code><br>
            <code>php artisan view:cache</code>
        </div>
    </div>

    <div class="alert-box">
        <strong>Importante:</strong> Para entornos en la nube, asegúrese de configurar correctamente el sistema de copias de seguridad automáticas de DigitalOcean (Backups) para prevenir pérdida de datos críticos.
    </div>

</body>
</html>
