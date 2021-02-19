<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Response;
use Config;
use DB;
use Illuminate\Support\Facades\Auth; #Auth facade

class CompanyController extends Controller
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

    public function getComSetting()
    {
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_COMSET'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $count = DB::connection($this->common)->table('tbl_update_companysetting')->count(); 
            $tableData = DB::connection($this->common)->table('tbl_update_companysetting')->get();
             
            return view('security.CompanySettings',['userProgramAccess' => $userProgramAccess,'tableData' => $tableData,'count'=>$count]);
        }
    }

     public function updatePost(Request $request)
    {

        $table = "tbl_update_companysetting";
        $name = $request->name;
        $address = $request->address;
        $tel1 = $request->tel1;
        $tel2 = $request->tel2;
        
        $count =DB::connection($this->common)->table($table)->count(); 
        if($count == '0'){
             $ok = DB::connection($this->common)->table($table)
                ->insert([
                    'name' => $request->name,
                    'address' => $request->address,
                    'tel1' => $request->tel1,
                    'tel2' => $request->tel2
                ]);


            if ($ok) {
                $msg = "Successfully saved.";
                return redirect('/companysetting')->with(['message'=>$msg]);
            } else {
                 $msg = "Saving Failed.";
                return redirect('/companysetting')->with(['err_message'=>$msg]);
            }
        } else {
             $ok = DB::connection($this->common)->table($table)
            ->update(array('name'=>$name,'address' =>$address,'tel1' =>$tel1,'tel2' => $tel2 ));


            if ($ok) {
                $msg = "Successfully saved.";
                return redirect('/companysetting')->with(['message'=>$msg]);
            } else {
                 $msg = "Saving Failed.";
                return redirect('/companysetting')->with(['err_message'=>$msg]);
            }
        }
        
        
    }
}
