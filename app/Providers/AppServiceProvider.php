<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useTailwind();

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasPermission($ability)) {
                return true;
            }
        });

        \Illuminate\Support\Facades\View::composer(['layouts.public', 'pages.*', 'welcome'], function ($view) {
            try {
                $view->with('siteSettings', \App\Models\SystemSetting::getAllSettings());
                $view->with('siteFaqs', \App\Models\SystemSetting::getFaqs());
            } catch (\Throwable $e) {
                $view->with('siteSettings', []);
                $view->with('siteFaqs', []);
            }
        });
    }

}
