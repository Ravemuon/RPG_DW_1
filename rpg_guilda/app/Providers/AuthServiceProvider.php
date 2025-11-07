<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Models
use App\Models\Campanha;
use App\Models\Sessao;
use App\Models\Personagem;

// Policies
use App\Policies\CampanhaPolicy;
use App\Policies\SessaoPolicy;
use App\Policies\PersonagemPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * O mapeamento das policies da aplicação.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Campanha::class => CampanhaPolicy::class,
        Sessao::class => SessaoPolicy::class,
        Personagem::class => PersonagemPolicy::class,
    ];

    /**
     * Registra quaisquer serviços de autenticação / autorização.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 🔹 Exemplo de Gate global (admin total)
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }
        });
    }
}
