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

class DropdownController extends Controller
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

    public function getDropdown(Request $request_data)
    {
    	$common = new CommonController;

        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_DESTI'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $selected_category = $request_data['option'];
            if(empty($selected_category))
            {
                $selected_category = '1';
            }

            $category = DB::connection($this->mysql)
                        ->table('tbl_mdropdown_category')->orderBy('category')->get();
            $dropdownlist = DB::connection($this->mysql)
                        ->table('tbl_mdropdowns')->where('category', '=', $selected_category)->get();

            return view('master.Dropdown', 
               ['userProgramAccess' => $userProgramAccess,
               'category' => $category,
               'selected_category' => $selected_category,
               'dropdownlist' => $dropdownlist]);
        }

    }

    public function postAddDropdown(Request $request_data)
    {
        try
        {
            $table = "";
            $master = 'tbl_mdropdowns';
            $id = $request_data['itemid'];
            $description = $request_data['description'];
            $category = $request_data['masterid'];
            $action = $request_data['action'];
            $msg ='';
            $result = 0;

            $msg_type ='message';


            $dataexist = DB::connection($this->mysql)->table($master)->where('description', $description)->where('category', $category)->get();

            if($dataexist)
            {
                $msg = "Data Already Exist.";
                $msg_type ='err_message';
            }
            else
            {
                if($action == 'ADD')
                {
                    $result = DB::connection($this->mysql)->table($master)
                    ->insert([
                        'description' => $description,
                        'category' => $category
                        ]);
                }
                else if($action == 'EDIT')
                {
                    $result = DB::connection($this->mysql)->table($master)
                    ->where('id', '=', $id)
                    ->update([
                        'description' => $description
                        ]);
                }

                if ($result)
                {
                    $msg = "Successfully saved.";
                }
                else
                {
                   $msg = "Saving Failed.";
                   $msg_type ='err_message';
               }
           }

           $selected_category = $category;
       }
       catch (Exception $e)
       {
        Log::error($e->getMessage());
        echo 'Message: ' .$e->getMessage();
    }

    return redirect('/dropdown?option=' . $category)->with([$msg_type=>$msg, 'selected_category' => $selected_category]);
}

public function postDeleteDropdown(Request $request)
{
    $category = $request['master'];
    $master = 'tbl_mdropdowns';
    $tray = $request['tray'];
    $traycount = $request['traycount'];

    try
    {
        if($traycount > 0)
        {
            $result = DB::connection($this->mysql)->table($master)->where('category', '=', $category)->wherein('id',$tray)->delete();

            if ($result) {
                $msg = "Successfully deleted selected records.";
                // return redirect('/dropdown?option=' . $category)->with(['message'=>$msg]);
            } else {
                $msg = "Deleting selected records Failed.";
               // return redirect('/dropdown?option=' .  $category)->with(['err_message'=>$msg]);
           }
       }
   }
   catch (Exception $e)
   {
        Log::error($e->getMessage());
        echo 'Message: ' .$e->getMessage();
    }

    return $msg;
}

public function postAddDropdownCategory(Request $request_data)
{
    try
    {
            $master = 'tbl_mdropdown_category';
            $category = $request_data['category'];
            $action = $request_data['action'];
            $masterid = $request_data['masterid'];

            $msg ='';
            $result = 0;
            $id = 0;

            $msg_type ='message';


            $dataexist = DB::connection($this->mysql)->table($master)->where('category', $category)->get();

            if($dataexist)
            {
                $msg = "Data Already Exist.";
                $msg_type ='err_message';
            }
            else
            {

                if($action == 'ADD')
                {
                    $result = DB::connection($this->mysql)->table($master)
                        ->insert([
                            'category' => $category
                            ]);
                    $id = DB::getPdo()->lastInsertId();
                }
                else if($action == 'EDIT')
                {
                    $result = DB::connection($this->mysql)->table($master)
                        ->where('id', '=', $masterid)
                        ->update([
                            'category' => $category
                            ]);
                    $id = $masterid;
                }

                if ($result)
                {
                    $msg = "Successfully saved.";
                }
                else
                {
                   $msg = "Saving Failed.";
                   $msg_type ='err_message';
               }
           }

           $selected_category = $id;
    }
   catch (Exception $e)
   {
        Log::error($e->getMessage());
        echo 'Message: ' .$e->getMessage();
    }

    return redirect('/dropdown?option=' . $selected_category)->with([$msg_type => $msg]);
}

public function postDelDropdownCategory(Request $request_data)
{
    $msg_type ='message';
    $msg ='';
    try
    {
        $catid = $request_data['catid'];

        $result = DB::connection($this->mysql)->table('tbl_mdropdowns')->where('category', '=', $catid)->delete();
        $result = DB::connection($this->mysql)->table('tbl_mdropdown_category')->where('id', '=', $catid)->delete();
                if ($result)
                {
                    $msg = "Successfully deleted.";
                }
                else
                {
                   $msg = "Delete Failed.";
                   $msg_type ='err_message';
               }
    }
   catch (Exception $e)
   {
        Log::error($e->getMessage());
        echo 'Message: ' .$e->getMessage();
    }

    return redirect('/dropdown')->with([$msg_type=>$msg]);
}

}
