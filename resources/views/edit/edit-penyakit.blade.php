<x-guest-layout>
    <h4 class="pb-4 font-bold text-3xl font-mali">Edit Penyakit</h4>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="/penyakit/{{ $penyakit->id }}">
        @method('PUT')
        @csrf

        <!-- Penyakit -->
        <div class="mt-4">
            <x-input-label for="nama_penyakit" :value="__('Nama Penyakit')" />
            <x-text-input id="nama_penyakit" class="block mt-1 w-full" type="text" name="nama_penyakit" :value="old('nama_penyakit', $penyakit->nama_penyakit)" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('nama_penyakit')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 space-x-3">
            <!-- Log in button -->
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>