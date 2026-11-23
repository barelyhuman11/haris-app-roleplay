<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\AdminComposer;
use App\View\Composers\MemberComposer;
use App\Helpers\Options;

class ViewServiceProvider extends ServiceProvider
{

    public function boot()
    {
        View::composer('admin/*', AdminComposer::class);
        View::composer('main/*', MemberComposer::class);
        
        try {
            $options = new Options();
            View::share('options', $options);
        }
        catch (\Illuminate\Database\QueryException $error) {
            View::share('options', null);
        }
    }

}