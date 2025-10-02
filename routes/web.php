<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionAdminController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::redirect('/', '/login');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Public subscription routes
Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');

// Authenticated user subscription routes
Route::middleware(['auth'])->group(function () {
    Route::get('/subscription/apply/{plan}', [SubscriptionController::class, 'showApplicationForm'])->name('subscription.apply');
    Route::post('/subscription/apply/{plan}', [SubscriptionController::class, 'submitApplication'])->name('subscription.submit');
    Route::get('/subscription/status', [SubscriptionController::class, 'applicationStatus'])->name('subscription.status');
    Route::get('/subscription/proof/{application}', [SubscriptionController::class, 'downloadProof'])->name('subscription.proof.download');
    Route::post('/subscription/cancel/{application}', [SubscriptionController::class, 'cancelApplication'])->name('subscription.cancel');
});

// Content routes
Route::get('/content', [ContentController::class, 'index'])->name('content.index');
Route::middleware(['auth'])->group(function () {
    Route::get('/content/{content}', [ContentController::class, 'show'])->name('content.show');
    Route::get('/content/{content}/download', [ContentController::class, 'download'])->name('content.download');
    Route::get('/content/{content}/stream', [ContentController::class, 'stream'])->name('content.stream');
});

// Admin routes secured by Gate
Route::prefix('admin')->middleware(['auth', 'can:admin'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard.index');

    // Subscription Plan Management
    Route::get('/subscription/plans', [SubscriptionPlanController::class, 'index'])->name('admin.subscription.plans.index');
    Route::get('/subscription/plans/create', [SubscriptionPlanController::class, 'create'])->name('admin.subscription.plans.create');
    Route::post('/subscription/plans', [SubscriptionPlanController::class, 'store'])->name('admin.subscription.plans.store');
    Route::get('/subscription/plans/{plan}/edit', [SubscriptionPlanController::class, 'edit'])->name('admin.subscription.plans.edit');
    Route::put('/subscription/plans/{plan}', [SubscriptionPlanController::class, 'update'])->name('admin.subscription.plans.update');
    Route::delete('/subscription/plans/{plan}', [SubscriptionPlanController::class, 'destroy'])->name('admin.subscription.plans.destroy');
    Route::post('/subscription/plans/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])->name('admin.subscription.plans.toggle-status');

    // Content Management
    Route::get('/content', [AdminContentController::class, 'index'])->name('admin.content.index');
    Route::get('/content/create', [AdminContentController::class, 'create'])->name('admin.content.create');
    Route::post('/content', [AdminContentController::class, 'store'])->name('admin.content.store');
    Route::get('/content/{content}/edit', [AdminContentController::class, 'edit'])->name('admin.content.edit');
    Route::put('/content/{content}', [AdminContentController::class, 'update'])->name('admin.content.update');
    Route::delete('/content/{content}', [AdminContentController::class, 'destroy'])->name('admin.content.destroy');
    Route::post('/content/{content}/toggle-publish', [AdminContentController::class, 'togglePublish'])->name('admin.content.toggle-publish');

    // Attach/Detach content to plans
    Route::post('/content/{content}/plans/{plan}', [AdminContentController::class, 'attachToPlan'])->name('admin.content.attach-plan');
    Route::delete('/content/{content}/plans/{plan}', [AdminContentController::class, 'detachFromPlan'])->name('admin.content.detach-plan');

    // Subscription applications
    Route::get('/subscriptions', [SubscriptionAdminController::class, 'index'])->name('admin.subscriptions.index');
    Route::get('/subscriptions/{application}', [SubscriptionAdminController::class, 'show'])->name('admin.subscriptions.show');
    Route::put('/subscriptions/{application}/status', [SubscriptionAdminController::class, 'updateStatus'])->name('admin.subscriptions.update-status');
    Route::post('/subscriptions/bulk-action', [SubscriptionAdminController::class, 'bulkAction'])->name('admin.subscriptions.bulk-action');
    Route::get('/subscriptions/{application}/proof', [SubscriptionAdminController::class, 'viewProof'])->name('admin.subscriptions.view-proof');
    Route::delete('/subscriptions/{application}', [SubscriptionAdminController::class, 'destroy'])->name('admin.subscriptions.destroy');

    // Subscriber management
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('admin.subscribers.index');
    Route::get('/subscribers/{subscriber}', [SubscriberController::class, 'show'])->name('admin.subscribers.show');
    Route::put('/subscribers/{subscriber}/subscription', [SubscriberController::class, 'updateSubscription'])->name('admin.subscribers.update-subscription');
    Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('admin.subscribers.destroy');
});

require __DIR__ . '/auth.php';
