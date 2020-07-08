<?php

namespace App\Console\Commands\NewAmo;

use App\Models\NewAmo\AmoNewPipeline;
use Illuminate\Console\Command;

class PipeLinesSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrm:new-pipelines-sync';

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
        AmoNewPipeline::sync();
    }
}
