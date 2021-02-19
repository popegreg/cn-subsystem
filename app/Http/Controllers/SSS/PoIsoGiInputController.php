<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PoIsoGiInputController.php
     MODULE NAME:  [3008-1] PO Status - ISO GI Input
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
* IsoGiInput Controller
*/
class  PoIsoGiInputController extends Controller
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
    public function getPoIsoGiInput(Request $request_data)
    {
        $po = trim($request_data['po']);
        $code = trim($request_data['code']);
        $datefrom = trim($request_data['from']);
        $dateto = trim($request_data['to']);
        
        # checking of login user access rights.
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_SSS')
                                    , $userProgramAccess))
        {
            # redirect to home page is user has no access to this page.
            return redirect('/home');
        }
        else
        {
            $result_t1 = $this->retrieveTable1($code);
            $result_t2 = $this->retrieveTable2($code, $datefrom, $dateto);

            return view('sss.PoIsoGiInput', 
                    ['userProgramAccess' => $userProgramAccess
                    , 'po' => $po
                    , 'code' => $code
                    , 'datefrom' => $datefrom
                    , 'dateto' => $dateto
                    , 't1' => $result_t1
                    , 't2' => $result_t2]);
        }
    }

    /**
    * Get the data of Table 1.
    **/
    private function retrieveTable1($name)
    {
        try
        {
            # retrieve ISO GI data from tbl_isogi_input.
            $result = DB::connection($this->mysql)->table('tempzymr0120 as iso')
            ->join('temp_sss_mrplist as mrp', 'mrp.mname', '=', 'iso.itemname')
            ->select(
                    DB::raw("(CASE mrp.orddate 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(mrp.orddate, '%m/%d/%y') 
                            END) AS PODATE")
                    , DB::raw('SUBSTRING(mrp.po,1,10) as PO')
                    , 'iso.itemcode as CODE'
                    , 'iso.itemname as NAME'
                    , 'mrp.orderbal as POBAL'
                    , 'mrp.orderqty as POQTY'
                    , DB::raw("(CASE mrp.duedate 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(mrp.duedate, '%m/%d/%y') 
                            END) AS DUEDATE")
                    , 'mrp.reqaccum as POREQ'
                    , 'mrp.balreq as BALREQ'
                    , DB::raw(" 0 as ALLOC")
                    , 'mrp.alloccalc as ALLOCAL'
                    , 'mrp.custname as CUSTOMER'
                    )
            ->where('iso.itemname', '=', $name)
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
    private function retrieveTable2($name, $from_date = NULL, $to_date = NULL)
    {
        try
        {

            if(empty($from_date)){ $from_date = NULL; };
            if(empty($to_date)){ $to_date = NULL; };

            if($from_date === NULL && $to_date === NULL)
            {
                # retieve all data is no Pickup Date filter.
                $result = DB::connection($this->mysql)->table('tempzymr0120 as iso')
                ->join('temp_sss_mrplist as mrp', 'mrp.mname', '=', 'iso.itemname')
                ->select(
                        DB::raw('SUBSTRING(mrp.po,1,10) as PO')
                        , 'iso.itemcode as CODE'
                        , 'iso.itemname as NAME'
                        , 'iso.qty as PUQTY'
                        , 'iso.vendor as SUPCODE'
                        , 'mrp.supname as SUPNAME'
                        , 'iso.specify_period as PICKUPDATE' //pickup_date
                        , 'iso.text as REMARKS' //remarks
                        , 'iso.po as ISO_PO' //po
                        , 'iso.purchasing_group as PRODNAME' //production_name
                        , 'iso.drawing_num as PR' //pr
                        , 'mrp.orderqty as POQTY'
                        , DB::raw("(CASE mrp.duedate 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(mrp.duedate, '%m/%d/%y') 
                                END) AS DUEDATE")
                        , 'mrp.reqaccum as POREQ'
                        , 'mrp.balreq as BALREQ'
                        , DB::raw(" 0 as ALLOC")
                        , 'mrp.alloccalc as ALLOCAL'
                        , 'mrp.custname as CUSTOMER'
                        )
                ->where('iso.itemname', '=', $name)
                ->get();
            }
            else
            {
                # filter data base on the Pickup date input.
                $result = DB::connection($this->mysql)->table('tempzymr0120 as iso')
                ->join('temp_sss_mrplist as mrp', 'mrp.mname', '=', 'iso.itemname')
                ->select(
                        DB::raw('SUBSTRING(mrp.po,1,10) as PO')
                        , 'iso.itemcode as CODE'
                        , 'iso.itemname as NAME'
                        , 'iso.qty as PUQTY'
                        , 'iso.vendor as SUPCODE'
                        , 'mrp.supname as SUPNAME'
                        , 'iso.specify_period as PICKUPDATE'
                        , 'iso.text as REMARKS'
                        , 'iso.po as ISO_PO'
                        , 'iso.purchasing_group as PRODNAME'
                        , 'iso.drawing_num as PR'
                        , 'mrp.orderqty as POQTY'
                        , DB::raw("(CASE mrp.duedate 
                                    WHEN '0000-00-00' THEN NULL 
                                    ELSE DATE_FORMAT(mrp.duedate, '%m/%d/%y') 
                                END) AS DUEDATE")
                        , 'mrp.reqaccum as POREQ'
                        , 'mrp.balreq as BALREQ'
                        , DB::raw(" 0 as ALLOC")
                        , 'mrp.alloccalc as ALLOCAL'
                        , 'mrp.custname as CUSTOMER'
                        )
                ->where('iso.itemname', '=', $name)
                ->whereRaw(" iso.itemname='". $name .
                     "' AND specify_period 
                     BETWEEN STR_TO_DATE('" . $from_date ."', '%m/%d/%Y') 
                     AND STR_TO_DATE('" . $to_date ."', '%m/%d/%Y')")
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
    * Get the data of Table 2.
    **/
    private function exportTable2($name)
    {
        try
        {
            # retrieve ISO GI for export.
            $result = DB::connection($this->mysql)->table('tempzymr0120 as iso')
            ->join('temp_sss_mrplist as mrp', 'mrp.mname', '=', 'iso.itemname')
            ->select(
                DB::raw('SUBSTRING(mrp.po,1,10) as PART_PO')
                , 'iso.itemcode as CODE'
                , 'iso.itemname as NAME'
                , 'iso.qty as PU_QTY'
                , 'iso.vendor as SUP_CODE'
                , 'mrp.supname as SUP_NAME'
                , 'iso.specify_period as PICKUP_DATE'
                , 'iso.text as REMARKS'
                , 'iso.po as PO'
                , 'iso.purchasing_group as PRODUCT_NAME'
                , 'iso.drawing_num as PR'
                )
            ->where('iso.itemname', '=', $name)
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
    * Export all the data to excel.
    **/
    public function postPrintIsoStatus(Request $request_data)
    {
        # get the selected supplier and db connection.
        $code = $request_data['code'];
        $data = array();

        # retrieve data
        $result_t2 = $this->exportTable2($code);
        
        # convert the object result to array readable format.
        foreach ($result_t2 as $datareport) 
        {
            $data[] = (array)$datareport;
            #or first convert it and then change its properties using 
            #an array syntax, it's up to you
        }

        # Create and export excel by feeding the array result.
        Excel::create('PO (ISO GI Input)', function($excel) use($data) 
        {

            $excel->sheet('ISO GI Input', function($sheet) use($data) 
            {
                $sheet->fromArray($data);
            });

        })->export('xls');
    }
}