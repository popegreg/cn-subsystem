<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use Illuminate\Http\Request;
use App\mUserprogram;
use Datatables;

use DB;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userProgramAccess = DB::connection($this->common)->table('muserprograms as U')
                                    ->join('mprograms as P', 'P.program_code', '=', 'U.program_code')
                                    ->select('P.program_name', 'U.program_code','U.user_id','U.read_write','P.program_class')
                                    ->where('U.user_id', Auth::user()->user_id)
                                    ->where('U.delete_flag', 0)
                                    ->orderBy('U.id','asc')->get();
        $data = DB::connection($this->mysql)
                    ->table('tbl_mrareport')
                    ->where('forordering', '>', 0)
                    ->select('ItemCode as itemcode',
                            'ItemName as itemname',
                            'ForOrdering as forordering')
                    ->get();
        
        return view('home', ['userProgramAccess' => $userProgramAccess,'datas' => $data]);
    }

    public function getData()
    {
        $data = DB::connection($this->mysql)
                    ->table('tbl_mrareport')
                    ->where('forordering', '>', 0)
                    ->select('ItemCode as itemcode',
                            'ItemName as itemname',
                            'ForOrdering as forordering');
        return Datatables::of($data)->make(true);
    }

     
}
