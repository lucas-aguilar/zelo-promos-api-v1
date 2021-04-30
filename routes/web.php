<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/{id_oferta}/{link_alias}', 'LandingPageController@index');
Route::get('/{location_link}', 'LocationPageController@index');

// Route::get('/', function () {
//     return view('lp');
// });




Route::get('storage/promo-images/{location_folder}/{filename}', function ($location_folder, $filename)
{
    $path = storage_path('app/public/promo-images/' . $location_folder . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('storage/logo-images/{location_id_folder}/{filename}', function ($location_id_folder, $filename)
{
    $path = storage_path('app/public/logo-images/' . $location_id_folder . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('storage/cover-images/{location_id_folder}/{filename}', function ($location_id_folder, $filename)
{
    $path = storage_path('app/public/cover-images/' . $location_id_folder . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});