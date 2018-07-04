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

Route::get('/', 'PagesController@index');

Route::resource('players','PlayersController');

// Route::get('test', 'PagesController@getTest');

Auth::routes();

Route::get('/dashboard', [ 'as' => 'dashboard', 'uses' => 'PlayersController@index']);


// Route::get('status', 'PagesController@getStatus');

Route::resource('status','StatusController');

Route::resource('test','TestsController');

Route::post('/sendmail', function (\Illuminate\Http\Request $request, \Illuminate\Mail\Mailer $mailer){
	$mailer
		->to($request->input('mail'))
		->queue(new \App\Mail\PlayingEmail('HB'));
	return redirect()->back()->with('success', 'Email sent!');
})->name('sendmail');

// Route::get('scheduler', function (){
//    \Illuminate\Support\Facades\Artisan::call('schedule:run');
// });

Route::get('scheduler', 'ArtisanController@handle');