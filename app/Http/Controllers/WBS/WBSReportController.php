<?php

namespace App\Http\Controllers\WBS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use App;
use Config;
use Carbon\Carbon;
use Dompdf\Dompdf;
use PDF;
use Excel;

class WBSReportController extends Controller
{
	protected $mysql;
	protected $wbs;
	protected $mssql;
	protected $common;
	
	public function __construct()
	{
		$this->middleware('auth');
		$com = new CommonController;

		if (Auth::user() != null) {
			$this->mysql = $com->userDBcon(Auth::user()->productline,'mysql');
			$this->wbs = $com->userDBcon(Auth::user()->productline,'wbs');
			$this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
			$this->common = $com->userDBcon(Auth::user()->productline,'common');
		} else {
			return redirect('/');
		}
	}

	public function getWBSReport()
	{
		$common = new CommonController;
		if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_WBSRPRT'), $userProgramAccess))
		{
			return redirect('/home');
		}
		else
		{

			return view('wbs.wbsreport',['userProgramAccess' => $userProgramAccess]);
		}
	}

	public function getIQCreport()
	{
		$html = '<style>
					#data {
					  border-collapse: collapse;
					  width: 100%;
					  font-size:10px
					}

					#data thead td {
					  border: 1px solid black;
					  text-align: center;
					}

					#data tbody td {
					  border-bottom: 1px solid black;
					}

					#info {
					  width: 100%;
					}

					#info thead td {
					  text-align: center;
					}


				  </style>
				  <table id="info">
					<thead>
					  <tr>
						<td colspan="5">
						  <h2>INSPECTION RESULT RECORD</h2>
						</td>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td width="10%">partname</td>
						<td width="20%">qeq</td>
						<td width="20%">Type of Inspection</td>
						<td width="20%">asd</td>
						<td width="30%" style="text-align:center">date</td>
					  </tr>
					  <tr>
						<td>partcode</td>
						<td>asd</td>
						<td>Ac</td>
						<td>asd</td>
						<td></td>
					  </tr>
					  <tr>
						<td></td>
						<td></td>
						<td>Re</td>
						<td>asd</td>
						<td></td>
					  </tr>
					</tbody>
				  </table>


				  <table id="data">
					<thead>
					  <tr>
						<td>FY-WW</td>
						<td>Date Inspected</td>
						<td>App Ctrl No.</td>
						<td>Shift</td>
						<td>From</td>
						<td>To</td>
						<td># of Sub</td>
						<td>Lot Qty</td>
						<td>Sample Size</td>
						<td>AQL</td>
						<td>Severity of Inspection</td>
						<td>Inspection Level</td>
						<td>Lot No.</td>
						<td>Qty of Defects</td>
						<td>Mode of Defects</td>
						<td>Determination on Lot Acceptability</td>
						<td>Inspector</td>
						<td>Remarks</td>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					  </tr>
					</tbody>
				  </table>';

		$pdf = App::make('snappy.pdf.wrapper');
		$pdf->loadHTML($html)->setPaper('letter')->setOrientation('landscape');
		return $pdf->inline();
		// $dompdf = new Dompdf();
		// $dompdf->loadHtml($html);
		// $dompdf->setPaper('letter', 'landscape');
		// $dompdf->render();
		// $dompdf->stream('IQC_Inspection_'.Carbon::now().'.pdf');
	}

	public function getWBSMatKit(Request $req)
	{
		$ctr = 0;
		$value = null;
		$result = null;

		$from_cond = '';
		$to_cond = '';
		$pono_cond = '';
		$issno_cond = '';
		$device_cond ='';
		$item_cond ='';

		try {
			# Create PO No. Condition.
			if(empty($req->pono))
			{
				$pono_cond = '';
			} else {
				$pono_cond = " AND k.po_no = '" . $req->pono . "'";
			}

			# Create Issuance No. Condition
			if(empty($req->issno))
			{
				$issno_cond ='';
			} else {
				$issno_cond = " AND k.issuance_no = '" . $req->issno . "'";
			}

			if(empty($req->device))
			{
				$device_cond ='';
			} else {
				$device_cond = " AND k.device_code = '" . $req->device . "'";
			}

			if(empty($req->item))
			{
				$item_cond ='';
			} else {
				$item_cond = " AND i.item = '" . $req->item . "'";
			}

			if (!empty($req->from) && !empty($req->to)) {
				$from_cond = "AND DATE_FORMAT(k.issuance_date, '%Y-%m-%d') BETWEEN '" . $req->from . "' AND '" . $req->to . "'";
			} else {
				$from_cond = '';
				$to_cond = '';
			}

			$data = DB::connection($this->wbs)->table('tbl_wbs_kit_issuance as i')
					->leftJoin('tbl_wbs_material_kitting as k', function($join) {
						$join->on('i.issue_no','=','k.issuance_no');
						$join->on('i.po','=','k.po_no');
					})
					->whereRaw(" 1=1 ".$pono_cond.$issno_cond.$device_cond.$item_cond.$from_cond.$to_cond)
					->select('i.issue_no as issue_no',
							'i.item as item',
							'i.po as po',
							DB::raw('SUM(i.issued_qty) as qty'),
							'i.lot_no as lot_no',
							'k.created_at as fdate',
							'i.item_desc as item_desc',
							'k.kit_no as kit_no',
							'k.status',
							'k.prepared_by as prepared_by')
					->orderBy('k.created_at','asc')
					->groupBy('issue_no',
								'item',
								'po',
								'lot_no',
								'fdate',
								'item_desc',
								'kit_no',
								'status')
					->distinct()
					->get();

			if ($req->mode == 'dispatch') {
				$this->mkl_dispatch($data);
			} else {
				$this->mkl_summary($data);
			}
		}
		catch (Exception $e) {
			Log::error($e->getMessage());
		}
	}

	private function mkl_dispatch($data)
	{
		$dt = Carbon::now();
		$date = substr($dt->format('Ymd'), 2);
		
		Excel::create('FOR_DISPATCH_MAT_KIT_'.$date, function($excel) use($data)
		{
			$excel->sheet('Sheet1', function($sheet) use($data)
			{
				$sheet->cell('A1', "PORDER");
				$sheet->cell('B1', "CODE");
				$sheet->cell('C1', "MOTO");
				$sheet->cell('D1', "HOKAN");
				$sheet->cell('E1', "SEIBAN");
				$sheet->cell('F1', "BEDA");
				$sheet->cell('G1', "KVOL");
				$sheet->cell('H1', "PICKDATE");
				$sheet->cell('I1', "LOTNAME");
				$sheet->cell('J1', "TSLIP_NUM");
				$sheet->cell('K1', "NOTE");

				$sheet->setColumnFormat([
					'G' => '0.0000',
				]);

				$sheet->setWidth(array(
					'B'     =>  15,
					'E'     =>  20
				));

				$sheet->setColumnFormat(array(
					'B'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
					'E'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT
				));

				$row = 2;
				$status = '';
				foreach ($data as $key => $mk) {
					if ($mk->status == 'O') {
						$status = 'Open';
					}

					if ($mk->status == 'X') {
						$status = 'Closed';
					}

					if ($mk->status == 'C') {
						$status = 'Cancelled';
					}

					$sheet->cell('A'.$row, "");
					$sheet->setCellValueExplicit('B'.$row,  $mk->item, \PHPExcel_Cell_DataType::TYPE_STRING);
					//$sheet->cell('B'.$row, $mk->item);
					$sheet->cell('C'.$row, "WHS100");
					$sheet->cell('D'.$row, "ASSY100");
					$sheet->setCellValueExplicit('E'.$row,  $mk->po, \PHPExcel_Cell_DataType::TYPE_STRING);
					//$sheet->cell('E'.$row, $mk->po);
					$sheet->cell('F'.$row, "1");
					$sheet->cell('G'.$row, $mk->qty);
					$sheet->cell('H'.$row, $this->convertDate($mk->fdate,'Ymd'));
					$sheet->cell('I'.$row, $mk->lot_no);
					$sheet->cell('J'.$row, substr($mk->issue_no,4));
					$sheet->cell('K'.$row, $mk->kit_no.'/'.$mk->prepared_by);
					$sheet->cell('L'.$row, $mk->item_desc);
					$sheet->cell('M'.$row, $status);
					$row++;
				}
			});

		})->download('xls');
	}

	private function mkl_summary($data)
	{
		$dt = Carbon::now();
		$date = $dt->format('m-d-y');

		$com = new CommonController;
		$com_info = $com->getCompanyInfo();
		
		Excel::create('Material_Kitting_List_'.$date, function($excel) use($data,$com_info)
		{
			$excel->sheet('Report', function($sheet) use($data,$com_info)
			{
				$sheet->setHeight(1, 15);
				$sheet->mergeCells('A1:I1');
				$sheet->cells('A1:I1', function($cells) {
					$cells->setAlignment('center');
				});
				$sheet->cell('A1',$com_info['name']);

				$sheet->setHeight(2, 15);
				$sheet->mergeCells('A2:I2');
				$sheet->cells('A2:I2', function($cells) {
					$cells->setAlignment('center');
				});
				$sheet->cell('A2',$com_info['address']);

				$sheet->setHeight(4, 20);
				$sheet->mergeCells('A4:I4');
				$sheet->cells('A4:I4', function($cells) {
					$cells->setAlignment('center');
					$cells->setFont([
						'family'     => 'Calibri',
						'size'       => '14',
						'bold'       =>  true,
						'underline'  =>  true
					]);
				});
				$sheet->cell('A4',"MATERIAL KITTING LIST SUMMARY");

				$sheet->setHeight(6, 15);
				$sheet->cells('A6:I6', function($cells) {
					$cells->setFont([
						'family'     => 'Calibri',
						'size'       => '11',
						'bold'       =>  true,
					]);
					// Set all borders (top, right, bottom, left)
					$cells->setBorder('solid', 'solid', 'solid', 'solid');
				});
				$sheet->cell('A6', "ITEM/PART NO.");
				$sheet->cell('B6', "PO No.");
				$sheet->cell('C6', "Issued Qty.");
				$sheet->cell('D6', "LOT No.");
				$sheet->cell('E6', "Kit No.");
				$sheet->cell('F6', "Prepared By");
				$sheet->cell('G6', "Created Date");
				$sheet->cell('H6', "Transaction No.");
				$sheet->cell('I6', "Item Description");

				$sheet->setWidth(array(
					'A'     =>  15,
					'B'     =>  20
				));

				$sheet->setColumnFormat(array(
					'A'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
					'B'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT
				));

				$row = 7;

				foreach ($data as $key => $mk) {
					$sheet->setHeight($row, 15);
					$sheet->setCellValueExplicit('A'.$row,  $mk->item, \PHPExcel_Cell_DataType::TYPE_STRING);
					$sheet->setCellValueExplicit('B'.$row,  $mk->po, \PHPExcel_Cell_DataType::TYPE_STRING);
					// $sheet->cell('A'.$row, $mk->item);
					// $sheet->cell('B'.$row, $mk->po);
					$sheet->cell('C'.$row, $mk->qty);
					$sheet->cell('D'.$row, $mk->lot_no);
					$sheet->cell('E'.$row, $mk->kit_no);
					$sheet->cell('F'.$row, $mk->prepared_by);
					$sheet->cell('G'.$row, $this->convertDate($mk->fdate,'m/d/Y h:i A'));
					$sheet->cell('H'.$row, substr($mk->issue_no,4));
					$sheet->cell('I'.$row, $mk->item_desc);
					$row++;
				}
				
				$sheet->cells('A6:I'.$row, function($cells) {
					$cells->setBorder('solid', 'solid', 'solid', 'solid');
				});
			});
		})->download('xls');
	}

	public function getWBSSakidashi(Request $req)
	{
		$ctr = 0;
		$value = null;
		$result = null;

		$from_cond = '';
		$to_cond = '';
		$pono_cond = '';
		$issno_cond = '';
		$device_cond ='';
		$item_cond ='';

		try {
			# Create PO No. Condition.
			if(empty($req->pono))
			{
				$pono_cond = '';
			} else {
				$pono_cond = " AND k.po_no = '" . $req->pono . "'";
			}

			# Create Issuance No. Condition
			if(empty($req->issno))
			{
				$issno_cond ='';
			} else {
				$issno_cond = " AND k.issuance_no = '" . $req->issno . "'";
			}

			if(empty($req->device))
			{
				$device_cond ='';
			} else {
				$device_cond = " AND k.device_code = '" . $req->device . "'";
			}

			if(empty($req->item))
			{
				$item_cond ='';
			} else {
				$item_cond = " AND i.item = '" . $req->item . "'";
			}

			if (!empty($req->from) && !empty($req->to)) {
				$from_cond = " AND DATE_FORMAT(k.issuance_date, '%Y-%m-%d') BETWEEN '" . $req->from . "' AND '" . $req->to . "' ";
				$from_cond .= " AND DATE_FORMAT(k.created_at, '%Y-%m-%d') BETWEEN '" . $req->from . "' AND '" . $req->to . "' ";
			} else {
				$from_cond = '';
				$to_cond = '';
			}

			$data = DB::connection($this->wbs)->table('tbl_wbs_sakidashi_issuance as i')
					->join('tbl_wbs_sakidashi_issuance_item as k','i.issuance_no','=','k.issuance_no')
					->whereRaw(" 1=1 ".$pono_cond.$issno_cond.$device_cond.$item_cond.$from_cond.$to_cond)
					->select('i.issuance_no as issuance_no',
							'k.item as item',
							'i.po_no as po',
							'k.issued_qty as qty',
							'k.lot_no as lot_no',
							'k.issuance_date as fdate',
							'k.item_desc as item_desc',
							'k.pair_no as pair_no',
							'i.incharge as incharge',
							'i.status')
					->get();

			if ($req->mode == 'dispatch') {
				$this->saki_dispatch($data);
			} else {
				$this->saki_summary($data);
			}
		}
		catch (Exception $e) {
			Log::error($e->getMessage());
		}
	}

	private function saki_dispatch($data)
	{
		$dt = Carbon::now();
		$date = substr($dt->format('Ymd'), 2);
		
		Excel::create('FOR_DISPATCH_SAKIDASHI_ISSUANCE_'.$date, function($excel) use($data)
		{
			$excel->sheet('Sheet1', function($sheet) use($data)
			{
				$sheet->cell('A1', "PORDER");
				$sheet->cell('B1', "CODE");
				$sheet->cell('C1', "MOTO");
				$sheet->cell('D1', "HOKAN");
				$sheet->cell('E1', "SEIBAN");
				$sheet->cell('F1', "BEDA");
				$sheet->cell('G1', "KVOL");
				$sheet->cell('H1', "PICKDATE");
				$sheet->cell('I1', "LOTNAME");
				$sheet->cell('J1', "TSLIP_NUM");
				$sheet->cell('K1', "NOTE");

				$row = 2;

				$sheet->setWidth(array(
					'B'     =>  15,
					'E'     =>  20
				));

				$sheet->setColumnFormat(array(
					'B'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
					'E'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT
				));

				foreach ($data as $key => $sk) {
					$sheet->cell('A'.$row, "");
					$sheet->setCellValueExplicit('B'.$row,  $sk->item, \PHPExcel_Cell_DataType::TYPE_STRING);
					//$sheet->cell('B'.$row, $sk->item);
					$sheet->cell('C'.$row, "WHS100");
					$sheet->cell('D'.$row, "ASSY100");
					$sheet->setCellValueExplicit('E'.$row,  $sk->po, \PHPExcel_Cell_DataType::TYPE_STRING);
					//$sheet->cell('E'.$row, $sk->po);
					$sheet->cell('F'.$row, "1");
					$sheet->cell('G'.$row, $sk->qty);
					$sheet->cell('H'.$row, $this->convertDate($sk->fdate,'Ymd'));
					$sheet->cell('I'.$row, $sk->lot_no);
					$sheet->cell('J'.$row, $sk->issuance_no);
					$sheet->cell('K'.$row, $sk->pair_no." / ".$sk->incharge);
					$sheet->cell('L'.$row, $sk->item_desc);
					$sheet->cell('M'.$row, $sk->status);
					$row++;
				}
			});

		})->download('xls');
	}

	private function saki_summary($data)
	{
		$dt = Carbon::now();
		$date = $dt->format('m-d-y');

		$com = new CommonController;
		$com_info = $com->getCompanyInfo();
		
		Excel::create('Sakidashi_Issuance_'.$date, function($excel) use($data,$com_info)
		{
			$excel->sheet('Report', function($sheet) use($data,$com_info)
			{
				$sheet->setHeight(1, 15);
				$sheet->mergeCells('A1:H1');
				$sheet->cells('A1:H1', function($cells) {
					$cells->setAlignment('center');
				});
				$sheet->cell('A1',$com_info['name']);

				$sheet->setHeight(2, 15);
				$sheet->mergeCells('A2:H2');
				$sheet->cells('A2:H2', function($cells) {
					$cells->setAlignment('center');
				});
				$sheet->cell('A2',$com_info['address']);

				$sheet->setHeight(4, 20);
				$sheet->mergeCells('A4:H4');
				$sheet->cells('A4:H4', function($cells) {
					$cells->setAlignment('center');
					$cells->setFont([
						'family'     => 'Calibri',
						'size'       => '14',
						'bold'       =>  true,
						'underline'  =>  true
					]);
				});
				$sheet->cell('A4',"SAKIDASHI ISSUANCE SUMMARY");

				$sheet->setHeight(6, 15);
				$sheet->cells('A6:H6', function($cells) {
					$cells->setFont([
						'family'     => 'Calibri',
						'size'       => '11',
						'bold'       =>  true,
					]);
					// Set all borders (top, right, bottom, left)
					$cells->setBorder('solid', 'solid', 'solid', 'solid');
				});
				$sheet->cell('A6', "Transaction");
				$sheet->cell('B6', "Created Date");
				$sheet->cell('C6', "PO No.");
				$sheet->cell('D6', "Item No.");
				$sheet->cell('E6', "Item Description");
				$sheet->cell('F6', "Issued Qty.");
				$sheet->cell('G6', "LOT No.");
				$sheet->cell('H6', "Pair No.");

				$row = 7;

				$sheet->setWidth(array(
					'D'     =>  15,
					'C'     =>  20
				));

				$sheet->setColumnFormat(array(
					'D'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
					'C'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT
				));

				foreach ($data as $key => $sk) {
					$sheet->setHeight($row, 15);
					$sheet->cell('A'.$row, $sk->issuance_no);
					$sheet->cell('B'.$row, $this->convertDate($sk->fdate,'m/d/Y h:i A'));
					$sheet->setCellValueExplicit('C'.$row,  $sk->po, \PHPExcel_Cell_DataType::TYPE_STRING);
					$sheet->setCellValueExplicit('D'.$row,  $sk->item, \PHPExcel_Cell_DataType::TYPE_STRING);
					// $sheet->cell('C'.$row, $sk->po);
					// $sheet->cell('D'.$row, $sk->item);
					$sheet->cell('E'.$row, $sk->item_desc);
					$sheet->cell('F'.$row, $sk->qty);
					$sheet->cell('G'.$row, $sk->lot_no);
					$sheet->cell('H'.$row, $sk->pair_no);
					$row++;
				}
				
				$sheet->cells('A6:H'.$row, function($cells) {
					$cells->setBorder('solid', 'solid', 'solid', 'solid');
				});
			});
		})->download('xls');
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

	public function wbsreportphyreport(Request $req)
	{
		$dt = Carbon::now();
		$date = substr($dt->format('Ymd'), 2);

		$com = new CommonController;
		$com_info = $com->getCompanyInfo();
		
		Excel::create('Actual_Inventory_'.$req->datefrom.'_'.$req->dateto, function($excel) use($req,$com_info)
		{
			$excel->sheet('Report', function($sheet) use($req,$com_info)
			{
				$sheet->setHeight(1, 15);
				$sheet->mergeCells('A1:K1');
				$sheet->cells('A1:K1', function($cells) {
					$cells->setAlignment('center');
				});
				$sheet->cell('A1',$com_info['name']);

				$sheet->setHeight(2, 15);
				$sheet->mergeCells('A2:K2');
				$sheet->cells('A2:K2', function($cells) {
					$cells->setAlignment('center');
				});
				$sheet->cell('A2',$com_info['address']);

				$sheet->setHeight(4, 20);
				$sheet->mergeCells('A4:K4');
				$sheet->cells('A4:K4', function($cells) {
					$cells->setAlignment('center');
					$cells->setFont([
						'family'     => 'Calibri',
						'size'       => '14',
						'bold'       =>  true,
						'underline'  =>  true
					]);
				});
				$sheet->cell('A4',"ACTUAL INVENTORY");

				$sheet->setHeight(6, 15);
				$sheet->cells('A6:K6', function($cells) {
					$cells->setFont([
						'family'     => 'Calibri',
						'size'       => '11',
						'bold'       =>  true,
					]);
					// Set all borders (top, right, bottom, left)
					$cells->setBorder('solid', 'solid', 'solid', 'solid');
				});
				$sheet->cell('A6',"Item No");
				$sheet->cell('B6',"Item Description");
				$sheet->cell('C6',"Location");
				$sheet->cell('D6',"WHS100");
				$sheet->cell('E6',"WHS102");
				$sheet->cell('F6',"WHSNON");
				$sheet->cell('G6',"WHSSM");
				$sheet->cell('H6',"WHSNG");
				$sheet->cell('I6',"Inventory");
				$sheet->cell('J6',"Actual");
				$sheet->cell('K6',"Variance");

				$datefrom = $req->datefrom;
				$dateto = $req->dateto;
				$location = $req->location;
				$field = '';
				if($location == ""){
					$field = DB::connection($this->wbs)->table('tbl_wbs_physical_inventory_details')
							->where('created_at','>=',$datefrom)
							->where('created_at','<=',$dateto)
							->get();
				}else{
					$field = DB::connection($this->wbs)->table('tbl_wbs_physical_inventory_details')
							->where('created_at','>=',$datefrom)
							->where('created_at','<=',$dateto)
							->where('location',$location)
							->get();    
				}

				
				$row = 7;
				foreach ($field as $key => $val) {
					$sheet->setHeight($row, 15);
					$sheet->cell('A'.$row, $val->item);
					$sheet->cell('B'.$row, $val->item_desc);
					$sheet->cell('C'.$row, $val->location);
					$sheet->cell('D'.$row, ($val->whs100 == 0)? '0.00' : $val->whs100);
					$sheet->cell('E'.$row, ($val->whs102 == 0)? '0.00' : $val->whs102);
					$sheet->cell('F'.$row, ($val->whsnon == 0)? '0.00' : $val->whsnon);
					$sheet->cell('G'.$row, ($val->whssm == 0)? '0.00' : $val->whssm);
					$sheet->cell('H'.$row, ($val->whsng == 0)? '0.00' : $val->whsng);
					$sheet->cell('I'.$row, ($val->variance == 0)? '0.00' : $val->variance);
					$sheet->cell('J'.$row, ($val->actual_qty == 0)? '0.00' : $val->actual_qty);
					$sheet->cell('K'.$row, ($val->variance == 0)? '0.00' : $val->variance);
					$row++;
				}
			});

		})->download('xls');
	}

	public function wbsreportwmireport(Request $req)
	{
		$ctr = 0;
		$value = null;
		$result = null;

		$from_cond = '';
		$to_cond = '';
		$pono_cond = '';
		$issno_cond = '';
		$item_cond ='';

		if(empty($req->pono))
		{
			$pono_cond = '';
		} else {
			$pono_cond = " AND pono = '" . $req->pono . "'";
		}

		# Create Issuance No. Condition
		if(empty($req->ctrl_no))
		{
			$issno_cond ='';
		} else {
			if ($req->mode == 'pmr') {
				$issno_cond = " AND w.request_no = '" . $req->ctrl_no . "'";
			} else {
				$issno_cond = " AND w.issuance_no = '" . $req->ctrl_no . "'";
			}
		}

		if(empty($req->item))
		{
			$item_cond ='';
		} else {
			$item_cond = " AND w.item = '" . $req->item . "'";
		}

		if (!empty($req->datefrom) && !empty($req->dateto)) {
			if ($req->mode == 'pmr') {
				$from_cond = "AND w.issued_date BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
			} else {
				$from_cond = "AND w.issued_date BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
			}
		} else {
			$from_cond = '';
			$to_cond = '';
		}

		$data = DB::connection($this->wbs)
				->select("SELECT w.issuance_no as issue_no, 
								w.request_no,
								w.item, 
								w.item_desc, 
								w.lot_no,
								w.issued_date, 
								SUM(w.issued_qty_t) as qty, 
								w.request_qty as req_qty,
								po1.pono as po,
								w.status as status,
								w.update_user
						FROM tbl_wbs_warehouse_mat_issuance_details as w
							LEFT JOIN tbl_request_summary as po1
							ON w.request_no = po1.transno
						WHERE w.issued_qty_t <> 0 ".$pono_cond.$issno_cond.$item_cond.$from_cond."
							GROUP BY w.issuance_no,w.item,w.lot_no
							ORDER BY w.request_no");

		
		$this->warehouse_issuance($data);
	}

	public function wbsreportpmrreport(Request $req)
	{
		$ctr = 0;
		$value = null;
		$result = null;

		$from_cond = '';
		$date_cond = '';
		$to_cond = '';
		$pono_cond = '';
		$issno_cond = '';
		$item_cond ='';

		if(empty($req->pono))
		{
			$pono_cond = '';
		} else {
			$pono_cond = " AND pono = '" . $req->pono . "'";
		}

		# Create Issuance No. Condition
		if(empty($req->ctrl_no))
		{
			$issno_cond ='';
		} else {
			if ($req->mode == 'pmr') {
				$issno_cond = " AND s.request_no = '" . $req->ctrl_no . "'";
			} else {
				$issno_cond = " AND s.issuance_no = '" . $req->ctrl_no . "'";
			}
		}

		if(empty($req->item))
		{
			$item_cond ='';
		} else {
			$item_cond = " AND d.code = '" . $req->item . "'";
		}

		if (!empty($req->datefrom) && !empty($req->dateto)) {
			if ($req->mode == 'pmr') {
				$from_cond = "AND DATE_FORMAT(rs.created_at,'%Y-%m-%d') BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
				$date_cond = "AND DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
			} else {
				$from_cond = "AND DATE_FORMAT(rs.created_at,'%Y-%m-%d') BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
				$date_cond = "AND DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
			}
		} else {
			$from_cond = '';
			$to_cond = '';
		}

		$data = DB::connection($this->wbs)
					->select("select d.transno as transno,
									d.whstransno as whstransno,
									rs.pono as pono,
									d.`code` as item,
									d.`name` as item_desc,
									ifnull(d.requestqty,'') as requestqty,
									ifnull(d.servedqty,0) as servedqty,
									d.classification as classification,
									d.requestedby as requestedby,
									DATE_FORMAT(rs.created_at,'%Y-%m-%d') as request_date,
									ifnull(d.last_served_date,'') as served_date,
									d.remarks as remarks
								from pmi_wbs_cn.tbl_request_detail as d
								left join (select transno,pono,created_at
											from pmi_wbs_cn.tbl_request_summary
											where 1=1 ".$date_cond."
											order by id desc) as rs
								on rs.transno = d.transno
								where 1=1 ".$pono_cond.$issno_cond.$item_cond.$from_cond."
								group by d.transno,
									d.whstransno,
									rs.pono,
									d.`code`,
									d.`name`,
									ifnull(d.requestqty,''),
									ifnull(d.servedqty,0),
									d.classification,
									d.requestedby,
									DATE_FORMAT(rs.created_at,'%Y-%m-%d'),
									ifnull(d.last_served_date,''),
									d.remarks");

		
		$this->production_request($data);
	}

	private function warehouse_issuance($data)
	{
		$dt = Carbon::now();
		$date = substr($dt->format('Ymd'), 2);
		
		Excel::create('FOR_DISPATCH_'.$date, function($excel) use($data)
		{
			$excel->sheet('Sheet1', function($sheet) use($data)
			{
				$sheet->cell('A1', "CODE");
				$sheet->cell('B1', "MOTO");
				$sheet->cell('C1', "HOKAN");
				$sheet->cell('D1', "SEIBAN");
				$sheet->cell('E1', "BEDA");
				$sheet->cell('F1', "JITU");
				$sheet->cell('G1', "JITU0");
				$sheet->cell('H1', "NOTE");
				$sheet->cell('I1', "LOTNAME");
				$sheet->cell('J1', "PICKDATE");
				$sheet->cell('K1', "CTRL_NO");
				$sheet->cell('L1', "REQ_NO");
				$sheet->cell('M1', "SERVED_BY");

				$row = 2;

				foreach ($data as $key => $wmi) {
					if ($wmi->qty > 0) {
						$sheet->cell('A'.$row, $wmi->item);
						$sheet->cell('B'.$row, "");
						$sheet->cell('C'.$row, "WHS100");
						$sheet->cell('D'.$row, $wmi->po);
						$sheet->cell('E'.$row, "1");
						$sheet->cell('F'.$row, $wmi->qty);
						$sheet->cell('G'.$row, "");
						$sheet->cell('H'.$row, "");
						$sheet->cell('I'.$row, $wmi->lot_no);
						$sheet->cell('J'.$row, $this->convertDate($wmi->issued_date,'Ymd'));
						$sheet->cell('K'.$row, $wmi->issue_no);
						$sheet->cell('L'.$row, $wmi->request_no);
						$sheet->cell('M'.$row, $wmi->update_user);
						$sheet->cell('N'.$row, $wmi->item_desc);
						$sheet->cell('O'.$row, $wmi->status);
						$row++;
					}
				}
			});

		})->download('xls');
	}

	private function production_request($data)
	{
		$dt = Carbon::now();
		$date = substr($dt->format('Ymd'), 2);

		//return dd($data);
		
		Excel::create('TXSLIPJITU_'.$date, function($excel) use($data)
		{
			$excel->sheet('Sheet1', function($sheet) use($data)
			{
				$sheet->cell('A1', "PORDER");
				$sheet->cell('B1', "PEDA");
				$sheet->cell('C1', "CODE");
				$sheet->cell('D1', "VENDOR");
				$sheet->cell('E1', "AKUBU");
				$sheet->cell('F1', "JITU");
				$sheet->cell('G1', "JITU0");
				$sheet->cell('H1', "NOTE");
				$sheet->cell('I1', "SEIBAN");
				$sheet->cell('J1', "HOKAN");
				$sheet->cell('K1', "CTRL_NO");
				$sheet->cell('L1', "DATE");
				$sheet->cell('M1', "DESC");
				$sheet->cell('N1', "REQ_BY");
				$sheet->cell('O1', "STATUS");
				$sheet->cell('P1', "REMARKS");
				$sheet->cell('Q1', "ACTUAL REQUEST");
				$sheet->cell('R1', "Serve DATE");

				$sheet->setAutoSize(false);

				$sheet->setWidth(array(
					'C'     =>  15,
					'I'     =>  20
				));

				$sheet->setColumnFormat(array(
					'C'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
					'I'     =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT
				));

				$row = 2;
				foreach ($data as $key => $pmr) {
					$sheet->cell('A'.$row, "");
					$sheet->cell('B'.$row, "0.00");

					$sheet->setCellValueExplicit('C'.$row, $pmr->item, \PHPExcel_Cell_DataType::TYPE_STRING);
					// $sheet->cell('C'.$row, $pmr->item);

					$vendor = $this->getVENDOR($pmr->item);

					$sheet->cell('D'.$row, $vendor);
					$sheet->cell('E'.$row, "B");
					$sheet->cell('F'.$row, ($pmr->servedqty == 0)? "0.00" : $pmr->servedqty);
					$sheet->cell('G'.$row, ($pmr->servedqty == 0)? "0.00" : $pmr->servedqty);
					$sheet->cell('H'.$row, $pmr->classification);

					$sheet->setCellValueExplicit('I'.$row, $pmr->pono, \PHPExcel_Cell_DataType::TYPE_STRING);
					//$sheet->cell('I'.$row, $pmr->pono);

					$sheet->cell('J'.$row, "ASSY100");
					$sheet->cell('K'.$row, $pmr->transno);
					$sheet->cell('L'.$row, $this->convertDate($pmr->request_date,'Ymd'));
					$sheet->cell('M'.$row, $pmr->item_desc);
					$sheet->cell('N'.$row, $pmr->requestedby);
					$sheet->cell('O'.$row, $this->getProdReqStatus($pmr->transno,$pmr->item));
					$sheet->cell('P'.$row, $pmr->remarks);
					$sheet->cell('Q'.$row, ($pmr->requestqty == 0)? "0.00" : $pmr->requestqty);
					$sheet->cell('R'.$row, $pmr->served_date);

					// $pmr
					$row++;
				}

				
			});
		})->download('xls');
	}

	private function getProdReqStatus($transno,$code)
	{
		$db = DB::connection($this->wbs)->table('tbl_wbs_warehouse_mat_issuance_details')
				->where('request_no',$transno)
				// ->where('pmr_detail_id',$detailid)
				->where('item',$code)
				->select('status')
				->count();
		if ($db > 0) {
			$db = DB::connection($this->wbs)->table('tbl_wbs_warehouse_mat_issuance_details')
					->where('request_no',$transno)
					// ->where('pmr_detail_id',$detailid)
					->where('item',$code)
					->select('status')
					->first();
			return $db->status;
		} else {
			return 'Open';
		}
	}

	private function getVENDOR($code)
	{
		$db = DB::connection($this->mssql)->table('XITEM')
				->select('VENDOR')
				->where('CODE',$code)
				->first();

		if (is_null($db)) {
			return '';
		}

		return $db->VENDOR;
	}

	private function newDate($time)
	{
		$old_date = date($time);             
		$old_date_timestamp = strtotime($old_date);
		$new_date = date('Ymd', $old_date_timestamp); 

		return $new_date;
	}

	private function convertDate($date,$format)
	{
		$time = strtotime($date);
		$newdate = date($format,$time);
		return $newdate;
	}

	public function wbsInventory()
	{
		$dt = Carbon::now();
		$date = substr($dt->format('Ymd'), 2);

		//return dd($data);
		
		Excel::create('WBS_INV_'.$date, function($excel)
		{
			$excel->sheet('Sheet1', function($sheet)
			{
				$sheet->cell('A1', "ITEM CODE");
				$sheet->cell('B1', "DESCRIPTION");
				$sheet->cell('C1', "QTY");
				$sheet->cell('D1', "LOT NUM");
				$sheet->cell('E1', "RECEIVED DATE");

				$row = 2;
				$data = DB::connection($this->wbs)->table('tbl_wbs_inventory')
							->select('item',
									'item_desc',
									DB::raw('SUM(qty) as qty'),
									'lot_no',
									'received_date')
							->groupBy('item',
									'item_desc',
									'lot_no',
									'received_date')
							->get();
				foreach ($data as $key => $inv) {
					$sheet->cell('A'.$row, $inv->item);
					$sheet->cell('B'.$row, $inv->item_desc);
					$sheet->cell('C'.$row, $inv->qty);
					$sheet->cell('D'.$row, $inv->lot_no);
					$sheet->cell('E'.$row, $inv->received_date);
					$row++;
				}
			});
		})->download('xls');
	}

	public function wbsfiforeasonlog(Request $req)
	{
		// $com = new CommonController;
		$datefrom = $req->datefrom;
		$dateto = $req->dateto;
		  
		$from_cond = '';
		$to_cond = '';
	 
		if (!empty($datefrom) && !empty($dateto)) {

			$from_cond = " created_at BETWEEN '" . $datefrom . "' AND '" .$dateto. "'";

		} else {
			$from_cond = '';
			$to_cond = '';
		}
		 // dd($this->wbs);  
		$data = DB::connection($this->wbs)
					->select("SELECT * FROM pmi_wbs_cn.fifologs
							WHERE ".$from_cond." and reason_id <> '' and approved_id <> '' 
							 ORDER BY created_at DESC");
					
		$this->wbs_fifo($data);
	}

	private function wbs_fifo($data)
	{

		Excel::create('Fifo_Reason_Logs', function($excel) use($data)
		{
			$excel->sheet('Sheet1', function($sheet) use($data)
			{
				$datefrom = '';
				$dateto = '';

				if ($datefrom !== '') {
					$datefrom = $this->com->convertDate('Y-m-d');
					$dateto = $this->com->convertDate('Y-m-d');

					$date_cond = " created_at BETWEEN '".$datefrom."' AND '".$dateto."'";

				}
				$sheet->cell('A1', "issuance_no");
				$sheet->cell('B1', "item");
				$sheet->cell('C1', "item_desc");
				$sheet->cell('D1', "lot_no");
				$sheet->cell('E1', "remarks");
				$sheet->cell('F1', "create_user");
				$sheet->cell('G1', "reason_id");
				$sheet->cell('H1', "approved_id");
				$sheet->cell('I1', "created_at");

				$row = 2;
				foreach ($data as $key => $fifo) {

					$sheet->cell('A'.$row, $fifo->issuance_no);
					$sheet->cell('B'.$row, $fifo->item);
					$sheet->cell('C'.$row, $fifo->item_desc);
					$sheet->cell('D'.$row, $fifo->lot_no);
					$sheet->cell('E'.$row, $fifo->remarks);
					$sheet->cell('F'.$row, $fifo->create_user);
					$sheet->cell('G'.$row, $fifo->reason_id);
					$sheet->cell('H'.$row, $fifo->approved_id);
					$sheet->cell('I'.$row, $fifo->created_at);


					$row++;
				}
			});
		})->download('xls');

	}

	public function wbsreportcheckfifoexist(Request $req)
	{

		$datefrom = $req->datefrom;
		$dateto = $req->dateto;
		$data = DB::connection($this->wbs)
					->select("SELECT *
					FROM pmi_wbs_cn.tbl_wbs_kit_issuance 
					WHERE created_at BETWEEN  '".$datefrom."' AND  '".$dateto."'");

		// dd($data);
		return $data;
	}
}
