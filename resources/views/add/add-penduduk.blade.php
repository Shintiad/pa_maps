<x-guest-layout>
    <h4 class="pb-2 font-bold text-3xl font-mali">Tambah Penduduk</h4>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="/penduduk/add">
        @csrf
        <!-- Tahun -->
        <div class="mt-4">
            <x-input-label for="tahun_id" :value="__('Tahun')" />
            <select id="tahun_id" name="tahun_id" class="block mt-1 w-full form-select rounded-md" required>
                <option value="">Pilih Tahun</option>
                @foreach ($tahun as $item)
                    <option value="{{ $item->id }}">{{ $item->tahun }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('tahun_id')" class="mt-2" />
        </div>

        <!-- Kecamatan -->
        <div class="mt-4">
            <x-input-label for="kecamatan_id" :value="__('Kecamatan')" />
            <select id="kecamatan_id" name="kecamatan_id" class="block mt-1 w-full form-select rounded-md" required>
                <option value="">Pilih Kecamatan</option>
                @foreach ($kecamatan as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kecamatan }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kecamatan_id')" class="mt-2" />
        </div>

        <!-- Penduduk -->
        <div class="mt-4">
            <x-input-label for="jumlah_penduduk" :value="__('Jumlah Penduduk')" />
            <x-text-input id="jumlah_penduduk" class="block mt-1 w-full" type="number" name="jumlah_penduduk" placeholder="Masukkan jumlah penduduk" :value="old('jumlah_penduduk')" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('jumlah_penduduk')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 space-x-3">
            <!-- Log in button -->
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>