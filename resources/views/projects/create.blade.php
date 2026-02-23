@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<x-page-wrapper title="Create Project">

    <x-slot name="actions">
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Projects
        </a>
    </x-slot>

    <form id="createProjectForm" action="{{ route('projects.store') }}" method="POST">
        @csrf

        {{-- ========================= --}}
        {{-- PROJECT IDENTITY SECTION --}}
        {{-- ========================= --}}
        <div class="form-section mb-4">
            <div class="section-title">Project Identity</div>

            <div class="row g-4 mt-1">

                <div class="col-12 col-lg-6">
                    <label class="form-label">Project Name</label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-lg-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location"
                        class="form-control @error('location') is-invalid @enderror"
                        value="{{ old('location') }}" required>
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-lg-3">
                    <label class="form-label">Sub-sector</label>
                    <select name="sub_sector"
                        class="form-select @error('sub_sector') is-invalid @enderror" required>
                        <option value="">— Select —</option>
                        <option value="basic_education" {{ old('sub_sector') == 'basic_education' ? 'selected' : '' }}>Basic Education</option>
                        <option value="higher_education" {{ old('sub_sector') == 'higher_education' ? 'selected' : '' }}>Higher Education</option>
                        <option value="madaris_education" {{ old('sub_sector') == 'madaris_education' ? 'selected' : '' }}>Madaris Education</option>
                        <option value="technical_education" {{ old('sub_sector') == 'technical_education' ? 'selected' : '' }}>Technical Education</option>
                        <option value="others" {{ old('sub_sector') == 'others' ? 'selected' : '' }}>Others</option>
                    </select>
                    @error('sub_sector')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ========================= --}}
        {{-- FUNDING INFORMATION --}}
        {{-- ========================= --}}
        <div class="form-section mb-4">
            <div class="section-title">Funding Information</div>

            <div class="row g-4 mt-1">

                <div class="col-12 col-lg-6">
                    <label class="form-label">Source of Fund</label>
                    <select name="source_of_fund"
                        class="form-select @error('source_of_fund') is-invalid @enderror" required>
                        <option value="">— Select —</option>
                        @foreach (['GAAB','QRF','TDIF','SDF','CF','SB','BEFF','ODA','LOCAL','FOR APPROVAL'] as $source)
                            <option value="{{ $source }}" {{ old('source_of_fund') === $source ? 'selected' : '' }}>
                                {{ $source }}
                            </option>
                        @endforeach
                    </select>
                    @error('source_of_fund')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label">Funding Year</label>
                    <select name="funding_year"
                        class="form-select @error('funding_year') is-invalid @enderror" required>
                        <option value="">— Select —</option>
                        @for ($year = 2025; $year <= 2035; $year++)
                            <option value="{{ $year }}" {{ old('funding_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                        <option value="FOR APPROVAL" {{ old('funding_year') === 'FOR APPROVAL' ? 'selected' : '' }}>
                            FOR APPROVAL
                        </option>
                    </select>
                    @error('funding_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label">Amount (PHP)</label>
                    <input type="number" name="amount" step="0.01"
                        class="form-control @error('amount') is-invalid @enderror"
                        value="{{ old('amount') }}" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ========================= --}}
        {{-- DESCRIPTION & TIMELINE --}}
        {{-- ========================= --}}
        <div class="form-section mb-4">
            <div class="section-title">Timeline & Description</div>

            <div class="row g-4 mt-1">

                <div class="col-12 col-lg-8">
                    <label class="form-label">
                        Description <span class="text-muted">(optional)</span>
                    </label>
                    <textarea name="description"
                        rows="5"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Brief description of the project (scope, purpose, notes)">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-lg-4">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date"
                                class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date') }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- ========================= --}}
        {{-- ACTION BUTTONS --}}
        {{-- ========================= --}}
        <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
            <a href="{{ route('projects.index') }}" class="btn btn-light">
                Cancel
            </a>

            <button type="submit" id="createProjectBtn" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i>
                Create Project
            </button>
        </div>

    </form>

    {{-- Prevent Double Submit --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createProjectForm');
            const button = document.getElementById('createProjectBtn');

            if (form && button) {
                form.addEventListener('submit', function() {
                    button.disabled = true;
                    button.innerText = "Creating...";
                });
            }
        });
    </script>

</x-page-wrapper>
@endsection
