<?php

namespace App\Http\Controllers\WBS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use DB;
use Config;
use Carbon\Carbon;
use PDF;
use App;
use Excel;

class WBSMaterialDispositionController extends Controller
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

    public function getMatDisposistion()
    {
		$pgcode = Config::get('constants.MODULE_CODE_MATDIS');

        if(!$this->com->getAccessRights($pgcode, $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
			$dispositions = $this->com->getDropdownById(90);

	        return view('wbs.materialdisposition',[
						'userProgramAccess' => $userProgramAccess,
						'dispositions' => $dispositions,
						'pgcode' => $pgcode,
                        'pgaccess' => $this->com->getPgAccess($pgcode)
					]);
	    }
    }

	public function getDataDisposition(Request $req)
	{
		if ($req->to == "" && $req->transaction_no == "") {
            return $this->last();
		}

		if ($req->to == "" && $req->transaction_no !== "") {
			$trans = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
					->select(
						DB::raw("id"),
						DB::raw("transaction_no"),
						DB::raw("remarks"),
						DB::raw("create_user"),
						DB::raw("update_user"),
						DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
						DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
					)
					->where('transaction_no',$req->transaction_no)
					->first();

			if ($this->com->checkIfExistObject($trans) > 0) {
				$details = DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')
								->where('transaction_id',$trans->id)
								->select(
									DB::raw("id"),
									DB::raw("transaction_id"),
									DB::raw("item"),
									DB::raw("item_desc"),
									DB::raw("ifnull(lot_no,'') as lot_no"),
									DB::raw("ifnull(qty,0) as qty"),
									DB::raw("if(expiration = '0000-00-00','',DATE_FORMAT(expiration, '%Y-%m-%d')) as expiration"),
									DB::raw("ifnull(disposition,'') as disposition"),
									DB::raw("ifnull(remarks,'') as remarks"),
									DB::raw("inv_id"),
									DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
									DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
								)
								->get();
			}
		}

		

		
		if ($req->to !== "" && $req->transaction_no !== "") {
            return $this->navigate($req->to,$req->transaction_no);
		}

		$data = [
			'transaction' => $trans,
			'details' => $details
		];

    	return $data;
	}
	
	private function navigate($to,$transaction_no)
    {
        switch ($to) {
            case 'first':
                return $this->first();
                break;

            case 'prev':
                return $this->prev($transaction_no);
                break;

            case 'next':
                return $this->next($transaction_no);
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
        $data = [
			'transaction' => [],		
			'details' => []
		];
		
        $trans = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
                        ->select(
							DB::raw("id"),
							DB::raw("transaction_no"),
							DB::raw("remarks"),
							DB::raw("create_user"),
							DB::raw("update_user"),
							DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
							DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
						)
                        ->where("id", "=", function ($query) {
							$query->select(DB::raw(" MIN(id)"))
									->from('tbl_wbs_material_disposition');
                          })
                        ->first();

        if ($this->com->checkIfExistObject($trans) > 0) {
            $details = DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')
							->where('transaction_id',$trans->id)
							->select(
								DB::raw("id"),
								DB::raw("transaction_id"),
								DB::raw("item"),
								DB::raw("item_desc"),
								DB::raw("ifnull(lot_no,'') as lot_no"),
								DB::raw("ifnull(qty,0) as qty"),
								DB::raw("if(expiration = '0000-00-00','',DATE_FORMAT(expiration, '%Y-%m-%d')) as expiration"),
								DB::raw("ifnull(disposition,'') as disposition"),
								DB::raw("ifnull(remarks,'') as remarks"),
								DB::raw("inv_id"),
								DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
								DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
							)
							->get();

            return $data = [
                            'transaction' => $trans,
                            'details' => $details
                        ];
        }
        return $data;
    }

    private function prev($transaction_no) 
    {
		$data = [
			'transaction' => [],		
			'details' => []
		];
		
        $nxt = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
					->where('transaction_no',$transaction_no)
					->select('id')->first();

        if ($this->com->checkIfExistObject($nxt) > 0) {
            $trans = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
                        ->select(
							DB::raw("id"),
							DB::raw("transaction_no"),
							DB::raw("remarks"),
							DB::raw("create_user"),
							DB::raw("update_user"),
							DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
							DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
						)
                        ->where("id","<",$nxt->id)
                        ->orderBy("id","DESC")
                        ->first();

            if ($this->com->checkIfExistObject($trans) > 0) {

                $details = DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')
								->where('transaction_id',$trans->id)
								->select(
									DB::raw("id"),
									DB::raw("transaction_id"),
									DB::raw("item"),
									DB::raw("item_desc"),
									DB::raw("ifnull(lot_no,'') as lot_no"),
									DB::raw("ifnull(qty,0) as qty"),
									DB::raw("if(expiration = '0000-00-00','',DATE_FORMAT(expiration, '%Y-%m-%d')) as expiration"),
									DB::raw("ifnull(disposition,'') as disposition"),
									DB::raw("ifnull(remarks,'') as remarks"),
									DB::raw("inv_id"),
									DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
									DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
								)
								->get();

                return $data = [
                                'transaction' => $trans,
                                'details' => $details
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

    private function next($transaction_no) 
    {
        $data = [
			'transaction' => [],		
			'details' => []
		];

        $nxt = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
                ->where('transaction_no',$transaction_no)
                ->select('id')->first();

        if ($this->com->checkIfExistObject($nxt) > 0) {
            $trans =  DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
                        ->select(
							DB::raw("id"),
							DB::raw("transaction_no"),
							DB::raw("remarks"),
							DB::raw("create_user"),
							DB::raw("update_user"),
							DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
							DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
						)
                        ->where("id",">",$nxt->id)
                        ->orderBy("id","ASC")
                        ->first();

            if ($this->com->checkIfExistObject($trans) > 0) {
                $details = DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')
								->where('transaction_id',$trans->id)
								->select(
									DB::raw("id"),
									DB::raw("transaction_id"),
									DB::raw("item"),
									DB::raw("item_desc"),
									DB::raw("ifnull(lot_no,'') as lot_no"),
									DB::raw("ifnull(qty,0) as qty"),
									DB::raw("if(expiration = '0000-00-00','',DATE_FORMAT(expiration, '%Y-%m-%d')) as expiration"),
									DB::raw("ifnull(disposition,'') as disposition"),
									DB::raw("ifnull(remarks,'') as remarks"),
									DB::raw("inv_id"),
									DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
									DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
								)
								->get();

                return $data = [
                                'transaction' => $trans,
                                'details' => $details
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
		$data = [
			'transaction' => [],		
			'details' => []
		];
        $trans = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
                        ->select(
							DB::raw("id"),
							DB::raw("transaction_no"),
							DB::raw("remarks"),
							DB::raw("create_user"),
							DB::raw("update_user"),
							DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
							DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
						)
                        ->where("id", "=", function ($query) {
                            $query->select(DB::raw(" MAX(id)"))
								->from('tbl_wbs_material_disposition');
							})
                        ->first();

        if ($this->com->checkIfExistObject($trans) > 0) {
            $details = DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')
								->where('transaction_id',$trans->id)
								->select(
									DB::raw("id"),
									DB::raw("transaction_id"),
									DB::raw("item"),
									DB::raw("item_desc"),
									DB::raw("ifnull(lot_no,'') as lot_no"),
									DB::raw("ifnull(qty,0) as qty"),
									DB::raw("if(expiration = '0000-00-00','',DATE_FORMAT(expiration, '%Y-%m-%d')) as expiration"),
									DB::raw("ifnull(disposition,'') as disposition"),
									DB::raw("ifnull(remarks,'') as remarks"),
									DB::raw("inv_id"),
									DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at"),
									DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated_at")
								)
								->get();

            return $data = [
                            'transaction' => $trans,
                            'details' => $details
                        ];
        }
        return $data;
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
		                ->whereRaw("i.qty > 0 AND i.iqc_status IN ('2','4') ".$item_cond) //i.item=r.item AND 
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
		                ->whereRaw("i.qty > 0 AND i.iqc_status IN ('2','4') ".$item_cond)//i.item=r.item AND 
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
	}
	
	public function saveDisposition(Request $req)
	{
		$data = [
			'msg' => 'Saving failed.',
			'status' => 'failed'
		];

		if ($req->disposition_id == "") {
			$transaction_no = $this->com->getTransCode('MAT_DIS');
            $dis_id = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
                        ->insertGetId([
							'transaction_no' => $transaction_no,
							'remarks' => $req->remarks,
							'create_user' => Auth::user()->user_id,
							'update_user' => Auth::user()->user_id,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]);

			$params = [];

			if ($dis_id) {
				if (count($req->detail_id) > 0) {
					foreach ($req->detail_id as $key => $id) {
						array_push($params, [
							'transaction_id' => $dis_id,
							'item' => $req->detail_item[$key],
							'item_desc' => $req->detail_item_desc[$key],
							'lot_no' => $req->detail_lot_no[$key],
							'qty' => $req->detail_qty[$key],
							'expiration' => $req->detail_expiration[$key],
							'disposition' => $req->detail_disposition[$key],
							'remarks' => $req->detail_remarks[$key],
							'inv_id' => $req->detail_inv_id[$key],
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]);
					}

					$insert = array_chunk($params, 100);

					foreach ($insert as $batch) {
						DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')->insert($batch);

						// deduct to inventory if necessary
					}
				}

				$data = [
					'trans_no' => $transaction_no,
					'msg' => 'Data was successfully saved.',
					'status' => 'success'
				];
			} else {
				return $data;
			}
		} else {
			$trans = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
						->where('id', $req->disposition_id)
                        ->update([
							'remarks' => $req->remarks,
							'update_user' => Auth::user()->user_id,
							'updated_at' => date('Y-m-d H:i:s')
						]);

			if ($trans) {
				if (count($req->detail_id) > 0) {
					foreach ($req->detail_id as $key => $id) {
						$exist = DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')
									->where('id', $id)
									->count();

						if ($exist > 0) {
							DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')
								->where('id', $id)
								->update([
									'item' => $req->detail_item[$key],
									'item_desc' => $req->detail_item_desc[$key],
									'lot_no' => $req->detail_lot_no[$key],
									'qty' => $req->detail_qty[$key],
									'expiration' => $req->detail_expiration[$key],
									'disposition' => $req->detail_disposition[$key],
									'remarks' => $req->detail_remarks[$key],
									'inv_id' => $req->detail_inv_id[$key],
									'updated_at' => date('Y-m-d H:i:s')
								]);
						} else {
							DB::connection($this->mysql)->table('tbl_wbs_material_disposition_details')
								->insert([
									'transaction_id' => $req->disposition_id,
									'item' => $req->detail_item[$key],
									'item_desc' => $req->detail_item_desc[$key],
									'lot_no' => $req->detail_lot_no[$key],
									'qty' => $req->detail_qty[$key],
									'expiration' => $req->detail_expiration[$key],
									'disposition' => $req->detail_disposition[$key],
									'remarks' => $req->detail_remarks[$key],
									'inv_id' => $req->detail_inv_id[$key],
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
								]);
						}
					}
				}

				$data = [
					'trans_no' => $req->transaction_no,
					'msg' => 'Data was successfully saved.',
					'status' => 'success'
				];
			} else {
				return $data;
			}
		}

		return $data;
	}

	public function searchDisposition(Request $req)
	{
		$item_cond = '';
		$lot_cond = '';
		$trans_cond = '';
		$date_cond = '';

		if(empty($req->srch_item))
        {
            $item_cond = '';
        }
        else
        {
            $item_cond = " AND d.item = '" . $req->srch_item . "'";
		}
		
		if(empty($req->srch_lot_no))
        {
            $lot_cond = '';
        }
        else
        {
            $lot_cond = " AND d.lot_no = '" . $req->srch_lot_no . "'";
		}
		
		if(empty($req->srch_trans_no))
        {
            $trans_cond = '';
        }
        else
        {
            $trans_cond = " AND m.transaction_no = '" . $req->srch_trans_no . "'";
		}
		
		if (empty($req->srch_from) && empty($req->srch_to)) {
			$date_cond = '';
		} else {
			$date_cond = "AND DATE_FORMAT(m.created_at,'%Y-%m-%d') BETWEEN '" . $req->srch_from . "' AND '" . $req->srch_to . "'";
		}

		$data = DB::connection($this->mysql)
				->select("SELECT m.id,
								m.transaction_no,
								d.item,
								d.item_desc,
								d.lot_no,
								d.qty,
								m.create_user,
								m.created_at
						FROM pmi_wbs_cn.tbl_wbs_material_disposition as m
						inner join pmi_wbs_cn.tbl_wbs_material_disposition_details as d
						on m.id = d.transaction_id
						where 1=1 ".$date_cond.$trans_cond.$lot_cond.$item_cond."
						group by m.id,
								m.transaction_no,
								d.item,
								d.item_desc,
								d.lot_no,
								d.qty,
								m.create_user,
								m.created_at
						order by id desc");

		return $data;
	}




    public function dispositionsave(Request $input){
    	$itemcode = $input->itemcode;
    	$itemname = $input->itemname;
    	$lotno = $input->lotno;
    	$lotqty = $input->lotqty;
    	$disposition = $input->disposition;
    	$createdby = $input->createdby;
    	$createddate = $input->createddate;
    	$updatedby = $input->updatedby;
    	$updateddate = $input->updateddate;
    	$status = $input->status;
    	$id = $input->id;
    	$hdqty = $input->hdqty;

    	if($status == "ADD"){
    		$ok = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
		    		->insert([
		    				'itemcode'=>$itemcode,
		    				'itemname'=>$itemname,
		    				'lotno'=>$lotno,
		    				'lotqty'=>$lotqty,
		    				'disposition'=>$disposition,
		    				'createdby'=>$createdby,
		    				'createddate'=>$createddate,
		    				'updatedby'=>$updatedby,
		    				'updateddate'=>$updateddate
		    			]);
    				DB::connection($this->mysql)->table('tbl_wbs_inventory')
    					->where('item',$itemcode)
    					->where('item_desc',$itemname)
    					->where('lot_no',$lotno)	
    					->update(array(
    						'qty'=>$hdqty
    					));

    		if($ok){
    			return "SAVED";
    		}else{
    			return "NOT";
    		}
    	}
    	if($status == "EDIT"){
    		$ok = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
		    		->where('id',$id)
		    		->update(array(
		    				'itemcode'=>$itemcode,
		    				'itemname'=>$itemname,
		    				'lotno'=>$lotno,
		    				'lotqty'=>$lotqty,
		    				'disposition'=>$disposition,
		    				'createdby'=>$createdby,
		    				'createddate'=>$createddate,
		    				'updatedby'=>$updatedby,
		    				'updateddate'=>$updateddate
		    			));	
		    		DB::connection($this->mysql)->table('tbl_wbs_inventory')
    					->where('item',$itemcode)
    					->where('item_desc',$itemname)
    					->where('lot_no',$lotno)	
    					->update(array(
    						'qty'=>$hdqty
    					));
    		if($ok){
    			return "UPDATED";
    		}else{
    			return "NOT";
    		}
    	}
    }

    public function deleteDisposition(Request $input){
    	$id = $input->id;
    	$ok = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')
    		->where('id',$id)
    		->delete();

    	if($ok){
			return "DELETED";
		}else{
			return "NOT";
		}	
    }

    public function dispositionExportToExcel(){
    	try
        { 
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            
            Excel::create('OQC_Inspection_Report'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $dt = Carbon::now();
                    $date = $dt->format('m/d/Y');

                    $sheet->cell('A1',"Item/Part Code");
                    $sheet->cell('B1',"Item Name");
                    $sheet->cell('C1',"Quantity");
                    $sheet->cell('D1',"Lot No");
                    $sheet->cell('E1',"Expiration");
                    $sheet->cell('F1',"Remarks");
             
                    $sheet->setHeight(1,20);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('#ADD8E6');
                        $row->setFontSize(15);
                        $row->setAlignment('center');
                    });
                   
                    $sheet->setStyle(array(
                        'font' => array(
                            'name'  =>  'Calibri',
                            'size'  =>  10
                        )
                    ));

			    	$field = DB::connection($this->mysql)->table('tbl_wbs_material_disposition')->orderBy('id','DESC')->get();
                    $row = 2;
                    foreach ($field as $key => $val) {
                        $sheet->cell('A'.$row, $val->itemcode);
                        $sheet->cell('B'.$row, $val->itemname);
                        $sheet->cell('C'.$row, $val->lotqty);
                        $sheet->cell('D'.$row, $val->lotno);
                        $sheet->cell('E'.$row, $val->createddate);
                        $sheet->cell('F'.$row, $val->disposition);
                
                        $sheet->row($row, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->setHeight($row,20);
                        $row++;
                    }
                });

            })->download('xls');
        } catch (Exception $e) {
            return redirect(url('/wbsprodmatreturn'))->with(['err_message' => $e]);
        }    
    }

    public function itemcodechange(Request $input){
    	$itemcode = $input->itemcode;
    	$table = DB::connection($this->mysql)->table('tbl_wbs_inventory')
    				->select('item','item_desc','qty','lot_no')
    				->where('item',$itemcode)
    				->get();

    	return $table;
    }

}
