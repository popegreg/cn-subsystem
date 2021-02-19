<?php

namespace App\Listeners;

use App\Events\UpdateIQCInspection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class UpdateIQCInspectionFired
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
     * @param  UpdateIQCInspection  $event
     * @return void
     */
    public function handle(UpdateIQCInspection $event)
    {
        DB::connection($event->con)->table('tbl_wbs_inventory')
            ->where('invoice_no','like','PPS%')
            ->update([
                'iqc_status' => 1,
                'for_kitting' => 1
            ]);

        DB::connection($event->con)->table('tbl_wbs_inventory')
            ->whereIn('iqc_status',[0,3])
            ->where('judgement','Accepted')
            ->update([
                'iqc_status' => 1,
                'for_kitting' => 1
            ]);

        DB::connection($event->con)->table('tbl_wbs_inventory')
            ->whereIn('iqc_status',[0,3])
            ->where('judgement','Rejected')
            ->update([
                'iqc_status' => 2,
                'for_kitting' => 0
            ]);

        DB::connection($event->con)->table('tbl_wbs_material_receiving_batch')
            ->where('invoice_no','like','PPS%')
            ->update([
                'iqc_status' => 1,
                'for_kitting' => 1
            ]);

        DB::connection($event->con)->table('tbl_wbs_material_receiving_batch')
            ->whereIn('iqc_status',[0,3])
            ->where('judgement','Accepted')
            ->update([
                'iqc_status' => 1,
                'for_kitting' => 1
            ]);

        DB::connection($event->con)->table('tbl_wbs_material_receiving_batch')
            ->whereIn('iqc_status',[0,3])
            ->where('judgement','Rejected')
            ->update([
                'iqc_status' => 2,
                'for_kitting' => 0
            ]);

        DB::connection($event->con)->table('tbl_wbs_local_receiving_batch')
            ->whereIn('iqc_status',[0,3])
            ->where('judgement','Accepted')
            ->update([
                'iqc_status' => 1,
                'for_kitting' => 1
            ]);

        DB::connection($event->con)->table('tbl_wbs_local_receiving_batch')
            ->whereIn('iqc_status',[0,3])
            ->where('judgement','Rejected')
            ->update([
                'iqc_status' => 2,
                'for_kitting' => 0
            ]);

        $wbs = DB::connection($event->con)->table('tbl_wbs_inventory')
                ->where('iqc_status',3)->get();

        if (count((array)$wbs) > 0) {
            foreach ($wbs as $key => $inv) {
                if (strpos($event->con, 'cn') !== false) {
                    $iqc = DB::connection('mysqlcn')->table('iqc_inspections')
                                ->where('invoice_no',$inv->invoice_no)
                                ->where('partcode',$inv->item)
                                ->where('lot_no',$inv->lot_no)
                                ->where('judgement','<>','On-going')
                                ->get();

                    if (count((array)$iqc) > 0) {
                        $status = 0;
                        if (isset($iqc->judgement)) {
                            switch ($iqc->judgement) {
                                case 'Accepted':
                                    $status = 1;
                                    break;

                                case 'Rejected':
                                    $status = 2;
                                    break;
                                
                                default:
                                    $status = 1;
                                    break;
                            }
                            DB::connection($event->con)->table('tbl_wbs_inventory')
                                ->where('invoice_no',$inv->invoice_no)
                                ->where('item',$inv->item)
                                ->where('lot_no',$inv->lot_no)
                                ->update(['iqc_status',$status]);
                        }
                        
                    }
                }
                
            }
        }

        


        \Log::info('IQC Inspection Updated at '.date('Y-m-d g:i:s a'));
    }
}
