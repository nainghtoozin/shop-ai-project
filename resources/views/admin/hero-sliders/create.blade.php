@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Create Slide</h1>
            <p class="text-muted mb-0">Add a new hero carousel slide</p>
        </div>
        <div>
            <a href="{{ route('admin.hero-sliders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Please fix the errors below.</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Slide Details</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.hero-sliders.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <x-input-label for="title" value="Title (optional)" />
                                        <x-text-input id="title" type="text" name="title" value="{{ old('title') }}" />
                                        <x-input-error :messages="$errors->get('title')" />
                                    </div>

                                    <div class="mb-3">
                                        <x-input-label for="subtitle" value="Subtitle (optional)" />
                                        <textarea id="subtitle" name="subtitle" class="form-control {{ $errors->has('subtitle') ? 'is-invalid' : '' }}" rows="3">{{ old('subtitle') }}</textarea>
                                        <x-input-error :messages="$errors->get('subtitle')" />
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <x-input-label for="badge_text" value="Badge Text (optional)" />
                                            <x-text-input id="badge_text" type="text" name="badge_text" value="{{ old('badge_text') }}" placeholder="e.g., Discount" />
                                            <x-input-error :messages="$errors->get('badge_text')" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-input-label for="sort_order" value="Sort Order" />
                                            <x-text-input id="sort_order" type="number" min="0" step="1" name="sort_order" value="{{ old('sort_order', 0) }}" required />
                                            <x-input-error :messages="$errors->get('sort_order')" />
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <x-input-label for="link" value="Link (optional)" />
                                        <x-text-input id="link" type="url" name="link" value="{{ old('link') }}" placeholder="https://..." />
                                        <x-input-error :messages="$errors->get('link')" />
                                        <div class="form-text">If set, the slide image will link to this URL.</div>
                                    </div>

                                    <div class="mt-3">
                                        <x-input-label value="Status" />
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <x-input-label for="image" value="Image" />
                                        <input id="image" type="file" name="image" class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}" accept="image/*" required>
                                        <x-input-error :messages="$errors->get('image')" />
                                        <div class="form-text">JPG/PNG/WEBP up to 4MB.</div>
                                    </div>

                                    <div class="border rounded p-2 bg-light">
                                        <div class="text-muted small mb-2">Preview</div>
                                        <img id="previewImg" src="" alt="Preview" class="img-fluid rounded d-none" style="max-height: 220px; width: 100%; object-fit: cover;">
                                        <div id="previewEmpty" class="text-muted small">Select an image to preview.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('admin.hero-sliders.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('image');
            const img = document.getElementById('previewImg');
            const empty = document.getElementById('previewEmpty');

            if (!input) return;
            input.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) {
                    if (img) img.classList.add('d-none');
                    if (empty) empty.classList.remove('d-none');
                    return;
                }
                const url = URL.createObjectURL(file);
                if (img) {
                    img.src = url;
                    img.classList.remove('d-none');
                }
                if (empty) empty.classList.add('d-none');
            });
        });
    </script>
@endpush
