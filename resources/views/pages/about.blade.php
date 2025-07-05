@extends('layouts.main')

@section('title', 'About')

@section('header')

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Tentang Website Ini</h1>
        </div>
    </div>
</div>

@endsection

@section('content')

<!-- Main content -->
<div class="container-fluid pb-4">
    <div class="card rounded-2xl ps-3 pe-3 pb-4 pt-4">
        <div class="card-body">
            <div>
                <h5 class="card-title text-lg text-teal-400 font-bold ps-1">{{ $about['brand'] }}</h5>
                <p class="card-text text-justify pt-3 ps-4 pe-4">
                    L - EndeMap adalah website pemetaan spasial temporal penyakit endemik di Kabupaten Lamongan. Pemetaan ini menggunakan Metabase untuk memetakan penyakit endemik setiap kecamatan.<br><br>
                    Data yang digunakan diperoleh dari website profile lamongan yang telah dipublish (dapat diakses untuk umum) pada alamat: 
                    <a href="https://lamongankab.go.id/beranda/dinkes/posting/1872" target="_blank">https://lamongankab.go.id/beranda/dinkes/posting/1872</a>. <br><br>
                    Website ini dikembangkan oleh Shintia Dewi Rahmawati sebagai Tugas Akhir perkuliahan di prodi D3 Teknik Informatika PENS. Kritik dan saran silahkan dikirimkan pada contact di bawah yaa....😉
                </p>
            </div>
        </div>
    </div>
</div>

@endsection