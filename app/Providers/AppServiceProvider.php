<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Category;

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
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('staff', function ($user) {
            return in_array($user->role, ['staff', 'admin']);
        });

        Gate::define('borrower', function ($user) {
            return in_array($user->role, ['borrower', 'staff', 'admin']);
        });

        // Provide categories to header component for category filtering links
        View::composer('components.header', function ($view) {
            $view->with('categories', Category::all());
        });

        // Provide categories to filter component
        View::composer('components.filter', function ($view) {
            $view->with('categories', Category::select(['name','cate_id'])->orderBy('name')->get());
        });
    }
}
