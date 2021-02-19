<?php
namespace App\Http\Controllers\QCDB;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Config;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth; #Auth facade
use Dompdf\Dompdf;
use Carbon\Carbon;
use App\IQCInspection;
use Illuminate\Http\Request;
use App\Http\Requests;
use Excel;
use Event;
use PDF;
use App\Events\UpdateIQCInspection;

class IQCInspectionController extends Controller
{
	protected $mysql;
	protected $mssql;
	protected $common;
	protected $com;
	protected $wbs;

	public function __construct()
	{
		$this->middleware('auth');
		$this->com = new CommonController;

		if (Auth::user() != null) {
			$this->mysql = $this->com->userDBcon(Auth::user()->productline,'mysql');
			$this->wbs = $this->com->userDBcon(Auth::user()->productline,'wbs');
			$this->mssql = $this->com->userDBcon(Auth::user()->productline,'mssql');
			$this->common = $this->com->userDBcon(Auth::user()->productline,'common');
		} else {
			return redirect('/');
		}
	}

	public function getIQCInspection(Request $request)
	{
		if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_IQCINS'), $userProgramAccess))
		{
			return redirect('/home');
		}
		else
		{

			return view('qcdb.iqcinspection',[
						'userProgramAccess' => $userProgramAccess]);
		}
	}

	public function getInvoiceItems(Request $req)
	{
		$db = DB::connection($this->wbs)->table('tbl_wbs_inventory')
				->select('item as id','item as text')
				->where('not_for_iqc',0)
				->where('invoice_no',$req->invoiceno)
				// ->where('judgement', null)
				// ->orWhere('judgement','')
				// ->orWhere('judgement','On-going')
				->distinct()
				->get();
		if ($this->checkIfExistObject($db) > 0) {
			return $db;
			//$obj = json_decode(json_encode($array));
		} else {
			return null;
		}
	}

	private function formatDate($date, $format)
	{
		if(empty($date))
		{
			return null;
		}
		else
		{
			return date($format,strtotime($date));
		}
	}

	private function insertToInspection($req)
	{
		$lots = [];
		$inv_id = [];
		$mr_id = [];

		$lot_qty = 0;

		if ($req->is_batching > 0) {
			$lots = explode(',',$req->lot_no);
			$inv_id = (is_null($req->inv_id) || empty($req->inv_id))? [] : explode(',',$req->inv_id);
			$mr_id = (is_null($req->mr_id) || empty($req->mr_id))? [] : explode(',',$req->mr_id);

			foreach ($lots as $key => $lot) {
				$lot_qty = $lot_qty + $this->getLotQty($req->invoice_no,$req->partcode,$lot);

				$status = 0;
				$kitting = 0;

				if ($req->judgement == 'Accepted') {
					$status = 1;
					$kitting = 1;
				} 

				if ($req->judgement == 'Rejected') {
					$status = 2;
					$kitting = 0;
				}

				$table = 'tbl_wbs_local_receiving_batch';

				$checker = -1;

				if (is_null($inv_id) || empty($inv_id)) {
					$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
								->where('not_for_iqc',0)
								->where('invoice_no',$req->invoice_no)
								->where('item',$req->partcode)
								->where('lot_no',$lot)
								->count();
				} else {
					$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
								->where('id',$mr_id[$key])
								->count();
				}

				
				if ($checker > 0) {
					$table = 'tbl_wbs_material_receiving_batch';
				}

				if (is_null($inv_id) || empty($inv_id)) {
					DB::connection($this->wbs)->table($table)
						->where('not_for_iqc',0)
						->where('invoice_no',$req->invoice_no)
						->where('item',$req->partcode)
						->where('lot_no',$lot)
						->update([
							'iqc_status' => $status,
							'for_kitting' => $kitting,
							'iqc_result' => $req->remarks,
							'judgement' => $req->judgement,
							'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
							'ins_time' => $req->time_ins_to,
							'ins_by' => $req->inspector,
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);
					DB::connection($this->wbs)->table('tbl_wbs_inventory')
						->where('not_for_iqc',0)
						->where('invoice_no',$req->invoice_no)
						->where('item',$req->partcode)
						->where('lot_no',$lot)
						->update([
							'iqc_status' => $status,
							'for_kitting' => $kitting,
							'iqc_result' => $req->remarks,
							'judgement' => $req->judgement,
							'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
							'ins_time' => $req->time_ins_to,
							'ins_by' => $req->inspector,
							'update_pg' => 'IQC Inspection Insert Batch',
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);
				} else {
					DB::connection($this->wbs)->table($table)
						->where('id',$mr_id[$key])
						->update([
							'iqc_status' => $status,
							'for_kitting' => $kitting,
							'iqc_result' => $req->remarks,
							'judgement' => $req->judgement,
							'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
							'ins_time' => $req->time_ins_to,
							'ins_by' => $req->inspector,
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);
					DB::connection($this->wbs)->table('tbl_wbs_inventory')
						->where('id',$inv_id[$key])
						->update([
							'iqc_status' => $status,
							'for_kitting' => $kitting,
							'iqc_result' => $req->remarks,
							'judgement' => $req->judgement,
							'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
							'ins_time' => $req->time_ins_to,
							'ins_by' => $req->inspector,
							'update_pg' => 'IQC Inspection Insert Batch',
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);
				}

				DB::connection($this->mysql)->table('iqc_inspections')
					->insert([
						'invoice_no' => $req->invoice_no,
						'partcode' => $req->partcode,
						'partname' => $req->partname,
						'supplier' => $req->supplier,
						'app_date' => $req->app_date,
						'app_time' => $req->app_time,
						'app_no' => $req->app_no,
						'lot_no' => $lot,
						'lot_qty' => $lot_qty,
						'type_of_inspection' => $req->type_of_inspection,
						'severity_of_inspection' => $req->severity_of_inspection,
						'inspection_lvl' => $req->inspection_lvl,
						'aql' => $req->aql,
						'accept' => $req->accept,
						'reject' => $req->reject,
						'date_ispected' => $req->date_inspected,
						'ww' => $req->ww,
						'fy' => $req->fy,
						'shift' => $req->shift,
						'time_ins_from' => $req->time_ins_from,
						'time_ins_to' => $req->time_ins_to,
						'inspector' => $req->inspector,
						'submission' => $req->submission,
						'judgement' => $req->judgement,
						'lot_inspected' => $req->lot_inspected,
						'lot_accepted' => $req->lot_accepted,
						'sample_size' => $req->sample_size,
						'no_of_defects' => $req->no_of_defects,
						'remarks' => $req->remarks,
						'classification' => $req->classification,
						'family' => $req->family,
						'dbcon' => Auth::user()->productline,
						'updated_at' => Carbon::now(),
						'inv_id' => $inv_id[$key],
						'mr_id' => $mr_id[$key]
					]);
			}
		} else {
			$lot_qty = $this->getLotQty($req->invoice_no,$req->partcode,$req->lot_no);
			$status = 0;
			$kitting = 0;

			if ($req->judgement == 'Accepted') {
				$status = 1;
				$kitting = 1;
			} 

			if ($req->judgement == 'Rejected') {
				$status = 2;
				$kitting = 0;
			}

			$table = 'tbl_wbs_local_receiving_batch';

			$checker = -1;

			if (is_null($inv_id) || empty($inv_id)) {
				$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
							->where('not_for_iqc',0)
							->where('invoice_no',$req->invoice_no)
							->where('item',$req->partcode)
							->where('lot_no',$req->lot_no)
							->count();
			} else {
				$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
							->where('id',$req->mr_id)
							->count();
			}

			if ($checker > 0) {
				$table = 'tbl_wbs_material_receiving_batch';
			}

			if (is_null($req->inv_id) || empty($req->inv_id)) {
				DB::connection($this->wbs)->table($table)
					->where('not_for_iqc',0)
					->where('invoice_no',$req->invoice_no)
					->where('item',$req->partcode)
					->where('lot_no',$req->lot_no)
					->update([
						'iqc_status' => $status,
						'for_kitting' => $kitting,
						'iqc_result' => $req->remarks,
						'judgement' => $req->judgement,
						'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
						'ins_time' => $req->time_ins_to,
						'ins_by' => $req->inspector,
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
					]);
				DB::connection($this->wbs)->table('tbl_wbs_inventory')
					->where('not_for_iqc',0)
					->where('invoice_no',$req->invoice_no)
					->where('item',$req->partcode)
					->where('lot_no',$req->lot_no)
					->update([
						'iqc_status' => $status,
						'for_kitting' => $kitting,
						'iqc_result' => $req->remarks,
						'judgement' => $req->judgement,
						'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
						'ins_time' => $req->time_ins_to,
						'ins_by' => $req->inspector,
						'update_pg' => 'IQC Inspection Insert',
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
					]);
			} else {
				DB::connection($this->wbs)->table($table)
					->where('id',$req->mr_id)
					->update([
						'iqc_status' => $status,
						'for_kitting' => $kitting,
						'iqc_result' => $req->remarks,
						'judgement' => $req->judgement,
						'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
						'ins_time' => $req->time_ins_to,
						'ins_by' => $req->inspector,
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
					]);
				DB::connection($this->wbs)->table('tbl_wbs_inventory')
					->where('id',$req->inv_id)
					->update([
						'iqc_status' => $status,
						'for_kitting' => $kitting,
						'iqc_result' => $req->remarks,
						'judgement' => $req->judgement,
						'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
						'ins_time' => $req->time_ins_to,
						'ins_by' => $req->inspector,
						'update_pg' => 'IQC Inspection Insert',
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
					]);
			}

			DB::connection($this->mysql)->table('iqc_inspections')
				->insert([
					'invoice_no' => $req->invoice_no,
					'partcode' => $req->partcode,
					'partname' => $req->partname,
					'supplier' => $req->supplier,
					'app_date' => $req->app_date,
					'app_time' => $req->app_time,
					'app_no' => $req->app_no,
					'lot_no' => $req->lot_no,
					'lot_qty' => $lot_qty,
					'type_of_inspection' => $req->type_of_inspection,
					'severity_of_inspection' => $req->severity_of_inspection,
					'inspection_lvl' => $req->inspection_lvl,
					'aql' => $req->aql,
					'accept' => $req->accept,
					'reject' => $req->reject,
					'date_ispected' => $req->date_inspected,
					'ww' => $req->ww,
					'fy' => $req->fy,
					'shift' => $req->shift,
					'time_ins_from' => $req->time_ins_from,
					'time_ins_to' => $req->time_ins_to,
					'inspector' => $req->inspector,
					'submission' => $req->submission,
					'judgement' => $req->judgement,
					'lot_inspected' => $req->lot_inspected,
					'lot_accepted' => $req->lot_accepted,
					'sample_size' => $req->sample_size,
					'no_of_defects' => $req->no_of_defects,
					'remarks' => $req->remarks,
					'classification' => $req->classification,
					'family' => $req->family,
					'dbcon' => Auth::user()->productline,
					'updated_at' => Carbon::now(),
					'inv_id' => $req->inv_id,
					'mr_id' => $req->mr_id
				]);
		}            
	}

	private function insertToInspectionBatching($req,$lots)
	{
		$lot_qty = 0;
		$array_lots = explode(',',$lots);

		foreach ($array_lots as $key => $lot) {
			$lot_qty = $lot_qty + $this->getLotQty($req->invoice_no,$req->partcode,$lot);

			$status = 0;
			$kitting = 0;

			if ($req->judgement == 'Accepted') {
				$status = 1;
				$kitting = 1;
			} 

			if ($req->judgement == 'Rejected') {
				$status = 2;
				$kitting = 0;
			}

			$table = 'tbl_wbs_local_receiving_batch';

			$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
							->where('not_for_iqc',0)
							->where('invoice_no',$req->invoice_no)
							->where('item',$req->partcode)
							->where('lot_no',$lot)
							->count();
			if ($checker > 0) {
				$table = 'tbl_wbs_material_receiving_batch';
			}

			DB::connection($this->wbs)->table($table)
				->where('not_for_iqc',0)
				->where('invoice_no',$req->invoice_no)
				->where('item',$req->partcode)
				->where('lot_no',$lot)
				->update([
					'iqc_status' => $status,
					'for_kitting' => $kitting,
					'iqc_result' => $req->remarks,
					'judgement' => $req->judgement,
					'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
					'ins_time' => $req->time_ins_to,
					'ins_by' => $req->inspector,
					'updated_at' => Carbon::now(),
				]);
			DB::connection($this->wbs)->table('tbl_wbs_inventory')
				->where('not_for_iqc',0)
				->where('invoice_no',$req->invoice_no)
				->where('item',$req->partcode)
				->where('lot_no',$lot)
				->update([
					'iqc_status' => $status,
					'for_kitting' => $kitting,
					'iqc_result' => $req->remarks,
					'judgement' => $req->judgement,
					'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
					'ins_time' => $req->time_ins_to,
					'ins_by' => $req->inspector,
					'updated_at' => Carbon::now(),
				]);
		}

		DB::connection($this->mysql)->table('iqc_inspections')
			->insert([
				'invoice_no' => $req->invoice_no,
				'partcode' => $req->partcode,
				'partname' => $req->partname,
				'supplier' => $req->supplier,
				'app_date' => $req->app_date,
				'app_time' => $req->app_time,
				'app_no' => $req->app_no,
				'lot_no' => $lots,
				'lot_qty' => $lot_qty,
				'type_of_inspection' => $req->type_of_inspection,
				'severity_of_inspection' => $req->severity_of_inspection,
				'inspection_lvl' => $req->inspection_lvl,
				'aql' => $req->aql,
				'accept' => $req->accept,
				'reject' => $req->reject,
				'date_ispected' => $req->date_inspected,
				'ww' => $req->ww,
				'fy' => $req->fy,
				'shift' => $req->shift,
				'time_ins_from' => $req->time_ins_from,
				'time_ins_to' => $req->time_ins_to,
				'inspector' => $req->inspector,
				'submission' => $req->submission,
				'judgement' => $req->judgement,
				'lot_inspected' => $req->lot_inspected,
				'lot_accepted' => $req->lot_accepted,
				'sample_size' => $req->sample_size,
				'no_of_defects' => $req->no_of_defects,
				'remarks' => $req->remarks,
				'classification' => $req->classification,
				'family' => $req->family,
				'dbcon' => Auth::user()->productline,
				'updated_at' => Carbon::now(),
			]);
	}

	public function saveInspection(Request $req)
	{
		$data = [
			'return_status' => 'failed',
			'msg' => "Saving Failed."
		];
		$query = false;

		if ($req->save_status == 'ADD') {
			// if ($req->is_batching > 0) {
			//     $this->insertToInspectionBatching($req,$req->lot_no);
			//     //$this->insertHistory($req->lot_no,$req);
			// } else {
			//     $this->insertToInspection($req,$req->lot_no);
			//     //$this->insertHistory($req->lot_no,$req);
			// }
			$this->insertToInspection($req,$req->lot_no);

			$query = true;

		} else {
			//$lots = $req->lot_no;

			//if ($req->is_batching > 0) {
				//$lots = explode(',',$req->lot_no);
				//$this->updateInspection($req,$lots);
				//$this->insertHistory($lots,$req);
			//} //else {
				//$this->updateInspection($req,$req->lot_no);
				//$this->insertHistory($req->lot_no,$req);
			//}

			$this->updateInspection($req);

			// if ($this->checkInspection($req) > 0) {
			//     if ($req->batching > 0) {
			//         $this->insertToInspectionBatching($req,$req->lot_no);
			//         $this->insertHistory($req->lot_no,$req);
			//     } else {
			//         $this->insertToInspection($req,$req->lot_no);
			//         $this->insertHistory($req->lot_no,$req);
			//     }
			// }else {
				
			// }
			
			$query = true;
		}

		if ($query) {
			Event::fire(new UpdateIQCInspection($this->wbs));
			$data = [
				'return_status' => 'success',
				'msg' => "Successfully Saved."
			];
		}
		return $data;
	}

	public function specialAccept(Request $req)
	{
		$judgement = $req->judgement;
		$lots = $req->lot_no;
		$wbs_inventory;

		$query = false;
		
		$lot_qty = 0;
		$array_lots = explode(',',$lots);

		$data = [
			'return_status' => 'failed',
			'msg' => "Item Already Accepted."
		];

		$check_duplicate = DB::connection($this->mysql)->table('iqc_inspections')
							->where('lot_no', $lots)
							->where('invoice_no', $req->invoice_no)
							->where('judgement', 'Special Accept')
							->count();
		if ($check_duplicate <= 0) {
				
			foreach ($array_lots as $key => $lot) {
				$lot_qty = $lot_qty + $this->getLotQty($req->invoice_no,$req->partcodelbl,$lot);

				$status = 4;
				$kitting = 1;

				// DB::connection($this->wbs)->table('tbl_wbs_inventory')
				//     ->where('invoice_no',$req->invoice_no)
				//     ->where('item',$req->partcodelbl)
				//     ->where('lot_no',$lot)
				//     ->update([
				//         'iqc_status' => $status,
				//         'for_kitting' => $kitting,
				//         'iqc_result' => $req->remarks,
				//         'judgement' => 'Special Accept',
				//         'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
				//         'ins_time' => $req->time_ins_to,
				//         'ins_by' => $req->inspector,
				//         'update_user' => Auth::user()->user_id,
				//         'updated_at' => Carbon::now(),
				//     ]);
				$wbs_inventory = DB::connection($this->wbs)->table('tbl_wbs_inventory')
					->where('invoice_no',$req->invoice_no)
					->where('item',$req->partcodelbl)
					->where('lot_no',$lot)
					->first();

				DB::connection($this->wbs)->table('tbl_wbs_inventory')
					->insert([
						'app_by' => $wbs_inventory->app_by,
						'app_date' => $wbs_inventory->app_date,
						'app_time'  => $wbs_inventory->app_time,
						'box' => $wbs_inventory->box,
						'box_qty' => $wbs_inventory->box_qty,
						'create_pg' => $wbs_inventory->create_pg,
						'create_user' => $wbs_inventory->create_user,
						'created_at' => $wbs_inventory->created_at,
						'deleted' => $wbs_inventory->deleted,
						'drawing_num' => $wbs_inventory->drawing_num,
						'for_kitting' => $kitting,
						'ins_by' => $wbs_inventory->ins_by,
						'ins_date' => $wbs_inventory->ins_date,
						'ins_time' => $wbs_inventory->ins_time,
						'invoice_no' => $wbs_inventory->invoice_no,
						'iqc_result' => $wbs_inventory->iqc_result,
						'iqc_status' => $status,
						'is_printed' => $wbs_inventory->is_printed,
						'item' => $wbs_inventory->item,
						'item_desc' => $wbs_inventory->item_desc,
						'judgement' => "Special Accept",
						'loc_batch_id' => $wbs_inventory->loc_batch_id,
						'location' => $wbs_inventory->location,
						'lot_no' => $wbs_inventory->lot_no,
						'mat_batch_id' => $wbs_inventory->mat_batch_id,
						'not_for_iqc' => $wbs_inventory->not_for_iqc,
						'plating_date' => $wbs_inventory->plating_date,
						'pressed_date' => $wbs_inventory->pressed_date,
						'qty' => $wbs_inventory->qty,
						'received_date' => $wbs_inventory->received_date,
						'supplier' => $wbs_inventory->supplier,
						'update_pg' => $wbs_inventory->update_pg,
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
						'wbs_mr_id' => $wbs_inventory->wbs_mr_id
					]);
			}
			
			DB::connection($this->mysql)->table('iqc_inspections')
			->insert([
				'invoice_no' => $req->invoice_no,
				'partcode' => $req->partcodelbl,
				'partname' => $req->partname,
				'supplier' => $req->supplier,
				'app_date' => $req->app_date,
				'app_time' => $req->app_time,
				'app_no' => $req->app_no,
				'lot_no' => $lots,
				'lot_qty' => $lot_qty,
				'type_of_inspection' => $req->type_of_inspection,
				'severity_of_inspection' => $req->severity_of_inspection,
				'inspection_lvl' => $req->inspection_lvl,
				'aql' => $req->aql,
				'accept' => $req->accept,
				'reject' => $req->reject,
				'date_ispected' => $req->date_inspected,
				'ww' => $req->ww,
				'fy' => $req->fy,
				'shift' => $req->shift,
				'time_ins_from' => $req->time_ins_from,
				'time_ins_to' => $req->time_ins_to,
				'inspector' => $req->inspector,
				'submission' => $req->submission,
				'judgement' => 'Special Accept',
				'lot_inspected' => $req->lot_inspected,
				'lot_accepted' => $req->lot_accepted,
				'sample_size' => $req->sample_size,
				'no_of_defects' => $req->no_of_defects,
				'remarks' => $req->remarks,
				'classification' => $req->classification,
				'dbcon' => Auth::user()->productline,
				'updated_at' => Carbon::now(),
			]);
			$query = true;
		}else {
			$query = false;
			$data = [
				'return_status' => 'failed',
				'msg' => "Item Already Accepted."
			];
		}
		if ($query) {
			Event::fire(new UpdateIQCInspection($this->wbs));
			$data = [
				'return_status' => 'success',
				'msg' => "Special Acceptance Success."
			];
		}

		return $data;
	}

	private function updateInspection($req)
	{
		$lots = [];
		$inv_id = [];
		$mr_id = [];

		//return dd($req->mr_id);

		if ($req->is_batching > 0) {
			$lots = explode(',',$req->lot_no);
			$inv_id = (is_null($req->inv_id) || empty($req->inv_id))? [] : explode(',',$req->inv_id);
			$mr_id = (is_null($req->mr_id) || empty($req->mr_id))? [] : explode(',',$req->mr_id);

			foreach ($lots as $key => $lot) {
				$lot_qty = $this->getLotQty($req->invoice_no,$req->partcode,$lot);
				$status = 0;
				$kitting = 0;

				if ($req->judgement == 'Accepted') {
					$status = 1;
					$kitting = 1;
				} 

				if ($req->judgement == 'Rejected') {
					$status = 2;
					$kitting = 0;
				}

				$table = 'tbl_wbs_local_receiving_batch';

				$checker = -1;

				if (is_null($inv_id) || empty($inv_id)) {
					$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
								->where('not_for_iqc',0)
								->where('invoice_no',$req->invoice_no)
								->where('item',$req->partcode)
								->where('lot_no',$lot)
								->count();
				} else {
					$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
								->where('id',$mr_id[$key])
								->count();
				}

				if ($checker > 0) {
					$table = 'tbl_wbs_material_receiving_batch';
				}

				if (is_null($inv_id) || empty($inv_id)) {
					DB::connection($this->wbs)->table($table)
						->where('not_for_iqc',0)
						->where('invoice_no',$req->invoice_no)
						->where('item',$req->partcode)
						->where('lot_no',$lot)
						->update([
							'iqc_status' => $status,
							'for_kitting' => $kitting,
							'iqc_result' => $req->remarks,
							'judgement' => $req->judgement,
							'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
							'ins_time' => $req->time_ins_to,
							'ins_by' => $req->inspector,
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);
					DB::connection($this->wbs)->table('tbl_wbs_inventory')
						->where('not_for_iqc',0)
						->where('invoice_no',$req->invoice_no)
						->where('item',$req->partcode)
						->where('lot_no',$lot)
						->update([
							'iqc_status' => $status,
							'for_kitting' => $kitting,
							'iqc_result' => $req->remarks,
							'judgement' => $req->judgement,
							'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
							'ins_time' => $req->time_ins_to,
							'ins_by' => $req->inspector,
							'update_pg' => 'IQC Inspection Batch from On-going',
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);
				} else {
					DB::connection($this->wbs)->table($table)
						->where('id',$mr_id[$key])
						->update([
							'iqc_status' => $status,
							'for_kitting' => $kitting,
							'iqc_result' => $req->remarks,
							'judgement' => $req->judgement,
							'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
							'ins_time' => $req->time_ins_to,
							'ins_by' => $req->inspector,
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);
					DB::connection($this->wbs)->table('tbl_wbs_inventory')
						->where('id',$inv_id[$key])
						->update([
							'iqc_status' => $status,
							'for_kitting' => $kitting,
							'iqc_result' => $req->remarks,
							'judgement' => $req->judgement,
							'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
							'ins_time' => $req->time_ins_to,
							'ins_by' => $req->inspector,
							'update_pg' => 'IQC Inspection Batch from On-going',
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now(),
						]);
				}
			}
		} else {
			$lot_qty = $this->getLotQty($req->invoice_no,$req->partcode,$req->lot_no);
			$status = 0;
			$kitting = 0;

			if ($req->judgement == 'Accepted') {
				$status = 1;
				$kitting = 1;
			} 

			if ($req->judgement == 'Rejected') {
				$status = 2;
				$kitting = 0;
			}

			$table = 'tbl_wbs_local_receiving_batch';

			$checker = -1;

			if (is_null($inv_id) || empty($inv_id)) {
				$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
							->where('not_for_iqc',0)
							->where('invoice_no',$req->invoice_no)
							->where('item',$req->partcode)
							->where('lot_no',$req->lot_no)
							->count();
			} else {
				$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
							->where('id',$req->mr_id)
							->count();
			}

			if ($checker > 0) {
				$table = 'tbl_wbs_material_receiving_batch';
			}

			if (is_null($req->inv_id) || empty($req->inv_id)) {
				DB::connection($this->wbs)->table($table)
					->where('not_for_iqc',0)
					->where('invoice_no',$req->invoice_no)
					->where('item',$req->partcode)
					->where('lot_no',$req->lot_no)
					->update([
						'iqc_status' => $status,
						'for_kitting' => $kitting,
						'iqc_result' => $req->remarks,
						'judgement' => $req->judgement,
						'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
						'ins_time' => $req->time_ins_to,
						'ins_by' => $req->inspector,
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
					]);
				DB::connection($this->wbs)->table('tbl_wbs_inventory')
					->where('not_for_iqc',0)
					->where('invoice_no',$req->invoice_no)
					->where('item',$req->partcode)
					->where('lot_no',$req->lot_no)
					->update([
						'iqc_status' => $status,
						'for_kitting' => $kitting,
						'iqc_result' => $req->remarks,
						'judgement' => $req->judgement,
						'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
						'ins_time' => $req->time_ins_to,
						'ins_by' => $req->inspector,
						'update_pg' => 'IQC Inspection from On-going',
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
					]);
			} else {
				DB::connection($this->wbs)->table($table)
					->where('id',$req->mr_id)
					->update([
						'iqc_status' => $status,
						'for_kitting' => $kitting,
						'iqc_result' => $req->remarks,
						'judgement' => $req->judgement,
						'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
						'ins_time' => $req->time_ins_to,
						'ins_by' => $req->inspector,
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
					]);
				DB::connection($this->wbs)->table('tbl_wbs_inventory')
					->where('id',$req->inv_id)
					->update([
						'iqc_status' => $status,
						'for_kitting' => $kitting,
						'iqc_result' => $req->remarks,
						'judgement' => $req->judgement,
						'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
						'ins_time' => $req->time_ins_to,
						'ins_by' => $req->inspector,
						'update_pg' => 'IQC Inspection from On-going',
						'update_user' => Auth::user()->user_id,
						'updated_at' => Carbon::now(),
					]);
			}
		}

		DB::connection($this->mysql)->table('iqc_inspections')
			->where('id',$req->id)
			->update([
				'partcode' => $req->partcode,
				'partname' => $req->partname,
				'supplier' => $req->supplier,
				'app_date' => $req->app_date,
				'app_time' => $req->app_time,
				'app_no' => $req->app_no,
				'lot_no' => $req->lot_no,
				'lot_qty' => $req->lot_qty,
				'type_of_inspection' => $req->type_of_inspection,
				'severity_of_inspection' => $req->severity_of_inspection,
				'inspection_lvl' => $req->inspection_lvl,
				'aql' => $req->aql,
				'accept' => $req->accept,
				'reject' => $req->reject,
				'date_ispected' => $req->date_inspected,
				'ww' => $req->ww,
				'fy' => $req->fy,
				'shift' => $req->shift,
				'time_ins_from' => $req->time_ins_from,
				'time_ins_to' => $req->time_ins_to,
				'inspector' => $req->inspector,
				'submission' => $req->submission,
				'judgement' => $req->judgement,
				'lot_inspected' => $req->lot_inspected,
				'lot_accepted' => $req->lot_accepted,
				'sample_size' => $req->sample_size,
				'no_of_defects' => $req->no_of_defects,
				'remarks' => $req->remarks,
				'classification' => $req->classification,
				'family' => $req->family,
				'dbcon' => Auth::user()->productline,
				'updated_at' => Carbon::now(),
				'inv_id' => $req->inv_id,
				'mr_id' => $req->mr_id
			]);
	}

	private function updateInspectionBatching($req,$lots)
	{
		$lot_qty = 0;
		foreach ($lots as $key => $lot) {
			$lot_qty = $lot_qty + $this->getLotQty($req->invoice_no,$req->partcode,$lot);

			$status = 0;
			$kitting = 0;

			if ($req->judgement == 'Accepted') {
				$status = 1;
				$kitting = 1;
			} 

			if ($req->judgement == 'Rejected') {
				$status = 2;
				$kitting = 0;
			}

			$table = 'tbl_wbs_local_receiving_batch';

			$checker = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
							->where('not_for_iqc',0)
							->where('invoice_no',$req->invoice_no)
							->where('item',$req->partcode)
							->where('lot_no',$lot)
							->count();

			if ($checker > 0) {
				$table = 'tbl_wbs_material_receiving_batch';
			}

			DB::connection($this->wbs)->table($table)
				->where('not_for_iqc',0)
				->where('invoice_no',$req->invoice_no)
				->where('item',$req->partcode)
				->where('lot_no',$lot)
				->update([
					'iqc_status' => $status,
					'for_kitting' => $kitting,
					'iqc_result' => $req->remarks,
					'judgement' => $req->judgement,
					'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
					'ins_time' => $req->time_ins_to,
					'ins_by' => $req->inspector,
					'updated_at' => Carbon::now(),
				]);
			DB::connection($this->wbs)->table('tbl_wbs_inventory')
				->where('not_for_iqc',0)
				->where('invoice_no',$req->invoice_no)
				->where('item',$req->partcode)
				->where('lot_no',$lot)
				->update([
					'iqc_status' => $status,
					'for_kitting' => $kitting,
					'iqc_result' => $req->remarks,
					'judgement' => $req->judgement,
					'ins_date' => $this->formatDate($req->date_inspected,'m/d/Y'),
					'ins_time' => $req->time_ins_to,
					'ins_by' => $req->inspector,
					'updated_at' => Carbon::now(),
				]);
		}

		DB::connection($this->mysql)->table('iqc_inspections')
			->where('id',$req->id)
			->update([
				'partcode' => $req->partcode,
				'partname' => $req->partname,
				'supplier' => $req->supplier,
				'app_date' => $req->app_date,
				'app_time' => $req->app_time,
				'app_no' => $req->app_no,
				'lot_no' => $req->lot_no,
				'lot_qty' => $req->lot_qty,
				'type_of_inspection' => $req->type_of_inspection,
				'severity_of_inspection' => $req->severity_of_inspection,
				'inspection_lvl' => $req->inspection_lvl,
				'aql' => $req->aql,
				'accept' => $req->accept,
				'reject' => $req->reject,
				'date_ispected' => $req->date_inspected,
				'ww' => $req->ww,
				'fy' => $req->fy,
				'shift' => $req->shift,
				'time_ins_from' => $req->time_ins_from,
				'time_ins_to' => $req->time_ins_to,
				'inspector' => $req->inspector,
				'submission' => $req->submission,
				'judgement' => $req->judgement,
				'lot_inspected' => $req->lot_inspected,
				'lot_accepted' => $req->lot_accepted,
				'sample_size' => $req->sample_size,
				'no_of_defects' => $req->no_of_defects,
				'remarks' => $req->remarks,
				'classification' => $req->classification,
				'family' => $req->family,
				'dbcon' => Auth::user()->productline,
				'updated_at' => Carbon::now(),
			]);
	}

	private function checkInspection($req)
	{
		$check = DB::connection($this->mysql)
					->table('iqc_inspections')
					->where('id',$req->id)
					->where('judgement','<>','On-going')
					->count();
		return $check;

	}

	private function insertHistory($lots,$req)
	{
		foreach ($lots as $key => $lot) {
			$lot_qty = $this->getLotQty($req->invoice,$req->partcode,$lot);

			DB::connection($this->mysql)->table('iqc_inspections_history')
				->insert([
					'invoice_no' => $req->invoice_no,
					'partcode' => $req->partcode,
					'partname' => $req->partname,
					'supplier' => $req->supplier,
					'app_date' => $req->app_date,
					'app_time' => $req->app_time,
					'app_no' => $req->app_no,
					'lot_no' => $lot,
					'lot_qty' => $lot_qty,
					'type_of_inspection' => $req->type_of_inspection,
					'severity_of_inspection' => $req->severity_of_inspection,
					'inspection_lvl' => $req->inspection_lvl,
					'aql' => $req->aql,
					'accept' => $req->accept,
					'reject' => $req->reject,
					'date_ispected' => $req->date_inspected,
					'ww' => $req->ww,
					'fy' => $req->fy,
					'shift' => $req->shift,
					'time_ins_from' => $req->time_ins_from,
					'time_ins_to' => $req->time_ins_to,
					'inspector' => $req->inspector,
					'submission' => $req->submission,
					'judgement' => $req->judgement,
					'lot_inspected' => $req->lot_inspected,
					'lot_accepted' => $req->lot_accepted,
					'sample_size' => $req->sample_size,
					'no_of_defects' => $req->no_of_defects,
					'remarks' => $req->remarks,
					'dbcon' => Auth::user()->productline,
					'created_at' => Carbon::now(),
				]);
		}
	}

	private function requalifyInventory($app_no,$partcode,$lot)
	{
		DB::connection($this->wbs)->table('tbl_wbs_inventory')
			->where('wbs_mr_id', $app_no)
			->where('item', $partcode)
			->where('lot_no', $lot)
			->update([
				'received_date' => date('Y-m-d')
			]);
	}

	public function getShift(Request $req)
	{
		$data = [];
		$from = $this->convertTime($req->from);
		$to = $this->convertTime($req->to);
		$shift = DB::connection($this->mysql)->table('iqc_shift')
					->whereRaw("'".$from."' between time_from and time_to")
					->select('shift')
					->first();
		if (count((array)$shift) > 0) {
			return $data = [
						'shift' => $shift->shift
						];
		}

		return $data = [
						'shift' => 'Shift B'
						];
	}

	private function convertTime($time)
	{
		return date('H:i:s',strtotime($time));
	}

	public function getInvoiceItemDetails(Request $req)
	{
		$db;
		$checker = DB::connection($this->wbs)->table('tbl_wbs_inventory')
				->where('invoice_no',$req->invoiceno)
				->where('item',$req->item)->count();
		if ($checker > 0) {
			$db = DB::connection($this->wbs)->table('tbl_wbs_inventory as b')
					->select('item_desc',
							'supplier',
							'app_time',
							'app_date',
							'wbs_mr_id',
							DB::raw("SUM(qty) as lot_qty"))
					->where('invoice_no',$req->invoiceno)
					->where('item',$req->item)
					->first();

			$lot = DB::connection($this->wbs)->table('tbl_wbs_inventory')
					->where('invoice_no',$req->invoiceno)
					->where('item',$req->item)
					// ->where('not_for_iqc',0)
					->select(
						'lot_no as id',
						'lot_no as text',
						'id as inv_id',
						DB::raw("if(mat_batch_id is not null, mat_batch_id, loc_batch_id) as mr_id"),
						'qty as qty'
					)
					->get();

			if ($this->checkIfExistObject($db) > 0 && $this->checkIfExistObject($lot) > 0) {
				return $data = [
					'lot' => $lot,
					'details' => $db
				];
			}
		} else {

			return $data = [
				'lot' => '',
				'details' => ''
			];
		}
	}

	public function calculateLotQty(Request $req)
	{
		$lot_qty = 0;
		$table = "tbl_wbs_local_receiving_batch";
		$join_id = "i.loc_batch_id";

		$lots = implode("', '", $req->lot_no);
		if (empty($req->lot_no)) {
			return $lot_qty;
		} else {
			$count = DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
						->where('invoice_no',$req->invoiceno)
						->where('item',$req->item)
						->count();

			if ($count > 0) {
				$table = "tbl_wbs_material_receiving_batch";
				$join_id = "i.mr_batch_id";
			}

			// $db = DB::connection($this->wbs)
			// 		->select("SELECT r.item, 
			// 						SUM(r.qty) as lot_qty, 
			// 						r.invoice_no
			// 				FROM pmi_wbs_cn.tbl_wbs_inventory as i
			// 				inner join pmi_wbs_cn.tbl_wbs_local_receiving_batch as r
			// 				on ".$join_id." = r.id
			// 				where r.wbs_loc_id = '".$table."'
			// 				and r.item = '".$req->item."'
			// 				and r.lot_no in ('".$lots."')
			// 				group by r.item, 
			// 						r.invoice_no");

			foreach ($req->lot_no as $key => $lot) {
				$db = DB::connection($this->wbs)->table($table)
						->select('qty as lot_qty')
						->where('item',$req->item)
						->where('invoice_no',$req->invoiceno)
						->where('lot_no',$lot)
						->first();
				if ($this->checkIfExistObject($db) > 0) {
					$lot_qty = $lot_qty + $db->lot_qty;
				}
			}

			// if ($this->checkIfExistObject($db) > 0) {
			// 	$lot_qty = $db[0]->lot_qty;
			// }
			return $lot_qty;
		}
	}

	private function getLotQty($invoice,$item,$lot)
	{
		$lot_qty = 0;
		$db = DB::connection($this->wbs)->table('tbl_wbs_inventory')
				->select('qty as lot_qty')
				->where('item',$item)
				->where('invoice_no',$invoice)
				->where('lot_no',$lot)
				->first();
		if ($this->checkIfExistObject($db) > 0) {
			$lot_qty = $db->lot_qty;
		}

		return $lot_qty;
	}

	public function SamplingPlan(Request $req)
	{
		$size = 0;
		$accept = 0;
		$reject = 1;
		if ($req->soi == 'Normal') {
			if ($req->il == 'S2') {
				if ($req->aql == 0.65) {
					if ($req->lot_qty <= 20) {
						$size = $req->lot_qty;
					}

					if ($req->lot_qty > 20) {
						$size = 20;
					}
				}
			}

			if ($req->il == 'S3') {
				if ($req->aql == 0.40) {
					if ($req->lot_qty <= 32) {
						$size = $req->lot_qty;
					}

					if ($req->lot_qty > 32) {
						$size = 32;
					}
				}

				if ($req->aql == 1.00) {
					if ($req->lot_qty < 13) {
						$size = $req->lot_qty;
					}

					if ($req->lot_qty > 13) {
						$size = 13;
					}
				}

				if ($req->aql == 0.25) {
					if ($req->lot_qty < 50) {
						$size = $req->lot_qty;
					}

					if ($req->lot_qty > 50) {
						$size = 50;
					}
				}
			}

			if ($req->il == 'II') {
				# code...
			}

			if ($req->aql == 2.5) {
				$size = 2;
			}
		} else {
			if ($req->il == 'S2') {
				# code...
			}

			if ($req->il == 'S3') {
				if ($req->aql == 0.40) {
					if ($req->lot_qty <= 50) {
						$size = $req->lot_qty;
					}

					if ($req->lot_qty > 50) {
						$size = 50;
					}
				}

				if ($req->aql == 0.25) {
					if ($req->lot_qty < 80) {
						$size = $req->lot_qty;
					}

					if ($req->lot_qty > 80) {
						$size = 80;
					}
				}
			}

			if ($req->il == 'II') {
				# code...
			}

			if ($req->aql == 2.5) {
				$size = 2;
			}
		}

		return $data = [
			'sample_size' => $size,
			'accept' => $accept,
			'reject' => $reject,
			'date_inspected' => date('Y-m-d'),
			'inspector' =>Auth::user()->user_id
		];
	}

	public function getDropdowns()
	{
		$family = $this->com->getDropdownByNameSelect2('Family');
		$tofinspection = $this->com->getDropdownByNameSelect2('Type of Inspection');
		$sofinspection = $this->com->getDropdownByNameSelect2('Severity of Inspection');
		$inspectionlvl = $this->com->getDropdownByNameSelect2('Inspection Level');
		$aql = $this->com->getDropdownByNameSelect2('AQL');
		$shift = $this->com->getDropdownByNameSelect2('Shift');
		$submission = $this->com->getDropdownByNameSelect2('Submission');
		$shift = $this->com->getDropdownByNameSelect2('Shift');
		$mod = $this->com->getDropdownByNameSelect2('Mode of Defect - IQC Inspection');
		$supplier = $this->com->getDropdownByNameSelect2('Supplier');
		$classification = $this->com->getDropdownByNameSelect2('Category');

		return $data = [
					'family' => $family,
					'tofinspection' => $tofinspection,
					'sofinspection' => $sofinspection,
					'inspectionlvl' => $inspectionlvl,
					'aql' => $aql,
					'shift' => $shift,
					'submission' => $submission,
					'shift' => $shift,
					'mod' => $mod,
					'supplier' => $supplier,
					'classification' => $classification,
				];
	}

	private function checkIfExistObject($object)
	{
	   return count( (array)$object);
	}

	private function array_to_object($array)
	{
		return (object) $array;
	}

	public function getWorkWeek()
	{
		$yr = 52;
		$apr = date('Y')."-04-01";
		$aprweek = date("W", strtotime($apr));

		$diff = $yr - $aprweek;
		$date = Carbon::now();
		$weeknow = $date->format("W");

		$workweek = $diff + $weeknow + 1;

		if ($workweek > 52) {
			return $data = ['workweek' => $workweek - 52];
		} else {
			return $data = ['workweek' => $workweek];
		}
	}

	public function saveModeOfDefectsInspection(Request $req)
	{
		$data = [
			'return_status' => "failed",
			"msg" => "Mode of Defect saving failed."
		];

		$total_mod_count = $req->current_count + $req->qty;

		if ($total_mod_count > $req->sample_size) {
			$data = [
				'return_status' => "failed",
				"msg" => "Mode of Defect quantity is more than the Sample Size.",
				"count" => $total_mod_count
			];
		} else {
			if ($req->status == 'ADD') {
				$query = DB::connection($this->mysql)->table('tbl_mod_iqc_inspection')
							->insert([
								'invoice_no' => $req->invoiceno,
								'partcode' => $req->item,
								'mod' => $req->mod,
								'qty' => $req->qty,
								'lot_no' => $req->lot_no,
								'created_at' => Carbon::now(),
							]);
			} else {
				$query = DB::connection($this->mysql)->table('tbl_mod_iqc_inspection')
							->where('id',$req->id)
							->update([
								'mod' => $req->mod,
								'qty' => $req->qty,
								'updated_at' => Carbon::now(),
							]);
			}


			if ($query == true) {
				$data = [
					'return_status' => "success",
					"msg" => "Mode of Defect successfully saved.",
					"count" => $total_mod_count
				];
			}
		} 

		return $data;
	}

	public function deleteModeOfDefectsInspection(Request $req)
	{
		$data = [
			'return_status' => "failed",
			"msg" => "Mode of Defect deleting failed."
		];

		$query = false;

		foreach ($req->id as $key => $id) {
			$delete = DB::connection($this->mysql)->table('tbl_mod_iqc_inspection')
						->where('id',$id)
						->delete();
			if ($delete == true) {
				$query = true;
			}
		}


		if ($query == true) {
			$data = [
				'return_status' => "success",
				"msg" => "Mode of Defect successfully deleted."
			];
		}

		return $data;
	}

	public function getModeOfDefectsInspection(Request $req)
	{
		$db = DB::connection($this->mysql)->table('tbl_mod_iqc_inspection')
				->where('invoice_no',$req->invoiceno)
				->where('partcode',$req->item)
				->get();
		if ($this->checkIfExistObject($db) > 0) {
			return $db;
		}
	}

	public function getIQCData(Request $req)
	{
		$inspection =  DB::connection($this->mysql)->table('iqc_inspections')
							->where('judgement','<>','On-going')
							->orderBy('id','desc')
							->select(['id','invoice_no','partcode','partname','supplier',
									'app_date','app_time','app_no','lot_no','lot_qty',
									'type_of_inspection','severity_of_inspection',
									'inspection_lvl','aql','accept','reject','date_ispected',
									'ww','fy','time_ins_from','time_ins_to','shift',
									'inspector','submission','judgement','lot_inspected',
									'lot_accepted','sample_size','no_of_defects','remarks',
									'classification','family','inv_id','mr_id']);
								
		return Datatables::of($inspection)
						->editColumn('id', function ($data) {
							return $data->id;
						})
						->editColumn('time_ins_from', function ($data) {
							return $data->time_ins_from.' - '.$data->time_ins_to;
						})
						->addColumn('action', function ($data) {
							return '<a href="javascript:;" class="btn input-sm blue btn_editiqc" data-id="'.$data->id.'"
												data-invoice_no="'.$data->invoice_no.'"
												data-partcode="'.$data->partcode.'"
												data-partname="'.$data->partname.'"
												data-supplier="'.$data->supplier.'"
												data-app_date="'.$data->app_date.'"
												data-app_time="'.$data->app_time.'"
												data-app_no="'.$data->app_no.'"
												data-lot_no="'.$data->lot_no.'"
												data-lot_qty="'.$data->lot_qty.'"
												data-type_of_inspection="'.$data->type_of_inspection.'"
												data-severity_of_inspection="'.$data->severity_of_inspection.'"
												data-inspection_lvl="'.$data->inspection_lvl.'"
												data-aql="'.$data->aql.'"
												data-accept="'.$data->accept.'"
												data-reject="'.$data->reject.'"
												data-date_ispected="'.$data->date_ispected.'"
												data-ww="'.$data->ww.'"
												data-fy="'.$data->fy.'"
												data-time_ins_from="'.$data->time_ins_from.'"
												data-time_ins_to="'.$data->time_ins_to.'"
												data-shift="'.$data->shift.'"
												data-inspector="'.$data->inspector.'"
												data-submission="'.$data->submission.'"
												data-judgement="'.$data->judgement.'"
												data-lot_inspected="'.$data->lot_inspected.'"
												data-lot_accepted="'.$data->lot_accepted.'"
												data-sample_size="'.$data->sample_size.'"
												data-no_of_defects="'.$data->no_of_defects.'"
												data-remarks="'.$data->remarks.'"
												data-classification="'.$data->classification.'"
												data-family="'.$data->family.'"
												data-inv_id="'.$data->inv_id.'"
												data-mr_id="'.$data->mr_id.'">
												<i class="fa fa-edit"></i>
											</a>';
						})
						->make(true);
	}

	public function getOngoing(Request $req)
	{
		$onGoing =  DB::connection($this->mysql)->table('iqc_inspections')
					->where('judgement','On-going')
					->orderBy('id','desc')
					->select(['id','invoice_no','partcode','partname',
							'supplier','app_date','app_time','app_no',
							'lot_no','lot_qty','type_of_inspection',
							'severity_of_inspection','inspection_lvl',
							'aql','accept','reject','date_ispected','ww',
							'fy','time_ins_from','time_ins_to','shift',
							'inspector','submission','judgement',
							'lot_inspected','lot_accepted','sample_size',
							'no_of_defects','remarks','inv_id','mr_id']);

		return Datatables::of($onGoing)
						->editColumn('id', function ($data) {
							return $data->id;
						})
						->addColumn('action', function ($data) {
							$batching = 0;
							if ( count(explode(',',$data->lot_no)) > 1 ) {
								$batching = 1;
							}
							return '<a href="javascript:;" class="btn input-sm blue btn_editongiong" data-id="'.$data->id.'"
												data-invoice_no="'.$data->invoice_no.'"
												data-partcode="'.$data->partcode.'"
												data-partname="'.$data->partname.'"
												data-supplier="'.$data->supplier.'"
												data-app_date="'.$data->app_date.'"
												data-app_time="'.$data->app_time.'"
												data-app_no="'.$data->app_no.'"
												data-lot_no="'.$data->lot_no.'"
												data-lot_qty="'.$data->lot_qty.'"
												data-type_of_inspection="'.$data->type_of_inspection.'"
												data-severity_of_inspection="'.$data->severity_of_inspection.'"
												data-inspection_lvl="'.$data->inspection_lvl.'"
												data-aql="'.$data->aql.'"
												data-accept="'.$data->accept.'"
												data-reject="'.$data->reject.'"
												data-date_ispected="'.$data->date_ispected.'"
												data-ww="'.$data->ww.'"
												data-fy="'.$data->fy.'"
												data-time_ins_from="'.$data->time_ins_from.'"
												data-time_ins_to="'.$data->time_ins_to.'"
												data-shift="'.$data->shift.'"
												data-inspector="'.$data->inspector.'"
												data-submission="'.$data->submission.'"
												data-judgement="'.$data->judgement.'"
												data-lot_inspected="'.$data->lot_inspected.'"
												data-lot_accepted="'.$data->lot_accepted.'"
												data-sample_size="'.$data->sample_size.'"
												data-no_of_defects="'.$data->no_of_defects.'"
												data-remarks="'.$data->remarks.'"
												data-batching="'.$batching.'"
												data-inv_id="'.$data->inv_id.'"
												data-mr_id="'.$data->mr_id.'">
												<i class="fa fa-edit"></i>
											</a>';
						})
						->make(true);
	}

	public function deleteOnGoing(Request $req)
	{
		$data = [
			'return_status' => "failed",
			"msg" => "On-going data deleting failed."
		];

		$query = false;

		foreach ($req->id as $key => $id) {
			$iqc = DB::connection($this->mysql)->table('iqc_inspections')
						->where('id',$id)
						->first();

			if (count((array)$iqc) > 0) {
				$lot_nos = explode(',', $iqc->lot_no);

				foreach ($lot_nos as $key => $lot) {
					$checkInv = DB::connection($this->wbs)->update(
										"UPDATE tbl_wbs_inventory SET iqc_status='0',not_for_iqc='0'
										WHERE invoice_no='".$iqc->invoice_no."' AND item='".$iqc->partcode."' AND lot_no='".$lot."'"
									);
									
					if (strpos($iqc->app_no, 'loc') !== false) {
						$checkBatch = DB::connection($this->wbs)->update(
										"UPDATE tbl_wbs_local_receiving_batch SET iqc_status='0',not_for_iqc='0'
										WHERE invoice_no='".$iqc->invoice_no."' AND item='".$iqc->partcode."' AND lot_no='".$lot."'"
									);
					} else {
						$checkBatch = DB::connection($this->wbs)->update(
										"UPDATE tbl_wbs_material_receiving_batch SET iqc_status='0',not_for_iqc='0'
										WHERE invoice_no='".$iqc->invoice_no."' AND item='".$iqc->partcode."' AND lot_no='".$lot."'"
									);
					}
					
				}
				//if ($checkBatch == true) {
					$delete = DB::connection($this->mysql)->table('iqc_inspections')
								->where('id',$id)
								->delete();
					//$query = true;
				//}
				$query = true;
			}

		}
		


		if ($query == true) {
			$data = [
				'return_status' => "success",
				"msg" => "Inspection data successfully deleted."
			];
		}

		return $data;
	}

	public function deleteIQCInspection(Request $req)
	{
		$data = [
			'return_status' => "failed",
			"msg" => "Inspection data deleting failed."
		];

		$query = false;

		foreach ($req->id as $key => $id) {
			$iqc = DB::connection($this->mysql)->table('iqc_inspections')
						->where('id',$id)
						->first();

			if (count((array)$iqc) > 0) {
				$lot_nos = explode(',', $iqc->lot_no);

				foreach ($lot_nos as $key => $lot) {
					$checkInv = DB::connection($this->wbs)->update(
										"UPDATE tbl_wbs_inventory SET iqc_status='0',not_for_iqc='0'
										WHERE invoice_no='".$iqc->invoice_no."' AND item='".$iqc->partcode."' AND lot_no='".$lot."'"
									);
									
					if (strpos($iqc->app_no, 'loc') !== false) {
						$checkBatch = DB::connection($this->wbs)->update(
										"UPDATE tbl_wbs_local_receiving_batch SET iqc_status='0',not_for_iqc='0'
										WHERE invoice_no='".$iqc->invoice_no."' AND item='".$iqc->partcode."' AND lot_no='".$lot."'"
									);
					} else {
						$checkBatch = DB::connection($this->wbs)->update(
										"UPDATE tbl_wbs_material_receiving_batch SET iqc_status='0',not_for_iqc='0'
										WHERE invoice_no='".$iqc->invoice_no."' AND item='".$iqc->partcode."' AND lot_no='".$lot."'"
									);
					}
					
				}
				//if ($checkBatch == true) {
					$delete = DB::connection($this->mysql)->table('iqc_inspections')
								->where('id',$id)
								->delete();
					//$query = true;
				//}
				$query = true;
			}

		}


		if ($query == true) {
			$data = [
				'return_status' => "success",
				"msg" => "Inspection data successfully deleted."
			];
		}

		return $data;
	}

	public function getItemsSearch(Request $req)
	{
		$db = DB::connection($this->mysql)->table('iqc_inspections')
				->select('partcode as id','partcode as text')
				->distinct()
				->get();

		if ($this->checkIfExistObject($db) > 0) {
			return $db;
		}
	}

	public function searchInspection(Request $req)
	{
		$from_cond = '';
		$to_cond = '';
		$item_cond ='';

		if(empty($req->item))
		{
			$item_cond ='';
		} else {
			$item_cond = " AND partcode = '" . $req->item . "'";
		}

		if (!empty($req->from) && !empty($req->to)) {
			$from_cond = "AND date_ispected BETWEEN '" . $req->from . "' AND '" . $req->to . "'";
		} else {
			$from_cond = '';
			$to_cond = '';
		}

		$inspection =  DB::connection($this->mysql)->table('iqc_inspections')
							->whereRaw(" judgement <> 'On-going' ".$item_cond.$from_cond.$to_cond)
							->orderBy('id','desc')
							->select(['id','invoice_no','partcode','partname','supplier',
									'app_date','app_time','app_no','lot_no','lot_qty',
									'type_of_inspection','severity_of_inspection',
									'inspection_lvl','aql','accept','reject','date_ispected',
									'ww','fy','time_ins_from','time_ins_to','shift',
									'inspector','submission','judgement','lot_inspected',
									'lot_accepted','sample_size','no_of_defects','remarks',
									'classification','family','inv_id','mr_id']);
								
		return Datatables::of($inspection)
						->editColumn('id', function ($data) {
							return $data->id;
						})
						->editColumn('time_ins_from', function ($data) {
							return $data->time_ins_from.' - '.$data->time_ins_to;
						})
						->addColumn('action', function ($data) {
							return '<a href="javascript:;" class="btn input-sm blue btn_editiqc" data-id="'.$data->id.'"
												data-invoice_no="'.$data->invoice_no.'"
												data-partcode="'.$data->partcode.'"
												data-partname="'.$data->partname.'"
												data-supplier="'.$data->supplier.'"
												data-app_date="'.$data->app_date.'"
												data-app_time="'.$data->app_time.'"
												data-app_no="'.$data->app_no.'"
												data-lot_no="'.$data->lot_no.'"
												data-lot_qty="'.$data->lot_qty.'"
												data-type_of_inspection="'.$data->type_of_inspection.'"
												data-severity_of_inspection="'.$data->severity_of_inspection.'"
												data-inspection_lvl="'.$data->inspection_lvl.'"
												data-aql="'.$data->aql.'"
												data-accept="'.$data->accept.'"
												data-reject="'.$data->reject.'"
												data-date_ispected="'.$data->date_ispected.'"
												data-ww="'.$data->ww.'"
												data-fy="'.$data->fy.'"
												data-time_ins_from="'.$data->time_ins_from.'"
												data-time_ins_to="'.$data->time_ins_to.'"
												data-shift="'.$data->shift.'"
												data-inspector="'.$data->inspector.'"
												data-submission="'.$data->submission.'"
												data-judgement="'.$data->judgement.'"
												data-lot_inspected="'.$data->lot_inspected.'"
												data-lot_accepted="'.$data->lot_accepted.'"
												data-sample_size="'.$data->sample_size.'"
												data-no_of_defects="'.$data->no_of_defects.'"
												data-remarks="'.$data->remarks.'"
												data-classification="'.$data->classification.'"
												data-family="'.$data->family.'"
												data-inv_id="'.$data->inv_id.'"
												data-mr_id="'.$data->mr_id.'">
												<i class="fa fa-edit"></i>
											</a>';
						})
						->make(true);


		// $data = DB::connection($this->mysql)->table('iqc_inspections')
		// 			->whereRaw(" 1=1 ".$item_cond.$from_cond.$to_cond)
		// 			->get();
		// return $data;
	}

	public function searchHistory(Request $req)
	{
		$from_cond = '';
		$to_cond = '';
		$item_cond ='';
		$lot_cond = '';
		$judge_cond = '';

		if(empty($req->item))
		{
			$item_cond ='';
		} else {
			$item_cond = " AND partcode = '" . $req->item . "'";
		}

		if (!empty($req->from) && !empty($req->to)) {
			$from_cond = "AND date_ispected BETWEEN '" . $req->from . "' AND '" . $req->to . "'";
		} else {
			$from_cond = '';
			$to_cond = '';
		}

		if(empty($req->lotno))
		{
			$lot_cond ='';
		} else {
			$lot_cond = " AND lot_no = '" . $req->lotno . "'";
		}

		if(empty($req->judgement))
		{
			$judge_cond ='';
		} else {
			$judge_cond = " AND judgement = '" . $req->judgement . "'";
		}

		$data = DB::connection($this->mysql)->table('iqc_inspections_history')
					->whereRaw(" 1=1 ".$item_cond.$lot_cond.$judge_cond.$from_cond.$to_cond)
					->get();
		return $data;
	}

	//REQUALIFICATION
	public function getItemsRequalification()
	{
		$db = DB::connection($this->mysql)->table('iqc_inspections')
				->select('partcode as id','partcode as text')
				->where('judgement','Accepted')
				->distinct()
				->get();
		if ($this->checkIfExistObject($db) > 0) {
			return $db;
		}
	}

	public function getAppNoRequalification(Request $req)
	{
		$db = DB::connection($this->mysql)->table('iqc_inspections')
				->select('app_no as id','app_no as text')
				->where('judgement','Accepted')
				->where('partcode',$req->item)
				->distinct()
				->get();

		if ($this->checkIfExistObject($db) > 0) {
			return $db;
		}
	}

	public function getDetailsRequalification(Request $req)
	{
		$db = DB::connection($this->mysql)->table('iqc_inspections')
				->where('judgement','Accepted')
				->where('partcode',$req->item)
				->where('app_no',$req->app_no)
				->select('partname','supplier','app_date','app_time','lot_qty')
				->distinct()
				->first();

		$lots = DB::connection($this->mysql)->table('iqc_inspections')
				->where('judgement','Accepted')
				->where('partcode',$req->item)
				->where('app_no',$req->app_no)
				->select('lot_no')
				->get();

		if ($this->checkIfExistObject($db) > 0 || $this->checkIfExistObject($lots) > 0) {
			$arr = [];
			foreach ($lots as $key => $lot) {
				$arr = explode(',',$lot->lot_no);
			}
			$lotnos = [];
			$lotval = [];
			foreach ($arr as $key => $x) {
				$object = json_decode(json_encode(['id'=>$x,'text'=>$x]), FALSE);
				array_push($lotnos,$object);
				array_push($lotval,$x);
			}
			return $data = [
				'details' => $db,
				'lots' => $lotnos,
				'lotval' => $lotval
			];
		}
	}

	public function calculateLotQtyRequalification(Request $req)
	{
		$lot_qty = 0;
		if (empty($req->lot_no)) {
			return $lot_qty;
		} else {
			foreach ($req->lot_no as $key => $lot) {
				$db = DB::connection($this->wbs)->table('tbl_wbs_inventory')
						->select('qty as lot_qty')
						->where('item',$req->item)
						->where('wbs_mr_id',$req->app_no)
						->where('lot_no',$lot)
						->first();
				if ($this->checkIfExistObject($db) > 0) {
					$lot_qty = $lot_qty + $db->lot_qty;
				}
			}
			return $lot_qty;
		}
	}

	public function visualInspectionRequalification(Request $req)
	{
		$db = DB::connection($this->mysql)->table('iqc_inspections')
				->where('judgement','Accepted')
				->where('partcode',$req->item)
				->where('app_no',$req->app_no)
				->get();

		if ($this->checkIfExistObject($db) > 0) {
			return $db;
		}
	}

	public function saveRequalification(Request $req)
	{
		$data = [
			'return_status' => 'failed',
			'msg' => "Saving Failed."
		];
		$query = false;
		$status = 0;
		$kitting = 0;

		if ($req->judgement == 'Accepted') {
			$status = 1;
			$kitting = 1;
		} else {
			$status = 2;
			$kitting = 0;
		}

		if ($req->save_status == 'ADD') {
			DB::connection($this->mysql)->table('iqc_inspections_rq')
				->insert([
					'ctrl_no_rq' => $req->ctrlno,
					'partcode_rq' => $req->partcode,
					'partname_rq' => $req->partname,
					'supplier_rq' => $req->supplier,
					'app_date_rq' => $req->app_date,
					'app_time_rq' => $req->app_time,
					'app_no_rq' => $req->app_no,
					'lot_no_rq' =>$req->lot_no,
					'lot_qty_rq' => $req->lot_qty,
					'date_ispected_rq' => $req->date_inspected,
					'ww_rq' => $req->ww,
					'fy_rq' => $req->fy,
					'shift_rq' => $req->shift,
					'time_ins_from_rq' => $req->time_ins_from,
					'time_ins_to_rq' => $req->time_ins_to,
					'inspector_rq' => $req->inspector,
					'submission_rq' => $req->submission,
					'judgement_rq' => $req->judgement,
					'lot_inspected_rq' => $req->lot_inspected,
					'lot_accepted_rq' => $req->lot_accepted,
					'no_of_defects_rq' => $req->no_of_defects,
					'remarks_rq' => $req->remarks,
					'dbcon_rq' => Auth::user()->productline,
					'created_at' => Carbon::now(),
				]);
			if (is_string($req->lot_no)) {
				$lots = explode(',',$req->lot_no);
				$this->requalifyInventory($req->app_no,$req->partcode,$lots);
			} else {
				$this->requalifyInventory($req->app_no,$req->partcode,$req->lot_no);
			}

			$query = true;

		} else {
			DB::connection($this->mysql)->table('iqc_inspections_rq')
				->where('id',$req->id)
				->update([
					'partcode_rq' => $req->partcode,
					'partname_rq' => $req->partname,
					'supplier_rq' => $req->supplier,
					'app_date_rq' => $req->app_date,
					'app_time_rq' => $req->app_time,
					'app_no_rq' => $req->app_no,
					'lot_no_rq' => $req->lot_no,
					'lot_qty_rq' => $req->lot_qty,
					'date_ispected_rq' => $req->date_inspected,
					'ww_rq' => $req->ww,
					'fy_rq' => $req->fy,
					'shift_rq' => $req->shift,
					'time_ins_from_rq' => $req->time_ins_from,
					'time_ins_to_rq' => $req->time_ins_to,
					'inspector_rq' => $req->inspector,
					'submission_rq' => $req->submission,
					'judgement_rq' => $req->judgement,
					'lot_inspected_rq' => $req->lot_inspected,
					'lot_accepted_rq' => $req->lot_accepted,
					'no_of_defects_rq' => $req->no_of_defects,
					'remarks_rq' => $req->remarks,
					'dbcon_rq' => Auth::user()->productline,
					'updated_at' => Carbon::now(),
				]);

				if (is_string($req->lot_no)) {
					$lots = explode(',',$req->lot_no);
					$this->requalifyInventory($req->app_no,$req->partcode,$lots);
				} else {
					$this->requalifyInventory($req->app_no,$req->partcode,$req->lot_no);
				}

			$query = true;
		}

		if ($query) {
			$data = [
				'return_status' => 'success',
				'msg' => "Successfully Saved."
			];
		}

		return $data;
	}

	public function getRequaliData(Request $req)
	{
		return DB::connection($this->mysql)->table('iqc_inspections_rq')
					->take($req->row)
					->get();
	}

	public function deleteRequalification(Request $req)
	{
		$data = [
			'return_status' => "failed",
			"msg" => "Re-qualified data deleting failed."
		];

		$query = false;

		foreach ($req->id as $key => $id) {
			$delete = DB::connection($this->mysql)->table('iqc_inspections_rq')
						->where('id',$id)
						->delete();
			if ($delete == true) {
				$query = true;
			}
		}


		if ($query == true) {
			$data = [
				'return_status' => "success",
				"msg" => "Re-qualified data successfully deleted."
			];
		}

		return $data;
	}

	public function getmodeOfDefectsRequaliData(Request $req)
	{
		$db = DB::connection($this->mysql)->table('tbl_mod_iqc_rq')
				->where('partcode',$req->item)
				->get();
		if ($this->checkIfExistObject($db) > 0) {
			return $db;
		}
	}

	public function saveModRequalification(Request $req)
	{
		$data = [
			'return_status' => "failed",
			"msg" => "Mode of Defect saving failed."
		];

		if ($req->status == 'ADD') {
			$query = DB::connection($this->mysql)->table('tbl_mod_iqc_rq')
						->insert([
							'partcode' => $req->item,
							'mod' => $req->mod,
							'qty' => $req->qty,
							'created_at' => Carbon::now(),
						]);
		} else {
			$query = DB::connection($this->mysql)->table('tbl_mod_iqc_rq')
						->where('id',$req->id)
						->update([
							'mod' => $req->mod,
							'qty' => $req->qty,
							'updated_at' => Carbon::now(),
						]);
		}


		if ($query == true) {
			$data = [
				'return_status' => "success",
				"msg" => "Mode of Defect successfully saved."
			];
		}

		return $data;
	}

	public function deleteModRequalification(Request $req)
	{
		$data = [
			'return_status' => "failed",
			"msg" => "Mode of Defect deleting failed."
		];

		$query = false;

		foreach ($req->id as $key => $id) {
			$delete = DB::connection($this->mysql)->table('tbl_mod_iqc_rq')
						->where('id',$id)
						->delete();
			if ($delete == true) {
				$query = true;
			}
		}


		if ($query == true) {
			$data = [
				'return_status' => "success",
				"msg" => "Mode of Defect successfully deleted."
			];
		}

		return $data;
	}

	//GROUP BY
	public function getGroupbyContent(Request $req)
	{
		if (!empty($req->field)) {
			$db = DB::connection($this->mysql)->table('iqc_inspections')
					->select($req->field.' as id',$req->field.' as text')
					->distinct()
					->get();
			if ($this->checkIfExistObject($db) > 0) {
				return $db;
			}
		}
	}

	public function getGroupByTable(Request $req)
	{
		return $this->IQCDatatableQuery($req,false);
	}

	public function getInspectionByDate(Request $req)
	{
		$date_inspected = '';

		if (!empty($req->from) && !empty($req->to)) {
			$date_inspected = "date_ispected BETWEEN '".$req->from."' AND '".$req->to."'";
		}

		$db = DB::connection($this->mysql)
				->select("SELECT *
						FROM iqc_inspections
						WHERE ".$date_inspected);

		if ($this->checkIfExistObject($db) > 0) {
			return $db;
		}
	}

	private function IQCDatatableQuery($req,$join)
	{
		$g1 = ''; $g2 = ''; $g3 = '';
		$g1c = ''; $g2c = ''; $g3c = '';
		$date_inspected = '';
		$groupBy = [];

		// wheres
		if (!empty($req->from) && !empty($req->to)) {
			$date_inspected = "date_ispected BETWEEN '".$req->from."' AND '".$req->to."'";
		}

		if (!empty($req->field1) && !empty($req->content1)) {
			$g1c = " AND ".$req->field1."='".$req->content1."'";
		}

		if (!empty($req->field2) && !empty($req->content2)) {
			$g2c = " AND ".$req->field2."='".$req->content2."'";
		}

		if (!empty($req->field3) && !empty($req->content3)) {
			$g3c = " AND ".$req->field3."='".$req->content3."'";
		}

		if (!empty($req->field1)) {
			$g1 = $req->field1;
			array_push($groupBy, $g1);
		}

		if (!empty($req->field2)) {
			$g2 = $req->field2;
			array_push($groupBy, $g2);
		}

		if (!empty($req->field3)) {
			$g3 = $req->field3;
			array_push($groupBy, $g3);
		}

		$grp = implode(',',$groupBy);
		// $grby = substr($grp,0,-1);
		
		$grby = "";

		if (count($groupBy) > 0) {
			$grby = " GROUP BY ".$grp;
		}
		
		if ($join == false) {
			$db = DB::connection($this->mysql)
				->select("SELECT sample_size,
								SUM(lot_qty) AS lot_qty,
								SUM(no_of_defects) AS no_of_defects,
								SUM(lot_accepted) AS lot_accepted,
								SUM(lot_inspected) AS lot_inspected,
								supplier, app_date, date_ispected, judgement,
								time_ins_from, time_ins_to, app_no, fy, ww, submission,
								partcode, partname, lot_no, aql
						FROM iqc_inspections
						WHERE ".$date_inspected.$g1c.$g2c.$g3c.$grby);
		} else {

			$db = DB::connection($this->mysql)
				->select("SELECT a.invoice_no,a.partcode,a.partname,a.supplier,a.app_date,
								a.app_time,a.app_no,a.lot_no,a.lot_qty,a.type_of_inspection,a.severity_of_inspection,
								a.inspection_lvl,a.aql,a.accept,a.reject,a.date_ispected,a.ww,a.fy,a.shift,
								a.time_ins_from,a.time_ins_to,a.inspector,a.submission,a.judgement,a.lot_inspected,
								a.lot_accepted,a.sample_size,a.no_of_defects,a.remarks,b.mod
						FROM iqc_inspections as a
						LEFT JOIN tbl_mod_iqc_inspection as b ON a.invoice_no = b.invoice_no
						WHERE ".$date_inspected.$g1c.$g2c.$g3c.$grby);
		}
		

		if ($this->checkIfExistObject($db) > 0) {
			return $db;
		}
	}

	public function getIQCreport(Request $req)
	{
		$dt = Carbon::now();
		$company_info = $this->com->getCompanyInfo();
		$date = substr($dt->format('  M j, Y  h:i A '), 2);
		$header = DB::connection($this->mysql)->table('iqc_inspection_group')
					->select('invoice_no',
							'supplier',
							'app_no',
							'app_date',
							'app_time',
							'severity_of_inspection',
							'inspection_lvl',
							'type_of_inspection',
							'aql',
							'accept',
							'reject')
					->groupBy('invoice_no',
							'supplier',
							'app_no',
							'app_date',
							'app_time',
							'severity_of_inspection',
							'inspection_lvl',
							'type_of_inspection',
							'aql',
							'accept',
							'reject')
					->get();

		$details = DB::connection($this->mysql)->table('iqc_inspection_group')->get();

		$data = [
			'company_info' => $company_info,
			'header' => $header,
			'details' => $details,
			'date' => $date,
		];

		$pdf = PDF::loadView('pdf.iqc', $data)
					->setPaper('A4')
					->setOption('margin-top', 10)
					->setOption('margin-bottom', 5)
					->setOption('margin-left', 1)
					->setOption('margin-right', 1)
					->setOrientation('landscape');

		return $pdf->inline('IQC_Inspection_'.Carbon::now());
	}

	public function getIQCreportexcel()
	{
		try
		{
			$dt = Carbon::now();
			$date = substr($dt->format('Ymd'), 2);

			Excel::create('IQC_Inspection_Report'.$date, function($excel)
			{
				$excel->sheet('Sheet1', function($sheet)
				{
						$sheet->setFreeze('A7');
						$sheet->setWidth('A', 5);

						$details = DB::connection($this->mysql)->table('iqc_inspection_group')->get();

						$dt = Carbon::now();
						$com_info = $this->com->getCompanyInfo();

						$date = substr($dt->format('  M j, Y  h:i A '), 2);

						$sheet->setHeight(1, 15);
						$sheet->mergeCells('A1:AE1');
						$sheet->cells('A1:AE1', function($cells) {
							$cells->setAlignment('center');
						});
						$sheet->cell('A1',$com_info['name']);

						$sheet->setHeight(2, 15);
						$sheet->mergeCells('A2:AE2');
						$sheet->cells('A2:AE2', function($cells) {
							$cells->setAlignment('center');
						});
						$sheet->cell('A2',$com_info['address']);

						$sheet->setHeight(4, 20);
						$sheet->mergeCells('A4:AE4');
						$sheet->cells('A4:AE4', function($cells) {
							$cells->setAlignment('center');
							$cells->setFont([
								'family'     => 'Calibri',
								'size'       => '14',
								'bold'       =>  true,
								'underline'  =>  true
							]);
						});
						$sheet->cell('A4',"IQC INSPECTION SUMMARY");

						$sheet->setHeight(6, 20);
						$sheet->cells('B6:AE6', function($cells) {
							$cells->setBorder('thick','thick','thick','thick');
							$cells->setFont([
								'family'     => 'Calibri',
								'size'       => '11',
								'bold'       =>  true,
							]);
						});



						$sheet->cell('B6',"Invoice No.");
						$sheet->cell('C6',"Part Code");
						$sheet->cell('D6',"Part Name");
						$sheet->cell('E6',"Supplier");
						$sheet->cell('F6',"App. Date");
						$sheet->cell('G6',"App. Time");
						$sheet->cell('H6',"App. No.");
						$sheet->cell('I6',"Lot No.");
						$sheet->cell('J6',"Lot Qty.");
						$sheet->cell('K6',"Type of Inspection");
						$sheet->cell('L6',"Severity of Inspection");
						$sheet->cell('M6',"Inspection Level");
						$sheet->cell('N6',"AQL");
						$sheet->cell('O6',"Accept");
						$sheet->cell('P6',"Reject");
						$sheet->cell('Q6',"Date Inspected");
						$sheet->cell('R6',"WW");
						$sheet->cell('S6',"FY");
						$sheet->cell('T6',"Shift");
						$sheet->cell('U6',"Inspected From");
						$sheet->cell('V6',"Inspected To");
						$sheet->cell('W6',"Inspector");
						$sheet->cell('X6',"Submission");
						$sheet->cell('Y6',"Judgement");
						$sheet->cell('Z6',"Lot Inspected");
						$sheet->cell('AA6',"Lot Accepted");
						$sheet->cell('AB6',"Sample Size");
						$sheet->cell('AC6',"No. of Defects");
						$sheet->cell('AD6',"Remarks");
						$sheet->cell('AE6',"Classification");

						$row = 7;

						$sheet->setHeight(7, 20);

						$lot_qty = 0;
						$po_qty = 0;
						$balance = 0;

						foreach ($details as $key => $qc) {

							$sheet->cells('B'.$row.':AE'.$row, function($cells) {
								$cells->setFont([
									'family'     => 'Calibri',
									'size'       => '11',
								]);
							});
							$sheet->cell('B'.$row, function($cell) use($qc) {
								$cell->setValue($qc->invoice_no);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('C'.$row, function($cell) use($qc) {
								$cell->setValue($qc->partcode);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('D'.$row, function($cell) use($qc) {
								$cell->setValue($qc->partname);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('E'.$row, function($cell) use($qc) {
								$cell->setValue($qc->supplier);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('F'.$row, function($cell) use($qc) {
								$cell->setValue($qc->app_date);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('G'.$row, function($cell) use($qc) {
								$cell->setValue($qc->app_time);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('H'.$row, function($cell) use($qc) {
								$cell->setValue($qc->app_no);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('I'.$row, function($cell) use($qc) {
								$cell->setValue($qc->lot_no);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('J'.$row, function($cell) use($qc) {
								$cell->setValue($qc->lot_qty);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('K'.$row, function($cell) use($qc) {
								$cell->setValue($qc->type_of_inspection);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('L'.$row, function($cell) use($qc) {
								$cell->setValue($qc->severity_of_inspection);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('M'.$row, function($cell) use($qc) {
								$cell->setValue($qc->inspection_lvl);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('N'.$row, function($cell) use($qc) {
								$cell->setValue($qc->aql);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('O'.$row, function($cell) use($qc) {
								$cell->setValue($qc->accept);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('P'.$row, function($cell) use($qc) {
								$cell->setValue($qc->reject);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('Q'.$row, function($cell) use($qc) {
								$cell->setValue($qc->date_ispected);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('R'.$row, function($cell) use($qc) {
								$cell->setValue($qc->ww);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('S'.$row, function($cell) use($qc) {
								$cell->setValue($qc->fy);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('T'.$row, function($cell) use($qc) {
								$cell->setValue($qc->shift);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('U'.$row, function($cell) use($qc) {
								$cell->setValue($qc->time_ins_from);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('V'.$row, function($cell) use($qc) {
								$cell->setValue($qc->time_ins_to);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('W'.$row, function($cell) use($qc) {
								$cell->setValue($qc->inspector);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('X'.$row, function($cell) use($qc) {
								$cell->setValue($qc->submission);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('Y'.$row, function($cell) use($qc) {
								$cell->setValue($qc->judgement);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('Z'.$row, function($cell) use($qc) {
								$cell->setValue($qc->lot_inspected);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('AA'.$row, function($cell) use($qc) {
								$cell->setValue($qc->lot_accepted);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('AB'.$row, function($cell) use($qc) {
								$cell->setValue($qc->sample_size);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('AC'.$row, function($cell) use($qc) {
								$cell->setValue($qc->no_of_defects);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('AD'.$row, function($cell) use($qc) {
								$cell->setValue($qc->remarks);
								$cell->setBorder('thin','thin','thin','thin');
							});

							$sheet->cell('AE'.$row, function($cell) use($qc) {
								$cell->setValue($qc->classification);
								$cell->setBorder('thin','thin','thin','thin');
							});
							$row++;
						}

				});

			})->download('xls');
		} catch (Exception $e) {
			return redirect(url('/iqcinspection'))->with(['err_message' => $e]);
		}
	}

	public function uploadfiles(Request $req)
	{
		$inspection_data = $req->file('inspection_data');
		$inspection_mod = $req->file('inspection_mod');
		$requali_data = $req->file('requali_data');
		$requali_mod = $req->file('requali_mod');

		$data = [
			'return_status' => 'failed',
			'msg' => 'Upload was unsuccessful.'
		];

		$process = false;

		if (isset($inspection_data)) {
			$this->uploadInspection($inspection_data);
			$process = true;
		}

		if (isset($inspection_mod)) {
			$this->uploadInspectionMod($inspection_mod);
			$process = true;
		}

		if (isset($requali_data)) {
			$process = true;
		}

		if (isset($requali_mod)) {
			$process = true;
		}

		if ($process == true) {
			$data = [
				'return_status' => 'success',
				'msg' => 'Data were successfully uploaded.'
			];
		}

		return $data;
	}

	private function uploadInsepectionInsert($field)
	{
		$status = 0;
		$kitting = 0;

		if ($field['judgement'] == 'Accepted') {
			$status = 1;
			$kitting = 1;
		} else {
			$status = 2;
			$kitting = 0;
		}

		$lot_qty = $this->getLotQty($field['invoice_no'],$field['partcode'],$field['lot_no']);
		
		DB::connection($this->mysql)->table('iqc_inspections')
			->insert([
				'invoice_no' => $field['invoice_no'],
				'partcode' => $field['partcode'],
				'partname' => $field['partname'],
				'supplier' => $field['supplier'],
				'app_date' => $field['app_date'],
				'app_time' => $field['app_time'],
				'app_no' => $field['app_no'],
				'lot_no' => $field['lot_no'],
				'lot_qty' => $lot_qty,
				'type_of_inspection' => $field['type_of_inspection'],
				'severity_of_inspection' => $field['severity_of_inspection'],
				'inspection_lvl' => $field['inspection_lvl'],
				'aql' => $field['aql'],
				'accept' => $field['accept'],
				'reject' => $field['reject'],
				'date_ispected' => $field['date_inspected'],
				'ww' => $field['ww'],
				'fy' => $field['fy'],
				'shift' => $field['shift'],
				'time_ins_from' => $field['time_inspection_from'],
				'time_ins_to' => $field['time_inspection_to'],
				'inspector' => $field['inspector'],
				'submission' => $field['submission'],
				'judgement' => $field['judgement'],
				'lot_inspected' => $field['lot_inspected'],
				'lot_accepted' => $field['lot_accepted'],
				'sample_size' => $field['sample_size'],
				'no_of_defects' => $field['no_of_defects'],
				'remarks' => $field['remarks'],
				'dbcon' => Auth::user()->productline,
				'created_at' => Carbon::now(),
			]);

		DB::connection($this->mysql)->table('iqc_inspections_history')
				->insert([
					'invoice_no' => $field['invoice_no'],
					'partcode' => $field['partcode'],
					'partname' => $field['partname'],
					'supplier' => $field['supplier'],
					'app_date' => $field['app_date'],
					'app_time' => $field['app_time'],
					'app_no' => $field['app_no'],
					'lot_no' => $field['lot_no'],
					'lot_qty' => $lot_qty,
					'type_of_inspection' => $field['type_of_inspection'],
					'severity_of_inspection' => $field['severity_of_inspection'],
					'inspection_lvl' => $field['inspection_lvl'],
					'aql' => $field['aql'],
					'accept' => $field['accept'],
					'reject' => $field['reject'],
					'date_ispected' => $field['date_inspected'],
					'ww' => $field['ww'],
					'fy' => $field['fy'],
					'shift' => $field['shift'],
					'time_ins_from' => $field['time_inspection_from'],
					'time_ins_to' => $field['time_inspection_to'],
					'inspector' => $field['inspector'],
					'submission' => $field['submission'],
					'judgement' => $field['judgement'],
					'lot_inspected' => $field['lot_inspected'],
					'lot_accepted' => $field['lot_accepted'],
					'sample_size' => $field['sample_size'],
					'no_of_defects' => $field['no_of_defects'],
					'remarks' => $field['remarks'],
					'dbcon' => Auth::user()->productline,
					'created_at' => Carbon::now(),
				]);

		DB::connection($this->wbs)->table('tbl_wbs_material_receiving_batch')
			->where('invoice_no', $field['invoice_no'])
			->where('wbs_mr_id', $field['app_no'])
			->where('item', $field['partcode'])
			->where('lot_no', $field['lot_no'])
			->update([
				'iqc_status' => $status,
				'for_kitting' => $kitting,
				'iqc_result' => $field['remarks']
			]);

		DB::connection($this->wbs)->table('tbl_wbs_inventory')
			->where('invoice_no', $field['invoice_no'])
			->where('wbs_mr_id', $field['app_no'])
			->where('item', $field['partcode'])
			->where('lot_no', $field['lot_no'])
			->update([
				'iqc_status' => $status,
				'for_kitting' => $kitting,
				'iqc_result' => $field['remarks']
			]);
	}

	private function uploadInspection($inspection_data)
	{
		Excel::load($inspection_data, function($reader) {

			$results = $reader->get();
			$fields = $results->toArray();

			foreach ($fields as $key => $field) {
				if ($this->ItemInspectionExists($field['invoice_no'],$field['partcode'],$field['lot_no']) < 1) {
					$this->uploadInsepectionInsert($field);
				}
			}
		});
	}

	private function uploadInspectionMod($inspection_mod)
	{
		Excel::load($inspection_mod, function($reader) {

			$results = $reader->get();
			$fields = $results->toArray();

			foreach ($fields as $key => $field) {
				if ($this->ItemInspectionModExists($field['invoice_no'],$field['partcode'],$field['lot_no'],$field['mod']) < 1) {
					$this->insertInspectionMod($field);
				} else {
					$this->updateInspectionMod($field);
				}
			}
		});
	}

	private function insertInspectionMod($field)
	{
		DB::connection($this->mysql)->table('tbl_mod_iqc_inspection')
			->insert([
				'invoice_no' => $field['invoice_no'],
				'partcode' => $field['partcode'],
				'mod' => $field['mod'],
				'qty' => $field['qty'],
				'lot_no' => $field['lot_no'],
				'created_at' => Carbon::now(),
			]);
	}

	private function updateInspectionMod($field)
	{
		$oldmod = DB::connection($this->mysql)->table('tbl_mod_iqc_inspection')
					->select('qty')
					->where('invoice_no', $field['invoice_no'])
					->where('partcode', $field['partcode'])
					->where('mod', $field['mod'])
					->where('lot_no', $field['lot_no'])
					->first();

		$newqty = $oldmod + $field['qty'];

		DB::connection($this->mysql)->table('tbl_mod_iqc_inspection')
			->where('invoice_no', $field['invoice_no'])
			->where('partcode', $field['partcode'])
			->where('mod', $field['mod'])
			->where('lot_no', $field['lot_no'])
			->update([
				'qty' => $newqty,
				'updated_at' => Carbon::now(),
			]);
	}

	private function uploadRequali($requali_data)
	{
		
	}

	private function uploadRequaliMod($requali_mod)
	{
		
	}

	private function ItemInspectionExists($invoiceno,$partcode,$lotno)
	{
		$cnt = DB::connection($this->mysql)->table('iqc_inspections')
					->where('invoice_no',$invoiceno)
					->where('partcode',$partcode)
					->where('lot_no','like','%'.$lotno.'%')
					->count();
		return $cnt;
	}

	private function ItemInspectionModExists($invoiceno,$partcode,$lotno,$mod)
	{
		$cnt = DB::connection($this->mysql)->table('tbl_mod_iqc_inspection')
					->where('invoice_no',$invoiceno)
					->where('partcode',$partcode)
					->where('lot_no',$lotno)
					->where('mod',$mod)
					->count();
		return $cnt;
	}

	public function ReportDataCheck(Request $req)
	{

		if ($req->report_type == 'pdf') {
			$check = DB::connection($this->mysql)->table('iqc_inspection_group')
						->select('invoice_no',
								'supplier',
								'app_no',
								'app_date',
								'app_time',
								'severity_of_inspection',
								'inspection_lvl',
								'type_of_inspection',
								'aql',
								'accept',
								'reject')
						->groupBy('invoice_no',
								'supplier',
								'app_no',
								'app_date',
								'app_time',
								'severity_of_inspection',
								'inspection_lvl',
								'type_of_inspection',
								'aql',
								'accept',
								'reject')
						->count();

		} else {
			$check = DB::connection($this->mysql)->table('iqc_inspection_group')->count();
		}

		$data = ['DataCount' => $check];

		return response()->json($data);
	}

}
