<?php

namespace App\Http\Controllers\Main\Misc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;

class SitemapController extends Controller
{
    public $maximumItems = 3000;

    public function index()
    {
        $sitemaps = [];

        $postsTotal = Content::where('type', 'POST')->count();
        $postSitemapsTotal = ceil($postsTotal / $this->maximumItems);
        for ($i = 1; $i <= $postSitemapsTotal; $i++) {
            $sitemaps[] = route('sitemap.posts', ['index' => $i]);
        }

        return response()->view('main.misc.sitemap.sitemaps-index', compact('sitemaps'), 200)->header('Content-Type', 'application/xml');
    }

    public function sitemapPosts($index)
    {
        $maximumItems = $this->maximumItems;

        $skipItems = ($index - 1) * $maximumItems;

        $posts = Content::where('type', 'POST')
            ->orderBy('id', 'ASC')
            ->skip($skipItems)
            ->take($maximumItems)
            ->get();

        $urls = [];

        foreach ($posts as $post) {
            $urls[] = [
                'loc'       => route('post', ['post' => $post]),
                'lastmod'   => $post->updated_at->format('Y-m-d'),
            ];
        }

        return response()->view('main.misc.sitemap.sitemap', compact('urls'), 200)->header('Content-Type', 'application/xml');
    }
}
