<?php

namespace Invictushub\MsGraph\Console\Commands;

use Invictushub\MsGraph\Facades\MsGraph;
use Illuminate\Console\Command;

class MsGraphKeepAliveCommand extends Command
{
    protected $signature = 'msgraph:keep-alive';

    protected $description = 'Run this command to refresh token if its due to expire. schedule this to run daily to avoid token expiring when using CLI commands';

    public function handle(): void
    {
        if (MsGraph::isConnected()) {
            MsGraph::getAccessToken($redirectWhenNotConnected = false);
            $this->comment('connected');
        }
    }
}
