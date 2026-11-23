<?php

namespace App\Http\Controllers\Admin\Settings\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Permission;

class FallbackController extends Controller
{
    public function edit(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-settings-general'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Page Settings') . ' : ' . __('404') => '',
        ];

        return view('admin.settings.pages.fallback', compact('breadcrumb'));
    }

    public function update(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-settings-general'));

        $validated = $request->validate([
            'fallback_page_enable_fake_error_page' => ['required', 'in:Y,N'],
            'fallback_page_redirect_to_home_page' => ['required', 'in:Y,N'],
            'blocked_slugs' => ['nullable'],
        ]);

        $options = $this->options;
        $options->setMany([
            'fallback_page_enable_fake_error_page' => $validated['fallback_page_enable_fake_error_page'],
            'fallback_page_redirect_to_home_page' => $validated['fallback_page_redirect_to_home_page'],
            'blocked_slugs' => $validated['blocked_slugs'],
        ]);

        return redirect()->back()->with('success', __('Settings saved.'));
    }
}
