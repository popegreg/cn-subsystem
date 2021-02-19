<?php
namespace App\Http\Controllers\Phase2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Config;
use DB;
use Excel;
use Illuminate\Support\Facades\Auth; #Auth facade
use Datatables;

class ByPartsController extends Controller
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
    
    public function getByParts()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_INVQUERY'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $parts = DB::connection($this->mysql)->table('tbl_stockquery')->where('prodcode','<>','')->get();
            return view('phase2.ByParts',['userProgramAccess' => $userProgramAccess]); //, 'parts' => $parts
        }
    }

    public function getByPartsItems()
    {
        $db = DB::connection($this->mysql)->table('tbl_stockquery')->get();
        return $db;
    }

    public function postByPartshowItem(Request $request)
    {
        // $parts = $this->byPartsdetails($request->partname);
        // return $parts;
        $parts = DB::connection($this->mysql)
                    ->table('tbl_stockquery')
                    ->select(['code',
                            'name',
                            'price',
                            'vendor',
                            'whssm',
                            'whsnon',
                            'whs102',
                            'whs100',
                            'assy100',
                            'assy102',
                            'stocktotal',
                            'available',
                            'requirement']);
        // $parts = DB::connection($this->mysql)->table('tbl_stockquery')->get();
        return Datatables::of($parts)
                        // ->filter(function ($query) use ($request) {
                        //     if ($request->has('code')) {
                        //         $query->where('code', 'like', "%{$request->get('code')}%");
                        //     }

                        //     if ($request->has('name')) {
                        //         $query->where('name', 'like', "%{$request->get('name')}%");
                        //     }
                        // })
                        ->make(true);
    }

    private function byPartsdetails($code)
    {
        try {

            return DB::connection($this->mssql)
                        ->table('XPRTS as b')
                        ->join('XHEAD as h', 'b.KCODE', '=', 'h.CODE')
                        ->join('XSLIP as s', 'b.KCODE', '=', 's.CODE')
                        ->join('XHIKI as d', 'b.KCODE', '=', 'd.CODE')
                        ->join('XTANK as p', 'b.KCODE', '=', 'p.CODE')
                        ->join(DB::raw("( SELECT z.CODE
                                        , SUM(z.ZAIK) as CurrInv
                                        , ISNULL(z1.ZAIK,0) as WHSSM
                                        , ISNULL(z2.ZAIK,0) as WHSNON
                                        , ISNULL(z3.ZAIK,0) as WHS102
                                        , ISNULL(z4.ZAIK,0) as WHS100
                                        , ISNULL(z5.ZAIK,0) as ASSY100
                                        , ISNULL(z6.ZAIK,0) as ASSY102
                                    FROM XZAIK z
                                    LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS-SM'
                                    LEFT JOIN XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS-NON'
                                    LEFT JOIN XZAIK z3 ON z3.CODE = z.CODE AND z3.HOKAN = 'WHS102'
                                    LEFT JOIN XZAIK z4 ON z4.CODE = z.CODE AND z4.HOKAN = 'WHS100'
                                    LEFT JOIN XZAIK z5 ON z5.CODE = z.CODE AND z5.HOKAN = 'ASSY100'
                                    LEFT JOIN XZAIK z6 ON z6.CODE = z.CODE AND z6.HOKAN = 'ASSY102'
                                    GROUP BY z.CODE
                                        , z1.ZAIK
                                        , z2.ZAIK
                                        , z3.ZAIK
                                        , z4.ZAIK
                                        , z5.ZAIK
                                        , z6.ZAIK) as x"),'x.CODE', '=', 'b.KCODE')
                        ->select(DB::raw('b.KCODE as partcode'),
                            DB::raw('h.NAME as partname'),
                            'i.VENDOR',
                            'p.PRICE',
                            DB::raw('x.WHSSM as WHSSM'),
                            DB::raw('x.WHSNON as WHSNON'),
                            DB::raw('x.WHS100 as WHS100'),
                            DB::raw('x.WHS102 as WHS102'),
                            DB::raw('x.ASSY100 as ASSY100'),
                            DB::raw('x.ASSY102 as ASSY102'),
                            DB::raw('SUM(d.KVOL) - SUM(d.TJITU) AS req'),
                            DB::raw("SUM(s.TJITU) as available"),
                            'x.CurrInv')
                        ->where('b.KCODE',$code)
                        ->groupBy('b.KCODE', 'h.NAME','s.VENDOR','p.PRICE','x.WHSSM','x.WHSNON','x.WHS100','x.WHS102','x.ASSY100','x.ASSY102','x.CurrInv')
                        ->get();
        } catch (Exception $e) {
            return redirect(url('/inventoryquerybyparts'))->with(['err_message' => $e]);
        }
    }
}
