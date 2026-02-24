<div class="row g-4">

@foreach($data as $user)

<div class="col-12 col-lg-6">
    <div class="card personnel-card shadow-sm border-0 h-100 text-muted">
        <div class="card-body d-flex flex-column">

            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">

                {{-- ================= LEFT PROFILE SECTION ================= --}}
                <div class="d-flex align-items-start gap-3">

                    {{-- Photo --}}
                    <img
                        src="{{ $user->photo
                            ? asset('storage/' . $user->photo)
                            : asset('images/default-avatar.png') }}"
                        class="rounded-circle border"
                        style="width: 120px; height: 120px; object-fit: cover;">

                    <div>

                        {{-- NAME + ROLE BADGE --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            <div class="fw-semibold">
                                {{ $user->name }}
                            </div>

                            <span class="badge rounded-pill {{ $user->role_badge_class }}">
                                {{ ucfirst($user->role) }}
                            </span>

                        </div>

                        {{-- DESIGNATION --}}
                        <div class="small text-muted mt-1">
                            {{ $user->designation ?? '—' }}
                        </div>

                        {{-- CONTACT INFO --}}
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

                    </div>

                </div>

                {{-- ================= RIGHT STATUS SECTION ================= --}}
                <div class="d-flex flex-column align-items-md-end gap-2 mt-3 mt-md-0">

                    <span class="badge bg-secondary rounded-pill">
                        <i class="bi bi-person-x-fill me-1"></i>
                        Inactive
                    </span>

                    <div class="small text-muted">
                        <i class="bi bi-clock-history me-1"></i>
                        Deactivated {{ $user->deactivated_at?->format('F d, Y') }}
                    </div>

                </div>

            </div>

            {{-- ================= FOOTER ACTION ================= --}}
            <div class="mt-auto pt-3">
                <button
                    class="btn btn-sm btn-success w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmActionModal"
                    data-action="{{ route('personnel.reactivate', $user->id) }}"
                    data-method="PATCH"
                    data-title="Reactivate Personnel"
                    data-message="Are you sure you want to reactivate this personnel?"
                    data-confirm-text="Reactivate"
                    data-confirm-class="btn-success">
                    Reactivate
                </button>
            </div>

        </div>
    </div>
</div>

@endforeach

</div>

<div class="mt-4">
    {{ $data->links() }}
</div>