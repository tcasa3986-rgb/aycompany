@extends('layouts.app')
@section('title', 'Leer Mensaje')
@section('page-title', 'Mensaje')

@section('content')
<div style="max-width:760px;">

<div style="display:flex;gap:12px;margin-bottom:20px;">
    <a href="{{ route('mensajes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Bandeja</a>
    <a href="{{ route('mensajes.create') }}?reply={{ $mensaje->remitente_id }}" class="btn btn-primary">
        <i class="fas fa-reply"></i> Responder
    </a>
    <form method="POST" action="{{ route('mensajes.destroy', $mensaje) }}" style="margin-left:auto;">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Archivar este mensaje?')">
            <i class="fas fa-archive"></i> Archivar
        </button>
    </form>
</div>

<div class="card">
    {{-- Cabecera --}}
    <div style="padding:24px 28px;border-bottom:1px solid var(--border);">
        <div style="font-size:20px;font-weight:700;margin-bottom:16px;">{{ $mensaje->asunto }}</div>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:16px;">
                    {{ strtoupper(substr($mensaje->remitente->name??'U',0,1)) }}
                </div>
                <div>
                    <div style="font-weight:600;">{{ $mensaje->remitente->name ?? '—' }}</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $mensaje->remitente->email ?? '' }}</div>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:12px;color:var(--muted);">Para: <strong>{{ $mensaje->destinatario->name ?? '—' }}</strong></div>
                <div style="font-size:12px;color:var(--muted);">{{ $mensaje->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Cuerpo --}}
    <div style="padding:28px;min-height:200px;font-size:14.5px;line-height:1.75;color:var(--text);white-space:pre-wrap;">{{ $mensaje->cuerpo }}</div>

    {{-- Footer --}}
    <div style="padding:16px 28px;border-top:1px solid var(--border);background:#f8fafc;border-radius:0 0 16px 16px;">
        <div style="font-size:12px;color:var(--muted);">
            @if($mensaje->leido)
                <i class="fas fa-check-double" style="color:var(--success);"></i>
                Leído el {{ $mensaje->leido_en?->format('d/m/Y H:i') }}
            @else
                <i class="fas fa-clock"></i> No leído aún
            @endif
        </div>
    </div>
</div>

</div>
@endsection
