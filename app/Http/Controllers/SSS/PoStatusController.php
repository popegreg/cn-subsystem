<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PoStatusController.php
     MODULE NAME:  3008-1 - PO Status
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.05.03
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.03     MESPINOSA       Initial Draft
     100-00-02   1     2016.05.18     MESPINOSA       Retrieve data from MySQL.
*******************************************************************************/
?>
<?php
namespace App\Http\Controllers\SSS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use Config;
use Carbon\Carbon;
use PDF;
use File;
use App;
use Response;

/**
* PoStatus Controller
*/
class  PoStatusController extends Controller
{

    protected $mysql;
    protected $mssql;
    protected $common;

    # PoStatusController constructor.
    public function __construct()
    {
        $this->middleware('auth');
        header("Content-Type: text/html; charset=SHIFT-JIS");
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'sss');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    /**
    * Get All OrderDataReports.
    */
    public function getPoStatus(Request $request_data)
    {
        $po = trim($request_data['po']);

        # for access rights checking.
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_POSTATS')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            # for initial page loading.
            if(!empty($po))
            {
                $result_po = $this->retrievePo($po
                    ,Config::get('constants.EMPTY_FILTER_VALUE'), $code);
                // $result_answer = $this->retrieveAnswer($po);
                // $result_details = $this->retrieveDetails($po, '');

                return view('sss.PoStatus', 
                        ['userProgramAccess' => $userProgramAccess, 
                        'po' => $result_po,
                        // 'answers' => $result_answer,
                        // 'details_data' => $result_details,
                        'po_s' => $po,
                        'name' => '']);
            }
            # for page reload with po parameter.
            else
            {
                $po=Config::get('constants.EMPTY_FILTER_VALUE');
                $result_po = $this->retrievePo($po
                    ,Config::get('constants.EMPTY_FILTER_VALUE'), $code);
                // $result_answer = $this->retrieveAnswer(Config::get('constants.EMPTY_FILTER_VALUE'));
                // $result_details = $this->retrieveDetails($code, '');

                // $result_po = "";
                // $result_answer = "";
                // $result_details = "";

                return view('sss.PoStatus', 
                        ['userProgramAccess' => $userProgramAccess, 
                        'po' => $result_po,
                        // 'answers' => $result_answer,
                        // 'details_data' => $result_details,
                        'code' => '',
                        'po_s' => $po,
                        'name' => '']);
            }

        }
    }

    public function ajaxPOStatus(Request $request_data)
    {
        $po = trim($request_data['po']);
        $name = trim($request_data['name']);

        
        $result_po = $this->retrievePo($po
                    ,Config::get('constants.EMPTY_FILTER_VALUE'), $code);
        $result_answer = $this->retrieveAnswer($po);
        $result_details = $this->retrieveDetails($po, $name,$po,null,null,$request_data->row);

        $data = [
            'po' => $result_po,
            'answers' => $result_answer,
            'details_data' => $result_details,
            'po_s' => $po,
            'code' => $code,
        ];

        return $data;
    }

    /**
    * Reload Po Status.
    */
    public function postPoStatus(Request $request_data)
    {
        $result = false;
        $po = trim($request_data['po']);
        $name = trim($request_data['name']);
        $message = '';

        # for access rights checking.
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PRRS')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            try
            {
                if(empty($po))
                {
                    $po = Config::get('constants.EMPTY_FILTER_VALUE');
                }

                if(empty($name))
                {
                    $name = Config::get('constants.EMPTY_FILTER_VALUE');   
                }
                
                $result_po = $this->retrievePo($po,$name, $code);
                // $result_answer = $this->retrieveAnswer($po);
                // $result_details = $this->retrieveDetails($code, '', $po);

            }
            catch (Exception $e) 
            {
                Log::error($e->getMessage());
                echo 'Message: ' .$e->getMessage();
            }

            return view('sss.PoStatus', 
                    ['userProgramAccess' => $userProgramAccess, 
                    'po' => $result_po,
                    // 'answers' => $result_answer,
                    // 'details_data' => $result_details,
                    'po_s' => $po,
                    'name' => $name]);
        }
    }

    /**
    * Get PO Details.
    **/
    private function retrievePo(&$po, $name, &$code)
    {
        try
        {
            if($name == Config::get('constants.EMPTY_FILTER_VALUE') 
                && $po <> Config::get('constants.EMPTY_FILTER_VALUE'))
            {
                # PO is not empty.
                $result = DB::connection($this->mysql)->table('temp_sss_mrplist')
                ->select(
                        DB::raw("SUBSTRING(po, 1, 10) AS PO")
                        , 'dcode as Code'
                        , 'dname as Name'
                        , DB::raw("CONCAT(custcode, ' ',custname) as Customer")
                        , DB::raw("CONCAT(balreq, ' / ', schdqty) as Qty")
                        , DB::raw("balreq as KVOL")
                        , DB::raw("(CASE orddate 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(orddate, '%m/%d/%y') 
                                   END) AS PODate")
                        , DB::raw("(CASE duedate 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(duedate, '%m/%d/%y') 
                                   END) AS Demand")
                        , DB::raw(" '' as POTime ")
                        , 'supname as UpdatedBy'
                        , DB::raw(" NULL as Remarks ")
                        )
                ->where('po', 'like', $po . '%')
                ->skip(0)->take(1) 
                ->get();

            }
            elseif($name <> Config::get('constants.EMPTY_FILTER_VALUE') 
                && $po == Config::get('constants.EMPTY_FILTER_VALUE'))
            {
                # Name is not empty
                $result = DB::connection($this->mysql)->table('temp_sss_mrplists')
                ->select(
                        DB::raw("SUBSTRING(po, 1, 10) AS PO")
                        , 'dcode as Code'
                        , 'dname as Name'
                        , DB::raw("CONCAT(custcode, ' ',custname) as Customer")
                        , DB::raw("CONCAT(balreq, ' / ', schdqty) as Qty")
                        , DB::raw("balreq as KVOL")
                        , DB::raw("(CASE orddate 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(orddate, '%m/%d/%y') 
                                   END) AS PODate")
                        , DB::raw("(CASE duedate 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(duedate, '%m/%d/%y') 
                                   END) AS Demand")
                        , DB::raw(" '' as POTime ")
                        , 'supname as UpdatedBy'
                        , DB::raw(" NULL as Remarks ")
                        )
                ->where('dname', $name)
                ->skip(0)->take(1) 
                ->get();
            }
            else
            { 
                # PO and Name is not empty.
                $result = DB::connection($this->mysql)->table('temp_sss_mrplist')
                ->select(
                        DB::raw("SUBSTRING(po, 1, 10) AS PO")
                        , 'dcode as Code'
                        , 'dname as Name'
                        , DB::raw("CONCAT(custcode, ' ',custname) as Customer")
                        , DB::raw("CONCAT(balreq, ' / ', schdqty) as Qty")
                        , DB::raw("balreq as KVOL")
                        , DB::raw("(CASE orddate 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(orddate, '%m/%d/%y') 
                                   END) AS PODate")
                        , DB::raw("(CASE duedate 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(duedate, '%m/%d/%y') 
                                   END) AS Demand")
                        , DB::raw(" '' as POTime ")
                        , 'supname as UpdatedBy'
                        , DB::raw(" NULL as Remarks ")
                        )
                ->where('po', 'like', $po . '%')
                ->where('dname', $name)
                ->skip(0)->take(1) 
                ->get();   
            }

            # retrieve the Code and PO for Details data loading.
            foreach ($result as $key => $value) 
            {
                $value = get_object_vars($value);
                $code = $value['Code'];
                $po = $value['PO'];
                break;
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
        return $result;
    }

    /**
    * Get R3 Answer.
    **/
    private function retrieveAnswer($po)
    {
        try
        {
            # get R3Answer data.
            $result = DB::connection($this->mysql)->table('temp_sss_prdanswer')
            ->select('po'
                , 'r3answer'
                // , DB::raw("(CASE r3answer 
                //             WHEN '0000-00-00' THEN NULL 
                //             ELSE DATE_FORMAT(r3answer, '%m/%d/%y') 
                //            END) AS r3answer")
                , 'time'
                , 'qty' 
                , 're')
            ->where('po', 'like', $po . '%')
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
    * Get the details of the PO.
    * All or filtered by PODate.
    **/
    private function retrieveDetails($po_s, $name, $po = NULL,
        $from_date = NULL, $to_date = NULL, $row = NULL)
    {        
        try
        {
            if($po == NULL)
            {
                $po = ' AND po IS NULL';
            }
            else
            {
                $po = " AND po ='" . $po . "'";
            }

            // $var_dump = $po;

            if($from_date === NULL && $to_date === NULL)
            {
                # get all Details data.
                $result = DB::connection($this->mysql)->table('temp_sss_mrplist')
                ->select('dcode as CODE'
                    , 'mcode as KCODE'
                    , 'mname as PNAME'
                    , 'vendor as VI'
                    , 'orderqty AS PoReq'
                    , 'orderbal as PoBalance'
                    , 'ttlbalreq as GrossReq'
                    , DB::raw("IFNULL(assy100,0) AS ASSY100")
                    , DB::raw("IFNULL(whs100,0) AS WHS100")
                    , DB::raw("IFNULL(whs102,0) AS WHS102")
                    , DB::raw("IFNULL(ttlcurrinvtry,0) AS TOTAL")
                    // , 'assy100 AS ASSY100'
                    // , 'whs100 AS WHS100'
                    // , 'whs102 AS WHS102'
                    // , 'ttlcurrinvtry AS TOTAL'
                    , 'reqaccum as InvResr'
                    , 'ttlprbal as PrBal'
                    , 'mrp as MRP'
                    , DB::raw("(CASE prissued 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(prissued, '%m/%d/%y') 
                               END) AS PR_Issued")
                    , 'pr as PR'
                    , 'flight as PickUpGR'
                    , 'yecpo as YecPo'
                    , DB::raw("(CASE yecpu 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(yecpu, '%m/%d/%y') 
                               END) AS YecPu")
                    , 'deliqty as PuQty'
                    , 'check as Check'
                    , 'deliaccum as Deliaccum'
                    , 'supname as Incharge'
                    , 're as RE'
                    , 'status as Status'
                    , 'alloccalc as AllocationCalc'
                    )
                ->where('yecpo',$po_s)
                // ->skip(0)->take($row)
                ->get();
            }
            else
            {
                # get Details data filteres with PO Date.
                $result = DB::connection($this->mysql)->table('temp_sss_mrplist')
                ->select('dcode as CODE'
                    , 'mcode as KCODE'
                    , 'mname as PNAME'
                    , 'vendor as VI'
                    , 'schdqty AS PoReq'
                    , 'balreq as PoBalance'
                    , 'ttlbalreq as GrossReq'
                    , 'assy100 AS ASSY100'
                    , 'whs100 AS WHS100'
                    , 'whs102 AS WHS102'
                    , 'ttlcurrinvtry AS TOTAL'
                    , 'reqaccum as InvResr'
                    , 'ttlprbal as PrBal'
                    , 'mrp as MRP'
                    , DB::raw("(CASE prissued 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(prissued, '%m/%d/%y') 
                               END) AS PR_Issued")
                    , 'pr as PR'
                    , 'flight as PickUpGR'
                    , 'yecpo as YecPo'
                    , DB::raw("(CASE yecpu 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(yecpu, '%m/%d/%y') 
                               END) AS YecPu")
                    , 'deliqty as PuQty'
                    , 'check as Check'
                    , 'deliaccum as Deliaccum'
                    , 'supname as Incharge'
                    , 're as RE'
                    , 'status as Status'
                    , 'alloccalc as AllocationCalc'
                    )
                ->whereRaw(" yecpo = '". $po .
                 "' AND orddate 
                 BETWEEN STR_TO_DATE('" . $from_date ."', '%m/%d/%Y') 
                 AND STR_TO_DATE('" . $to_date ."', '%m/%d/%Y')" )
                // ->skip(0)->take($row)
                ->get();
            }

        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }

        return $result;
    }

    /**
    * Export the report to PDF.
    **/
    public function printToPdf(Request $request_data)
    {
        # maximum execution time -1 is infinite.
        ini_set('max_execution_time', -1);

        $po = trim($request_data['po']);
        $from = trim($request_data['from']);
        $to = trim($request_data['to']);
        $po_obj = Array();
        $name = Config::get('constants.EMPTY_FILTER_VALUE');

        try
        {
            if(empty($po))
            {
                $po = Config::get('constants.EMPTY_FILTER_VALUE');
            }

            $result_po = $this->retrievePo($po, $name, $code);
            $result_answer = $this->retrieveAnswer($po);

            if(empty($from) || empty($to))
            {
                $result_details = $this->retrieveDetails($po, '');
            }
            else
            {
                $result_details = $this->retrieveDetails($po, '', '', $from, $to);   
            }
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }

        # display empty values if no data found.
        if(count($result_po) <= 0)
        {
            $po_obj['po'] = '';
            $po_obj['code'] = '';
            $po_obj['name'] = '';
            $po_obj['cust'] = '';
            $po_obj['qty'] = '';
            $po_obj['kvol'] = '';
            $po_obj['podate'] = '';
            $po_obj['demand'] = '';
            $po_obj['time'] = '';
            $po_obj['by'] = '';
            $po_obj['remarks'] = '';
        }
        else
        {
            # get the PO information.
            foreach ($result_po as $key => $value) 
            {
                $value = get_object_vars($value);
                $po_obj['po'] = $value['PO'];
                $po_obj['code'] = $value['Code'];
                $po_obj['name'] = $value['Name'];
                $po_obj['cust'] = $value['Customer'];
                $po_obj['qty'] = $value['Qty'];
                $po_obj['kvol'] = $value['KVOL'];
                $po_obj['podate'] = $value['PODate'];
                $po_obj['demand'] = $value['Demand'];
                $po_obj['time'] = $value['POTime'];
                $po_obj['by'] = $value['UpdatedBy'];
                $po_obj['remarks'] = $value['Remarks'];
                break;
            }
        }

        # generate report in html format for PDF transformation.
        $report1 = '
                    <h3 style="box-sizing: border-box; line-height: 1.1; margin-top: 20px; 
        margin-bottom: 10px; font-size: 18px;">PO STATUS CONFIRMATION</h3>
                <table border="0" cellpadding="0" cellspacing="0" style="width: 1000px; font-size: 8px;">
                    <tbody>
                        <tr>
                            <td style="width: 500px;">
                                <table border="0" cellpadding="1" cellspacing="1" style="width: 500px;">
                                    <tbody>
                                        <tr>
                                            <td>PO :</td>
                                            <td><strong>'.$po_obj['po'].'</strong></td>
                                            <td>PO DATE :</td>
                                            <td>'.$po_obj['podate'].'</td>
                                        </tr>
                                        <tr>
                                            <td>CODE :</td>
                                            <td>'.$po_obj['code'].'</td>
                                            <td>DEMAND :</td>
                                            <td>'.$po_obj['demand'].'</td>
                                        </tr>
                                        <tr>
                                            <td>NAME :</td>
                                            <td><strong>'.$po_obj['name'].'</strong></td>
                                            <td>UPDATED BY :</td>
                                            <td>'.$po_obj['by'].'</td>
                                        </tr>
                                        <tr>
                                            <td>CUSTOMER :</td>
                                            <td colspan="3" >'. $po_obj['cust'] .'</td>
                                        </tr>
                                        <tr>
                                            <td>QUANTITY :</td>
                                            <td colspan="3" >'.$po_obj['qty'].'</td>
                                        </tr>
                                        <tr>
                                            <td>MEMO / REMINDERS:</td>
                                            <td colspan="3" ></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" rowspan="1">
                                            <span style="line-height: 20.8px;">'.$po_obj['remarks']
                                            .'</span></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <p></p>
                            </td>
                            <td style="vertical-align: top; width: 70px;"/>
                            <td style="vertical-align: top; width: 430px;">
                                <h3><strong>R3 ANSWER</strong></h3>

                                <table border="1" cellpadding="0" cellspacing="0" style="width: 400px; font-size: 8px;">
                                    <tbody>
                                        <tr>
                                            <td><strong>PO</strong></td>
                                            <td><strong>R3Answer</strong></td>
                                            <td><strong>Time</strong></td>
                                            <td><strong>Qty</strong></td>
                                            <td><strong>Remarks</strong></td>
                                        </tr>';
            $table1 = '';
            # plot all r3answer data.
            foreach ($result_answer as $key => $value) {
                $value = get_object_vars($value);
                $table1 =  $table1 . '<tr>
                                            <td>'.$value['po'].'</td>
                                            <td>'.$value['r3answer'].'</td>
                                            <td style="text-align: right;">'.$value['time'].'</td>
                                            <td style="text-align: right;">'.$value['qty'].'</td>
                                            <td>'.$value['re'].'</td>
                                        </tr>';
            }

            $table2 = '</tbody>
                                </table>
                                <p></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <h3><strong>Details</strong></h3>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: center;">
                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 10px;">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: center;"><strong>CODE</strong></td>
                                            <td style="text-align: center;"><strong>NAME</strong></td>
                                            <td style="text-align: center;"><strong>VI</strong></td>
                                            <td style="text-align: center;"><strong>PO Req</strong></td>
                                            <td style="text-align: center;"><strong>PO Balance</strong></td>
                                            <td style="text-align: center;"><strong>Gross Req</strong></td>
                                            <td style="text-align: center;"><strong>ASSY100</strong></td>
                                            <td style="text-align: center;"><strong>WHS100</strong></td>
                                            <td style="text-align: center;"><strong>WHS102</strong></td>
                                            <td style="text-align: center;"><strong>TOTAL</strong></td>
                                            <td style="text-align: center;"><strong>INV RESR</strong></td>
                                            <td style="text-align: center;"><strong>PR BAL</strong></td>
                                            <td style="text-align: center;"><strong>MRP</strong></td>
                                            <td style="text-align: center;"><strong>PR ISSUED</strong></td>
                                            <td style="text-align: center;"><strong>PR</strong></td>
                                            <td style="text-align: center;"><strong>PICK UP (GR)</strong></td>
                                            <td style="text-align: center;"><strong>YECPO</strong></td>
                                            <td style="text-align: center;"><strong>PEC P/U</strong></td>
                                            <td style="text-align: center;"><strong>P/U QTY</strong></td>
                                            <td style="text-align: center;"><strong>CHECK</strong></td>
                                            <td style="text-align: center;"><strong>DELI ACCUM</strong></td>
                                            <td style="text-align: center;"><strong>IN CHARGE</strong></td>
                                            <td style="text-align: center;"><strong>RE</strong></td>
                                            <td style="text-align: center;"><strong>STATUS</strong></td>
                                            <td style="text-align: center;"><strong>ALLOCATION CALC</strong></td>
                                        </tr>';
        $report2 = '';
        
        #plot all Details data.
        foreach ($result_details as $key => $value) 
        {
            $value = get_object_vars($value);

            if($value['Check'] == 'FromStock')
            {
                $col_check = '<td style="text-align: center; background-color: lightblue">'.$value['Check'].'</td>';
            }
            else if($value['Check'] == 'Allocation')
            {
                $col_check = '<td style="text-align: center; background-color: red">'.$value['Check'].'</td>';
            }
            else
            {
                $col_check = '<td style="text-align: center;">'.$value['Check'].'</td>';
            }

            $report2 = $report2 . '<tr>
                            <td style="text-align: center;">'.$value['KCODE'].'</td>
                            <td style="text-align: center;">'.$value['PNAME'].'</td>
                            <td style="text-align: center;">'.$value['VI'].'</td>
                            <td style="text-align: right;">'.$value['PoReq'].'</td>
                            <td style="text-align: right;">'.$value['PoBalance'].'</td>
                            <td style="text-align: right;">'.$value['GrossReq'].'</td>
                            <td style="text-align: right;">'.$value['ASSY100'].'</td>
                            <td style="text-align: right;">'.$value['WHS100'].'</td>
                            <td style="text-align: right;">'.$value['WHS102'].'</td>
                            <td style="text-align: right;">'.$value['TOTAL'].'</td>
                            <td style="text-align: right;">'.$value['InvResr'].'</td>
                            <td style="text-align: right;">'.$value['PrBal'].'</td>
                            <td style="text-align: right;">'.$value['MRP'].'</td>
                            <td style="text-align: center;">'.$value['PR_Issued'].'</td>
                            <td style="text-align: center;">'.$value['PR'].'</td>
                            <td style="text-align: center;">'.$value['PickUpGR'].'</td>
                            <td style="text-align: center;">'.$value['YecPo'].'</td>
                            <td style="text-align: center;">'.$value['YecPu'].'</td>
                            <td style="text-align: right;">'.$value['PuQty'].'</td>'
                            . $col_check .
                            '<td style="text-align: right;">'.$value['Deliaccum'].'</td>
                            <td style="text-align: center;">'.$value['Incharge'].'</td>
                            <td style="text-align: center;">'.$value['RE'].'</td>
                            <td style="text-align: center;">'.$value['Status'].'</td>
                            <td style="text-align: center;">'.$value['AllocationCalc'].'</td>
                        </tr>';
        }
        $report3 = '</tbody>
                                </table>
                                <p></p>
                            </td>
                        </tr>
                    </tbody>
                </table>';

        # gather all html parts.
        $html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . $report1 . $table1 . $table2 . $report2. $report3;

        // $pdf = PDF::loadHTML($html)->setPaper('letter', 'landscape');
        // return $pdf->stream('PO_Status_'.Carbon::now().'.pdf');
        $data = [
            'po_obj' => $po_obj,
            'result_answer' => $result_answer,
            'result_details' => $result_details
        ];

        return PDF::loadView('pdf.postatus', $data)
                ->setPaper('A4')
                ->setOrientation('landscape')
                // ->setOption('header-html', url('/material-list-header/'.$header))
                ->setOption('margin-top', 10)
                ->setOption('margin-bottom', 15)
                ->stream('PO_Status_'.Carbon::now().'.pdf');

        // # apply snappy pdf wrapper
        // $pdf = App::make('snappy.pdf.wrapper');
        // # transform html to pdf format.
        // $pdf->loadHTML($html)->setPaper('A4')->setOrientation('landscape');
        // # display PDF report to response.
        // return $pdf->inline();     
    }
}