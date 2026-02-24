<div class="row g-4">

    @foreach($data as $slide)

    <div class="col-12 col-lg-6">
        <div class="card slide-card shadow-sm border-0 h-100 text-muted">
            <div class="card-body d-flex flex-column">

                {{-- IMAGE --}}
                <div class="slide-image-wrapper position-relative mb-3">

                    <img src="{{ asset('storage/' . $slide->image_path) }}"
                        class="img-fluid rounded slide-thumbnail"
                        style="height:200px; width:100%; object-fit:cover; cursor:pointer;"
                        data-bs-toggle="modal"
                        data-bs-target="#archiveSlidePreviewModal"
                        data-image="{{ asset('storage/' . $slide->image_path) }}">

                    <div class="slide-overlay d-flex align-items-center justify-content-center">
                        <i class="bi bi-zoom-in text-white fs-4"></i>
                    </div>

                </div>

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-start mb-3">

                    <div class="fw-semibold">
                        {{ $slide->title }}
                    </div>

                    <span class="badge bg-secondary rounded-pill">
                        <i class="bi bi-archive-fill me-1"></i>
                        Archived
                    </span>

                </div>

                {{-- DESCRIPTION --}}
                <div class="small mb-3 flex-grow-1">

                    @if($slide->description)

                    <span id="preview-slide-desc-{{ $slide->id }}">
                        {{ \Illuminate\Support\Str::limit($slide->description, 150) }}
                    </span>

                    <span id="full-slide-desc-{{ $slide->id }}"
                        class="d-none text-dark">
                        {{ $slide->description }}
                    </span>

                    @if(strlen($slide->description) > 150)
                    <button type="button"
                        class="btn btn-link btn-sm p-0"
                        onclick="toggleArchivedDesc({{ $slide->id }})"
                        id="btn-slide-desc-{{ $slide->id }}">
                        View Full Description
                    </button>
                    @endif

                    @else
                    —
                    @endif

                </div>

                {{-- ORDER --}}
                <div class="small mb-3">
                    <strong>Order:</strong> {{ $slide->display_order }}
                </div>

                {{-- ARCHIVED DATE --}}
                <div class="small text-muted mb-3">
                    <i class="bi bi-clock-history me-1"></i>
                    Archived {{ $slide->deleted_at?->format('F d, Y') }}
                </div>

                {{-- FOOTER --}}
                <div class="mt-auto">
                    <button
                        class="btn btn-sm btn-success w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmActionModal"
                        data-action="{{ route('slides.restore', $slide->id) }}"
                        data-method="PATCH"
                        data-title="Restore Slide"
                        data-message="Are you sure you want to restore this slide?"
                        data-confirm-text="Restore"
                        data-confirm-class="btn-success">
                        Restore
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

{{-- Archive Slide Preview Modal --}}
<div class="modal fade" id="archiveSlidePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">

            <div class="modal-body text-center p-0">
                <img id="archiveSlidePreviewImage"
                     class="img-fluid rounded shadow">
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const previewModal = document.getElementById('archiveSlidePreviewModal');
    const previewImage = document.getElementById('archiveSlidePreviewImage');

    if (!previewModal || !previewImage) return;

    previewModal.addEventListener('show.bs.modal', function(event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        const imageSrc = trigger.getAttribute('data-image');
        previewImage.src = imageSrc;
    });

});
</script>
@endpush