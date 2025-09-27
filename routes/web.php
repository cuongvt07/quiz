<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImportController;

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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/categories', [AdminController::class, 'indexCategories'])->name('categories.index');
    Route::get('/subjects', [AdminController::class, 'indexSubjects'])->name('subjects.index');
    Route::get('/exams', [AdminController::class, 'indexExams'])->name('exams.index');
    Route::get('/import', [ImportController::class, 'showImportForm'])->name('import.form');
    Route::post('/import', [ImportController::class, 'import'])->name('import');
    Route::get('/exams/create', [AdminController::class, 'createExam'])->name('exams.create');
    Route::post('/exams', [AdminController::class, 'storeExam'])->name('exams.store');
    
    // Admin dashboard route
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Danh mục câu hỏi
    Route::get('categories', [AdminController::class, 'indexCategories'])->name('categories.index');
    Route::get('categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('categories/{category}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
});

// Admin authentication routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login')->middleware('guest');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Redirect to admin dashboard if authenticated
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
});
