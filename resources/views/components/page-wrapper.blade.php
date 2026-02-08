<div class="page-wrapper">

    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <div class="page-title">{{ $title }}</div>

            @isset($subtitle)
                <div class="page-subtitle">
                    {{ $subtitle }}
                </div>
            @endisset
        </div>

        {{-- Header Actions (buttons, etc.) --}}
        @isset($actions)
            <div>
                {{ $actions }}
            </div>
        @endisset
    </div>

    {{-- Alert --}}
    @isset($alert)
        <div class="alert alert-{{ $alertType ?? 'info' }} mb-0">
            {{ $alert }}
        </div>
    @endisset

    {{-- Page Content --}}
    {{ $slot }}

</div>
