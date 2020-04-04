<?php

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
    return $router->app->version();
});
$router->get('version', 'v2\UsersController@version');
$router->post('submit', 'ScreeningController@submit');
$router->post('uii/submit', 'ScreeningController@UiiSubmit');

$router->post('daftar', 'AuthController@daftar');

$router->post(
    'auth/login',
    [
       'uses' => 'AuthController@authenticate',
    ]
);

$router->group(['prefix' => 'v2'], function () use ($router) {
    $router->post(
        'auth/login',
        [
           'uses' => 'AuthController@authenticate',
        ]
    );
});

$router->group([
                'middleware' => 'jwt.auth',
            ], function () use ($router) {
                /*
                 * Routes for resource screening
                 */

                $router->get('screening', 'ScreeningController@all');
                $router->get('screening/{id}', 'ScreeningController@get');
                $router->post('screening', 'ScreeningController@add');
                $router->put('screening/{id}', 'ScreeningController@put');
                $router->delete('screening/{id}', 'ScreeningController@remove');

                /*
                 * Routes for resource screening
                 */
                $router->group(['prefix' => 'uii'], function () use ($router) {
                    $router->get('screening', 'ScreeningController@getUii');
                    $router->get('screening/{id}', 'ScreeningController@get');
                    $router->post('screening', 'ScreeningController@add');
                    $router->put('screening/{id}', 'ScreeningController@put');
                    $router->delete('screening/{id}', 'ScreeningController@remove');
                });
            });
