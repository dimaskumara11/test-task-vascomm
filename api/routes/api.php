<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Laravel\Passport\Exceptions\MissingScopeException;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return response()->json([
        "data" => [],
        "message" => "API Active"
    ]);
});

$router->post('/login', ['uses' => 'UserController@login']);

// require __DIR__ . '/../routes/api.php';
$router->group(['prefix' => 'user', 'middleware' => ['auth', 'scopes:user-access']], function () use ($router) {
    $router->group(['middleware' => ['scopes:user-read']], function () use ($router) {
        $router->get('/', ['uses' => 'UserController@index']);
    });
    $router->group(['middleware' => ['scopes:user-create']], function () use ($router) {
        $router->post('/', ['uses' => 'UserController@create']);
    });
    $router->group(['middleware' => ['scopes:user-update']], function () use ($router) {
        $router->put('/{id}', ['uses' => 'UserController@update']);
    });
    $router->group(['middleware' => ['scopes:user-delete']], function () use ($router) {
        $router->delete('/{id}', ['uses' => 'UserController@delete']);
    });
});

$router->group(['prefix' => 'product', 'middleware' => ['auth', 'scopes:product-access']], function () use ($router) {
    $router->group(['middleware' => ['scopes:product-read']], function () use ($router) {
        $router->get('/', ['uses' => 'ProductController@index']);
    });
    $router->group(['middleware' => ['scopes:product-create']], function () use ($router) {
        $router->post('/', ['uses' => 'ProductController@create']);
    });
    $router->group(['middleware' => ['scopes:product-update']], function () use ($router) {
        $router->post('/{id}', ['uses' => 'ProductController@update']);
    });
    $router->group(['middleware' => ['scopes:product-delete']], function () use ($router) {
        $router->delete('/{id}', ['uses' => 'ProductController@delete']);
    });
});
