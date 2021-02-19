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

class PackingListSettingController extends Controller
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

    public function getPackListSetting(Request $request)
    {
        $id = $request->id;
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PLSET'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {

            $tableData = DB::connection($this->common)->table('tbl_packinglist_setting')->get();
            return view('security.packinglistsettings',['userProgramAccess' => $userProgramAccess, 'tableData' => $tableData]);
        }
    }

    public function postSave(Request $req)
    {
        switch ($req->ctrl) {
            case 'add':
                $inserted = DB::connection($this->common)->table('tbl_packinglist_setting')->insert([
                                'assign' => $req->assign,
                                'user' => $req->user,
                                'prodline' => $req->prodline
                            ]);
                if ($inserted) {
                    $msg['msg'] = "You have successfully added new details";
                    return $msg;
                }
                break;

            case 'edit':
                $updated = DB::connection($this->common)->table('tbl_packinglist_setting')->where('id',$req->id)
                            ->update([
                                'assign' => $req->assign,
                                'user' => $req->user,
                                'prodline' => $req->prodline
                            ]);
                if ($updated) {
                    $msg['msg'] = "You have successfully updated a detail";
                    return $msg;
                }
                break;
            
            default:
                $msg['msg'] = "Something went wrong while proccessing";
                return $msg;
                break;
        }
        
    }

    public function postDelete(Request $req)
    {
        foreach ($req->id as $key => $id) {
            $deleted = DB::connection($this->common)->table('tbl_packinglist_setting')->where('id',$id)->delete();
        }

        if ($deleted) {
            $msg['msg'] = "You have successfully deleted some details";
            return $msg;
        } else {
            $msg['msg'] = "Something went wrong while proccessing";
            return $msg;
        }
    }

   
}
