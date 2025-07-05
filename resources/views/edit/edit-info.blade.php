<x-guest-layout>
    <h4 class="pb-2 font-bold text-3xl font-mali">
        Edit Informasi Penyakit {{ $infoPenyakit->nama_penyakit ?? '' }}
    </h4>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mt-1 text-sm text-gray-500">
        Tips: Gunakan format "- " untuk membuat daftar poin, seperti:
        <br>- Poin pertama
        <br>- Poin kedua
    </div>

    <form method="POST" action="/info-penyakit/{{ $infoPenyakit->id }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf

        <!-- Info Penyakit -->
        <div class="mt-4">
            <x-input-label for="pengertian" :value="__('Pengertian')" />
            <textarea id="pengertian" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="pengertian" placeholder="Pengertian" rows="3" style="white-space: pre-wrap;">{{ old('pengertian', $infoPenyakit->pengertian) }}</textarea>
            <x-input-error :messages="$errors->get('pengertian')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="penyebab" :value="__('Penyebab')" />
            <textarea id="penyebab" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="penyebab" placeholder="Penyebab" rows="3" style="white-space: pre-wrap;">{{ old('penyebab', $infoPenyakit->penyebab) }}</textarea>
            <x-input-error :messages="$errors->get('penyebab')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="gejala" :value="__('Gejala')" />
            <textarea id="gejala" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="gejala" placeholder="Gejala" rows="3" style="white-space: pre-wrap;">{{ old('gejala', $infoPenyakit->gejala) }}</textarea>
            <x-input-error :messages="$errors->get('gejala')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="diagnosis" :value="__('Diagnosis')" />
            <textarea id="diagnosis" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="diagnosis" placeholder="Diagnosis" rows="3" style="white-space: pre-wrap;">{{ old('diagnosis', $infoPenyakit->diagnosis) }}</textarea>
            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="komplikasi" :value="__('Komplikasi')" />
            <textarea id="komplikasi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="komplikasi" placeholder="Komplikasi" rows="3" style="white-space: pre-wrap;">{{ old('komplikasi', $infoPenyakit->komplikasi) }}</textarea>
            <x-input-error :messages="$errors->get('komplikasi')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="pengobatan" :value="__('Pengobatan')" />
            <textarea id="pengobatan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="pengobatan" placeholder="Pengobatan" rows="3" style="white-space: pre-wrap;">{{ old('pengobatan', $infoPenyakit->pengobatan) }}</textarea>
            <x-input-error :messages="$errors->get('pengobatan')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="pencegahan" :value="__('Pencegahan')" />
            <textarea id="pencegahan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="pencegahan" placeholder="Pencegahan" rows="3" style="white-space: pre-wrap;">{{ old('pencegahan', $infoPenyakit->pencegahan) }}</textarea>
            <x-input-error :messages="$errors->get('pencegahan')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="gambar" :value="__('Image')" />

            <!-- Tampilkan pratinjau gambar yang diunggah sebelumnya -->
            @if ($infoPenyakit->gambar)
            <div class="mb-3 mt-1">
                <img src="{{ asset('storage/' . $infoPenyakit->gambar) }}" alt="{{ $infoPenyakit->nama_penyakit }}" class="w-40 h-auto rounded-lg">
            </div>
            @endif

            <div class="flex items-center border-2 rounded-md">
                <label for="gambar" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1.5 px-4 rounded">
                    Choose File
                </label>
                <span id="file-chosen" class="ml-3 text-gray-600">{{ $infoPenyakit->gambar ? basename($infoPenyakit->gambar) : 'No File Chosen' }}</span>
                <input id="gambar" class="hidden" type="file" name="gambar" accept="image/*" onchange="updateFileName()" />
            </div>

            <x-input-error :messages="$errors->get('gambar')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="sumber_informasi" :value="__('Sumber Informasi')" />
            <x-text-input id="sumber_informasi" class="block mt-1 w-full" type="text" name="sumber_informasi" placeholder="https://..." :value="old('sumber_informasi', $infoPenyakit->sumber_informasi)" autocomplete="tel" />
            <x-input-error :messages="$errors->get('sumber_informasi')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 space-x-3">
            <!-- Log in button -->
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>
        </div>
    </form>
    <script>
        function updateFileName() {
            const input = document.getElementById('gambar');
            const fileName = input.files[0]?.name || 'No File Chosen';
            document.getElementById('file-chosen').textContent = fileName;
        }
    </script>
</x-guest-layout>