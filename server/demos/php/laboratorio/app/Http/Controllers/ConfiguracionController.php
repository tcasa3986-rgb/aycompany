<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    private array $defaults = [
        'lab_nombre'      => ['valor' => 'LabSalud Clínico',       'tipo' => 'texto',    'descripcion' => 'Nombre del laboratorio'],
        'lab_ruc'         => ['valor' => '',                         'tipo' => 'texto',    'descripcion' => 'RUC del laboratorio'],
        'lab_direccion'   => ['valor' => '',                         'tipo' => 'texto',    'descripcion' => 'Dirección del laboratorio'],
        'lab_telefono'    => ['valor' => '',                         'tipo' => 'texto',    'descripcion' => 'Teléfono de contacto'],
        'lab_email'       => ['valor' => '',                         'tipo' => 'texto',    'descripcion' => 'Correo electrónico'],
        'lab_ciudad'      => ['valor' => 'Lima',                     'tipo' => 'texto',    'descripcion' => 'Ciudad'],
        'igv_porcentaje'  => ['valor' => '18',                       'tipo' => 'numero',   'descripcion' => 'Porcentaje de IGV (%)'],
        'moneda_simbolo'  => ['valor' => 'S/',                       'tipo' => 'texto',    'descripcion' => 'Símbolo de moneda'],
        'dias_entrega'    => ['valor' => '1',                        'tipo' => 'numero',   'descripcion' => 'Días estándar de entrega de resultados'],
        'pie_resultado'   => ['valor' => 'Los resultados son confidenciales y de uso médico exclusivo.', 'tipo' => 'texto', 'descripcion' => 'Texto al pie del reporte de resultados'],
    ];

    public function index()
    {
        $configs = [];
        foreach ($this->defaults as $clave => $data) {
            $config = Configuracion::firstOrCreate(
                ['clave' => $clave],
                ['valor' => $data['valor'], 'tipo' => $data['tipo'], 'descripcion' => $data['descripcion']]
            );
            $configs[$clave] = $config;
        }

        return view('configuracion.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'configs'   => 'required|array',
            'configs.*' => 'nullable|string|max:1000',
        ]);

        foreach ($request->configs as $clave => $valor) {
            Configuracion::set($clave, $valor ?? '');
        }

        return back()->with('success', 'Configuración del sistema guardada correctamente.');
    }
}
