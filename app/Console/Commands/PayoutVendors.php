<?php

namespace App\Console\Commands;

use App\Enum\OrderStatusEnum;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PayoutVendors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payout:vendors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform vendors payout on the 1st of each month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly payout process for vendors...');

        $vendors = Vendor::eligibleForPayout()->get();

        foreach ($vendors as $vendor) {
            $this->processPayout($vendor);
        }

        $this->info('Monthly payout process for completed.');

        return Command::SUCCESS;
    }

    protected function processPayout(Vendor $vendor): void
    {
        $this->info('Processing payout for vendor [ID=' . $vendor->user_id . '] - "' . $vendor->store_name . '"');

        try {
            DB::beginTransaction();

            $startingFrom = Payout::where('vendor_id', $vendor->user_id)
                ->orderBy('until', 'desc')
                ->value('until');

            $startingFrom = $startingFrom ?: Carbon::now()->startOfYear();

            $until = Carbon::now()->subMonthNoOverflow()->startOfMonth(); // select one month befor the current date and checking no over flow like feb 30 and seleceting start of that month

            $this->info('starting from: ' . $startingFrom->toDateTimeString());
            $this->info('until: ' . $until->toDateTimeString());

            $vendorSubtotal = Order::query()
                ->where('vendor_user_id', $vendor->user_id)
                ->where('status', OrderStatusEnum::Paid->value)
                ->whereBetween('created_at', [$startingFrom, $until])
                ->sum('vendor_subtotal');

            if ($vendorSubtotal) {
                $this->info('Payout made with amount: ' . $vendorSubtotal);

                Payout::create([
                    'vendor_id' => $vendor->user_id,
                    'amount' => $vendorSubtotal,
                    'starting_from' => $startingFrom,
                    'until' => $until
                ]);

                // stripe connect package for tarsnefr money to user
                $vendor->user->transfer((int) ($vendorSubtotal * 100), config('app.currency'));
            } else {
                $this->info('Nothing to process.');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }
    }
}
