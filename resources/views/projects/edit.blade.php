@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<x-page-wrapper title="Edit Project">

    <x-slot name="actions">
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary">
            ← Back to Projects
        </a>
    </x-slot>

    <form id="editProjectForm" action="{{ route('projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- Project Name --}}
            <div class="col-md-6">
                <label class="form-label">Project Name</label>
                <input type="text"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $project->name) }}"
                    required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Location --}}
            <div class="col-md-3">
                <label class="form-label">Location</label>
                <input type="text"
                    name="location"
                    class="form-control @error('location') is-invalid @enderror"
                    value="{{ old('location', $project->location) }}"
                    required>
                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Sub-sector --}}
            <div class="col-md-3">
                <label class="form-label">Sub-sector</label>
                <select name="sub_sector"
                    class="form-select @error('sub_sector') is-invalid @enderror"
                    required>
                    <option value="">— Select Sub-sector —</option>

                    @php
                    $subSectors = [
                    'basic_education' => 'Basic Education',
                    'higher_education' => 'Higher Education',
                    'madaris_education' => 'Madaris Education',
                    'technical_education' => 'Technical Education',
                    'others' => 'Others',
                    ];
                    @endphp

                    @foreach ($subSectors as $key => $label)
                    <option value="{{ $key }}"
                        {{ old('sub_sector', $project->sub_sector) === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @error('sub_sector')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Source of Fund --}}
            <div class="col-md-6">
                <label class="form-label">Source of Fund</label>
                <select name="source_of_fund"
                    class="form-select @error('source_of_fund') is-invalid @enderror"
                    required>
                    <option value="">— Select Source of Fund —</option>

                    @php
                    $sources = ['GAAB','QRF','TDIF','SDF','CF','SB','BEFF','ODA','LOCAL','For Approval'];
                    @endphp

                    @foreach ($sources as $source)
                    <option value="{{ $source }}"
                        {{ old('source_of_fund', $project->source_of_fund) === $source ? 'selected' : '' }}>
                        {{ $source }}
                    </option>
                    @endforeach
                </select>
                @error('source_of_fund')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Funding Year --}}
            <div class="col-md-3">
                <label class="form-label">Funding Year</label>
                <select name="funding_year"
                    class="form-select @error('funding_year') is-invalid @enderror"
                    required>
                    <option value="">— Select Funding Year —</option>

                    @for ($year = 2025; $year <= 2035; $year++)
                        <option value="{{ $year }}"
                        {{ old('funding_year', $project->funding_year) == $year ? 'selected' : '' }}>
                        {{ $year }}
                        </option>
                        @endfor

                        <option value="For Approval"
                            {{ old('funding_year', $project->funding_year) === 'For Approval' ? 'selected' : '' }}>
                            For Approval
                        </option>
                </select>
                @error('funding_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Amount --}}
            <div class="col-md-3">
                <label class="form-label">Amount (PHP)</label>
                <input type="number"
                    name="amount"
                    step="0.01"
                    class="form-control @error('amount') is-invalid @enderror"
                    value="{{ old('amount', $project->amount) }}"
                    required>
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Description (NO EXTRA SPACES) --}}
            <div class="col-md-8">
                <label class="form-label">
                    Description <span class="text-muted">(optional)</span>
                </label>
                <textarea name="description"
                    rows="5"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Brief description of the project">{{ old('description', $project->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Dates --}}
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date"
                        name="start_date"
                        class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"
                        required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Due Date</label>
                    <input type="date"
                        name="due_date"
                        class="form-control @error('due_date') is-invalid @enderror"
                        value="{{ old('due_date', optional($project->due_date)->format('Y-m-d')) }}"
                        required>
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" id="editProjectBtn" class="btn btn-primary">
                Update Project
            </button>
        </div>

    </form>

    {{-- Project Edit Script for protect --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('editProjectForm');
            const button = document.getElementById('editProjectBtn');

            if (form && button) {
                form.addEventListener('submit', function() {
                    button.disabled = true;
                    button.innerText = "Updating...";
                });
            }

        });
    </script>


</x-page-wrapper>
@endsection