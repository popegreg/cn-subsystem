<?php

namespace App\Http\Controllers\WBS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use DB;
use Config;
use Carbon\Carbon;
use PDF;
use App;
use Excel;
use File;

class WBSMaterialKittingController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;
    protected $com;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $this->com->userDBcon(Auth::user()->productline,'wbs');
            $this->mssql = $this->com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $this->com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function index()
    {
        $pgcode = Config::get('constants.MODULE_CODE_MATKIT');

        if(!$this->com->getAccessRights($pgcode, $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
          $data = DB::connection($this->common)
                    ->select("SELECT id,dropdown_reason FROM
                        dropdown_fifo_reason
                        ");
                        
            return view('wbs.materialkitting',[
                            'userProgramAccess' => $userProgramAccess, 
                            'data' => $data,
                            'pgcode' => $pgcode,
                            'pgaccess' => $this->com->getPgAccess($pgcode)
                        ]);
        }
    }

    public function postSearchPO(Request $req)
    {
    	$data = [
    		'msg' => "Searching failed.",
    		'status' => 'failed'
    	];

        if ($this->NotAvailable($req->po) > 0) {
        	$data = [
	    		'msg' => "P.O. number was already closed.",
	    		'status' => 'failed'
	    	];
        } else {
            $this->com->truncateTable($this->mysql,'tbl_temp_material_kitting');

            $info = '';
            $details = '';
            $info = DB::connection($this->mssql)
                        ->table('XSLIP as s')
                        ->leftJoin('XHEAD as h', 's.CODE', '=', 'h.CODE')
                        ->leftjoin('XRECE as r', 's.SEIBAN','=','r.SORDER')
                        ->select(DB::raw('s.CODE as code'),
                                DB::raw('h.NAME as prodname'),
                                DB::raw('r.KVOL as POqty'),
                                DB::raw('s.PORDER as porder'),
                                DB::raw('r.SEDA as branch'))
                        ->where('s.SEIBAN',$req->po)
                        ->orderBy('r.SEDA','desc')
                        ->orderBy('s.PORDER','desc')
                        ->first();

                        // SELECT s.CODE as code,
                        //         h.NAME as prodname,
                        //         r.KVOL as POqty,
                        //         s.PORDER as porder,
                        //         r.SEDA as branch
                        // FROM XSLIP as s
                        // LEFT JOIN XHEAD as H ON s.CODE = h.CODE
                        // LEFT JOIN XRECE as r ON s.SEIBAN = r.SORDER
                        // WHERE s.SEIBAN = ''
                        // ORDER BY r.SEDA DESC, s.PORDER DESC

            if (count((array)$info) > 0) {
                $details = DB::connection($this->mssql)
                                ->select("SELECT hk.CODE as kcode, 
                                                h.NAME as partname, 
                                                hk.KVOL as rqdqty, 
                                                x.RACKNO as location, 
                                                i.DRAWING_NUM as drawnum, 
                                                i.VENDOR as supplier, 
                                                x.WHS100 as whs100, 
                                                x.WHS102 as whs102,
                                                xp.SIYOU as usage
                                        FROM XRECE r
                                        LEFT JOIN XHIKI hk ON r.CODE = hk.OYACODE AND hk.PORDER = '".$info->porder."'
                                        LEFT JOIN XITEM i ON i.CODE = hk.CODE
                                        LEFT JOIN XHEAD h ON h.CODE = hk.CODE
                                        LEFT JOIN XPRTS AS xp ON xp.KCODE = h.CODE AND xp.CODE = hk.OYACODE
                                        LEFT JOIN (SELECT z.CODE, 
                                                        ISNULL(z1.ZAIK,0) as WHS100, 
                                                        ISNULL(z2.ZAIK,0) as WHS102, 
                                                        z1.RACKNO FROM XZAIK z
                                                    LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
                                                    LEFT JOIN XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS102'
                                                    WHERE z1.ZAIK IS NOT NULL
                                                    GROUP BY z.CODE, z1.ZAIK, z2.ZAIK, z1.RACKNO
                                        ) x ON x.CODE = hk.CODE
                                        WHERE r.SORDER = '".$req->po."' AND hk.PORDER = '".$info->porder."'
                                        GROUP BY hk.CODE, 
                                                h.NAME, 
                                                i.VENDOR, 
                                                hk.KVOL, 
                                                i.DRAWING_NUM, 
                                                x.WHS100, 
                                                x.WHS102, 
                                                x.RACKNO,
                                                xp.SIYOU");

                                // SELECT hk.CODE as kcode, 
                                //         h.NAME as partname, 
                                //         hk.KVOL as rqdqty, 
                                //         x.RACKNO as location, 
                                //         i.DRAWING_NUM as drawnum, 
                                //         i.VENDOR as supplier, 
                                //         x.WHS100 as whs100, 
                                //         x.WHS102 as whs102,
                                //         xp.SIYOU as usage
                                // FROM XRECE r
                                // LEFT JOIN XSLIP s ON r.SORDER = s.SEIBAN
                                // LEFT JOIN XHIKI hk ON s.PORDER = hk.PORDER
                                // LEFT JOIN XITEM i ON i.CODE = hk.CODE
                                // LEFT JOIN XHEAD h ON h.CODE = hk.CODE
                                // LEFT JOIN (SELECT z.CODE, 
                                //                 ISNULL(z1.ZAIK,0) as WHS100, 
                                //                 ISNULL(z2.ZAIK,0) as WHS102, 
                                //                 z1.RACKNO FROM XZAIK z
                                //            LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
                                //            LEFT JOIN XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS102'
                                //            WHERE z1.ZAIK IS NOT NULL
                                //            GROUP BY z.CODE, z1.ZAIK, z2.ZAIK, z1.RACKNO
                                // ) x ON x.CODE = hk.CODE
                                // JOIN XPRTS AS xp ON xp.KCODE = hk.CODE AND xp.CODE = hk.OYACODE
                                // WHERE r.SORDER = '".$req->po."' AND s.PORDER = '".$info->porder."'
                                // GROUP BY hk.CODE, 
                                //         h.NAME, 
                                //         i.VENDOR, 
                                //         hk.KVOL, 
                                //         i.DRAWING_NUM, 
                                //         x.WHS100, 
                                //         x.WHS102, 
                                //         x.RACKNO,
                                //         xp.SIYOU

                                        
                $dt = Carbon::now();
                $yr = substr($dt->format('Y'), 2);
                $mm = $dt->format('m');
                $po = $req->po;

                $id=1;
                $usage = 0.0000;
                $rqdqty = 0;
                $params = [];

                if (count((array)$details) > 0) {
                    foreach ($details as $key => $detail) {
                        $usage = $detail->usage;
                        if ($detail->rqdqty % $info->POqty == 0) {
                            $usage = $detail->rqdqty / $info->POqty;
                            $rqdqty = $detail->rqdqty;
                        } else {
                            if ($usage > 0 && $usage < 1) {
                                $usage = $detail->rqdqty / $info->POqty;
                                $rqdqty = $usage * $info->POqty;
                            } else {
                                $usage = (int)$usage;
                                $rqdqty = $usage * $info->POqty;
                            }
                        }

                        $check = $this->checkIfDuplicate($po,$info->code,$detail->kcode,$rqdqty,$this->lastinsert(),$detail->drawnum,$detail->supplier,$detail->whs100,$detail->whs102);

                        if ($check > 0) {
                            DB::connection($this->mysql)->table('tbl_temp_material_kitting')
                                ->where('id',$this->lastinsert())
                                ->delete();

                            $this->insertIntoTempMaterialKittingTable($id,$po,$info,$detail,$usage,$rqdqty,$req);
                            $id++;
                        } else {
                            $this->insertIntoTempMaterialKittingTable($id,$po,$info,$detail,$usage,$rqdqty,$req);
                            $id++;
                        }
                    }

                    $data = DB::connection($this->mysql)->table('tbl_temp_material_kitting')->where('issue_no',$po)->get();
                    if ($this->com->checkIfExistObject($data) > 0) {
                        $data = DB::connection($this->mysql)->table('tbl_temp_material_kitting')->where('issue_no',$po)->get();
                    } else {
                        $data = [
                            'msg' => "No data retrieved.",
                            'status' => 'failed'
                        ];
                    }
                } else {
                    $data = [
                        'msg' => "P.O. not found.",
                        'status' => 'failed'
                    ];
                }

            } else {
                $data = [
                    'msg' => "P.O. is not yet registered in YPICS.",
                    'status' => 'failed'
                ];
            }
        }

        return $data;
    }
//DEAN POGI
    private function insertIntoTempMaterialKittingTable($id,$po,$info,$detail,$usage,$rqdqty,$req)
    {
    	DB::connection($this->mysql)->table('tbl_temp_material_kitting')->insert([
            'issue_no' => $po,
            'code' => $info->code,
            'prodname' => $info->prodname,
            'POqty' => $this->cleanQty($info->POqty),
            'detailid' => $id,
            'item' => $detail->kcode,
            'item_desc' => $this->com->convert_unicode($detail->partname),
            'usage' => $detail->usage,
            'rqd_qty' => $this->cleanQty($rqdqty),
            'kit_qty' => 0.0000,
            'issued_qty' => ($this->getIssuedQty($req->po,$detail->kcode) !== null)? $this->getIssuedQty($req->po,$detail->kcode):0.0000,
            'location' => $detail->location,
            'drawing_no' => $this->com->convert_unicode($detail->drawnum),
            'supplier' => $detail->supplier,
            'whs100' => $detail->whs100,
            'whs102' => $detail->whs102
        ]);
    }

    private function cleanQty($qty)
    {
        $qqtys = str_replace(',', '',$qty );
        if( is_numeric( $qqtys ) ) {
            $qty = $qqtys;
        }

        return $qty;
    }

    private function NotAvailable($po)
    {
        return DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                    ->where('po_no',$po)
                    ->where('status','Closed')
                    ->count();
    }

    private function getIssuedQty($po,$item)
    {
        $db = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                ->where('po',$po)
                ->where('item',$item)
                ->select(DB::raw("SUM(issued_qty) as issued_qty"))
                ->get();
        if ($this->com->checkIfExistObject($db)) {
            return $db[0]->issued_qty;
        }
    }

    private function checkIfDuplicate($issue_no,$code,$item,$rqd_qty,$id,$drawing_no,$supplier,$whs100,$whs102)
    {
        $cnt = DB::connection($this->mysql)->table('tbl_temp_material_kitting')
                    ->where([
                        ['issue_no',$issue_no],
                        ['code',$code],
                        ['item',$item],
                        ['rqd_qty',$rqd_qty],
                        ['drawing_no',$drawing_no],
                        ['supplier',$supplier],
                        ['whs100',$whs100],
                        ['whs102',$whs102],
                        ['id',$id]
                    ])
                    ->where('location','')->count();
        return $cnt;
    }

    private function lastinsert()
    {
        $db = DB::connection($this->mysql)->table('tbl_temp_material_kitting')
                ->select('id')
                ->orderBy('id','desc')->first();
        if ($this->com->checkIfExistObject($db) > 0) {
            return $db->id;
        }
    }
    
    public function postSaveKitDetails(Request $req)
    {
        $dt = Carbon::now();
        $date = $dt->format('Y-m-d');
        $params = [];

        $data = [
        	'msg' => "Saving failed.",
        	'status' => 'failed'
        ];
        if (empty($req->id)) {
            $issuance_no = $this->com->getTransCode('MKL_ISS');
            $insert = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                        ->insert([
                            'issuance_no' => $issuance_no,
                            'po_no' => $req->po,
                            'device_code' => $req->devicecode,
                            'device_name' => $req->devicename,
                            'po_qty' => $this->cleanQty($req->poqty),
                            'kit_qty' => $this->cleanQty($req->kitqty),
                            'kit_no' => $req->kitno,
                            'prepared_by' => $req->preparedby,
                            'status' => 'O',
                            'assessment'=>$req->assessment,
                            'issuance_date' => $date,
                            'create_user' => Auth::user()->user_id,
                            'update_user' => Auth::user()->user_id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
            // dd($insert);
            if ($insert) {
                foreach ($req->kit_detail_id as $key => $idkit) {
                    array_push($params, [
                            'issue_no' => $issuance_no,
                            'po' => $req->po,
                            'detailid' => $idkit,
                            'item' => $req->kit_itemcode[$key],
                            'item_desc' => $req->kit_itemname[$key],
                            'usage' => $req->kit_usage[$key],
                            'rqd_qty' => $this->cleanQty($req->kit_rqdqty[$key]),
                            'kit_qty' => $this->cleanQty($req->kit_qty[$key]),
                            'issued_qty' => $this->cleanQty($req->kit_issuedqty[$key]),
                            'location' => $req->kit_loaction[$key],
                            'drawing_no' => $req->kit_drawno[$key],
                            'supplier' => $req->kit_supplier[$key],
                            'whs100' => $req->kit_whs100[$key],
                            'whs102' => $req->kit_whs102[$key],
                            'create_user' => Auth::user()->user_id,
                            'update_user' => Auth::user()->user_id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'status' => 'O',
                        ]);
                }

                DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')->insert($params);

                $data = [
                    'msg' => "Issuance No. [".$issuance_no."] was successfully saved.",
                    'status' => 'success'
                ];
            }
        } else {
            $status = '';
            if ($req->status == 'Open') {
                $status = 'O';
            }

            if ($req->status == 'Cancelled') {
                $status = 'C';
            }

            if ($req->status == 'Closed') {
                $status = 'X';
            }

            DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                ->where('id',$req->id)
                ->update([
                    'po_qty' => $this->cleanQty($req->poqty),
                    'kit_qty' => $this->cleanQty($req->kitqty),
                    'kit_no' => $req->kitno,
                    'assessment' => $req->assessment,
                    'prepared_by' => $req->preparedby,
                    'update_user' => Auth::user()->user_id,
                    'updated_at' => Carbon::now()
                ]);

            $updateKit = false;
            foreach ($req->kitting_details_id as $key => $id) {
                $updateKit = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                                ->where('id',$id)
                                ->update([
                                    'item' => $req->kit_itemcode[$key],
                                    'item_desc' => $req->kit_itemname[$key],
                                    'usage' => $req->kit_usage[$key],
                                    'rqd_qty' => $this->cleanQty($req->kit_rqdqty[$key]),
                                    'kit_qty' => $this->cleanQty($req->kit_qty[$key]),
                                    'issued_qty' => $this->cleanQty($req->kit_issuedqty[$key]),
                                    'location' => $req->kit_loaction[$key],
                                    'drawing_no' => $req->kit_drawno[$key],
                                    'supplier' => $req->kit_supplier[$key],
                                    'whs100' => $req->kit_whs100[$key],
                                    'whs102' => $req->kit_whs102[$key],
                                    'update_user' => Auth::user()->user_id,
                                    'updated_at' => Carbon::now(),
                                    'status' => $status
                                ]);
            }

            if ($req->iss_db_id == '') {

                if (isset($req->issdetailid)) {
                    foreach ($req->issdetailid as $key => $iss_id) {
                        $check_issuance = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                            ->where('issue_no',$req->issuanceno)
                                            ->where('po',$req->po)
                                            ->where('detailid',$iss_id)
                                            ->count();
                        if ($check_issuance < 1) {
                            $check_kit_issued_qty = 0;
                            $kit_issue =  DB::connection($this->mysql)->table('tbl_wbs_kit_issuance as i')
                                        ->join('tbl_wbs_material_kitting as k', function($join) {
                                            $join->on('k.po_no', '=', 'i.po');
                                            $join->on('i.issue_no', '=', 'k.issuance_no');
                                        })
                                        ->where('i.po',$req->po)
                                        ->where('i.item',$req->issitem[$key])
                                        ->where('k.status','<>','C')
                                        ->select(
                                            DB::raw("SUM(i.issued_qty) as issued_qty")
                                        )->first();
                            
                            $kit_detail = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                                                ->where('issue_no', $req->issuanceno)
                                                ->where('po',$req->po)
                                                ->where('item', $req->issitem[$key])
                                                ->select('issued_qty','kit_qty','rqd_qty')
                                                ->first();
                            $check_kit_issued_qty = $kit_issue->issued_qty + $req->ississued_qty[$key];

                            if ($check_kit_issued_qty > $kit_detail->rqd_qty) {
                                $data = [
                                    'msg' => "Issuance Qty. is greater than required qty.",
                                    'status' => 'failed'
                                ];
                                return $data;
                            } else {
                                DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                                    ->where('issue_no', $req->issuanceno)
                                    ->where('po',$req->po)
                                    ->where('item', $req->issitem[$key])
                                    ->increment('issued_qty',$req->ississued_qty[$key]);
                                    // ->update([
                                    //     'issued_qty' => DB::raw("(issued_qty + ".$req->ississued_qty[$key].")")
                                    // ]);

                                DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                    ->insert([
                                        'issue_no' => $req->issuanceno,
                                        'po' => $req->po,
                                        'detailid' => $iss_id,
                                        'item' => $req->issitem[$key],
                                        'item_desc' => $req->issitemdesc[$key],
                                        'kit_qty' => $this->cleanQty($req->isskit_qty[$key]),
                                        'issued_qty' => $this->cleanQty($req->ississued_qty[$key]),
                                        'lot_no' => $req->isslot_no[$key],
                                        'location' => $req->isslocation[$key],
                                        'remarks' => $req->issremarks[$key],
                                        'create_user' => Auth::user()->user_id,
                                        'update_user' => Auth::user()->user_id,
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now(),
                                        'reason_id' =>$req->id_reason[$key],
                                        'approved_id'=>$req->user[$key],
                                        'inv_id' => $req->fifoid[$key],
                                        // 'approved_date' => Carbon::now(),
                                        'status' => 'O'
                                    ]);

                                $inv = DB::connection($this->mysql)->table('tbl_wbs_inventory')
                                        ->where('id',$req->fifoid[$key])
                                        ->decrement('qty',$this->cleanQty($req->ississued_qty[$key]));

                                // $inv = DB::connection($this->mysql)->table('tbl_wbs_inventory')
                                //         ->select('qty')
                                //         ->where('id',$req->fifoid[$key])
                                //         ->first();

                                // if ($this->com->checkIfExistObject($inv)) {

                                //     $qty = $inv->qty - $this->cleanQty($req->ississued_qty[$key]);
                                //     DB::connection($this->mysql)->table('tbl_wbs_inventory')
                                //         ->where('id',$req->fifoid[$key])
                                //         ->update([
                                //             'qty' => $qty
                                //         ]);
                                // }
                            }
                        }
                    }

                    //$this->updateIssuedQty($req->kitting_details_id);

                    if ($this->checkIfStatus($req->issuanceno)) {
                        DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                            ->where('issuance_no',$req->issuanceno)->update(['status'=>'X']);
                        DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                            ->where('issue_no',$req->issuanceno)->update(['status'=>'X']);
                        DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                            ->where('issue_no',$req->issuanceno)->update(['status'=>'X']);
                    }

                    $data = [
                        'msg' => "Issuance No. [".$req->issuanceno."] was successfully saved and added Details.",
                        'status' => 'success'
                    ];
                }

                if ($updateKit) {
                    $data = [
                        'msg' => "Issuance No. [".$req->issuanceno."] was successfully saved and added Details.",
                        'status' => 'success'
                    ];
                }
            } else {
                foreach ($req->iss_db_id as $key => $id) {
                    $issued = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                ->where('id',$id)
                                ->first();

                                DB::connection($this->mysql)->table('tbl_wbs_inventory')
                                    ->where('id',$req->fifoid[$key])
                                    ->increment('qty',$issued->issued_qty);

                    $db = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                            ->where('id',$id)
                            ->update([
                                'item' => $req->issitem[$key],
                                'item_desc' => $req->issitemdesc[$key],
                                'kit_qty' => $this->cleanQty($req->isskit_qty[$key]),
                                'issued_qty' => $this->cleanQty($req->ississued_qty[$key]),
                                'lot_no' => $req->isslot_no[$key],
                                'location' => $req->isslocation[$key],
                                'remarks' => $req->issremarks[$key],
                                'inv_id' => $req->fifoid[$key],
                                'update_user' => Auth::user()->user_id,
                                'updated_at' => Carbon::now()
                            ]); 

                    DB::connection($this->mysql)->table('tbl_wbs_inventory')
                            ->where('id',$req->fifoid[$key])
                            ->decrement('qty', $req->ississued_qty[$key]);
                            
                    // $inv = DB::connection($this->mysql)->table('tbl_wbs_inventory')
                    //         ->select('qty')
                    //         ->where('id',$req->fifoid[$key])
                    //         ->first();

                    // if ($this->com->checkIfExistObject($inv)) {
                    //     $qty = $inv->qty - $req->ississued_qty[$key];
                    //     DB::connection($this->mysql)->table('tbl_wbs_inventory')
                    //         ->where('id',$req->fifoid[$key])
                    //         ->update([
                    //             'qty' => $qty
                    //         ]);
                    // }

                    $iss = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                 ->where('issue_no',$req->issuanceno)
                                 ->where('item',$req->issitem[$key])
                                 ->select(DB::raw('SUM(issued_qty) as issued_qty'))
                                 ->first();

                    DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                        ->where('issue_no',$req->issuanceno)
                        ->where('item',$req->issitem[$key])
                        ->update(['issued_qty' => $this->cleanQty($iss->issued_qty)]);
                }

                if ($db) {
                    if ($this->checkIfStatus($req->issuanceno)) {
                        DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                            ->where('issuance_no',$req->issuanceno)->update(['status'=>'X']);
                        DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                            ->where('issue_no',$req->issuanceno)->update(['status'=>'X']);
                        DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                            ->where('issue_no',$req->issuanceno)->update(['status'=>'X']);
                    }

                    $this->checkIfPOisClosed($req->po);
                    
                    $data = [
                        'msg' => "Issuance No. [".$req->issuanceno."] was successfully updated Issuance Details",
                        'status' => 'success'
                    ];
                }
            }
        }
        return $data;
    }
// DEAN POGI
    private function updateIssuedQty($kitting_details_id)
    {
        foreach ($kitting_details_id as $key => $id) {
            $iss_qty = 0;
            $kit = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                        ->where('id',$id)
                        ->select('issue_no','item','issued_qty','kit_qty')
                        ->first();

            if ($kit->kit_qty > $kit->issued_qty) {
                $iss = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                            ->where('issue_no', $kit->issue_no)
                            ->where('item', $kit->item)
                            ->select('issued_qty')
                            ->orderBy('created_at','desc')
                            ->first();

                if ($this->com->checkIfExistObject($iss)) {
                     $iss_qty = $this->cleanQty($kit->issued_qty) + $this->cleanQty($iss->issued_qty);

                    if ($iss_qty > $this->cleanQty($kit->kit_qty)) {
                        $data = [
                            'msg' => "Issuance Qty. is greater than required qty.",
                            'status' => 'failed'
                        ];
                    } else {
                        $iss = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                                ->where('id',$id)
                                ->update(['issued_qty' => $this->cleanQty($iss_qty)]);

                    }
                }
            }
        }
    }

    public function checkIssuedQty(Request $req)
    {
    	$check = ['status' => 'success', 'save_status' => $req->iss_save_status];
    	if ($req->iss_save_status == 'EDIT') {
    		# code...
    	} else {
	        //$iss_qty = 0;
	        // $kit = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
	        //             ->where('issue_no',$req->issuanceno)
	        //             ->where('item',$req->item)
	        //             ->select('issued_qty','kit_qty')
	        //             ->first();
	        // $iss_qty = $this->cleanQty($kit->issued_qty) + $this->cleanQty($req->qty);

            // if ($iss_qty > $kit->kit_qty) {
            //     $check = ['status' => 'failed'];
            // }

            // 09.08.2020

            $kit_iss = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance as i')
                        ->join('tbl_wbs_material_kitting as k', function($join) {
                            $join->on('k.po_no', '=', 'i.po');
                            $join->on('i.issue_no', '=', 'k.issuance_no');
                        })
                        ->where('i.po',$req->po)
                        ->where('i.item',$req->item)
                        ->where('k.status','<>','C')
                        ->select(
                            DB::raw("SUM(i.issued_qty) as issued_qty")
                        )
                        ->first();

            if (count((array)$kit_iss) > 0) {
                $total_issued_qty = $this->cleanQty($kit_iss->issued_qty) + $this->cleanQty($req->qty);

                $kit_details = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                                ->where('po',$req->po)
                                ->where('item',$req->item)
                                ->where('issue_no',$req->issuanceno)
                                ->select(
                                    'rqd_qty'
                                )
                                ->first();

                if ($total_issued_qty > $kit_details->rqd_qty) {
                    $check = [
                        'status' => 'failed', 
                        'save_status' => $req->iss_save_status,
                        'total_issued_qty' => $kit_iss->issued_qty,
                        'po' => $req->po,
                        'rqd_qty' => $kit_details->rqd_qty,
                        'input_issued_qty' => $req->qty
                    ];
                }
            }
                
            // 09.08.2020

	        
    	}

        return $check;
    }

    private function checkIfStatus($issuanceno)
    {
        $equal = true;
        $req = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                ->where('issue_no',$issuanceno)
                ->select(DB::raw("SUM(kit_qty) as kit_qty"))
                ->first();
        $db = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                ->where('issue_no',$issuanceno)
                ->select(DB::raw("SUM(issued_qty) as issued_qty"))
                ->first();
        if ($this->com->checkIfExistObject($db)) {
            if ($this->cleanQty($req->kit_qty) > $db->issued_qty || $this->cleanQty($req->kit_qty) < $db->issued_qty) {
                $equal = false;
            }
        }

        return $equal;
    }

    public function getMaterialKittingData(Request $req)
    {
    	if (empty($req->to) && !empty($req->id)) {
    		$kitinfo = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
							->select('id'
							    , 'issuance_no'
							    , 'po_no'
							    , 'device_code'
							    , 'device_name'
							    , DB::raw("FORMAT(po_qty, 4) AS po_qty")
							    , DB::raw("FORMAT(kit_qty, 4) AS kit_qty")
							    , 'kit_no'
							    , 'prepared_by'
							    , 'status'
                                , 'assessment'
							    , 'create_user'
							    , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
							    , 'update_user'
							    , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
							->where('id',$req->id)
							->first();

    		if ($this->com->checkIfExistObject($kitinfo) > 0) {
	            // $kitdetails = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
             //                    ->where('issue_no',$kitinfo->issuance_no)
             //                    ->where('po',$kitinfo->po_no)
             //                    ->get();
                $kitdetails = DB::connection($this->mysql)
                                ->select("select d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                ifnull(SUM(i.issued_qty),0) AS issued_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102
                                        from tbl_wbs_material_kitting_details as d
                                        left join tbl_wbs_kit_issuance as i
                                        on i.item = d.item and i.po = '".$kitinfo->po_no."'
                                        where d.issue_no = '".$kitinfo->issuance_no."'
                                        and d.po = '".$kitinfo->po_no."'
                                        group by d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102");

	            $kitissuance = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                ->where('issue_no',$kitinfo->issuance_no)
                                ->where('po',$kitinfo->po_no)
                                ->get();

	            return $data = [
                                'status' => 'success',
			                	'kitinfo' => $kitinfo,
			                	'kitdetails' => $kitdetails,
			                	'kitissuance' => $kitissuance
			                ];
	        } else {
                $kitinfo = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                            ->select('id'
                                , 'issuance_no'
                                , 'po_no'
                                , 'device_code'
                                , 'device_name'
                                , DB::raw("FORMAT(po_qty, 4) AS po_qty")
                                , DB::raw("FORMAT(kit_qty, 4) AS kit_qty")
                                , 'kit_no'
                                , 'prepared_by'
                                , 'status'
                                ,'assessment'
                                , 'create_user'
                                , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                                , 'update_user'
                                , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                            ->where('issuance_no',$req->id)
                            ->first();
                if ($this->com->checkIfExistObject($kitinfo) > 0) {
                    // $kitdetails = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                    //                 ->where('issue_no',$kitinfo->issuance_no)
                    //                 ->where('po',$kitinfo->po_no)
                    //                 ->get();
                    $kitdetails = DB::connection($this->mysql)
                                    ->select("select d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                ifnull(SUM(i.issued_qty),0) AS issued_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102
                                        from tbl_wbs_material_kitting_details as d
                                        left join tbl_wbs_kit_issuance as i
                                        on i.item = d.item and i.po = '".$kitinfo->po_no."'
                                        where d.issue_no = '".$kitinfo->issuance_no."'
                                        and d.po = '".$kitinfo->po_no."'
                                        group by d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102");

                    $kitissuance = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                    ->where('issue_no',$kitinfo->issuance_no)
                                    ->where('po',$kitinfo->po_no)
                                    ->get();

                    return $data = [
                                    'status' => 'success',
                                    'kitinfo' => $kitinfo,
                                    'kitdetails' => $kitdetails,
                                    'kitissuance' => $kitissuance
                                ];
                }
            }

            return $data = [
                'status' => 'failed',
                'msg' => 'No data found.'
            ];
    	}

    	if (!empty($req->to) && !empty($req->id)) {
    		return $this->navigate($req->to,$req->id);
    	}
    	if (empty($req->to) && empty($req->id)) {
    		return $this->last();
    	}
    }

    public function getKitData(Request $req)
    {
    	return DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
    			->where('issue_no',$req->issuanceno)->get();
    }

    private function navigate($to,$id)
    {
    	switch ($to) {
            case 'next':
                return $this->next($id);
                break;

            case 'prev':
                return $this->prev($id);
                break;

            case 'last':
                return $this->last();
                break;

            case 'first':
                return $this->first();
                break;

            default:
                return $this->last();
                break;
        }
    }

    private function next($id) 
    {
        $data = [];
        $nxt = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                        ->where('id',$id)
                        ->select('id')->first();

        if ($this->com->checkIfExistObject($nxt) > 0) {
            $kitinfo = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                        ->select('id'
                            , 'issuance_no'
                            , 'po_no'
                            , 'device_code'
                            , 'device_name'
                            , DB::raw("FORMAT(po_qty, 4) AS po_qty")
                            , DB::raw("FORMAT(kit_qty, 4) AS kit_qty")
                            , 'kit_no'
                            , 'prepared_by'
                            , 'status'
                            ,'assessment'
                            , 'create_user'
                            , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                            , 'update_user'
                            , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("id",">",$nxt->id)
                        ->orderBy("id")
                        ->first();

            if ($this->com->checkIfExistObject($kitinfo) > 0) {

            	// $kitdetails = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
             //                    ->where('issue_no',$kitinfo->issuance_no)
             //                    ->where('po',$kitinfo->po_no)
             //                    ->get();
                $kitdetails = DB::connection($this->mysql)
                                ->select("select d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                ifnull(SUM(i.issued_qty),0) AS issued_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102
                                        from tbl_wbs_material_kitting_details as d
                                        left join tbl_wbs_kit_issuance as i
                                        on i.item = d.item and i.po = '".$kitinfo->po_no."'
                                        where d.issue_no = '".$kitinfo->issuance_no."'
                                        and d.po = '".$kitinfo->po_no."'
                                        group by d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102");
            	$kitissuance = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                ->where('issue_no',$kitinfo->issuance_no)
                                ->where('po',$kitinfo->po_no)
                                ->get();

                return $data = [
		                	'kitinfo' => $kitinfo,
		                	'kitdetails' => $kitdetails,
		                	'kitissuance' => $kitissuance
		                ];
            } else {
                return $this->last();
            }
        } else {
            $data = [
                    'msg' => "You've reached the last Material Issuance Number",
                    'status' => 'failed'
                ];
        }

        return $data;
    }

    private function last()
    {
    	$data = [];
        $kitinfo =  DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
						->select('id'
						    , 'issuance_no'
						    , 'po_no'
						    , 'device_code'
						    , 'device_name'
						    , DB::raw("FORMAT(po_qty, 4) AS po_qty")
						    , DB::raw("FORMAT(kit_qty, 4) AS kit_qty")
						    , 'kit_no'
						    , 'prepared_by'
						    , 'status'
                            ,'assessment'
						    , 'create_user'
						    , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
						    , 'update_user'
						    , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MAX(id)"))
                              ->from('tbl_wbs_material_kitting');
                          })
						->first();
        if ($this->com->checkIfExistObject($kitinfo) > 0) {

            // $kitdetails = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
            //                 ->where('issue_no',$kitinfo->issuance_no)
            //                 ->where('po',$kitinfo->po_no)
            //                 ->get();
            $kitdetails = DB::connection($this->mysql)
                            ->select("select d.id,
                                            d.detailid,
                                            d.item,
                                            d.item_desc,
                                            d.`usage`,
                                            d.rqd_qty,
                                            d.kit_qty,
                                            ifnull(SUM(i.issued_qty),0) AS issued_qty,
                                            d.location,
                                            d.drawing_no,
                                            d.supplier,
                                            d.whs100,
                                            d.whs102
                                    from tbl_wbs_material_kitting_details as d
                                    left join tbl_wbs_kit_issuance as i
                                    on i.item = d.item and i.po = '".$kitinfo->po_no."'
                                    where d.issue_no = '".$kitinfo->issuance_no."'
                                    and d.po = '".$kitinfo->po_no."'
                                    group by d.id,
                                            d.detailid,
                                            d.item,
                                            d.item_desc,
                                            d.`usage`,
                                            d.rqd_qty,
                                            d.kit_qty,
                                            d.location,
                                            d.drawing_no,
                                            d.supplier,
                                            d.whs100,
                                            d.whs102");
            $kitissuance = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                            ->where('issue_no',$kitinfo->issuance_no)
                            ->where('po',$kitinfo->po_no)
                            ->get();

            $data = [
                	'kitinfo' => $kitinfo,
                	'kitdetails' => $kitdetails,
                	'kitissuance' => $kitissuance
                ];
        }

        return $data;
    }

    private function prev($id)
    {
        $data = [];
        $nxt = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                        ->where('id',$id)
                        ->select('id')->first();

        if ($this->com->checkIfExistObject($nxt) > 0) {
            //$nxtid = $nxt->id - 1;

            $kitinfo = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                        ->select('id'
                            , 'issuance_no'
                            , 'po_no'
                            , 'device_code'
                            , 'device_name'
                            , DB::raw("FORMAT(po_qty, 4) AS po_qty")
                            , DB::raw("FORMAT(kit_qty, 4) AS kit_qty")
                            , 'kit_no'
                            , 'prepared_by'
                            , 'status'
                            ,'assessment'
                            , 'create_user'
                            , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                            , 'update_user'
                            , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("id","<",$nxt->id)
                        ->orderBy("id","DESC")
                        ->first();

            if ($this->com->checkIfExistObject($kitinfo) > 0) {

            	// $kitdetails = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
             //                    ->where('issue_no',$kitinfo->issuance_no)
             //                    ->where('po',$kitinfo->po_no)
             //                    ->get();
                $kitdetails = DB::connection($this->mysql)
                                ->select("select d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                ifnull(SUM(i.issued_qty),0) AS issued_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102
                                        from tbl_wbs_material_kitting_details as d
                                        left join tbl_wbs_kit_issuance as i
                                        on i.item = d.item and i.po = '".$kitinfo->po_no."'
                                        where d.issue_no = '".$kitinfo->issuance_no."'
                                        and d.po = '".$kitinfo->po_no."'
                                        group by d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102");

            	$kitissuance = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                    ->where('issue_no',$kitinfo->issuance_no)
                                    ->where('po',$kitinfo->po_no)
                                    ->get();

                return $data = [
		                	'kitinfo' => $kitinfo,
		                	'kitdetails' => $kitdetails,
		                	'kitissuance' => $kitissuance
		                ];
            } else {
                return $this->first();
            }
        } else {
            $data = [
                'msg' => "You've reached the first Material Issuance Number",
                'status' => 'failed'
            ];
        }
        return $data;
    }

    private function first()
    {
    	$data = [];
        $kitinfo = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
						->select('id'
						    , 'issuance_no'
						    , 'po_no'
						    , 'device_code'
						    , 'device_name'
						    , DB::raw("FORMAT(po_qty, 4) AS po_qty")
						    , DB::raw("FORMAT(kit_qty, 4) AS kit_qty")
						    , 'kit_no'
						    , 'prepared_by'
						    , 'status'
                            ,'assessment'
						    , 'create_user'
						    , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
						    , 'update_user'
						    , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MIN(id)"))
                              ->from('tbl_wbs_material_kitting');
                          })
						->first();

        if ($this->com->checkIfExistObject($kitinfo) > 0) {

            // $kitdetails = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
            //                 ->where('issue_no',$kitinfo->issuance_no)
            //                 ->where('po',$kitinfo->po_no)
            //                 ->get();
            $kitdetails = DB::connection($this->mysql)
                                ->select("select d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                ifnull(SUM(i.issued_qty),0) AS issued_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102
                                        from tbl_wbs_material_kitting_details as d
                                        left join tbl_wbs_kit_issuance as i
                                        on i.item = d.item and i.po = '".$kitinfo->po_no."'
                                        where d.issue_no = '".$kitinfo->issuance_no."'
                                        and d.po = '".$kitinfo->po_no."'
                                        group by d.id,
                                                d.detailid,
                                                d.item,
                                                d.item_desc,
                                                d.`usage`,
                                                d.rqd_qty,
                                                d.kit_qty,
                                                d.location,
                                                d.drawing_no,
                                                d.supplier,
                                                d.whs100,
                                                d.whs102");
            $kitissuance = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                ->where('issue_no',$kitinfo->issuance_no)
                                ->where('po',$kitinfo->po_no)
                                ->get();

            $data = [
                	'kitinfo' => $kitinfo,
                	'kitdetails' => $kitdetails,
                	'kitissuance' => $kitissuance
                ];
		}
		return $data;
    }

    private function getKitInfoByID($id)
    {
    	return DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
					->select('id'
					    , 'issuance_no'
					    , 'po_no'
					    , 'device_code'
					    , 'device_name'
					    , DB::raw("po_qty AS po_qty")
					    , DB::raw("kit_qty AS kit_qty")
					    , 'kit_no'
					    , 'prepared_by'
					    , 'status'
                         ,'assessment'
					    , 'create_user'
					    , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
					    , 'update_user'
					    , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
					->where("id", $id)
					->first();
    }

    public function kittingList(Request $req)
    {
        //$id = trim($req['id']);
        $cur_id = '';
        $issuance_no = '';
        $max_id = '';
        $max_id = '';
        $whs100 = [];
        $whs102 = [];
        $whsnon = [];
        $whssm = [];
        $assy102 = [];

        $dt = Carbon::now();
        $date = substr($dt->format('  M j, Y A'), 2);

        $company_info = $this->com->getCompanyInfo();

        //$mk_data = $this->getKitInfoByID($id)
        $mk_data = DB::connection($this->mssql)
                        ->table('XSLIP as s')
                        ->leftJoin('XHEAD as h', 's.CODE', '=', 'h.CODE')
                        ->leftjoin('XRECE as r', 's.SEIBAN','=','r.SORDER')
                        ->select(DB::raw('s.CODE as code'),
                                DB::raw('h.NAME as prodname'),
                                DB::raw('r.KVOL as POqty'),
                                DB::raw('s.PORDER as porder'),
                                DB::raw('r.SEDA as branch'))
                        ->where('s.SEIBAN',$req->po)
                        ->orderBy('r.SEDA','desc')
                        ->orderBy('s.PORDER','desc')
                        ->first();

        if(count((array)$mk_data) > 0)
        {
            $info = DB::connection($this->mssql)
                        ->table('XSLIP as s')
                        ->leftJoin('XHEAD as h', 's.CODE', '=', 'h.CODE')
                        ->leftjoin('XRECE as r', 's.SEIBAN','=','r.SORDER')
                        ->select(DB::raw('s.CODE as code'),
                                DB::raw('h.NAME as prodname'),
                                DB::raw('r.KVOL as POqty'),
                                DB::raw('s.PORDER as porder'),
                                DB::raw('r.SEDA as branch'),
                                DB::raw('s.SEIBAN as po'))
                        ->where('s.SEIBAN',$req->po)
                        ->orderBy('r.SEDA','desc')
                        ->orderBy('s.PORDER','desc')
                        ->first();
                    
            // heto yung sa details...
            $mk_details_data = DB::connection($this->mssql)
                                ->select("SELECT hk.CODE as kcode, 
                                                h.NAME as partname, 
                                                hk.KVOL as rqdqty, 
                                                x.RACKNO as location, 
                                                i.DRAWING_NUM as drawnum, 
                                                i.VENDOR as supplier, 
                                                x.WHS100 as whs100, 
                                                x.WHS102 as whs102,
                                                xp.SIYOU as usage
                                        FROM XRECE r
                                        LEFT JOIN XHIKI hk ON r.CODE = hk.OYACODE AND hk.PORDER = '".$info->porder."'
                                        LEFT JOIN XITEM i ON i.CODE = hk.CODE
                                        LEFT JOIN XHEAD h ON h.CODE = hk.CODE
                                        LEFT JOIN XPRTS AS xp ON xp.KCODE = h.CODE AND xp.CODE = hk.OYACODE
                                        LEFT JOIN (SELECT z.CODE, 
                                                        ISNULL(z1.ZAIK,0) as WHS100, 
                                                        ISNULL(z2.ZAIK,0) as WHS102, 
                                                        z1.RACKNO FROM XZAIK z
                                                    LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
                                                    LEFT JOIN XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS102'
                                                    WHERE z1.ZAIK IS NOT NULL
                                                    GROUP BY z.CODE, z1.ZAIK, z2.ZAIK, z1.RACKNO
                                        ) x ON x.CODE = hk.CODE
                                        WHERE r.SORDER = '".$req->po."' AND hk.PORDER = '".$info->porder."'
                                        GROUP BY hk.CODE, 
                                                h.NAME, 
                                                i.VENDOR, 
                                                hk.KVOL, 
                                                i.DRAWING_NUM, 
                                                x.WHS100, 
                                                x.WHS102, 
                                                x.RACKNO,
                                                xp.SIYOU");


            foreach ($mk_details_data as $key => $row) {
                $w100 = $this->getHokanZaik($row->kcode,'WHS100');
                $w102 = $this->getHokanZaik($row->kcode,'WHS102');
                $non = $this->getHokanZaik($row->kcode,'WHS-NON');
                $sm = $this->getHokanZaik($row->kcode,'WHS-SM');
                $assy = $this->getHokanZaik($row->kcode,'ASSY102');
                array_push($whs100, $w100);
                array_push($whs102, $w102);
                array_push($whsnon, $non);
                array_push($whssm, $sm);
                array_push($assy102, $assy);
            }

            $data = [
                'date' => $date,
                'company_info' => $company_info,
                'info' => $info,
                'mk_details_data' => $mk_details_data,
                'kitqty' => $req->kitqty,
                'whs100' => $whs100,
                'whs102' => $whs102,
                'whsnon' => $whsnon,
                'whssm' => $whssm,
                'assy102' => $assy102,
            ];

            //return dd($data);

            $pdf = PDF::loadView('pdf.wbs_material_kitting', $data)
                        ->setPaper('A4')
                        ->setOption('margin-top', 10)->setOption('margin-bottom', 5)
                        ->setOrientation('landscape');

            return $pdf->inline('KittingList_'.$req->po);

        }
        
    }

    private function getHokanZaik($item,$hokan)
    {
    	$db = DB::connection($this->mssql)->table('XZAIK')
    				->select('ZAIK')
    				->where('CODE',$item)
    				->where('HOKAN',$hokan)
    				->first();
    	if ($this->com->checkIfExistObject($db) > 0) {
    		return intval($db->ZAIK);
    	} else {
    		return '0';
    	}
    }

    public function transferSlip(Request $req)
    {

        $id = trim($req['id']);
        $cur_id = '';
        $issuance_no = '';
        $max_id = '';

        $dt = Carbon::now();
        $date = substr($dt->format('  M j, Y A'), 2);
        $company_info = $this->com->getCompanyInfo();

        $mk_data = $this->getKitInfoByID($id);

        if(count((array)$mk_data) > 0)
        {
            $issuanceno = $mk_data->issuance_no;
            $pono       = $mk_data->po_no;
            $devicecode = $mk_data->device_code;
            $devicename = $mk_data->device_name;
            $poqty      = $mk_data->po_qty;
            $kitqty     = $mk_data->kit_qty;
            $kitno      = $mk_data->kit_no;
            $preparedby = $mk_data->prepared_by;
            $createdat  = $mk_data->created_at;
            $status     = $mk_data->status;
            $assessment = $mk_data->assessment;


            if ($status == 'O') {
                $status = 'Open';
            }

            if ($status == 'C') {
                $status = 'Cancelled';
            }

            if ($status == 'X') {
                $status = 'Closed';
            }
            

            $mk_details_data = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance AS I')
                            ->leftJoin('tbl_wbs_material_kitting_details as D', 'D.issue_no', '=', 'I.issue_no')
                            ->leftJoin('tbl_wbs_material_kitting as K', 'I.issue_no', '=', 'K.issuance_no')
                            ->where('I.issue_no',$issuanceno)
                            ->whereRaw('I.item = D.item')
                            ->select('I.item'
                                    , 'I.item_desc'
                                    ,'K.assessment'
                                    , DB::raw('FORMAT(D.usage,2) AS `usage`')
                                    , DB::raw('D.rqd_qty AS rqd_qty')
                                    , DB::raw('I.issued_qty AS issued_qty')
                                    //, DB::raw('SUM(I.issued_qty) AS issued_qty')
                                    , 'I.lot_no')
                            ->groupBy('I.item','I.item_desc','D.usage','D.rqd_qty','I.issued_qty','I.lot_no')
                            ->orderBy('D.id')
                            ->get();
        }
        else
        {
            $issuanceno = '';
            $pono       = '';
            $devicecode = '';
            $devicename = '';
            $poqty      = '';
            $kitqty     = '';
            $kitno      = '';
            $preparedby = '';
            $createdat  = '';
            $status     = '';
            $assessment = '';

            $mk_details_data = [];
        }

        $html = '<!DOCTYPE html>
                    <html>

                    <head>
                        <style type="text/css">
                            @page
                            {
                                margin-top: 0.0em;
                                margin-bottom: 0.0em;
                                margin-left: 0.0em;
                                margin-right: 0.0em;
                            }
                            @page :first
                            {
                                margin-top: 0px;
                            }
                            html {
                                height: 50%;
                            }
                            body
                            {
                                height: 650px;
                                margin: 3px, 3px, 3px, 3px;
                                padding: 0px;
                                min-height: 100vh;
                                border: solid 1px;
                            }
                            #headerA
                            {
                                position: fixed;
                                left: 20px; right: 20px; top: 0px;
                                text-align: center;
                                height: 90px;
                            }
                            #headerB
                            {
                                position: absolute;
                                left: -20px; right: -20px; top: -240px;
                                text-align: center;
                                width:100%;
                                height: 220px;
                            }
                            #footer
                            {
                                position: fixed;
                                left: 3px; right: 3px; bottom: 90px;
                                text-align: center;
                                height: 40px;
                            }
                            .pagenum:before
                            {
                                content: counter(page) " of " counter(pages);
                            }
                            .rTable {
                                display: table;
                                width: 100%;
                            }

                            .rTableRow {
                                display: table-row;
                            }

                            .rTableHeading {
                                background-color: #ddd;
                                display: table-header-group;
                            }

                            .rTableCell,
                            .rTableHead {
                                display: table-cell;
                                padding: 1px 10px;
                                border: 0px solid #999999;
                            }

                            .rTableCell-bordered,
                            .rTableHead-bordered {
                                display: table-cell;
                                padding: 3px 10px;
                                border: 1px solid black;
                            }

                            .rTableCell-half{
                                display: table-cell;
                                padding: 3px 10px;
                                border: 2px solid black;
                                width:50%;
                            }

                            .width-10 {
                                width:10%;
                            }
                            .width-20 {
                                width:20%;
                            }
                            .width-23 {
                                width:23%;
                            }
                            .width-25 {
                                width:25%;
                            }
                            .width-30 {
                                width:30%;
                            }
                            .width-32 {
                                width:32%;
                            }
                            .width-35 {
                                width:35%;
                            }
                            .width-40 {
                                width:40%;
                            }
                            .width-45 {
                                width:45%;
                            }
                            .width-50 {
                                width:50%;
                            }
                            .width-60 {
                                width:60%;
                            }
                            .width-70 {
                                width:70%;
                            }
                            .width-100 {
                                width:100%;
                            }

                            .height-30 {
                                height:500px;
                            }

                            .center{
                                text-align: center;
                            }

                            .right{
                                text-align: right;
                            }

                            .largeText{
                                font-size: 18px;
                            }

                            .large1Text{
                                font-size: 12px;
                            }

                            .mediumText{
                                font-size: 11px;
                            }

                            .smallText{
                                font-size: 8px;
                            }
                            .small1Text{
                                font-size: 7px;
                            }

                            .smallestText{
                                font-size: 5px;
                            }

                            .fontArial
                            {
                                font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
                            }

                            .rTableHeading {
                                display: table-header-group;
                                background-color: #ddd;
                                font-weight: bold;
                            }

                            .rTableFoot {
                                display: table-footer-group;
                                font-weight: bold;
                                background-color: #ddd;
                            }

                            .rTableBody {
                                display: table-row-group;
                            }
                            .rBorder-1 {
                                border: 1px solid black;
                            }
                            .rBorder-2 {
                                border: 3px solid black;
                            }
                            label {
                              display: block;
                              padding-left: 5px;
                              text-indent: -5px;
                            }
                            input {
                              width: 13px;
                              height: 13px;
                              padding: 0;
                              margin:0;
                              vertical-align: bottom;
                              position: relative;
                              top: -1px;
                              *overflow: hidden;
                            }
                        </style>
                    </head>
                        
                    <body>
                        <div class="rTable fontArial">
                            <div class="rTableBody">
                                <div class="rTableRow">
                                    <div class="rTableCell-bordered rBorder-2" style="height:650px; min-height:650px">
                                        <div class="rTable">
                                            <div class="rTableBody">
                                                <div class="rTableRow">
                                                    <div class="rTableCell large1Text width-70 ">
                                                        <strong><ins class="largeText">MATERIAL ISSUANCE SHEET</ins></strong>
                                                    </div>
                                                    <div class="rTableCell right width-30 mediumText">Warehouse<br/>COPY</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rTable small1Text">
                                            <div class="rTableBody">
                                                <div class="rTableRow">
                                                    <div class="rTableCell mediumText width-25 right">PO :</div>
                                                    <div class="rTableCell mediumText width-30 left"><strong>'. $pono .'</strong></div>
                                                    <div class="rTableCell mediumText width-45 right">Page: 1 of 1</div>
                                                </div>
                                                <div class="rTableRow">
                                                    <div class="rTableCell mediumText width-25 right">DEVICE NAME:</div>
                                                    <div class="rTableCell mediumText width-30 left"><strong>'. $devicename .'</strong></div>
                                                    <div class="rTableCell mediumText width-45">Transfer Slip #: <strong> '. $issuanceno .'</strong></div>
                                                </div>
                                                <div class="rTableRow">
                                                    <div class="rTableCell mediumText width-25 right">ORDER QTY.:</div>
                                                    <div class="rTableCell mediumText width-30 left">'. $poqty .'</div>

                                                     <div class="rTableCell mediumText width-45">Assessment#: <strong> '. $assessment .'</strong></div>

                                                </div>
                                                <div class="rTableRow">
                                                    <div class="rTableCell mediumText width-25 right">KIT QTY :</div>
                                                    <div class="rTableCell mediumText width-30 left">'. $kitqty .'</div>
                                                </div>
                                                <div class="rTableRow">
                                                    <div class="rTableCell mediumText width-25 right">KIT NUMBER :</div>
                                                    <div class="rTableCell mediumText width-30 left">'. $kitno .'</div>
                                                </div>
                                                <div class="rTableRow">
                                                    <div class="rTableCell mediumText width-25 right">PREPARED DT:</div>
                                                    <div class="rTableCell mediumText width-30 left">'. $createdat .'</div>
                                                    <div class="rTableCell mediumText width-45"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="rTable mediumText">
                                            <div class="rTableRow">
                                                <div class="rTableCell-bordered mediumText"><b>Item</b></div>
                                                <div class="rTableCell-bordered mediumText"><b>Part Name / Code</b></div>
                                                <div class="rTableCell-bordered mediumText"><b>USG</b></div>
                                                <div class="rTableCell-bordered mediumText"><b>RQD</b></div>
                                                <div class="rTableCell-bordered mediumText"><b>QTY</b></div>
                                                <div class="rTableCell-bordered mediumText"><b>LOT</b></div>
                                            </div>';
        $html2 = '';
        $sum1 = 0;
        foreach ($mk_details_data as $key => $value)
        {
                         $html2 = $html2 . ' <div class="rTableRow mediumText">
                                        <div class="rTableCell-bordered">'. $value->item .'</div>
                                        <div class="rTableCell-bordered width-30">'. $value->item_desc .'</div>
                                        <div class="rTableCell-bordered">'. $value->usage .'</div>
                                        <div class="rTableCell-bordered">'. $value->rqd_qty .'</div>
                                        <div class="rTableCell-bordered">'. $value->issued_qty .'</div>
                                        <div class="rTableCell-bordered">'. $value->lot_no .'</div>
                                    </div> ';
                        $sum1 = $sum1 + $value->issued_qty;
        }

        $html2.= ' </div>
                    <br>
                    <div class="rTable smallText">
                    <div class="rTableRow mediumText">
                        <div class="rTableCell mediumText">Prepared By:</div>
                        <div class="rTableCell mediumText width-30">'.$preparedby.'</div>
                        <div class="rTableCell mediumText"></div>
                        <div class="rTableCell large1Text">Total Issued Qty: <strong>'.$sum1.'</strong></div>
                        <div class="rTableCell mediumText"></div>
                        <div class="rTableCell mediumText"></div>
                    </div> ';
        $html2.= ' <div class="rTableRow mediumText">
                        <div class="rTableCell mediumText">Received By:</div>
                        <div class="rTableCell mediumText width-30">__________________</div>
                        <div class="rTableCell mediumText"></div>
                        <div class="rTableCell mediumText">Date: __________________</div>
                        <div class="rTableCell mediumText"></div>
                        <div class="rTableCell mediumText"></div>
                    </div> ';
        $html2.= ' <div class="rTableRow mediumText">
                        <div class="rTableCell mediumText">Transfer Slip:</div>
                        <div class="rTableCell mediumText width-30"><strong>'.$issuanceno.'</strong></div>
                        <div class="rTableCell mediumText"></div>
                        <div class="rTableCell mediumText"></div>
                        <div class="rTableCell mediumText"></div>
                        <div class="rTableCell mediumText"></div>
                    </div> ';

        $html3 =  ' </div>
                </div>
                <div class="rTableCell-bordered rBorder-2">
                    <div class="rTable">
                        <div class="rTableBody">
                            <div class="rTableRow">
                                <div class="rTableCell large1Text width-70">
                                    <strong><ins class="largeText">MATERIAL ISSUANCE SHEET</ins></strong>
                                </div>
                                <div class="rTableCell right width-30 mediumText">Production<br/>COPY</div>
                            </div>
                        </div>
                    </div>
                    <div class="rTable small1Text">
                        <div class="rTableBody">
                            <div class="rTableRow">
                                <div class="rTableCell mediumText width-25 right">PO :</div>
                                <div class="rTableCell mediumText width-30 left"><strong>'. $pono .'</strong></div>
                                <div class="rTableCell mediumText width-45 right">Page: 1 of 1</div>
                            </div>
                            <div class="rTableRow">
                                <div class="rTableCell mediumText width-25 right">DEVICE NAME:</div>
                                <div class="rTableCell mediumText width-30 left"><strong>'. $devicename .'</strong></div>
                                <div class="rTableCell mediumText width-45">Transfer Slip #: <strong> '. $issuanceno .'</strong></div>
                            </div>
                            <div class="rTableRow">
                                <div class="rTableCell mediumText width-25 right">ORDER QTY.:</div>
                                <div class="rTableCell mediumText width-30 left">'. $poqty .'</div>
                                <div class="rTableCell mediumText width-45">Assessment#: <strong> '. $assessment .'</strong></div>
                            </div>
                            <div class="rTableRow">
                                <div class="rTableCell mediumText width-25 right">KIT QTY :</div>
                                <div class="rTableCell mediumText width-30 left">'. $kitqty .'</div>
                            </div>
                            <div class="rTableRow">
                                <div class="rTableCell mediumText width-25 right">KIT NUMBER :</div>
                                <div class="rTableCell mediumText width-30 left">'. $kitno .'</div>
                            </div>
                            <div class="rTableRow">
                                <div class="rTableCell mediumText width-25 right">PREPARED DT:</div>
                                <div class="rTableCell mediumText width-30 left">'. $createdat .'</div>
                                <div class="rTableCell mediumText width-45"></div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="rTable smallText">
                        <div class="rTableRow">
                            <div class="rTableCell-bordered mediumText"><b>Item</b></div>
                            <div class="rTableCell-bordered mediumText"><b>Part Name / Code</b></div>
                            <div class="rTableCell-bordered mediumText"><b>USG</b></div>
                            <div class="rTableCell-bordered mediumText"><b>RQD</b></div>
                            <div class="rTableCell-bordered mediumText"><b>QTY</b></div>
                            <div class="rTableCell-bordered mediumText"><b>LOT</b></div>
                        </div>';
        $html4 ='';
        $sum2 = 0;
        foreach ($mk_details_data as $key => $value)
        {
            $html4  = $html4 . '<div class="rTableRow mediumText">
                                    <div class="rTableCell-bordered mediumText">'. $value->item .'</div>
                                    <div class="rTableCell-bordered mediumText width-30">'. $value->item_desc .'</div>
                                    <div class="rTableCell-bordered mediumText">'. $value->usage .'</div>
                                    <div class="rTableCell-bordered mediumText">'. $value->rqd_qty .'</div>
                                    <div class="rTableCell-bordered mediumText">'. $value->issued_qty .'</div>
                                    <div class="rTableCell-bordered mediumText">'. $value->lot_no .'</div>
                                </div>';
            $sum2 = $sum2 + $value->issued_qty;
        }

            $html4.= ' </div>
                        <br>
                        <div class="rTable smallText">
                        <div class="rTableRow mediumText">
                            <div class="rTableCell mediumText">Prepared By:</div>
                            <div class="rTableCell mediumText width-30">'.$preparedby.'</div>
                            <div class="rTableCell mediumText"></div>
                            <div class="rTableCell large1Text">Total Issued Qty: <strong>'.$sum2.'</strong></div>
                            <div class="rTableCell mediumText"></div>
                            <div class="rTableCell mediumText"></div>
                        </div> ';
            $html4.= ' <div class="rTableRow mediumText">
                            <div class="rTableCell mediumText">Received By:</div>
                            <div class="rTableCell mediumText width-30">__________________</div>
                            <div class="rTableCell mediumText"></div>
                            <div class="rTableCell mediumText">Date: __________________</div>
                            <div class="rTableCell mediumText"></div>
                            <div class="rTableCell mediumText"></div>
                        </div> ';
            $html4.= ' <div class="rTableRow mediumText">
                            <div class="rTableCell mediumText">Transfer Slip:</div>
                            <div class="rTableCell mediumText width-30"><strong>'.$issuanceno.'</strong></div>
                            <div class="rTableCell mediumText"></div>
                            <div class="rTableCell mediumText"></div>
                            <div class="rTableCell mediumText"></div>
                            <div class="rTableCell mediumText"></div>
                       </div> ';

                $html5 =  '</div>
                        </div>
                    </div>
                </div>
            </div>
        </body>

        </html> ';
        // echo $html;

        # gather all html parts.
        $html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . $html . $html2 . $html3. $html4. $html5;

        // $pdf = PDF::loadHTML($html)->setPaper('A4', 'landscape');

        // return $pdf->stream('Material_Issuance'.Carbon::now().'.pdf');

        # apply snappy pdf wrapper
        // $data = [
        //     'issuanceno' => $issuanceno,
        //     'pono' => $pono,
        //     'devicecode' => $devicecode,
        //     'devicename' => $devicename,
        //     'poqty' => $poqty,
        //     'kitqty' => $kitqty,
        //     'kitno' => $kitno,
        //     'preparedby' => $preparedby,
        //     'createdat' => $createdat,
        //     'status' => $status,
        //     'mk_details_data' => $mk_details_data
        // ];
        $pdf = App::make('snappy.pdf.wrapper');
        # transform html to pdf format.
        $pdf->loadHTML($html)
            ->setPaper('A4')
            ->setOption('margin-top', 2)
            ->setOption('margin-left', 1)
            ->setOption('margin-right', 2)
            ->setOption('margin-bottom', 2)
            ->setOrientation('portrait');
        # display PDF report to response.
        return $pdf->inline('Material_Issuance_Sheet_'.$issuanceno.'_'.$pono);

        // $pdf = PDF::loadView('pdf.material_issuance_sheet', $data)
        //             ->setPaper('A4')
        //             ->setOption('margin-top', 6)
        //             ->setOption('margin-left', 3)
        //             ->setOption('margin-right', 3)
        //             ->setOption('margin-bottom', 3)
        //             ->setOrientation('landscape');

        // return $pdf->inline('Material_Issuance_Sheet_'.$issuanceno.'_'.$pono);
    }

    public function postDeleteKitDetails(Request $req)
    {
        $data = [
            'msg' => "Deleting failed.",
            'status' => 'failed'
        ];

        $deleted = false;
        foreach ($req->ids as $key => $d) {
            $checkIssuance = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                                ->where('issue_no',$d['issue_no'])
                                ->where('item',$d['item'])
                                ->count();
            if ($checkIssuance < 1) {
                $deleted = DB::connection($this->mysql)->table('tbl_wbs_material_kitting_details')
                            ->where('id',$d['id'])
                            ->delete();
            } else {
                $data = [
                    'msg' => "Item ".$d['item']." already had an issuance, you cannot delete it.",
                    'status' => 'failed'
                ];
                return $data;
            }
            
        }

        if ($deleted) {
            $data = [
                'msg' => "Details were successfully deleted.",
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function postDeleteIssDetails(Request $req)
    {
        $data = [
            'msg' => "Deleting failed.",
            'status' => 'failed'
        ];

        $deleted = false;
        $unreg_id = [];
        $not_reg = 0;
        foreach ($req->ids as $key => $d) {
            $iss = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                        ->where('id',$d['id'])
                        ->first();

            if (!is_null($iss)) {
                if (is_null($iss->inv_id)) {
                    DB::connection($this->mysql)->table('tbl_wbs_inventory')
                        ->where('item',$iss->item)
                        ->where('lot_no',$iss->lot_no)
                        ->increment('qty', $iss->issued_qty);
                } else {
                    DB::connection($this->mysql)->table('tbl_wbs_inventory')
                        ->where('id',$iss->inv_id)
                        ->increment('qty', $iss->issued_qty);
                }

                $deleted = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                            ->where('id',$d['id'])
                            ->delete();
            } else {
                $deleted = true;
                $not_reg++;
                array_push($unreg_id, $d['detailid'] );
            }
        }

        if ($deleted) {
            $data = [
                'msg' => "Details were successfully deleted.",
                'status' => 'success',
                'detailid' => $unreg_id
            ];
        }

        //return dd($req->ids);

        return $data;
    }

    public function postCancelMatKit(Request $req)
    {
        $data = [
            'msg' => "Cancelling P.O. failed.",
            'status' => 'failed'
        ];

        $updated = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                        ->where('issuance_no',$req->issuanceno)
                        ->update([
                            'status' => 'C'
                        ]);
        if ($updated) {
            $data = [
                'msg' => "Issuance No. [".$req->issuanceno."] was successfully cancelled.",
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function getItemAndLotnumFifo(Request $req)
    {
        $location_cond = '';
        $lotno_cond ='';
        $item_cond ='';

        if(empty($req->lotno)) {
            $lotno_cond ='';
        } else {
            $lotno_cond = " AND i.lot_no = '" . $req->lotno . "'";
        }

        if(empty($req->item)) {
            $item_cond ='';
        } else {
            $item_cond = " AND i.item = '" . $req->item . "'";
        }

        $checklot = DB::connection($this->mysql)->table('tbl_wbs_inventory as i')
        				->whereRaw("1=1".$item_cond)->where('i.deleted',0)->count();

        if ($checklot > 0) {
        	$data = DB::connection($this->mysql)->table('tbl_wbs_inventory as i')
		                ->join('tbl_wbs_material_kitting_details as k','i.item','=','k.item')
		                // ->join('tbl_wbs_material_receiving_batch as r','i.invoice_no','=','r.invoice_no')
		                ->whereRaw("i.qty > 0 AND i.iqc_status IN ('1','4') AND k.issue_no='".$req->issuanceno."'".$lotno_cond.$item_cond) //i.item=r.item AND 
                        ->where('i.deleted',0)
		                ->select(DB::raw('i.id as id'),
                            DB::raw('i.item as item'),
                            DB::raw('i.item_desc as item_desc'),
                            // DB::raw('r.qty as received_qty'),
                            DB::raw('i.qty as qty'),
                            DB::raw("IFNULL(i.lot_no,'') as lot_no"),
                            DB::raw('i.location as location'),
                            DB::raw("DATE_FORMAT(i.received_date, '%Y-%m-%d') as receive_date"),
                           // DB::raw("DATE_FORMAT(i.created_at, '%Y-%m-%d') as created_at"),
                            DB::raw('k.kit_qty as kit_qty'))
                        ->distinct()
		                ->orderBy('i.received_date','asc')
                        ->orderBy('i.lot_no','asc') 
		                ->get();
        } else {
        	$data = DB::connection($this->mysql)->table('tbl_wbs_inventory as i')
		                ->join('tbl_wbs_material_kitting_details as k','i.item','=','k.item')
		                // ->join('tbl_wbs_material_receiving_batch as r','i.invoice_no','=','r.invoice_no')
		                ->whereRaw("i.qty > 0 AND i.iqc_status IN ('1','4') AND k.issue_no='".$req->issuanceno."'".$item_cond)//i.item=r.item AND 
                        ->where('i.deleted',0)
		                ->select(DB::raw('i.id as id'),
                            DB::raw('i.item as item'),
                            DB::raw('i.item_desc as item_desc'),
                            // DB::raw('r.qty as received_qty'),
                            DB::raw('i.qty as qty'),
                            DB::raw("IFNULL(i.lot_no,'') as lot_no"),
                            DB::raw('i.location as location'),
                            DB::raw("DATE_FORMAT(i.received_date, '%Y-%m-%d') as receive_date"),
                            DB::raw('k.kit_qty as kit_qty'))
                        ->distinct()
		                ->orderBy('i.received_date','asc')
                        ->orderBy('i.lot_no','asc') 
		                ->get();
        }
        
        return $data;
    }

    public function searchKitData(Request $req)
    {
        $ctr = 0;
        $value = null;
        $result = null;

        $pono_cond = '';
        $kitno_cond = '';
        $preparedby_cond = '';
        $slipno_cond = '';
        $status_cond = '';

        # Create PO No. Condition.
        if(empty($req->pono))
        {
            $pono_cond = '';
        }
        else
        {
            $pono_cond = " AND po_no = '" . $req->pono . "'";
        }

        # Create Kit No. Condition
        if(empty($req->kitno))
        {
            $kitno_cond ='';
        }
        else
        {
            $kitno_cond = " AND kit_no = '" . $req->kitno . "'";
        }

        # Create Prepared By Condition
        if(empty($req->preparedby))
        {
            $preparedby_cond ='';
        }
        else
        {
            $preparedby_cond = " AND prepared_by = '" . $req->preparedby . "'";
        }

        # Create Slip No. Condition
        if(empty($req->slipno))
        {
            $slipno_cond = '';
        }
        else
        {
            $slipno_cond = " AND issue_no = '" . $req->slipno . "'";
        }

        # Create Status Condition
        if(empty($req->pono) || empty($req->pono) || empty($req->kitno) || empty($req->preparedby) || empty($req->slipno)) {
            if (isset($req->status) && count($req->status) > 0) {
                $status_cond = " AND `status` IN(";
                foreach ($req->status as $key => $status) {
                    $status_cond .= "'".$status."',";
                }
                $status_cond = substr($status_cond, 0,-1);
                $status_cond .= ")";
            } else {
                $status_cond = "";
            }
        }

        # Retrieve Data using the generated conditions.
        $data = DB::connection($this->mysql)->table('tbl_wbs_material_kitting')
                    ->whereRaw(" 1=1 "
                        . $pono_cond
                        . $kitno_cond
                        . $preparedby_cond
                        . $slipno_cond
                        . $status_cond)
                    ->get();
        return $data;
    }

    public function fifoReason(Request $req)
    {
        $user = $req->user;
       
        $data = [
                'status' => 'failed',
            ];
        $insert = DB::connection($this->mysql)->table('tbl_wbs_fiforeason')
                    ->insert([
                        'item' => $req->item,
                        'lotno' => $req->lotno,
                        'issuanceno' => $req->issuanceno,
                        'reason' => $req->reason,
                        'reason_id' =>$req->reason_id,
                        'created_id' => Auth::user()->user_id,
                        'created_at' => Carbon::now(),
                        'approved_id' => $req->user,
                        'approved_date' =>Carbon::now(),
                        'created_date' => Carbon::now()
                        ]);

        if ($insert) {
            $data = [
                'status' => 'success',
            ];
        }

        return $data;
    }

    public function printBarCode(Request $req)
    {
        $data = DB::connection($this->mysql)
                    ->table('tbl_wbs_kit_issuance')
                    ->where('issue_no',$req->issuanceno)
                    ->where('detailid',$req->id)
                    ->first();
        if ($this->com->checkIfExistObject($data) > 0) {
            $path = storage_path().'/brcodekitting';
                            if (!File::exists($path)) {
                                File::makeDirectory($path,755, true, true);
                            }
            $filename = $data->issue_no.'_'.$data->po.'_'.$data->item.'.prn';

            $content = 'CLIP ON'."\r\n";
            $content .= 'CLIP BARCODE ON'."\r\n";
            $content .= 'DIR2'."\r\n";
            $content .= 'PP310,766:AN7'."\r\n";
            $content .= 'DIR2'."\r\n";
            $content .= 'FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 10'."\r\n";
            $content .= 'PP60,776:FT "Swiss 721 Bold BT",20,0,78'."\r\n";
            $content .= 'PP290,450:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 8'."\r\n";
            $content .= 'PT "'.$data->create_user.'"'."\r\n";
            $content .= 'PP290,200:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 8'."\r\n";
            $content .= 'PT "'.$data->created_at.'"'."\r\n";
            $content .= 'PP260,480:BARSET "CODE128",2,1,3,30'."\r\n";
            $content .= 'PB "'.$data->po.'"'."\r\n";
            $content .= 'PP220,350:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 6'."\r\n";
            $content .= 'PT "'.$data->po.'"'."\r\n";
            $content .= 'PP200,520:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 6'."\r\n";
            $content .= 'PT "Qty.:"'."\r\n";
            $content .= 'PP200,440:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 8'."\r\n";
            $content .= 'PT "'.$data->issued_qty.'"'."\r\n";
            $content .= 'PP200,360:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 8'."\r\n";
            $content .= 'PT "pc(s)"'."\r\n";
            $content .= 'PP160,400:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 6'."\r\n";
            $content .= 'PT "LOT:"'."\r\n";
            $content .= 'PP160,480:BARSET "CODE128",2,1,3,30'."\r\n";
            $content .= 'PB "'.$data->lot_no.'"'."\r\n";
            $content .= 'PP120,350:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 6'."\r\n";
            $content .= 'PT "'.$data->lot_no.'"'."\r\n";
            $content .= 'PP100,350:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 6'."\r\n";
            $content .= 'PT "'.$data->item_desc.'"'."\r\n";
            $content .= 'PP80,480:BARSET "CODE128",2,1,3,30'."\r\n";
            $content .= 'PB "'.$data->item.'"'."\r\n";
            $content .= 'PP40,350:FT "Swiss 721 BT"'."\r\n";
            $content .= 'FONTSIZE 6'."\r\n";
            $content .= 'PT "'.$data->item.'"'."\r\n";
            $content .= 'PP150,779:AN7'."\r\n";
            $content .= 'PF'."\r\n";


            $myfile = fopen($path."/".$filename, "w") or die("Unable to open file!");
            fwrite($myfile, $content);
            fclose($myfile);

            $headers = [
                            'Content-type'=>'text/plain', 
                            'Content-Disposition'=>sprintf('attachment; filename="%s"', $filename)
                        ];
        
            return \Response::download($path.'/'.$filename, $filename, $headers);
        } 
    }

    public function fifoReasonExcel(Request $req)
    {
        $dt = Carbon::now();
        $date = substr($dt->format('Ymd'), 2);
        $issuanceno = $req->issuanceno;
        
        Excel::create('FIFO_REASONS_'.$req->issuanceno.'_'.$date, function($excel) use($issuanceno)
        {
            $excel->sheet('Sheet1', function($sheet) use($issuanceno)
            {
                $sheet->cell('A1', "ISSUANCE NO");
                $sheet->cell('B1', "ITEM");
                $sheet->cell('C1', "LOTNO");
                $sheet->cell('D1', "REASON");
                $sheet->cell('E1', "REASON_ID");
                $sheet->cell('F1', "DATE");

                $row = 2;
                $data = DB::connection($this->mysql)->table('tbl_wbs_fiforeason')
                            ->where('issuanceno',$issuanceno)
                            ->get();

                foreach ($data as $key => $mk) {
                    $sheet->cell('A'.$row, $mk->issuanceno);
                    $sheet->cell('B'.$row, $mk->item);
                    $sheet->cell('C'.$row, $mk->lotno);
                    $sheet->cell('D'.$row, $mk->reason);
                    $sheet->cell('E'.$row, $mk->reason_id);
                    $sheet->cell('F'.$row, $mk->created_at);
                    $row++;
                }
            });

        })->export('xls');
    }

    private function checkIfPOisClosed($po)
    {
        $status = 'O';
        $ypics = DB::connection($this->mssql)
                    ->table('XSLIP as s')
                    ->leftJoin('XHEAD as h', 's.CODE', '=', 'h.CODE')
                    ->leftjoin('XRECE as r', 's.SEIBAN','=','r.SORDER')
                    ->select(DB::raw('s.CODE as code'),
                            DB::raw('h.NAME as prodname'),
                            DB::raw('r.KVOL as POqty'),
                            DB::raw('s.PORDER as porder'),
                            DB::raw('r.SEDA as branch'))
                    ->where('s.SEIBAN',$po)
                    ->orderBy('r.SEDA','desc')
                    ->first();

                  
        $iss = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                    ->where('po',$po)
                    ->select(DB::raw("SUM(issued_qty) as issued_qty"))
                    ->first();
        //return $iss->issued_qty;
        if ($ypics->POqty === $iss->issued_qty) {
            $status = 'X';
        }
        return $status;
    }

    public function CheckAuthorizedUser(Request $req){
        $user_id = $req->user_id;
        $actual_password = $req->actual_password;
        $check_user =  Auth::user()->user_id;
              
        $user = DB::connection($this->common)
                ->select("SELECT * FROM users WHERE
                         user_id = '".$user_id."' AND actual_password = '".$actual_password."'
                         ");
        return $user;  
    }
}
