<nav class="fixed bottom-0 left-0 right-0 bg-[#f9f9ff] border-t border-base-300 shadow-lg z-50">
    <div class="flex items-center justify-around px-4 py-2 max-w-2xl mx-auto">
        <!-- Beranda -->
        <a href="{{ route('beranda') }}" class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('beranda') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="{{ request()->routeIs('beranda') ? 'currentColor' : 'none' }}" stroke="{{ request()->routeIs('beranda') ? 'none' : 'currentColor' }}" stroke-width="1.8">
                <path d="M11.47 3.841a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.061l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.689z" />
                <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.432z" />
            </svg>
            <span class="text-xs {{ request()->routeIs('beranda') ? 'font-semibold' : 'font-medium' }}">Beranda</span>
        </a>
        
        <!-- Riwayat -->
        <a href="/riwayat" class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('riwayat') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-xs {{ request()->routeIs('riwayat') ? 'font-semibold' : 'font-medium' }}">Riwayat</span>
        </a>
        
        <!-- Statistik -->
        <a href="/statistik" class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('statistik') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
            <span class="text-xs {{ request()->routeIs('statistik') ? 'font-semibold' : 'font-medium' }}">Statistik</span>
        </a>
        
        <!-- Profil -->
        <a href="/profil" class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('profil') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-xs {{ request()->routeIs('profil') ? 'font-semibold' : 'font-medium' }}">Profil</span>
        </a>
    </div>
</nav>