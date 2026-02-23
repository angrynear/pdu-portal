@extends('layouts.app')

@section('title', 'Slideshow Manager')

@section('content')

<x-page-wrapper title="Slideshow Manager">

    <x-slot name="actions">
        <a href="{{ route('slides.create') }}"
           class="btn btn-sm btn-outline-primary">
            + Add Slide
        </a>
    </x-slot>

    {{-- ===================================================== --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:60px;">No.</th>
                        <th style="width:180px;">Preview</th>
                        <th style="width:250px;">Title</th>
                        <th style="width:400px;">Description</th>
                        <th style="width:100px;">Order</th>
                        <th style="width:80px;">Status</th>
                        <th class="text-center" style="width:100px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($slides as $slide)
                    <tr>

                        <td class="text-center">
                            {{ $slides->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <img src="{{ asset('storage/' . $slide->image_path) }}"
                                 class="img-fluid rounded"
                                 style="max-height:80px;">
                        </td>

                        <td>{{ $slide->title ?? '—' }}</td>

                        <td>

                            @if($slide->description)

                                <div>
                                    <span id="preview-desc-{{ $slide->id }}">
                                        {{ \Illuminate\Support\Str::limit($slide->description, 130) }}
                                    </span>

                                    <span id="full-desc-{{ $slide->id }}"
                                          class="d-none text-dark">
                                        {{ $slide->description }}
                                    </span>
                                </div>

                                @if(strlen($slide->description) > 130)
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

                        </td>

                        <td>{{ $slide->display_order }}</td>

                        <td>
                            @if($slide->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>

                        <td class="text-center">

                            <a href="{{ route('slides.edit', $slide) }}"
                               class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <button
                                type="button"
                                class="btn btn-sm btn-danger ms-1"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmActionModal"
                                data-action="{{ route('slides.archive', $slide) }}"
                                data-method="PATCH"
                                data-title="Archive Slide"
                                data-message="Are you sure you want to archive this slide?"
                                data-confirm-text="Archive"
                                data-confirm-class="btn-danger">
                                Archive
                            </button>

                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No slides found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $slides->links() }}
            </div>
        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- MOBILE CARD VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-lg-none">

        @forelse ($slides as $slide)

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">

                {{-- Preview --}}
                <div class="mb-2 text-center">
                    <img src="{{ asset('storage/' . $slide->image_path) }}"
                         class="img-fluid rounded"
                         style="max-height:160px;">
                </div>

                {{-- Title --}}
                <div class="fw-bold mb-1">
                    {{ $slide->title ?? '—' }}
                </div>

                {{-- Description --}}
                <div class="small mb-2">

                    @if($slide->description)

                        <span id="preview-mobile-desc-{{ $slide->id }}">
                            {{ \Illuminate\Support\Str::limit($slide->description, 150) }}
                        </span>

                        <span id="full-mobile-desc-{{ $slide->id }}"
                              class="d-none text-dark">
                            {{ $slide->description }}
                        </span>

                        @if(strlen($slide->description) > 150)
                            <button type="button"
                                    class="btn btn-link btn-sm p-0"
                                    onclick="toggleSlideDescMobile({{ $slide->id }})"
                                    id="btn-mobile-desc-{{ $slide->id }}">
                                View Full Description
                            </button>
                        @endif

                    @else
                        —
                    @endif

                </div>

                {{-- Order --}}
                <div class="small mb-1">
                    <strong>Order:</strong> {{ $slide->display_order }}
                </div>

                {{-- Status --}}
                <div class="small mb-3">
                    <strong>Status:</strong>
                    @if($slide->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="d-grid d-flex gap-1">

                    <a href="{{ route('slides.edit', $slide) }}"
                       class="btn btn-sm btn-primary flex-fill">
                        Edit
                    </a>

                    <button
                        type="button"
                        class="btn btn-sm btn-danger flex-fill"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmActionModal"
                        data-action="{{ route('slides.archive', $slide) }}"
                        data-method="PATCH"
                        data-title="Archive Slide"
                        data-message="Are you sure you want to archive this slide?"
                        data-confirm-text="Archive"
                        data-confirm-class="btn-danger">
                        Archive
                    </button>

                </div>

            </div>
        </div>

        @empty
            <div class="text-center text-muted py-4">
                No slides found.
            </div>
        @endforelse

        <div class="mt-3">
            {{ $slides->links() }}
        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- SCRIPTS --}}
    {{-- ===================================================== --}}
    @push('scripts')
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

        function toggleSlideDescMobile(id) {
            const preview = document.getElementById('preview-mobile-desc-' + id);
            const full = document.getElementById('full-mobile-desc-' + id);
            const button = document.getElementById('btn-mobile-desc-' + id);

            if (!preview || !full || !button) return;

            if (full.classList.contains('d-none')) {
                preview.classList.add('d-none');
                full.classList.remove('d-none');
                button.innerText = 'Hide';
            } else {
                preview.classList.remove('d-none');
                full.classList.add('d-none');
                button.innerText = 'View Full Description';
            }
        }
    </script>
    @endpush

</x-page-wrapper>

@endsection
