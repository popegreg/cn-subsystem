<?php
namespace App\Http\Controllers\SSS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Config;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use App\ts_zypf0090;
use Illuminate\Support\Facades\Redirect;
use Excel;

class DataUpdateController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;

    public function __construct()
    {
        $this->middleware('auth');
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'mrp');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getDataUpdate()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_DATUPD'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('sss.DataUpdate',['userProgramAccess' => $userProgramAccess]);
        }
    }

    //INSERT TEXT FILE DATA TO tempzymr0120 (MYSQL TABLE)
    public function postPartsAnswer(Request $req)
    {
        $isogi = $req->file('partsanswerfile');
        //VALIDATE FILE
        try {
            if (strpos($isogi->getClientOriginalName(), 'ISOGI_ZYMR0120') !== false) {
                $counter = 0;
                //GET AND SPLIT DATA
                foreach(preg_split("/((\r?\n)|(\r\n?))/", $this->getFileContent($isogi)) as $line) {
                    //CHANGE isDelete COLUMN TO TRUE
                    if($counter == 0) {
                        $this->softDelete('tempzymr0120');
                    } elseif($counter > 0) {
                        $LinesArray = explode("\t", $line);
                        
                        // INSERT
                        $this->insertDataToZymr0120($LinesArray);

                        /*// UPDATE tbl_isogi_input Table
                        $this->update_Tbl_Isogi_Input_Table($LinesArray);*/
                    }

                    $counter++;
                }
                return response()->json(array('status'=>1),200);
            } else {
                return response()->json(array('status'=>0),200);
            }
        } catch (Exception $e) {
            return response()->json(array('status'=>$e->getMessage()),200);
        }
        
    }


    public function post_mrp_and_r3answer(Request $req)
    {
        if ($req->hasFile('mrpdata') || $req->hasFile('r3answer')) {
            $mrpdata = $req->file('mrpdata');
            $r3answer = $req->file('r3answer');

            ini_set('max_execution_time', 0);
            // return $mrpdata;

            //VALIDATE FILE
             if (strpos($mrpdata->getClientOriginalName(), 'MRP') != false && strpos($r3answer->getClientOriginalName(), 'ZYPF0090') != false) 
             {
                 try {
                     //CHANGE isDelete COLUMN TO TRUE
                     if (is_readable($mrpdata)) 
                     {

                         $this->softDelete('temp_sss_mrplist');
                         $this->softDelete('temp_sss_deliverystatus');
                             
                         Excel::load($mrpdata, function ($reader) use ($mrpdata)
                         {
                             $reader->formatDates(false);
                             $xcel = $reader->toArray();

                             //q_List
                             foreach ($xcel[0] as $key => $row) 
                             {
                                 //INSERT TEXT FILE DATA TO temp_sss_mrplist (MYSQL TABLE)                                
                                $this->insertDataToMrp($row, $mrpdata->getClientOriginalName());
                               
                             }

                             //q_TheoreticalKitDate
                             foreach ($xcel[1] as $key => $row) 
                             {
                                 //INSERT TEXT FILE DATA TO temp_sss_deliverystatus (MYSQL TABLE)                                
                                $this->insertDataToDeliveryStatus($row);
                               
                             }
                         });
                     } else {
                         return response()->json(array('status'=>0),200);
                     }
                    
                 } catch (Exception $e) {
                     return response()->json(array('status'=>$e->getMessage()),200);
                 }
        
                 $counter = 0;

                 foreach(preg_split("/((\r?\n)|(\r\n?))/", $this->getFileContent($r3answer)) as $lines) 
                 {
                     if($counter == 0) 
                     {
                         $this->softDelete('ts_zypf0090');
                         $this->softDelete('temp_sss_prdanswer');                         
                     } elseif($counter > 0) {
                         $LinesArray = explode("\t", $lines);
                         //INSERT TEXT FILE DATA TO ts_zypf0090 (MYSQL TABLE)
                         $this->insertDataToR3answer($LinesArray);

                         // UPDATE temp_prodanswer_data Table
                         $this->updateTempProdAnswerDataTable($LinesArray);

                     }

                     $counter++;
                 }

                 $this->updateTempMrpListCountId();
                 $this->importReasonMaster($mrpdata->getClientOriginalName());

                 return response()->json(array('status'=>1),200);
             } else {
                return response()->json(array('status'=>0),200);
             }
        } else {
            return response()->json(array('status'=>0),200);
        }
    }


    public function update_Tbl_Isogi_Input_Table($i)
    {
        DB::connection($this->mysql)->table("tbl_isogi_input")
        ->where('yec_po', '=', $i[5])
        ->where('code', '=', $i[1])
        ->update(array('name' => mb_convert_encoding($i[6], 'UTF-8', 'Shift-JIS'),'supplier' => mb_convert_encoding($i[4], 'UTF-8', 'Shift-JIS')));
    }

    public function updateTempProdAnswerDataTable($i)
    {
        if (!empty($i[0])) {
            DB::connection($this->mysql)->table("temp_prodanswer_data")
            ->where('pcode', '=', $i[1])
            ->where('po', '=', $i[3])
            ->update(array('pname' => $i[2],'qty' => $i[5],'time' => $i[8]));
        }
    }

    public function getFileContent($file)
    {
            //$f = Input::file($file);
            return File::get($file);
    }

    public function softDelete($table)
    {
        DB::connection($this->mysql)->table($table)->truncate();
        // ->where('isDeleted', '=', "false")
        // ->update(array('isDeleted' => "true"));  // update the record in the DB. 
    }

    //GET THE LAST DATE UPDATED
    public function getFileDate()
    {
        return json_encode( DB::connection($this->mysql)->select( DB::raw("SELECT IF((SELECT created_at FROM tempzymr0120 ORDER BY created_at DESC LIMIT 1) > (SELECT created_at FROM ts_mrp ORDER BY created_at DESC LIMIT 1), (SELECT created_at FROM tempzymr0120 ORDER BY created_at DESC LIMIT 1), (SELECT created_at FROM ts_mrp ORDER BY created_at DESC LIMIT 1)) AS created_at") ));
    }

    public function insertDataToMrp($data, $filename)
    {
        //echo "<pre>",print_r($i),"</pre>";
        try {

            if (isset($data['mcode']) && isset($data['mname']) && isset($data['schdqty'])) {

                //qimp_timp_PrdAnswer_PPS
                if($data['vendor'] == 'PPD' 
                    && (strpos(mb_convert_encoding($data['mname'], 'UTF-8', 'Shift-JIS'), 'A1') 
                        || strpos(mb_convert_encoding($data['mname'], 'UTF-8', 'Shift-JIS'), 'KS')
                        || strpos(mb_convert_encoding($data['mname'], 'UTF-8', 'Shift-JIS'), 'AC')))
                {
                    $data['supcode'] = "Y016000";
                    $data['supname'] = "PPS GRINDING";
                }

                DB::connection($this->mysql)->table('temp_sss_mrplist')
                    ->insert([
                        'mcode'         => $data['mcode'],
                        'mname'         => $data['mname'],//mb_convert_encoding($data['mname'], 'UTF-8', 'Shift-JIS'),
                        'vendor'        => $data['vendor'],
                        'assy100'       => $data['assy100'],
                        'assy102'       => $data['assy102'],
                        'whs100'        => $data['whs100'],
                        'whs102'        => $data['whs102'],
                        'whs106'        => $data['whs106'],
                        'whssm'         => $data['whssm'],
                        'whsnon'        => $data['whsnon'],
                        'ttlcurrinvtry' => $data['ttlcurrinvtry'],
                        'orddate'       => $data['orddate'],
                        'po'            => substr($data['po'], 0, 10),
                        'dcode'         => $data['dcode'],
                        'dname'         => substr($data['dname'], 0, strpos($data['dname'], ' - ')),
                        'orderbal'      => $data['orderbal'],
                        'duedate'       => $data['duedate'],
                        'orderqty'      => $data['orderqty'],
                        'schdqty'       => $data['schdqty'],
                        'balreq'        => $data['balreq'],
                        'ttlbalreq'     => $data['ttlbalreq'],
                        'reqaccum'      => $data['reqaccum'],
                        'ttlprbal'      => $data['ttlpr_bal'],
                        'prissued'      => $data['pr_issued'],
                        'pr'            => $data['pr'],
                        'yecpo'         => $data['yec_po'],
                        'yecpu'         => $data['yec_pu'],
                        'flight'        => $data['flight'],
                        'deliqty'       => $data['deliqty'],
                        'deliaccum'     => $data['deliaccum'],
                        'check'         => $data['check'],
                        'supcode'       => $data['supcode'],
                        'supname'       => $data['supname'],//mb_convert_encoding($data['supname'],'UTF-8', 'Shift-JIS'),
                        'incharge'      => Auth::user()->user_id,
                        're'            => $data['re'],
                        'status'        => $data['status'],
                        'filedate'      => substr($filename, strpos($filename, 'MRP') + 4, 8),
                        'custcode'      => $data['custcode'],
                        'custname'      => $data['custname'],//mb_convert_encoding($data['custname'],'UTF-8', 'Shift-JIS'),
                        'tsmdate'       => substr($filename, strpos($filename, 'MRP') + 4, 8),
                        'alloccalc'     => $data['alloccalc'],
                        'mrp'           => ($data['mrp'] == NULL)? '0.0':$data['mrp']
                        ]);

                /*DB::connection($this->mysql)->table('ts_mrp')->insert([
                   'mcode' => $data['mcode'],
                   'mname' => mb_convert_encoding($data['mname'], 'UTF-8', 'Shift-JIS'),
                   'vendor' => mb_convert_encoding($data['vendor'], 'UTF-8', 'Shift-JIS'),
                   'assy100' => ($data['assy100'] == null)? "0":$data['assy100'],
                   'assy102' => ($data['assy102'] == null)? "0":$data['assy102'],
                   'whs100' =>  ($data['whs100'] == null)? "0":$data['whs100'],
                   'whs102' =>  ($data['whs102'] == null)? "0":$data['whs102'],
                   'whs106' =>  ($data['whs106'] == null)? "0":$data['whs106'],
                   'whs_sm' =>  ($data['whs_sm'] == null)? "0":$data['whs_sm'],
                   'whs_non' => ($data['whs_non'] == null)? "0":$data['whs_non'],
                   'ttlcurrinvtry' => ($data['ttlcurrinvtry'] == null)? "0":$data['ttlcurrinvtry'],
                   'orddate' => $data['orddate'],
                   'duedate' => $data['duedate'],
                   'po' => ($data['po'] == null)? "0":$data['po'],
                   'dcode' => $data['dcode'],
                   'dname' => mb_convert_encoding($data['dname'], 'UTF-8', 'Shift-JIS'),
                   'orderqty' => ($data['orderqty'] == null)? "0":$data['orderqty'],
                   'orderbal' => ($data['orderbal'] == null)? "0":$data['orderbal'],
                   'custcode' => ($data['custcode'] == null)? "0":$data['custcode'],
                   'custname' => mb_convert_encoding($data['custname'], 'UTF-8', 'Shift-JIS'),
                   'schdqty' => ($data['schdqty'] == null)? "0":$data['schdqty'],
                   'balreq' => ($data['balreq'] == null)? "0":$data['balreq'],
                   'ttlbalreq' => ($data['ttlbalreq'] == null)? "0":$data['ttlbalreq'],
                   'reqaccum' => ($data['reqaccum'] == null)? "0":$data['reqaccum'],
                   'alloccalc' => ($data['alloccalc'] == null)? "0":$data['alloccalc'],
                   'ttlpr_bal' => ($data['ttlpr_bal'] == null)? "0":$data['ttlpr_bal'],
                   'mrp' => ($data['mrp'] == null)? "0":$data['mrp'],
                   'pr_issued' => $data['pr_issued'],
                   'pr' => $data['pr'],
                   'yec_po' => $data['yec_po'],
                   'yec_pu' => $data['yec_pu'],
                   'flight' => $data['flight'],
                   'deliqty' => $data['deliqty'],
                   'deliaccum' => $data['deliaccum'],
                   'check' => $data['check'],
                   'supcode' => $data['supcode'],
                   'supname' => mb_convert_encoding($data['supname'], 'UTF-8', 'Shift-JIS'),
                   're' => $data['re'],
                   'status' => $data['status'],
                   'isDeleted' => "false"
                ]);*/
            }

        } catch (Exception $e) {
            return response()->json(array('status'=>$e->getMessage()),200);
        }
    }

    public function insertDataToDeliveryStatus($data)
    {
        try
        {

            DB::connection($this->mysql)->table('temp_sss_deliverystatus')
                ->insert([
                    'po'        => substr($data['po'], 0,10),
                    'dcode'     => $data['dcode'],
                    'dname'     => substr($data['dname'], 0, strpos($data['dname'], ' - ')),
                    'ordqty'    => $data['ordqty'],
                    'ordbal'    => $data['ordbal'],
                    'orddate'   => $data['orddate'],
                    'duedate'   => $data['duedate'],
                    'custcode'  => $data['custcode'],
                    'custname'  => $data['custname'],
                    'procy'     => $data['procy'],
                    'maxpud_y'  => $data['maxpud_y'],
                    'stock_y'   => $data['stock_y'],
                    'nosched_y' => $data['nosched_y'],
                    'yec'       => $data['yec'],
                    'procp'     => $data['procp'],
                    'maxpud_p'  => $data['maxpud_p'],
                    'stock_p'   => $data['stock_p'],
                    'nosched_p' => $data['nosched_p'],
                    'pmi'       => $data['pmi'],
                    'check'     => $data['check']
                ]);            
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
    }

    public function insertDataToR3answer($i)
    {
        try {
            if (!empty($i[0])) {
               //echo "<pre>",print_r($i),"</pre>";

                DB::connection($this->mysql)->table('ts_zypf0090')
                    ->insert([
                        'item_code' => $i[1],
                        'item_text' => $i[2],
                        'product_purchase_order' => $i[3],
                        'item_number' => $i[4],
                        'purchase_order_quantity' => $i[5],
                        'statistical_delivery_date' => $i[6],
                        'purchasing_delivery_date' => $i[7],
                        'current_answer_time' => $i[8],
                        'sales_order' => $i[9],
                        'sales_order_specification' => $i[10],
                        'proposed_response_date' => $i[11],
                        'proposed_answer_time' => $i[12],
                        'answer_quantity' => $i[13],
                        'supplier_sector' => $i[14],
                        'mrp_administrator' => $i[15],
                        'issuing_storage_location' => $i[16],
                        'planned_order_number' => $i[17],
                        'production_orders' => $i[18],
                        'purchase_order_number' => $i[19],
                        'specification' => $i[20],
                        'required_date' => $i[21],
                        'proposed_division' => $i[22],
                        'last_proposed_change_classification' => $i[23],
                        'inventory_provisions_have_classification' => $i[24],
                        'lock_change_classification' => $i[25],
                        'vendor_code' => $i[26],
                        'complete_po' => $i[3].$i[4],
                        'isDeleted' => "false"
                        ]);

                DB::connection($this->mysql)->table('temp_sss_prdanswer')
                ->insert([
                    'pcode'    => $i[1],
                    'pname'    => $i[2],
                    'po'       => $i[3],
                    'qty'      => $i[5],
                    'r3answer' => $i[6],
                    'time'     => $i[8]
                    ]);
            }
            
        } catch (Exception $e) {
            return response()->json(array('status'=>$e->getMessage()),200);
        }
    }



    public function insertDataToZymr0120($i)
    {
        try {
            // echo "<pre>",print_r($i),"</pre>";
            if (isset($i[0]) && isset($i[1])) {

                // echo "<pre>",print_r($i),"</pre>";
                if(isset($i[19]) == false)
                { 
                    $i[19] = date("Y-m-d",strtotime(date("Y-m-d", strtotime($i[17])) . " +1 year")); 
                }

                DB::connection($this->mysql)->table('tempzymr0120')->insert([
                    'mrp_manager' => $i[0],
                    'purchasing_group' => (isset($i[1]))? $i[1] : "",
                    'order_date' => (isset($i[2]))? $i[2] : "",
                    'vendor' => $i[3],
                    'vendor_name' => mb_convert_encoding($i[4], 'UTF-8', 'Shift-JIS'),
                    'itemcode' => $i[5],
                    'itemname' => mb_convert_encoding($i[6], 'UTF-8', 'Shift-JIS'),
                    'po' => $i[7],
                    'itemno' => $i[8],
                    'drawing_num' => $i[9],
                    'unit' => $i[10],
                    'qty' => $i[11],
                    'num_of_residuals' => $i[12],
                    'currency' => $i[13],
                    'unit_price' => $i[14],
                    'order_amount' => $i[15],
                    'order_the_remaining_amount_of_money' => $i[16],
                    'specify_period' => $i[17],
                    'first_sector' => $i[18],
                    'ans_satisfied_period' => $i[19],
                    'answer_time' => $i[20],
                    'num_response' => $i[22],
                    'library' => $i[23],
                    'reason' => $i[24],
                    'loan' => $i[25],
                    'project' => $i[26],
                    'text' => $i[27],
                    'asset_num' => $i[28],
                    'asset_aux_num' => $i[29],
                    'supplied_prod_code' => $i[30],
                    'payment_good_text' => $i[31],
                    'surface_designation' => $i[32],
                    'configuration' => $i[33],
                    'customer_id' => "", //nawawala
                    'company_name' => "", //nawawala
                    'order_approval_date' => $i[34],
                    'order_approval_time' => $i[35],
                    'answer_force_to_pay' => $i[36],
                    'answer_force_moment' => $i[37],
                    'policy_group' => $i[38],
                    'shipment_text' => $i[39],
                    'complete_po' =>$i[7].$i[8],
                    'isDeleted' => "false"
                ]);
            }
            
            
        } catch (Exception $e) {
            return response()->json(array('status'=>$e->getMessage()),200);
        }
    }

    private function updateTempMrpListCountId()
    {
        try
        {
            DB::connection($this->mysql)->statement("
                    UPDATE temp_sss_mrplist AS m
                    INNER JOIN (
                          SELECT  @row_num := IF(@prev_value=o.mcode,@row_num+1,1) AS rowno
                                 ,o.po 
                                 ,o.mcode
                                 ,o.deliaccum
                                 ,@prev_value := o.mcode
                                 ,o.id
                            FROM temp_sss_mrplist o,
                                 (SELECT @row_num := 1) X,
                                 (SELECT @prev_value := '' COLLATE utf8_unicode_ci) Y
                           ORDER BY o.po, o.mcode, o.deliaccum ASC
                    ) AS r ON m.id = r.id
                    SET m.countid = r.rowno");
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
    }

    private function importReasonMaster()
    {
        try
        {
            $common = new CommonController;
            $mysql_con = $common->getDatabaseNameByDbCon($this->mysql);
            $common_con = $common->getDatabaseNameByDbCon($this->common);

            $memo = DB::connection($this->mysql)->select("
                    SELECT qexp_SampleDoujiProduct_work.PO, 
                        Re & ' : ' & qexp_SampleDoujiProduct_work.name AS STATUS, 
                        NOW() AS `DATE`, 
                        'DefaultUpdate' AS NAME
                    FROM (
                        SELECT timp_MRP_List.PO, 
                            timp_MRP_List.Re, 
                            `mtbl_Die-setCode`.name
                        FROM ".$mysql_con.".temp_sss_mrplist AS timp_MRP_List 
                        LEFT JOIN ".$common_con.".mjustifications AS `mtbl_Die-setCode` 
                            ON timp_MRP_List.Re = `mtbl_Die-setCode`.CODE
                        GROUP BY timp_MRP_List.PO, 
                            timp_MRP_List.Re, 
                            `mtbl_Die-setCode`.name
                        HAVING (((timp_MRP_List.Re) IN ('A','B','C','D','E','F')))
                    ) AS qexp_SampleDoujiProduct_work 
                    LEFT JOIN ".$mysql_con.".temp_sss_memo AS tbl_Memo 
                        ON (qexp_SampleDoujiProduct_work.Re = tbl_Memo.StatusCode) 
                        AND (qexp_SampleDoujiProduct_work.PO = tbl_Memo.PO)
                    WHERE (((tbl_Memo.PO) IS NULL))
                ");

            // var_dump($memo);

            //Insert into temp_sss_memo.
            DB::connection($this->mysql)->table('temp_sss_memo')->delete();
            DB::connection($this->mysql)->statement('ALTER TABLE `temp_sss_memo` AUTO_INCREMENT = 0;');
            foreach ($memo as $key => $memo_data) {
                DB::connection($this->mysql)->table('temp_sss_memo')
                    ->insert([
                    'po'            => $memo_data->PO,
                    'dieset_status' => $memo_data->STATUS,
                    'lastupdated'   => $memo_data->DATE,
                    'pc_name'       => $memo_data->NAME
                    ]);
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }
    }

}
