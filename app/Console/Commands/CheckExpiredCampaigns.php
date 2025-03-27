<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckExpiredCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-campaigns-users';

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
        $this->info('Checking for expired campaign users...');

        // Primero obtenemos los IDs de los usuarios expirados
        $expiredUserIds = User::query()
            ->select('users.id')
            ->whereNotNull('campaign_id')
            ->where('is_actived', true)
            ->where('dietician_id', 1)
            ->whereRaw('DATE_ADD(users.created_at, INTERVAL campaigns.free_days DAY) < NOW()')
            ->join('campaigns', 'users.campaign_id', '=', 'campaigns.id')
            ->pluck('id');

        // Luego actualizamos esos usuarios
        $deactivatedCount = User::whereIn('id', $expiredUserIds)
            ->update([
                'is_actived' => false
            ]);

        $this->info("Command completed. {$deactivatedCount} users were deactivated.");
    }
}
