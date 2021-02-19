<?php
namespace App\Http\Controllers\QCDB;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Config;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth; #Auth facade
use Excel;
use PDF;

class FGSController extends Controller
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

	public function getFGS()
	{
		$this->com = new CommonController;
		if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_FGSDB'), $userProgramAccess))
		{
			return redirect('/home');
		}
		else
		{
			return view('qcdb.fgs',['userProgramAccess' => $userProgramAccess]);
		}
	}

	public function getfgsYPICSrecords(Request $req)
	{
		$query = DB::connection($this->mssql)
					->select("SELECT R.SORDER as PO,
							  R.CODE as devicecode,
							  H.NAME as DEVNAME,
							  SUM(R.KVOL) as POQTY
					  FROM XRECE as R
					  LEFT JOIN XHEAD as H ON R.CODE = H.CODE
					  WHERE R.SORDER = '".$req->pono."'
					  GROUP BY R.SORDER,
							  R.CODE,
							  H.NAME");

		return response()->json($query);
	}

	public function getFGSdata(Request $req)
	{
		$query = DB::connection($this->mysql)
					->table('fgs')
					->orderBy('id','desc')
					->select([
						'id',
						'po_no',
						'date',
						'device_name',
						'qty',
						'total_num_of_lots',
						'dbcon'
					]);

		// if ($req->datefrom !== "" || !is_null($req->datefrom)) {
		// 	$date = " AND `date` BETWEEN '".$this->convertDate($req->datefrom,'Y-m-d')."' AND '".$this->convertDate($req->dateto,'Y-m-d')."'";
		// }

		// if ($req->mode == 'group') {
		// 	if ($req->g1 !== "" && $req->g2 !== "" && $req->g3 !== "") {
		// 		$group .= $req->g1 . ", " . $req->g2 . ", " .$req->g3;
		// 	}

		// 	if ($req->g1 == "" && $req->g2 == "" && $req->g3 == "") {
		// 		$group = "";
		// 	}

		// 	if ($group == "") {
		// 		$query = DB::connection($this->mysql)
		// 					->table('fgs')
		// 					->whereRaw("1=1".$date)
		// 					->orderBy('id','desc')
		// 					->groupBy($req->g1)
		// 					->select([
		// 						'id',
		// 						'po_no',
		// 						'date',
		// 						'device_name',
		// 						DB::raw("SUM(qty) as qty"),
		// 						DB::raw("SUM(total_num_of_lots) as total_num_of_lots"),
		// 						'dbcon'
		// 					]);
		// 	}



		// 	if($req->g1 !==""){
		// 		$query = DB::connection($this->mysql)->table('fgs')
		// 						->whereRaw("1=1".$date)
		// 						->orderBy('id','desc')
		// 						->groupBy($req->g1)
		// 						->select([
		// 							'id',
		// 							'po_no',
		// 							'date',
		// 							'device_name',
		// 							DB::raw("SUM(qty) as qty"),
		// 							DB::raw("SUM(total_num_of_lots) as total_num_of_lots"),
		// 							'dbcon'
		// 						]);
		// 	}
		// 	if($req->g2 !==""){
		// 		$tableData = DB::connection($this->mysql)->table('fgs')
		// 					->whereRaw("1=1".$date)
		// 					->orderBy('id','desc')
		// 					->groupBy($req->g1,$req->g2)
		// 					->select([
		// 						'id',
		// 						'po_no',
		// 						'date',
		// 						'device_name',
		// 						DB::raw("SUM(qty) as qty"),
		// 						DB::raw("SUM(total_num_of_lots) as total_num_of_lots"),
		// 						'dbcon'
		// 					]);
		// 	}
		// 	if($req->g3 !==""){
		// 		$tableData = DB::connection($this->mysql)->table('fgs')
		// 					->whereRaw("1=1".$date)
		// 					->orderBy('id','desc')
		// 					->groupBy($req->g1,$req->g2,$req->g3)
		// 					->select([
		// 						'id',
		// 						'po_no',
		// 						'date',
		// 						'device_name',
		// 						DB::raw("SUM(qty) as qty"),
		// 						DB::raw("SUM(total_num_of_lots) as total_num_of_lots"),
		// 						'dbcon'
		// 					]);
		// 	}
		// }

		// if ($req->mode == 'search') {
		// 	$date="";
		// 	$pono="";

		// 	if ($req->datefrom !== "") {
		// 		$date=" AND date BETWEEN '".$this->convertDate($req->datefrom,'Y-m-d')."' AND '".$this->convertDate($req->dateto,'Y-m-d')."'";
		// 	}

		// 	if ($req->pono !== "") {
		// 		$date=" AND po_no = '".$req->pono."'";
		// 	}

		// 	$tableData = DB::connection($this->mysql)->table('fgs')
		// 				->whereRaw("1=1".$date.$pono)
		// 				->orderBy('id','desc')
		// 				->select([
		// 					'id',
		// 					'po_no',
		// 					'date',
		// 					'device_name',
		// 					'qty',
		// 					'total_num_of_lots',
		// 					'dbcon'
		// 				]);
		// }

		// return response()->json($query);
		return Datatables::of($query)
						->editColumn('id', function($data) {
							return $data->id;
						})
						->addColumn('action', function($data) {
							return '<button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="'.$data->id.'|'.$data->po_no.'|'.$data->date.'|'.$data->device_name.'|'.$data->qty.'|'.$data->total_num_of_lots.'|'.$data->dbcon.'"><i class="fa fa-edit"></i></button>';
						})
						->make(true);
	}

	public function fgsSearch(Request $req)
	{
		$pono_cond = '';
		$date_cond = '';

		if(!empty($req->pono))
		{
			$pono_cond = " AND po_no = '" . $req->pono . "'";
		}

		if (!empty($req->datefrom) && !empty($req->dateto)) {
			$date_cond = " AND `date` BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
		}

		$query = DB::connection($this->mysql)
					->table('fgs')
					->orderBy('id','desc')
					->whereRaw(" 1=1 ".$pono_cond.$date_cond)
					->select([
						'id',
						'po_no',
						'date',
						'device_name',
						'qty',
						'total_num_of_lots',
						'dbcon'
					]);
		//return response()->json($query);
		return Datatables::of($query)
						->editColumn('id', function($data) {
							return $data->id;
						})
						->addColumn('action', function($data) {
							return '<button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="'.$data->id.'|'.$data->po_no.'|'.$data->date.'|'.$data->device_name.'|'.$data->qty.'|'.$data->total_num_of_lots.'|'.$data->dbcon.'"><i class="fa fa-edit"></i></button>';
						})
						->make(true);
	}

	public function fgsGroupBy($value='')
	{
		$date_cond = '';
		$data = [];

		if (!empty($req->datefrom) && !empty($req->dateto)) {
			$date_cond = " AND `date` BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
		}


		$query = DB::connection($this->mysql)
					->table('fgs')
					->orderBy('id','desc')
					->whereRaw(" 1=1 ".$date_cond)
					->select([
						'id',
						'po_no',
						'date',
						'device_name',
						DB::raw("SUM(qty) as qty"),
						DB::raw("SUM(total_num_of_lots) as total_num_of_lots"),
						'dbcon'
					]);
		$data = $query;

		if (!empty($req->g1)) {
			$data = $query->groupBy($req->g1);
		}

		if (!empty($req->g2)) {
			$data = $query->groupBy($req->g1,$req->g2);
			if (!empty($req->g1)) {
				$data = $query->groupBy($req->g2);
			}
		}

		if (!empty($req->g3)) {
			$data = $query->groupBy($req->g1,$req->g2,$req->g3);
			if (!empty($req->g1) && !empty($req->g2)) {
				$data = $query->groupBy($req->g3);
			}
		}

		return Datatables::of($data)
						->editColumn('id', function($data) {
							return $data->id;
						})
						->addColumn('action', function($data) {
							return '<button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="'.$data->id.'|'.$data->po_no.'|'.$data->date.'|'.$data->device_name.'|'.$data->qty.'|'.$data->total_num_of_lots.'|'.$data->dbcon.'"><i class="fa fa-edit"></i></button>';
						})
						->make(true);
	}

	public function fgsSave(Request $req)
	{
		$data = [
			'msg' => 'Saving FGS data was failed.',
			'status' => 'failed'
		];

		$query = false;

		if ($req->hd_status !== '' && $req->hd_status == 'ADD') {
			$query = DB::connection($this->mysql)->table('fgs')
						->insert([
							'date' => $this->convertDate($req->date,'Y-m-d'),
							'po_no' => $req->po_no,
							'device_name' => $req->device_name,
							'qty' => $req->quantity,
							'total_num_of_lots' => $req->total_lots,
							'dbcon' => 'CN',
							'create_user' => Auth::user()->user_id,
							'created_at' => Carbon::now(),
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now()
						]);
		} else {
			$query = DB::connection($this->mysql)->table('fgs')
						->where('id','=',$req->id)
						->update([
							'date' => $this->convertDate($req->date,'Y-m-d'),
							'po_no' => $req->po_no,
							'device_name' => $req->device_name,
							'qty' => $req->quantity,
							'total_num_of_lots' => $req->total_lots,
							'update_user' => Auth::user()->user_id,
							'updated_at' => Carbon::now()
						]);
		}

		if ($query) {
			$data = [
				'msg' => 'Data was successfully saved.',
				'status' => 'success'
			];
		}

		return $data;	
	}

	public function fgsDelete(Request $req)
	{
		$data = [
			'msg' => ' Deleting data was failed.',
			'status' => 'failed'
		];

		$query = false;

		if (count($req->ids) > 0) {
			$query = DB::connection($this->mysql)
						->table('fgs')
						->wherein('id',$req->ids)
						->delete();
		 }

		 if ($query) {
			$data = [
				'msg' => 'Data was successfully deleted.',
				'status' => 'success'
			];
		 }

		 return response()->json($data);
	}

	private function convertDate($date,$format)
	{
		$time = strtotime($date);
		$newdate = date($format,$time);
		return $newdate;
	}

	public function ReportDataCheck(Request $req)
    {
        $check;
        $data = [];

        $pono_cond = '';
		$date_cond = '';

		if(!empty($req->pono))
		{
			$pono_cond = " AND po_no = '" . $req->pono . "'";
		}

		if (!empty($req->datefrom) && !empty($req->dateto)) {
			$date_cond = " AND `date` BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
		}

        if ($req->report_type == 'search') {
            $check = DB::connection($this->mysql)
						->table('fgs')
						->orderBy('id','desc')
						->whereRaw(" 1=1 ".$pono_cond.$date_cond)
						->select(
							'id',
							'po_no',
							'date',
							'device_name',
							'qty',
							'total_num_of_lots',
							'dbcon'
						);

        } else {
            $query = DB::connection($this->mysql)
					->table('fgs')
					->orderBy('id','desc')
					->whereRaw(" 1=1 ".$date_cond)
					->select(
						'id',
						'po_no',
						'date',
						'device_name',
						DB::raw("SUM(qty) as qty"),
						DB::raw("SUM(total_num_of_lots) as total_num_of_lots"),
						'dbcon'
					);
			$check = $query;

			if (!empty($req->g1)) {
				$check = $query->groupBy($req->g1);
			}

			if (!empty($req->g2)) {
				$check = $query->groupBy($req->g1,$req->g2);
				if (!empty($req->g1)) {
					$check = $query->groupBy($req->g2);
				}
			}

			if (!empty($req->g3)) {
				$check = $query->groupBy($req->g1,$req->g2,$req->g3);
				if (!empty($req->g1) && !empty($req->g2)) {
					$check = $query->groupBy($req->g3);
				}
			}
        }

        $data = ['DataCount' => count($check->get())];

        return response()->json($data);
    }

    public function fgsReport(Request $req)
    {
    	$data = 0;
        $data = [];

        $pono_cond = '';
		$date_cond = '';

		if(!empty($req->pono))
		{
			$pono_cond = " AND po_no = '" . $req->pono . "'";
		}

		if (!empty($req->datefrom) && !empty($req->dateto)) {
			$date_cond = " AND `date` BETWEEN '" . $req->datefrom . "' AND '" . $req->dateto . "'";
		}

        if ($req->report_type == 'search') {
            $data = DB::connection($this->mysql)
						->table('fgs')
						->orderBy('id','desc')
						->whereRaw(" 1=1 ".$pono_cond.$date_cond)
						->select(
							'id',
							'po_no',
							'date',
							'device_name',
							'qty',
							'total_num_of_lots',
							'dbcon'
						);
        } else {
            $query = DB::connection($this->mysql)
					->table('fgs')
					->orderBy('id','desc')
					->whereRaw(" 1=1 ".$date_cond)
					->select(
						'id',
						'po_no',
						'date',
						'device_name',
						DB::raw("SUM(qty) as qty"),
						DB::raw("SUM(total_num_of_lots) as total_num_of_lots"),
						'dbcon'
					);
			$data = $query;

			if (!empty($req->g1)) {
				$data = $query->groupBy($req->g1);
			}

			if (!empty($req->g2)) {
				$data = $query->groupBy($req->g1,$req->g2);
				if (!empty($req->g1)) {
					$data = $query->groupBy($req->g2);
				}
			}

			if (!empty($req->g3)) {
				$data = $query->groupBy($req->g1,$req->g2,$req->g3);
				if (!empty($req->g1) && !empty($req->g2)) {
					$data = $query->groupBy($req->g3);
				}
			}
        }

        if ($req->mode == 'pdf') {
        	return $this->PDFReport($data->get());
        } else {
        	return $this->ExcelReport($data->get());
        }
    }

	private function PDFReport($data)
	{ 
		$dt = Carbon::now();
        $company_info = $this->com->getCompanyInfo();
        $date = substr($dt->format('  M j, Y  h:i A '), 2);

        $arrData = [
            'company_info' => $company_info,
            'details' => $data,
            'date' => $date,
        ];

        $pdf = PDF::loadView('pdf.fgs', $arrData)
                    ->setPaper('A4')
                    ->setOption('margin-top', 10)
                    ->setOption('margin-bottom', 5)
                    ->setOption('margin-left', 1)
                    ->setOption('margin-right', 1)
                    ->setOrientation('landscape');

        return $pdf->inline('FGS_Inspection_'.Carbon::now());
	}

	private function ExcelReport($data)
	{
		$dt = Carbon::now();
		$date = substr($dt->format('Ymd'), 2);
		Excel::create('FGS'.$date, function($excel) use($data)
		{
			$excel->sheet('Sheet1', function($sheet) use($data)
			{
				$dt = Carbon::now();
				$date = $dt->format('m/d/Y');

				$sheet->setCellValue('A1', 'CN OQC FGS');
				$sheet->mergeCells('A1:G1');

				$sheet->cell('B3', function($cell) {
					$cell->setValue("Date");
					$cell->setBorder('thin','thin','thin','thin');
				});

				$sheet->cell('C3', function($cell) {
					$cell->setValue("P.O. Number");
					$cell->setBorder('thin','thin','thin','thin');
				});

				$sheet->cell('D3', function($cell) {
					$cell->setValue("Device Name");
					$cell->setBorder('thin','thin','thin','thin');
				});

				$sheet->cell('E3', function($cell) {
					$cell->setValue("Quantity");
					$cell->setBorder('thin','thin','thin','thin');
				});

				$sheet->cell('F3', function($cell) {
					$cell->setValue("Total No. of Lots");
					$cell->setBorder('thin','thin','thin','thin');
				});

				$sheet->setHeight([
					1 => 30,
					3 => 20
				]);

				$sheet->row(1, function ($row) {
					$row->setFontFamily('Calibri');
					$row->setBackground('#ADD8E6');
					$row->setFontSize(15);
					$row->setAlignment('center');
				});

				$sheet->row(3, function ($row) {
					$row->setFontFamily('Calibri');
					$row->setBackground('#ADD8E6');
					$row->setFontSize(10);
					$row->setAlignment('center');
				});

				$sheet->setStyle([
					'font' => [
						'name'  =>  'Calibri',
						'size'  =>  10
					]
				]);

				$row = 4;

				$lot_qty = 0;

				foreach ($data as $key => $d) {
					$lot_qty += $d->qty;

					$sheet->cell('B'.$row, function($cell) use($d) {
						$cell->setValue($d->date);
						$cell->setBorder('thin','thin','thin','thin');
					});

					$sheet->cell('C'.$row, function($cell) use($d) {
						$cell->setValue($d->po_no);
						$cell->setBorder('thin','thin','thin','thin');
					});

					$sheet->cell('D'.$row, function($cell) use($d) {
						$cell->setValue($d->device_name);
						$cell->setBorder('thin','thin','thin','thin');
					});

					$sheet->cell('E'.$row, function($cell) use($d) {
						$cell->setValue($d->qty);
						$cell->setBorder('thin','thin','thin','thin');
					});

					$sheet->cell('F'.$row, function($cell) use($d) {
						$cell->setValue($d->total_num_of_lots);
						$cell->setBorder('thin','thin','thin','thin');
					});

					$sheet->row($row, function ($row) {
						$row->setFontFamily('Calibri');
						$row->setFontSize(10);
						$row->setAlignment('center');
					});

					$sheet->setHeight($row,20);
					$row++;
				}

				$row += 1;

				$sheet->row($row, function ($row) {
					$row->setFontFamily('Calibri');
					$row->setFontSize(10);
					$row->setAlignment('center');
				});

				$sheet->setHeight($row,20);

				$sheet->cell('B'.$row, "Total Qty:");
				$sheet->cell('C'.$row, $lot_qty);

				$sheet->cell('E'.$row, "Date:");
				$sheet->cell('F'.$row, Carbon::now());
			});
		})->download('xls');
	}




	public function getFGSreportexcel(Request $request)
	{ 
		$data = json_decode($request->data);
		$status = $data->status;

		if ($status == "SEARCH") {
			try
			{ 
				$dt = Carbon::now();
				$date = substr($dt->format('Ymd'), 2);
				  
				Excel::create('FGS'.$date, function($excel) use($request)
				{
				   $excel->sheet('Sheet1', function($sheet) use($request)
				   {
					  $datefrom = $request->datefrom;
					  $dateto = $request->dateto;
					  $dt = Carbon::now();
					  $date = $dt->format('m/d/Y');
		   
					  $sheet->setCellValue('A1', 'TS OQC FGS');
					  $sheet->mergeCells('A1:G1');

					  $sheet->cell('B3',"Date");
					  $sheet->cell('C3',"P.O. Number");
					  $sheet->cell('D3',"Device Name");
					  $sheet->cell('E3',"Quantity");
					  $sheet->cell('F3',"Total No. of Lots");

					  $sheet->setHeight(array(
						 1=>30,
						 3=>20
					  ));
					  $sheet->row(1, function ($row) {
						 $row->setFontFamily('Calibri');
						 $row->setBackground('#ADD8E6');
						 $row->setFontSize(15);
						 $row->setAlignment('center');
					  });
					  $sheet->row(3, function ($row) {
						 $row->setFontFamily('Calibri');
						 $row->setBackground('#ADD8E6');
						 $row->setFontSize(10);
						 $row->setAlignment('center');
					  });
					  $sheet->setStyle(array(
						 'font' => array(
						 'name'  =>  'Calibri',
						 'size'  =>  10
						 )
					  ));
					  $data = json_decode($request->data);
					  $date_inspected = $data->date_inspected;
					  $pono = $data->pono;
					  $device_name = $data->device_name;
					  $qty = $data->qty;
					  $total_lots = $data->total_lots;  
					  $searchpono = $data->searchpono;

					  $row = 4;
					  $field = DB::connection($this->mysql)->table('fgs')->get();
					  foreach ($pono as $key => $val) {
						 $sheet->cell('B'.$row, $date_inspected[$key]);
						 $sheet->cell('C'.$row, $pono[$key]);
						 $sheet->cell('D'.$row, $device_name[$key]);
						 $sheet->cell('E'.$row, $qty[$key]);
						 $sheet->cell('F'.$row, $total_lots[$key]);
						
						 $sheet->row($row, function ($row) {
							$row->setFontFamily('Calibri');
							$row->setFontSize(10);
							$row->setAlignment('center');
						 });
						 $sheet->setHeight($row,20);
						 $row++;
					  }
					  $sheet->row($row, function ($row) {
						 $row->setFontFamily('Calibri');
						 $row->setFontSize(10);
						 $row->setAlignment('center');
					  });
					  $sheet->setHeight($row,20);
					 
					  $sheet->cell('B'.$row, "Date:");
					  $sheet->cell('C'.$row, Carbon::now());
				   });

				})->download('xls');
		 } catch (Exception $e) {
			return redirect(url('/fgs'))->with(['err_message' => $e]);
		 }       
	  }else{
		 try
		 { 
			$dt = Carbon::now();
			$date = substr($dt->format('Ymd'), 2);
			  
			Excel::create('FGS'.$date, function($excel) use($request)
			{
			   $excel->sheet('Sheet1', function($sheet) use($request)
			   {
				  $datefrom = $request->datefrom;
				  $dateto = $request->dateto;
				  $dt = Carbon::now();
				  $date = $dt->format('m/d/Y');
	   
				  $sheet->setCellValue('A1', 'TS OQC FGS');
				  $sheet->mergeCells('A1:G1');

				  $sheet->cell('B3',"Date");
				  $sheet->cell('C3',"P.O. Number");
				  $sheet->cell('D3',"Device Name");
				  $sheet->cell('E3',"Quantity");
				  $sheet->cell('F3',"Total No. of Lots");

				  $sheet->setHeight(array(
					 1=>30,
					 3=>20
				  ));
				  $sheet->row(1, function ($row) {
					 $row->setFontFamily('Calibri');
					 $row->setBackground('#ADD8E6');
					 $row->setFontSize(15);
					 $row->setAlignment('center');
				  });
				  $sheet->row(3, function ($row) {
					 $row->setFontFamily('Calibri');
					 $row->setBackground('#ADD8E6');
					 $row->setFontSize(10);
					 $row->setAlignment('center');
				  });
				  $sheet->setStyle(array(
					 'font' => array(
					 'name'  =>  'Calibri',
					 'size'  =>  10
					 )
				  ));
				  $data = json_decode($request->data);
				  $date_inspected = $data->date_inspected;
				  $pono = $data->pono;
				  $device_name = $data->device_name;
				  $qty = $data->qty;
				  $total_lots = $data->total_lots;  
				  $searchpono = $data->searchpono;

				  $row = 4;
				  $field = DB::connection($this->mysql)->table('fgs')->get();
				  foreach ($pono as $key => $val) {
					 $sheet->cell('B'.$row, $date_inspected[$key]);
					 $sheet->cell('C'.$row, $pono[$key]);
					 $sheet->cell('D'.$row, $device_name[$key]);
					 $sheet->cell('E'.$row, $qty[$key]);
					 $sheet->cell('F'.$row, $total_lots[$key]);
					
					 $sheet->row($row, function ($row) {
						$row->setFontFamily('Calibri');
						$row->setFontSize(10);
						$row->setAlignment('center');
					 });
					 $sheet->setHeight($row,20);
					 $row++;
				  }
				  $sheet->row($row, function ($row) {
					 $row->setFontFamily('Calibri');
					 $row->setFontSize(10);
					 $row->setAlignment('center');
				  });
				  $sheet->setHeight($row,20);
			  
				  $sheet->cell('B'.$row, "Date:");
				  $sheet->cell('C'.$row, Carbon::now());
			   });

			})->download('xls');
		 } catch (Exception $e) {
			return redirect(url('/fgs'))->with(['err_message' => $e]);
		 }       
	  }   
	}

	public function fgsdbgroupby(Request $request)
	{        
		/*$data = array_filter($request->input('data'));*/
		//$fields = "'".implode("','",$data)."'";
		$data = $request->data;
		$datefrom = $request->data['datefrom'];
		$dateto = $request->data['dateto'];
		$g1 = $request->data['g1'];
		$g2 = $request->data['g2'];
		$g3 = $request->data['g3'];
		$field='';
		if($g1){
			if($datefrom == "" && $dateto == ""){
				$field = DB::connection($this->mysql)->table('fgs')
				->select('date','po_no','device_name',DB::raw("SUM(qty) as qty"),DB::raw("SUM(total_num_of_lots) as total_num_of_lots"))
				->groupBy($g1)
				->get();    
			} else {
				$field = DB::connection($this->mysql)->table('fgs')
				->whereBetween('date',[$datefrom, $dateto])
				->select('date','po_no','device_name',DB::raw("SUM(qty) as qty"),DB::raw("SUM(total_num_of_lots) as total_num_of_lots"))
				->groupBy($g1)
				->get();        
			}    
		}
		if($g2){
			if($datefrom == "" && $dateto == ""){
				$field = DB::connection($this->mysql)->table('fgs')
				->select('date','po_no','device_name',DB::raw("SUM(qty) as qty"),DB::raw("SUM(total_num_of_lots) as total_num_of_lots"))
				->groupBy($g1,$g2)
				->get();   
			} else {
				$field = DB::connection($this->mysql)->table('fgs')
				->whereBetween('date',[$datefrom, $dateto])
				->select('date','po_no','device_name',DB::raw("SUM(qty) as qty"),DB::raw("SUM(total_num_of_lots) as total_num_of_lots"))
				->groupBy($g1,$g2)
				->get();       
			}
			
		}
		if($g3){
			if($datefrom =="" && $dateto == ""){
				$field = DB::connection($this->mysql)->table('fgs')
				->select('date','po_no','device_name',DB::raw("SUM(qty) as qty"),DB::raw("SUM(total_num_of_lots) as total_num_of_lots"))
				->groupBy($g1,$g2,$g3)
				->get();       
			} else {
				$field = DB::connection($this->mysql)->table('fgs')
				->whereBetween('date',[$datefrom, $dateto])
				->select('date','po_no','device_name',DB::raw("SUM(qty) as qty"),DB::raw("SUM(total_num_of_lots) as total_num_of_lots"))
				->groupBy($g1,$g2,$g3)
				->get();      
			}    
		}
		
		return $field;
	}
   
	public function fgsdbselectgroupby1(Request $request)
	{        
		$g1 = $request->data;
		$table = DB::connection($this->mysql)->table('fgs')
				->select($g1)
				->distinct()
				->get();

		return $table;
	}

}
