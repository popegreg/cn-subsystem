<?php

namespace App\Http\Controllers\WBS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Config;
use Carbon\Carbon;
use PDF;
use Excel;
use Datatables;
use Event;
use App\Events\WHSCheckRequest;
use File;

class WBSWhsIssuanceController extends Controller
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

    public function index()
    {
		$pgcode = Config::get('constants.MODULE_CODE_WHSMATISS');

    	if(!$this->com->getAccessRights($pgcode, $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $data = DB::connection($this->common)
            		->select("SELECT id,dropdown_reason FROM
                        dropdown_fifo_reason
                      ");
            return view('wbs.whsissuance',[
						'userProgramAccess' => $userProgramAccess,
						'data' => $data,
						'pgcode' => $pgcode,
                        'pgaccess' => $this->com->getPgAccess($pgcode)
					]);
        }
    }

    public function getPendingRequest()
    {
		$data = DB::connection($this->mysql)
					->table('tbl_request_summary')
					->where('status','<>','Cancelled')
					->where('status','<>','Closed')
					->orderBy('id','desc')
					->select('id',
						DB::raw("ifnull(transno,'') as transno"),
						DB::raw("ifnull(DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s'),'') as created_at"),
						DB::raw("ifnull(pono,'') as pono"),
						DB::raw("ifnull(destination,'') as destination"),
						DB::raw("ifnull(line,'') as line"),	
						DB::raw("ifnull(status,'') as status"),
						DB::raw("ifnull(requestedby,'') as requestedby"),
						DB::raw("ifnull(lastservedby,'') as lastservedby"),
						DB::raw("ifnull(lastserveddate,'') as lastserveddate")
					);
        return Datatables::of($data)
                        ->addColumn('action', function($data) {
                            return '<a href="javascript:;" class="btn btn-circle btn-primary btn-sm btn_view_details" data-transno="'.$data->transno.
                            		'" data-status="'.$data->status.'">
                                    <i class="fa fa-search"></i>
                                </a>';
                        })
                        ->setRowClass(function($data) {
                            if ($data->status == 'Serving') {
                                return 'alert-info';
                            }

                            if ($data->status == 'Alert') {
                                return 'alert-danger';
                            }
                        })
                        ->make(true);
        return $data;
    }

    public function viewReqDetails(Request $req)
    {
    	$data = DB::connection($this->mysql)->table('tbl_request_detail')
                    ->where('transno',$req->transno)
                    ->select('id',
                        'transno',
                		'detailid',
						'code',
						'name',
						'classification',
						'issuedqty',
						'requestqty',
						'location',
						'lot_no',
						'remarks',
						'request_date',
						'requestedby',
						'acknowledgeby',
						DB::raw("IFNULL(servedqty,0) AS servedqty"),
						DB::raw("IFNULL(last_served_by,'') AS last_served_by"),
						DB::raw("IFNULL(last_served_date,'') AS last_served_date"))
                   	->get();
	    return $data;
    }

    public function getReqDetails(Request $req)
    {
    	$whs = [];
    	$details = [];
    	$status = 'Alert';
    	$id = '';
    	$checkWhs = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
    					->where('request_no',$req->transno)
    					->count();

    	if ($checkWhs > 0) {
			$whsdetails = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
							->select(
								'id',
								DB::raw("ifnull(issuance_no,'') as issuance_no"),
								DB::raw("ifnull(request_no,'') as request_no"),
								DB::raw("ifnull(detail_id,'') as detail_id"),
								DB::raw("ifnull(item,'') as item"),
								DB::raw("ifnull(item_desc,'') as item_desc"),
								DB::raw("ifnull(remarks,'') as remarks"),
								DB::raw("ifnull(approved_id,'') as approved_id"),
								DB::raw("ifnull(reason_id,'') as reason_id"),
								DB::raw("ifnull(pmr_detail_id,'') as pmr_detail_id"),
								DB::raw("ifnull(request_qty,0) as request_qty"),
								DB::raw("ifnull(issued_qty_o,0) as issued_qty_o"),
								DB::raw("ifnull(issued_qty_t,0) as issued_qty_t"),
								DB::raw("ifnull(lot_no,'') as lot_no"),
								DB::raw("ifnull(location,'') as location"),
								DB::raw("ifnull(inv_id,'') as inv_id")
							)
							->where('request_no',$req->transno)
							->get();

    		$whs = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
    					->where('request_no',$req->transno)->select('id','status')->first();

    		$status = $whs->status;
    		$id = $whs->id;

    		foreach ($whsdetails as $key => $w) {
	    		array_push($details,[
	    			'id' => $w->id,
					'issuance_no' => $w->issuance_no, 
					'request_no' => $w->request_no, 
					'detail_id' => $w->detail_id, 
					'item' => $w->item, 
					'item_desc' => $w->item_desc, 
					'remarks'=>$w->remarks,
					'approved_id'=>$w->approved_id,
					'reason_id'=>$w->reason_id,
					'pmr_detail_id' => $w->pmr_detail_id, 
					'request_qty' => $w->request_qty, 
					'issued_qty_o' => $w->issued_qty_o, 
					'issued_qty_t' => $w->issued_qty_t,
					'servedqty' => $w->issued_qty_t,
					'lot_no' => $w->lot_no, 
					'location' => $w->location,
					'inv_id' => $w->inv_id
	    		]);
	    	}
    	}

		$request = DB::connection($this->mysql)->table('tbl_request_detail')
                    ->whereIn('id',$req->ids)
                    ->select('id',
                        DB::raw("transno as request_no"),
                        DB::raw("whstransno as issuance_no"),
                		DB::raw("detailid as detail_id"),
						DB::raw("code as item"),
						DB::raw("name as item_desc"),
						DB::raw("requestqty as request_qty"),
						DB::raw("issuedqty as issued_qty_o"),
						'lot_no',
						'location',
						DB::raw("IFNULL(servedqty,0) AS issued_qty_t"))
                   	->get();


        foreach ($request as $key => $w) {
	    	array_push($details,[
    			'id' => $w->id, 
				'issuance_no' => $w->issuance_no, 
				'request_no' => $w->request_no, 
				'detail_id' => $w->detail_id, 
				'item' => $w->item, 
				'item_desc' => $w->item_desc, 
				'pmr_detail_id' => $w->id, 
				'request_qty' => $w->request_qty, 
				'issued_qty_o' => $w->issued_qty_o, 
				'issued_qty_t' => 0,
				'servedqty' => $w->issued_qty_t,
				'lot_no' => '', 
				'location' => $w->location,
    		]);
    	}

		$totals = DB::connection($this->mysql)->table('tbl_request_detail')
					->where('transno',$req->transno)
					->where(DB::raw("REPLACE(UPPER(remarks),' ','')"),'!=',"NONEEDREPLACEMENT")
					->select(
						DB::raw("SUM(requestqty) as total_req_qty"),
						DB::raw("SUM(servedqty) as total_served_qty")
					)->first();
					
		$served_qty_per_items = DB::connection($this->mysql)->table('tbl_request_detail')
				                    ->where('transno',$req->transno)
				                    ->select(
				                    	DB::raw("code as item"),
										DB::raw("SUM(servedqty) as served_qty")
									)
									->groupBy('code')
									->get();
		$served_qtys = [];
		foreach ($served_qty_per_items as $key => $served) {
			array_push($served_qtys, [
				$served->item => $served->served_qty
			]);
		}

	    return $data = [
	    	'details' => $details,
			'totals' => $totals,
	    	'status' => $status,
	    	'served' => $served_qty_per_items,
	    	'id' => $id
	    ];
    }

    public function getInventory(Request $req)
    {
        $item_cond ='';

        if(empty($req->item)) {
            $item_cond ='';
        } else {
            $item_cond = " AND i.item = '" . $req->item . "'";
        }

        $checklot = DB::connection($this->mysql)->table('tbl_wbs_inventory as i')
        				->whereRaw("1=1".$item_cond)->where('i.deleted',0)->count();

        if ($checklot > 0) {
        	$data = DB::connection($this->mysql)->table('tbl_wbs_inventory as i')
		                ->whereRaw("i.qty > 0 AND i.iqc_status IN ('1','4') ".$item_cond) //i.item=r.item AND 
                        ->where('i.deleted',0)
		                ->select(DB::raw('i.id as id'),
                            DB::raw('i.item as item'),
                            DB::raw('i.item_desc as item_desc'),
                            DB::raw('i.qty as qty'),
                            DB::raw("IFNULL(i.lot_no,'') as lot_no"),
                            DB::raw('i.location as location'),
							DB::raw("DATE_FORMAT(i.received_date, '%Y-%m-%d') as received_date")
						)
                        ->distinct()
		                ->orderBy('i.received_date','asc')
                        ->orderBy('i.lot_no','asc') 
		                ->get();
        } else {
        	$data = DB::connection($this->mysql)->table('tbl_wbs_inventory as i')
		                ->whereRaw("i.qty > 0 AND i.iqc_status IN ('1','4') ".$item_cond)//i.item=r.item AND 
                        ->where('i.deleted',0)
		                ->select(DB::raw('i.id as id'),
                            DB::raw('i.item as item'),
                            DB::raw('i.item_desc as item_desc'),
                            DB::raw('i.qty as qty'),
                            DB::raw("IFNULL(i.lot_no,'') as lot_no"),
                            DB::raw('i.location as location'),
							DB::raw("DATE_FORMAT(i.received_date, '%Y-%m-%d') as received_date")
						)
                        ->distinct()
		                ->orderBy('i.received_date','asc')
                        ->orderBy('i.lot_no','asc') 
		                ->get();
        }
        
        return $data;
    	// $data = DB::connection($this->mysql)->table('tbl_wbs_inventory')
    	// 			->where('item',$req->item)
    	// 			->where('for_kitting',1)
    	// 			->where('qty','>',0)
    	// 			->where('deleted',0)
    	// 			->orderBy('received_date','asc')
    	// 			->orderBy('lot_no','asc')
    	// 			->select(
    	// 				'id',
    	// 				'item',
    	// 				'item_desc',
    	// 				'qty',
    	// 				'lot_no',
    	// 				DB::raw("ifnull(DATE_FORMAT(received_date, '%Y-%m-%d'),'') as received_date")
    	// 			)->get();
    	// return $data;
    }

    public function save(Request $req)
    {
    	$data = [
    		'msg' => 'Saving failed.',
    		'status' => 'failed',
	    	'issuance_no' => $req->issuance_no
    	];

    	if ($req->id == '') {
    		foreach ($req->detail_id as $key => $detail_id) {
    			DB::connection($this->mysql)->table('tbl_request_detail')
					// ->where('whstransno',$req->issuance_no)
					// ->where('transno',$req->req_no)
					->where('id',$req->pmr_detail_id[$key])
					->increment('servedqty', $req->issued_qty_t[$key],[
						'lot_no' => $req->lot_no[$key],
						'last_served_by' => Auth::user()->user_id,
						'last_served_date' => date('Y-m-d H:i:s')
					]);

				//$status = $this->getDetailStatus($req->issuance_no,$req->req_no,$req->pmr_detail_id[$key]);

				$status = 'Serving';

				if ($req->issued_qty_t[$key] >= $req->request_qty[$key]) {
					$status = 'Closed';
				}

				DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
					->insert([
						'issuance_no' => $req->issuance_no,
						'request_no' => $req->req_no,
						'pmr_detail_id' => $req->pmr_detail_id[$key],
						'detail_id' => $detail_id,
						'item' => $req->item[$key],
						'item_desc' => $req->item_desc[$key],
						'request_qty' => $req->request_qty[$key],
						'issued_qty_o' => $req->issued_qty_o[$key],
						'issued_qty_t' => $req->issued_qty_t[$key],
						'remarks' => $req->iss_remarks[$key],
						'reason_id'=> $req->id_reason[$key],
						'approved_id' => $req->user[$key],
						'lot_no' => $req->lot_no[$key],
						'location' => $req->location[$key],
						'status' => $status,
						'create_user' => Auth::user()->user_id,
						'update_user' => Auth::user()->user_id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
						'issued_date' => date('Y-m-d'),
						'inv_id' => $req->inv_id[$key]
					]);

				DB::connection($this->mysql)->table('tbl_wbs_inventory')
					->where('id',$req->inv_id[$key])
					->decrement('qty',$req->issued_qty_t[$key]);
			}

			$sum_status = $this->getSummaryStatus($req->issuance_no,$req->req_no);

    		DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
				->insert([
					'issuance_no' => $req->issuance_no,
					'request_no' => $req->req_no,
					'status' => $sum_status,
					'total_req_qty' => $req->total_req_qty,
					'create_user' => Auth::user()->user_id,
					'update_user' => Auth::user()->user_id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);

			DB::connection($this->mysql)->table('tbl_request_summary')
					->where('whstransno',$req->issuance_no)
					->update([
						'status' => $sum_status,
						'lastservedby' => Auth::user()->user_id,
						'lastserveddate' => date('Y-m-d H:i:s'),
					]);

			$data = [
	    		'msg' => 'Successfully saved.',
	    		'status' => 'success',
	    		'issuance_no' => $req->issuance_no
	    	];

    	} else {
    		DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
    			->where('issuance_no',$req->issuance_no)->delete();

    		foreach ($req->detail_id as $key => $detail_id) {
    			DB::connection($this->mysql)->table('tbl_wbs_inventory')
					->where('id',$req->inv_id[$key])
					->decrement('qty',$req->issued_qty_t[$key]);
    			

				//$status = $this->getDetailStatus($req->issuance_no,$req->req_no,$req->pmr_detail_id[$key]);
				$status = 'Serving';

				if ($req->issued_qty_t[$key] >= $req->request_qty[$key]) {
					$status = 'Closed';
				}

				DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
					->insert([
						'issuance_no' => $req->issuance_no,
						'request_no' => $req->req_no,
						'pmr_detail_id' => $req->pmr_detail_id[$key],
						'detail_id' => $detail_id,
						'item' => $req->item[$key],
						'item_desc' => $req->item_desc[$key],
						'request_qty' => $req->request_qty[$key],
						'issued_qty_o' => $req->issued_qty_o[$key],
						'issued_qty_t' => $req->issued_qty_t[$key],
						'lot_no' => $req->lot_no[$key],
						'remarks'=> $req->iss_remarks[$key],
						'reason_id'=>$req->id_reason[$key],
						'approved_id'=>$req->user[$key],
						'location' => $req->location[$key],
						'status' => $status,
						'create_user' => Auth::user()->user_id,
						'update_user' => Auth::user()->user_id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
						'issued_date' => date('Y-m-d'),
						'inv_id' => $req->inv_id[$key]
					]);
				
			}

			$issued_details = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
								->where('request_no',$req->req_no)
								->select('issuance_no',
										'request_no',
										'pmr_detail_id',
										'item',
										'item_desc',
										'request_qty',
										'issued_qty_o',
										DB::raw('SUM(issued_qty_t) as issued_qty_t'))
								->groupBy('issuance_no',
										'request_no',
										'pmr_detail_id',
										'item',
										'item_desc',
										'request_qty',
										'issued_qty_o')
								->get();

			foreach ($issued_details as $key => $dt) {
				$status = $this->getDetailStatus($req->issuance_no,$req->req_no,$dt->pmr_detail_id);

    			if ($status == 'Serving') {
    				DB::connection($this->mysql)->table('tbl_request_detail')
						->where('id',$dt->pmr_detail_id)
						->update([
							'servedqty' => $dt->issued_qty_t,
							'lot_no' => 'N/A',
							'last_served_by' => Auth::user()->user_id,
							'last_served_date' => date('Y-m-d H:i:s')
						]);
    			}
			}

			$sum_status = $this->getSummaryStatus($req->issuance_no,$req->req_no);

			DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
				->where('issuance_no',$req->issuance_no)
				->update([
					'status' => $sum_status,
					'update_user' => Auth::user()->user_id,
					'updated_at' => date('Y-m-d H:i:s'),
				]);

			DB::connection($this->mysql)->table('tbl_request_summary')
					->where('whstransno',$req->issuance_no)
					->update(['status' => $sum_status]);
					
			$data = [
	    		'msg' => 'Successfully saved.',
	    		'status' => 'success',
	    		'issuance_no' => $req->issuance_no
	    	];
    	}

    	return $data;
    }

    private function getDetailStatus($issuance_no,$req_no,$id)
    {
    	$status = 'Alert';
    	$data = DB::connection($this->mysql)->table('tbl_request_detail')
						// ->where('whstransno',$issuance_no)
						// ->where('transno',$req_no)
						->where('id',$id)
						->select(
							'requestqty','servedqty'
						)->first();
		if ($data->requestqty > $data->servedqty) {
			$status = 'Serving';
		}

		if ($data->requestqty <= $data->servedqty) {
			$status = 'Closed';
		}

		return $status;
    }

    private function getSummaryStatus($issuance_no,$req_no)
    {
    	$status = 'Alert';
    	$data = DB::connection($this->mysql)->table('tbl_request_detail')
						->where('whstransno',$issuance_no)
						->where('transno',$req_no)
						->where(DB::raw("REPLACE(UPPER(remarks),' ','')"),'!=',"NONEEDREPLACEMENT")
						->select(
							DB::raw('SUM(requestqty) as requestqty'),
							DB::raw('SUM(servedqty) as servedqty')
						)->first();

		if ($data->requestqty > $data->servedqty) {
			$status = 'Serving';
		}

		if ($data->requestqty <= $data->servedqty) {
			$status = 'Closed';
		}

		return $status;
    }

    public function getData(Request $req)
    {
    	if (empty($req->to) && !empty($req->issuance_no)) {
    		$summary = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
						->select('id',
							'issuance_no',
							'request_no',
							'status',
							'total_req_qty',
							'create_user',
							'update_user',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where('issuance_no',$req->issuance_no)
						->first();

    		if ($this->com->checkIfExistObject($summary) > 0) {
    			$request = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
    						->where('issuance_no',$req->issuance_no)
    						->select(
    							DB::raw("SUM(issued_qty_t) as total_served_qty")
    						)->get();

    			$total_bal_qty = $summary->total_req_qty - $request[0]->total_served_qty;

	            $details = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
                                ->where('issuance_no',$summary->issuance_no)
                                ->select('id',
									'issuance_no',
									'request_no',
									'pmr_detail_id',
									'detail_id',
									'item',
									'item_desc',
									'request_qty',
									'issued_qty_o',
									'issued_qty_t',
									'lot_no',
									'remarks',
									'reason_id',
									'approved_id',
									'create_user',
									'update_user',
									'inv_id',
									DB::raw("IFNULL(location,'') as location"),
									DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
									DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"),
									DB::raw("DATE_FORMAT(issued_date, '%Y-%m-%d') as issued_date"))
                               	->get();
                $served_qty_per_items = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
											->where('issuance_no',$summary->issuance_no)
											->select('item',
													DB::raw('SUM(issued_qty_t) as served_qty'))
											->groupBy('item')
											->get();

				$served_qtys = [];
				foreach ($served_qty_per_items as $key => $served) {
					array_push($served_qtys, [
						$served->item => $served->served_qty
					]);
				}

	            return $data = [
                                'summary' => $summary,
			                	'details' => $details,
			                	'served_qty_per_item' => $served_qty_per_items,
			                	'total_req_qty' => $summary->total_req_qty,
			                	'total_served_qty' => $request[0]->total_served_qty,
			                	'total_bal_qty' => $total_bal_qty,
			                	'request' => $request
			                ];
	        } else {
	        	return $data = [
	                'status' => 'failed',
	                'msg' => 'No data found.'
	            ];
	        }
    	}

    	if (!empty($req->to) && !empty($req->issuance_no)) {
    		return $this->navigate($req->to,$req->issuance_no);
    	}
    	if (empty($req->to) && empty($req->issuance_no)) {
    		return $this->last();
    	}
    }

    private function navigate($to,$issuance_no)
    {
    	switch ($to) {
    		case 'first':
                return $this->first();
                break;

            case 'prev':
                return $this->prev($issuance_no);
                break;

            case 'next':
                return $this->next($issuance_no);
                break;

            case 'last':
                return $this->last();
                break;

            default:
                return $this->last();
                break;
        }
    }

    private function first()
    {
    	$data = [];
        $summary = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
						->select('id',
							'issuance_no',
							'request_no',
							'status',
							'total_req_qty',
							'create_user',
							'update_user',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MIN(id)"))
                              ->from('tbl_wbs_warehouse_mat_issuance');
                          })
						->first();

        if ($this->com->checkIfExistObject($summary) > 0) {
        	$request = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
    						->where('issuance_no',$summary->issuance_no)
    						->select(
    							DB::raw("SUM(issued_qty_t) as total_served_qty")
							)->get();

    			$total_bal_qty = $summary->total_req_qty - $request[0]->total_served_qty;

            $details = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
                            ->where('issuance_no',$summary->issuance_no)
                            ->select('id',
								'issuance_no',
								'request_no',
								'pmr_detail_id',
								'detail_id',
								'item',
								'remarks',
								'reason_id',
								'approved_id',
								'item_desc',
								'request_qty',
								'issued_qty_o',
								'issued_qty_t',
								'lot_no',
								'create_user',
								'update_user',
								'inv_id',
								DB::raw("IFNULL(location,'') as location"),
								DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
								DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"),
								DB::raw("DATE_FORMAT(issued_date, '%Y-%m-%d') as issued_date"))
                           	->get();

            $served_qty_per_items = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
								->where('issuance_no',$summary->issuance_no)
								->select('item',
										DB::raw('SUM(issued_qty_t) as served_qty'))
								->groupBy('item')
								->get();

			$served_qtys = [];
			foreach ($served_qty_per_items as $key => $served) {
				array_push($served_qtys, [
					$served->item => $served->served_qty
				]);
			}

            return $data = [
                            'summary' => $summary,
		                	'details' => $details,
		                	'total_req_qty' => $summary->total_req_qty,
			                'total_served_qty' => $request[0]->total_served_qty,
		                	'total_bal_qty' => $total_bal_qty,
		                	'served_qty_per_item' => $served_qty_per_items,
		                	'request' => $request
		                ];
		}
		return $data;
    }

    private function prev($issuance_no)
    {
        $data = [];
        $nxt = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
                ->where('issuance_no',$issuance_no)
                ->select('id')->first();

        if ($this->com->checkIfExistObject($nxt) > 0) {
            $summary = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
						->select('id',
							'issuance_no',
							'request_no',
							'status',
							'total_req_qty',
							'create_user',
							'update_user',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("id","<",$nxt->id)
                        ->orderBy("id","DESC")
                        ->first();

            if ($this->com->checkIfExistObject($summary) > 0) {
            	$request = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
	    						->where('issuance_no',$summary->issuance_no)
	    						->select(
	    							DB::raw("SUM(issued_qty_t) as total_served_qty")
								)->get();

    			$total_bal_qty = $summary->total_req_qty - $request[0]->total_served_qty;

            	$details = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
                                ->where('issuance_no',$summary->issuance_no)
                                ->select('id',
									'issuance_no',
									'request_no',
									'pmr_detail_id',
									'detail_id',
									'item',
									'remarks',
									'reason_id',
									'approved_id',
									'item_desc',
									'request_qty',
									'issued_qty_o',
									'issued_qty_t',
									'lot_no',
									'create_user',
									'update_user',
									'inv_id',
									DB::raw("IFNULL(location,'') as location"),
									DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
									DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"),
									DB::raw("DATE_FORMAT(issued_date, '%Y-%m-%d') as issued_date"))
                               	->get();

                $served_qty_per_items = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
								->where('issuance_no',$summary->issuance_no)
								->select('item',
										DB::raw('SUM(issued_qty_t) as served_qty'))
								->groupBy('item')
								->get();

				$served_qtys = [];
				foreach ($served_qty_per_items as $key => $served) {
					array_push($served_qtys, [
						$served->item => $served->served_qty
					]);
				}

	            return $data = [
                                'summary' => $summary,
			                	'details' => $details,
			                	'total_req_qty' => $summary->total_req_qty,
			                	'total_served_qty' => $request[0]->total_served_qty,
			                	'total_bal_qty' => $total_bal_qty,
			                	'served_qty_per_item' => $served_qty_per_items,
			                	'request' => $request
			                ];
            } else {
                return $this->first();
            }
        } else {
            $data = [
                'msg' => "You've reached the first Request Number",
                'status' => 'failed'
            ];
        }
        return $data;
    }

    private function next($issuance_no) 
    {
        $data = [];
        $nxt = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
                ->where('issuance_no',$issuance_no)
                ->select('id')->first();

        if ($this->com->checkIfExistObject($nxt) > 0) {
            $summary = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
						->select('id',
							'issuance_no',
							'request_no',
							'status',
							'total_req_qty',
							'create_user',
							'update_user',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("id",">",$nxt->id)
                        ->orderBy("id")
                        ->first();

            if ($this->com->checkIfExistObject($summary) > 0) {
            	$request = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
	    						->where('issuance_no',$summary->issuance_no)
	    						->select(
	    							DB::raw("SUM(issued_qty_t) as total_served_qty")
								)->get();

    			$total_bal_qty = $summary->total_req_qty - $request[0]->total_served_qty;

            	$details = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
                                ->where('issuance_no',$summary->issuance_no)
                                ->select('id',
									'issuance_no',
									'request_no',
									'pmr_detail_id',
									'detail_id',
									'item',
									'item_desc',
									'remarks',
									'reason_id',
									'approved_id',
									'request_qty',
									'issued_qty_o',
									'issued_qty_t',
									'lot_no',
									'create_user',
									'update_user',
									'inv_id',
									DB::raw("IFNULL(location,'') as location"),
									DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
									DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"),
									DB::raw("DATE_FORMAT(issued_date, '%Y-%m-%d') as issued_date"))
                               	->get();

                $served_qty_per_items = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
								->where('issuance_no',$summary->issuance_no)
								->select('item',
										DB::raw('SUM(issued_qty_t) as served_qty'))
								->groupBy('item')
								->get();

				$served_qtys = [];
				foreach ($served_qty_per_items as $key => $served) {
					array_push($served_qtys, [
						$served->item => $served->served_qty
					]);
				}

	            return $data = [
                                'summary' => $summary,
			                	'details' => $details,
			                	'total_req_qty' => $summary->total_req_qty,
			                	'total_served_qty' => $request[0]->total_served_qty,
			                	'total_bal_qty' => $total_bal_qty,
			                	'served_qty_per_item' => $served_qty_per_items,
			                	'request' => $request
			                ];
            } else {
                return $this->last();
            }
        } else {
            $data = [
                    'msg' => "You've reached the last Request Number",
                    'status' => 'failed'
                ];
        }

        return $data;
    }

    private function last()
    {
    	$data = [];
        $summary = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
						->select('id',
							'issuance_no',
							'request_no',
							'status',
							'total_req_qty',
							'create_user',
							'update_user',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MAX(id)"))
                              ->from('tbl_wbs_warehouse_mat_issuance');
                          })
						->first();

        if ($this->com->checkIfExistObject($summary) > 0) {
        	$request = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
    						->where('issuance_no',$summary->issuance_no)
    						->select(
    							DB::raw("SUM(issued_qty_t) as total_served_qty")
							)->get();

    			$total_bal_qty = $summary->total_req_qty - $request[0]->total_served_qty;

            $details = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
                        ->where('issuance_no',$summary->issuance_no)
                        ->select('id',
							'issuance_no',
							'request_no',
							'pmr_detail_id',
							'detail_id',
							'item',
							'item_desc',
							'remarks',
							'reason_id',
							'approved_id',
							'request_qty',
							'issued_qty_o',
							'issued_qty_t',
							'lot_no',
							'create_user',
							'update_user',
							'inv_id',
							DB::raw("IFNULL(location,'') as location"),
							DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
							DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"),
							DB::raw("DATE_FORMAT(issued_date, '%Y-%m-%d') as issued_date"))
                       	->get();

            $served_qty_per_items = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
									->where('issuance_no',$summary->issuance_no)
									->select('item',
											DB::raw('SUM(issued_qty_t) as served_qty'))
									->groupBy('item')
									->get();

			$served_qtys = [];
			foreach ($served_qty_per_items as $key => $served) {
				array_push($served_qtys, [
					$served->item => $served->served_qty
				]);
			}

            return $data = [
                            'summary' => $summary,
		                	'details' => $details,
		                	'total_req_qty' => $summary->total_req_qty,
			                'total_served_qty' => $request[0]->total_served_qty,
		                	'total_bal_qty' => $total_bal_qty,
		                	'served_qty_per_item' => $served_qty_per_items,
		                	'request' => $request
		                ];
        }

        return $data;
    }

    public function cancelIssuance(Request $req)
    {
    	$data = [
	    		'msg' => "Cancelling failed.",
	    		'status' => 'failed',
	    		'issuance_no' => $req->cancel_issuance_no
	    	];

    	$checkWHS = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
    					->where('issuance_no',$req->cancel_issuance_no)
    					->count();
    	if ($checkWHS > 0) {
    		DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
    					->where('issuance_no',$req->cancel_issuance_no)
    					->update(['status' => 'Cancelled']);

    		DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
    					->where('issuance_no',$req->cancel_issuance_no)
    					->where('status','<>','Closed')
    					->update(['status' => 'Cancelled']);
    	}

    	$cancelled = DB::connection($this->mysql)->table('tbl_request_summary')
	    				->where('whstransno',$req->cancel_issuance_no)
	    				->update(['status' => 'Cancelled']);

	    if ($cancelled) {
	    	$data = [
	    		'msg' => "Issuance No. [".$req->cancel_issuance_no."] is now cancelled.",
	    		'status' => 'success',
	    		'issuance_no' => $req->cancel_issuance_no
	    	];
	    }

	    return $data;
    }

    public function searchIssuance(Request $req)
    {
        $ctr = 0;
        $value = null;
        $result = null;

        $issuance_cond = '';
		$request_no_cond = '';
		$status_cond = '';
		$date_cond = '';

        try
        {
            if(empty($req->srch_issuance_no))
            {
                $issuance_cond ='';
            }
            else
            {
                $issuance_cond = " AND issuance_no like '%" . $req->srch_issuance_no . "%'";
            }

            if(empty($req->srch_request_no))
            {
                $request_no_cond ='';
            }
            else
            {
                $request_no_cond = " AND request_no like '%" . $req->srch_request_no . "%'";
            }

            if(empty($req->srch_from) || empty($req->srch_to))
            {
                $date_cond = '';
            }
            else
            {
                $date_cond = "AND issued_date BETWEEN '" . $req->srch_from . "' AND '" . $req->srch_to . "'";
            }

            $statuses = [];


            if(isset($req->srch_serving))
            {
                array_push($statuses,'Serving');
            }

            if(isset($req->srch_closed))
            {
            	array_push($statuses,'Closed');
            }

            if(isset($req->srch_cancelled))
            {
            	array_push($statuses,'Cancelled');
            }

            $stats = implode("','", $statuses);

            if (count($statuses) > 0) {
            	$status_cond = " AND `status` IN ('". $stats."')";
            } else {
            	$status_cond = "";
            }

            $details = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
	                        ->select( 'id'
	                            , 'issuance_no'
	                            , 'request_no'
	                            , 'status'
	                            , 'create_user'
	                            , DB::raw("(CASE created_at
	                                WHEN '0000-00-00' THEN NULL
	                                ELSE DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s')
	                               END) AS created_at")
	                            , 'update_user'
	                            , DB::raw("(CASE updated_at
	                                WHEN '0000-00-00' THEN NULL
	                                ELSE DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s')
	                               END) AS updated_at"))
	                        ->whereRaw(" 1=1 "
	                            . $issuance_cond
	                            . $request_no_cond
	                            . $status_cond
	                            . $date_cond)
	                        ->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $details;
    }

    public function exportToExcel(Request $req)
    {
        try
        { 
            $date = substr(date('Ymd'), 2);
            
            Excel::create('WBS_Dispatch'.$date, function($excel) use($req)
            {
                $excel->sheet('Sheet1', function($sheet) use($req)
                {
                    $dt = Carbon::now();
                    $date = $dt->format('m/d/Y');
                    $sheet->cell('A1',"PORDER");
                    $sheet->cell('B1',"CODE");
                    $sheet->cell('C1',"MOTO");
                    $sheet->cell('D1',"HOKAN");
                    $sheet->cell('E1',"SEIBAN");
                    $sheet->cell('F1',"PEDA");
                    $sheet->cell('G1',"JITUO");
                    $sheet->cell('H1',"LOTNAME");
                    $sheet->cell('I1',"FDATE");
                    $sheet->cell('J1',"TSLIP_NUM");
                    $sheet->cell('K1',"NAME");

                    $issuance_no = $req->issuance_no;

                    $field = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details as a')
                                ->leftJoin('tbl_request_summary as b','a.request_no','=','b.transno')
                                ->select('b.pono',
                                        'a.item',
                                        'a.issued_qty_t',
                                        DB::raw("IFNULL(a.lot_no,'') as lot_no"),
                                        'a.created_at',
                                        'a.request_no',
                                        'a.item_desc',
                                        'a.issuance_no')
                                ->where('a.issuance_no',$issuance_no)
                                ->get();
                    
                    $row = 2;
                    foreach ($field as $key => $val) {
                        $sheet->cell('A'.$row,"");
                        $sheet->cell('B'.$row,$val->item);
                        $sheet->cell('C'.$row,"WHS100");
                        $sheet->cell('D'.$row,"ASSY100");
                        $sheet->cell('E'.$row, $val->pono);
                        $sheet->cell('F'.$row, 1);
                        $sheet->cell('G'.$row, $val->issued_qty_t);
                        $sheet->cell('H'.$row, $val->lot_no);
                        $sheet->cell('I'.$row, $this->com->convertDate($val->created_at,'Ymd'));
                        $sheet->cell('J'.$row, substr($val->issuance_no,4));
                        $sheet->cell('K'.$row, $val->item_desc);
                        $row++;
                    }
                });

            })->download('xls');
        } catch (Exception $e) {
            return redirect(url('/whs-issuance'))->with(['err_message' => $e]);
        }
    }

    public function exportToPDF(Request $req)
    {
        $dt = Carbon::now();
        $date = substr($dt->format('  M j, Y A'), 2);
        $company_info = $this->com->getCompanyInfo();

        $issuance_no = $req->issuance_no;

        $summary = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
                        ->where('issuance_no',$issuance_no)->first();

        $details = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
                    ->where('issuance_no',$issuance_no)
                    ->select('id',
							'issuance_no',
							'request_no',
							'pmr_detail_id',
							'detail_id',
							'item',
							'item_desc',
							'request_qty',
							'issued_qty_o',
							'issued_qty_t',
							'lot_no',
							DB::raw("IFNULL(location,'') as location"),
							DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
							DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"),
							DB::raw("DATE_FORMAT(issued_date, '%Y-%m-%d') as issued_date"))
                    ->get();
        $tot = DB::connection($this->mysql)->table('tbl_request_detail')
					->where('whstransno',$issuance_no)
					->select(
						DB::raw("SUM(servedqty) as total_served_qty")
					)->first();

        $data = [
        	'date' => $date,
        	'company_info' => $company_info,
            'summary' => $summary,
            'details' => $details,
            'total_served_qty' => $tot->total_served_qty,
        ];

        $pdf = PDF::loadView('pdf.wbs_material_issuance', $data)
                    ->setPaper('A4')
                    ->setOption('margin-top', 6)
                    ->setOption('margin-left', 3)
                    ->setOption('margin-right', 3)
                    ->setOption('margin-bottom', 5)
                    ->setOrientation('landscape');
        return $pdf->inline('Warehouse_Issuance_'.$date);
    }

    public function cleanData()
    {
    	$data = [];
    	$edited = [];
    	$requests = DB::connection($this->mysql)->table('tbl_request_summary')
    					->where('status','<>','Alert')
    					->select(
    						DB::raw('transno as transno')
    					)->get();

    	foreach ($requests as $key => $req) {
    		$detail = DB::connection($this->mysql)->table('tbl_request_detail')
						->where('transno',$req->transno)
						->select(
							DB::raw('SUM(servedqty) as total_served_qty'),
							DB::raw('SUM(requestqty) as total_req_qty')
						)->get();

    		if ($detail[0]->total_req_qty > $detail[0]->total_served_qty) {
    			DB::connection($this->mysql)->table('tbl_request_summary')
    				->where('transno',$req->transno)
    				->update(['status' => 'Serving']);

    			DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
    				->where('request_no',$req->transno)
    				->update(['status' => 'Serving']);

    			array_push($edited,[
    				'request_no' => $req->transno,
    				'detail' => $detail
    			]);
    		}
    	}

    	$data = [
    		'requests' => $requests,
    		'edited' => $edited
    	];

    	return dd($data);
    }

    public function printBarcode(Request $req)
    {
    	$path = storage_path().'/brcodekitting';

        if (!File::exists($path)) {
            File::makeDirectory($path,777, true, true);
        }

        $filename = $req->issuance_no.'_'.$req->item.'.prn';

        $content = 'CLIP ON'."\r\n";
        $content .= 'CLIP BARCODE ON'."\r\n";
        $content .= 'DIR2'."\r\n";
        $content .= 'PP310,766:AN7'."\r\n";
        $content .= 'DIR2'."\r\n";
        $content .= 'FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 10'."\r\n";
        $content .= 'PP60,776:FT "Swiss 721 Bold BT",20,0,78'."\r\n";
        $content .= 'PP290,450:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 8'."\r\n";
        $content .= 'PT "'.$req->create_user.'"'."\r\n";
        $content .= 'PP290,200:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 8'."\r\n";
        $content .= 'PT "'.$req->created_at.'"'."\r\n";
        $content .= 'PP260,480:BARSET "CODE128",2,1,3,30'."\r\n";
        $content .= 'PB "'.$req->issuance_no.'"'."\r\n";
        $content .= 'PP220,350:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 6'."\r\n";
        $content .= 'PT "'.$req->issuance_no.'"'."\r\n";
        $content .= 'PP200,520:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 6'."\r\n";
        $content .= 'PT "Qty.:"'."\r\n";
        $content .= 'PP200,440:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 8'."\r\n";
        $content .= 'PT "'.$req->issued_qty_t.'"'."\r\n";
        $content .= 'PP200,360:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 8'."\r\n";
        $content .= 'PT "pc(s)"'."\r\n";
        $content .= 'PP160,400:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 6'."\r\n";
        $content .= 'PT ""'."\r\n";
        $content .= 'PP160,480:BARSET "CODE128",2,1,3,30'."\r\n";
        $content .= 'PB "'.$req->lot_no.'"'."\r\n";
        $content .= 'PP120,350:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 6'."\r\n";
        $content .= 'PT "'.$req->lot_no.'"'."\r\n";
        $content .= 'PP100,350:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 6'."\r\n";
        $content .= 'PT "'.$req->item_desc.'"'."\r\n";
        $content .= 'PP80,480:BARSET "CODE128",2,1,3,30'."\r\n";
        $content .= 'PB "'.$req->item.'"'."\r\n";
        $content .= 'PP40,350:FT "Swiss 721 BT"'."\r\n";
        $content .= 'FONTSIZE 6'."\r\n";
        $content .= 'PT "'.$req->item.'"'."\r\n";
        $content .= 'PP150,779:AN7'."\r\n";
        $content .= 'PF'."\r\n";

        // echo '<pre>',$content,'</pre>';

        $myfile = fopen($path."/".$filename, "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);

        return [
        	'filename' => $filename
        ];
        
    }

    public function downloadBarcode(Request $req)
    {
		$path = storage_path().'/brcodekitting';

	    $headers = [
	    	'Content-type'=>'text/plain',
	    	'Content-Disposition'=>sprintf('attachment; filename="%s"', $req->filename)
	    ];
	    return \Response::download($path.'/'.$req->filename, $req->filename, $headers);
    }

    public function Authorized(Request $req)
    {
    	$user_id = $req->user_id;
    	$actual_password = $req->actual_password;

    	$user = DB::connection($this->common)
                ->select("SELECT * FROM users WHERE
                         user_id = '".$user_id."' AND actual_password = '".$actual_password."'
                         ");
        return $user;  
    }

    public function SaveFifo(Request $req)
    {
        $user = $req->user;
        // dd($user_id);

        // dd($user_id);
        $data = [
                'status' => 'failed',
            ];

        $insert = DB::connection($this->mysql)->table('tbl_wbs_fiforeason')
                    ->insert([
                        'item' => $req->item,
                        'lotno' => $req->lotno,
                        'issuanceno' => $req->issuanceno,
                        'reason' => $req->reason,
                        'reason_id' =>$req->reason_id,
                        // 'remarks'=>$req->reason,
                        'created_id' => Auth::user()->user_id,
                        'created_date' => Carbon::now(),
                        'approved_id' => $req->user,
                        'approved_date' =>Carbon::now()
                        ]);

        if ($insert) {
            $data = [
                'status' => 'success',
            ];
        }

        return $data;
    }

}