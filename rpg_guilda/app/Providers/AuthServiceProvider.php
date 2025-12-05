<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Campanha;
use App\Models\Sessao;
use App\Models\Personagem;
use App\Policies\CampanhaPolicy;
use App\Policies\SessaoPolicy;
use App\Policies\PersonagemPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Campanha::class => CampanhaPolicy::class,
        Sessao::class => SessaoPolicy::class,
        Personagem::class => PersonagemPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }
        });
    }
}
