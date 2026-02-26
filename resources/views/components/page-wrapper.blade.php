<div class="page-wrapper container-fluid px-3 px-md-4 py-3 py-md-4">

    {{-- Page Header --}}
    <div class="page-header 
            d-flex 
            flex-column 
            gap-3 
            mb-3 mb-md-4">

        {{-- Title Section --}}
        <div>
            <h4 class="page-title mb-0">
                {{ $title }}
            </h4>

            @isset($subtitle)
            <div class="page-subtitle text-muted small mt-1">
                {{ $subtitle }}
            </div>
            @endisset
        </div>

        {{-- Header Actions --}}
        @isset($actions)
        <div class="w-100 d-flex justify-content-center justify-content-lg-end">
            <div class="d-flex flex-wrap gap-2 w-100 w-lg-auto justify-content-center justify-content-lg-end">
                {{ $actions }}
            </div>
        </div>
        @endisset

    </div>

    {{-- Alert --}}
    @isset($alert)
    <div class="alert alert-{{ $alertType ?? 'info' }} mb-3">
        {{ $alert }}
    </div>
    @endisset

    {{-- Page Content --}}
    <div class="page-content">
        {{ $slot }}
    </div>

</div>