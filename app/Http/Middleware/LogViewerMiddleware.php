<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * 
 * @todo Inplementar a verificação do usuario!!!!!!
 */
class LogViewerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $user = \Auth::guard('sanctum')->user();
        // var_dump($user);
        // dd('LogViewerMiddleware');
        return $next($request);
    }
}
