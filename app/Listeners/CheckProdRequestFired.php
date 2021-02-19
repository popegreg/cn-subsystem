<?php

namespace App\Listeners;

use App\Events\CheckProdRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class CheckProdRequestFired
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CheckProdRequest  $event
     * @return void
     */
    public function handle(CheckProdRequest $event)
    {
        \Log::info('send at '.date('Y-m-d g:i:s a'));
        return DB::connection($event->con)->table('tbl_request_summary')
                    ->where('status','<>','Cancelled')
                    ->where('status','<>','Closed')
                    ->orderBy('id','desc')
                    ->get();
    }
}
