<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Carbon\Carbon;
use App\User; #User Model for DB
use App\mProgram;
use App\mUserprogram;
use App\mProductline;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use Illuminate\Http\Request;
use Excel;
use PDF;
use DB;
use Config;

class UserController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->initUsers();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->initCreateUsers();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        #initiate other variables
        $pg_code = "2001";
        $pg_name = "User Master";

        #instantiate User model
        $user = new User();
        $user->user_id = $req->user_id;
        $user->lastname = $req->lname;
        $user->firstname = $req->fname;
        $user->middlename = $req->mname;
        $user->password = bcrypt($req->pword);
        $user->productline = $req->productline;
        $user->actual_password = $req->pword;
        $user->locked = $req->locked;
        $user->Authorization = $req->Authorization;
        $user->create_pg = $pg_code;
        $user->create_user = Auth::user()->user_id;

        if ($user->save()) {
            # read only
            if (!empty($req->rw)) {
                foreach ($req->rw as $key => $pcodeRW) {
                    $pname = DB::connection($this->common)->table('mprograms')->where('program_code',$pcodeRW)->select('program_name')->first();
                    $userprog = new mUserprogram();
                    $userprog->program_code = $pcodeRW;
                    $userprog->user_id = $req->user_id;
                    $userprog->id_tblusers = $user->id;
                    $userprog->program_name = $pname->program_name;
                    $userprog->read_write = 1;
                    $userprog->create_pg = $pg_code;
                    $userprog->create_user = Auth::user()->user_id;
                    $userprog->save();
                }
            }
            
            # read only
            if (!empty($req->r)) {
                foreach ($req->r as $key => $pcodeR) {
                    $pname = DB::connection($this->common)->table('mprograms')->where('program_code',$pcodeR)->select('program_name')->first();
                    $userprog = new mUserprogram();
                    $userprog->program_code = $pcodeR;
                    $userprog->user_id = $req->user_id;
                    $userprog->id_tblusers = $user->id;
                    $userprog->program_name = $pname->program_name;
                    $userprog->read_write = 2;
                    $userprog->create_pg = $pg_code;
                    $userprog->create_user = Auth::user()->user_id;

                    $userprog->save();
                }
            }
            $msg['msg'] = "New User was successfully added";
            return $msg;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->initEditUsers($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $id)
    {
        #initiate other variables
        $pg_code = "2001";
        $pg_name = "User Master";

        #instantiate query
        $user = User::find($id);
        $user->user_id = $req->user_id;
        $user->lastname = $req->lname;
        $user->firstname = $req->fname;
        $user->middlename = $req->mname;
        $user->password = bcrypt($req->pword);
        $user->productline = $req->productline;
        $user->actual_password = $req->pword;
        $user->locked = $req->locked;
        $user->Authorization = $req->Authorization;
        $user->create_pg = $pg_code;
        $user->create_user = Auth::user()->user_id;
        $user->update_user = Auth::user()->user_id;

        if ($user->save()) {
            if ($this->countAccess($id) > 0) {
                DB::connection($this->common)->table('muserprograms')->where('id_tblusers',$id)->delete();
            }
            # read write
            if (!empty($req->rw)) {
                foreach ($req->rw as $key => $pcodeRW) {
                    $pname = DB::connection($this->common)->table('mprograms')->where('program_code',$pcodeRW)->select('program_name')->first();
                    $userprog = new mUserprogram();
                    $userprog->program_code = $pcodeRW;
                    $userprog->user_id = $req->user_id;
                    $userprog->id_tblusers = $id;
                    $userprog->program_name = $pname->program_name;
                    $userprog->read_write = 1;
                    $userprog->create_pg = $pg_code;
                    $userprog->create_user = Auth::user()->user_id;
                    $user->update_user = Auth::user()->user_id;
                    $userprog->save();
                }
            }
            
            # read only
            if (!empty($req->r)) {
                foreach ($req->r as $key => $pcodeR) {
                    $pname = DB::connection($this->common)->table('mprograms')->where('program_code',$pcodeR)->select('program_name')->first();
                    $userprog = new mUserprogram();
                    $userprog->program_code = $pcodeR;
                    $userprog->user_id = $req->user_id;
                    $userprog->id_tblusers = $user->id;
                    $userprog->program_name = $pname->program_name;
                    $userprog->read_write = 2;
                    $userprog->create_pg = $pg_code;
                    $userprog->create_user = Auth::user()->user_id;
                    $user->update_user = Auth::user()->user_id;
                    $userprog->save();
                }
            }
            $msg['msg'] = "This User was successfully updated";
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($id == Auth::user()->id) {
            $msg['msg'] = "You cannot delete yourself.";
            $msg['status'] = "error";
            return $msg;
        } else {
            User::where('id', $id)->delete();
            mUserprogram::where('id_tblusers', $id)->delete();

            $msg['msg'] = "You successfully deleted a user.";
            $msg['status'] = "success";
            return $msg;
        }
    }

    private function initUsers()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_USERS'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $users = User::where('delete_flag',0)->where('productline',Auth::user()->productline)
                        ->orderBy('created_at','desc')->get();
            $masters = mProgram::where('program_class','Master Management')->orderBy('id','asc')->get();
            $operations = mProgram::where('program_class','Operational Management')->orderBy('id','asc')->get();
            $sssprog = mProgram::where('program_class','SSS')->orderBy('id','asc')->get();
            $security = mProgram::where('program_class','Security Management')->orderBy('id','asc')->get();
            $wbsprog = mProgram::where('program_class','WBS')->orderBy('id','asc')->get();
            $qcdbprog = mProgram::where('program_class','QCDB')->orderBy('id','asc')->get();
            $qcmldprog = mProgram::where('program_class','QCMLD')->orderBy('id','asc')->get();
            $ypics = mProgram::where('program_class','YPICS')->orderBy('id','asc')->get();

            return view('master.user', [
                'users' => $users,
                'masters' => $masters,
                'operations' => $operations,
                'sssprog' => $sssprog,
                'wbsprog' => $wbsprog,
                'qcdbprog' => $qcdbprog,
                'qcmldprog' => $qcmldprog,
                'userProgramAccess' => $userProgramAccess,
                'security' => $security,
                'ypics' => $ypics
               ]);
        }
    }

    private function initCreateUsers()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_USERS'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $users = User::where('delete_flag',0)
                        ->orderBy('created_at','desc')->get();
            $masters = mProgram::where('program_class','Master Management')->orderBy('id','asc')->get();
            $operations = mProgram::where('program_class','Operational Management')->orderBy('id','asc')->get();
            $security = mProgram::where('program_class','Security Management')->orderBy('id','asc')->get();
            $sssprog = mProgram::where('program_class','SSS')->orderBy('id','asc')->get();
            $wbsprog = mProgram::where('program_class','WBS')->orderBy('id','asc')->get();
            $qcdbprog = mProgram::where('program_class','QCDB')->orderBy('id','asc')->get();
            $qcmldprog = mProgram::where('program_class','QCMLD')->orderBy('id','asc')->get();
            $ypics = mProgram::where('program_class','YPICS')->orderBy('id','asc')->get();

            return view('master.createuser', [
                'users' => $users,
                'masters' => $masters,
                'operations' => $operations,
                'sssprog' => $sssprog,
                'wbsprog' => $wbsprog,
                'qcdbprog' => $qcdbprog,
                'qcmldprog' => $qcmldprog,
                'userProgramAccess' => $userProgramAccess,
                'security' => $security,
                'ypics' => $ypics
               ]);
        }
    }

    private function initEditUsers($id)
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_USERS'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $userdetails = DB::connection($this->common)->table('users')->where('id',$id)->first();
            $masters = mProgram::where('program_class','Master Management')->orderBy('id','asc')->get();
            $operations = mProgram::where('program_class','Operational Management')->orderBy('id','asc')->get();
            $security = mProgram::where('program_class','Security Management')->orderBy('id','asc')->get();
            $sssprog = mProgram::where('program_class','SSS')->orderBy('id','asc')->get();
            $wbsprog = mProgram::where('program_class','WBS')->orderBy('id','asc')->get();
            $qcdbprog = mProgram::where('program_class','QCDB')->orderBy('id','asc')->get();
            $qcmldprog = mProgram::where('program_class','QCMLD')->orderBy('id','asc')->get();
            $ypics = mProgram::where('program_class','YPICS')->orderBy('id','asc')->get();
            $userprogs = DB::connection($this->common)->table('muserprograms')->where('id_tblusers',$id)->get();

            $progs = [];
            foreach ($userprogs as $key => $userprog) {
                $progs[$userprog->program_code] = $userprog->read_write;
            }

            return view('master.edituser', [
                'masters' => $masters,
                'operations' => $operations,
                'sssprog' => $sssprog,
                'wbsprog' => $wbsprog,
                'qcdbprog' => $qcdbprog,
                'qcmldprog' => $qcmldprog,
                'userProgramAccess' => $userProgramAccess,
                'security' => $security,
                'userdetails' => $userdetails,
                'progs' => $progs,
                'ypics' => $ypics,
               ]);
        }
    }

    private function countAccess($id)
    {
        return DB::connection($this->common)->table('muserprograms')->where('id_tblusers',$id)->count();
    }
}
