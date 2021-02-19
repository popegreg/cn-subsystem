<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: WBSIqcController.php
     MODULE NAME:  3006 : WBS - IQC Inspection
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.07.05
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.07.05     MESPINOSA       Initial Draft
     200-00-01   1     2016.07.01    AK.DELAROSA      Revision
*******************************************************************************/
?>
<?php

namespace App\Http\Controllers\WBS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; #Auth facade
use Carbon\Carbon;
use Datatables;
use Config;
use DB;
use PDF;
use App;

/**
* IQC Controller
**/
class WBSIqcController extends Controller
{
    /**
    * IQC constructor.
    **/
    protected $wbs;
    protected $mysql;
    protected $mssql;
    protected $common;

    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->wbs = $com->userDBcon(Auth::user()->productline,'wbs');
            $this->mysql = $com->userDBcon(Auth::user()->productline,'mysql');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }
    
    public function getWbsIqc(Request $request_data)
    {
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_IQCINS'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {

        	# Render WBS Page.
            return view('wbs.iqc',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function getLoadwbs(Request $req)
    {

        $iqc = DB::connection($this->wbs)->table('tbl_wbs_inventory as i')
                    ->leftJoin('tbl_wbs_material_receiving_batch as b','i.mat_batch_id','=','b.id')
                    ->leftJoin('tbl_wbs_local_receiving_batch as l','i.loc_batch_id','=','l.id')
                    ->where('i.iqc_status',$req->status)
                    ->where('i.not_for_iqc',0)
                    // ->where('b.qty','>',0)
                    ->orderBy('i.created_at','desc')
                    ->select([
                            DB::raw('i.id as id'),
                            DB::raw('i.item as item'),
                            DB::raw('i.item_desc as item_desc'),
                            DB::raw('i.supplier as supplier'),
                            DB::raw("IFNULL(b.qty,(SELECT b.qty FROM tbl_wbs_local_receiving_batch as b
                                                    where b.id = i.loc_batch_id)) as qty"),
                            DB::raw('i.lot_no as lot_no'),
                            DB::raw('i.drawing_num as drawing_num'),
                            DB::raw('i.wbs_mr_id as wbs_mr_id'),
                            DB::raw('i.invoice_no as invoice_no'),
                            DB::raw('i.iqc_result as iqc_result'),
                            DB::raw('i.updated_at as updated_at'),
                            DB::raw('i.update_user as update_user'),
                            DB::raw('i.iqc_status as iqc_status'),
                            DB::raw('i.ins_date as ins_date'),
                            DB::raw('i.ins_time as ins_time'),
                            DB::raw('i.ins_by as ins_by'),
                            DB::raw('i.app_date as app_date'),
                            DB::raw('i.app_time as app_time'),
                            DB::raw('i.app_by as app_by')
                        ]);

        return Datatables::of($iqc)
                        ->editColumn('id', function ($data) {
                            return $data->id;
                        })
                        ->editColumn('iqc_status', function ($data) {
                            if ($data->iqc_status == 0) {
                                return "Pending";
                            }

                            if ($data->iqc_status == 1) {
                                return "Accepted";
                            }

                            if ($data->iqc_status == 2) {
                                return "Rejected";
                            }

                            if ($data->iqc_status == 3) {
                                return "On-going";
                            }

                            if ($data->iqc_status == 4) {
                                return "Special Accept";
                            }
                        })
                        ->editColumn('app_date', function ($data) {
                            return $data->app_date.' '.$data->app_time;
                        })
                        ->editColumn('app_by', function ($data) {
                            return $data->app_by;
                        })
                        ->editColumn('ins_date', function ($data) {
                            return $data->ins_date.' '.$data->ins_time;
                        })
                        ->editColumn('ins_by', function ($data) {
                            return $data->ins_by;
                        })
                        ->addColumn('action', function ($data) {
                            return '<a href="javascript:;" class="updatesinglebtn btn btn-primary btn-sm input-sm" data-id="'.$data->id.
                                    '" data-app_date="'.$data->app_date.
                                    '" data-app_time="'.$data->app_time.'"><i class="fa fa-edit"></i></a>';
                        })
                        ->make(true);
    }

    public function postUpdateIQCstatus(Request $req)
    {
        $id = $req->id;
        $for_kit = '';
        $judgement = '';

        switch ($req->statusup) {
            case '1':
                $for_kit = '1';
                $judgement = 'Accepted';
                break;
            case '2':
                $for_kit = '0';
                $judgement = 'Rejected';
                break;
            case '3':
                $for_kit = '0';
                $judgement = 'On-going';
                break;

            default:
                $for_kit = '0';
                break;
        }

        $details = DB::connection($this->wbs)->table('tbl_wbs_inventory')
                        ->where('id',$id)->first();

        $app = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
                    ->select(
                            'id',
                            'app_date',
                            'app_time',
                            'wbs_mr_id'
                    )
                    ->where('id',$details->mat_batch_id)->first();

        if (count((array)$app) < 1) {
            $app = DB::connection($this->wbs)->table('tbl_wbs_local_receiving_batch')
                        ->select(
                                'id',
                                'app_date',
                                'app_time',
                                DB::raw('wbs_loc_id as wbs_mr_id')
                        )
                        ->where('id',$details->loc_batch_id)->first();

            DB::connection($this->wbs)->table('tbl_wbs_local_receiving_batch')
                    ->where('id',$details->loc_batch_id)
                    ->update([
                        'iqc_status' => $req->statusup,
                        'iqc_result' => $req->iqcresup,
                        'for_kitting'=> $for_kit,
                        'updated_at' => Carbon::now()
                    ]);
        }

        DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
            ->where('id',$details->mat_batch_id)
            ->update([
                'iqc_status' => $req->statusup,
                'iqc_result' => $req->iqcresup,
                'for_kitting'=> $for_kit,
                'updated_at' => Carbon::now()
            ]);

        $update = DB::connection($this->wbs)->table('tbl_wbs_inventory')
                    ->where('id',$id)
                    ->update([
                        'iqc_status' => $req->statusup,
                        'iqc_result' => $req->iqcresup,
                        'for_kitting'=> $for_kit,
                        'update_pg' => 'WBS IQC - ' . $judgement,
                        'update_user' => Auth::user()->user_id,
                        'updated_at' => Carbon::now()
                    ]);

        $checkIfExistIQC = DB::connection($this->mysql)->table('iqc_inspections')
                                ->where('inv_id',$req->id)
                                ->where('mr_id',$app->id)
                                ->count();

        if ($checkIfExistIQC > 0) {
            DB::connection($this->mysql)->table('iqc_inspections')
                ->where('inv_id',$req->id)
                ->where('mr_id',$app->id)
                ->update([
                    'time_ins_from' => $req->start_time,
                    'inspector' => $req->inspector,
                    'judgement' => $judgement,
                    'updated_at' => Carbon::now()
                ]);
        } else {
            DB::connection($this->mysql)->table('iqc_inspections')
                ->insert([
                    'invoice_no' => $details->invoice_no,
                    'partcode' => $details->item,
                    'partname' => $details->item_desc,
                    'supplier' => $details->supplier,
                    'app_date' => $app->app_date,
                    'app_time' => $app->app_time,
                    'app_no' => $app->wbs_mr_id,
                    'lot_no' => $details->lot_no,
                    'lot_qty' => $details->qty,
                    'time_ins_from' => $req->start_time,
                    'inspector' => $req->inspector,
                    'judgement' => $judgement,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'inv_id' => $req->id,
                    'mr_id' => $app->id
                ]);
        }

        if($update) {
            return response()->json(array('status'=>1),200);
        } else {
            return response()->json(array('status'=>0),200);
        }
    }

    public function postUpdateIQCstatusBulk(Request $req)
    {
        $cnt = 0;
        $idcount = count($req->id);
        $for_kit = '';
        $judgement = '';
        
        switch ($req->statusup) {
            case '1':
                $for_kit = '1';
                $judgement = 'Accepted';
                break;
            case '2':
                $for_kit = '0';
                $judgement = 'Rejected';
                break;
            case '3':
                $for_kit = '0';
                $judgement = 'On-going';
                break;

            default:
                $for_kit = '0';
                break;
        }

        $invoice_no = '';
        $partcode = '';
        $partname = '';
        $supplier = '';
        $app_date = '';
        $app_time = '';
        $app_no = '';
        $lot_qty = 0;
        $time_ins_from = '';
        $inspector = '';
        $lot = '';
        $invid = '';
        $mrid = '';
        
        $mrbatchid = 0;

        $same_item = false;

        $params = [];
        $lot_no = [];
        $inv_id = [];
        $mr_id = [];

        if (count(array_unique($req->item)) === 1) {
             $same_item = true;
        }

        if ($same_item == true) {
            foreach ($req->id as $key => $id) {
                // $iqc = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
                //             ->where('id',$id)->first();
                // $app = DB::connection($this->wbs)->table('tbl_wbs_material_receiving')
                //             ->where('invoice_no',$iqc->invoice_no)->first();

                $iqc = DB::connection($this->wbs)
                            ->table('tbl_wbs_inventory')
                            ->where('id',$id)
                            ->first();

                $app = DB::connection($this->wbs)
                            ->table('tbl_wbs_material_receiving')
                            ->where('invoice_no',$iqc->invoice_no)
                            ->first();

                if (count((array)$app) < 1) {
                    $app = DB::connection($this->wbs)->table('tbl_wbs_local_receiving')
                                ->where('invoice_no',$iqc->invoice_no)
                                ->first();

                    DB::connection($this->wbs)->table('tbl_wbs_local_receiving_batch')
                        ->where('id',$iqc->loc_batch_id)
                        ->update([
                            'iqc_status' => $req->statusup,
                            'iqc_result' => $req->iqcresup,
                            'for_kitting'=> $for_kit,
                            'updated_at' => Carbon::now(),
                        ]);

                    $mrbatchid = $iqc->loc_batch_id;
                } else {
                    DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
                        ->where('id',$iqc->mat_batch_id)
                        ->update([
                            'iqc_status' => $req->statusup,
                            'iqc_result' => $req->iqcresup,
                            'for_kitting'=> $for_kit,
                            'updated_at' => Carbon::now(),
                        ]);

                    $mrbatchid = $iqc->mat_batch_id;
                }

                array_push($inv_id,$id);
                array_push($mr_id,$mrbatchid);

                array_push($lot_no, $iqc->lot_no);

                $invoice_no = $iqc->invoice_no;
                $partcode = $iqc->item;
                $partname = $iqc->item_desc;
                $supplier = $iqc->supplier;
                $app_date = $app->app_date;
                $app_time = $app->app_time;
                $app_no = $app->receive_no;
                $lot_qty = $lot_qty + $iqc->qty;
                $time_ins_from = $req->start_time;
                $inspector = $req->inspector;

                DB::connection($this->wbs)->table('tbl_wbs_inventory')
                    ->where('id',$id)
                    ->update([
                        'iqc_status' => $req->statusup,
                        'iqc_result' => $req->iqcresup,
                        'for_kitting'=> $for_kit,
                        'update_pg' => 'WBS IQC BULK - ' . $judgement,
                        'update_user' => Auth::user()->user_id,
                        'updated_at' => Carbon::now()
                    ]);
                $cnt++;
            }


            $lot = implode(',', $lot_no);
            $invid = implode(',', $inv_id);
            $mrid = implode(',', $mr_id);

            $params = [
                'invoice_no' => $invoice_no,
                'partcode' => $partcode,
                'partname' => $partname,
                'supplier' => $supplier,
                'app_date' => $app_date,
                'app_time' => $app_time,
                'app_no' => $app_no,
                'lot_no' => $lot,
                'lot_qty' => $lot_qty,
                'time_ins_from' => $time_ins_from,
                'inspector' => $inspector,
                'judgement' => $judgement,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'inv_id' => $invid,
                'mr_id' => $mrid
            ];

            DB::connection($this->mysql)->table('iqc_inspections')->insert($params);
        } else {
            foreach ($req->id as $key => $id) {
                $details = DB::connection($this->wbs)->table('tbl_wbs_inventory')
                            ->where('id',$id)->first();

                $app = DB::connection($this->wbs)->table('tbl_wbs_material_receiving')
                        ->where('invoice_no',$details->invoice_no)->first();

                if (count((array)$app) < 1) {
                    $app = DB::connection($this->wbs)->table('tbl_wbs_local_receiving')
                            ->where('invoice_no',$details->invoice_no)->first();

                    DB::connection($this->wbs)->table('tbl_wbs_local_receiving_batch')
                        ->where('id',$details->loc_batch_id)
                        ->update([
                            'iqc_status' => $req->statusup,
                            'iqc_result' => $req->iqcresup,
                            'for_kitting'=> $for_kit,
                            'updated_at' => Carbon::now(),
                        ]);
                    $mrbatchid = $details->loc_batch_id;
                } else {
                    DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
                        ->where('id',$details->mat_batch_id)
                        ->update([
                            'iqc_status' => $req->statusup,
                            'iqc_result' => $req->iqcresup,
                            'for_kitting'=> $for_kit,
                            'updated_at' => Carbon::now(),
                        ]);
                    $mrbatchid = $details->mat_batch_id;
                }
                
                DB::connection($this->mysql)->table('iqc_inspections')
                    ->insert([
                        'invoice_no' => $details->invoice_no,
                        'partcode' => $details->item,
                        'partname' => $details->item_desc,
                        'supplier' => $details->supplier,
                        'app_date' => $app->app_date,
                        'app_time' => $app->app_time,
                        'app_no' => $app->receive_no,
                        'lot_no' => $details->lot_no,
                        'lot_qty' => $details->qty,
                        'time_ins_from' => $req->start_time,
                        'inspector' => $req->inspector,
                        'judgement' => $judgement,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'inv_id' => $id,
                        'mr_id' => $mrbatchid
                    ]);

                DB::connection($this->wbs)->table('tbl_wbs_inventory')
                    ->where('id',$id)
                    ->update([
                        'iqc_status' => $req->statusup,
                        'iqc_result' => $req->iqcresup,
                        'for_kitting'=> $for_kit,
                        'update_pg' => 'WBS IQC - BULK ' . $judgement,
                        'update_user' => Auth::user()->user_id,
                        'updated_at' => Carbon::now()
                    ]);
                $cnt++;
            }
        }

        if($cnt == $idcount) {
            return response()->json(array('status'=>1),200);
        } else {
            return response()->json(array('status'=>0),200);
        }
    }

    public function getSearch(Request $req)
    {
        $from = $req->from;
        $to = $req->to;
        $recno = $req->recno;
        $status = $req->status;
        $itemno = $req->itemno;
        $lotno = $req->lotno;
        $invoice_no = $req->invoice_no;
        // $receivedate_cond = '';
        // $item_cond = '';
        // $status_cond = '';
        // $lotno_cond = '';

        if(is_null($from) and is_null($to) or $from == '' and $to == '')
        {
            $receivedate_cond = '';
        }
        else
        {
            $receivedate_cond = " AND i.received_date BETWEEN '" . $from . "' AND '" . $to . "'";
        }

        if($itemno == '')
        {
            $item_cond ='';
        }
        else
        {
            $item_cond = " AND i.item = '" . $itemno . "'";
        }

        if($status == '')
        {
            $status_cond = '';
        }
        else
        {
            $status_cond = " AND i.iqc_status = '" . $status . "'";
        }

        if($lotno == '')
        {
            $lotno_cond = '';
        }
        else
        {
            $lotno_cond = " AND i.lot_no = '" . $lotno . "'";
        }

        if($recno == '')
        {
            $recno_cond = '';
        }
        else
        {
            $recno_cond = " AND i.wbs_mr_id = '" . $recno . "'";
        }

        if($invoice_no == '')
        {
            $invoice_no_cond = '';
        }
        else
        {
            $invoice_no_cond = " AND i.invoice_no = '" . $invoice_no . "'";
        }

        $iqc = DB::connection($this->wbs)->table('tbl_wbs_inventory as i')
                    ->leftJoin('tbl_wbs_material_receiving_batch as b','i.mat_batch_id','=','b.id')
                    ->leftJoin('tbl_wbs_local_receiving_batch as l','i.loc_batch_id','=','l.id')
                    ->whereRaw("i.qty = (IFNULL(b.qty,(
                                                SELECT b.qty
                                                FROM tbl_wbs_local_receiving_batch as b
                                                where b.id = i.loc_batch_id
                                            )))". $receivedate_cond
                                                . $item_cond
                                                . $status_cond
                                                . $lotno_cond
                                                . $recno_cond
                                                . $invoice_no_cond)
                    ->orderBy('i.created_at','desc')
                    ->select([
                            DB::raw('i.id as id'),
                            DB::raw('i.item as item'),
                            DB::raw('i.item_desc as item_desc'),
                            DB::raw('i.supplier as supplier'),
                            DB::raw("IFNULL(b.qty,(SELECT loc.qty FROM tbl_wbs_local_receiving_batch as loc
                                                    where loc.id = i.loc_batch_id)) as qty"),
                            DB::raw('i.lot_no as lot_no'),
                            DB::raw('i.drawing_num as drawing_num'),
                            DB::raw('i.wbs_mr_id as wbs_mr_id'),
                            DB::raw('i.invoice_no as invoice_no'),
                            DB::raw('i.iqc_result as iqc_result'),
                            DB::raw('i.updated_at as updated_at'),
                            DB::raw('i.update_user as update_user'),
                            DB::raw('i.iqc_status as iqc_status'),
                            DB::raw('i.ins_date as ins_date'),
                            DB::raw('i.ins_time as ins_time'),
                            DB::raw('i.ins_by as ins_by'),
                            DB::raw('i.app_date as app_date'),
                            DB::raw('i.app_time as app_time'),
                            DB::raw('i.app_by as app_by'),
                        ]);

        return Datatables::of($iqc)
                        ->editColumn('id', function ($data) {
                            return $data->id;
                        })
                        ->editColumn('iqc_status', function ($data) {
                            if ($data->iqc_status == 0) {
                                return "Pending";
                            }

                            if ($data->iqc_status == 1) {
                                return "Accepted";
                            }

                            if ($data->iqc_status == 2) {
                                return "Rejected";
                            }

                            if ($data->iqc_status == 3) {
                                return "On-going";
                            }

                            if ($data->iqc_status == 4) {
                                return "Special Accept";
                            }
                        })
                        ->addColumn('action', function ($data) {
                            return '<a href="javascript:;" class="updatesinglebtn btn btn-primary btn-sm input-sm" data-id="'.$data->id.'"><i class="fa fa-edit"></i></a>';
                        })
                        ->make(true);
    }

    private function checkIfExistObject($object)
    {
       return count( (array)$object);
    }
}
