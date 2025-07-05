<x-guest-layout>
    <h4 class="pb-4 font-bold text-3xl font-mali">Edit Tahun</h4>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="/tahun/{{ $tahun->id }}">
        @method('PUT')
        @csrf
        
        <!-- Tahun -->
        <div class="mt-4">
            <x-input-label for="tahun" :value="__('Tahun')" />
            <select id="tahun" name="tahun" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </select>
            <x-input-error :messages="$errors->get('tahun')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 space-x-3">
            <!-- Log in button -->
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearSelect = document.getElementById('tahun');
            const currentYear = new Date().getFullYear();
            const savedYear = parseInt("{{ $tahun->tahun }}");
            
            // Generate years (from current year - 10 to current year + 10)
            for(let year = currentYear - 10; year <= currentYear + 10; year++) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                if(year === savedYear) {
                    option.selected = true;
                }
                yearSelect.appendChild(option);
            }
        });
    </script>
</x-guest-layout>