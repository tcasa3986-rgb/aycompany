<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configs = Configuracion::all()->groupBy('grupo');
        return view('configuracion.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $clave => $valor) {
            Configuracion::establecer($clave, $valor);
        }

        Cache::flush();

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
