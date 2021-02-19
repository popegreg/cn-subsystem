<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: WBSPhysicalInventoryController.php
     MODULE NAME:  3006 : WBS - Physical Inventory
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.08.01
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.08.01     MESPINOSA       Initial Draft
*******************************************************************************/
?>
<?php

namespace App\Http\Controllers\WBS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use Dompdf\Dompdf;
use PDF;
use DB;
use Config;
use Excel;

/**
* Physical Inventory Controller
**/
class WBSPhysicalInventoryController extends Controller
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

    /**
    * Load Phisical Inventory Data
    **/
    public function getPhysicalInventory(Request $request_data)
    {
        $pgcode = Config::get('constants.MODULE_CODE_PHYINV');
        
        if(!$this->com->getAccessRights($pgcode, $userProgramAccess))
        {
            return redirect('/home'); 
        }
        else
        {
            $pi_data = [];
            $pi_batch_data = [];
            $ismax = false;
            $max_id = 0;
            $cur_id = 0;

            # Get parameters.
            $getrecid = trim($request_data['recid']);
            $getaction = trim($request_data['action']);
            $getinventoryno = trim($request_data['hdninventoryno']);
            $getinventorydate = trim($request_data['inventorydate']);
            $getlocation = trim($request_data['location']);

            $inventory_no = '';
            $batchUpdateFlag = '0';

            $db = 'sqlsrvbu';

            # Load material receving in VIEW MODE (Default)
                # 1. Paggination
                # 2. Load Data By Location
                # 3. Search
                # 4. Discard Changes
            if(empty($getaction) || $getaction == 'VIEW')
            {
                $id = trim($request_data['id']);
                $getpage = trim($request_data['page']);
                $action = 'VIEW';
                $batchUpdateFlag = null;

                if(empty($id))
                {
                    $getpage = 'MIN';
                }

                # Retreive Parts Receive Invoice Data
                $pi_data = $this->getPiData($getpage, $id, $cur_id, $inventory_no, $getlocation, $max_id);
                # Get the Record count of Parts Receive Invoice Data
                $pi_data_cnt = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')->count();

                if($pi_data_cnt > 0)
                {
                    # Retreive Parts Receive Batch Details Data
                    $pi_batch_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory_details')
                                    ->select('id', 'item', 'item_desc as description'
                                        , 'location'
                                        , DB::raw('FORMAT(whs100, 4) AS whs100')
                                        , DB::raw('FORMAT(whs102, 4) AS whs102')
                                        , DB::raw('FORMAT(whsnon, 4) AS whsnon')
                                        , DB::raw('FORMAT(whssm, 4) AS whssm')
                                        , DB::raw('FORMAT(whsng, 4) AS whsng')
                                        , DB::raw('FORMAT((whs100 + whs102 
                                                + whsnon + whssm 
                                                + whsng),4) as inventory_qty')
                                        , DB::raw('FORMAT(actual_qty,4) as actual_qty')
                                        , DB::raw('FORMAT(variance,4) as variance'), 'remarks')
                                    ->where('wbs_pi_id', '=', $inventory_no)
                                    ->orderBy('location', 'ASC')
                                    ->orderBy('item', 'ASC')
                                    ->get();
                }
                $ismax = false;
                $items = DB::connection($this->mssql)
                            ->table('XZAIK AS R')
                            ->join('XHEAD AS H','H.CODE', '=','R.CODE')
                            ->whereRaw("R.RACKNO like '%" . $getlocation . "%'")
                            ->select('R.CODE as code', 'H.NAME as name')
                            ->groupBy('R.CODE', 'H.NAME')
                            ->get();
            }
            else
            {
                $action = $getaction;
                $pi_data = '';
                $pi_details_data = '';
                $ismax = false;
                $items = NULL;

                if($action =='ADD')
                {
                    $inventoryno = '';
                }
                else
                {
                    $inventoryno = $getinventoryno;   
                    $id = $getrecid;
                }

                # Retreive Parts Receive Invoice Data
                $pi_data = $this->getPiDataByRackNo($db,
                                            $getlocation, $action,
                                            $id, $inventoryno,
                                            $max_id);

                # Retreive Parts Receive Batch Details Data
                $pi_batch_data = $this->getPiDetailsDataByRackNo($db,$getlocation);
                $batchUpdateFlag = '1';

                $items = DB::connection($this->mssql)
                            ->table('XZAIK AS Z')
                            ->join('XHEAD AS H','H.CODE', '=','Z.CODE')
                            ->whereRaw("Z.RACKNO like '%" . $getlocation . "%'")
                            ->select('H.CODE as code', 'H.NAME as name')
                            ->groupBy('H.CODE', 'H.NAME')
                            ->get();
            }

            if($max_id == $cur_id)
            {
                $ismax = true;
            }

            # Render WBS Page.
            return view('wbs.physicalinventory',['userProgramAccess' => $userProgramAccess
                        ,'pi_data' => $pi_data
                        ,'pi_batch_data' => $pi_batch_data
                        ,'ismax' => $ismax
                        ,'action' => $action
                        ,'batchUpdateFlag' => $batchUpdateFlag
                        ,'items' => $items
                        ,'pgcode' => $pgcode
                        ,'pgaccess' => $this->com->getPgAccess($pgcode)
                    ]);
        }
    }


    /**
    * Retreive Parts Receiving Invoice Data.
    **/
    private function getPiData($getpage, &$id, &$cur_id, &$inventory_no, &$location_no, &$max_id)
    {
        $result = null;

        try
        {
            switch ($getpage) 
            {
                # Go to First Page.
                case 'MIN':
                    $pi_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                       ->select('id'
                            , 'inventory_no'
                            , 'location'
                            , DB::raw("DATE_FORMAT(inventory_date, '%Y-%m-%d') as inventory_date")
                            , DB::raw("DATE_FORMAT(inventory_date, '%H:%i:%s') as inventory_time")
                            , DB::raw("DATE_FORMAT(actual_date, '%Y-%m-%d') as actual_date")
                            , DB::raw("DATE_FORMAT(actual_date, '%H:%i:%s') as actual_time")
                            , 'counted_by'
                            , 'remarks'
                            , 'status'
                            , 'create_user'
                            , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                            , 'update_user'
                            , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                       ->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MIN(id)"))
                              ->from('tbl_wbs_physical_inventory');
                          })
                        ->get();
                    break;
                
                # Go to Last Page.
                case 'MAX':
                    $pi_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                       ->select('id'
                            , 'inventory_no'
                            , 'location'
                            , DB::raw("DATE_FORMAT(inventory_date, '%Y-%m-%d') as inventory_date")
                            , DB::raw("DATE_FORMAT(inventory_date, '%H:%i:%s') as inventory_time")
                            , DB::raw("DATE_FORMAT(actual_date, '%Y-%m-%d') as actual_date")
                            , DB::raw("DATE_FORMAT(actual_date, '%H:%i:%s') as actual_time")
                            , 'counted_by'
                            , 'remarks'
                            , 'status'
                            , 'create_user'
                            , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                            , 'update_user'
                            , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                       ->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MAX(id)"))
                              ->from('tbl_wbs_physical_inventory');
                          })
                        ->get();
                    break;

                # Go to Previous Page.
                case 'PRV':
                    $pi_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                       ->select('id'
                            , 'inventory_no'
                            , 'location'
                            , DB::raw("DATE_FORMAT(inventory_date, '%Y-%m-%d') as inventory_date")
                            , DB::raw("DATE_FORMAT(inventory_date, '%H:%i:%s') as inventory_time")
                            , DB::raw("DATE_FORMAT(actual_date, '%Y-%m-%d') as actual_date")
                            , DB::raw("DATE_FORMAT(actual_date, '%H:%i:%s') as actual_time")
                            , 'counted_by'
                            , 'remarks'
                            , 'status'
                            , 'create_user'
                            , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                            , 'update_user'
                            , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("id", "<", $id)
                        ->orderBy("id", "DESC")
                        ->skip(0)->take(1)
                        ->get();
                    break;

                # Go to Next Page.
                case 'NXT':
                    $pi_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                       ->select('id'
                            , 'inventory_no'
                            , 'location'
                            , DB::raw("DATE_FORMAT(inventory_date, '%Y-%m-%d') as inventory_date")
                            , DB::raw("DATE_FORMAT(inventory_date, '%H:%i:%s') as inventory_time")
                            , DB::raw("DATE_FORMAT(actual_date, '%Y-%m-%d') as actual_date")
                            , DB::raw("DATE_FORMAT(actual_date, '%H:%i:%s') as actual_time")
                            , 'counted_by'
                            , 'remarks'
                            , 'status'
                            , 'create_user'
                            , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                            , 'update_user'
                            , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("id", ">", $id)
                        ->orderBy("id")
                        ->skip(0)->take(1)->get();
                    break;

                # Go to Specified MATCode.
                case 'PI':
                    $pi_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                       ->select('id'
                            , 'inventory_no'
                            , 'location'
                            , DB::raw("DATE_FORMAT(inventory_date, '%Y-%m-%d') as inventory_date")
                            , DB::raw("DATE_FORMAT(inventory_date, '%H:%i:%s') as inventory_time")
                            , DB::raw("DATE_FORMAT(actual_date, '%Y-%m-%d') as actual_date")
                            , DB::raw("DATE_FORMAT(actual_date, '%H:%i:%s') as actual_time")
                            , 'counted_by'
                            , 'remarks'
                            , 'status'
                            , 'create_user'
                            , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                            , 'update_user'
                            , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("inventory_no", "=", $id)
                        ->orderBy("id")
                        ->skip(0)->take(1)->get();
                    break;

                # Go to Specified Page.
                default:
                    $pi_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                       ->select('id'
                            , 'inventory_no'
                            , 'location'
                            , DB::raw("DATE_FORMAT(inventory_date, '%Y-%m-%d') as inventory_date")
                            , DB::raw("DATE_FORMAT(inventory_date, '%H:%i:%s') as inventory_time")
                            , DB::raw("DATE_FORMAT(actual_date, '%Y-%m-%d') as actual_date")
                            , DB::raw("DATE_FORMAT(actual_date, '%H:%i:%s') as actual_time")
                            , 'counted_by'
                            , 'remarks'
                            , 'status'
                            , 'create_user'
                            , DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at")
                            , 'update_user'
                            , DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                       ->where("id", "=", $id)
                        ->get();
                    break;
            }

            # Get the ID of the last record.
            $max_id = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')->select(DB::raw("MAX(id) as id"))->get();

            # retrieve the inventoryno and id for Details data loading.
            foreach ($pi_data as $key => $value) 
            {
                $value = get_object_vars($value);
                $inventory_no = $value['inventory_no'];
                $location_no = $value['location'];
                $cur_id = $value['id'];
                break;
            }

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
        return $pi_data;
    }

    /**
    * Retreive Parts Receive Data Using Location.
    */
    private function getPiDataByRackNo($db, &$getrackno, 
                                            $action,
                                            &$id, 
                                            &$inventory_no,
                                            &$pi_data_cnt)
    {
        $pi_data = null;

        # Validate Inventory No.
        if($action == 'ADD')
        {
            $inventory_no = 'NULL';
        }
        else
        {
            $inventory_no = "'" . $inventory_no . "'";
        }

        try
        {
            /*
            SELECT ROW_NUMBER() OVER(ORDER BY Z.CODE) AS id, Z.CODE as code, 
                Z.RACKNO as location,
                '' as remarks
            FROM XZAIK Z
            WHERE Z.RACKNO = 'R05-1/L5-011';
            */
            # Retreive Parts Receive Invoice Data
            $pi_data = DB::connection($this->mssql)
                        ->table('XZAIK AS Z')
                        ->select(DB::raw("1 AS id")
                            , DB::raw($inventory_no . " as inventory_no")
                            , DB::raw("'". $getrackno."' as location")
                            , DB::raw("CONVERT(VARCHAR(10),GETDATE(), 101) as inventory_date")
                            , DB::raw("CONVERT(VARCHAR(10),GETDATE(), 108) as inventory_time")
                            , DB::raw("CONVERT(VARCHAR(10),GETDATE(), 101) as actual_date")
                            , DB::raw("CONVERT(VARCHAR(10),GETDATE(), 108) as actual_time")
                            , DB::raw(" '' as status")
                            , DB::raw(" '' as remarks")
                            , DB::raw(" '". Auth::user()->user_id ."' as counted_by")
                            , DB::raw(" '". Auth::user()->user_id ."' as create_user")
                            , DB::raw("CONVERT(varchar, GETDATE(), 101) as created_at")
                            , DB::raw(" '". Auth::user()->user_id ."' as update_user")
                            , DB::raw("CONVERT(varchar, GETDATE(), 101) as updated_at"))
                        ->whereRaw("Z.RACKNO like '%". $getrackno ."%'")
                        ->skip(0)->take(1)->get();

            # Retreive the Last ID if Parts Receive Invoice Data
            $pi_data_cnt = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')->select(DB::raw("MAX(id) as id"))->get();

            # retrieve the id for Details data loading.
            foreach ($pi_data as $key => $value) 
            {
                $value = get_object_vars($value);
                $getrackno = $value['location'];
                $id = $value['id'];
                break;
            }            
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }
        return $pi_data;
    }

    /**
    * Retreive Parts Receive Details Data Using Location.
    */
    private function getPiDetailsDataByRackNo($db, $getrackno)
    {
        $result = null;
        try
        {
            $result = DB::connection($this->mssql)
                    ->table('XZAIK AS Z')
                    ->join('XHEAD AS H','H.CODE', '=','Z.CODE')
                    ->leftJoin(DB::raw("(select code, hokan, zaik 
                                        from xzaik 
                                        where HOKAN = 'WHS100') AS w1")
                                , 'w1.CODE', '=', 'Z.CODE')
                    ->leftJoin(DB::raw("(select code, hokan, zaik 
                                        from xzaik 
                                        where HOKAN = 'WHS102') AS w2")
                                , 'w2.CODE', '=', 'Z.CODE')
                    ->leftJoin(DB::raw("(select code, hokan, zaik 
                                        from xzaik 
                                        where HOKAN = 'WHS-NON') AS w3")
                                , 'w3.CODE', '=', 'Z.CODE')
                    ->leftJoin(DB::raw("(select code, hokan, zaik 
                                        from xzaik 
                                        where HOKAN = 'WHS-SM') AS w4")
                                , 'w4.CODE', '=', 'Z.CODE')
                    ->leftJoin(DB::raw("(select code, hokan, zaik 
                                        from xzaik 
                                        where HOKAN = 'WHS-NG') AS w5")
                                , 'w5.CODE', '=', 'Z.CODE')
                   ->select('Z.CODE AS item'
                        , 'H.NAME AS description'
                        , 'Z.RACKNO as location'
                        , DB::raw("CAST(ISNULL(w1.ZAIK, 0) AS VARCHAR) AS 'whs100'")
                        , DB::raw("CAST(0.0000 AS VARCHAR) AS 'whs100_actual_qty'")
                        , DB::raw("CAST(ISNULL(w2.ZAIK, 0) AS VARCHAR) AS 'whs102'")
                        , DB::raw("CAST(0.0000 AS VARCHAR) AS 'whs102_actual_qty'")
                        , DB::raw("CAST(ISNULL(w3.ZAIK, 0) AS VARCHAR) AS 'whsnon'")
                        , DB::raw("CAST(0.0000 AS VARCHAR) AS 'whsnon_actual_qty'")
                        , DB::raw("CAST(ISNULL(w4.ZAIK, 0) AS VARCHAR) AS 'whssm'")
                        , DB::raw("CAST(0.0000 AS VARCHAR) AS 'whssm_actual_qty'")
                        , DB::raw("CAST(ISNULL(w5.ZAIK, 0) AS VARCHAR) AS 'whsng'")
                        , DB::raw("CAST(0.0000 AS VARCHAR) AS 'whsng_actual_qty'")
                        , DB::raw("CAST((ISNULL(w1.ZAIK, 0) + ISNULL(w2.ZAIK, 0) 
                            + ISNULL(w3.ZAIK, 0) + ISNULL(w4.ZAIK, 0) 
                            + ISNULL(w5.ZAIK, 0)) AS VARCHAR) AS inventory_qty")
                        , DB::raw("CAST(0.0000 AS VARCHAR) AS 'actual_qty'")
                        , DB::raw("CAST(((ISNULL(w1.ZAIK, 0) + ISNULL(w2.ZAIK, 0) 
                            + ISNULL(w3.ZAIK, 0) + ISNULL(w4.ZAIK, 0) 
                            + ISNULL(w5.ZAIK, 0)) * -1) AS VARCHAR) AS variance")
                        , DB::raw("'' as remarks"))
                    ->whereRaw("Z.RACKNO like '%" . $getrackno . "%'")
                    ->orderBy('Z.RACKNO', 'ASC')
                    ->orderBy('Z.CODE', 'ASC')
                    ->get();
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Insert Parts Receive, Details, Summary and Batch Details.
    **/
    public function savePiWbs(Request $request_data)
    {
        $data         = $request_data['pi_arr'];
        $details_arr = $request_data['detail_arr'];
        $batchUpdateflag = $request_data['batchUpdateflag'];

        try
        {
            // if(is_null($details_arr))
            // {
            //     $pi_batch_data = null;
            // }
            // else
            // {
            //     $pi_batch_data = $this->mergeArray($details_arr);
            // }

            $result = $this->insertPi(json_decode($data), json_decode($details_arr));
            // $result = 0;
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
    public function updatePiWbs(Request $request_data)
    {
        $data = $request_data['pi_arr'];
        $details_arr = $request_data['detail_arr'];
        $batchUpdateflag = $request_data['batchUpdateflag'];

        $result = 0;

        try
        {
            // if($batchUpdateflag == '1')
            // {
            //     if(is_null($details_arr))
            //     {
            //         $pi_batch_data = null;
            //     }
            //     else
            //     {
            //         $pi_batch_data = $this->mergeArray($details_arr);
            //     }
            // }
            // else
            // {
            //     $pi_batch_data = $this->mergeArray($details_arr);
            // }
            $newdata = json_decode($data);
            $result = $this->updatePi(json_decode($data), json_decode($details_arr), $batchUpdateflag);

            $result = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                      ->select('id')
                      ->where('inventory_no', '=', $newdata->inventoryno)
                      ->get();
            $result = $result[0]->id;
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Update Status of Parts Receive Invoice Data to CANCEL.
    **/
    public function cancelPiWbs(Request $request_data)
    {
        $result = 0;
        try
        {
            $recid = $request_data['id'];
            $result = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                    ->where('id', $recid)
                    ->update([
                        'status'       => 'Cancelled'
                        ,'update_user' => Auth::user()->user_id
                        ,'updated_at'  => date("Y/m/d H:i:sa")]);

            $message = "Selected transaction successfully cancelled.";
            $output = redirect(url('/wbsphysicalinventory?page=CUR&id='. $recid))
                        ->with(['message' => $message]);
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }
        return $output;
    }

    /**
    * Validate and Fotmat Date.
    **/
    private function formatDate($date, $format)
    {
        if(empty($date))
        {
            return null;
        }
        else
        {
            return date($format,strtotime($date));
        }
    }

    /**
    * Search Parts Receive Data.
    **/
    public function searchPiWbsData(Request $request_data)
    {
        $condition = $request_data['condition_arr'];
        $ctr = 0;
        $value = null;
        $result = null;

        $idx_srch_location  = 0;
        $idx_srch_inv_from  = 1;
        $idx_srch_inv_to    = 2;
        $idx_srch_act_from  = 3;
        $idx_srch_act_to    = 4;
        $idx_srch_countedby = 5;
        $idx_srch_open      = 6;
        $idx_srch_closed    = 7;
        $idx_srch_cancelled = 8;

        $invdate_cond = '';
        $actdate_cond = '';
        $location_cond = '';
        $countedby_cond = '';
        $status_cond = '';

        try
        {
            $condition[$idx_srch_inv_from] = $this->formatDate($condition[$idx_srch_inv_from], 'Y-m-d');
            $condition[$idx_srch_inv_to] = $this->formatDate($condition[$idx_srch_inv_to], 'Y-m-d');
            # Create Inventory Date Condition.
            if(is_null($condition[$idx_srch_inv_from]) and is_null($condition[$idx_srch_inv_to]))
            {
                $invdate_cond = '';
            }
            else
            {
                $invdate_cond = " AND DATE_FORMAT(inventory_date, '%Y-%m-%d') BETWEEN '" . $condition[$idx_srch_inv_from] . "' AND '" . $condition[$idx_srch_inv_to] . "'";
            }

            $condition[$idx_srch_act_from] = $this->formatDate($condition[$idx_srch_act_from], 'Y-m-d');
            $condition[$idx_srch_act_to] = $this->formatDate($condition[$idx_srch_act_to], 'Y-m-d');
            # Create Actual Date Condition.
            if(is_null($condition[$idx_srch_act_from]) and is_null($condition[$idx_srch_act_to]))
            {
                $actdate_cond = '';
            }
            else
            {
                $actdate_cond = " AND DATE_FORMAT(actual_date, '%Y-%m-%d') BETWEEN '" . $condition[$idx_srch_act_from] . "' AND '" . $condition[$idx_srch_act_to] . "'";
            }

            # Create Location. Condition
            if(empty($condition[$idx_srch_location]))
            {
                $location_cond ='';
            }
            else
            {
                $location_cond = " AND location like '%" . $condition[$idx_srch_location] . "%'";
            }

            # Create Pallet No. Condition
            if(empty($condition[$idx_srch_countedby]))
            {
                $countedby_cond = '';
            }
            else
            {
                $countedby_cond = " AND counted_by = '" . $condition[$idx_srch_countedby] . "'";
            }

            # Create Status Condition
            if($condition[$idx_srch_open] > 0 || $condition[$idx_srch_closed] > 0 || $condition[$idx_srch_cancelled] > 0)
            {
                if($condition[$idx_srch_open] == 1)
                {
                    $open = "'Open'";
                }
                else
                {
                    $open = "''";   
                }

                if($condition[$idx_srch_closed] == 1)
                {
                    $close = "'Close'";
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

            # Retrieve Data using the generated conditions.
            $pi_details_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory')
                        ->select( 'id'
                            , 'inventory_no'
                            , 'location'
                            , DB::raw("(CASE inventory_date 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(inventory_date, '%Y-%m-%d %H:%i:%s') 
                               END) AS inventory_date")
                            , DB::raw("(CASE actual_date 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(actual_date, '%Y-%m-%d %H:%i:%s') 
                               END) AS actual_date")
                            , 'counted_by'
                            , 'status'
                            , 'create_user'
                            , DB::raw("(CASE created_at 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') 
                               END) AS created_at")
                            , 'update_user'
                            , DB::raw("(CASE updated_at 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') 
                               END) AS updated_at"))
                        ->whereRaw(" 1=1 " 
                            . $invdate_cond
                            . $actdate_cond
                            . $location_cond
                            . $countedby_cond
                            . $status_cond)
                        ->get();
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $pi_details_data;
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

    /**
    * Insert Parts Receive, Details, Summary and Batch Details.
    **/
    private function insertPi($pi_data, $pi_batch_data)
    {
        $result=false;
        $variancesum = 0;
        $inventory_no = 0;

        try
        {
            $com = new CommonController();
            $inventory_no = $com->getTransCode('PHY_INV');

            DB::connection($this->mysql)->table("tbl_wbs_physical_inventory")
                ->insert([
                    'inventory_no'    => $inventory_no
                    ,'inventory_date' => date('Y/m/d H:i:sa',strtotime($pi_data->inventorydate))
                    ,'location'       => $pi_data->location
                    ,'actual_date'    => date('Y/m/d H:i:sa',strtotime($pi_data->actualdate))
                    ,'counted_by'     => $pi_data->countedby
                    ,'remarks'        => $pi_data->remarks
                    ,'status'         => 'Open'
                    ,'create_user'    => Auth::user()->user_id
                    ,'update_user'    => Auth::user()->user_id
                    ,'created_at'     => date("Y/m/d H:i:sa")
                    ,'updated_at'     => date("Y/m/d H:i:sa")
                    ]);       

            # insert all added MR Batch Data.
            if(isset($pi_batch_data))
            {
                foreach ($pi_batch_data->inputItemNo as $key => $item) 
                {
                    $result = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory_details')
                            ->insert(
                                ['wbs_pi_id'        => $inventory_no
                                ,'item'             => $item
                                ,'item_desc'        => $pi_batch_data->inputItem[$key]
                                ,'location'         => $pi_batch_data->inputLocation[$key]
                                ,'whs100'           => $pi_batch_data->inputwhs100[$key]
                                ,'whs102'           => $pi_batch_data->inputwhs102[$key]
                                ,'whsnon'           => $pi_batch_data->inputwhsnon[$key]
                                ,'whssm'            => $pi_batch_data->inputwhssm[$key]
                                ,'whsng'            => $pi_batch_data->inputwhsng[$key]
                                ,'actual_qty'       => $pi_batch_data->inputActualQty[$key]
                                ,'variance'         => $pi_batch_data->inputVariance[$key]
                                ,'remarks'          => $pi_batch_data->inputRemarks[$key]
                                ,'create_user'      => Auth::user()->user_id
                                ,'update_user'      => Auth::user()->user_id
                                ,'created_at'       => date("Y/m/d H:i:sa")
                                ,'updated_at'       => date("Y/m/d H:i:sa")
                                ]);
                }
            }

            $result = $this->computeVariance($inventory_no);
            $result = 0;
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
    private function updatePi($pi_data, $pi_batch_data , $isbatchUpdate)
    {
        $result=false;
        $variancesum   = 0;
        $status = '';

        try
        {

            if(empty($pi_data->status))
            {
                $status = 'Open';
            }

            DB::connection($this->mysql)->table("tbl_wbs_physical_inventory")
            ->where('inventory_no', $pi_data->inventoryno)
            ->update([
               'inventory_date' => date('Y/m/d H:i:sa',strtotime($pi_data->inventorydate))
               ,'location'       => $pi_data->location
               ,'actual_date'    => date('Y/m/d H:i:sa',strtotime($pi_data->actualdate))
               ,'counted_by'     => $pi_data->countedby
               ,'remarks'        => $pi_data->remarks
               ,'status'        => $status
               ,'update_user'   => Auth::user()->user_id
               ,'updated_at'    => date("Y/m/d H:i:sa")
               ]);


            if($isbatchUpdate == '1')
            {
                $result = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory_details')
                            ->where('wbs_pi_id', '=', $pi_data->inventoryno)
                            ->delete();

                foreach ($pi_batch_data->inputItemNo as $key => $item) 
                {
                    $result = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory_details')
                            ->insert(
                                ['wbs_pi_id'        => $pi_data->inventoryno
                                ,'item'             => $item
                                ,'item_desc'        => $pi_batch_data->inputItem[$key]
                                ,'location'         => $pi_batch_data->inputLocation[$key]
                                ,'whs100'           => $pi_batch_data->inputwhs100[$key]
                                ,'whs102'           => $pi_batch_data->inputwhs102[$key]
                                ,'whsnon'           => $pi_batch_data->inputwhsnon[$key]
                                ,'whssm'            => $pi_batch_data->inputwhssm[$key]
                                ,'whsng'            => $pi_batch_data->inputwhsng[$key]
                                ,'actual_qty'       => $pi_batch_data->inputActualQty[$key]
                                ,'variance'         => $pi_batch_data->inputVariance[$key]
                                ,'remarks'          => $pi_batch_data->inputRemarks[$key]
                                ,'create_user'      => Auth::user()->user_id
                                ,'update_user'      => Auth::user()->user_id
                                ,'created_at'       => date("Y/m/d H:i:sa")
                                ,'updated_at'       => date("Y/m/d H:i:sa")
                                ]);
                }
            }
            else
            {
                foreach ($pi_batch_data->inputItemNo as $key => $item) 
                {
                    $result = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory_details')
                            ->where('id', '=', $pi_batch_data->inputBatchId[$key])
                            ->update([
                                'wbs_pi_id'        => $pi_data->inventoryno
                                ,'item'             => $item
                                ,'item_desc'        => $pi_batch_data->inputItem[$key]
                                ,'location'         => $pi_batch_data->inputLocation[$key]
                                ,'whs100'           => $pi_batch_data->inputwhs100[$key]
                                ,'whs102'           => $pi_batch_data->inputwhs102[$key]
                                ,'whsnon'           => $pi_batch_data->inputwhsnon[$key]
                                ,'whssm'            => $pi_batch_data->inputwhssm[$key]
                                ,'whsng'            => $pi_batch_data->inputwhsng[$key]
                                ,'actual_qty'       => $pi_batch_data->inputActualQty[$key]
                                ,'variance'         => $pi_batch_data->inputVariance[$key]
                                ,'remarks'          => $pi_batch_data->inputRemarks[$key]
                                ,'created_at'       => date("Y/m/d H:i:sa")
                                ,'updated_at'       => date("Y/m/d H:i:sa")
                                ]);

                            
                }
            }

            $result = $this->computeVariance($pi_data->inventoryno);
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }
    
        return $result;
    }

    /**
    * Compute variance per inventory no.
    **/
    private function computeVariance($inventory_no)
    {
        $result=0;
        $idx_inventoryno   = 0;

        try
        {
            $variancesum = 0;
            $getvariance = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory_details')
            ->where('wbs_pi_id', $inventory_no)
            ->select(DB::raw('IFNULL(SUM(variance), 0) as variance'))
            ->groupBy('wbs_pi_id')
            ->get();

            foreach ($getvariance as $key => $value) 
            {
                $value = get_object_vars($value);
                $variancesum = floatval($value['variance']);
                break;
            }

            if($variancesum == 0)
            {
                DB::connection($this->mysql)->table("tbl_wbs_physical_inventory")
                ->where('inventory_no', $inventory_no)
                ->update(['status'=> 'Close' ]);  
            }
            else
            {
                DB::connection($this->mysql)->table("tbl_wbs_physical_inventory")
                ->where('inventory_no', $inventory_no)
                ->update(['status'=> 'Open' ]);     
            }
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }
        return $result;

    }

    /**
    * Generate Parts Report.
    **/
    public function printPiReport(Request $request_data)
    {
        $id = trim($request_data['id']);
        $cur_id = '';
        $inventory_no = '';
        $max_id = '';
        $max_id = '';

        $dt = Carbon::now();
        $date = substr($dt->format('  M j, Y A'), 2);

        $company_info = $this->com->getCompanyInfo();

        $pi_data = $this->getPiData('CUR', $id, $cur_id, $inventoryno, $getlocation, $max_id);


        if(count($pi_data) > 0)
        {
            $inventoryno   = $pi_data[0]->inventory_no;
            $inventorydate = $pi_data[0]->inventory_date . ' ' . $pi_data[0]->inventory_time;
            $location      = $pi_data[0]->location;
            $actualdate    = $pi_data[0]->actual_date . ' ' . $pi_data[0]->actual_time;
            $countedby     = $pi_data[0]->counted_by;
            $remarks       = $pi_data[0]->remarks;
            $status        = $pi_data[0]->status;

            $pi_details_data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory as r')
                            ->join('tbl_wbs_physical_inventory_details as d', 'd.wbs_pi_id', '=', 'r.inventory_no')
                            ->whereRaw("r.inventory_no ='" . $inventoryno . "' 
                                        AND d.wbs_pi_id = r.inventory_no")
                            ->select('d.wbs_pi_id'
                                    ,'d.item'
                                    , 'd.item_desc'
                                    , 'd.location'
                                    , DB::raw('FORMAT(d.whs100,2) AS whs100')
                                    , DB::raw('FORMAT(d.whs102,2) AS whs102')
                                    , DB::raw('FORMAT(d.whsnon,2) AS whsnon')
                                    , DB::raw('FORMAT(d.whssm,2) AS whssm')
                                    , DB::raw('FORMAT(d.whsng,2) AS whsng')
                                    , DB::raw('FORMAT(d.whs100 + d.whs102 + d.whsnon + d.whssm + d.whsng,2) AS inventory_qty')
                                    , DB::raw('FORMAT(d.actual_qty,2) AS actual_qty')
                                    , DB::raw('FORMAT(d.variance,2) AS variance'))
                            ->orderBy('d.item')
                            ->get();
        }
        else
        {
            $inventoryno = '';
            $inventorydate = '';
            $location = '';
            $actualdate = '';
            $countedby = '';
            $remarks = '';
            $status = '';
            $pi_details_data = [];
        }

        $html = '
        <html>
          <head>
            <style>
                .header,
                .footer {
                    width: 100%;
                    text-align: center;
                    position: fixed;
                }
                .header {
                    top: 0px;
                }
                .footer {
                    bottom: 0px;
                }
                .pagenum:before {
                    content: counter(page);
                }
                .fontArial
                {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
                }
            </style>
          </head>
          <body>

            <div class="footer fontArial">
                <hr />
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size:12px;">
                    <tbody>
                        <tr>
                            <td align="left">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td>Date:</td>
                                        <td>'. $date .'</td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                            <td align="right">
                            <table align="right" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td>Page:</td>
                                        <td><span class="pagenum"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>          
        <table class="fontArial" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td align="center">
                            <h4>'. $company_info['name'] .'</h4>
                            <p style="line-height: 1.8px; font-size:12px; ">'. $company_info['address'] .'</p>
                            <p style="line-height: 1.8px; font-size:12px; ">'. $company_info['tel1'] . ' ' . $company_info['tel2'] .'</p>
                            <h2><ins>PHYSICAL INVENTORY</ins></h2>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="fontArial" border="0" cellpadding="3" cellspacing="3" style="width: 100%;  font-size:12px;">
                    <tbody>
                        <tr>
                            <td style="width: 100px;">Inventory No. :</td>
                            <td colspan="2">'. $inventoryno .'</td>
                            <td>&nbsp;</td>
                            <td style="width: 100px;">Counted By</td>
                            <td colspan="2">'. $countedby .'</td>
                        </tr>
                        <tr>
                            <td style="width: 100px;">Location :</td>
                            <td colspan="2">'. $location .'</td>
                            <td>&nbsp;</td>
                            <td style="width: 100px;">Remarks :</td>
                            <td colspan="2">'. $remarks .'</td>
                        </tr>
                        <tr>
                            <td style="width: 100px;">Inventory Date :</td>
                            <td colspan="2">'. $inventorydate .'</td>
                            <td>&nbsp;</td>
                            <td style="width: 100px;">Status :</td>
                            <td colspan="2">'. $status .'</td>
                        </tr>
                        <tr>
                            <td style="width: 100px;">Actual Date :</td>
                            <td colspan="2">'. $actualdate .'</td>
                            <td>&nbsp;</td>
                            <td style="width: 100px;"></td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <table class="fontArial"  style="border: 2px solid black ; border-collapse: collapse; width:100%; cellspacing:0; cellpadding:0; font-size:10px;">
                    <thead style="border: 2px solid black;">
                        <tr>
                            <th style="border-right: 1px solid black;" scope="col"><strong>Item No.</strong></th>
                            <th style="border-right: 1px solid black; width:200px;" scope="col"><strong>Item Description</strong></th>
                            <th style="border-right: 1px solid black; width:100px;" scope="col"><strong>Location</strong></th>
                            <th style="border-right: 1px solid black;" scope="col"><strong>WHS100</strong></th>
                            <th style="border-right: 1px solid black;" scope="col"><strong>WHS102</strong></th>
                            <th style="border-right: 1px solid black;" scope="col"><strong>WHSNON</strong></th>
                            <th style="border-right: 1px solid black;" scope="col"><strong>WHSSM</strong></th>
                            <th style="border-right: 1px solid black;" scope="col"><strong>WHSNG</strong></th>
                            <th style="border-right: 1px solid black;" scope="col"><strong>Inventory</strong></th>
                            <th style="border-right: 1px solid black;" scope="col"><strong>Actual</strong></th>
                            <th style="border-right: 1px solid black;" scope="col"><strong>Variance</strong></th>
                        </tr>
                    </thead>
                    <tbody>';

            $html2 = '';

            foreach ($pi_details_data as $key => $row) 
            {
                $html2 = $html2 .'<tr>
                        <td style="border-right: 1px solid black; text-align: left;">'. $row->item .'</td>
                        <td style="border-right: 1px solid black; text-align: left; width:200px;" >'. $row->item_desc .'</td>
                        <td style="border-right: 1px solid black; text-align: left; width:100px;">'. $row->location .'</td>
                        <td style="border-right: 1px solid black; text-align: right;">'. $row->whs100 .'</td>
                        <td style="border-right: 1px solid black; text-align: right;">'. $row->whs102 .'</td>
                        <td style="border-right: 1px solid black; text-align: right;">'. $row->whsnon .'</td>
                        <td style="border-right: 1px solid black; text-align: right;">'. $row->whssm .'</td>
                        <td style="border-right: 1px solid black; text-align: right;">'. $row->whsng .'</td>
                        <td style="border-right: 1px solid black; text-align: right;">'. $row->inventory_qty .'</td>
                        <td style="border-right: 1px solid black; text-align: right;">'. $row->actual_qty .'</td>
                        <td style="border-right: 1px solid black; text-align: right;">'. $row->variance .'</td>
                        </tr>';
            }

            $html3 = '</tbody>
                </table>
          </body>
        </html>';
        // echo $html;

        # gather all html parts.
        $html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . $html . $html2 . $html3;

        $dompdf = new Dompdf();
        $dompdf->loadHTML($html);
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        return $dompdf->stream('Physical Inventory'.Carbon::now().'.pdf');
        
        // $pdf = PDF::loadHTML($html)->setPaper('letter', 'landscape');
        // return $pdf->stream('Parts Receiveing'.Carbon::now().'.pdf');

        // # apply snappy pdf wrapper
        // $pdf = App::make('snappy.pdf.wrapper');
        // # transform html to pdf format.
        // $pdf->loadHTML($html)->setPaper('A4')->setOrientation('landscape');
        // # display PDF report to response.
        // return $pdf->inline();
    }

    public function getBRdetails(Request $req)
    {
        $data = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory_details')
                    ->where('wbs_pi_id',$req->inventoryno)
                    ->where('item',$req->item)
                    ->first();
        return json_encode($data);
    }

    public function wbsPiReport_Excel(Request $array)
    {
        try
        { 
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            
            Excel::create('Physical Inventory'.$date, function($excel) use($array)
            {
                $excel->sheet('Sheet1', function($sheet) use($array)
                {
                    $dt = Carbon::now();
                    $date = $dt->format('m/d/Y');

                    $sheet->cell('A1',"INV No");
                    $sheet->cell('B1',"INV Date");
                    $sheet->cell('C1',"ACT Date");
                    $sheet->cell('D1',"Item No");
                    $sheet->cell('E1',"Item Description");
                    $sheet->cell('F1',"Location");
                    $sheet->cell('G1',"WHS100");
                    $sheet->cell('H1',"WHS102");
                    $sheet->cell('I1',"WHSNON");
                    $sheet->cell('J1',"WHSSM");
                    $sheet->cell('K1',"WHSNG");
                    $sheet->cell('L1',"Inventory");
                    $sheet->cell('M1',"Actual");
                    $sheet->cell('N1',"Variance");
                    $sheet->cell('O1',"Counted By");
                    $sheet->cell('P1',"Remarks");
                    $sheet->cell('Q1',"Status");
             
                    $sheet->setHeight(1,20);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('#ADD8E6');
                        $row->setFontSize(12);
                        $row->setAlignment('center');
                    });
                   
                    $sheet->setStyle(array(
                        'font' => array(
                            'name'  =>  'Calibri',
                            'size'  =>  15
                        )
                    ));

                    $inventoryno = $array->inventoryno;
                    $location = $array->location;

                    $field = DB::connection($this->mysql)->table('tbl_wbs_physical_inventory_details as a')
                                ->leftJoin('tbl_wbs_physical_inventory as b','a.wbs_pi_id','=','b.inventory_no')
                                ->select('a.item','a.item_desc','a.location','a.whs100','a.whs102','a.whsnon','a.whssm','a.whsng','a.variance','a.actual_qty','b.inventory_no','b.inventory_date','b.actual_date','b.counted_by','b.remarks','b.status')
                                ->where('b.inventory_no',$inventoryno)
                                ->where('a.location','like',$location.'%')
                                ->get();
                    
                    $row = 2;
                    foreach ($field as $key => $val) {
                        $sheet->cell('A'.$row, $val->inventory_no);
                        $sheet->cell('B'.$row, $val->inventory_date);
                        $sheet->cell('C'.$row, $val->actual_date);
                        $sheet->cell('D'.$row, $val->item);
                        $sheet->cell('E'.$row, $val->item_desc);
                        $sheet->cell('F'.$row, $val->location);
                        $sheet->cell('G'.$row, $val->whs100);
                        $sheet->cell('H'.$row, $val->whs102);
                        $sheet->cell('I'.$row, $val->whsnon);
                        $sheet->cell('J'.$row, $val->whssm);
                        $sheet->cell('K'.$row, $val->whsng);
                        $sheet->cell('L'.$row, $val->variance);
                        $sheet->cell('M'.$row, $val->actual_qty);
                        $sheet->cell('N'.$row, $val->variance);
                        $sheet->cell('O'.$row, $val->counted_by);
                        $sheet->cell('P'.$row, $val->remarks);
                        $sheet->cell('Q'.$row, $val->status);

                        $sheet->row($row, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->setHeight($row,20);
                        $row++;
                    }
                });

            })->download('xls');
        } catch (Exception $e) {
            return redirect(url('/wbsphysicalinventory'))->with(['err_message' => $e]);
        }    

    }

}
