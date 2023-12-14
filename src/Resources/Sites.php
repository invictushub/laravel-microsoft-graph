<?php

namespace Invictushub\MsGraph\Resources;

use Invictushub\MsGraph\Facades\MsGraph;

class Sites extends MsGraph
{
    public function get(): array
    {
        return MsGraph::get('sites?search=*');
    }
}
