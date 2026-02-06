<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function edit()
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('setting.view'), 403);

        $settings = Setting::allCached();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:2000'],
            'footer_text' => ['nullable', 'string', 'max:2000'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],

            'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'site_favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:1024'],
        ]);

        $current = Setting::allCached();

        $data = [
            'site_name' => $validated['site_name'] ?? ($current['site_name'] ?? null),
            'contact_email' => $validated['contact_email'] ?? ($current['contact_email'] ?? null),
            'contact_phone' => $validated['contact_phone'] ?? ($current['contact_phone'] ?? null),
            'address' => $validated['address'] ?? ($current['address'] ?? null),
            'footer_text' => $validated['footer_text'] ?? ($current['footer_text'] ?? null),
            'facebook_url' => $validated['facebook_url'] ?? ($current['facebook_url'] ?? null),
            'instagram_url' => $validated['instagram_url'] ?? ($current['instagram_url'] ?? null),
            'twitter_url' => $validated['twitter_url'] ?? ($current['twitter_url'] ?? null),
        ];

        $newLogoPath = null;
        $newFaviconPath = null;

        if ($request->hasFile('site_logo')) {
            $newLogoPath = $request->file('site_logo')->store('settings', 'public');
            $data['site_logo'] = $newLogoPath;
        }

        if ($request->hasFile('site_favicon')) {
            $newFaviconPath = $request->file('site_favicon')->store('settings', 'public');
            $data['site_favicon'] = $newFaviconPath;
        }

        DB::transaction(function () use ($data) {
            Setting::setMany($data);
        });

        // Cleanup old files after successful update
        if ($newLogoPath && !empty($current['site_logo']) && $current['site_logo'] !== $newLogoPath) {
            Storage::disk('public')->delete($current['site_logo']);
        }
        if ($newFaviconPath && !empty($current['site_favicon']) && $current['site_favicon'] !== $newFaviconPath) {
            Storage::disk('public')->delete($current['site_favicon']);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Settings updated successfully.');
    }
}
