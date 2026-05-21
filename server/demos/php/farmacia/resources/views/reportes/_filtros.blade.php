<form method="GET" action="{{ route($route) }}" class="flex flex-wrap items-end gap-3 mb-4">
    <div>
        <label class="label">Desde</label>
        <input type="date" name="desde" value="{{ request('desde', isset($desde) ? $desde->format('Y-m-d') : '') }}" class="input">
    </div>
    <div>
        <label class="label">Hasta</label>
        <input type="date" name="hasta" value="{{ request('hasta', isset($hasta) ? $hasta->format('Y-m-d') : '') }}" class="input">
    </div>
    <button type="submit" class="btn-primary">Aplicar</button>
    <a href="{{ route($route, array_merge(request()->only('desde','hasta'), ['export' => 'pdf'])) }}" class="btn-secondary">📄 PDF</a>
    <a href="{{ route($route, array_merge(request()->only('desde','hasta'), ['export' => 'excel'])) }}" class="btn-secondary">📊 Excel</a>
</form>
