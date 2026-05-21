<header
    class="bg-[#F0F5F9] dark:bg-gray-900 sticky top-0 z-30 pt-4 pb-2 px-8 flex justify-between items-center transition-colors">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $title ?? 'Dashboard' }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Progress Update</p>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center space-x-6">
        <!-- Search -->
        <div class="relative hidden md:block">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" placeholder="Search"
                class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 border border-gray-100 dark:border-gray-700 rounded-full pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4A88F6] w-64 shadow-sm dark:shadow-none transition-colors">
        </div>


        <!-- Notification Bell -->
        <x-notification-bell />

        <!-- User Dropdown -->
        <div class="flex items-center space-x-4">
            <form method="POST" action="/logout" class="hidden sm:block">
                @csrf
                <button type="submit"
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 bg-white dark:bg-gray-800 rounded-full shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                </button>
            </form>

            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="flex items-center space-x-3 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-100 dark:border-gray-700 text-gray-800 dark:text-white rounded-full pl-5 pr-1.5 py-1.5 transition shadow-sm dark:shadow-none">
                        <div class="text-left hidden sm:block">
                            <div class="text-xs font-semibold text-gray-700 dark:text-gray-200">Hello,
                                {{ Auth::user()->name }}!
                            </div>
                            <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ Auth::user()->role_label }}
                            </div>
                        </div>
                        <img class="w-9 h-9 rounded-full border border-gray-100 dark:border-gray-600 object-cover bg-white"
                            src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}">
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link href="/profile">
                        {{ __('Mi Perfil') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="/logout">
                        @csrf
                        <x-dropdown-link href="/logout" onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Cerrar Sesión') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>