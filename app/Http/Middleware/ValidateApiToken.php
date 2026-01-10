<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Auth\Exceptions\InvalidApiTokenException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to validate API token in request headers.
 *
 * Checks for a configurable API token header and validates
 * it against the configured token value. Skips validation
 * if no token is configured.
 */
class ValidateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming request
     * @param Closure $next The next middleware
     * @return Response The response
     * @throws InvalidApiTokenException When token is missing or invalid
     */
    public function handle(Request $request, Closure $next): Response
    {
        $headerName = config('api.token_header', 'x-api-token');
        $validToken = config('api.token');

        // If no token is configured, skip validation
        if (empty($validToken)) {
            return $next($request);
        }

        $providedToken = $request->header($headerName);

        if (empty($providedToken) || !hash_equals($validToken, $providedToken)) {
            throw new InvalidApiTokenException();
        }

        return $next($request);
    }
}
