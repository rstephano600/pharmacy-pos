<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use App\Models\MedicineBatch;
// use App\Models\SaleItem;
// use App\Observers\MedicineBatchObserver;
use App\Observers\SaleItemObserver;

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
        // MedicineBatch::observe(MedicineBatchObserver::class);
        // SaleItem::observe(SaleItemObserver::class);
    }

}
