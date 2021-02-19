<?php
namespace App\Http\Controllers\Phase2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use App\pr_orig;
use App\pr_change;
use Carbon\Carbon;
use Config;
use Excel;
use DB;

class PRChangeController extends Controller
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


    public function getPRChange()
    {
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PRCHANGE'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('phase2.PRChange',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function postOrigPR(Request $request)
    {
        // for getting extension getClientOriginalExtension()
        // for getting path getPathName();
        // get the mime type getMimeType()
        // get the max file size set from php.ini getMaxFilesize()

        $pr_orig = $request->file('originalpr');

		if (empty($pr_orig)) {
			$message = "No file was uploaded.";
			return redirect(url('/prchange'))->with(['prorig_modal' => $message]);
		} else {
			if ($pr_orig->getClientOriginalExtension() == 'xls' || $pr_orig->getClientOriginalExtension() == 'XLS') {
	            try {
                    $this->truncateTable('pr_orig');
                    $this->truncateTable('pr_orig_bu2');

                    $ok = Excel::load($pr_orig, function ($reader) use($pr_orig) {
                            $reader->formatDates(false);

                            $common = new CommonController;
                            $db = $common->userDBconFromStr($pr_orig->getClientOriginalName());

                            foreach ($reader->toArray() as $key => $col) {

                                $xhead = DB::connection($this->mssql)
                                        ->table('XHEAD')
                                        ->select('code', 'name')
                                        ->where('code', '=', $col['code'])
                                        ->first();

                                //Insert xls file data to pr_orig(MYSQL TABLE)
                                if (array_key_exists("salesno",$col) && $this->checkIfExistObject($xhead) > 0) {
                                    $this->insertDataToOrigPR($col, $xhead);
                                }
                            }
                        });
                    if ($ok == true) {
                        $message = "TS PR data imported successfully.";
                        return redirect(url('/prchange'))->with(['prorig_modal' => $message]);
                    } else {
                        $message = "TS PR data imported failed.";
                        return redirect(url('/prchange'))->with(['prorig_modal' => $message]);
                    }

                } catch (Exception $e) {

                }
	        } else {
                $message = "File must be .xls file.";
                return redirect(url('/prchange'))->with(['err_message' => $message]);
	        }
		}

    }

    private function checkIfExistObject($object)
    {
       return count( (array)$object);
    }

    public function postChangePR(Request $request)
    {
        // for getting extension getClientOriginalExtension()
        // for getting path getPathName();
        // get the mime type getMimeType()
        // get the max file size set from php.ini getMaxFilesize()

        $pr_change = $request->file('changepr');

		if (empty($pr_change)) {
			$message = "No file was uploaded.";
			return redirect(url('/prchange'))->with(['prchange_modal' => $message]);
		} else {
			if ($pr_change->getClientOriginalExtension() == 'xls' || $pr_change->getClientOriginalExtension() == 'XLS') {
	            try {
                    $this->truncateTable('pr_change');
                    $this->truncateTable('pr_change_work_bu2');
                    $this->truncateTable('pr_moq_exess_summary_bu2');
                    $db = 'sqlsrvbu';

                    $ok = Excel::load($pr_change, function ($reader) use($pr_change) {
                            $reader->formatDates(false);
                            foreach ($reader->toArray() as $key => $col) {

                                $common = new CommonController;
                                $db = $common->userDBconFromStr($pr_change->getClientOriginalName());

                                // foreach ($reader->toArray() as $key => $col) {

                                    $xhead = DB::connection($this->mssql)
                                            ->table('XHEAD')
                                            ->select('code', 'note')
                                            ->where('code', '=', $col['code'])
                                            ->first();

                                //Insert xls file data to pr_orig(MYSQL TABLE)
                                //if ($col[''] == '*') {
                                if ($this->checkIfExistObject($xhead) > 0) {
                                    $this->insertDataToChangePR($col, $xhead);
                                }
                                    
                                //}
                               // }
                           }
                        });
                    if ($ok == true) {

                        $this->preparePROutput($db);
                        //$this->PR_output();
                        $message = "PR Change imported successfully";
                        return redirect(url('/prchange'))->with(['prchange_modal' => $message,'download'=>'download']);
                    } else {
                        $message = "PR Change imported failed";
                        return redirect(url('/prchange'))->with(['prchange_modal' => $message]);
                    }

                } catch (Exception $e) {

                }
	        } else {
                $message = "File must be .xls file.";
                return redirect(url('/prchange'))->with(['err_message' => $message]);
	            
	        }
		}

    }

    private function truncateTable($table)
    {
        return DB::connection($this->mysql)->table($table)->truncate();
    }

    private function insertDataToOrigPR($data, $xhead)
    {
        try {

            $po = substr($data['purchaseorderno'], 16);
            $ponum = substr($po, 0,-4);

            DB::connection($this->mysql)->table('pr_orig')
                ->insert([
                    'sales_no' => $data['salesno'],
                    'sales_type' => $data['salestype'],
                    'sales_org' => $data['salesorg'],
                    'commercial' => $data['commercial'],
                    'sales_org' => $data['section'],
                    'sales_branch' => $data['salesbranch'],
                    'sales_g' => $data['salesg'],
                    'supplier' => $data['supplier'],
                    'destination' => $data['destination'],
                    'player' => $data['payer'],
                    'assistant' => $data['assistant'],
                    'po_num' => $ponum,
                    'issued_date' => $data['issuedate'],
                    'flight_need_date' => $data['flightneeddate'],
                    'headertext' => $data['headertext'],
                    'pcode' => $data['code'],
                    'itemtext' => $data['itemtext'],
                    'orderqty' => $data['orderquantity'],
                    'unit' => $data['unit']
                    ]);

            DB::connection($this->mysql)->table('pr_orig_bu2')
            ->insert([
                'pr'         => substr($data['purchaseorderno'], 16,12),
                'issuedate' => $data['issuedate'],
                'code'       => $data['code'],
                'partname'   => $xhead->name,
                'ordqty'     => $data['orderquantity']
                ]);

        } catch (Exception $e) {

        }
    }

    private function insertDataToChangePR($data, $xhead)
    {
        try {
            $po = substr($data['purchaseorderno'], 16);
            $ponum = substr($po, 0,-4);

            DB::connection($this->mysql)->table('pr_change')
                ->insert([
                    'sales_no' => $data['salesno'],
                    'sales_type' => $data['salestype'],
                    'sales_org' => $data['salesorg'],
                    'commercial' => $data['commercial'],
                    'sales_org' => $data['section'],
                    'sales_branch' => $data['salesbranch'],
                    'sales_g' => $data['salesg'],
                    'supplier' => $data['supplier'],
                    'destination' => $data['destination'],
                    'player' => $data['payer'],
                    'assistant' => $data['assistant'],
                    'po_num' => $ponum,
                    'issued_date' => $data['issuedate'],
                    'flight_need_date' => $data['flightneeddate'],
                    'headertext' => $data['headertext'],
                    'pcode' => $data['code'],
                    'itemtext' => $data['itemtext'],
                    'orderqty' => $data['orderquantity'],
                    'unit' => $data['unit'],
                    'classification' => $data['note']
                    ]);

            DB::connection($this->mysql)->table('pr_change_work_bu2')
            ->insert([
                'orderno'    => substr($data['purchaseorderno'], 16,12),
                'issueddate' => $data['issuedate'],
                'code'       => $data['code'],
                'newqty'     => $data['orderquantity'],
                'bikou'      => $xhead->note
                ]);

        } catch (Exception $e) {

        }
    }

    public function PR_output()
    {
        /*$output = DB::connection($this->mysql)->table('pr_orig as po')
                    ->join('pr_change as pc','po.po_num','=','pc.po_num')
                    ->select(
                        DB::raw('po.po_num as OrderNo'),
                        DB::raw('po.issued_date as issuedate'),
                        DB::raw('po.pcode as CODE'),
                        DB::raw('po.orderqty as OriginalQty'),
                        DB::raw('sum(pc.orderqty) as NewQty'),
                        DB::raw('sum(pc.orderqty) - po.orderqty as MOQExcess')
                    )
                    ->groupBy('pc.po_num')
                    ->get();

        $msDB = [];
        foreach ($output as $key => $mysql) {
            $check = DB::connection('sqlsrvbu')
                        ->table('XHEAD as h')
                        ->join('XTANK as u','h.CODE','=','u.CODE')
                        ->select('h.NAME','u.PRICE','h.CODE')
                        ->where('h.CODE',$mysql->CODE)
                        ->get();
            $msDB[] = $check;
            //echo "<pre>",print_r($check),"</pre>";
        }
        */

        try {
            $data = [];
            $output = DB::connection($this->mysql)->table('pr_moq_exess_summary_bu2')
                    ->select(
                        DB::raw('orderno as OrderNo'),
                        DB::raw('issuedate as issuedate'), 
                        DB::raw('code as CODE'),
                        DB::raw("partname as 'Part Name'"),
                        DB::raw('unitprice as UnitPrice'),
                        DB::raw('originalqty as OriginalQty'),
                        DB::raw('newqty as NewQty'),
                        DB::raw("moqexcess as 'MOQ Excess'"),
                        DB::raw('amount as Amount'),
                        DB::raw('category as BIKOU')
                        )
                    ->get();

            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $time = $dt->format('his');
            $path = public_path().'/PR_Change_Output/';

            // # convert the object result to array readable format.
            foreach ($output as $datareport) 
            {
                $data[] = (array)$datareport;
                #or first convert it and then change its properties using 
                #an array syntax, it's up to you
            }

            // File::makeDirectory($path, 0777, true, true);
            // if (!File::exists($path)) {
            //     File::makeDirectory($path, 0777, true, true);
            // }

            /* Excel::create('PR_Change_'.$date, function($excel) use($output,$msDB){
                $excel->sheet('PR_Change_Output', function($sheet) use($output,$msDB){
                    $sheet->cell('A1', "OrderNo");
                    $sheet->cell('B1', "issuedate");
                    $sheet->cell('C1', "CODE");
                    $sheet->cell('D1', "Part Name");
                    $sheet->cell('E1', "UnitPrice");
                    $sheet->cell('F1', "OriginalQty");
                    $sheet->cell('G1', "NewQty");
                    $sheet->cell('H1', "MOQ Excess");
                    $sheet->cell('I1', "Amount");
                    $sheet->cell('J1', "BIKOU");

                    $row = 2;
                    $cnt = 0;
                    foreach ($msDB as $key => $ms) {
                        if (isset($ms[0])) {
                            $amt = $ms[0]->PRICE * $output[$cnt]->MOQExcess;
                            if ($amt == 0) {
                                $amt = "0.0";
                            }
                            if ($output[$cnt]->MOQExcess == 0) {
                                $output[$cnt]->MOQExcess = "0.0";
                            }

                            $sheet->cell('A'.$row, $output[$cnt]->OrderNo);
                            $sheet->cell('B'.$row, $output[$cnt]->issuedate);
                            $sheet->cell('C'.$row, $output[$cnt]->CODE);
                            $sheet->cell('D'.$row, $ms[0]->NAME);
                            $sheet->cell('E'.$row, $ms[0]->PRICE);
                            $sheet->cell('F'.$row, $output[$cnt]->OriginalQty);
                            $sheet->cell('G'.$row, $output[$cnt]->NewQty);
                            $sheet->cell('H'.$row, $output[$cnt]->MOQExcess);
                            $sheet->cell('I'.$row, $amt);
                            $sheet->cell('J'.$row, "");
                            $row++;
                            $cnt++;
                        }

                    }
                });
            })->store('xls',$path);*/

            # Create and export excel by feeding the array result.
            Excel::create('PR_Change_' . $date, function($excel) use($data) 
            {

                $excel->sheet('PR_Change_Output', function($sheet) use($data) 
                {
                    $sheet->fromArray($data);
                });

            #download and save the excel file.
            })->download('xls'); //,$path

        } catch (Exception $e) {
            return redirect(url('/prchange'))->with(['err_message' => $e]);
        }
    }

    private function preparePROutput($db)
    {
        try
        {
            /* $moqexcess = DB::connection($this->mysql)->select("
                SELECT pc.po_num, 
                    pob.partname, 
                    pob.ordqty, 
                    pcwb.NewQty, 
                    (NewQty-pob.ordqty) AS 'MOQExcess', 
                    (NewQty-pob.ordqty) AS Amount, 
                    pcwb.BIKOU
                FROM pr_change AS pc 
                INNER JOIN pr_orig_bu2 AS pob ON pc.po_num = pob.PR
                INNER JOIN (
                    SELECT po_num AS OrderNo,
                        Change_BU2_YEC.issued_date, 
                        Change_BU2_YEC.pcode, 
                        SUM(Change_BU2_YEC.orderqty) AS NewQty, 
                        c.BIKOU
                    FROM pr_change Change_BU2_YEC 
                    INNER JOIN pr_change_work_bu2 c ON Change_BU2_YEC.pcode = c.CODE
                    GROUP BY po_num, 
                        Change_BU2_YEC.issued_date, 
                        Change_BU2_YEC.pcode, 
                        c.BIKOU
                ) AS pcwb ON pc.po_num = pcwb.OrderNo
                WHERE pc.classification IS NOT NULL");*/

             $moqexcess = DB::connection($this->mysql)->select("
                SELECT prc.po_num,
                    pob.partname, 
                    pob.ordqty, 
                    pc.NewQty, 
                    (pc.NewQty-pob.ordqty) AS 'MOQExcess', 
                    (pc.NewQty-pob.ordqty) AS Amount,
                    (SELECT BIKOU FROM pr_change_work_bu2 c WHERE BIKOU IS NOT NULL AND c.orderno = pc.po_num LIMIT 1) AS BIKOU
                FROM (SELECT DISTINCT po_num 
                    FROM pr_change 
                    WHERE classification IS NOT NULL
                    -- AND po_num = 'PR1620017689'
                    ) prc
                INNER JOIN pr_orig_bu2 pob ON prc.po_num = pob.pr
                INNER JOIN 
                (SELECT po_num, 
                    SUM(orderqty) AS NewQty
                FROM pr_change
                GROUP BY po_num) pc ON pc.po_num = prc.po_num");

            foreach ($moqexcess as $key => $data) 
            {
                $xslip = DB::connection($this->mssql)->table('XSLIP')
                        ->select('ddate','code','price','porder')
                        ->where('porder', '=', $data->po_num)
                        ->get();

            if(isset($xslip[0]))
            {
                DB::connection($this->mysql)->table('pr_moq_exess_summary_bu2')
                    ->insert([
                        'category'    => $data->BIKOU,
                        'orderno'     => $data->po_num,
                        'issuedate'   => date('Y/m/d',strtotime(substr($xslip[0]->ddate, 0,8))), //Left([issuedate],4) & "/" & Mid([issuedate],5,2) & "/" & Right([issuedate],2)
                        'ym'          => date('Y-m',strtotime(substr($xslip[0]->ddate, 0,8))), //Left([issuedate],4) & "-" & Mid([issuedate],5,2)
                        'code'        => $xslip[0]->code,
                        'partname'    => $data->partname,
                        'unitprice'   => $xslip[0]->price,
                        'originalqty' => $data->ordqty,
                        'newqty'      => $data->NewQty,
                        'moqexcess'   => $data->MOQExcess,
                        'amount'      => $data->Amount * $xslip[0]->price
                        ]);

                }
            }

        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
    }
}
