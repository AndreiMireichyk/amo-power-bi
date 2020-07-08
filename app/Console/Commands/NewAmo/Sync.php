<?php

namespace App\Console\Commands\NewAmo;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrm:new-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Полная синхронизация';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('amocrm:new-contacts-sync');
        Artisan::call('amocrm:new-leads-sync');
    }
}
