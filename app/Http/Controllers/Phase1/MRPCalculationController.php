<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: MRPCalculationController.php
     MODULE NAME:  [3007] MRP CALCULATION
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2016.05.17
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.17     AK.DELAROSA     Initial Draft
     100-00-02   1     2016.05.24     MESPINOSA       Continue the development.
     100-00-03   1     2016.10.12     AKDELAROSA      Debug whole module
     200-00-00   1     2016.11.22     AKDELAROSA      Recode Module
*******************************************************************************/
?>
<?php
namespace App\Http\Controllers\Phase1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Carbon\Carbon;
use Excel;
use Config;
use DB;

/**
* MRP Calculation Controller
*/
class MRPCalculationController extends Controller
{

    protected $mysql;
    protected $mssql;
    protected $common;

    /**
    * MRP Calculation Controller Constructor
    **/
    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'mrp');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    /**
    * MRP Calculation initial page load.
    **/
    public function getMRP()
    {
        $common = new CommonController;

        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_MRP'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $sss = DB::connection($this->common)->table('muserprograms as U')
                                    ->join('mprograms as P', 'P.program_code', '=', 'U.program_code')
                                    ->select('P.program_name', 'U.program_code','U.user_id','U.read_write')
                                    ->where('U.user_id', Auth::user()->user_id)
                                    ->where('U.delete_flag', 0)
                                    ->where('P.program_class','SSS')
                                    ->orderBy('U.id','asc')->get();

            $wbs = DB::connection($this->common)->table('muserprograms as U')
                                    ->join('mprograms as P', 'P.program_code', '=', 'U.program_code')
                                    ->select('P.program_name', 'U.program_code','U.user_id','U.read_write')
                                    ->where('U.user_id', Auth::user()->user_id)
                                    ->where('U.delete_flag', 0)
                                    ->where('P.program_class','WBS')
                                    ->orderBy('U.id','asc')->get();
            $qcdb = DB::connection($this->common)->table('muserprograms as U')
                                    ->join('mprograms as P', 'P.program_code', '=', 'U.program_code')
                                    ->select('P.program_name', 'U.program_code','U.user_id','U.read_write')
                                    ->where('U.user_id', Auth::user()->user_id)
                                    ->where('U.delete_flag', 0)
                                    ->where('P.program_class','QCDB')
                                    ->orderBy('U.id','asc')->get();

            $qcmld = DB::connection($this->common)->table('muserprograms as U')
                                    ->join('mprograms as P', 'P.program_code', '=', 'U.program_code')
                                    ->select('P.program_name', 'U.program_code','U.user_id','U.read_write')
                                    ->where('U.user_id', Auth::user()->user_id)
                                    ->where('U.delete_flag', 0)
                                    ->where('P.program_class','QCMLD')
                                    ->orderBy('U.id','asc')->get();
            return view('phase1.mrpCalculation',['userProgramAccess' => $userProgramAccess,'ssss' => $sss,
                'wbss' => $wbs,
                'qcdbs' => $qcdb,
                'qcmlds' => $qcmld]);
        }
    }

    /**
    * MRP Calculation page reload.
    **/
    public function postReadFiles(Request $request)
    {
        $msg_type = 'Success';
        $pps_result = 0;
        $inv_result = 0;
        $pps = [];
        $invoice = [];

        try 
        {
            ini_set('max_execution_time', 0);
            $files = array(
                      'partsdata' => $request->file('partsdata'),
                      'ppsdata' => $request->file('ppsdata'),
                      'invoicedata' => $request->file('invoicedata')
                    );

            # check if the uploaded Parts File is valid.
            if($this->isPartsFileValid($files['partsdata'], $message, $db, $schema))
            {
                $pps_result = $this->isPpsFileValid($files['ppsdata'], $message);
                # check if the uploaded PPS File is valid.
                if( $pps_result == 1) #PPS File is valid.
                {
                    # read PPS File records.
                    $pps = $this->readPPS($files['ppsdata']);
                    //return dd($pps);
                }
                elseif ($pps_result < 0) #PPS file is invalid.
                {
                    $msg_type = 'err_message';
                    return redirect(url('/mrpcalculation'))->with([$msg_type => $message]);
                }
                
                $inv_result = $this->isInvoiceFileValid($files['invoicedata'], $message);
                
                # check if the uploaded Invoice File is valid.
                if($inv_result == 1) #Invoice File is valid
                {
                    # read Invoice File records.
                    $invoice = $this->readInvoice($files['invoicedata']);
                }
                elseif($inv_result < 0) #Invoice File is invalid
                {
                    $msg_type = 'err_message';
                    return redirect(url('/mrpcalculation'))->with([$msg_type => $message]);
                }

                # create temp_MRP
                $this->createTempMrp($db, $schema);

                # create temp_mrp_prbalance
                $this->createTempPrBalance($db, $schema);

                # create temp_mrp_ordbalance
                $this->createTempOrdBalance($db, $schema);

                # read & insert MRP Data.
                $this->readParts($files['partsdata'], $pps, $invoice, $db, $schema);

                # create temp_mrp_check and temp_mrp_xitem.
                $this->createTempCheck($db, $schema);

                # export MRP Data to excel.
                //$this->exportMrpDataToExcel();

                $message = 'MRP Calculation is done. MRP Ouput is extracted and ready for download.';
            }
            else
            {
                $msg_type = 'Failed';
                $message = "Process failed.";
            }
        } 
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return redirect(url('/mrpcalculation'))->with(['msg' => $message, 'msg_type'=>$msg_type]);
    }
    
    /**
    * Check Parts File if valid.
    **/
    private function isPartsFileValid($parts, &$message, &$db, &$schema)
    {
        $result = false;
        $db = Config::get('constants.DB_SQLSRV_BU');
        $schema = Config::get('constants.DB_SHCEMA_BU');

        try 
        {
            if($parts != null)
            {
                $partsName = $parts->getClientOriginalName();
                $partsExt = $parts->getClientOriginalExtension();

                if ($partsExt != 'txt') 
                {
                    $message = "Parts Answer Data must be a text format.";
                }
                else 
                {
                    # check if the filename contains 'ZYPF0150'.
                    if (is_numeric(strpos($partsName,"ZYPF0150"))) 
                    {
                        # check if the file is from TS database.
                        if(is_numeric(strpos($partsName,"TS")))
                        {
                            # connects to Probe DB -> DB_SQLSRV_TS.
                            $db = Config::get('constants.DB_SQLSRV_BU');
                            $schema = Config::get('constants.DB_SHCEMA_BU');
                        }
                        # check if the file is from BU database.
                        elseif(is_numeric(strpos($partsName,"BU")))
                        {
                            # connects to BU DB -> DB_SQLSRV_BU.
                            $db = Config::get('constants.DB_SQLSRV_BU');
                            $schema = Config::get('constants.DB_SHCEMA_BU');
                        }
                        # check if the file is from CN database.
                        elseif(is_numeric(strpos($partsName,"CN")))
                        {
                            # connects to CN DB -> DB_SQLSRV_CN.
                            $db = Config::get('constants.DB_SQLSRV_CN');
                            $schema = Config::get('constants.DB_SHCEMA_CN');
                        }
                        # check if the file is from YF database.
                        elseif(is_numeric(strpos($partsName,"YF")))
                        {
                            # connects to YF DB -> DB_SQLSRV_YF.
                            $db = Config::get('constants.DB_SQLSRV_YF');
                            $schema = Config::get('constants.DB_SHCEMA_YF');
                        }
                        $result = true;
                    }
                    else
                    {
                        $message = "Parts Answer Data file is invalid.";
                    }
                }
            }
            else
            {
                $message = "Please upload necessary files.";
            }
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Check PPS File if valid.
    **/
    private function isPpsFileValid($pps, &$message)
    {
        $result = 0;

        try {

            if($pps != null)
            {
                //getting file names and extension
                $ppsName = $pps->getClientOriginalName();
                $ppsExt = $pps->getClientOriginalExtension();

                # PPS File must be in excel format.
                if ($ppsExt == 'xlsx' || $ppsExt == 'xls' || $ppsExt == 'XLS') 
                {
                    # PPS file must contain 'PPS' in filename.
                    // if (is_numeric(strpos($ppsName,"PPS"))) 
                    // {
                    //     $result = 1;
                    // }
                    // else
                    // {
                    //     $message = "PPS file is invalid.";
                    //     $result = -1;
                    // }
                    $result = 1;
                }
                else
                {
                    $message = "PPS must be in excel format.";
                    $result = -1;
                }
            }
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Check Invoice File if valid.
    **/
    private function isInvoiceFileValid($invoice, &$message)
    {
        $result = 0;

        try {

            # check if user upload an invoice file.
            if($invoice != null)
            {
                //getting file names and extension
                $invoiceName = $invoice->getClientOriginalName();
                $invoiceExt = $invoice->getClientOriginalExtension();

                # Invoice file must be in txt format.
                if ($invoiceExt != 'txt') 
                {
                    $message = "Invoice file must be in text format.";
                    $result = -1;
                }
                else
                {
                    # Invoice file must contain txt in filename
                    if (is_numeric(strpos($invoiceName,"txt"))) 
                    {
                        $result = 1;
                    }
                    else
                    {
                        $message = "Invoice file is invalid.";
                        $result = -1;
                    }
                }
            }
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Read Parts File content.
    **/
    private function readParts($parts, $pps, $invoice, $db, $schema)
    {
        ini_set('MAX_EXECUTION_TIME', -1);
        try 
        {
            # get the Parts file contents.
            $row = explode(PHP_EOL, file_get_contents($parts));
            $keys = array_keys($row);
            $cnt = 0;

            # delete all data from temp_mrp_info table.
            $this->truncateTable('temp_mrp_info');
            # delete all mrp data from tbl_mrp_zypf0150 table.
            $this->truncateTable('tbl_mrp_zypf0150');
            # delete all mrp data from temp_mrp_zypf0150 table.
            $this->truncateTable('temp_mrp_zypf0150');

            # loop the Part file contents.
            for ($i=1; $i < count($keys); $i++) 
            {
                $key = $keys[$i];
                $content = $row[$key];
                $data = array_filter(array_map("trim", explode("\t", $content)));

                # check if each row has contents.
                if (isset($data[0])) 
                {
                    # Customer order number
                    if (!isset($data[0])) { $data[0] = ""; }
                    # Answer delivery time
                    if (!isset($data[1])) { $data[1] = "";}
                    # The number of payment
                    if (!isset($data[2])) { $data[2] = ""; }
                    # Item code
                    if (!isset($data[3])) { $data[3] = ""; }
                    # Item text
                    if (!isset($data[4])) { $data[4] = ""; }
                    # Sales Slip Number
                    if (!isset($data[5])) { $data[5] = ""; }
                    # Sales document item number
                    if (!isset($data[6])) { $data[6] = ""; }
                    # Specified delivery date
                    if (!isset($data[7])) { $data[7] = ""; }
                    # Purchasing document number
                    if (!isset($data[8])) { $data[8] = ""; }
                    # Purchasing document item number
                    if (!isset($data[9])) { $data[9] = ""; }
                    # Item Code (Product)
                    if (!isset($data[12])) { $data[12] = ""; }
                    # Item Text (Product)
                    if (!isset($data[13])) { $data[13] = ""; }
                    # Purchasing document number (Product)
                    if (!isset($data[14])) { $data[14] = ""; }
                    # Purchasing document item number (Product)
                    if (!isset($data[15])) { $data[15] = ""; }
                    # Purchasing document date (Product)
                    if (!isset($data[16])) { $data[16] = ""; }
                    # Purchase order quantity (Product)
                    if (!isset($data[17])) { $data[17] = ""; }
                    # Order unit (Product)
                    if (!isset($data[18])) { $data[18] = ""; }
                    # Sales document number (Product)
                    if (!isset($data[19])) { $data[19] = ""; }
                     # Sales document item number (Product)
                    if (!isset($data[20])) { $data[20] = ""; }
                    # Customer specified delivery date (Product)
                    if (!isset($data[21])) { $data[21] = ""; }
                    # Vendor Code
                    if (!isset($data[22])) { $data[22] = ""; }
                    # Company Name
                    if (!isset($data[23])) { $data[23] = ""; }
                    # Delay Reason
                    if (!isset($data[24])) { $data[24] = ""; }

                    // echo '<pre>',print_r($data),'</pre>';
                    $result_mrp = $this->insertMrpData($data, $db, $schema);
                }
            }
            

            # delete all data from temp_prodanswer_data table.
            // DB::connection($this->mysql)->table('temp_prodanswer_data')->delete();
            # delete all data from tbl_mrp_pps table.
            $this->truncateTable('tbl_mrp_pps');
            $this->truncateTable('temp_mrp_ppsanswer');

            # insert each PPS data to mysql DB.
            foreach ($pps as $pps_key => $pps_value) 
            {
                # insert PPS Data
                # If necessary perform UPDATE in MRP data.
                
                if($pps_value['itemcode'] != NULL && $pps_value['orderno'] != NULL) //
                {
                    $result_pps = $this->insertPpsData($pps_value);
                }
            }

            # delete all data from temp_prodanswer_data table.
            //DB::connection($this->mysql)->table('temp_prodanswer_data')->delete(); #For Q&A.
            # delete all data from tbl_mrp_invoice table.
            $this->truncateTable('tbl_mrp_invoice');
            $this->truncateTable('temp_mrp_invoice');

            # insert each Invoice data to mysql DB.
            foreach ($invoice as $inv_key => $inv_value) 
            {
                # A valid invoice file has 7 columns.
                if(count($inv_value) == 7)
                {
                    # insert Invoice Data
                    # If necessary perform UPDATE in MRP data.
                    $result_inv = $this->insertInvoiceData($inv_value);
                }
            }

        } 
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }
        
    }

    /**
    * Read PPS File content.
    **/
    private function readPPS($pps)
    {
        $result;

        try 
        {
            # read PPS file contents using Laravel Excel library.
            Excel::load($pps, function ($reader) use(&$result)
            {
                $result = $reader->toArray();
            });
        } 
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Read Invoice File content.
    **/
    private function readInvoice($invoice)
    {
        $row = explode(PHP_EOL, file_get_contents($invoice));
        $keys = array_keys($row);

        $invoice_arr = [];

        // pinching the txt file by pieces
        for ($i=1; $i < count($keys); $i++) 
        {
            $key = $keys[$i];
            $content = $row[$key];
            $data = array_filter(array_map("trim", explode("\t", $content)));

            # check if the row has data.
            if (isset($data[0])) 
            {
                $data[0] = trim($data[0],'"');
                $data[1] = trim($data[1],'"');
                $data[2] = trim($data[2],'"');
                $data[3] = trim($data[3],'"');
                $data[4] = trim($data[4],'"');
                $data[5] = trim($data[5],'"');
                $data[6] = trim($data[6],'"');
            }

            array_push($invoice_arr, $data);
        }

        return ($invoice_arr);
    }

    /**
    * Retrieve necessary data from YPICS DB.
    **/
    private function getDataFromYpics($code, $pr, $db, $schema)
    {
        $data[] = NULL;

        try
        {
            # connect to related DB connection.
            $result = DB::connection($db)
            ->table($schema . 'XPRTS as p')
            ->join($schema . 'XHEAD as h', 'h.CODE', '=', 'p.KCODE')
            ->join($schema . 'XITEM as i', 'i.CODE', '=', 'h.CODE')
            ->join($schema . 'XSLIP as s', 's.CODE', '=', 'h.CODE')
            ->join(DB::raw("(SELECT z.CODE
                                , ISNULL(za1.ZAIK,0) as ASSY100
                                , ISNULL(za2.ZAIK,0) as ASSY102
                                , ISNULL(z1.ZAIK,0) as WHS100
                                , ISNULL(z2.ZAIK,0) as WHS102
                                , ISNULL(z3.ZAIK,0) as WHS106
                                , ISNULL(z4.ZAIK,0) as 'WHS_SM'
                                , ISNULL(z5.ZAIK,0) as 'WHS_NON'
                            FROM " . $schema . "XZAIK z 
                            LEFT JOIN " . $schema . 
                                "XZAIK za1 ON za1.CODE = z.CODE AND za1.HOKAN = 'ASSY100'
                            LEFT JOIN " . $schema . 
                                "XZAIK za2 ON za2.CODE = z.CODE AND za2.HOKAN = 'ASSY102'
                            LEFT JOIN " . $schema . 
                                "XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
                            LEFT JOIN " . $schema . 
                                "XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS102'
                            LEFT JOIN " . $schema . 
                                "XZAIK z3 ON z3.CODE = z.CODE AND z3.HOKAN = 'WHS106'
                            LEFT JOIN " . $schema . 
                                "XZAIK z4 ON z4.CODE = z.CODE AND z4.HOKAN = 'WHS-SM'
                            LEFT JOIN " . $schema . 
                                "XZAIK z5 ON z5.CODE = z.CODE AND z5.HOKAN = 'WHS-NON'
                            GROUP BY z.CODE
                                , za1.ZAIK
                                , za2.ZAIK
                                , z1.ZAIK
                                , z2.ZAIK
                                , z3.ZAIK
                                , z4.ZAIK
                                , z5.ZAIK
                            ) as z"), "z.CODE", "=" ,"h.CODE")
            ->select(
                's.PORDER AS PO'
                , 'h.CODE AS HCODE'
                , 'p.CODE AS PCODE'
                , 'p.KCODE'
                , 'h.NAME as PNAME'
                , 'i.VENDOR'
                , 'z.ASSY100'
                , 'z.ASSY102'
                , 'z.WHS100'
                , 'z.WHS102'
                , 'z.WHS106'
                , DB::raw("z.WHS_SM AS 'WHS-SM' ")
                , DB::raw("z.WHS_NON AS 'WHS-NON' ")
                , DB::raw("ISNULL(z.ASSY100 + z.ASSY102 + z.WHS100 + z.WHS102 
                        + z.WHS106 + z.WHS_SM + z.WHS_NON,0)  AS TOTAL")
                , 's.WVOL'
                , 's.KVOL'
                , 's.TJITU'
                , DB::raw("s.KVOL - s.TJITU AS TOTAL_BAL_REQ")
                , DB::raw("ROUND(CAST((p.SIYOU * 20) AS FLOAT),2) as POREQ")
                , DB::raw("ROUND(CAST((p.SIYOU * 20) AS FLOAT),2) as POBAL")
                , DB::raw("0 as DELIQTY")
                , DB::raw("0 as DELIQCCUM")
                , DB::raw("'' as 'CHECK'")
                , DB::raw("ISNULL(s.APPROVEKEY, '') as SUP_CODE")
                , DB::raw("ISNULL(s.APPROVER, '') as SUP_NAME")
                , DB::raw("s.INPUTDATE AS 'iDATE'")
                )
            ->where('p.KCODE', '=',$code)
            ->where('s.PORDER', 'like', $pr . '%')
            ->get();

            # convert the object result to array readable format.
            foreach ($result as $ypics) 
            {
                $data[] = (array)$ypics;
                #or first convert it and then change its properties using 
                #an array syntax, it's up to you
            }

        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $data;
    }

    /**
    * Insert data to temp_mrp_info table.
    **/
    private function insertMrpData($data, $db, $schema)
    {
        $result = false;

        try
        {
            /*if($data[3] == NULL //mcode
                || $data[4] == NULL //mname
                || $data[8] == NULL //PO
                || $data[9] == NULL //PO
                || $data[12] == NULL //dcode
                || $data[13] == NULL //dname
                || $data[0] == NULL) //PR
            {
                # if one of the required data is null do not insert data.
                $result = true;
            }
            else
            {
                #retrieve data from YPICS DB.
                $ypics = $this->getDataFromYpics($data[3], substr($data[0], 0, strpos($data[0], '-', 0)), $db, $schema);
                
                # get the second item in array output.
                if(count($ypics)>1)
                {
                    $ypics = $ypics[1];
                }

                # array must contain atleast 25 columns.
                if (count($ypics) >= 26)
                {
                    # insert Parts data to temp_mrp_info for SSS module.
                    $result = DB::connection($this->mysql)->table('temp_mrp_info')
                    ->insert([
                            'mcode'          => $data[3],  //Itemcode
                            'mname'          => $data[4],  //Itemtext
                            'vendor'         => ($ypics['VENDOR'] == 0)?"0.0":$ypics['VENDOR'], //VendorCode
                            'assy100'        => ($ypics['ASSY100'] == 0)?"0.0":$ypics['ASSY100'], 
                            'assy102'        => ($ypics['ASSY102'] == 0)?"0.0":$ypics['ASSY102'],
                            'whs100'         => ($ypics['WHS100'] == 0)?"0.0":$ypics['WHS100'],
                            'whs102'         => ($ypics['WHS102'] == 0)?"0.0":$ypics['WHS102'],
                            'whs106'         => ($ypics['WHS106'] == 0)?"0.0":$ypics['WHS106'],
                            'whs_sm'         => ($ypics['WHS-SM'] == 0)?"0.0":$ypics['WHS-SM'],
                            'whs_non'        => ($ypics['WHS-NON'] == 0)?"0.0":$ypics['WHS-NON'],
                            'total_curr_inv' => ($ypics['TOTAL'] == 0)?"0.0":$ypics['TOTAL'],
                            'order_date'     => $data[16], //Purchasingdocumentdate(Product)
                            'due_date'       => $data[7],  //Specifieddeliverydate
                            'po'             => $data[8] . $data[9],  //Customerordernumber
                            'dcode'          => $data[12], //ItemCode(Product)
                            'dname'          => $data[13], //ItemText(Product)
                            'order_qty'      => ($data[17] == 0)?"0.0":$data[17], //Purchaseorderquantity(Product)
                            'order_bal'      => ($ypics['WVOL'] == 0)?"0.0":$ypics['WVOL'],
                            'cust_code'      => $data[22], // Company Name
                            'cust_name'      => mb_convert_encoding($data[23], 'UTF-8', 'Shift-JIS'), // Company Name
                            'sched_qty'      => ($ypics['KVOL'] == 0)?"0.0":$ypics['KVOL'],
                            'balance_req'    => ($ypics['TJITU'] == 0)?"0.0":$ypics['TJITU'],
                            'total_bal_req'  => ($ypics['TOTAL_BAL_REQ'] == 0)?"0.0":$ypics['TOTAL_BAL_REQ'], //KVOL - TJITU
                            'req_accum'      => '',
                            'allocation_calc'=> '',
                            'total_pr_bal'   => '',
                            'mrp'            => '',
                            'pr_issued'      => '',//$data[25], //inputdate
                            'pr'             => substr($data[0],0,stripos($data[0], '-')),
                            'yec_po'         => '',
                            'yec_pu'         => '',
                            'flight'         => '',
                            'deli_qty'       => '',
                            'deliaccum'      => '',
                            'check'          => '', // 'FromStock' or 'Allocation' or value #please follow the correct caption, to avoid case sensitivity
                            'sup_code'       => $ypics['SUP_CODE'],
                            'sup_name'       => $ypics['SUP_NAME'],
                            're'             => $data[24], //DelayReason
                            'status'         => '',
                            'created_at'     => date("Y/m/d h:i:sa"),
                            'updated_at'     => date("Y/m/d h:i:sa")
                            ]);
                }
            }*/

            if(strpos($data[0],'-') > 0)
            {
                $pr = substr($data[0], 0, strpos($data[0],'-'));
            }
            else
            {
                $pr = $data[0];
            }
            # insert Part data to temp_mrp_zypf0150.
            // $result = DB::connection($this->mysql)->table('temp_mrp_zypf0150')
            //     ->insert([
            //         'pr'         => $pr,
            //         'grdate'     => $data[1],
            //         'qty'        => $data[17],
            //         'mcode'      => $data[3],
            //         'mname'      => $data[4],
            //         'fltneeded'  => $data[7], //For Q&A
            //         'supcode'    => $data[22],
            //         'supname'    => mb_convert_encoding($data[23], 'UTF-8', 'Shift-JIS'),
            //         'reasoncode' => mb_convert_encoding($data[24], 'UTF-8', 'Shift-JIS'),
            //         'yecpo'      => $data[14]
            //         ]);

            # insert Part data to tbl_mrp_zypf0150 for reference.
            $result = DB::connection($this->mysql)->table('tbl_mrp_zypf0150')
                ->insert([
                    'pr'                  => $data[0],
                    'response_del_time'   => $data[1],
                    'payment_no'          => $data[2],
                    'mcode'               => $data[3],
                    'mname'               => $data[4],
                    'slip_no'             => $data[5],
                    'doc_item_no'         => $data[6],
                    'expected_del_date'   => $data[7],
                    'po_doc_no'           => $data[8],
                    'po_doc_item_no'      => $data[9],
                    'reorder_qty'         => $data[10],
                    'unit'                => $data[11],
                    'dcode'               => $data[12],
                    'dname'               => $data[13],
                    'p_po_doc_no'         => $data[14],
                    'p_po_doc_item_no'    => $data[15],
                    'p_po_doc_date'       => $data[16],
                    'p_po_order_qty'      => $data[17],
                    'p_order_qty'         => $data[18],
                    'p_doc_no'            => $data[19],
                    'p_doc_item_no'       => $data[20],
                    'p_expected_del_date' => $data[21],
                    'vendor'              => $data[22],
                    'cust_name'           => mb_convert_encoding($data[23], 'UTF-8', 'Shift-JIS'),
                    're'                  => mb_convert_encoding($data[24], 'UTF-8', 'Shift-JIS'),
                    'created_at'          => date("Y/m/d h:i:sa"),
                    'updated_at'          => date("Y/m/d h:i:sa")
                    ]);
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Insert data to temp_prodanswer_data table.
    **/
    private function insertPpsData($data)
    {
        $result = false;
        $remarks = isset($data['remarks'])? $data['remarks']:'';

        try
        {
            # remarks columns cannot be NULL.
            if($remarks == NULL)
            {
                # set to empty string.
                $remarks = '';
            }

            if($data['ppd_reply'] == Null)
            {
                $time = '';
            }
            else
            {
                $time = $data['ppd_reply']->format('Hi');
            }

            # insert PPS data to temp_prodanswer_data for SSS module.
            $result = DB::connection($this->mysql)->table('temp_prodanswer_data')
            ->insert([
                    'pcode'     => $data['itemcode'],
                    'pname'      => $data['name'],
                    'po'         => $data['orderno'],
                    'qty'        => $data['schdqty'],
                    'r3answer'   => $data['ppd_reply'],
                    'time'       => $time,
                    're'         => $remarks,
                    'created_at' => date("Y/m/d h:i:sa"),
                    'updated_at' => date("Y/m/d h:i:sa")
                    ]);

            if(strpos($data['orderno'],'-') > 0)
            {
                $pr = substr($data['orderno'], 0, strpos($data['orderno'],'-'));
            }
            else
            {
                $pr = $data['orderno'];
            }
            # insert PPS data to temp_mrp_ppsanswer for reference.
            $result = DB::connection($this->mysql)->table('temp_mrp_ppsanswer')
            ->insert([
                    'orderno'  => $pr,
                    'ppdreply' => $data['ppd_reply'],
                    'schdqty'  => $data['schdqty'],
                    'itemcode' => $data['itemcode'],
                    'name'     => $data['name'],
                    'remarks'  => $remarks
                    ]);

            // # insert PPS data to tbl_mrp_pps for reference.
            /*$result = DB::connection($this->mysql)->table('tbl_mrp_pps')
            ->insert([
                    'code'       => $data['itemcode'],
                    'name'       => $data['name'],
                    'order_no'   => $data['orderno'],
                    'sched_qty'  => $data['schdqty'],
                    'ppdr_reply' => $data['ppd_reply'],
                    're'         => $data['remarks'],
                    'created_at' => date("Y/m/d h:i:sa"),
                    'updated_at' => date("Y/m/d h:i:sa")
                    ]);*/

        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }
        return $result;
    }

    /**
    * Insert data to <invoice> table.
    **/
    private function insertInvoiceData($data)
    {
        $result = false;

        try
        {
            if(count($data) > 0)
            {
                if(strpos($data[5],'-') > 0)
                {
                    $pr = substr($data[5], 0, strpos($data[5],'-'));
                }
                else
                {
                    $pr = $data[5];
                }
                # insert Invoice data to temp_mrp_invoice.
                $result = DB::connection($this->mysql)->table('temp_mrp_invoice')
                ->insert([
                    'invoiceno' => $data[0],
                    'fltdate'   => $data[1],
                    'mcode'     => $data[2],
                    'mname'     => $data[3],
                    'qty'       => $data[4],
                    'pr'        => $data[5],
                    'price'     => $data[6]]);

                # insert Invoice data to tbl_mrp_invoice.
                /*$result = DB::connection($this->mysql)->table('tbl_mrp_invoice')
                ->insert([
                        'no'         => $data[0],
                        'flight'     => $data[1],
                        'pcode'      => $data[2],
                        'pname'      => $data[3],
                        'qty'        => $data[4],
                        'podata'     => $data[5],
                        'unit_price' => $data[6],
                        'created_at' => date("Y/m/d h:i:sa"),
                        'updated_at' => date("Y/m/d h:i:sa")
                        ]);*/

            }
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
        }
        return $result;
    }

        /**
        * Export MRP data to Excel.
        **/
    /* private function exportMrpData()
    {
        $dt = Carbon::now();
        $date = substr($dt->format('Y-m-d'), 2);
        $data = array();
        $data2 = array();
        $path = public_path() . '/MRP_data_files_SSS';

        # retrieve data from temp_mrp_info.
        $result = DB::connection($this->mysql)->table('temp_mrp_info')
        ->select('mcode AS MCode',
                'mname AS MName',
                'vendor AS VENDOR',
                'assy100 AS ASSY100',
                'assy102 AS ASSY102',
                'whs100 AS WHS100',
                'whs102 AS WHS102',
                'whs106 AS WHS106',
                'whs_sm AS WHS-SM',
                'whs_non AS WHS-NON',
                'total_curr_inv AS TtlCurrInvtry',
                'order_date AS OrdDate',
                'due_date AS DueDate',
                'po AS PO',
                'dcode AS DCode',
                'dname AS DName',
                'order_qty AS OrderQty',
                'order_bal AS OrderBal',
                'cust_code AS CustCode',
                'cust_name AS CustName',
                'sched_qty AS SchdQty',
                'balance_req AS BalReq',
                'total_bal_req AS TtlBalReq',
                'req_accum AS ReqAccum',
                'allocation_calc AS AllocCalc',
                'total_pr_bal AS TtlPR_Bal',
                'mrp AS MRP',
                'pr_issued AS PR_Issued',
                'pr AS PR',
                'yec_po AS YEC_PO',
                'yec_pu AS YEC_PU',
                'flight AS Flight',
                'deli_qty AS DeliQty',
                'deliaccum AS DeliAccum',
                'check AS Check',
                'sup_code AS SupCode',
                'sup_name AS SupName',
                're AS Re',
                'status AS Status')
        ->get();

        # retrieve data from temp_mrp_info.
        $result2 = DB::connection($this->mysql)->table('temp_mrp_info')
        ->select('po AS PO',
                'dcode AS DCode',
                'dname AS DName',
                DB::raw("SUM(order_qty) AS OrdQty"),
                DB::raw("SUM(order_bal) AS OrdBal"),
                'order_date AS OrdDate',
                'due_date AS DueDate',
                'cust_code AS CustCode',
                'cust_name AS CustName',
                DB::raw("IF(vendor = 'YEC', vendor, '') AS PROCY"),
                DB::raw(" '' AS MaxPUD_Y"),
                DB::raw(" '' AS Stock_Y"),
                DB::raw(" '' AS NoSched_Y"),
                DB::raw(" '' AS YEC"),
                DB::raw(" IF(vendor = 'YEC', vendor, '') AS PROCP"),
                DB::raw(" '' AS MaxPUD_P"),
                DB::raw(" '' AS Stock_P"),
                DB::raw(" '' AS NoSched_P"),
                DB::raw(" '' AS PMI"),
                'check AS Check')
        ->groupBy('po'
                , 'dcode'
                , 'dname'
                , 'order_date'
                , 'due_date'
                , 'cust_code'
                , 'cust_name'
                , 'vendor')
        ->get();
        
        # convert the object result to array readable format.
        foreach ($result as $datareport) 
        {
            $data[] = (array)$datareport;
            #or first convert it and then change its properties using 
            #an array syntax, it's up to you
        }

        # convert the object result to array readable format.
        foreach ($result2 as $datareport) 
        {
            $data2[] = (array)$datareport;
            #or first convert it and then change its properties using 
            #an array syntax, it's up to you
        }

        # Create and export excel by feeding the array result.
        Excel::create(Auth::user()->productline.'_MRP_' . $date.'_forSSS', function($excel) use($data, $data2) 
        {

            $excel->sheet('q_List', function($sheet) use($data) 
            {
                $sheet->fromArray($data);
            });

            $excel->sheet('q_TheoreticalKitDate', function($sheet) use($data2) 
            {
                $sheet->fromArray($data2);
            });
        #download and save the excel file.
        })->store('xls',$path)->export('xls');
    }
    */

    /**
    * Create temp_mrp table.
    * Data is coming from TPICS using the current connection.
    * */
    private function createTempMrp($db, $schema)
    {
        try
        {
            $mrp = DB::connection($db)
            ->table(DB::raw("(
                        SELECT q_MRP_work1_Cross.MCode, 
                            q_MRP_work1_Cross.MName, 
                            q_MRP_Procurement.VENDOR AS Procurement, 
                            q_MRP_work1_Cross.ASSY100, 
                            q_MRP_work1_Cross.ASSY102, 
                            q_MRP_work1_Cross.WHS100, 
                            q_MRP_work1_Cross.WHS102, 
                            q_MRP_work1_Cross.WHS106, 
                            q_MRP_work1_Cross.[WHS-NON], 
                            q_MRP_work1_Cross.[WHS-SM], 
                            q_MRP_work1_Cross.TtlCrrInv, 
                            isnull(q_MRP_work2_PartsReqmnts.TtlBalReq,0) AS TtlBalReq, 
                            isnull(q_MRP_work3_PR_Bal.TotalPRBal,0) AS TotalPRBal
                        FROM (
                            select MCode, 
                                MName, 
                                isnull(ASSY100,0) AS ASSY100 , 
                                isnull(ASSY102,0) AS ASSY102, 
                                isnull(WHS100,0) AS WHS100, 
                                isnull(WHS102,0) AS WHS102, 
                                isnull(WHS106,0) AS WHS106, 
                                isnull([WHS-NON],0) AS [WHS-NON], 
                                isnull([WHS-SM],0) AS [WHS-SM],
                                (isnull(ASSY100,0) + isnull(ASSY102,0) 
                                    + isnull(WHS100,0) + isnull(WHS102,0) 
                                    + isnull(WHS106,0) + isnull([WHS-NON],0) 
                                    + isnull([WHS-SM],0)) AS TtlCrrInv
                            from (
                                SELECT q_MRP_work1_CurrInvtry.MCode, 
                                    q_MRP_work1_CurrInvtry.MName, 
                                    Sum(q_MRP_work1_CurrInvtry.CurrInv) AS TtlCrrInv, 
                                    Strg
                                FROM (
                                    SELECT XZAIK.CODE AS MCode, 
                                        XHEAD.NAME AS MName, 
                                        XZAIK.HOKAN AS Strg, 
                                        XZAIK.ZAIK AS CurrInv
                                    FROM ". $schema ."XZAIK 
                                    INNER JOIN ". $schema ."XHEAD ON XZAIK.CODE = XHEAD.CODE
                                    WHERE XZAIK.HOKAN Not Like 'WHS101' 
                                        And XZAIK.HOKAN Not Like '*NG%'
                                    ) q_MRP_work1_CurrInvtry
                                GROUP BY q_MRP_work1_CurrInvtry.MCode, 
                                    q_MRP_work1_CurrInvtry.MName, Strg
                            ) d
                            pivot(
                                sum(TtlCrrInv)
                                for Strg in (Code, 
                                        Name, 
                                        ASSY100, 
                                        ASSY102, 
                                        WHS100, 
                                        WHS102, 
                                        WHS106, 
                                        [WHS-NON], 
                                        [WHS-SM])
                                    ) piv
                            ) q_MRP_work1_Cross 
                        LEFT JOIN (
                            SELECT XSLIP.CODE AS MCode,
                                sum(XSLIP.KVOL-XSLIP.TJITU) AS TtlBalReq
                            FROM ". $schema ."XSLIP 
                            INNER JOIN ". $schema ."XHEAD ON XSLIP.CODE = XHEAD.CODE 
                            INNER JOIN ". $schema ."XRECE ON XSLIP.SEIBAN = XRECE.SORDER
                            WHERE XSLIP.KVOL-XSLIP.TJITU > 0
                            GROUP BY XSLIP.CODE
                        ) q_MRP_work2_PartsReqmnts ON q_MRP_work1_Cross.MCode = q_MRP_work2_PartsReqmnts.MCode
                        LEFT JOIN (
                            SELECT XSLIP.CODE AS MCode,
                                XSLIP.VENDOR AS [PROC],
                                SUM([KVOL]-[TJITU]) AS TotalPRBal
                            FROM ". $schema ."XSLIP 
                            INNER JOIN ". $schema ."XHEAD ON XSLIP.CODE = XHEAD.CODE
                            WHERE XSLIP.PORDER Not Like 'WK%' 
                                AND XSLIP.PORDER Not Like 'GR%' 
                                AND KVOL-TJITU <> 0
                            GROUP BY XSLIP.CODE, 
                                XSLIP.VENDOR
                        )q_MRP_work3_PR_Bal ON q_MRP_work1_Cross.MCode = q_MRP_work3_PR_Bal.MCode
                        LEFT JOIN (
                            SELECT XITEM.CODE AS CODE, 
                                XITEM.VENDOR AS VENDOR
                            FROM ". $schema ."XITEM
                            GROUP BY XITEM.CODE, XITEM.VENDOR
                            HAVING XITEM.VENDOR Not Like '*ASSY%'
                        ) q_MRP_Procurement ON q_MRP_work1_Cross.MCode = q_MRP_Procurement.CODE
                    ) q_MRP_work"))
            ->leftJoin(DB::raw("(
                    SELECT XSLIP.SEIBAN AS PO, 
                        XSLIP.PORDER AS WO, 
                        CONVERT(VARCHAR(10), SUBSTRING(JDATE, 1, 8), 102)  AS OrdDate, 
                        CONVERT(VARCHAR(10), SUBSTRING(CDATE, 1, 8), 102) AS DueDate, 
                        XSLIP.CODE AS MCode, 
                        XHEAD.NAME AS MName, 
                        XSLIP.HOKAN AS WdrwSource, 
                        XSLIP.KVOL AS SchdQty, 
                        XSLIP.TJITU AS ActTotal, 
                        XSLIP.KVOL-XSLIP.TJITU AS BalReqrd, 
                        SUM(XSLIP.KVOL-XSLIP.TJITU) 
                            OVER(PARTITION BY XSLIP.CODE 
                                ORDER BY XSLIP.CODE,CDATE,XSLIP.SEIBAN ROWS 
                                BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS AccumReq
                        FROM ". $schema ."XSLIP 
                        INNER JOIN ". $schema ."XHEAD ON XSLIP.CODE = XHEAD.CODE 
                        INNER JOIN ". $schema ."XRECE ON XSLIP.SEIBAN = XRECE.SORDER
                        WHERE XSLIP.KVOL-XSLIP.TJITU>0
                    ) t_PartsRequirements"), 'q_MRP_work.MCode', '=', 't_PartsRequirements.MCode')
            ->leftJoin(DB::raw("(
                    SELECT XRECE.SORDER AS PO, 
                        XRECE.CODE AS DCode, 
                        XHEAD.NAME AS DName, 
                        CONVERT(VARCHAR(10), SUBSTRING(JDATE, 1, 8), 102) AS OrdDate, 
                        CONVERT(VARCHAR(10), SUBSTRING(CDATE, 1, 8), 102) AS DueDate, 
                        XRECE.KVOL AS OrdQty, 
                        XRECE.KVOL-XRECE.TJITU AS OrdBal, 
                        XRECE.CUST AS CustCode,
                        XCUST.CNAME AS CustName, 
                        q_UPD1_4_OrderBalance_work.TJITU AS ActTotal, 
                        XRECE.HVOL AS MfgNoAllocTotal, 
                        XRECE.KVOL AS SchdQty 
                    FROM ". $schema ."XRECE 
                    INNER JOIN ". $schema ."XHEAD ON XRECE.CODE = XHEAD.CODE
                    INNER JOIN (
                        SELECT XSLIP.PORDER, 
                            XSLIP.SEIBAN, 
                            XSLIP.KVOL, 
                            XSLIP.TJITU, 
                            XSLIP.HVOL,
                            CONVERT(VARCHAR(10), SUBSTRING(NDATE, 1, 8), 102) AS ScdFinishDate
                        FROM ". $schema ."XSLIP
                        WHERE XSLIP.SEIBAN IS NOT NULL 
                            AND XSLIP.HVOL>0
                        ) q_UPD1_4_OrderBalance_work ON XRECE.SORDER = q_UPD1_4_OrderBalance_work.SEIBAN
                        INNER JOIN XCUST ON XRECE.CUST = XCUST.CUST
                        WHERE XRECE.KVOL - XRECE.TJITU >0 
                            AND XRECE.KVOL > 0
                    ) t_OrderBalance"), 't_PartsRequirements.PO', '=', 't_OrderBalance.PO')
            ->select('q_MRP_work.MCode', 
                        'q_MRP_work.MName', 
                        'q_MRP_work.Procurement', 
                        DB::raw('IIf([ASSY100] Is Null,0,[ASSY100]) AS AS100'), 
                        DB::raw('IIf([ASSY102] Is Null,0,[ASSY102]) AS AS102'), 
                        DB::raw('IIf([WHS100] Is Null,0,[WHS100]) AS WH100'), 
                        DB::raw('IIf([WHS102] Is Null,0,[WHS102]) AS WH102'), 
                        DB::raw('IIf([WHS106] Is Null,0,[WHS106]) AS WH106'), 
                        DB::raw('IIf([WHS-SM] Is Null,0,[WHS-SM]) AS WHSM'), 
                        DB::raw('IIf([WHS-NON] Is Null,0,[WHS-NON]) AS WHNON'), 
                        'q_MRP_work.TtlCrrInv', 
                        't_OrderBalance.OrdDate', 
                        't_OrderBalance.DueDate', 
                        DB::raw('t_PartsRequirements.DueDate AS PDueDate'), 
                        't_PartsRequirements.PO', 
                        't_OrderBalance.DCode', 
                        't_OrderBalance.DName', 
                        't_OrderBalance.OrdQty', 
                        't_OrderBalance.OrdBal', 
                        DB::raw('IIf([t_PartsRequirements].[SchdQty] Is Null,0,[t_PartsRequirements].[SchdQty]) AS ReqQ'), 
                        DB::raw('IIf([BalReqrd] Is Null,0,[BalReqrd]) AS BalReq'), 
                        DB::raw('IIf([AccumReq] Is Null,0,[AccumReq]) AS AccumR'), 
                        DB::raw('(q_MRP_work.TtlCrrInv - (IIf([AccumReq] Is Null,0,[AccumReq]))) AS AllocationCalc'), 
                        DB::raw('IIf([TtlBalReq] Is Null,0,[TtlBalReq]) AS TtlBR'), 
                        DB::raw('IIf([TotalPRBal] Is Null,0,[TotalPRBal]) AS TtlPR'), 
                        DB::raw('((IIf([TtlBalReq] Is Null,0,[TtlBalReq]) + q_MRP_work.TtlCrrInv) - IIf([TtlBalReq] Is Null,0,[TtlBalReq])) AS MRP')
                        )
            ->whereNotNull('t_PartsRequirements.PO')
            ->get();

            //dd($mrp);

            #Insert into temp mrp.
            $this->truncateTable('temp_mrp');
            foreach ($mrp as $key => $mrp_data) {
                DB::connection($this->mysql)->table('temp_mrp')
                    ->insert([
                    'mcode'         => $mrp_data->MCode,
                    'mname'         => $mrp_data->MName,
                    'procurement'   => $mrp_data->Procurement,
                    'as100'         => $mrp_data->AS100,
                    'as102'         => $mrp_data->AS102,
                    'wh100'         => $mrp_data->WH100,
                    'wh102'         => $mrp_data->WH102,
                    'wh106'         => $mrp_data->WH106,
                    'whsm'          => $mrp_data->WHSM,
                    'whnon'         => $mrp_data->WHNON,
                    'ttlcrrinv'     => $mrp_data->TtlCrrInv,
                    'orddate'       => $mrp_data->OrdDate,
                    'duedate'       => $mrp_data->DueDate,
                    'pduedate'      => $mrp_data->PDueDate,
                    'po'            => $mrp_data->PO,
                    'dcode'         => $mrp_data->DCode,
                    'dname'         => $mrp_data->DName,
                    'ordqty'        => $mrp_data->OrdQty,
                    'ordbal'        => $mrp_data->OrdBal,
                    'reqq'          => $mrp_data->ReqQ,
                    'balreq'        => $mrp_data->BalReq,
                    'accumr'        => $mrp_data->AccumR,
                    'allocationcalc'=> $mrp_data->AllocationCalc,
                    'ttlbr'         => $mrp_data->TtlBR,
                    'ttlpr'         => $mrp_data->TtlPR,
                    'mrp'           => $mrp_data->MRP
                    ]);
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $mrp;
    }

    /**
    * Export MRP Data to Excel
    * */
    public function exportMrpDataToExcel()
    {

        $dt = Carbon::now();
        $date = substr($dt->format('Y-m-d'), 2);
        $data = array();
        $data2 = array();
        $path = public_path() . '/MRP_data_files_SSS';

        // # retrieve data from temp_mrp_info.
        $q_List = $this->getQList();

        // # retrieve data from temp_mrp_info.
        $q_TheoreticalKitDate = $this->getQTheoreticalKitDate();
        
        // # convert the object result to array readable format.
        foreach ($q_List as $datareport) 
        {
            $data[] = (array)$datareport;
            #or first convert it and then change its properties using 
            #an array syntax, it's up to you
        }

        # convert the object result to array readable format.
        foreach ($q_TheoreticalKitDate as $datareport) 
        {
            $data2[] = (array)$datareport;
            #or first convert it and then change its properties using 
            #an array syntax, it's up to you
        }

        // # Create and export excel by feeding the array result.
        Excel::create(Auth::user()->productline.'_MRP_' . $date.'_forSSS', function($excel) use($data, $data2) 
        {

            $excel->sheet('q_List', function($sheet) use($data) 
            {
                $sheet->fromArray($data);
            });

            $excel->sheet('q_TheoreticalKitDate', function($sheet) use($data2) 
            {
                $sheet->fromArray($data2);
            });
        #download and save the excel file.
        })->store('xls',$path)->export('xls');
    }

    /**
    * Create temp_PR_Balance table.
    * Data is coming from TPICS using the current connection.
    * */
    private function createTempPrBalance($db, $schema)
    {
        try
        {
            $pr_balance = DB::connection($db)
            ->table($schema . 'XSLIP')
            ->join($schema . 'XHEAD', 'XSLIP.CODE', '=', 'XHEAD.CODE')
            ->select(
                DB::raw('XSLIP.PORDER AS PR'), 
                DB::raw('XSLIP.CODE AS MCode'), 
                DB::raw('XHEAD.NAME AS MName'), 
                DB::raw('XSLIP.VENDOR AS [PROC]'), 
                DB::raw('XSLIP.KVOL AS SchdQty'), 
                DB::raw('XSLIP.TJITU AS ActTotal'), 
                DB::raw('[KVOL]-[TJITU] AS BalReq'), 
                DB::raw('CONVERT(VARCHAR(10), SUBSTRING(DDATE, 1, 8), 102) AS OrdIssued'), 
                DB::raw('CONVERT(VARCHAR(10), SUBSTRING(PDATE, 1, 8), 102) AS FltNeeded'), 
                DB::raw('SUM([KVOL]-[TJITU]) 
                        OVER(PARTITION BY XSLIP.CODE 
                            ORDER BY XSLIP.CODE,PDATE,XSLIP.PORDER ROWS 
                            BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS AccumBal')
                )
            ->whereRaw("XSLIP.PORDER Not Like 'WK%' AND XSLIP.PORDER Not Like 'GR%' AND KVOL-TJITU <> 0")
            ->get();

            // var_dump($pr_balance);

            //Insert into temp_mrp_prbalance mrp.
            $this->truncateTable('temp_mrp_prbalance');
            foreach ($pr_balance as $key => $prb_data) {
                DB::connection($this->mysql)->table('temp_mrp_prbalance')
                    ->insert([
                    'pr'        => $prb_data->PR,
                    'mcode'     => $prb_data->MCode,
                    'mname'     => $prb_data->MName,
                    'proc'      => $prb_data->PROC,
                    'schdqty'   => $prb_data->SchdQty,
                    'acttotal'  => $prb_data->ActTotal,
                    'balreq'    => $prb_data->BalReq,
                    'ordissued' => $prb_data->OrdIssued,
                    'fltneeded' => $prb_data->FltNeeded,
                    'accumbal'  => $prb_data->AccumBal
                    ]);
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $pr_balance;
    }

    /**
    * Create temp_ordbalance table.
    * Data is coming from TPICS using the current connection.
    * */
    private function createTempOrdBalance($db, $schema)
    {

        try
        {
            $ord_balance = DB::connection($db)
            ->table($schema . 'XRECE')
            ->join($schema . 'XHEAD', 'XRECE.CODE', '=', 'XHEAD.CODE')
            ->join(DB::raw("(
                            SELECT XSLIP.PORDER, 
                                XSLIP.SEIBAN, 
                                XSLIP.KVOL, 
                                XSLIP.TJITU, 
                                XSLIP.HVOL,
                                CONVERT(VARCHAR(10), SUBSTRING(NDATE, 1, 8), 102) AS ScdFinishDate
                            FROM ".$schema ."XSLIP
                            WHERE XSLIP.SEIBAN IS NOT NULL 
                                AND XSLIP.HVOL>0
                            ) AS q_UPD1_4_OrderBalance_work"), 
                            'XRECE.SORDER', '=', 'q_UPD1_4_OrderBalance_work.SEIBAN')
            ->join($schema . 'XCUST', 'XRECE.CUST', '=', 'XCUST.CUST')
            ->select(
                DB::raw('XRECE.SORDER AS PO'), 
                DB::raw('XRECE.CODE AS DCode'), 
                DB::raw('XHEAD.NAME AS DName'), 
                DB::raw('CONVERT(VARCHAR(10), SUBSTRING(JDATE, 1, 8), 102) AS OrdDate'), 
                DB::raw('CONVERT(VARCHAR(10), SUBSTRING(CDATE, 1, 8), 102) AS DueDate'), 
                DB::raw('XRECE.KVOL AS OrdQty'), 
                DB::raw('(XRECE.KVOL - XRECE.TJITU) AS OrdBal'), 
                DB::raw('XRECE.CUST AS CustCode'), 
                DB::raw('XCUST.CNAME AS CustName'), 
                DB::raw('q_UPD1_4_OrderBalance_work.TJITU AS ActTotal'), 
                DB::raw('XRECE.HVOL AS MfgNoAllocTotal'), 
                DB::raw('XRECE.KVOL AS SchdQty')
                )
            ->orderBy('XRECE.SORDER')
            ->whereRaw(DB::raw('(XRECE.KVOL - XRECE.TJITU) > 0 AND XRECE.KVOL > 0'))
            ->get();

            // var_dump($pr_balance);

            //Insert into temp_mrp_ordbalance mrp.
            $this->truncateTable('temp_mrp_ordbalance');
            foreach ($ord_balance as $key => $ordb_data) {
                DB::connection($this->mysql)->table('temp_mrp_ordbalance')
                    ->insert([
                    'po'              => $ordb_data->PO,
                    'dcode'           => $ordb_data->DCode,
                    'dname'           => $ordb_data->DName,
                    'orddate'         => $ordb_data->OrdDate,
                    'duedate'         => $ordb_data->DueDate,
                    'ordqty'          => $ordb_data->OrdQty,
                    'ordbal'          => $ordb_data->OrdBal,
                    'custcode'        => $ordb_data->CustCode,
                    'custname'        => $ordb_data->CustName,
                    'acttotal'        => $ordb_data->ActTotal,
                    'mfgnoalloctotal' => $ordb_data->MfgNoAllocTotal,
                    'schdqty'         => $ordb_data->SchdQty
                    ]);
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $ord_balance;
    }

    /**
    * Create temp_mrp_check.
    * Data is coming from mysql DB.
    * */
    private function createTempCheck($db, $schema)
    {
        try
        {
            $check = DB::connection($this->mysql)->select("
                    SELECT t_MRP.MCode, 
                        t_MRP.MName, 
                        t_MRP.procurement, 
                        t_MRP.AS100, 
                        t_MRP.AS102, 
                        t_MRP.WH100, 
                        t_MRP.WH102, 
                        t_MRP.WH106, 
                        t_MRP.`WHSM`, 
                        t_MRP.`WHNON`, 
                        t_MRP.TtlCrrInv, 
                        t_MRP.OrdDate, 
                        t_MRP.DueDate, 
                        t_MRP.PO, 
                        t_MRP.DCode, 
                        t_MRP.DName, 
                        t_MRP.OrdQty, 
                        t_MRP.OrdBal, 
                        t_MRP.reqq, 
                        t_MRP.BalReq, 
                        t_MRP.TtlBR, 
                        t_MRP.Accumr, 
                        t_MRP.AllocationCalc, 
                        t_MRP.TtlPR, 
                        t_MRP.MRP, 
                        IF(`check`='Fromstock',NULL,`OrdIssued`) AS PR_Issued, 
                        IF(`CHeck`='Fromstock',NULL,`PR`) AS PR_No, 
                        IF(`Check`='Fromstock',NULL,`GR_Date`) AS YEC_PU, 
                        IF(`Check`='FromStock',NULL,`Flt`) AS Flight, 
                        IF(`Check`='FromStock',NULL,`q_FS6_5_AllocationProcess_work1`.SchdQty) AS DeliQty, 
                        IF(`Check`='FromStock',NULL,`Accum`) AS DeliAccum, 
                        q_FS6_5_AllocationProcess_work2.SupCode, 
                        q_FS6_5_AllocationProcess_work2.SupName, 
                        '' AS Re, 
                        '' AS status, 
                        q_FS6_5_AllocationProcess_work1.Check 
                    FROM temp_mrp t_MRP 
                    LEFT JOIN (
                        SELECT t_Allocation.MCode, 
                            t_Allocation.MName, 
                            t_Allocation.TtlCrrInv, 
                            t_Allocation.PO, 
                            t_Allocation.accumr, 
                            t_Allocation.OrdIssued, 
                            t_Allocation.PR, 
                            t_Allocation.gr_date, 
                            t_Allocation.flt, 
                            t_Allocation.SchdQty, 
                            t_Allocation.Accum, 
                            MIN(t_Allocation.PR) AS BPR, 
                            MIN(t_Allocation.AllocationCalc) AS BAC, 
                            t_Allocation.Check
                        FROM (
                            SELECT t_mrp.MCode, 
                                t_mrp.MName, 
                                t_mrp.TtlCrrInv, 
                                t_mrp.PO, 
                                t_mrp.DueDate, 
                                t_mrp.BalReq, 
                                t_mrp.accumr, 
                                temp_mrp_prbalance.OrdIssued, 
                                temp_mrp_prbalance.PR, 
                                t_PartsAnswer.GR_Date, 
                                t_PartsAnswer.Flt, 
                                t_PartsAnswer.SchdQty, 
                                temp_mrp_prbalance.BalReq AS PRBal, 
                                t_PartsAnswer.Accum,
                                temp_mrp_prbalance.AccumBal, 
                                t_mrp.AllocationCalc, 
                                t_mrp.TtlPR, 
                                IF(Accum IS NULL,TtlCrrInv-accumr,t_PartsAnswer.Accum+TtlCrrInv-accumr) AS AllocCalc2, 
                                IF(t_mrp.TtlCrrInv>=t_mrp.accumr,
                                    'FromStock',
                                    IF(t_mrp.TtlCrrInv+t_PartsAnswer.Accum>=t_mrp.accumr,
                                        'Allocation',
                                        (t_mrp.TtlCrrInv+t_PartsAnswer.Accum)-(t_mrp.accumr-t_mrp.BalReq))) AS 'check'
                            FROM temp_mrp t_mrp
                            LEFT JOIN (
                                SELECT *,
                                       @sum := IF(@cat = t.mcode,@sum,0) + t.schdqty_a AS accum,
                                       @cat := mcode AS mcode2
                                  FROM (
                                    SELECT *, SUM(a.schdqty) AS schdqty_a
                                    FROM (
                                        SELECT `pr`, 
                                            `grdate` AS gr_date, 
                                            SUM(`qty`) AS schdqty, 
                                            `mcode`, 
                                            `mname`, 
                                            `fltneeded`, 
                                            `supcode`, 
                                            `supname`, 
                                            `reasoncode`, 
                                            `yecpo` AS yec_po, 
                                            NULL AS flt
                                        FROM temp_mrp_zypf0150
                                        GROUP BY `pr`, 
                                            `grdate`, 
                                            `mcode`, 
                                            `mname`, 
                                            `fltneeded`, 
                                            `supcode`, 
                                            `supname`, 
                                            `reasoncode`, 
                                            `yecpo`
                                        UNION
                                        SELECT `orderno` AS pr, 
                                            `ppdreply` AS gr_date, 
                                            `schdqty`, 
                                            `itemcode` AS mcode, 
                                            `name` AS mname, 
                                            NULL AS fltneeded, 
                                            NULL AS supcode, 
                                            NULL AS supname,
                                            `remarks` AS reasoncode, 
                                            NULL AS yec_po, 
                                            NULL AS flt
                                        FROM `temp_mrp_ppsanswer`
                                        UNION
                                        SELECT `pr`, 
                                            `fltdate` AS gr_date, 
                                            SUM(`qty`) AS schdqty, 
                                            `mcode`, 
                                            `mname`, 
                                            NULL AS fltneeded, 
                                            NULL AS supcode, 
                                            NULL AS supname, 
                                            NULL AS reasoncode, 
                                            NULL AS yec_po, 
                                            '*' AS flt
                                        FROM `temp_mrp_invoice`
                                        GROUP BY fltdate, 
                                            mcode, 
                                            mname, 
                                            pr, 
                                            '*'
                                    ) a
                                    GROUP BY `pr`, 
                                        gr_date, 
                                        schdqty, 
                                        `mcode`, 
                                        `mname`, 
                                        `fltneeded`, 
                                        `supcode`, 
                                        `supname`, 
                                        `reasoncode`, 
                                        yec_po, 
                                        flt
                                    ORDER BY mcode, 
                                        gr_date, 
                                        pr
                                    ) t
                                  JOIN (SELECT @sum := 0) s
                                  JOIN (SELECT @cat := '' COLLATE utf8_unicode_ci) c
                                  ORDER BY mcode, gr_date, pr
                            ) t_PartsAnswer ON t_mrp.MCode = t_PartsAnswer.MCode
                            LEFT JOIN temp_mrp_prbalance 
                                ON t_PartsAnswer.PR = temp_mrp_prbalance.PR
                            GROUP BY t_mrp.MCode, 
                                t_mrp.MName, 
                                t_mrp.TtlCrrInv, 
                                t_mrp.PO, 
                                t_mrp.DueDate, 
                                t_mrp.BalReq, 
                                t_mrp.accumr, 
                                temp_mrp_prbalance.OrdIssued, 
                                temp_mrp_prbalance.PR, 
                                t_PartsAnswer.GR_Date, 
                                t_PartsAnswer.Flt, 
                                t_PartsAnswer.SchdQty, 
                                temp_mrp_prbalance.BalReq, 
                                t_PartsAnswer.Accum, 
                                temp_mrp_prbalance.AccumBal, 
                                t_mrp.AllocationCalc, 
                                t_mrp.TtlPR
                            HAVING (((t_mrp.BalReq)<>0))
                            ORDER BY t_mrp.MCode, 
                                t_mrp.DueDate, 
                                t_PartsAnswer.GR_Date
                        )t_Allocation
                        GROUP BY t_Allocation.MCode, 
                            t_Allocation.MName, 
                            t_Allocation.TtlCrrInv, 
                            t_Allocation.PO, 
                            t_Allocation.accumr, 
                            t_Allocation.Check
                    )q_FS6_5_AllocationProcess_work1 
                        ON (t_MRP.MCode = q_FS6_5_AllocationProcess_work1.MCode) 
                        AND (t_MRP.PO = q_FS6_5_AllocationProcess_work1.PO)
                    LEFT JOIN (
                        SELECT t_ZYPF0150.MCode, 
                            t_ZYPF0150.MName, 
                            t_ZYPF0150.SupCode, 
                            t_ZYPF0150.SupName
                        FROM temp_mrp_zypf0150 t_ZYPF0150
                        GROUP BY t_ZYPF0150.MCode, 
                            t_ZYPF0150.MName, 
                            t_ZYPF0150.SupCode, 
                            t_ZYPF0150.SupName
                        HAVING ((NOT (t_ZYPF0150.SupCode) IS NULL))
                    )q_FS6_5_AllocationProcess_work2 
                        ON t_MRP.MCode = q_FS6_5_AllocationProcess_work2.MCode
                    WHERE ((NOT (t_MRP.PO) IS NULL))
                    ORDER BY t_MRP.MCode, t_MRP.PO
                ");


            //Insert into temp_mrp_check..
            $this->truncateTable('temp_mrp_check');
            foreach ($check as $key => $check_data) {
                DB::connection($this->mysql)->table('temp_mrp_check')
                    ->insert([
                        'mcode'          => $check_data->MCode,
                        'mname'          => $check_data->MName,
                        'procurement'    => $check_data->procurement,
                        'as100'          => $check_data->AS100,
                        'as102'          => $check_data->AS102,
                        'wh100'          => $check_data->WH100,
                        'wh102'          => $check_data->WH102,
                        'wh106'          => $check_data->WH106,
                        'whsm'           => $check_data->WHSM,
                        'whnon'          => $check_data->WHNON,
                        'ttlcrrinv'      => $check_data->TtlCrrInv,
                        'orddate'        => $check_data->OrdDate,
                        'duedate'        => $check_data->DueDate,
                        'po'             => $check_data->PO,
                        'dcode'          => $check_data->DCode,
                        'dname'          => $check_data->DName,
                        'ordqty'         => $check_data->OrdQty,
                        'ordbal'         => $check_data->OrdBal,
                        'reqq'           => $check_data->reqq,
                        'balreq'         => $check_data->BalReq,
                        'ttlbr'          => $check_data->TtlBR,
                        'accumr'         => $check_data->Accumr,
                        'allocationcalc' => $check_data->AllocationCalc,
                        'ttlpr'          => $check_data->TtlPR,
                        'mrp'            => $check_data->MRP,
                        'pr_issued'      => $check_data->PR_Issued,
                        'pr_no'          => $check_data->PR_No,
                        'yec_pu'         => $check_data->YEC_PU,
                        'flight'         => $check_data->Flight,
                        'deliqty'        => $check_data->DeliQty,
                        'deliaccum'      => $check_data->DeliAccum,
                        'supcode'        => $check_data->SupCode,
                        'supname'        => $check_data->SupName,
                        're'             => $check_data->Re,
                        'status'         => $check_data->status,
                        'check'          => $check_data->Check
                    ]);
            }
            
            // var_dump(count($check));

            $unique_mcode = DB::connection($this->mysql)->select("select DISTINCT mcode as mcode from temp_mrp_check");
            
            $mcode = '';
            foreach ($unique_mcode as $key => $row) 
            {
                //collect unique mcode.
                if($mcode == '')
                {
                    $mcode =  "'" . $row->mcode . "'";
                }
                else
                {
                    $mcode =  $mcode . ",'" . $row->mcode . "'";
                }
            }

            // var_dump($mcode);

            $xitem_vendor = DB::connection($db)->select("select code, vendor from xitem where code in (" . $mcode . ")" );

            // var_dump($xitem_vendor);

            //Insert into temp_xitem.
            $this->truncateTable('temp_mrp_xitem');
            foreach ($xitem_vendor as $key => $xitem_data) {
                DB::connection($this->mysql)->table('temp_mrp_xitem')
                    ->insert([
                        'code'     => $xitem_data->code,
                        'vendor'   => $xitem_data->vendor
                    ]);
            }

        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
    }

    /**
    * Query q_list
    * Data is coming from mysql DB.
    * */
    private function getQList()
    {
        try
        {

            $q_list = DB::connection($this->mysql)->select("
                        SELECT `t_check`.`MCode`,
                               `t_check`.`MName`,
                               t_check.procurement AS VENDOR,
                               t_check.AS100 AS ASSY100,
                               t_check.AS102 AS ASSY102,
                               t_check.WH100 AS WHS100,
                               t_check.WH102 AS WHS102,
                               t_check.WH106 AS WHS106,
                               t_check.WHSM AS `WHSSM`,
                               t_check.WHNON AS `WHSNON`,
                               t_check.TtlCrrInv AS TtlCurrInvtry,
                               `t_check`.`OrdDate`,
                               `t_check`.`DueDate`,
                               `t_check`.`PO`,
                               `t_check`.`DCode`,
                               `t_check`.`DName`,
                               t_check.OrdQty AS OrderQty,
                               t_check.OrdBal AS OrderBal,
                               `t_OrderBalance`.`CustCode`,
                               `t_OrderBalance`.`CustName`,
                               t_check.DeliQty AS SchdQty,
                               `t_check`.`BalReq`,
                               t_check.ttlbr AS TtlBalReq,
                               t_check.accumr AS ReqAccum,
                               t_check.allocationcalc AS AllocCalc,
                               t_check.ttlpr AS TtlPR_Bal,
                               `t_check`.`MRP`,
                               `t_check`.`PR_Issued`,
                               t_check.PR_No AS PR,
                               `q_List_YEC_PO`.`YEC_PO`,
                               `t_check`.`YEC_PU`,
                               `t_check`.`Flight`,
                               `t_check`.`DeliQty`,
                               `t_check`.`DeliAccum`,
                               `t_check`.`Check`,
                               `q_List_YEC_PO`.`SupCode`,
                               `q_List_YEC_PO`.`SupName`,
                               `t_check`.`Re`,
                               `t_check`.`Status`
                        FROM temp_mrp_check AS t_check
                        LEFT JOIN `temp_mrp_ordbalance` AS `t_OrderBalance` 
                        ON `t_check`.`PO` = `t_OrderBalance`.`PO`
                        LEFT JOIN
                          (SELECT t_ZYPF0150.PR,
                                  t_ZYPF0150.MCode,
                                  t_ZYPF0150.MName,
                                  t_ZYPF0150.YECPO AS YEC_PO,
                                  t_ZYPF0150.GRDate AS GR_Date,
                                  SUM(t_ZYPF0150.Qty) AS TtlQty,
                                  t_ZYPF0150.SupCode,
                                  t_ZYPF0150.SupName,
                                  t_ZYPF0150.ReasonCode,
                                  t_ZYPF0150.ReasonCode AS Reason
                           FROM temp_mrp_zypf0150 AS t_ZYPF0150
                           GROUP BY t_ZYPF0150.PR,
                                    t_ZYPF0150.MCode,
                                    t_ZYPF0150.MName,
                                    t_ZYPF0150.YECPO,
                                    t_ZYPF0150.GRDate,
                                    t_ZYPF0150.SupCode,
                                    t_ZYPF0150.SupName,
                                    t_ZYPF0150.ReasonCode
                        ) AS q_List_YEC_PO 
                        ON `t_check`.`PR_No` = `q_List_YEC_PO`.`PR`
                        AND `t_check`.`YEC_PU` = q_List_YEC_PO.GR_Date
                        AND `t_check`.`DeliQty` = q_List_YEC_PO.TtlQty
                        GROUP BY `t_check`.`MCode`,
                                 `t_check`.`MName`,
                                 `t_check`.`procurement`,
                                 `t_check`.`AS100`,
                                 `t_check`.`AS102`,
                                 `t_check`.`WH100`,
                                 `t_check`.`WH102`,
                                 `t_check`.`WH106`,
                                 `t_check`.`WHSM`,
                                 `t_check`.`WHNON`,
                                 `t_check`.`TtlCrrInv`,
                                 `t_check`.`OrdDate`,
                                 `t_check`.`DueDate`,
                                 `t_check`.`PO`,
                                 `t_check`.`DCode`,
                                 `t_check`.`DName`,
                                 `t_check`.`OrdQty`,
                                 `t_check`.`OrdBal`,
                                 `t_OrderBalance`.`CustCode`,
                                 `t_OrderBalance`.`CustName`,
                                 `t_check`.`DeliQty`,
                                 `t_check`.`BalReq`,
                                 `t_check`.`ttlbr`,
                                 `t_check`.`accumr`,
                                 `t_check`.`allocationcalc`,
                                 `t_check`.`ttlpr`,
                                 `t_check`.`MRP`,
                                 `t_check`.`PR_Issued`,
                                 `t_check`.`PR_No`,
                                 `q_List_YEC_PO`.`YEC_PO`,
                                 `t_check`.`YEC_PU`,
                                 `t_check`.`Flight`,
                                 `t_check`.`DeliQty`,
                                 `t_check`.`DeliAccum`,
                                 `t_check`.`Check`,
                                 `q_List_YEC_PO`.`SupCode`,
                                 `q_List_YEC_PO`.`SupName`,
                                 `t_check`.`Re`,
                                 `t_check`.`Status`,
                                 `t_check`.`PR_No`,
                                 `q_List_YEC_PO`.`YEC_PO`
                        ORDER BY `t_check`.`MCode` DESC
            "); 

            // var_dump($q_list);

        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $q_list;
    }

    /**
    * Query q_TheoreticalKitDate
    * Data is coming from mySQL DB.
    * */
    private function getQTheoreticalKitDate()
    {
        try
        {

            $q_TheoreticalKitDate = DB::connection($this->mysql)->select("
                    SELECT t_OrderBalance.PO, 
                        t_OrderBalance.DCode, 
                        t_OrderBalance.DName, 
                        t_OrderBalance.OrdQty, 
                        t_OrderBalance.OrdBal, 
                        t_OrderBalance.OrdDate, 
                        t_OrderBalance.DueDate, 
                        t_OrderBalance.CustCode, 
                        t_OrderBalance.CustName, 
                        q_TheoreticalKitDate_YEC.PROC AS PROCY, 
                        q_TheoreticalKitDate_YEC.MaxPUD_Y, 
                        q_TheoreticalKitDate_YEC.Stock_Y, 
                        q_TheoreticalKitDate_YEC.NoSched_Y, 
                        IF(ISNULL(NoSched_Y)=TRUE,IF(ISNULL(Stock_Y)=TRUE,MaxPUD_Y,Stock_Y),NoSched_Y) AS YEC, 
                        q_TheoreticalKitDate_PMI.PROC AS PROCP, 
                        q_TheoreticalKitDate_PMI.MaxPUD_P, 
                        q_TheoreticalKitDate_PMI.Stock_P, 
                        q_TheoreticalKitDate_PMI.NoSched_P, 
                        IF(ISNULL(q_TheoreticalKitDate_PMI.PROC)=TRUE,
                            '----',
                            IF(ISNULL(NoSched_P)=TRUE,
                                IF(ISNULL(Stock_P)=TRUE,
                                    MaxPUD_P,
                                    Stock_P),
                                NoSched_P)) AS PMI, 
                        IF(ISNULL(IF(ISNULL(NoSched_Y)=TRUE,
                                IF(ISNULL(Stock_Y)=TRUE,
                                    MaxPUD_Y,
                                    Stock_Y),
                                NoSched_Y))=TRUE,
                                IF(ISNULL(q_TheoreticalKitDate_PMI.PROC)=TRUE,
                                    '----',
                                    IF(ISNULL(NoSched_P)=TRUE,
                                        IF(ISNULL(Stock_P)=TRUE,
                                            MaxPUD_P,
                                            Stock_P),
                                        NoSched_P)),
                                IF(IF(ISNULL(q_TheoreticalKitDate_PMI.PROC)=TRUE,
                                    '----',
                                    IF(ISNULL(NoSched_P)=TRUE,
                                        IF(ISNULL(Stock_P)=TRUE,
                                            MaxPUD_P,
                                            Stock_P),
                                        NoSched_P)
                                    )='----',
                                    IF(ISNULL(NoSched_Y)=TRUE,
                                        IF(ISNULL(Stock_Y)=TRUE,
                                            MaxPUD_Y,
                                            Stock_Y),
                                        NoSched_Y),
                                    IF(IF(ISNULL(NoSched_Y)=TRUE,
                                        IF(ISNULL(Stock_Y)=TRUE,
                                            MaxPUD_Y,
                                            Stock_Y),
                                        NoSched_Y)='XXX',
                                            IF(ISNULL(NoSched_Y)=TRUE,
                                                IF(ISNULL(Stock_Y)=TRUE,
                                                    MaxPUD_Y,
                                                    Stock_Y),
                                                NoSched_Y),
                                            IF(IF(ISNULL(q_TheoreticalKitDate_PMI.PROC)=TRUE,
                                                '----',
                                                IF(ISNULL(NoSched_P)=TRUE,
                                                    IF(ISNULL(Stock_P)=TRUE,
                                                        MaxPUD_P,
                                                        Stock_P),
                                                    NoSched_P)
                                                )='Fromstock',
                                                    IF(ISNULL(NoSched_Y)=TRUE,
                                                        IF(ISNULL(Stock_Y)=TRUE,
                                                            MaxPUD_Y,
                                                            Stock_Y),
                                                        NoSched_Y),
                                                        IF(ISNULL(q_TheoreticalKitDate_PMI.PROC)=TRUE,
                                                            '----',
                                                            IF(ISNULL(NoSched_P)=TRUE,
                                                                IF(ISNULL(Stock_P)=TRUE,
                                                                    MaxPUD_P,
                                                                    Stock_P),
                                                                NoSched_P)
                                                            ))))) AS `Check`
                    FROM temp_mrp_ordbalance AS t_OrderBalance 
                    LEFT JOIN (
                        SELECT t_Check.PO, 
                            q_TheoreticalKitDate_PROC.VENDOR AS `PROC`, 
                            q_TheoreticalKitDate_YEC_Allocation.MaxPUDOfMax AS MaxPUD_Y, 
                            q_TheoreticalKitDate_YEC_Available.KitStatus AS Stock_Y, 
                            q_TheoreticalKitDate_YEC_Incomplete.KitStatus AS NoSched_Y
                        FROM temp_mrp_check AS t_Check 
                        LEFT JOIN (
                            SELECT t_Check.PO, 
                                MAX(q_TheoreticalKitDate_work2.MaxPUD) AS MaxPUDOfMax, 
                                t_Check.procurement AS PROC, q_TheoreticalKitDate_work2.CheckOfMax, 
                                IF(MAX(q_TheoreticalKitDate_work2.MaxPUD) > CURDATE() +60,'XXX',MAX(q_TheoreticalKitDate_work2.MaxPUD)) AS `CHECK`
                            FROM temp_mrp_check AS t_Check 
                            LEFT JOIN (
                                SELECT q_TheoreticalKitDate_work.PO, 
                                    q_TheoreticalKitDate_work.VENDOR AS `PROC`, 
                                    q_TheoreticalKitDate_work.MaxPUD, 
                                    q_TheoreticalKitDate_work.CheckOfMax
                                FROM (
                                    SELECT t_Check.PO, 
                                        t_Check.procurement AS VENDOR, 
                                        t_Check.MCode, 
                                        MAX(t_Check.YEC_PU) AS MaxPUD, 
                                        MAX(t_Check.Check) AS CheckOfMax
                                    FROM temp_mrp_check AS t_Check 
                                    GROUP BY t_Check.PO, 
                                        t_Check.procurement, 
                                        t_Check.MCode
                                    ORDER BY t_Check.PO, t_Check.MCode
                                )q_TheoreticalKitDate_work
                                GROUP BY q_TheoreticalKitDate_work.PO, 
                                    q_TheoreticalKitDate_work.VENDOR, 
                                    q_TheoreticalKitDate_work.MaxPUD, 
                                    q_TheoreticalKitDate_work.CheckOfMax
                            ) q_TheoreticalKitDate_work2 
                                ON t_Check.PO = q_TheoreticalKitDate_work2.PO
                            GROUP BY t_Check.PO, t_Check.procurement, 
                                q_TheoreticalKitDate_work2.CheckOfMax
                            HAVING (((t_Check.procurement)='YEC') 
                                AND ((q_TheoreticalKitDate_work2.CheckOfMax) LIKE 'Allocation'))
                            )q_TheoreticalKitDate_YEC_Allocation 
                            ON t_Check.PO=q_TheoreticalKitDate_YEC_Allocation.PO
                        LEFT JOIN (
                            SELECT t_Check.PO,
                                MAX(t_Check.YEC_PU) AS MaxPUD, 
                                t_Check.procurement AS PROC, 
                                t_Check.Check AS KitStatus, 
                                t_Check.YEC_PU,
                                q_TheoreticalKitDate_YEC_Allocation.PO AS alloc_po, 
                                q_TheoreticalKitDate_YEC_Incomplete_1.PO AS inc_po
                            FROM temp_mrp_check AS t_Check 
                            LEFT JOIN (
                                SELECT t_Check.PO, 
                                    MAX(q_TheoreticalKitDate_work2.MaxPUD) AS MaxPUDOfMax, 
                                    t_Check.procurement AS PROC, q_TheoreticalKitDate_work2.CheckOfMax, 
                                    IF(MAX(q_TheoreticalKitDate_work2.MaxPUD) > CURDATE() +60,'XXX',MAX(q_TheoreticalKitDate_work2.MaxPUD)) AS `CHECK`
                                FROM temp_mrp_check AS t_Check 
                                LEFT JOIN (
                                    SELECT q_TheoreticalKitDate_work.PO, 
                                        q_TheoreticalKitDate_work.VENDOR AS `PROC`, 
                                        q_TheoreticalKitDate_work.MaxPUD, 
                                        q_TheoreticalKitDate_work.CheckOfMax
                                    FROM (
                                        SELECT t_Check.PO, 
                                            t_Check.procurement AS VENDOR, 
                                            t_Check.MCode, 
                                            MAX(t_Check.YEC_PU) AS MaxPUD, 
                                            MAX(t_Check.Check) AS CheckOfMax
                                        FROM temp_mrp_check AS t_Check 
                                        GROUP BY t_Check.PO, 
                                            t_Check.procurement, 
                                            t_Check.MCode
                                        ORDER BY t_Check.PO, t_Check.MCode
                                    )q_TheoreticalKitDate_work
                                    GROUP BY q_TheoreticalKitDate_work.PO, 
                                        q_TheoreticalKitDate_work.VENDOR, 
                                        q_TheoreticalKitDate_work.MaxPUD, 
                                        q_TheoreticalKitDate_work.CheckOfMax
                                ) q_TheoreticalKitDate_work2 
                                    ON t_Check.PO = q_TheoreticalKitDate_work2.PO
                                GROUP BY t_Check.PO, t_Check.procurement, 
                                    q_TheoreticalKitDate_work2.CheckOfMax
                                HAVING (((t_Check.procurement)='YEC') 
                                    AND ((q_TheoreticalKitDate_work2.CheckOfMax) LIKE 'Allocation'))
                            )q_TheoreticalKitDate_YEC_Allocation 
                            ON t_Check.PO=q_TheoreticalKitDate_YEC_Allocation.PO
                            LEFT JOIN (
                                SELECT t_Check.PO, 
                                    q_TheoreticalKitDate_work2.MaxPUD, 
                                    t_Check.procurement, 
                                    q_TheoreticalKitDate_work2.CheckOfMax, 
                                    'XXX' AS KitStatus
                                FROM temp_mrp_check AS t_Check 
                                INNER JOIN (
                                    SELECT q_TheoreticalKitDate_work.PO, 
                                        q_TheoreticalKitDate_work.VENDOR AS `PROC`, 
                                        q_TheoreticalKitDate_work.MaxPUD, 
                                        q_TheoreticalKitDate_work.CheckOfMax
                                    FROM (
                                        SELECT t_Check.PO, 
                                            t_Check.procurement AS VENDOR, 
                                            t_Check.MCode, 
                                            MAX(t_Check.YEC_PU) AS MaxPUD, 
                                            MAX(t_Check.Check) AS CheckOfMax
                                        FROM temp_mrp_check AS t_Check 
                                        GROUP BY t_Check.PO, 
                                            t_Check.procurement, 
                                            t_Check.MCode
                                        ORDER BY t_Check.PO, 
                                            t_Check.MCode
                                    )q_TheoreticalKitDate_work
                                    GROUP BY q_TheoreticalKitDate_work.PO, 
                                        q_TheoreticalKitDate_work.VENDOR, 
                                        q_TheoreticalKitDate_work.MaxPUD, 
                                        q_TheoreticalKitDate_work.CheckOfMax
                                ) q_TheoreticalKitDate_work2 
                                    ON t_Check.PO = q_TheoreticalKitDate_work2.PO
                                GROUP BY t_Check.PO, 
                                    q_TheoreticalKitDate_work2.MaxPUD, 
                                    t_Check.procurement, 
                                    q_TheoreticalKitDate_work2.CheckOfMax, 
                                    'XXX'
                                HAVING ((NOT (t_Check.PO) IS NULL) 
                                    AND ((t_Check.procurement) LIKE 'Y%') 
                                    AND ((q_TheoreticalKitDate_work2.CheckOfMax) IS NULL))
                            ) AS q_TheoreticalKitDate_YEC_Incomplete_1 
                            ON t_Check.PO=q_TheoreticalKitDate_YEC_Incomplete_1.PO
                            GROUP BY t_Check.PO, 
                                t_Check.procurement, 
                                t_Check.Check, 
                                t_Check.YEC_PU, 
                                q_TheoreticalKitDate_YEC_Allocation.PO, 
                                q_TheoreticalKitDate_YEC_Incomplete_1.PO
                            HAVING t_Check.procurement NOT LIKE 'P%'
                                AND t_Check.Check='FromStock'
                                AND q_TheoreticalKitDate_YEC_Allocation.PO IS NULL
                                AND q_TheoreticalKitDate_YEC_Incomplete_1.PO IS NULL
                        ) q_TheoreticalKitDate_YEC_Available 
                            ON t_Check.PO=q_TheoreticalKitDate_YEC_Available.PO 
                        LEFT JOIN (
                            SELECT t_Check.PO, 
                                q_TheoreticalKitDate_work2.MaxPUD, 
                                t_Check.procurement, 
                                q_TheoreticalKitDate_work2.CheckOfMax, 
                                'XXX' AS KitStatus
                            FROM temp_mrp_check AS t_Check 
                            INNER JOIN (
                                SELECT q_TheoreticalKitDate_work.PO, 
                                    q_TheoreticalKitDate_work.VENDOR AS `PROC`, 
                                    q_TheoreticalKitDate_work.MaxPUD, 
                                    q_TheoreticalKitDate_work.CheckOfMax
                                FROM (
                                    SELECT t_Check.PO, 
                                        t_Check.procurement AS VENDOR, 
                                        t_Check.MCode, 
                                        MAX(t_Check.YEC_PU) AS MaxPUD, 
                                        MAX(t_Check.Check) AS CheckOfMax
                                    FROM temp_mrp_check AS t_Check 
                                    GROUP BY t_Check.PO, 
                                        t_Check.procurement, 
                                        t_Check.MCode
                                    ORDER BY t_Check.PO, 
                                        t_Check.MCode
                                )q_TheoreticalKitDate_work
                                GROUP BY q_TheoreticalKitDate_work.PO, 
                                    q_TheoreticalKitDate_work.VENDOR, 
                                    q_TheoreticalKitDate_work.MaxPUD, 
                                    q_TheoreticalKitDate_work.CheckOfMax
                            ) q_TheoreticalKitDate_work2 
                                ON t_Check.PO = q_TheoreticalKitDate_work2.PO
                            GROUP BY t_Check.PO, 
                                q_TheoreticalKitDate_work2.MaxPUD, 
                                t_Check.procurement, 
                                q_TheoreticalKitDate_work2.CheckOfMax, 
                                'XXX'
                            HAVING ((NOT (t_Check.PO) IS NULL) 
                                AND ((t_Check.procurement) LIKE 'Y%') 
                                AND ((q_TheoreticalKitDate_work2.CheckOfMax) IS NULL))
                        ) q_TheoreticalKitDate_YEC_Incomplete 
                            ON t_Check.PO=q_TheoreticalKitDate_YEC_Incomplete.PO 
                        LEFT JOIN (
                            SELECT `code`, vendor
                            FROM temp_mrp_xitem
                            )q_TheoreticalKitDate_PROC 
                            ON t_Check.MCode=q_TheoreticalKitDate_PROC.CODE
                        WHERE (((q_TheoreticalKitDate_PROC.VENDOR) LIKE 'Y%'))
                    ) AS q_TheoreticalKitDate_YEC 
                    ON t_OrderBalance.PO = q_TheoreticalKitDate_YEC.PO 
                    LEFT JOIN (
                        SELECT t_Check.PO, q_TheoreticalKitDate_PROC.VENDOR AS `PROC`, 
                                q_TheoreticalKitDate_PMI_Allocation.MaxPUDOfMax AS MaxPUD_P, 
                                q_TheoreticalKitDate_PMI_Available.KitStatus AS Stock_P, 
                                q_TheoreticalKitDate_PMI_Incomplete.KitStatus AS NoSched_P
                        FROM temp_mrp_check AS t_Check 
                        LEFT JOIN (
                            SELECT `code`, vendor
                            FROM temp_mrp_xitem
                            ) q_TheoreticalKitDate_PROC ON t_Check.MCode=q_TheoreticalKitDate_PROC.CODE
                        LEFT JOIN (
                            SELECT t_Check.PO, MAX(q_TheoreticalKitDate_work2.MaxPUD) AS MaxPUDOfMax, 
                                t_Check.procurement, q_TheoreticalKitDate_work2.CheckOfMax, 
                                IF(MAX(q_TheoreticalKitDate_work2.MaxPUD)>CURDATE()+60,'XXX',MAX(q_TheoreticalKitDate_work2.MaxPUD)) AS `Check`
                            FROM temp_mrp_check t_Check 
                            LEFT JOIN (
                                SELECT q_TheoreticalKitDate_work.PO, 
                                    q_TheoreticalKitDate_work.VENDOR AS `PROC`, 
                                    q_TheoreticalKitDate_work.MaxPUD, 
                                    q_TheoreticalKitDate_work.CheckOfMax
                                FROM (
                                    SELECT t_Check.PO, 
                                        q_TheoreticalKitDate_PROC.VENDOR, 
                                        t_Check.MCode, 
                                        MAX(t_Check.YEC_PU) AS MaxPUD, 
                                        MAX(t_Check.Check) AS CheckOfMax
                                    FROM temp_mrp_check AS t_Check 
                                    LEFT JOIN (
                                        SELECT `code`, vendor
                                        FROM temp_mrp_xitem
                                    )q_TheoreticalKitDate_PROC 
                                    ON t_Check.MCode = q_TheoreticalKitDate_PROC.CODE
                                    GROUP BY t_Check.PO, 
                                        q_TheoreticalKitDate_PROC.VENDOR, 
                                        t_Check.MCode
                                    ORDER BY t_Check.PO, 
                                        t_Check.MCode
                                ) q_TheoreticalKitDate_work
                                GROUP BY q_TheoreticalKitDate_work.PO, 
                                    q_TheoreticalKitDate_work.VENDOR, 
                                    q_TheoreticalKitDate_work.MaxPUD, 
                                    q_TheoreticalKitDate_work.CheckOfMax
                            ) q_TheoreticalKitDate_work2 ON t_Check.PO=q_TheoreticalKitDate_work2.PO
                            GROUP BY t_Check.PO, t_Check.procurement, q_TheoreticalKitDate_work2.CheckOfMax
                            HAVING (((t_Check.procurement)='PMI') AND ((q_TheoreticalKitDate_work2.CheckOfMax) LIKE 'Allocation'))
                        )q_TheoreticalKitDate_PMI_Allocation ON t_Check.PO=q_TheoreticalKitDate_PMI_Allocation.PO
                        LEFT JOIN (
                            SELECT t_Check.PO, 
                                MAX(t_Check.YEC_PU) AS MaxPUD, 
                                t_Check.procurement, 
                                t_Check.Check AS KitStatus, 
                                t_Check.YEC_PU,
                                q_TheoreticalKitDate_PMI_Allocation.PO AS alloc_po,
                                q_TheoreticalKitDate_PMI_Incomplete.PO AS inc_po
                            FROM temp_mrp_check AS t_Check 
                            LEFT JOIN (
                                SELECT t_Check.PO, MAX(q_TheoreticalKitDate_work2.MaxPUD) AS MaxPUDOfMax, 
                                    t_Check.procurement, q_TheoreticalKitDate_work2.CheckOfMax, 
                                    IF(MAX(q_TheoreticalKitDate_work2.MaxPUD)>CURDATE()+60,'XXX',MAX(q_TheoreticalKitDate_work2.MaxPUD)) AS `Check`
                                FROM temp_mrp_check t_Check 
                                LEFT JOIN (
                                    SELECT q_TheoreticalKitDate_work.PO, 
                                        q_TheoreticalKitDate_work.VENDOR AS `PROC`, 
                                        q_TheoreticalKitDate_work.MaxPUD, 
                                        q_TheoreticalKitDate_work.CheckOfMax
                                    FROM (
                                        SELECT t_Check.PO, 
                                            q_TheoreticalKitDate_PROC.VENDOR, 
                                            t_Check.MCode, 
                                            MAX(t_Check.YEC_PU) AS MaxPUD, 
                                            MAX(t_Check.Check) AS CheckOfMax
                                        FROM temp_mrp_check AS t_Check 
                                        LEFT JOIN (
                                            SELECT `code`, vendor
                                            FROM temp_mrp_xitem
                                        )q_TheoreticalKitDate_PROC 
                                        ON t_Check.MCode = q_TheoreticalKitDate_PROC.CODE
                                        GROUP BY t_Check.PO, 
                                            q_TheoreticalKitDate_PROC.VENDOR, 
                                            t_Check.MCode
                                        ORDER BY t_Check.PO, 
                                            t_Check.MCode
                                    ) q_TheoreticalKitDate_work
                                    GROUP BY q_TheoreticalKitDate_work.PO, 
                                        q_TheoreticalKitDate_work.VENDOR, 
                                        q_TheoreticalKitDate_work.MaxPUD, 
                                        q_TheoreticalKitDate_work.CheckOfMax
                                ) q_TheoreticalKitDate_work2 ON t_Check.PO=q_TheoreticalKitDate_work2.PO
                                GROUP BY t_Check.PO, t_Check.procurement, q_TheoreticalKitDate_work2.CheckOfMax
                                HAVING (((t_Check.procurement)='PMI') AND ((q_TheoreticalKitDate_work2.CheckOfMax) LIKE 'Allocation'))
                            )q_TheoreticalKitDate_PMI_Allocation 
                            ON t_Check.PO=q_TheoreticalKitDate_PMI_Allocation.PO
                            LEFT JOIN (
                                SELECT t_Check.PO, 
                                    q_TheoreticalKitDate_work2.MaxPUD, 
                                    t_Check.procurement, 
                                    q_TheoreticalKitDate_work2.CheckOfMax, 
                                    'XXX' AS KitStatus
                                FROM temp_mrp_check AS t_Check 
                                INNER JOIN (
                                    SELECT q_TheoreticalKitDate_work.PO, 
                                        q_TheoreticalKitDate_work.VENDOR AS `PROC`, 
                                        q_TheoreticalKitDate_work.MaxPUD, 
                                        q_TheoreticalKitDate_work.CheckOfMax
                                    FROM (
                                        SELECT t_Check.PO, 
                                            q_TheoreticalKitDate_PROC.VENDOR, 
                                            t_Check.MCode, 
                                            MAX(t_Check.YEC_PU) AS MaxPUD, 
                                            MAX(t_Check.Check) AS CheckOfMax
                                        FROM temp_mrp_check AS t_Check 
                                        LEFT JOIN (
                                            SELECT `code`, vendor
                                            FROM temp_mrp_xitem
                                        )q_TheoreticalKitDate_PROC 
                                        ON t_Check.MCode = q_TheoreticalKitDate_PROC.CODE
                                        GROUP BY t_Check.PO, 
                                            q_TheoreticalKitDate_PROC.VENDOR, 
                                            t_Check.MCode
                                        ORDER BY t_Check.PO, 
                                            t_Check.MCode
                                    ) q_TheoreticalKitDate_work
                                    GROUP BY q_TheoreticalKitDate_work.PO, 
                                        q_TheoreticalKitDate_work.VENDOR, 
                                        q_TheoreticalKitDate_work.MaxPUD, 
                                        q_TheoreticalKitDate_work.CheckOfMax
                                ) q_TheoreticalKitDate_work2 
                                ON t_Check.PO=q_TheoreticalKitDate_work2.PO
                                GROUP BY t_Check.PO, 
                                    q_TheoreticalKitDate_work2.MaxPUD, 
                                    t_Check.procurement, 
                                    q_TheoreticalKitDate_work2.CheckOfMax, 
                                    'XXX'
                                HAVING ((NOT (t_Check.PO) IS NULL) 
                                    AND ((t_Check.procurement) LIKE 'P%') 
                                    AND ((q_TheoreticalKitDate_work2.CheckOfMax) IS NULL))
                            )q_TheoreticalKitDate_PMI_Incomplete 
                            ON t_Check.PO=q_TheoreticalKitDate_PMI_Incomplete.PO
                            GROUP BY t_Check.PO, 
                                t_Check.procurement, 
                                t_Check.Check, 
                                t_Check.YEC_PU, 
                                q_TheoreticalKitDate_PMI_Allocation.PO, 
                                q_TheoreticalKitDate_PMI_Incomplete.PO
                            HAVING (((t_Check.procurement) NOT LIKE 'Y%') 
                                AND ((t_Check.Check)='FromStock') 
                                AND ((q_TheoreticalKitDate_PMI_Allocation.PO) IS NULL) 
                                AND ((q_TheoreticalKitDate_PMI_Incomplete.PO) IS NULL))
                        ) q_TheoreticalKitDate_PMI_Available ON t_Check.PO=q_TheoreticalKitDate_PMI_Available.PO
                        LEFT JOIN (
                            SELECT t_Check.PO, 
                                q_TheoreticalKitDate_work2.MaxPUD, 
                                t_Check.procurement, 
                                q_TheoreticalKitDate_work2.CheckOfMax, 
                                'XXX' AS KitStatus
                            FROM temp_mrp_check AS t_Check 
                            INNER JOIN (
                                SELECT q_TheoreticalKitDate_work.PO, 
                                    q_TheoreticalKitDate_work.VENDOR AS `PROC`, 
                                    q_TheoreticalKitDate_work.MaxPUD, 
                                    q_TheoreticalKitDate_work.CheckOfMax
                                FROM (
                                    SELECT t_Check.PO, 
                                        q_TheoreticalKitDate_PROC.VENDOR, 
                                        t_Check.MCode, 
                                        MAX(t_Check.YEC_PU) AS MaxPUD, 
                                        MAX(t_Check.Check) AS CheckOfMax
                                    FROM temp_mrp_check AS t_Check 
                                    LEFT JOIN (
                                        SELECT `code`, vendor
                                        FROM temp_mrp_xitem
                                    )q_TheoreticalKitDate_PROC 
                                    ON t_Check.MCode = q_TheoreticalKitDate_PROC.CODE
                                    GROUP BY t_Check.PO, 
                                        q_TheoreticalKitDate_PROC.VENDOR, 
                                        t_Check.MCode
                                    ORDER BY t_Check.PO, 
                                        t_Check.MCode
                                ) q_TheoreticalKitDate_work
                                GROUP BY q_TheoreticalKitDate_work.PO, 
                                    q_TheoreticalKitDate_work.VENDOR, 
                                    q_TheoreticalKitDate_work.MaxPUD, 
                                    q_TheoreticalKitDate_work.CheckOfMax
                            ) q_TheoreticalKitDate_work2 
                            ON t_Check.PO=q_TheoreticalKitDate_work2.PO
                            GROUP BY t_Check.PO, 
                                q_TheoreticalKitDate_work2.MaxPUD, 
                                t_Check.procurement, 
                                q_TheoreticalKitDate_work2.CheckOfMax, 
                                'XXX'
                            HAVING ((NOT (t_Check.PO) IS NULL) 
                                AND ((t_Check.procurement) LIKE 'P%') 
                                AND ((q_TheoreticalKitDate_work2.CheckOfMax) IS NULL))
                        ) q_TheoreticalKitDate_PMI_Incomplete ON t_Check.PO=q_TheoreticalKitDate_PMI_Incomplete.PO
                        GROUP BY t_Check.PO, q_TheoreticalKitDate_PROC.VENDOR, 
                                q_TheoreticalKitDate_PMI_Allocation.MaxPUDOfMax, 
                                q_TheoreticalKitDate_PMI_Available.KitStatus, 
                                q_TheoreticalKitDate_PMI_Incomplete.KitStatus
                        HAVING (((q_TheoreticalKitDate_PROC.VENDOR) LIKE 'P%'))
                    ) AS q_TheoreticalKitDate_PMI 
                    ON t_OrderBalance.PO = q_TheoreticalKitDate_PMI.PO
                    GROUP BY t_OrderBalance.PO, 
                        t_OrderBalance.DCode, 
                        t_OrderBalance.DName, 
                        t_OrderBalance.OrdQty, 
                        t_OrderBalance.OrdBal, 
                        t_OrderBalance.OrdDate, 
                        t_OrderBalance.DueDate, 
                        t_OrderBalance.CustCode, 
                        t_OrderBalance.CustName, 
                        q_TheoreticalKitDate_YEC.PROC, 
                        q_TheoreticalKitDate_YEC.MaxPUD_Y, 
                        q_TheoreticalKitDate_YEC.Stock_Y, 
                        q_TheoreticalKitDate_YEC.NoSched_Y, 
                        q_TheoreticalKitDate_PMI.PROC, 
                        q_TheoreticalKitDate_PMI.MaxPUD_P, 
                        q_TheoreticalKitDate_PMI.Stock_P, 
                        q_TheoreticalKitDate_PMI.NoSched_P
                ");

            // var_dump($q_TheoreticalKitDate);
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $q_TheoreticalKitDate;
    }

    private function truncateTable($table)
    {
        return DB::connection($this->mysql)->table($table)->truncate();
    }
}