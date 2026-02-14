@extends('layouts.app')

@section('title', 'Archived Slides')

@section('content')
<x-page-wrapper title="Archived Slides">

    <x-slot name="actions">
        <a href="{{ route('slides.index') }}"
            class="btn btn-sm btn-secondary">
            ← Back to Slides
        </a>
    </x-slot>

    @if ($slides->isEmpty())
    <div class="text-center text-muted">
        No archived slides found.
    </div>
    @else

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:60px;">No.</th>
                    <th style="width:180px;">Preview</th>
                    <th style="width:250px;">Title</th>
                    <th style="width:400px;">Description</th>
                    <th style="width:70px;">Order</th>
                    <th class="text-center" style="width:150px;">Archived At</th>
                    <th class="text-center" style="width:100px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($slides as $slide)
                <tr>

                    <td class="text-center">
                        {{ $slides->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <img src="{{ asset('storage/' . $slide->image_path) }}"
                            class="img-fluid rounded"
                            style="max-height:80px;">
                    </td>

                    <td>
                        {{ $slide->title }}
                    </td>

                    <td>

                        @if($slide->description)

                        <div>
                            <span id="preview-desc-{{ $slide->id }}">
                                {{ \Illuminate\Support\Str::limit($slide->description, 130) }}
                            </span>

                            <span id="full-desc-{{ $slide->id }}" class="d-none text-dark">
                                {{ $slide->description }}
                            </span>
                        </div>

                        @if(strlen($slide->description) > 130)
                        <button type="button"
                            class="btn btn-link btn-sm p-0"
                            onclick="toggleArchivedDesc({{ $slide->id }})"
                            id="btn-desc-{{ $slide->id }}">
                            View Full Description
                        </button>
                        @endif

                        @else
                        —
                        @endif

                    </td>


                    <td>
                        {{ $slide->display_order }}
                    </td>

                    <td class="text-center">
                        {{ $slide->deleted_at?->format('F d, Y') }}
                    </td>

                    <td class="text-center">

                        <button
                            class="btn btn-sm btn-success"
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

                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $slides->links() }}
        </div>

    </div>

    @endif

    @push('scripts')
    <script>
        function toggleArchivedDesc(id) {
            const preview = document.getElementById('preview-desc-' + id);
            const full = document.getElementById('full-desc-' + id);
            const button = document.getElementById('btn-desc-' + id);

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
    @endpush

</x-page-wrapper>
@endsection