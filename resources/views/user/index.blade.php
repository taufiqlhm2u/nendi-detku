<x-layout>
    <div class="bg-[#f9f9ff] text-[#191c21] min-h-screen pb-20">

        {{-- ===== Top App Bar ===== --}}
        <x-user-header />

        @livewire('user.beranda')
        
        <!-- ===== Bottom Navigation Bar ===== -->
        <x-user-dock />
    </div>
</x-layout>
