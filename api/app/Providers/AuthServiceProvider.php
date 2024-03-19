<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
        $this->register();

        $menu = ["product", "user"];
        $scopes = [];
        foreach ($menu as $value) {
            $scopes["$value-access"] = "access $value";
            $scopes["$value-read"] = "read $value";
            $scopes["$value-create"] = "create $value";
            $scopes["$value-update"] = "update $value";
            $scopes["$value-delete"] = "delete $value";
        }
        Passport::tokensCan($scopes);
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });
    }
}
