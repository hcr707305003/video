<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Caiji\sohuController;

class sohu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sohu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collecting sohu data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $caiji = new sohuController();
      $caiji->sohu();
    }
}
