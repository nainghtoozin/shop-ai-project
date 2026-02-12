@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Website Settings</h1>
            <p class="text-muted mb-0">Manage frontend branding and contact details</p>
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

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" @cannot('setting.edit') onsubmit="return false;" @endcannot>
            @csrf

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">General</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? '') }}" @cannot('setting.edit') disabled @endcannot>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.default_language') }}</label>
                                <select name="default_language" class="form-select" @cannot('setting.edit') disabled @endcannot>
                                    @foreach (($supportedLocales ?? ['en' => 'English', 'my' => 'Myanmar']) as $code => $label)
                                        <option value="{{ $code }}" {{ old('default_language', $settings['default_language'] ?? 'en') === $code ? 'selected' : '' }}>
                                            {{ $label }} ({{ strtoupper($code) }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Used for new visitors. Users can switch language from the navbar.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" @cannot('setting.edit') disabled @endcannot>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" @cannot('setting.edit') disabled @endcannot>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3" @cannot('setting.edit') disabled @endcannot>{{ old('address', $settings['address'] ?? '') }}</textarea>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Footer Text</label>
                                <textarea name="footer_text" class="form-control" rows="2" @cannot('setting.edit') disabled @endcannot>{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Social Links</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Facebook URL</label>
                                    <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" @cannot('setting.edit') disabled @endcannot>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Instagram URL</label>
                                    <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" @cannot('setting.edit') disabled @endcannot>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Twitter URL</label>
                                    <input type="url" name="twitter_url" class="form-control" value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}" @cannot('setting.edit') disabled @endcannot>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Branding</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Site Logo</label>
                                <input type="file" name="site_logo" class="form-control" accept="image/*" @cannot('setting.edit') disabled @endcannot>
                                <div class="form-text">PNG/JPG/WEBP/SVG up to 2MB.</div>
                                @if (!empty($settings['site_logo']))
                                    <div class="mt-2">
                                        <div class="text-muted small mb-1">Current:</div>
                                        <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="img-fluid rounded border" style="max-height: 90px;">
                                    </div>
                                @endif
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Favicon</label>
                                <input type="file" name="site_favicon" class="form-control" accept="image/png,image/x-icon" @cannot('setting.edit') disabled @endcannot>
                                <div class="form-text">PNG/ICO up to 1MB.</div>
                                @if (!empty($settings['site_favicon']))
                                    <div class="mt-2">
                                        <div class="text-muted small mb-1">Current:</div>
                                        <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Favicon" class="rounded border" style="width: 48px; height: 48px; object-fit: contain;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-body">
                            @can('setting.edit')
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-save me-2"></i>Save Settings
                                </button>
                            @else
                                <div class="alert alert-warning mb-0" role="alert">
                                    <i class="bi bi-shield-lock me-2"></i>You have view-only access.
                                </div>
                            @endcan
                            <small class="text-muted d-block mt-2">Settings are cached for performance.</small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
