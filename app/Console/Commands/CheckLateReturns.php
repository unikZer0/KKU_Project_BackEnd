<?php

namespace App\Console\Commands;

use App\Services\LateReturnService;
use Illuminate\Console\Command;

class CheckLateReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrow:check-late-returns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for late equipment returns and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for late returns...');
        
        $lateReturnService = new LateReturnService();
        $lateCount = $lateReturnService->checkLateReturns();
        
        if ($lateCount > 0) {
            $this->info("Found {$lateCount} late returns and sent notifications.");
        } else {
            $this->info('No late returns found.');
        }
        
        return Command::SUCCESS;
    }
}
