<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dusterio\LumenPassport\LumenPassport;
use Carbon\Carbon;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
		LumenPassport::routes($this->app);
    	LumenPassport::tokensExpireIn(Carbon::now()->addMinutes(10));
        // LumenPassport::refreshTokensExpireIn(Carbon::now()->addMinutes(10));
    }
}
