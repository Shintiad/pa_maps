@extends('layouts.main')

@section('title', 'Unduh Data')

@section('header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Unduh Data</h1>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid pb-4">
    <div class="card rounded-2xl ps-3 pe-3 pb-4 pt-4">
        <div class="card-body">
            <div>
                <!-- Filter Data -->
                <div class="mb-3 ms-2">
                    <form id="filterForm" method="GET" action="{{ route('unduh-data') }}">
                        <div class="flex items-end gap-3 flex-wrap">
                            <div class="flex-shrink-0">
                                <label for="tahun_id" class="block font-bold ms-2 mb-1">Pilih Tahun:</label>
                                <select id="tahun_id" name="tahun_id" class="form-select max-w-40 rounded-md">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($tahun as $item)
                                    <option value="{{ $item->id }}" {{ request('tahun_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->tahun }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-shrink-0">
                                <label for="kecamatan_id" class="block font-bold ms-2 mb-1">Pilih Kecamatan:</label>
                                <select id="kecamatan_id" name="kecamatan_id" class="form-select max-w-48 rounded-md">
                                    <option value="">Semua kecamatan</option>
                                    @foreach ($kecamatan as $item)
                                    <option value="{{ $item->id }}" {{ request('kecamatan_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_kecamatan }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-shrink-0">
                                <label for="penyakit_id" class="block font-bold ms-2 mb-1">Pilih Penyakit:</label>
                                <select id="penyakit_id" name="penyakit_id" class="form-select max-w-48 rounded-md">
                                    <option value="">Semua Penyakit</option>
                                    @foreach ($penyakit as $item)
                                    <option value="{{ $item->id }}" {{ request('penyakit_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_penyakit }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="hidden lg:block flex-shrink-0">
                                <button type="submit" class="btn bg-blue-500 text-white hover:bg-blue-700 h-[38px]">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>

                        <div class="lg:hidden mt-3">
                            <button type="submit" class="btn bg-blue-500 text-white hover:bg-blue-700">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Status Alert untuk feedback -->
                @if(session('error'))
                <div class="alert alert-danger mt-3">
                    {{ session('error') }}
                </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
                @endif

                @if(!request('tahun_id') && !request('kecamatan_id') && !request('penyakit_id'))
                <div class="warning ms-2">
                    <p class="text-red-600 font-semibold">Mohon filter data terlebih dahulu sebelum mengunduh file dengan kategori tertentu!</p>
                </div>
                @endif

                <!-- Download Buttons -->
                <div class="mt-3 ms-2 flex space-x-4">
                    <!-- Form untuk Excel -->
                    <form action="{{ route('export-excel') }}" method="GET">
                        <input type="hidden" name="tahun_id" value="{{ request('tahun_id') }}">
                        <input type="hidden" name="kecamatan_id" value="{{ request('kecamatan_id') }}">
                        <input type="hidden" name="penyakit_id" value="{{ request('penyakit_id') }}">

                        <button type="submit" class="btn bg-green-700 text-white hover:bg-green-800 flex items-center">
                            <i class="fas fa-file-excel mr-2"></i> Unduh Excel
                        </button>
                    </form>

                    <!-- Form untuk PDF -->
                    <form action="{{ route('export-pdf') }}" method="GET">
                        <input type="hidden" name="tahun_id" value="{{ request('tahun_id') }}">
                        <input type="hidden" name="kecamatan_id" value="{{ request('kecamatan_id') }}">
                        <input type="hidden" name="penyakit_id" value="{{ request('penyakit_id') }}">

                        <button type="submit" class="btn bg-red-500 text-white hover:bg-red-700 flex items-center">
                            <i class="fas fa-file-pdf mr-2"></i> Unduh PDF
                        </button>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="mt-4">
                    <h4 class="font-bold mb-3">Data Kasus Penyakit</h4>
                    <div class="overflow-x-scroll">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tahun</th>
                                    <th>Kecamatan</th>
                                    <th>Nama Penyakit</th>
                                    <th>Jumlah Terjangkit</th>
                                    <th>Jumlah Meninggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kasus as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->tahunKasus->tahun }}</td>
                                    <td>{{ $item->kecamatanKasus->nama_kecamatan }}</td>
                                    <td>{{ $item->penyakitKasus->nama_penyakit }}</td>
                                    <td>{{ $item->terjangkit }}</td>
                                    <td>{{ $item->meninggal ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data yang sesuai dengan filter</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when filters change (optional)
        const selects = document.querySelectorAll('select[name="tahun_id"], select[name="kecamatan_id"], select[name="penyakit_id"]');

        selects.forEach(select => {
            select.addEventListener('change', function() {
                // Uncomment jika Anda ingin filter otomatis saat pilihan berubah
                document.getElementById('filterForm').submit();
            });
        });
    });
</script>
@endpush