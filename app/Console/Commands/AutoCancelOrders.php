<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\OrderCancellationController;

class AutoCancelOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-cancel orders where seller hasn\'t shipped for 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-cancel process...');
        
        $controller = new OrderCancellationController();
        $result = $controller->autoCancel();
        
        $data = $result->getData(true);
        
        $this->info("Auto-cancel completed: {$data['cancelled_count']} orders cancelled");
        $this->info($data['message']);
        
        return 0;
    }
}