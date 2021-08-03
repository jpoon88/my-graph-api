<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
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

Route::get('/', function() {
    return view('home');
 })->name('home');
 

Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');

Route::get('/login', [AuthController::class, 'signin'])->name('login')
  ->middleware('guest');;

Route::get('/callback', [AuthController::class, 'callback']);
Route::post('/logout', [AuthController::class, 'signout'])
        ->name('logout')
        ->middleware('auth');

Route::get('/calendar', [CalendarController::class,'index'])->name('calendar');
Route::post('/calendars', [CalendarController::class,'calendars'])->name('calendars');


Route::post('/events', [EventController::class,'list'])->name('events');
Route::post('/calendar/events', [EventController::class,'calendar_events'])->name('calendar_events');

Route::get('/people', [PeopleController::class, 'index'])->name('people');
        

Route::post('/test', [CalendarController::class,'test'])->name('test');

// Route::get('/test', [CalendarController::class,'test']);

