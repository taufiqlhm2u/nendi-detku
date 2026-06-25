@php
    $authUser = Auth::user();
    $avatarUrl = $authUser->photo_profile ? asset('storage/' . $authUser->photo_profile) : asset('images/user-default.jpeg');
@endphp
<header
    class="w-full sticky top-0 z-50 bg-[#f9f9ff] border-b border-[#e1e2e9] flex justify-between items-center px-4 py-3 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="avatar">
            <div class="w-10 h-10 rounded-full ring ring-primary ring-offset-1 overflow-hidden">
                <img alt="Foto Profil" src="{{ $avatarUrl }}"
                    onerror="this.src='{{ asset('images/user-default.jpeg') }}'">
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold text-primary tracking-wider uppercase">Halo,</p>
            <h1 class="md:text-lg md:font-bold sm:text-md sm:font-semibold text-base-content leading-tight">{{ $authUser->name }}!</h1>
        </div>
    </div>
</header>
