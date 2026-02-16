<div class="page-wrapper container-fluid px-3 px-md-4 py-3 py-md-4">

    {{-- Page Header --}}
    <div class="page-header 
                d-flex 
                flex-column 
                flex-md-row 
                justify-content-between 
                align-items-start 
                align-items-md-center 
                gap-2 
                gap-md-0 
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
            <div class="d-flex flex-wrap gap-2">
                {{ $actions }}
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