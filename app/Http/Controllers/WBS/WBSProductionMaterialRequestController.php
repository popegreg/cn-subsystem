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
use Event;
use App\Events\CheckProdRequest;
use App\Events\WHSCheckRequest;

class WBSProductionMaterialRequestController extends Controller
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
		$pgcode = Config::get('constants.MODULE_CODE_PRDMATREQ');
        if(!$this->com->getAccessRights($pgcode, $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('wbs.productmaterialrequest',[
						'userProgramAccess' => $userProgramAccess,
						'pgcode' => $pgcode,
                        'pgaccess' => $this->com->getPgAccess($pgcode)
					]);
        }
    }

    public function SearchPO(Request $req)
    {
        $this->com->truncateTable($this->mysql,'temp_wbs_prodmatrequest');

        $info = DB::connection($this->mssql)
                        ->table('XSLIP as s')
                        ->leftJoin('XHEAD as h', 's.CODE', '=', 'h.CODE')
                        ->leftjoin('XRECE as r', 's.SEIBAN','=','r.SORDER')
                        ->select(DB::raw('s.CODE as code'),
                                DB::raw('h.NAME as prodname'),
                                DB::raw('r.KVOL as POqty'),
                                DB::raw('s.PORDER as porder'))
                        ->where('s.SEIBAN',$req->po)
                        ->orderBy('s.PORDER','desc')
                        ->first();

        if (count((array)$info) > 0) {
        	$db = DB::connection($this->mssql)
	                ->select("SELECT r.SORDER as po,
	                                hk.CODE as code, 
	                                h.NAME as name, 
	                                hk.KVOL as rqdqty, 
	                                x.RACKNO as location, 
	                                i.DRAWING_NUM as drawnum, 
	                                i.VENDOR as supplier, 
	                                x.WHS100 as whs100, 
	                                x.WHS102 as whs102
	                        FROM XRECE r
	                        LEFT JOIN XSLIP s ON r.SORDER = s.SEIBAN
	                        LEFT JOIN XHIKI hk ON s.PORDER = hk.PORDER
	                        LEFT JOIN XITEM i ON i.CODE = hk.CODE
	                        LEFT JOIN XHEAD h ON h.CODE = hk.CODE
	                        LEFT JOIN (SELECT z.CODE, 
	                                        ISNULL(z1.ZAIK,0) as WHS100, 
	                                        ISNULL(z2.ZAIK,0) as WHS102, 
	                                        z1.RACKNO FROM XZAIK z
	                                   LEFT JOIN XZAIK z1 ON z1.CODE = z.CODE AND z1.HOKAN = 'WHS100'
	                                   LEFT JOIN XZAIK z2 ON z2.CODE = z.CODE AND z2.HOKAN = 'WHS102'
	                                   WHERE z.RACKNO <> ''
	                                   GROUP BY z.CODE, z1.ZAIK, z2.ZAIK, z1.RACKNO
	                        ) x ON x.CODE = hk.CODE
	                        WHERE r.SORDER = '".$req->po."' AND s.PORDER = '".$info->porder."'
	                        GROUP BY r.SORDER,
	                                hk.CODE, 
	                                h.NAME, 
	                                i.VENDOR, 
	                                hk.KVOL, 
	                                i.DRAWING_NUM, 
	                                x.WHS100, 
	                                x.WHS102, 
	                                x.RACKNO");

	        foreach ($db as $key => $val) {
	            $this->checkIfInSakiAndKit($val);
	        }

	        $data = DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')->get();
	        return $data;
        }
        return [];
    }

    private function checkIfInSakiAndKit($prod)
    {
        $kit = DB::connection($this->mysql)->table('tbl_wbs_kit_issuance')
                    ->where('po',$prod->po)
                    ->where('item',$prod->code)
                    ->select('lot_no as lotno', DB::raw("issued_qty as issuedqty"))
                    ->get();

         $saki = DB::connection($this->mysql)->table('tbl_wbs_sakidashi_issuance as s')
                    ->join('tbl_wbs_sakidashi_issuance_item as i', 's.issuance_no','=','i.issuance_no')
                    ->where('s.po_no',$prod->po)
                    ->where('item',$prod->code)
                    ->select('lot_no as lotno', 'issued_qty as issuedqty')
                    ->get();

        if ($this->com->checkIfExistObject($kit) > 0) {
            foreach ($kit as $key => $x) {
                DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')->insert([
                    'po' => $prod->po,
                    'code' => $prod->code,
                    'name' => $prod->name,
                    'issuedqty' => (isset($x->issuedqty)) ? $x->issuedqty : "0.0000",
                    'lot_no' => (isset($x->lotno)) ? $x->lotno : "",
                    'location' => (isset($prd->location)) ? $prd->location : ""
                ]);
            }
        }

        if ($this->com->checkIfExistObject($saki) > 0) {
            foreach ($saki as $key => $x) {
                DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')->insert([
                    'po' => $prod->po,
                    'code' => $prod->code,
                    'name' => $prod->name,
                    'issuedqty' => (isset($x->issuedqty)) ? $x->issuedqty : "0.0000",
                    'lot_no' => (isset($x->lotno)) ? $x->lotno : "",
                    'location' => $prod->location
                ]);
            }
        }

        if ($this->com->checkIfExistObject($saki) < 1 && $this->com->checkIfExistObject($kit) < 1) {
            DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')->insert([
                'po' => $prod->po,
                'code' => $prod->code,
                'name' => $prod->name,
                'issuedqty' => "0.0000",
                'lot_no' => "",
                'location' => $prod->location
            ]);
        }
    }

    public function selectPOeDetails(Request $req)
    {
        $db = DB::connection($this->mysql)->table('temp_wbs_prodmatrequest')
	            ->whereIn('id',$req->ids)
	            ->select('id',
	                    'po',
	                    'code',
	                    'name',
	                    DB::raw("IFNULL(lot_no,'') as lot_no"),
	                    DB::raw("IFNULL(classification,'') as classification"),
	                    DB::raw("IFNULL(issuedqty,0) as issuedqty"),
	                    DB::raw("IFNULL(requestqty,0) as requestqty"),
	                    DB::raw("IFNULL(location,'') as location"))
	            ->get();
        return $db;
    }

    public function getSelections()
    {
    	return $data = [
	    	'line' => $this->com->getDropdownByName('linedestination'),
	        'prod' => $this->com->getDropdownByName('productdestination'),
	        'class' => $this->com->getDropdownByName('classification'),
	    ];
    }

    public function save(Request $req)
    {
    	$data = [
    		'msg' => 'Saving failed.',
    		'status' => 'failed'
		];
		$requestqty = 0;

    	if ($req->req_no == '') {
    		$status = 'Alert';

	        $transno_no = $this->com->getTransCode('PRD_REQ');
	        $whstransNo = $this->com->getTransCode('WAR_ISS');

	        DB::connection($this->mysql)->table('tbl_request_summary')
	            ->insert([
	                'transno' => $transno_no,
	                'whstransno' => $whstransNo,
	                'pono' => $req->po,
	                'destination' => $req->prod_destination,
	                'line' => $req->line_destination,
	                'status' => $status,
	                'remarks' => $req->remarkspmr,
	                'requestedby' => Auth::user()->user_id,
	                'createdby' => Auth::user()->user_id,
	                'updatedby' => Auth::user()->user_id,
	                'created_at' => date('Y-m-d h:i:s'),
	                'updated_at' => date('Y-m-d h:i:s'),
	                'requested_at' => date('Y-m-d'),
	            ]);

	        $params = [];

	        foreach ($req->detailid as $key => $detailid)
	        {
				
				// if ($req->remarks[$key] == "NO NEED REPLACEMENT") {
				// 	$requestqty = 0;
				// } else {
				// 	$requestqty = $req->requestqty[$key];
				// }
				
	        	array_push($params,[
	        		'transno' => $transno_no,
	        		'whstransno' => $whstransNo,
	        		'detailid' => $detailid,
	        		'code' => $req->code[$key],
	        		'name' => $req->name[$key],
	        		'classification' => $req->classification[$key],
	        		'issuedqty' => $req->issuedqty[$key],
	        		'requestqty' => $req->requestqty[$key],
	        		'location' => $req->location[$key],
	        		'lot_no' => $req->lot_no[$key],
	        		'remarks' => $req->remarks[$key],
	        		'request_date' => date('Y-m-d'),
	        		'requestedby' => Auth::user()->user_id,
	        		'created_at' => date('Y-m-d h:i:s'),
	        		'updated_at' => date('Y-m-d h:i:s'),
	        	]);
	        }

	        $fields = array_chunk($params, 1000);

            foreach ($fields as $field) {
                DB::connection($this->mysql)->table('tbl_request_detail')->insert($field);
            }

            $data = [
	    		'msg' => 'Successfully saved.',
	    		'status' => 'success',
	    		'req_no' => $transno_no
	    	];
    	} else {
    		$summary = DB::connection($this->mysql)->table('tbl_request_summary')
    					->where('transno',$req->req_no)->first();

    		DB::connection($this->mysql)->table('tbl_request_detail')
    			->where('transno',$req->req_no)->delete();

    		$params = [];

	        foreach ($req->detailid as $key => $detailid)
	        {
	        	$whs = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
	        				->where('request_no',$summary->transno)
	        				->where('item',$req->code[$key])
	        				->select(
	        					DB::raw("IFNULL(SUM(issued_qty_t),0) as servedqty"),
	        					DB::raw("IFNULL(create_user,'') as last_served_by"),
	        					DB::raw("IFNULL(issued_date,'') as last_served_date")
	        				)
	        				->first();

	        	array_push($params,[
	        		'transno' => $summary->transno,
	        		'whstransno' => $summary->whstransno,
	        		'detailid' => $detailid,
	        		'code' => $req->code[$key],
	        		'name' => $req->name[$key],
	        		'classification' => $req->classification[$key],
	        		'issuedqty' => $req->issuedqty[$key],
	        		'requestqty' => $req->requestqty[$key],
	        		'location' => $req->location[$key],
	        		'lot_no' => $req->lot_no[$key],
	        		'remarks' => $req->remarks[$key],
	        		'request_date' => $summary->requested_at,
	        		'requestedby' => $summary->requestedby,
	        		'servedqty' => $whs->servedqty,
	        		'last_served_by' => $whs->last_served_by,
	        		'last_served_date' => $whs->last_served_date,
	        		'created_at' => $summary->created_at,
	        		'updated_at' => date('Y-m-d h:i:s'),
	        	]);
	        }

	        $fields = array_chunk($params, 1000);

            foreach ($fields as $field) {
                DB::connection($this->mysql)->table('tbl_request_detail')->insert($field);
            }

            DB::connection($this->mysql)->table('tbl_request_summary')
    			->where('transno',$req->req_no)
	            ->update([
	                'pono' => $req->po,
	                'destination' => $req->prod_destination,
	                'line' => $req->line_destination,
	                'status' => $this->checkStatus($req->req_no),
	                'remarks' => $req->remarkspmr,
	                'updatedby' => Auth::user()->user_id,
	                'updated_at' => date('Y-m-d h:i:s'),
	                'requested_at' => date('Y-m-d'),
	            ]);

            $data = [
	    		'msg' => 'Successfully saved.',
	    		'status' => 'success',
	    		'req_no' => $req->req_no
	    	];
    	}

    	return $data;
    }

    public function getData(Request $req)
    {
    	if (empty($req->to) && !empty($req->req_no)) {
    		$summary = DB::connection($this->mysql)->table('tbl_request_summary')
						->select('id',
						    'transno',
							'pono',
							'destination',
							'line',
							'status',
							'remarks',
							'createdby',
							'updatedby',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where('transno',$req->req_no)
						->first();

    		if ($this->com->checkIfExistObject($summary) > 0) {
	            $details = DB::connection($this->mysql)->table('tbl_request_detail')
                                ->where('transno',$summary->transno)
                                ->select('id',
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

	            return $data = [
                                'summary' => $summary,
			                	'details' => $details,
			                ];
	        } else {
	        	return $data = [
	                'status' => 'failed',
	                'msg' => 'No data found.'
	            ];
	        }
    	}

    	if (!empty($req->to) && !empty($req->req_no)) {
    		return $this->navigate($req->to,$req->req_no);
    	}
    	if (empty($req->to) && empty($req->req_no)) {
    		return $this->last();
    	}
    }

    private function navigate($to,$req_no)
    {
    	switch ($to) {
    		case 'first':
                return $this->first();
                break;

            case 'prev':
                return $this->prev($req_no);
                break;

            case 'next':
                return $this->next($req_no);
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
        $summary = DB::connection($this->mysql)->table('tbl_request_summary')
						->select('id',
						    'transno',
							'pono',
							'destination',
							'line',
							'status',
							'remarks',
							'createdby',
							'updatedby',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MIN(id)"))
                              ->from('tbl_request_summary');
                          })
						->first();

        if ($this->com->checkIfExistObject($summary) > 0) {
            $details = DB::connection($this->mysql)->table('tbl_request_detail')
                        ->where('transno',$summary->transno)
                        ->select('id',
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

            return $data = [
                            'summary' => $summary,
		                	'details' => $details,
		                ];
		}
		return $data;
    }

    private function prev($req_no)
    {
        $data = [];
        $nxt = DB::connection($this->mysql)->table('tbl_request_summary')
                        ->where('transno',$req_no)
                        ->select('id')->first();

        if ($this->com->checkIfExistObject($nxt) > 0) {
            $summary = DB::connection($this->mysql)->table('tbl_request_summary')
                        ->select('id',
						    'transno',
							'pono',
							'destination',
							'line',
							'status',
							'remarks',
							'createdby',
							'updatedby',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("id","<",$nxt->id)
                        ->orderBy("id","DESC")
                        ->first();

            if ($this->com->checkIfExistObject($summary) > 0) {
            	$details = DB::connection($this->mysql)->table('tbl_request_detail')
                                ->where('transno',$summary->transno)
                                ->select('id',
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

	            return $data = [
                                'summary' => $summary,
			                	'details' => $details,
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

    private function next($req_no) 
    {
        $data = [];
        $nxt = DB::connection($this->mysql)->table('tbl_request_summary')
                        ->where('transno',$req_no)
                        ->select('id')->first();

        if ($this->com->checkIfExistObject($nxt) > 0) {
            $summary = DB::connection($this->mysql)->table('tbl_request_summary')
                        ->select('id',
						    'transno',
							'pono',
							'destination',
							'line',
							'status',
							'remarks',
							'createdby',
							'updatedby',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
                        ->where("id",">",$nxt->id)
                        ->orderBy("id")
                        ->first();

            if ($this->com->checkIfExistObject($summary) > 0) {
            	$details = DB::connection($this->mysql)->table('tbl_request_detail')
                                ->where('transno',$summary->transno)
                                ->select('id',
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

	            return $data = [
                                'summary' => $summary,
			                	'details' => $details,
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
        $summary = DB::connection($this->mysql)->table('tbl_request_summary')
                        ->select('id',
						    'transno',
							'pono',
							'destination',
							'line',
							'status',
							'remarks',
							'createdby',
							'updatedby',
						    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
						    DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MAX(id)"))
                              ->from('tbl_request_summary');
                          })
						->first();

        if ($this->com->checkIfExistObject($summary) > 0) {
            $details = DB::connection($this->mysql)->table('tbl_request_detail')
                            ->where('transno',$summary->transno)
                            ->select('id',
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

            return $data = [
                            'summary' => $summary,
		                	'details' => $details,
		                ];
        }

        return $data;
    }

    private function checkStatus($req_no)
    {
    	$status = 'Alert';

    	$check = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
    				->where('request_no',$req_no)->count();

    	if ($check > 0) {
    		$data = DB::connection($this->mysql)->table('tbl_request_detail')
	    				->where('transno',$req_no)
	    				->select(
	    					DB::raw("SUM(requestqty) as requestqty"),
	    					DB::raw("SUM(servedqty) as servedqty")
	    				)->first();

	    	if ($data->requestqty > $data->servedqty) {
	    		$status = 'Serving';
	    	}

	    	if ($data->requestqty <= $data->servedqty) {
	    		$status = 'Closed';
	    	}
    	}

    	return $status;
    }

    public function acknowledge(Request $req)
    {
    	$data = [
				'status' => 'failed',
				'msg' => 'Acknowledging failed.'
			];

    	$update = DB::connection($this->mysql)->table('tbl_request_detail')
		            ->where('id',$req->id)
		            ->update([
		                'acknowledgeby' => Auth::user()->user_id,
		                'acknowledge_all' => 1
		            ]);
		if ($update) {
			$check = DB::connection($this->mysql)->table('tbl_request_detail')
			            ->where('acknowledge_all',0)
			            ->where('transno',$req->req_no)
			            ->count();

		    if ($check < 1) {
		    	$up_sum = DB::connection($this->mysql)->table('tbl_request_summary')
		    				->where('transno',$req->req_no)
		    				->update(['acknowledge_all' => 1]);
		    }

			$data = [
				'status' => 'success',
				'req_no' => $req->req_no
			];
		}

		return $data;
    }

    public function getPDF(Request $req)
    {
        $dt = Carbon::now();
        $date = substr($dt->format('  M j, Y A'), 2);
        $company_info = $this->com->getCompanyInfo();

		$summary = DB::connection($this->mysql)->table('tbl_request_summary')
						->select('id',
							'transno',
							'pono',
							'destination',
							'line',
							'status',
							'requestedby',
							'lastservedby',
							'requested_at',
							DB::raw("DATE_FORMAT(lastserveddate, '%Y-%m-%d') as lastserveddate"),
							'createdby',
							DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at"),
							'updatedby',
							DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at"))
						->where('transno', $req->req_no)
						->first();

        $details = DB::connection($this->mysql)->table('tbl_request_detail')
                        ->where('transno','=', $req->req_no)
                        ->select('code'
                                , 'name'
                                , DB::raw('FORMAT(issuedqty,2) AS issuedqty')
                                , DB::raw('FORMAT(requestqty,2) AS requestqty')
                                , DB::raw('FORMAT(servedqty,2) AS servedqty')
                                , 'classification'
                                , 'lot_no'
                                , 'remarks'
                                , 'requestedby'
                                , 'acknowledgeby')
                        ->orderBy('code')
                        ->get();


        $data = [
            'date' => $date,
            'company_info' => $company_info,
            'summary' => $summary,
            'details' => $details,
        ];

        $pdf = PDF::loadView('pdf.wbs_production_request', $data)
                    ->setPaper('A4')
                    ->setOption('margin-top', 10)->setOption('margin-bottom', 5)
                    ->setOrientation('landscape');
        return $pdf->inline('Product_Material_Request_'.$date);
    }

    public function cancelRequest(Request $req)
    {
    	$data = [
	    		'msg' => "Cancelling failed.",
	    		'status' => 'failed',
	    		'req_no' => $req->cancel_req_no
	    	];

    	$checkWHS = DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
    					->where('request_no',$req->cancel_req_no)
    					->count();
    	if ($checkWHS > 0) {
    		DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance')
    					->where('request_no',$req->cancel_req_no)
    					->update(['status' => 'Cancelled']);

    		DB::connection($this->mysql)->table('tbl_wbs_warehouse_mat_issuance_details')
    					->where('request_no',$req->cancel_req_no)
    					->where('status','<>','Closed')
    					->update(['status' => 'Cancelled']);
    	}

    	$cancelled = DB::connection($this->mysql)->table('tbl_request_summary')
	    				->where('transno',$req->cancel_req_no)
	    				->update(['status' => 'Cancelled']);

	    if ($cancelled) {
	    	$data = [
	    		'msg' => "Request No. [".$req->cancel_req_no."] is now cancelled.",
	    		'status' => 'success',
	    		'req_no' => $req->cancel_req_no
	    	];
	    }

	    return $data;
    }

    public function searchRequest(Request $req)
    {
        $ctr = 0;
        $value = null;
        $result = null;

        $pono_cond = '';
        $prodes_cond = '';
        $linedes_cond = '';
        $status_cond = '';
        $date_cond = '';
        $req_no_cond = '';

        try
        {
            if(empty($req->srch_po))
            {
                $pono_cond ='';
            }
            else
            {
                $pono_cond = " AND pono like '" . $req->srch_po . "'";
            }

            if(empty($req->srch_req_no))
            {
                $req_no_cond ='';
            }
            else
            {
                $req_no_cond = " AND transno like '" . $req->srch_req_no . "'";
            }

            if(empty($req->srch_from) || empty($req->srch_to))
            {
                $date_cond = '';
            }
            else
            {
                $date_cond = "AND requested_at BETWEEN '" . $req->srch_from . "' AND '" . $req->srch_to . "'";
            }

            $statuses = [];


            if(isset($req->srch_open))
            {
                array_push($statuses,'Alert');
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

            $details = DB::connection($this->mysql)->table('tbl_request_summary')
                        ->select( 'id'
                            , 'transno'
                            , 'pono'
                            , 'destination'
                            , 'line'
                            , 'status'
                            , 'createdby'
                            , DB::raw("(CASE created_at
                                WHEN '0000-00-00' THEN NULL
                                ELSE DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s')
                               END) AS created_at")
                            , 'updatedby'
                            , DB::raw("(CASE updated_at
                                WHEN '0000-00-00' THEN NULL
                                ELSE DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s')
                               END) AS updated_at"))
                        ->whereRaw(" 1=1 "
                            . $pono_cond
                            . $status_cond
                            . $date_cond
                            . $req_no_cond)
                        ->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $details;
    }
}
