<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('money', function ($money) {
            return "<?php echo (new NumberFormatter('en_US', NumberFormatter::PADDING_POSITION))->format($money); ?>";
        });
        
        Str::macro('initials', function($text) {
            return Str::of($text)
                ->explode(' ', 2)
                ->map(fn($word) => strtoupper($word[0]))
                ->join('');
        });
    }
}
