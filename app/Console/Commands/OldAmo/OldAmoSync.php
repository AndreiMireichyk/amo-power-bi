<?php

namespace App\Console\Commands\OldAmo;

use App\Models\OldAmo\AmoOldContact;
use Illuminate\Console\Command;

class OldAmoSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old_amo:sync';

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
     * @return void
     */
    public function handle()
    {

        $start = time();

        AmoOldContact::sync();

       echo "Затрачено времени ".date('H:i:s', time()-$start);

    }
}
