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
use Datatables;

class YPICSDispatchController extends Controller
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
        if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_DISPATCH'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
        	# Render WBS Page.
            return view('ypics.ypicsdispatch',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function processExcelFile(Request $req)
    {
    	$file = $req->file('dispatch_file');
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
        $params_update = [];
        $params_none = [];
        $ypics_params = [];
        // $multi = false;
        // $multimulti = false;

        // get and insert XPICK DATA from YPICS to Local DB
        $ypics = DB::connection($this->mssql)
            			->select("select p.PORDER as porder,
            							p.CODE as code,
            							p.SEIBAN as seiban,
            							h.NAME as code_name,
            							v.BNAME as motoname,
            							s2.BNAME as hokanname,
            							h.TANI1 as tani
								 from XPICK p 
								 left join XHEAD h on p.CODE = h.CODE
								 left join XSECT v on p.MOTO = v.BUMO
								 left join XSECT s2 on p.HOKAN = s2.BUMO
								 Where p.PACTVOL < p.KVOL
								 order by p.PORDER,p.PEDA,p.CODE");
        foreach ($ypics as $key => $y) {
        	array_push($ypics_params, [
        		'porder' => $y->porder,
        		'code' => $y->code,
        		'code_name' => $y->code_name,
        		'seiban' => $y->seiban,
        		'motoname' => $y->motoname,
        		'hokanname' => $y->hokanname,
        		'tani' => $y->tani,
        		'create_user' => Auth::user()->user_id,
        		'created_at' => date('Y-m-d H:i:s'),
        		'update_user' => Auth::user()->user_id,
        		'updated_at' => date('Y-m-d H:i:s'),
        	]);
        }

        $this->truncate('tbl_xpick');
        $count = count($ypics_params);
        $ypics_batch = array_chunk($ypics_params, 1000);
        foreach ($ypics_batch as $ybatch) {
            DB::connection($this->mysql)->table('tbl_xpick')->insert($ybatch);
        }

        // Insert uploaded XLSX data to Local DB
        foreach ($data as $key => $x) {
            $kvol = 0.0;
            if (isset($x['jitu0'])) {
                $kvol = $x['jitu0'];
            }

            if (isset($x['need'])) {
                $kvol = $x['need'];
            }

            if (isset($x['kvol'])) {
                $kvol = $x['kvol'];
            }

        	array_push($params,[
            	'porder' => (isset($x['porder']))? $x['porder'] : '',
            	'code' => (isset($x['code']))? $x['code'] : '',
            	'moto' => (isset($x['moto']))? $x['moto'] : '',
            	'hokan' => (isset($x['hokan']))? $x['hokan'] : '',
            	'seiban' => (isset($x['seiban']))? $x['seiban'] : '',
            	'beda' => (isset($x['beda']))? $x['beda'] : '',
            	'kvol' => $kvol,
            	'need' => $kvol,
            	'pickdate' => (isset($x['pickdate']))? $x['pickdate'] : '',
            	'lotname' => (isset($x['lotname']))? $x['lotname'] : '',
            	'tslip_num' => (isset($x['tslip_num']))? $x['tslip_num'] : '',
            	'note' => (isset($x['note']))? $x['note'] : '',
            	'created_at' => date('Y-m-d H:i:s'),
            	'create_user' => Auth::user()->user_id,
            	'updated_at' => date('Y-m-d H:i:s'),
            	'update_user' => Auth::user()->user_id,
            ]);
        }

        $this->truncate('tbl_dispatch_txpickjitu');
        $count = count($params);
        $insertBatchs = array_chunk($params, 1000);
        foreach ($insertBatchs as $batch) {
            DB::connection($this->mysql)->table('tbl_dispatch_txpickjitu')->insert($batch);
        }

        // Counter check uploaded data to txpick
        $checkdb = DB::connection($this->mysql)
        			->select("SELECT x.porder, 
        							d.code, 
        							d.moto, 
        							d.hokan, 
        							d.seiban, 
        							d.beda, 
        							d.kvol, 
        							d.need, 
        							d.pickdate, 
        							d.lotname, 
        							d.tslip_num, 
        							d.note, 
        							d.created_at,
        							d.create_user,
        							d.updated_at,
        							d.update_user
							FROM tbl_dispatch_txpickjitu d LEFT JOIN tbl_xpick x
							ON x.code = d.code"); // AND x.seiban = d.seiban"

        $cnt = 1;
        foreach ($checkdb as $key => $ck) {
        	if ($ck->porder == '' || $ck->porder == null) {
        		array_push($params_none, [
	        		'porder' => $ck->porder,
	            	'code' => $ck->code,
	            	'moto' => $ck->moto,
	            	'hokan' => $ck->hokan,
	            	'seiban' => $ck->seiban,
	            	'beda' => $ck->beda,
	            	'kvol' => $ck->kvol,
	            	'need' => $ck->need,
	            	'pickdate' => $ck->pickdate,
	            	'lotname' => $ck->lotname,
	            	'tslip_num' => $ck->tslip_num,
	            	'note' => $ck->note,
	            	'created_at' => date('Y-m-d H:i:s'),
	            	'create_user' => Auth::user()->user_id,
	            	'updated_at' => date('Y-m-d H:i:s'),
	            	'update_user' => Auth::user()->user_id,
	        	]);
        	} else {
	        	// DB::connection($this->mysql)->table('tbl_dispatch_txpickjitu')
	        	// 	->where('id',$cnt)
	         //    	->update([
		        // 		'porder' => $ck->porder,
		        //     	'updated_at' => date('Y-m-d H:i:s'),
		        //     	'update_user' => Auth::user()->user_id,
		        // 	]);
                array_push($params_update, [
                    'porder' => $ck->porder,
                    'code' => $ck->code,
                    'moto' => $ck->moto,
                    'hokan' => $ck->hokan,
                    'seiban' => $ck->seiban,
                    'beda' => $ck->beda,
                    'kvol' => $ck->kvol,
                    'need' => $ck->need,
                    'pickdate' => $ck->pickdate,
                    'lotname' => $ck->lotname,
                    'tslip_num' => $ck->tslip_num,
                    'note' => $ck->note,
                    'created_at' => date('Y-m-d H:i:s'),
                    'create_user' => Auth::user()->user_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'update_user' => Auth::user()->user_id,
                ]);
        	}
            $cnt++;
        }

        $this->truncate('tbl_dispatch_txpickjitu_none');
        $count = count($params_none);
        $ckInsertBatch = array_chunk($params_none, 1000);
        foreach ($ckInsertBatch as $ckbatch) {
            DB::connection($this->mysql)->table('tbl_dispatch_txpickjitu_none')->insert($ckbatch);
        }

        $this->truncate('tbl_dispatch_txpickjitu');
        $count = count($params_update);
        $updateBatch = array_chunk($params_update, 1000);
        foreach ($updateBatch as $upBatch) {
            DB::connection($this->mysql)->table('tbl_dispatch_txpickjitu')->insert($upBatch);
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
        Excel::create('TXPICKJITU', function($excel) {
            $excel->sheet('TXHIKI', function($sheet) {
            	$sheet->cell('A1', "PORDER");
				$sheet->cell('B1', "CODE");
				$sheet->cell('C1', "MOTO");
				$sheet->cell('D1', "HOKAN");
				$sheet->cell('E1', "SEIBAN");
				$sheet->cell('F1', "BEDA");
				$sheet->cell('G1', "JITU0");
				$sheet->cell('H1', "NEED");
				$sheet->cell('I1', "PICKDATE");
				$sheet->cell('J1', "LOTNAME");
				$sheet->cell('K1', "TSLIP_NUM");
				$sheet->cell('L1', "NOTE");

                $sheet->setColumnFormat([
                    'G' => '0.0000',
                ]);

                $ypics = DB::connection($this->mysql)->table('tbl_dispatch_txpickjitu')
                			->where('porder','<>','')
                			->get();

                $row = 2;
                //sort($allProd);
                foreach ($ypics as $key => $x) {
                    $sheet->cell('A'.$row, $x->porder);
					$sheet->cell('B'.$row, $x->code);
					$sheet->cell('C'.$row, $x->moto);
					$sheet->cell('D'.$row, $x->hokan);
					$sheet->cell('E'.$row, $x->seiban);
					$sheet->cell('F'.$row, $x->beda);
					$sheet->cell('G'.$row, $x->kvol);
					$sheet->cell('H'.$row, $x->need);
					$sheet->cell('I'.$row, $x->pickdate);
					$sheet->cell('J'.$row, $x->lotname);
					$sheet->cell('K'.$row, $x->tslip_num);
					$sheet->cell('L'.$row, $x->note);
                    $row++;
                }
            });
        })->download('xlsx');
    }

    public function checkData()
    {
        $data = ['status' => 'failed'];
        $count = DB::connection($this->mysql)->table('tbl_dispatch_txpickjitu')
        			->where('porder','<>','')
        			->count();
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

    public function getDispatchData()
    {
    	$db = DB::connection($this->mysql)->table('tbl_dispatch_txpickjitu_none')
                ->groupBy('code')
    			->select(['id',
    					'porder',
						'code',
						'moto',
						'hokan',
						'seiban',
						'beda',
						'kvol',
						'need',
						'pickdate',
						'lotname',
						'tslip_num',
						'note',
						'created_at',
						'create_user',
						'updated_at',
						'update_user']);
    	return Datatables::of($db)->make(true);

    }
}
