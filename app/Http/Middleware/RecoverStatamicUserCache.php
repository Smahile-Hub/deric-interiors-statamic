<?php

namespace App\Http\Middleware;

use App\Support\ControlPanelSafety;
use Closure;

class RecoverStatamicUserCache
{
    public function handle($request, Closure $next)
    {
        ControlPanelSafety::recoverStaleUserCache();

        return $next($request);
    }
}
