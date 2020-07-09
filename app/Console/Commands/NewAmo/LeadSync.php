<?php

namespace App\Console\Commands\NewAmo;

use App\Models\NewAmo\AmoNewLead;
use Illuminate\Console\Command;

class LeadSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrm:new-leads-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync сделок из новой црм';

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
        AmoNewLead::sync();
    }
}
