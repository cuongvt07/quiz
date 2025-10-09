<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================
// FRONTEND ROUTES
// ==========================
Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('home');

Route::prefix('exam-list')->name('exams.')->group(function() {
    Route::get('/', [\App\Http\Controllers\Frontend\ExamController::class, 'index'])->name('list');
    Route::get('/type/{type}', [\App\Http\Controllers\Frontend\ExamController::class, 'index'])->name('list.type');
    Route::get('/detail/{exam}', [\App\Http\Controllers\Frontend\ExamController::class, 'show'])->name('show');

    Route::middleware('auth')->group(function() {
        Route::post('/start/{exam}', [\App\Http\Controllers\Frontend\ExamController::class, 'start'])->name('start');
        Route::get('/take/{attempt}', [\App\Http\Controllers\Frontend\ExamController::class, 'take'])->name('take');
        Route::post('/{attempt}/save-answer', [\App\Http\Controllers\Frontend\ExamController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/{attempt}/submit', [\App\Http\Controllers\Frontend\ExamController::class, 'submit'])->name('submit');
        Route::get('/{attempt}/result', [\App\Http\Controllers\Frontend\ExamController::class, 'result'])->name('result');
    });
});

// ==========================
// USER ROUTES (AUTHENTICATED)
// ==========================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    // ✅ redirect theo role
    // Route dashboard đã bị loại bỏ theo yêu cầu

    // Hồ sơ người dùng
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // Lịch sử thi
    Route::prefix('exam-history')->name('exam-history.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\ExamHistoryController::class, 'index'])->name('index');
        Route::get('/detail/{attempt}', [\App\Http\Controllers\User\ExamHistoryController::class, 'show'])->name('show');
    });

    // Bài thi người dùng
    Route::prefix('exams')->name('user.exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\UserExamController::class, 'index'])->name('index');
        Route::get('/{exam}', [\App\Http\Controllers\User\UserExamController::class, 'show'])->name('show');
        Route::post('/{exam}/start', [\App\Http\Controllers\User\UserExamController::class, 'start'])->name('start');
        Route::get('/{attempt}/take', [\App\Http\Controllers\User\UserExamController::class, 'take'])->name('take');
        Route::post('/{attempt}/save-answer', [\App\Http\Controllers\User\UserExamController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/{attempt}/submit', [\App\Http\Controllers\User\UserExamController::class, 'submit'])->name('submit');
        Route::get('/{attempt}/result', [\App\Http\Controllers\User\UserExamController::class, 'result'])->name('result');
    });
});

// ==========================
// ADMIN AUTH ROUTES
// ==========================
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])
    ->name('admin.login')
    ->middleware(['guest']);

Route::post('/admin/login', [AdminController::class, 'login'])
    ->name('admin.login.submit')
    ->middleware(['role.redirect']);

Route::post('/admin/logout', [AdminController::class, 'logout'])
    ->name('admin.logout');

// ==========================
// ADMIN ROUTES (PROTECTED)
// ==========================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Gói nâng cấp
    Route::get('subscription-plans', [\App\Http\Controllers\SubscriptionPlanController::class, 'index'])->name('subscription_plans.index');
    Route::post('subscription-plans', [\App\Http\Controllers\SubscriptionPlanController::class, 'store'])->name('subscription_plans.store');
    Route::put('subscription-plans/{subscription_plan}', [\App\Http\Controllers\SubscriptionPlanController::class, 'update'])->name('subscription_plans.update');
    Route::delete('subscription-plans/{subscription_plan}', [\App\Http\Controllers\SubscriptionPlanController::class, 'destroy'])->name('subscription_plans.destroy');

    // Thành viên đăng ký gói
    Route::get('subscriptions', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/create', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('subscriptions', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('subscriptions/{subscription}', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::delete('subscriptions/{subscription}', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    Route::post('subscriptions/{subscription}/renew', [\App\Http\Controllers\Admin\SubscriptionRenewalController::class, 'renew'])->name('subscriptions.renew');

    // Bài thi
    Route::get('exams/nangluc', [\App\Http\Controllers\ExamController::class, 'indexNangLuc'])->name('exams.nangluc');
    Route::get('exams/tuduy', [\App\Http\Controllers\ExamController::class, 'indexTuDuy'])->name('exams.tuduy');
    Route::post('exams/{exam}/questions/batch-update', [\App\Http\Controllers\ExamController::class, 'batchUpdateQuestions'])->name('exams.questions.batch-update');
    Route::post('exams/{exam}/questions/{question}/toggle-status', [\App\Http\Controllers\ExamController::class, 'toggleQuestionStatus'])->name('exams.questions.toggle-status');
    Route::post('exams/{exam}/questions/import', [\App\Http\Controllers\ExamController::class, 'importQuestions'])->name('exams.questions.import');
    Route::get('exams/questions/template', [\App\Http\Controllers\ExamController::class, 'downloadTemplate'])->name('exams.questions.template');
    Route::resource('exams', \App\Http\Controllers\ExamController::class)->names('exams');

    // Câu hỏi
    Route::post('exams/{exam}/questions', [\App\Http\Controllers\ExamQuestionController::class, 'store'])->name('exam.questions.store');
    Route::put('exams/{exam}/questions/{question}', [\App\Http\Controllers\ExamQuestionController::class, 'update'])->name('exam.questions.update');
    Route::delete('exams/{exam}/questions/{question}', [\App\Http\Controllers\ExamQuestionController::class, 'destroy'])->name('exam.questions.destroy');

    // Danh mục
    Route::get('categories', [AdminController::class, 'indexCategories'])->name('categories.index');
    Route::get('categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('categories/{category}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Môn học
    Route::get('subjects', [\App\Http\Controllers\SubjectController::class, 'index'])->name('subjects.index');
    Route::get('subjects/create', [\App\Http\Controllers\SubjectController::class, 'create'])->name('subjects.create');
    Route::post('subjects', [\App\Http\Controllers\SubjectController::class, 'store'])->name('subjects.store');
    Route::get('subjects/{subject}/edit', [\App\Http\Controllers\SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('subjects/{subject}', [\App\Http\Controllers\SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('subjects/{subject}', [\App\Http\Controllers\SubjectController::class, 'destroy'])->name('subjects.destroy');

    // Tài khoản
    Route::get('accounts/admins', [\App\Http\Controllers\UsersController::class, 'admins'])->name('accounts.admins');
    Route::get('accounts/users', [\App\Http\Controllers\UsersController::class, 'users'])->name('accounts.users');
    Route::resource('users', \App\Http\Controllers\UsersController::class)->names('users');
    Route::post('admins', [\App\Http\Controllers\AdminAccountController::class, 'store'])->name('admins.store');
    Route::put('admins/{admin}', [\App\Http\Controllers\AdminAccountController::class, 'update'])->name('admins.update');
    Route::delete('admins/{admin}', [\App\Http\Controllers\AdminAccountController::class, 'destroy'])->name('admins.destroy');

    // Import
    Route::get('/import', [ImportController::class, 'showImportForm'])->name('import.form');
    Route::post('/import', [ImportController::class, 'import'])->name('import');
});

// ==========================
// REDIRECT SAU ĐĂNG NHẬP
// ==========================
Route::get('/redirect-after-login', function () {
    if (auth()->check()) {
        return (auth()->user()->is_admin ?? false)
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }
    return redirect('/login');
})->name('redirect.after.login');

// ==========================
// FALLBACK ROUTE
// ==========================
Route::fallback(function () {
    return redirect()->route('home');
});
