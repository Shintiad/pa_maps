@extends('layouts.main')

@section('title', 'Kasus Penyakit')

@section('header')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 pb-2">
            <h1 class="m-0">Kasus Penyakit Endemik di Kab. Lamongan</h1>
        </div>
        @if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <a href="{{ route('add-kasus') }}" class="btn btn-success relative group">
                    <i class="fa-solid fa-circle-plus"></i> Kasus Penyakit
                    <span class="absolute top-1/2 right-full -translate-y-1/2 mr-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                        Tambah Data Kasus Penyakit
                    </span>
                </a>
            </ol>
        </div>
        @endif
        <div class="mt-3">
            <!-- Success Alert -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Error Alert -->
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Validation Errors -->
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('content')
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data kasus penyakit ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" class="inline-block">
                    @method('DELETE')
                    @csrf
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="mb-4 ms-2">
        <form method="GET" action="{{ route('kasus') }}" class="flex items-end space-x-4">
            <div>
                <label for="tahun_id" class="block font-bold ms-2 mb-1">Filter Tahun:</label>
                <select id="tahun_id" name="tahun_id" class="form-select max-w-40 rounded-md">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahun as $item)
                    <option value="{{ $item->id }}" {{ request('tahun_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->tahun }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="kecamatan_id" class="block font-bold ms-2 mb-1">Filter Kecamatan:</label>
                <select id="kecamatan_id" name="kecamatan_id" class="form-select max-w-48 rounded-md">
                    <option value="">Semua kecamatan</option>
                    @foreach ($kecamatan as $item)
                    <option value="{{ $item->id }}" {{ request('kecamatan_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_kecamatan }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="penyakit_id" class="block font-bold ms-2 mb-1">Filter Penyakit:</label>
                <select id="penyakit_id" name="penyakit_id" class="form-select max-w-48 rounded-md">
                    <option value="">Semua Penyakit</option>
                    @foreach ($penyakit as $item)
                    <option value="{{ $item->id }}" {{ request('penyakit_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_penyakit }}
                    </option>
                    @endforeach
                </select>
            </div>
            <!-- Filter Button -->
            <div>
                <button type="submit" class="btn bg-teal-500 text-white hover:bg-teal-700 h-[38px]">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="card rounded-2xl ps-3 pe-3">
        <div class="overflow-x-scroll">
            <table class="table text-center">
                <thead class="items-center">
                    <tr>
                        <th>No</th>
                        <th>
                            <a href="{{ route('kasus', array_merge(request()->query(), ['sort' => 'tahun', 'direction' => $sort === 'tahun' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="flex items-center justify-center text-gray-700 hover:text-gray-900">
                                Tahun
                                @if($sort === 'tahun')
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort-{{ $direction === 'asc' ? 'up translate-y-1 ' : 'down -translate-y-0.5' }}"></i>
                                </span>
                                @else
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort text-sm text-gray-400"></i>
                                </span>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('kasus', array_merge(request()->query(), ['sort' => 'nama_kecamatan', 'direction' => $sort === 'nama_kecamatan' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="flex items-center justify-center text-gray-700 hover:text-gray-900">
                                Nama Kecamatan
                                @if($sort === 'nama_kecamatan')
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort-{{ $direction === 'asc' ? 'up translate-y-1 ' : 'down -translate-y-0.5' }}"></i>
                                </span>
                                @else
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort text-sm text-gray-400"></i>
                                </span>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('kasus', array_merge(request()->query(), ['sort' => 'nama_penyakit', 'direction' => $sort === 'nama_penyakit' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="flex items-center justify-center text-gray-700 hover:text-gray-900">
                                Nama Penyakit
                                @if($sort === 'nama_penyakit')
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort-{{ $direction === 'asc' ? 'up translate-y-1 ' : 'down -translate-y-0.5' }}"></i>
                                </span>
                                @else
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort text-sm text-gray-400"></i>
                                </span>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('kasus', array_merge(request()->query(), ['sort' => 'terjangkit', 'direction' => $sort === 'terjangkit' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="flex items-center justify-center text-gray-700 hover:text-gray-900">
                                Jumlah Terjangkit
                                @if($sort === 'terjangkit')
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort-{{ $direction === 'asc' ? 'up translate-y-1 ' : 'down -translate-y-0.5' }}"></i>
                                </span>
                                @else
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort text-sm text-gray-400"></i>
                                </span>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('kasus', array_merge(request()->query(), ['sort' => 'meninggal', 'direction' => $sort === 'meninggal' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="flex items-center justify-center text-gray-700 hover:text-gray-900">
                                Jumlah Meninggal
                                @if($sort === 'meninggal')
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort-{{ $direction === 'asc' ? 'up translate-y-1 ' : 'down -translate-y-0.5' }}"></i>
                                </span>
                                @else
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort text-sm text-gray-400"></i>
                                </span>
                                @endif
                            </a>
                        </th>
                        @if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')
                        <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if($kasus->isEmpty())
                    <tr>
                        <td colspan="{{ auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin' ? '6' : '5' }}" class="text-center py-4">
                            <p class="text-gray-500 text-md">Tidak ada data kasus penyakit</p>
                        </td>
                    </tr>
                    @else
                    @foreach ($kasus as $index => $kasusList)
                    <tr>
                        <td>{{ $kasus->firstItem() + $index }}</td>
                        <td>{{ $kasusList->tahunKasus->tahun }}</td>
                        <td>{{ $kasusList->kecamatanKasus->nama_kecamatan }}</td>
                        <td>{{ $kasusList->penyakitKasus->nama_penyakit }}</td>
                        <td>{{ $kasusList->terjangkit }}</td>
                        <td>{{ $kasusList->meninggal }}</td>
                        @if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')
                        <td class="text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="/kasus/{{ $kasusList->id }}/edit" class="btn btn-primary relative group">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                                        Edit Data Kasus Penyakit
                                    </span>
                                </a>
                                <form action="/kasus/{{ $kasusList->id }}" method="POST" class="inline-block">
                                    @method('DELETE')
                                    @csrf
                                    <button type="button" class="btn btn-danger delete-btn relative group"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $kasusList->id }}">
                                        <i class="fa-solid fa-trash"></i>
                                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                                            Hapus Data Kasus Penyakit
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4 pb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <p class="text-muted ms-2">
                    Menampilkan {{ $kasus->firstItem() }} hingga {{ $kasus->lastItem() }} dari {{ $kasus->total() }} data
                </p>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                {{ $kasus->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteForm = document.querySelector('#deleteForm');

        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const kasusId = button.getAttribute('data-id');
                deleteForm.action = `/kasus/${kasusId}`;
            });
        });
    });
</script>
@endsection