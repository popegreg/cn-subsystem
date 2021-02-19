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

class ReBOMController extends Controller
{
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
    
    public function getReBOM()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_INVQUERY'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('phase2.ReBOM',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function postReBOMItems()
    {
        $db = DB::connection($this->mysql)->table('tbl_stockquery')->where('prodcode','<>','')->get();
        return $db;
    }

    public function reBOMgetProduct(Request $request)
    {
        $db = DB::connection($this->mysql)->table('tbl_stockquery')
                        ->where('name','like',$request->partname.'%')
                        ->select('prodcode','prodname','usage')
                        ->get();

        return $db;
    }

    public function reBOMdetails(Request $request)
    {
        try {

            return DB::connection($this->mysql)->table('tbl_stockquery')
                        ->where('name','like',$request->partname.'%')
                        ->select('vendor','price','assy100','assy102','whs100','whs102','whsnon','whssm')
                        ->get();
        } catch (Exception $e) {
            return redirect(url('/inventoryqueryrebom'))->with(['err_message' => $e]);
        }
    }

}
