<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PackingListSystemController.php
     MODULE NAME:  3006 : Packing List System
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.07.05
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.07.05     MESPINOSA       Initial Draft
*******************************************************************************/
?>
<?php
namespace App\Http\Controllers\Phase2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use Datatables;
use Carbon\Carbon;
use Config;
use Excel;
use Dompdf\Dompdf;
use DB;
use PDF;
use App;

/**
* Packing List & Details System Controller
**/
class PackingListSystemController extends Controller
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

    /**
    * Initial Page Load.
    **/
    public function getPackingListSystem(Request $request_data)
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PLSYSTEM'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $invoicedate_from = $request_data['srch_from'];
            $invoicedate_to   = $request_data['srch_to'];
            //$dbconnection = 'sqlsrvbu';
            $productlines = DB::connection($this->common)->table('mproductlines')->get();
            $dbconnection = Auth::user()->productline;

            //$packinglist = $this->getPackingList($invoicedate_from, $invoicedate_to);

            return view('phase2.PackingListSystem'
                    ,['userProgramAccess' => $userProgramAccess
                    // , 'packinglist' => $packinglist
                    , 'srchfrom' => $invoicedate_from
                    , 'srchto' => $invoicedate_to
                    , 'productlines' => $productlines
                    , 'dbconnection' => $dbconnection]);
        }
    }

    /**
    * Retrieve Packing List from DB.
    **/

    public function getPackingListDatable(Request $req)
    {
        $invoicedate_from = $req->from;
        $invoicedate_to = $req->to;
        $id = $req->id;
        try
        {
            if(empty($invoicedate_from) || empty($invoicedate_to))
            {
                # Get all records without filter
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
                                'port_destination',
                                DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = port_destination) as port_destination_name"),
                                'description_of_goods',
                                DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = description_of_goods) as description_of_goods_name"),
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
                                'updated_at'
                            ]);
            }
            else
            {
                if(empty($id))
                {
                    # Get records in between invoice date indicated
                    $packinglist = DB::connection($this->mysql)->table('tbl_packing_list')
                                    ->whereRaw("invoice_date BETWEEN STR_TO_DATE('" . $invoicedate_from
                                        ."', '%m/%d/%Y') AND STR_TO_DATE('" . $invoicedate_to ."', '%m/%d/%Y')")
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
                                        'port_destination',
                                        DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = port_destination) as port_destination_name"),
                                        'description_of_goods',
                                        DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = description_of_goods) as description_of_goods_name"),
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
                                        'updated_at'
                                    ]);
                                    
                }
                else
                {
                    # Get records with the given ID.
                    $packinglist = DB::connection($this->mysql)->table('tbl_packing_list')
                                    ->where('id', $id)
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
                                        'port_destination',
                                        DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = port_destination) as port_destination_name"),
                                        'description_of_goods',
                                        DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = description_of_goods) as description_of_goods_name"),
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
                                        'updated_at'
                                    ]);
                                    
                }
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return Datatables::of($packinglist)
                        ->editColumn('id', function ($data) {
                            return $data->id;
                        })
                        ->editColumn('remarks', function ($data) {
                            return $data->remarks_time . '<br>' .
                                    $data->remarks_pickupdate . '<br>' .
                                    $data->remarks_s_no;
                        })
                        ->editColumn('carrier', function($data) {
                            return $data->carrier_name;
                        })
                        ->editColumn('port_destination', function($data) {
                            return $data->port_destination_name;
                        })
                        // ->editColumn('description_of_goods', function($data) {
                        //     return $data->description_of_goods_name;
                        // })
                        ->editColumn('from', function ($data) {
                            return $data->from . '<br>' .
                                    $data->to . '<br>' .
                                    $data->freight;
                        })
                        ->make(true);
    }


    private function getPackingList($invoicedate_from,$invoicedate_to,$id='')
    {
        try
        {
            if(empty($invoicedate_from) || empty($invoicedate_to))
            {
                # Get all records without filter
                $packinglist = DB::connection($this->mysql)->table('tbl_packing_list')
                            ->select('id',
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
                                DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = carrier) as carrier_name"),
                                DB::raw("DATE_FORMAT(date_ship, '%m/%d/%Y') as date_ship"),
                                'port_loading',
                                'port_destination',
                                DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = port_destination) as port_destination_name"),
                                'description_of_goods',
                                DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = description_of_goods) as description_of_goods_name"),
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
                                'updated_at'
                            )->get();
            }
            else
            {
                if(empty($id))
                {
                    # Get records in between invoice date indicated
                    $packinglist = DB::connection($this->mysql)->table('tbl_packing_list')
                                    ->whereRaw("invoice_date BETWEEN STR_TO_DATE('" . $invoicedate_from
                                        ."', '%m/%d/%Y') AND STR_TO_DATE('" . $invoicedate_to ."', '%m/%d/%Y')")
                                    ->select('id',
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
                                        DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = carrier) as carrier_name"),
                                        DB::raw("DATE_FORMAT(date_ship, '%m/%d/%Y') as date_ship"),
                                        'port_loading',
                                        'port_destination',
                                        DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = port_destination) as port_destination_name"),
                                        'description_of_goods',
                                        DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = description_of_goods) as description_of_goods_name"),
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
                                        'updated_at'
                                    )->get();
                                    
                }
                else
                {
                    # Get records with the given ID.
                    $packinglist = DB::connection($this->mysql)->table('tbl_packing_list')
                                    ->where('id', $id)
                                    ->select('id',
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
                                        DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = carrier) as carrier_name"),
                                        DB::raw("DATE_FORMAT(date_ship, '%m/%d/%Y') as date_ship"),
                                        'port_loading',
                                        'port_destination',
                                        DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = port_destination) as port_destination_name"),
                                        'description_of_goods',
                                        DB::raw("(SELECT description from pmi_cn.tbl_mdropdowns where id = description_of_goods) as description_of_goods_name"),
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
                                        'updated_at'
                                    )->get();
                                    
                }
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $packinglist;
    }

    private function getLastID()
    {
        $db = DB::connection($this->mysql)->table('tbl_packing_list')
                ->select('id')
                ->orderBy('id','desc')
                ->first();
        return $db->id;
    }

    /**
    * Save packing list & details.
    **/
    public function savePackingList(Request $request_data)
    {
        $result = false;
        // try
        // {
            #Get details values
            $details = $request_data['details'];
            #Get details list values
            $detailsList = $request_data['detailsList'];

            #Validate dateship
            if(empty($details[3]))
            {
                $dateship    = '';
            }
            else
            {
                $dateship    = date('Y-m-d',strtotime($details[3]));
            }
            #Validate invoicedate
            if(empty($details[7]))
            {
                $invoicedate = '';
            }
            else
            {
                $invoicedate = date('Y-m-d',strtotime($details[7]));
            }
            #Validate remarks time
            if(empty($details[8]) || $details[8] == 'NA' || $details[8] == 'na' || $details[8] == 'n/a' || $details[8] == 'N/A')
            {
                $r_time      = '';
            }
            else
            {
                $r_time = date('h:i A',strtotime($details[8]));
            }
            #Validate remarks pickup date
            if(empty($details[9]) || $details[9] == 'NA' || $details[9] == 'na' || $details[9] == 'n/a' || $details[9] == 'N/A')
            {
                $r_pickupdate= 'N/A';
            }
            else
            {
                $r_pickupdate= date('Y-m-d',strtotime($details[9]));
            }

            if(empty($details[2]) || $details[2] == 'NA' || $details[2] == 'na' || $details[2] == 'n/a' || $details[2] == 'N/A')
            {
                $carrier= 'N/A';
            }
            else
            {
                $carrier= $details[2];
            }

            $soldtoid        = $details[0];
            $soldto          = $details[1];
            #[3]dateship
            $portloading     = $details[4];
            $portdes         = $details[5];
            $controlno       = $details[6];
            #[7]invoicedate
            #[8]r_time
            #[9]r_pickupdate
            $sno             = $details[10];
            $shipto          = $details[11];
            $shipinstruction = $details[12];
            $casemarks       = $details[13];
            $note            = $details[14];
            $to              = $details[15];
            $freight         = $details[16];
            $from            = 'PRICON MICROELECTRONICS, INC.';

            $preparedby      = $details[18];
            $checks          = $details[19];
            $gweight         = $details[20];

            $checkedby = '';
            $message = '';

            if ($checks != null) {
                foreach ($checks as $key => $value) {
                    $checkedby .= $value ." / ";
                }
            }
            

            $selecteditem = $details[17];

            #INSERT or UPDATE
            if(empty($selecteditem))
            {
                #Insert or Add new record
                $re_time = $details[8];
                if ($details[8] == 'N/A'){
                    $re_time = 'N/A';
                }else{
                    $re_time = date('h:i A',strtotime($details[8]));
             
                }
                DB::connection($this->mysql)->table('tbl_packing_list')
                    ->insert(['control_no'     => $controlno,
                        'invoice_date'         => $invoicedate,
                        'invoice_no'           => '',
                        'remarks_time'         => $re_time, //date('h:i A',strtotime($details[8])),
                        'remarks_pickupdate'   => $r_pickupdate,
                        'remarks_s_no'         => $sno,
                        'sold_to_id'           => $soldtoid,
                        'sold_to'              => $soldto,
                        'ship_to'              => $shipto,
                        'carrier'              => $carrier,
                        'date_ship'            => $dateship,
                        'port_loading'         => $portloading,
                        'port_destination'     => $portdes,
                        'description_of_goods' => $shipinstruction,
                        'case_marks'           => $casemarks,
                        'note'                 => $note,
                        'from'                 => $from,
                        'to'                   => $to,
                        'freight'              => $freight,
                        'preparedby'           => $preparedby,
                        'checkedby'            => $checkedby,
                        'grossweight_invoicing'=> $gweight,
                        'create_user'          => Auth::user()->user_id,
                        'update_user'          => Auth::user()->user_id,
                        'created_at'           => date("Y/m/d h:i:sa"),
                        'updated_at'           => date("Y/m/d h:i:sa")
                    ]);

                    $selecteditem = $this->getLastID();

                if (count($detailsList) > 0) {
                    
                    DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->where('packinglist_id',$selecteditem)
                            ->delete();
                    
                    $this->insertPackingDetails($detailsList, $selecteditem, 'ADD');
                    foreach ($detailsList as $key => $row) {
                        if (strlen($row[1]) == 15 || substr($row[1],0,2) == 'SJ' || substr($row[1],0,2) == 'ES') {
                            $unitprice = $row[4];
                        } else {
                            if ($this->checkBUNR($row[1]) == 'PACKAGING') {
                                $unitprice = $row[4];
                            }
                            if(strpos($this->getDropdownVal($shipinstruction),'RAW MATERIALS') !== false || strpos($this->getDropdownVal($shipinstruction),'RM') !== false) {

                                $markup = DB::connection($this->common)->table('invoicing_markup')
                                            ->where('prod_line',Auth::user()->productline)
                                            ->select('multiplier')->first();

                                $percent = $row[4]*$markup->multiplier;
                                $unitprice = $row[4]+ $percent;
                            } else {
                                $unitprice = $row[4];
                            }
                        }
                        $amt = $row[5] * $unitprice;
                        DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->insert([
                                'packinglist_id' => $selecteditem,
                                'item_no' => $row[0], 
                                'packinglist_ctrl' => $controlno,
                                'products' => $this->getDropdownVal($shipinstruction),
                                'sold_to_id' => $soldtoid,
                                'soldto_address' => $soldto,
                                'shipto_address' => $shipto,
                                'ship_date' => $dateship,
                                'shippedfrom' => $from,
                                'shipto' => $to,
                                'carrier' => ($carrier == 'N/A')? $carrier : $this->getDropdownVal($carrier),
                                'gross_weight' => $gweight,
                                'terms_of_payment' => '30 DAYS FR. BILLING DATE',
                                'po_no' => $row[1],
                                'description' => $row[2],
                                'country_origin' => 'PHILIPPINES',
                                'quantity' => $row[5],
                                'unitprice' => $unitprice,
                                'amount' => $amt,
                                'pickup_date' => $this->convertDate($r_pickupdate,'Y-m-d'),
                                'invoice_date' => $this->convertDate($invoicedate,'Y-m-d'),
                                'freight' => $freight,
                                'via' => "BY AIR",
                                'note_hightlight' => $note,
                                'item_code' => $row[3],
                                'case_marks' => $casemarks,
                                'created_at' => date("Y/m/d h:i:sa"),
                                'updated_at' => date("Y/m/d h:i:sa")
                            ]);
                    }
                }

                $message = "Packing List Successfully Added.";
            }
            else
            {
                $selecteditem    = $details[17];
                if ($this->checkIfDoneInvoice($selecteditem)) {
                    DB::connection($this->mysql)->table('tbl_packing_list')
                    ->where('id', $selecteditem)
                    ->update(['invoicing_status' => "Revised"]);
                }
                #Update record
                $re_time = $details[8];
                if (empty($details[8]) || $details[8] == 'NA' || $details[8] == 'na' || $details[8] == 'n/a' || $details[8] == 'N/A'){
                    $re_time = 'N/A';
                }else{
                    $re_time = date('h:i A',strtotime($details[8]));
                }

                $result = DB::connection($this->mysql)->table('tbl_packing_list')
                    ->where('id', $selecteditem)
                    ->update(['control_no'     => $controlno,
                        'invoice_date'         => $invoicedate,
                        'invoice_no'           => '',
                        'remarks_time'         => $re_time, //date('h:i A',strtotime($details[8])),
                        'remarks_pickupdate'   => $r_pickupdate,
                        'remarks_s_no'         => $sno,
                        'sold_to_id'           => $soldtoid,
                        'sold_to'              => $soldto,
                        'ship_to'              => $shipto,
                        'carrier'              => $carrier,
                        'date_ship'            => $dateship,
                        'port_loading'         => $portloading,
                        'port_destination'     => $portdes,
                        'description_of_goods' => $shipinstruction,
                        'case_marks'           => $casemarks,
                        'note'                 => $note,
                        'from'                 => $from,
                        'to'                   => $to,
                        'freight'              => $freight,
                        'preparedby'           => $preparedby,
                        'checkedby'            => $checkedby,
                        'grossweight_invoicing'=> $gweight,
                        'update_user'          => Auth::user()->user_id,
                        'updated_at'           => date("Y/m/d h:i:sa")
                ]);

                if (count($detailsList) > 0) {
                    $this->insertPackingDetails($detailsList, $selecteditem, 'UPD');

                    // $old = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                    //         ->where('packinglist_id',$selecteditem)
                    //         ->get();
                    $old = DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->where('packinglist_id',$selecteditem)
                            ->delete();

                    foreach ($detailsList as $key => $row) {
                        if (strlen($row[1]) == 15 || substr($row[1],0,2) == 'SJ' || substr($row[1],0,2) == 'ES') {
                            $unitprice = $row[4];
                        } else {
                            if ($this->checkBUNR($row[1]) == 'PACKAGING') {
                                $unitprice = $row[4];
                            }
                            if(strpos($this->getDropdownVal($shipinstruction),'RAW MATERIALS') !== false || strpos($this->getDropdownVal($shipinstruction),'RM') !== false) {

                                $markup = DB::connection($this->common)->table('invoicing_markup')
                                            ->where('prod_line',Auth::user()->productline)
                                            ->select('multiplier')->first();

                                $percent = $row[4]*$markup->multiplier;
                                $unitprice = $row[4]+ $percent;
                            } else {
                                $unitprice = $row[4];
                            }
                        }
                        $amt = $row[5] * $unitprice;

                        DB::connection($this->mysql)->table('ypics_invoicingdetails')
                            ->insert([
                                'packinglist_id' => $selecteditem,
                                'item_no' => $row[0], 
                                'packinglist_ctrl' => $controlno,
                                'products' => $this->getDropdownVal($shipinstruction),
                                'sold_to_id' => $soldtoid,
                                'soldto_address' => $soldto,
                                'shipto_address' => $shipto,
                                'ship_date' => $dateship,
                                'shippedfrom' => $from,
                                'shipto' => $to,
                                'carrier' => $this->getDropdownVal($carrier),
                                'gross_weight' => $gweight,
                                'terms_of_payment' => '30 DAYS FR. BILLING DATE',
                                'po_no' => $row[1],
                                'description' => $row[2],
                                'country_origin' => 'PHILIPPINES',
                                'quantity' => $row[5],
                                'unitprice' => $unitprice,
                                'amount' => $amt,
                                'pickup_date' => $this->convertDate($r_pickupdate,'Y-m-d'),
                                'invoice_date' => $this->convertDate($invoicedate,'Y-m-d'),
                                'freight' => $freight,
                                'via' => "BY AIR",
                                'note_hightlight' => $note,
                                'item_code' => $row[3],
                                'case_marks' => $casemarks,
                                'updated_at' => date("Y/m/d h:i:sa")
                            ]);
                    }
                }
                $message = "Packing List Successfully Updated.";
            }

            $output = $message;
            return $selecteditem;
        // }
        // catch (Exception $e)
        // {
        //     Log::error($e->getMessage());
        // }
        
    }

    /**
    * Insert Packing details List.
    **/
    private function insertPackingDetails($detailsList, $selecteditem, $action)
    {
        $result = 0;
        $unitprice = 0;
        try
        {
            if(count($detailsList) >= 1)
            {
                #Delete existing if action is UPDATE.
                if($action == 'UPD')
                {
                    DB::connection($this->mysql)->table('tbl_packing_list_details')->where('packing_id',$selecteditem)->delete();
                }

                foreach ($detailsList as $key => $detailsRow)
                {
                    if (strlen($detailsRow[1]) == 15 || substr($detailsRow[1],0,2) == 'SJ' || substr($detailsRow[1],0,2) == 'ES') {
                        $unitprice = $detailsRow[4];
                    } else {
                        if ($this->checkBUNR($detailsRow[1]) == 'PACKAGING') {
                            $unitprice = $detailsRow[4];
                        } else {
                            $unitprice = $detailsRow[4];
                            // $percent = number_format($detailsRow[4]*0.05,4);
                            // $unitprice = $detailsRow[4]+ $percent;
                        }
                    }

                    $result = DB::connection($this->mysql)->table('tbl_packing_list_details')
                        ->insert([
                            'packing_id'     => $selecteditem
                            , 'po'           => $detailsRow[1] #pono
                            , 'description'  => $detailsRow[2] #desc
                            , 'item_code'    => $detailsRow[3] #product code
                            , 'price'        => $unitprice #price
                            , 'box_no'       => $detailsRow[0] #boxno
                            , 'qty'          => $detailsRow[5] #qty
                            , 'gross_weight' => $detailsRow[6] #gross
                            , 'create_user'  => Auth::user()->user_id
                            , 'update_user'  => Auth::user()->user_id
                            , 'created_at'   => date("Y/m/d h:i:sa")
                            , 'updated_at'   => date("Y/m/d h:i:sa")
                        ]);
                }
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    private function getDropdownVal($id)
    {
        $dropdown = DB::connection($this->main_mysql)->table('tbl_mdropdowns')->where('id',$id)->first();

        if ($this->checkIfExistObject($dropdown) > 0) {
            return $dropdown->description;
        } else {
            return "N/A";
        }
        
    }

    /**
    * Delete Packing details List.
    **/
    public function deletePackingList(Request $request_data)
    {
        try
        {
            $selectedid = $request_data['id'];

            if ($this->checkIfDoneInvoice($selectedid)) {
                $message = "Selected Packing List Cannot be Deleted, because It already made an Invoice.";
            } else {
                DB::connection($this->mysql)->table('tbl_packing_list_details')
                    ->where('packing_id',$selectedid)
                    ->delete();
                DB::connection($this->mysql)->table('tbl_packing_list')
                    ->whereRaw("id in (". $selectedid .")")
                    ->delete();
                DB::connection($this->mysql)->table('ypics_invoicingdetails')
                        ->where('packinglist_id', $selectedid)
                        ->delete();

                $message = "Selected Packing List Successfully Deleted.";
            }

            $output = redirect(url('packinglistsystem'))
                        ->with(['message' => $message]);
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $output;
    }

    /**
    * Load Packing Details.
    **/
    public function getPackingListDetails(Request $request_data)
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PLSYSTEM'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $selecteditem = $request_data['selecteditem'];
            $dbconnection = $request_data['dbconnection'];

            if(empty($selecteditem))
            {
                $packinglist = null;
                $packingdetails = null;
            }
            else
            {
                $packinglist = $this->getPackingList(Config::get('constants.EMPTY_FILTER_VALUE')
                                                    , Config::get('constants.EMPTY_FILTER_VALUE')
                                                    , $selecteditem);
                $packingdetails = $this->getPackingDetails($selecteditem);
            }

            $soldto = DB::connection($this->common)->table('tbl_soldto')->get();

            $common = new CommonController();
            $carrier = $common->getDropdownByName('carrier');
            $portOfDestination = $common->getDropdownByName('portofdestination');
            $descOfGoods = $common->getDropdownByName('descriptionofgoods');
            $checkedby =  DB::connection($this->common)->table('tbl_packinglist_setting')->where('assign','checkedby')->where('prodline',$dbconnection)->select('user')->get();
            $preparedby =  DB::connection($this->common)->table('tbl_packinglist_setting')->where('assign','preparedby')->where('prodline',$dbconnection)->select('user')->get();

            return view('phase2.PackingListDetails',['userProgramAccess' => $userProgramAccess
                    , 'selected_customer' => 'X'
                    , 'soldto'            => $soldto
                    , 'carrier'           => $carrier
                    , 'portOfDestination' => $portOfDestination
                    , 'descOfGoods'       => $descOfGoods
                    , 'packinglist'       => $packinglist
                    , 'packingdetails'    => $packingdetails
                    , 'dbconnection'      => $dbconnection
                    , 'checkedby' => $checkedby
                    , 'preparedby' => $preparedby
                    // 'ssss' => $sss,
                    // 'wbss' => $wbs,
                    // 'qcdbs' => $qcdb,
                    // 'qcmlds' => $qcmld
                ]);
        }
    }

    /**
    * Retrieve Packing Details from DB with specified ID.
    **/
    public function getPackingDetails($packinglistid)
    {
        $result = 0;
        try
        {
            $result = DB::connection($this->mysql)->table('tbl_packing_list_details')
                ->where('packing_id', '=',$packinglistid)
                ->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
    * Retrive Orders from YPICS.
    **/
    public function getPorders(Request $request_data)
    {
        $output = '';
        $result = 0;

        $porder = $request_data['porder'];
        $dbconnection = $request_data['dbconnection'];

        switch ($dbconnection) {
            case 'CN':
                $dbconnection = 'sqlsrvcn';
                break;
            case 'TS':
                $dbconnection = 'sqlsrvbu';
                break;
            case 'YF':
                $dbconnection = 'sqlsrvyf';
                break;
            case 'PPS':
                $dbconnection = 'sqlsrvpps';
                break;
            case 'MOLD':
                $dbconnection = 'sqlsrvmold';
                break;
            default:
                $dbconnection = 'sqlsrvbu';
                break;
        }

        

        $countOutput = DB::connection($this->mssql)->table('XSLIP AS D')
                    ->leftjoin('XHIKI AS HK', 'HK.PORDER', '=', 'D.PORDER')
                    ->leftjoin('XHEAD AS H', 'H.CODE', '=', 'D.CODE')
                    ->leftjoin('XBAIK AS B', 'B.CODE', '=', 'H.CODE')
                    ->where('D.SEIBAN', 'like', $porder.'%')
                    ->groupBy('D.SEIBAN'
                            , 'H.NAME'
                            , 'D.CODE'
                            , 'B.PRICE'
                            , 'D.KVOL')
                    ->select('D.SEIBAN'
                            , 'H.NAME'
                            , 'D.CODE as CODE'
                            , 'B.PRICE AS PRICE'
                            , 'D.KVOL')
                    ->count();

            if ($countOutput > 0) {
                $output = DB::connection($this->mssql)->table('XSLIP AS D')
                    ->leftjoin('XHIKI AS HK', 'HK.PORDER', '=', 'D.PORDER')
                    ->leftjoin('XHEAD AS H', 'H.CODE', '=', 'D.CODE')
                    ->leftjoin('XBAIK AS B', 'B.CODE', '=', 'H.CODE')
                    ->where('D.SEIBAN', 'like', $porder.'%')
                    ->groupBy('D.SEIBAN'
                            , 'H.NAME'
                            , 'D.CODE'
                            , 'B.PRICE'
                            , 'D.KVOL')
                    ->select('D.SEIBAN'
                            , 'H.NAME'
                            , 'D.CODE as CODE'
                            , 'B.PRICE AS PRICE'
                            , 'D.KVOL')
                    ->get();
            } else {
                // raw mats will go here
                $output = DB::connection($this->mssql)->table('XSLIP AS D')
                    // ->leftjoin('XHIKI AS HK', 'HK.PORDER', '=', 'D.PORDER')
                    ->leftjoin('XHEAD AS H', 'H.CODE', '=', 'D.CODE')
                    ->join('XBAIK AS B', 'B.CODE', '=', 'H.CODE')
                    ->where('B.CODE', 'like', $porder.'%')
                    ->groupBy('D.CODE'
                            , 'H.NAME'
                            , 'B.PRICE')
                    ->select(DB::raw('D.CODE AS PORDER')
                            , DB::raw('H.NAME')
                            , DB::raw("'CN' as CODE")
                            , DB::raw('ISNULL(B.PRICE,0.0000) AS PRICE')
                            , DB::raw('SUM(D.KVOL) as KVOL'))
                    ->get();
            }

            if (count((array)$output) == 0) {
                $output = DB::connection($this->mssql)->table('XSLIP AS D')
                    ->leftjoin('XHEAD AS H', 'H.CODE', '=', 'D.CODE')
                    ->leftjoin('XBAIK AS B', 'B.CODE', '=', 'H.CODE')
                    ->where('B.CODE', 'like', $porder.'%')
                    ->groupBy('D.CODE'
                            , 'H.NAME'
                            , 'B.PRICE')
                    ->select(DB::raw('D.CODE AS PORDER')
                            , DB::raw('H.NAME')
                            , DB::raw("'CN' as CODE")
                            , DB::raw('ISNULL(B.PRICE,0.0000) AS PRICE')
                            , DB::raw('SUM(D.KVOL) as KVOL'))
                    ->get();
            }



            if (count((array)$output) == 0) {
                // $output = DB::connection($this->mssql)->table('XHEAD AS D')
                //     ->leftJoin('XTANK AS B', 'B.CODE', '=', 'D.CODE')
                //     ->where('D.CODE', 'like', $porder.'%')
                //     ->groupBy('D.CODE'
                //             , 'D.NAME'
                //             , 'B.SPRICE')
                //     ->select(DB::raw('D.CODE AS PORDER')
                //             , DB::raw('D.NAME')
                //             , DB::raw("'TS' as CODE")
                //             , DB::raw('ISNULL(B.SPRICE,0.0000)'))
                //     ->get();
                $output = DB::connection($this->mssql)
                            ->select("SELECT D.CODE AS PORDER,
                                            D.NAME,
                                            'CN' AS CODE,
                                            CASE
                                                when (SELECT TOP 1 B.SPRICE
                                                        FROM XTANK AS B
                                                        where B.CODE = '".$porder."') IS NOT NULL
                                                    then (SELECT TOP 1 B.SPRICE
                                                            FROM XTANK AS B
                                                            where B.CODE = '".$porder."')
                                            else ISNULL((SELECT TOP 1 B.PRICE
                                                        FROM XBAIK AS B
                                                        where B.CODE = '".$porder."'),0)
                                            END as PRICE
                                            FROM XHEAD AS D
                                            where D.CODE = '".$porder."'");
            }
            if (count((array)$output) == 0) {
                $output = DB::connection($this->mssql)->table('XRECE AS D')
                    ->leftJoin('XHEAD AS H', 'H.CODE', '=', 'D.CODE')
                    ->leftjoin('XBAIK AS B', 'B.CODE', '=', 'H.CODE')
                    ->where('D.SORDER', 'like', $porder.'%')
                    ->groupBy('D.SORDER'
                            , 'H.NAME'
                            , 'D.CODE'
                            , 'B.PRICE'
                            , 'D.KVOL')
                    ->select(DB::raw('D.SORDER AS PORDER')
                            , DB::raw('H.NAME')
                            , DB::raw('D.CODE as CODE')
                            , DB::raw("B.PRICE AS PRICE")
                            , DB::raw('D.KVOL as KVOL'))
                    ->get();
            }

        $result = 0;

        // $this->utf8_encode_deep($output);


        return $output;
    }
    
//DEAN
    private function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            //$input = utf8_encode($input);
            iconv("iso-8859-1","UTF-8",$input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                if (is_object($value)) {
                    $vals = array_keys(get_object_vars($value));

                    foreach ($vals as $val) {
                        iconv("iso-8859-1","UTF-8",$val);
                    }
                } else {
                    iconv("iso-8859-1","UTF-8",$value);
                }
                
            }

            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));

            foreach ($vars as $var) {
                iconv("iso-8859-1","UTF-8",$var);
            }
        }
    }

    /**
    * Retrieve Packing List from DB.
    **/
    private function exportPackingList($invoicedate_from, $invoicedate_to)
    {
        try
        {
            if(empty($invoicedate_from) || empty($invoicedate_to))
            {
                $packinglist = DB::connection($this->mysql)->table('tbl_packing_list as p')
                            ->join('tbl_packing_list_details as yp', 'p.id','=', 'yp.packing_id')
                            // ->groupby()
                            ->select(
                            DB::raw("p.control_no AS 'CTR #'"),
                            DB::raw("yp.po AS 'PO'"),
                            DB::raw("yp.item_code AS 'Product Code'"),
                            DB::raw("yp.description AS 'Product Name'"),
                            DB::raw("yp.qty AS 'Ship QTY'"),
                            DB::raw("DATE_FORMAT(p.date_ship, '%m/%d/%Y') AS 'Date Ship'"),
                            DB::raw("DATE_FORMAT(p.invoice_date, '%m/%d/%Y') as 'Invoice Date'"),
                            DB::raw("p.remarks_s_no AS 'Remarks'"),
                            DB::raw("p.sold_to AS 'Sold To'"),
                            DB::raw("p.ship_to AS 'Ship To'"),
                            DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = carrier) as Carrier"),
                            DB::raw("p.port_loading AS 'Port of Loading'"),
                            DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = port_destination) AS 'Port of Destination'"),
                            DB::raw("CONCAT(`from`, '\r\n' ,`to`, '\r\n',p.freight) AS 'Shipping Instruction'"),
                            DB::raw("p.case_marks AS 'Case Marks'"),
                            'p.note AS Note')
                            ->get();
            }
            else
            {

                $packinglist = DB::connection($this->mysql)->table('tbl_packing_list as p')
                            ->join('tbl_packing_list_details as yp', 'p.id','=', 'yp.packing_id')
                            ->select(
                            DB::raw("p.control_no AS 'CTR #'"),
                            DB::raw("yp.po AS 'PO'"),
                            DB::raw("yp.item_code AS 'Product Code'"),
                            DB::raw("yp.description AS 'Product Name'"),
                            DB::raw("yp.qty AS 'Ship QTY'"),
                            DB::raw("DATE_FORMAT(p.date_ship, '%m/%d/%Y') AS 'Date Ship'"),
                            DB::raw("DATE_FORMAT(p.invoice_date, '%m/%d/%Y') as 'Invoice Date'"),
                            DB::raw("p.remarks_s_no AS 'Remarks'"),
                            DB::raw("p.sold_to AS 'Sold To'"),
                            DB::raw("p.ship_to AS 'Ship To'"),
                            DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = carrier) as Carrier"),
                            DB::raw("p.port_loading AS 'Port of Loading'"),
                            DB::raw("(SELECT description from pmi_common.tbl_mdropdowns where id = port_destination) AS 'Port of Destination'"),
                            DB::raw("CONCAT(`from`, '\r\n' ,`to`, '\r\n',p.freight) AS 'Shipping Instruction'"),
                            DB::raw("p.case_marks AS 'Case Marks'"),
                            'p.note AS Note')
                            ->whereRaw("invoice_date BETWEEN '" . $invoicedate_from
                                    ."' AND '" . $invoicedate_to ."'")
                            ->get();
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $packinglist;
    }

    /**
    * Check if YPICS Invoicing were set.
    **/
    public function checkIfDoneInvoice($id)
    {
        $count = DB::connection($this->mysql)->table('tbl_packing_list')
                    ->where('id',$id)
                    ->where('invoicing_status','Complete')
                    ->count();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Export Packing List to Excel.
    **/
    public function exportListToXls(Request $request_data)
    {

        $request_data['porder'];
        # get the selected supplier and db connection.
        $data = array();

        if(empty($request_data['from']) || empty($request_data['to']))
        {
            $invoicedate_from = '';
            $invoicedate_to   = '';
        }
        else
        {
            $invoicedate_from = date('Y-m-d',strtotime($request_data['from']));
            $invoicedate_to   = date('Y-m-d',strtotime($request_data['to']));
        }


        # retrieve data
        $result = $this->exportPackingList($invoicedate_from, $invoicedate_to);

        # convert the object result to array readable format.
        foreach ($result as $datareport)
        {
            $data[] = (array)$datareport;
            #or first convert it and then change its properties using
            #an array syntax, it's up to you
        }

        # Create and export excel by feeding the array result.
        Excel::create('Packing List', function($excel) use($data)
        {

            $excel->sheet('Packing List', function($sheet) use($data)
            {
                $sheet->fromArray($data);
            });

        })->export('xls');
    }

    /**
    * Export Packing List to PDF.
    **/
    public function exportListToPdf(Request $request_data)
    {
        try
        {
            if(empty($request_data['from']) || empty($request_data['to']))
            {
                $invoicedate_from = '';
                $invoicedate_to   = '';
            }
            else
            {
                $invoicedate_from = date('m/d/Y',strtotime($request_data['from']));
                $invoicedate_to   = date('m/d/Y',strtotime($request_data['to']));
            }

            # retrieve data
            $result = $this->getPackingList($invoicedate_from, $invoicedate_to);

            $html1 = '<p><h3>PACKING LIST SYSTEM</h3></p>
                        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 10px;">
                        <thead>
                            <tr>
                                <td>CTR #</td>
                                <td>Invoice Date</td>
                                <td>Remarks</td>
                                <td>Sold To</td>
                                <td>Ship To</td>
                                <td>Carrier</td>
                                <td>Date Ship</td>
                                <td>Port of Loading</td>
                                <td>Port of Destination</td>
                                <td>Shipping Instruction</td>
                                <td>Case Marks</td>
                                <td>Note</td>
                            </tr>
                        </thead>
                        <tbody>';

                        $html2 = '';
                        foreach ($result as $key => $packingdata)
                        {
                            $packingdata = get_object_vars($packingdata);
                            $html2 = $html2 . '<tr>
                                <td>'. $packingdata['control_no'] . '</td>
                                <td>'. $packingdata['invoice_date'] . '</td>
                                <td>'. $packingdata['remarks_time'] . ' <br/> '. $packingdata['remarks_pickupdate'] . ' <br/> '.$packingdata['remarks_s_no'] . '</td>
                                <td>'. $packingdata['sold_to'] . '</td>
                                <td>'. $packingdata['ship_to'] . '</td>
                                <td>'. $packingdata['carrier_name'] . '</td>
                                <td>'. $packingdata['date_ship'] . '</td>
                                <td>'. $packingdata['port_loading'] . '</td>
                                <td>'. $packingdata['port_destination_name'] . '</td>
                                <td>'. $packingdata['from'] . ' <br/> '. $packingdata['to'] . ' <br/> '.$packingdata['freight'] .  '</td>
                                <td>'. $packingdata['case_marks'] . '</td>
                                <td>'. $packingdata['note'] . '</td>
                            </tr>';
                        }

            $html3 = '</tbody>
                    </table>';

            # gather all html parts.
            $html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . $html1 . $html2 . $html3;

        $dompdf = new Dompdf();
        $dompdf->loadHTML($html);
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        return $dompdf->stream('Packing List'.Carbon::now().'.pdf');

            //$pdf = PDF2::loadHTML($html)->setPaper('letter', 'landscape');
            //return $pdf->stream('Packing List'.Carbon::now().'.pdf');

            // # apply snappy pdf wrapper
            // $pdf = App::make('snappy.pdf.wrapper');
            // # transform html to pdf format.
            // $pdf->loadHTML($html)->setPaper('A4')->setOrientation('landscape');
            // # display PDF report to response.
            // return $pdf->inline();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
    }

    /**
    * Format Packing Details List Data.
    **/
    private function createrows($list, $index)
    {
        $result = '';
        $ctr = 0;

        if(count($list) > 0)
        {
            foreach ($list as $key => $value)
            {
                $value = get_object_vars($value);
                if(empty($result))
                {
                    $result = $value[$index] . '<br/>';
                }
                else
                {
                    $result = $result . $value[$index] . '<br/>';
                }
                if($ctr == Config::get('constants.PLSYSTEM_PRINTLIMIT')-1)
                {
                    break;
                }
                $ctr++;
            }
        }
        return $result;
    }

    /**
    * Print Packing Details to PDF.
    **/
    public function exportListToPdfPrint(Request $request_data)
    {
        try
        {
            $id = $request_data['id'];

            # retrieve data
            $result = $this->getPackingList(Config::get('constants.EMPTY_FILTER_VALUE')
                , Config::get('constants.EMPTY_FILTER_VALUE'), $id);
            $resultdetails = $this->getPackingDetails($id);
            $total = DB::connection($this->mysql)->table('tbl_packing_list_details')
                    ->select(DB::raw("SUM(qty) as total"))
                    ->where('packing_id', $id)
                    ->groupBy('packing_id')
                    ->get();


            if(count($total) > 0)
            {
                $total = $total[0]->total;
            }
            else
            {
                $total = 0;
            }

            $col_box   = $this->createrows($resultdetails, 'box_no');
            $col_po    = $this->createrows($resultdetails, 'po');
            $col_desc  = $this->createrows($resultdetails, 'description');
            $col_qty   = $this->createrows($resultdetails, 'qty');
            $col_gross = $this->createrows($resultdetails, 'gross_weight');
            $col_code = $this->createrows($resultdetails, 'item_code');
            /*
            $preparedBy= Config::get('constants.PLSYSTEM_PREPAREDBY');
            $checkedBy = Config::get('constants.PLSYSTEM_CHECKEDBY');
            $copy      = Config::get('constants.PLSYSTEM_COPY');*/

            $common = new CommonController();
            //$copy = $common->getPackingListSettingsByName('checkedbysection');

            $whs = "Whse Jr/Sr Supvr/Manager";
            $copy = "Traffic/Prod'n/QC";

            $marginT = $request_data['top'];
            $marginR = $request_data['right'];
            $marginB = $request_data['bottom'];
            $marginL = $request_data['left'];

            $control_no = (isset($result[0]->control_no)) ? $result[0]->control_no : "";
            $invoice_date = (isset($result[0]->invoice_date)) ? $result[0]->invoice_date : "";
            $remarks_time = (isset($result[0]->remarks_time)) ? $result[0]->remarks_time : "";
            $remarks_pickupdate = (isset($result[0]->remarks_pickupdate)) ? $result[0]->remarks_pickupdate : "";
            $remarks_s_no = (isset($result[0]->remarks_s_no)) ? $result[0]->remarks_s_no : "";
            $sold_to = (isset($result[0]->sold_to)) ? $result[0]->sold_to : "";
            $ship_to = (isset($result[0]->ship_to)) ? $result[0]->ship_to : "";
            $carrier_name = (isset($result[0]->carrier_name)) ? $result[0]->carrier_name : "";
            $port_loading = (isset($result[0]->port_loading)) ? $result[0]->port_loading : "";
            $date_ship = (isset($result[0]->date_ship)) ? $result[0]->date_ship : "";
            $port_destination_name = (isset($result[0]->port_destination_name)) ? $result[0]->port_destination_name : "";
            $from = (isset($result[0]->from)) ? $result[0]->from : "";
            $to = (isset($result[0]->to)) ? $result[0]->to : "";
            $freight = (isset($result[0]->freight)) ? $result[0]->freight : "";
            $description_of_goods_name = (isset($result[0]->description_of_goods_name)) ? $result[0]->description_of_goods_name : "";
            $case_marks = (isset($result[0]->case_marks)) ? $result[0]->case_marks : "";
            $note = (isset($result[0]->note)) ? $result[0]->note : "";
            $preparedBy = (isset($result[0]->preparedby)) ? $result[0]->preparedby : "";
            $checkedBy = (isset($result[0]->checkedby)) ? $result[0]->checkedby : "";
            $grsweight = (isset($result[0]->grossweight_invoicing)) ? $result[0]->grossweight_invoicing : "";

            
            $data = [
                'whs' => $whs,
                'copy' => $copy,
                'marginT' => $marginT,
                'marginR' => $marginR,
                'marginB' => $marginB,
                'marginL' => $marginL,
                'control_no' => $control_no,
                'invoice_date' => $invoice_date,
                'remarks_time' => $remarks_time,
                'remarks_pickupdate' => $remarks_pickupdate,
                'remarks_s_no' => $remarks_s_no,
                'sold_to' => $sold_to,
                'ship_to' => $ship_to,
                'carrier_name' => $carrier_name,
                'port_loading' => $port_loading,
                'date_ship' => $date_ship,
                'port_destination_name' => $port_destination_name,
                'from' => $from,
                'to' => $to,
                'freight' => $freight,
                'description_of_goods_name' => $description_of_goods_name,
                'case_marks' => $case_marks,
                'note' => $note,
                'preparedBy' => $preparedBy,
                'checkedBy' => $checkedBy,
                'grsweight' => $grsweight,
                'id' => $id,
                'result' => $result,
                'resultdetails' => $resultdetails,
                'total' => $total,
                'col_box' => $col_box,
                'col_po' => $col_po,
                'col_desc' => $col_desc,
                'col_qty' => $col_qty,
                'col_gross' => $col_gross,
                'col_code' => $col_code,
            ];
            
            $pdf = PDF::loadView('pdf.packinglist', $data)
                        ->setPaper('A4')
                        ->setOrientation('portrait')
                        ->setOption('margin-top', $marginT)
                        ->setOption('margin-right', $marginR)
                        ->setOption('margin-bottom', $marginB)
                        ->setOption('margin-left', $marginL);
            return $pdf->inline('PakingList_'.$control_no.'.pdf');
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
    }

    private function checkIfExistObject($object)
    {
       return count( (array)$object);
    }

    private function convertDate($date,$format)
    {
        $time = strtotime($date);
        $newdate = date($format,$time);
        return $newdate;
    }

    private function checkBUNR($code)
    {
        $db = DB::connection($this->mssql)->table('XITEM')
                ->select('BUNR')
                ->where('CODE',$code)
                ->first();
        return $db->BUNR;
    }
}
