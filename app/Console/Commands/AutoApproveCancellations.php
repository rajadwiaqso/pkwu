<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\trx;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoApproveCancellations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-approve-cancellations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-approve cancellation requests that sellers haven\'t responded to within 1 day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-approve cancellations process...');
        
        // Find cancellation requests that are pending approval and past the 1-day deadline
        $oneDayAgo = Carbon::now()->subDay();
        $now = Carbon::now();

        $pendingCancellations = trx::where('status', 'proses pembatalan')
                                   ->where('seller_response_deadline', '<=', $now)
                                   ->whereNull('seller_response_at')
                                   ->get();


        $test = trx::where('status', 'proses pembatalan')->where('seller_response_deadline', '<=', $now)->whereNull('seller_response_at')->get();
        $this->info('Test count of proses pembatalan orders: ' . $test->count() . ' trx: ' . $test->pluck('trx_id')->join(', ') . ' deadline: ' . $test->pluck('seller_response_deadline')->join(', ') . ' ' . $oneDayAgo);
        $this->info("Found {$pendingCancellations->count()} pending cancellations to auto-approve");

        foreach ($pendingCancellations as $order) {
            $this->processAutoApproval($order);
        }

        $this->info("Auto-approval process completed: {$pendingCancellations->count()} cancellations auto-approved");
        
        return 0;
    }

    private function processAutoApproval($order)
    {
        try {
            // Update order status to approved
            $order->update([
                'cancellation_status' => 'auto_approved',
                'cancelled_by' => 'auto_approved',
                'status' => 'cancelled',
                'from' => 'system', // Set from as system for auto-approval
                'cancelled_at' => now(),
                'seller_response' => 'Otomatis disetujui karena penjual tidak merespons dalam 1 hari',
                'seller_response_at' => now()
            ]);

            // Process refund
            $this->processRefund($order);

            // Notify buyer
            $this->notifyBuyer($order, 'auto_approved_cancellation');

            $this->info("Auto-approved cancellation for order: {$order->trx_id}");
            
        } catch (\Exception $e) {
            $this->error("Failed to auto-approve order {$order->trx_id}: {$e->getMessage()}");
            Log::error('Auto-approve cancellation failed for order ' . $order->trx_id . ': ' . $e->getMessage());
        }
    }

    private function processRefund($order)
    {
        // Add refund logic here
        // This would integrate with your payment gateway
        
        // For now, just log the refund
        Log::info('Refund processed for auto-approved cancellation: ' . $order->trx_id . ', Amount: ' . $order->total);
    }

    private function notifyBuyer($order, $type = 'cancelled')
    {
        // Send notification to buyer
        // This would integrate with your notification system
        
        Log::info('Buyer notified for auto-approved cancellation: ' . $order->trx_id);
    }
}
