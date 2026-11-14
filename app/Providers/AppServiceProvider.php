<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registrar los servicios de la aplicación.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializar los servicios de la aplicación.
     */
    public function boot(): void
    {
        // Mantener la configuración predeterminada de Laravel.
        $this->configureDefaults();

        // ============================================
        // PERMISOS DEL SUPER ADMIN
        // ============================================

        Gate::before(function ($user, string $ability) {

            // El SUPER ADMIN tiene acceso a todos
            // los permisos evaluados mediante Gate.

            if ($user->hasRole('SUPER ADMIN', 'web')) {
                return true;
            }

            // Los demás usuarios serán evaluados
            // según sus permisos asignados en Spatie.

            return null;
        });
    }

    /**
     * Configurar los comportamientos predeterminados.
     */
    protected function configureDefaults(): void
    {
        // Fechas inmutables.
        Date::use(CarbonImmutable::class);

        // Proteger comandos destructivos en producción.
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        // Reglas de seguridad para contraseñas.
        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
