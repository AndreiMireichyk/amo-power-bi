<?php

namespace App\Console\Commands\NewAmo;

use App\Models\NewAmo\AmoNewContact;
use Illuminate\Console\Command;

class ContactSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrm:new-contacts-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync контактов из новой црм';

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
        AmoNewContact::sync();
    }
}
