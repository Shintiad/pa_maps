@extends('layouts.main')

@section('title', 'Desa')

@section('header')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 pb-2">
            <h1 class="m-0">Daftar Desa di Kab. Lamongan</h1>
        </div>
        @if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <a href="{{ route('add-desa') }}" class="btn btn-success relative group">
                    <i class="fa-solid fa-circle-plus"></i> Desa
                    <span class="absolute top-1/2 right-full -translate-y-1/2 mr-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                        Tambah Desa
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
                Apakah Anda yakin ingin menghapus desa ini?
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
        <form method="GET" action="{{ route('desa') }}" class="flex items-end space-x-4">
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
                <thead>
                    <tr>
                        <th>No</th>
                        <th>
                            <a href="{{ route('desa', array_merge(request()->query(), ['sort' => 'nama_kecamatan', 'direction' => $sort === 'nama_kecamatan' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
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
                            <a href="{{ route('desa', ['sort' => 'nama_desa', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                                class="flex items-center justify-center text-gray-700 hover:text-gray-900">
                                <span>Nama Desa</span>
                                @if($sort === 'nama_desa')
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-sort-{{ $direction === 'asc' ? 'up translate-y-1' : 'down -translate-y-0.5' }} text-sm"></i>
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
                    @foreach ($desa as $index => $desaList)
                    <tr>
                        <td>{{ $desa->firstItem() + $index }}</td>
                        <td>{{ $desaList->kecamatanDesa->nama_kecamatan }}</td>
                        <td>{{ $desaList->nama_desa }}</td>
                        @if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')
                        <td class="text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="/desa/{{ $desaList->id }}/edit" class="btn btn-primary relative group">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                                        Edit desa
                                    </span>
                                </a>
                                <form action="/desa/{{ $desaList->id }}" method="POST" class="inline-block">
                                    @method('DELETE')
                                    @csrf
                                    <button type="button" class="btn btn-danger delete-btn relative group"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $desaList->id }}">
                                        <i class="fa-solid fa-trash"></i>
                                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                                            Hapus Desa
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4 pb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <p class="text-muted ms-2">
                    Menampilkan {{ $desa->firstItem() }} hingga {{ $desa->lastItem() }} dari {{ $desa->total() }} data
                </p>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                {{ $desa->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
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
                const desaId = button.getAttribute('data-id');
                deleteForm.action = `/desa/${desaId}`;
            });
        });
    });
</script>
@endsection