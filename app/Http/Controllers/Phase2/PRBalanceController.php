<?php
namespace App\Http\Controllers\Phase2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use Config;
use DB;
use PDO;
use PDOVertica; //Add this line
use Excel;
use Carbon\Carbon;

class PRBalanceController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;

    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'mysql');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getPRBalance()
    {
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PRBALANCE'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('phase2.PRBalance',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function postFiles(Request $req)
    {
        if (!empty($req->file('inputdata'))) 
        {
            return $this->getData($req->file('inputdata'),$req->file('invoice'));
        }
    }

    private function getData($inputfile,$invoice)
    {
        try {
            ini_set('max_execution_time', 0);

            /*$this->truncateTable('prb_outputs');
            $this->truncateTable('prb_inputs');*/

            $this->getDbConnection($inputfile->getClientOriginalName(), $db, $schema);

            $ypics = $this->getYPICS();

            /*foreach ($ypics as $key => $ypic) {
                $this->insertDataToOutput($ypic->pr,
                                        $ypic->code,
                                        $ypic->name,
                                        $ypic->supplier,
                                        $ypic->WHS100,
                                        $ypic->WHS102,
                                        $ypic->ASSY100,
                                        $ypic->ASSY102,
                                        $ypic->WHSNON,
                                        $ypic->WHSSM,
                                        $ypic->orderissuedate,
                                        $ypic->podqty,
                                        $ypic->pprbal,
                                        $ypic->req,
                                        $ypic->CurrentInv);
            }*/

            $this->extractInputfile($inputfile,$invoice);

            /*$yecs = $this->retrieveData();
            foreach ($yecs as $key => $yec) {
                $update = DB::connection($this->mysql)->table('prb_outputs')
                            ->where('pr',$yec->pr)
                            ->update([
                                'yecqty' => $yec->yec_qty,
                            ]);
            }*/
           $this->OutputFile();

        } catch (Exception $e) {
            return redirect(url('/prbalance'))->with(['prb_err' => $e]);
        }
    }

    private function getDbConnection($filename, &$db ='', &$schema = '')
    {

        # check if the file is from TS database.
        if(is_numeric(strpos($filename,"TS")))
        {
            # connects to Probe DB -> DB_SQLSRV_TS.
            $db = Config::get('constants.DB_SQLSRV_BU');
            $schema = Config::get('constants.DB_SHCEMA_BU');
        }
        # check if the file is from BU database.
        elseif(is_numeric(strpos($filename,"BU")))
        {
            # connects to BU DB -> DB_SQLSRV_BU.
            $db = Config::get('constants.DB_SQLSRV_BU');
            $schema = Config::get('constants.DB_SHCEMA_BU');
        }
        # check if the file is from CN database.
        elseif(is_numeric(strpos($filename,"CN")))
        {
            # connects to CN DB -> DB_SQLSRV_CN.
            $db = Config::get('constants.DB_SQLSRV_CN');
            $schema = Config::get('constants.DB_SHCEMA_CN');
        }
        # check if the file is from YF database.
        elseif(is_numeric(strpos($filename,"YF")))
        {
            # connects to YF DB -> DB_SQLSRV_YF.
            $db = Config::get('constants.DB_SQLSRV_YF');
            $schema = Config::get('constants.DB_SHCEMA_YF');
        }
        else
        {
            # connects to Probe DB -> DB_SQLSRV_TS.
            $db = Config::get('constants.DB_SQLSRV_BU');
            $schema = Config::get('constants.DB_SHCEMA_BU');
        }
    }

    private function getYPICS()
    {

        $xlsip = DB::connection($this->mssql)
                ->select("
                    SELECT s.PORDER AS PR, 
                        s.CODE AS MCode, 
                        hts.NAME AS MName, 
                        s.KVOL AS PODQty, 
                        CONCAT(SUBSTRING([DDATE],0,5), '/' , 
                            SUBSTRING([DDATE],5,2) , '/' , 
                            SUBSTRING([DDATE],7,2)) AS OrderIssueDate, 
                        [KVOL]-[TJITU] AS PPRBal, 
                        s.NOTE AS Remarks, 
                        s.PDATE, 
                        s.NDATE, 
                        s.IDATE
                    FROM XSLIP s 
                    INNER JOIN XHEAD hts ON s.CODE = hts.CODE
                    WHERE (((s.PORDER) Not Like 'PR10%') 
                        AND (([KVOL]-[TJITU])<>0) 
                        AND ((s.VENDOR)='YEC'))
                    ");

        $this->truncateTable('temp_prb_xslip');
        foreach ($xlsip as $key => $data) 
        {
            #insert xslip data to temp_prb_xslip.
            DB::connection($this->mysql)->table('temp_prb_xslip')
            ->insert([
                'pr'             => $data->PR,
                'mcode'          => $data->MCode,
                'mname'          => $data->MName,
                'podqty'         => $data->PODQty,
                'orderissuedate' => $data->OrderIssueDate,
                'pprbal'         => $data->PPRBal,
                'remarks'        => $data->Remarks,
                'pdate'          => $data->PDATE,
                'ndate'          => $data->NDATE,
                'idate'          => $data->IDATE                
                ]);
        }

        $uniqueCode = DB::connection($this->mysql)->select("
                SELECT DISTINCT mcode FROM temp_prb_xslip");

        $codes = '';
        foreach ($uniqueCode as $key => $data) 
        {
            if($codes == '')
            {
                $codes = "'" . $data->mcode . "'";
            }
            else
            {
                $codes = $codes . ", '". $data->mcode . "'";
            }
        }


        $uniquePR = DB::connection($this->mysql)->select("
                SELECT DISTINCT pr FROM temp_prb_xslip");

        $prs = '';
        foreach ($uniquePR as $key => $data) 
        {
            if($prs == '')
            {
                $prs = "'" . $data->pr . "'";
            }
            else
            {
                $prs = $prs . ", '". $data->pr . "'";
            }
        }

        $origprorder = DB::connection($this->mssql)->select("
            SELECT s.PORDER, 
                s.IDATE, 
                s.CODE, 
                hts.NAME, 
                s.KVOL
            FROM XSLIP s 
            INNER JOIN XHEAD hts ON s.CODE = hts.CODE
            WHERE s.PORDER Not Like 'WK%' AND s.PORDER in (".$prs.")
            ");

        $this->truncateTable('temp_prb_origprorder');
        foreach ($origprorder as $key => $data) 
        {
            #insert xslip data to temp_prb_origprorder.
            DB::connection($this->mysql)->table('temp_prb_origprorder')
            ->insert([
                'porder'=> $data->PORDER,
                'idate' => $data->IDATE,
                'code'  => $data->CODE,
                'name'  => $data->NAME,
                'kvol'  => $data->KVOL
                ]);
        }

        $xzaik = DB::connection($this->mssql)
                ->select("
                    SELECT xz.*, 
                        isnull(xh.TtlBalReq,0) as TtlBalReq, 
                        Supplier,
                        isnull(ASSY100,0) as ASSY100, 
                        isnull(ASSY102,0) as ASSY102, 
                        isnull(WHS100,0) as WHS100, 
                        isnull(WHS102,0) as WHS102, 
                        isnull(WHSNON,0) as WHSNON, 
                        isnull(WHSSM,0) as WHSSM, 
                        isnull(TtlCrrInv,0) as TtlCrrInv
                    FROM 
                    (
                        SELECT zts.CODE, hts.NAME, Sum(zts.ZAIK) AS TotalInventory
                        FROM XZAIK zts 
                        INNER JOIN XHEAD hts ON zts.CODE = hts.CODE
                        WHERE (((zts.HOKAN) Not Like 'WHS101'))
                        GROUP BY zts.CODE, hts.NAME
                    ) xz
                    LEFT JOIN (
                        SELECT ISNULL(h.Code,0) as Code, ISNULL(Sum(h.KVOL-TJITU),0) AS TtlBalReq
                        FROM XHIKI h
                        GROUP BY h.Code
                        HAVING Sum(h.KVOL-TJITU) > 0
                    ) xh ON xh.CODE = xz.CODE
                    LEFT JOIN (
                        SELECT * FROM (
                        select CODE, SUPPLIER, 
                            isnull(ASSY100,0) AS ASSY100 , 
                            isnull(ASSY102,0) AS ASSY102 ,
                            isnull(WHS100,0) AS WHS100, 
                            isnull(WHS102,0) AS WHS102, 
                            isnull([WHS-NON],0) AS WHSNON, 
                            isnull([WHS-SM],0) AS WHSSM, 
                            (isnull(ASSY100,0) 
                                + isnull(ASSY102,0)
                                + isnull(WHS100,0)
                                + isnull(WHS102,0)
                                + isnull([WHS-NON],0)
                                + isnull([WHS-SM],0)) AS TtlCrrInv
                        from (
                            SELECT zts.CODE, i.VENDOR as SUPPLIER, Sum(zts.ZAIK) AS Total, zts.HOKAN
                            FROM XZAIK zts
                            LEFT JOIN XITEM i ON zts.CODE = i.CODE
                            WHERE (((zts.HOKAN) Like 'ASSY100' Or (zts.HOKAN) Like 'WHS100'))
                            GROUP BY zts.CODE, i.VENDOR, zts.HOKAN
                            ) d
                        pivot(
                            sum(Total)
                            for HOKAN in (
                                ASSY100,
                                ASSY102,
                                WHS100,
                                WHS102,
                                [WHS-NON],
                                [WHS-SM])
                            ) piv
                        ) a
                        WHERE A.TtlCrrInv > 0                   
                    ) xzhokan ON xzhokan.CODE = xz.CODE
                    WHERE (TotalInventory > 0 or xh.TtlBalReq > 0)
                        AND xzhokan.CODE in (".$codes.")
                    ");

        $this->truncateTable('temp_prb_xzaik_xhiki');
        foreach ($xzaik as $key => $data) 
        {
            #insert xslip data to temp_prb_xzaik_xhiki.
            DB::connection($this->mysql)->table('temp_prb_xzaik_xhiki')
            ->insert([
                'code'           => $data->CODE,
                'name'           => $data->NAME,
                'totalinventory' => $data->TotalInventory,
                'totalbalreq'    => $data->TtlBalReq,
                'supplier'       => $data->Supplier,
                'assy100'        => $data->ASSY100,
                'assy102'        => $data->ASSY102,
                'whs100'         => $data->WHS100,
                'whs102'         => $data->WHS102,
                'whsnon'         => $data->WHSNON,
                'whssm'         => $data->WHSSM,
                'total'          => $data->TtlCrrInv  
                ]);
        }

/*        return DB::connection($this->mssql)
                ->table('XHIKI as d')
                ->join('XHEAD as h', 'd.CODE', '=', 'h.CODE')
                ->join('XITEM as i', 'd.CODE', '=', 'i.CODE')
                ->join('XSLIP as s', 'd.CODE', '=', 's.CODE')
                ->join(DB::raw("( SELECT z.CODE
                                , SUM(z.ZAIK) as CurrentInv
                                , ISNULL(z1.ZAIK,0) as WHS100
                                , ISNULL(z2.ZAIK,0) as WHS102
                                , ISNULL(z3.ZAIK,0) as ASSY100
                                , ISNULL(z4.ZAIK,0) as ASSY102
                                , ISNULL(z5.ZAIK,0) as WHSNON
                                , ISNULL(z6.ZAIK,0) as WHSSM
                            FROM XZAIK z
                            LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
                            LEFT JOIN XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS102'
                            LEFT JOIN XZAIK z3 ON z3.CODE = z.CODE AND z3.HOKAN = 'ASSY100'
                            LEFT JOIN XZAIK z4 ON z4.CODE = z.CODE AND z4.HOKAN = 'ASSY102'
                            LEFT JOIN XZAIK z5 ON z5.CODE = z.CODE AND z5.HOKAN = 'WHS-NON'
                            LEFT JOIN XZAIK z6 ON z6.CODE = z.CODE AND z6.HOKAN = 'WHS-SM'
                            GROUP BY z.CODE
                                , z1.ZAIK
                                , z2.ZAIK
                                , z3.ZAIK
                                , z4.ZAIK
                                , z5.ZAIK
                                , z6.ZAIK) as x"),'x.CODE', '=', 'd.CODE')
                ->select(DB::raw('s.PORDER as pr'),
                    DB::raw('s.CODE as code'),
                    DB::raw('h.NAME as name'),
                    DB::raw('x.WHS100 as WHS100'),
                    DB::raw('x.WHS102 as WHS102'),
                    DB::raw('x.ASSY100 as ASSY100'),
                    DB::raw('x.ASSY102 as ASSY102'),
                    DB::raw('x.WHSNON as WHSNON'),
                    DB::raw('x.WHSSM as WHSSM'),
                    DB::raw('s.KVOL as podqty'),
                    DB::raw('s.KVOL - s.TJITU as pprbal'),
                    DB::raw('SUM(d.KVOL) - SUM(d.TJITU) AS req'),
                    'x.CurrentInv',
                    DB::raw('s.VENDOR as supplier'),
                    DB::raw('s.DDATE as orderissuedate'))
                ->where(DB::raw('s.KVOL- s.TJITU'),'<>','0.0000')
                ->where('s.CODE','<>','')
                ->groupBy('s.CODE', 'h.NAME', 's.KVOL', 'x.WHS100', 'x.WHS102', 'x.ASSY100', 'x.ASSY102', 'x.WHSNON', 'x.WHSSM', 's.TJITU','s.PORDER','x.CurrentInv','s.VENDOR','s.DDATE')
                ->get();*/
                // ->where('s.PORDER','like', 'PR%')
                // ->orWhere('s.PORDER','like', 'GR%')
                // ->orWhere('s.PORDER','like', 'AD%')
                 //->skip(0)->take(100)
    }

    private function merge($arr)
    {
        return call_user_func_array('array_merge', $arr);
    }

    private function retrieveData()
    {
        $data = DB::connection($this->mysql)->table('prb_inputs')
                    ->select(
                        'pr',
                        'code',
                        'name',
                        DB::raw("SUM(payment) as yec_qty"))
                    ->groupBy('pr')
                    ->get();
        return $data;
    }

    private function checkinsxslip($pr)
    {
        return DB::connection($this->mssql)
                ->table('XSLIP')
                ->where('PORDER',$pr)
                ->count();
    }

    private function checkYEC($pr)
    {
        return DB::connection($this->mysql)->table('prb_outputs')
                ->where('pr',$pr)
                ->count();
    }

    private function checkifExist($code)
    {
        return DB::connection($this->mssql)
                ->table('XSLIP as S')
                ->join('XZAIK as Z','S.CODE','=','Z.CODE')
                ->select('S.CODE')
                ->where('S.CODE',$code)
                ->count();
    }

    private function extractInputfile($txt,$invoice)
    {
        $file = explode(PHP_EOL, file_get_contents($txt));
        $keys = array_keys($file);

        $this->truncateTable('temp_prb_noukikaito');
        for ($i=1; $i < count($keys); $i++)
        {
            $key = $keys[$i];
            $content = $file[$key];
            $data = array_filter(array_map("trim", explode("\t", $content)));
            //call_user_func_array('array_merge', $data);
            if (isset($data[0])) {
                $name = mb_convert_encoding($data[4],"UTF-8","SJIS");
                if (isset($data[23])) {
                    $compname = mb_convert_encoding($data[23],'UTF-8','SJIS');
                } else {
                    $compname ='';
                }

                $pr = substr($data[0], 0, strpos($data[0], "-"));
                if (strpos($pr, 'AD') !== false || strpos($pr, 'PR') !== false || strpos($pr, 'GR') !== false || strpos($pr, 'SH') !== false || strpos($pr, 'SM') !== false) {
                    $prno = $pr;
                } else {
                    $prno = "AD".$pr;
                }

                if ($this->checkinsxslip($prno) > 0) {
                    $db = DB::connection($this->mysql)->table('prb_inputs')
                            ->insertGetID([
                                'pr' => (isset($prno) === TRUE ? $prno : ""),
                                'payment' => (isset($data[2]) === TRUE ? $data[2] : ""),
                                'code' => (isset($data[3]) === TRUE ? $data[3] : "0.00"),
                                'name' => (isset($name) === TRUE ? $name : ""),
                            ]);
                }

                DB::connection($this->mysql)->table('temp_prb_noukikaito')
                    ->insert([
                        'pr'           => $data[0],
                        'arrivedate'   => $data[1],
                        'payment'      => $data[2],
                        'code'         => $data[3],
                        'name'         => $data[4],
                        'pmifltneeded' => $data[7],
                        'bal'          => $data[10]
                        ]);
            }
        }


        $this->truncateTable('temp_prb_invoice');
        if(isset($invoice))
        {
            $file = explode(PHP_EOL, file_get_contents($invoice));
            $keys = array_keys($file);
            for ($i=1; $i < count($keys); $i++)
            {
                $key = $keys[$i];
                $content = $file[$key];
                $data = array_filter(array_map("trim", explode("\t", $content)));

                    if(isset($data[0])) 
                    {
                        DB::connection($this->mysql)->table('temp_prb_invoice')
                            ->insert([
                                'no'         => str_replace('"','',$data[0]),
                                'flightdate' => $data[1],
                                'code'       => str_replace('"','',$data[2]),
                                'name'       => mb_convert_encoding(str_replace('"','',$data[3]),'UTF-8','SJIS'),
                                'invqty'     => str_replace('"','',$data[4]),
                                'podata'     => str_replace('"','',$data[5]),
                                'sunitprice' => str_replace('"','',$data[6])
                                ]);
                    }
            }
        }

    }

    private function truncateTable($table)
    {
        return DB::connection($this->mysql)->table($table)->truncate();
    }

    private function insertDataToOutput($pr,$code,$name,$supplier,$WHS100,$WHS102,$ASSY100,$ASSY102,$WHSNON,$WHSSM,$orderissuedate,$podqty,$pprbal,$req,$CurrentInv)
    {
        try {
            $Total = $WHS100+$WHS102+$ASSY100+$ASSY102+$WHSNON+$WHSSM;
            $od = substr($orderissuedate,0,-1);
            $y = substr($od,0,4);
            $m = substr($od,4,-2);
            $d = substr($od,-2);

            $date = $y."/".$d."/".$m;
            $id = DB::connection($this->mysql)->table('prb_outputs')
                    ->insertGetId([
                         'pr' => $pr,
                         'mcode' => $code,
                         'mname' => $name,
                         'supplier' => $supplier,
                         'whs100' => $WHS100,
                         'whs102' => $WHS102,
                         'assy100' => $ASSY100,
                         'assy102' => $ASSY102,
                         'whsnon' => $WHSNON,
                         'whssm' => $WHSSM,
                         'total' => $Total,
                         'orderissuedate' => $date,
                         'podqty' => $podqty,
                         'pprbal' => $pprbal,
                         'yecqty' => "0.00",
                         'invoiceqty' => "0.00",
                         'difference' => "0.00",
                         'requirement' => $req,
                         'currentinvntry' => $CurrentInv
                     ]);

        } catch (Exception $e) {

        }
    }

    public function OutputFile()
    {
        $dt = Carbon::now();
        $date = substr($dt->format('Ymd'), 2);
        $path = public_path().'/PRBalance/'.Auth::user()->user_id.'/';

        Excel::create('PRBalance_Output_'.$date, function($excel)
        {
            $excel->sheet('Sheet1', function($sheet)
            {
                $sheet->cell('A1', "PR");
                $sheet->cell('B1', "MCode");
                $sheet->cell('C1', "Mname");
                $sheet->cell('D1', "Supplier"); //
                $sheet->cell('E1', "WHS100");
                $sheet->cell('F1', "WHS102"); //
                $sheet->cell('G1', "ASSY100");
                $sheet->cell('H1', "ASSY102"); //
                $sheet->cell('I1', "WHSNON"); //
                $sheet->cell('J1', "WHSSM"); //
                $sheet->cell('K1', "Total");
                $sheet->cell('L1', "OrderIssueDate");
                $sheet->cell('M1', "PODQty");
                $sheet->cell('N1', "PPRBal");
                $sheet->cell('O1', "YEC_Qty");
                $sheet->cell('P1', "InvoiceQty");
                $sheet->cell('Q1', "Difference");
                $sheet->cell('R1', "CurrentInvntry");
                $sheet->cell('S1', "Requirement");
                $sheet->cell('T1', "Chk");
                $sheet->cell('U1', "Remarks");


                $row = 2;
                // $prb = DB::connection($this->mysql)->table('prb_outputs')->get();
                $prb = $this->getPrbOutput();
                $chk = '*';
                foreach ($prb as $key => $pr) {
                    // $diff = ($pr->pprbal - $pr->yecqty) - $pr->invoiceqty;
                    if(isset($pr->OrderIssueDate))
                    {
                        $orddate = date_format(date_create($pr->OrderIssueDate), 'Y-m-d');   
                    }
                    else
                    {
                        $orddate = '0000-00-00';   
                    }

                    $sheet->cell('A'.$row, $pr->PR1);
                    $sheet->cell('B'.$row, $pr->MCode);
                    $sheet->cell('C'.$row, mb_convert_encoding($pr->MName,"UTF-8","SJIS"));
                    $sheet->cell('D'.$row, $pr->Supplier);
                    $sheet->cell('E'.$row, ($pr->WHS100 == 0) ? "0.00" : $pr->WHS100);
                    $sheet->cell('F'.$row, ($pr->WHS102 == 0) ? "0.00" : $pr->WHS102);
                    $sheet->cell('G'.$row, ($pr->ASSY100 == 0) ? "0.00" : $pr->ASSY100);
                    $sheet->cell('H'.$row, ($pr->ASSY102 == 0) ? "0.00" : $pr->ASSY102);
                    $sheet->cell('I'.$row, ($pr->WHSNON == 0) ? "0.00" : $pr->WHSNON);
                    $sheet->cell('J'.$row, ($pr->WHSSM == 0) ? "0.00" : $pr->WHSSM);
                    $sheet->cell('K'.$row, ($pr->Total == 0) ? "0.00" : $pr->Total);
                    $sheet->cell('L'.$row, $orddate);
                    $sheet->cell('M'.$row, ($pr->PODQty == 0) ? "0.00" : $pr->PODQty);
                    $sheet->cell('N'.$row, ($pr->PPRBal == 0) ? "0.00" : $pr->PPRBal);
                    $sheet->cell('O'.$row, ($pr->YEC_Qty == 0) ? "0.00" : $pr->YEC_Qty);
                    $sheet->cell('P'.$row, ($pr->InvoiceQty == 0) ? "0.00" : $pr->InvoiceQty);
                    $sheet->cell('Q'.$row, ($pr->Difference == 0) ? "0.00" : $pr->Difference);
                    $sheet->cell('R'.$row, ($pr->CurrentInvtry == 0) ? "0.00" : $pr->CurrentInvtry);
                    $sheet->cell('S'.$row, ($pr->Requirement == 0) ? "0.00" : $pr->Requirement);
                    /*if ($pr->PPRBal == $pr->YEC_Qty) {
                        $chk = '';
                    } else {
                        $chk = '*';
                    }*/
                    $sheet->cell('T'.$row, $pr->Chk);
                    $sheet->cell('U'.$row, $pr->Remarks);
                    $row++;
                }

            });

        })->download('xls');
    }

    private function getPrbOutput()
    {
        try
        {
            $prb = DB::connection($this->mysql)->select("
                SELECT LEFT(PR,12) as PR1, MCode, MName, Supplier, SUM(WHS100) as WHS100, SUM(WHS102) as WHS102, SUM(ASSY100) as ASSY100,
                       SUM(ASSY102) as ASSY102, SUM(WHSNON) as WHSNON, SUM(WHSSM)as WHSSM, SUM(Total) as Total,
                       OrderIssueDate as OrderIssueDate, SUM(PODQty) as PODQty, SUM(PPRBal) as PPRBal, SUM(YEC_Qty) as YEC_Qty ,
                       SUM(InvoiceQty) as InvoiceQty, SUM(PPRBal-YEC_Qty-InvoiceQty) as Difference, SUM(CurrentInvtry) as CurrentInvtry,
                       SUM(Requirement) as Requirement, IF(SUM(PPRBal-YEC_Qty-InvoiceQty)=0,'',Chk) as Chk, Remarks
                FROM (SELECT PR_DifferenceCheck2.PR, 
                                    PR_DifferenceCheck2.MCode, 
                                    PR_DifferenceCheck2.MName, 
                                    zh.Supplier,
                                    IF(zh.WHS100 IS NULL,0,zh.WHS100) AS WHS100, 
                                    IF(zh.WHS102 IS NULL,0,zh.WHS102) AS WHS102, 
                                    IF(zh.ASSY100 IS NULL, 0,zh.ASSY100) AS ASSY100, 
                                    IF(zh.ASSY102 IS NULL, 0,zh.ASSY102) AS ASSY102, 
                                    IF(zh.WHSNON IS NULL,0,zh.WHSNON) AS WHSNON, 
                                    IF(zh.WHSSM IS NULL,0,zh.WHSSM) AS WHSSM, 
                                    IF(zh.Total IS NULL,0,zh.Total) AS Total, 
                                    PR_DifferenceCheck2.OrderIssueDate, 
                                    PR_DifferenceCheck2.PODQty, 
                                    PR_DifferenceCheck2.PPRBal, 
                                    IF(YQty IS NULL,0,YQty) AS YEC_Qty, 
                                    IF(InvQty IS NULL,0,InvQty) AS InvoiceQty, 
                                    (PPRBal-(IF(YQty IS NULL,0,YQty))-(IF(InvQty IS NULL,0,InvQty))) AS Difference, 
                                    zh.TotalInventory AS CurrentInvtry, 
                                    zh.TotalBalReq AS Requirement, 
                                    IF(+(PPRBal-(IF(YQty IS NULL,0,YQty)+IF(InvQty IS NULL,0,InvQty)))=0,'','*') AS Chk, 
                                    PR_DifferenceCheck2.Remarks 
                                FROM temp_prb_xslip AS PR_DifferenceCheck2 
                                LEFT JOIN (
                                    SELECT PR,IF(PR IS NULL,'',SUBSTR(PR,1,IF(INSTR(PR,'-')>0,INSTR(PR,'-'),LENGTH (PR))-1)) AS YPR, 
                                        Noukikaito.Code, 
                                        Noukikaito.Name, 
                                        SUM(Noukikaito.payment) AS YQty
                                    FROM temp_prb_noukikaito Noukikaito
                                    GROUP BY IF(PR IS NULL,'',SUBSTR(PR,1,INSTR(PR,'-')-1)), Noukikaito.Code, Noukikaito.Name
                                    HAVING (((IF(PR IS NULL,'',SUBSTR(PR,1,INSTR(PR,'-')-1))) NOT LIKE 'PR10%'))
                                ) PR_DifferenceCheck1 ON PR_DifferenceCheck2.PR=PR_DifferenceCheck1.YPR
                                LEFT JOIN temp_prb_xzaik_xhiki zh ON PR_DifferenceCheck2.MCode=zh.CODE
                                LEFT JOIN temp_prb_invoice PR_DifferenceCheck1_1 ON PR_DifferenceCheck1_1.PODATA = PR_DifferenceCheck1.YPR
                                LEFT JOIN temp_prb_origprorder opro ON opro.CODE = PR_DifferenceCheck2.MCode
                                UNION
                                SELECT PR_DifferenceCheck1.PR AS PR, 
                                    PR_DifferenceCheck1.MCode, 
                                    PR_DifferenceCheck1.MName, 
                                    IF(zh.Supplier IS NULL,'',zh.Supplier) AS Supplier,
                                    0 AS WHS100,
                                    0 AS WHS102,
                                    0 AS ASSY100,
                                    0 AS ASSY102,
                                    0 AS WHSNON,
                                    0 AS WHSSM,
                                    0 AS Total,
                                    PR_DifferenceCheck2.OrderIssueDate, 
                                    IF(PODQty IS NULL, 0, PODQty) AS PODQty, 
                                    IF(PPRBal IS NULL,0,PPRBal) AS PPRBal, 
                                    PR_DifferenceCheck1.YQty AS YEC_QTY,
                                    IF(InvQty IS NULL,0,InvQty) AS InvoiceQty, 
                                    IF(PPRBal IS NULL,0,PPRBal)-YQty-IF(InvQty IS NULL,0,InvQty) AS Difference, 
                                    IF(zh.TotalInventory IS NULL, 0,zh.TotalInventory) AS CurrentInvtry, 
                                    IF(Total_PR_Balance.TtlPR_Bal IS NULL, 0 ,Total_PR_Balance.TtlPR_Bal) AS Requirement, 
                                    IF(IF(PPRBal IS NULL,0,PPRBal)-(YQty-IF(InvQty IS NULL,0,InvQty))=0,'','*') AS Chk, 
                                    PR_DifferenceCheck2.Remarks
                                FROM temp_prb_xslip AS PR_DifferenceCheck2 
                                RIGHT JOIN (
                                    SELECT PR,IF(PR IS NULL,'',SUBSTR(PR,1,IF(INSTR(PR,'-')>0,INSTR(PR,'-'),LENGTH (PR))-1)) AS YPR, 
                                        Noukikaito.Code AS mcode, 
                                        Noukikaito.Name AS mname, 
                                        SUM(Noukikaito.payment) AS YQty
                                    FROM temp_prb_noukikaito Noukikaito
                                    GROUP BY IF(PR IS NULL,'',SUBSTR(PR,1,INSTR(PR,'-')-1)), Noukikaito.Code, Noukikaito.Name
                                    HAVING (((IF(PR IS NULL,'',SUBSTR(PR,1,INSTR(PR,'-')-1))) NOT LIKE 'PR10%'))
                                )PR_DifferenceCheck1 ON PR_DifferenceCheck2.PR=PR_DifferenceCheck1.YPR
                                LEFT JOIN (
                                    SELECT mcode, SUM(pprbal) AS TtlPR_Bal
                                    FROM temp_prb_xslip
                                    GROUP BY mcode
                                )Total_PR_Balance ON PR_DifferenceCheck1.MCode=Total_PR_Balance.MCode
                                LEFT JOIN temp_prb_xzaik_xhiki zh ON PR_DifferenceCheck1.MCode=zh.CODE
                                LEFT JOIN temp_prb_invoice PR_DifferenceCheck1_1 ON PR_DifferenceCheck1_1.PODATA = PR_DifferenceCheck1.YPR
                                LEFT JOIN temp_prb_origprorder opro ON opro.CODE = PR_DifferenceCheck2.MCode
                                WHERE PR_DifferenceCheck1.YPR NOT LIKE 'PR10%'
                                    AND PR_DifferenceCheck2.PR IS NULL
                                UNION
                                SELECT invoice.PODATA AS PR, 
                                    invoice.CODE AS MCODE, 
                                    invoice.NAME AS MNAME, 
                                    IF(xzh.Supplier IS NULL,'',xzh.Supplier) AS Supplier,
                                    0 AS WHS100,
                                    0 AS WHS102,
                                    0 AS ASSY100,
                                    0 AS ASSY102,
                                    0 AS WHSNON,
                                    0 AS WHSSM,
                                    0 AS Total,
                                    xslip.OrderIssueDate, 
                                    IF(xslip.PODQty IS NULL,0,xslip.PODQty) AS PODQty, 
                                    IF(PPRBal IS NULL,0,PPRBal) AS PPRBal, 
                                    IF(YQty IS NULL,0,YQty) AS YEC_QTY, 
                                    invoice.InvQty AS InvoiceQty,
                                    0 AS Difference,
                                    xzh.TotalInventory AS CurrentInvtry, 
                                    xzh.TotalBalReq AS Requirement, 
                                    NULL AS Chk,
                                    xslip.Remarks
                                FROM(
                                    SELECT PR,IF(PR IS NULL,'',SUBSTR(PR,1,IF(INSTR(PR,'-')>0,INSTR(PR,'-'),LENGTH (PR))-1)) AS YPR, 
                                        Noukikaito.Code AS mcode, 
                                        Noukikaito.Name AS mname, 
                                        SUM(Noukikaito.payment) AS YQty
                                    FROM temp_prb_noukikaito Noukikaito
                                    GROUP BY IF(PR IS NULL,'',SUBSTR(PR,1,INSTR(PR,'-')-1)), Noukikaito.Code, Noukikaito.Name
                                    HAVING ((IF(PR IS NULL,'',SUBSTR(PR,1,INSTR(PR,'-')-1))) NOT LIKE 'PR10%')
                                ) AS noukikaito 
                                RIGHT JOIN temp_prb_invoice invoice ON invoice.PODATA = noukikaito.YPR
                                LEFT JOIN temp_prb_xslip AS xslip ON invoice.PODATA = xslip.PR
                                LEFT JOIN temp_prb_origprorder opro ON opro.CODE = xslip.MCode
                                LEFT JOIN temp_prb_xzaik_xhiki AS xzh ON invoice.CODE = xzh.Code
                                WHERE invoice.PODATA NOT LIKE 'PR10%'
                                    AND xslip.PR IS NULL
                                    AND noukikaito.YPR IS NULL) as a
                GROUP BY PR1, MCode
                ");
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $prb;
    }
}
