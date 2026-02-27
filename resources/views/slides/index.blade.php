@extends('layouts.app')

@section('title', 'Slideshow Manager')

@section('content')

<x-page-wrapper title="Slideshow Manager">

    {{-- ================= SLIDESHOW LIST ================= --}}
    <div class="row g-4">

        @forelse ($slides as $slide)

        <div class="col-12 col-lg-6">
            <div class="card slide-card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column">

                    {{-- IMAGE --}}
                    <div class="slide-image-wrapper position-relative mb-3"
                        data-bs-toggle="modal"
                        data-bs-target="#previewSlideModal"
                        data-image="{{ asset('storage/' . $slide->image_path) }}">

                        <img src="{{ asset('storage/' . $slide->image_path) }}"
                            class="img-fluid rounded slide-thumbnail"
                            style="height:200px; width:100%; object-fit:cover;">

                        <div class="slide-overlay d-flex align-items-center justify-content-center">
                            <i class="bi bi-zoom-in text-white fs-4"></i>
                        </div>

                    </div>

                    {{-- TITLE + STATUS --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="fw-semibold">
                            {{ $slide->title ?? '—' }}
                        </div>

                        <span class="badge {{ $slide->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $slide->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="small text-muted mb-3 flex-grow-1">

                        @if($slide->description)

                        <span id="preview-desc-{{ $slide->id }}">
                            {{ \Illuminate\Support\Str::limit($slide->description, 150) }}
                        </span>

                        <span id="full-desc-{{ $slide->id }}"
                            class="d-none text-dark">
                            {{ $slide->description }}
                        </span>

                        @if(strlen($slide->description) > 150)
                        <button type="button"
                            class="btn btn-link btn-sm p-0"
                            onclick="toggleSlideDesc({{ $slide->id }})"
                            id="btn-desc-{{ $slide->id }}">
                            View Full Description
                        </button>
                        @endif

                        @else
                        —
                        @endif

                    </div>

                    {{-- FOOTER META + ACTIONS --}}
                    <div class="d-flex justify-content-between align-items-center mt-auto">

                        <div class="small text-muted">
                            Order: <strong>{{ $slide->display_order }}</strong>
                        </div>

                        <div class="d-flex gap-2">

                            <a href="{{ route('slides.edit', $slide) }}"
                                class="btn btn-sm btn-light">
                                <i class="bi bi-pencil-fill"></i>
                            </a>

                            <button
                                type="button"
                                class="btn btn-sm btn-light"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmActionModal"
                                data-action="{{ route('slides.archive', $slide) }}"
                                data-method="PATCH"
                                data-title="Archive Slide"
                                data-message="Are you sure you want to archive this slide?"
                                data-confirm-text="Archive"
                                data-confirm-class="btn-danger">
                                <i class="bi bi-archive-fill text-danger"></i>
                            </button>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        @empty
        <div class="col-12">
            <div class="text-center text-muted py-0">
                No slides found.
            </div>
        </div>
        @endforelse

    </div>

    <div class="mt-4">
        {{ $slides->links() }}
    </div>

    {{-- ADD --}}
    @if(auth()->user()->isAdmin())
    <a href="{{ route('slides.create') }}"
        class="btn btn-success rounded-circle shadow mobile-fab">
        <i class="bi bi-plus-lg"></i>
    </a>
    @endif


    {{-- Image Preview Modal --}}
    <div class="modal fade" id="previewSlideModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">

                <div class="modal-body text-center p-0">
                    <img id="previewSlideImage"
                        class="img-fluid rounded shadow">
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Toggle Description Script --}}
    <script>
        function toggleSlideDesc(id) {
            const preview = document.getElementById('preview-desc-' + id);
            const full = document.getElementById('full-desc-' + id);
            const button = document.getElementById('btn-desc-' + id);

            if (!preview || !full || !button) return;

            if (full.classList.contains('d-none')) {
                preview.classList.add('d-none');
                full.classList.remove('d-none');
                button.innerText = 'Hide Full Description';
            } else {
                preview.classList.remove('d-none');
                full.classList.add('d-none');
                button.innerText = 'View Full Description';
            }
        }
    </script>

    {{-- Image Preview Modal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const previewModal = document.getElementById('previewSlideModal');
            const previewImage = document.getElementById('previewSlideImage');

            if (!previewModal || !previewImage) return;

            previewModal.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                const imageSrc = trigger.dataset.image;
                previewImage.src = imageSrc;
            });

        });
    </script>
    @endpush

</x-page-wrapper>

@endsection