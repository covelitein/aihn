<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Use the default behavior for reporting
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle expired/invalid CSRF token (TokenMismatchException)
        if ($e instanceof TokenMismatchException) {
            // For AJAX/JSON requests, return JSON
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'message' => 'Session expired or invalid CSRF token. Please refresh the page and try again.'
                ], 419);
            }

            // For normal web forms, redirect back with input (except token) and a friendly message
            return redirect()->back()
                ->withInput($request->except('_token'))
                ->with('error', 'Your session has expired. Please try again.');
        }

        return parent::render($request, $e);
    }
}
