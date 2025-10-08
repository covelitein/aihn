<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Password;

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
			$query = User::with(['profile'])
				->where('is_admin', false);

			// Filter by subscription status
			$status = $request->string('status');
			if ($status && $status !== 'all') {
				switch ($status) {
					case 'active':
						$query->whereNotNull('renewal_date')->where('renewal_date', '>=', now());
						break;
					case 'expired':
						$query->whereNotNull('renewal_date')->where('renewal_date', '<', now());
						break;
					case 'pending':
						// No pending concept; show those without renewal set
						$query->whereNull('renewal_date');
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
				->addColumn('status_badge', function (User $user) {
					if ($user->renewal_date && $user->renewal_date->isFuture()) {
						return '<span class="badge bg-success">Active</span>';
					}
					if ($user->renewal_date && $user->renewal_date->isPast()) {
						return '<span class="badge bg-warning text-dark">Expired</span>';
					}
					return '<span class="badge bg-light text-muted">No Renewal</span>';
				})
				->addColumn('actions', function (User $user) {
					// Use AppUI.confirm to show modal and submit form programmatically
					$formId = 'delete-subscriber-' . $user->id;
					$confirmMessage = addslashes("Delete user {$user->name}?");
					return '<div class="d-flex justify-content-end">'
						.'<a href="'.route('admin.subscribers.show', $user).'" class="btn btn-sm btn-info me-1"><i class="bi bi-eye"></i></a>'
						.'<form id="'.$formId.'" action="'.route('admin.subscribers.destroy', $user).'" method="POST" class="d-inline">'
							.csrf_field().method_field('DELETE')
							.'<button type="button" onclick="AppUI.confirm(\'{$confirmMessage}\', \'Confirm Deletion\').then(function(ok){ if(ok) document.getElementById(\''.$formId.'\').submit(); });" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>'
						.'</form>'
					.'</div>';
				})
				->rawColumns(['status_badge', 'actions'])
				->make(true);
		}

		// Non-AJAX: initial render
		$query = User::with(['profile'])
			->where('is_admin', false);

		$status = $request->string('status');
		if ($status && $status !== 'all') {
			switch ($status) {
				case 'active':
					$query->whereNotNull('renewal_date')->where('renewal_date', '>=', now());
					break;
				case 'expired':
					$query->whereNotNull('renewal_date')->where('renewal_date', '<', now());
					break;
				case 'pending':
					$query->whereNull('renewal_date');
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

        // Preload mentors and current mentor assignments
        $mentors = User::where('is_mentor', true)->orderBy('name')->get(['id','name','email']);
        $mentorAssignments = [];
        if ($subscribers->count() > 0) {
            $userIds = $subscribers->pluck('id')->all();
            $accepted = \App\Models\MentorRequest::whereIn('user_id', $userIds)
                ->where('status', 'accepted')
                ->get(['user_id','mentor_id']);
            foreach ($accepted as $row) {
                $mentorAssignments[$row->user_id] = $row->mentor_id;
            }
        }

        $stats = [
			'total' => User::where('is_admin', false)->count(),
			'active' => User::where('is_admin', false)->whereNotNull('renewal_date')->where('renewal_date', '>=', now())->count(),
			'expired' => User::where('is_admin', false)->whereNotNull('renewal_date')->where('renewal_date', '<', now())->count(),
			'pending' => User::where('is_admin', false)->whereNull('renewal_date')->count()
		];

        return view('admin.subscribers.index', compact('subscribers', 'stats', 'mentors', 'mentorAssignments'));
	}

	public function show(User $subscriber)
	{
		$subscriber->load([
			'profile'
		]);

		$mentors = User::where('is_mentor', true)->orderBy('name')->get(['id','name','email']);

		return view('admin.subscribers.show', compact('subscriber', 'mentors'));
	}

	public function updateSubscription(Request $request, User $subscriber)
	{
		// Subscription management has been removed from the UI. Handle via admin workflows or via direct DB updates.
		return redirect()->back()->with('warning', 'Subscription management is disabled. Manage access via the admin settings.');
	}

	public function destroy(User $subscriber)
	{
		$this->authorize('superadmin');
		$subscriber->delete();

		return redirect()->route('admin.subscribers.index')
			->with('success', 'Subscriber deleted successfully.');
	}

	public function toggleMentor(User $subscriber)
	{
		$subscriber->update(['is_mentor' => ! (bool) $subscriber->is_mentor]);
		try {
			request()->user()->notify(new \App\Notifications\GenericMessageNotification(
				($subscriber->is_mentor ? 'Promoted ' : 'Demoted ').$subscriber->name.' '.($subscriber->is_mentor ? 'to' : 'from').' mentor'
			));
		} catch (\Throwable $e) {}
		return redirect()->back()->with('success', 'Mentor status updated.');
	}

	public function updateRenewal(Request $request, User $subscriber)
	{
		$request->validate([
			'renewal_date' => 'nullable|date'
		]);

		$subscriber->update([
			'renewal_date' => $request->renewal_date
		]);

		return redirect()->back()->with('success', 'Renewal date updated.');
	}
	public function create()
	{
		return view('admin.subscribers.create');
	}

	public function store(Request $request)
	{
		$this->authorize('superadmin');
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email',
			'phone' => 'nullable|string|max:50',
			'password' => 'required|string|min:8',
			'is_admin' => 'nullable|boolean',
			'is_mentor' => 'nullable|boolean',
			'renewal_date' => 'nullable|date'
		]);

		$user = \App\Models\User::create([
			'name' => $request->name,
			'email' => $request->email,
			'phone' => $request->phone,
			'password' => \Illuminate\Support\Facades\Hash::make($request->password),
			'is_admin' => (bool) $request->filled('is_admin'),
			'is_mentor' => (bool) $request->filled('is_mentor'),
			'renewal_date' => $request->renewal_date
		]);

		// Notify the creator and the new user (if email verified later) via database notification
		try {
			$request->user()->notify(new \App\Notifications\GenericMessageNotification('User '.$user->name.' has been created.'));
		} catch (\Throwable $e) {}

		return redirect()->route('admin.subscribers.index')
			->with('success', 'User added successfully. Password set by super admin.');
	}

    public function assignMentor(Request $request, User $subscriber)
    {
        $request->validate([
            'mentor_id' => 'required|exists:users,id'
        ]);

        $mentor = User::findOrFail($request->mentor_id);
        if (! $mentor->is_mentor) {
            return redirect()->back()->with('error', 'Selected user is not a mentor.');
        }

        // Store assignment using mentor_requests as accepted mapping
        \App\Models\MentorRequest::updateOrCreate(
            ['user_id' => $subscriber->id, 'mentor_id' => $mentor->id],
            ['status' => 'accepted']
        );

        // Reject other pending requests for this user
        \App\Models\MentorRequest::where('user_id', $subscriber->id)
            ->where('mentor_id', '!=', $mentor->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Mentor assigned.');
    }

}