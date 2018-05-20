<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Aquí puede definir cómo desea que los usuarios sean autenticados para Lumen
        // solicitud. La devolución de llamada que recibe la instancia de solicitud entrante
        // debería devolver una instancia de usuario o null. Usted es libre de obtener
        // la instancia del Usuario a través de un token API o cualquier otro método necesario.

        // Nota importante el token a utilizar debe estar definido en el CORS middleware
        // 'vendor/Vluzrmos/LumenCors/CorsService' de lo contrario no pasara.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $user = User::where('Authorization', $request->header('Authorization'))->first();
                return $user;
            }
        });
    }
}
