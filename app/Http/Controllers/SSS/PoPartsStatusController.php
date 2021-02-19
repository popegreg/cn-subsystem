<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PoPartsStatusController.php
     MODULE NAME:  [3008-1] PO Status : Parts Status
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
use Excel;
use File;

/**
* PartsStatus Controller
*/
class  PoPartsStatusController extends Controller
{

    protected $mysql;
    protected $mssql;
    protected $common;

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
    public function getPoPartsStatus(Request $request_data)
    {
        $po = trim($request_data['po']);
        $haspart = trim($request_data['haspart']);
        $code = trim($request_data['code']);

        # check loginuser access rights.
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PRTSTATS')
                                    , $userProgramAccess))
        {
            #if no access redirect to home page.
            return redirect('/home');
        }
        else
        {
            if(empty($code))
            {
                # for empty result.
                $code = Config::get('constants.EMPTY_FILTER_VALUE');
            }

            $result_part = $this->retrieveDetails($code, $haspart);
            $result_t1 = $this->retrieveTable1($code, $haspart);
            $result_t2 = $this->retrieveTable2($code, $haspart);

            return view('sss.PoPartsStatus', 
                    ['userProgramAccess' => $userProgramAccess
                    , 'parts' => $result_part
                    , 't1' => $result_t1
                    , 't2' => $result_t2
                    , 'po' => $po
                    , 'has_part' => $haspart]);
        }
    }

    /**
    * Get Details of the Supplier.
    **/
    private function retrieveDetails($code, $haspart)
    {        
        try
        {
            # if page is comming from the Part Status BUTTON
            if($haspart==1)
            {
                $result = DB::connection($this->mysql)->table('temp_sss_mrplist')
                ->select('mcode AS CODE'
                            , 'mname AS NAME'
                            , DB::raw("sum(isnull(assy100,0)) AS ASSY100")
                            , DB::raw("sum(isnull(whs100,0)) AS WHS100")
                            , DB::raw("sum(isnull(whs102,0)) AS WHS102")
                            , DB::raw("sum(isnull(ttlcurrinvtry,0)) AS TOTAL")
                            , DB::raw("sum(isnull(ttlbalreq,0)) AS GROSS_REQ")
                            , DB::raw("sum(ttlcurrinvtry) - sum(ttlbalreq) AS EXCESS")
                            , DB::raw("sum(mrp) as MRP")
                            , DB::raw("(CASE prissued 
                                        WHEN '0000-00-00' THEN NULL 
                                        ELSE DATE_FORMAT(prissued, '%m/%d/%y') 
                                       END) AS PR_ISSUED")
                            , DB::raw("sum(ttlprbal) AS PR_BAL")
                            , DB::raw("sum(ttlprbal) + (sum(ttlcurrinvtry) - sum(ttlbalreq)) AS STOCK"))
                ->where('dcode', $code)
                ->groupBy('mcode', 'mname', 'prissued')
                ->get();
            }
            # if page is comming from the Part Status LINK
            else
            {
                $result = DB::connection($this->mysql)->table('temp_sss_mrplist')
                ->select('mcode AS CODE'
                    , 'mname AS NAME'
                    , 'assy100 AS ASSY100'
                    , 'whs100 AS WHS100'
                    , 'whs102 AS WHS102'
                    , 'ttlcurrinvtry AS TOTAL'
                    , 'ttlbalreq as GROSS_REQ'
                    , DB::raw("(ttlcurrinvtry - ttlbalreq) AS EXCESS")
                    , 'mrp as MRP'
                    , DB::raw("(CASE prissued 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(prissued, '%m/%d/%y') 
                               END) AS PR_ISSUED")
                    , 'ttlprbal as PR_BAL'
                    , DB::raw("(ttlprbal + (ttlcurrinvtry - ttlbalreq)) AS STOCK")
                    )
                ->where('mcode', $code)
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
    * Get the data of Table 1.
    **/
    private function retrieveTable1($code, $haspart)
    {
        $condition = '';
        try
        {
            # if page is comming from the Part Status BUTTON
            if ($haspart == '1')
            {
                $condition = 'dcode';
            }
            # if page is comming from the Part Status LINK
            else
            {
                $condition = 'mcode';
            }

            $result = DB::connection($this->mysql)->table('temp_sss_mrplist') 
                        ->select(DB::raw("(CASE prissued 
                                            WHEN '0000-00-00' THEN NULL 
                                            ELSE DATE_FORMAT(prissued, '%m/%d/%y') 
                                           END) AS PR_ISSUED")
                                , 'pr AS PR'
                                , DB::raw("(CASE yecpu 
                                            WHEN '0000-00-00' THEN NULL 
                                            ELSE DATE_FORMAT(yecpu, '%m/%d/%y') 
                                           END) AS YEC_PU")
                                , DB::raw("'' AS FI")
                                , 'deliqty AS DELIQTY'
                                , 'deliaccum AS DELIACCUM')
            ->where($condition, $code)
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
    * Get the data of Table 2.
    **/
    private function retrieveTable2($code, $haspart)
    {
        $condition = '';
        try
        {
            # if page is comming from the Part Status BUTTON
            if ($haspart == '1')
            {
                $condition = 'mcode';
            }
            # if page is comming from the Part Status LINK
            else
            {
                $condition = 'mcode';
            }

            $result = DB::connection($this->mysql)->table('temp_sss_mrplist') 
                        ->select(DB::raw("(CASE orddate 
                                            WHEN '0000-00-00' THEN NULL 
                                            ELSE DATE_FORMAT(orddate, '%m/%d/%y') 
                                           END) AS PODATE")
                                , DB::raw('SUBSTRING(po, 1,10) AS PO')
                                , 'dcode AS CODE'
                                , 'dname AS NAME'
                                , 'balreq AS POBAL'
                                , 'schdqty AS POQTY'
                                , DB::raw("(CASE duedate 
                                            WHEN '0000-00-00' THEN NULL 
                                            ELSE DATE_FORMAT(duedate, '%m/%d/%y') 
                                           END) AS DUEDATE")
                                , 'balreq AS POREQ'
                                , 'balreq AS BALREQ'
                                , 'alloccalc AS ALLOC'
                                , 'alloccalc AS ALLOCAL'
                                , 'custname AS CUSTOMERNAME')
            ->where($condition, $code)
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
    * Export the Table 2 to excel.
    **/
    public function postPrintPoPartsStatus(Request $request_data)
    {
        # get the selected supplier and db connection.
        $code = $request_data['code'];
        $has_part = $request_data['has_part'];
        $data = array();

        # retrieve data
        $result_t2 = $this->retrieveTable2($code, $has_part);
        
        # convert the object result to array readable format.
        foreach ($result_t2 as $datareport) 
        {
            $data[] = (array)$datareport;
            #or first convert it and then change its properties using 
            #an array syntax, it's up to you
        }

        # Create and export excel by feeding the array result.
        Excel::create('PO (Parts Status)', function($excel) use($data) 
        {

            $excel->sheet('Parts Status', function($sheet) use($data) 
            {
                $sheet->fromArray($data);
            });

        })->export('xls');
    }
}