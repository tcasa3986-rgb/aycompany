<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user para demo ─────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Administrador', 'password' => 'password123']
        );

        // ── Settings ────────────────────────────────────────
        $defaults = [
            'company_name'         => 'Mi Empresa S.A.C.',
            'company_ruc'          => '20512345678',
            'company_address'      => 'Av. Javier Prado Este 4200, Lima',
            'company_phone'        => '+51 01 234-5678',
            'company_email'        => 'ventas@miempresa.com.pe',
            'company_website'      => 'https://www.miempresa.com.pe',
            'default_currency'     => 'PEN',
            'default_tax_rate'     => '18',
            'quotation_prefix'     => 'COT',
            'terms_and_conditions' => "1. Los precios son válidos por 30 días desde la fecha de emisión.\n2. El plazo de entrega se coordinará al confirmar el pedido.\n3. Los pagos se realizarán según lo acordado entre las partes.\n4. Los precios incluyen IGV (18%) salvo indicación contraria.",
        ];
        foreach ($defaults as $k => $v) Setting::set($k, $v);

        // ── Clients ─────────────────────────────────────────
        $clients = [
            ['name'=>'Corporación Andina S.A.C.',   'document_number'=>'20100102475','email'=>'compras@andina.pe',    'phone'=>'01 234-5678','address'=>'Jr. Camaná 410, Lima'],
            ['name'=>'Tech Solutions Perú S.R.L.', 'document_number'=>'20523456789','email'=>'admin@techsol.pe',     'phone'=>'01 987-6543','address'=>'Av. Larco 1301, Miraflores'],
            ['name'=>'Distribuidora El Sol E.I.R.L.','document_number'=>'20234567890','email'=>'gerencia@elsol.pe',   'phone'=>'044 23-4567','address'=>'Av. España 1801, Trujillo'],
            ['name'=>'Constructora Lima Norte SAC', 'document_number'=>'20345678901','email'=>'obras@limanorte.pe',   'phone'=>'01 345-6789','address'=>'Av. Túpac Amaru 2850, Comas'],
            ['name'=>'Agro Export Ica S.A.',         'document_number'=>'20456789012','email'=>'export@agroica.pe',   'phone'=>'056 23-4567','address'=>'Panamericana Sur km 305, Ica'],
            ['name'=>'Inversiones Pacífico S.A.C.',  'document_number'=>'20567890123','email'=>'finanzas@pacifico.pe','phone'=>'01 456-7890','address'=>'Av. del Ejército 900, San Isidro'],
            ['name'=>'Clínica San Felipe S.A.',      'document_number'=>'20678901234','email'=>'logistica@sanfelipe.pe','phone'=>'01 567-8901','address'=>'Av. Gregorio Escobedo 650, Lima'],
        ];
        foreach ($clients as $c) Client::create($c);

        // ── Companies ────────────────────────────────────────
        Company::create(['name'=>'Proveedor Alpha S.A.C.','document_number'=>'20789012345','email'=>'info@alpha.pe','phone'=>'01 678-9012','address'=>'Av. Industrial 1234, Ate']);

        // ── Products ─────────────────────────────────────────
        $products = [
            ['name'=>'Servicio de Consultoría Tecnológica', 'description'=>'Asesoría en transformación digital y sistemas de información','price'=>250.00,'unit'=>'hr'],
            ['name'=>'Desarrollo de Software a Medida',     'description'=>'Desarrollo de aplicaciones web y móviles personalizadas','price'=>3500.00,'unit'=>'mes'],
            ['name'=>'Licencia Software ERP (Anual)',        'description'=>'Licencia de uso anual del sistema ERP empresarial','price'=>1800.00,'unit'=>'año'],
            ['name'=>'Mantenimiento de Servidores',          'description'=>'Servicio mensual de mantenimiento y monitoreo de infraestructura','price'=>850.00,'unit'=>'mes'],
            ['name'=>'Capacitación de Usuarios',             'description'=>'Taller presencial o virtual de formación en el uso del sistema','price'=>400.00,'unit'=>'sesión'],
            ['name'=>'Soporte Técnico Premium',              'description'=>'Soporte prioritario 24/7 con SLA garantizado','price'=>600.00,'unit'=>'mes'],
            ['name'=>'Migración de Base de Datos',           'description'=>'Migración y validación de datos entre sistemas','price'=>2200.00,'unit'=>'und'],
            ['name'=>'Auditoría de Seguridad Informática',   'description'=>'Evaluación de vulnerabilidades y reporte ejecutivo','price'=>3000.00,'unit'=>'und'],
            ['name'=>'Diseño UI/UX de Aplicación',           'description'=>'Diseño de interfaces y experiencia de usuario','price'=>1500.00,'unit'=>'proyecto'],
            ['name'=>'Hosting Cloud Premium (Anual)',         'description'=>'Alojamiento en la nube con 99.9% uptime garantizado','price'=>950.00,'unit'=>'año'],
        ];
        foreach ($products as $p) Product::create($p);

        // ── Quotations (últimos 6 meses) ─────────────────────
        $allClients  = Client::all();
        $allProducts = Product::all();
        $statuses    = ['Borrador','Emitida','Aprobada','Aprobada','Rechazada'];
        $currencies  = ['PEN','PEN','PEN','USD'];

        $quotationData = [
            // Mes -5
            ['months_ago'=>5,'client'=>0,'products'=>[[0,10,250],[4,2,400]],'taxRate'=>18,'currency'=>'PEN','status'=>'Aprobada'],
            ['months_ago'=>5,'client'=>1,'products'=>[[1,3,3500],[5,2,600]],'taxRate'=>18,'currency'=>'PEN','status'=>'Aprobada'],
            // Mes -4
            ['months_ago'=>4,'client'=>2,'products'=>[[2,2,1800],[9,1,950]],'taxRate'=>18,'currency'=>'PEN','status'=>'Aprobada'],
            ['months_ago'=>4,'client'=>3,'products'=>[[6,1,2200],[3,3,850]],'taxRate'=>18,'currency'=>'PEN','status'=>'Rechazada'],
            ['months_ago'=>4,'client'=>4,'products'=>[[0,8,250],[4,3,400]],'taxRate'=>18,'currency'=>'USD','status'=>'Aprobada'],
            // Mes -3
            ['months_ago'=>3,'client'=>5,'products'=>[[7,1,3000],[5,4,600]],'taxRate'=>18,'currency'=>'PEN','status'=>'Aprobada'],
            ['months_ago'=>3,'client'=>6,'products'=>[[8,1,1500],[4,2,400]],'taxRate'=>18,'currency'=>'PEN','status'=>'Emitida'],
            ['months_ago'=>3,'client'=>0,'products'=>[[1,2,3500],[0,20,250]],'taxRate'=>18,'currency'=>'PEN','status'=>'Rechazada'],
            // Mes -2
            ['months_ago'=>2,'client'=>1,'products'=>[[2,3,1800],[9,2,950]],'taxRate'=>18,'currency'=>'PEN','status'=>'Aprobada'],
            ['months_ago'=>2,'client'=>2,'products'=>[[3,6,850],[5,3,600]],'taxRate'=>18,'currency'=>'PEN','status'=>'Aprobada'],
            ['months_ago'=>2,'client'=>3,'products'=>[[7,1,3000],[6,1,2200]],'taxRate'=>18,'currency'=>'USD','status'=>'Emitida'],
            ['months_ago'=>2,'client'=>4,'products'=>[[0,15,250],[4,5,400]],'taxRate'=>18,'currency'=>'PEN','status'=>'Borrador'],
            // Mes -1
            ['months_ago'=>1,'client'=>5,'products'=>[[8,2,1500],[1,1,3500]],'taxRate'=>18,'currency'=>'PEN','status'=>'Aprobada'],
            ['months_ago'=>1,'client'=>6,'products'=>[[9,3,950],[2,2,1800]],'taxRate'=>18,'currency'=>'PEN','status'=>'Emitida'],
            ['months_ago'=>1,'client'=>0,'products'=>[[5,6,600],[0,12,250]],'taxRate'=>18,'currency'=>'PEN','status'=>'Borrador'],
            // Mes actual
            ['months_ago'=>0,'client'=>1,'products'=>[[7,1,3000],[4,4,400]],'taxRate'=>18,'currency'=>'PEN','status'=>'Emitida'],
            ['months_ago'=>0,'client'=>2,'products'=>[[1,2,3500],[3,4,850]],'taxRate'=>18,'currency'=>'PEN','status'=>'Borrador'],
            ['months_ago'=>0,'client'=>3,'products'=>[[6,1,2200],[5,5,600]],'taxRate'=>18,'currency'=>'USD','status'=>'Emitida'],
            ['months_ago'=>0,'client'=>4,'products'=>[[8,1,1500],[9,1,950]],'taxRate'=>18,'currency'=>'PEN','status'=>'Borrador'],
            ['months_ago'=>0,'client'=>5,'products'=>[[0,20,250],[2,1,1800]],'taxRate'=>18,'currency'=>'PEN','status'=>'Emitida'],
        ];

        foreach ($quotationData as $idx => $qd) {
            $client   = $allClients[$qd['client']];
            $date     = now()->subMonths($qd['months_ago'])->subDays(rand(0,25));
            $dueDate  = $date->copy()->addDays(30);
            $currency = $qd['currency'];
            $taxRate  = $qd['taxRate'];
            $prefix   = 'COT';
            $year     = $date->year;
            $num      = str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
            $qNumber  = "{$prefix}-{$year}-{$num}";

            $subtotal = 0;
            $lineData = [];
            foreach ($qd['products'] as [$pi, $qty, $price]) {
                $prod = $allProducts[$pi];
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
                'notes'            => 'Cotización generada como demostración del sistema.',
                'terms'            => "1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV ({$taxRate}%).",
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
    }
}
