<?php

namespace App\Exceptions;

use App\Exceptions\Domain\DomainException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $exception) {
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof DomainException) {
            return $this->renderDomainException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    protected function renderDomainException(Request $request, DomainException $exception)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $exception->userMessage(),
                'errors' => $exception->context() ?: null,
            ], $exception->status());
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('flash', [
                'tone' => 'danger',
                'message' => $exception->userMessage(),
            ]);
    }
}
