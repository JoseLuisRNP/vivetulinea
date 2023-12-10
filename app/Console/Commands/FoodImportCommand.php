<?php

namespace App\Console\Commands;

use App\Imports\FoodImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class FoodImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:food-import';

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
        $this->output->title('Starting import');
        (new FoodImport())->withOutput($this->output)->import('food.xlsx');
        $this->output->success('Import successful');
    }
}
