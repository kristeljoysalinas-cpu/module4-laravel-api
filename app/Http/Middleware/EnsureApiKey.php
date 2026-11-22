<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKey
{
    /**
     * Allow the request only if the X-API-KEY header matches API_KEY in .env.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) env('API_KEY', '');
        $given = (string) $request->header('X-API-KEY', '');

        // hash_equals compares in constant time (avoids timing attacks)
        if ($expected === '' || ! hash_equals($expected, $given)) {
            return response()->json([
                'message' => 'Unauthorized: missing or invalid API key',
            ], 401);
        }

        return $next($request);
    }
}
