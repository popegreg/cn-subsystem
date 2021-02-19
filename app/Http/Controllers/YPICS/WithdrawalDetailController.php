<?php

namespace App\Http\Controllers\YPICS;

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

class WithdrawalDetailController extends Controller
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

    public function index()
    {
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_XHIKI'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
        	# Render WBS Page.
            return view('ypics.withdrawaldetail',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function processExcelFile(Request $req)
    {
    	$file = $req->file('xhiki_file');
        $output = [
            'msg' => "Uploading failed.",
            'status' => 'failed',
        ];
    	$data;

        //return dd($file);

    	Excel::load($file, function ($reader) use(&$data)
        {
            $data = $reader->toArray();
        });

        call_user_func_array('array_merge', $data);

        //return dd($data);
        // echo "<pre>",print_r($data),"</pre>";

        $params = [];
        // $multi = false;
        // $multimulti = false;

        $this->truncate('tbl_xhiki');
        $seiban = '';
        foreach ($data as $key => $x) {
            //if (isset($x['seiban'])) {
                $seiban = $x['seiban'];
                if ($seiban == '' || $seiban == null) {
                    $ypics = DB::connection($this->mssql)->table('XSLIP')
                                ->where('PORDER',$x['porder'])
                                ->first();
                    if ($this->checkIfExistObject($ypics) > 0) {
                        $seiban = $ypics->SEIBAN;
                    }
                }

                array_push($params,[
                    'hid' => (isset($x['hid']))? $x['hid'] : '',
                    'porder' => (isset($x['porder']))? $x['porder'] : '',
                    'peda' => (isset($x['peda']))? $x['peda'] : '',
                    'code' => (isset($x['code']))? $x['code'] : '',
                    'oyacode' => (isset($x['oyacode']))? $x['oyacode'] : '',
                    'bumo' => (isset($x['bumo']))? $x['bumo'] : '',
                    'hokan' => (isset($x['hokan']))? $x['hokan'] : '',
                    'nextbumo' => (isset($x['nextbumo']))? $x['nextbumo'] : '',
                    'opt' => (isset($x['opt']))? $x['opt'] : '',
                    'kvol' => (isset($x['kvol']))? $x['kvol'] : '',
                    'pickvol' => (isset($x['pickvol']))? $x['pickvol'] : '',
                    'pdate' => (isset($x['pdate']))? $x['pdate'] : '',
                    'ndate' => (isset($x['ndate']))? $x['ndate'] : '',
                    'seiban' => (isset($seiban))? $seiban : '',
                    'beda' => (isset($x['beda']))? $x['beda'] : '',
                    'tjitu' => (isset($x['tjitu']))? $x['tjitu'] : '',
                    'idate' => (isset($x['idate']))? $x['idate'] : '',
                    'hand' => (isset($x['hand']))? $x['hand'] : '',
                    'note' => (isset($x['note']))? $x['note'] : '',
                    'psumi' => (isset($x['psumi']))? $x['psumi'] : '',
                    'dofukusuu' => (isset($x['dofukusuu']))? $x['dofukusuu'] : '',
                    'doseiban' => (isset($x['doseiban']))? $x['doseiban'] : '',
                    'seibhku' => (isset($x['seibhku']))? $x['seibhku'] : '',
                    'hikiku' => (isset($x['hikiku']))? $x['hikiku'] : '',
                    'pickku' => (isset($x['pickku']))? $x['pickku'] : '',
                    'inputdate' => (isset($x['inputdate']))? $x['inputdate'] : '',
                    'inputuser' => (isset($x['inputuser']))? $x['inputuser'] : '',
                    'name' => (isset($x['name']))? $x['name'] : '',
                    'oyaname' => (isset($x['oyaname']))? $x['oyaname'] : '',
                    'bumoname' => (isset($x['bumoname']))? $x['bumoname'] : '',
                    'hokanname' => (isset($x['hokanname']))? $x['hokanname'] : '',
                    'nextbumoname' => (isset($x['nextbumoname']))? $x['nextbumoname'] : '',
                ]);
            // } else {
            //     $multi = true;
            //     break;
            // }
        }

        // if ($multi) {
        //     foreach ($data[0] as $key => $x) {
        //         if (isset($x['seiban'])) {
        //             $seiban = $x['seiban'];
        //             if ($seiban == '' || $seiban == null) {
        //                 $ypics = DB::connection($this->mssql)->table('XSLIP')
        //                             ->where('PORDER',$x['porder'])
        //                             ->first();
        //                 if ($this->checkIfExistObject($ypics) > 0) {
        //                     $seiban = $ypics->SEIBAN;
        //                 }
        //             }

        //             array_push($params,[
        //                 'hid' => (isset($x['hid']))? $x['hid'] : '',
        //                 'porder' => (isset($x['porder']))? $x['porder'] : '',
        //                 'peda' => (isset($x['peda']))? $x['peda'] : '',
        //                 'code' => (isset($x['code']))? $x['code'] : '',
        //                 'oyacode' => (isset($x['oyacode']))? $x['oyacode'] : '',
        //                 'bumo' => (isset($x['bumo']))? $x['bumo'] : '',
        //                 'hokan' => (isset($x['hokan']))? $x['hokan'] : '',
        //                 'nextbumo' => (isset($x['nextbumo']))? $x['nextbumo'] : '',
        //                 'opt' => (isset($x['opt']))? $x['opt'] : '',
        //                 'kvol' => (isset($x['kvol']))? $x['kvol'] : '',
        //                 'pickvol' => (isset($x['pickvol']))? $x['pickvol'] : '',
        //                 'pdate' => (isset($x['pdate']))? $x['pdate'] : '',
        //                 'ndate' => (isset($x['ndate']))? $x['ndate'] : '',
        //                 'seiban' => (isset($seiban))? $seiban : '',
        //                 'beda' => (isset($x['beda']))? $x['beda'] : '',
        //                 'tjitu' => (isset($x['tjitu']))? $x['tjitu'] : '',
        //                 'idate' => (isset($x['idate']))? $x['idate'] : '',
        //                 'hand' => (isset($x['hand']))? $x['hand'] : '',
        //                 'note' => (isset($x['note']))? $x['note'] : '',
        //                 'psumi' => (isset($x['psumi']))? $x['psumi'] : '',
        //                 'dofukusuu' => (isset($x['dofukusuu']))? $x['dofukusuu'] : '',
        //                 'doseiban' => (isset($x['doseiban']))? $x['doseiban'] : '',
        //                 'seibhku' => (isset($x['seibhku']))? $x['seibhku'] : '',
        //                 'hikiku' => (isset($x['hikiku']))? $x['hikiku'] : '',
        //                 'pickku' => (isset($x['pickku']))? $x['pickku'] : '',
        //                 'inputdate' => (isset($x['inputdate']))? $x['inputdate'] : '',
        //                 'inputuser' => (isset($x['inputuser']))? $x['inputuser'] : '',
        //                 'name' => (isset($x['name']))? $x['name'] : '',
        //                 'oyaname' => (isset($x['oyaname']))? $x['oyaname'] : '',
        //                 'bumoname' => (isset($x['bumoname']))? $x['bumoname'] : '',
        //                 'hokanname' => (isset($x['hokanname']))? $x['hokanname'] : '',
        //                 'nextbumoname' => (isset($x['nextbumoname']))? $x['nextbumoname'] : '',
        //             ]);
        //         } else {
        //             break;
        //             $multimulti = true;
        //         }
        //     }

        //     if ($multimulti) {
        //         foreach ($data as $key => $x) {
        //             $seiban = $x['seiban'];
        //             if ($seiban == '' || $seiban == null) {
        //                 $ypics = DB::connection($this->mssql)->table('XSLIP')
        //                             ->where('PORDER',$x['porder'])
        //                             ->first();
        //                 if ($this->checkIfExistObject($ypics) > 0) {
        //                     $seiban = $ypics->SEIBAN;
        //                 }
        //             }

        //             array_push($params,[
        //                 'hid' => (isset($x['hid']))? $x['hid'] : '',
        //                 'porder' => (isset($x['porder']))? $x['porder'] : '',
        //                 'peda' => (isset($x['peda']))? $x['peda'] : '',
        //                 'code' => (isset($x['code']))? $x['code'] : '',
        //                 'oyacode' => (isset($x['oyacode']))? $x['oyacode'] : '',
        //                 'bumo' => (isset($x['bumo']))? $x['bumo'] : '',
        //                 'hokan' => (isset($x['hokan']))? $x['hokan'] : '',
        //                 'nextbumo' => (isset($x['nextbumo']))? $x['nextbumo'] : '',
        //                 'opt' => (isset($x['opt']))? $x['opt'] : '',
        //                 'kvol' => (isset($x['kvol']))? $x['kvol'] : '',
        //                 'pickvol' => (isset($x['pickvol']))? $x['pickvol'] : '',
        //                 'pdate' => (isset($x['pdate']))? $x['pdate'] : '',
        //                 'ndate' => (isset($x['ndate']))? $x['ndate'] : '',
        //                 'seiban' => (isset($seiban))? $seiban : '',
        //                 'beda' => (isset($x['beda']))? $x['beda'] : '',
        //                 'tjitu' => (isset($x['tjitu']))? $x['tjitu'] : '',
        //                 'idate' => (isset($x['idate']))? $x['idate'] : '',
        //                 'hand' => (isset($x['hand']))? $x['hand'] : '',
        //                 'note' => (isset($x['note']))? $x['note'] : '',
        //                 'psumi' => (isset($x['psumi']))? $x['psumi'] : '',
        //                 'dofukusuu' => (isset($x['dofukusuu']))? $x['dofukusuu'] : '',
        //                 'doseiban' => (isset($x['doseiban']))? $x['doseiban'] : '',
        //                 'seibhku' => (isset($x['seibhku']))? $x['seibhku'] : '',
        //                 'hikiku' => (isset($x['hikiku']))? $x['hikiku'] : '',
        //                 'pickku' => (isset($x['pickku']))? $x['pickku'] : '',
        //                 'inputdate' => (isset($x['inputdate']))? $x['inputdate'] : '',
        //                 'inputuser' => (isset($x['inputuser']))? $x['inputuser'] : '',
        //                 'name' => (isset($x['name']))? $x['name'] : '',
        //                 'oyaname' => (isset($x['oyaname']))? $x['oyaname'] : '',
        //                 'bumoname' => (isset($x['bumoname']))? $x['bumoname'] : '',
        //                 'hokanname' => (isset($x['hokanname']))? $x['hokanname'] : '',
        //                 'nextbumoname' => (isset($x['nextbumoname']))? $x['nextbumoname'] : '',
        //             ]);
        //         }
        //     }

        // }

        $count = count($params);
        $insertBatchs = array_chunk($params, 1000);
        foreach ($insertBatchs as $batch) {
            DB::connection($this->mysql)->table('tbl_xhiki')->insert($batch);
        }

        if ($count > 0) {
            $output = [
                'msg' => "Uploading Successfully.",
                'status' => 'success',
            ];
        } else {
            $output = [
                'msg' => "No data was uploaded.",
                'status' => 'failed',
            ];
        }

        return $output;
    }

    public function downloadExcelFile()
    {
        Excel::create('TXHIKI', function($excel) {
            $excel->sheet('TXHIKI', function($sheet) {
                $sheet->cell('A1', "HID");
                $sheet->cell('B1', "PORDER");
                $sheet->cell('C1', "PEDA");
                $sheet->cell('D1', "CODE");
                $sheet->cell('E1', "OYACODE");
                $sheet->cell('F1', "BUMO");
                $sheet->cell('G1', "HOKAN");
                $sheet->cell('H1', "NEXTBUMO");
                $sheet->cell('I1', "OPT");
                $sheet->cell('J1', "KVOL");
                $sheet->cell('K1', "PICKVOL");
                $sheet->cell('L1', "PDATE");
                $sheet->cell('M1', "NDATE");
                $sheet->cell('N1', "SEIBAN");
                $sheet->cell('O1', "BEDA");
                $sheet->cell('P1', "TJITU");
                $sheet->cell('Q1', "IDATE");
                $sheet->cell('R1', "HAND");
                $sheet->cell('S1', "NOTE");
                $sheet->cell('T1', "PSUMI");
                $sheet->cell('U1', "DOFUKUSUU");
                $sheet->cell('V1', "DOSEIBAN");
                $sheet->cell('W1', "SEIBHKU");
                $sheet->cell('X1', "HIKIKU");
                $sheet->cell('Y1', "PICKKU");
                $sheet->cell('Z1', "INPUTDATE");
                $sheet->cell('AA1', "INPUTNAME");
                $sheet->cell('AB1', "NAME");
                $sheet->cell('AC1', "OYANAME");
                $sheet->cell('AD1', "BUMONAME");
                $sheet->cell('AE1', "HOKANNAME");
                $sheet->cell('AF1', "NEXTBUMONAME");

                $ypics = DB::connection($this->mysql)->table('tbl_xhiki')->get();

                $row = 2;
                //sort($allProd);
                foreach ($ypics as $key => $x) {
                    $sheet->cell('A'.$row, $x->hid);
                    $sheet->cell('B'.$row, $x->porder);
                    $sheet->cell('C'.$row, $x->peda);
                    $sheet->cell('D'.$row, $x->code);
                    $sheet->cell('E'.$row, $x->oyacode);
                    $sheet->cell('F'.$row, $x->bumo);
                    $sheet->cell('G'.$row, $x->hokan);
                    $sheet->cell('H'.$row, $x->nextbumo);
                    $sheet->cell('I'.$row, $x->opt);
                    $sheet->cell('J'.$row, $x->kvol);
                    $sheet->cell('K'.$row, $x->pickvol);
                    $sheet->cell('L'.$row, $x->pdate);
                    $sheet->cell('M'.$row, $x->ndate);
                    $sheet->cell('N'.$row, $x->seiban);
                    $sheet->cell('O'.$row, $x->beda);
                    $sheet->cell('P'.$row, $x->tjitu);
                    $sheet->cell('Q'.$row, $x->idate);
                    $sheet->cell('R'.$row, $x->hand);
                    $sheet->cell('S'.$row, $x->note);
                    $sheet->cell('T'.$row, $x->psumi);
                    $sheet->cell('U'.$row, $x->dofukusuu);
                    $sheet->cell('V'.$row, $x->doseiban);
                    $sheet->cell('W'.$row, $x->seibhku);
                    $sheet->cell('X'.$row, $x->hikiku);
                    $sheet->cell('Y'.$row, $x->pickku);
                    $sheet->cell('Z'.$row, $x->inputdate);
                    $sheet->cell('AA'.$row, $x->inputuser);
                    $sheet->cell('AB'.$row, $x->name);
                    $sheet->cell('AC'.$row, $x->oyaname);
                    $sheet->cell('AD'.$row, $x->bumoname);
                    $sheet->cell('AE'.$row, $x->hokanname);
                    $sheet->cell('AF'.$row, $x->nextbumoname);
                    $row++;
                }
            });
        })->download('xlsx');
    }

    public function checkData()
    {
        $data = ['status' => 'failed'];
        $count = DB::connection($this->mysql)->table('tbl_xhiki')->count();
        if ($count > 0) {
            $data = ['status' => 'success'];
        }
        return $data;
    }

    private function checkIfExistObject($object)
    {
       return count( (array)$object);
    }

    private function truncate($tbl)
    {
        DB::connection($this->mysql)->table($tbl)->truncate();
    }
}