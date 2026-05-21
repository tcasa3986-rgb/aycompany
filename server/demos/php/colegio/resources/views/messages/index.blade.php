@extends('layouts.app')
@section('title', 'Mensajes')
@section('page-title', 'Bandeja de Mensajes')

@section('content')

<div style="display:flex;gap:20px;">

{{-- Sidebar mensajes --}}
<div style="width:240px;flex-shrink:0;">
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body" style="padding:16px;">
            <a href="{{ route('mensajes.create') }}" class="btn btn-primary" style="width:100%;justify-content:center;">
                <i class="fas fa-pen"></i> Nuevo Mensaje
            </a>
        </div>
    </div>
    <div class="card">
        <div style="padding:8px 0;">
            <a href="{{ route('mensajes.index') }}" class="menu-item active" style="border-left:3px solid #3b82f6;background:rgba(59,130,246,0.08);color:#1e3a8a;padding:12px 20px;display:flex;align-items:center;gap:10px;text-decoration:none;font-size:13.5px;font-weight:600;">
                <i class="fas fa-inbox" style="width:18px;text-align:center;"></i> Recibidos
                @if($noLeidos > 0)
                    <span class="badge badge-danger" style="margin-left:auto;">{{ $noLeidos }}</span>
                @endif
            </a>
            <div style="padding:12px 20px;display:flex;align-items:center;gap:10px;color:var(--muted);font-size:13.5px;">
                <i class="fas fa-paper-plane" style="width:18px;text-align:center;"></i> Enviados
            </div>
        </div>
    </div>
</div>

{{-- Lista de mensajes --}}
<div style="flex:1;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Mensajes Recibidos</span>
            @if($noLeidos > 0)
                <span class="badge badge-danger">{{ $noLeidos }} sin leer</span>
            @endif
        </div>

        @forelse($recibidos as $msg)
            <a href="{{ route('mensajes.show', $msg) }}"
               style="display:flex;align-items:flex-start;gap:14px;padding:16px 22px;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text);transition:background .15s;{{ !$msg->leido ? 'background:#f0f7ff;' : '' }}">
                <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:15px;flex-shrink:0;">
                    {{ strtoupper(substr($msg->remitente->name ?? 'U', 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span style="font-weight:{{ $msg->leido ? '500' : '700' }};font-size:14px;">
                            {{ $msg->remitente->name ?? '—' }}
                        </span>
                        <span style="font-size:11px;color:var(--muted);white-space:nowrap;">
                            {{ $msg->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div style="font-size:13.5px;font-weight:{{ $msg->leido ? '400' : '600' }};margin-bottom:3px;">
                        {{ $msg->asunto }}
                    </div>
                    <div style="font-size:12px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ Str::limit(strip_tags($msg->cuerpo), 80) }}
                    </div>
                </div>
                @if(!$msg->leido)
                    <div style="width:8px;height:8px;background:#3b82f6;border-radius:50%;flex-shrink:0;margin-top:6px;"></div>
                @endif
            </a>
        @empty
            <div style="text-align:center;padding:64px;color:var(--muted);">
                <i class="fas fa-inbox" style="font-size:48px;opacity:.2;display:block;margin-bottom:16px;"></i>
                <p style="font-size:15px;">Tu bandeja está vacía</p>
            </div>
        @endforelse

        @if($recibidos->hasPages())
        <div style="padding:16px 22px;border-top:1px solid var(--border);">
            {{ $recibidos->links() }}
        </div>
        @endif
    </div>
</div>

</div>
@endsection
