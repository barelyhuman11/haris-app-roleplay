<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Permission;
use App\Models\Content as Post;
use App\Tables\PostsTable;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    private $contentType = 'POST';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-posts-index'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Posts') => '',
        ];

        $postsTable = new PostsTable();
        $postsTable->setPermissions($userAuth);
        $postsTable->prepare();

        return view('admin.posts.index', compact('breadcrumb', 'postsTable'));
    }

    public function indexData(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-posts-index'));

        $queryLimit = $request->query('limit', 10);
        $queryOffset = $request->query('offset', 0);
        $querySort = $request->query('sort', 'id');
        $queryOrder = $request->query('order', 'desc');
        $querySearch = $request->query('search');

        $postsCount = Post::type($this->contentType)->count();

        $posts = Post::type($this->contentType)
            ->when($querySearch, function ($query) use ($querySearch) {
                $query->where('title', 'like', '%' . $querySearch . '%');
            });
        $postsCountFiltered = $posts->count();

        $posts = $posts->orderBy($querySort, $queryOrder)
            ->skip($queryOffset)
            ->take($queryLimit)
            ->get();

        return [
            'total' => $postsCountFiltered,
            'totalNotFiltered' => $postsCount,
            'rows' => $posts->map(function ($post) use ($userAuth) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'status' => $post->status == 'PUBLISH' ? '<span class="badge badge-info">' . __('Published') . '</span>' : '<span class="badge badge-secondary">' . __('Draft') . '</span>',
                    'slug' => $post->slug,
                    'menu' => view('admin.posts._menu', ['post' => $post, 'userAuth' => $userAuth])->render(),
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
        $this->authorize('auth-check', $userAuth->authorize('admin-posts-create'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Posts') => route('admin.posts.index'),
            __('Create Post') => '',
        ];

        return view('admin.posts.create', compact('breadcrumb'));
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
        $this->authorize('auth-check', $userAuth->authorize('admin-posts-create'));

        if ($request->post_slug) {
            $postSlugRule = ['between:1,200', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:contents,slug'];
            $postSlug = $request->post_slug;
        }
        else {
            $postSlugRule = ['nullable'];
            $postSlug = '';
        }

        $request->validate([
            'post_title' => ['required', 'between:1,200'],
            'post_content' => [],
            'post_description' => [],
            'post_slug' => $postSlugRule,
            'status' => ['required', 'in:PUBLISH,DRAFT'],
        ]);

        Post::create([
            'user_id' => $request->user()->id,
            'slug' => $postSlug,
            'title' => $request->post_title,
            'excerpt' => $request->post_description,
            'content' => $request->post_content,
            'status' => $request->status,
            'type' => 'POST',
        ]);

        return redirect()->route('admin.posts.index')->with('success', __('Post has been created!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post ${{ modelInstance}}
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        if ($post->type != $this->contentType) {
            abort(404);
        }

        return redirect()->route('admin.posts.index');
    }

    /**
     * Show the form for editing the specified resource.
     * @param  \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Post $post)
    {
        if ($post->type != $this->contentType) {
            abort(404);
        }

        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-posts-edit'));

        $breadcrumb = [
            __('Dashboard') => route('admin.index'),
            __('Posts') => route('admin.posts.index'),
            __('Edit Post') => '',
        ];

        return view('admin.posts.edit', compact('post', 'breadcrumb'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        if ($post->type != $this->contentType) {
            abort(404);
        }

        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-posts-edit'));

        $validated = $request->validate([
            'post_title' => ['required', 'between:1,200'],
            'post_content' => [],
            'post_description' => [],
            'post_slug' => ['required', 'between:1,200', 'regex:/^[a-zA-Z0-9_-]+$/', Rule::unique('contents', 'slug')->ignore($post->id)],
            'status' => ['required', 'in:PUBLISH,DRAFT'],
        ]);

        $post->update([
            'slug' => $request->post_slug,
            'title' => $request->post_title,
            'excerpt' => $request->post_description,
            'content' => $request->post_content,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.posts.edit', ['post' => $post])->with('success', __('Post has been updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Post $post)
    {
        if ($post->type != $this->contentType) {
            abort(404);
        }
        
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-posts-delete'));

        $post->delete();

        return redirect()->back()->with('success', __('Post has been deleted!'));
    }

    public function bulkDestroy(Request $request)
    {
        $userAuth = new Permission($request->user()->group, 'admin');
        $this->authorize('auth-check', $userAuth->authorize('admin-posts-delete'));

        $itemsIDToDelete = $request->post('bulkID');
        $itemsIDToDelete = json_decode($itemsIDToDelete, true);

        foreach ($itemsIDToDelete as $itemIDToDelete) {
            $item = Post::find($itemIDToDelete);
            if ($item->type == $this->contentType) {
                $item->delete();
            }
        }

        return redirect()->back()->with('success', __(':count Posts has been deleted!', ['count' => count($itemsIDToDelete)]));
    }
}