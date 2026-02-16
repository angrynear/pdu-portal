@extends('layouts.app')

@section('title', 'Edit Slide')

@section('content')
<x-page-wrapper title="Edit Slide">

    <x-slot name="actions">
        <a href="{{ route('slides.index') }}"
           class="btn btn-sm btn-outline-secondary">
            ← Back to Slides
        </a>
    </x-slot>

    <form id="editSlideForm"
          action="{{ route('slides.update', $slide) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ================= IMAGE SECTION ================= --}}
        <div class="row mb-4">
            <div class="col-12 text-center">

                <label class="form-label fw-semibold mb-2 d-block">
                    Slide Image
                </label>

                <div class="d-flex justify-content-center">
                    <div id="previewContainer"
                         class="border rounded overflow-hidden"
                         style="width:100%; max-width:700px; aspect-ratio:16/9; background:#f8f9fa;">

                        <div class="d-flex align-items-center justify-content-center h-100">

                            @if($slide->image_path)
                                <img id="previewImage"
                                     src="{{ asset('storage/' . $slide->image_path) }}"
                                     class="w-100 h-100"
                                     style="object-fit: cover;">
                                <span id="placeholderText" class="d-none"></span>
                            @else
                                <span id="placeholderText" class="text-muted">
                                    No image selected
                                </span>
                                <img id="previewImage"
                                     class="w-100 h-100 d-none"
                                     style="object-fit: cover;">
                            @endif

                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button"
                            class="btn btn-outline-primary btn-sm px-4"
                            onclick="document.getElementById('imageInput').click();">
                        Replace Photo
                    </button>
                </div>

                <input type="file"
                       id="imageInput"
                       name="image"
                       class="d-none @error('image') is-invalid @enderror"
                       accept="image/*">

                @error('image')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror

            </div>
        </div>

        {{-- ================= FORM FIELDS ================= --}}
        <div class="row g-4">

            {{-- TITLE --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">
                    Title <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="title"
                       value="{{ old('title', $slide->title) }}"
                       class="form-control @error('title') is-invalid @enderror"
                       required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ORDER --}}
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Order</label>
                <input type="number"
                       name="display_order"
                       value="{{ old('display_order', $slide->display_order) }}"
                       class="form-control @error('display_order') is-invalid @enderror"
                       required>
                @error('display_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- STATUS --}}
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="is_active"
                        class="form-select @error('is_active') is-invalid @enderror"
                        required>
                    <option value="1" {{ old('is_active', $slide->is_active) == 1 ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="0" {{ old('is_active', $slide->is_active) == 0 ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- DESCRIPTION --}}
            <div class="col-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description"
                          rows="5"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Optional brief description">{{ old('description', $slide->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        {{-- ================= FOOTER ================= --}}
        <div class="mt-4 d-flex flex-column flex-sm-row justify-content-end gap-2">

            <a href="{{ route('slides.index') }}"
               class="btn btn-secondary w-100 w-sm-auto flex-fill">
                Cancel
            </a>

            <button type="submit"
                    id="editSlideBtn"
                    class="btn btn-primary w-100 w-sm-auto flex-fill">
                Update Slide
            </button>

        </div>

    </form>

    {{-- ================= SCRIPTS ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const imageInput = document.getElementById('imageInput');
            const previewImage = document.getElementById('previewImage');
            const placeholderText = document.getElementById('placeholderText');
            const form = document.getElementById('editSlideForm');
            const submitBtn = document.getElementById('editSlideBtn');

            if (imageInput) {
                imageInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function() {
                            previewImage.src = reader.result;
                            previewImage.classList.remove('d-none');
                            if (placeholderText) {
                                placeholderText.classList.add('d-none');
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (form && submitBtn) {
                form.addEventListener('submit', function(){
                    submitBtn.disabled = true;
                    submitBtn.innerText = "Updating...";
                });
            }

        });
    </script>

</x-page-wrapper>
@endsection
