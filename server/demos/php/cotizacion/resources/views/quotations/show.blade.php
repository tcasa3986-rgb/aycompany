<x-app-layout>
    <x-slot name="title">{{ $quotation->quotation_number }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-secondary">Editar</a>

        {{-- Clonar --}}
        <form method="POST" action="{{ route('quotations.clone', $quotation) }}" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Duplicar
            </button>
        </form>

        {{-- Enviar por Email --}}
        <button type="button" class="btn btn-info" onclick="document.getElementById('emailModal').style.display='flex'">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Enviar Email
        </button>

        {{-- Vista previa PDF --}}
        <a href="{{ route('quotations.preview', $quotation) }}" class="btn btn-secondary" target="_blank">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Vista Previa
        </a>

        {{-- Descargar PDF --}}
        <a href="{{ route('quotations.pdf', $quotation) }}" class="btn btn-success" target="_blank">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Descargar PDF
        </a>

        <form method="POST" action="{{ route('quotations.destroy', $quotation) }}"
              onsubmit="return confirm('¿Eliminar esta cotización?')" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Eliminar</button>
        </form>
    </x-slot>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

        {{-- Detalle principal --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card">
                {{-- Encabezado --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <div>
                        <div style="font-size:22px;font-weight:700;">{{ $quotation->quotation_number }}</div>
                        <div style="margin-top:6px;display:flex;gap:8px;">
                            <span class="badge {{ $quotation->status_color }}">{{ $quotation->status }}</span>
                            <span class="badge badge-amber">{{ $quotation->currency }}</span>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:11px;color:var(--text-muted);">Total</div>
                        <div style="font-size:26px;font-weight:800;color:var(--accent);">
                            {{ $quotation->currency_symbol }} {{ number_format($quotation->total, 2) }}
                        </div>
                    </div>
                </div>

                {{-- Info del cliente y fechas --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:24px;padding:16px;background:rgba(59,130,246,.05);border-radius:8px;border:1px solid rgba(59,130,246,.1);">
                    <div>
                        <div class="form-label">Cliente</div>
                        <div style="font-weight:600">{{ $quotation->client->name }}</div>
                        @if($quotation->client->document_number)
                            <div style="color:var(--text-muted);font-size:12px">{{ $quotation->client->document_number }}</div>
                        @endif
                        @if($quotation->client->email)
                            <div style="color:var(--text-muted);font-size:12px">{{ $quotation->client->email }}</div>
                        @endif
                    </div>
                    <div>
                        <div class="form-label">Fecha de Emisión</div>
                        <div style="font-weight:600">{{ $quotation->issue_date->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="form-label">Vencimiento</div>
                        <div style="font-weight:600">{{ $quotation->due_date ? $quotation->due_date->format('d/m/Y') : '—' }}</div>
                        @if($quotation->due_date && $quotation->due_date->isPast() && $quotation->status === 'Emitida')
                            <div style="color:#f87171;font-size:11px;font-weight:600;">⚠ Vencida</div>
                        @endif
                    </div>
                </div>

                {{-- Ítems --}}
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th style="text-align:right">Cant.</th>
                                <th style="text-align:right">P. Unit.</th>
                                <th style="text-align:right">Dto. %</th>
                                <th style="text-align:right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotation->details as $i => $d)
                            <tr>
                                <td style="color:var(--text-muted)">{{ $i + 1 }}</td>
                                <td style="font-weight:500">{{ $d->product_name }}</td>
                                <td style="color:var(--text-muted)">{{ $d->unit ?: '—' }}</td>
                                <td style="text-align:right">{{ rtrim(rtrim(number_format($d->quantity, 3), '0'), '.') }}</td>
                                <td style="text-align:right">{{ $quotation->currency_symbol }} {{ number_format($d->unit_price, 2) }}</td>
                                <td style="text-align:right">
                                    @if($d->discount_pct > 0)
                                        <span class="badge badge-amber">{{ $d->discount_pct }}%</span>
                                    @else
                                        <span style="color:var(--text-muted)">—</span>
                                    @endif
                                </td>
                                <td style="text-align:right;font-weight:600">
                                    {{ $quotation->currency_symbol }} {{ number_format($d->subtotal, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totales --}}
                <div style="margin-top:16px;border-top:1px solid var(--border);padding-top:16px;display:flex;justify-content:flex-end;">
                    <div style="min-width:280px;display:flex;flex-direction:column;gap:8px;">
                        @php $sym = $quotation->currency_symbol; @endphp
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--text-muted)">Subtotal bruto</span>
                            <span>{{ $sym }} {{ number_format($quotation->subtotal, 2) }}</span>
                        </div>
                        @if($quotation->discount_amount > 0)
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--text-muted)">Descuento</span>
                            <span style="color:#f87171">- {{ $sym }} {{ number_format($quotation->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--text-muted)">IGV</span>
                            <span>{{ $sym }} {{ number_format($quotation->tax_amount, 2) }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;border-top:1px solid var(--border);padding-top:8px;">
                            <span style="font-weight:700;font-size:16px;">Total</span>
                            <span style="font-weight:800;font-size:22px;color:var(--accent)">
                                {{ $sym }} {{ number_format($quotation->total, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Notas --}}
                @if($quotation->notes)
                <div style="margin-top:16px;padding:14px;background:rgba(148,163,184,.06);border-radius:8px;border:1px solid var(--border);">
                    <div class="form-label">Notas</div>
                    <div style="color:var(--text-muted);font-size:13px;white-space:pre-line;">{{ $quotation->notes }}</div>
                </div>
                @endif

                {{-- T&C --}}
                @if($quotation->terms)
                <div style="margin-top:12px;padding:14px;background:rgba(148,163,184,.06);border-radius:8px;border:1px solid var(--border);">
                    <div class="form-label">Términos y Condiciones</div>
                    <div style="color:var(--text-muted);font-size:12px;white-space:pre-line;">{{ $quotation->terms }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Panel lateral — Estado --}}
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-header"><span class="card-title">Cambiar Estado</span></div>
            @php
                $transitions = [
                    'Borrador'  => ['Emitida'],
                    'Emitida'   => ['Aprobada', 'Rechazada'],
                    'Aprobada'  => [],
                    'Rechazada' => ['Borrador'],
                ];
                $available = $transitions[$quotation->status] ?? [];
            @endphp

            <div style="margin-bottom:16px;">
                <div class="form-label">Estado actual</div>
                <span class="badge {{ $quotation->status_color }}" style="font-size:13px;padding:6px 14px;">
                    {{ $quotation->status }}
                </span>
            </div>

            @foreach($available as $newStatus)
                @php
                    $color = match($newStatus) {
                        'Aprobada'  => 'btn-success',
                        'Rechazada' => 'btn-danger',
                        'Emitida'   => 'btn-primary',
                        default     => 'btn-secondary',
                    };
                @endphp
                <form method="POST" action="{{ route('quotations.status', $quotation) }}" style="margin-bottom:8px;">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $newStatus }}">
                    <button type="submit" class="btn {{ $color }}" style="width:100%;justify-content:center;">
                        Marcar como {{ $newStatus }}
                    </button>
                </form>
            @endforeach

            @if(!count($available))
                <p style="color:var(--text-muted);font-size:12px;">No hay transiciones disponibles.</p>
            @endif

            {{-- Empresa emisora --}}
            <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:16px;">
                <div class="form-label" style="margin-bottom:8px;">Emitido por</div>
                <div style="font-size:13px;font-weight:600;">{{ $company['company_name'] }}</div>
                @if($company['company_ruc'])
                <div style="font-size:11px;color:var(--text-muted);">RUC: {{ $company['company_ruc'] }}</div>
                @endif
                @if($company['company_email'])
                <div style="font-size:11px;color:var(--text-muted);">{{ $company['company_email'] }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ EMAIL MODAL ══ --}}
    <div id="emailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:14px;padding:32px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div style="font-size:16px;font-weight:700;color:var(--text-main);">
                    Enviar Cotización por Email
                </div>
                <button onclick="document.getElementById('emailModal').style.display='none'"
                        style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--text-muted);">✕</button>
            </div>
            <form method="POST" action="{{ route('quotations.email', $quotation) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Destinatario *</label>
                    <input type="email" name="to_email" class="form-control" required
                           value="{{ $quotation->client->email ?? '' }}"
                           placeholder="cliente@empresa.com">
                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">
                        Se adjuntará el PDF de la cotización automáticamente.
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Mensaje Personalizado (opcional)</label>
                    <textarea name="custom_message" class="form-control" rows="3"
                              placeholder="Estimado cliente, adjuntamos la cotización solicitada..."></textarea>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary"
                            onclick="document.getElementById('emailModal').style.display='none'">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Enviar Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
