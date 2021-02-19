<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: CommonController.php
     MODULE NAME:  Common
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.18
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.28     MESPINOSA       Initial Draft
     100-00-03   1     2016.06.07     MESPINOSA       Filter data by adding delete_flag = 0.
     100-00-03   2     2016.10.27     AKDELAROSA      Company wide user db connection
*******************************************************************************/
?>
<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade

/**
* PartsRejectionRate Controller
*/
class  CommonController extends Controller
{
    /**
    * Get All OrderDataReports.
    */

    protected $mysql;
    protected $mssql;
    protected $common;
    protected $wbs;

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->mysql = $this->userDBcon(Auth::user()->productline,'mysql');
            $this->wbs = $this->userDBcon(Auth::user()->productline,'wbs');
            $this->mssql = $this->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $this->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }
    
    public function getAccessRights($module_code, &$user_program_access)
    {
        $result = true;

        try
        {
            $user_program_access = DB::connection($this->common)
                                    ->table('muserprograms as U')
                                    ->join('mprograms as P', 'P.program_code', '=', 'U.program_code')
                                    ->select('P.program_name', 'U.program_code','U.user_id','U.read_write','P.program_class')
                                    ->where('U.user_id', Auth::user()->user_id)
                                    ->where('U.delete_flag', 0)
                                    ->orderBy('U.id','asc')
                                    ->get();

            $checkAccess = DB::connection($this->common)
                            ->table('muserprograms')
                            ->where('user_id',Auth::user()->user_id)
                            ->where('program_code',$module_code)
                            ->count();
            if ($checkAccess == 0) {
                $result = false;
            }

            // foreach ($user_program_access as $key => $value)
            // {
            //     $value = get_object_vars($value);
            //     if($value['program_code']==$module_code and $value['read_write']=="0")
            //     {
            //         $result = false;
            //     }
            // }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function getCompanyInfo()
    {
        $result[] = null;

        try
        {
            $result = DB::connection($this->common)->table('tbl_update_companysetting')->orderBy('updated_at', 'desc')->skip(0)->take(1)->get();
            foreach ($result as $key => $value)
            {
                $result = get_object_vars($value);
            }

            if(count($result) <= 0)
            {
                $result['name'] = '';
                $result['address'] = '';
                $result['tel1'] = '';
                $result['tel2'] = '';
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function getWbsNextCode($code)
    {
        $result = '';
        $new_code = 'ERROR';

        try
        {
            $result = DB::connection($this->wbs)->table('tbl_transaction')
                    ->select(DB::raw("CONCAT(prefix, LPAD(IFNULL(nextno, 0), nextnolength, '0')) AS new_code"),
                                'nextno',
                                'month')
                    ->where('code', '=', $code)
                    ->get();

            foreach ($result as $key => $value)
            {
                $result = get_object_vars($value);
                break;
            }

            if(count($result) <= 0)
            {
                $result['new_code'] = 'ERROR';
                $result['nextno'] = 0;
            }

            if ($result['month'] == date('m')) {
                DB::connection($this->wbs)->table('tbl_transaction')
                    ->where('code', '=', $code)
                    ->update(['nextno' => $result['nextno'] + 1]);
            } else {
                DB::connection($this->wbs)->table('tbl_transaction')
                    ->where('code', '=', $code)
                    ->update(['nextno' => 1, 'month' => date('m')]);

                $result = DB::connection($this->wbs)->table('tbl_transaction')
                            ->select(DB::raw("CONCAT(prefix, LPAD(IFNULL(nextno, 0), nextnolength, '0')) AS new_code"),
                                        'nextno',
                                        'month')
                            ->where('code', '=', $code)
                            ->get();

                foreach ($result as $key => $value)
                {
                    $result = get_object_vars($value);
                    break;
                }

                if(count($result) <= 0)
                {
                    $result['new_code'] = 'ERROR';
                    $result['nextno'] = 0;
                }
                DB::connection($this->wbs)->table('tbl_transaction')
                    ->where('code', '=', $code)
                    ->update(['nextno' => $result['nextno'] + 1]);
            }

            
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function getTransCode($transcode)
    {
        $nextReceivingNo = $this->getWbsNextCode($transcode);
        $issuance_no = str_replace('YYMM',date("ym"),$nextReceivingNo['new_code']);

        return $issuance_no;
    }

    public function getWbsPrevCode($code)
    {
        $result = '';
        $new_code = 'ERROR';

        try
        {
            $result = DB::connection($this->wbs)->table('tbl_transaction')
                    ->select(DB::raw("CONCAT(prefix, LPAD(IFNULL(nextno, 0), nextnolength, '0')) AS new_code"), 'nextno')
                    ->where('code', '=', $code)
                    ->get();

            foreach ($result as $key => $value)
            {
                $result = get_object_vars($value);
                break;
            }

            if(count($result) <= 0)
            {
                $result['new_code'] = 'ERROR';
                $result['nextno'] = 0;
            }

            DB::connection($this->wbs)->table('tbl_transaction')
                ->where('code', '=', $code)
                ->update(['nextno' => $result['nextno'] - 1]);
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function getDropdownById($cathegory)
    {
        try
        {
            $result = DB::connection($this->mysql)->table('tbl_mdropdowns')->where('category', '=', $cathegory)->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function getDropdownByIdSelect2($cathegory)
    {
        try
        {
            $result = DB::connection($this->mysql)->table('tbl_mdropdowns')
                        ->where('category', '=', $cathegory)
                        ->select('description as id','description as text')
                        ->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function getDropdownByNameSelect2($name)
    {
        $name = str_replace(" ","",strtolower($name));

        try
        {
            $result = DB::connection($this->mysql)->table('tbl_mdropdowns')->where('category', '='
                , DB::raw("(SELECT id FROM tbl_mdropdown_category WHERE LOWER(REPLACE(category, ' ', ''))='". $name ."')"))
                ->select('description as id', 'description as text')
                ->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function getDropdownByName($name)
    {
        $name = str_replace(" ","",strtolower($name));

        try
        {
            $result = DB::connection($this->mysql)->table('tbl_mdropdowns')->where('category', '='
                , DB::raw("(SELECT id FROM tbl_mdropdown_category WHERE LOWER(REPLACE(category, ' ', ''))='". $name ."')"))->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function getPackingListSettingsByName($name)
    {
        $name = str_replace(" ","",strtolower($name));

        try
        {
            $result = DB::table('tbl_packinglist_setting')->select('value')->where('name', '=', $name)->get();

            if(count($result) > 0)
            {
                $name = $result[0]->value;
            }
            else
            {
                $name = '';
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $name;
    }

    public function toArray($array,$merge = false)
    {
        if (is_array($array)) {
            $arr = array_unique($array);
            if ($merge == True || $merge == true) {
                $res = json_decode(json_encode($arr), True);
                return call_user_func_array("array_merge",$res);
            } else {
                return json_decode(json_encode($arr), True);
            }
        }
    }

    public function merge($array)
    {
        if (is_array($array)) {
            $res = json_decode(json_encode($array), True);
            return call_user_func_array("array_merge",$res);
        }
    }

    public function userDBcon($db,$dbms)
    {
        switch ($dbms) {
            case 'mysql':
                switch ($db) {
                    case 'TS':
                        return "mysqlts";
                        break;

                    case 'CN':
                        return "mysqlcn";
                        break;

                    case 'YF':
                        return "mysqlyf";
                        break;

                    case 'MOLDING':
                        return "mysqlmold";
                        break;

                    case 'PPS':
                        return "mysqlpps";
                        break;

                    case 'PROBE':
                        return "mysqlprobe";
                        break;

                    default:
                        return "mysqlts";
                        break;
                }
            break;

            case 'stocksquery':
                switch ($db) {
                    case 'TS':
                        return "mysqlstockqueryts";
                        break;

                    case 'CN':
                        return "mysqlstockquerycn";
                        break;

                    case 'YF':
                        return "mysqlstockqueryyf";
                        break;

                    default:
                        return "mysqlstockqueryts";
                        break;
                }
            break;

            case 'wbs':
                switch ($db) {
                    case 'TS':
                        return "mysqlwbsts";
                        break;

                    case 'CN':
                        return "mysqlwbscn";
                        break;

                    case 'YF':
                        return "mysqlwbsyf";
                        break;

                    default:
                        return "mysqlwbsts";
                        break;
                }
            break;

            case 'traffic':
                switch ($db) {
                    case 'TS':
                        return "mysqltrafficts";
                        break;

                    case 'CN':
                        return "mysqltrafficcn";
                        break;

                    case 'YF':
                        return "mysqltrafficyf";
                        break;

                    default:
                        return "mysqltrafficts";
                        break;
                }
            break;

            case 'mrp':
                switch ($db) {
                    case 'TS':
                        return "mysqlmrpts";
                        break;

                    case 'CN':
                        return "mysqlmrpcn";
                        break;

                    case 'YF':
                        return "mysqlmrpyf";
                        break;

                    default:
                        return "mysqlmrpts";
                        break;
                }
            break;

            case 'sss':
                switch ($db) {
                    case 'TS':
                        return "mysqlsssts";
                        break;

                    case 'CN':
                        return "mysqlssscn";
                        break;

                    case 'YF':
                        return "mysqlsssyf";
                        break;

                    default:
                        return "mysqlsssts";
                        break;
                }
            break;

            case 'mssql':
                switch ($db) {
                    case 'TS':
                        return "sqlsrvbu";
                        break;

                    case 'CN':
                        return "sqlsrvcn";
                        break;

                    case 'YF':
                        return "sqlsrvyf";
                        break;

                    case 'MOLDING':
                        return "sqlsrvmold";
                        break;

                    case 'PPS':
                        return "sqlsrvpps";
                        break;

                    case 'PROBE':
                        return "sqlsrvprobe";
                        break;

                    default:
                        return "sqlsrvbu";
                        break;
                }
            break;

            case 'common':
                return "common";
                break;
            break;

            case 'barcode':
                switch ($db) {
                    case 'TS':
                        return "mysql_barcode";
                        break;

                    case 'CN':
                        return "mysql_barcode_cn";
                        break;

                    default:
                        return "mysql_barcode";
                        break;
                }
            break;

            case 'pps_invoice':
                switch ($db) {
                    case 'TS':
                        return "pps_invoice";
                        break;

                    case 'CN':
                        return "pps_invoice";
                        break;

                    default:
                        return "pps_invoice";
                        break;
                }
            break;

            default:
                return "common";
                break;
            break;
        }

    }

    public function userDBconFromStr($str)
    {

        if(strpos($str, 'TS'))
        {
            return "sqlsrvbu";
        }
        else if (strpos($str, 'CN'))
        {
            return "sqlsrvcn";
        }
        else if (strpos($str, 'YF'))
        {
            return "sqlsrvyf";
        }
        else if (strpos($str, 'MOLDING'))
        {
            return "sqlsrvmold";
        }
        else if (strpos($str, 'PPS'))
        {
            return "sqlsrvpps";
        }
        else if (strpos($str, 'BU'))
        {
            return "sqlsrvbu";
        }
        else
        {
            return "sqlsrvbu";
        }
    }

    public function getDatabaseNameByDbCon($con)
    {
        switch ($con) {
            case 'mysqlts':
                return 'pmi_ts';
                break;
            case 'mysqlcn':
                return 'pmi_cn';
                break;
            case 'mysqlyf':
                return 'pmi_yf';
                break;
            case 'mysqlmold':
                return 'pmi_ts';
                break;
            case 'sqlsrvpps':
                return 'pmi_ts';
                break;
            case 'sqlsrvprobe':
                return 'pmi_ts';
                break;
            case 'sqlsrvbu':
                return 'pmi_ts';
                break;
            case 'sqlsrvcn':
                return 'pmi_ts';
                break;
            case 'sqlsrvyf':
                return 'pmi_ts';
                break;
            case 'sqlsrvmold':
                return 'pmi_ts';
                break;
            case 'sqlsrvpps':
                return 'pmi_ts';
                break;
            case 'sqlsrvprobe':
                return 'pmi_ts';
                break;
            case 'sqlsrvbu':
                return 'pmi_ts';
                break;

            case 'mysqlsssts':
                return 'pmi_sss_ts';
                break;

            case 'mysqlmrpts':
                return 'pmi_mrp_ts';
                break;

            default:
                return 'pmi_common';
                break;
        }
    }

    public function getr3Connection($con)
    {
        if (strpos($con, 'TS') !== false) {
            return 'TS';
        }
        if (strpos($con, 'CN') !== false) {
            return 'CN';
        }
        if (strpos($con, 'YF') !== false) {
            return 'YF';
        }
    }

    // public function truncateTable($table)
    // {
    //     return DB::connection($this->mysql)->table($table)->truncate();
    // }

    public function truncateTable($con,$table)
    {
        if ($con == '') {
            $con = $this->mysql;
        }
        return DB::connection($con)->table($table)->truncate();
    }

    public function checkIfExistObject($object)
    {
       return count( (array)$object);
    }

    public function convertDate($date,$format)
    {
        $time = strtotime($date);
        $newdate = date($format,$time);
        return $newdate;
    }

    public function convert_unicode($str)
    {
        if (mb_detect_encoding($str, 'UTF-8', true) === false) { 
            $str = utf8_encode($str);
        }

        return $str;
    }

    public function getPgAccess($pgcode)
    {
        $pg = DB::connection($this->common)->table('muserprograms')
                ->where('user_id', Auth::user()->user_id)
                ->where('program_code',$pgcode)
                ->first();

        if ($this->checkIfExistObject($pg) > 0) {
            return $pg->read_write;
        }

        return 0;
    }
}
