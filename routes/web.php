<?php

use App\Http\Controllers\EmailController;
use App\Http\Controllers\HomeController;
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

//sources: indeed,remoteio,ziprecluter,glassdoor,larajobs,authenticjobs,weworkremotely
// linkedin https://www.linkedin.com/jobs/search/?f_WT=2&geoId=92000000&keywords=laravel
//Route::get('/', function () {
//    (new \App\Importers\Remoteio())->import();
//});


Route::get('/', [HomeController::class, 'index']);
Route::post('/email-subscribe', [EmailController::class, 'store'])->name('email.subscribe');
