<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ElectionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'election:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating elections status by date';

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
        $today = date('Y-m-d');

        Log::info("checking and update status for elections");

        // updating status election where start_date is today
        DB::table('elections')->where('start_date', $today)
            ->update([
                'status' => "ongoing"
            ]);

        //updating status election to finish where end_date is today
        DB::table('elections')->where('end_date', $today)
            ->update([
                'status' => "finish"
            ]);
    }
}
