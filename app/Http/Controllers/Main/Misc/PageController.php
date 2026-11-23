<?php

namespace App\Http\Controllers\Main\Misc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content as Page;
use App\Helpers\MetaData;
use App\Helpers\Text;

class PageController extends Controller
{
    public function page(Page $page)
    {
        if ($page->status == 'DRAFT' || $page->type != 'PAGE') {
            abort(404);
        }

        $breadcrumb = [
            __('Home') => route('index'),
            $page->title => '',
        ];

        // Generate meta data
        $metaData = new MetaData();
        $metaData->desc(Text::plain($page->content, 160));
        $metaData->canonical(route('page', ['page' => $page]));

        return view('main.misc.pages.page', compact('page', 'breadcrumb', 'metaData'));
    }
}