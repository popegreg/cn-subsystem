<?php
namespace App\Http\Controllers\Phase3;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Config;
use Datatables;
use Dompdf\Dompdf;
use Carbon\Carbon;
use App\YPICSInvoicing;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use View;
use App;
use PDF;
use Excel;

class YPICSInvoicingController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;
    
    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->main_mysql = $com->userDBcon(Auth::user()->productline,'mysql');
            $this->mysql = $com->userDBcon(Auth::user()->productline,'traffic');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getInvoicing()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_INVCING')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $carrier = '';
            $pod = '';
            return view('phase3.ypicsinvoicing',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function getPackingListData()
    {
        $packinglist = DB::connection($this->mysql)->table('tbl_packing_list')
                            ->orderBy('id','desc')
                            ->select(['id',
                                'control_no',
                                DB::raw("DATE_FORMAT(invoice_date, '%m/%d/%Y') as invoice_date"),
                                'invoice_no',
                                'remarks_time',
                                DB::raw("DATE_FORMAT(remarks_pickupdate, '%m/%d/%Y') as remarks_pickupdate"),
                                'remarks_s_no',
                                'sold_to_id',
                                'sold_to',
                                'ship_to',
                                'carrier',
                                DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = carrier) as carrier_name"),
                                DB::raw("DATE_FORMAT(date_ship, '%m/%d/%Y') as date_ship"),
                                'port_loading',
                                // 'port_destination',
                                DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = port_destination) as port_destination"),
                                // 'description_of_goods',
                                DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = description_of_goods) as description_of_goods"),
                                'case_marks',
                                'note',
                                'from',
                                'to',
                                'freight',
                                'preparedby',
                                'checkedby',
                                'grossweight_invoicing',
                                'create_user',
                                'update_user',
                                'created_at',
                                'updated_at',
                                'invoicing_status'
                            ]);
        return Datatables::of($packinglist)
                        ->addColumn('action', function($data) {
                            return '<a href="javascript:;" class="btn purple btn_edit input-sm" id="btn_edit" data-ctrl="'.$data->control_no.'">
                                            <i class="fa fa-edit"></i>
                                        </a>';
                        })
                        ->editColumn('remarks_time', function ($data) {
                            return $data->remarks_time . '<br>' .
                                    $data->remarks_pickupdate . '<br>' .
                                    $data->remarks_s_no;
                        })
                        ->editColumn('shipping', function ($data) {
                            return $data->from . '<br>' .
                                    $data->to . '<br>' .
                                    $data->freight;
                        })
                        ->setRowId('control_no')
                        ->setRowClass(function($data) {
                            if ($data->invoicing_status == 'Complete') {
                                return 'alert-success';
                            }

                            if ($data->invoicing_status == 'Revised') {
                                return 'alert-danger';
                            }
                        })
                        ->make(true);
    }

    public function getInvoiceData()
    {
        $invoice = DB::connection($this->mysql)->table('ypics_invoicings')
                    ->orderBy('id','desc')
                    ->select([
                        'id',
                        'invoice_no',
                        'invoice_date',
                        'packinglist_ctrl',
                        'customer',
                        'description_of_goods',
                        'quantity',
                        'amount',
                        'destination',
                    ]);
        return Datatables::of($invoice)
                        ->addColumn('action', function($data) {
                            return '<a href="javascript:;" class="btn red btn_delete_invoice input-sm" data-id="'.$data->id.'">
                                            <i class="fa fa-trash"></i>
                                        </a>';
                        })->make(true);
    }

    public function getDetailsInvoicing($ctrl)
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_INVCING')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {

            $packinginfo = DB::connection($this->mysql)->table('tbl_packing_list')->where('control_no',$ctrl)->get();
            $packinglist = DB::connection($this->mysql)->table('tbl_packing_list')->orderBy('id', 'desc')->get();

            $packdetails = DB::connection($this->mysql)->table('tbl_packing_list_details as d')
                                ->join('tbl_packing_list as i', 'i.id', '=', 'd.packing_id')
                                ->where('i.control_no',$ctrl)
                                ->get();

            $invoice = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->select(
                                    'id',
                                    'packinglist_ctrl',
                                    'item_no',
                                    'po_no',
                                    'description',
                                    'draft_shipment',
                                    'item_code',
                                    DB::raw('SUM(quantity) AS quantity'),
                                    DB::raw('FORMAT(unitprice,4) as unitprice'),
                                    DB::raw('FORMAT(SUM(amount),2) as amount'),
                                    'prepared_by',
                                    'ship_date',
                                    'shippedfrom',
                                    'invoice_date',
                                    'products',
                                    'soldto_address',
                                    'shipto_address',
                                    'carrier',
                                    'gross_weight',
                                    'terms_of_payment',
                                    'country_origin',
                                    'revision_no',
                                    'transaction_no',
                                    'for_bir_no',
                                    'pickup_date',
                                    'freight',
                                    'via',
                                    'sailing_on',
                                    'no_of_packaging',
                                    'awb_no',
                                    'remarks',
                                    'note_hightlight')
                            ->where('packinglist_ctrl',$ctrl)
                            ->groupBY('po_no','description','draft_shipment','prepared_by')
                            // ->distinct();
                            ->get();

            $carrier = "";
            $pod = "";

            foreach ($packinginfo as $key => $pki) {
                $carrier = DB::connection($this->main_mysql)->table('tbl_mdropdowns')->where('id',$pki->carrier)->first();
                $pod = DB::connection($this->main_mysql)->table('tbl_mdropdowns')->where('id',$pki->port_destination)->first();
            }

            $preparedby = "";
            
            return view('phase3.detailsYpicsInvoicing',['userProgramAccess' => $userProgramAccess,
                'packinginfo' => $packinginfo,
                'packinglist' => $packinglist,
                'invoice' => $invoice,
                'packdetails' => $packdetails,
                'carrier' => $carrier,
                'pod' => $pod]); //'packinginfo' => $packinginfo
        }
    }

    public function getInvoiceDetails($id)
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_INVCING')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {

            $packinginfo = DB::connection($this->mysql)->table('tbl_packing_list')->where('control_no',$ctrl)->get();
            $packinglist = DB::connection($this->mysql)->table('tbl_packing_list')->orderBy('id', 'desc')->get();

            $packdetails = DB::connection($this->mysql)->table('tbl_packing_list_details as d')
                                ->join('tbl_packing_list as i', 'i.id', '=', 'd.packing_id')
                                ->where('i.control_no',$ctrl)
                                ->get();
            $invoice = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->select(
                                    'id',
                                    'packinglist_ctrl',
                                    'item_no',
                                    'po_no',
                                    'description',
                                    'draft_shipment',
                                    'item_code',
                                    DB::raw('SUM(quantity) AS quantity'),
                                    DB::raw('FORMAT(unitprice,4) AS unitprice'),
                                    DB::raw('SUM(amount) as amount'),
                                    'prepared_by',
                                    'ship_date',
                                    'shippedfrom',
                                    'invoice_date',
                                    'products',
                                    'soldto_address',
                                    'shipto_address',
                                    'carrier',
                                    'gross_weight',
                                    'terms_of_payment',
                                    'country_origin',
                                    'revision_no',
                                    'transaction_no',
                                    'for_bir_no',
                                    'pickup_date',
                                    'freight',
                                    'via',
                                    'sailing_on',
                                    'no_of_packaging',
                                    'awb_no',
                                    'remarks',
                                    'note_hightlight')
                            ->where('packinglist_ctrl',$ctrl)
                            ->groupBY('po_no','description','draft_shipment','prepared_by')
                            ->get();

            $carrier = "";
            $pod = "";

            foreach ($packinginfo as $key => $pki) {
                $carrier = DB::connection($this->main_mysql)->table('tbl_mdropdowns')->where('id',$pki->carrier)->first();
                $pod = DB::connection($this->main_mysql)->table('tbl_mdropdowns')->where('id',$pki->port_destination)->first();
            }

            $preparedby = "";
            
            return view('phase3.detailsYpicsInvoicing',['userProgramAccess' => $userProgramAccess,
                'packinginfo' => $packinginfo,
                'packinglist' => $packinglist,
                'invoice' => $invoice,
                'packdetails' => $packdetails,
                'carrier' => $carrier,
                'pod' => $pod]); //'packinginfo' => $packinginfo
        }
    }

    public function getNCV(Request $req)
    {
        $descofgoods = DB::connection($this->mysql)->table('tbl_packing_list')
                                ->where('control_no',$req->ctrl_no)
                                ->where('note','like','%NOT FOR BILLING%')
                                ->select('description_of_goods')
                                ->count();

        $goods = DB::connection($this->mysql)->table('tbl_packing_list')
                                ->where('control_no',$req->ctrl_no)
                                ->select('description_of_goods')
                                ->first();

        $data = DB::connection($this->main_mysql)->table('tbl_mdropdowns')->where('id',$goods->description_of_goods)->first();

        if ($descofgoods > 0) {
            return "NCV(".$data->description.")";
        } else {
            return $data->description;
        }
    }

    public function getInvoiceStatus(Request $req)
    {
        $data = DB::connection($this->mysql)->table('tbl_packing_list')
                    ->where('control_no',$req->ctrl_no)
                    ->select('invoicing_status')
                    ->first();
        return $data->invoicing_status;
    }

    public function getDetails(Request $req)
    {
        $ctrl = $req->ctrl_no;
        $box = $req->box_no;
        $packdetails = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->where('packinglist_ctrl',$ctrl)
                            ->where('item_no',$box)
                            ->get();
        return $packdetails;
    }

    public function getInitDetails(Request $req)
    {
        $ctrl = $req->ctrl_no;
        $packdetails = DB::connection($this->mysql)->table('ypics_invoicingdetails as d')
                            ->leftJoin('ypics_invoicings as i','d.packinglist_ctrl','=','i.packinglist_ctrl')
                            ->where('d.packinglist_ctrl',$ctrl)
                            ->select(DB::raw('d.sold_to_id as sold_to_id'),
                                    DB::raw('IF(i.soldto_address <> "",i.soldto_address,d.soldto_address) as soldto_address'),
                                    DB::raw('IF(i.shipto_address <> "",i.shipto_address,d.shipto_address) as shipto_address'),
                                    DB::raw('IF(i.shippedfrom <> "",i.shippedfrom,d.shippedfrom) as shippedfrom'),
                                    DB::raw('IF(i.shipto <> "",i.shipto,d.shipto) as shipto'),
                                    DB::raw('IF(i.carrier <> "",i.carrier,d.carrier) as carrier'),
                                    DB::raw('IF(i.gross_weight <> "",i.gross_weight,d.gross_weight) as gross_weight'),
                                    DB::raw('IF(i.terms_of_payment <> "",i.terms_of_payment,d.terms_of_payment) as terms_of_payment'),
                                    DB::raw('d.po_no as po_no'),
                                    DB::raw('d.description as description'),
                                    DB::raw('IF(i.country_origin <> "",i.country_origin,d.country_origin) as country_origin'),
                                    DB::raw('IF(i.quantity <> "",i.quantity,d.quantity) as quantity'),
                                    DB::raw('d.unitprice as unitprice'),
                                    DB::raw('IF(i.amount <> "",i.amount,d.amount) as amount'),
                                    DB::raw('IF(i.freight <> "",i.freight,d.freight) as freight'),
                                    DB::raw('IF(i.note_hightlight <> "",i.note_hightlight,d.note_hightlight) as note_hightlight'),
                                    DB::raw('d.ship_date as ship_date'),
                                    DB::raw('IF(i.case_marks <> "",i.case_marks,d.case_marks) as case_marks'),
                                    DB::raw('IF(i.revision_no <> "",i.revision_no,d.revision_no) as revision_no'),
                                    DB::raw('IF(i.transaction_no <> "",i.transaction_no,d.transaction_no) as transaction_no'),
                                    DB::raw('IF(i.for_bir_no <> "",i.for_bir_no,d.for_bir_no) as for_bir_no'),
                                    DB::raw('IF(i.via <> "",i.via,d.via) as via'),
                                    DB::raw('d.sailing_on as sailing_on'),
                                    DB::raw('d.no_of_packaging as no_of_packaging'),
                                    DB::raw('IF(i.awb_no <> "",i.awb_no,d.awb_no) as awb_no'),
                                    DB::raw('d.remarks as remarks'),
                                    DB::raw('IF(i.pickup_date <> "",i.pickup_date,d.pickup_date) as pickup_date'),
                                    DB::raw('IF(i.invoice_date <> "",i.invoice_date,d.invoice_date) as invoice_date'))
                            ->groupBy('i.packinglist_ctrl')
                            // ->distinct()
                            ->get();
        return $packdetails;
    }

    public function carrier(Request $req)
    {
        $carrier = DB::connection($this->common)->table('tbl_mdropdowns')->where('id',$req->id)->first();
        return $carrier->description;
    }

    public function descOfGoods(Request $req)
    {
        $descgoods = DB::connection($this->common)->table('tbl_mdropdowns')->where('id',$req->id)->first();
        return $descgoods->description;
    }

    public function portOfDestination(Request $req)
    {
        $pod = DB::connection($this->common)->table('tbl_mdropdowns')->where('id',$req->id)->first();
        return $pod->description;
    }

    private function ifExistInInvoicing($ctrl_no,$trans_no)
    {
        $hasRecord = DB::connection($this->mysql)->table('ypics_invoicings')
                        ->where('packinglist_ctrl',$ctrl_no)
                        ->where('transaction_no',$trans_no)
                        ->count();
        return $hasRecord;
    }

    public function postSaveDetails(Request $req)
    {
        $pack_id = DB::connection($this->mysql)->table('tbl_packing_list')
                    ->where('control_no',$req->packinglist_ctrl)
                    ->select('id')
                    ->first();

        $details = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                    ->where('packinglist_id',$pack_id->id)
                    ->get();

        if ($this->ifExistInInvoicing($req->packinglist_ctrl,$req->transaction_no) > 0) {
            return $this->UpdateInvoice($details,$req);
        } else {
            return $this->InsertInvoice($details,$req);
        }
    }

    private function InsertInvoice($details,$req)
    {
        $qty = 0;
        $amount = 0;
        $tot_amount = 0;
        $prod = 0;
        foreach ($details as $key => $dt) {
            $prod = $dt->quantity * str_replace(',','',$dt->unitprice);
            //$amount = number_format($prod,2);

            $amount = $dt->quantity * str_replace(',','',$dt->unitprice);

            DB::connection($this->mysql)->table('ypics_invoicingdetails')
                ->where('packinglist_ctrl',$req->packinglist_ctrl)
                ->update([
                    'products' => $req->description_of_goods,
                    'revision_no' => $req->revision_no,
                    'transaction_no' => $req->transaction_no,
                    'for_bir_no' => $req->for_bir_no,
                    'pickup_date' => $this->convertDate($req->pickup_date,'Y-m-d'),
                    'invoice_date' => $this->convertDate($req->invoice_date,'Y-m-d'),
                    'freight' => $req->freight,
                    'via' => $req->via,
                    'sailing_on' => $req->sailing_on,
                    'no_of_packaging' => $req->no_of_packaging,
                    'awb_no' => $req->awb_no,
                    'prepared_by' => $req->prepared_by,
                    'remarks' => $req->remarks,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            $qty += $dt->quantity;
            // $tot_amount += $amount;
            $tot_amount += number_format($amount,2,'.','');
        }
        

        DB::connection($this->mysql)->table('ypics_invoicings')->insert([
            'invoice_no' => $req->transaction_no,
            'packinglist_ctrl' => $req->packinglist_ctrl,
            'customer' => $this->getSoldto($req->sold_to_id),
            'description_of_goods' => $req->description_of_goods,
            'quantity' => $qty,
            'amount' => $tot_amount,
            'destination' => $req->shipto,
            'products' => $req->description_of_goods,
            'revision_no' => $req->revision_no,
            'transaction_no' => $req->transaction_no,
            'for_bir_no' => $req->for_bir_no,
            'pickup_date' => $this->convertDate($req->pickup_date,'Y-m-d'),
            'invoice_date' => $this->convertDate($req->invoice_date,'Y-m-d'),
            'freight' => $req->freight,
            'via' => $req->via,
            'sailing_on' => $req->sailing_on,
            'no_of_packaging' => $req->no_of_packaging,
            'awb_no' => $req->awb_no,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::connection($this->mysql)->table('tbl_packing_list')->where('control_no', $req->packinglist_ctrl)->update(['invoicing_status' => 'Complete']);
        $msg['msg'] = "Invoice was successfully saved";
        return $msg;
    }

    private function UpdateInvoice($details,$req)
    {
        $qty = 0;
        $amount = 0;
        $tot_amount = 0;
        $prod = 0;
        foreach ($details as $key => $dt) {
            $prod = $dt->quantity * str_replace(',','',$dt->unitprice);
            //$amount = number_format($prod,2);

            $amount = str_replace(',','',$dt->unitprice) * $dt->quantity;

            DB::connection($this->mysql)->table('ypics_invoicingdetails')
                ->where('packinglist_ctrl',$req->packinglist_ctrl)->update([
                    'products' => $req->description_of_goods,
                    'revision_no' => $req->revision_no,
                    'transaction_no' => $req->transaction_no,
                    'for_bir_no' => $req->for_bir_no,
                    'pickup_date' => $this->convertDate($req->pickup_date,'Y-m-d'),
                    'invoice_date' => $this->convertDate($req->invoice_date,'Y-m-d'),
                    'freight' => $req->freight,
                    'via' => $req->via,
                    'sailing_on' => $req->sailing_on,
                    'no_of_packaging' => $req->no_of_packaging,
                    'awb_no' => $req->awb_no,
                    'prepared_by' => $req->prepared_by,
                    'remarks' => $req->remarks,
                    'note_hightlight' => $req->note_hightlight,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            $qty += $dt->quantity;
            $tot_amount += number_format($amount,2,'.','');
            //$tot_amount += $amount;
        }
        DB::connection($this->mysql)->table('ypics_invoicings')
                ->where('transaction_no',$req->transaction_no)->update([
                    'transaction_no' => $req->transaction_no,
                    'packinglist_ctrl' => $req->packinglist_ctrl,
                    'customer' => $this->getSoldto($req->sold_to_id),
                    'description_of_goods' => $req->description_of_goods,
                    'quantity' => $qty,
                    'amount' => $tot_amount,
                    'destination' => $req->shipto,
                    'products' => $req->description_of_goods,
                    'revision_no' => $req->revision_no,
                    'transaction_no' => $req->transaction_no,
                    'for_bir_no' => $req->for_bir_no,
                    'pickup_date' => $this->convertDate($req->pickup_date,'Y-m-d'),
                    'invoice_date' => $this->convertDate($req->invoice_date,'Y-m-d'),
                    'freight' => $req->freight,
                    'via' => $req->via,
                    'sailing_on' => $req->sailing_on,
                    'no_of_packaging' => $req->no_of_packaging,
                    'awb_no' => $req->awb_no,
                    'prepared_by' => $req->prepared_by,
                    'note_hightlight' => $req->note_hightlight,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        DB::connection($this->mysql)->table('tbl_packing_list')
            ->where('control_no', $req->packinglist_ctrl)
            ->update(['invoicing_status' => 'Complete']);

        $msg['msg'] = "Invoice was successfully updated";
        return $msg;
    }

    public function editDraftShipment(Request $req)
    {
        try {
            $updated = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                ->where('po_no',$req->pk)
                ->where('packinglist_ctrl',$req->ctrl)
                ->update(['draft_shipment' => $req->value]);

        } catch (Exception $e) {
            return response()->json(array('status'=>$e->getMessage()),200);
        }

        if($updated) {
            return response()->json(array('status'=>1),200);
        } else {
            return response()->json(array('status'=>0),200);
        }
    }

    private function getSoldto($id)
    {
        $soldto = DB::connection($this->common)->table('tbl_soldto')->where('code',$id)->select('companyname')->first();
        return $soldto->companyname;
    }

    public function getPrintOut($ctrl)
    {
        $check = DB::connection($this->mysql)->table('ypics_invoicingdetails')->where('packinglist_ctrl',$ctrl)->count();
        if ($check > 0) {
            $details1 = DB::connection($this->mysql)->table('ypics_invoicings as i')
                            ->leftJoin('ypics_invoicingdetails as d','i.packinglist_ctrl','=','d.packinglist_ctrl')
                            ->select(
                                    DB::raw('IF(i.transaction_no <> "",i.transaction_no,d.transaction_no) as transaction_no'),
                                    DB::raw('IF(i.revision_no <> "",i.revision_no,d.revision_no) as revision_no'),
                                    DB::raw('IF(i.pickup_date <> "",i.pickup_date,d.pickup_date) as pickup_date'),
                                    DB::raw('IF(i.invoice_date <> "",i.invoice_date,d.invoice_date) as invoice_date'),
                                    DB::raw('IF(i.soldto_address <> "",i.soldto_address,d.soldto_address) as soldto_address'),
                                    DB::raw('IF(i.shipto_address <> "",i.shipto_address,d.shipto_address) as shipto_address'),
                                    DB::raw('IF(i.shippedfrom <> "",i.shippedfrom,d.shippedfrom) as shippedfrom'),
                                    DB::raw('IF(i.shipto <> "",i.shipto,d.shipto) as shipto'),
                                    DB::raw('IF(i.carrier <> "",i.carrier,d.carrier) as carrier'),
                                    DB::raw('IF(i.freight <> "",i.freight,d.freight) as freight'),
                                    DB::raw('IF(i.gross_weight <> "",i.gross_weight,d.gross_weight) as gross_weight'),
                                    DB::raw('IF(i.terms_of_payment <> "",i.terms_of_payment,d.terms_of_payment) as terms_of_payment'),
                                    DB::raw('IF(i.via <> "",i.via,d.via) as via'),
                                    DB::raw('IF(i.awb_no <> "",i.awb_no,d.awb_no) as awb_no'),
                                    DB::raw('IF(i.sailing_on <> "",i.sailing_on,d.sailing_on) as sailing_on'),
                                    DB::raw('IF(i.country_origin <> "",i.country_origin,d.country_origin) as country_origin'),
                                    DB::raw('IF(i.no_of_packaging <> "",i.no_of_packaging,d.no_of_packaging) as no_of_packaging'),
                                    DB::raw('IF(i.case_marks <> "",i.case_marks,d.case_marks) as case_marks'),
                                    DB::raw('IF(i.products <> "",i.products,d.products) as products'),
                                    DB::raw('IF(i.note_hightlight <> "",i.note_hightlight,d.note_hightlight) as note_hightlight'),
                                    DB::raw('i.packinglist_ctrl as packinglist_ctrl'))
                            ->where('i.packinglist_ctrl',$ctrl)
                            ->groupBy('i.packinglist_ctrl')
                            ->first();

            $details2 = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->select(
                                    'po_no',
                                    'description',
                                    'draft_shipment',
                                    'item_code',
                                    'revision_no',
                                    'transaction_no',
                                    'for_bir_no',
                                    DB::raw('SUM(quantity) AS quantity'),
                                    DB::raw('FORMAT(unitprice,4) AS unitprice'),
                                    DB::raw('SUM(amount) as amount'),
                                    'prepared_by')
                            ->where('packinglist_ctrl',$ctrl)
                            ->groupBY('po_no','description','draft_shipment','prepared_by')
                            ->get();
            $details3 = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->where('packinglist_ctrl',$ctrl)
                            ->select(DB::raw("SUM(quantity) as tot_qty"),DB::raw("SUM(amount) as tot_amt"))
                            ->groupBy('packinglist_ctrl')
                            ->first();
            $data = [
                'details1' => $details1,
                'details2' => $details2,
                'details3' => $details3
            ];

            return PDF::loadView('pdf.ypicsinvoice', $data)
                        ->setPaper('A4')
                        ->setOrientation('portrait')
                        ->setOption('margin-top', 20)
                        ->setOption('margin-right', 6)
                        ->setOption('margin-left', 5)
                        ->setOption('margin-bottom', 0)
                        ->inline();
        }
        
    }

    public function getShippingList(Request $req)
    {
        $from_rep = $this->convertDate($req->from_rep,'Y-m-d');
        $to_rep = $this->convertDate($req->to_rep,'Y-m-d');

        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            Excel::create('ShippingList_'.$date, function($excel) use($from_rep, $to_rep)
            {
                $excel->sheet('ShippingList', function($sheet) use($from_rep, $to_rep)
                {
                    $sheet->setHeight(1, 15);
                    $sheet->cells('A1:H1', function($cells) {
                        $cells->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('A1', "INVOICE_NO");//"INVOICE SUMMARY( SALES )");
                    $sheet->cell('B1', "No need to fill up");//"InvoiceDate");
                    $sheet->cell('C1', "QTY:");//"INVOICE_NO");
                    $sheet->cell('D1', "PU_DATE");//"QTY");
                    $sheet->cell('E1', "YEC PO NO");//"PU-DATE");
                    $sheet->cell('F1', "PO branch");//"YEC PO");
                    $sheet->cell('G1', "Code for");//"PO BRANCH");
                    $sheet->cell('H1', "Device name");//"CODE FOR");
                    //$sheet->cell('H2', "DESCRIPTION");
                    //$sheet->cell('I2', "Destination");

                    $row = 2;
                    // $datas = DB::connection($this->mysql)->table('ypics_invoicings')
                    //             ->whereRaw("invoice_date BETWEEN '".$from_rep."' AND '".$to_rep."'")
                    //             ->select('invoice_date')
                    //             ->get();

                    //foreach ($datas as $key => $data) {
                        $subs = DB::connection($this->mysql)->table('ypics_invoicings as i')
                                    ->join('ypics_invoicingdetails as d','i.packinglist_ctrl','=','d.packinglist_ctrl')
                                    ->whereRaw("i.invoice_date BETWEEN '".$from_rep."' AND '".$to_rep."'")
                                    ->select('i.invoice_no',
                                            DB::raw("SUM(d.quantity) as  quantity"),
                                            'd.pickup_date',
                                            'd.draft_shipment',
                                            'd.po_no',
                                            DB::raw("'10' AS po_branch"),
                                            'd.description',
                                            'i.destination')
                                    ->groupBy('i.invoice_no',
                                            'd.pickup_date',
                                            'd.po_no',
                                            'd.description',
                                            'i.destination',
                                            'd.draft_shipment')
                                    ->get();

                        foreach ($subs as $key => $sub) {
                            if (strlen($sub->po_no) == 15) {
                                $sheet->setHeight($row, 15);
                                $sheet->cell('A'.$row, substr($sub->invoice_no,4));
                                $sheet->cell('C'.$row, $sub->quantity);
                                $sheet->cell('D'.$row, $this->convertDate($sub->pickup_date,'Ymd'));
                                $sheet->cell('E'.$row, $sub->po_no);
                                $sheet->cell('F'.$row, $sub->po_branch);
                                $sheet->cell('G'.$row, (empty($sub->draft_shipment))?"W001":"W021");
                                $sheet->cell('H'.$row, $sub->description);
                                //$sheet->cell('I'.$row, $sub->destination);
                                $row++;
                            }
                        }
                    //}

                });
            })->download('xls');
            // ->store('xls',$path)->export();


        } catch (Exception $e) {
        }
    }

    public function getInvoiceSummary(Request $req)
    {
        $from_rep = $this->convertDate($req->from_rep,'Y-m-d');
        $to_rep = $this->convertDate($req->to_rep,'Y-m-d');

        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            Excel::create('InvoiceSummary_'.$date, function($excel) use($from_rep, $to_rep)
            {
                $excel->sheet('InvoiceSummary', function($sheet) use($from_rep, $to_rep)
                {
                    $sheet->setHeight(1, 20);
                    $sheet->cells('A1:I1', function($cells) {
                        $cells->setFont([
                            'family'     => 'Calibri',
                            'size'       => '16',
                            'bold'       =>  true,
                        ]);
                    });
                    $sheet->mergeCells('A1:I1');
                    $sheet->cell('A1', "INVOICE SUMMARY( SALES )");

                    $sheet->setHeight(2, 15);
                    $sheet->cells('A2:I2', function($cells) {
                        $cells->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('A2', "InvoiceDate");
                    $sheet->cell('B2', "InvNo");
                    $sheet->cell('C2', "BIR No..");
                    $sheet->cell('D2', "Packing");
                    $sheet->cell('E2', "Customer");
                    $sheet->cell('F2', "A / R c Particulars");
                    $sheet->cell('G2', "Quantity");
                    $sheet->cell('H2', "Amount");
                    $sheet->cell('I2', "Destination");

                    $row = 3;
                    $datas = DB::connection($this->mysql)->table('ypics_invoicings')
                                ->whereRaw("invoice_date BETWEEN '".$from_rep."' AND '".$to_rep."'")
                                ->select('invoice_date')
                                ->distinct()
                                ->orderBy('invoice_date','asc')
                                ->get();

                    foreach ($datas as $key => $data) {
                        $sheet->setHeight($row, 15);
                        $sheet->cell('A'.$row, function($cell) {
                            $cell->setFont([
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true,
                            ]);
                        });
                        $sheet->cell('A'.$row, $this->convertDate($data->invoice_date,'Y/m/d'));
                        $subs = DB::connection($this->mysql)->table('ypics_invoicings as i')
                                    ->select('i.invoice_no',
                                            'i.packinglist_ctrl',
                                            'i.customer',
                                            'i.description_of_goods',
                                            DB::raw('SUM(d.amount) as amount'),
                                            DB::raw('SUM(d.quantity) as quantity'),
                                            'i.destination',
                                            DB::raw('IF(d.for_bir_no <> "",d.for_bir_no,i.for_bir_no) as bir_no'))
                                    ->leftJoin('ypics_invoicingdetails as d', 'i.packinglist_ctrl','=','d.packinglist_ctrl')
                                    ->where('i.invoice_date',$data->invoice_date)
                                    ->groupBy('i.invoice_no',
                                            'i.packinglist_ctrl')
                                            // 'i.customer',
                                            // 'i.description_of_goods',
                                            // 'i.destination',
                                            // 'd.for_bir_no')
                                    ->get();
                        $row+=1;
                        foreach ($subs as $key => $sub) {
                            $sheet->setHeight($row, 15);
                            if (strpos($sub->invoice_no,'PMI-') !== false) {
                                $sheet->cell('B'.$row, substr($sub->invoice_no,4));
                            } else {
                                $sheet->cell('B'.$row, $sub->invoice_no);
                                
                            }

                            $sheet->cell('C'.$row, $sub->bir_no);
                            $sheet->cell('D'.$row, $sub->packinglist_ctrl);
                            $sheet->cell('E'.$row, $sub->customer);
                            $sheet->cell('F'.$row, $sub->description_of_goods);
                            $sheet->cell('G'.$row, $sub->quantity);
                            $sheet->cell('H'.$row, $this->getAmount($sub->packinglist_ctrl)); // number_format($sub->amount,2,'.','')
                            $sheet->cell('I'.$row, $sub->destination);
                            $row++;
                        }
                        $row++;
                    }

                });
            })->download('xls');
            // ->store('xls',$path)->export();


        } catch (Exception $e) {
        }
    }

    public function getSalesReport(Request $req)
    {
        $from_rep = $this->convertDate($req->from_rep,'Y-m-d');
        $to_rep = $this->convertDate($req->to_rep,'Y-m-d');

        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            Excel::create('SalesReport_'.$date, function($excel) use($from_rep, $to_rep)
            {
                $excel->sheet('SalesReport', function($sheet) use($from_rep, $to_rep)
                {
                    $sheet->mergeCells('A1:G1');

                    $sheet->setHeight(1, 20);
                    $sheet->cells('A1:G1', function($cells) {
                        $cells->setFont([
                            'family'     => 'Calibri',
                            'size'       => '16',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('A1', "SALES REPORT");

                    $sheet->setHeight(3, 15);
                    $sheet->cells('A3:G3', function($cells) {
                        $cells->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('A3', "Category Name");
                    $sheet->cell('B3', "Invoice Date");
                    $sheet->cell('C3', "Invoice No.");
                    $sheet->cell('D3', "BIR No.");
                    $sheet->cell('E3', "Customer");
                    $sheet->cell('F3', "Quantity");
                    $sheet->cell('G3', "Amount");

                    $row = 4;
                    $datas = DB::connection($this->mysql)->table('ypics_invoicings')
                                ->whereRaw("invoice_date BETWEEN '".$from_rep."' AND '".$to_rep."'")
                                ->select('description_of_goods')
                                ->groupBy('description_of_goods')
                                ->get();
                    $grand_qty=0;
                    $grand_amt=0;

                    foreach ($datas as $key => $data) {
                        $sheet->setHeight($row, 15);
                        $sheet->cell('A'.$row, $data->description_of_goods);

                        // $subs = DB::connection($this->mysql)->table('ypics_invoicings')
                        //             ->whereRaw("description_of_goods = '".$data->description_of_goods."' AND invoice_date BETWEEN '".$from_rep."' AND '".$to_rep."'")
                        //             //->where('invoice_date',$data->invoice_date)
                        //             // ->groupBy('description_of_goods','invoice_no')
                        //             ->get();
                        $subs = DB::connection($this->mysql)->table('ypics_invoicings as i')
                                    ->select('i.invoice_no',
                                            'i.invoice_date',
                                            'i.transaction_no',
                                            'i.packinglist_ctrl',
                                            'i.customer',
                                            'i.description_of_goods',
                                            'i.amount',
                                            'i.quantity',
                                            // DB::raw('SUM(d.amount) as amount'),
                                            // DB::raw('SUM(d.quantity) as quantity'),
                                            'i.destination',
                                           DB::raw('IF(d.for_bir_no <> "",d.for_bir_no,i.for_bir_no) as bir_no'))
                                    ->leftJoin('ypics_invoicingdetails as d', 'i.packinglist_ctrl','=','d.packinglist_ctrl')
                                    ->whereRaw("i.description_of_goods = '".$data->description_of_goods."' AND i.invoice_date BETWEEN '".$from_rep."' AND '".$to_rep."'")
                                    ->groupBy('i.invoice_no',
                                            'i.transaction_no',
                                            'i.packinglist_ctrl',
                                            'i.customer',
                                            'i.description_of_goods',
                                            'i.destination')
                                    ->get();
                                    
                        $row+=1;
                        $qty=0;
                        $amt=0;
                        
                        foreach ($subs as $key => $sub) {
                            $sheet->setHeight($row, 15);
                            $sheet->cell('B'.$row, $sub->invoice_date);
                            $sheet->cell('C'.$row, substr($sub->invoice_no,4));
                            $sheet->cell('D'.$row, $this->getBIRno($sub->packinglist_ctrl));
                            $sheet->cell('E'.$row, $sub->customer);

                            $sheet->cells('F'.$row.':G'.$row, function($cells) {
                                $cells->setAlignment('right');
                            });

                            $sheet->cell('F'.$row, $this->getQty($sub->packinglist_ctrl));
                            $sheet->cell('G'.$row, $this->getAmount($sub->packinglist_ctrl));
                            $row++;
                            $qty+=$this->getQty($sub->packinglist_ctrl);
                            $amt+=$this->getAmount($sub->packinglist_ctrl);
                        }

                        $sheet->setHeight($row, 15);
                        $sheet->cells('E'.$row.':G'.$row, function($cells) {
                            $cells->setFont([
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true,
                            ]);
                        });

                        $sheet->cell('E'.$row, "Sub Total:");

                        $sheet->cells('F'.$row.':G'.$row, function($cells) {
                            $cells->setAlignment('right');
                        });

                        $sheet->cell('F'.$row, number_format($qty,2,'.',''));
                        $sheet->cell('G'.$row, number_format($amt,2,'.',''));
                        $row++;
                        $grand_qty+=$qty;
                        $grand_amt+=$amt;
                    }

                    $sheet->setHeight($row, 15);
                    $sheet->cells('E'.$row.':G'.$row, function($cells) {
                        $cells->setFont([
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true,
                        ]);
                    });

                    $sheet->cell('E'.$row, "Grand Total:");

                    $sheet->cells('F'.$row.':G'.$row, function($cells) {
                        $cells->setAlignment('right');
                    });

                    $sheet->cell('F'.$row, number_format($grand_qty,2,'.',''));
                    $sheet->cell('G'.$row, number_format($grand_amt,2,'.',''));

                });

            })->download('xls');
        } catch (Exception $e) {
        }
    }

    private function getBIRno($packinglist_ctrl)
    {
        $db = DB::connection($this->mysql)->table('ypics_invoicings as i')
                ->leftJoin('ypics_invoicingdetails as d', 'i.packinglist_ctrl','=','d.packinglist_ctrl')
                ->select(DB::raw('IF(d.for_bir_no <> "",d.for_bir_no,i.for_bir_no) as for_bir_no'))
                ->where('d.packinglist_ctrl',$packinglist_ctrl)
                ->groupBy('d.packinglist_ctrl')
                ->first();
                
                                    
                
        if (count((array)$db) > 0) {
            return $db->for_bir_no;
        }
    }

    private function convertDate($date,$format)
    {
        $time = strtotime($date);
        $newdate = date($format,$time);
        return $newdate;
    }

    public function deleteInvoice(Request $req)
    {
        $data = [
            'msg' => 'Deleting failed',
            'status' => 'failed'
        ];

        $delete = DB::connection($this->mysql)->table('ypics_invoicings')
                    ->where('id',$req->id)->delete();

        if ($delete) {
            $data = [
                'msg' => 'Invoice was successfully deleted.',
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function getAmount($packinglist_ctrl)
    {
        $details = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                        ->select(
                                'po_no',
                                'description',
                                'draft_shipment',
                                'item_code',
                                DB::raw('SUM(quantity) AS quantity'),
                                DB::raw('FORMAT(unitprice,4) AS unitprice'),
                                DB::raw('SUM(amount) as amount'),
                                'prepared_by')
                        ->where('packinglist_ctrl',$packinglist_ctrl)
                        ->groupBY('po_no','description','draft_shipment','prepared_by')
                        ->get();
        $tot_amount = 0;
        $amount = 0;
        foreach ($details as $key => $detail) {
            $amount = $detail->quantity * str_replace(',','',$detail->unitprice);
            $tot_amount += number_format($amount,2,'.','');
        }

        return $tot_amount;
    }

    public function getQty($packinglist_ctrl)
    {
        $details = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                        ->select(
                                'po_no',
                                'description',
                                'draft_shipment',
                                'item_code',
                                DB::raw('SUM(quantity) AS quantity'),
                                DB::raw('FORMAT(unitprice,4) AS unitprice'),
                                DB::raw('SUM(amount) as amount'),
                                'prepared_by')
                        ->where('packinglist_ctrl',$packinglist_ctrl)
                        ->groupBY('po_no','description','draft_shipment','prepared_by')
                        ->get();
        // $tot_amount = 0;
        $qty = 0;
        foreach ($details as $key => $detail) {
            $qty += $detail->quantity;
            //$tot_amount += number_format($amount,2,'.','');
        }

        return $qty;
    }

}
