<?php

namespace App\Console\Commands;

use App\Contracts\HttpCategoriesReceiver;
use App\Contracts\HttpProductReceiver;
use Illuminate\Console\Command;

class ReceiveCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:receive-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(HttpCategoriesReceiver $receiver)
    {
        $receiver->makeWork();
    }
}
