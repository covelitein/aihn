<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
// SubscriptionPlan model removed
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ContentController extends Controller implements HasMiddleware
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
		$query = Content::with(['author'])
			->when($request->search, function ($q) use ($request) {
				$q->search($request->search);
			})
			->when($request->type, function ($q) use ($request) {
				$q->where('type', $request->type);
			})
			->when($request->plan_id, function ($q) use ($request) {
				$q->forPlan($request->plan_id);
			})
			->when($request->published, function ($q) use ($request) {
				if ($request->published === 'yes') {
					$q->published();
				} else {
					$q->where('is_published', false);
				}
			});

		if ($request->ajax() || $request->has('draw')) {
			return DataTables::of($query)
				->addIndexColumn()
				->addColumn('author_name', function (Content $c) {
					return $c->author?->name ?? 'N/A';
				})
				->addColumn('plans', function (Content $c) {
					$badges = '';
					foreach ((array) ($c->accessible_plans ?? []) as $pid) {
						$badges .= '<span class="badge bg-primary me-1">#'.$pid.'</span>';
					}
					return $badges ?: '<span class="text-muted">None</span>';
				})
				->addColumn('published_badge', function (Content $c) {
					return $c->is_published
						? '<span class="badge bg-success">Published</span>'
						: '<span class="badge bg-warning">Draft</span>';
				})
				->addColumn('created_at_formatted', function (Content $c) {
					return $c->created_at?->format('M d, Y') ?? '';
				})
				->addColumn('actions', function (Content $c) {
					$formId = 'delete-content-' . $c->id;
					$confirmMessage = addslashes('Delete this content?');
					return '<div class="btn-group">'
						.'<a href="'.route('admin.content.edit', $c).'" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>'
						.'<form action="'.route('admin.content.toggle-publish', $c).'" method="POST" class="d-inline">'.csrf_field().'<button type="submit" class="btn btn-sm btn-outline-secondary" title="Toggle Publish"><i class="bi bi-eye'.($c->is_published?'-slash':'').'"></i></button></form>'
						.'<form id="'.$formId.'" action="'.route('admin.content.destroy', $c).'" method="POST" class="d-inline">'.csrf_field().method_field('DELETE').'<button type="button" onclick="AppUI.confirm(\''.$confirmMessage.'\').then(function(ok){ if(ok) document.getElementById(\''.$formId.'\').submit(); });" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button></form>'
					.'</div>';
				})
				->rawColumns(['plans', 'published_badge', 'actions'])
				->make(true);
		}

	$content = $query->latest()->paginate(20);
	// Plans removed; keep accessible_plans as raw IDs if present
	$plans = collect();
		$contentTypes = Content::TYPES;

		return view('admin.content.index', compact('content', 'plans', 'contentTypes'));
	}

	public function create()
	{
	$plans = collect();
		$contentTypes = Content::TYPES;

		return view('admin.content.form', compact('plans', 'contentTypes'));
	}

	public function store(Request $request)
	{
		$this->validateContent($request);

		$content = new Content($request->except('file'));

		if ($request->hasFile('file')) {
			$this->handleFileUpload($content, $request->file('file'));
		}

		$content->save();

		return redirect()
			->route('admin.content.index')
			->with('success', 'Content created successfully.');
	}

	public function edit(Content $content)
	{
	$plans = collect();
		$contentTypes = Content::TYPES;

		return view('admin.content.form', compact('content', 'plans', 'contentTypes'));
	}

	public function update(Request $request, Content $content)
	{
		$this->validateContent($request, $content);

		$content->fill($request->except('file'));

		if ($request->hasFile('file')) {
			$content->deleteFile(); // Delete old file
			$this->handleFileUpload($content, $request->file('file'));
		}

		$content->save();

		return redirect()
			->route('admin.content.index')
			->with('success', 'Content updated successfully.');
	}

	public function destroy(Content $content)
	{
		$content->delete(); // This will also delete the file through model events

		return redirect()
			->route('admin.content.index')
			->with('success', 'Content deleted successfully.');
	}

	public function togglePublish(Content $content)
	{
		if ($content->is_published) {
			$content->unpublish();
			$message = 'Content unpublished successfully.';
		} else {
			$content->publish();
			$message = 'Content published successfully.';
		}

		return redirect()
			->back()
			->with('success', $message);
	}

	// attachToPlan/detachFromPlan removed - plan management is admin-provisioned elsewhere

	protected function validateContent(Request $request, Content $content = null)
	{
		$rules = [
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'type' => 'required|string|in:' . implode(',', array_keys(Content::TYPES)),
			'content' => 'nullable|string|required_without:file',
			'file' => [
				$content ? 'nullable' : 'required_without:content',
				'file',
				'max:52428800' // 50MB max
			],
			// accessible_plans are optional free-form IDs now
			'accessible_plans' => 'nullable|array',
			'accessible_plans.*' => 'integer',
			'metadata' => 'nullable|array'
		];

		if ($request->type === 'video') {
			$rules['file'] = array_merge($rules['file'], ['mimes:mp4,avi,mov']);
		} elseif ($request->type === 'audio') {
			$rules['file'] = array_merge($rules['file'], ['mimes:mp3,wav,ogg']);
		} elseif ($request->type === 'document') {
			$rules['file'] = array_merge($rules['file'], ['mimes:pdf,doc,docx,xls,xlsx,ppt,pptx']);
		}

		return $request->validate($rules);
	}

	protected function handleFileUpload(Content $content, $file)
	{
		$fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
		$filePath = $file->storeAs('content/' . date('Y/m'), $fileName, 'private');

		$content->file_path = $filePath;
		$content->file_original_name = $file->getClientOriginalName();
		$content->file_mime_type = $file->getMimeType();
		$content->file_size = $file->getSize();
	}
}