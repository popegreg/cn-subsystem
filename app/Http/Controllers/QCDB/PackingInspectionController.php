<?php
namespace App\Http\Controllers\QCDB;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Config;
use App\PackingInspection;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth; #Auth facade
use Dompdf\Dompdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Excel;

class PackingInspectionController extends Controller
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

    public function getPackingInspection()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PCKNGDB')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('qcdb.packinginspection',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function initData()
    {
        $packingtype = $this->com->getDropdownByName('Packing Type for OQC Inspection');
        $unitcondition = $this->com->getDropdownByName('Unit Condition for OQC Inspection');
        $packingcodeperseries = $this->com->getDropdownByName('Packing Code(Per Series) for OQC Inspection');
        $mod = $this->com->getDropdownByName('Mode of Defect - Packing Inspection');
        $packingoperator = $this->com->getDropdownByName('Packing Operator for OQC Inspection');

        return $data = [
            'packingtypes'=>$packingtype,
            'unitconditions'=>$unitcondition,
            'packingcodeperseries'=>$packingcodeperseries,
            'mods'=>$mod,
            'packingoperators'=>$packingoperator
        ];
    }

    public function searchby(Request $request)
    {
        $datefrom = $request->datefrom;
        $dateto = $request->dateto;
        $pono = $request->pono;
       
        if($pono == ""){
            $table = DB::connection($this->mysql)->table('packing_inspections')
                    ->whereBetween('date_inspected', [$datefrom,$dateto])
                    ->get();   
        } 
        if($pono && $datefrom && $dateto) {
            $table = DB::connection($this->mysql)->table('packing_inspections')
                    ->where('po_num','=',$pono)
                    ->whereBetween('date_inspected', [$datefrom,$dateto])
                    ->get(); 
        } 
        if($pono){
            $table = DB::connection($this->mysql)->table('packing_inspections')
                    ->where('po_num','=',$pono)
                    ->get();
        }
        
        return $table;
    }

    public function getpackingYPICSrecords(Request $request)
    {
        $msrecords = DB::connection($this->mssql)
                        ->select("SELECT R.SORDER as PO,
                                        R.CODE as devicecode,
                                        H.NAME as DEVNAME,
                                        SUM(R.KVOL) as POQTY
                                FROM XRECE as R
                                LEFT JOIN XHEAD as H ON R.CODE = H.CODE
                                WHERE R.SORDER = '".$request->pono."'
                                GROUP BY R.SORDER,
                                        R.CODE,
                                        H.NAME");
        return $msrecords;
    }

    public function getlot(Request $request)
    {
        $pono = $request->pono;
        $cartonno = $request->cartonno;
        $table = DB::connection($this->mysql)->table('tbl_packinginspection_runcard')
                        ->where('pono',$pono)
                        ->where('carton_no',$cartonno)
                        ->select(DB::raw("SUM(runcard_qty) as qty"))
                        ->get();        
        return $table;
    }

    public function packing_runcard_edit(Request $request)
    {
        $data = DB::connection($this->mysql)->table('tbl_packinginspection_runcard')->where('id',$request->id)->get();
        return $data;
    }

    public function packgetrows(Request $req)
    {
        $date = "";
        $pono = "";
        $g1c = "";
        $g2c = "";
        $g3c = "";
        $g4c = "";
        $g5c = "";

        $table = DB::connection($this->mysql)
                    ->table('packing_inspections')
                    ->orderBy('id','desc')
                    ->select(['id',
                            'date_inspected',
                            'shipment_date',
                            'device_name',
                            'inspector',
                            'packing_type',
                            'unit_condition',
                            'packing_operator',
                            'remarks',
                            'packing_code_series',
                            'carton_num',
                            'packing_code',
                            'judgement',
                            'total_qty',
                            'mode_of_defect',
                            'dbcon',
                            'po_num']);
       
        if ($req->mode =='search') {
            if ($req->datefrom !== "") {
                $date=" AND date_inspected BETWEEN '".$this->convertDate($req->datefrom,'Y-m-d')."' AND '".$this->convertDate($req->dateto,'Y-m-d')."'";
            }

            if ($req->pono !== "") {
                $pono = " AND po_num = '".$req->pono."'";
            }

            $table = DB::connection($this->mysql)
                        ->table('packing_inspections')
                        ->whereRaw("1=1".$date.$pono)
                        ->orderBy('id','desc')
                        ->select(['id',
                                'date_inspected',
                                'shipment_date',
                                'device_name',
                                'inspector',
                                'packing_type',
                                'unit_condition',
                                'packing_operator',
                                'remarks',
                                'packing_code_series',
                                'carton_num',
                                'packing_code',
                                'judgement',
                                'total_qty',
                                'mode_of_defect',
                                'dbcon',
                                'po_num']);
        }

        if ($req->mode == 'group') {
            if ($req->datefrom !== "") {
                $date=" AND date_inspected BETWEEN '".$this->convertDate($req->datefrom,'Y-m-d')."' AND '".$this->convertDate($req->dateto,'Y-m-d')."'";
            }

            if ($req->g1 !== "" || $req->g1 !== null || $req->g1content !== "" || $req->g1content !== null) {
                $g1c = " AND ".$req->g1." = ".$req->g1content;
            }
            if ($req->g2 !== "" || $req->g2 !== null || $req->g2content !== "" || $req->g2content !== null) {
                $g2c = " AND ".$req->g2." = ".$req->g2content;
            }
            if ($req->g3 !== "" || $req->g3 !== null || $req->g3content !== "" || $req->g3content !== null) {
                $g3c = " AND ".$req->g3." = ".$req->g3content;
            }

            if ($req->g1 !== "" || $req->g1 !== null || $req->g1content !== "" || $req->g1content !== null) {
                $table = DB::connection($this->mysql)
                        ->table('packing_inspections')
                        ->whereRaw("1=1".$date.$g1c)
                        ->groupBy($req->g1)
                        ->orderBy('id','desc')
                        ->select(['id',
                                'date_inspected',
                                'shipment_date',
                                'device_name',
                                'inspector',
                                'packing_type',
                                'unit_condition',
                                'packing_operator',
                                'remarks',
                                'packing_code_series',
                                'carton_num',
                                'packing_code',
                                'judgement',
                                'total_qty',
                                'mode_of_defect',
                                'dbcon',
                                'po_num']);
            }

            if ($req->g2 !== "" || $req->g2 !== null || $req->g2content !== "" || $req->g2content !== null) {
                $table = DB::connection($this->mysql)
                        ->table('packing_inspections')
                        ->whereRaw("1=1".$date.$g1c.$g2c)
                        ->groupBy($req->g1,$req->g2)
                        ->orderBy('id','desc')
                        ->select(['id',
                                'date_inspected',
                                'shipment_date',
                                'device_name',
                                'inspector',
                                'packing_type',
                                'unit_condition',
                                'packing_operator',
                                'remarks',
                                'packing_code_series',
                                'carton_num',
                                'packing_code',
                                'judgement',
                                'total_qty',
                                'mode_of_defect',
                                'dbcon',
                                'po_num']);
            }

            if ($req->g3 !== "" || $req->g3 !== null || $req->g3content !== "" || $req->g3content !== null) {
                $table = DB::connection($this->mysql)
                        ->table('packing_inspections')
                        ->whereRaw("1=1".$date.$g1c.$g2c.$g3c)
                        ->groupBy($req->g1,$req->g2,$req->g3)
                        ->orderBy('id','desc')
                        ->select(['id',
                                'date_inspected',
                                'shipment_date',
                                'device_name',
                                'inspector',
                                'packing_type',
                                'unit_condition',
                                'packing_operator',
                                'remarks',
                                'packing_code_series',
                                'carton_num',
                                'packing_code',
                                'judgement',
                                'total_qty',
                                'mode_of_defect',
                                'dbcon',
                                'po_num']);
            }
            
        }

        return Datatables::of($table)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->editColumn('date_inspected', function($data) {
                            return $data->date_inspected;
                        })
                        ->editColumn('shipment_date', function($data) {
                            return $data->shipment_date;
                        })
                        ->editColumn('device_name', function($data) {
                            return $data->device_name;
                        })
                        ->editColumn('po_num', function($data) {
                            return $data->po_num;
                        })
                        ->editColumn('packing_operator', function($data) {
                            return $data->packing_operator;
                        })
                        ->editColumn('inspector', function($data) {
                            return $data->inspector;
                        })
                        ->editColumn('packing_type', function($data) {
                            return $data->packing_type;
                        })
                        ->editColumn('unit_condition', function($data) {
                            return $data->unit_condition;
                        })
                        ->editColumn('packing_code_series', function($data) {
                            return $data->packing_code_series;
                        })
                        ->editColumn('carton_num', function($data) {
                            return $data->carton_num;
                        })
                        ->editColumn('packing_code', function($data) {
                            return $data->packing_code;
                        })
                        ->editColumn('total_qty', function($data) {
                            return $data->total_qty;
                        })
                        ->editColumn('judgement', function($data) {
                            return $data->judgement;
                        })
                        ->editColumn('remarks', function($data) {
                            return $data->remarks;
                        })
                        ->addColumn('action', function($data) {
                            return '<button type="button" name="edit-task" class="btn input-sm btn-primary edit-task" value="'.$data->id. '|' .$data->date_inspected. '|' .$data->shipment_date. '|' .$data->device_name. '|' .$data->inspector. '|' .$data->packing_type. '|' .$data->unit_condition. '|' .$data->packing_operator. '|' .$data->remarks. '|' .$data->packing_code_series. '|' .$data->carton_num. '|' .$data->packing_code. '|' .$data->judgement. '|' .$data->total_qty. '|' .$data->mode_of_defect. '|' .$data->dbcon. '|' .$data->po_num.'">
                                    <i class="fa fa-edit"></i>
                                </button>';
                        })
                        ->make(true);
    }
   
    public function getPackingInspectionData()
    {
        $data = PackingInspection::all();
        return Datatables::of($data)->make(true);
    }

    public function packingSave(Request $request)
    {
        $f =  $request->data;
        $status = $f['status'];
        if($status == "ADD"){
            DB::connection($this->mysql)->table('packing_inspections')
            ->insert([
                    'po_num' => $f['pono'],
                    'date_inspected' => $f['inspdate'],
                    'shipment_date' => $f['shipdate'],
                    'device_name' => $f['seriesname'],
                    'inspector' => $f['inspector'],
                    'packing_type' => $f['packingtype'],
                    'unit_condition' => $f['unitcondition'],
                    'packing_operator' => $f['packingoperator'],
                    'remarks' => $f['remarks'],
                    'packing_code_series' => $f['packcodeperseries'],
                    'carton_num' => $f['cartonno'],
                    'packing_code' => $f['packcode'],
                    'judgement' => $f['judgement'],
                    'total_qty' => $f['totalqty'],
                    'mode_of_defect' => $f['mod'],
                    'dbcon' => $f['dbcon']
                ]);    
        } 
        if($status == "EDIT"){
            DB::connection($this->mysql)->table('packing_inspections')
            ->where('id','=',$f['id'])
            ->update(array(
                    'po_num' => $f['pono'],
                    'date_inspected' => $f['inspdate'],
                    'shipment_date' => $f['shipdate'],
                    'device_name' => $f['seriesname'],
                    'inspector' => $f['inspector'],
                    'packing_type' => $f['packingtype'],
                    'unit_condition' => $f['unitcondition'],
                    'packing_operator' => $f['packingoperator'],
                    'remarks' => $f['remarks'],
                    'packing_code_series' => $f['packcodeperseries'],
                    'carton_num' => $f['cartonno'],
                    'packing_code' => $f['packcode'],
                    'judgement' => $f['judgement'],
                    'total_qty' => $f['totalqty'],
                    'mode_of_defect' => $f['mod'],
                    'dbcon' => $f['dbcon']
                ));
        }
        
    }

    public function display_runcard(Request $request)
    {
        $rctableData = DB::connection($this->mysql)->table("tbl_packinginspection_runcard")
        ->where('pono',$request->pono)
        ->where('carton_no',$request->cartonno)
        ->get(); 
        return $rctableData;   
    }

    private function displayruncard($pono,$cartonno)
    {
        $rctableData = DB::connection($this->mysql)->table("tbl_packinginspection_runcard")->where('pono',$pono)->where('carton_no',$cartonno)->get(); 
        return $rctableData;
    }

    public function packing_runcard_Save(Request $request)
    {
        $f =  $request->data;
        $status = $f['rcstatus'];

        if($status == "ADD"){
            $cartonno = $f['carton_no'];
            DB::connection($this->mysql)->table('tbl_packinginspection_runcard')
            ->insert([
                    'pono' => $f['pono'],
                    'carton_no' => $cartonno,
                    'runcard_no' => $f['rcno'],
                    'runcard_qty' => $f['rcqty'],
                    'runcard_remarks' => $f['rcremarks'],  
                ]);    
        } 
        if($status == "EDIT"){
            $cartonno = $f['carton_no'];
            DB::connection($this->mysql)->table('tbl_packinginspection_runcard')
            ->where('id','=',$f['rcid'])
            ->update(array(
                    'pono' => $f['pono'],
                    'carton_no' => $cartonno,
                    'runcard_no' => $f['rcno'],
                    'runcard_qty' => $f['rcqty'],
                    'runcard_remarks' => $f['rcremarks'],  
                ));
        }
        return $this->displayruncard($f['pono'],$cartonno);
    }

    public function packingDelete(Request $request)
    {  
        $tray = $request->tray;
        $traycount = $request->traycount;  
        /*return $tray;  */
        if($traycount > 0){
            $ok = DB::connection($this->mysql)->table('packing_inspections')->wherein('id',$tray)->delete();
        } 
    }

    public function rcpackingEdit(Request $request)
    {
        $id = $request->id;
        $table = DB::connection($this->mysql)->table('tbl_packinginspection_runcard')->where('id','=',$id)->get();   
        return $table;
    }

    public function rcpackingDelete(Request $request)
    {  
        $tray = $request->tray;
        $traycount = $request->traycount;  
        $pono = $request->pono;
        $cartonno = $request->cartonno;
      /*  return $tray;  */
        if($traycount > 0){
            DB::connection($this->mysql)->table('tbl_packinginspection_runcard')->wherein('id',$tray)->delete();  
        } 
        return $this->displayruncard($pono,$cartonno);
    }

    public function getPACKINGreport(Request $request)
    {
        $data = json_decode($request->data);
        $date_inspected = $data->date_inspected;
        $shipment_date = $data->shipment_date;
        $device_name = $data->device_name;
        $po_num = $data->po_num;
        $packing_operator = $data->packing_operator;
        $inspector = $data->inspector;
        $packing_type = $data->packing_type;
        $unit_condition = $data->unit_condition;
        $packing_code_series = $data->packing_code_series;
        $carton_num = $data->carton_num;
        $packing_code = $data->packing_code;
        $total_qty = $data->total_qty;
        $judgement = $data->judgement;
        $remarks = $data->remarks;
        $searchpono = $data->searchpono;
        $datefrom = $data->datefrom;
        $dateto = $data->dateto;
        $status = $data->status;

        if($status == "SEARCH"){
                    if($searchpono == "" && $datefrom && $dateto){
                        $table = DB::connection($this->mysql)->table('packing_inspections')->whereBetween('date_inspected',[$datefrom,$dateto])->get();

                    }
                    if($searchpono){
                        $table = DB::connection($this->mysql)->table('packing_inspections')->where('po_num',$searchpono)->get();
                    }
                    if($searchpono && $datefrom && $dateto){
                        $table = DB::connection($this->mysql)->table('packing_inspections')->where('po_num',$searchpono)->whereBetween('date_inspected',[$datefrom,$dateto])->get();
                    }
                    $sql = DB::connection($this->mssql)
                            ->table('XHIKI as d')
                            ->join('XHEAD as h','d.OYACODE','=','h.CODE')
                            ->select(DB::raw("SUM(d.KVOL) as poqty"))
                            ->where('d.SEIBAN',$searchpono)
                            ->groupBy('d.SEIBAN','d.OYACODE','h.NAME')
                            ->get();

                    $html1 = '<style type="text/css" scoped>
                    
                    #data {
                      border-collapse: collapse;
                      width: 100%;
                      font-size:10px;
                      text-align:center;
                    }

                    #data thead td {
                      border: 1px solid black;
                      text-align: center;
                    }

                    #data tbody td {
                      border-bottom: 1px solid black;
                      alignment:center;
                    }

                    #info {
                      width: 100%;
                      font-size:10px;
                    }

                    .text-center {
                        text-align: center;
                    }

                    #info {
                      width: 100%;
                      font-size:11px
                    }
                    #info thead td {
                      text-align: center;
                    }

                    .font {
                        font-family: "Trebuchet MS", Helvetica, sans-serif;
                    }

                    .label{
                        font-size:10px;
                    }

                    </style>
                    <table id="info">
                        <thead>
                            <tr bgcolor="#ADD8E6">
                                <td colspan="6">
                                    <h2>TS-MFG FOR OQC INSPECTION</h2>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-left">Device Name</td>
                                <td>'.$table[0]->device_name.'</td>
                                <td class="align-left"></td>
                                <td></td>
                                <td class="align-left">Packing Type</td>
                                <td>'.$table[0]->packing_type.'</td>
                                
                            </tr>
                            <tr>
                                <td class="align-left">P.O Number</td>
                                <td>'.$table[0]->po_num.'</td>
                                <td class="align-left"></td>
                                <td></td>
                                <td class="align-left">Unit Condition</td>
                                <td>'.$table[0]->unit_condition.'</td>
                            </tr>
                            <tr>
                                <td class="align-left">P.O Quantity</td>
                                <td>'.$sql[0]->poqty.'</td>
                                <td class="align-left"></td>
                                <td></td>
                                <td class="align-left"></td>
                                <td></td>
                            </tr>
                        </thead>
                    </table>
                    <br>
                    <table id="data" border="1">
                        <thead>
                            <tr>
                                <td>Date Inspected</td>
                                <td>Shipment Date</td>
                                <td>Series Name</td>
                                <td>PO #</td>
                                <td>Packing Operator</td>
                                <td>Inspector</td>
                                <td>Packing Type</td>
                                <td>Unit Condition</td>
                                <td>Packing Code(Per Series)</td>
                                <td>Carton #</td>
                                <td>Packing Code</td>
                                <td>Qty</td>
                                <td>Judgement</td>
                                <td>remarks</td>
                            </tr>
                        </thead>
                        <tbody>';

            $html3 = '</tbody>
                      </table>';

            $html2 = '';
            
            $deduction = DB::connection($this->mysql)->table('tbl_packinginspection_runcard')->select(DB::raw("SUM(runcard_qty) as qty"))->where('pono',$searchpono)->get();
            $balance = $sql[0]->poqty - $deduction[0]->qty;
            $html4 = '<table width="100%">
                    <tr>
                        <td class="label">Total Qty:</td>
                        <td class="label">'.$table[0]->total_qty.'</td>
                        <td class="label"></td>
                        <td class="label">Balance:</td>
                        <td class="label">'.$balance.'</td>
                        <td class="label"></td>
                        <td class="label">Date:</td>
                        <td class="label">'.Carbon::now().'</td>
                    </tr>
                </table>';

            foreach ($po_num as $key => $v) {
               $html2 .= '<tr>
                    <td>'.$date_inspected[$key].'</td>
                    <td>'.$shipment_date[$key].'</td>
                    <td>'.$device_name[$key].'</td>
                    <td>'.$po_num[$key].'</td>
                    <td>'.$packing_operator[$key].'</td>
                    <td>'.$inspector[$key].'</td>
                    <td>'.$packing_type[$key].'</td>
                    <td>'.$unit_condition[$key].'</td>
                    <td>'.$packing_code_series[$key].'</td>
                    <td>'.$carton_num[$key].'</td>
                    <td>'.$packing_code[$key].'</td>
                    <td>'.$total_qty[$key].'</td>
                    <td>'.$judgement[$key].'</td>
                    <td>'.$remarks[$key].'</td>   
                </tr>';
            }

            $html_final = $html1.$html2.$html3.$html4;
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html_final);
            $dompdf->setPaper('letter', 'landscape');
            $dompdf->render();
            $dompdf->stream('Packing_Inspection'.Carbon::now().'.pdf');    
            
        } else {
            $html1 = '<style type="text/css" scoped>
                        
                    #data {
                      border-collapse: collapse;
                      width: 100%;
                      font-size:10px;
                      text-align:center;
                    }

                    #data thead td {
                      border: 1px solid black;
                      text-align: center;
                    }

                    #data tbody td {
                      border-bottom: 1px solid black;
                      alignment:center;
                    }

                    #info {
                      width: 100%;
                      font-size:10px;
                    }

                    .text-center {
                        text-align: center;
                    }

                    #info {
                      width: 100%;
                      font-size:11px
                    }
                    #info thead td {
                      text-align: center;
                    }

                    .font {
                        font-family: "Trebuchet MS", Helvetica, sans-serif;
                    }

                    .label{
                        font-size:10px;
                    }

                    </style>
                    <table id="info">
                        <thead>
                            <tr bgcolor="#ADD8E6">
                                <td colspan="6">
                                    <h2>TS-MFG FOR OQC INSPECTION</h2>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-left">Device Name</td>
                                <td></td>
                                <td class="align-left"></td>
                                <td></td>
                                <td class="align-left">Packing Type</td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td class="align-left">P.O Number</td>
                                <td></td>
                                <td class="align-left"></td>
                                <td></td>
                                <td class="align-left">Unit Condition</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="align-left">P.O Quantity</td>
                                <td></td>
                                <td class="align-left"></td>
                                <td></td>
                                <td class="align-left"></td>
                                <td></td>
                            </tr>
                        </thead>
                    </table>
                    <br>
                    <table id="data" border="1">
                        <thead>
                            <tr>
                                <td>Date Inspected</td>
                                <td>Shipment Date</td>
                                <td>Series Name</td>
                                <td>PO #</td>
                                <td>Packing Operator</td>
                                <td>Inspector</td>
                                <td>Packing Type</td>
                                <td>Unit Condition</td>
                                <td>Packing Code(Per Series)</td>
                                <td>Carton #</td>
                                <td>Packing Code</td>
                                <td>Qty</td>
                                <td>Judgement</td>
                                <td>remarks</td>
                            </tr>
                        </thead>
                        <tbody>';

            $html3 = '</tbody>
                      </table>';

            $html2 = '';

            $html4 = '<table width="100%">
                    <tr>
                        <td class="label">Total Qty:</td>
                        <td class="label"></td>
                        <td class="label"></td>
                        <td class="label">Balance:</td>
                        <td class="label"></td>
                        <td class="label"></td>
                        <td class="label">Date:</td>
                        <td class="label">'.Carbon::now().'</td>
                    </tr>
                </table>';
          
            foreach ($po_num as $key => $v) {
               $html2 .= '<tr>
                    <td>'.$date_inspected[$key].'</td>
                    <td>'.$shipment_date[$key].'</td>
                    <td>'.$device_name[$key].'</td>
                    <td>'.$po_num[$key].'</td>
                    <td>'.$packing_operator[$key].'</td>
                    <td>'.$inspector[$key].'</td>
                    <td>'.$packing_type[$key].'</td>
                    <td>'.$unit_condition[$key].'</td>
                    <td>'.$packing_code_series[$key].'</td>
                    <td>'.$carton_num[$key].'</td>
                    <td>'.$packing_code[$key].'</td>
                    <td>'.$total_qty[$key].'</td>
                    <td>'.$judgement[$key].'</td>
                    <td>'.$remarks[$key].'</td>   
                </tr>';
            }

            $html_final = $html1.$html2.$html3.$html4;
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html_final);
            $dompdf->setPaper('letter', 'landscape');
            $dompdf->render();
            $dompdf->stream('Packing_Inspection'.Carbon::now().'.pdf');    
        }
    }

    public function getPACKINGreportexcel(Request $request)
    { 
        $data = json_decode($request->data);
        $status = $data->status;

        if($status == "SEARCH"){
            try
            { 
                $dt = Carbon::now();
                $date = substr($dt->format('Ymd'), 2);
                
                Excel::create('PACKING_Inspection_Report'.$date, function($excel) use($request)
                {
                    $excel->sheet('Sheet1', function($sheet) use($request)
                    {
                       
                        $dt = Carbon::now();
                        $date = $dt->format('m/d/Y');
               
                        $sheet->setCellValue('A1', 'TS-MFG FOR OQC INSPECTION');
                        $sheet->mergeCells('A1:O1');

                        $data = json_decode($request->data);
                        $searchpono = $data->searchpono;
                        $datefrom = $data->datefrom;
                        $dateto = $data->dateto;

                        if($searchpono == "" && $datefrom && $dateto){
                            $table = DB::connection($this->mysql)->table('packing_inspections')->whereBetween('date_inspected',[$datefrom,$dateto])->get();
                        }
                        if($searchpono){
                            $table = DB::connection($this->mysql)->table('packing_inspections')->where('po_num',$searchpono)->get();
                        }
                        if($searchpono && $datefrom && $dateto){
                            $table = DB::connection($this->mysql)->table('packing_inspections')->where('po_num',$searchpono)->whereBetween('date_inspected',[$datefrom,$dateto])->get();
                        }
                        $sql = DB::connection($this->mssql)
                            ->table('XHIKI as d')
                            ->join('XHEAD as h','d.OYACODE','=','h.CODE')
                            ->select(DB::raw("SUM(d.KVOL) as poqty"))
                            ->where('d.SEIBAN',$searchpono)
                            ->groupBy('d.SEIBAN','d.OYACODE','h.NAME')
                            ->get();

                        $sheet->cell('C2',"Device Name");
                        $sheet->cell('C3',"P.O Number");
                        $sheet->cell('C4',"P.O Quantity");

                        $sheet->cell('D2',$table[0]->device_name);
                        $sheet->cell('D3',$table[0]->po_num);
                        $sheet->cell('D4',$sql[0]->poqty);
            
                        $sheet->cell('K2',"Packing Type");
                        $sheet->cell('K3',"Unit Condition");

                        $sheet->cell('L2',$table[0]->packing_type);
                        $sheet->cell('L3',$table[0]->unit_condition);

                        $sheet->cell('B6',"Date Inspected");
                        $sheet->cell('C6',"Shipment Date");
                        $sheet->cell('D6',"Device Name");
                        $sheet->cell('E6',"P.O #");
                        $sheet->cell('F6',"Packing Operator");
                        $sheet->cell('G6',"Inspector");
                        $sheet->cell('H6',"Packing Type");
                        $sheet->cell('I6',"Unit Condition");
                        $sheet->cell('J6',"Packing Code(Per Series)");
                        $sheet->cell('K6',"Carton #");
                        $sheet->cell('L6',"Packing Code");
                        $sheet->cell('M6',"Total Quantity");
                        $sheet->cell('N6',"Judgement");
                        $sheet->cell('O6',"Remarks");

                    
                        $sheet->setHeight(array(
                            1 => 30,
                            2 => 20,
                            3 => 20,
                            4 => 20,
                            6 => 20,
                           
                        ));
                        $sheet->row(1, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('#ADD8E6');
                            $row->setFontSize(20);
                            $row->setAlignment('center');
                        });
                        $sheet->row(6, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('#ADD8E6');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(2, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                        });
                        $sheet->row(3, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                        });
                        $sheet->row(4, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                        });
                        $sheet->setStyle(array(
                            'font' => array(
                                'name'  =>  'Calibri',
                                'size'  =>  10
                            )
                        ));
                        
                        $date_inspected = $data->date_inspected;
                        $shipment_date = $data->shipment_date;
                        $device_name = $data->device_name;
                        $po_num = $data->po_num;
                        $packing_operator = $data->packing_operator;
                        $inspector = $data->inspector;
                        $packing_type = $data->packing_type;
                        $unit_condition = $data->unit_condition;
                        $packing_code_series = $data->packing_code_series;
                        $carton_num = $data->carton_num;
                        $packing_code = $data->packing_code;
                        $total_qty = $data->total_qty;
                        $judgement = $data->judgement;
                        $remarks = $data->remarks;
                        

                        $row = 7;
                        foreach ($po_num as $key => $val) {
                            $sheet->cell('B'.$row, $date_inspected[$key]);
                            $sheet->cell('C'.$row, $shipment_date[$key]);
                            $sheet->cell('D'.$row, $device_name[$key]);
                            $sheet->cell('E'.$row, $po_num[$key]);
                            $sheet->cell('F'.$row, $packing_operator[$key]);
                            $sheet->cell('G'.$row, $inspector[$key]);
                            $sheet->cell('H'.$row, $packing_type[$key]);
                            $sheet->cell('I'.$row, $unit_condition[$key]);
                            $sheet->cell('J'.$row, $packing_code_series[$key]);
                            $sheet->cell('K'.$row, $carton_num[$key]);
                            $sheet->cell('L'.$row, $packing_code[$key]);
                            $sheet->cell('M'.$row, $total_qty[$key]);
                            $sheet->cell('N'.$row, $judgement[$key]);
                            $sheet->cell('O'.$row, $remarks[$key]);
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

        
                    $deduction = DB::connection($this->mysql)->table('tbl_packinginspection_runcard')->select(DB::raw("SUM(runcard_qty) as qty"))->where('pono',$searchpono)->get();
                    $balance = $sql[0]->poqty - $deduction[0]->qty;
                    $sheet->cell('B'.$row, "Total Qty:");
                    $sheet->cell('C'.$row, $table[0]->total_qty);
                    $sheet->cell('H'.$row, "Balance:");
                    $sheet->cell('I'.$row, $balance);
                    $sheet->cell('M'.$row, "Date:");
                    $sheet->cell('N'.$row, Carbon::now());
                    });

                })->download('xls');
            } catch (Exception $e) {
                return redirect(url('/packinginspection'))->with(['err_message' => $e]);
            }    
        } else {
            try
            { 
                $dt = Carbon::now();
                $date = substr($dt->format('Ymd'), 2);
                
                Excel::create('PACKING_Inspection_Report'.$date, function($excel) use($request)
                {
                    $excel->sheet('Sheet1', function($sheet) use($request)
                    {
                       
                        $dt = Carbon::now();
                        $date = $dt->format('m/d/Y');
               
                        $sheet->setCellValue('A1', 'TS-MFG FOR OQC INSPECTION');
                        $sheet->mergeCells('A1:O1');



                        $sheet->cell('C2',"Device Name");
                        $sheet->cell('C3',"P.O Number");
                        $sheet->cell('C4',"P.O Quantity");
            
                        $sheet->cell('K2',"Packing Type");
                        $sheet->cell('K3',"Unit Condition");

                        $sheet->cell('B6',"Date Inspected");
                        $sheet->cell('C6',"Shipment Date");
                        $sheet->cell('D6',"Device Name");
                        $sheet->cell('E6',"P.O #");
                        $sheet->cell('F6',"Packing Operator");
                        $sheet->cell('G6',"Inspector");
                        $sheet->cell('H6',"Packing Type");
                        $sheet->cell('I6',"Unit Condition");
                        $sheet->cell('J6',"Packing Code(Per Series)");
                        $sheet->cell('K6',"Carton #");
                        $sheet->cell('L6',"Packing Code");
                        $sheet->cell('M6',"Total Quantity");
                        $sheet->cell('N6',"Judgement");
                        $sheet->cell('O6',"Remarks");

                    
                        $sheet->setHeight(array(
                            1 => 30,
                            2 => 20,
                            3 => 20,
                            4 => 20,
                            6 => 20,
                           
                        ));
                        $sheet->row(1, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('#ADD8E6');
                            $row->setFontSize(20);
                            $row->setAlignment('center');
                        });
                        $sheet->row(6, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('#ADD8E6');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(2, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                        });
                        $sheet->row(3, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                        });
                        $sheet->row(4, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                        });
                        $sheet->setStyle(array(
                            'font' => array(
                                'name'  =>  'Calibri',
                                'size'  =>  10
                            )
                        ));
                        $data = json_decode($request->data);
                        $date_inspected = $data->date_inspected;
                        $shipment_date = $data->shipment_date;
                        $device_name = $data->device_name;
                        $po_num = $data->po_num;
                        $packing_operator = $data->packing_operator;
                        $inspector = $data->inspector;
                        $packing_type = $data->packing_type;
                        $unit_condition = $data->unit_condition;
                        $packing_code_series = $data->packing_code_series;
                        $carton_num = $data->carton_num;
                        $packing_code = $data->packing_code;
                        $total_qty = $data->total_qty;
                        $judgement = $data->judgement;
                        $remarks = $data->remarks;
                        $searchpono = $data->searchpono;
                        $datefrom = $data->datefrom;
                        $dateto = $data->dateto;

                        $row = 7;
                        foreach ($po_num as $key => $val) {
                            $sheet->cell('B'.$row, $date_inspected[$key]);
                            $sheet->cell('C'.$row, $shipment_date[$key]);
                            $sheet->cell('D'.$row, $device_name[$key]);
                            $sheet->cell('E'.$row, $po_num[$key]);
                            $sheet->cell('F'.$row, $packing_operator[$key]);
                            $sheet->cell('G'.$row, $inspector[$key]);
                            $sheet->cell('H'.$row, $packing_type[$key]);
                            $sheet->cell('I'.$row, $unit_condition[$key]);
                            $sheet->cell('J'.$row, $packing_code_series[$key]);
                            $sheet->cell('K'.$row, $carton_num[$key]);
                            $sheet->cell('L'.$row, $packing_code[$key]);
                            $sheet->cell('M'.$row, $total_qty[$key]);
                            $sheet->cell('N'.$row, $judgement[$key]);
                            $sheet->cell('O'.$row, $remarks[$key]);
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
                   
                    $sheet->cell('B'.$row, "Total Qty:");
                    $sheet->cell('C'.$row, "");
                    $sheet->cell('H'.$row, "Balance:");
                    $sheet->cell('I'.$row, "");
                    $sheet->cell('M'.$row, "Date:");
                    $sheet->cell('N'.$row, Carbon::now());
                    });

                })->download('xls');
            } catch (Exception $e) {
                return redirect(url('/packinginspection'))->with(['err_message' => $e]);
            }    
        }
    }/*END OF FUNCTION*/
    
    private function displaypack_mod($pono)
    {

        $table = DB::connection($this->mysql)->table('tbl_packmod')
                ->where('po_no',$pono)
                ->get();
        return $table;
    }

    public function displaypackmod(Request $request)
    {
        $pono = $request->pono;
        return $this->displaypack_mod($pono);
    }

    public function packmod_save(Request $request)
    {
        $status = $request->status;
        $id = $request->id;
        $pono = $request->pono;

        if($status == "ADD"){
            $table = DB::connection($this->mysql)->table('tbl_packmod')
            ->insert([
                'po_no'=>$pono,
                'mod' => $request->mod,
                'qty' => $request->qty
            ]);  
        }
        if($status == "EDIT"){
            
            $table = DB::connection($this->mysql)->table('tbl_packmod')
            ->where('id',$id)
            ->update(array(
                'po_no'=>$pono,
                'mod' => $request->mod,
                'qty' => $request->qty
            ));  
        }  
   
        return $this->displaypack_mod($pono);
    } 

    public function packmod_edit(Request $request)
    {
        $id = $request->id;
        $table = DB::connection($this->mysql)->table('tbl_packmod')->where('id',$id)->get();   
        return $table;
    }

    public function packmod_delete(Request $request)
    {
        $tray = $request->tray;
        $traycount = $request->traycount;  
        $pono = $request->pono;
       /* return $tray;  */
        if($traycount > 0){
            $ok = DB::connection($this->mysql)->table('tbl_packmod')->wherein('id',$tray)->delete();
        }
        return $this->displaypack_mod($pono);
    }

    public function getTotalmod(Request $request)
    {
        $pono = $request->pono;
        $table = DB::connection($this->mysql)->table('tbl_packmod')->select(DB::raw("SUM(qty) as qty"))->where('po_no',$pono)->get();
        return $table;
    }

    public function getTotalruncard(Request $request)
    {
        $pono = $request->pono;
        $cartonno = $request->cartonno;
        $table = DB::connection($this->mysql)->table('tbl_packinginspection_runcard')
                ->select(DB::raw("SUM(runcard_qty) as qty"))
                ->where('pono',$pono)
                ->where('carton_no',$cartonno)
                ->get();
        return $table;
    }

    public function packingdbgroupby(Request $request)
    {
        /*$data = array_filter($request->input('data'));*/
        //$fields = "'".implode("','",$data)."'";

        $datefrom = $request->data['datefrom'];
        $dateto = $request->data['dateto'];
        $g1 = $request->data['g1'];
        $g2 = $request->data['g2'];
        $g3 = $request->data['g3'];
        $g1content = $request->data['g1content'];
        $g2content = $request->data['g2content'];
        $g3content = $request->data['g3content'];
        $field='';
        if($g1){
            if($datefrom == "" && $dateto == ""){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                    ->where($g1,'=',$g1content)
                    ->get();    
            } else {
                $field = DB::connection($this->mysql)->table('packing_inspections')
                    ->whereBetween('date_inspected',[$datefrom, $dateto])
                    ->where($g1,'=',$g1content)
                    ->get();        
            }
            if($datefrom && $dateto && $g1content == ""){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                ->whereBetween('app_date',[$datefrom, $dateto])
                ->groupBy($g1)
                ->get();    
            }
            if($datefrom == "" && $dateto == "" && $g1content == ""){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                ->groupBy($g1)
                ->get();        
            }     
        }

        if($g2){
            if($datefrom == "" && $dateto == ""){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                    ->where($g1,'=',$g1content)
                    ->where($g2,'=',$g2content)
                    ->get();   
            } else {
                $field = DB::connection($this->mysql)->table('packing_inspections')
                    ->whereBetween('date_inspected',[$datefrom, $dateto])
                    ->where($g1,'=',$g1content)
                    ->where($g2,'=',$g2content)
                    ->get();       
            }
            if($datefrom && $dateto && $g1content == "" && $g2content){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                ->whereBetween('app_date',[$datefrom, $dateto])
                ->where($g2,'=',$g2content)
                ->groupBy($g1)
                ->get();    
            }
            if($datefrom == "" && $dateto == "" && $g1content == "" && $g2content = ""){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                ->groupBy($g1,$g2)
                ->get();        
            }
            
        }
        if($g3){
            if($datefrom =="" && $dateto == ""){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                    ->Where($g1,'=',$g1content)
                    ->where($g2,'=',$g2content)
                    ->where($g3,'=',$g3content)
                    ->get();       
            } else {
                $field = DB::connection($this->mysql)->table('packing_inspections')
                    ->whereBetween('date_inspected',[$datefrom, $dateto])
                    ->Where($g1,'=',$g1content)
                    ->where($g2,'=',$g2content)
                    ->where($g3,'=',$g3content)
                    ->get();      
            }
            if($datefrom && $dateto && $g1content == "" && $g2content == "" && $g3content){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                ->whereBetween('app_date',[$datefrom, $dateto])
                ->where($g3,'=',$g3content)
                ->groupBy($g1,$g2)
                ->get();    
            }
            if($datefrom && $dateto && $g1content == "" && $g2content && $g3content){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                ->whereBetween('app_date',[$datefrom, $dateto])
                ->where($g2,'=',$g2content)
                ->where($g3,'=',$g3content)
                ->groupBy($g1)
                ->get();     
            }
            if($datefrom == "" && $dateto == "" && $g1content == "" && $g2content && $g3content){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                ->where($g2,'=',$g2content)
                ->where($g3,'=',$g3content)
                ->groupBy($g1)
                ->get();     
            }
            if($datefrom == "" && $dateto == "" && $g1content == "" && $g2content == "" && $g3content == ""){
                $field = DB::connection($this->mysql)->table('packing_inspections')
                ->groupBy($g1,$g2,$g3)
                ->get();     
            }    
        }
        
        return $field;
    }

    public function packingselectgroupby1(Request $request)
    {
        $g1 = $request->data;
        $table = DB::connection($this->mysql)->table('packing_inspections')
                ->select($g1)
                ->distinct()
                ->get();

        return $table;
    }

    public function getmod(Request $request)
    {
        $pono = $request->pono;
        $table = DB::connection($this->mysql)->table('tbl_packmod')
                ->select(DB::raw("SUM(qty) AS qty"))
                ->where('po_no','=',$pono)
                ->get();
        return $table;
    }

    private function convertDate($date,$format)
    {
        $time = strtotime($date);
        $newdate = date($format,$time);
        return $newdate;
    }


}