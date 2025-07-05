@extends('layouts.main')

@section('title', 'Informasi Penyakit')

@section('header')
<style>
    ul.formatted-list {
        display: block;
        list-style-type: disc;
        margin-block-start: 0.5em;
        margin-block-end: 0.5em;
        margin-inline-start: 0;
        margin-inline-end: 0;
        padding-inline-start: 2em;
    }
    
    /* Responsive styles for small screens */
    @media (max-width: 768px) {
        .disease-tab {
            display: none !important;
        }
        
        .disease-tab.active-mobile {
            display: block !important;
        }
        
        #disease-tabs {
            justify-content: center;
        }
        
        .card-header .d-flex {
            gap: 1rem;
        }
        
        #prev-btn, #next-btn {
            min-width: 60px;
        }
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Informasi Penyakit</h1>
        </div>

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
                Apakah Anda yakin ingin menghapus informasi ini?
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
<div class="container-fluid pb-4">
    <div class="card rounded-2xl ps-3 pe-3 pb-5 pt-4">
        <div class="card-body">
            <div>
                <h5 class="card-title text-lg text-teal-400 font-bold ps-1">Penyakit Endemik</h5>
                <p class="card-text text-justify pt-3 ps-1 pe-1">
                    Penyakit endemik adalah penyakit yang selalu ada pada suatu daerah atau kelompok populasi tertentu. Setiap daerah mungkin memiliki penyakit endemis yang berbeda-beda. Salah satu penyebab hal ini bisa terjadi adalah perbedaan iklim di tiap wilayah. <br><br>
                    Penyakit endemik di Kabupaten Lamongan terdapat tujuh jenis penyakit, yaitu <b>DBD (Demam Berdarah Dengue), malaria, hepatitis B, kusta, tuberkulosis, filariasis, dan campak</b>. Informasi lebih lengkap mengenai beberapa penyakit tersebut, dapat dilihat di bawah ini.
                </p>
            </div>
        </div>
    </div>
    <div class="card rounded-2xl">
        <div class="card-header ps-4 pe-4 pt-2">
            <div class="d-flex justify-content-between align-items-center">
                <button id="prev-btn" class="btn btn-outline-dark">&lt; Prev</button>
                <ul class="nav nav-tabs card-header-tabs" id="disease-tabs">
                    @foreach ( $penyakit as $index => $penyakitList )
                    <li class="nav-item disease-tab @if($index >= 3) d-none @endif @if($index == 0) active-mobile @endif" data-index="{{ $index }}">
                        <a class="nav-link text-gray-600 hover:text-teal-500 @if($index == 0) active @endif"
                            href="#" data-id="{{ $penyakitList->id }}">
                            {{ $penyakitList->nama_penyakit }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                <button id="next-btn" class="btn btn-outline-dark">Next &gt;</button>
            </div>
        </div>
        <div class="card-body h-full">
            @foreach ( $penyakit as $index => $penyakitList )
            <div class="disease-content ps-4 pe-4 @if($index > 0) d-none @endif" id="content-{{ $penyakitList->id }}">
                <div class="text-end mb-3">
                    @if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')
                    @if($penyakitList->pengertian || $penyakitList->penyebab)
                    <a href="{{ route('edit-info', $penyakitList->id) }}" class="btn btn-warning me-2">
                        <i class="fa-solid fa-edit"></i>
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                            Edit Informasi Penyakit
                        </span>
                    </a>
                    <form action="/info-penyakit/{{ $penyakitList->id }}/reset" method="POST" class="inline-block">
                        @method('DELETE')
                        @csrf
                        <button type="button" class="btn btn-danger delete-btn relative group"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-id="{{ $penyakitList->id }}">
                            <i class="fa-solid fa-trash"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                                Reset Informasi Penyakit
                            </span>
                        </button>
                    </form>
                    @else
                    <a href="{{ route('add-info', $penyakitList->id) }}" class="btn btn-success">
                        <i class="fa-solid fa-circle-plus"></i> Informasi
                        <span class="absolute top-1/2 right-full -translate-y-1/2 mr-2 hidden group-hover:block w-max px-2 py-1 bg-white text-black text-sm font-medium rounded-lg shadow-lg">
                            Tambah Informasi Penyakit
                        </span>
                    </a>
                    @endif
                    @endif
                </div>

                @if($penyakitList->gambar)
                <div class="mt-2 mb-4">
                    <img src="{{ asset('storage/' . $penyakitList->gambar) }}" alt="Gambar" class="max-h-80">
                </div>
                @endif

                @if($penyakitList->pengertian)
                <div class="mb-4 text-justify">
                    <h4 class="fw-bold mb-3">Pengertian</h4>
                    <!-- <p>{{ $penyakitList->pengertian }}</p> -->
                    <p>{!! App\Helpers\TextFormatHelper::formatTextWithLists($penyakitList->pengertian) !!}</p>
                </div>
                @endif

                @if($penyakitList->penyebab)
                <div class="mb-4 text-justify">
                    <h4 class="fw-bold mb-3">Penyebab</h4>
                    <!-- <p>{{ $penyakitList->penyebab }}</p> -->
                    <p>{!! App\Helpers\TextFormatHelper::formatTextWithLists($penyakitList->penyebab) !!}</p>
                </div>
                @endif

                @if($penyakitList->gejala)
                <div class="mb-4 text-justify">
                    <h4 class="fw-bold mb-3">Gejala</h4>
                    <!-- <p>{{ $penyakitList->gejala }}</p> -->
                    <p>{!! App\Helpers\TextFormatHelper::formatTextWithLists($penyakitList->gejala) !!}</p>
                </div>
                @endif

                @if($penyakitList->diagnosis)
                <div class="mb-4 text-justify">
                    <h4 class="fw-bold mb-3">Diagnosis</h4>
                    <!-- <p>{{ $penyakitList->diagnosis }}</p> -->
                    <p>{!! App\Helpers\TextFormatHelper::formatTextWithLists($penyakitList->diagnosis) !!}</p>
                </div>
                @endif

                @if($penyakitList->komplikasi)
                <div class="mb-4 text-justify">
                    <h4 class="fw-bold mb-3">Komplikasi</h4>
                    <!-- <p>{{ $penyakitList->komplikasi }}</p> -->
                    <p>{!! App\Helpers\TextFormatHelper::formatTextWithLists($penyakitList->komplikasi) !!}</p>
                </div>
                @endif

                @if($penyakitList->pengobatan)
                <div class="mb-4 text-justify">
                    <h4 class="fw-bold mb-3">Pengobatan</h4>
                    <!-- <p>{{ $penyakitList->pengobatan }}</p> -->
                    <p>{!! App\Helpers\TextFormatHelper::formatTextWithLists($penyakitList->pengobatan) !!}</p>
                </div>
                @endif

                @if($penyakitList->pencegahan)
                <div class="mb-4 text-justify">
                    <h4 class="fw-bold mb-3">Pencegahan</h4>
                    <!-- <p>{{ $penyakitList->pencegahan }}</p> -->
                    <p>{!! App\Helpers\TextFormatHelper::formatTextWithLists($penyakitList->pencegahan) !!}</p>
                </div>
                @endif

                @if($penyakitList->sumber_informasi)
                <div class="mb-4">
                    <h4 class="fw-bold mb-3">Sumber Informasi</h4>
                    <a href="{{ $penyakitList->sumber_informasi }}">{{ $penyakitList->sumber_informasi }}</a>
                </div>
                @endif

                @if(!$penyakitList->pengertian && !$penyakitList->penyebab && !$penyakitList->gejala && !$penyakitList->diagnosis && !$penyakitList->komplikasi && !$penyakitList->pengobatan && !$penyakitList->pencegahan && !$penyakitList->sumber_informasi)
                <div class="text-center text-gray-500 pb-4">
                    Belum ada informasi detail untuk penyakit ini.
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteForm = document.querySelector('#deleteForm');
        const navLinks = document.querySelectorAll('#disease-tabs .nav-link');
        const diseaseTabs = document.querySelectorAll('#disease-tabs .disease-tab');
        const diseaseContents = document.querySelectorAll('.disease-content');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        let currentIndex = 0;
        const visibleTabs = 3;

        // Check if screen is mobile
        function isMobile() {
            return window.innerWidth <= 768;
        }

        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const penyakitId = button.getAttribute('data-id');
                deleteForm.action = `/info-penyakit/${penyakitId}/reset`;
            });
        });

        function handleTabClick(event) {
            event.preventDefault();
            const clickedTab = event.currentTarget;
            const tabIndex = parseInt(clickedTab.closest('.disease-tab').getAttribute('data-index'));
            const diseaseId = clickedTab.getAttribute('data-id');

            // Update current index for pagination
            currentIndex = tabIndex;

            // Set active tab
            setActiveTab(clickedTab);

            // Show the corresponding content
            showContent(diseaseId);
        }

        function setActiveTab(clickedTab) {
            navLinks.forEach(tab => {
                tab.classList.remove('active');
                tab.classList.add('text-gray-600', 'hover:text-teal-500');
            });

            clickedTab.classList.add('active');
            clickedTab.classList.remove('text-gray-600', 'hover:text-teal-500');
        }

        function showContent(diseaseId) {
            diseaseContents.forEach(content => {
                content.classList.add('d-none');
            });

            const contentToShow = document.getElementById(`content-${diseaseId}`);
            if (contentToShow) {
                contentToShow.classList.remove('d-none');
            }
        }

        function updateVisibleTabs() {
            if (isMobile()) {
                // Mobile view: show only current tab
                diseaseTabs.forEach((tab, index) => {
                    tab.classList.remove('active-mobile');
                    if (index === currentIndex) {
                        tab.classList.add('active-mobile');
                    }
                });
            } else {
                // Desktop view: show multiple tabs
                let startIndex = Math.max(0, Math.min(currentIndex - Math.floor(visibleTabs / 2), diseaseTabs.length - visibleTabs));

                diseaseTabs.forEach((tab, index) => {
                    tab.classList.remove('active-mobile');
                    if (index >= startIndex && index < startIndex + visibleTabs) {
                        tab.classList.remove('d-none');
                    } else {
                        tab.classList.add('d-none');
                    }
                });

                prevBtn.disabled = startIndex === 0;
                nextBtn.disabled = startIndex + visibleTabs >= diseaseTabs.length;
            }

            // Update button states for mobile
            if (isMobile()) {
                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex === diseaseTabs.length - 1;
            }
        }

        navLinks.forEach(link => {
            link.addEventListener('click', handleTabClick);
        });

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateVisibleTabs();
                const activeTab = navLinks[currentIndex];
                if (activeTab) {
                    // Trigger tab change
                    const tabIndex = parseInt(activeTab.closest('.disease-tab').getAttribute('data-index'));
                    const diseaseId = activeTab.getAttribute('data-id');
                    
                    setActiveTab(activeTab);
                    showContent(diseaseId);
                }
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < diseaseTabs.length - 1) {
                currentIndex++;
                updateVisibleTabs();
                const activeTab = navLinks[currentIndex];
                if (activeTab) {
                    // Trigger tab change
                    const tabIndex = parseInt(activeTab.closest('.disease-tab').getAttribute('data-index'));
                    const diseaseId = activeTab.getAttribute('data-id');
                    
                    setActiveTab(activeTab);
                    showContent(diseaseId);
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            updateVisibleTabs();
        });

        // Initialize with first tab active
        if (navLinks.length > 0) {
            const firstTab = navLinks[0];
            const firstDiseaseId = firstTab.getAttribute('data-id');
            showContent(firstDiseaseId);
        }

        updateVisibleTabs();
    });
</script>
@endsection