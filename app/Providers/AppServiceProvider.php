<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BigQueryService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BigQueryService::class, function () {
            return new BigQueryService(storage_path('app/credentials.json'), 'messages');
        });
    }
}
