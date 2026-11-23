<?php

namespace App\Http\Controllers\Admin\Settings\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Permission;

class HomeController extends Controller
{
    public function edit(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-settings-general'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Page Settings') . ' : ' . __('Home') => '',
        ];

        return view('admin.settings.pages.home', compact('breadcrumb'));
    }

    public function update(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-settings-general'));

        $validated = $request->validate([
            'home_page_title' => ['nullable'],
            'home_page_meta_description' => ['nullable'],
            'home_page_enable_fake_error_page' => ['required', 'in:Y,N'],
        ]);

        $options = $this->options;
        $options->setMany([
            'home_page_title' => $validated['home_page_title'],
            'home_page_meta_description' => $validated['home_page_meta_description'],
            'home_page_enable_fake_error_page' => $validated['home_page_enable_fake_error_page'],
        ]);

        return redirect()->back()->with('success', __('Settings saved.'));
    }
}
