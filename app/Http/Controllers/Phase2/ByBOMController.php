<?php
namespace App\Http\Controllers\Phase2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Datatables;
use Config;
use DB;
use Excel;
use Illuminate\Support\Facades\Auth; #Auth facade

class ByBOMController extends Controller
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

    public function getByBOM()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_INVQUERY'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            
            return view('phase2.ByBOM',['userProgramAccess' => $userProgramAccess]); // 'boms' => $boms
        }
    }

    public function byBOMdetails()
    {
        $boms = DB::connection($this->mysql)->table('tbl_stockquery')
                        ->select(['code',
                                'name',
                                'usage',
                                'price',
                                'vendor',
                                'assy100',
                                'whs100',
                                'whs102',
                                'whsnon',
                                'whssm',
                                'stocktotal',
                                'requirement',
                                'available',
                                'prbalance',
                                'prodcode',
                                'prodname'
                                ]);
        return Datatables::of($boms)->make(true);
    }

    public function getByBOMshowProdItems()
    {
        $prod = array();
        $name = array();
        $zaiks = DB::connection($this->mysql)->table('tbl_stockquery')->get();
        foreach ($zaiks as $key => $zaik) {
            $prod[] = DB::connection($this->mssql)
                        ->table('XPRTS as b')
                        ->leftJoin('XHEAD as h','b.CODE','=','h.CODE')
                        ->select('b.CODE','h.NAME')
                        ->where('b.KCODE',$zaik->code)
                        ->get();
        }
        $prd = call_user_func_array('array_merge', $prod);
        foreach ($prd as $key => $p) {
            $name[$p->CODE] = mb_convert_encoding($p->NAME,"UTF-8","SJIS");
        }

        $u = array_unique($name);

        return $u;

    }

    public function getByBOMProdItems()
    {
        $boms = $this->byBOMdetails();
        return $boms;
    }
}
