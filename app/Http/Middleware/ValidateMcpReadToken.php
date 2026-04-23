<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class ValidateMcpReadToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('mcp.read_token', '');

        if ($expected === '') {
            return response()->json(['message' => 'MCP non configuré (MCP_READ_TOKEN vide).'], 503);
        }

        $got = (string) ($request->bearerToken() ?? '');

        if (! hash_equals($expected, $got)) {
            return response()->json(['message' => 'Token MCP manquant ou invalide.'], 401);
        }

        return $next($request);
    }
}
