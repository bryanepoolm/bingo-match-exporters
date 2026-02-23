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
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verify Email Address - Bingo')
                ->greeting('Hello!')
                ->line('Welcome to Bingo! Please click the button below to verify your email address and continue setting up your company profile.')
                ->action('Verify Email Address', $url)
                ->line('If you did not create an account with Bingo, no further action is required.');
        });
    }
}
