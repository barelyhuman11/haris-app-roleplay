<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Permission;
use App\Helpers\Options;
use Illuminate\Validation\Rule;
use App\Models\Group;

class GeneralSettingsController extends Controller
{
    private $siteLanguages = [
        'en' => 'English',
        'id' => 'Bahasa Indonesia',
    ];

    public function edit(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-settings-general'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Settings') . ' : ' . __('General') => '',
        ];

        $siteLanguages = $this->siteLanguages;
        $siteLanguages = collect($siteLanguages)
            ->sort()
            ->toArray();

        $groups = Group::all();
        $groupsOptions = $groups->mapWithKeys(function ($group) {
                return [$group->id => $group->name];
            })
            ->toArray();
        $defaultGroup = $groups->first(function ($group) {
                return $group->is_admin == 'N';
            });

        return view('admin.settings.general', compact('breadcrumb', 'siteLanguages', 'groupsOptions', 'defaultGroup'));
    }

    public function update(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-settings-general'));

        $siteLanguages = $this->siteLanguages;
        $siteLanguages = collect($siteLanguages)
            ->keys()
            ->toArray();

        $groups = Group::all();
        $groupsIDs = $groups->map(function ($group) {
                return $group->id;
            })
            ->toArray();

        $validated = $request->validate([
            'site_title' => ['required'],
            'site_tagline' => ['nullable'],
            'enable_register' => ['required', 'in:Y,N'],
            'default_group' => ['required', Rule::in($groupsIDs)],
            'site_language' => ['required', Rule::in($siteLanguages)],
        ]);

        $options = $this->options;
        $options->setMany([
            'site_title' => $validated['site_title'],
            'site_tagline' => $validated['site_tagline'],
            'enable_register' => $validated['enable_register'],
            'default_group' => $validated['default_group'],
            'site_language' => $validated['site_language'],
        ]);

        return redirect()->back()->with('success', __('Settings saved.'));
    }
}
