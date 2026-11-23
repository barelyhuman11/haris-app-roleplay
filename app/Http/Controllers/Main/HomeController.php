<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Helpers\MetaData;
use App\Helpers\OpenGraph;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $posts = Content::type('POST')
            ->with(['contentMeta'])
            ->orderBy('id', 'DESC')
            ->simplePaginate(24);

        // Page data
        $currentPage = $request->query('page', 1);

        $pageTitle = $this->options->get('home_page_title');
        if (empty($pageTitle)) {
            $pageTitle = $this->options->get('site_title', config('app.name'));
        }

        $pageDescription = $this->options->get('home_page_meta_description');

        // Generate meta data
        $metaData = new MetaData();

        if ($currentPage == 1) {
            $metaData->canonical(route('index'));
        }
        else {
            $metaData->canonical(route('index', ['page' => $currentPage]));

            $pagePosition = __('pagination.page_position', ['position' => $currentPage]);

            $pageTitle .= ' - ' . $pagePosition;

            if (!empty($pageDescription)) {
                $pageDescription = $pagePosition . '. ' . $pageDescription;
            }
        }

        if (!empty(trim($pageDescription))) {
            $metaData->desc($pageDescription);
        }

        if ($posts->hasMorePages()) {
            $nextPage = $currentPage + 1;
            $metaData->linkRelNext(route('index', ['page' => $nextPage]));
        }

        if (!$posts->onFirstPage()) {
            if ($currentPage == 2) {
                $metaData->linkRelPrev(route('index'));
            }
            else {
                $prevPage = $currentPage - 1;
                $metaData->linkRelPrev(route('index', ['page' => $prevPage]));
            }
        }

        // Generate open graph
        $openGraph = new OpenGraph($this->options);

        if ($currentPage == 1) {
            $openGraph->url(route('index'));
        }
        else {
            $openGraph->url(route('index', ['page' => $currentPage]));
        }

        $openGraph->title($pageTitle);
        $openGraph->desc($pageDescription);

        return view('main.index', compact('posts', 'metaData', 'openGraph', 'pageTitle'));
    }
}
