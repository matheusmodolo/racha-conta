<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Password::defaults(fn () => Password::min(8));

        // Só IP bloquearia uma rede inteira. Só e-mail deixaria qualquer um travar a conta de outra pessoa.
        RateLimiter::for('login', fn (Request $r) => Limit::perMinute(5)->by($r->string('email')->lower().'|'.$r->ip()));
    }
}
