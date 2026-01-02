<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('expenses', ExpenseController::class);
    Route::post('/expenses/ocr', App\Http\Controllers\ExpenseOCRController::class)->name('expenses.ocr');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('budgets', BudgetController::class)->except(['show']);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/change-language', function() {
        $lang = request('language');
        if (in_array($lang, ['en', 'id', 'es', 'zh', 'ko', 'ja'])) {
            auth()->user()->update(['preferred_language' => $lang]);
        }
        return back();
    })->name('change.language');
});

require __DIR__.'/auth.php';
