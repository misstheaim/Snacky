<?php

namespace App\Console\Commands;

use App\Contracts\HttpProductReceiver;
use App\Models\Snack;
use Illuminate\Console\Command;

class SnackUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-snacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(HttpProductReceiver $receiver): void
    {
        $this->withProgressBar(Snack::all(), function (Snack $snack) use ($receiver) {
            $receiver->makeWork($snack->uzum_product_id);
        });
    }
}
