<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Quiz routes
    Route::get('/quizzes', App\Http\Livewire\Quiz\QuizList::class)->name('quizzes');
    Route::get('/quiz/{category:slug}', App\Http\Livewire\Quiz\QuizShow::class)->name('quiz.show');
    Route::get('/quiz/{category:slug}/result', App\Http\Livewire\Quiz\QuizResult::class)->name('quiz.result');
});
