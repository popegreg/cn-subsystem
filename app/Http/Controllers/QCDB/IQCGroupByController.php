<?php

namespace App\Http\Controllers\QCDB;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
use DB;
use Config;

class IQCGroupByController extends Controller
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

    public function CalculateDPPM(Request $req)
    {
        $g1 = (!isset($req->field1) || $req->field1 == '' || $req->field1 == null)? '': $req->field1;
        $g2 = (!isset($req->field2) || $req->field2 == '' || $req->field2 == null)? '': $req->field2;
        $g3 = (!isset($req->field3) || $req->field3 == '' || $req->field3 == null)? '': $req->field3;
        $content1 = (!isset($req->content1) || $req->content1 == '' || $req->content1 == null)? '%': $req->content1;
        $content2 = (!isset($req->content2) || $req->content2 == '' || $req->content2 == null)? '%': $req->content2;
        $content3 = (!isset($req->content3) || $req->content3 == '' || $req->content3 == null)? '%': $req->content3;

        $groupby = DB::connection($this->mysql)
                ->select(
                    DB::raw(
                        "CALL GetIQCGroupBy(
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

        if ($g1 !== '') {
            $grp1_query = DB::connection($this->mysql)->table('iqc_inspection_group')
                            ->select('g1','L1','DPPM1')
                            ->groupBy($g1)
                            ->orderBy('g1')
                            ->get();
            
            foreach ($grp1_query as $key => $gr1) {
                if ($g2 == '') {
                    $details_query = DB::connection($this->mysql)->table('iqc_inspection_group')
                                    ->select('invoice_no',
                                            'partcode',
                                            'partname',
                                            'supplier',
                                            'app_date',
                                            'app_time',
                                            'app_no',
                                            'lot_no',
                                            'lot_qty',
                                            'type_of_inspection',
                                            'severity_of_inspection',
                                            'inspection_lvl',
                                            'aql',
                                            'accept',
                                            'reject',
                                            'date_ispected',
                                            'ww',
                                            'fy',
                                            'shift',
                                            'time_ins_from',
                                            'time_ins_to',
                                            'inspector',
                                            'submission',
                                            'judgement',
                                            'lot_inspected',
                                            'lot_accepted',
                                            'sample_size',
                                            'no_of_defects',
                                            'remarks',
                                            'classification')
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

                    $grp2_query = DB::connection($this->mysql)->table('iqc_inspection_group')
                                    ->select('g1','g2','L2','DPPM2')
                                    ->where('g1',$gr1->g1)
                                    ->groupBy($g2)
                                    ->orderBy('g2')
                                    ->get();

                    foreach ($grp2_query as $key => $gr2) {
                        if ($g3 == '') {
                            $details_query = DB::connection($this->mysql)->table('iqc_inspection_group')
                                                ->select('invoice_no',
                                                        'partcode',
                                                        'partname',
                                                        'supplier',
                                                        'app_date',
                                                        'app_time',
                                                        'app_no',
                                                        'lot_no',
                                                        'lot_qty',
                                                        'type_of_inspection',
                                                        'severity_of_inspection',
                                                        'inspection_lvl',
                                                        'aql',
                                                        'accept',
                                                        'reject',
                                                        'date_ispected',
                                                        'ww',
                                                        'fy',
                                                        'shift',
                                                        'time_ins_from',
                                                        'time_ins_to',
                                                        'inspector',
                                                        'submission',
                                                        'judgement',
                                                        'lot_inspected',
                                                        'lot_accepted',
                                                        'sample_size',
                                                        'no_of_defects',
                                                        'remarks',
                                                        'classification')
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

                           $grp3_query = DB::connection($this->mysql)->table('iqc_inspection_group')
                                            ->select('g1','g2','g3','L3','DPPM3')
                                            ->where('g1',$gr1->g1)
                                            ->where('g2',$gr2->g2)
                                            ->groupBy($g3)
                                            ->orderBy('g3')
                                            ->get();

                            foreach ($grp3_query as $key => $gr3) {
                                $details_query = DB::connection($this->mysql)->table('iqc_inspection_group')
                                                    ->select('invoice_no',
                                                            'partcode',
                                                            'partname',
                                                            'supplier',
                                                            'app_date',
                                                            'app_time',
                                                            'app_no',
                                                            'lot_no',
                                                            'lot_qty',
                                                            'type_of_inspection',
                                                            'severity_of_inspection',
                                                            'inspection_lvl',
                                                            'aql',
                                                            'accept',
                                                            'reject',
                                                            'date_ispected',
                                                            'ww',
                                                            'fy',
                                                            'shift',
                                                            'time_ins_from',
                                                            'time_ins_to',
                                                            'inspector',
                                                            'submission',
                                                            'judgement',
                                                            'lot_inspected',
                                                            'lot_accepted',
                                                            'sample_size',
                                                            'no_of_defects',
                                                            'remarks',
                                                            'classification')
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
        
        
        return response()->json($data);
    }
    
    public function GroupByValues(Request $req)
    {
        $data = DB::connection($this->mysql)->table('iqc_inspections')
                ->select($req->field.' as field')
                ->distinct()
                ->get();

        return $data;
    }
}
