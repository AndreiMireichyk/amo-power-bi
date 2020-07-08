<?php

namespace App\Console\Commands\NewAmo;

use App\Models\NewAmo\AmoNewStatuses;
use Illuminate\Console\Command;

class StatusesSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrm:new-statuses-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        AmoNewStatuses::sync();
    }
}
