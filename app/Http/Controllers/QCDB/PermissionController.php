<?php

namespace App\Http\Controllers\QCDB;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Carbon\Carbon;
use Config;
use DB;

class PermissionController extends Controller
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

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_QCPERM')
									, $userProgramAccess))
		{
			return redirect('/home');
		}
		else
		{
			return view('qcdb.qc_permission',['userProgramAccess' => $userProgramAccess]);
		}
	}

	public function user_list()
	{
		$data = [
			'lists' => '',
			'status' => 'failed',
			'msg' => 'No records found.'
		];

		$list;

		try {
			$list = DB::connection($this->common)
						->table('users')
						->select('id','lastname','firstname','middlename','user_id')
						->where('productline','CN')
						->get();

			if (is_null($list)) {
				$data = [
					'lists' => '',
					'status' => 'failed',
					'msg' => 'No records found.'
				];
			} else {
				$data = [
					'lists' => $list,
					'status' => 'success',
					'msg' => 'List were successfully retreived.'
				];
			}
		} catch (Exception $e) {
			$data = [
				'lists' => '',
				'status' => 'error',
				'msg' => $e->message()
			];

			return response()->json($data);
		}

		return response()->json($data);
	}

	public function program_list()
	{
		$data = [
			'lists' => '',
			'status' => 'failed',
			'msg' => 'No records found.'
		];

		$list;

		try {
			$list = DB::connection($this->common)
						->table('mprograms')
						->select('id','program_code','program_name')
						->where('program_class','QCDB')
						->get();

			if (is_null($list)) {
				$data = [
					'lists' => '',
					'status' => 'failed',
					'msg' => 'No records found.'
				];
			} else {
				$data = [
					'lists' => $list,
					'status' => 'success',
					'msg' => 'List were successfully retreived.'
				];
			}
		} catch (Exception $e) {
			$data = [
				'lists' => '',
				'status' => 'error',
				'msg' => $e->message()
			];

			return response()->json($data);
		}

		return response()->json($data);
	}

	public function permission_list()
	{
		$data = [
			'lists' => '',
			'status' => 'failed',
			'msg' => 'No records found.'
		];

		$list;

		try {
			$list = DB::connection($this->mysql)
						->table('qc_permissions')
						->select('id','program_code')
						->get();

			if (is_null($list)) {
				$data = [
					'lists' => '',
					'status' => 'failed',
					'msg' => 'No records found.'
				];
			} else {
				$data = [
					'lists' => $list,
					'status' => 'success',
					'msg' => 'List were successfully retreived.'
				];
			}
		} catch (Exception $e) {
			$data = [
				'lists' => '',
				'status' => 'error',
				'msg' => $e->message()
			];

			return response()->json($data);
		}

		return response()->json($data);
	}

	public function savedata(Request $req)
	{
		$data = [
			'lists' => '',
			'status' => 'failed',
			'msg' => 'Saving Failed.'
		];
		try {
			$query = DB::connection($this->mysql)->table('qc_permissions')
						->insert([
							'user_id' => $req->txtid,
							'program_code' => $req->txtprogram,
							'create' => $req->chkCreate,
							'update' => $req->chkUpdate,
							'delete' => $req->chkDelete,
							'create_user' => Auth::user()->user_id,
							'created_at' => Carbon::now(),
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);

			$id = DB::connection($this->mysql)->table('qc_permissions')->insertGetId($query);

			if (empty($id)) {
				$data = [
					'lists' => DB::connection($this->mysql)->table('qc_permissions')->get(),
					'status' => 'failed',
					'msg' => 'Saving Failed.'
				];
			} else {
				$data = [
					'lists' => DB::connection($this->mysql)->table('qc_permissions')->get(),
					'status' => 'success',
					'msg' => 'Data was successfully saved.'
				];
			}
		} catch (Exception $e) {
			return dd($e);
		}

		

		return response()->json($data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
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
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
