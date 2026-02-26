@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Manage Personnel' : 'Personnel')

@section('content')
<x-page-wrapper
    title="Personnel List">

    {{-- ================= PERSONNEL LIST ================= --}}
    <div class="row g-4">
        @forelse($users as $user)

        <div class="col-12 col-lg-6">
            <div class="card personnel-card shadow-sm border-0 h-100">
                <div class="card-body">

                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">

                        {{-- ================= LEFT PROFILE SECTION ================= --}}
                        <div class="d-flex align-items-start gap-3">

                            {{-- Photo --}}
                            <img
                                src="{{ $user->photo
                                ? asset('storage/' . $user->photo)
                                : asset('images/default-avatar.png') }}"
                                class="rounded-circle border"
                                style="width: 150px; height: 150px; object-fit: cover;">

                            <div>

                                {{-- Name + Role --}}
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="{{ route('personnel.show', $user->id) }}"
                                        class="fw-semibold fs-6 text-decoration-none text-dark link-hover">
                                        {{ $user->name }}
                                    </a>

                                    <span class="badge {{ $user->role_badge_class }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>

                                {{-- Designation --}}
                                <div class="small text-muted">
                                    {{ $user->designation ?? '—' }}
                                </div>

                                {{-- Contact Info --}}
                                <div class="small text-muted mt-2">
                                    <div>
                                        <i class="bi bi-envelope me-1"></i>
                                        {{ $user->email }}
                                    </div>

                                    <div>
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $user->contact_number ?? '—' }}
                                    </div>
                                </div>

                                {{-- Task Summary --}}
                                <div class="small mt-2">
                                    <span class="text-muted">
                                        Ongoing:
                                        <strong>{{ $user->ongoing_tasks_count }}</strong>
                                    </span>
                                    <span class="mx-2 text-muted">•</span>
                                    <span class="text-muted">
                                        Total:
                                        <strong>{{ $user->total_tasks_count }}</strong>
                                    </span>
                                </div>

                            </div>

                        </div>

                        {{-- ================= RIGHT ACTION SECTION ================= --}}
                        <div class="d-flex flex-column align-items-md-end gap-2 mt-3 mt-md-0">

                            {{-- Status --}}
                            <span class="badge rounded-pill {{ $user->status_badge_class }}">
                                {{ ucfirst($user->account_status) }}
                            </span>

                            {{-- Action Buttons --}}
                            <div class="d-flex gap-2">

                                <a href="{{ route('personnel.edit', $user->id) }}"
                                    class="btn btn-sm btn-light">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                @if($user->account_status === 'active' && auth()->id() !== $user->id)
                                <button class="btn btn-sm btn-light"
                                    data-bs-toggle="modal"
                                    data-bs-target="#confirmActionModal"
                                    data-action="{{ route('personnel.deactivate', $user->id) }}"
                                    data-method="PATCH"
                                    data-title="Deactivate Personnel"
                                    data-message="Are you sure you want to deactivate this personnel?"
                                    data-confirm-text="Deactivate"
                                    data-confirm-class="btn-danger">
                                    <i class="bi bi-person-x-fill text-danger"></i>
                                </button>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        @empty
        <div class="text-center text-muted py-0">
            No personnel found.
        </div>
        @endforelse

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    {{-- ADD --}}
    @if(auth()->user()->isAdmin())
    <a href="{{ route('personnel.create') }}"
        class="btn btn-success rounded-circle shadow mobile-fab">
        <i class="bi bi-plus-lg"></i>
    </a>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const searchInput = document.getElementById('personnelSearch');
            const clearBtn = document.getElementById('clearSearch');

            function toggleClearButton() {
                if (!searchInput || !clearBtn) return;

                if (searchInput.value.length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    toggleClearButton();
                });

                toggleClearButton();
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    toggleClearButton();
                    searchInput.form.submit();
                });
            }

        });
    </script>
    @endpush

</x-page-wrapper>

@endsection