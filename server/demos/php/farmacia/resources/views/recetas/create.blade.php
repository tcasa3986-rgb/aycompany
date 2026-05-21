@extends('layouts.app')
@section('title', 'Nueva receta')
@section('section', 'Recetas')

@section('content')
<form method="POST" action="{{ route('recetas.store') }}" id="recetaForm">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="card card-pad lg:col-span-2 space-y-4">
            <h2 class="text-lg font-semibold text-gray-700">Datos de la receta</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Paciente</label>
                    <select name="cliente_id" class="input">
                        <option value="">— Cliente genérico —</option>
                        @foreach ($clientes as $c)
                            <option value="{{ $c->id }}">{{ trim($c->nombres . ' ' . $c->apellidos) }} @if($c->documento) · {{ $c->documento }} @endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Fecha *</label>
                    <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" class="input" required>
                </div>
                <div>
                    <label class="label">Médico *</label>
                    <input type="text" name="medico" class="input" required placeholder="Dr. Juan Pérez">
                </div>
                <div>
                    <label class="label">Especialidad</label>
                    <input type="text" name="especialidad" class="input" placeholder="Cardiología, pediatría...">
                </div>
                <div>
                    <label class="label">CMP</label>
                    <input type="text" name="cmp" class="input" placeholder="Colegio Médico">
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="retenida" value="1" class="rounded text-farmacia-500 focus:ring-farmacia-400">
                        <span class="ml-2">Receta retenida (medicamento controlado)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="label">Diagnóstico</label>
                <textarea name="diagnostico" rows="2" class="input"></textarea>
            </div>
            <div>
                <label class="label">Observaciones</label>
                <textarea name="observaciones" rows="2" class="input"></textarea>
            </div>

            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mt-4">Medicamentos prescritos</h3>

            <div class="flex gap-2 items-end">
                <div class="flex-1">
                    <select id="prodSel" class="input">
                        <option value="">— Selecciona un producto —</option>
                        @foreach ($productos as $p)
                            <option value="{{ $p->id }}"
                                    data-nombre="{{ $p->nombre }}{{ $p->concentracion ? ' · ' . $p->concentracion : '' }}"
                                    data-receta="{{ $p->requiere_receta ? '1' : '0' }}">
                                {{ $p->codigo }} · {{ $p->nombre }} @if($p->concentracion) · {{ $p->concentracion }} @endif
                                @if($p->requiere_receta) ⚠ controlado @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btnAdd" class="btn-secondary"><x-icon name="plus" class="h-4 w-4 mr-1" />Agregar</button>
            </div>

            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr>
                        <th>Medicamento</th>
                        <th class="w-24">Cant.</th>
                        <th>Indicaciones</th>
                        <th></th>
                    </tr></thead>
                    <tbody id="rows">
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">Sin medicamentos agregados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card-pad h-fit">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Acciones</h2>
            <div class="space-y-2">
                <button type="submit" class="btn-primary w-full">Guardar receta</button>
                <a href="{{ route('recetas.index') }}" class="btn-secondary text-center w-full block">Cancelar</a>
            </div>
            <p class="text-xs text-gray-400 mt-4">Los medicamentos marcados como ⚠ requieren receta retenida obligatoria.</p>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const $ = id => document.getElementById(id);
    const tbody = $('rows');
    let i = 0;

    $('btnAdd').addEventListener('click', () => {
        const sel = $('prodSel');
        const opt = sel.options[sel.selectedIndex];
        if (!opt.value) return;

        const idx = i++;
        if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';

        const tr = document.createElement('tr');
        const controlado = opt.dataset.receta === '1';
        tr.innerHTML = `
            <td class="font-medium text-gray-800">
                ${opt.dataset.nombre}
                ${controlado ? '<span class="badge bg-rose-50 text-rose-700 ml-2">Controlado</span>' : ''}
                <input type="hidden" name="items[${idx}][producto_id]" value="${opt.value}">
            </td>
            <td><input type="number" name="items[${idx}][cantidad]" min="1" value="1" class="input w-20 text-right"></td>
            <td><input type="text" name="items[${idx}][indicaciones]" class="input" placeholder="Ej. 1 tableta cada 8h por 7 días"></td>
            <td class="text-right"><button type="button" class="text-rose-500 text-xs hover:underline rm">Quitar</button></td>`;
        tbody.appendChild(tr);
        tr.querySelector('.rm').addEventListener('click', () => {
            tr.remove();
            if (!tbody.children.length) tbody.innerHTML = '<tr><td colspan="4" class="py-6 text-center text-gray-400">Sin medicamentos agregados</td></tr>';
        });

        if (controlado && ! document.querySelector('input[name="retenida"]').checked) {
            document.querySelector('input[name="retenida"]').checked = true;
        }
        sel.value = '';
    });
})();
</script>
@endpush
