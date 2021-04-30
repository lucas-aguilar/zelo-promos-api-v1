<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckIfUserIsEnabled;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'RegisterController@register');
Route::post('/obrigado', 'LandingPageController@store');


Route::middleware('auth:api')->group( function () {
	Route::get('mailchimp/lists', 'MailChimpController@getLists')->middleware(CheckIfUserIsEnabled::class);
	Route::get('mailchimp/campaigns', 'MailChimpController@getCampaigns')->middleware(CheckIfUserIsEnabled::class);
	Route::get('mailchimp/automations', 'MailChimpController@getAutomations')->middleware(CheckIfUserIsEnabled::class);
	Route::post('mailchimp/sendmail', 'MailChimpController@sendEmailTo')->middleware(CheckIfUserIsEnabled::class);

	Route::resource('locations', 'LocationController')->middleware(CheckIfUserIsEnabled::class);
    Route::delete('delete/location/{id}', 'LocationController@delete')->middleware(CheckIfUserIsEnabled::class);
    Route::put('enable/location/{id}', 'LocationController@enable')->middleware(CheckIfUserIsEnabled::class);
    Route::post('upload_image/location', 'LocationController@upload_image')->middleware(CheckIfUserIsEnabled::class);
    Route::delete('delete/promotion/{id}', 'PromotionController@delete')->middleware(CheckIfUserIsEnabled::class);
    Route::put('enable/promotion/{id}', 'PromotionController@enable')->middleware(CheckIfUserIsEnabled::class);
    Route::get('currentuser', 'LocationController@getLoggedUser')->middleware(CheckIfUserIsEnabled::class);
	Route::resource('promotions', 'PromotionController')->middleware(CheckIfUserIsEnabled::class);
    Route::resource('leads', 'LeadController')->middleware(CheckIfUserIsEnabled::class);
	Route::put('promotions/update', 'PromotionController@updatePromo')->middleware(CheckIfUserIsEnabled::class);
    Route::get('promosale/reservations', 'PromotionController@getPromoSales')->middleware(CheckIfUserIsEnabled::class);
    Route::get('promosale/sold', 'PromotionController@getSoldPromoSales')->middleware(CheckIfUserIsEnabled::class);
    Route::post('promosale/setsold', 'PromotionController@setPromoSaleSold')->middleware(CheckIfUserIsEnabled::class);
});