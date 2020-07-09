<?php

namespace App\Console\Commands\OldAmo;

use App\Models\OldAmo\AmoOldContact;
use Illuminate\Console\Command;

class ContactSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrm:old-contacts-sync';

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
        AmoOldContact::sync();
    }
}
