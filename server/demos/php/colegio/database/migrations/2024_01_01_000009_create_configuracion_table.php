<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 80)->unique();
            $table->text('valor')->nullable();
            $table->string('descripcion', 200)->nullable();
            $table->string('grupo', 50)->default('general');
            $table->timestamps();
        });

        // Valores por defecto del sistema
        $defaults = [
            ['clave' => 'colegio_nombre',    'valor' => 'Colegio CRM',                'grupo' => 'colegio',  'descripcion' => 'Nombre del colegio'],
            ['clave' => 'colegio_ruc',       'valor' => '20123456789',                'grupo' => 'colegio',  'descripcion' => 'RUC de la institución'],
            ['clave' => 'colegio_direccion', 'valor' => 'Av. Principal 123, Lima',    'grupo' => 'colegio',  'descripcion' => 'Dirección del colegio'],
            ['clave' => 'colegio_telefono',  'valor' => '(01) 234-5678',              'grupo' => 'colegio',  'descripcion' => 'Teléfono principal'],
            ['clave' => 'colegio_email',     'valor' => 'info@colegio.edu.pe',        'grupo' => 'colegio',  'descripcion' => 'Email institucional'],
            ['clave' => 'colegio_director',  'valor' => 'Director General',           'grupo' => 'colegio',  'descripcion' => 'Nombre del director'],
            ['clave' => 'anio_escolar',      'valor' => date('Y'),                    'grupo' => 'sistema',  'descripcion' => 'Año escolar activo'],
            ['clave' => 'nota_minima',       'valor' => '11',                         'grupo' => 'academico','descripcion' => 'Nota mínima para aprobar'],
            ['clave' => 'nota_maxima',       'valor' => '20',                         'grupo' => 'academico','descripcion' => 'Nota máxima del sistema'],
            ['clave' => 'num_bimestres',     'valor' => '4',                          'grupo' => 'academico','descripcion' => 'Número de bimestres'],
            ['clave' => 'moneda',            'valor' => 'S/.',                        'grupo' => 'sistema',  'descripcion' => 'Símbolo de moneda'],
            ['clave' => 'logo_url',          'valor' => '',                           'grupo' => 'colegio',  'descripcion' => 'URL del logo institucional'],
        ];

        foreach ($defaults as $item) {
            \DB::table('configuracion')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};
