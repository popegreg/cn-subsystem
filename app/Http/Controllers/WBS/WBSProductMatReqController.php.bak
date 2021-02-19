<?php

namespace App\Http\Controllers\WBS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use App\RequestSummary;
use App\RequestDetail;
use DB;
use Config;
use Carbon\Carbon;
use Dompdf\Dompdf;
use PDF;
use Event;
use App\Events\CheckProdRequest;
use App\Events\WHSCheckRequest;


class WBSProductMatReqController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;
    
    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'wbs');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getProdMatRequest(Request $request_data)
    {
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PRDMATREQ'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $pmr_data = [];
            $pmr_details_data = [];
            $ismax = false;
            $max_id = 0;
            $cur_id = 0;

            $nextno = '';

            # Get parameters.
            $getrecid = trim($request_data['recid']);
            $getaction = trim($request_data['action']);


            if(empty($getaction) || $getaction == 'VIEW')
            {
                $id = trim($request_data['id']);
                $getpage = trim($request_data['page']);
                $action = 'VIEW';
                $batchUpdateFlag = null;

                if(empty($id))
                {
                    $getpage = 'MAX';
                }

                # Retreive Parts Receive Invoice Data
                $pmr_data = $this->getPmrData($getpage, $id, $cur_id, $po_no, $trans_no, $max_id);
                # Get the Record count of Parts Receive Invoice Data
                $pmr_data_cnt = DB::connection($this->mysql)->table('tbl_request_summary')->count();

                if($pmr_data_cnt > 0)
                {
                    # Retreive Details Data
                    $pmr_details_data = DB::connection($this->mysql)->table('tbl_request_detail')
                                    ->select('id', 'transno', 'detailid'
                                        , 'code', 'name'
                                        , 'classification'
                                        , DB::raw('FORMAT(issuedqty, 4) AS issuedqty')
                                        , DB::raw('FORMAT(requestqty, 4) AS requestqty')
                                        , DB::raw('FORMAT(servedqty, 4) AS servedqty')
                                        , 'location'
                                        , 'lot_no'
                                        , 'last_served_by'
                                        , 'last_served_date'
                                        , 'remarks'
                                        , 'requestedby')
                                    ->where('transno', '=', $trans_no)
                                    ->get();
                }

                $ismax = false;
            }
            else
            {
                $action = $getaction;
                $reqno = DB::connection($this->mysql)->table('tbl_request_summary')
                            ->select(DB::raw("CONCAT('PMR', LPAD(IFNULL(MAX(id), 0) + 1, 7, '0')) AS reqno"))
                            ->get();

                $nextno = $reqno[0]->reqno;

            }

            if($max_id == $cur_id)
            {
                $ismax = true;
            }

            $common = new CommonController();
            $line = $common->getDropdownByName('linedestination');
            $prod = $common->getDropdownByName('productdestination');
            $class = $common->getDropdownByName('classification');

            return view('wbs.productmaterialrequest',[
                'userProgramAccess' => $userProgramAccess,
                'reqno' => $nextno,
                'line' => $line,
                'prod' => $prod,
                'class' => $class,
                'ismax' => $ismax,
                'action' => $action,
                'pmr_data' => $pmr_data,
                'pmr_details_data' => $pmr_details_data
            ]);
        }
    }

    public function postSearchPO(Request $request)
    {
        $this->truncateTable('temp_wbs_prodmatrequest');

        $info = DB::connection($this->mssql)
                        ->table('XSLIP as s')
                        ->leftJoin('XHEAD as h', 's.CODE', '=', 'h.CODE')
                        ->leftjoin('XRECE as r', 's.SEIBAN','=','r.SORDER')
                        ->select(DB::raw('s.CODE as code'),
                                DB::raw('h.NAME as prodname'),
                                DB::raw('r.KVOL as POqty'),
                                DB::raw('s.PORDER as porder'))
                        ->where('s.SEIBAN',$request->po)
                        ->orderBy('s.PORDER','desc')
                        ->first();

        $db = DB::connection($this->mssql)
                ->select("SELECT r.SORDER as po,
                                hk.CODE as code, 
                                h.NAME as name, 
                                hk.KVOL as rqdqty, 
                                x.RACKNO as location, 
                                i.DRAWING_NUM as drawnum, 
                                i.VENDOR as supplier, 
                                x.WHS100 as whs100, 
                                x.WHS102 as whs102
                        FROM XRECE r
                        LEFT JOIN XSLIP s ON r.SORDER = s.SEIBAN
                        LEFT JOIN XHIKI hk ON s.PORDER = hk.PORDER
                        LEFT JOIN XITEM i ON i.CODE = hk.CODE
                        LEFT JOIN XHEAD h ON h.CODE = hk.CODE
                        LEFT JOIN (SELECT z.CODE, 
                                        ISNULL(z1.ZAIK,0) as WHS100, 
                                        ISNULL(z2.ZAIK,0) as WHS102, 
                                        z1.RACKNO FROM XZAIK z
                                   LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
                                   LEFT JOIN XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS102'
                                   WHERE z.RACKNO <> ''
                                   GROUP BY z.CODE, z1.ZAIK, z2.ZAIK, z1.RACKNO
                        ) x ON x.CODE = hk.CODE
                        WHERE r.SORDER = '".$request->po."' AND s.PORDER = '".$info->porder."'
                        GROUP BY r.SORDER,
                                hk.CODE, 
                                h.NAME, 
                                i.VENDOR, 
                                hk.KVOL, 
                                i.DRAWING_NUM, 
                                x.WHS100, 
                                x.WHS102, 
                                x.RACKNO");
                // ->table('XRECE as r')
                // ->leftJoin('XSLIP as s','r.SORDER','=','s.SEIBAN')
                // ->leftJoin('XHIKI as hk','s.PORDER','=','hk.PORDER')
                // ->leftJoin('XHEAD as h','hk.CODE','=','h.CODE')
                // ->leftJoin('XZAIK as z','hk.CODE','=','z.CODE')
                // ->where('r.SORDER', '=', $request->po)
                // ->where('z.RACKNO', '<>', '')
                // ->select(DB::raw("r.SORDER as po"),
                //         DB::raw("hk.CODE as code"),
                //         DB::raw("h.NAME as name"),
                //         DB::raw("z.RACKNO as location"))
                // ->groupBy('r.SORDER','hk.CODE','h.NAME','z.RACKNO ')
                // ->get();

        foreach ($db as $key => $val) {
            $this->checkIfInSakiAndKit($val);
        }

        $data = DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')->get();
        return $data;

    }

    private function checkIfInSakiAndKit($prod)
    {
        $kit = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                    ->where('po',$prod->po)
                    ->where('item',$prod->code)
                    ->select('lot_no as lotno', DB::raw("issued_qty as issuedqty"))
                    ->get();

         $saki = DB::connection($this->mysql)->table('tbl_wbs_sakidashi_issuance as s')
                    ->join('tbl_wbs_sakidashi_issuance_item as i', 's.issuance_no','=','i.issuance_no')
                    ->where('s.po_no',$prod->po)
                    ->where('item',$prod->code)
                    ->select('lot_no as lotno', 'issued_qty as issuedqty')
                    ->get();

        if ($this->checkIfExistObject($kit) > 0) {
            foreach ($kit as $key => $x) {
                DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')->insert([
                    'po' => $prod->po,
                    'code' => $prod->code,
                    'name' => $prod->name,
                    'issuedqty' => (isset($x->issuedqty)) ? $x->issuedqty : "0.0000",
                    'lot_no' => (isset($x->lotno)) ? $x->lotno : "",
                    'location' => $prod->location
                ]);
            }
        }

        if ($this->checkIfExistObject($saki) > 0) {
            foreach ($saki as $key => $x) {
                DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')->insert([
                    'po' => $prod->po,
                    'code' => $prod->code,
                    'name' => $prod->name,
                    'issuedqty' => (isset($x->issuedqty)) ? $x->issuedqty : "0.0000",
                    'lot_no' => (isset($x->lotno)) ? $x->lotno : "",
                    'location' => $prod->location
                ]);
            }
        }

        if ($this->checkIfExistObject($saki) < 1 && $this->checkIfExistObject($kit) < 1) {
            DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')->insert([
                'po' => $prod->po,
                'code' => $prod->code,
                'name' => $prod->name,
                'issuedqty' => "0.0000",
                'lot_no' => "",
                'location' => $prod->location
            ]);
        }
    }

    private function checkIfExistObject($object)
    {
       return count( (array)$object);
    }

    public function postSaveDetail(Request $request)
    {
        $db = DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')
            ->where('po', '=', $request->po)
            ->select('id as detailid',
                    'po',
                    'code',
                    'name',
                    'lot_no as lotno',
                    'classification',
                    'issuedqty',
                    'requestqty',
                    'location')
            ->get();
        return $db;
    }

    private function getLocation($code)
    {
        $location = DB::connection($this->mssql)
                            ->table('XZAIK')
                            ->where('CODE',$code)
                            ->select('RACKNO')
                            ->get();
        return $location;
    }

    /**
    * Collate related arrays into 1 array.
    */
    private function mergeArray($item_arr)
    {
        $arr_sum = null;
        $cnt = 0;
        foreach ($item_arr as $itemkey => $item)
        {
            $ctr = 0;
            foreach ($item as $valuekey => $value)
            {
                $arr_sum[$ctr][$cnt] = $value;
                $ctr++;
            }
            $cnt++;
        }

        return $arr_sum;
    }

    public function postSaveRequest(Request $request_data)
    {
        $data = $request_data['pm_arr'];
        $details_arr = $request_data['detail_arr'];

        $result = 0;

        try
        {
            if(is_null($details_arr))
            {
                $pm_batch_data = null;
            }
            else
            {
                $pm_batch_data = $this->mergeArray($details_arr);
            }
            // return $pm_batch_data;

            $result = $this->updatePmr($data, $pm_batch_data);
            $result = 0;
            Event::fire(new WHSCheckRequest($this->mysql));
            //Event::fire(new CheckProdRequest($this->mysql));
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Update Parts Receive, Details, Summary and Batch Details.
    **/
    private function updatePmr($pm_data, $pm_batch_data)
    {
        $result=false;
        $variancesum   = 0;

        #array index of inventory data
        $idx_reqnopmr       = 0;
        $idx_ponopmr        = 1;
        $idx_prodes         = 2;
        $idx_linedes        = 3;
        $idx_remarks        = 4;
        $idx_statuspmr      = 5;
        $idx_createdbypmr   = 6;
        $idx_createddatepmr = 7;
        $idx_updatedbypmr   = 8;
        $idx_updateddatepmr = 9;

        #array index of inventory details data
        $idx_detailidtd        = 0;
        $idx_codetd            = 1;
        $idx_nametd            = 2;
        $idx_classsificationtd = 3;
        $idx_issuedqtytd       = 4;
        $idx_requestqtytd      = 5;
        $idx_servedqtytd       = 6;
        $idx_locationtd        = 7;
        $idx_lotnotd           = 8;
        $idx_lastservedbytd    = 9;
        $idx_lastserveddatetd  = 10;
        $idx_itemremarkstd     = 11;

        try
        {

            if(empty($pm_data[$idx_statuspmr]))
            {
                $pm_data[$idx_statuspmr] = 'Alert';
            }

            if(empty($pm_data[$idx_reqnopmr]))
            {

                $common = new CommonController();
                
                // $nextTransNo = $common->getWbsNextCode('PRD_REQ');
                // $transno_no = $nextTransNo['new_code'];
                $status = 'Alert';

                $transno_no = $common->getTransCode('PRD_REQ');
                $whstransNo = $common->getTransCode('WAR_ISS');

                DB::connection($this->mysql)->table('tbl_request_summary')
                    ->insert([
                        'transno'     => $transno_no,
                        'whstransno'  => $whstransNo,
                        'pono'        => $pm_data[$idx_ponopmr],
                        'destination' => $pm_data[$idx_prodes],
                        'line'        => $pm_data[$idx_linedes],
                        'status'      => $pm_data[$idx_statuspmr],
                        'requestedby' => Auth::user()->user_id,
                        'createdby' => Auth::user()->user_id,
                        'updatedby'   => Auth::user()->user_id,
                        'created_at'  => date("Y-m-d H:i:s"),
                        'updated_at'  => date("Y-m-d H:i:s"),
                        'requested_at'  => date("Y-m-d"),
                    ]);

                foreach ($pm_batch_data as $key => $detail)
                {
                    DB::connection($this->mysql)->table('tbl_request_detail')
                    ->insert([
                        'transno'     => $transno_no,
                        'whstransno'     => $whstransNo,
                        'detailid'       => $detail[$idx_detailidtd],
                        'code'           => $detail[$idx_codetd],
                        'name'           => $detail[$idx_nametd],
                        'classification' => $detail[$idx_classsificationtd],
                        'issuedqty'      => $detail[$idx_issuedqtytd],
                        'requestqty'     => $detail[$idx_requestqtytd],
                        'location'       => $detail[$idx_locationtd],
                        'lot_no'         => $detail[$idx_lotnotd],
                        'remarks'        => $detail[$idx_itemremarkstd],
                        'request_date' => date('Y-m-d'),
                        'requestedby'    => Auth::user()->user_id,
                        'created_at'     => date("Y/m/d H:i:sa"),
                        'updated_at'     => date("Y/m/d H:i:sa")
                    ]);
                }
            }
            else
            {
                $transno_no = $pm_data[$idx_reqnopmr];

                DB::connection($this->mysql)->table('tbl_request_summary')
                    ->where('transno', '=', $transno_no)
                    ->update([
                        'pono'        => $pm_data[$idx_ponopmr],
                        'destination' => $pm_data[$idx_prodes],
                        'line'        => $pm_data[$idx_linedes],
                        'status'      => $pm_data[$idx_statuspmr],
                        'requestedby' => Auth::user()->user_id,
                        'updatedby'   => Auth::user()->user_id,
                        'requested_at'  => date("Y-m-d"),
                        'updated_at'  => date("Y-m-d H:i:s")
                    ]);

                foreach ($pm_batch_data as $key => $detail)
                {
                    DB::connection($this->mysql)->table('tbl_request_detail')
                        ->where('id',$detail[$idx_detailidtd])
                        ->update([
                            'code'           => $detail[$idx_codetd],
                            'name'           => $detail[$idx_nametd],
                            'classification' => $detail[$idx_classsificationtd],
                            'issuedqty'      => $detail[$idx_issuedqtytd],
                            'requestqty'     => $detail[$idx_requestqtytd],
                            'location'       => $detail[$idx_locationtd],
                            'lot_no'         => $detail[$idx_lotnotd],
                            'remarks'        => $detail[$idx_itemremarkstd],
                            'request_date' => date('Y-m-d'),
                            'requestedby'    => Auth::user()->user_id,
                            'updated_at'     => date("Y/m/d H:i:sa")
                        ]);
                }

            }
            //$whstrans = DB::connection($this->mysql)->table('tbl_request_summary')->where('transno',$transno_no)->select('whstransno')->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function postSaveRequest2(Request $req)
    {
        $rs = new RequestSummary();
        $rd = new RequestDetail();
        $recid = $req->recid;

        /*$db = DB::connection($this->mysql)->table('tbl_request_summary')
                ->where('pono',$req->editpono)->count();*/



        $now = Carbon::now();
        if(empty($req->hdnreqnopmr))
        {
            $common = new CommonController();
            $nextTransNo = $common->getWbsNextCode('PRD_REQ');
            $transno_no = $nextTransNo['new_code'];
            $status = 'Alert';

            $rs->transno = $transno_no;
            $rs->pono = $req->ponopmr;
            $rs->destination = $req->selectedprodes;
            $rs->line = $req->selectedlinedes;
            $rs->status =  $status;
            $rs->requestedby = Auth::user()->user_id;
            $rs->createdby = Auth::user()->user_id;
            $rs->updatedby = Auth::user()->user_id;

            $rs->save();

            $recid = $rs->id;
        }
        else
        {

            $transno_no = $req->hdnreqnopmr;
            $status = $req->statuspmr;

            DB::connection($this->mysql)->table('tbl_request_summary')
                ->where('id', '=', $req->recid)
                ->update([
                    'transno'     => $transno_no,
                    'pono'        => $req->ponopmr,
                    'destination' => $req->selectedprodes,
                    'line'        => $req->selectedlinedes,
                    'status'      => $status,
                    'requestedby' => $req->updatedbypmr,
                    'updatedby'   => $req->updatedbypmr,
                    'created_at'  => $now,
                    'updated_at'  => $now
                ]);

            DB::connection($this->mysql)->table('tbl_request_detail')->where('transno', '=', $transno_no)->delete();
        }
            foreach ($req->editDetailid as $key => $detailid)
            {
                DB::connection($this->mysql)->table('tbl_request_detail')->insert([
                    'transno' => $transno_no,
                    'detailid' => $detailid,
                    'code' => $req['editcode'][$key],
                    'name' => $req['editdesc'][$key],
                    'classification' => $req['editclassification'][$key],
                    'issuedqty' => $req['editIssuedqty'][$key],
                    'requestqty' => $req['editRequestqty'][$key],
                    'location' => $req['editLocation'][$key],
                    'lot_no' => $req['editLotNo'][$key],
                    'remarks' => $req['editRemarks'][$key],
                    'requestedby' => Auth::user()->user_id,
                    'request_date' => date('Y-m-d'),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }

            Event::fire(new WHSCheckRequest($this->mysql));

            //Event::fire(new CheckProdRequest($this->mysql));


        if(empty($recid))
        {
        }

        $msg = "Detail is successfully saved.";
        return redirect('/wbsprodmatrequest?page=CUR&id=' . $recid)->with(['msg' => $msg]);
    }

    private function truncateTable($table)
    {
        return DB::connection($this->mysql)->table($table)->truncate();
    }

    private function getLastID()
    {
        $db = DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')
                ->select('id')
                ->orderBy('id','desc')
                ->skip(0)->take(1)->get();
        if (empty($db)) {
            $db = 0;
            return $db;
        } else {
            return $db[0]->id;
        }
    }

    /**
    * Retreive Parts Receiving Invoice Data.
    **/
    private function getPmrData($getpage, &$id, &$cur_id, &$po_no, &$trans_no, &$max_id)
    {
        $result = null;

        try
        {
            switch ($getpage)
            {
                # Go to First Page.
                case 'MIN':
                    $pmr_data = DB::connection($this->mysql)->table('tbl_request_summary')
                       ->select('id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'requestedby'
                            , 'lastservedby'
                            , DB::raw("DATE_FORMAT(lastserveddate, '%m/%d/%Y') as lastserveddate")
                            , 'createdby'
                            , DB::raw("DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') as created_at")
                            , 'updatedby'
                            , DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at"))
                       ->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MIN(id)"))
                              ->from('tbl_request_summary');
                          })
                        ->get();
                    break;

                # Go to Last Page.
                case 'MAX':
                    $pmr_data = DB::connection($this->mysql)->table('tbl_request_summary')
                       ->select('id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'requestedby'
                            , 'lastservedby'
                            , DB::raw("DATE_FORMAT(lastserveddate, '%m/%d/%Y') as lastserveddate")
                            , 'createdby'
                            , DB::raw("DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') as created_at")
                            , 'updatedby'
                            , DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at"))
                       ->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MAX(id)"))
                              ->from('tbl_request_summary');
                          })
                        ->get();
                    break;

                # Go to Previous Page.
                case 'PRV':
                    $pmr_data = DB::connection($this->mysql)->table('tbl_request_summary')
                       ->select('id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'requestedby'
                            , 'lastservedby'
                            , DB::raw("DATE_FORMAT(lastserveddate, '%m/%d/%Y') as lastserveddate")
                            , 'createdby'
                            , DB::raw("DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') as created_at")
                            , 'updatedby'
                            , DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at"))
                        ->where("id", "<", $id)
                        ->orderBy("id", "DESC")
                        ->skip(0)->take(1)
                        ->get();
                    break;

                # Go to Next Page.
                case 'NXT':
                    $pmr_data = DB::connection($this->mysql)->table('tbl_request_summary')
                       ->select('id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'requestedby'
                            , 'lastservedby'
                            , DB::raw("DATE_FORMAT(lastserveddate, '%m/%d/%Y') as lastserveddate")
                            , 'createdby'
                            , DB::raw("DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') as created_at")
                            , 'updatedby'
                            , DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at"))
                        ->where("id", ">", $id)
                        ->orderBy("id")
                        ->skip(0)->take(1)->get();
                    break;

                # Go to Specified MATCode.
                case 'PMR':
                    $pmr_data = DB::connection($this->mysql)->table('tbl_request_summary')
                       ->select('id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'requestedby'
                            , 'lastservedby'
                            , DB::raw("DATE_FORMAT(lastserveddate, '%m/%d/%Y') as lastserveddate")
                            , 'createdby'
                            , DB::raw("DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') as created_at")
                            , 'updatedby'
                            , DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at"))
                        ->where("transno", "=", $id)
                        ->orderBy("id")
                        ->skip(0)->take(1)->get();
                    break;

                # Go to Specified Page.
                default:
                    $pmr_data = DB::connection($this->mysql)->table('tbl_request_summary')
                       ->select('id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'requestedby'
                            , 'lastservedby'
                            , DB::raw("DATE_FORMAT(lastserveddate, '%m/%d/%Y') as lastserveddate")
                            , 'createdby'
                            , DB::raw("DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') as created_at")
                            , 'updatedby'
                            , DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at"))
                       ->where("id", "=", $id)
                        ->get();
                    break;
            }

            # retrieve the inventoryno and id for Details data loading.
            foreach ($pmr_data as $key => $value)
            {
                $value = get_object_vars($value);
                $trans_no = $value['transno'];
                $po_no = $value['pono'];
                $cur_id = $value['id'];
                break;
            }

            # Get the ID of the last record.
            $max_id = DB::connection($this->mysql)->table('tbl_request_summary')->select(DB::raw("MAX(id) as id"))->get();

            # retrieve the max id.
            foreach ($max_id as $key => $value)
            {
                $value = get_object_vars($value);
                $max_id = $value['id'];
                break;
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
        return $pmr_data;
    }

    public function postCancelPmr(Request $request_data)
    {
        $result = 0;
        try
        {
            $recid = $request_data['id'];
            $result = DB::connection($this->mysql)->table('tbl_request_summary')
                    ->where('id', $recid)
                    ->update([
                        'status'       => 'Cancelled'
                        ,'updatedby' => Auth::user()->user_id
                        ,'updated_at'  => date("Y/m/d H:i:sa")]);

            $message = "Selected transaction successfully cancelled.";
            $output = redirect(url('/wbsprodmatrequest?page=CUR&id='. $recid))
                        ->with(['message' => $message]);
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
        return $output;
    }

    public function postSearchPmr(Request $request_data)
    {

        $condition = $request_data['condition_arr'];

        $ctr = 0;
        $value = null;
        $result = null;

        $idx_srch_pono  = 0;
        $idx_srch_prodes  = 1;
        $idx_srch_linedes    = 2;
        $idx_srch_open = 3;
        $idx_srch_closed = 4;
        $idx_srch_cancelled = 5;
        $idx_req_from = 6;
        $idx_req_to = 7;

        $pono_cond = '';
        $prodes_cond = '';
        $linedes_cond = '';
        $status_cond = '';
        $date_cond = '';

        try
        {

            # Create Location. Condition
            if(empty($condition[$idx_srch_pono]))
            {
                $pono_cond ='';
            }
            else
            {
                $pono_cond = " AND pono like '" . $condition[$idx_srch_pono] . "'";
            }

            # Create Product Destination Condition
            if(empty($condition[$idx_srch_prodes]) || $condition[$idx_srch_prodes] == '-1')
            {
                $prodes_cond = '';
            }
            else
            {
                $prodes_cond = " AND destination = '" . $condition[$idx_srch_prodes] . "'";
            }

            # Create Pallet No. Condition
            if(empty($condition[$idx_srch_linedes]) || $condition[$idx_srch_linedes] == '-1')
            {
                $linedes_cond = '';
            }
            else
            {
                $linedes_cond = " AND line = '" . $condition[$idx_srch_linedes] . "'";
            }

            if(empty($condition[$idx_req_from]) || $condition[$idx_req_from] == '-1' || empty($condition[$idx_req_to]) || $condition[$idx_req_to] == '-1')
            {
                $date_cond = '';
            }
            else
            {
                $date_cond = "AND requested_at BETWEEN '" . $condition[$idx_req_from] . "' AND '" . $condition[$idx_req_to]. "'";
            }

            # Create Status Condition
            if($condition[$idx_srch_open] > 0 || $condition[$idx_srch_closed] > 0 || $condition[$idx_srch_cancelled] > 0)
            {
                if($condition[$idx_srch_open] == 1)
                {
                    $open = "'Alert'";
                }
                else
                {
                    $open = "''";
                }

                if($condition[$idx_srch_closed] == 1)
                {
                    $close = "'Closed'";
                }
                else
                {
                    $close = "''";
                }

                if($condition[$idx_srch_cancelled] == 1)
                {
                    $cancelled = "'Cancelled'";
                }
                else
                {
                    $cancelled = "''";
                }

                $status_cond = " AND `status` IN (". $open .", ". $close .",". $cancelled.")";
            }

            $common = new CommonController;
            # Retrieve Data using the generated conditions.
            $pmr_details_data = DB::connection($this->mysql)->table('tbl_request_summary')
                        ->select( 'id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'createdby'
                            , DB::raw("(CASE created_at
                                WHEN '0000-00-00' THEN NULL
                                ELSE DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p')
                               END) AS created_at")
                            , 'updatedby'
                            , DB::raw("(CASE updated_at
                                WHEN '0000-00-00' THEN NULL
                                ELSE DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p')
                               END) AS updated_at"))
                        ->whereRaw(" 1=1 "
                            . $pono_cond
                            . $prodes_cond
                            . $linedes_cond
                            . $status_cond
                            . $date_cond)
                        ->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $pmr_details_data;
    }

    /**
    * Generate Parts Report.
    **/
    public function postPrintPmr(Request $request_data)
    {
        $id = trim($request_data['id']);
        $cur_id = '';
        $inventory_no = '';
        $max_id = '';
        $max_id = '';

        $dt = Carbon::now();
        $date = substr($dt->format('  M j, Y A'), 2);

        $common = new CommonController;
        $company_info = $common->getCompanyInfo();

        $pmr_data = DB::connection($this->mysql)->table('tbl_request_summary')
                       ->select('id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'requestedby'
                            , 'lastservedby'
                            , DB::raw("DATE_FORMAT(lastserveddate, '%m/%d/%Y') as lastserveddate")
                            , 'createdby'
                            , DB::raw("DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') as created_at")
                            , 'updatedby'
                            , DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at"))
                       ->where("id", "=", $id)
                        ->get();


        if(count($pmr_data) > 0)
        {
            $transno            = $pmr_data[0]->transno;
            $pono               = $pmr_data[0]->pono;
            $productdestination = $pmr_data[0]->destination;
            $linedestination    = $pmr_data[0]->line;
            $requestedby        = $pmr_data[0]->requestedby;
            $daterequested      = $pmr_data[0]->updated_at;
            $status             = $pmr_data[0]->status;

            $pmr_details_data = DB::connection($this->mysql)->table('tbl_request_detail')
                            ->where('transno','=', $transno)
                            ->select('code'
                                    , 'name'
                                    , DB::raw('FORMAT(issuedqty,2) AS issuedqty')
                                    , DB::raw('FORMAT(requestqty,2) AS requestqty')
                                    , DB::raw('FORMAT(servedqty,2) AS servedqty')
                                    , 'classification'
                                    , 'lot_no'
                                    , 'remarks'
                                    , 'requestedby'
                                    , 'acknowledgeby')
                            ->orderBy('code')
                            ->get();
        }
        else
        {
            $transno            = "";
            $pono               = "";
            $productdestination = "";
            $linedestination    = "";
            $requestedby        = "";
            $daterequested      = "";
            $remarks            = "";
            $status             = "";
            $pmr_details_data = [];
        }

        $data = [
            'date' => $date,
            'company_info' => $company_info,
            'transno' => $transno,
            'pono' => $pono,
            'productdestination' => $productdestination,
            'linedestination' => $linedestination,
            'requestedby' => $requestedby,
            'daterequested' => $daterequested,
            'status' => $status,
            'pmr_details_data' => $pmr_details_data,
        ];

        $pdf = PDF::loadView('pdf.wbs_production_request', $data)
                    ->setPaper('A4')
                    ->setOption('margin-top', 10)->setOption('margin-bottom', 5)
                    ->setOrientation('landscape');
        return $pdf->inline('Product_Material_Request_'.$date);
    }

    public function getMassAlert()
    {
        $db = DB::connection($this->mysql)->table('tbl_request_detail')
                ->select('transno','code','requestqty')
                ->get();

        $status = "Closed";
        foreach ($db as $key => $pmr) {
            // $chck = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
            //         ->select('issuance_no','item',DB::raw("SUM(issued_qty_t) as issued_qty"))
            //         ->groupBy('issuance_no','item')
            //         ->where('request_no',$pmr->transno)
            //         ->count();
            
            // if ($chck > 0) {
            //     $whs = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
            //             ->select('issuance_no','item',DB::raw("SUM(issued_qty_t) as issued_qty"))
            //             ->groupBy('issuance_no','item')
            //             ->where('request_no',$pmr->transno)
            //             ->first();

            //     if ($pmr->requestqty == $whs->issued_qty) {
            //         DB::connection($this->mysql)->table('tbl_request_summary')
            //             ->where('transno',$pmr->transno)
            //             ->update(['status' => 'Closed']);
            //     }
            // }
            $check = DB::connection($this->mysql)->table('tbl_request_detail')
                        ->select('transno',DB::raw('SUM(requestqty) as requestqty'),
                                DB::raw('SUM(servedqty) as servedqty'))
                        ->where('transno',$pmr->transno)
                        ->groupBy('transno')
                        ->get();
            //if (isset($check[$key]->servedqty)) {
                if ( ($check[0]->servedqty > 0) && ($check[0]->requestqty == $check[0]->servedqty) ) {
                    $status = 'Closed';
                }

                if ( ($check[0]->servedqty < 1) ) {
                    $status = 'Alert';
                }

                if (($check[0]->servedqty > 0) && ($check[0]->requestqty > $check[0]->servedqty)) {
                    $status = 'Serving';
                }

                DB::connection($this->mysql)->table('tbl_request_summary')
                    ->where('transno',$pmr->transno)
                    ->update(['status' => $status]);
            //}
            
        }

        return DB::connection($this->mysql)->table('tbl_request_summary')
                    ->where('status','<>','Cancelled')
                    ->where('status','<>','Alert')
                    ->orderBy('id','desc')
                    ->get();
    }

    public function editAcknowledgeby(Request $req)
    {
        try {
            $updated = DB::connection($this->mysql)->table('tbl_request_detail')
                ->where('id',$req->pk)
                ->update([
                    'acknowledgeby' => $req->value,
                    'acknowledge_all' => 1]);

        } catch (Exception $e) {
            return response()->json(array('status'=>$e->getMessage()),200);
        }

        if($updated) {
            return response()->json(array('status'=>1),200);
        } else {
            return response()->json(array('status'=>0),200);
        }
    }

    public function checkAcknowledge(Request $req)
    {
        $data = [
                'return_status' => 'failed'
            ];
        $issuance = DB::connection($this->mysql)
                        ->table('tbl_request_detail')
                        ->where('transno',$req->transno)
                        ->where('acknowledge_all',0)
                        ->count();
        if ($issuance < 1) {
            DB::connection($this->mysql)
                ->table('tbl_request_summary')
                ->where('transno',$req->transno)
                ->update(['acknowledge_all'=>1]);
            $data = [
                'return_status' => 'success'
            ];
        }
        return $data;
    }

}
