<?php

namespace App\Http\Controllers\Phase1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;
use Excel;
use Config;
use DB;
use Schema;

class mraController extends Controller
{
    public $mysql;
    public $mssql;
    public $common;

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

    public function getMRA()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_MRA'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            //$mraitems = DB::select(DB::raw('CALL display_mra'));
            $mraitems = DB::connection($this->mysql)->table('tbl_mrareport')->where('ForOrdering','>',0)->get();
            return view('phase1.mra',['userProgramAccess' => $userProgramAccess,'mraitems' => $mraitems]);
        }
    }

    public function getMRAload()
    {
        $mraitems = DB::connection($this->mysql)->table('tbl_mrareport')
                        ->where('ForOrdering','>',0)
                        ->select()
                        ->get();
        return $mraitems;
    }
    
    private function truncateTable($table)
    {
        return DB::connection($this->mysql)->table($table)->truncate();
    }

    public function getMRAprint()
    {
    	try {
            $mraItems = $this->MRA(); //DB::connection('mysql')->table('tbl_mrareport')->get()
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);

            $filename = 'MRA_report_'.$date;

            Excel::create($filename, function($excel) use($mraItems) {
                $excel->sheet('Sheet1', function($sheet) use($mraItems) {
                    $sheet->cell('A1', "ItemCode");
                    $sheet->cell('B1', "ItemName");
                    $sheet->cell('C1', "BUNR");
                    $sheet->cell('D1', "TotalRequired");
                    $sheet->cell('E1', "TotalCompleted");
                    $sheet->cell('F1', "Req_To_Complete");
                    $sheet->cell('G1', "WHSE_100");
                    $sheet->cell('H1', "WHSE_102");
                    $sheet->cell('I1', "WHSE_NON");
                    $sheet->cell('J1', "ASSY100");
                    $sheet->cell('K1', "ASSY102");
                    $sheet->cell('L1', "WHS_SM");
                    $sheet->cell('M1', "Total_On-Hand");
                    $sheet->cell('N1', "OrderBalance");
                    $sheet->cell('O1', "ForOrdering");
                    $sheet->cell('P1', "MAINBUMO");

                    $row = 2;
                    $count = count($mraItems);
                    // for ($i=0; $i < $count; $i++) {
                    //     if (isset($mra->ItemCode)) {
                    foreach ($mraItems as $key => $mra) {
                        $OrderBalance = $mra->OrderBal;//$mra->Scheduled - $mra->Actual;
                        $total = $mra->WHS100 + $mra->WHSNON + $mra->ASSY100 + $mra->WHSSM;
                        $ForOrdering = $mra->REQCOM - $total - $OrderBalance;

                        if ($mra->TtlRequired == 0) {
                            $mra->TtlRequired = "0.0";
                        }
                        if ($mra->TtlCompleted == 0) {
                            $mra->TtlCompleted = "0.0";
                        }
                        if ($mra->REQCOM == 0) {
                            $mra->REQCOM = "0.0";
                        }
                        if ($mra->WHS100 == 0) {
                            $mra->WHS100 = "0.0";
                        }
                        if ($mra->WHSNON == 0) {
                            $mra->WHSNON = "0.0";
                        }
                        if ($mra->ASSY100 == 0) {
                            $mra->ASSY100 = "0.0";
                        }
                        if ($mra->ASSY102 == 0) {
                            $mra->ASSY102 = "0.0";
                        }
                        if ($mra->WHSSM == 0) {
                            $mra->WHSSM = "0.0";
                        }
                        if ($total == 0) {
                            $total = "0.0";
                        }
                        if ($OrderBalance == 0) {
                            $OrderBalance = "0.0";
                        }
                        if ($ForOrdering == 0) {
                            $ForOrdering = "0.0";
                        }

                        $sheet->cell('A'.$row, $mra->CODE);
                        $sheet->cell('B'.$row, $mra->NAME);
                        $sheet->cell('C'.$row, $mra->BUNR);
                        $sheet->cell('D'.$row, number_format($mra->TtlRequired, 2, '.', ''));
                        $sheet->cell('E'.$row, number_format($mra->TtlCompleted, 2, '.', ''));
                        $sheet->cell('F'.$row, number_format($mra->REQCOM, 2, '.', ''));
                        $sheet->cell('G'.$row, $mra->WHS100);
                        $sheet->cell('H'.$row, 0);
                        $sheet->cell('I'.$row, $mra->WHSNON);
                        $sheet->cell('J'.$row, $mra->ASSY100);
                        $sheet->cell('K'.$row, $mra->ASSY102);
                        $sheet->cell('L'.$row, $mra->WHSSM);
                        $sheet->cell('M'.$row, number_format($total, 2, '.', ''));
                        $sheet->cell('N'.$row, number_format($OrderBalance, 2, '.', ''));
                        $sheet->cell('O'.$row, number_format($ForOrdering, 2, '.', ''));
                        $sheet->cell('P'.$row, $mra->BUMO);
                        // }
                        $row++;
                    }
                });
            })->download('xls');

        } catch (Exception $e) {
            return redirect(url('/mra'))->with(['err_message' => $e]);
        }
    }


    private function insertToTblMra($code,$name,$type,$TtlReq,$TtlComp,$ReqToComplete,$WHS100,$WHSNON,$ASSY100,$ASSY102,$WHSSM,$MAINBUMO,$OrderBalance)
    {
        $total = $WHS100+$WHSNON+$ASSY100+$ASSY102+$WHSSM;
        $ForOrdering = $ReqToComplete - $total - $OrderBalance;
        return DB::connection($this->mysql)->table('tbl_mrareport')->insert([
                    'ItemCode' => $code,
                    'ItemName' => $name,
                    'ItemType' => $type,
                    'TtlRequired' => $TtlReq,
                    'TtlCompleted' => $TtlComp,
                    'ReqToComplete' => $ReqToComplete,
                    'WHSE100' => $WHS100,
                    'WHSE102' => 0,
                    'WHSE_NON' => $WHSNON,
                    'ASSY100' => $ASSY100,
                    'ASSY102' => $ASSY102,
                    'WHSESM' => $WHSSM,
                    'TotalOnHand' => $total,
                    'OrderBalance' => $OrderBalance,
                    'ForOrdering' => $ForOrdering,
                    'MAINBUMO' => $MAINBUMO,
                ]);
    }

    private function MRA()
    {
        try {
            $db = DB::connection($this->mssql)
                    ->select("SELECT i.CODE,    
                            h.NAME,
                            i.BUNR,
                            i.BUMO,
                            ISNULL(hk.totalReq,0) as TtlRequired,
                            ISNULL(hk.totalComplete,0) as TtlCompleted,
                            ISNULL(hk.REQCOM,0) as REQCOM,
                            SUM(x.WHS100) as WHS100,
                            SUM(x.WHSNON) as WHSNON,
                            SUM(x.ASSY100) as ASSY100,
                            SUM(x.ASSY102) as ASSY102,
                            SUM(x.WHSSM) as WHSSM,
                            ISNULL(SUM(s.KVOL),0) AS Scheduled,
                            ISNULL(SUM(s.TJITU),0) AS Actual,
                            ISNULL(s.OrderBal,0) as OrderBal
                        FROM XITEM i
                        LEFT join XHEAD h ON i.CODE = h.CODE
                        LEFT join (SELECT CODE,
                                        ISNULL(SUM(KVOL),0) as totalReq,
                                        ISNULL(SUM(TJITU),0) as totalComplete,
                                        ISNULL((SUM(KVOL) - SUM(TJITU)),0) as REQCOM
                            FROM XHIKI
                            GROUP BY CODE) hk ON i.CODE = hk.CODE
                        LEFT join (SELECT CODE,
                                        ISNULL(SUM(KVOL),0) as KVOL,
                                        ISNULL(SUM(TJITU),0) as TJITU,
                                        ISNULL((SUM(KVOL) - SUM(TJITU)),0) AS OrderBal FROM XSLIP
                                        WHERE PORDER not like 'GR%'
                           GROUP BY CODE) s ON i.CODE = s.CODE
                        LEFT join (SELECT z.CODE,
                                        ISNULL(z1.ZAIK,0) as WHS100,
                                        ISNULL(z3.ZAIK,0) as WHSNON,
                                        ISNULL(za1.ZAIK,0) as ASSY100,
                                        ISNULL(za2.ZAIK,0) as ASSY102,
                                        ISNULL(z4.ZAIK,0) as WHSSM,
                                        ISNULL(z1.ZAIK + z3.ZAIK + z4.ZAIK + za1.ZAIK, 0) as TOTAL
                                    FROM XZAIK z
                                        LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
                                        LEFT JOIN XZAIK z3 ON z3.CODE = z.CODE AND z3.HOKAN = 'WHS-NON'
                                        LEFT JOIN XZAIK za1 ON za1.CODE = z.CODE AND za1.HOKAN = 'ASSY100'
                                        LEFT JOIN XZAIK za2 ON za2.CODE = z.CODE AND za2.HOKAN = 'ASSY102'
                                        LEFT JOIN XZAIK z4 ON z4.CODE = z.CODE AND z4.HOKAN = 'WHS-SM'
                                    WHERE z.JYOGAI <> '1'
                                    GROUP BY z.CODE, z1.ZAIK , z3.ZAIK, z4.ZAIK, za1.ZAIK, za2.ZAIK
                                    ) AS x
                        ON i.CODE = x.CODE
                        WHERE i.BUMO = 'PURH100' and i.HOKAN = 'WHS100'
                        GROUP BY i.CODE, h.NAME, i.BUNR, i.BUMO, hk.totalReq, hk.totalComplete,hk.REQCOM,s.OrderBal");

            return $db;

        } catch (Exception $e) {
            return redirect(url('/mra'))->with(['err_message' => $e]);
        }
    }

    public function generateMRA()
    {
        //return $this->MRA();
        $this->truncateTable('tbl_mrareport');
        $OrderBalance = 0;
        foreach ($this->MRA() as $key => $mra) {
            $OrderBalance = $mra->OrderBal;
            $total = $mra->WHS100 + $mra->WHSNON + $mra->ASSY100 + $mra->WHSSM;
            $ForOrdering = $mra->REQCOM - $total - $OrderBalance;

            if ($ForOrdering > 0) {
                $this->insertToTblMra($mra->CODE,$mra->NAME,$mra->BUNR,$mra->TtlRequired,$mra->TtlCompleted,$mra->REQCOM,$mra->WHS100,$mra->WHSNON,$mra->ASSY100,$mra->ASSY102,$mra->WHSSM,$mra->BUMO,$OrderBalance);
            }

        }
        $mraitems = DB::connection($this->mysql)->table('tbl_mrareport')->where('ForOrdering','>',0)->get();

        return $mraitems;
    }
}