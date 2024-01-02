<?php

namespace App\Console\Commands;

use App\Models\Meal;
use Illuminate\Console\Command;

class DeleteMealsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-meals-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Meal::where('created_at', '<=', now()->subMonth())->delete();
    }
}
