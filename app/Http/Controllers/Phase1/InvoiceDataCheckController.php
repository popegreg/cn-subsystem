<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: InvoiceDataCheckController.php
     MODULE NAME:  [3005] Invoice Data Check
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2016.05.03
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.03     AK.DELAROSA     Initial Draft
     100-00-02   1     2016.06.09     MESPINOSA       Fix for Issue #027,#028,#029.
     100-00-02   2     2016.10.27     AK.DELAROSA     Fix query
*******************************************************************************/
?>
<?php
namespace App\Http\Controllers\Phase1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Config;
use DB;
use Excel;
use PDF;

class InvoiceDataCheckController extends Controller
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

    public function getInvoiceDataCheck()
    {
        $common = new CommonController;

        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_INVOICE'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('phase1.InvoiceDataCheck',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function postReadfile(Request $req)
    {
        $file = $req->file('importedData');

        # get file contents
        $row = explode(PHP_EOL, file_get_contents($file));
        $keys = array_keys($row);
        $origqty = 0;
        $dataqty = 0;
        $data = [];
        $invoice_data = '';
        $variance = [];

        # pinching the txt file by pieces
        # retrieve data by row, bypass the header.
        
        $this->truncateTable('invoice_data_check');
        $this->truncateTable('invoice_data_check_variance');
        $this->truncateTable('invoice_data_check_nonvariance');

        for ($i=1; $i < count($keys)-1; $i++) 
        {
            $q = [];
            $key = $keys[$i];
            $content = $row[$key];
            $txtdata = array_filter(array_map("trim", explode("\t", $content)));
            
            # check if the row contains columns.
            if (is_array($txtdata)) 
            {
                if(count($txtdata) == 7)
                {
                    # assign values accordingly.
                    $invoice  = trim($txtdata[0],'"');
                    $fDate    = trim($txtdata[1],'"');
                    $itemCode = trim($txtdata[2],'"');
                    $itemName = mb_convert_encoding(trim($txtdata[3],'"'),'UTF-8','SJIS');
                    $qty      = trim($txtdata[4],'"');
                    $pr       = substr(trim($txtdata[5],'"'),0,stripos(trim($txtdata[5],'"'), '-'));
                    $price    = trim($txtdata[6],'"');

                    $origqty = $origqty + $qty;
                    $invoice_data = $invoice;

                    $this->InsertToInvoice($invoice,$fDate,$itemCode,$itemName,$qty,$pr,$price);
                }
            }
        }

        $bal = 0;
        $overdelivery = 0;
        $neworderqty = 0;
        $overamount = 0;

        $this->getYpicsData();

        /*foreach ($txt_inv_data as $key => $txt_inv) {
            $dataqty = $dataqty + $this->getDataQty($txt_inv->pr);

            $prdata = $this->query('select distinct invoice,fdate,itemcode,itemname,pr,price,sum(qty) as qty from invoice_data_check where pr = :pr group by invoice, pr, itemcode, itemname, fdate, price',[':pr' => $txt_inv->pr]);

            if ($this->checkPRifExist($prdata[0]->pr)) {
                $invdata = $this->getTPICSdata($prdata[0]->pr);
                $bal = $invdata->qty - $invdata->actual;

                // no negative
                // if ($prdata[0]->qty > $bal) {
                    $overdelivery = $prdata[0]->qty - $bal;
                // } else {
                    //$overdelivery = $bal - $prdata[0]->qty;
                // }
                
                $neworderqty = $invdata->qty + $overdelivery;
                $overamount = $prdata[0]->price * $neworderqty;

                if ($overdelivery != 0 || $overdelivery != '0') {
                    $this->InsertToVariance($prdata[0]->invoice,$prdata[0]->fdate,$prdata[0]->pr,$prdata[0]->itemcode,$prdata[0]->itemname,$prdata[0]->price,$invdata->qty,$bal,$prdata[0]->qty,$overdelivery,$neworderqty,$overamount);
                } else {
                    $this->InsertToNonVariance($prdata[0]->invoice,$prdata[0]->fdate,$prdata[0]->pr,$prdata[0]->itemcode,$prdata[0]->itemname,$prdata[0]->price,$invdata->qty,$bal,$prdata[0]->qty,$overdelivery,$neworderqty,$overamount);
                }
            } else {
                if ($overdelivery != 0 || $overdelivery != '0') {
                    $this->InsertToVariance($prdata[0]->invoice,$prdata[0]->fdate,$prdata[0]->pr,$prdata[0]->itemcode,$prdata[0]->itemname,$prdata[0]->price,$invdata->qty,$bal,$prdata[0]->qty,$overdelivery,$neworderqty,$overamount);
                } else {
                    $this->InsertToNonVariance($prdata[0]->invoice,$prdata[0]->fdate,$prdata[0]->pr,$prdata[0]->itemcode,$prdata[0]->itemname,$prdata[0]->price,$invdata->qty,$bal,$prdata[0]->qty,$overdelivery,$neworderqty,$overamount);
                }
            }
        }*/

        // $variance = $this->query('select * from invoice_data_check_variance');

        $variance = DB::connection($this->mysql)->select("
                SELECT i.invoice AS invoiceno,
                    i.fdate AS fdate,
                    s.orderno AS pr, 
                    i.itemcode AS `code`, 
                    i.itemname AS partname, 
                    i.price AS unitprice,  
                    s.schdqty AS orderqty, 
                    s.AvailQty AS orderbal, 
                    SUM(i.qty) AS deliveredqty, 
                    ((SUM(i.qty)) - (s.AvailQty)) AS overdelivery,  
                    s.schdqty +((SUM(i.qty)) -(s.AvailQty)) AS neworderqty,  
                    i.price*((SUM(i.qty)) -(s.AvailQty)) AS overamount
                    FROM invoice_data_check i
                    LEFT JOIN invoice_data_check_xslip s ON i.PR = s.orderno
                    GROUP BY i.invoice, 
                        i.fdate,
                        s.orderno, 
                        i.itemcode, 
                        i.itemname, 
                        i.price,  
                        s.schdqty,
                        s.availqty
                    HAVING  ((SUM(i.qty)) - (s.AvailQty)) > 0
            ");
        // $nonvariance = $this->query('select * from invoice_data_check_nonvariance');
        $nonvariance = DB::connection($this->mysql)->select("
                SELECT i.invoice AS invoiceno,
                    i.fdate AS fdate,
                    s.orderno AS pr, 
                    i.itemcode AS `code`, 
                    i.itemname AS partname, 
                    i.price AS unitprice,  
                    s.schdqty AS orderqty, 
                    s.AvailQty AS orderbal, 
                    SUM(i.qty) AS deliveredqty, 
                    ((SUM(i.qty)) - (s.AvailQty)) AS overdelivery,  
                    s.schdqty +((SUM(i.qty)) -(s.AvailQty)) AS neworderqty,  
                    i.price*((SUM(i.qty)) -(s.AvailQty)) AS overamount
                    FROM invoice_data_check i
                    LEFT JOIN invoice_data_check_xslip s ON i.PR = s.orderno
                    GROUP BY i.invoice, 
                        i.fdate,
                        s.orderno, 
                        i.itemcode, 
                        i.itemname, 
                        i.price,  
                        s.schdqty,
                        s.availqty
                    HAVING  ((SUM(i.qty)) - (s.AvailQty)) = 0
            ");

        $costvariance = DB::connection($this->mysql)->select("
                SELECT i.PR, 
                    i.Invoice AS InvoiceNo, 
                    i.FDate AS FltDate, 
                    t.CODE AS `CODE`, 
                    i.itemname AS `NAME`, 
                    SUM(i.Qty) AS ReceiveQty, 
                    i.Price AS YEC_PRICE, 
                    t.PRICE AS PMI_PRICE, 
                    t.Price-i.Price AS Difference
                FROM invoice_data_check i 
                LEFT JOIN invoice_data_check_xtank t ON i.itemcode = t.CODE
                GROUP BY i.PR, 
                    i.Invoice, 
                    i.FDate, 
                    t.CODE, 
                    i.itemname, 
                    i.Price, 
                    t.PRICE, 
                    t.Price-i.Price, 
                    i.PR
                    HAVING (((t.Price-i.Price)<>0) AND (NOT (i.PR) IS NULL))
                ");


        return $data = [
                'origqty' => $origqty,
                'dataqty' => $dataqty,
                'invoice' => $invoice_data,
                'variance' => $variance,
                'nonvariance' => $nonvariance,
                'costvariance' => $costvariance
            ];
    }

    private function InsertToInvoice($invoice,$fdate,$itemcode,$itemname,$qty,$pr,$price)
    {
        DB::connection($this->mysql)->table('invoice_data_check')->insert([
            'invoice' => $invoice,
            'fdate' => $fdate,
            'itemcode' => $itemcode,
            'itemname' => $itemname,
            'qty' => $qty,
            'pr' => $pr,
            'price' => $price
        ]);
    }

/*    private function InsertToVariance($invoiceno,$fdate,$pr,$code,$partname,$unitprice,$orderqty,$orderbal,$deliveredqty,$overdelivery,$neworderqty,$overamount)
    {
        DB::connection($this->mysql)->table('invoice_data_check_variance')->insert([
            'invoiceno' => $invoiceno,
            'fdate' => $fdate,
            'pr' => $pr,
            'code' => $code,
            'partname' => $partname,
            'unitprice' => $unitprice,
            'orderqty' => $orderqty,
            'orderbal' => $orderbal,
            'deliveredqty' => $deliveredqty,
            'overdelivery' => $overdelivery,
            'neworderqty' => $neworderqty,
            'overamount' => $overamount
        ]);
    }

    private function InsertToNonVariance($invoiceno,$fdate,$pr,$code,$partname,$unitprice,$orderqty,$orderbal,$deliveredqty,$overdelivery,$neworderqty,$overamount)
    {
        DB::connection($this->mysql)->table('invoice_data_check_nonvariance')->insert([
            'invoiceno' => $invoiceno,
            'fdate' => $fdate,
            'pr' => $pr,
            'code' => $code,
            'partname' => $partname,
            'unitprice' => $unitprice,
            'orderqty' => $orderqty,
            'orderbal' => $orderbal,
            'deliveredqty' => $deliveredqty,
            'overdelivery' => $overdelivery,
            'neworderqty' => $neworderqty,
            'overamount' => $overamount
        ]);
    }
    */

    private function truncateTable($table)
    {
        return DB::connection($this->mysql)->table($table)->truncate();
    }


    private function query($query,$param = array())
    {
        if (count($param) > 0) {
            return DB::connection($this->mysql)->select($query,$param);
        } else {
            return DB::connection($this->mysql)->select($query);
        }
    }

    private function getTPICSdata($pr)
    {
        if ($this->checkPRifExist($pr) > 0) {
            $data = DB::connection($this->_con)->table('XSLIP')
                        ->select(DB::raw("SUM(KVOL) as qty"),
                                DB::raw("SUM(TJITU) as actual"))
                        ->where('PORDER', $pr)
                        ->get();
            return $data[0];
        }
    }

    private function getDataQty($pr)
    {
        if ($this->checkPRifExist($pr) > 0) {
            $data = DB::connection($this->_con)->table('XSLIP')
                        ->select(DB::raw("SUM(KVOL) as qty"))
                        ->where('PORDER', $pr)
                        ->get();
            return $data[0]->qty;
        } else {
            return 0;
        }
    }

    private function getDataPrice($pr)
    {
        if ($this->checkPRifExist($pr) > 0) {
            $data = DB::connection($this->_con)->table('XSLIP')
                        ->select(DB::raw("SUM(KVOL) as qty"),
                                'PRICE as price')
                        ->where('PORDER', $pr)
                        ->groupBy('PRICE')
                        ->get();
            return $data[0]->price;
        } else {
            return 0;
        }
    }

    private function checkPRifExist($pr)
    {
        return DB::connection($this->_con)->table('XSLIP')
                ->where('PORDER', $pr)
                ->count();
    }

    public function varianceExcel()
    {
        try 
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            
            Excel::create('InvoiceVariance_'.$date, function($excel) 
            {
                $excel->sheet('Sheet1', function($sheet) 
                {
                    $sheet->cell('A1', "InvoiceNo");
                    $sheet->cell('B1', "FltDate");
                    $sheet->cell('C1', "PR");
                    $sheet->cell('D1', "Code");
                    $sheet->cell('E1', "PartName");
                    $sheet->cell('F1', "UnitPrice");
                    $sheet->cell('G1', "OrderQty");
                    $sheet->cell('H1', "OrderBal");
                    $sheet->cell('I1', "DeliveredQty");
                    $sheet->cell('J1', "OverDelivery");
                    $sheet->cell('K1', "NewOrderQty");
                    $sheet->cell('L1', "OverAmount");

                    $row = 2;
                    /*$data = DB::connection($this->mysql)->table('invoice_data_check_variance')
                                ->get();*/
                    $data = DB::connection($this->mysql)->select("
                            SELECT i.invoice AS invoiceno,
                                i.fdate AS fdate,
                                s.orderno AS pr, 
                                i.itemcode AS `code`, 
                                i.itemname AS partname, 
                                i.price AS unitprice,  
                                s.schdqty AS orderqty, 
                                s.AvailQty AS orderbal, 
                                SUM(i.qty) AS deliveredqty, 
                                ((SUM(i.qty)) - (s.AvailQty)) AS overdelivery,  
                                s.schdqty +((SUM(i.qty)) -(s.AvailQty)) AS neworderqty,  
                                i.price*((SUM(i.qty)) -(s.AvailQty)) AS overamount
                                FROM invoice_data_check i
                                LEFT JOIN invoice_data_check_xslip s ON i.PR = s.orderno
                                GROUP BY i.invoice, 
                                    i.fdate,
                                    s.orderno, 
                                    i.itemcode, 
                                    i.itemname, 
                                    i.price,  
                                    s.schdqty,
                                    s.availqty
                                HAVING  ((SUM(i.qty)) - (s.AvailQty)) > 0
                        ");

                    foreach ($data as $key => $inv) {
                        $sheet->cell('A'.$row, $inv->invoiceno);
                        $sheet->cell('B'.$row, $inv->fdate);
                        $sheet->cell('C'.$row, $inv->pr);
                        $sheet->cell('D'.$row, $inv->code);
                        $sheet->cell('E'.$row, $inv->partname);
                        $sheet->cell('F'.$row, "'".$inv->unitprice);
                        $sheet->cell('G'.$row, ($inv->orderqty == 0)? '0.0':$inv->orderqty);
                        $sheet->cell('H'.$row, ($inv->orderbal == 0)? '0.0':$inv->orderbal);
                        $sheet->cell('I'.$row, ($inv->deliveredqty == 0)? '0.0':$inv->deliveredqty);
                        $sheet->cell('J'.$row, ($inv->overdelivery == 0)? '0.0':$inv->overdelivery);
                        $sheet->cell('K'.$row, ($inv->neworderqty == 0)? '0.0':$inv->neworderqty);
                        $sheet->cell('L'.$row, ($inv->overamount == 0)? '0.0':$inv->overamount);
                        $row++;
                    }
                });

            })->download('xls');

        } 
        catch (Exception $e) 
        {
            return redirect(url('/invoicedatacheck'))->with(['err_message' => $e]);
        }
    }

    public function nonVarianceCSV(Request $req)
    {
        try 
        {
           $this->prepareYpicsData();
           $this->preparetcalc();

            $dataqty = DB::connection($this->mysql)->select("
                SELECT sum(JITU) AS JITU FROM invoice_data_check_xsact");
            $origqty = DB::connection($this->mysql)->select("
                SELECT sum(qty) AS qty FROM invoice_data_check");
            $invoice = DB::connection($this->mysql)->select("
                SELECT invoice FROM invoice_data_check Limit 1");

            $data = [
                    'origqty' => $origqty[0]->qty,
                    'dataqty' => $dataqty[0]->JITU,
                    'invoice' => $invoice[0]->invoice
                ];

            return json_encode($data);

        } 
        catch (Exception $e) 
        {
            \Log::info($e);
            return redirect(url('/invoicedatacheck'))->with(['err_message' => $e]);
        }
    }

    public function nonVarianceExcel()
    {
        try 
        {

            $dt = Carbon::now();
            $date = $dt->format('mdY');
            $invce = DB::connection($this->mysql)->table('invoice_data_check_tcalc')
                    ->select('invoiceno')
                    ->first();
            
            Excel::create('TXSLIPJITU', function($excel) 
            {
                $excel->sheet('Sheet1', function($sheet) 
                {
                    $sheet->cell('A1', "PORDER");
                    $sheet->cell('B1', "AKUBU");
                    $sheet->cell('C1', "JITU");
                    $sheet->cell('D1', "FDATE");
                    $sheet->cell('E1', "FTIME");
                    $sheet->cell('F1', "APRICE");
                    $sheet->cell('G1', "HOKAN");
                    $sheet->cell('H1', "INVOICE_NUM");

                    $row = 2;
                    
                    // $data = DB::connection($this->mysql)->table('invoice_data_check_nonvariance')->get();
                    $data = DB::connection($this->mysql)->select("
                        SELECT * FROM invoice_data_check_xsact");

                    foreach ($data as $key => $inv) {
                        $sheet->cell('A'.$row, $inv->porder);
                        $sheet->cell('B'.$row, $inv->akubu);
                        $sheet->cell('C'.$row, $inv->jitu);
                        $sheet->cell('D'.$row, $this->formatDate($inv->fdate,'Ymd').'1');
                        $sheet->cell('E'.$row, $this->formatDate($inv->fdate,'H:i'));
                        $sheet->cell('F'.$row, ($inv->aprice == 0) ? "0.00" : $inv->aprice);
                        $sheet->cell('G'.$row, $inv->hokan);
                        $sheet->cell('H'.$row, $inv->invoice_num);
                        $row++;
                    }
                });

            })->download('xls');
        } 
        catch (Exception $e) 
        {
            return redirect(url('/invoicedatacheck'))->with(['err_message' => $e]);
        }
    }

    private function getHokan($pr)
    {
        $data = DB::connection($this->_con)->table('XSLIP')
                    ->where('PORDER', $pr)
                    ->select('HOKAN')
                    ->first();
        if ($this->checkIfExistObject($data) > 0) {
            return $data->HOKAN;
        }
        
    }

    private function checkIfExistObject($object)
    {
       return count( (array)$object);
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

    public function OverDeliveryPdf()
    {
        $content = "";
        $info = DB::connection($this->mysql)->table('invoice_data_check')->select('invoice','fdate')->first();
        
        /*$overdelivery = DB::connection($this->mysql)->table('invoice_data_check_variance')->get();*/
        $overdelivery = DB::connection($this->mysql)->select("
                    SELECT s.orderno AS pr, 
                        i.itemcode AS `code`, 
                        i.itemname AS `partname`, 
                        i.price AS unitprice,  
                        s.schdqty AS orderqty, 
                        s.AvailQty AS orderbal, 
                        SUM(i.qty) AS deliveredqty, 
                        ((SUM(i.qty)) - (s.AvailQty)) AS overdelivery,  
                        s.schdqty +((SUM(i.qty)) -(s.AvailQty)) AS neworderqty,  
                        i.price*((SUM(i.qty)) -(s.AvailQty)) AS overamount
                    FROM invoice_data_check i
                    LEFT JOIN invoice_data_check_xslip s ON i.PR = s.orderno
                    GROUP BY s.orderno, i.itemcode, i.itemname, i.price,  s.schdqty
                    HAVING  ((SUM(i.qty)) - (s.AvailQty)) > 0            
            ");

        $total = 0;
        foreach ($overdelivery as $key => $od) {
            $total = $total + $od->overamount;
            if ($od->overdelivery > 0) {
                $content .= '<tr>
                                <td class="tg-yw4l">'.$od->pr.'</td>
                                <td class="tg-yw4l">'.$od->code.'</td>
                                <td class="tg-yw4l">'.$od->partname.'</td>
                                <td class="tg-yw4l">'.$od->unitprice.'</td>
                                <td class="tg-yw4l">'.$od->orderqty.'</td>
                                <td class="tg-yw4l">'.$od->orderbal.'</td>
                                <td class="tg-yw4l">'.$od->deliveredqty.'</td>
                                <td class="tg-yw4l">'.$od->overdelivery.'</td>
                                <td class="tg-yw4l">'.$od->neworderqty.'</td>
                                <td class="tg-yw4l">'.$od->overamount.'</td>
                                <td class="tg-yw4l"><div class="box"></div></td>
                            </tr>';
            }
        }

        $grandtotal = '<div class="total">OVER DELIVERY AMOUNT: '.$total.'</div>';

        $header = '<!DOCTYPE html>
                    <div style="width:100%;border-top: solid 2px #1f71df; border-bottom: solid 2px #1f71df;padding:0px;marin-top:5px;margin-bottom:10px;">
                        <span style="font-family:Arial, sans-serif;padding:0px;margin:0px;color:#1f71df;margin-right:55%;"><strong>OVER DELIVERY REPORT</strong></span>
                        <span style="font-family:Arial, sans-serif;padding:0px;margin:0px;color:#1f71df">PRICON MICROELECTRONICS, INC.</span>
                    </div>';

        $html = '<style type="text/css">
                    .tg  {border-collapse:collapse;border-spacing:0;border-color:#999;margin:0px auto;margin-bottom:25px;}
                    .tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;color:#444;background-color:#F7FDFA;border-top-width:1px;border-bottom-width:1px;}
                    .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;color:#fff;background-color:#26ADE4;border-top-width:1px;border-bottom-width:1px;}
                    .tg .tg-baqh{text-align:center;vertical-align:top}
                    .tg .tg-9hbo{font-weight:bold;vertical-align:top}
                    .tg .tg-yw4l{vertical-align:top}
                    @media screen and (max-width: 767px) {.tg {width: auto !important;}.tg col {width: auto !important;}.tg-wrap {overflow-x: auto;-webkit-overflow-scrolling: touch;margin: auto 0px;}}
                    .info {width:100%;border-style:solid;border-width:1px;border-collapse:collapse;border-spacing:0;border-color:#999;font-family:Arial, sans-serif;padding:10px 5px;font-weight:bold;font-size:16px;margin-bottom:10px;}
                    .box {width:10px;height:10px;border-style:solid;border-width:1px;border-collapse:collapse;border-spacing:0;}
                    .total {width:300px;float:right;border:solid 2px #999;font-family:Arial, sans-serif;padding:10px 5px;font-weight:bold;font-size:16px;}
                </style>

                '.$header.'

                <table class="info">
                    <tr>
                        <td width="80%">Flight Date: '.$info->fdate.'</td>
                        <td>Invoice: '.$info->invoice.'</td>
                    </tr>
                </table>
                <div class="tg-wrap">
                    <table class="tg">
                            <tr>
                                <th class="tg-9hbo">Material_Code</th>
                                <th class="tg-9hbo">Order No<br></th>
                                <th class="tg-9hbo">Material_Name<br></th>
                                <th class="tg-9hbo">UnitPrice</th>
                                <th class="tg-9hbo">PRQty</th>
                                <th class="tg-9hbo">PRBal</th>
                                <th class="tg-9hbo">ReceivedQty</th>
                                <th class="tg-9hbo">OverDelivery</th>
                                <th class="tg-9hbo">NewPRQty</th>
                                <th class="tg-9hbo">Over Amount<br></th>
                                <th class="tg-baqh"></th>
                            </tr>
                        '.$content.'
                    </table>

                    <div style="width:100%">'.$grandtotal.'</div>
                </div>';

        return PDF::loadHTML($html)
                    ->setPaper('A4')
                    ->setOrientation('landscape')
                    ->setOption('margin-top', 10)
                    ->setOption('margin-left', 2)
                    ->setOption('margin-right', 2)
                    // ->setOption('header-html', $header)
                    ->inline('OverDelivery_'.Carbon::now().'.pdf');
    }

    public function UnitCostExcel()
    {
        try 
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            
            Excel::create('UnitCost_Difference_'.$date, function($excel) 
            {
                $excel->sheet('Sheet1', function($sheet) 
                {
                    $sheet->cell('A1', "PR");
                    $sheet->cell('B1', "InvoiceNo");
                    $sheet->cell('C1', "FltDate");
                    $sheet->cell('D1', "CODE");
                    $sheet->cell('E1', "NAME");
                    $sheet->cell('F1', "ReceiveQty");
                    $sheet->cell('G1', "YEC_PRICE");
                    $sheet->cell('H1', "PMI_PRICE");
                    $sheet->cell('I1', "Difference");

                    $row = 2;
                    /*$data = DB::connection($this->mysql)->table('invoice_data_check_variance')
                                ->get();*/
                    $data = DB::connection($this->mysql)->select("
                                SELECT i.PR, 
                                    i.Invoice AS InvoiceNo, 
                                    i.FDate AS FltDate, 
                                    t.CODE AS `CODE`, 
                                    i.itemname AS `NAME`, 
                                    SUM(i.Qty) AS ReceiveQty, 
                                    i.Price AS YEC_PRICE, 
                                    t.PRICE AS PMI_PRICE, 
                                    t.Price-i.Price AS Difference
                                FROM invoice_data_check i 
                                LEFT JOIN invoice_data_check_xtank t ON i.itemcode = t.CODE
                                GROUP BY i.PR, 
                                    i.Invoice, 
                                    i.FDate, 
                                    t.CODE, 
                                    i.itemname, 
                                    i.Price, 
                                    t.PRICE, 
                                    t.Price-i.Price, 
                                    i.PR
                                HAVING (((t.Price-i.Price)<>0) AND (NOT (i.PR) IS NULL))
                        ");

                    //$diff = 0;
                    foreach ($data as $key => $inv) {
                        //$diff = $this->getDataPrice($inv->pr) - $inv->unitprice;
                        $diff = $inv->Difference;
                        if ($diff != 0) {
                            $sheet->cell('A'.$row, $inv->PR);
                            $sheet->cell('B'.$row, $inv->InvoiceNo);
                            $sheet->cell('C'.$row, $inv->FltDate);
                            $sheet->cell('D'.$row, $inv->CODE);
                            $sheet->cell('E'.$row, $inv->NAME);
                            $sheet->cell('F'.$row, ($inv->ReceiveQty == 0)? '0.0':$inv->ReceiveQty);
                            $sheet->cell('G'.$row, $inv->YEC_PRICE);
                            $sheet->cell('H'.$row, $inv->PMI_PRICE);
                            $sheet->cell('I'.$row, $inv->Difference);
                            $row++;
                        }
                    }
                });

            })->download('xls');

        } 
        catch (Exception $e) 
        {
            return redirect(url('/invoicedatacheck'))->with(['err_message' => $e]);
        }
    }

    private function getYpicsData()
    {

        $uniquePR = $this->query('select distinct pr from invoice_data_check');
        $uniqueCode = $this->query('select distinct itemcode from invoice_data_check');

        $prs = '';
        foreach ($uniquePR as $key => $data) 
        {
            if($prs == '')
            {
                $prs = "'" . $data->pr . "'";
            }
            else
            {
                $prs = $prs . ", '". $data->pr . "'";
            }
        }

        $codes = '';
        foreach ($uniqueCode as $key => $data) 
        {
            if($codes == '')
            {
                $codes = "'" . $data->itemcode . "'";
            }
            else
            {
                $codes = $codes . ", '". $data->itemcode . "'";
            }
        }

        $xslip = DB::connection($this->mssql)
            ->select("
                SELECT DDATE, 
                    PORDER AS OrderNo, 
                    CODE AS ItemCode, 
                    VENDOR AS SupplierName, 
                    KVOL AS SchdQty, 
                    TJITU AS ActualTotal,
                    KVOL-TJITU AS AvailQtytoallocateMfgNo, 
                    NOTE AS Remarks, 
                    INPUTDATE AS CorrectDate, 
                    INPUTUSER AS CorrectUser,
                    DDATE
                FROM XSLIP
                WHERE PORDER Not Like 'WK%' AND PORDER IN (".$prs.")");

        $this->truncateTable('invoice_data_check_xslip');
        foreach ($xslip as $key => $data) 
        {
            DB::connection($this->mysql)->table('invoice_data_check_xslip')
                ->insert([
                    'orderno'      => $data->OrderNo,
                    'itemcode'     => $data->ItemCode,
                    'suppliername' => $data->SupplierName,
                    'schdqty'      => $data->SchdQty,
                    'actualqty'    => $data->ActualTotal,
                    'availqty'     => $data->AvailQtytoallocateMfgNo,
                    'remarks'      => $data->Remarks,
                    'correctdate'  => $data->CorrectDate,
                    'correctuser'  => $data->CorrectUser,
                    'ddate'        => $data->DDATE
                    ]);
        }

        $xtank = DB::connection($this->mssql)
            ->select("
                SELECT t.CODE, t.VENDOR, t.PRICE            
                FROM XTANK t            
                LEFT JOIN (SELECT CODE, max(TID) as MaximumTID 
                            FROM XTANK 
                            GROUP BY CODE) k ON k.MaximumTID = t.TID
                WHERE t.CODE IN (".$codes.")
                ");

        $this->truncateTable('invoice_data_check_xtank');
        foreach ($xtank as $key => $data) 
        {
            DB::connection($this->mysql)->table('invoice_data_check_xtank')
                ->insert([
                    'code'   => $data->CODE,
                    'vendor' => $data->VENDOR,
                    'price'  => $data->PRICE
                    ]);
        }
    }
    
    private function prepareYpicsData()
    {

        $uniquePR = $this->query('select distinct pr from invoice_data_check');
        $uniqueCode = $this->query('select distinct itemcode from invoice_data_check');
        
        $prs = '';
        foreach ($uniquePR as $key => $data) 
        {
            if($prs == '')
            {
                $prs = "'" . $data->pr . "'";
            }
            else
            {
                $prs = $prs . ", '". $data->pr . "'";
            }
        }

        $codes = '';
        foreach ($uniqueCode as $key => $data) 
        {
            if($codes == '')
            {
                $codes = "'" . $data->itemcode . "'";
            }
            else
            {
                $codes = $codes . ", '". $data->itemcode . "'";
            }
        }

        $allowance = DB::connection($this->mssql)
            ->select("
                SELECT allowance.CODE, 
                    allowance.NAME, 
                    Sum(allowance.BalanceRequirement) AS TOTAL_REQ, 
                    IIf((CONVERT(Int,Sum(allowance.BalanceRequirement)*0.01+0.5)*10)/10=0,1,
                        (CONVERT(Int,Sum(allowance.BalanceRequirement)*0.01+0.5)*10)/10) AS ALLOWANCE
                FROM 
                (
                    SELECT hk.PORDER, 
                        hk.CODE, 
                        hd.NAME, 
                        hk.OYACODE, 
                        hk.HOKAN, 
                        hk.KVOL, 
                        hk.TJITU, 
                        KVOL-TJITU AS BalanceRequirement, 
                        hk.INPUTDATE, 
                        hk.INPUTUSER
                    FROM XHIKI hk 
                    INNER JOIN XHEAD hd ON hk.CODE = hd.CODE
                    WHERE hk.PORDER Like 'WK%'
                        AND KVOL-TJITU<>0
                        AND hk.CODE in (".$codes.")
                ) allowance
                GROUP BY allowance.CODE, allowance.NAME
                ");

        $this->truncateTable('invoice_data_check_allowance');

        foreach ($allowance as $key => $data) 
        {
            #insert xslip data to invoice_data_check_allowance.
            DB::connection($this->mysql)->table('invoice_data_check_allowance')
            ->insert([
                'code'       => $data->CODE,
                'name'       => $data->NAME,
                'total_req'  => $data->TOTAL_REQ,
                'allowance'  => $data->ALLOWANCE
                ]);
        }

        $xzaik = DB::connection($this->mssql)
            ->select("
                SELECT z.CODE, z.HOKAN, Sum(z.ZAIK) AS ZAIK
                FROM XZAIK z
                WHERE z.CODE in (".$codes.")
                GROUP BY z.CODE, z.HOKAN
                HAVING z.HOKAN='WHS100' or z.HOKAN='WHS102'
                ");
        
        $this->truncateTable('invoice_data_check_xzaik');
        foreach ($xzaik as $key => $data) 
        {
            #insert xslip data to invoice_data_check_xzaik.
            DB::connection($this->mysql)->table('invoice_data_check_xzaik')
            ->insert([
                'code'  => $data->CODE,
                'hokan' => $data->HOKAN,
                'zaik'  => $data->ZAIK
                ]);
        }

    }

    private function preparetcalc()
    {
        $tcalc = DB::connection($this->mysql)->select("
                    SELECT *,
                           @f1 := IF(@cat = t.Code,@f1,0) + t.InvQty AS f1,
                            @f2 := IF(@cat = t.Code,@f1,0) + t.InvQty + WHSSTK AS f2,
                            @f3 := IF((@f2 - TOTAL_REQ) > 0, (t.InvQty + WHSSTK) - TOTAL_REQ,0) AS f3,
                            @f4 := IF((@f2 - TOTAL_REQ) = 0, 0, IF(ALLOWANCE > (@f3 + WHSAWS), @f3, IF((ALLOWANCE - WHSAWS) > t.InvQty, t.InvQty, ALLOWANCE - WHSAWS))) AS f4,
                            IF((t.InvQty - @f4) < 0, 0, t.InvQty - @f4)  AS f5,
                            @cnt := IF(@cat = t.Code,@cnt,0) + 1 AS `count`,
                            @cat := t.Code
                    FROM (
                        SELECT Q_INVOICE_byPR.InvoiceNo, 
                            Q_INVOICE_byPR.FltDate, 
                            Q_INVOICE_byPR.Code, 
                            Q_INVOICE_byPR.PartName, 
                            Q_INVOICE_byPR.PR, 
                            Q_INVOICE_byPR.UnitPrice, 
                            IF(ISNULL(Q_ALLOWANCE.TOTAL_REQ)=TRUE,0,Q_ALLOWANCE.TOTAL_REQ) AS TOTAL_REQ, 
                            IF(ISNULL(Q_WHS100.HOKAN)=TRUE,0,WHS100) AS WHSSTK, 
                            IF(ISNULL(Q_WHS102.HOKAN)=TRUE,0,WHS102) AS WHSAWS, 
                            IF(ISNULL(Q_ALLOWANCE.TOTAL_REQ)=TRUE,0,Q_GENERATE_CALC_TABLE_excess.EXCESS) AS EXCESS, 
                            Q_INVOICE_byPR.InvQty, 
                            IF(ISNULL(Q_ALLOWANCE.TOTAL_REQ)=TRUE,0,Q_ALLOWANCE.ALLOWANCE) AS ALLOWANCE, 
                            Q_PR_BALANCE.PR_BAL
                        FROM (((((
                            SELECT Invoice.Invoice AS InvoiceNo, 
                                Invoice.FDate AS FltDate, 
                                Invoice.ItemCode AS `Code`, 
                                Invoice.ItemName AS PartName, 
                                SUM(Invoice.Qty) AS InvQty, 
                                Invoice.PR, 
                                Invoice.Price AS UnitPrice
                            FROM invoice_data_check Invoice
                            GROUP BY Invoice.Invoice, 
                                Invoice.FDate, 
                                Invoice.ItemCode, 
                                Invoice.ItemName, 
                                Invoice.PR, 
                                Invoice.Price
                        ) Q_INVOICE_byPR -- invoice
                        LEFT JOIN (
                            SELECT s.orderno AS PORDER, 
                                s.itemcode AS `CODE`, 
                                s.schdqty AS KVOL, 
                                s.actualqty AS TJITU, 
                                s.availqty AS PR_BAL, 
                                SUBSTR(DDATE,0,4) & '/' & SUBSTR(DDATE,5,2) & '/' & SUBSTR(DDATE,7,2) AS `Date`
                            FROM invoice_data_check_xslip s
                            WHERE (((s.orderno) LIKE 'PR%' 
                                OR (s.orderno) LIKE 'AD%' 
                                OR (s.orderno) LIKE 'GR%') 
                                AND ((s.availqty)>0))
                        ) Q_PR_BALANCE ON Q_INVOICE_byPR.PR = Q_PR_BALANCE.PORDER)
                        LEFT JOIN invoice_data_check_allowance Q_ALLOWANCE ON Q_INVOICE_byPR.CODE = Q_ALLOWANCE.CODE)
                        LEFT JOIN (
                            SELECT *, zaik AS 'WHS100' FROM invoice_data_check_xzaik WHERE hokan = 'WHS100'
                            ) Q_WHS100 ON Q_INVOICE_byPR.CODE = Q_WHS100.CODE) 
                        LEFT JOIN (
                            SELECT Q_INVOICE_byPARTS.Code, 
                                Q_INVOICE_byPARTS.TotalQty, 
                                IF(ISNULL(Q_ALLOWANCE.CODE)=TRUE,0,Q_ALLOWANCE.TOTAL_REQ) AS TTL_REQ, 
                                IF(ISNULL(Q_WHS100.WHS100)=TRUE,0,Q_WHS100.WHS100) AS WHS, 
                                IF(ISNULL(Q_ALLOWANCE.ALLOWANCE)=TRUE,0,ALLOWANCE) AS AWS, 
                                IF(TotalQty+WHS100-IF(ISNULL(Q_ALLOWANCE.CODE)=TRUE,0,Q_ALLOWANCE.TOTAL_REQ)<0,0,TotalQty+WHS100-IF(ISNULL(Q_ALLOWANCE.CODE)=TRUE,0,Q_ALLOWANCE.TOTAL_REQ)) AS EXCESS
                            FROM ((
                                SELECT i.invoice AS InvoiceNo, 
                                    i.fdate AS FltDate, 
                                    i.itemcode AS CODE, 
                                    i.itemname AS PartName, 
                                    SUM(i.Qty) AS TotalQty, 
                                    i.price AS UnitPrice
                                FROM invoice_data_check i
                                GROUP BY i.Invoice, 
                                    i.FDate, 
                                    i.itemCode, 
                                    i.itemName, 
                                    i.Price
                            ) Q_INVOICE_byPARTS  -- invoice
                            LEFT JOIN invoice_data_check_allowance Q_ALLOWANCE ON Q_INVOICE_byPARTS.Code = Q_ALLOWANCE.CODE) 
                            LEFT JOIN (
                                SELECT *, zaik AS 'WHS100' FROM invoice_data_check_xzaik WHERE hokan = 'WHS100'
                                )Q_WHS100 ON Q_INVOICE_byPARTS.Code = Q_WHS100.CODE
                        )Q_GENERATE_CALC_TABLE_excess ON Q_INVOICE_byPR.CODE = Q_GENERATE_CALC_TABLE_excess.CODE)
                        LEFT JOIN (
                            SELECT *, zaik AS 'WHS102' FROM invoice_data_check_xzaik WHERE hokan = 'WHS102'
                            ) Q_WHS102 ON Q_INVOICE_byPR.CODE = Q_WHS102.CODE
                        WHERE (((Q_INVOICE_byPR.PR) NOT LIKE 'GR%' 
                            AND (Q_INVOICE_byPR.PR) NOT LIKE 'SH%'))
                    ) t
                    JOIN (SELECT @f1 := 0) f1
                    JOIN (SELECT @f2 := 0) f2
                    JOIN (SELECT @cnt := 0) `count`
                    JOIN (SELECT @cat := '') c
            ");

        $this->truncateTable('invoice_data_check_tcalc');
        foreach ($tcalc as $key => $data) 
        {
            DB::connection($this->mysql)->table('invoice_data_check_tcalc')
                ->insert([
                    'invoiceno' => $data->InvoiceNo,
                    'fltdate'   => $data->FltDate,
                    'code'      => $data->Code,
                    'partname'  => $data->PartName,
                    'pr'        => $data->PR,
                    'unitprice' => $data->UnitPrice,
                    'total_req' => $data->TOTAL_REQ,
                    'whs100'    => $data->WHSSTK,
                    'whs102'    => $data->WHSAWS,
                    'excess'    => $data->EXCESS,
                    'invqty'    => $data->InvQty,
                    'allowance' => $data->ALLOWANCE,
                    'pr_bal'    => $data->PR_BAL,
                    'f1'        => $data->f1,
                    'f2'        => $data->f2,
                    'f3'        => $data->f3,
                    'f4'        => $data->f4,
                    'f5'        => $data->f5,
                    'count'     => $data->count
                    ]);
        }

        DB::connection($this->mysql)->statement("
                UPDATE invoice_data_check_tcalc tc
                LEFT JOIN (
                    SELECT COUNT(`code`) AS `COUNT`, `code`
                    FROM invoice_data_check_tcalc
                    GROUP BY `code`
                ) T_CALC_TABLE_work ON (tc.`code` = T_CALC_TABLE_work.`code`) AND (tc.`count` = T_CALC_TABLE_work.COUNT) 
                SET F4 = 0
                WHERE (((T_CALC_TABLE_work.Code) IS NULL))");

        DB::connection($this->mysql)->statement("
                UPDATE invoice_data_check_tcalc tc SET tc.F5 = IF(InvQty-F4<0,0,InvQty-F4)");

        $xsact = DB::connection($this->mysql)->select("
                -- INSERT INTO xSACT_for_UPLOAD ( PORDER, AKUBU, JITU, FDATE, FTIME, APRICE, HOKAN, INVOICE_NUM )
                SELECT T_CALC.PR AS PORDER, 'J' AS AKUBU, 
                    T_CALC.F5 AS JITU, 
                    FltDate AS FDATE, 
                    TIME_FORMAT(NOW(), '%H:%i') AS FTIME, 
                    UnitPrice AS APRICE, 
                    'WHS100' AS HOKAN, T_CALC.InvoiceNo AS INVOICE_NUM
                FROM invoice_data_check_tcalc T_CALC
                WHERE (((T_CALC.F5)<>0))
                UNION
                -- INSERT INTO xSACT_for_UPLOAD ( PORDER, AKUBU, JITU, FDATE, FTIME, APRICE, HOKAN, INVOICE_NUM )
                SELECT T_CALC.PR AS PORDER, 
                    'J' AS AKUBU, T_CALC.F4 AS JITU, 
                    FltDate AS FDATE, 
                    TIME_FORMAT(NOW(), '%H:%i') AS FTIME, 
                    UnitPrice AS APRICE, 
                    'WHS102' AS HOKAN, T_CALC.InvoiceNo AS INVOICE_NUM
                FROM invoice_data_check_tcalc T_CALC
                WHERE (((T_CALC.F4)<>0))
                UNION
                -- INSERT INTO xSACT_for_UPLOAD ( PORDER, AKUBU, JITU, FDATE, FTIME, APRICE, HOKAN, INVOICE_NUM )
                SELECT Q_INVOICE_byPR.PR, 
                    'J' AS AKUBU, 
                    Q_INVOICE_byPR.InvQty AS JITU, 
                    FltDate AS FDATE, 
                    TIME_FORMAT(NOW(), '%H:%i') AS FTIME, 
                    UnitPrice AS APRICE, 
                    'WHS100' AS HOKAN, 
                    Q_INVOICE_byPR.InvoiceNo AS INVOICE_NUM
                FROM (((((
                        SELECT Invoice.Invoice AS InvoiceNo, 
                            Invoice.FDate AS FltDate, 
                            Invoice.ItemCode AS `Code`, 
                            Invoice.ItemName AS PartName, 
                            SUM(Invoice.Qty) AS InvQty, 
                            Invoice.PR, 
                            Invoice.Price AS UnitPrice
                        FROM invoice_data_check Invoice
                        GROUP BY Invoice.Invoice, 
                            Invoice.FDate, 
                            Invoice.ItemCode, 
                            Invoice.ItemName, 
                            Invoice.PR, 
                            Invoice.Price
                    )Q_INVOICE_byPR 
                LEFT JOIN (
                        SELECT s.orderno AS PORDER, 
                            s.itemcode AS `CODE`, 
                            s.schdqty AS KVOL, 
                            s.actualqty AS TJITU, 
                            s.availqty AS PR_BAL, 
                            SUBSTR(DDATE,0,4) & '/' & SUBSTR(DDATE,5,2) & '/' & SUBSTR(DDATE,7,2) AS `Date`
                        FROM invoice_data_check_xslip s
                        WHERE (((s.orderno) LIKE 'PR%' 
                            OR (s.orderno) LIKE 'AD%' 
                            OR (s.orderno) LIKE 'GR%') 
                            AND ((s.availqty)>0))
                    )Q_PR_BALANCE ON Q_INVOICE_byPR.PR = Q_PR_BALANCE.PORDER) 
                LEFT JOIN invoice_data_check_allowance Q_ALLOWANCE ON Q_INVOICE_byPR.Code = Q_ALLOWANCE.CODE) 
                LEFT JOIN (
                        SELECT *, zaik AS 'WHS100' FROM invoice_data_check_xzaik WHERE hokan = 'WHS100'
                        )Q_WHS100 ON Q_INVOICE_byPR.Code = Q_WHS100.CODE) 
                LEFT JOIN (
                        SELECT Q_INVOICE_byPARTS.Code, 
                            Q_INVOICE_byPARTS.TotalQty, 
                            IF(ISNULL(Q_ALLOWANCE.CODE)=TRUE,0,Q_ALLOWANCE.TOTAL_REQ) AS TTL_REQ, 
                            IF(ISNULL(Q_WHS100.WHS100)=TRUE,0,Q_WHS100.WHS100) AS WHS, 
                            IF(ISNULL(Q_ALLOWANCE.ALLOWANCE)=TRUE,0,ALLOWANCE) AS AWS, 
                            IF(TotalQty+WHS100-IF(ISNULL(Q_ALLOWANCE.CODE)=TRUE,0,Q_ALLOWANCE.TOTAL_REQ)<0,0,TotalQty+WHS100-IF(ISNULL(Q_ALLOWANCE.CODE)=TRUE,0,Q_ALLOWANCE.TOTAL_REQ)) AS EXCESS
                        FROM ((
                            SELECT i.invoice AS InvoiceNo, 
                                i.fdate AS FltDate, 
                                i.itemcode AS CODE, 
                                i.itemname AS PartName, 
                                SUM(i.Qty) AS TotalQty, 
                                i.price AS UnitPrice
                            FROM invoice_data_check i
                            GROUP BY i.Invoice, 
                                i.FDate, 
                                i.itemCode, 
                                i.itemName, 
                                i.Price
                        ) Q_INVOICE_byPARTS  -- invoice
                        LEFT JOIN invoice_data_check_allowance Q_ALLOWANCE ON Q_INVOICE_byPARTS.Code = Q_ALLOWANCE.CODE) 
                        LEFT JOIN (
                            SELECT *, zaik AS 'WHS100' FROM invoice_data_check_xzaik WHERE hokan = 'WHS100'
                            )Q_WHS100 ON Q_INVOICE_byPARTS.Code = Q_WHS100.CODE
                    )Q_GENERATE_CALC_TABLE_excess ON Q_INVOICE_byPR.Code = Q_GENERATE_CALC_TABLE_excess.Code) 
                LEFT JOIN (
                        SELECT *, zaik AS 'WHS102' FROM invoice_data_check_xzaik WHERE hokan = 'WHS102'
                        )Q_WHS102 ON Q_INVOICE_byPR.Code = Q_WHS102.CODE
                WHERE (((Q_INVOICE_byPR.PR) LIKE 'GR%' OR (Q_INVOICE_byPR.PR) LIKE 'SH%'));
            ");

        
        $this->truncateTable('invoice_data_check_xsact');
        foreach ($xsact as $key => $data) 
        {
            DB::connection($this->mysql)->table('invoice_data_check_xsact')
                ->insert([
                    'porder' => $data->PORDER,     
                    'akubu'   => $data->AKUBU,  
                    'jitu'    => $data->JITU,    
                    'fdate'   => $data->FDATE,    
                    'ftime'   => $this->formatDate($data->FTIME, 'Ymd').'1',    
                    'aprice'  => $data->APRICE,    
                    'hokan'    => $data->HOKAN,  
                    'invoice_num' => $data->INVOICE_NUM
                    ]);
        }
    }
}
