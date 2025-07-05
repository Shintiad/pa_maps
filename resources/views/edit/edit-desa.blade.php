<x-guest-layout>
    <h4 class="pb-4 font-bold text-3xl font-mali">Edit Desa</h4>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="/desa/{{ $desa->id }}">
        @method('PUT')
        @csrf

        <!-- Kecamatan -->
        <div class="mt-4">
            <x-input-label for="kecamatan_id" :value="__('Kecamatan')" />
            <select id="kecamatan_id" name="kecamatan_id" class="block mt-1 w-full form-select rounded-md" required>
                <option value="">Pilih Kecamatan</option>
                @foreach ($kecamatan as $item)
                    <option value="{{ $item->id }}" {{ $desa->kecamatan_id == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_kecamatan }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kecamatan_id')" class="mt-2" />
        </div>

        <!-- Desa -->
        <div class="mt-4">
            <x-input-label for="nama_desa" :value="__('Nama Desa')" />
            <x-text-input id="nama_desa" class="block mt-1 w-full" type="text" name="nama_desa" :value="old('nama_desa', $desa->nama_desa)" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('nama_desa')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 space-x-3">
            <!-- Log in button -->
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>