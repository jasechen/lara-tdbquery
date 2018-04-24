<?php

namespace Jasechen\Tdbquery;

use Jasechen\Tdbquery\Tdbquery;
use Illuminate\Support\ServiceProvider;

class TdbqueryServiceProvider extends ServiceProvider
{

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton(Tdbquery::class, function () {
            return new Tdbquery();
        });
    }

}