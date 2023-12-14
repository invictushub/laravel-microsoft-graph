<?php

namespace Invictushub\MsGraph;

use Closure;
use Invictushub\MsGraph\Facades\MsGraph;
use Illuminate\Http\Request;

class MsGraphAuthenticated
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! MsGraph::isConnected()) {
            return MsGraph::connect();
        }

        return $next($request);
    }
}
