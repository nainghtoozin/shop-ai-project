@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Hero Slider</h1>
            <p class="text-muted mb-0">Manage homepage carousel slides</p>
        </div>
        <div>
            @can('setting.edit')
                <a href="{{ route('admin.hero-sliders.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Add Slide
                </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Slides ({{ $sliders->total() }})</h6>
                <small class="text-muted">Ordered by sort order</small>
            </div>
            <div class="card-body p-0">
                @if ($sliders->count() === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-images fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No slides found</h5>
                        @can('setting.edit')
                            <a href="{{ route('admin.hero-sliders.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Create First Slide
                            </a>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Preview</th>
                                    <th>Content</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sliders as $s)
                                    <tr>
                                        <td style="width: 160px;">
                                            <img src="{{ asset('storage/' . $s->image) }}" alt="Slide" class="rounded border" style="width: 140px; height: 70px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($s->badge_text)
                                                    <span class="badge bg-warning text-dark">{{ $s->badge_text }}</span>
                                                @endif
                                                <div class="fw-semibold">{{ $s->title ?: '(No title)' }}</div>
                                            </div>
                                            @if ($s->subtitle)
                                                <div class="text-muted small">{{ Str::limit($s->subtitle, 120) }}</div>
                                            @endif
                                            @if ($s->link)
                                                <div class="small"><a href="{{ $s->link }}" target="_blank" rel="noopener">{{ $s->link }}</a></div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary text-white">{{ $s->sort_order }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="hs-status-{{ $s->id }}"
                                                        {{ $s->is_active ? 'checked' : '' }}
                                                        onchange="toggleHeroSliderStatus({{ $s->id }})"
                                                        @cannot('setting.edit') disabled @endcannot>
                                                </div>
                                                <span class="badge {{ $s->is_active ? 'bg-success' : 'bg-danger' }} text-white ms-2" data-hs-status-badge="{{ $s->id }}">
                                                    {{ $s->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            @can('setting.edit')
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.hero-sliders.edit', $s) }}" class="btn btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                                        data-id="{{ $s->id }}" data-title="{{ $s->title ?: 'Slide #' . $s->id }}"
                                                        onclick="confirmDelete(this.dataset.id, this.dataset.title)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if ($sliders->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $sliders->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleHeroSliderStatus(id) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `{{ route("admin.hero-sliders.toggle-status", 0) }}`.replace(/0$/, id);

            fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (!data || !data.success) return;
                    const badge = document.querySelector('[data-hs-status-badge="' + id + '"]');
                    const sw = document.getElementById('hs-status-' + id);
                    if (badge) {
                        badge.className = 'badge ' + (data.is_active ? 'bg-success' : 'bg-danger') + ' text-white ms-2';
                        badge.textContent = data.is_active ? 'Active' : 'Inactive';
                    }
                    if (sw) sw.checked = !!data.is_active;
                })
                .catch(() => {
                    const sw = document.getElementById('hs-status-' + id);
                    if (sw) sw.checked = !sw.checked;
                });
        }

        function confirmDelete(id, title) {
            if (!confirm('Are you sure you want to delete ' + title + '?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route("admin.hero-sliders.destroy", 0) }}`.replace(/0$/, id);
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').getAttribute('content') +
                '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush
