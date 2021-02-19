<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PartsStatusController.php
     MODULE NAME:  [3008-2] Parts Status
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.05.24
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.24     MESPINOSA       Initial Draft
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
class  PartsStatusController extends Controller
{

    protected $mysql;
    protected $mssql;
    protected $common;

    public function __construct()
    {
        $this->middleware('auth');
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
    public function getPartsStatus(Request $request_data)
    {
        $name = trim($request_data['name']);

        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_SSS')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            if(empty($name))
            {
                $name = Config::get('constants.EMPTY_FILTER_VALUE');
            }

            $result_part = $this->retrieveDetails($name);
            $result_t1 = $this->retrieveTable1($name);
            $result_t2 = $this->retrieveTable2($name);

            return view('sss.PartsStatus', 
                    ['userProgramAccess' => $userProgramAccess
                    , 'parts' => $result_part
                    , 't1' => $result_t1
                    , 't2' => $result_t2
                    , 'name' => $name]);
        }
    }

    /**
    * Get Details of the Supplier.
    **/
    private function retrieveDetails($name)
    {        
        try
        {
            $result = DB::connection($this->mysql)->table('temp_sss_mrplist')
            ->select('mcode AS CODE'
                        , 'mname AS NAME'
                        , DB::raw("sum(assy100) AS ASSY100")
                        , DB::raw("sum(whs100) AS WHS100")
                        , DB::raw("sum(whs102) AS WHS102")
                        , DB::raw("sum(ttlcurrinvtry) AS TOTAL")
                        , DB::raw("sum(ttlbalreq) AS GROSS_REQ")
                        , DB::raw("sum(ttlcurrinvtry) - sum(ttlbalreq) AS EXCESS")
                        , DB::raw("sum(mrp) as MRP")
                        , DB::raw("(CASE prissued 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(prissued, '%m/%d/%y') 
                                   END) AS PR_ISSUED")
                        , DB::raw("sum(ttlprbal) AS PR_BAL")
                        , DB::raw("sum(ttlprbal) + (sum(ttlcurrinvtry) - sum(ttlbalreq)) AS STOCK"))
            ->where('mname', $name)
            ->groupBy('mcode', 'mname', 'prissued')
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
    * Get the data of Table 1.
    **/
    private function retrieveTable1($name)
    {
        try
        {
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
            ->where('mname', $name)
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
    private function retrieveTable2($name)
    {
        try
        {
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
            ->where('mname', $name)
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
    public function postPrintPartsStatus(Request $request_data)
    {
        # get the selected supplier and db connection.
        $name = $request_data['name'];
        $data = array();

        # retrieve data
        $result_t2 = $this->retrieveTable2($name);
        
        # convert the object result to array readable format.
        foreach ($result_t2 as $datareport) 
        {
            $data[] = (array)$datareport;
            #or first convert it and then change its properties using 
            #an array syntax, it's up to you
        }

        # Create and export excel by feeding the array result.
        Excel::create('Parts Status', function($excel) use($data) 
        {

            $excel->sheet('Parts Status', function($sheet) use($data) 
            {
                $sheet->fromArray($data);
            });

        })->export('xls');
    }
}