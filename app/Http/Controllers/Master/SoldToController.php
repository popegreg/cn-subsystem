<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use Illuminate\Http\Response;
use Config;
use DB;

class SoldToController extends Controller
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

    public function getSold(Request $request)
    {
        $id = $request->id;
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_SLDTO'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $tableData = DB::connection($this->common)->table('tbl_soldto')->get();
            return view('master.SoldTo',['userProgramAccess' => $userProgramAccess, 'tableData' => $tableData]); 
            /*if($id){
                $count = DB::connection($this->common)->table('tbl_soldto')->count();
                $pagination = DB:: table('tbl_soldto')->where('id',$id)->paginate(5); 
                $searchedid = DB::connection($this->common)->table('tbl_soldto')->where('id',$id)->get();
                return view('SoldTo',['userProgramAccess' => $userProgramAccess,'tableData' => $searchedid, 'tableData' => $pagination]);
            } else {
                $count = DB::connection($this->common)->table('tbl_soldto')->count(); 
                $pagination = DB:: table('tbl_soldto')->paginate(5); 
                $tableData = DB::connection($this->common)->table('tbl_soldto')->get();
                return view('SoldTo',['userProgramAccess' => $userProgramAccess,'tableData' => $tableData, 'tableData' => $pagination]); 
            }*/
           
        }
    }

     public function postAddsold(Request $request)
    {
        $table = "tbl_soldto";
        $field = $request->data;
        $exist = $field['code'];
        $dataexist = DB::connection($this->common)->table($table)->where('code',$exist)->get();
        if($dataexist){
            $msg = "Data Already Exist.";
            return redirect('/sold-to')->with(['err_message'=>$msg]);
        } else {
            $display ;
            $ok = DB::connection($this->common)->table($table)
                    ->insert([
                        'code' => $field['code'],
                        'companyname' => $field['compname'],
                        'description' => $field['description'],                      
                    ]);

            if ($ok) {
                $msg = "Successfully saved.";
                return redirect('/sold-to')->with(['message'=>$msg]);
            } else {
                    $msg = "Saving Failed.";
                return redirect('/sold-to')->with(['err_message'=>$msg]);
            }
        }
       
    }

    public function updatePost(Request $request)
    {

        $table = "tbl_soldto";
        $field = $request->data;
        $id = $field['masterid'];
        $code = $field['code'];
        $compname = $field['compname'];
        $description = $field['description'];

        $ok = DB::connection($this->common)->table($table)
            ->where('id', $id)
            ->update(array('code'=>$code,'companyname'=>$compname,'description' =>$description));

        if ($ok) {
            $msg = "Successfully saved.";
            return redirect('/sold-to')->with(['message'=>$msg]);
        } else {
             $msg = "Saving Failed.";
            return redirect('/sold-to')->with(['err_message'=>$msg]);
        }
    }

   /* public function deletePost(Request $request){
        $productId = $request->masterid;      
        $ok = DB::connection($this->common)->table('tbl_soldto')->where('id', $productId)->delete();
       if ($ok) {
            $msg = "Record successfully deleted.";
            return redirect('/sold-to')->with(['message'=>$msg]);
        } else {
             $msg = "Deleteing failed";
            return redirect('/sold-to')->with(['err_message'=>$msg]);
        }
    }*/

    public function deleteAllPost(Request $request)
    {
       
        $tray = $request->tray;
        $traycount = $request->traycount;
       
        if($traycount > 0){
            $ok = DB::connection($this->common)->table('tbl_soldto')->wherein('id',$tray)->delete();
        
            if ($ok) {
                $msg = "Successfully deleted selected records.";
                return redirect('/sold-to')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/sold-to')->with(['err_message'=>$msg]);
            }
        } else {
             $ok = DB::connection($this->common)->table('tbl_soldto')->delete();
        
            if ($ok) {
                $msg = "Successfully deleted all records.";
                return redirect('/sold-to')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/sold-to')->with(['err_message'=>$msg]);
            }
        }
    }
    /*public function searchPost(Request $request)
    {
       
        $find = $request->find;
        $searchid = DB::connection($this->common)->table('tbl_soldto')
        ->where('code', '=', $find)
        ->get();

       if(count($searchid)>0){
            $id = $searchid[0]->id;//converts an arrray object----------
            $msg = "Record Found";
            return redirect('/sold-to?id='. $id)->with(['message'=>$msg,'tableData'=>$id]);

        }else if(count($searchid==0)) {
            
            $msg = "No Record Found";
            return redirect('/sold-to')->with(['err_message'=>$msg]);
        }
      
      
    }
*/
    
}
