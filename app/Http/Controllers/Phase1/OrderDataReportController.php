<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: OrderDataReportController.php
     MODULE NAME:  [3002] Order Data Report
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.18
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.18     MESPINOSA       Initial Draft
     100-00-02   1     2016.04.28     MESPINOSA       1.Implement constants.
                                                      2.Fix direct url input.
     100-00-02   2     2016.10.27     AK.DELAROSA     Fix Bugs
*******************************************************************************/

namespace App\Http\Controllers\Phase1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Log;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use Config;
use Carbon\Carbon;
use Datatables;

/**
* OrderDataReport Controller
*/
class OrderDataReportController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;
    protected $com;
    /**
    * OrderDataReportController Constructor.
    */
    public function __construct()
    {
        $this->middleware('auth');
        $this->com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $this->com->userDBcon(Auth::user()->productline,'mysql');
            $this->mssql = $this->com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $this->com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function index()
    {
        if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_YPICS'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            #retrieve default page content
            $suppliers = DB::connection($this->common)->table('msuppliers')->get();
            $productline = DB::connection($this->common)->table('mproductlines')->get();
            #load view
            return view('phase1.OrderDataReport', [
                        'suppliers' => $suppliers, 
                        'productlines' => $productline,
                        'selected_supplier' => '0',
                        'dbconnection' => Auth::user()->productline,
                        'userProgramAccess' => $userProgramAccess,
                        'active' => 'ypicsr3',
                    ]);
        }
    }

    private function getOrderDataReportDetails($db)
    {
        $result = false;
        $data = date("ymd");

        try
        {
            $result = DB::connection($this->mysql)->table('tbl_order_data_report_details')
                    ->where('db', $db)//sqlsrv
                    ->select('inputdate')
                    ->orderBy('id', 'desc')
                    ->skip(0)->take(1)
                    ->get();

            foreach ($result as $key => $value) 
            {
                $value = get_object_vars($value);
                $data = $value['inputdate'];
                break;
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $data;
    }

    public function getYpicsUserData()
    {
        $ypicsuser = DB::connection($this->mssql)
                        ->table('XCONT')
                        ->where('intval', '>', 0)
                        ->where('intval', '<', 99)
                        ->select('inputuser', 'inputdate', 'ckey', 'intval')
                        ->get();

        return $ypicsuser;
    }

    private function retrieveOrderDataReport($database_connection, $selected_supplier)
    {
        $nodata = false;
        $supplier_name = '';
        $schema = '';
        $inputdate = date("ymd");

        switch ($database_connection) 
        {
            # connect to pmi_cn DB.
            case 'CN':
            $database = Config::get('constants.DB_SQLSRV_CN');
            $schema = Config::get('constants.DB_SCHEMA_CN');
            $supplier_name = 'Y016006';

            break;

            # connect to pmi_iscd DB.
            case 'TS':
            $database = Config::get('constants.DB_SQLSRV_BU');
            $schema = Config::get('constants.DB_SHCEMA_BU');
            $supplier_name = 'Y016000';
            break;
            
            # connect to pmi_yf DB.
            case 'YF':
            $database = Config::get('constants.DB_SQLSRV_YF');
            $schema = Config::get('constants.DB_SCHEMA_YF');
            $supplier_name = 'Y016003';
            break;
            
            # connect to pmi_cn DB BUT no data will be retrieved.
            default :
            $nodata = true;
            $database = Config::get('constants.DB_SQLSRV_CN');
            $schema = Config::get('constants.DB_SCHEMA_CN');
            break;
        }

        $inputdate = $this->getOrderDataReportDetails($database);

        # if incorrect DB connection, get the table structure only without data.
        if($nodata)
        {
            $selected_supplier = '0';
            $inputdate = date("ymd");
        }
        
        # retrieve order data report for the the specific DB.
        $order_data_report = DB::connection($database)
        ->table('XSLIP as S')
        ->leftJoin('XHEAD as H', 'H.CODE', '=', 'S.CODE')
        ->select(
            DB::raw("'' AS salesno"),
            DB::raw("'ZOR4' AS salestype"),
            DB::raw("'S022' AS salesorg"),
            DB::raw("'60' AS commercial"),
            DB::raw("'90' AS section"),
            DB::raw("'' AS salesbranch"),
            DB::raw("'' AS salesg"),
            DB::raw(" '" . $selected_supplier . "' AS supplier"), 
            DB::raw("'' AS destination"),
            DB::raw("'' AS payer"),
            DB::raw("'' AS assistant"),
            DB::raw("CONCAT( " . "'0000000000000'". " ,
                convert(int,right(datepart(year,getdate()),2)) , 
                "."'-'"." , S.PORDER, "."'-'"." , 
                replicate("."0".", 3 - len(ROW_NUMBER() OVER(ORDER BY S.PORDER))), 
                ROW_NUMBER() OVER(ORDER BY S.PORDER)) AS purchaseorderno"),
            DB::raw("S.PDATE AS issuedate"),
            DB::raw("S.NDATE AS flightneeddate"),
            DB::raw("'' AS headertext"),
            DB::raw("S.CODE as code "), 
            DB::raw("H.NAME AS itemtext"),
            DB::raw("S.KVOL - S.TJITU as orderquantity"), 
            DB::raw("H.TANI1 AS unit")
        )
        ->where('vendor', '=', $selected_supplier )
        ->whereIn('ema', [0,1])
        ->where('bumo', 'like', 'PURH%')
        ->where('psumi', '=', '')
        ->where('S.inputdate', 'like', date("ymd") . '%')//$inputdate . 
        ->get();

        $this->insertToOrderDataReport($order_data_report,$database_connection,$inputdate);

        $output = $this->getOrderDataReportDB();

        return $output;
    }

    /*
     Count the returned values of retrieveOrderDataReport
     */
    
    private function getOrderDataReportCount($value)
    {
        return count($value);
    }

    /**
    * Retrieve MRP Users. 
    **/
    private function retrieveMrpUsers($database_connection)
    {

        $nodata = false;
        $schema = '';
        switch ($database_connection) 
        {
            # connect to pmi_cn DB.
            case 'CN':
            $database = Config::get('constants.DB_SQLSRV_CN');
            $schema = Config::get('constants.DB_SCHEMA_CN');
            $nodata = true;
            break;

            # connect to pmi_iscd DB.
            case 'TS':
            $database = Config::get('constants.DB_SQLSRV_BU');
            $schema = Config::get('constants.DB_SHCEMA_BU');
            $nodata = true;
            break;

            # connect to pmi_yf DB.
            case 'YF':
            $database = Config::get('constants.DB_SQLSRV_YF');
            $schema = Config::get('constants.DB_SCHEMA_YF');
            break;

            # connect to pmi_cn DB BUT no data will be retrieved.
            default :
            $nodata = true;
            $database = Config::get('constants.DB_SQLSRV_CN');
            $schema = Config::get('constants.DB_SCHEMA_CN');
            $nodata = true;
            break;
        }

        if($nodata)
        {
            # if incorrect DB connection, get the table structure only without data.
            $mrpusers = DB::connection($database)
            ->table('XCONT')
            ->select('inputuser', 'inputdate', 'ckey' , 'intval')
            ->where('inputdate', '=', 0)
            ->get();
        }
        else
        {
            # retrieve MRP Users depending on the DB connection.
            $mrpusers = DB::connection($database)
            ->table('XCONT')
            ->select('inputuser', 'inputdate', 'ckey', 'intval')
            ->where('intval', '>', 0)
            ->where('intval', '<', 99)
            ->get();
        }      

        return $mrpusers;
    }
    
    /**
    * Reload Order Data Report according to the selected DB Connection.
    **/
    public function postOrderDataReport(Request $req)
    {
        $data = [
            'msg' => 'Getting data was unsuccessful.',
            'status' => 'failed'
        ];

        $supplier_id = $req->supplier;
        $dbconnection = Auth::user()->productline;
        $productline_id = $req->productline;
        $database = $this->mssql;

        $this->com->truncateTable($this->mysql,'order_data_report');
        $order_data_report = $this->retrieveOrderDataReport($dbconnection, $supplier_id);
        $countData = $this->getOrderDataReportCount($order_data_report);

        if ($countData > 0) {
            $data = [
                'msg' => 'Data generated successfully.',
                'status' => 'success',
                'successModal' => true
            ];
        } else {
            $data = [
                'msg' => 'No data generated in this report.',
                'status' => 'failed',
                'successModal' => false
            ];
        }

        return $data;
    }

    public function getYPICSR3datatable()
    {
        $data = DB::connection($this->mysql)->table('order_data_report')
                    ->where('orderquantity','<>',0)
                    ->select([
                        'id',
                        'salesno',
                        'salestype',
                        'salesorg',
                        'commercial',
                        'section',
                        'salesbranch',
                        'salesg',
                        'supplier',
                        'destination',
                        'payer',
                        'assistant',
                        'purchaseorderno',
                        'issuedate',
                        'flightneeddate',
                        'headertext',
                        'code',
                        'itemtext',
                        'orderquantity',
                        'unit',
                        'dbcon'
                    ]);
        return Datatables::of($data)->make(true);
    }

    private function getSupplier($val)
    {
        return $val;
    }

    /**
    * Export Order Data Report to Excel.
    **/
    public function printOrderDataReport(Request $req)
    {
        $common = new CommonController;
        # get the selected supplier and db connection.
        $supplier = str_replace(' ', '', $req->selected_supplier);
        $dbconnection = str_replace(' ', '', $req->selected_dbconnect);
        $con = '';
        $data = array();

        switch ($dbconnection) {
            case 'TS':
                $con = 'BU2';
                break;
            case 'CN':
                $con = 'BU1';
                break;
            case 'YF':
                $con = 'CONNECTORS';
                break;
            
            default:
                $con = 'BU2';
                break;
        }
        
        // # convert the object result to array readable format.
        // foreach ($order_data_report as $orderdatareport) 
        // {
        //     $data[] = (array)$orderdatareport;
        //     #or first convert it and then change its properties using 
        //     #an array syntax, it's up to you
        // }

        # Create and export excel by feeding the array result.
        
        //return $dbconnection;
        
        $dt = Carbon::now();
        $date = $dt->format('m-d-Y');
        Excel::create('PMI_PO_'.$con.'_'.$supplier.'_'.$date, function($excel)
        {
            $excel->sheet('OrderDataReport', function($sheet)
            {
                $sheet->cell('A1', "salesno");
                $sheet->cell('B1', "salestype");
                $sheet->cell('C1', "salesorg");
                $sheet->cell('D1', "commercial");
                $sheet->cell('E1', "section");
                $sheet->cell('F1', "salesbranch");
                $sheet->cell('G1', "salesg");
                $sheet->cell('H1', "supplier");
                $sheet->cell('I1', "destination");
                $sheet->cell('J1', "payer");
                $sheet->cell('K1', "assistant");
                $sheet->cell('L1', "purchaseorderno");
                $sheet->cell('M1', "issuedate");
                $sheet->cell('N1', "flightneeddate");
                $sheet->cell('O1', "headertext");
                $sheet->cell('P1', "code");
                $sheet->cell('Q1', "itemtext");
                $sheet->cell('R1', "orderquantity");
                $sheet->cell('S1', "unit");

                $row = 2;
                $order_data_report = $this->getOrderDataReportDB();
                foreach ($order_data_report as $key => $r3) {
                    $sheet->cell('A'.$row, $r3->salesno);
                    $sheet->cell('B'.$row, $r3->salestype);
                    $sheet->cell('C'.$row, $r3->salesorg);
                    $sheet->cell('D'.$row, $r3->commercial);
                    $sheet->cell('E'.$row, $r3->section);
                    $sheet->cell('F'.$row, $r3->salesbranch);
                    $sheet->cell('G'.$row, $r3->salesg);
                    $sheet->cell('H'.$row, $r3->supplier);
                    $sheet->cell('I'.$row, $r3->destination);
                    $sheet->cell('J'.$row, $r3->payer);
                    $sheet->cell('K'.$row, $r3->assistant);
                    $sheet->cell('L'.$row, $r3->purchaseorderno);
                    $sheet->cell('M'.$row, substr($r3->issuedate, 0,8));
                    $sheet->cell('N'.$row, substr($r3->flightneeddate, 0,8));
                    $sheet->cell('O'.$row, $r3->headertext);
                    $sheet->cell('P'.$row, $r3->code);
                    $sheet->cell('Q'.$row, "");
                    $sheet->cell('R'.$row, $r3->orderquantity);
                    $sheet->cell('S'.$row, $r3->unit);

                    $sheet->cell('U'.$row, $r3->itemtext);
                    $row++;
                }

            });

        })->download('xls');
    }

    /**
    * Start or Stop using YPICS.
    **/
    public function startStopUsingYpics(Request $req)
    {
        // $data = [
        //     'msg' => 'Switching failed.',
        //     'status' => 'failed'
        // ];
        // # retrieve the action (START/STOP) and database connection.
        // $action = $req->action;

        // $ckeys = json_decode($req->ckey);
        // $intvals = json_decode($req->intval);
        // $mrpusers;
        // $result = false;
        // $nodata = false;
        // $schema = '';
        // $message = '';

        // if ($action == 'START')
        // {
        //     # get previously stop mrp users.
        //     $ypics = $this->getYpics($this->mssql);

        //     foreach ($ypics as $key => $x) {
        //         # update intval to start the YPICS.
        //         # to start the YPICS by setting 1 to intval, set $x['intval'] to 1.
        //         $result = $this->updateYpics($x->database, 0, 1, $x->ckey, $x->intval, $schema);
        //         if($result)
        //         {
        //             # delete the temporary YPICS.
        //             $result = $this->deleteYpics($x->database, $x->ckey);
        //         }
        //     }
        //     $data = [
        //         'msg' => 'MRP Users was successfully started.',
        //         'status' => 'success'
        //     ];
        // }

        // if ($action == 'STOP')
        // { 
        //     if (count($ckeys) < 1 || count($intvals) < 1) {
        //         $data = [
        //             'msg' => 'No users using YPICS.',
        //             'status' => 'failed'
        //         ];
        //     } else {
        //         # combine the ckey and intval values in one array.
        //         $ctr=0;
        //         foreach ($ckeys as $ckey => $ckeyvalue) {

        //             $mrpusers[$ctr][0] = $ckeyvalue;
        //             $ctr++;

        //         }
        //         $ctr=0;
        //         foreach ($intvals as $intval => $intvalvalue) {

        //             $mrpusers[$ctr][1] = $intvalvalue;
        //             $ctr++;

        //         }

        //         # backup mrp user data
        //         foreach ($mrpusers as $mrpuser => $mrpuservalue) {
        //             # insert temporary YPICS to sqlsrv.
        //             $result = $this->insertYpics($this->mssql, $mrpuservalue[0], (isset($mrpuservalue[1]))? $mrpuservalue[1] : $mrpuservalue[0]);
        //             if($result)
        //             {
        //                 # update the intval to 0 to stop the YPICS.
        //                 $result = $this->updateYpics($this->mssql, 1, 0, $mrpuservalue[0], 0, $schema);
        //             }
        //         }
        //         $data = [
        //             'msg' => 'YPICS Users was successfully stoped.',
        //             'status' => 'success'
        //         ];
        //    }
        // }

        // return $data;
    }

    /**
    * Get temporary YPICS to start.
    **/
    private function getYpics($db)
    {
        $result = false;
        try
        {
            # get temporary ypics
            $result = DB::connection($this->mysql)->table('xcont')
                        ->where("database", "=", $db)
                        ->get();
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }

        return $result;
    }

    /**
    * Edit the YPICS to start or stop.
    **/
    private function updateYpics($db, $from, $to, $ckey, $intval, $schema)
    {
        $result = false;
        try
        {
            // if($intval > 1)
            // {
            //     $to = $intval;
            // }
            # update intval from $from.value to $to.value
            $result = DB::connection($this->mssql)
                        ->table('XCONT')
                        ->where('ckey', $ckey)
                        ->update(['intval' => $to]);
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
        return $result;
    }

    /**
    * Insert temporary YPICS to sqlsrv.
    **/
    private function insertYpics($db, $ckey, $intval)
    {
        $result = false;
        try
        {
            # insert intval , ckey, databse
            $result = DB::connection($this->mysql)->table('xcont')
            ->insert(
                ['ckey' => $ckey, 
                'intval' => $intval,
                'database' => $db,
                'updated_at' =>date("Y/m/d h:i:sa"),
                'created_at' =>date("Y/m/d h:i:sa")
                ]);
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
        return $result;
    }

    /**
    * Delete temporary YPICS from sqlsrv.
    **/
    private function deleteYpics($db, $ckey)
    {
        $result = false;
        try
        {
            # insert intval , ckey, databse
            $result = DB::connection($this->mysql)->table('xcont')
            ->where('ckey', '=',$ckey)
            ->where('database', '=',$db)
            ->delete();
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
        return $result;
    }

    private function insertToOrderDataReport($data,$dbcon,$inputdate)
    {
        $params = [];
        foreach ($data as $key => $ypics) {
            array_push($params, [
                'salesno' => $ypics->salesno,
                'salestype' => $ypics->salestype,
                'salesorg' => $ypics->salesorg,
                'commercial' => $ypics->commercial,
                'section' => $ypics->section,
                'salesbranch' => $ypics->salesbranch,
                'salesg' => $ypics->salesg,
                'supplier' => $this->getCust($ypics->supplier,$dbcon),
                'destination' => $ypics->destination,
                'payer' => $ypics->payer,
                'assistant' => $ypics->assistant,
                'purchaseorderno' => $ypics->purchaseorderno,
                'issuedate' => '20'.$inputdate,
                'flightneeddate' => $ypics->flightneeddate,
                'headertext' => $ypics->headertext,
                'code' => $ypics->code,
                'itemtext' => $ypics->itemtext,
                'orderquantity' => $ypics->orderquantity,
                'unit' => $ypics->unit,
                'dbcon' => $dbcon
            ]);
        }
        DB::connection($this->mysql)->table('order_data_report')->insert($params);
    }

    private function getOrderDataReportDB()
    {
        $data = DB::connection($this->mysql)->table('order_data_report')
                    ->where('orderquantity','<>',0)
                    ->get();
        return $data;
    }

    private function getCust($supplier,$dbcon)
    {
        if ($supplier == 'PPD' || $supplier == 'PPS') {
            return 'PPD';
        }
        switch ($dbcon) {
            case 'TS':
                return 'Y016000';
                break;
            case 'CN':
                return 'Y016006';
                break;
            case 'YF':
                return 'Y016003';
                break;
            
            default:
                return 'Y016000';
                break;
        }
    }

}