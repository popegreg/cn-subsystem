<?php
namespace App\Http\Controllers\QCDB;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Config;
use App\OQCInspection;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use PDF;
use Carbon\Carbon;
use Excel;

class OQCInspectionController extends Controller
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
            return view('qcdb.oqcinspection',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function initData()
    {
        $displaymod = DB::connection($this->mysql)->table('oqc_inspections_mod')->get();
        $countrecords = DB::connection($this->mysql)->table('oqc_inspections')->count();
        $family = $this->com->getDropdownByName('Family');
        //$family = $this->com->getDropdownByName('Family');
        $tofinspection = $this->com->getDropdownByName('Type of Inspection');
        $sofinspection = $this->com->getDropdownByName('Severity of Inspection');
        $inspectionlvl = $this->com->getDropdownByName('Inspection Level');
        $assemblyline = $this->com->getDropdownByName('Assembly Line');
        $aql = $this->com->getDropdownByName('AQL');
        $shift = $this->com->getDropdownByName('Shift');
        $submission = $this->com->getDropdownByName('Submission');
        $shift = $this->com->getDropdownByName('Shift');
        $mod = $this->com->getDropdownByName('Mode of Defect - OQC Inscpection Molding');

        return $data = [
            'oqcmod'=>$displaymod,
            'families' =>$family,
            'tofinspections' => $tofinspection,
            'sofinspections' => $sofinspection,
            'inspectionlvls' =>$inspectionlvl,
            'aqls' =>$aql,
            'shifts' =>$shift,
            'submissions'=>$submission,
            'mods'=>$mod,
            'assemblyline'=>$assemblyline,
            'count'=>$countrecords
        ];
    }

    public function getmodoqc(Request $request)
    {
        $pono = $request->pono;
        $table = DB::connection($this->mysql)->table('oqc_inspections_mod')->select('mod')->where('pono',$pono)->get();
        return $table;
    }
/*
    public function OQCDataTable(Request $req)
    {
        $output = '';
        $po = '';
        $date = '';
        $select = [
                    'id',
                    'fy',
                    'ww',
                    'date_inspected',
                    'device_name',
                    'time_ins_from',
                    'time_ins_to',
                    'submission',
                    'lot_qty',
                    'sample_size',
                    'num_of_defects',
                    'lot_no',
                    'po_qty',
                    'judgement',
                    'inspector',
                    'remarks',
                    'type',
                    'shift',
                    'assembly_line',
                    'app_date',
                    'app_time',
                    'prod_category',
                    'po_no',
                    'customer',
                    'family',
                    'type_of_inspection',
                    'severity_of_inspection',
                    'inspection_lvl',
                    'aql',
                    'accept',
                    'reject',
                    'coc_req',
                    'lot_inspected',
                    'lot_accepted'
                ];

        if ($req->type == 'search') {
            if (!empty($req->search_po) || $req->pono !== '') {
                $po = " AND po_no = '".$req->search_po."'";
            }

            if ($req->search_from !== '' || !empty($req->search_from)) {
                $date = " AND date_inspected BETWEEN '".$this->com->convertDate($req->search_from,'Y-m-d')."' AND '".$this->com->convertDate($req->search_to,'Y-m-d')."'";
            }

            $query = DB::connection($this->mysql)->table('oqc_inspections')
                        ->whereRaw("1=1".$po.$date)
                        ->orderBy('id','desc')
                        ->select($select);
        } else {
            $query = DB::connection($this->mysql)->table('oqc_inspections')
                    ->orderBy('id','desc')
                    ->select($select);
        }

        return Datatables::of($query)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->addColumn('action', function($data) {
                            return '<button type="button" class="btn btn-sm btn-primary btn_edit_inspection" data-id="'.$data->id.'" data-assembly_line="'.$data->assembly_line.'" data-app_date="'.$data->app_date.'" data-app_time="'.$data->app_time.'" data-lot_no="'.$data->lot_no.'" data-prod_category="'.$data->prod_category.'" data-po_no="'.$data->po_no.'" data-device_name="'.$data->device_name.'" data-customer="'.$data->customer.'" data-po_qty="'.$data->po_qty.'" data-family="'.$data->family.'" data-type_of_inspection="'.$data->type_of_inspection.'" data-severity_of_inspection="'.$data->severity_of_inspection.'" data-inspection_lvl="'.$data->inspection_lvl.'" data-aql="'.$data->aql.'" data-accept="'.$data->accept.'" data-reject="'.$data->reject.'" data-date_inspected="'.$data->date_inspected.'" data-ww="'.$data->ww.'" data-fy="'.$data->fy.'" data-shift="'.$data->shift.'" data-time_ins_from="'.$data->time_ins_from.'" data-time_ins_to="'.$data->time_ins_to.'" data-inspector="'.$data->inspector.'" data-submission="'.$data->submission.'" data-coc_req="'.$data->coc_req.'" data-judgement="'.$data->judgement.'" data-lot_qty="'.$data->lot_qty.'" data-sample_size="'.$data->sample_size.'" data-lot_inspected="'.$data->lot_inspected.'" data-lot_accepted="'.$data->lot_accepted.'" data-num_of_defects="'.$data->num_of_defects.'" data-remarks="'.$data->remarks.'" data-type="'.$data->type.'">'.
                                '   <i class="fa fa-edit"></i> '.
                            '</button>';
                        })
                        ->addColumn('fyww', function($data) {
                            return $data->fy.' - '.$data->ww;
                        })
                        ->addColumn('mod', function($data) use($req,$output) {
                            if ($data->judgement == 'Accept') {
                                return 'NDF';
                            } else {
                                if($req->report_status == "GROUPBY"){
                                    $table = DB::connection($this->mysql)->table('oqc_inspections_mod')
                                        ->select('pono',DB::raw("(GROUP_CONCAT(mod1 SEPARATOR ' , ')) AS mod1"),DB::raw("(GROUP_CONCAT(lotno SEPARATOR ' , ')) AS lot_no"),'submission','qty')
                                        ->groupBy('pono','submission','device')
                                        ->get();

                                } else {
                                    $table = DB::connection($this->mysql)->table('oqc_inspections as a')
                                        ->leftJoin('oqc_inspections_mod as b','a.lot_no','=','b.lotno')
                                        ->select('a.po_no',
                                                'b.mod1',
                                                'a.lot_no',
                                                'a.submission')
                                        ->where('b.pono',$data->po_no)
                                        ->where('a.lot_no',$data->lot_no)
                                        ->where('a.submission',$data->submission)
                                        ->get();
                                }

                                foreach ($table as $key => $data) {
                                    $output[$key] = 'ok';//$data->mod1;
                                    // $output['lotno'][$key] = $data->lot_no;
                                }

                                return $output;
                            }
                        })
                        ->make(true);
    }
*/
    public function OQCDataTable(Request $req)
    {
        $output = '';
        $po = '';
        $date = '';
        $select = [
                    'id',
                    'fy',
                    'ww',
                    'date_inspected',
                    'device_name',
                    'time_ins_from',
                    'time_ins_to',
                    'submission',
                    'lot_qty',
                    'sample_size',
                    'num_of_defects',
                    'lot_no',
                    'po_qty',
                    'judgement',
                    'inspector',
                    'remarks',
                    'type',
                    'shift',
                    'assembly_line',
                    'app_date',
                    'app_time',
                    'prod_category',
                    'po_no',
                    'customer',
                    'family',
                    'type_of_inspection',
                    'severity_of_inspection',
                    'inspection_lvl',
                    'aql',
                    'accept',
                    'reject',
                    'coc_req',
                    'lot_inspected',
                    'lot_accepted'
                ];

        if ($req->type == 'search') {
            if (!empty($req->search_po) || $req->pono !== '') {
                $po = " AND po_no = '".$req->search_po."'";
            }

            if ($req->search_from !== '' || !empty($req->search_from)) {
                $date = " AND date_inspected BETWEEN '".$this->com->convertDate($req->search_from,'Y-m-d')."' AND '".$this->com->convertDate($req->search_to,'Y-m-d')."'";
            }

            $query = DB::connection($this->mysql)->table('oqc_inspections')
                        ->whereRaw("1=1".$po.$date)
                        ->orderBy('id','desc')
                        ->select($select);
        } else {
            $query = DB::connection($this->mysql)->table('oqc_inspections')
                    ->orderBy('id','desc')
                    ->select($select);
        }

        return Datatables::of($query)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->addColumn('action', function($data) {
                            return '<button type="button" class="btn btn-sm btn-primary btn_edit_inspection" data-id="'.$data->id.'" data-assembly_line="'.$data->assembly_line.'" data-app_date="'.$data->app_date.'" data-app_time="'.$data->app_time.'" data-lot_no="'.$data->lot_no.'" data-prod_category="'.$data->prod_category.'" data-po_no="'.$data->po_no.'" data-device_name="'.$data->device_name.'" data-customer="'.$data->customer.'" data-po_qty="'.$data->po_qty.'" data-family="'.$data->family.'" data-type_of_inspection="'.$data->type_of_inspection.'" data-severity_of_inspection="'.$data->severity_of_inspection.'" data-inspection_lvl="'.$data->inspection_lvl.'" data-aql="'.$data->aql.'" data-accept="'.$data->accept.'" data-reject="'.$data->reject.'" data-date_inspected="'.$data->date_inspected.'" data-ww="'.$data->ww.'" data-fy="'.$data->fy.'" data-shift="'.$data->shift.'" data-time_ins_from="'.$data->time_ins_from.'" data-time_ins_to="'.$data->time_ins_to.'" data-inspector="'.$data->inspector.'" data-submission="'.$data->submission.'" data-coc_req="'.$data->coc_req.'" data-judgement="'.$data->judgement.'" data-lot_qty="'.$data->lot_qty.'" data-sample_size="'.$data->sample_size.'" data-lot_inspected="'.$data->lot_inspected.'" data-lot_accepted="'.$data->lot_accepted.'" data-num_of_defects="'.$data->num_of_defects.'" data-remarks="'.$data->remarks.'" data-type="'.$data->type.'">'.
                                '   <i class="fa fa-edit"></i> '.
                            '</button>';
                        })
                        ->addColumn('fyww', function($data) {
                            return $data->fy.' - '.$data->ww;
                        })
                        ->addColumn('mod', function($data) use($req,$output) {
                            if ($data->judgement == 'Accept') {
                                return 'NDF';
                            } else {
                                if($req->report_status == "GROUPBY"){
                                    $gbTable = DB::connection($this->mysql)->table('oqc_inspections_mod')
                                        ->select('pono',DB::raw("(GROUP_CONCAT(mod1 SEPARATOR ' , ')) AS mod1"),DB::raw("(GROUP_CONCAT(lotno SEPARATOR ' , ')) AS lot_no"),'submission','qty')
                                        ->groupBy('pono','submission','device')
                                        ->get();

                                    foreach ($gbTable as $key => $data) {
                                        $output[$key] = $data->mod1;
                                        // $output['lotno'][$key] = $data->lot_no;
                                    }

                                    return $output;

                                } else {
                                    $sTable = DB::connection($this->mysql)->table('oqc_inspections as a')
                                        ->leftJoin('oqc_inspections_mod as b','a.lot_no','=','b.lotno')
                                        ->select('a.po_no',
                                                'b.mod1',
                                                'a.lot_no',
                                                'a.submission')
                                        ->where('a.po_no',$data->po_no) // b.pono
                                        ->where('a.lot_no',$data->lot_no)
                                        ->where('a.submission',$data->submission)
                                        ->get();

                                    foreach ($sTable as $key => $data) {
                                        /* removed [$key] on $output */

                                        $output = ($data->mod1 == '')? 'N/A' : $data->mod1;
                                        // $output['lotno'][$key] = $data->lot_no;
                                    }

                                    return $output;
                                }
                            }
                        })
                        ->make(true);
    }
    
    public function getPOdetails(Request $req)
    {
        if (!empty($req->po)) {
            if ($req->is_probe > 0) {
                $msrecords = DB::connection($this->mssql)
                            ->select("SELECT R.SORDER as po,
                                            HK.CODE as device_code,
                                            H.NAME as device_name,
                                            R.CUST as customer_code,
                                            C.CNAME as customer_name,
                                            HK.KVOL as po_qty,
                                            I.BUNR
                                    FROM XRECE as R
                                    LEFT JOIN XSLIP as S on R.SORDER = S.SEIBAN
                                    LEFT JOIN XHIKI as HK on S.PORDER  = HK.PORDER
                                    LEFT JOIN XHEAD as H ON HK.CODE = H.CODE
                                    LEFT JOIN XITEM as I ON HK.CODE = I.CODE
                                    LEFT JOIN XCUST as C ON R.CUST = C.CUST
                                    WHERE R.SORDER like '".$req->po."%'
                                    AND I.BUNR = 'PROBE'");
            } else {
                $msrecords = DB::connection($this->mssql)
                                ->table('XRECE as R')
                                ->leftJoin('XHEAD as H','R.CODE','=','H.CODE')
                                ->leftJoin('XCUST as C','R.CUST','=','C.CUST')
                                ->where('R.SORDER','like',$req->po."%")
                                ->select(DB::raw('R.SORDER as po'),
                                        DB::raw('R.CODE as device_code'),
                                        DB::raw('H.NAME as device_name'),
                                        DB::raw('R.CUST as customer_code'),
                                        DB::raw('C.CNAME as customer_name'),
                                        DB::raw('SUM(R.KVOL) as po_qty'))
                                ->groupBy('R.SORDER',
                                        'R.CODE',
                                        'H.NAME',
                                        'R.CUST',
                                        'C.CNAME')
                                ->get();

            }
        }

        return $msrecords;
    }

    public function getProbeProduct(Request $req)
    {
        $msrecords = DB::connection($this->mssql)->table('XHEAD AS H')
                        ->leftJoin('XPRTS as R','H.CODE','=','R.CODE')
                        ->where('R.KCODE',$req->code)
                        ->select('R.CODE as devicecode',
                                'H.NAME as DEVNAME',
                                'R.KCODE as partcode')
                        ->get();
        return $msrecords;
    }

    public function ModDataTable(Request $req)
    {
        $select = [
                    'id',
                    'pono',
                    'device',
                    'lotno',
                    'submission',
                    'mod1',
                    'modid',
                    'qty'
                ];

        $query = DB::connection($this->mysql)->table('oqc_inspections_mod')
                    ->where('pono',$req->pono)
                    // ->where('device',$req->device)
                    // ->where('lotno',$req->lotno)
                    ->where('submission',$req->submission)
                    ->orderBy('id','desc')
                    ->select($select);
        return Datatables::of($query)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->addColumn('action', function($data) {
                            return '<button type="button" class="btn btn-sm btn-primary btn_edit_mod" data-id="'.$data->id.'"'.
                                    ' data-pono="'.$data->pono.'" data-device="'.$data->device.'" data-lotno="'.$data->lotno.'"'.
                                    ' data-submission="'.$data->submission.'" data-mod1="'.$data->mod1.'" data-qty="'.$data->qty.'"'.
                                    'data-ins-id="'.$data->modid.'">'.
                                        '<i class="fa fa-edit"></i>'.
                                    '</button>';
                        })
                        ->make(true);
    }

    private function validateInspection($req)
    {
        $rules = [
            'assembly_line' => 'required',
            'lot_no' => 'required',
            'app_date' => 'required',
            'app_time' => 'required',
            'prod_category' => 'required',
            'po_no' => 'required',
            'series_name' => 'required',
            'customer' => 'required',
            'po_qty' => 'required|numeric',
            'family' => 'required',
            'type_of_inspection' => 'required',
            'severity_of_inspection' => 'required',
            'inspection_lvl' => 'required',
            'aql' => 'required',
            'date_inspected' => 'required',
            'shift' => 'required',
            'time_ins_from' => 'required',
            'time_ins_to' => 'required',
            'submission' => 'required',
            'coc_req' => 'required',
            'judgement' => 'required',
            'lot_qty' => 'required|numeric',
            'sample_size' => 'required|numeric',
            'lot_inspected' => 'required',
            'lot_accepted' => 'required',
        ];

        $msg = [
            'assembly_line.required' => 'Assembly Line is required.',
            'lot_no.required' => 'Lot number is required.',
            'app_date.required' => 'Application Date is required.',
            'app_time.required' => 'Application Time is required.',
            'prod_category.required' => 'Production Category is required.',
            'po_no.required' => 'P.O. number is required.',
            'series_name.required' => 'Series name is required.',
            'customer.required' => 'Customer is required.',
            'po_qty.required' => 'P.O. Quantity is required.',
            'po_qty.numeric' => 'P.O. Quantity must be numeric',
            'family.required' => 'Family is required.',
            'type_of_inspection.required' => 'Type of Inspection is required.',
            'severity_of_inspection.required' => 'Severity of Inspection is required.',
            'inspection_lvl.required' => 'Inspection Level is required.',
            'aql.required' => 'AQL is required.',
            'date_inspected.required' => 'Date Inspected is required.',
            'shift.required' => 'Shift is required.',
            'time_ins_from.required' => 'Time inspection from is required.',
            'time_ins_to.required' => 'Time inspection to is required.',
            'submission.required' => 'Submission is required.',
            'coc_req.required' => 'COC Requirements is required.',
            'judgement.required' => 'Judgement is required.',
            'lot_qty.required' => 'Lot Quantity is required.',
            'lot_qty.numeric' => 'Lot Quantity must be numeric',
            'sample_size.required' => 'Sample Size is required.',
            'sample_size.numeric' => 'Sample Size must be numeric',
            'lot_inspected.required' => 'Lot Inspected is required.',
            'lot_accepted.required' => 'Lot Accepted is required.',
        ];

        return $this->validate($req, $rules, $msg);
    }

    public function saveInspection(Request $req)
    {
        $this->validateInspection($req);

        $data = [
            'msg' => 'Saving failed.',
            'status' => 'failed'

        ];

        $values = [
                    'assembly_line' => $req->assembly_line,
                    'lot_no' => $req->lot_no,
                    'app_date' => $this->com->convertDate($req->app_date,'Y-m-d'),
                    'app_time' => $req->app_time,
                    'prod_category' => $req->prod_category,
                    'po_no' => $req->po_no,
                    'device_name' => $req->series_name,
                    'customer' => $req->customer,
                    'po_qty' => $req->po_qty,
                    'family' => $req->family,
                    'type_of_inspection' => $req->type_of_inspection,
                    'severity_of_inspection' => $req->severity_of_inspection,
                    'inspection_lvl' => $req->inspection_lvl,
                    'aql' => $req->aql,
                    'accept' => $req->accept,
                    'reject' => $req->reject,
                    'date_inspected' => $this->com->convertDate($req->date_inspected,'Y-m-d'),
                    'ww' => $req->ww,
                    'fy' => $req->fy,
                    'time_ins_from' => $req->time_ins_from,
                    'time_ins_to' => $req->time_ins_to,
                    'shift' => $req->shift,
                    'inspector' => $req->inspector,
                    'submission' => $req->submission,
                    'coc_req' => $req->coc_req,
                    'judgement' => $req->judgement,
                    'lot_qty' => $req->lot_qty,
                    'sample_size' => $req->sample_size,
                    'lot_inspected' => $req->lot_inspected,
                    'lot_accepted' => $req->lot_accepted,
                    'lot_rejected' => ($req->lot_accepted == 1)? 0 : 1,
                    'num_of_defects' => ($req->lot_accepted == 1)? 0 : $req->no_of_defects,
                    'remarks' => $req->remarks,
                    'type'=> ($req->is_probe == 1)? 'PROBE PIN':'IC SOCKET',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

        if ($req->inspection_save_status == 'ADD') {
            $query = DB::connection($this->mysql)->table("oqc_inspections")
                        ->insert($values);
        } else {
            $query = DB::connection($this->mysql)->table("oqc_inspections")
                        ->where('id',$req->inspection_id)
                        ->update($values);
        }

        if ($query) {
            $data = [
                'msg' => 'Inspection Successfully saved.',
                'status' => 'success'

            ];
        }

        return $data;
    }

    public function deleteInspection(Request $req)
    {
        $data = [
            'msg' => 'Deleting failed.',
            'status' => 'failed'
        ];

        $delete = DB::connection($this->mysql)->table('oqc_inspections')
                    ->whereIn('id',$req->ids)
                    ->delete();
        if ($delete) {
            $data = [
                'msg' => 'Successfully deleted.',
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function getWorkWeek()
    {
        $yr = 52;
        $apr = date('Y').'-04-01';
        $aprweek = date("W", strtotime($apr));

        $diff = $yr - $aprweek;
        $date = Carbon::now();
        $weeknow = $date->format("W");

        $workweek = $diff + $weeknow + 1;
        if ($workweek > 52) {
            $workweek = $workweek - 52;
        }
        return $workweek;
    }

    private function validateModeOfDefects($req)
    {
        $rules = [
            'mode_of_defects_name' => 'required',
            'mod_qty' => 'required|numeric',
        ];

        $msg = [
            'mode_of_defects_name.required' => 'Mode of defect is required.',
            'mod_qty.required' => 'Mode quantity is required.',
            'mod_qty.numeric' => 'Mode quantity must be numeric.'
        ];

        return $this->validate($req, $rules, $msg);
    }

    public function saveModeOfDefects(Request $req)
    {
        $this->validateModeOfDefects($req);

        $data = [
            'msg' => 'Saving failed.',
            'status' => 'failed'
        ];

        if ($req->mode_save_status == 'ADD') {
            $query = DB::connection($this->mysql)->table('oqc_inspections_mod')
                        ->insert([
                            'pono' => $req->mod_po,
                            'device' => $req->mod_device,
                            'lotno' => $req->mod_lotno,
                            'submission' => $req->mod_submission,
                            'mod1' => $req->mode_of_defects_name,
                            'qty' => $req->mod_qty,
                            'modid' => $req->ins_id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
        } else {
            $query = DB::connection($this->mysql)->table('oqc_inspections_mod')
                        ->where('id', $req->mod_id)
                        ->update([
                            'pono' => $req->mod_po,
                            'device' => $req->mod_device,
                            'lotno' => $req->mod_lotno,
                            'submission' => $req->mod_submission,
                            'mod1' => $req->mode_of_defects_name,
                            'qty' => $req->mod_qty,
                            'updated_at' => Carbon::now(),
                        ]);
        }

        if ($query) {
            $data = [
                'msg' => 'Mode of defects successfully saved.',
                'status' => 'success',
            ];
        }
    }

    public function deleteModeOfDefects(Request $req)
    {
        $data = [
            'msg' => 'Deleting failed.',
            'status' => 'failed'
        ];

        $delete = DB::connection($this->mysql)->table('oqc_inspections_mod')
                    ->whereIn('id',$req->ids)
                    ->delete();
        if ($delete) {
            $data = [
                'msg' => 'Successfully deleted.',
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function PDFReport(Request $req)
    {
        $date = '';
        $po = '';
        $sums = [];

        if ($req->from !== '' || !empty($req->from)) {
            $date = " AND a.date_inspected BETWEEN '".$this->com->convertDate($req->from,'Y-m-d')."' AND '".$this->com->convertDate($req->to,'Y-m-d')."'";
        }

        if ($req->po !== '' || !empty($req->po)) {
            $po = " AND a.po_no = '".$req->po."'";
            return $this->PDFwithPO($req,$po,$date);
        }

        $details = DB::connection($this->mysql)->table('oqc_inspections as a')
                ->leftJoin('oqc_inspections_mod as b', function ($join) {
                    $join->on('a.po_no','=','b.pono');
                    $join->on('a.submission','=','b.submission');
                })
                ->whereRaw("1=1".$po.$date)
                ->groupBy('a.po_no','a.lot_no','a.submission')
                ->orderBy('id','desc')
                ->select('a.id'
                    ,DB::raw('a.fy as fy')
                    ,DB::raw('a.ww as ww')
                    ,DB::raw('a.date_inspected as date_inspected')
                    ,DB::raw('a.shift as shift')
                    ,DB::raw('a.time_ins_from as time_ins_from')
                    ,DB::raw('a.time_ins_to as time_ins_to')
                    ,DB::raw('a.submission as submission')
                    ,DB::raw('a.lot_qty as lot_qty')
                    ,DB::raw('a.sample_size as sample_size')
                    ,DB::raw('a.num_of_defects as num_of_defects')
                    ,DB::raw('a.lot_no as lot_no')
                    ,DB::raw('b.mod1 as mod1')
                    ,DB::raw("IFNULL(SUM(b.qty),0) as qty")
                    ,DB::raw('a.judgement as judgement')
                    ,DB::raw('a.inspector as inspector')
                    ,DB::raw('a.remarks as remarks')
                    ,DB::raw('a.assembly_line as assembly_line')
                    ,DB::raw('a.app_date as app_date')
                    ,DB::raw('a.app_time as app_time')
                    ,DB::raw('a.prod_category as prod_category')
                    ,DB::raw('a.po_no as po_no')
                    ,DB::raw('a.device_name as device_name')
                    ,DB::raw('a.customer as customer')
                    ,DB::raw('a.po_qty as po_qty')
                    ,DB::raw('a.family as family')
                    ,DB::raw('a.type_of_inspection as type_of_inspection')
                    ,DB::raw('a.severity_of_inspection as severity_of_inspection')
                    ,DB::raw('a.inspection_lvl as inspection_lvl')
                    ,DB::raw('a.aql as aql')
                    ,DB::raw('a.accept as accept')
                    ,DB::raw('a.reject as reject')
                    ,DB::raw('a.coc_req as coc_req')
                    ,DB::raw('a.lot_inspected as lot_inspected')
                    ,DB::raw('a.lot_accepted as lot_accepted')
                    ,DB::raw('a.dbcon as dbcon')
                    ,DB::raw("IF(judgement = 'Accept','NDF',a.modid) as modid")
                    ,DB::raw('a.type as type'))
                ->get();


        $dt = Carbon::now();
        $company_info = $this->com->getCompanyInfo();
        $date = substr($dt->format('  M j, Y  h:i A '), 2);

        $data = [
            'company_info' => $company_info,
            'details' => $details,
            'date' => $date,
        ];

        $pdf = PDF::loadView('pdf.oqc', $data)
                    ->setPaper('A4')
                    ->setOption('margin-top', 10)
                    ->setOption('margin-bottom', 5)
                    ->setOption('margin-left', 1)
                    ->setOption('margin-right', 1)
                    ->setOrientation('landscape');

        return $pdf->inline('OQC_Inspection_'.Carbon::now());
    }

    public function PDFwithPO($req,$po,$date)
    {
        $header = DB::connection($this->mysql)->table('oqc_inspections as a')
                ->whereRaw("1=1".$po.$date)
                ->groupBy('a.prod_category',
                            'a.po_no',
                            'a.device_name',
                            'a.customer',
                            'a.po_qty',
                            'a.severity_of_inspection',
                            'a.inspection_lvl',
                            'a.aql',
                            'a.accept',
                            'a.reject',
                            'a.coc_req')
                ->select('a.prod_category',
                            'a.po_no',
                            'a.device_name',
                            'a.customer',
                            'a.po_qty',
                            'a.severity_of_inspection',
                            'a.inspection_lvl',
                            'a.aql',
                            'a.accept',
                            'a.reject',
                            'a.coc_req')->get();

        $details = DB::connection($this->mysql)->table('oqc_inspections as a')
                ->leftJoin('oqc_inspections_mod as b', function ($join) {
                    $join->on('a.po_no','=','b.pono');
                    $join->on('a.submission','=','b.submission');
                })
                ->whereRaw("1=1".$po.$date)
                ->groupBy('a.prod_category',
                            'a.po_no',
                            'a.device_name',
                            'a.customer',
                            'a.po_qty',
                            'a.severity_of_inspection',
                            'a.inspection_lvl',
                            'a.aql',
                            'a.accept',
                            'a.reject',
                            'a.coc_req')
                ->orderBy('id','desc')
                ->select('a.id'
                    ,DB::raw('a.fy as fy')
                    ,DB::raw('a.ww as ww')
                    ,DB::raw('a.date_inspected as date_inspected')
                    ,DB::raw('a.shift as shift')
                    ,DB::raw('a.time_ins_from as time_ins_from')
                    ,DB::raw('a.time_ins_to as time_ins_to')
                    ,DB::raw('a.submission as submission')
                    ,DB::raw('SUM(a.lot_qty) as lot_qty')
                    ,DB::raw('a.sample_size as sample_size')
                    ,DB::raw('a.num_of_defects as num_of_defects')
                    ,DB::raw('a.lot_no as lot_no')
                    ,DB::raw('b.mod1 as mod1')
                    ,DB::raw("IFNULL(SUM(b.qty),0) as qty")
                    ,DB::raw('a.judgement as judgement')
                    ,DB::raw('a.inspector as inspector')
                    ,DB::raw('a.remarks as remarks')
                    ,DB::raw('a.assembly_line as assembly_line')
                    ,DB::raw('a.app_date as app_date')
                    ,DB::raw('a.app_time as app_time')
                    ,DB::raw('a.prod_category as prod_category')
                    ,DB::raw('a.po_no as po_no')
                    ,DB::raw('a.device_name as device_name')
                    ,DB::raw('a.customer as customer')
                    ,DB::raw('SUM(a.po_qty) as po_qty')
                    ,DB::raw('a.family as family')
                    ,DB::raw('a.type_of_inspection as type_of_inspection')
                    ,DB::raw('a.severity_of_inspection as severity_of_inspection')
                    ,DB::raw('a.inspection_lvl as inspection_lvl')
                    ,DB::raw('a.aql as aql')
                    ,DB::raw('a.accept as accept')
                    ,DB::raw('a.reject as reject')
                    ,DB::raw('a.coc_req as coc_req')
                    ,DB::raw('a.lot_inspected as lot_inspected')
                    ,DB::raw('a.lot_accepted as lot_accepted')
                    ,DB::raw('a.dbcon as dbcon')
                    ,DB::raw("IF(judgement = 'Accept','NDF',a.modid) as modid")
                    ,DB::raw('a.type as type'))
                ->get();

        $dt = Carbon::now();
        $company_info = $this->com->getCompanyInfo();
        $date = substr($dt->format('  M j, Y  h:i A '), 2);

        if (count((array)$details) > 0) {
            $data = [
                'company_info' => $company_info,
                'details' => $details,
                'header' => $header,
                'date' => $date,
                'po' => $po
            ];

            $pdf = PDF::loadView('pdf.oqcwithpo', $data)
                        ->setPaper('A4')
                        ->setOption('margin-top', 10)
                        ->setOption('margin-bottom', 5)
                        ->setOption('margin-left', 2)
                        ->setOption('margin-right', 2)
                        ->setOrientation('landscape');

            return $pdf->inline('OQC_Inspection_'.Carbon::now());
        }
    }

    public function ExcelReport(Request $req)
    {
        $dt = Carbon::now();
        $dates = substr($dt->format('Ymd'), 2);

        Excel::create('OQC_Inspection_Report'.$dates, function($excel) use($dt,$req)
        {
            $com_info = $this->com->getCompanyInfo();
            $date_today = substr($dt->format('  M j, Y  h:i A '), 2);

            $date = '';
            if ($req->from !== '' || !empty($req->from)) {
                $date = " AND a.date_inspected BETWEEN '".$this->com->convertDate($req->from,'Y-m-d').
                "' AND '".$this->com->convertDate($req->to,'Y-m-d')."'";
            }

            if ($req->po !== '' || !empty($req->po)) {
                $po = " AND a.po_no = '".$req->po."'";
            }

            $infos = DB::connection($this->mysql)->table('oqc_inspections as a')
                        ->leftJoin('oqc_inspections_mod as b', function ($join) {
                            $join->on('a.po_no','=','b.pono');
                            $join->on('a.submission','=','b.submission');
                        })
                        ->whereRaw("1=1".$po.$date)
                        ->groupBy('a.po_no','a.device_name','a.date_inspected','a.submission','a.judgement',
                                'a.prod_category','a.customer','a.severity_of_inspection',
                                'a.inspection_lvl','a.aql','a.accept','a.reject','a.coc_req',
                                'a.type_of_inspection','a.po_qty')
                        ->select(DB::raw('a.fy as fy')
                            ,DB::raw('a.ww as ww')
                            ,DB::raw('a.date_inspected as date_inspected')
                            ,DB::raw('a.shift as shift')
                            ,DB::raw('a.time_ins_from as time_ins_from')
                            ,DB::raw('a.time_ins_to as time_ins_to')
                            ,DB::raw('a.submission as submission')
                            ,DB::raw('a.lot_qty as lot_qty')
                            ,DB::raw('a.sample_size as sample_size')
                            ,DB::raw('a.num_of_defects as num_of_defects')
                            ,DB::raw('a.lot_no as lot_no')
                            ,DB::raw('b.mod1 as mod1')
                            ,DB::raw("IFNULL(SUM(b.qty),0) as qty")
                            ,DB::raw('a.judgement as judgement')
                            ,DB::raw('a.inspector as inspector')
                            ,DB::raw('a.remarks as remarks')
                            ,DB::raw('a.assembly_line as assembly_line')
                            ,DB::raw('a.app_date as app_date')
                            ,DB::raw('a.app_time as app_time')
                            ,DB::raw('a.prod_category as prod_category')
                            ,DB::raw('a.po_no as po_no')
                            ,DB::raw('a.device_name as device_name')
                            ,DB::raw('a.customer as customer')
                            ,DB::raw('a.po_qty as po_qty')
                            ,DB::raw('a.family as family')
                            ,DB::raw('a.type_of_inspection as type_of_inspection')
                            ,DB::raw('a.severity_of_inspection as severity_of_inspection')
                            ,DB::raw('a.inspection_lvl as inspection_lvl')
                            ,DB::raw('a.aql as aql')
                            ,DB::raw('a.accept as accept')
                            ,DB::raw('a.reject as reject')
                            ,DB::raw('a.coc_req as coc_req')
                            ,DB::raw('a.lot_inspected as lot_inspected')
                            ,DB::raw('a.lot_accepted as lot_accepted')
                            ,DB::raw('a.dbcon as dbcon')
                            ,DB::raw("IF(judgement = 'Accept','NDF',a.modid) as modid")
                            ,DB::raw('a.type as type'))
                        ->get();

            foreach ($infos as $key => $info) {
                $excel->sheet($info->po_no, function($sheet) use($com_info,$date,$info,$date_today)
                {
                    $sheet->setFreeze('A13');
                    $date = '';
                    $po = '';

                    $details = DB::connection($this->mysql)->table('oqc_inspections as a')
                                    ->leftJoin('oqc_inspections_mod as b', function ($join) {
                                        $join->on('a.po_no','=','b.pono');
                                        $join->on('a.submission','=','b.submission');
                                    })
                                    ->where('po_no',$info->po_no)
                                    ->groupBy('a.po_no','a.device_name','a.date_inspected','a.submission','a.judgement',
                                            'a.prod_category','a.customer','a.severity_of_inspection',
                                            'a.inspection_lvl','a.aql','a.accept','a.reject','a.coc_req',
                                            'a.type_of_inspection','a.po_qty')
                                    ->select(DB::raw('a.fy as fy')
                                        ,DB::raw('a.ww as ww')
                                        ,DB::raw('a.date_inspected as date_inspected')
                                        ,DB::raw('a.shift as shift')
                                        ,DB::raw('a.time_ins_from as time_ins_from')
                                        ,DB::raw('a.time_ins_to as time_ins_to')
                                        ,DB::raw('a.submission as submission')
                                        ,DB::raw('a.lot_qty as lot_qty')
                                        ,DB::raw('a.sample_size as sample_size')
                                        ,DB::raw('a.num_of_defects as num_of_defects')
                                        ,DB::raw('a.lot_no as lot_no')
                                        ,DB::raw('b.mod1 as mod1')
                                        ,DB::raw("IFNULL(SUM(b.qty),0) as qty")
                                        ,DB::raw('a.judgement as judgement')
                                        ,DB::raw('a.inspector as inspector')
                                        ,DB::raw('a.remarks as remarks')
                                        ,DB::raw('a.assembly_line as assembly_line')
                                        ,DB::raw('a.app_date as app_date')
                                        ,DB::raw('a.app_time as app_time')
                                        ,DB::raw('a.prod_category as prod_category')
                                        ,DB::raw('a.po_no as po_no')
                                        ,DB::raw('a.device_name as device_name')
                                        ,DB::raw('a.customer as customer')
                                        ,DB::raw('a.po_qty as po_qty')
                                        ,DB::raw('a.family as family')
                                        ,DB::raw('a.type_of_inspection as type_of_inspection')
                                        ,DB::raw('a.severity_of_inspection as severity_of_inspection')
                                        ,DB::raw('a.inspection_lvl as inspection_lvl')
                                        ,DB::raw('a.aql as aql')
                                        ,DB::raw('a.accept as accept')
                                        ,DB::raw('a.reject as reject')
                                        ,DB::raw('a.coc_req as coc_req')
                                        ,DB::raw('a.lot_inspected as lot_inspected')
                                        ,DB::raw('a.lot_accepted as lot_accepted')
                                        ,DB::raw('a.dbcon as dbcon')
                                        ,DB::raw("IF(judgement = 'Accept','NDF',a.modid) as modid")
                                        ,DB::raw('a.type as type'))
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
                    $sheet->cell('C'.$row, $date_today);
                    $sheet->setHeight($row,20);
                });
            }

        })->download('xls');
    }

    public function GroupByValues(Request $req)
    {
        $data = DB::connection($this->mysql)->table('oqc_inspections')
                ->select($req->field.' as field')
                ->orderBy($req->field)
                ->distinct()
                ->get();

        return $data;
    }

    public function CalculateDPPM(Request $req)
    {
        return $this->DPPMTables($req,false);
    }

    private function DPPMTables($req,$join)
    {
        $g1 = ''; $g2 = ''; $g3 = '';
        $g1c = ''; $g2c = ''; $g3c = '';
        $date_inspected = '';
        $groupBy = [];

        // wheres
        if (!empty($req->gfrom) && !empty($req->gto)) {
            $date_inspected = " AND date_inspected BETWEEN '".$this->com->convertDate($req->gfrom,'Y-m-d')."' AND '".$this->com->convertDate($req->gto,'Y-m-d')."'";
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
                    ->select("SELECT SUM(lot_qty) AS lot_qty,
                                    SUM(sample_size) AS sample_size,
                                    SUM(num_of_defects) AS num_of_defects,
                                    SUM(lot_accepted) AS lot_accepted,
                                    SUM(lot_rejected) AS lot_rejected,
                                    SUM(lot_inspected) AS lot_inspected,
                                    fy,ww,date_inspected,shift,
                                    time_ins_from,time_ins_to,submission,
                                    lot_no,judgement,inspector,remarks,
                                    assembly_line,customer,po_no,aql,
                                    prod_category,family,device_name
                                FROM oqc_inspections
                                WHERE 1=1".$date_inspected.$g1c.$g2c.$g3c.$grby);
        } else {

            $db = DB::connection($this->mysql)
                ->select("SELECT SUM(i.lot_qty) AS lot_qty,
                                SUM(i.sample_size) AS sample_size,
                                SUM(i.num_of_defects) AS num_of_defects,
                                SUM(i.lot_accepted) AS lot_accepted,
                                SUM(i.lot_rejected) AS lot_rejected,
                                SUM(i.lot_inspected) AS lot_inspected,
                                fy,ww,date_inspected,shift,
                                time_ins_from,time_ins_to,submission,
                                lot_no,judgement,inspector,remarks,
                                assembly_line,customer,po_no,aql,
                                prod_category,family,device_name
                            FROM oqc_inspections as i
                        LEFT JOIN oqc_inspections_mod as m ON i.po_no = m.pono
                        WHERE 1=1".$date_inspected.$g1c.$g2c.$g3c.$grby);
        }

        if ($this->com->checkIfExistObject($db) > 0) {
            return $db;
        }
    }

    public function SamplingPlan(Request $req)
    {
        $code = DB::connection($this->mysql)->table('oqc_sampling_plan_inspection_level')
                    ->whereRaw($req->lot_qty .' BETWEEN size_from AND size_to')
                    ->select(DB::raw($req->il.' as code'))
                    ->first();
        return $this->getSamplingPlanValues($req,$code->code);
    }

    private function getSamplingPlanValues($req,$code)
    {
        $type = '';
        $severity_size = '';
        $data = [];
        switch ($req->soi) {
            case 'Normal':
                $severity_size = 'sample_size_normal';
                $type = 'Normal';
                break;

            case 'Tightened':
                $severity_size = 'sample_size_tightened';
                $type = 'Tightened';
                break;

            case 'Reduced':
                $severity_size = 'sample_size_reduced';
                $type = 'Reduced';
                break;

            default:
                # code...
                break;
        }

        if (is_numeric($req->aql)) {
            $size = DB::connection($this->mysql)->table('oqc_sampling_plan_sample_size')
                        ->where('sample_size_code',$code)
                        ->select($severity_size.' as size')
                        ->first();
            $plan = DB::connection($this->mysql)->table('oqc_aql_ac_re')
                        ->where('size',$size->size)
                        ->where('type_of_inspection',$type)
                        ->select('size',
                                DB::raw("`".$req->aql."_ac` as accept"),
                                DB::raw("`".$req->aql."_re` as reject"))
                        ->first();
            if ($plan->accept == null) {
                $splan = DB::connection($this->mysql)->table('oqc_aql_ac_re')
                        ->where('size',$plan->reject)
                        ->where('type_of_inspection',$type)
                        ->select('size',
                                DB::raw("`".$req->aql."_ac` as accept"),
                                DB::raw("`".$req->aql."_re` as reject"))
                        ->first();

                if ($req->lot_qty >= $splan->size) {
                    $data = [
                        'size' => $splan->size,
                        'accept' => $splan->accept,
                        'reject' => $splan->reject
                    ];
                } else {
                    $data = [
                        'size' => $req->lot_qty,
                        'accept' => $splan->accept,
                        'reject' => $splan->reject
                    ];
                }
                
                return response()->json($data);
            }

            if ($req->lot_qty >= $plan->size) {
                $data = [
                    'size' => $plan->size,
                    'accept' => $plan->accept,
                    'reject' => $plan->reject
                ];
            } else {
                $data = [
                    'size' => $req->lot_qty,
                    'accept' => $plan->accept,
                    'reject' => $plan->reject
                ];
            }
        } else {
            //return response()->json($data = ['gago' => 'gago']);
            return $this->nonNumericAQL($req);
        }
        

        return response()->json($data);
    }

    public function nonNumericAQL($req)
    {
        $data = [
            'ins_lvl' => 'II',
            'size' => 0,
            'accept' => 0,
            'reject' => 1
        ];

        if ($req->lot_qty >= 1 && $req->lot_qty <= 8) {
            $data = [
                'ins_lvl' => 'II',
                'size' => 2,
                'accept' => 0,
                'reject' => 1
            ];
        }

        if ($req->lot_qty >= 9 && $req->lot_qty <= 15) {
            $data = [
                'ins_lvl' => 'II',
                'size' => 3,
                'accept' => 0,
                'reject' => 1
            ];
        }

        if ($req->lot_qty >= 16 && $req->lot_qty <= 25) {
            $data = [
                'ins_lvl' => 'II',
                'size' => 5,
                'accept' => 0,
                'reject' => 1
            ];
        }

        if ($req->lot_qty >= 26 && $req->lot_qty <= 50) {
            $data = [
                'ins_lvl' => 'II',
                'size' => 8,
                'accept' => 0,
                'reject' => 1
            ];
        }

        if ($req->lot_qty >= 51) {
            $data = [
                'ins_lvl' => 'II',
                'size' => 13,
                'accept' => 0,
                'reject' => 1
            ];
        }

        return response()->json($data);
    }

    public function getNumOfDefectives(Request $req)
    {
        $db = DB::connection($this->mysql)->table('oqc_inspections_mod')
                ->where('modid',$req->id)
                ->select(
                    DB::raw("SUM(qty) as no_of_defectives")
                )
                ->groupBy('modid')->first();
        if (count((array)$db) > 0) {
            return $db->no_of_defectives;
        } else {
            return 0;
        }
    }

    public function getShift(Request $req)
    {
        $data = [];
        $from = $this->convertTime($req->from);
        $to = $this->convertTime($req->to);
        $shift = DB::connection($this->mysql)->table('oqc_shift')
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
}
