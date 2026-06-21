<nav class="fixed bottom-0 left-0 right-0 bg-[#f9f9ff] border-t border-base-300 shadow-lg z-50">
    <div class="flex items-center justify-around px-4 py-2 max-w-2xl mx-auto">
        <!-- Beranda -->
        <a href="{{ route('beranda') }}" class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('beranda') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[26px]" style="{{ request()->routeIs('beranda') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">home</span>
            <span class="text-xs {{ request()->routeIs('beranda') ? 'font-semibold' : 'font-medium' }}">Beranda</span>
        </a>
        
        <!-- Riwayat -->
        <a href="{{route('history')}}" class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('history') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[26px]" style="{{ request()->routeIs('history') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">history</span>
            <span class="text-xs {{ request()->routeIs('history') ? 'font-semibold' : 'font-medium' }}">Riwayat</span>
        </a>
        
        <!-- Statistik -->
        <a href="/statistik" class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('statistik') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[26px]" style="{{ request()->routeIs('statistik') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">bar_chart</span>
            <span class="text-xs {{ request()->routeIs('statistik') ? 'font-semibold' : 'font-medium' }}">Statistik</span>
        </a>
        
        <!-- Profil -->
        <a href="/profil" class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('profil') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[26px]" style="{{ request()->routeIs('profil') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">person</span>
            <span class="text-xs {{ request()->routeIs('profil') ? 'font-semibold' : 'font-medium' }}">Profil</span>
        </a>
    </div>
</nav>