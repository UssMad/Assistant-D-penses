<?php

namespace App\Providers;

use App\Models\Recu;
use App\Policies\RecuPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Recu::class => RecuPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
