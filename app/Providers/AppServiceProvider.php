<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Inertia::share([
            'auth' => fn () => auth('web')->check()
                ? (function () {
                    /** @var User $user */
                    $user = auth('web')->user();

                    return [
                        'user' => $user,
                        'roles' => $user->roles->pluck('name'),
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                    ];
                })()
                : null,
        ]);
    }
}
