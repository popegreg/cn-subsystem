<?php
namespace App\Http\Controllers\SSS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Config;
use DB;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Excel;
use PDF;
class DeliveryWarningController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;

    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'sss');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getDeliveryWarning()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_DELWRNG'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('sss.DeliveryWarning',['userProgramAccess' => $userProgramAccess]);
        }
    }

    //GET ALL DATA
    public function getAllDeliveryWarning(Request $req)
    {
         return DB::connection($this->mssql)->table('XRECE as R')
                    ->join('XHEAD as H', 'H.CODE','=','R.CODE')
                    ->join('XCUST as C', 'C.CUST','=','R.CUST')
                    ->select('R.CDATE AS order_date','R.SORDER AS po','H.CODE as code','H.NAME as name','R.CVOL as order_qty','C.CNAME as customer','R.KVOL as sched_qty')
                    ->orderBy('R.CDATE', 'DESC')
                    ->skip(0)->take($req->row)
                    ->get();
    }

    //GET ALL DATA WHERE DATE IS EQUAL TO DATE INPUTED
    public function loadDeliveryWarningWithDate()
    {

        $fd = $_GET['fd'];
        $td = $_GET['td'];

        $data = DB::connection($this->mssql)->table('XRECE as R')
                    ->join('XHEAD as H', 'H.CODE','=','R.CODE')
                    ->join('XCUST as C', 'C.CUST','=','R.CUST')
                    ->where('R.CDATE', '>=', $fd)
                    ->where('R.CDATE', '<=', $td)
                    ->select('R.CDATE AS order_date','R.SORDER AS po','H.CODE as code','H.NAME as name','R.CVOL as order_qty','C.CNAME as customer','R.KVOL as sched_qty')
                    ->orderBy('R.CDATE', 'DESC')
                    ->get();



        return $data;
    }


        //GENERATE PDF
    public function postDeliveryWarningPDF()
    {
        set_time_limit(0);
        ini_set("memory_limit",-1);
        ini_set('max_execution_time', 0);

        $output = '<style type="text/css">
                    body{
                    margin:0 auto;
                    font-size: 11px;}
                    </style><h2> Parts Delivery Warning Check </h2><table style="width:100%;" >';

         $fd = $_POST['fd_pdf'];
         $td = $_POST['td_pdf'];

         $data = $this->getDistinctDataPDF($fd,$td);

         foreach ($data as $orderdate) {
            $output = $output.'<tr><td><h2> '.$this->getDateFormat($orderdate->order_date).' </h2></td></tr>
                    <tr  align="left">
                    <th>Order Date</th>
                    <th>PO</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Order Qty</th>
                    <th>Customer</th>
                    <th>Sched Qty</th>
                    <th>Parts Completion</th>
                    <th>YEC</th>
                    <th>PMI</th>

                    </tr>
                    <td colspan="10"><hr/></td>';

             $ndata = $this->getDataPDF($fd,$td,$orderdate->order_date);
             foreach ($ndata as $d) {
                $output = $output.'<tr>
                        <td>'.$this->getDateFormat($d->order_date).'</td>
                        <td>'.substr_replace($d->po, "", -5).'</td>
                        <td>'.$d->code.'</td>
                        <td>'.$d->name.'</td>
                        <td>'.$d->order_qty.'</td>
                        <td>'.$d->customer.'</td>
                        <td>'.$d->sched_qty.'</td>
                        <td></td>
                        <td></td>
                        <td></td>

                        </tr>';
             }
             $output = $output.'<tr><td colspan="10"><hr/></td></tr>';

         }

         $output = $output.'</table>';
         $output = mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
         $pdf = PDF::loadHTML($output)->setPaper('letter', 'landscape');
         return $pdf->stream("Delivery_Warning.pdf");

         
                       
    }

    //ARRANGE DATE TO MONTH / DAY / YEAR
    public function getDateFormat($date)
    {

        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);

        return $month."/".$day."/".$year;
    }




    
    //GENERATE EXCEL
    public function postDeliveryWarningExcel()
    {

     $fd = $_POST['fd'];
     $td = $_POST['td'];

     $data = $this->getDataFromDatabase($fd,$td);

        try {

            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            
            
            Excel::create('DeliveryWarning_report_'.$date, function($excel) use($data) {
                $excel->sheet('Sheet1', function($sheet) use($data) {
                    $sheet->cell('A1', "Order Date");
                    $sheet->cell('B1', "PO");
                    $sheet->cell('C1', "Code");
                    $sheet->cell('D1', "Name");
                    $sheet->cell('E1', "Order Qty");
                    $sheet->cell('F1', "Customer");
                    $sheet->cell('G1', "Sched Qty");
                    
                    $row = 2;
                    $count = count($data);
                    
                    for ($i=0; $i < $count; $i++) {
                            $sheet->cell('A'.$row, $this->getDateFormat($data[$i]->order_date));
                            $sheet->cell('B'.$row, substr_replace($data[$i]->po, "", -5));
                            $sheet->cell('C'.$row, $data[$i]->code);
                            $sheet->cell('D'.$row, $data[$i]->name);
                            $sheet->cell('E'.$row, $data[$i]->order_qty);
                            $sheet->cell('F'.$row, $data[$i]->customer);
                            $sheet->cell('G'.$row, $data[$i]->sched_qty);
                        $row++;
                    }
                });
            })->download('xls');

        } catch (Exception $e) {
            return Redirect::back()->with('err_message',$e);
        }
    }

    public function getDataFromDatabase($fd,$td)
    {
            $data = "";
            if($fd == "" || $td == "" || $fd == "1" || $td == "1")
             {
                
                $data = DB::connection($this->mssql)->table('XRECE as R')
                    ->join('XHEAD as H', 'H.CODE','=','R.CODE')
                    ->join('XCUST as C', 'C.CUST','=','R.CUST')
                    ->orderBy('R.CDATE', 'DESC')
                    ->limit(0)->take(50)
                    ->get(['R.CDATE AS order_date','R.SORDER AS po','H.CODE as code','H.NAME as name','R.CVOL as order_qty','C.CNAME as customer','R.KVOL as sched_qty']);

                    

             }else
             {
                    $data = DB::connection($this->mssql)->table('XRECE as R')
                    ->join('XHEAD as H', 'H.CODE','=','R.CODE')
                    ->join('XCUST as C', 'C.CUST','=','R.CUST')
                    ->where('R.CDATE', '>=', $fd)
                    ->where('R.CDATE', '<=', $td)
                    ->orderBy('R.CDATE', 'DESC')
                    ->limit(0)->take(50)
                    ->get(['R.CDATE AS order_date','R.SORDER AS po','H.CODE as code','H.NAME as name','R.CVOL as order_qty','C.CNAME as customer','R.KVOL as sched_qty']);
             }
             return $data;
    }


     public function getDataPDF($fd,$td,$orderdate)
    {
            $data = "";
            if($fd == "" || $td == "" || $fd == "1" || $td == "1")
             {

                $data = DB::connection($this->mssql)->table('XRECE as R')
                    ->join('XHEAD as H', 'H.CODE','=','R.CODE')
                    ->join('XCUST as C', 'C.CUST','=','R.CUST')
                    ->where('R.CDATE', $orderdate)
                    ->orderBy('R.CDATE', 'DESC')
                    ->limit(0)->take(50)
                    ->get(['R.CDATE AS order_date','R.SORDER AS po','H.CODE as code','H.NAME as name','R.CVOL as order_qty','C.CNAME as customer','R.KVOL as sched_qty']);

                    

             }else
             {
                    $data = DB::connection($this->mssql)->table('XRECE as R')
                    ->join('XHEAD as H', 'H.CODE','=','R.CODE')
                    ->join('XCUST as C', 'C.CUST','=','R.CUST')
                    ->where('R.CDATE', '>=', $fd)
                    ->where('R.CDATE', '<=', $td)
                    ->where('R.CDATE', $orderdate)
                    ->orderBy('R.CDATE', 'DESC')
                    ->limit(0)->take(50)
                    ->get(['R.CDATE AS order_date','R.SORDER AS po','H.CODE as code','H.NAME as name','R.CVOL as order_qty','C.CNAME as customer','R.KVOL as sched_qty']);
             }
             return $data;
    }


    public function getDistinctDataPDF($fd,$td)
    {
            $data = "";
            if($fd == "" || $td == "" || $fd == "1" || $td == "1")
             {

                $data = DB::connection($this->mssql)->table('XRECE as R')
                    ->join('XHEAD as H', 'H.CODE','=','R.CODE')
                    ->join('XCUST as C', 'C.CUST','=','R.CUST')
                    ->orderBy('R.CDATE', 'DESC')
                    ->distinct()
                    ->limit(0)->take(50)
                    ->get(['R.CDATE AS order_date']);

                    

             }else
             {
                   $data = DB::connection($this->mssql)->table('XRECE as R')
                    ->join('XHEAD as H', 'H.CODE','=','R.CODE')
                    ->join('XCUST as C', 'C.CUST','=','R.CUST')
                    ->where('R.CDATE', '>=', $fd)
                    ->where('R.CDATE', '<=', $td)
                    ->orderBy('R.CDATE', 'DESC')
                    ->distinct()
                    ->limit(0)->take(50)
                    ->get(['R.CDATE AS order_date']);
             }
             return $data;
    }



}
