@php
    $authUser = Auth::user();
    $avatarUrl = $authUser->photo_profile
        ? asset('storage/' . $authUser->photo_profile)
        : asset('images/user-default.jpeg');

    $hour = now()->hour;
    $greeting = match (true) {
        $hour < 11 => 'Selamat Pagi',
        $hour < 15 => 'Selamat Siang',
        $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam',
    };
@endphp

<header
    class="w-full sticky top-0 z-50 bg-[#f9f9ff]/80 backdrop-blur-md border-b border-[#e1e2e9] flex justify-between items-center px-4 pt-[max(0.875rem,env(safe-area-inset-top))] pb-3.5">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('profile.index') ?? '#' }}"
            class="avatar shrink-0 active:scale-95 transition-transform duration-150">
            <div class="w-11 h-11 rounded-full ring-2 ring-primary/15 overflow-hidden">
                <img alt="Foto Profil" src="{{ $avatarUrl }}" class="w-full h-full object-cover"
                    onerror="this.src='{{ asset('images/user-default.jpeg') }}'">
            </div>
        </a>
        <div class="min-w-0">
            <p class="text-[11px] font-medium text-primary/70 tracking-wide leading-none">{{ $greeting }},</p>
            <h1 class="text-[17px] font-bold text-base-content leading-tight truncate mt-0.5">
                {{ $authUser->name }}
            </h1>
        </div>
    </div>
</header>
