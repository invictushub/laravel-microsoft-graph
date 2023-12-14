<?php

namespace Invictushub\MsGraph\Console\Commands;

use Invictushub\MsGraph\Facades\MsGraphAdmin;
use Illuminate\Console\Command;

class MsGraphAdminKeepAliveCommand extends Command
{
    protected $signature = 'msgraphadmin:keep-alive';

    protected $description = 'Run this command to refresh token if its due to expire. schedule this to run daily to avoid token expiring when using CLI commands';

    public function handle(): void
    {
        if (MsGraphAdmin::isConnected()) {
            MsGraphAdmin::getAccessToken($redirectWhenNotConnected = false);
            $this->comment('connected');
        }
    }
}
