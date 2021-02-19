<?php
namespace App\Http\Controllers\QCMLD;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Config;
use Yajra\Datatables\Datatables;
use Dompdf\Dompdf;
use Carbon\Carbon;
use App\OQCInspectionMolding;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth; #Auth facade
use Excel;

class OQCMoldingController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;

    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'mysql');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getOQCMolding()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_QCMLD')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $customer = $common->getDropdownByName('Customer');
            $family = $common->getDropdownByName('Family');
            $tofinspection = $common->getDropdownByName('Type of Inspection');
            $sofinspection = $common->getDropdownByName('Severity of Inspection');
            $inspectionlvl = $common->getDropdownByName('Inspection Level');
            $dieno = $common->getDropdownByName('Die No');
            $shift = $common->getDropdownByName('Shift');
            $submission = $common->getDropdownByName('Submission');
            $aql = $common->getDropdownByName('AQL');
            $mods = $common->getDropdownByName('Mode of Defect - OQC Inscpection Molding');
            $oqcmod = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')->get();
            $family = $common->getDropdownByName('Family');

            return view('qcmld.oqcmolding',['userProgramAccess' => $userProgramAccess,'customers'=>$customer,'familys'=>$family,'tofinspections'=>$tofinspection,'sofinspections'=>$sofinspection,'inspectionlvls'=>$inspectionlvl,
                'oqcmod'=> $oqcmod,'mods'=> $mods,'dienos'=> $dieno,'submissions'=>$submission,'aqls'=> $aql,'shifts' => $shift,
                'customers' => $customer,'families'=>$family]);
        }
    }

    public function getLoadOQC(Request $req)
    {
        $po = $req->po;
        $to = $req->to;
        $from = $req->from;
        if ($po == '' && $to == '' && $from == '') {
            $loaded = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                        ->orderBy('id','desc')
                        ->get();
            return $loaded;
        }
        if ($po != '' &&  $to == '' && $from == '') {
            $loaded = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                        ->where('po_no',$po)
                        ->orderBy('id','desc')
                        ->get();
            return $loaded;
        }
        if ($po == '' &&  $to != '' && $from != '') {
            $loaded = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                        ->where('date_inspected','>=',$from)
                        ->where('date_inspected','<=',$to)
                        ->orderBy('id','desc')
                        ->get();
            return $loaded;
        } 
        if ($po != '' && $to != '' && $from != '') {
            $loaded = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                        ->where('date_inspected','>=',$from)
                        ->where('date_inspected','<=',$to)
                        ->where('po_no',$po)
                        ->orderBy('id','desc')
                        ->get();
            return $loaded;
        }
        
    }

    public function getPO(Request $req)
    {
        $data = DB::connection($this->mssql)->table('vslhi')
                ->where('PORDER',$req->po)
                ->select('CODE as partcode',
                        'NAME as partname',
                        'VENDORNAME as customer',
                        'KVOL as qty')
                ->get();
        $count = DB::connection($this->mssql)->table('vslhi')
                ->where('PORDER',$req->po)
                ->select('CODE as partcode',
                        'NAME as partname',
                        'VENDORNAME as customer',
                        'KVOL as qty')
                ->get();
        if ($count > 0) {
            return $data;
        } else {
            return $count;
        }
    }

    public function getCust(Request $req)
    {
        $data = DB::connection('sqlsrvmold')->table('XCUST')
                ->where('CUST',$req->custcode)
                ->select('CNAME')
                ->get();
        return $data;
    }

    //lot number
        public function postLotNo(Request $req)
        {
            $sum  = $this->TotalQty($req->po) + $req->qty;
            $diff = $req->total_qty - $sum;
            if ($this->lotNumberExists($req->po,$req->lot_no) > 0) {
                $data['status'] = "existing";
                return $data;
            } elseif ($diff < 0) {
                $data['status'] = "greater";
                $data['total'] = $this->TotalQty($req->po);
                return $data;
            } else {
                $inserted = DB::connection($this->mysql)->table('lot_number_qcmolding')->insert([
                    'po' => $req->po,
                    'lot_no' => $req->lot_no,
                    'qty' => $req->qty,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                if ($inserted == true) {
                    $data['status'] = "success";
                    return $data;
                } else {
                    $data['status'] = "error";
                    return $data;
                }
            }
        }

        public function deleteLotNo(Request $req)
        {
            $deleted = DB::connection($this->mysql)->table('lot_number_qcmolding')
                            ->where('id',$req->id)
                            ->delete();

            if ($deleted == true) {
                $data['status'] = "success";
                return $data;
            } else {
                $data['status'] = "error";
                return $data;
            }
        }

        public function getLotNo(Request $req)
        {
            $data = DB::connection($this->mysql)->table('lot_number_qcmolding')
                        ->where('po',$req->po)
                        ->get();

            return $data;
        }

        private function lotNumberExists($po,$lot)
        {
            $data = DB::connection($this->mysql)->table('lot_number_qcmolding')
                        ->where('po',$po)
                        ->where('lot_no',$lot)
                        ->count();

            return $data;
        }

        private function TotalQty($po)
        {
            $data = DB::connection($this->mysql)->table('lot_number_qcmolding')
                        ->where('po',$po)
                        ->select(DB::raw("SUM(qty) as total"))
                        ->groupBy('po')
                        ->get();
            if ($data != null) {
                return $data[0]->total;
            }
        }

        public function getTotalQty(Request $req)
        {
            $data = DB::connection($this->mysql)->table('lot_number_qcmolding')
                        ->where('po',$req->po)
                        ->select(DB::raw("SUM(qty) as total"))
                        ->groupBy('po')
                        ->get();
            if ($data != null) {
                return $data[0]->total;
            }
        }

    //mode of defects
        public function getModTbl(Request $req)
        {
            $data = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')
                        ->where('po',$req->po)
                        ->where('partcode',$req->partcode)
                        ->select('id','po','partcode','description',DB::raw('SUM(qty) as qty'))
                        ->groupBy('po','partcode','description')
                        ->distinct()
                        ->get();
            return $data;
        }

        public function getAllMod(Request $req)
        {
            $data = DB::connection($this->mysql)->table('oqc_inspection_molding_mod_collections')
                        ->where('description','like','%{$req->mod}%')
                        ->get();
            return response()->json($data);
        }

        public function postMod(Request $req)
        {
            $state = $req->state;
            $id = $req->id;

            if($state == "ADD"){
                $inserted = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')
                            ->insert([
                                'po' => $req->po,
                                'partcode' => $req->partcode,
                                'description' => $req->mod,
                                'qty' => $req->qty,
                                'submission' => $req->submission,
                                'lotno' => $req->lotno,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                if ($inserted) {
                    $data['status'] = 'success';
                    return $data;
                } else {
                    $data['status'] = 'error';
                    return $data;
                }
            }
            if($state == "EDIT"){
                $updated = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')
                                ->where('id',$id)
                                ->update([
                                    'description' => $req->mod,
                                    'qty' => $req->qty,
                                    'submission' => $req->submission,
                                    'lotno' => $req->lotno,
                                    'updated_at' => Carbon::now()
                                ]);

                if ($updated) {
                    $data['status'] = 'success';
                    return $data;
                } else {
                    $data['status'] = 'error';
                    return $data;
                }
            }

            if($state == "ndf"){
                $inserted = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')
                            ->insert([
                                'po' => $req->po,
                                'partcode' => $req->partcode,
                                'description' => $req->mod,
                                'qty' => $req->qty,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                if ($inserted) {
                    $data['status'] = 'success_ndf';
                    return $data;
                } else {
                    $data['status'] = 'error';
                    return $data;
                }
            }
        }

        public function getMODTotalQty(Request $req)
        {
            $data = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')
                        ->where('po',$req->po)
                        ->where('partcode',$req->partcode)
                        ->select(DB::raw("SUM(qty) as total"))
                        ->groupBy('po')
                        ->get();
            if ($data != null) {
                return $data[0]->total;
            }
        }

        public function deleteMod(Request $req)
        {
            $deleted = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')
                            ->where('po', $req->po)
                            ->where('partcode', $req->partcode)
                            ->where('description', $req->mod)
                            ->delete();
            if ($deleted) {
                $data['status'] = 'success';
                return $data;
            } else {
                $data['status'] = 'error';
                return $data;
            }
        }
    

    public function saveOQC(Request $req)
    {
        $time = Carbon::now();
        if ($req->status == 'insert') {
            $lot_rejected = '';
            $nod = '';
           
            if($req->lot_accepted == 0){
                $lot_rejected = 1;
                $nod = $req->no_of_defects;
            }else{
                $lot_rejected = 0;
                $nod = 0;
            }
            $inserted = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                    ->insert([
                        'po_no' => $req->po_no,
                        'partcode' => $req->part_code,
                        'partname' => $req->part_name,
                        'customer' => $req->customer,
                        'family' => $req->family,
                        'total_qty' => $req->total_qty,
                        'lot_qty' => $req->total_qty,
                        'lot_no' => $req->lot_no,
                        'die_no' => $req->die_num,
                        'qty' => $req->quantity,
                        'type_of_inspection' => $req->type_of_inspection,
                        'severity_of_inspection' => $req->severity_of_inspection,
                        'inspection_lvl' => $req->inspection_lvl,
                        'aql' => $req->aql,
                        'accept' => $req->accept,
                        'reject' => $req->reject,
                        'date_inspected' => $req->date_inspected,
                        'shift' => $req->shift,
                        'inspector' => $req->inspector,
                        'submission' => $req->submission,
                        'visual_operator' => $req->visual_operator,
                        'fy_no' => $req->fy,
                        'ww_no' => $req->ww,
                        'remarks' => $req->remarks,
                        'ptcp_tnr' => $req->ptcp_tnr,
                        'lot_inspected' => $req->lot_inspected,
                        'lot_accepted' => $req->lot_accepted,
                        'lot_rejected' => $lot_rejected,
                        'sample_size' => $req->sample_size,
                        'num_of_defectives' => $nod,
                        'judgement' => $req->judgement,
                        'from' => $req->from,
                        'to' => $time->format('h:i:s A'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

            if ($inserted) {
                $data['status'] = 'success';
                return $data;
            } else {
                $data['status'] = 'error';
                return $data;
            }
        } 
        if($req->status == 'update'){
            $lot_rejected = '';
            $nod = '';
            if($req->lot_accepted == 0){
                $lot_rejected = 1;
                $nod = $req->nofdefects;
            }else{
                $lot_rejected = 0;
                $nod = 0;
            }
            $updated = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                        ->where('id',$req->id)
                        ->update([
                            'partcode' => $req->part_code,
                            'partname' => $req->part_name,
                            'customer' => $req->customer,
                            'family' => $req->family,
                            'total_qty' => $req->total_qty,
                            'lot_no' => $req->lot_no,
                            'lot_qty' => $req->total_qty,
                            'die_no' => $req->die_num,
                            'qty' => $req->quantity,
                            'type_of_inspection' => $req->type_of_inspection,
                            'severity_of_inspection' => $req->severity_of_inspection,
                            'inspection_lvl' => $req->inspection_lvl,
                            'aql' => $req->aql,
                            'accept' => $req->accept,
                            'reject' => $req->reject,
                            'date_inspected' => $req->date_inspected,
                            'shift' => $req->shift,
                            'inspector' => $req->inspector,
                            'submission' => $req->submission,
                            'visual_operator' => $req->visual_operator,
                            'fy_no' => $req->fy,
                            'ww_no' => $req->ww,
                            'remarks' => $req->remarks,
                            'ptcp_tnr' => $req->ptcp_tnr,
                            'lot_inspected' => $req->lot_inspected,
                            'lot_accepted' => $req->lot_accepted,
                            'lot_rejected' => $lot_rejected,
                            'sample_size' => $req->sample_size,
                            'num_of_defectives' => $nod,
                            'judgement' => $req->judgement,
                            'updated_at' => Carbon::now()
                        ]);

            if ($updated) {
                $data['status'] = 'update_success';
                return $data;
            } else {
                $data['status'] = 'error';
                return $data;
            }
        }
    }

    public function deleteOQC(Request $req)
    {
        $deleted = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                            ->where('id', $req->id)
                            ->delete();
        if ($deleted) {
            $data['status'] = 'success';
            return $data;
        } else {
            $data['status'] = 'error';
            return $data;
        }
    }

    public function exportToPDF(Request $request)
    {
        $data = json_decode($request->data);
        $pono = $data->pono;
        $date_inspected = $data->date_inspected;
        $submission = $data->submission;
        $fy = $data->fy;
        $ww = $data->ww;
        $customer = $data->customer;
        $partcode = $data->partcode;
        $partname = $data->partname;
        $lotno = $data->lotno;
        $qty = $data->qty;
        $lotqty = $data->lotqty;
        $shift = $data->shift;
        $remarks = $data->remarks;
        $from = $data->from;
        $to = $data->to;
        $samplesize = $data->samplesize;
        $nod = $data->nod;
        $mod = $data->mod;
        $ptcptnr = $data->ptcptnr;
        $judgement = $data->judgement;
        $inspector = $data->inspector;
        $searchpono = $data->searchpono;
        $datefrom = $data->datefrom;
        $dateto = $data->dateto;
        $status = $data->status;

        if($status == "SEARCH"){
            if($searchpono == ""){
                $field = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                        ->whereBetween('date_inspected', [$datefrom,$dateto])
                        ->get();   
            } else {
                $field = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                        ->where('po_no','=',$searchpono)
                        ->whereBetween('date_inspected', [$datefrom,$dateto])
                        ->get();  
            }
            if($datefrom == "" && $dateto == ""){
                $field = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                        ->where('po_no',$searchpono)
                        ->get();     
            }
            $po_qty = $field[0]->lot_qty + 0;

            $html1 = '<style type="text/css" scoped>
                        table,span,b,u {
                            font-size: 10px;
                        }
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
                        #info thead td {
                            text-align: center;
                        }
                        #date{
                            text-align:right;
                        }
                    </style>

                    <table id="info">
                        <thead>
                            <tr bgcolor="#ADD8E6">
                                <td colspan="9">
                                    <h2>PPS OQC INSPECTION RESULT RECORD</h2>
                                </td>
                            </tr>
                            <tr>
                                <td>Parts Name:</td>
                                <td>'.$field[0]->partname.'</td>
                                <td></td>
                                <td>Customer Name:</td>
                                <td>'.$field[0]->customer.'</td>
                                <td></td>
                                <td>AQL:</td>
                                <td>'.$field[0]->aql.'</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Parts Code:</td>
                                <td>'.$field[0]->partcode.'</td>
                                <td></td>
                                <td>Type of Inspection:</td>
                                <td>'.$field[0]->type_of_inspection.'</td>
                                <td></td>
                                <td>Ac:</td>
                                <td>'.$field[0]->accept.'</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>P.O. Number:</td>
                                <td>'.$field[0]->po_no.'</td>
                                <td></td>
                                <td>Severity of:</td>
                                <td>'.$field[0]->severity_of_inspection.'</td>
                                <td></td>
                                <td>Re:</td>
                                <td>'.$field[0]->reject.'</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>P.O. Qty</td>
                                <td>'.$po_qty.'</td>
                                <td></td>
                                <td>Inspection Level:</td>
                                <td>'.$field[0]->inspection_lvl.'</td>
                                <td></td>
                                <td></td>
                                <td"></td>
                                <td></td>
                            </tr>
                        </thead>
                    </table>
                    <table id="data" border="1">
                        <thead>
                            <tr>
                                <th width="6.67%">Date Inspected</th>
                                <th width="3.67%">Shift</th>
                                <th width="6.67%">From</th>
                                <th width="6.67%">To</th>
                                <th width="6.67%"># of Sub</th>
                                <th width="6.67%">Lot Number</th>
                                <th width="6.67%">Lot Size</th>
                                <th width="6.67%">Sample Size</th>
                                <th width="6.67%">No. of Defective</th>
                                <th width="6.67%">Mode of Defects</th>
                                <th width="6.67%">Qty</th>
                                <th width="9.67%">Determination Lot Acceptability</th>
                                <th width="6.67%">PTCP/TNR No.</th>
                                <th width="6.67%">Inspector</th>
                                <th width="6.67%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>';

                $html2 = '';
                $html3 = '</tbody>
                        </table>';
                
            foreach ($pono as $key => $po) {
                $html2 .= '<tr>
                        <td width="6.67%">'.$date_inspected[$key].'</td>
                        <td width="3.67%">'.$shift[$key].'</td>
                        <td width="6.67%">'.$from[$key].'</td>
                        <td width="6.67%">'.$to[$key].'</td>
                        <td width="6.67%">'.$submission[$key].'</td>
                        <td width="6.67%">'.$lotno[$key].'</td>
                        <td width="6.67%">'.$lotqty[$key].'</td>
                        <td width="6.67%">'.$samplesize[$key].'</td>
                        <td width="6.67%">'.$nod[$key].'</td>
                        <td width="6.67%">'.$mod[$key].'</td>
                        <td width="6.67%">'.$qty[$key].'</td>
                        <td width="9.67%">'.$judgement[$key].'</td>
                        <td width="6.67%">'.$ptcptnr[$key].'</td>
                        <td width="6.67%">'.$inspector[$key].'</td>
                        <td width="6.67%">'.$remarks[$key].'</td>
                    </tr>';
            }

            $table = DB::connection($this->mysql)->table('oqc_inspection_moldings')->select(DB::raw("SUM(qty) AS qty"),'lot_qty')->where('po_no',$searchpono)->where('submission','=',"1st")->get();
            $total_qty = $table[0]->qty + 0;
            $balance = $table[0]->lot_qty - $table[0]->qty;
            $html4 = '<table width="100%">
                        <tr>
                            <td class="label" width="12.5%">Total Qty:</td>
                            <td class="value" width="12.5%">'.$total_qty.'</td>
                            <td width="12.5%"></td>
                            <td class="label" width="12.5%">Balance:</td>
                            <td class="value" width="12.5%">'.$balance.'</td>
                            <td width="12.5%"></td>
                            <td class="label" width="12.5%">Date:</td>
                            <td class="value" width="12.5%">'.Carbon::now().'</td>
                        </tr>
                    </table>';

            $html = $html1.$html2.$html3.$html4;
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'landscape');
            $dompdf->render();
            $dompdf->stream('OQC_Inspection_Molding_'.Carbon::now());
        }else{
            $html1 = '<style type="text/css" scoped>
                        table,span,b,u {
                            font-size: 10px;
                        }
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
                        #info thead td {
                            text-align: center;
                        }
                        #date{
                            text-align:right;
                        }
                    </style>

                    <table id="info">
                        <thead>
                            <tr bgcolor="#ADD8E6">
                                <td colspan="9">
                                    <h2>PPS OQC INSPECTION RESULT RECORD</h2>
                                </td>
                            </tr>
                            <tr>
                                <td>Parts Name:</td>
                                <td></td>
                                <td></td>
                                <td>Customer Name:</td>
                                <td></td>
                                <td></td>
                                <td>AQL:</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Parts Code:</td>
                                <td></td>
                                <td></td>
                                <td>Type of Inspection:</td>
                                <td></td>
                                <td></td>
                                <td>Ac:</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>P.O. Number:</td>
                                <td></td>
                                <td></td>
                                <td>Severity of:</td>
                                <td></td>
                                <td></td>
                                <td>Re:</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>P.O. Qty</td>
                                <td></td>
                                <td></td>
                                <td>Inspection Level:</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td"></td>
                                <td></td>
                            </tr>
                        </thead>
                    </table>
                    <table id="data" border="1">
                        <thead>
                            <tr>
                                <th width="6.67%">Date Inspected</th>
                                <th width="3.67%">Shift</th>
                                <th width="6.67%">From</th>
                                <th width="6.67%">To</th>
                                <th width="6.67%"># of Sub</th>
                                <th width="6.67%">Lot Number</th>
                                <th width="6.67%">Lot Size</th>
                                <th width="6.67%">Sample Size</th>
                                <th width="6.67%">No. of Defective</th>
                                <th width="6.67%">Mode of Defects</th>
                                <th width="6.67%">Qty</th>
                                <th width="9.67%">Determination Lot Acceptability</th>
                                <th width="6.67%">PTCP/TNR No.</th>
                                <th width="6.67%">Inspector</th>
                                <th width="6.67%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>';

                $html2 = '';
                $html3 = '</tbody>
                        </table>';
                
            foreach ($pono as $key => $po) {
                $html2 .= '<tr>
                        <td width="6.67%">'.$date_inspected[$key].'</td>
                        <td width="3.67%">'.$shift[$key].'</td>
                        <td width="6.67%">'.$from[$key].'</td>
                        <td width="6.67%">'.$to[$key].'</td>
                        <td width="6.67%">'.$submission[$key].'</td>
                        <td width="6.67%">'.$lotno[$key].'</td>
                        <td width="6.67%">'.$lotqty[$key].'</td>
                        <td width="6.67%">'.$samplesize[$key].'</td>
                        <td width="6.67%">'.$nod[$key].'</td>
                        <td width="6.67%">'.$mod[$key].'</td>
                        <td width="6.67%">'.$qty[$key].'</td>
                        <td width="9.67%">'.$judgement[$key].'</td>
                        <td width="6.67%">'.$ptcptnr[$key].'</td>
                        <td width="6.67%">'.$inspector[$key].'</td>
                        <td width="6.67%">'.$remarks[$key].'</td>
                    </tr>';
            }

            $html4 = '<table width="100%">
                        <tr>
                            <td class="label" width="12.5%">Total Qty:</td>
                            <td class="value" width="12.5%"></td>
                            <td width="12.5%"></td>
                            <td class="label" width="12.5%">Balance:</td>
                            <td class="value" width="12.5%"></td>
                            <td width="12.5%"></td>
                            <td class="label" width="12.5%">Date:</td>
                            <td class="value" width="12.5%">'.Carbon::now().'</td>
                        </tr>
                    </table>';

            $html = $html1.$html2.$html3.$html4;
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'landscape');
            $dompdf->render();
            $dompdf->stream('OQC_Inspection_Molding_'.Carbon::now());   
        }
        
    }

    public function exportToExcel(Request $req)
    {
        $data = json_decode($req->data);
        $status = $data->status;
        if($status == "SEARCH"){
            try
            {
                $dt = Carbon::now();
             /*   $data = $this->reportsQuery($req->po,$req->from,$req->to);*/
                Excel::create('OQC_Inspection_Molding_'.$dt, function($excel) use($req)
                {
                    $excel->sheet('Sheet1', function($sheet) use($req)
                    {
                        $data = json_decode($req->data);
                        $searchpono = $data->searchpono;
                        $datefrom = $data->datefrom;
                        $dateto = $data->dateto;
                        if($searchpono == ""){
                            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                                    ->whereBetween('date_inspected', [$datefrom,$dateto])
                                    ->get();   
                        } else {
                            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                                    ->where('po_no','=',$searchpono)
                                    ->whereBetween('date_inspected', [$datefrom,$dateto])
                                    ->get();  
                        }
                        if($datefrom == "" && $dateto == ""){
                            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                                    ->where('po_no',$searchpono)
                                    ->get();     
                        }

                        $sheet->setCellValue('A1', 'PPS OQC INSPECTION RESULT RECORD');
                        $sheet->mergeCells('A1:P1');

                        $sheet->cell('B3', "Parts Name");
                        $sheet->cell('B4', "Parts Code");
                        $sheet->cell('B5', "P.O. Number");
                        $sheet->cell('B6', "P.O. Qty.");

                        $sheet->cell('C3',$field[0]->partname);
                        $sheet->cell('C4',$field[0]->partcode);
                        $sheet->cell('C5',$field[0]->po_no);
                        $sheet->cell('C6',$field[0]->lot_qty);

                        $sheet->cell('I3', "Customer Name");
                        $sheet->cell('I4', "Type of inspection");
                        $sheet->cell('I5', "Severity of");
                        $sheet->cell('I6', "Inspection Level");

                        $sheet->cell('J3',$field[0]->customer);
                        $sheet->cell('J4',$field[0]->type_of_inspection);
                        $sheet->cell('J5',$field[0]->severity_of_inspection);
                        $sheet->cell('J6',$field[0]->inspection_lvl);

                        $sheet->cell('N3', "AQL");
                        $sheet->cell('N4', "Ac");
                        $sheet->cell('N5', "Re");

                        $sheet->cell('O3',$field[0]->aql);
                        $sheet->cell('O4',$field[0]->accept);
                        $sheet->cell('O5',$field[0]->reject);

                        $sheet->cell('B8', "Date Inspected");
                        $sheet->cell('C8', "Shift");
                        $sheet->cell('D8', "From");
                        $sheet->cell('E8', "To");
                        $sheet->cell('F8', "# of Sub");
                        $sheet->cell('G8', "Lot Number");
                        $sheet->cell('H8', "Lot Size");
                        $sheet->cell('I8', "Sample Size");
                        $sheet->cell('J8', "No. of Defective");
                        $sheet->cell('K8', "Mode of Defects");
                        $sheet->cell('L8', "Qty");
                        $sheet->cell('M8', "Determination on Lot Acceptability");
                        $sheet->cell('N8', "PTCP/TNR No.");
                        $sheet->cell('O8', "Inspector");
                        $sheet->cell('P8', "Remarks");

                        $sheet->row(1, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('#ADD8E6');
                            $row->setFontSize(20);
                            $row->setAlignment('center');
                        });
                        $sheet->setHeight(array(
                            1 => 30,
                            3 => 20,
                            4 => 20,
                            5 => 20,
                            6 => 20,
                            8 => 20,
                        ));
                        $sheet->row(8, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('#ADD8E6');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(3, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(4, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(5, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(6, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });

                        $pono = $data->pono;
                        $date_inspected = $data->date_inspected;
                        $submission = $data->submission;
                        $fy = $data->fy;
                        $ww = $data->ww;
                        $customer = $data->customer;
                        $partcode = $data->partcode;
                        $partname = $data->partname;
                        $lotno = $data->lotno;
                        $qty = $data->qty;
                        $lotqty = $data->lotqty;
                        $shift = $data->shift;
                        $remarks = $data->remarks;
                        $from = $data->from;
                        $to = $data->to;
                        $samplesize = $data->samplesize;
                        $nod = $data->nod;
                        $ptcptnr = $data->ptcptnr;
                        $judgement = $data->judgement;
                        $inspector = $data->inspector;
                        

                        $row = 9;
                        foreach ($pono as $key => $po) {
                            $sheet->cell('B'.$row, $date_inspected[$key]);
                            $sheet->cell('C'.$row, $shift[$key]);
                            $sheet->cell('D'.$row, $from[$key]);
                            $sheet->cell('E'.$row, $to[$key]);
                            $sheet->cell('F'.$row, $submission[$key]);
                            $sheet->cell('G'.$row, $lotno[$key]);
                            $sheet->cell('H'.$row, $lotqty[$key]);
                            $sheet->cell('I'.$row, $samplesize[$key]);
                            $sheet->cell('J'.$row, $nod[$key]);
                            $sheet->cell('K'.$row, "");
                            $sheet->cell('L'.$row, $qty[$key]);
                            $sheet->cell('M'.$row, $judgement[$key]);
                            $sheet->cell('N'.$row, $ptcptnr[$key]);
                            $sheet->cell('O'.$row, $inspector[$key]);
                            $sheet->cell('P'.$row, $remarks[$key]);

                            $sheet->row($row, function ($row) {
                                $row->setFontFamily('Calibri');
                                $row->setFontSize(10);
                                $row->setAlignment('center');
                            });

                            $sheet->setHeight(array(
                                $row => 20
                            ));
                            $row++;
                        }
                        $sheet->row($row, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->setHeight(array(
                            $row => 20
                        ));
                        $table = DB::connection($this->mysql)->table('oqc_inspection_moldings')->select(DB::raw("SUM(qty) AS qty"),'lot_qty')->where('po_no',$searchpono)->where('submission','=',"1st")->get();
                        $balance = $table[0]->lot_qty - $table[0]->qty;

                        $sheet->cell('B'.$row, "Total Qty:");
                        $sheet->cell('C'.$row, $table[0]->qty);
                        $sheet->cell('G'.$row, "Balance:");
                        $sheet->cell('H'.$row, $balance);
                        $sheet->cell('J'.$row, "Date:");
                        $sheet->cell('K'.$row, Carbon::now());

                    });

                })->download('xls');
                // ->store('xls',$path)->export();


            } catch (Exception $e) {
            }   
        } else {
            try
            {
                $dt = Carbon::now();
             /*   $data = $this->reportsQuery($req->po,$req->from,$req->to);*/
                Excel::create('OQC_Inspection_Molding_'.$dt, function($excel) use($req)
                {
                    $excel->sheet('Sheet1', function($sheet) use($req)
                    {
                        $sheet->setCellValue('A1', 'PPS OQC INSPECTION RESULT RECORD');
                        $sheet->mergeCells('A1:P1');

                        $sheet->cell('B3', "Parts Name");
                        $sheet->cell('B4', "Parts Code");
                        $sheet->cell('B5', "P.O. Number");
                        $sheet->cell('B6', "P.O. Qty.");

                        $sheet->cell('C3',"");
                        $sheet->cell('C4',"");
                        $sheet->cell('C5',"");
                        $sheet->cell('C6',"");

                        $sheet->cell('I3', "Customer Name");
                        $sheet->cell('I4', "Type of inspection");
                        $sheet->cell('I5', "Severity of");
                        $sheet->cell('I6', "Inspection Level");

                        $sheet->cell('J3',"");
                        $sheet->cell('J4',"");
                        $sheet->cell('J5',"");
                        $sheet->cell('J6',"");

                        $sheet->cell('N3', "AQL");
                        $sheet->cell('N4', "Ac");
                        $sheet->cell('N5', "Re");

                        $sheet->cell('O3',"");
                        $sheet->cell('O4',"");
                        $sheet->cell('O5',"");

                        $sheet->cell('B8', "Date Inspected");
                        $sheet->cell('C8', "Shift");
                        $sheet->cell('D8', "From");
                        $sheet->cell('E8', "To");
                        $sheet->cell('F8', "# of Sub");
                        $sheet->cell('G8', "Lot Number");
                        $sheet->cell('H8', "Lot Size");
                        $sheet->cell('I8', "Sample Size");
                        $sheet->cell('J8', "No. of Defective");
                        $sheet->cell('K8', "Mode of Defects");
                        $sheet->cell('L8', "Qty");
                        $sheet->cell('M8', "Determination on Lot Acceptability");
                        $sheet->cell('N8', "PTCP/TNR No.");
                        $sheet->cell('O8', "Inspector");
                        $sheet->cell('P8', "Remarks");

                        $sheet->row(1, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('#ADD8E6');
                            $row->setFontSize(20);
                            $row->setAlignment('center');
                        });
                        $sheet->setHeight(array(
                            1 => 30,
                            3 => 20,
                            4 => 20,
                            5 => 20,
                            6 => 20,
                            8 => 20,
                        ));

                        $sheet->row(8, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('#ADD8E6');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(3, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(4, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(5, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->row(6, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });

                       
                        $data = json_decode($req->data);
                        $pono = $data->pono;
                        $date_inspected = $data->date_inspected;
                        $submission = $data->submission;
                        $fy = $data->fy;
                        $ww = $data->ww;
                        $customer = $data->customer;
                        $partcode = $data->partcode;
                        $partname = $data->partname;
                        $lotno = $data->lotno;
                        $qty = $data->qty;
                        $lotqty = $data->lotqty;
                        $shift = $data->shift;
                        $remarks = $data->remarks;
                        $from = $data->from;
                        $to = $data->to;
                        $samplesize = $data->samplesize;
                        $nod = $data->nod;
                        $ptcptnr = $data->ptcptnr;
                        $judgement = $data->judgement;
                        $inspector = $data->inspector;
                        $searchpono = $data->searchpono;
                        $datefrom = $data->datefrom;
                        $dateto = $data->dateto;

                        $row = 9;
                        foreach ($pono as $key => $po) {
                            $sheet->cell('B'.$row, $date_inspected[$key]);
                            $sheet->cell('C'.$row, $shift[$key]);
                            $sheet->cell('D'.$row, $from[$key]);
                            $sheet->cell('E'.$row, $to[$key]);
                            $sheet->cell('F'.$row, $submission[$key]);
                            $sheet->cell('G'.$row, $lotno[$key]);
                            $sheet->cell('H'.$row, $lotqty[$key]);
                            $sheet->cell('I'.$row, $samplesize[$key]);
                            $sheet->cell('J'.$row, $nod[$key]);
                            $sheet->cell('K'.$row, "");
                            $sheet->cell('L'.$row, $qty[$key]);
                            $sheet->cell('M'.$row, $judgement[$key]);
                            $sheet->cell('N'.$row, $ptcptnr[$key]);
                            $sheet->cell('O'.$row, $inspector[$key]);
                            $sheet->cell('P'.$row, $remarks[$key]);

                            $sheet->row($row, function ($row) {
                                $row->setFontFamily('Calibri');
                                $row->setFontSize(10);
                                $row->setAlignment('center');
                            });
                            $sheet->setHeight(array(
                                $row => 20
                            ));
                            $row++;
                        }
                        $sheet->row($row, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setFontSize(10);
                            $row->setAlignment('center');
                        });
                        $sheet->setHeight(array(
                            $row => 20
                        ));

                        $sheet->cell('B'.$row, "Total Qty:");
                        $sheet->cell('C'.$row, "");
                        $sheet->cell('G'.$row, "Balance:");
                        $sheet->cell('H'.$row, "");
                        $sheet->cell('J'.$row, "Date:");
                        $sheet->cell('K'.$row, Carbon::now());

                    });

                })->download('xls');
                // ->store('xls',$path)->export();


            } catch (Exception $e) {
            }
        }
        
    }

    public function oqcmoldselectgroupby1(Request $request){        
        $g1 = $request->data;
        $table = DB::connection($this->mysql)->table('oqc_inspection_moldings')
                ->select($g1)
                ->distinct()
                ->get();

        return $table;
    }

    public function oqcmoldgroupby(Request $request){        
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
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select('a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.lot_inspected','a.lot_accepted','a.lot_rejected','a.sample_size','a.judgement','a.id','a.num_of_defectives','a.from','a.to','a.dbcon','a.lot_no','b.description',DB::raw("SUM(b.qty) as qty"))
                ->whereBetween('a.date_inspected',[$datefrom, $dateto])
                ->groupBy('a.po_no','a.'.$g1,'a.lot_no')
                ->orderBy('a.lot_qty')
                ->get();
        }
        if($g1 && $g1content){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->where('a.'.$g1,'=',$g1content)
                ->select('a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.lot_inspected','a.lot_accepted','a.lot_rejected','a.sample_size','a.judgement','a.id','a.num_of_defectives','a.from','a.to','a.dbcon','a.lot_no','b.description',DB::raw("SUM(b.qty) as qty"))
                ->whereBetween('a.date_inspected',[$datefrom, $dateto])
                ->groupBy('a.po_no','a.'.$g1,'a.lot_no')
                ->orderBy('a.lot_qty')
                ->get();
        }
        if($g1 && $g2){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select('a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.lot_inspected','a.lot_accepted','a.lot_rejected','a.sample_size','a.judgement','a.id','a.num_of_defectives','a.from','a.to','a.dbcon','a.lot_no','b.description',DB::raw("SUM(b.qty) as qty"))
                ->whereBetween('a.date_inspected',[$datefrom, $dateto])
                ->groupBy('a.po_no','a.lot_no','a.'.$g1,'a.'.$g2)
                ->orderBy('a.lot_qty')
                ->get();
        }
        if($g1 && $g1content && $g2){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->where('a.'.$g1,'=',$g1content)
                ->select('a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.lot_inspected','a.lot_accepted','a.lot_rejected','a.sample_size','a.judgement','a.id','a.num_of_defectives','a.from','a.to','a.dbcon','a.lot_no','b.description',DB::raw("SUM(b.qty) as qty"))
                ->whereBetween('a.date_inspected',[$datefrom, $dateto])
                ->groupBy('a.po_no','a.lot_no','a.'.$g1,'a.'.$g2)
                ->orderBy('a.lot_qty')
                ->get();
        }
        if($g1 && $g1content && $g2 && $g2content){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->where('a.'.$g1,'=',$g1content)
                ->where('a.'.$g2,'=',$g2content)
                ->select('a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.lot_inspected','a.lot_accepted','a.lot_rejected','a.sample_size','a.judgement','a.id','a.num_of_defectives','a.from','a.to','a.dbcon','a.lot_no','b.description',DB::raw("SUM(b.qty) as qty"))
                ->whereBetween('a.date_inspected',[$datefrom, $dateto])
                ->groupBy('a.po_no','a.lot_no','a.'.$g1,'a.'.$g2)
                ->orderBy('a.lot_qty')
                ->get();
        }
        if($g1 && $g2 && $g3){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select('a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.lot_inspected','a.lot_accepted','a.lot_rejected','a.sample_size','a.judgement','a.id','a.num_of_defectives','a.from','a.to','a.dbcon','a.lot_no','b.description',DB::raw("SUM(b.qty) as qty"))
                ->whereBetween('a.date_inspected',[$datefrom, $dateto])
                ->groupBy('a.po_no','a.lot_no','a.'.$g1,'a.'.$g2,'a.'.$g3)
                ->orderBy('a.lot_qty')
                ->get();
        }
        if($g1 && $g1content && $g2 && $g2content && $g3){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->where('a.'.$g1,'=',$g1content)
                ->where('a.'.$g2,'=',$g2content)
                ->select('a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.lot_inspected','a.lot_accepted','a.lot_rejected','a.sample_size','a.judgement','a.id','a.num_of_defectives','a.from','a.to','a.dbcon','a.lot_no','b.description',DB::raw("SUM(b.qty) as qty"))
                ->whereBetween('a.date_inspected',[$datefrom, $dateto])
                ->groupBy('a.po_no','a.lot_no','a.'.$g1,'a.'.$g2,'a.'.$g3)
                ->orderBy('a.lot_qty')
                ->get();   
        }
        if($g1 && $g1content && $g2 && $g2content && $g3 && $g3content){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->where('a.'.$g1,'=',$g1content)
                ->where('a.'.$g2,'=',$g2content)
                ->where('a.'.$g3,'=',$g3content)
                ->select('a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.lot_inspected','a.lot_accepted','a.lot_rejected','a.sample_size','a.judgement','a.id','a.num_of_defectives','a.from','a.to','a.dbcon','a.lot_no','b.description',DB::raw("SUM(b.qty) as qty"))
                ->whereBetween('a.date_inspected',[$datefrom, $dateto])
                ->groupBy('a.po_no','a.lot_no','a.'.$g1,'a.'.$g2,'a.'.$g3)
                ->orderBy('a.lot_qty')
                ->get();   
        }
        return $field;   
    }

    public function getoqcmoldlarlrrdppm(Request $request){
        $datefrom = $request->datefrom;
        $dateto = $request->dateto;
        $g1 = $request->g1;
        $g1content = $request->g1content;
        $g2 = $request->g2;
        $g2content = $request->g2content;
        $g3 = $request->g3;
        $g3content = $request->g3content;
        $status = $request->status;
        $field='';
     
        if($g1 && $g2){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select(DB::raw("SUM(sample_size) AS sample_size")
                        ,DB::raw("SUM(lot_qty) AS lot_qty")
                        ,DB::raw("SUM(num_of_defectives) AS num_of_defects")
                        ,DB::raw("SUM(lot_accepted) AS lot_accepted")
                        ,DB::raw("SUM(lot_rejected) AS lot_rejected")
                        ,DB::raw("SUM(lot_inspected) AS lot_inspected")
                        ,'a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.from','a.to','a.dbcon','b.description',DB::raw("SUM(b.qty) as qty"))
                ->groupBy($g1,$g2)
                ->get();
        }else if($g1 && $g1content){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select(DB::raw("SUM(sample_size) AS sample_size")
                        ,DB::raw("SUM(lot_qty) AS lot_qty")
                        ,DB::raw("SUM(num_of_defectives) AS num_of_defects")
                        ,DB::raw("SUM(lot_accepted) AS lot_accepted")
                        ,DB::raw("SUM(lot_rejected) AS lot_rejected")
                        ,DB::raw("SUM(lot_inspected) AS lot_inspected")
                        ,'a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.from','a.to','a.dbcon','b.description',DB::raw("SUM(b.qty) as qty"))
                ->get();
        }else if($g1 && $g1content && $g2){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select(DB::raw("SUM(sample_size) AS sample_size")
                        ,DB::raw("SUM(lot_qty) AS lot_qty")
                        ,DB::raw("SUM(num_of_defectives) AS num_of_defects")
                        ,DB::raw("SUM(lot_accepted) AS lot_accepted")
                        ,DB::raw("SUM(lot_rejected) AS lot_rejected")
                        ,DB::raw("SUM(lot_inspected) AS lot_inspected")
                        ,'a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.from','a.to','a.dbcon','b.description',DB::raw("SUM(b.qty) as qty"))
                ->where($g1,$g1content)
                ->groupBy($g2)
                ->get();
        }else if($g1 && $g1content && $g2 && $g2content){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select(DB::raw("SUM(sample_size) AS sample_size")
                        ,DB::raw("SUM(lot_qty) AS lot_qty")
                        ,DB::raw("SUM(num_of_defectives) AS num_of_defects")
                        ,DB::raw("SUM(lot_accepted) AS lot_accepted")
                        ,DB::raw("SUM(lot_rejected) AS lot_rejected")
                        ,DB::raw("SUM(lot_inspected) AS lot_inspected")
                        ,'a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.from','a.to','a.dbcon','b.description',DB::raw("SUM(b.qty) as qty"))
                ->where($g1,$g1content)
                ->wehre($g2,$g2content)
                ->get();
        }else if($g1 && $g1content && $g2 && $g2content && $g3){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select(DB::raw("SUM(sample_size) AS sample_size")
                        ,DB::raw("SUM(lot_qty) AS lot_qty")
                        ,DB::raw("SUM(num_of_defectives) AS num_of_defects")
                        ,DB::raw("SUM(lot_accepted) AS lot_accepted")
                        ,DB::raw("SUM(lot_rejected) AS lot_rejected")
                        ,DB::raw("SUM(lot_inspected) AS lot_inspected")
                        ,'a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.from','a.to','a.dbcon','b.description',DB::raw("SUM(b.qty) as qty"))
                ->where($g1,$g1content)
                ->wehre($g2,$g2content)
                ->groupBy($g3)
                ->get();
        }else if($g1 && $g1content && $g2 && $g2content && $g3 && $g3content){
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select(DB::raw("SUM(sample_size) AS sample_size")
                        ,DB::raw("SUM(lot_qty) AS lot_qty")
                        ,DB::raw("SUM(num_of_defectives) AS num_of_defects")
                        ,DB::raw("SUM(lot_accepted) AS lot_accepted")
                        ,DB::raw("SUM(lot_rejected) AS lot_rejected")
                        ,DB::raw("SUM(lot_inspected) AS lot_inspected")
                        ,'a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.from','a.to','a.dbcon','b.description',DB::raw("SUM(b.qty) as qty"))
                ->where($g1,$g1content)
                ->wehre($g2,$g2content)
                ->wehre($g3,$g3content)
                ->get();
        }else{
            //$g1-----------   
            $field = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select(DB::raw("SUM(sample_size) AS sample_size")
                        ,DB::raw("SUM(lot_qty) AS lot_qty")
                        ,DB::raw("SUM(num_of_defectives) AS num_of_defects")
                        ,DB::raw("SUM(lot_accepted) AS lot_accepted")
                        ,DB::raw("SUM(lot_rejected) AS lot_rejected")
                        ,DB::raw("SUM(lot_inspected) AS lot_inspected")
                        ,'a.po_no','a.partcode','a.partname','a.customer','a.family','a.total_qty','a.die_no','a.qty','a.lot_no','a.type_of_inspection','a.severity_of_inspection','a.inspection_lvl','a.aql','a.accept','a.reject','a.date_inspected','a.shift','a.inspector','a.submission','a.visual_operator','a.fy_no','a.ww_no','a.remarks','a.ptcp_tnr','a.from','a.to','a.dbcon','b.description',DB::raw("SUM(b.qty) as qty"))
                ->groupBy($g1)
                ->get();
        }
        return $field;   

    }

    public function moldtime(Request $r)
    {   
        $timefrom = $this->convertStringToTime($r->timefrom);
        $timeto = $this->convertStringToTime($r->timeto);

        if($timefrom >= $this->convertStringToTime("7:30 AM") && $timeto <= $this->convertStringToTime("7:29 PM")) {
            return "Shift A";
        } else {
            return "Shift B";
        } 
    }

    private function convertStringToTime($time)
    {
        $dtime = Carbon::createFromFormat("G:i A", $time);
        $timestamp = $dtime->getTimestamp();

        return $timestamp;
    }

    public function editDefects(Request $request){
        $data = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')
            ->where('po',$request->pono)
            ->where('partcode',$request->partcode)
            ->where('description',$request->description)
            ->select('id','po','partcode','description',DB::raw('SUM(qty) as qty'))
            ->distinct()
            ->get();
        return $data;
    }

    public function getmoldmodcounts(Request $request){
        $hdstatus = $request->report_status;
        $datefrom = $request->datefrom;
        $dateto = $request->dateto;
        $output = []; 
        $table = '';                          
        if($hdstatus == "GROUPBY"){
            $table = DB::connection($this->mysql)->table('tbl_oqc_molding_mod')
                ->select('po',DB::raw("(GROUP_CONCAT(description SEPARATOR ' , ')) AS description"),DB::raw("(GROUP_CONCAT(lotno SEPARATOR ' , ')) AS lotno"),'submission','qty')
                ->groupBy('po','submission','partcode')
                ->get();        
               
        } else {
            $table = DB::connection($this->mysql)->table('oqc_inspection_moldings as a')
                ->leftJoin('tbl_oqc_molding_mod as b','a.lot_no','=','b.lotno')
                ->select('a.po_no','b.description','a.lot_no as lotno','a.submission')
                ->where('b.po',$request->pono)
                ->where('a.lot_no',$request->lotno)
                ->where('a.submission',$request->subs)
                ->get();    
        }
        foreach ($table as $key => $data) {
            $output['mod'][$key] = $data->description;
            $output['lotno'][$key] = $data->lotno;
        }
        return $output;
    }

    public function getoqcmoldtotallarlrrdppm(Request $request){
        $datefrom = $request->datefrom;
        $dateto = $request->dateto;
        $g1 = $request->g1;
        $g1content = $request->g1content;
        $g2 = $request->g2;
        $g2content = $request->g2content;
        $g3 = $request->g3;
        $g3content = $request->g3content;
        $status = $request->status;
        
        $field = DB::connection($this->mysql)->table('oqc_inspection_moldings')
        ->whereBetween('date_inspected',[$datefrom, $dateto])
        ->select(DB::raw("SUM(sample_size) AS sample_size")
            ,DB::raw("SUM(lot_qty) AS lot_qty")
            ,DB::raw("SUM(num_of_defectives) AS num_of_defects")
            ,DB::raw("SUM(lot_accepted) AS lot_accepted")
            ,DB::raw("SUM(lot_rejected) AS lot_rejected")
            ,DB::raw("SUM(lot_inspected) AS lot_inspected")
            ,'submission')
        ->groupBy('submission')->get();    
     
        return $field;
    } 

}//------end-----------
