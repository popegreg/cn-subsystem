<?php

namespace App\Http\Controllers\QCDB;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
use DB;
use Config;
use PDF;
use Carbon\Carbon;
use Excel;

class OQCGroupByController extends Controller
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
        if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_OQCDB')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('qcdb.oqc_groupby',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function CalculateDPPM(Request $req)
    {
        $g1 = (!isset($req->field1) || $req->field1 == '' || $req->field1 == null)? '': $req->field1;
        $g2 = (!isset($req->field2) || $req->field2 == '' || $req->field2 == null)? '': $req->field2;
        $g3 = (!isset($req->field3) || $req->field3 == '' || $req->field3 == null)? '': $req->field3;
        $content1 = (!isset($req->content1) || $req->content1 == '' || $req->content1 == null)? '%': $req->content1;
        $content2 = (!isset($req->content2) || $req->content2 == '' || $req->content2 == null)? '%': $req->content2;
        $content3 = (!isset($req->content3) || $req->content3 == '' || $req->content3 == null)? '%': $req->content3;

        DB::connection($this->mysql)
            ->select(
                DB::raw(
                    "CALL GetOQCGroupBy(
                    '".$this->com->convertDate($req->gfrom,'Y-m-d')."',
                    '".$this->com->convertDate($req->gto,'Y-m-d')."',
                    '".$g1."',
                    '".$content1."',
                    '".$g2."',
                    '".$content2."',
                    '".$g3."',
                    '".$content3."')"
                )
            );

        $data = [];
        $node1 = [];
        $node2 = [];
        $node3 = [];
        $details = [];

        $check = DB::connection($this->mysql)->table('oqc_inspection_group')->count();

        if ($check > 0) {
            if ($g1 !== '') {
                $grp1_query = DB::connection($this->mysql)->table('oqc_inspection_group')
                                ->select('g1','L1','DPPM1')
                                ->groupBy($g1)
                                ->orderBy('g1')
                                ->get();
                
                foreach ($grp1_query as $key => $gr1) {
                    if ($g2 == '') {
                        $details_query = DB::connection($this->mysql)->table('oqc_inspection_group')
                                        ->select('assembly_line',
                                                'lot_no',
                                                'app_date',
                                                'app_time',
                                                'prod_category',
                                                'po_no',
                                                'device_name',
                                                'customer',
                                                'po_qty',
                                                'family',
                                                'type_of_inspection',
                                                'severity_of_inspection',
                                                'inspection_lvl',
                                                'aql',
                                                'accept',
                                                'reject',
                                                'date_inspected',
                                                'ww',
                                                'fy',
                                                'time_ins_from',
                                                'time_ins_to',
                                                'shift',
                                                'inspector',
                                                'submission',
                                                'coc_req',
                                                'judgement',
                                                'lot_qty',
                                                'sample_size',
                                                'lot_inspected',
                                                'lot_accepted',
                                                'num_of_defects',
                                                'remarks',
                                                'type',
                                                'modid')
                                        ->where('g1',$gr1->g1)
                                        ->get();

                        array_push($node1, [
                            'group' => $gr1->g1,
                            'LAR' => $gr1->L1,
                            'DPPM' => $gr1->DPPM1,
                            'field' => $g1,
                            'details' => $details_query
                        ]);
                    } else {

                        $grp2_query = DB::connection($this->mysql)->table('oqc_inspection_group')
                                        ->select('g1','g2','L2','DPPM2')
                                        ->where('g1',$gr1->g1)
                                        ->groupBy($g2)
                                        ->orderBy('g2')
                                        ->get();

                        foreach ($grp2_query as $key => $gr2) {
                            if ($g3 == '') {
                                $details_query = DB::connection($this->mysql)->table('oqc_inspection_group')
                                                    ->select('assembly_line',
                                                            'lot_no',
                                                            'app_date',
                                                            'app_time',
                                                            'prod_category',
                                                            'po_no',
                                                            'device_name',
                                                            'customer',
                                                            'po_qty',
                                                            'family',
                                                            'type_of_inspection',
                                                            'severity_of_inspection',
                                                            'inspection_lvl',
                                                            'aql',
                                                            'accept',
                                                            'reject',
                                                            'date_inspected',
                                                            'ww',
                                                            'fy',
                                                            'time_ins_from',
                                                            'time_ins_to',
                                                            'shift',
                                                            'inspector',
                                                            'submission',
                                                            'coc_req',
                                                            'judgement',
                                                            'lot_qty',
                                                            'sample_size',
                                                            'lot_inspected',
                                                            'lot_accepted',
                                                            'num_of_defects',
                                                            'remarks',
                                                            'type',
                                                            'modid')
                                                    ->where('g1',$gr1->g1)
                                                    ->where('g2',$gr2->g2)
                                                    ->get();
                                array_push($node2, [
                                    'g1' => $gr1->g1,
                                    'group' => $gr2->g2,
                                    'LAR' => $gr2->L2,
                                    'DPPM' => $gr2->DPPM2,
                                    'field' => $g2,
                                    'details' => $details_query
                                ]);
                            } else {

                               $grp3_query = DB::connection($this->mysql)->table('oqc_inspection_group')
                                                ->select('g1','g2','g3','L3','DPPM3')
                                                ->where('g1',$gr1->g1)
                                                ->where('g2',$gr2->g2)
                                                ->groupBy($g3)
                                                ->orderBy('g3')
                                                ->get();

                                foreach ($grp3_query as $key => $gr3) {
                                    $details_query = DB::connection($this->mysql)->table('oqc_inspection_group')
                                                        ->select('assembly_line',
                                                                'lot_no',
                                                                'app_date',
                                                                'app_time',
                                                                'prod_category',
                                                                'po_no',
                                                                'device_name',
                                                                'customer',
                                                                'po_qty',
                                                                'family',
                                                                'type_of_inspection',
                                                                'severity_of_inspection',
                                                                'inspection_lvl',
                                                                'aql',
                                                                'accept',
                                                                'reject',
                                                                'date_inspected',
                                                                'ww',
                                                                'fy',
                                                                'time_ins_from',
                                                                'time_ins_to',
                                                                'shift',
                                                                'inspector',
                                                                'submission',
                                                                'coc_req',
                                                                'judgement',
                                                                'lot_qty',
                                                                'sample_size',
                                                                'lot_inspected',
                                                                'lot_accepted',
                                                                'num_of_defects',
                                                                'remarks',
                                                                'type',
                                                                'modid')
                                                        ->where('g1',$gr1->g1)
                                                        ->where('g2',$gr2->g2)
                                                        ->where('g3',$gr3->g3)
                                                        ->get();
                                    array_push($node3, [
                                        'g1' => $gr1->g1,
                                        'g2' => $gr2->g2,
                                        'group' => $gr3->g3,
                                        'LAR' => $gr3->L3,
                                        'DPPM' => $gr3->DPPM3,
                                        'field' => $g3,
                                        'details' => $details_query
                                    ]);
                                }

                                array_push($node2, [
                                    'g1' => $gr1->g1,
                                    'group' => $gr2->g2,
                                    'LAR' => $gr2->L2,
                                    'DPPM' => $gr2->DPPM2,
                                    'field' => $g2,
                                    'details' => []
                                ]);
                            }
                        }

                        array_push($node1, [
                            'group' => $gr1->g1,
                            'LAR' => $gr1->L1,
                            'DPPM' => $gr1->DPPM1,
                            'field' => $g1,
                            'details' => []
                        ]);
                    }
                }
            }

            $data = [
                'node1' => $node1,
                'node2' => $node2,
                'node3' => $node3
            ];
        } else {
            $data = [
                'msg' => "No data generated.",
                'status' => 'failed'
            ];
        }

            
        
        
        return response()->json($data);
    }

    public function GrpByPDFReport()
    {
        $date = '';
        $po = '';

        $header = DB::connection($this->mysql)->table('oqc_inspection_group')
                    ->groupBy('prod_category',
                            'po_no',
                            'device_name')
                    ->select('prod_category',
                            'po_no',
                            'device_name',
                            'customer',
                            DB::raw('SUM(po_qty) AS po_qty'),
                            'severity_of_inspection',
                            'inspection_lvl',
                            'aql',
                            'accept',
                            'reject',
                            'coc_req')
                    ->get();

        $details = DB::connection($this->mysql)->table('oqc_inspection_group')->get();

        $dt = Carbon::now();
        $company_info = $this->com->getCompanyInfo();
        $date = substr($dt->format('  M j, Y  h:i A '), 2);

        $data = [
            'company_info' => $company_info,
            'details' => $details,
            'date' => $date,
            'header' => $header
        ];

        $pdf = PDF::loadView('pdf.oqcwithpo', $data)
                    ->setPaper('A4')
                    ->setOption('margin-top', 10)
                    ->setOption('margin-bottom', 5)
                    ->setOption('margin-left', 1)
                    ->setOption('margin-right', 1)
                    ->setOrientation('landscape');

        return $pdf->inline('OQC_Inspection_'.Carbon::now());
    }

    public function GrpByExcelReport()
    {
        $dt = Carbon::now();
        $dates = substr($dt->format('Ymd'), 2);

        Excel::create('OQC_Inspection_Report'.$dates, function($excel) use($dt)
        {
            $com_info = $this->com->getCompanyInfo();
            $date = substr($dt->format('  M j, Y  h:i A '), 2);

            $infos = DB::connection($this->mysql)->table('oqc_inspection_group')
                        ->groupBy('po_no','device_name','date_inspected','submission','judgement',
                                'prod_category','customer','severity_of_inspection',
                                'inspection_lvl','aql','accept','reject','coc_req',
                                'type_of_inspection','po_qty')
                        ->select('fy',
                                'ww',
                                'date_inspected',
                                'shift',
                                'time_ins_from',
                                'time_ins_to',
                                'submission',
                                'lot_qty',
                                'sample_size',
                                'num_of_defects',
                                'lot_no',
                                'judgement',
                                'inspector',
                                'remarks',
                                'assembly_line',
                                'app_date',
                                'app_time',
                                'prod_category',
                                'po_no',
                                'device_name',
                                'customer',
                                'po_qty',
                                'family',
                                'type_of_inspection',
                                'severity_of_inspection',
                                'inspection_lvl',
                                'aql',
                                'accept',
                                'reject',
                                'coc_req',
                                'lot_inspected',
                                'lot_accepted',
                                'dbcon',
                                'modid',
                                'type')
                        ->get();

            foreach ($infos as $key => $info) {
                $excel->sheet($info->po_no, function($sheet) use($com_info,$date,$info)
                {
                    $sheet->setFreeze('A13');

                    $details = DB::connection($this->mysql)->table('oqc_inspection_group')
                                ->groupBy('po_no','device_name','date_inspected','submission','judgement',
                                            'prod_category','customer','severity_of_inspection',
                                            'inspection_lvl','aql','accept','reject','coc_req',
                                            'type_of_inspection','po_qty')
                                ->where('po_no',$info->po_no)
                                ->select('fy',
                                    'ww',
                                    'date_inspected',
                                    'shift',
                                    'time_ins_from',
                                    'time_ins_to',
                                    'submission',
                                    'lot_qty',
                                    'sample_size',
                                    'num_of_defects',
                                    'lot_no',
                                    'judgement',
                                    'inspector',
                                    'remarks',
                                    'assembly_line',
                                    'app_date',
                                    'app_time',
                                    'prod_category',
                                    'po_no',
                                    'device_name',
                                    'customer',
                                    'po_qty',
                                    'family',
                                    'type_of_inspection',
                                    'severity_of_inspection',
                                    'inspection_lvl',
                                    'aql',
                                    'accept',
                                    'reject',
                                    'coc_req',
                                    'lot_inspected',
                                    'lot_accepted',
                                    'dbcon',
                                    'modid',
                                    'type')
                                ->get();

                    $sheet->setHeight(1, 15);
                    $sheet->mergeCells('A1:O1');
                    $sheet->cells('A1:O1', function($cells) {
                        $cells->setAlignment('center');
                    });
                    $sheet->cell('A1',$com_info['name']);

                    $sheet->setHeight(2, 15);
                    $sheet->mergeCells('A2:O2');
                    $sheet->cells('A2:O2', function($cells) {
                        $cells->setAlignment('center');
                    });
                    $sheet->cell('A2',$com_info['address']);

                    $sheet->setHeight(4, 20);
                    $sheet->mergeCells('A4:O4');
                    $sheet->cells('A4:O4', function($cells) {
                        $cells->setAlignment('center');
                        $cells->setFont([
                            'family'     => 'Calibri',
                            'size'       => '14',
                            'bold'       =>  true,
                            'underline'  =>  true
                        ]);
                    });
                    $sheet->cell('A4',"OQC INSPECTION RESULT");

                    $sheet->setHeight(11, 15);
                    $sheet->cells('A12:O12', function($cells) {
                        $cells->setBorder('thin','thin','thin','thin');
                        $cells->setFont([
                            'family'     => 'Calibri',
                            'size'       => '12',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('B6', function($cell) {
                        $cell->setValue('Series Name');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('B7', function($cell) {
                        $cell->setValue('Category');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });                    

                    $sheet->cell('B8', function($cell) {
                        $cell->setValue('P.O.');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('B9', function($cell) {
                        $cell->setValue('P.O. Qty.');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('C6',$info->device_name);
                    $sheet->cell('C7',$info->prod_category);
                    $sheet->cell('C8',$info->po_no);
                    $sheet->cell('C9',$info->po_qty);

                    $sheet->cell('E6', function($cell) {
                        $cell->setValue('Customer Name');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('E7', function($cell) {
                        $cell->setValue('COC Requirements');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('E8', function($cell) {
                        $cell->setValue('Type of Inspection');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('E9', function($cell) {
                        $cell->setValue('Severity of Inspection');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('E10', function($cell) {
                        $cell->setValue('Inspection Level');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('F6',$info->customer);
                    $sheet->cell('F7',$info->coc_req);
                    $sheet->cell('F8',$info->type_of_inspection);
                    $sheet->cell('F9',$info->severity_of_inspection);
                    $sheet->cell('F10',$info->inspection_lvl);
                    
                    $sheet->cell('H7', function($cell) {
                        $cell->setValue('AQL');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('H8', function($cell) {
                        $cell->setValue('Ac');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('H9', function($cell) {
                        $cell->setValue('Re');
                        $cell->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });
                    
                    $sheet->cell('I7',$info->aql);
                    $sheet->cell('I8',($info->accept < 1)? '0.00': $info->accept);
                    $sheet->cell('I9',($info->reject < 1)? '0.00': $info->reject);

                    $sheet->setHeight(6, 15);
                    $sheet->setHeight(7, 15);
                    $sheet->setHeight(8, 15);
                    $sheet->setHeight(9, 15);
                    $sheet->setHeight(10, 15);

                    $sheet->cell('B12',"FY-WW");
                    $sheet->cell('C12',"Date Inspected");
                    $sheet->cell('D12',"Shift");
                    $sheet->cell('E12',"Time Inspected");
                    $sheet->cell('F12',"# of Sub");
                    $sheet->cell('G12',"Lot Size");
                    $sheet->cell('H12',"Sample Size");
                    $sheet->cell('I12',"No. of Defective");
                    $sheet->cell('J12',"Lot No.");
                    $sheet->cell('K12',"Mode of Defects");
                    $sheet->cell('L12',"Qty");
                    $sheet->cell('M12',"Judgement");
                    $sheet->cell('N12',"Inspector");
                    $sheet->cell('O12',"Remarks");

                    $row = 13;

                    $sheet->setHeight(12, 15);

                    $lot_qty = 0;
                    $po_qty = 0;
                    $balance = 0;

                    foreach ($details as $key => $qc) {
                        $lot_qty += $qc->lot_qty;
                        $po_qty += $qc->po_qty;

                        $sheet->cells('B'.$row.':O'.$row, function($cells) {
                            // Set all borders (top, right, bottom, left)
                            $cells->setBorder(array(
                                'top'   => array(
                                    'style' => 'thin'
                                ),
                            ));
                            $cells->setFont([
                                'family'     => 'Calibri',
                                'size'       => '11',
                            ]);
                        });

                        $sheet->cell('B'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->fy.'-'.$qc->ww);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('C'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->date_inspected);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('D'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->shift);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('E'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->time_ins_from.'-'.$qc->time_ins_to);
                            $cell->setBorder('thin','thin','thin','thin');
                        });
                        $sheet->cell('F'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->submission);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('G'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->lot_qty);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('H'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->sample_size);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('I'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->num_of_defects);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('J'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->lot_no);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('K'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->modid);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('L'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->num_of_defects);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('M'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->judgement);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('N'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->inspector);
                            $cell->setBorder('thin','thin','thin','thin');
                        });

                        $sheet->cell('O'.$row, function($cell) use($qc) {
                            $cell->setValue($qc->remarks);
                            $cell->setBorder('thin','thin','thin','thin');
                        });
                        
                        $sheet->row($row, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(11);
                        });
                        $sheet->setHeight($row,20);
                        $row++;
                    }

                    $balance = $po_qty - $lot_qty;

                    $sheet->cell('B'.$row, "Total Qty:");
                    $sheet->cell('C'.$row, $lot_qty);
                    $sheet->setHeight($row,20);
                    $row++;
                    $sheet->cell('B'.$row, "Balance:");
                    $sheet->cell('C'.$row, ($balance < 1)? '0.00':$balance);
                    $sheet->setHeight($row,20);
                    $row++;
                    $sheet->cell('B'.$row, "Date:");
                    $sheet->cell('C'.$row, $date);
                    $sheet->setHeight($row,20);
                });
            }

        })->download('xls');
    }
}
