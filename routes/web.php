<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\MentorDirectoryController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\MentorRequestController;

Route::redirect('/', '/login');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Safety net: if an admin hits /dashboard directly, redirect them to admin dashboard
Route::middleware(['auth', 'can:admin'])->get('/dashboard-admin-redirect', function () {
    return redirect()->route('admin.dashboard.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Heartbeat route to keep session alive for interactive users (optional)
Route::post('/_heartbeat', function () {
    return response('', 204);
})->middleware('auth')->name('heartbeat');



// Subscription-related public routes removed/redirected: platform uses admin-provisioned access.
Route::get('/subscription/plans', function () { return redirect()->route('dashboard'); })->name('subscription.plans');

// Authenticated user subscription routes (redirected to dashboard)
Route::middleware(['auth'])->group(function () {
    Route::get('/subscription/apply/{plan}', function () { return redirect()->route('dashboard'); })->name('subscription.apply');
    Route::post('/subscription/apply/{plan}', function () { return redirect()->route('dashboard'); })->name('subscription.submit');
    Route::get('/subscription/status', function () { return redirect()->route('dashboard'); })->name('subscription.status');
    Route::get('/subscription/proof/{application}', function () { return redirect()->route('dashboard'); })->name('subscription.proof.download');
    Route::post('/subscription/cancel/{application}', function () { return redirect()->route('dashboard'); })->name('subscription.cancel');
});

// Content routes (require auth for all)
Route::middleware(['auth'])->group(function () {
    Route::get('/content', [ContentController::class, 'index'])->name('content.index');
    Route::get('/content/{content}', [ContentController::class, 'show'])->name('content.show');
    Route::get('/content/{content}/download', [ContentController::class, 'download'])->name('content.download');
    Route::get('/content/{content}/stream', [ContentController::class, 'stream'])->name('content.stream');
    // Mentor directory and mentor requests
    Route::get('/mentors', [MentorDirectoryController::class, 'index'])->name('mentors.index');
    Route::post('/mentor-requests', [MentorRequestController::class, 'store'])->name('mentor-requests.store');
    Route::post('/mentor-requests/{mentorRequest}/accept', [MentorRequestController::class, 'accept'])->name('mentor-requests.accept');
    Route::post('/mentor-requests/{mentorRequest}/reject', [MentorRequestController::class, 'reject'])->name('mentor-requests.reject');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Mentor requests (user side)
    Route::get('/mentors/requests', [MentorRequestController::class, 'index'])->name('mentors.requests');
    Route::post('/mentors/request', [MentorRequestController::class, 'request'])->name('mentors.request');
    Route::post('/mentors/respond/{mentorRequest}', [MentorRequestController::class, 'respond'])->name('mentors.respond');
});

// Admin routes secured by Gate
Route::prefix('admin')->middleware(['auth', 'can:admin'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard.index');

    // Subscription Plan Management removed: platform is admin-provisioned. Keep routes as redirects to admin dashboard.
    Route::get('/subscription/plans', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscription.plans.index');
    Route::get('/subscription/plans/create', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscription.plans.create');
    Route::post('/subscription/plans', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscription.plans.store');
    Route::get('/subscription/plans/{plan}/edit', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscription.plans.edit');
    Route::put('/subscription/plans/{plan}', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscription.plans.update');
    Route::delete('/subscription/plans/{plan}', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscription.plans.destroy');
    Route::post('/subscription/plans/{plan}/toggle-status', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscription.plans.toggle-status');

    // Content Management
    Route::get('/content', [AdminContentController::class, 'index'])->name('admin.content.index');
    Route::get('/content/create', [AdminContentController::class, 'create'])->name('admin.content.create');
    Route::post('/content', [AdminContentController::class, 'store'])->name('admin.content.store');
    Route::get('/content/{content}/edit', [AdminContentController::class, 'edit'])->name('admin.content.edit');
    Route::put('/content/{content}', [AdminContentController::class, 'update'])->name('admin.content.update');
    Route::delete('/content/{content}', [AdminContentController::class, 'destroy'])->name('admin.content.destroy');
    Route::post('/content/{content}/toggle-publish', [AdminContentController::class, 'togglePublish'])->name('admin.content.toggle-publish');

    // Attach/Detach content to plans removed (plans archived)

    // Subscription application management removed — redirect to admin dashboard
    Route::get('/subscriptions', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscriptions.index');
    Route::get('/subscriptions/{application}', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscriptions.show');
    Route::put('/subscriptions/{application}/status', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscriptions.update-status');
    Route::post('/subscriptions/bulk-action', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscriptions.bulk-action');
    Route::get('/subscriptions/{application}/proof', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscriptions.view-proof');
    Route::delete('/subscriptions/{application}', function () { return redirect()->route('admin.dashboard.index'); })->name('admin.subscriptions.destroy');

    // Subscriber management
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('admin.subscribers.index');
    // Admin add user routes
    Route::get('/subscribers/create', [SubscriberController::class, 'create'])->name('admin.subscribers.create');
    Route::post('/subscribers', [SubscriberController::class, 'store'])->name('admin.subscribers.store');
    Route::get('/subscribers/{subscriber}', [SubscriberController::class, 'show'])->name('admin.subscribers.show');
    Route::post('/subscribers/{subscriber}/assign-mentor', [SubscriberController::class, 'assignMentor'])->name('admin.subscribers.assign-mentor');
    Route::put('/subscribers/{subscriber}/subscription', [SubscriberController::class, 'updateSubscription'])->name('admin.subscribers.update-subscription');
    Route::post('/subscribers/{subscriber}/toggle-mentor', [SubscriberController::class, 'toggleMentor'])->name('admin.subscribers.toggle-mentor');
    Route::post('/subscribers/{subscriber}/renewal', [SubscriberController::class, 'updateRenewal'])->name('admin.subscribers.update-renewal');
    Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('admin.subscribers.destroy');

    // Admin and Mentor directories
    Route::get('/admins', [AdminUserController::class, 'index'])->name('admin.admins.index');
    Route::middleware('can:superadmin')->group(function () {
        Route::get('/admins/create', [AdminUserController::class, 'create'])->name('admin.admins.create');
        Route::post('/admins', [AdminUserController::class, 'store'])->name('admin.admins.store');
        Route::delete('/admins/{admin}', [AdminUserController::class, 'destroy'])->name('admin.admins.destroy');
    });
    Route::get('/mentors', [MentorController::class, 'index'])->name('admin.mentors.index');
});

require __DIR__ . '/auth.php';
