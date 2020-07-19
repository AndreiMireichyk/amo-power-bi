<?php

namespace App\Console\Commands\OldAmo;

use App\Models\OldAmo\AmoOldTask;
use Illuminate\Console\Command;

class TasksSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrmold:tasks-sync';

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
        AmoOldTask::sync();
    }
}
