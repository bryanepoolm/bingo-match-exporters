<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Web\CompanyController;
use App\Http\Controllers\Web\ExplorerController;
use App\Http\Controllers\Web\MatchController;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/aviso-de-privacidad', function () {
    return Inertia::render('PrivacyPolicy');
})->name('privacy-policy');


Route::get('/email/verify', function () {
    return Inertia::render('Auth/VerifyEmail');
})->middleware('auth')->name('verification.notice');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/company/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::post('/company/update', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('/company/document', [CompanyController::class, 'destroyDocument'])->name('company.document.destroy');
    
    Route::get('/explorer', [ExplorerController::class, 'index'])->name('explorer.index');
    Route::get('/explorer/{company}', [ExplorerController::class, 'show'])->name('explorer.show');
    
    // Connection Request
    Route::get('/connect/{company}', [MatchController::class, 'create'])->name('matches.create');
    Route::post('/connect/{company}', [MatchController::class, 'store'])->name('matches.store');
    
    Route::get('/requests', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/requests/{match}', [MatchController::class, 'show'])->name('matches.show')->withTrashed();
    Route::get('/requests/{match}/edit', [MatchController::class, 'edit'])->name('matches.edit');
    Route::put('/requests/{match}', [App\Http\Controllers\Web\MatchController::class, 'update'])->name('matches.update');
    Route::delete('/requests/{match}', [App\Http\Controllers\Web\MatchController::class, 'destroy'])->name('matches.destroy');
    Route::post('/requests/{match}/reject', [App\Http\Controllers\Web\MatchController::class, 'reject'])->name('matches.reject');
    Route::post('/requests/{match}/accept', [App\Http\Controllers\Web\MatchController::class, 'accept'])->name('matches.accept');
    
    Route::get('/partners', [App\Http\Controllers\Web\PartnerController::class, 'index'])->name('partners.index');

    // Chat
    Route::get('/requests/{match}/messages', [App\Http\Controllers\Web\ChatController::class, 'index'])->name('matches.messages.index');
    Route::post('/requests/{match}/messages', [App\Http\Controllers\Web\ChatController::class, 'store'])->name('matches.messages.store');
    Route::post('/requests/{match}/status', [\App\Http\Controllers\Web\MatchController::class, 'updateStatus'])->name('matches.status.update');
    
    Route::get('/api/hs-codes/search', [ProductController::class, 'searchHsCodes'])->name('api.hs-codes.search');
    Route::resource('products', ProductController::class);
});
