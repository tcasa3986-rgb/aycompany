<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configs = Configuracion::allAgrupadas();
        return view('configuracion.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method', 'logo']);

        // Guardar cada valor de texto
        foreach ($data as $clave => $valor) {
            Configuracion::set($clave, $valor);
        }

        // Manejar subida de logo
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,svg|max:2048'
            ]);

            $path = $request->file('logo')->store('public/logos');
            $url = Storage::url($path);
            
            Configuracion::set('logo_url', $url);
        }

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
