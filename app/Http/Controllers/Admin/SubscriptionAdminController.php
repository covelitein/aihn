<?php

namespace App\Http\Controllers\Admin;

use App\Events\SubscriptionApproved;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class SubscriptionAdminController extends Controller implements HasMiddleware
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
		// DataTables sends a 'draw' parameter; also handle generic AJAX
		if ($request->ajax() || $request->has('draw')) {
			$query = SubscriptionApplication::with(['user', 'plan']);

			// Optional filters
			if ($request->filled('status')) {
				$query->where('status', $request->string('status'));
			}

			if ($request->filled('date_from')) {
				$query->whereDate('submitted_at', '>=', $request->date('date_from'));
			}

			if ($request->filled('date_to')) {
				$query->whereDate('submitted_at', '<=', $request->date('date_to'));
			}

			return DataTables::of($query)
				->addIndexColumn()
				->addColumn('user_name', function ($application) {
					return $application->user->name ?? 'N/A';
				})
				->addColumn('user_email', function ($application) {
					return $application->user->email ?? 'N/A';
				})
				->addColumn('plan_name', function ($application) {
					return $application->plan->name ?? 'N/A';
				})
				->addColumn('amount_formatted', function ($application) {
					return '$' . number_format($application->amount_paid, 2);
				})
				->addColumn('submitted_at_formatted', function ($application) {
					return $application->submitted_at?->format('M d, Y H:i') ?? 'N/A';
				})
				->addColumn('status_badge', function ($application) {
					$badgeClass = [
						'pending' => 'warning',
						'under_review' => 'info',
						'approved' => 'success',
						'rejected' => 'danger',
						'expired' => 'secondary'
					][$application->status] ?? 'secondary';

					return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($application->status) . '</span>';
				})
				->addColumn('actions', function ($application) {
					return '
					<div class="btn-group">
						<a href="' . route('admin.subscriptions.show', $application) . '" 
						   class="btn btn-sm btn-primary" title="View">
							<i class="bi bi-eye"></i>
						</a>
						<button type="button" 
								class="btn btn-sm btn-warning approve-btn" 
								data-id="' . $application->id . '" 
								title="Approve">
							<i class="bi bi-check-circle"></i>
						</button>
						<button type="button" 
								class="btn btn-sm btn-danger reject-btn" 
								data-id="' . $application->id . '" 
								title="Reject">
							<i class="bi bi-x-circle"></i>
						</button>
					</div>
				';
				})
				->rawColumns(['status_badge', 'actions'])
				->make(true);
		}

		$stats = [
			'pending' => SubscriptionApplication::pending()->count(),
			'under_review' => SubscriptionApplication::underReview()->count(),
			'approved' => SubscriptionApplication::approved()->count(),
			'rejected' => SubscriptionApplication::rejected()->count(),
			'total' => SubscriptionApplication::count(),
		];

		return view('admin.subscriptions.index', compact('stats'));
	}

	public function show(SubscriptionApplication $application)
	{
		$application->load(['user.profile', 'plan']);

		return view('admin.subscriptions.show', compact('application'));
	}

	public function updateStatus(Request $request, SubscriptionApplication $application)
	{
		$request->validate([
			'status' => 'required|in:under_review,approved,rejected',
			'admin_notes' => 'nullable|string|max:1000',
			'rejection_reason' => 'required_if:status,rejected|string|max:500'
		]);

		switch ($request->status) {
			case 'under_review':
				$application->markAsUnderReview();
				$message = 'Application marked as under review.';
				break;

			case 'approved':
				$application->approve($request->admin_notes);
				$message = 'Application approved successfully.';
				break;

			case 'rejected':
				$application->reject($request->rejection_reason);
				$message = 'Application rejected.';
				break;
		}

		return redirect()->route('admin.subscriptions.show', $application)
			->with('success', $message);
	}

	public function bulkAction(Request $request)
	{
		$request->validate([
			'action' => 'required|in:approve,reject,mark_review',
			'applications' => 'required|array',
			'applications.*' => 'exists:subscription_applications,id'
		]);

		$count = 0;
		foreach ($request->applications as $applicationId) {
			$application = SubscriptionApplication::find($applicationId);

			switch ($request->action) {
				case 'approve':
					$application->approve();
					$count++;
					break;

				case 'reject':
					$application->reject('Bulk action rejection');
					$count++;
					break;

				case 'mark_review':
					$application->markAsUnderReview();
					$count++;
					break;
			}
		}

		return redirect()->back()->with('success', "{$count} applications updated successfully.");
	}

	public function viewProof(SubscriptionApplication $application)
	{
		if (!Storage::disk('public')->exists($application->proof_of_payment)) {
			abort(404);
		}

		return Storage::disk('public')->response($application->proof_of_payment);
	}

	public function destroy(SubscriptionApplication $application)
	{
		// Delete proof file
		if ($application->proof_of_payment) {
			Storage::disk('public')->delete($application->proof_of_payment);
		}

		$application->delete();

		return redirect()->route('admin.subscriptions.index')
			->with('success', 'Application deleted successfully.');
	}
}