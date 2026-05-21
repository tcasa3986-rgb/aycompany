<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_PE');

        // 1. Agregar 10 Clientes
        $newClients = [];
        for ($i = 0; $i < 10; $i++) {
            $newClients[] = Client::create([
                'name' => $faker->company . ' ' . $faker->companySuffix,
                'document_number' => $faker->numerify('20#########'),
                'email' => $faker->unique()->companyEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ]);
        }

        // 2. Agregar 10 Empresas
        for ($i = 0; $i < 10; $i++) {
            Company::create([
                'name' => $faker->company . ' (Subsidiaria)',
                'document_number' => $faker->numerify('20#########'),
                'email' => 'subsidiaria' . $i . '@' . $faker->safeEmailDomain,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ]);
        }

        // 3. Agregar 10 Productos
        $newProducts = [];
        $unidades = ['und', 'kg', 'lt', 'mt', 'hr', 'mes', 'caja', 'srv'];
        for ($i = 0; $i < 10; $i++) {
            $newProducts[] = Product::create([
                'name' => 'Producto / Servicio Demo ' . strtoupper($faker->bothify('?-###')),
                'description' => $faker->sentence,
                'price' => $faker->randomFloat(2, 50, 5000),
                'unit' => $faker->randomElement($unidades),
            ]);
        }

        // 4. Agregar 10 Cotizaciones (distribuidas en los últimos 6 meses)
        $statuses = ['Borrador', 'Emitida', 'Aprobada', 'Rechazada'];
        $currencies = ['PEN', 'USD', 'EUR'];
        
        // Obtener prefijo y número actual para no sobreescribir (asumiendo formato COT-YYYY-NNNN)
        $latestQuotation = Quotation::latest('id')->first();
        $lastNum = 100; // base si no existe
        if ($latestQuotation && preg_match('/-(\d+)$/', $latestQuotation->quotation_number, $matches)) {
            $lastNum = (int) $matches[1];
        }

        for ($i = 0; $i < 10; $i++) {
            $client = $faker->randomElement($newClients); // usar los nuevos para que tengan data
            $date = now()->subMonths(rand(0, 5))->subDays(rand(0, 25));
            $dueDate = $date->copy()->addDays(30);
            $currency = $faker->randomElement($currencies);
            $status = $faker->randomElement($statuses);
            
            $num = str_pad(++$lastNum, 4, '0', STR_PAD_LEFT);
            $qNumber = "COT-{$date->year}-{$num}";

            $taxRate = 18;
            $subtotal = 0;
            
            // Cada cotización tendrá entre 1 y 4 detalles
            $details = [];
            $numDetails = rand(1, 4);
            for ($d = 0; $d < $numDetails; $d++) {
                $product = $faker->randomElement($newProducts);
                $qty = rand(1, 10);
                $lineSubtotal = round($qty * $product->price, 2);
                $subtotal += $lineSubtotal;
                
                $details[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'discount_pct' => 0,
                    'subtotal' => $lineSubtotal
                ];
            }
            
            $taxAmount = round($subtotal * $taxRate / 100, 2);
            $total = round($subtotal + $taxAmount, 2);

            $quotation = Quotation::create([
                'quotation_number' => $qNumber,
                'issue_date' => $date->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'client_id' => $client->id,
                'currency' => $currency,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'status' => $status,
                'notes' => 'Generado por Faker Seeder',
                'terms' => "Términos de demostración.\nGenerado automáticamente.",
            ]);

            foreach ($details as $detail) {
                $detail['quotation_id'] = $quotation->id;
                QuotationDetail::create($detail);
            }
        }
    }
}
