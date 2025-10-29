<?php
/**
 * -------------------------------------------------------------
 * Project: AIHN Platform
 * -------------------------------------------------------------
 * Description:
 * This file was collaboratively developed as part of the AIHN
 * platform modules. It demonstrates teamwork and shared logic.
 *
 * Authors:
 *  - John Nwanosike <johnnwanosike@gmail.com>
 *  - Abraham Covenant <abrahamcovenant2004@gmail.com>
 * -------------------------------------------------------------
 */

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

        // Unified access: all authenticated users can access published content
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $content = $query->latest()->paginate(12);
        $contentTypes = Content::TYPES;

        return view('content.index', compact('content', 'contentTypes'));
    }

    public function show(Content $content)
    {
        // Require authentication; unified access allows authenticated users to view published content
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this content.');
        }

        $content->incrementViews();

        return view('content.show', compact('content'));
    }

    public function download(Content $content)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to download this content.');
        }

        if (! $content->file_path) {
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

        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to stream this content.');
        }

        if (! $content->file_path || ! in_array($content->type, ['video', 'audio'])) {
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