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

class TransactionController extends Controller
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

    public function getTransetting(Request $request)
    {
        $id = $request->id;
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_TRANSET'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $tableData = DB::connection($this->common)->table('tbl_transaction')->get();
            return view('security.TransactionSettings',['userProgramAccess' => $userProgramAccess, 'tableData' => $tableData]); 
        }
    }

    public function postAddDescription(Request $request)
    {
       
        $table = "tbl_transaction";
        $exist = $request->data;
        $dataexist = DB::connection($this->common)->table($table)->where('code',$exist['code'])->get();
        if($dataexist){
            $msg = "Data Already Exist.";
            return redirect('/transactionsetting')->with(['err_message'=>$msg]);
        } else { 
                $ok = DB::connection($this->common)->table($table)
                ->insert([
                    'code' => $exist['code'],
                    'description' => $exist['desc'],
                    'prefix' => $exist['prefix'],
                    'prefixformat' => $exist['prefixfm'],
                    'nextno' => $exist['nextno'],
                    'nextnolength' => $exist['nextnolength'],
                ]);

                if ($ok) {
                    $msg = "Successfully saved.";
                    return redirect('/transactionsetting')->with(['message'=>$msg]);
                } else {
                     $msg = "Saving Failed.";
                    return redirect('/transactionsetting')->with(['err_message'=>$msg]);
                }
        }  
        
    }

    public function updatePost(Request $request)
    {

        $table = "tbl_transaction";
        $field = $request->data;
        $code = $field['code'];
        $desc = $field['desc'];
        $prefix = $field['prefix'];
        $prefixfm = $field['prefixfm'];
        $nextno = $field['nextno'];
        $nextnolength = $field['nextnolength'];
        $id = $field['masterid'];
 
        $ok = DB::connection($this->common)->table($table)
            ->where('id', $id)
            ->update(array('code'=>$code,'description' =>$desc,'prefix' =>$prefix,'prefixformat'=>$prefixfm,'nextno'=>$nextno,'nextnolength'=> $nextnolength ));

        if ($ok) {
            $msg = "Successfully saved.";
            return redirect('/transactionsetting')->with(['message'=>$msg]);
        } else {
             $msg = "Saving Failed.";
            return redirect('/transactionsetting')->with(['err_message'=>$msg]);
        }
    }

    public function deleteAllPost(Request $request){  
        $tray = $request->tray;
        $traycount = $request->traycount;  
       /* return $tray;  */
         if($traycount > 0){
            $ok = DB::connection($this->common)->table('tbl_transaction')->wherein('id',$tray)->delete();
        
            if ($ok) {
                $msg = "Successfully deleted selected records.";
                return redirect('/transactionsetting')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/transactionsetting')->with(['err_message'=>$msg]);
            }
        } else {
             $ok = DB::connection($this->common)->table('tbl_transaction')->delete();
        
            if ($ok) {
                $msg = "Successfully deleted all records.";
                return redirect('/transactionsetting')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/transactionsetting')->with(['err_message'=>$msg]);
            }
        }
    }

   /* public function searchPost(Request $request)
    {
       
        $find = $request->find;
        $searchid = DB::connection($this->common)->table('tbl_transaction')
        ->where('code', '=', $find)
        ->get();

       if(count($searchid)>0){
            $id = $searchid[0]->id;//converts an arrray object----------
            $msg = "Record Found";
            return redirect('/transactionsetting?id='. $id)->with(['message'=>$msg,'tableData'=>$id]);

        }else if(count($searchid==0)) {
            
            $msg = "No Record Found";
            return redirect('/transactionsetting')->with(['err_message'=>$msg]);
        }
      
      
    }*/



}
