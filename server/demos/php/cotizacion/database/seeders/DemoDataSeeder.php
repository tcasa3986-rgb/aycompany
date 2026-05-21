<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Settings (solo si no existen) ────────────────────
        $settingsDefaults = [
            'company_name'         => 'CotizaPro Demo S.A.C.',
            'company_ruc'          => '20512345678',
            'company_address'      => 'Av. Javier Prado Este 4200, Lima',
            'company_phone'        => '+51 01 234-5678',
            'company_email'        => 'ventas@cotizapro.pe',
            'company_website'      => 'https://www.cotizapro.pe',
            'default_currency'     => 'PEN',
            'default_tax_rate'     => '18',
            'quotation_prefix'     => 'COT',
            'terms_and_conditions' => "1. Los precios son válidos por 30 días desde la fecha de emisión.\n2. El plazo de entrega se coordinará al confirmar el pedido.\n3. Los pagos se realizarán según lo acordado entre las partes.\n4. Los precios incluyen IGV (18%) salvo indicación contraria.",
        ];
        foreach ($settingsDefaults as $k => $v) {
            Setting::firstOrCreate(['key' => $k], ['value' => $v]);
        }

        // ── 10 Clientes ──────────────────────────────────────
        $clientsData = [
            ['name' => 'Corporación Andina S.A.C.',      'document_number' => '20100102475', 'email' => 'compras@andina.pe',        'phone' => '01 234-5678', 'address' => 'Jr. Camaná 410, Lima'],
            ['name' => 'Tech Solutions Perú S.R.L.',     'document_number' => '20523456789', 'email' => 'admin@techsol.pe',         'phone' => '01 987-6543', 'address' => 'Av. Larco 1301, Miraflores'],
            ['name' => 'Distribuidora El Sol E.I.R.L.',  'document_number' => '20234567890', 'email' => 'gerencia@elsol.pe',        'phone' => '044 23-4567', 'address' => 'Av. España 1801, Trujillo'],
            ['name' => 'Constructora Lima Norte SAC',    'document_number' => '20345678901', 'email' => 'obras@limanorte.pe',       'phone' => '01 345-6789', 'address' => 'Av. Túpac Amaru 2850, Comas'],
            ['name' => 'Agro Export Ica S.A.',           'document_number' => '20456789012', 'email' => 'export@agroica.pe',       'phone' => '056 23-4567', 'address' => 'Panamericana Sur km 305, Ica'],
            ['name' => 'Inversiones Pacífico S.A.C.',    'document_number' => '20567890123', 'email' => 'finanzas@pacifico.pe',     'phone' => '01 456-7890', 'address' => 'Av. del Ejército 900, San Isidro'],
            ['name' => 'Clínica San Felipe S.A.',        'document_number' => '20678901234', 'email' => 'logistica@sanfelipe.pe',   'phone' => '01 567-8901', 'address' => 'Av. Gregorio Escobedo 650, Jesús María'],
            ['name' => 'Minera Horizonte S.A.C.',        'document_number' => '20789012345', 'email' => 'adquisiciones@horizonte.pe','phone' => '044 56-7890', 'address' => 'Av. Mansiche 1390, Trujillo'],
            ['name' => 'Grupo Educativo Innova SAC',     'document_number' => '20890123456', 'email' => 'compras@innova.edu.pe',    'phone' => '01 678-9012', 'address' => 'Av. La Encalada 1257, Surco'],
            ['name' => 'Ferretería Industrial Norte SAC','document_number' => '20901234567', 'email' => 'ventas@ferronorte.pe',     'phone' => '073 34-5678', 'address' => 'Jr. Loreto 560, Piura'],
        ];

        $clients = [];
        foreach ($clientsData as $c) {
            $clients[] = Client::firstOrCreate(['document_number' => $c['document_number']], $c);
        }

        // ── 10 Empresas (proveedores/socios) ─────────────────
        $companiesData = [
            ['name' => 'Proveedor Alpha S.A.C.',         'document_number' => '20111222333', 'email' => 'info@alpha.pe',          'phone' => '01 678-9012', 'address' => 'Av. Industrial 1234, Ate'],
            ['name' => 'Importadora Beta E.I.R.L.',      'document_number' => '20222333444', 'email' => 'info@beta-imports.pe',   'phone' => '01 789-0123', 'address' => 'Jr. Ucayali 890, Lima'],
            ['name' => 'Logística Gamma S.A.',            'document_number' => '20333444555', 'email' => 'ops@loggamma.pe',        'phone' => '01 890-1234', 'address' => 'Av. Argentina 1800, Callao'],
            ['name' => 'Servicios Delta S.A.C.',         'document_number' => '20444555666', 'email' => 'admin@servdelta.pe',     'phone' => '01 901-2345', 'address' => 'Calle Los Pinos 220, San Borja'],
            ['name' => 'Tecnología Epsilon S.R.L.',      'document_number' => '20555666777', 'email' => 'soporte@epsilon.tech',   'phone' => '01 012-3456', 'address' => 'Av. Benavides 4550, Miraflores'],
            ['name' => 'Constructora Zeta SAC',          'document_number' => '20666777888', 'email' => 'obras@zeta.pe',          'phone' => '044 12-3456', 'address' => 'Av. Húsares de Junín 320, Trujillo'],
            ['name' => 'Distribuidora Eta E.I.R.L.',     'document_number' => '20777888999', 'email' => 'ventas@eta-distrib.pe',  'phone' => '054 23-4567', 'address' => 'Mercado Mayorista s/n, Arequipa'],
            ['name' => 'Consultora Theta S.A.C.',        'document_number' => '20888999000', 'email' => 'consult@theta.pe',       'phone' => '01 123-4567', 'address' => 'Av. Camino Real 390, San Isidro'],
            ['name' => 'Agencia Iota S.A.',              'document_number' => '20999000111', 'email' => 'info@iota-agency.pe',    'phone' => '01 234-5670', 'address' => 'Jr. de la Unión 548, Lima'],
            ['name' => 'Industrias Kappa SAC',           'document_number' => '20100200300', 'email' => 'planta@kappa-ind.pe',    'phone' => '064 34-5678', 'address' => 'Av. Ferrocarril 1050, Huancayo'],
        ];

        foreach ($companiesData as $co) {
            Company::firstOrCreate(['document_number' => $co['document_number']], $co);
        }

        // ── 10 Productos ─────────────────────────────────────
        $productsData = [
            ['name' => 'Consultoría Tecnológica',        'description' => 'Asesoría en transformación digital y sistemas de información',       'price' => 250.00,  'unit' => 'hr'],
            ['name' => 'Desarrollo de Software a Medida','description' => 'Desarrollo de aplicaciones web y móviles personalizadas',             'price' => 3500.00, 'unit' => 'mes'],
            ['name' => 'Licencia ERP Anual',             'description' => 'Licencia de uso anual del sistema ERP empresarial',                   'price' => 1800.00, 'unit' => 'año'],
            ['name' => 'Mantenimiento de Servidores',    'description' => 'Servicio mensual de mantenimiento y monitoreo de infraestructura',    'price' => 850.00,  'unit' => 'mes'],
            ['name' => 'Capacitación de Usuarios',       'description' => 'Taller presencial o virtual de formación en el uso del sistema',      'price' => 400.00,  'unit' => 'sesión'],
            ['name' => 'Soporte Técnico Premium',        'description' => 'Soporte prioritario 24/7 con SLA garantizado',                        'price' => 600.00,  'unit' => 'mes'],
            ['name' => 'Migración de Base de Datos',     'description' => 'Migración y validación de datos entre sistemas',                      'price' => 2200.00, 'unit' => 'und'],
            ['name' => 'Auditoría de Seguridad',         'description' => 'Evaluación de vulnerabilidades y reporte ejecutivo',                  'price' => 3000.00, 'unit' => 'und'],
            ['name' => 'Diseño UI/UX de Aplicación',    'description' => 'Diseño de interfaces y experiencia de usuario',                        'price' => 1500.00, 'unit' => 'proyecto'],
            ['name' => 'Hosting Cloud Premium (Anual)',  'description' => 'Alojamiento en la nube con 99.9% uptime garantizado',                 'price' => 950.00,  'unit' => 'año'],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $products[] = Product::firstOrCreate(['name' => $p['name']], $p);
        }

        // ── 10 Cotizaciones distribuidas en los últimos 6 meses ──
        // Distribuidas para que los gráficos del dashboard muestren variedad
        $quotationDefs = [
            // [meses_atrás, índice_cliente, [[idx_prod, qty, precio], ...], moneda, estado]
            ['months' => 5, 'client' => 0, 'lines' => [[0,10,250],[4,2,400]],   'currency' => 'PEN', 'status' => 'Aprobada'],
            ['months' => 5, 'client' => 1, 'lines' => [[1,3,3500],[5,2,600]],   'currency' => 'PEN', 'status' => 'Aprobada'],
            ['months' => 4, 'client' => 2, 'lines' => [[2,2,1800],[9,1,950]],   'currency' => 'PEN', 'status' => 'Aprobada'],
            ['months' => 4, 'client' => 3, 'lines' => [[6,1,2200],[3,3,850]],   'currency' => 'PEN', 'status' => 'Rechazada'],
            ['months' => 3, 'client' => 4, 'lines' => [[7,1,3000],[5,4,600]],   'currency' => 'USD', 'status' => 'Aprobada'],
            ['months' => 3, 'client' => 5, 'lines' => [[8,1,1500],[4,2,400]],   'currency' => 'PEN', 'status' => 'Emitida'],
            ['months' => 2, 'client' => 6, 'lines' => [[1,2,3500],[0,20,250]],  'currency' => 'PEN', 'status' => 'Aprobada'],
            ['months' => 2, 'client' => 7, 'lines' => [[3,6,850],[5,3,600]],    'currency' => 'PEN', 'status' => 'Emitida'],
            ['months' => 1, 'client' => 8, 'lines' => [[8,2,1500],[1,1,3500]],  'currency' => 'PEN', 'status' => 'Aprobada'],
            ['months' => 0, 'client' => 9, 'lines' => [[7,1,3000],[4,4,400]],   'currency' => 'PEN', 'status' => 'Borrador'],
        ];

        // Obtener el último número de cotización para no colisionar
        $lastNum = Quotation::count();

        foreach ($quotationDefs as $idx => $qd) {
            $client   = $clients[$qd['client']];
            $date     = now()->subMonths($qd['months'])->subDays(rand(1, 20));
            $dueDate  = $date->copy()->addDays(30);
            $currency = $qd['currency'];
            $taxRate  = 18;
            $num      = str_pad($lastNum + $idx + 1, 4, '0', STR_PAD_LEFT);
            $qNumber  = 'COT-' . $date->year . '-' . $num;

            // Evitar números duplicados
            while (Quotation::where('quotation_number', $qNumber)->exists()) {
                $lastNum++;
                $num     = str_pad($lastNum + $idx + 1, 4, '0', STR_PAD_LEFT);
                $qNumber = 'COT-' . $date->year . '-' . $num;
            }

            $subtotal = 0;
            $lineData = [];
            foreach ($qd['lines'] as [$pi, $qty, $price]) {
                $prod = $products[$pi];
                $lineSubtotal = round($qty * $price, 2);
                $subtotal    += $lineSubtotal;
                $lineData[]   = ['product' => $prod, 'qty' => $qty, 'price' => $price, 'sub' => $lineSubtotal];
            }
            $tax   = round($subtotal * $taxRate / 100, 2);
            $total = round($subtotal + $tax, 2);

            $quotation = Quotation::create([
                'quotation_number' => $qNumber,
                'issue_date'       => $date->toDateString(),
                'due_date'         => $dueDate->toDateString(),
                'client_id'        => $client->id,
                'currency'         => $currency,
                'subtotal'         => $subtotal,
                'discount_amount'  => 0,
                'tax_amount'       => $tax,
                'total'            => $total,
                'status'           => $qd['status'],
                'notes'            => 'Cotización de demostración generada automáticamente.',
                'terms'            => "1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).",
            ]);

            foreach ($lineData as $l) {
                QuotationDetail::create([
                    'quotation_id' => $quotation->id,
                    'product_id'   => $l['product']->id,
                    'product_name' => $l['product']->name,
                    'unit'         => $l['product']->unit,
                    'quantity'     => $l['qty'],
                    'unit_price'   => $l['price'],
                    'discount_pct' => 0,
                    'subtotal'     => $l['sub'],
                ]);
            }
        }

        $this->command->info('✅ DemoDataSeeder completado:');
        $this->command->info('   • ' . count($clientsData)   . ' clientes');
        $this->command->info('   • ' . count($companiesData)  . ' empresas');
        $this->command->info('   • ' . count($productsData)   . ' productos');
        $this->command->info('   • ' . count($quotationDefs)  . ' cotizaciones con sus detalles');
    }
}
