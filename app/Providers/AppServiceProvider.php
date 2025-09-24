<?php

namespace App\Providers;

use App\Models\Posts;
use App\Observers\PostsObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\MenuFrontend;
use Illuminate\Support\Facades\View;
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
        Paginator::useBootstrapFive();
        // Posts::observe(PostsObserver::class);
         View::composer('*', function ($view) {
        $menuTree = MenuFrontend::with('children')
            ->whereNull('parent_id')
            ->where('active', 1)
            ->orderBy('stt')
            ->get();

        $view->with('menuTree', $menuTree);
    });

    // $footerColumn2Menus = MenuFrontend::with('subMenus')
    //     ->whereRaw("FIND_IN_SET('footer', position)")
    //     ->where('footer_column', 2)
    //     ->whereNull('parent_id')
    //     ->where('active', 1)
    //     ->orderBy('stt')
    //     ->get();

    // $footerColumn3Menus = MenuFrontend::with('subMenus')
    //     ->whereRaw("FIND_IN_SET('footer', position)")
    //     ->where('footer_column', 3)
    //     ->whereNull('parent_id')
    //     ->where('active', 1)
    //     ->orderBy('stt')
    //     ->get();

    // View::share('footerColumn2Menus', $footerColumn2Menus);
    // View::share('footerColumn3Menus', $footerColumn3Menus);
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . env('GOOGLE_APPLICATION_CREDENTIALS'));
    }

}
