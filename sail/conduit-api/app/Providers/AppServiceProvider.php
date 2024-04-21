<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('unique_in_array', function ($attribute, $value, $parameters, $validator) {
            if(is_array($value) || $value instanceof \Countable) {
                return count($value) === count(array_unique($value));
            } else {
                return false;
            }
        });
    }
}
