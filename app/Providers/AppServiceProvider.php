<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\trx;
use App\Observers\TransactionObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use Illuminate\Support\Facades\URL;
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
      
    
}
}
