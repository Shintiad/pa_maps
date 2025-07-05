@extends('layouts.main')

@section('title', 'Admin')

@section('header')
@if(auth()->check() && auth()->user()->role == 'superadmin')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Daftar Admin</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <a href="{{ route('add-admin') }}" class="btn btn-success relative group">
                    <i class="fa-solid fa-circle-plus"></i> Tambah
                    <span class="absolute top-1/2 right-full -translate-y-1/2 mr-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                        Tambah Admin
                    </span>
                </a>
            </ol>
        </div>
    </div>
    <!-- Search Form -->
    <form method="GET" action="{{ route('admin-search') }}" class="mt-3 mb-3">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control rounded-l-lg" placeholder="Cari pengguna..." value="{{ request('keyword') }}">
            <button class="btn btn-primary rounded-r-lg" type="submit"><i class="fa-solid fa-search"></i> Cari</button>
        </div>
    </form>
    <div class="mt-3">
        <!-- Success Alert -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
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
@else
<div class="container-fluid">
    <div class="row mb-2 text-center">
        <div class="col">
            <h1 class="m-0 text-red-400">Mohon maaf! Halaman hanya dapat diakses oleh Superadmin.</h1>
        </div>
    </div>
</div>
@endif

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
                Apakah Anda yakin ingin menghapus admin ini?
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
@if(auth()->check() && auth()->user()->role == 'superadmin')
<div class="container-fluid">
    @if($admin->isEmpty())
    <div class="text-center">
        <p class="text-gray-500 text-md">Admin tidak ditemukan</p>
    </div>
    @else
    @foreach($admin as $index => $adminList)
    <div class="card rounded-2xl pt-2 ps-3 pe-3 mb-3 transition ease-in-out hover:scale-95">
        <div class="overflow-x-scroll">
            <div class="card-body">
                <div class="d-flex justify-between">
                    <div>
                        <h5 class="card-title text-lg text-teal-400 font-bold">{{ $adminList->name }}</h5>
                        <p class="card-text">
                        <table class="table table-borderless">
                            <tr>
                                <td>E-mail</td>
                                <td>:</td>
                                <td>{{ $adminList->email }}</td>
                            </tr>
                            <tr>
                                <td>No Telepon</td>
                                <td>:</td>
                                <td>{{ $adminList->phone }}</td>
                            </tr>
                        </table>
                        </p>
                    </div>
                    <div class="d-flex flex-column justify-center items-end">
                        <a href="/admin/{{ $adminList->id }}/edit" class="btn btn-primary mb-2 relative group">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                                Edit User
                            </span>
                        </a>
                        <form action="/admin/{{ $adminList->id }}" method="POST" class="d-inline-block">
                            @method('DELETE')
                            @csrf
                            <button type="button" class="btn btn-danger delete-btn relative group"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-id="{{ $adminList->id }}">
                                <i class="fa-solid fa-trash"></i>
                                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                                    Hapus Admin
                                </span>
                            </button>
                        </form>
                        @if(is_null($adminList->email_verified_at))
                        <form action="{{ route('verify-email-admin', $adminList->id) }}" method="POST" class="d-inline-block mt-2">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-envelope-check"></i> Verifikasi Email
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif

    <!-- Pagination Links -->
    <div class="mt-4 pb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <p class="text-muted ms-2">
                    Menampilkan {{ $admin->firstItem() }} hingga {{ $admin->lastItem() }} dari {{ $admin->total() }} data
                </p>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                {{ $admin->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteForm = document.querySelector('#deleteForm');

        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const userId = button.getAttribute('data-id');
                deleteForm.action = `/admin/${userId}`;
            });
        });
    });
</script>
@endsection