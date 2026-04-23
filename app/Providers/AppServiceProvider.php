<?php

namespace App\Providers;

use App\Http\Middleware\DricCpBrandingAuthenticatedInertiaRequests;
use App\Http\Middleware\DricCpBrandingInertiaRequests;
use Illuminate\Support\ServiceProvider;
use Statamic\Facades\CP\Nav as CpNav;
use Statamic\Http\Middleware\CP\HandleAuthenticatedInertiaRequests;
use Statamic\Http\Middleware\CP\HandleInertiaRequests;
use Statamic\Statamic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(HandleInertiaRequests::class, DricCpBrandingInertiaRequests::class);
        $this->app->bind(HandleAuthenticatedInertiaRequests::class, DricCpBrandingAuthenticatedInertiaRequests::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Statamic::vite('app', [
            'input' => [
                'resources/js/cp.js',
                'resources/css/cp.css',
            ],
            'hotFile' => public_path('cp-hot'),
            'buildDirectory' => 'vendor/app',
        ]);

        CpNav::extend(function ($nav): void {
            $nav
                ->remove('Top Level', 'Dashboard')
                ->remove('Content', 'Collections')
                ->remove('Content', 'Navigation')
                ->remove('Content', 'Taxonomies')
                ->remove('Content', 'Assets')
                ->remove('Content', 'Globals')
                ->remove('Fields')
                ->remove('Tools')
                ->remove('Settings')
                ->remove('Users');

            $cp = url(config('statamic.cp.route'));

            $nav->content('Pages')
                ->url("{$cp}/collections/pages")
                ->icon('collections')
                ->order(10);

            $nav->content('Blog')
                ->url("{$cp}/collections/blog")
                ->icon('collections')
                ->order(20);

            $nav->content('Shop Page')
                ->url("{$cp}/collections/pages/entries/shop")
                ->icon('collections')
                ->order(30);

            $nav->content('Catalogue Page')
                ->url("{$cp}/collections/pages/entries/catalogue")
                ->icon('assets')
                ->order(40);

            $nav->content('Media Library')
                ->url("{$cp}/assets")
                ->icon('assets')
                ->order(50);

            $nav->content('Site Settings')
                ->url("{$cp}/globals/settings")
                ->icon('globals')
                ->order(60);

            $nav->content('Enquiries')
                ->url("{$cp}/forms/contact")
                ->icon('forms')
                ->order(70);
        });
    }
}
