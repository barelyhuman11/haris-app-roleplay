<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Permission;
use App\Models\Option;
use Illuminate\Validation\Rule;

class WidgetsController extends Controller
{
    private function getSections()
    {
        return [
            'widget_header' => __('All Pages') . ' : ' . __('Header'),
            'widget_footer' => __('All Pages') . ' : ' . __('Footer'),
        ];
    }

    private function getUserAgents()
    {
        return [
            'ALL'       => __('All User Agents'),
            'BOT'       => __('Bot'),
            'HUMAN'     => __('Human'),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-widgets-index'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Widgets') => '',
        ];

        $widgets = Option::where('name', 'LIKE', 'widget_%')
            ->orderBy('id', 'DESC')
            ->get();

        $sections = $this->getSections();

        return view('admin.widgets.index', compact('breadcrumb', 'widgets', 'sections'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-widgets-create'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Widgets') => route('admin.settings.widgets.index'),
            __('Create Widget') => '',
        ];

        $sections = $this->getSections();
        $userAgents = $this->getUserAgents();
        $countries = \App\Helpers\Countries::getCountries();

        return view('admin.widgets.create', compact('breadcrumb', 'sections', 'userAgents', 'countries'));
    }

    /**
     * Store a newly created resource in a storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-widgets-create'));

        $sections = $this->getSections();
        $sectionsArray = collect($sections)->keys()->toArray();

        $validated = $request->validate([
            'section' => ['required', Rule::in($sectionsArray)],
            'widget_name' => ['required'],
            'content' => ['nullable'],
            'user_agent' => ['nullable', 'in:ALL,BOT,HUMAN'],
            'countries' => ['required', 'in:ALL,WHITELIST,BLACKLIST'],
            'list_countries' => ['nullable', 'array'],
        ]);

        $options = $this->options;

        $widget = [
            'name' => $validated['widget_name'],
            'content' => $validated['content'],
            'user_agent' => $validated['user_agent'],
            'countries' => $validated['countries'],
            'list_countries' => !empty($validated['list_countries']) ? $validated['list_countries'] : [],
        ];
        $options->set($validated['section'], $widget, false);

        return redirect()->route('admin.settings.widgets.index')->with('success', __('Widget has been created!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Option ${{ modelInstance}}
     * @return \Illuminate\Http\Response
     */
    public function show(Option $option)
    {
        return redirect()->route('admin.settings.widgets.index');
    }

    /**
     * Show the form for editing the specified resource.
     * @param  \App\Models\Option $option
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Option $widget)
    {
        $sections = $this->getSections();
        $sectionsArray = collect($sections)->keys()->toArray();
        $userAgents = $this->getUserAgents();
        $countries = \App\Helpers\Countries::getCountries();

        if (!in_array($widget->name, $sectionsArray)) {
            abort(404);
        }

        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-widgets-edit'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Widgets') => route('admin.settings.widgets.index'),
            __('Edit Widget') => '',
        ];

        return view('admin.widgets.edit', compact('widget', 'breadcrumb', 'sections', 'userAgents', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Option $option
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Option $widget)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-widgets-edit'));

        $sections = $this->getSections();
        $sectionsArray = collect($sections)->keys()->toArray();

        $validated = $request->validate([
            'section' => ['required', Rule::in($sectionsArray)],
            'widget_name' => ['required'],
            'content' => ['nullable'],
            'user_agent' => ['nullable', 'in:ALL,BOT,HUMAN'],
            'countries' => ['required', 'in:ALL,WHITELIST,BLACKLIST'],
            'list_countries' => ['nullable', 'array'],
        ]);

        $options = $this->options;

        $widgetArray = [
            'name' => $validated['widget_name'],
            'content' => $validated['content'],
            'user_agent' => $validated['user_agent'],
            'countries' => $validated['countries'],
            'list_countries' => !empty($validated['list_countries']) ? $validated['list_countries'] : [],
        ];
        $options->update($widget, $validated['section'], $widgetArray);

        return redirect()->route('admin.settings.widgets.edit', ['widget' => $widget])->with('success', __('Widget has been updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Option $option
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Option $widget)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-widgets-delete'));

        $widget->delete();

        return redirect()->back()->with('success', __('Widget has been deleted!'));
    }
}