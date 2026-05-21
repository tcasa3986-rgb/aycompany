{{-- Componente de búsqueda global --}}
<div x-data="globalSearch()">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input x-model="query" @input.debounce.300ms="search()" @keydown.escape="clearSearch()"
            @focus="showResults = true" type="text" placeholder="Buscar equipos, empleados... (Ctrl+K)"
            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">

        {{-- Results Dropdown --}}
        <div x-show="showResults && (results.equipos?.length > 0 || results.empleados?.length > 0 || results.asignaciones?.length > 0 || loading)"
            x-cloak @click.away="showResults = false"
            class="absolute z-50 w-full mt-2 bg-gray-800 border border-gray-700 rounded-lg shadow-2xl max-h-96 overflow-y-auto">

            {{-- Loading --}}
            <div x-show="loading" class="px-4 py-3 text-gray-400 text-center">
                <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>

            {{-- Equipos --}}
            <div x-show="results.equipos?.length > 0">
                <div class="px-4 py-2 bg-gray-750 border-b border-gray-700">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase">Equipos</h3>
                </div>
                <template x-for="item in results.equipos" :key="item.id">
                    <a :href="item.url" class="block px-4 py-3 hover:bg-gray-700 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white font-medium" x-text="item.titulo"></p>
                                <p class="text-gray-400 text-sm" x-text="item.subtitulo"></p>
                            </div>
                            <span :class="{
                                'px-2 py-1 rounded-full text-xs font-semibold': true,
                                'bg-green-500/20 text-green-400': item.estado === 'Disponible',
                                'bg-blue-500/20 text-blue-400': item.estado === 'Asignado',
                                'bg-orange-500/20 text-orange-400': item.estado === 'En Reparacion',
                                'bg-red-500/20 text-red-400': item.estado === 'De Baja'
                            }" x-text="item.estado"></span>
                        </div>
                    </a>
                </template>
            </div>

            {{-- Empleados --}}
            <div x-show="results.empleados?.length > 0">
                <div class="px-4 py-2 bg-gray-750 border-b border-gray-700">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase">Empleados</h3>
                </div>
                <template x-for="item in results.empleados" :key="item.id">
                    <a :href="item.url" class="block px-4 py-3 hover:bg-gray-700 transition">
                        <p class="text-white font-medium" x-text="item.titulo"></p>
                        <p class="text-gray-400 text-sm" x-text="item.subtitulo"></p>
                    </a>
                </template>
            </div>

            {{-- Asignaciones --}}
            <div x-show="results.asignaciones?.length > 0">
                <div class="px-4 py-2 bg-gray-750 border-b border-gray-700">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase">Asignaciones</h3>
                </div>
                <template x-for="item in results.asignaciones" :key="item.id">
                    <a :href="item.url" class="block px-4 py-3 hover:bg-gray-700 transition">
                        <p class="text-white font-medium" x-text="item.titulo"></p>
                        <p class="text-gray-400 text-sm" x-text="item.subtitulo"></p>
                    </a>
                </template>
            </div>

            {{-- No Results --}}
            <div x-show="!loading && query.length >= 2 && (!results.equipos?.length && !results.empleados?.length && !results.asignaciones?.length)"
                class="px-4 py-6 text-center text-gray-400">
                No se encontraron resultados
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function globalSearch() {
            return {
                query: '',
                results: {},
                loading: false,
                showResults: false,

                init() {
                    // Keyboard shortcut Ctrl+K
                    document.addEventListener('keydown', (e) => {
                        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                            e.preventDefault();
                            this.$el.querySelector('input').focus();
                        }
                    });
                },

                async search() {
                    if (this.query.length < 2) {
                        this.results = {};
                        this.showResults = false;
                        return;
                    }

                    this.loading = true;
                    this.showResults = true;

                    try {
                        const response = await fetch(`/search?q=${encodeURIComponent(this.query)}`);
                        this.results = await response.json();
                    } catch (error) {
                        console.error('Error en búsqueda:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                clearSearch() {
                    this.query = '';
                    this.results = {};
                    this.showResults = false;
                }
            }
        }
    </script>
@endpush