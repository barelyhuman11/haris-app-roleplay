<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Permission;

class SitemapSettingsController extends Controller {
    
    public function edit(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-settings-sitemap'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Settings') . ' : ' . __('Sitemap') => '',
        ];

        return view('admin.settings.sitemap', compact('breadcrumb'));
    }

    public function update(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-settings-sitemap'));
        
        $validated = $request->validate([
            'sitemap_items' => ['required', 'numeric'],
            'sitemap_lastmod' => ['required', 'in:Y,N'],
            'sitemap_changefreq' => ['nullable', 'in:always,hourly,daily,weekly,monthly,yearly,never'],
            'sitemap_priority' => ['nullable'],
            'sitemap_index_lastmod' => ['required', 'in:Y,N'],
        ]);

        $options = $this->options;
        $options->setMany([
            'sitemap_items' => $validated['sitemap_items'],
            'sitemap_lastmod' => $validated['sitemap_lastmod'],
            'sitemap_changefreq' => $validated['sitemap_changefreq'],
            'sitemap_priority' => $validated['sitemap_priority'],
            'sitemap_index_lastmod' => $validated['sitemap_index_lastmod'],
        ]);

        return redirect()->back()->with('success', __('Settings saved.'));
    }

}