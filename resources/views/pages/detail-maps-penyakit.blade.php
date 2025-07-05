@extends('layouts.main')

@section('title', 'Detail Maps')

@section('header')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="m-0">Detail Maps Pemetaan Sebaran Penyakit Endemik Per-Kecamatan</h1>
        </div>
    </div>
    <div class="row">
        <div class="mt-4">
            <label class="block text-base font-medium text-gray-700 mb-2">Pilih Kecamatan:</label>
            <select id="district-select" class="form-select w-full max-w-md rounded-md">
                <option value="">-- Pilih Kecamatan --</option>
                @foreach($kecamatan as $kec)
                <option value="{{ $kec->id }}" disabled>{{ $kec->nama_kecamatan }}</option>
                @endforeach
            </select>
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
    <div class="card rounded-2xl">
        <div class="card-header ps-4 pe-4 pt-2">
            <div class="d-flex justify-content-between align-items-center">
                <button id="prev-btn" class="btn btn-outline-dark">&lt; Prev</button>                <ul class="nav nav-tabs card-header-tabs flex-nowrap overflow-hidden" id="year-tabs">
                    @foreach($tahun as $index => $thn)
                    <li class="nav-item year-tab flex-shrink-0">
                        <a class="nav-link text-gray-600 hover:text-teal-500 @if ($loop->first) active @endif"
                            aria-current="true" href="#"
                            data-year-id="{{ $thn->id }}">
                            {{ $thn->tahun }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                <button id="next-btn" class="btn btn-outline-dark">Next &gt;</button>
            </div>
        </div>
        <div class="card-body h-fit">
            <div class="h-full">
                <div class="flex flex-wrap justify-center items-center -mt-2 -ml-2 mb-4" id="disease-buttons">
                    @foreach($penyakit as $penyakitList)
                    <a href="#" class="btn text-white p-2 mt-2 ml-2 transition-colors duration-200 ease-in-out @if ($loop->first) bg-teal-700 @else bg-teal-500 @endif hover:bg-teal-600" data-disease-id="{{ $penyakitList->id }}">
                        {{ $penyakitList->nama_penyakit }}
                    </a>
                    @endforeach
                </div>

                <iframe id="map-iframe" src="" frameborder="0" allowtransparency class="h-screen w-full hidden"></iframe>
                <div class="flex flex-col items-center justify-center">
                    <p id="no-map-message" class="text-center text-gray-500 pb-2">Pilih kecamatan untuk melihat peta sebaran penyakit.</p>
                    <a id="create-map-btn" href="#" class="btn btn-info mt-2 text-white text-center hidden w-auto px-4"
                        data-user-role="{{ auth()->check() ? auth()->user()->role : 'user' }}">Buat Peta Sebaran</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('#year-tabs .nav-link');
        const yearTabs = document.querySelectorAll('#year-tabs .year-tab');
        const iframe = document.getElementById('map-iframe');
        const noMapMessage = document.getElementById('no-map-message');
        const createMapBtn = document.getElementById('create-map-btn');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const districtSelect = document.getElementById('district-select');
        let currentIndex = 0;
        let visibleTabs = 5; // Default for desktop
        let currentYearId = '{{ $tahun->first()->id }}';
        let currentDiseaseId = '{{ $penyakit->first()->id }}';
        let currentDistrictId = ''; // Akan dipersistkan
        let dataAvailability = {};
        let sortedDistricts = []; // Menyimpan district yang sudah diurutkan
        let originalOptions = []; // Menyimpan option asli untuk restore        // Function to get visible tabs count based on screen size
        function getVisibleTabsCount() {
            if (window.innerWidth < 576) { // xs screens
                return 1;
            } else if (window.innerWidth < 768) { // sm screens
                return 2;
            } else if (window.innerWidth < 900) { // md-small screens
                return 3;
            } else if (window.innerWidth < 1200) { // md-large screens
                return 4;
            } else { // lg screens and above
                return 5;
            }
        }

        // Check if screen is mobile/tablet
        function isMobile() {
            return window.innerWidth < 768; // Bootstrap md breakpoint
        }

        // Debug function
        function debugLog(message, data = null) {
            console.log('[DEBUG]', message, data);
        }

        // Simpan option asli saat load
        function saveOriginalOptions() {
            originalOptions = Array.from(districtSelect.querySelectorAll('option')).map(option => ({
                value: option.value,
                text: option.textContent,
                disabled: option.disabled
            }));
            debugLog('Original options saved:', originalOptions);
        }

        function disableElement(element) {
            element.classList.add('disabled');
            element.style.opacity = '0.5';
            element.style.cursor = 'not-allowed';
            element.style.pointerEvents = 'none';
            element.setAttribute('aria-disabled', 'true');
        }

        function enableElement(element) {            element.classList.remove('disabled');
            element.style.opacity = '1';
            element.style.cursor = 'pointer';
            element.style.pointerEvents = 'auto';
            element.removeAttribute('aria-disabled');
        }

        function updateTabsAndButtonsState() {
            debugLog('Updating tabs and buttons state', {
                currentYearId,
                currentDiseaseId,
                dataAvailability
            });

            // Update year tabs - PERBAIKAN: gunakan case_data_availability untuk enable/disable
            navLinks.forEach(link => {
                const yearId = link.getAttribute('data-year-id');

                // Cek apakah ada CASE DATA untuk tahun ini dengan penyakit apapun
                let hasAnyCaseData = false;
                Object.keys(window.caseDataAvailability || {}).forEach(key => {
                    if (key.startsWith(yearId + '-')) {
                        const availability = window.caseDataAvailability[key];
                        if (availability.has_data && availability.districts.length > 0) {
                            hasAnyCaseData = true;
                        }
                    }
                });

                // Fallback ke data_availability jika caseDataAvailability belum ter-load
                if (!hasAnyCaseData && dataAvailability) {
                    Object.keys(dataAvailability).forEach(key => {
                        if (key.startsWith(yearId + '-')) {
                            const availability = dataAvailability[key];
                            if (availability.has_data && availability.districts.length > 0) {
                                hasAnyCaseData = true;
                            }
                        }
                    });
                }

                debugLog(`Year tab ${yearId} has case data:`, hasAnyCaseData);

                if (!hasAnyCaseData) {
                    disableElement(link);
                } else {
                    enableElement(link);
                }
            });

            // Update disease buttons - PERBAIKAN: enable jika ada case data
            document.querySelectorAll('#disease-buttons .btn').forEach(button => {
                const diseaseId = button.getAttribute('data-disease-id');

                // Cek apakah ada case data untuk kombinasi tahun-penyakit ini
                const caseKey = `${currentYearId}-${diseaseId}`;
                const hasCaseData = (window.caseDataAvailability && window.caseDataAvailability[caseKey] && 
                                   window.caseDataAvailability[caseKey].has_data) ||
                                  (dataAvailability[caseKey] && dataAvailability[caseKey].has_data);

                // SELALU enable jika ada case data (meskipun belum ada map)
                if (hasCaseData) {
                    enableElement(button);
                } else {
                    // Disable jika benar-benar tidak ada data case
                    disableElement(button);
                }

                // Update styling berdasarkan selection
                if (diseaseId === currentDiseaseId) {
                    button.classList.add('bg-teal-700');
                    button.classList.remove('bg-teal-500', 'bg-gray-400');
                } else {
                    if (hasCaseData) {
                        button.classList.add('bg-teal-500');
                        button.classList.remove('bg-teal-700', 'bg-gray-400');
                    } else {
                        button.classList.add('bg-gray-400');
                        button.classList.remove('bg-teal-700', 'bg-teal-500');
                    }
                }
                
                if (hasCaseData) {
                    button.classList.add('hover:bg-teal-600');
                } else {
                    button.classList.remove('hover:bg-teal-600');
                }
            });
        }        function updateDistrictOptions() {
            const key = `${currentYearId}-${currentDiseaseId}`;
            
            // PERBAIKAN: Prioritaskan case data availability untuk menentukan enabled districts
            const caseAvailability = window.caseDataAvailability && window.caseDataAvailability[key] ? 
                window.caseDataAvailability[key] : { has_data: false, districts: [] };
                
            const availability = dataAvailability[key] || { has_data: false, districts: [] };
            
            // Gabungkan districts dari case data dan data availability
            const allEnabledDistricts = [
                ...(caseAvailability.districts || []),
                ...(availability.districts || [])
            ];
            const uniqueEnabledDistricts = [...new Set(allEnabledDistricts)];

            debugLog('Updating district options', {
                key,
                caseAvailability,
                availability,
                enabledDistricts: uniqueEnabledDistricts,
                currentYearId,
                currentDiseaseId,
                sortedDistricts
            });

            // Simpan nilai yang dipilih sebelumnya
            const previouslySelectedValue = districtSelect.value;

            // Clear existing options (except first one)
            while (districtSelect.children.length > 1) {
                districtSelect.removeChild(districtSelect.lastChild);
            }

            // Restore default option
            districtSelect.children[0].textContent = '-- Pilih Kecamatan --';
            districtSelect.children[0].value = '';

            if (uniqueEnabledDistricts.length > 0) {
                // Pisahkan districts menjadi enabled dan disabled
                const enabledDistricts = [];
                const disabledDistricts = [];

                sortedDistricts.forEach(district => {
                    if (uniqueEnabledDistricts.includes(district.id)) {
                        enabledDistricts.push(district);
                    } else {
                        disabledDistricts.push(district);
                    }
                });

                debugLog('Separated districts:', {
                    enabled: enabledDistricts,
                    disabled: disabledDistricts
                });

                // Tambahkan enabled districts terlebih dahulu (di atas)
                enabledDistricts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    option.disabled = false;
                    districtSelect.appendChild(option);
                });

                // Tambahkan disabled districts
                disabledDistricts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    option.disabled = true;
                    districtSelect.appendChild(option);
                });

                debugLog('Districts reordered - enabled first, then disabled');
            } else {
                // Jika tidak ada data, restore semua options sebagai disabled
                originalOptions.forEach((optionData, index) => {
                    if (index === 0) return; // Skip default option

                    const option = document.createElement('option');
                    option.value = optionData.value;
                    option.textContent = optionData.text;
                    option.disabled = true;
                    districtSelect.appendChild(option);
                });

                debugLog('No data available - all districts disabled');
            }

            // PERBAIKAN: Pertahankan pilihan kecamatan
            if (currentDistrictId || previouslySelectedValue) {
                const districtIdToSet = currentDistrictId || previouslySelectedValue;

                // Cek apakah district tersebut masih ada dalam option list
                const targetOption = Array.from(districtSelect.options).find(option =>
                    option.value === districtIdToSet.toString()
                );

                if (targetOption) {
                    // Set nilai select ke district yang dipilih
                    districtSelect.value = districtIdToSet;

                    // Update currentDistrictId jika belum ter-set
                    if (!currentDistrictId) {
                        currentDistrictId = districtIdToSet;
                    }

                    debugLog('District selection maintained:', districtIdToSet);

                    // Jika district disabled, tampilkan pesan khusus tapi tetap pertahankan pilihan
                    if (targetOption.disabled) {
                        debugLog('Selected district is disabled for this combination, but selection maintained');
                    }
                } else {
                    debugLog('Previously selected district not found in options');
                    // Hanya reset jika benar-benar tidak ada
                    districtSelect.value = '';
                }
            }

            // Debug final state
            const finalOptions = Array.from(districtSelect.querySelectorAll('option')).map(opt => ({
                value: opt.value,
                text: opt.textContent,
                disabled: opt.disabled,
                selected: opt.selected
            }));
            debugLog('Final options after reordering:', finalOptions);
            debugLog('Final select value:', districtSelect.value);
        }

        function loadDataAvailability() {
            // Gunakan endpoint yang benar
            const url = `/detail-maps-penyakit/get-link?tahun_id=${currentYearId}&penyakit_id=${currentDiseaseId}`;

            debugLog('Loading data availability from:', url);

            return fetch(url)
                .then(response => {
                    debugLog('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })                .then(data => {
                    debugLog('Raw response data:', data);

                    // Pastikan data_availability ter-set dengan benar
                    if (data.data_availability) {
                        dataAvailability = data.data_availability;
                        debugLog('Data availability updated:', dataAvailability);
                    } else {
                        debugLog('WARNING: No data_availability in response');
                        dataAvailability = {};
                    }

                    // PERBAIKAN: Store case data availability to global variable
                    if (data.case_data_availability) {
                        window.caseDataAvailability = data.case_data_availability;
                        debugLog('Case data availability updated:', window.caseDataAvailability);
                    } else {
                        debugLog('WARNING: No case_data_availability in response');
                        window.caseDataAvailability = {};
                    }

                    // Update sorted districts jika ada
                    if (data.sorted_districts) {
                        sortedDistricts = data.sorted_districts;
                        debugLog('Sorted districts updated:', sortedDistricts);
                    }

                    // Debug: cek data untuk kombinasi saat ini
                    const currentKey = `${currentYearId}-${currentDiseaseId}`;
                    const currentAvailability = dataAvailability[currentKey];
                    const currentCaseAvailability = window.caseDataAvailability[currentKey];
                    debugLog('Current combination availability:', {
                        key: currentKey,
                        data: currentAvailability,
                        caseData: currentCaseAvailability
                    });

                    updateTabsAndButtonsState();
                    updateDistrictOptions();
                    return data;
                })                .catch(error => {
                    console.error('Error loading data availability:', error);
                    debugLog('Error loading data availability:', error);

                    // Set empty data availability on error
                    dataAvailability = {};
                    sortedDistricts = [];
                    window.caseDataAvailability = {}; // PERBAIKAN: reset case data availability
                    updateTabsAndButtonsState();
                    updateDistrictOptions();

                    // Jangan throw error, return empty response
                    return {
                        data_availability: {},
                        sorted_districts: [],
                        case_data_availability: {},
                        status: 'error'
                    };
                });
        }

        function setActiveTab(clickedTab) {
            navLinks.forEach(tab => {
                tab.classList.remove('active');
                tab.classList.add('text-gray-600', 'hover:text-teal-500');
            });

            clickedTab.classList.add('active');
            clickedTab.classList.remove('text-gray-600', 'hover:text-teal-500');
        }        function updateVisibleTabs() {
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
            }        }

        function updateIframe() {
            // Update create map button href first
            const activeDistrictId = districtSelect.value || currentDistrictId;
            if (activeDistrictId) {
                createMapBtn.href = `/regenerate-detail-disease/${currentYearId}/${currentDiseaseId}/${activeDistrictId}`;
            }

            // Gunakan district yang aktif dipilih atau district yang tersimpan
            const districtToUse = districtSelect.value || currentDistrictId;            // Jika tidak ada district yang dipilih atau tersimpan
            if (!districtToUse) {
                iframe.classList.add('hidden');
                createMapBtn.classList.add('hidden');

                // PERBAIKAN: Cek case data availability untuk pesan yang tepat
                const key = `${currentYearId}-${currentDiseaseId}`;
                const caseAvailability = window.caseDataAvailability && window.caseDataAvailability[key] ? 
                    window.caseDataAvailability[key] : { has_data: false, districts: [] };
                const availability = dataAvailability[key] || { has_data: false, districts: [] };
                
                const hasAnyCaseData = caseAvailability.has_data && caseAvailability.districts.length > 0;
                const hasAnyData = availability.has_data && availability.districts.length > 0;

                if (hasAnyCaseData || hasAnyData) {
                    noMapMessage.textContent = 'Pilih kecamatan untuk melihat peta sebaran penyakit.';
                } else {
                    noMapMessage.textContent = 'Tidak ada data yang tersedia untuk kombinasi tahun dan penyakit ini.';
                }
                noMapMessage.classList.remove('hidden');
                return;
            }

            // Cek apakah district yang dipilih tersedia untuk kombinasi saat ini
            const key = `${currentYearId}-${currentDiseaseId}`;
            
            // PERBAIKAN: Cek case data availability terlebih dahulu
            const caseAvailability = window.caseDataAvailability && window.caseDataAvailability[key] ? 
                window.caseDataAvailability[key] : { has_data: false, districts: [] };
            
            const availability = dataAvailability[key] || { has_data: false, districts: [] };
            
            // Gabungkan district dari kedua sumber
            const allAvailableDistricts = [
                ...(caseAvailability.districts || []),
                ...(availability.districts || [])
            ];
            const uniqueAvailableDistricts = [...new Set(allAvailableDistricts)];
            
            const districtIdNum = parseInt(districtToUse);
            const isDistrictAvailable = uniqueAvailableDistricts.includes(districtIdNum);

            if (!isDistrictAvailable) {
                // District dipilih tapi tidak tersedia untuk kombinasi ini
                iframe.classList.add('hidden');
                createMapBtn.classList.add('hidden');
                noMapMessage.textContent = 'Kecamatan yang dipilih tidak memiliki data untuk kombinasi tahun dan penyakit ini. Silakan pilih kecamatan lain.';
                noMapMessage.classList.remove('hidden');
                return;
            }

            const url = `/detail-maps-penyakit/get-link?tahun_id=${currentYearId}&penyakit_id=${currentDiseaseId}&kecamatan_id=${districtToUse}`;

            debugLog('Fetching map for specific district:', url);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    debugLog('Map data received:', data);

                    iframe.classList.add('hidden');
                    noMapMessage.classList.add('hidden');
                    createMapBtn.classList.add('hidden');

                    // Update data availability
                    if (data.data_availability) {
                        dataAvailability = data.data_availability;
                    }

                    // Store case data availability for reference
                    if (data.case_data_availability) {
                        window.caseDataAvailability = data.case_data_availability;
                    }

                    switch (data.status) {
                        case 'has_map':
                            iframe.src = data.link_metabase;
                            iframe.classList.remove('hidden');
                            break;

                        case 'no_map_but_has_case_data':
                            noMapMessage.textContent = 'Peta belum tersedia untuk kombinasi ini, namun data kasus telah lengkap.';
                            noMapMessage.classList.remove('hidden');

                            // Show create button for admin/superadmin
                            if (createMapBtn) {
                                const userRole = createMapBtn.getAttribute('data-user-role');
                                if (userRole == 'superadmin' || userRole == 'admin') {
                                    createMapBtn.classList.remove('hidden');
                                }
                            }
                            break;

                        case 'no_map':
                            noMapMessage.textContent = 'Tidak ada peta yang tersedia dan data kasus belum lengkap untuk kombinasi ini.';
                            noMapMessage.classList.remove('hidden');
                            break;

                        case 'no_data':
                            noMapMessage.textContent = 'Tidak ada data yang tersedia untuk kombinasi tahun, penyakit, dan kecamatan ini.';
                            noMapMessage.classList.remove('hidden');
                            break;

                        case 'select_district':
                            noMapMessage.textContent = 'Pilih kecamatan untuk melihat peta sebaran penyakit.';
                            noMapMessage.classList.remove('hidden');
                            break;

                        case 'error':
                            noMapMessage.textContent = 'Terjadi kesalahan saat memuat data.';
                            noMapMessage.classList.remove('hidden');
                            break;

                        default:
                            noMapMessage.textContent = 'Peta tidak tersedia.';
                            noMapMessage.classList.remove('hidden');
                            break;
                    }
                })
                .catch(error => {
                    console.error('Error fetching the map link:', error);
                    debugLog('Error fetching map:', error);
                    noMapMessage.textContent = 'Terjadi kesalahan saat memuat data.';
                    noMapMessage.classList.remove('hidden');
                    iframe.classList.add('hidden');
                });
        }

        function handleTabClick(e) {
            e.preventDefault();
            if (!e.currentTarget.classList.contains('disabled')) {
                setActiveTab(e.currentTarget);
                currentYearId = e.currentTarget.getAttribute('data-year-id');
                currentIndex = Array.from(yearTabs).indexOf(e.currentTarget.closest('.year-tab'));
                updateVisibleTabs();

                // JANGAN reset district selection - pertahankan pilihan user
                // currentDistrictId tetap sama

                // Load new data availability and update UI
                loadDataAvailability().then(() => {
                    updateIframe();
                });
            }
        }

        // Event Listeners
        navLinks.forEach(link => {
            link.addEventListener('click', handleTabClick);
        });

        document.querySelectorAll('#disease-buttons .btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                currentDiseaseId = this.getAttribute('data-disease-id');
                document.querySelectorAll('#disease-buttons .btn').forEach(btn => {
                    btn.classList.remove('bg-teal-700');
                    btn.classList.add('bg-teal-500');
                });
                this.classList.remove('bg-teal-500');
                this.classList.add('bg-teal-700');

                // JANGAN reset district selection - pertahankan pilihan user
                // currentDistrictId tetap sama

                // Load new data availability and update UI
                loadDataAvailability().then(() => {
                    updateIframe();
                });
            });
        });

        districtSelect.addEventListener('change', function() {
            currentDistrictId = this.value; // Simpan pilihan district
            debugLog('District changed and saved to:', currentDistrictId);
            updateIframe();
        });        function navigateToTab(direction) {
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
                    currentYearId = targetTab.getAttribute('data-year-id');
                    updateVisibleTabs();
                    updateIframe();
                }
            }
        }

        prevBtn.addEventListener('click', () => {
            navigateToTab('prev');
        });

        nextBtn.addEventListener('click', () => {
            navigateToTab('next');        });

        // Handle window resize
        window.addEventListener('resize', function() {
            updateVisibleTabs();
        });

        // Initialize
        debugLog('Initializing with:', {
            currentYearId,
            currentDiseaseId
        });

        // Simpan options asli
        saveOriginalOptions();

        if (navLinks.length > 0) {
            const firstAvailableTab = navLinks[0];
            currentIndex = Array.from(yearTabs).indexOf(firstAvailableTab.closest('.year-tab'));
            setActiveTab(firstAvailableTab);
            updateVisibleTabs();

            // Load initial data availability
            loadDataAvailability().then(() => {
                updateIframe();
            });
        }    });
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