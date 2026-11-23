<?php

namespace App\Http\Controllers\Main\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content as Post;
use App\Helpers\MetaData;
use App\Helpers\OpenGraph;

class PostController extends Controller
{
    protected $contentType = 'POST';

    public function index(Post $post)
    {
        if ($post->type != $this->contentType) {
            abort(404);
        }

        // Page data
        $pageTitle = $post->title;
        $breadcrumb = [
            __('Home') => route('index'),
            $pageTitle => '',
        ];

        // Related posts
        $relatedPosts = Post::type($this->contentType)
            ->where('id', '<', $post->id)
            ->orderBy('id', 'DESC')
            ->take(5)
            ->get();
        if (count($relatedPosts) == 0) {
            $relatedPosts = Post::type($this->contentType)
                ->where('id', '>', $post->id)
                ->orderBy('id', 'DESC')
                ->take(5)
                ->get();
        }

        // Generate meta data
        $metaData = new MetaData();
        $metaData->canonical(route('post', ['post' => $post]));

        if (!empty(trim($post->excerpt))) {
            $metaData->desc(trim($post->excerpt));
        }

        // Generate open graph
        $openGraph = new OpenGraph($this->options);
        $openGraph->url(route('post', ['post' => $post]));
        $openGraph->title($pageTitle);

        if (!empty(trim($post->excerpt))) {
            $openGraph->desc(trim($post->excerpt));
        }
        
        return view('main.contents.post', compact('post', 'pageTitle', 'breadcrumb', 'relatedPosts', 'metaData', 'openGraph'));
    }
}
