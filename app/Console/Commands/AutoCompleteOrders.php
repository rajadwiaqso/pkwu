<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\trx;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCompleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-complete orders where buyer hasn\'t confirmed for 3 days after shipped';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-complete process for shipped orders...');

        // Get orders that have been shipped for 3+ days without buyer confirmation
        $threeDaysAgo = Carbon::now()->subDays(1);
        
        $ordersToComplete = trx::where('status', 'shipped')
                                ->where('shipped_at', '<=', Carbon::now())
                                ->whereNull('auto_completed_at')
                                ->get();

            
        // $this->info(trx::where('status', 'shipped')
        //                         ->where('shipped_at', '<=', $threeDaysAgo)
        //                         ->whereNull('auto_completed_at')
        //                         ->count() . " orders found for auto-completion." . $threeDaysAgo);
        $completedCount = 0;

        foreach ($ordersToComplete as $order) {
            DB::beginTransaction();
            try {
                // Get status_date and add 'done' timestamp
                $statusDate = is_array($order->status_date) 
                    ? $order->status_date 
                    : (is_string($order->status_date) 
                        ? json_decode($order->status_date, true) ?? [] 
                        : []);
                
                $statusDate['done'] = now()->format('Y-m-d H:i:s');

                // Update order to 'done' status with 'from' = 'system'
                $order->update([
                    'status' => 'done',
                    'from' => 'system', // System auto-completed
                    'status_date' => $statusDate,
                    'auto_completed_at' => now(),
                    'completed_at' => now()
                ]);

                // Log the auto-completion
                Log::info("Order {$order->trx_id} auto-completed by system after 3 days of shipped status");

                // Notify buyer and seller
                $this->notifyBuyer($order);
                $this->notifySeller($order);

                $completedCount++;
                
                DB::commit();

                $this->info("✓ Order {$order->trx_id} auto-completed");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("✗ Failed to auto-complete order {$order->trx_id}: " . $e->getMessage());
                Log::error("Auto-complete failed for order {$order->trx_id}: " . $e->getMessage());
            }
        }

        $this->info("Auto-complete process completed: {$completedCount} orders auto-completed");

        return Command::SUCCESS;
    }

    /**
     * Notify buyer about auto-completion
     */
    private function notifyBuyer($order)
    {
        // TODO: Integrate with notification system
        // Send email/notification to buyer
        Log::info("Buyer notification sent for auto-completed order: {$order->trx_id}");
    }

    /**
     * Notify seller about auto-completion
     */
    private function notifySeller($order)
    {
        // TODO: Integrate with notification system
        // Send email/notification to seller
        Log::info("Seller notification sent for auto-completed order: {$order->trx_id}");
    }
}
