@extends('layouts.app')

@section('title', 'Create Slide')

@section('content')
<x-page-wrapper title="Create Slide">

    <x-slot name="actions">
        <a href="{{ route('slides.index') }}"
            class="btn btn-sm btn-outline-secondary">
            ← Back to Slides
        </a>
    </x-slot>

    <form id="createSlideForm"
        action="{{ route('slides.store') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf

        {{-- ================= IMAGE SECTION ================= --}}
        <div class="row mb-4">
            <div class="col-12 text-center">

                <label class="form-label fw-semibold mb-2 d-block">
                    Slide Image <span class="text-danger">*</span>
                </label>

                <div class="d-flex justify-content-center">
                    <div id="previewContainer"
                        class="border rounded overflow-hidden"
                        style="width:100%; max-width:700px; aspect-ratio:16/9; background:#f8f9fa;">

                        <div class="d-flex align-items-center justify-content-center h-100">

                            <span id="placeholderText" class="text-muted">
                                No image selected
                            </span>

                            <img id="previewImage"
                                class="w-100 h-100 d-none"
                                style="object-fit: cover;">
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button"
                        class="btn btn-primary btn-sm px-4"
                        onclick="document.getElementById('imageInput').click();">
                        Choose Photo
                    </button>
                </div>

                <input type="file"
                    id="imageInput"
                    name="image"
                    class="d-none"
                    accept="image/*"
                    required>

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
                    value="{{ old('title') }}"
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
                    value="{{ old('display_order', $nextOrder ?? 1) }}"
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
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
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
                    placeholder="Optional brief description">{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        {{-- ================= FOOTER ================= --}}
        <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
            <a href="{{ route('slides.index') }}"
                class="btn btn-light">
                Cancel
            </a>

            <button type="submit"
                id="createSlideBtn"
                class="btn btn-primary px-4">
                <i class="bi bi-images me-2"></i>
                Create Slide
            </button>
        </div>

    </form>

    {{-- ================= SCRIPTS ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const imageInput = document.getElementById('imageInput');
            const previewImage = document.getElementById('previewImage');
            const placeholderText = document.getElementById('placeholderText');
            const form = document.getElementById('createSlideForm');
            const submitBtn = document.getElementById('createSlideBtn');

            if (imageInput) {
                imageInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function() {
                            previewImage.src = reader.result;
                            previewImage.classList.remove('d-none');
                            placeholderText.classList.add('d-none');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.innerText = "Creating...";
                });
            }

        });
    </script>

</x-page-wrapper>
@endsection