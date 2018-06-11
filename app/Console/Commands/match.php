<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Caiji\ContentController;

class match extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'match the resource and the video exist';

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
      $caiji = new ContentController();
      $caiji->matching_resource_and_video_Agreement();
    }
}