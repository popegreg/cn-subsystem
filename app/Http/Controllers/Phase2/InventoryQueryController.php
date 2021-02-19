<?php
namespace App\Http\Controllers\Phase2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use Config;
use DB;
use Excel;
use Carbon\Carbon;

class InventoryQueryController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;
    
    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'stocksquery');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getInventoryQuery()
    {
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_STCKQUERY'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('phase2.InventoryQuery',['userProgramAccess' => $userProgramAccess]);
        }
    }

    private function byPartsgetProduct($code)
    {
        $db = DB::connection($this->mssql)
                ->table('XITEM as i')
                ->join('XHEAD as h','i.CODE','=','h.CODE')
                ->join('XPRTS as b','i.CODE','=','b.KCODE')
                ->select('i.CODE','h.NAME','b.CODE','b.SIYOU')
                ->where('b.KCODE',$code)
                ->skip(0)->take(1000)->get();

        return $db;
    }

    private function byPartsdetails($code)
    {
        try {

            return DB::connection($this->mssql)
                        ->table('XITEM as i')
                        ->join('XHEAD as h', 'i.CODE', '=','h.CODE')
                        ->join('XTANK as p', 'i.CODE','=','p.CODE')
                        ->join(DB::raw("( SELECT z.CODE
                                , ISNULL(z1.ZAIK,0) as WHS100
                                , ISNULL(z2.ZAIK,0) as WHS102
                                , ISNULL(z3.ZAIK,0) as WHSNON
                                , ISNULL(z4.ZAIK,0) as ASSY100
                                , ISNULL(z5.ZAIK,0) as ASSY102
                                , ISNULL(z6.ZAIK,0) as WHSSM
                            FROM XZAIK z
                            LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
                            LEFT JOIN XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS102'
                            LEFT JOIN XZAIK z3 ON Z3.CODE = z.CODE AND Z3.HOKAN = 'WHS-NON'
                            LEFT JOIN XZAIK z4 ON z4.CODE = z.CODE AND z4.HOKAN = 'ASSY100'
                            LEFT JOIN XZAIK z5 ON z5.CODE = z.CODE AND z5.HOKAN = 'ASSY102'
                            LEFT JOIN XZAIK Z6 ON Z6.CODE = z.CODE AND Z6.HOKAN = 'WHS-SM'
                            GROUP BY z.CODE
                                , z1.ZAIK
                                , z2.ZAIK
                                , Z3.ZAIK
                                , z4.ZAIK
                                , z5.ZAIK
                                , z6.ZAIK) x"), 'x.CODE', '=', 'i.CODE')
                        ->select('i.CODE'
                            , 'i.VENDOR'
                            , 'p.PRICE'
                            , 'h.NAME'
                            , DB::raw("SUM(x.WHS100) as WHS100")
                            , DB::raw("SUM(x.WHS102) as WHS102")
                            , DB::raw("SUM(x.WHSNON) as WHSNON")
                            , DB::raw("SUM(x.ASSY100) as ASSY100")
                            , DB::raw("SUM(x.ASSY102) as ASSY102")
                            , DB::raw("SUM(x.WHSSM) as WHSSM"))
                        ->where('i.CODE',$code)
                        ->groupBy('i.CODE', 'i.VENDOR','p.PRICE','h.NAME')
                        ->get(); //->skip(0)->take(100)

        } catch (Exception $e) {
            return redirect(url('/inventoryquerybyparts'))->with(['err_message' => $e]);
        }
    }



    private function byBOMgetProduct($code)
    {
        $db = DB::connection($this->mssql)
                ->table('XITEM as i')
                ->join('XHEAD as h','i.CODE','=','h.CODE')
                ->join('XPRTS as b','i.CODE','=','b.CODE')
                ->select('i.CODE','h.NAME','b.SIYOU')
                ->where('i.CODE',$code)
                ->skip(0)->take(1000)->get();

        return $db;
    }







    private function truncateTable($table)
    {
        return DB::connection($this->mysql)->table($table)->truncate();
    }

    public function postUpdatebtn(Request $request)
    {
        ini_set('max_execution_time', 0);
        $this->truncateTable('tbl_stockquery');

        $value = [];

        $dbs = DB::connection($this->mssql)
                    ->select(DB::raw("
                        SELECT partcode, NAME, VENDOR, PRICE, 
                            WHSSM, WHSNON, WHS100, WHS102, ASSY100, ASSY102, WHSNG, 
                            (WHSSM + WHSNON + WHS100 + WHS102 + ASSY100 + ASSY102 + WHSNG) AS TOTAL, 
                            REQ, 
                            (WHSSM + WHSNON + WHS100 + WHS102 + ASSY100 + ASSY102 + WHSNG) - REQ AS AVAILABLE, 
                            PRBAL, 
                            ISNULL((SELECT TOP(1)
                                d.OYACODE as prodcode
                                FROM XHIKI as d
                                JOIN XHEAD as h ON d.OYACODE = h.CODE
                                WHERE d.CODE = tbl.partcode), '') AS PRODCODE,
                            ISNULL((SELECT TOP(1)
                                h.NAME
                                FROM XHIKI as d
                                JOIN XHEAD as h ON d.OYACODE = h.CODE
                                WHERE d.CODE = tbl.partcode), '') AS PRODNAME, 
                            USAGE, UPDATED
                        FROM (
                            SELECT z.CODE as partcode
                                , h.NAME as name
                                , ISNULL(vp.vendor,'') as VENDOR
                                , ISNULL(vp.price,0) as PRICE
                                , ISNULL(z6.ZAIK,0) as WHSSM
                                , ISNULL(z3.ZAIK,0) as WHSNON
                                , ISNULL(z2.ZAIK,0) as WHS102
                                , ISNULL(z1.ZAIK,0) as WHS100
                                , ISNULL(z4.ZAIK,0) as ASSY100
                                , ISNULL(z5.ZAIK,0) as ASSY102
                                , ISNULL(z7.ZAIK,0) as WHSNG
                                , ISNULL(avl.req, 0.0000) as REQ
                                , ISNULL(avl.prbal, 0.0000) as PRBAL
                                , SUM(b.SIYOU) AS USAGE
                                , ISNULL(avl.upd, '') as UPDATED
                            FROM XZAIK z
                            LEFT JOIN XPRTS b ON b.KCODE = z.CODE
                            LEFT JOIN XHEAD h ON z.CODE = h.CODE
                            LEFT JOIN (SELECT CODE, ZAIK FROM XZAIK WHERE HOKAN = 'WHS100') z1 ON z1.CODE = z.CODE
                            LEFT JOIN (SELECT CODE, ZAIK FROM XZAIK WHERE HOKAN = 'WHS102') z2 ON z2.CODE = z.CODE
                            LEFT JOIN (SELECT CODE, ZAIK FROM XZAIK WHERE HOKAN = 'WHS-NON') z3 ON Z3.CODE = z.CODE
                            LEFT JOIN (SELECT CODE, ZAIK FROM XZAIK WHERE HOKAN = 'ASSY100') z4 ON z4.CODE = z.CODE
                            LEFT JOIN (SELECT CODE, ZAIK FROM XZAIK WHERE HOKAN = 'ASSY102') z5 ON z5.CODE = z.CODE
                            LEFT JOIN (SELECT CODE, ZAIK FROM XZAIK WHERE HOKAN = 'WHS-SM') Z6 ON Z6.CODE = z.CODE
                            LEFT JOIN (SELECT CODE, ZAIK FROM XZAIK WHERE HOKAN = 'WHS-NG' AND JYOGAI = 0) Z7 ON Z7.CODE = z.CODE
                            LEFT JOIN (
                                    SELECT p.CODE, 
                                        p.PRICE as price, 
                                        i.VENDOR as vendor 
                                    FROM XTANK as p 
                                    JOIN XITEM as i ON p.CODE = i.CODE
                                ) vp ON vp.CODE = z.CODE
                            LEFT JOIN (
                                    SELECT h.CODE, SUM(h.req) AS req, SUM(ISNULL(xs.prbal,0)) AS prbal, CAST(MAX(xs.upd) AS varchar) as upd
                                    FROM (
                                        SELECT d.CODE,
                                            SUM(d.KVOL) - SUM(d.TJITU) as req
                                        FROM XHIKI as d
                                        GROUP BY d.CODE
                                        ) h
                                    LEFT JOIN (
                                        SELECT s.CODE,
                                            SUM(s.KVOL) - SUM(s.TJITU) as prbal,
                                            CAST(MAX(s.INPUTDATE) AS varchar) as upd
                                        FROM XSLIP as s
                                        GROUP BY s.CODE
                                        HAVING SUM(s.KVOL) - SUM(s.TJITU) > 0
                                    )xs ON xs.CODE = h.CODE
                                    GROUP BY h.CODE
                                ) avl ON avl.CODE = z.CODE
                            GROUP BY z.CODE
                                , h.NAME
                                , z1.ZAIK
                                , z2.ZAIK
                                , Z3.ZAIK
                                , z4.ZAIK
                                , z5.ZAIK
                                , z6.ZAIK
                                , z7.ZAIK
                                , vp.price
                                , vp.vendor
                                , avl.prbal
                                , avl.req
                                , avl.upd
                        ) tbl;"));
        
        foreach ($dbs as $key => $db) {
            array_push($value, [
                    'code' => $db->partcode,
                    'name' => $db->NAME,
                    'vendor' => $db->VENDOR,
                    'price' => $db->PRICE,
                    'whssm' => $db->WHSSM,
                    'whsnon' => $db->WHSNON,
                    'whs102' => $db->WHS102,
                    'whs100' => $db->WHS100,
                    'assy100' => $db->ASSY100,
                    'assy102' => $db->ASSY102,
                    'stocktotal' => $db->TOTAL,
                    'requirement' => $db->REQ,
                    'available' => $db->AVAILABLE,
                    'prbalance' => $db->PRBAL,
                    'prodcode' => $db->PRODCODE,
                    'prodname' => $db->PRODNAME,
                    'usage' => $db->USAGE,
                    'updated' => $db->UPDATED,
                    'whsng' => $db->WHSNG,
                ]);
        }

        $insertBatchs = array_chunk($value, 1000);
        foreach ($insertBatchs as $batch) {
            DB::connection($this->mysql)->table('tbl_stockquery')->insert($batch);
        }
    }

    public function getStockQueryExcel(Request $req)
    {
        // try
        // {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);

            Excel::create('StockQuery_'.$date, function($excel)
            {
                $excel->sheet('Q_STOCK_INQUIRY', function($sheet)
                {
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "VENDOR");
                    $sheet->cell('D1', "PRICE");
                    $sheet->cell('E1', "ASSY100");
                    $sheet->cell('F1', "ASSY102");
                    $sheet->cell('G1', "WHS100");
                    $sheet->cell('H1', "WHS102");
                    $sheet->cell('I1', "WHS-NON");
                    $sheet->cell('J1', "WHS-SM");
                    $sheet->cell('K1', "WHS-NG");
                    $sheet->cell('L1', "StockTotal");
                    $sheet->cell('M1', "CurrentRequirement");
                    $sheet->cell('N1', "AvailableStock");
                    $sheet->cell('O1', "PR_Balance");

                    $row = 2;
                    $db = DB::connection($this->mysql)->table('tbl_stockquery')->get();
                    foreach ($db as $key => $item) {
                        $sheet->cell('A'.$row, $item->code);
                        $sheet->cell('B'.$row, $item->name);
                        $sheet->cell('C'.$row, $item->vendor);
                        $sheet->cell('D'.$row, $item->price);
                        $sheet->cell('E'.$row, ($item->assy100 == 0)? '0.00': $item->assy100);
                        $sheet->cell('F'.$row, ($item->assy102 == 0)? '0.00': $item->assy102);
                        $sheet->cell('G'.$row, ($item->whs100 == 0)? '0.00': $item->whs100);
                        $sheet->cell('H'.$row, ($item->whs102 == 0)? '0.00': $item->whs102);
                        $sheet->cell('I'.$row, ($item->whsnon == 0)? '0.00': $item->whsnon);
                        $sheet->cell('J'.$row, ($item->whssm == 0)? '0.00': $item->whssm);
                        $sheet->cell('K'.$row, ($item->whssm == 0)? '0.00': $item->whsng);
                        $sheet->cell('L'.$row, ($item->stocktotal == 0)? '0.00': $item->stocktotal);
                        $sheet->cell('M'.$row, ($item->requirement == 0)? '0.00': $item->requirement);
                        $sheet->cell('N'.$row, ($item->available == 0)? '0.00': $item->available);
                        $sheet->cell('O'.$row, ($item->prbalance == 0)? '0.00': $item->prbalance);
                        $row++;
                    }
                });
            })->export('xls');

        // }
        // catch (Exception $e)
        // {
        //     return redirect(url('/inventoryquery'))->with(['err_message' => $e]);
        // }
    }
}
