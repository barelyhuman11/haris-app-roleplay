<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Permission;
use App\Models\Content as Page;
use App\Tables\PagesTable;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    private $contentType = 'PAGE';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-pages-index'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Pages') => '',
        ];

        $pagesTable = new PagesTable;
        $pagesTable->setPermissions($userAuth);
        $pagesTable->prepare();

        return view('admin.pages.index', compact('breadcrumb', 'pagesTable'));
    }

    public function indexData(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-pages-index'));

        $queryLimit = $request->query('limit', 10);
        $queryOffset = $request->query('offset', 0);
        $querySort = $request->query('sort', 'id');
        $queryOrder = $request->query('order', 'desc');
        $querySearch = $request->query('search');

        $pagesCount = Page::type($this->contentType)->count();

        $pages = Page::type($this->contentType)
            ->when($querySearch, function ($query) use ($querySearch) {
                $query->where('title', 'like', '%' . $querySearch . '%');
            });
        $pagesCountFiltered = $pages->count();

        $pages = $pages->orderBy($querySort, $queryOrder)
            ->skip($queryOffset)
            ->take($queryLimit)
            ->get();

        return [
            'total' => $pagesCountFiltered,
            'totalNotFiltered' => $pagesCount,
            'rows' => $pages->map(function ($page) use ($userAuth) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'status' => $page->status == 'PUBLISH' ? '<span class="badge badge-info">' . __('Published') . '</span>' : '<span class="badge badge-secondary">' . __('Draft') . '</span>',
                    'slug' => $page->slug,
                    'menu' => view('admin.pages._menu', ['page' => $page, 'userAuth' => $userAuth])->render(),
                ];
            }),
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-pages-create'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Pages') => route('admin.pages.index'),
            __('Create Page') => '',
        ];

        return view('admin.pages.create', compact('breadcrumb'));
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
        $this->authorize('auth-check', $userAuth->authorize('admin-pages-create'));

        if ($request->page_slug) {
            $pageSlugRule = ['between:1,200', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:contents,slug'];
            $pageSlug = $request->page_slug;
        }
        else {
            $pageSlugRule = ['nullable'];
            $pageSlug = '';
        }

        $request->validate([
            'page_title' => ['required', 'between:1,200'],
            'page_content' => ['max:60000'],
            'page_description' => ['max:500'],
            'status' => ['required', 'in:PUBLISH,DRAFT'],
            'page_slug' => $pageSlugRule,
        ]);

        Page::create([
            'user_id' => $request->user()->id,
            'slug' => $pageSlug,
            'title' => $request->page_title,
            'excerpt' => $request->page_description,
            'content' => $request->page_content,
            'status' => $request->status,
            'type' => 'PAGE',
        ]);

        return redirect()->route('admin.pages.index')->with('success', __('Page has been created!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Page ${{ modelInstance}}
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        if ($page->type != $this->contentType) {
            abort(404);
        }

        return redirect()->route('admin.pages.index');
    }

    /**
     * Show the form for editing the specified resource.
     * @param  \App\Models\Page $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Page $page)
    {
        if ($page->type != $this->contentType) {
            abort(404);
        }

        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-pages-edit'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Pages') => route('admin.pages.index'),
            __('Edit Page') => '',
        ];

        return view('admin.pages.edit', compact('page', 'breadcrumb'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Page $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        if ($page->type != $this->contentType) {
            abort(404);
        }

        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-pages-edit'));

        $validated = $request->validate([
            'page_title' => ['required', 'between:1,200'],
            'page_content' => ['max:60000'],
            'page_description' => ['max:500'],
            'status' => ['required', 'in:PUBLISH,DRAFT'],
            'page_slug' => ['required', 'between:1,200', 'regex:/^[a-zA-Z0-9_-]+$/', Rule::unique('contents', 'slug')->ignore($page->id)],
        ]);

        $page->update([
            'slug' => $request->page_slug,
            'title' => $request->page_title,
            'excerpt' => $request->page_description,
            'content' => $request->page_content,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.pages.edit', ['page' => $page])->with('success', __('Page has been updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Page $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Page $page)
    {
        if ($page->type != $this->contentType) {
            abort(404);
        }
        
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-pages-delete'));

        $page->delete();

        return redirect()->back()->with('success', __('Page has been deleted!'));
    }

    public function bulkDestroy(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-pages-delete'));

        $itemsIDToDelete = $request->post('bulkID');
        $itemsIDToDelete = json_decode($itemsIDToDelete, true);

        foreach ($itemsIDToDelete as $itemIDToDelete) {
            $item = Page::find($itemIDToDelete);
            if ($item->type == $this->contentType) {
                $item->delete();
            }
        }

        return redirect()->back()->with('success', __(':count pages has been deleted!', ['count' => count($itemsIDToDelete)]));
    }
}