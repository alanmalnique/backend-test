<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'App\\Http\\Controllers\\',
], function () {
    Route::resource('customers', 'CustomerController', ['only' => ['show', 'store', 'update', 'destroy']]);

    Route::group([
        'prefix' => 'accounts',
    ], function() {
        Route::get('{id}', 'AccountController@getBalance');
        Route::post('{id}/deposit', 'AccountController@deposit');
        Route::post('{id}/withdraw', 'AccountController@withdraw');
        Route::post('transfer', 'AccountController@transfer');
    });
});
