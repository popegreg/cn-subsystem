<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; #Auth facade
use Config;
use DB;

class WBSSettingController extends Controller
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

    public function getWBSSetting(Request $request)
    {
        $id = $request->id;
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_WBSSET'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $tableData = DB::connection($this->common)->table('tbl_wbssetting')->get();
            return view('security.WBSsettings',['userProgramAccess' => $userProgramAccess, 'tableData' => $tableData]); 
        }
    }

    public function postAddDescription(Request $request)
    {
        // return dd($request->all());
        $table = "tbl_wbssetting";
        $exist = $request->data;

        $dataexist = DB::connection($this->common)->table($table)->where('name',$exist['name'])->get();
       /* return $exist['desc'];*/
        if($dataexist){
            $msg = "Data Already Exist.";
            return redirect('/wbssetiing')->with(['err_message'=>$msg]);
        } else {
            $display ;
            $ok = DB::connection($this->common)->table($table)
                    ->insert([
                        'name' => $exist['name'],
                        'description' => $exist['desc'],
                        'value' => $exist['val']
                    ]);

            if ($ok) {
                $msg = "Successfully saved.";
                return redirect('/wbssetiing')->with(['message'=>$msg]);
            } else {
                    $msg = "Saving Failed.";
                return redirect('/wbssetiing')->with(['err_message'=>$msg]);
            }
        }
       
    }

    public function updatePost(Request $request)
    {

        $table = "tbl_wbssetting";
        $exist = $request->data;
 
        $ok = DB::connection($this->common)->table($table)
            ->where('id', $exist['masterid'])
            ->update(array('name'=>$exist['name'],'description' =>$exist['desc'],'value' =>$exist['val'] ));

        if ($ok) {
            $msg = "Successfully saved.";
            return redirect('/wbssetiing')->with(['message'=>$msg]);
        } else {
             $msg = "Saving Failed.";
            return redirect('/wbssetiing')->with(['err_message'=>$msg]);
        }
    }

    public function deleteAllPost(Request $request)
    {
       
        $tray = $request->tray;
        $traycount = $request->traycount;
       /* return $tray;*/
        if($traycount > 0){
            $ok = DB::connection($this->common)->table('tbl_wbssetting')->wherein('id',$tray)->delete();
        
            if ($ok) {
                $msg = "Successfully deleted selected records.";
                return redirect('/wbssetiing')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/wbssetiing')->with(['err_message'=>$msg]);
            }
        } else {
             $ok = DB::connection($this->common)->table('tbl_wbssetting')->delete();
        
            if ($ok) {
                $msg = "Successfully deleted all records.";
                return redirect('/wbssetiing')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/wbssetiing')->with(['err_message'=>$msg]);
            }
        }
       
    }

   /* public function searchPost(Request $request)
    {
       
        $find = $request->find;
        $searchid = DB::connection($this->common)->table('tbl_wbssetting')
        ->where('name', 'LIKE', $find)
        ->get();

       if(count($searchid)>0){
            $id = $searchid[0]->id;//converts an arrray object----------
            $msg = "Record Found";
            return redirect('/wbssetiing?id='. $id)->with(['message'=>$msg,'tableData'=>$id]);

        }else if(count($searchid==0)) {
            
            $msg = "No Record Found";
            return redirect('/wbssetiing')->with(['err_message'=>$msg]);
        }
      
      
    }
*/

 

}
