<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class headersValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->headers->has('Content-Type')){
            return response()->json([
                'message' => 'Content-Type must be filled',
            ], 400);
        }

        if ($request->headers->get('Content-Type') !== 'application/json') {
            return response()->json([
                'message' => 'Content-Type must be application/json',
            ], 400);
        }

        if (!$request->headers->has('Timestamp')){
            return response()->json([
                'message' => 'Timestamp must be filled',
            ], 400);
        }

        // Sementara di disabled
        // if ($request->headers->get('Timestamp') !== Carbon::now()->toIso8601String()) {
        //     return response()->json([
        //         'message' => 'Timestamp not valid',
        //     ], 400);
        // }
          
        return $next($request);
    }
}