<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('settings.frontend_url') . 'reset-password?token=' . $token . '&email=' . $user->email;
        });
        \App\Models\Group::observe(\App\Observers\GroupObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        Relation::morphMap([
        'group'  => \App\Models\Group::class,
        'role'   => \App\Models\GroupRole::class,
        'member' => \App\Models\GroupMember::class,
        'user'   => \App\Models\User::class,
    ]);
    }
}
