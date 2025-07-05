<x-guest-layout>
    <h4 class="pb-4 font-bold text-3xl font-mali">Edit Kasus</h4>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="/kasus/{{ $kasus->id }}">
        @method('PUT')
        @csrf
        <!-- Tahun -->
        <div class="mt-4">
            <x-input-label for="tahun_id" :value="__('Tahun')" />
            <select id="tahun_id" name="tahun_id" class="block mt-1 w-full form-select rounded-md" required>
                <option value="">Pilih Tahun</option>
                @foreach ($tahun as $item)
                    <option value="{{ $item->id }}" {{ $kasus->tahun_id == $item->id ? 'selected' : '' }}>
                        {{ $item->tahun }}
                    </option>
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
                    <option value="{{ $item->id }}" {{ $kasus->kecamatan_id == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_kecamatan }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kecamatan_id')" class="mt-2" />
        </div>

        <!-- Penyakit -->
        <div class="mt-4">
            <x-input-label for="penyakit_id" :value="__('Nama Penyakit')" />
            <select id="penyakit_id" name="penyakit_id" class="block mt-1 w-full form-select rounded-md" required>
                <option value="">Pilih penyakit</option>
                @foreach ($penyakit as $item)
                    <option value="{{ $item->id }}" {{ $kasus->penyakit_id == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_penyakit }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('penyakit_id')" class="mt-2" />
        </div>

        <!-- Kasus -->
        <div class="mt-4">
            <x-input-label for="terjangkit" :value="__('Jumlah Terjangkit')" />
            <x-text-input id="terjangkit" class="block mt-1 w-full" type="number" name="terjangkit" :value="old('terjangkit', $kasus->terjangkit)" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('terjangkit')" class="mt-2" />
        </div>

        <!-- Meninggal -->
        <div class="mt-4">
            <x-input-label for="meninggal" :value="__('Jumlah Meninggal')" />
            <x-text-input id="meninggal" class="block mt-1 w-full" type="number" name="meninggal" :value="old('meninggal', $kasus->meninggal)" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('meninggal')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 space-x-3">
            <!-- Log in button -->
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>