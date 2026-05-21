<?php

namespace App\Http\Controllers;

use App\Models\Interaccion;
use Illuminate\Http\Request;

class PosInteractionController extends Controller
{
    public function check(Request $request)
    {
        $principios = $request->get('principios', []);
        
        if (!is_array($principios) || count($principios) < 2) {
            return response()->json([]);
        }

        $interacciones = Interaccion::buscarEntre($principios);

        return response()->json($interacciones);
    }
}
