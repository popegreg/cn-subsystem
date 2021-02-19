<?php
namespace App\Http\Controllers\SSS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Config;
use DB;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Excel;
use PDF;
use Illuminate\Support\Facades\Auth; #Auth facade

class AnswerInputManagementController extends Controller
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

    public function getIndex()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_ANSMNGT'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
                                    
            return view('sss.AnswerInputManagement',['userProgramAccess' => $userProgramAccess,
                'data' => $this->getAllAnswerInputManagement()]);
        }
    }

    //SELECT ALL DATA
    public function getAllAnswerInputManagement()
    {
                    
        // $data = DB::connection($this->mysql)->table('ts_zypf0090 as z90')
        //             ->join('ts_zypf0090 as z90','m.po','=','z90.complete_po')
        //             ->join('tempzymr0120 as z120','z90.complete_po','=','z120.complete_po')
        //             ->orderBy('z120.order_date','desc')
        //             ->select(DB::raw("z120.order_date as order_date"),
        //                         DB::raw("z90.item_code as code"),
        //                         DB::raw("z90.item_text as name"),
        //                         DB::raw("z90.purchase_order_number as po"),
        //                         DB::raw("z90.purchase_order_quantity as qty"),
        //                         DB::raw("z90.answer_quantity as r3answer"),
        //                         DB::raw("z90.current_answer_time as time"),
        //                         DB::raw("z120.shipment_text as remarks"),
        //                         DB::raw("z120.vendor as custcode"),
        //                         DB::raw("z120.vendor_name as customer"))
        //             ->get();
        $data = DB::connection($this->mysql)->table('tempzymr0120')
                    ->select('order_date as order_date',
                            'itemcode as code',
                            'itemname as name',
                            'po as po',
                            'qty as qty',
                            'ans_satisfied_period as r3answer',
                            'answer_force_moment as time',
                            'shipment_text as remarks',
                            'vendor as custcode',
                            'vendor_name as customer')
                    ->orderBy('order_date','desc')
                    ->get();
        return $data;
    }

    // SELECT ALL DATA WHERE PRODUCTS ARE NOT EQUAL TO EXCEPTIONS
    public function answerinputmanagementloadwithexceptions()
    {
                    $orderdate = $_GET['orderdate'];
                     $exceptions = json_decode($_GET['exceptions']);
                     $radio = $_GET['radio'];

                    return $this->getData($orderdate,$exceptions,$radio);
    }

    public function getData($orderdate,$exceptions,$r)
    {
            if(count($exceptions) > 0)
                return DB::connection($this->mysql)->table('tempzymr0120')
                    ->where('isDeleted', 'false')
                    ->where('order_date', $r, $orderdate)
                    ->whereNotIn('itemname',$exceptions)
                    ->select('order_date as order_date',
                            'itemcode as code',
                            'itemname as name',
                            'po as po',
                            'qty as qty',
                            'ans_satisfied_period as r3answer',
                            'answer_force_moment as time',
                            'shipment_text as remarks',
                            'vendor as custcode',
                            'vendor_name as customer')
                    ->orderBy('order_date','desc')
                    ->get();
            else
                return DB::connection($this->mysql)->table('tempzymr0120')
                    ->where('isDeleted', 'false')
                    ->where('order_date', $r, $orderdate)
                    ->select('order_date as order_date',
                            'itemcode as code',
                            'itemname as name',
                            'po as po',
                            'qty as qty',
                            'ans_satisfied_period as r3answer',
                            'answer_force_moment as time',
                            'shipment_text as remarks',
                            'vendor as custcode',
                            'vendor_name as customer')
                    ->orderBy('order_date','desc')
                    ->get();
    }

        
    //GENERATE EXCEL
    public function postanswerinputmanagementexcel()
    {
        $orderdate = $_POST['hidorderdate'];
        $exceptions = json_decode($_POST['hidexceptions']);
        $radio = $_POST['hidradio'];
        $data = "";

        if($radio!="") {
            $data = $this->getData($orderdate,$exceptions,$radio);
        } else {
            $data = $this->getAllAnswerInputManagement();
        }
        
                    
        try {
             
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            
            
            Excel::create('Answer_input_report_'.$date, function($excel) use($data) {
                $excel->sheet('Sheet1', function($sheet) use($data) {
                    $sheet->cell('A1', "OrderDate");
                    $sheet->cell('B1', "PO");
                    $sheet->cell('C1', "PCode");
                    $sheet->cell('D1', "PName");
                    $sheet->cell('E1', "Qty");
                    $sheet->cell('F1', "R3Answer");
                    $sheet->cell('G1', "Time");
                    $sheet->cell('H1', "Remarks");
                    $sheet->cell('I1', "CustCode");
                    $sheet->cell('J1', "Customer");
                    
                    $row = 2;
                    
                    for ($i=0; $i < count($data); $i++) {
                            $sheet->cell('A'.$row, $data[$i]->order_date);
                            $sheet->cell('B'.$row, $data[$i]->po);
                            $sheet->cell('C'.$row, $data[$i]->code);
                            $sheet->cell('D'.$row, $data[$i]->name);
                            $sheet->cell('E'.$row, $data[$i]->qty);
                            $sheet->cell('F'.$row, $data[$i]->r3answer);
                            $sheet->cell('G'.$row, $data[$i]->time);
                            $sheet->cell('H'.$row, $data[$i]->remarks);
                            $sheet->cell('I'.$row, $data[$i]->custcode);
                            $sheet->cell('J'.$row, $data[$i]->customer);
                        $row++;
                    }
                });
            })->download('xls');
        
        } catch (Exception $e) {
            return Redirect::back()->with('err_message',$e);
        }

    }

    
}
