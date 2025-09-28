
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
    // Bài thi chia 2 module theo type môn học
    Route::get('exams/nangluc', [\App\Http\Controllers\ExamController::class, 'indexNangLuc'])->name('exams.nangluc');
    Route::get('exams/tuduy', [\App\Http\Controllers\ExamController::class, 'indexTuDuy'])->name('exams.tuduy');
    Route::get('/categories', [AdminController::class, 'indexCategories'])->name('categories.index');
    Route::get('/subjects', [\App\Http\Controllers\SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [\App\Http\Controllers\SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [\App\Http\Controllers\SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}/edit', [\App\Http\Controllers\SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{subject}', [\App\Http\Controllers\SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [\App\Http\Controllers\SubjectController::class, 'destroy'])->name('subjects.destroy');
    Route::resource('exams', \App\Http\Controllers\ExamController::class)->names('exams');
    Route::get('/import', [ImportController::class, 'showImportForm'])->name('import.form');
    Route::post('/import', [ImportController::class, 'import'])->name('import');
    
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

    // Gói nâng cấp thành viên
    Route::get('subscription-plans', [\App\Http\Controllers\SubscriptionPlanController::class, 'index'])->name('subscription_plans.index');
    Route::post('subscription-plans', [\App\Http\Controllers\SubscriptionPlanController::class, 'store'])->name('subscription_plans.store');
    Route::put('subscription-plans/{subscription_plan}', [\App\Http\Controllers\SubscriptionPlanController::class, 'update'])->name('subscription_plans.update');
    Route::delete('subscription-plans/{subscription_plan}', [\App\Http\Controllers\SubscriptionPlanController::class, 'destroy'])->name('subscription_plans.destroy');

    
    // Quản lý tài khoản: 2 tab
    Route::get('accounts/admins', [\App\Http\Controllers\UsersController::class, 'admins'])->name('accounts.admins');
    Route::get('accounts/users', [\App\Http\Controllers\UsersController::class, 'users'])->name('accounts.users');
    // Nếu vẫn cần resource users cho AJAX CRUD
    Route::resource('users', \App\Http\Controllers\UsersController::class)->names('users');
    Route::post('admins', [\App\Http\Controllers\AdminAccountController::class, 'store'])->name('admins.store');
    Route::put('admins/{admin}', [\App\Http\Controllers\AdminAccountController::class, 'update'])->name('admins.update');
    Route::delete('admins/{admin}', [\App\Http\Controllers\AdminAccountController::class, 'destroy'])->name('admins.destroy');

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
