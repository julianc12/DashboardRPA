<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
        ], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/rpa/{id}/create', 'RpaCommandController@create');
    $router->get('/rpa/{id}/edit', 'RpaController@edit');

    $router->post('/rpa/data', 'RpaController@callDataDb');
    $router->post('/orquestacion/call', 'RpaOrquestacionController@callOrq');


    $router->post('/detener/rpa', 'RpaController@detener')->name('admin.home');

    $router->get('/rpa/{id}/{rpa}/edit', 'RpaCommandController@edit')->name('admin.home');
    $router->get('/rpa/transaction/{id}', 'TransactionController@show')->name('admin.home');
    $router->get('/rpa/{rpa}/{command}', 'RpaCommandController@showRpacommand')->name('admin.home');
    $router->post('/datos', 'RpaCommandController@InserData')->name('admin.home');
    $router->post('/update', 'RpaCommandController@UpdateData')->name('admin.home');
    $router->post('/rpcreate', 'RpaController@InsertRpa')->name('admin.home');
    $router->post('/rpedit', 'RpaController@UpdateRpa')->name('admin.home');
    $router->post('/rpedit', 'RpaController@UpdateRpa')->name('admin.home');
    $router->delete('/rpa/{id}/{deletes}', 'RpaCommandController@Delete')->name('admin.home');

    $router->get('/rpa/transaction/{idtran}/{iddeta}', 'TransactionDetailController@show')->name('admin.home');
    $router->resources(['/rpaproperties' => RpaPropertyController::class]);
    $router->resources(['/rpac/cguno_documentos_config' => CgunoDocumentosConfigController::class]);
    $router->resources(['/rpa/transaction' => TransactionController::class]);
    $router->resources(['/rpa/transaction_detail' => TransactionDetailController::class]);
    $router->resources(['/rpa/tokens' => AccessTokenController::class]);
    $router->resources(['/rpa' => RpaController::class]);
    // $router->resources(['/type' => TypeCommandController::class]);
    $router->resources(['/orquestacion' => RpaOrquestacionController::class]);
    $router->resources(['/url/endpoint' => RpaEndpointController::class]);
    $router->resources(['/orqrpa' => OrqRpaController::class]);
    $router->resources(['/path_manual' => UrlController::class]);
});
Route::get('/storage/images/{filename}', function ($filename) {
    $path = storage_path('app/public/images/' . $filename);

    $rute = url("rpa/rpa/edit");
    if (!File::exists($rute)) {
        abort(404);
    }

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});
