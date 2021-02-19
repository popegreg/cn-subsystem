<?php
/*******************************************************************************
     Copyright (c) Company Nam All rights reserved.

     FILE NAME: WBSEmailSettingsController.php
     MODULE NAME:  3021 : WBS - Email Notification Settings
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2017.03.29
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2017.03.29    AK.DELAROSA      Initial Draft
*******************************************************************************/

namespace App\Http\Controllers\WBS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Response;
use Config;
use DB;
use Illuminate\Support\Facades\Auth; #Auth facade
use Carbon\Carbon;
use PDF;
use Datatables;

class WBSEmailSettingsController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;
    protected $com;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $this->com->userDBcon(Auth::user()->productline,'wbs');
            $this->mssql = $this->com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $this->com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getEmailSettings(Request $req)
    {
        $pgcode = Config::get('constants.MODULE_CODE_EMAIL');
        
        if(!$this->com->getAccessRights($pgcode, $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
        	# Render WBS Page.
            return view('wbs.emailsettings',[
                        'userProgramAccess' => $userProgramAccess,
                        'pgcode' => $pgcode,
                        'pgaccess' => $this->com->getPgAccess($pgcode)
                    ]);
        }
    }

    public function LoadData(Request $req)
    {
        $data = [
            'msg' => "No data was loaded, please create new data.",
            'return_status' => 'failed'
        ];

        $count = DB::connection($this->mysql)
                ->table('tbl_wbs_emailsettings')
                ->count();

        if ($count > 0) {
            $details = DB::connection($this->mysql)
                ->table('tbl_wbs_emailsettings')
                ->get();

            if ($details) {
                $data = [
                    'msg' => "Successfully Loaded",
                    'return_status' => 'success',
                    'details' => $details
                ];
            }
        } else {
            $data = [
                    'msg' => "No data was loaded, please create new data.",
                    'return_status' => 'nodata'
                ];
        }
        
        return $data;
    }

    private function checkDb($email)
    {
        $count = DB::connection($this->mysql)
                    ->table('tbl_wbs_emailsettings')
                    ->where('sendto',$email)
                    ->count();
        return $count;
    }

    public function saveEmailSettings(Request $req)
    {
        $data = [
            'msg' => "Saving Failed.",
            'return_status' => 'failed'
        ];

        $saved = false;

        foreach ($req->sendto as $key => $sendto) {
            if (!empty($sendto) || !empty($req->sendto_name[$key])) {
                if ($this->checkDb($sendto) < 1) {
                    $saved = DB::connection($this->mysql)
                                ->table('tbl_wbs_emailsettings')
                                ->insert([
                                    'sendto' => $sendto,
                                    'sendto_name' => $req->sendto_name[$key]
                                ]);
                    $saved = true;
                } else {
                    $saved = DB::connection($this->mysql)
                                ->table('tbl_wbs_emailsettings')
                                ->where('id',$req->id[$key])
                                ->update([
                                    'sendto' => $sendto,
                                    'sendto_name' => $req->sendto_name[$key]
                                ]);
                    $saved = true;
                }
            }
        }

        if ($saved) {
            $data = [
                'msg' => "Successfully saved.",
                'return_status' => 'success'
            ];
        }

        return $data;
    }

    public function deleteEmail(Request $req)
    {
        $data = [
            'msg' => "Deleting Failed.",
            'return_status' => 'failed'
        ];

        $deleted = DB::connection($this->mysql)
                        ->table('tbl_wbs_emailsettings')
                        ->where('id',$req->id)
                        ->delete();
        if ($deleted) {
            $data = [
                'msg' => "Recipient was successfully deleted.",
                'return_status' => 'success'
            ];
        }

        return $data;
    }
}
