<?php

namespace App\Console\Commands;

use App\Services\UzumHttpGraphQlCategoriesReceiver;
use App\Services\UzumHttpProductReceiver;
use Illuminate\Console\Command;

class ddd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(UzumHttpProductReceiver $receiver)
    {
        $receiver->makeWork(152133333490);
    }
}
