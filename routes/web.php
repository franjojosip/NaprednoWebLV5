<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'users', 'middleware' => 'role:admin', 'as' => 'admin.'], function () {
    Route::get('/',  [\App\Http\Controllers\UserController::class, 'index']);
    Route::get('/update/{id}', [\App\Http\Controllers\UserController::class, 'update']);
    Route::put('/edit/{id}', [\App\Http\Controllers\UserController::class, 'edit']);
});


Route::get('locale/{locale}', function ($locale){
    Session::put('locale', $locale);

    if (request()->fullUrl() === redirect()->back()->getTargetUrl()) {
        return redirect('/');
    }

    return redirect()->back();
});


Route::group(['prefix' => 'tasks', 'as' => 'tasks.'], function () {
    //CRUD Tasks
    Route::get('/', [\App\Http\Controllers\TaskController::class, 'index']);
    Route::get('/add', [\App\Http\Controllers\TaskController::class, 'add']);
    Route::post('/create', [\App\Http\Controllers\TaskController::class, 'create']);
    Route::get('/update/{id}', [\App\Http\Controllers\TaskController::class, 'update']);
    Route::put('/edit/{id}', [\App\Http\Controllers\TaskController::class, 'edit']);
    Route::delete('/delete/{id}', [\App\Http\Controllers\TaskController::class, 'delete']);

    //Student choose thesis
    Route::get('/select/{id}', [\App\Http\Controllers\TaskController::class, 'select']);

    //Choose student for thesis
    Route::get('/show/{id}', [\App\Http\Controllers\TaskController::class, 'show']);
    Route::put('/choose/{id}', [\App\Http\Controllers\TaskController::class, 'choose']);

    //Sort chosen tasks
    Route::get('/sort', [\App\Http\Controllers\TaskController::class, 'sort']);
    Route::post('/update-sort', [\App\Http\Controllers\TaskController::class, 'updateSort']);
});
