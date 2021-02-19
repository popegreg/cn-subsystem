<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\User;
use Config;
use DB;

class ChangePasswordController extends Controller
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

    public function getChangePassword()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_SEC'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
        	$users = User::where('delete_flag','0')
        				->where('user_id', Auth::user()->user_id)
                        ->orderBy('created_at','desc')->get();

            return view('security.ChangePassword',['users' => $users,'userProgramAccess' => $userProgramAccess]);
        }
    }

    public function postChangePass(Request $request)
    {
        $userdata = User::where('id', Auth::user()->id)->first();

        // if ($request->NewPass == $userdata->actual_password) {
        // 	$message = "New password is the same as the old password.";
        //     return redirect(url('/changepassword'))->with(['err_message' => $message]);
        // }

        if (empty($request->NewPass) && empty($request->ConPass) && empty($request->OldPass)) {
            $user = User::where('id',Auth::user()->id)
                        ->update([
                                    'user_id' => $request->user_id,
                                    'update_pg' => '4001',
                                    'update_user' => Auth::user()->user_id
                                ]);
            DB::connection($this->common)->table('muserprograms')->where('id_tblusers',Auth::user()->id)->update(['user_id' => $request->user_id]);
            $message = "Successfully saved.";
            return redirect(url('/changepassword'))->with(['message' => $message]);
        } else {
            if ($request->NewPass != $request->ConPass) {
                $message = "New password and Confirm password did not match.";
                return redirect(url('/changepassword'))->with(['err_message' => $message]);
            }
            if ($request->OldPass != $userdata->actual_password) {
                $message = "The Old password that you input did not match from the database.";
                return redirect(url('/changepassword'))->with(['err_message' => $message]);
            }
            if ($request->NewPass != $userdata->actual_password) {
                if ($request->NewPass == $request->ConPass) {
                    $user = User::where('id',Auth::user()->id)
                                ->update([
                                            'user_id' => $request->user_id,
                                            'password' => bcrypt($request->NewPass),
                                            'actual_password' => $request->NewPass,
                                            'update_pg' => '4001',
                                            'update_user' => Auth::user()->user_id
                                        ]);
                    $message = "Successfully saved.";
                    return redirect(url('/changepassword'))->with(['message' => $message]);
                } else {
                    $message = "New password and Confirm password did not match.";
                    return redirect(url('/changepassword'))->with(['err_message' => $message]);
                }
            }
        }
    }
}
