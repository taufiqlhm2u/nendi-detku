<x-layout>
    <div class="bg-[#f9f9ff] text-[#191c21] min-h-screen pb-24">

        {{-- Top Bar --}}
        <div class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-base-200">
            <div class="max-w-xl mx-auto flex items-center gap-3 px-4 py-3">
                <a href="{{ route('profile.index') }}"
                    class="btn btn-sm btn-ghost btn-circle">
                    <span class="material-symbols-outlined text-[20px]">arrow_back_ios_new</span>
                </a>
                <h1 class="text-base font-bold flex-1">Edit profile</h1>
            </div>
        </div>

        @livewire('user.profile.edit', ['user' => $user])

        <!-- ===== Bottom Navigation Bar ===== -->
        <x-user-dock />

    </div>
</x-layout>
