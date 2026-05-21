<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function index()
    {
        $recibidos = Mensaje::where('destinatario_id', auth()->id())
            ->where('archivado', false)
            ->with('remitente')
            ->latest()
            ->paginate(15);

        $enviados = Mensaje::where('remitente_id', auth()->id())
            ->with('destinatario')
            ->latest()
            ->take(5)
            ->get();

        $noLeidos = Mensaje::where('destinatario_id', auth()->id())
            ->where('leido', false)
            ->count();

        return view('messages.index', compact('recibidos', 'enviados', 'noLeidos'));
    }

    public function create()
    {
        $usuarios = User::where('id', '!=', auth()->id())->where('activo', true)->get();
        return view('messages.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destinatario_id' => 'required|exists:users,id',
            'asunto'          => 'required|string|max:200',
            'cuerpo'          => 'required|string',
        ]);

        Mensaje::create([
            'remitente_id'    => auth()->id(),
            'destinatario_id' => $request->destinatario_id,
            'asunto'          => $request->asunto,
            'cuerpo'          => $request->cuerpo,
        ]);

        return redirect()->route('mensajes.index')
            ->with('success', 'Mensaje enviado correctamente.');
    }

    public function show(Mensaje $mensaje)
    {
        if ($mensaje->destinatario_id === auth()->id()) {
            $mensaje->marcarLeido();
        }
        $mensaje->load(['remitente', 'destinatario']);
        return view('messages.show', compact('mensaje'));
    }

    public function destroy(Mensaje $mensaje)
    {
        $mensaje->update(['archivado' => true]);
        return redirect()->route('mensajes.index')
            ->with('success', 'Mensaje archivado.');
    }
}
