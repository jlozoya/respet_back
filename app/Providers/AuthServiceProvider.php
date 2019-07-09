<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\LumenPassport\LumenPassport;

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
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];
    /**
     * Register any authentication / authorization services.
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
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });

        LumenPassport::routes($this->app->router);
    }
}
