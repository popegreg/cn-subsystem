<?php

namespace App\Listeners;

use App\Events\WHSCheckRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class WHSCheckRequestFired
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
     * @param  WHSCheckRequest  $event
     * @return void
     */
    public function handle(WHSCheckRequest $event)
    {
        $db = DB::connection($event->conn)->table('tbl_request_summary')
                ->select('transno','status')
                ->where('transno',$event->transno)
                ->first();

        $status = "Closed";
        //foreach ($db as $key => $pmr) {
        if ($db->status !== 'Cancelled') {
            $requestqty = DB::connection($event->conn)->table('tbl_request_detail')
                            ->where('transno',$db->transno)->sum('requestqty');

            $servedqty = DB::connection($event->conn)->table('tbl_request_detail')
                            ->where('transno',$db->transno)->sum('servedqty');

                if ( ($servedqty > 0) && ($requestqty == $servedqty) ) {
                    $status = 'Closed';
                }

                if ( ($servedqty < 1) ) {
                    $status = 'Alert';
                }

                if ( ($servedqty > 0) && ($requestqty > $servedqty)) {
                    $status = 'Serving';
                }

                DB::connection($event->conn)->table('tbl_request_summary')
                    ->where('transno',$db->transno)
                    ->update(['status' => $status]);
            
        }
    }
}
