<?php

namespace App\Providers;

use App\Models\Depenses;
use App\Models\Recu;
use App\Policies\DepensesPolicy;
use App\Policies\RecuPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Recu::class => RecuPolicy::class,
        Depenses::class => DepensesPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
