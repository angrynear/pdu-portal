@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<x-page-wrapper title="Create Project">

    <x-slot name="actions">
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary">
            ← Back to Projects
        </a>
    </x-slot>

    <form id="createProjectForm" action="{{ route('projects.store') }}" method="POST">
        @csrf

        <div class="row g-4">

            {{-- Project Identity --}}
            <div class="col-md-6">
                <label class="form-label">Project Name</label>
                <input type="text" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Location</label>
                <input type="text" name="location"
                    class="form-control @error('location') is-invalid @enderror"
                    value="{{ old('location') }}" required>
                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
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
                @error('sub_sector')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Funding Information --}}
            <div class="col-md-6">
                <label class="form-label">Source of Fund</label>
                <select name="source_of_fund"
                    class="form-select @error('source_of_fund') is-invalid @enderror" required>
                    <option value="">— Select —</option>
                    @foreach (['GAAB','QRF','TDIF','SDF','CF','SB','BEFF','ODA','LOCAL','For Approval'] as $source)
                    <option value="{{ $source }}" {{ old('source_of_fund') === $source ? 'selected' : '' }}>
                        {{ $source }}
                    </option>
                    @endforeach
                </select>
                @error('source_of_fund')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Funding Year</label>
                <select name="funding_year"
                    class="form-select @error('funding_year') is-invalid @enderror" required>
                    <option value="">— Select —</option>
                    @for ($year = 2025; $year <= 2035; $year++)
                        <option value="{{ $year }}" {{ old('funding_year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                        </option>
                        @endfor
                        <option value="For Approval" {{ old('funding_year') === 'For Approval' ? 'selected' : '' }}>
                            For Approval
                        </option>
                </select>
                @error('funding_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Amount (PHP)</label>
                <input type="number" name="amount" step="0.01"
                    class="form-control @error('amount') is-invalid @enderror"
                    value="{{ old('amount') }}" required>
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Description and Timeline --}}
            <div class="col-md-8">
                <label class="form-label">
                    Description <span class="text-muted">(optional)</span>
                </label>
                <textarea name="description"
                    rows="5"
                    class="form-control pt-2 @error('description') is-invalid @enderror"
                    placeholder="Brief description of the project (scope, purpose, notes)">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date"
                        class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ old('start_date') }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date"
                        class="form-control @error('due_date') is-invalid @enderror"
                        value="{{ old('due_date') }}" required>
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" id="createProjectBtn" class="btn btn-primary">
                Create Project
            </button>
        </div>
    </form>

    {{-- Project Create Script for Creating... --}}
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