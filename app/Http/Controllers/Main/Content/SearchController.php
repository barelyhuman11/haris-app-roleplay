<?php

namespace App\Http\Controllers\Main\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content as Post;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('q');

        if (!$searchQuery) {
            return redirect()->route('index');
        }

        $posts = Post::type('POST')
            ->with(['contentMeta'])
            ->where(function ($query) use ($searchQuery) {
                $searchQuery = trim($searchQuery);
                $searchQuery = preg_replace('/\s+/', ' ', $searchQuery);
                $searchQuery = explode(' ', $searchQuery);
                foreach ($searchQuery as $search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                }
            })
            ->simplePaginate(24)
            ->withQueryString();

        return view('main.contents.search', compact('searchQuery', 'posts'));
    }
}
