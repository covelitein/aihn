<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\DataTables;

class SubscriberController extends Controller implements HasMiddleware
{
	public static function middleware(): array
	{
		return [
			new Middleware('auth'),
			new Middleware('can:admin'),
		];
	}

	public function index(Request $request)
	{
		if ($request->ajax() || $request->has('draw')) {
			$query = User::with(['profile', 'currentSubscription.plan'])
				->where('is_admin', false);

			// Filter by subscription status
			$status = $request->string('status');
			if ($status && $status !== 'all') {
				switch ($status) {
					case 'active':
						$query->subscribed();
						break;
					case 'expired':
						$query->expired();
						break;
					case 'pending':
						$query->pending();
						break;
				}
			}

			// Search
			if ($request->filled('search')) {
				$search = $request->string('search');
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%")
						->orWhereHas('profile', function ($q) use ($search) {
							$q->where('company_name', 'like', "%{$search}%");
						});
				});
			}

			return DataTables::of($query)
				->addIndexColumn()
				->addColumn('company', function (User $user) {
					return $user->profile->company_name ?? 'N/A';
				})
				->addColumn('plan_name', function (User $user) {
					return $user->currentSubscription?->plan?->name ?? '<span class="text-muted">No active plan</span>';
				})
				->addColumn('status_badge', function (User $user) {
					if ($user->is_subscribed) return '<span class="badge bg-success">Active</span>';
					if ($user->hasPendingApplication()) return '<span class="badge bg-warning">Pending</span>';
					return '<span class="badge bg-danger">Expired</span>';
				})
				->addColumn('expiry_formatted', function (User $user) {
					if (!$user->subscription_expires_at) return '<span class="text-muted">N/A</span>';
					$cls = $user->subscription_expires_at->isPast() ? 'text-danger' : 'text-success';
					return '<span class="'.$cls.'">'.$user->subscription_expires_at->format('M d, Y').'</span>';
				})
				->addColumn('actions', function (User $user) {
					return '
					<div class="d-flex justify-content-end">
						<a href="'.route('admin.subscribers.show', $user).'" class="btn btn-sm btn-info me-1"><i class="bi bi-eye"></i></a>
						<button type="button" class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#extendModal'.$user->id.'"><i class="bi bi-calendar-plus"></i></button>
						<form action="'.route('admin.subscribers.destroy', $user).'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this subscriber?\')">
							'.csrf_field().method_field('DELETE').'
							<button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
						</form>
					</div>';
				})
				->rawColumns(['plan_name', 'status_badge', 'expiry_formatted', 'actions'])
				->make(true);
		}

		// Non-AJAX: initial render
		$query = User::with(['profile', 'currentSubscription.plan'])
			->where('is_admin', false);

		$status = $request->string('status');
		if ($status && $status !== 'all') {
			switch ($status) {
				case 'active':
					$query->subscribed();
					break;
				case 'expired':
					$query->expired();
					break;
				case 'pending':
					$query->pending();
					break;
			}
		}

		if ($request->filled('search')) {
			$search = $request->string('search');
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%")
					->orWhereHas('profile', function ($q) use ($search) {
						$q->where('company_name', 'like', "%{$search}%");
					});
			});
		}

		$subscribers = $query->latest()->paginate(25);

		$stats = [
			'total' => User::where('is_admin', false)->count(),
			'active' => User::where('is_admin', false)->subscribed()->count(),
			'expired' => User::where('is_admin', false)->expired()->count(),
			'pending' => User::where('is_admin', false)->pending()->count()
		];

		return view('admin.subscribers.index', compact('subscribers', 'stats'));
	}

	public function show(User $subscriber)
	{
		$subscriber->load([
			'profile',
			'subscriptionApplications.plan',
			'currentSubscription.plan'
		]);

		return view('admin.subscribers.show', compact('subscriber'));
	}

	public function updateSubscription(Request $request, User $subscriber)
	{
		$request->validate([
			'action' => 'required|in:activate,deactivate,extend',
			'extension_days' => 'required_if:action,extend|integer|min:1|max:365'
		]);

		switch ($request->action) {
			case 'activate':
				$subscriber->update([
					'is_subscribed' => true,
					'subscription_status' => 'active',
					'subscription_expires_at' => now()->addMonth()
				]);
				$message = 'Subscription activated successfully.';
				break;

			case 'deactivate':
				$subscriber->update([
					'is_subscribed' => false,
					'subscription_status' => 'expired'
				]);
				$message = 'Subscription deactivated successfully.';
				break;

			case 'extend':
				$newExpiry = $subscriber->subscription_expires_at
					? $subscriber->subscription_expires_at->addDays($request->extension_days)
					: now()->addDays($request->extension_days);

				$subscriber->update([
					'subscription_expires_at' => $newExpiry,
					'is_subscribed' => true,
					'subscription_status' => 'active'
				]);
				$message = "Subscription extended by {$request->extension_days} days.";
				break;
		}

		return redirect()->back()->with('success', $message);
	}

	public function destroy(User $subscriber)
	{
		// Prevent deletion of users with active subscriptions
		if ($subscriber->is_subscription_active) {
			return redirect()->back()->with('error', 'Cannot delete user with active subscription.');
		}

		$subscriber->delete();

		return redirect()->route('admin.subscribers.index')
			->with('success', 'Subscriber deleted successfully.');
	}
}