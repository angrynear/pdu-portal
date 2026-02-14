@extends('layouts.app')

@section('title', 'Edit Slide')

@section('content')
<x-page-wrapper title="Edit Slide">

    <x-slot name="actions">
        <a href="{{ route('slides.index') }}" class="btn btn-sm btn-secondary">
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
            <div class="col-md-8 mx-auto text-center">

                <div id="previewContainer"
                     class="d-flex align-items-center justify-content-center p-4 mb-3"
                     style="height:350px;">

                    @if($slide->image_path)
                        <img id="previewImage"
                             src="{{ asset('storage/' . $slide->image_path) }}"
                             class="img-fluid rounded"
                             style="max-height:100%; object-fit:cover;">
                        <span id="placeholderText" class="d-none"></span>
                    @else
                        <span id="placeholderText" class="text-muted">
                            No image selected
                        </span>
                        <img id="previewImage"
                             class="img-fluid rounded d-none"
                             style="max-height:100%; object-fit:cover;">
                    @endif
                </div>

                <input type="file"
                       id="imageInput"
                       name="image"
                       class="form-control d-none @error('image') is-invalid @enderror">

                <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        onclick="document.getElementById('imageInput').click();">
                    Replace Photo
                </button>

                @error('image')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-4">

            {{-- Title (REQUIRED) --}}
            <div class="col-md-6">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text"
                       name="title"
                       value="{{ old('title', $slide->title) }}"
                       class="form-control @error('title') is-invalid @enderror"
                       required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Display Order --}}
            <div class="col-md-3">
                <label class="form-label">Order</label>
                <input type="number"
                       name="display_order"
                       value="{{ old('display_order', $slide->display_order) }}"
                       class="form-control @error('display_order') is-invalid @enderror"
                       required>
                @error('display_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status --}}
            <div class="col-md-3">
                <label class="form-label">Status</label>
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

            {{-- Description --}}
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description"
                          rows="5"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Optional brief description">{{ old('description', $slide->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('slides.index') }}" class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit"
                    id="editSlideBtn"
                    class="btn btn-primary">
                Update Slide
            </button>
        </div>

    </form>

    {{-- Preview Script --}}
    <script>
        const imageInput = document.getElementById('imageInput');
        const previewImage = document.getElementById('previewImage');
        const placeholderText = document.getElementById('placeholderText');

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

        document.getElementById('editSlideForm').addEventListener('submit', function(){
            const btn = document.getElementById('editSlideBtn');
            btn.disabled = true;
            btn.innerText = "Updating...";
        });
    </script>

</x-page-wrapper>
@endsection
