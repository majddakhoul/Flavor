<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected function flash(RedirectResponse $response, string $message, string $tone = 'success'): RedirectResponse
    {
        return $response->with('flash', ['tone' => $tone, 'message' => $message]);
    }

    protected function done(string $route, string $message, array $parameters = []): RedirectResponse
    {
        return $this->flash(redirect()->route($route, $parameters), $message);
    }
}
