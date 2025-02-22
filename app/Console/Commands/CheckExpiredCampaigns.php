<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Console\Command;

class CheckExpiredCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and deactivate users from expired campaigns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaigns = Campaign::where('end_date', '<', now())->pluck('id');

        User::whereIn('campaign_id', $campaigns->values())->where('dietician_id', 1)->update(['is_actived' => null]);
    }
}
