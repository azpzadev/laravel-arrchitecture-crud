<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to force JSON responses for all API requests.
 *
 * Sets the Accept header to application/json to ensure all
 * responses are formatted as JSON regardless of the original
 * request headers.
 */
class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming request
     * @param Closure $next The next middleware
     * @return Response The response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
