<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Schema::defaultStringLength(191);

        /* ADD THIS LINES */
        $this->commands([
            InstallCommand::class,
            ClientCommand::class,
            KeysCommand::class,
        ]);

        View::composer('*', function ($view) {
            $excluded = [
                'api',
                'setup',
                'update',
                'password',
                'online_store',
            ];

            $firstSegment = Request::segment(1); // Get the first segment of the URL

            if (! in_array($firstSegment, $excluded)) {
                $view->with('app_settings', Setting::first());
                $view->with('categories', \App\Models\Category::with('subcategories')->orderBy('name')->get());
            }

            if (auth()->check()) {
                $view->with('unread_notifications_count', \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count());
            } else {
                $view->with('unread_notifications_count', 0);
            }
        });

        // Set the default guard to 'store' for all 'store/*' routes
        $this->app['router']->matched(function (\Illuminate\Routing\Events\RouteMatched $event) {
            if ($event->route->action['middleware'] === 'auth.store') {
                Auth::shouldUse('store');
            }
        });
    }
}
