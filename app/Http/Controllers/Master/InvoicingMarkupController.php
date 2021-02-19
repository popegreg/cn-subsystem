<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use DB;
use Carbon\Carbon;
use Datatables;

class InvoicingMarkupController extends Controller
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
            $this->mysql = $this->com->userDBcon(Auth::user()->productline,'mysql');
            $this->mssql = $this->com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $this->com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }
    public function index()
    {
        if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_MARKUP'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('master.invoicing_markup',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function store(Request $req)
    {
        $data = [
            'msg' => 'Saving failed.',
            'status' => 'failed'
        ];

        $rules = [
            'prod_line' => 'required',
            'mark_up' => 'required|numeric',
        ];

        $this->validate($req,$rules);

        if (empty($req->id)) {
            $markup = DB::connection($this->common)->table('invoicing_markup')
                        ->insertGetId([
                            'prod_line' => $req->prod_line,
                            'mark_up' => $req->mark_up,
                            'multiplier' => $this->toDecimal($req->mark_up),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'create_user' => Auth::user()->user_id,
                            'update_user' => Auth::user()->user_id
                        ]);
        } else {
            $markup = DB::connection($this->common)->table('invoicing_markup')
                        ->where('id',$req->id)
                        ->update([
                            'prod_line' => $req->prod_line,
                            'mark_up' => $req->mark_up,
                            'multiplier' => $this->toDecimal($req->mark_up),
                            'updated_at' => Carbon::now(),
                            'update_user' => Auth::user()->user_id
                        ]);
        }

        

        if ($markup) {
            $data = [
                'msg' => 'Successfully saved.',
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function show()
    {
        $markup = DB::connection($this->common)->table('invoicing_markup')
                    ->select([
                        'id',
                        'prod_line',
                        'mark_up',
                        'update_user',
                        DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at")
                    ]);
        return Datatables::of($markup)
                        ->addColumn('action', function($data) {
                            return '<button class="btn btn-sm btn-primary edit_markup" data-id="'.$data->id.'" data-prod_line="'.$data->prod_line.'" data-mark_up="'.$data->mark_up.'">'.
                                        '<i class="fa fa-edit"></i>'.
                                    '</button>'.
                                    '<button class="btn btn-sm btn-danger delete_markup" data-id="'.$data->id.'">'.
                                        '<i class="fa fa-trash"></i>'.
                                    '</button>';
                        })
                        ->make(true);
    }

    public function edit($id)
    {
        $markup = DB::connection($this->common)->table('invoicing_markup')
                    ->where('id',$id)
                    ->get();
        return response()->json($markup);
    }

    public function update(Request $req, $id)
    {
        $data = [
            'msg' => 'Saving failed.',
            'status' => 'failed'
        ];

        $rules = [
            'prod_line' => 'required',
            'mark_up' => 'required',
        ];

        $this->validate($req,$rules);

        $markup = DB::connection($this->common)->table('invoicing_markup')
                    ->where('id',$id)
                    ->update([
                        'prod_line' => $req->prodline,
                        'mark_up' => $req->mark_up,
                        'updated_at' => Carbon::now(),
                        'update_user' => Auth::user()->user_id
                    ]);

        if ($markup) {
            $data = [
                'msg' => 'Successfully saved.',
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function destroy(Request $req)
    {
        $data = [
            'msg' => 'Deleting failed.',
            'status' => 'failed'
        ];

        $markup = DB::connection($this->common)->table('invoicing_markup')
                    ->where('id',$req->id)
                    ->delete();

        if ($markup) {
            $data = [
                'msg' => 'Successfully deleted.',
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function toDecimal($percent)
    {
        $percent = str_replace('%', '', $percent);
        return $percent / 100;
    }
}
