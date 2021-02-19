<?php
namespace App\Http\Controllers\SSS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Config;
use DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth; #Auth facade
use Carbon\Carbon;
use Excel;
use PDF;

class SampleDoujiInputController extends Controller
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
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_DOUJI'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('sss.SampleDoujiInput',['userProgramAccess' => $userProgramAccess]);
        }
    }

    //SELECT ALL DATA
    public function getAllSampleDoujiInput()
    {
        $data = DB::connection($this->mysql)->table('temp_sss_mrplist')
                    ->select('orddate AS order_date',
                        'po AS po',
                        'dcode AS dcode',
                        'orderbal AS order_bal',
                        'orderqty AS order_qty',
                        'custcode AS cust_code',
                        'custname AS cust_name',
                        'mcode AS mcode',
                        'mname AS mname',
                        'custcode AS sup_code',
                        'custname AS sup_name',
                        're AS re',
                        'status AS status')
                    ->distinct()
                    ->get();
        return $data;
    }

    private function getSupplier($code)
    {
        $data = DB::connection($this->mssql)->table('XITEM')
                    ->where('CODE',$code)
                    ->select('VENDOR')
                    ->get();
        return (isset($data[0]->VENDOR))? $data[0]->VENDOR: "";
    }

    //GENERATE EXCEL
    public function postDoujiExportExcel()
    {
     $data = $this->getAllSampleDoujiInput();

        try {

            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            
            
            Excel::create('Douji_report_'.$date, function($excel) use($data) {
                $excel->sheet('Sheet1', function($sheet) use($data) {
                    $sheet->cell('A1', "Order Date");
                    $sheet->cell('B1', "PO");
                    $sheet->cell('C1', "DCode");
                    $sheet->cell('D1', "OrderBal");
                    $sheet->cell('E1', "Order Qty");
                    $sheet->cell('F1', "CustCode");
                    $sheet->cell('G1', "Customer");
                    $sheet->cell('H1', "MCode");
                    $sheet->cell('I1', "MName");
                    $sheet->cell('J1', "SupplierCode");
                    $sheet->cell('K1', "SupplierName");
                    $sheet->cell('L1', "RE");
                    $sheet->cell('M1', "Status");
                    
                    $row = 2;
                    foreach ($data as $key => $douji) {
                        $sheet->cell('A'.$row, $douji->order_date);
                        $sheet->cell('B'.$row, $douji->po);
                        $sheet->cell('C'.$row, $douji->dcode);
                        $sheet->cell('D'.$row, $douji->order_bal);
                        $sheet->cell('E'.$row, $douji->order_qty);
                        $sheet->cell('F'.$row, $douji->cust_code);
                        $sheet->cell('G'.$row, $douji->cust_name);
                        $sheet->cell('H'.$row, $douji->mcode);
                        $sheet->cell('I'.$row, $douji->mname);
                        $sheet->cell('J'.$row, $this->getSupplier($douji->dcode));
                        $sheet->cell('K'.$row, $this->getSupplier($douji->dcode));
                        $sheet->cell('L'.$row, $douji->re);
                        $sheet->cell('M'.$row, $douji->status);
                        $row++;
                    }
                });
            })->download('xls');

        } catch (Exception $e) {
            return Redirect::back()->with('err_message',$e);
        }
    }


}
