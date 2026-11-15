<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Languages
{
    
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
