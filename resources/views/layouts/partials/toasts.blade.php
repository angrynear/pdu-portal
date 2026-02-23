    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">

        @foreach (['success', 'error', 'warning'] as $type)
        @if (session($type))
        <div class="toast align-items-center border-0
                {{ $type === 'success' ? 'text-bg-success' : '' }}
                {{ $type === 'error' ? 'text-bg-danger' : '' }}
                {{ $type === 'warning' ? 'text-bg-warning text-dark' : '' }}"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
            data-bs-delay="2000">

            <div class="d-flex">
                <div class="toast-body">
                    {{ session($type) }}
                </div>

                <button type="button"
                    class="btn-close {{ $type === 'warning' ? '' : 'btn-close-white' }} me-2 m-auto"
                    data-bs-dismiss="toast">
                </button>
            </div>
        </div>
        @endif
        @endforeach

    </div>