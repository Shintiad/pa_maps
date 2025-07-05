@extends('layouts.main')

@section('title', 'Maps Penduduk')

@section('header')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 pb-2">
            <h1 class="m-0">Maps Pemetaan Sebaran Penduduk</h1>
        </div>
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
@endsection

@section('content')
<!-- Main content -->
<div class="container-fluid pb-4">
    <div class="card text-center rounded-2xl">
        <div class="card-header ps-4 pe-4 pt-2">
            <div class="d-flex justify-content-between align-items-center">
                <button id="prev-btn" class="btn btn-outline-dark btn-sm">
                    <span class="d-none d-md-inline">&lt; Prev</span>
                    <span class="d-md-none">&lt; Prev</span>
                </button>
                <div class="flex-grow-1 mx-2 overflow-hidden">
                    <ul class="nav nav-tabs card-header-tabs justify-content-center mb-0" id="year-tabs">
                        @foreach($tahunData as $index => $thn)
                        <li class="nav-item year-tab 
                            @if($index >= 5) d-none d-md-block @endif
                            @if($index >= 1) d-none d-sm-block @endif">
                            <a class="nav-link text-gray-600 hover:text-teal-500 
                                @if ($loop->first) active @endif
                                @if ($thn['data_status'] !== 'complete') disabled @endif"
                                aria-current="true"
                                href="#"
                                data-year-id="{{ $thn['id'] }}"
                                data-link-metabase="{{ $thn['link_metabase'] }}"
                                data-status="{{ $thn['data_status'] }}"
                                data-message="{{ $thn['status_message'] }}"
                                @if ($thn['data_status'] !=='complete' )
                                title="{{ $thn['status_message'] }}"
                                style="cursor: not-allowed; opacity: 0.6;"
                                @endif>
                                {{ $thn['tahun'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <button id="next-btn" class="btn btn-outline-dark btn-sm">
                    <span class="d-none d-md-inline">Next &gt;</span>
                    <span class="d-md-none">Next &gt;</span>
                </button>
            </div>
        </div>
        <div class="card-body h-fit">
            <div class="h-full">
                @php
                $firstCompleteYear = collect($tahunData)->first(function($year) {
                return $year['data_status'] === 'complete';
                });
                @endphp
                <iframe id="map-iframe"
                    src="{{ $firstCompleteYear ? $firstCompleteYear['link_metabase'] : '' }}"
                    frameborder="0"
                    allowtransparency
                    class="h-screen w-full z-10 {{ !$firstCompleteYear ? 'hidden' : '' }}">
                </iframe>
                <p id="no-map-message" class="text-center text-gray-500 pt-2 pb-2 {{ $firstCompleteYear && $firstCompleteYear['link_metabase'] ? 'hidden' : '' }}">
                    Belum ada peta yang tersedia untuk tahun ini.
                </p>
                <a id="create-map-btn"
                    href="/regenerate-population/{{ $firstCompleteYear ? $firstCompleteYear['id'] : '' }}"
                    class="btn btn-info mt-2 text-white {{ $firstCompleteYear && $firstCompleteYear['link_metabase'] ? 'hidden' : '' }}"
                    data-user-role="{{ auth()->check() ? auth()->user()->role : 'user' }}">
                    Buat Peta Sebaran
                </a>
                <p id="data-status-message" class="text-center text-gray-500 pt-2 pb-2 hidden"></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('#year-tabs .nav-link:not(.disabled)');
        const yearTabs = document.querySelectorAll('#year-tabs .year-tab');
        const iframe = document.getElementById('map-iframe');
        const noMapMessage = document.getElementById('no-map-message');
        const createMapBtn = document.getElementById('create-map-btn');
        const dataStatusMessage = document.getElementById('data-status-message');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        let currentIndex = 0;

        // Function to get visible tabs count based on screen size
        function getVisibleTabsCount() {
            if (window.innerWidth < 576) { // xs screens
                return 1;
            } else if (window.innerWidth < 768) { // sm screens
                return 2;
            } else if (window.innerWidth < 992) { // md screens
                return 3;
            } else if (window.innerWidth < 1200) { // lg screens
                return 4;
            } else { // xl screens and above
                return 5;
            }
        }

        function setActiveTab(clickedTab) {
            if (clickedTab.classList.contains('disabled')) {
                return;
            }

            navLinks.forEach(tab => {
                tab.classList.remove('active');
                tab.classList.add('text-gray-600', 'hover:text-teal-500');
            });

            clickedTab.classList.add('active');
            clickedTab.classList.remove('text-gray-600', 'hover:text-teal-500');
        }

        function updateIframe(linkMetabase, status, message) {
            iframe.classList.add('hidden');
            noMapMessage.classList.add('hidden');
            dataStatusMessage.classList.add('hidden');
            createMapBtn.classList.add('hidden');

            if (status === 'complete') {
                if (linkMetabase) {
                    iframe.src = linkMetabase;
                    iframe.classList.remove('hidden');
                } else {
                    noMapMessage.classList.remove('hidden');
                    if (createMapBtn) {
                        const userRole = createMapBtn.getAttribute('data-user-role');
                        if (userRole == 'superadmin' || userRole == 'admin') {
                            createMapBtn.classList.remove('hidden');
                        } else {
                            createMapBtn.classList.add('hidden');
                        }
                    }
                }
            } else {
                dataStatusMessage.textContent = message;
                dataStatusMessage.classList.remove('hidden');

                if (createMapBtn) {
                    const userRole = createMapBtn.getAttribute('data-user-role');
                    if (userRole == 'superadmin' || userRole == 'admin') {
                        createMapBtn.classList.remove('hidden');
                    } else {
                        createMapBtn.classList.add('hidden');
                    }
                }
            }
        }

        function handleTabClick(e) {
            e.preventDefault();
            const tab = e.currentTarget;

            if (tab.classList.contains('disabled')) {
                return;
            }

            setActiveTab(tab);
            updateIframe(
                tab.getAttribute('data-link-metabase'),
                tab.getAttribute('data-status'),
                tab.getAttribute('data-message')
            );

            createMapBtn.href = `/regenerate-population/${tab.getAttribute('data-year-id')}`;
            currentIndex = Array.from(yearTabs).indexOf(tab.closest('.year-tab'));
            updateVisibleTabs();
        }

        function updateVisibleTabs() {
            const visibleTabs = getVisibleTabsCount();
            let startIndex = Math.max(0, Math.min(currentIndex - Math.floor(visibleTabs / 2), yearTabs.length - visibleTabs));
            
            // Ensure current tab is always visible
            if (currentIndex < startIndex) {
                startIndex = currentIndex;
            } else if (currentIndex >= startIndex + visibleTabs) {
                startIndex = currentIndex - visibleTabs + 1;
            }

            yearTabs.forEach((tab, index) => {
                if (index >= startIndex && index < startIndex + visibleTabs) {
                    tab.classList.remove('d-none');
                } else {
                    tab.classList.add('d-none');
                }
            });

            // Update button states
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === yearTabs.length - 1;
            
            // Add visual feedback for disabled buttons
            if (prevBtn.disabled) {
                prevBtn.classList.add('opacity-50');
            } else {
                prevBtn.classList.remove('opacity-50');
            }
            
            if (nextBtn.disabled) {
                nextBtn.classList.add('opacity-50');
            } else {
                nextBtn.classList.remove('opacity-50');
            }
        }

        function navigateToTab(direction) {
            let newIndex = currentIndex;
            
            if (direction === 'prev' && currentIndex > 0) {
                newIndex = currentIndex - 1;
            } else if (direction === 'next' && currentIndex < yearTabs.length - 1) {
                newIndex = currentIndex + 1;
            }
            
            if (newIndex !== currentIndex) {
                const targetTab = yearTabs[newIndex].querySelector('.nav-link');
                if (targetTab && !targetTab.classList.contains('disabled')) {
                    currentIndex = newIndex;
                    setActiveTab(targetTab);
                    updateIframe(
                        targetTab.getAttribute('data-link-metabase'),
                        targetTab.getAttribute('data-status'),
                        targetTab.getAttribute('data-message')
                    );
                    createMapBtn.href = `/regenerate-population/${targetTab.getAttribute('data-year-id')}`;
                    updateVisibleTabs();
                }
            }
        }

        // Event listeners
        navLinks.forEach(link => {
            link.addEventListener('click', handleTabClick);
        });

        prevBtn.addEventListener('click', () => {
            navigateToTab('prev');
        });

        nextBtn.addEventListener('click', () => {
            navigateToTab('next');
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            updateVisibleTabs();
        });

        // Initialize
        const firstAvailableTab = document.querySelector('#year-tabs .nav-link:not(.disabled)');
        if (firstAvailableTab) {
            currentIndex = Array.from(navLinks).indexOf(firstAvailableTab);
            setActiveTab(firstAvailableTab);
            updateIframe(
                firstAvailableTab.getAttribute('data-link-metabase'),
                firstAvailableTab.getAttribute('data-status'),
                firstAvailableTab.getAttribute('data-message')
            );
        }

        updateVisibleTabs();
    });
</script>

<style>
/* Additional responsive styles */
@media (max-width: 575.98px) {
    .card-header .d-flex {
        gap: 0.25rem;
    }
    
    #year-tabs .nav-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
}

@media (max-width: 767.98px) {
    #year-tabs .nav-link {
        padding: 0.5rem 0.75rem;
    }
}

/* Smooth transition for tab visibility */
.year-tab {
    transition: opacity 0.2s ease-in-out;
}

.year-tab.d-none {
    display: none !important;
}

/* Button hover effects */
#prev-btn:not(:disabled):hover,
#next-btn:not(:disabled):hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#prev-btn:disabled,
#next-btn:disabled {
    cursor: not-allowed;
}
</style>
@endsection