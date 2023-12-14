<?php

namespace Invictushub\MsGraph;

use Closure;
use Invictushub\MsGraph\Facades\MsGraphAdmin;
use Illuminate\Http\Request;

class MsGraphAdminAuthenticated
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! MsGraphAdmin::isConnected()) {
            return MsGraphAdmin::connect();
        }

        return $next($request);
    }
}
