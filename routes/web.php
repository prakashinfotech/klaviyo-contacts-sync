<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact-us', [\App\Http\Controllers\ContactUsController::class, 'index'])->name('contact-us');
Route::post('/contact-us-store', [\App\Http\Controllers\ContactUsController::class, 'store'])->name('contact-us-store');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('contact', 'App\Http\Controllers\ContactController')->names('contact');
Route::post('list', ['App\Http\Controllers\ContactController', 'index'])->name('contact-list');
Route::post('contact-delete', ['App\Http\Controllers\ContactController', 'delete'])->name('contact-delete');

Route::get('import', [App\Http\Controllers\ContactController::class, 'import'])->name('import');
Route::post('import-contacts', [App\Http\Controllers\ContactController::class, 'importContacts'])->name('import-contacts');

Route::get('queue-test', function(){

    \App\Models\Contact::get()->map(function ($contact) {
        dispatch(new App\Jobs\SendContactJob($contact));
    });

    dd('done');
});
