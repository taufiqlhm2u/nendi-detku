<x-layout>
    <div class="bg-[#f9f9ff] text-[#191c21] min-h-screen pb-32">
        
        @livewire('user.history', ['user' => $user])

        <!-- ===== Bottom Navigation Bar ===== -->
        <x-user-dock />
        
    </div>
</x-layout>