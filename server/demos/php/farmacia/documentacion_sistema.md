# Documentación Técnica: ERP Farmacia

## 1. Tecnología del Sistema
El sistema ha sido desarrollado utilizando el stack **LEMP** (Linux, Nginx, MySQL, PHP) con el framework **Laravel 11**.

*   **Backend:** PHP 8.2+ / Laravel 11
*   **Base de Datos:** MySQL 8.0
*   **Frontend:** Blade Templates + Tailwind CSS + Vite
*   **Librerías Principales:**
    *   `spatie/laravel-permission`: Gestión de roles y permisos.
    *   `barryvdh/laravel-dompdf`: Generación de reportes PDF.
    *   `maatwebsite/excel`: Exportación a hojas de cálculo.
    *   `chart.js`: Visualización de métricas en el Dashboard.

## 2. Funcionalidades Principales
1.  **Punto de Venta (POS):** Venta rápida con búsqueda en tiempo real y gestión de caja.
2.  **Inventario de Productos:** Control de stock, categorías y alertas de existencias bajas.
3.  **Gestión de Lotes:** Control de fechas de vencimiento por lote de producto.
4.  **Caja Registradora:** Flujo completo de apertura, movimientos y cierre diario.
5.  **Recetas Médicas:** Control de medicamentos que requieren prescripción.
6.  **Compras y Proveedores:** Registro de órdenes de compra y recepción de mercadería.
7.  **Clientes y Fidelidad:** Historial de pacientes y sistema de puntos.
8.  **Reportes:** Exportación de ventas, stock y vencimientos.

## 3. Guía de Instalación (Local)
1.  **Software Necesario:**
    *   Descargar e instalar [Laragon](https://laragon.org/) o [XAMPP](https://www.apachefriends.org/).
    *   Instalar [Composer](https://getcomposer.org/).
2.  **Configuración de BD:**
    *   Crear base de datos `farmacia_erp` en MySQL.
3.  **Comandos de Instalación:**
    ```bash
    composer install
    php artisan key:generate
    php artisan migrate --seed
    php artisan serve
    ```
4.  **Acceso:** `http://localhost:8000` (Admin: `admin@farmacia.test` / `password`).

## 4. Hosting Sugerido y Despliegue
### Hosting Recomendado: **DigitalOcean**
Se sugiere un VPS (Droplet) de $4 o $6 mensual. Ofrece control total sobre el servidor PHP y MySQL.

### Pasos para Despliegue (VPS):
1.  **Servidor:** Configurar Ubuntu con Nginx + PHP 8.2-FPM.
2.  **Base de Datos:** Configurar MySQL y crear usuario seguro.
3.  **Código:** Subir vía Git o SFTP.
4.  **Permisos:** Asegurar que `storage` y `bootstrap/cache` tengan permisos de escritura.
5.  **Dominio:** Configurar certificado SSL con Certbot (Let's Encrypt).
