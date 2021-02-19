<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Config;
use DB;
use Session;
use Illuminate\Support\Facades\Auth; #Auth facade

class DropdownfifoMasterController extends Controller
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

    public function Dropdownfifo(Request $req)
    {
    	$common = new CommonController;

        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_DESTI'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            // $selected_category = $request_data['option'];
            // if(empty($selected_category))
            // {
            //     $selected_category = '1';
            // }

            // $category = DB::connection($this->mysql)
            //             ->table('tbl_mdropdown_category')->orderBy('category')->get();
            // $dropdownlist = DB::connection($this->mysql)
            //             ->table('tbl_mdropdowns')->where('category', '=', $selected_category)->get();

            // return view('master.dropdownfifo', 
            //    ['userProgramAccess' => $userProgramAccess]);
            return view('master.dropdownfifo',['userProgramAccess' => $userProgramAccess]);
        }

    }	

    public function GetReason(Request $req)
    {
    	  $data = DB::connection($this->common)
                    ->select("SELECT id,dropdown_reason FROM
                        dropdown_fifo_reason
                     ");

         return $data;
    }	

    public function AddnewReason(Request $req)
    {


    	 $data = DB::connection($this->common)
    	 			->table('dropdown_fifo_reason')
    	 			->insert([
    	 			'dropdown_reason' => $req->dropdown_reason,	
    	 			]);

    	 return json_decode($data );
    }

    public function UpdateReason(Request $req)
    {

    	 $data = DB::connection($this->common)
    	 			->table('dropdown_fifo_reason')
    	 			->where('id',$req->id)
    	 			->update([
    	 			'dropdown_reason' => $req->dropdown_reason,	
    	 			]);


    	return json_decode($data);
    }

    public function DeleteReason(Request $req)
    {
  
    	 foreach ($req->ids as $key => $id) {
              $data = DB::connection($this->common)
             ->table('dropdown_fifo_reason')
             ->where('id',$id)
             ->delete();
           
        }  
        return response()->json($data);
    }

    public function Checkreason(Request $req)
    {
    	$dropdown_reason = $req->dropdown_reason;

    	$data = DB::connection($this->common)
    				->table('dropdown_fifo_reason')
    				->where('dropdown_reason',$dropdown_reason)
    				->get();
    				
    	return response()->json($data);
    }
}
