<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $query = Content::published()
            ->when($request->search, function ($q) use ($request) {
                $q->search($request->search);
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('type', $request->type);
            });

        if (auth()->check() && auth()->user()->is_subscription_active) {
            $activePlanId = auth()->user()->activeSubscription()->value('plan_id');

            if ($activePlanId) {
                $query->forPlan($activePlanId);
            } else {
                // Debug: log why no plan ID was found
                \Log::warning('User has active subscription but no plan_id', [
                    'user_id' => auth()->id(),
                    'subscription' => auth()->user()->activeSubscription
                ]);
                $query->whereRaw('1 = 0');
            }
        } else {
            $query->whereRaw('1 = 0');
        }

        $content = $query->latest()->paginate(12);
        $contentTypes = Content::TYPES;

        return view('content.index', compact('content', 'contentTypes'));
    }

    public function show(Content $content)
    {
        // Check if user can access the content
        if (!auth()->check() || !$content->isAccessibleByUser(auth()->user())) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'Please subscribe to access this content.');
        }

        $content->incrementViews();

        return view('content.show', compact('content'));
    }

    public function download(Content $content)
    {
        // Check if user can access the content
        if (!auth()->check() || !$content->isAccessibleByUser(auth()->user())) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'Please subscribe to access this content.');
        }

        if (!$content->file_path) {
            abort(404);
        }

        return Storage::disk('private')->download(
            $content->file_path,
            $content->file_original_name,
            ['Content-Type' => $content->file_mime_type]
        );
    }

    public function stream(Content $content)
    {
        // Check if user can access the content
        if (!auth()->check() || !$content->isAccessibleByUser(auth()->user())) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'Please subscribe to access this content.');
        }

        if (!$content->file_path || !in_array($content->type, ['video', 'audio'])) {
            abort(404);
        }

        $stream = new StreamedResponse(function () use ($content) {
            $stream = Storage::disk('private')->readStream($content->file_path);
            fpassthru($stream);
            fclose($stream);
        });

        $stream->headers->set('Content-Type', $content->file_mime_type);

        return $stream;
    }
}