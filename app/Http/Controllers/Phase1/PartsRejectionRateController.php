<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PartsRejectionRateController.php
     MODULE NAME:  30004 - PRRS
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.28
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.28     MESPINOSA       Initial Draft
*******************************************************************************/
?>
<?php

namespace App\Http\Controllers\Phase1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Log;
use App\Prrs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use Config;
use Excel;
use File;

/**
* PartsRejectionRate Controller
*/
class  PartsRejectionRateController extends Controller
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

    /**
    * Get All OrderDataReports.
    */
    public function getPartsRejectionRate(Request $req)
    {
        # validate user login.
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PRRS')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            # valid user

            $default='';

            # get the latest prrs information.
            $lastest_prrs = DB::connection($this->mysql)->table('prrs')
            ->select(
                'id',
                DB::raw("LPAD(period_covered,2,'0') AS period_covered"),
                DB::raw("LPAD(round(standard1,2),6,'0') AS standard1"),
                DB::raw("CONCAT('$',LPAD(round(lower_limit_price,2),6,'0')) AS lower_limit_price"),
                DB::raw("LPAD(round(standard2,2),6,'0') AS standard2"),
                DB::raw("LPAD(for_gr_po,3,'0') AS for_gr_po"),
                DB::raw("DATE_FORMAT(updated_at,'%m/%d/%Y %h:%i %p') as updated_at"),
                DB::raw("CONCAT('(',DATE_FORMAT(DATE_ADD(updated_at, 
                    INTERVAL (period_covered)*-1 MONTH),'%m/%d/%Y'), 
                    ' - ',DATE_FORMAT(updated_at,'%m/%d/%Y'), 
                    ')') as period"),
                DB::raw("DATE_FORMAT(updated_at,'%m/%d/%Y') as last_day")
                )
            ->orderBy('id', 'desc')
            ->first();

            # get classifications from ts database
            $classifications = DB::connection($this->mssql)
                                    ->table('XITEM')
                                    ->select(DB::raw('DISTINCT BUNR as bunr'))
                                    ->where('BUMO', 'LIKE', 'PURH%')
                                    ->where('BUNR', '<>',  '')
                                    ->orderBy('BUNR')
                                    ->get();
            //limit to BUMO LIKE 'PURH%'

            # get prrs classifications.
            if(isset($lastest_prrs))
            {
                $saved_classifications = DB::connection($this->mysql)->table('prrs_classification')->where('prrs_id', $lastest_prrs->id )->get();
            }
            else
            {
                $saved_classifications = DB::connection($this->mysql)->table('prrs_classification')->where('prrs_id', 0 )->get();
            }

            # create default classifications when there is no data from the database.
            $default = $this->createDefaultClassification($classifications);

            $has_file = DB::connection($this->mysql)->table('ypicsfileinfo')->where('token', $req['_token'])->count();

            #render PRRS view
            return view('phase1.PartsRejectionRateSystem', 
                    ['userProgramAccess' => $userProgramAccess,
                    'prrs' => $lastest_prrs,
                    'hasfile' => $has_file,
                    'classifications' => $classifications,
                    'sclassifications' => $saved_classifications,
                    'select' => $default]);
        }
    }

    /**
    * Create default classification, if no prrs classification data in DB.
    */
    private function createDefaultClassification($classifications)
    {
        $select1 = '';
        $select2 = '';
        $select3 = '';
        $select4 = '';
        $select5 = '';

        $select1 = '<tr class="odd gradeX" id="item1">
                                <td id="count">
                                    1
                                </td>
                                <td>
                                    <select name="select_classification" id="sitem1" class="form-control form-filter input-sm select_classification" >
                                        <option value="0">Select...</option>';
        foreach($classifications as $classification)
        {
            $select2 = $select2 . '<option value="' . $classification->bunr .'">'. $classification->bunr . '</option>';
        }
        $select3 ='</select>
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right pull-right input-sm txt_qty" id="txt_qty" name="txt_qty">
                                </td>
                            ';

        $select4 ='</select>
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right pull-right input-sm txt_hpercent" id="txt_hpercent" name="txt_hpercent" value="0.0%">
                                </td>
                            ';
        $select5 ='</select>
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right pull-right input-sm txt_percent" id="txt_percent" name="txt_percent" value="0.0%">
                                </td>
                            </tr>';
        return $select1 . $select2 . $select3 . $select4 . $select5;
    }

    /**
    * Collate 2 related array into 1 array.
    */
    private function mergeArray($select_arr, $value1_arr, $value2_arr, $value3_arr)
    {
        $ctr = 0;

        foreach ($select_arr as $key => $value) 
        {
            $arr_sum[$ctr][0] = $value;
            $ctr++;
        }
        $ctr=0;
        foreach ($value1_arr as $key => $value) 
        {
            $arr_sum[$ctr][1] = $value;
            $ctr++;
        }
        $ctr=0;
        foreach ($value2_arr as $key => $value) 
        {
            $arr_sum[$ctr][2] = $value;
            $ctr++;
        }
        $ctr=0;
        foreach ($value3_arr as $key => $value) 
        {
            $arr_sum[$ctr][3] = $value;
            $ctr++;
        }
        
        return $arr_sum;
    }
    
    /**
    * Save PRRS info.
    */
    public function postPartsRejectionRate(Request $req)
    {
        $obj = $req['prrs_obj'];
        $select_arr = $req['select_arr'];
        $value1_arr = $req['value1_arr'];
        $value2_arr = $req['value2_arr'];
        $value3_arr = $req['value3_arr'];
        $message = '';
        $msg_type = 'message';
        $result = false;

        try
        {
            # insert prrs info
            DB::connection($this->mysql)->table('prrs')->truncate();
            $result = DB::connection($this->mysql)->table('prrs')
                        ->insert([
                            'period_covered' => intval($obj[1]),
                            'standard1' => str_replace('%', '', $obj[2]),
                            'lower_limit_price' => floatval(str_replace('$', '', $obj[3])),
                            'standard2' => str_replace('%', '', $obj[4]),
                            'for_gr_po' => intval($obj[5]),
                            'created_at' => date("Y-m-d h:i:s"),
                            'updated_at' => date("Y-m-d h:i:s")
                        ]);

            if($result)
            {
                # merge selected prrs classification into one array.
                $selected_classifications = $this->mergeArray($select_arr, $value1_arr, $value2_arr, $value3_arr);
                
                # insert all selected prrs classifications.
                DB::connection($this->mysql)->table('prrs_classification')->truncate();
                $lastInsert = DB::connection($this->mysql)->table('prrs')
                                ->select('id')->orderBy('id','desc')->first();

                foreach ($selected_classifications as $key => $value) 
                {
                    $result = DB::connection($this->mysql)->table('prrs_classification')
                            ->insert(
                                ['prrs_id' => $lastInsert->id, 
                                'classification' => $value[0],
                                'margin_qty' => $value[1],
                                'percentage_l' => $value[2],
                                'percentage_h' => $value[3],
                                'updated_at' =>date("Y/m/d h:i:sa"),
                                'created_at' =>date("Y/m/d h:i:sa")
                                ]);
                }

                $message = "PRRS information successfully saved.";
                $msg_type = 'message';
            }
            else
            {
                $message = "Unable to update PRRS at this moment. Please try again.";
                $msg_type = 'err_message';
            }
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }

        return $message;
    }

    /**
    * Upload YPICS Report.
    **/
    public function postPrrsUploadFile(Request $req)
    {
        $ypics = $req->file('file_ypics');
        $token = $req['_token'];
        $message = "";
        $msg_type = 'message';
        $results = "";
        $file = '';
        $name = '';
        $filename = '';
        $extension = '';


        if(isset($_FILES['file']) )
        {
            if($_FILES['file']['size'] > 100000000) 
            {
                $message = "File too big.";
                $msg_type = 'err_message';
                return redirect(url('/partsrejectionrate'))
                        ->with([$msg_type => $message, 
                            'hasfile' => $filename]);                    
            }
        }

        // return dd($request_data->all());

        # check if the uploaded file is empty.
        if (!empty($ypics))
        {

            $file = $ypics->getPathName();
            $name = str_replace('.tmp','.xls' , $ypics->getFilename());
            $filename = $ypics->getClientOriginalName();
            $extension = $ypics->getClientOriginalExtension();

            DB::connection($this->mysql)->table('prrs_filename')->truncate();

            DB::connection($this->mysql)->
                table('prrs_filename')->insert([
                    'filename' => $filename
                ]);

            # check if the uploaded file is in excel format.
            if($extension == 'xls' || $extension == 'xlsx')
            {
                # read uploaded file and save in the server.
                Excel::load($file, function($reader) use ($token, &$message, $name)
                {
                   // Getting all results
                    $results = $reader->get();
                    $results = $results->toArray();

                    $isvalid = true;

                    # delete unused uploaded files.
                    $this->destroy(Config::get('constants.PROJECT_EXPORT_PATH') . $name, $token);
                    $this->deleteGeneratedYpics($token);

                    DB::connection($this->mysql)->table('tbl_prrs_data')->truncate();

                    foreach ($results as $key => $row) 
                    {
                        # validate the columns of the uploaded files
                        if($this->validateColumns($row))
                        {
                            // $this->saveUploadedFile($name, $token);
                            // break;

                            DB::connection($this->mysql)->table('tbl_prrs_data')
                                ->insert([
                                    'salestype' => $row['salestype'],
                                    'salesorg' => $row['salesorg'],
                                    'commercial' => $row['commercial'],
                                    'section' => $row['section'],
                                    'supplier' => $row['supplier'],
                                    'purchaseorderno' => $row['purchaseorderno'],
                                    'issuedate' => $row['issuedate'],
                                    'flightneeddate' => $row['flightneeddate'],
                                    'code' => $row['code'],
                                    'itemtext' => $row['itemtext'],
                                    'orderquantity' => $row['orderquantity'],
                                    'unit' => $row['unit'],
                                    'bunr' => $this->getBUNR($row['code']),
                                    'created_at' => date('Y-m-d')
                                ]);
                        }
                        else
                        {
                            $isvalid = false;
                            $message = "Please select a valid excel file.";
                            $msg_type = 'err_message';
                            break;
                        }
                    }

                    if($isvalid)
                    {
                        foreach ($results as $key => $row) 
                        {
                            //collect pr code
                            //$pr_code[] =  $row['code'];

                            $allowance = $this->getNewQTY($this->getBUNR($row['code']),$row['orderquantity']);
                            $addqty = 0;
                            if ($row['orderquantity'] < 100) { //49
                                $addqty = 1;

                            }
                            $newqty = round(intval($row['orderquantity']) + $addqty + $allowance);

                            DB::connection($this->mysql)->table('tbl_prrs_data')
                                ->where('purchaseorderno',$row['purchaseorderno'])
                                ->update([
                                    'neworderqty' => $newqty,
                                    'allowance' => $allowance,
                                    'updated_at' => date('Y-m-d')
                                ]);
                        }

                        // switch ($this->getDbConnection($name))
                        // {
                        //     # connect to pmi_cn DB.
                        //     case 'CN':
                        //     $database = Config::get('constants.DB_SQLSRV_CN');
                        //     $schema = Config::get('constants.DB_SCHEMA_CN');
                        //     break;

                        //     # connect to pmi_iscd DB.
                        //     case 'TS':
                        //     $database = Config::get('constants.DB_SQLSRV_BU');
                        //     $schema = Config::get('constants.DB_SHCEMA_BU');
                        //     break;

                        //     # connect to pmi_yf DB.
                        //     case 'YF':
                        //     $database = Config::get('constants.DB_SQLSRV_YF');
                        //     $schema = Config::get('constants.DB_SCHEMA_YF');
                        //     break;

                        //     # connect to pmi_cn DB BUT no data will be retrieved.
                        //     default :
                        //     $nodata = true;
                        //     $database = Config::get('constants.DB_SQLSRV_CN');
                        //     $schema = Config::get('constants.DB_SCHEMA_CN');
                        //     break;
                        // }

                        // //var_dump($database);

                        // //get standard allowance.
                        // $this->getStandardAllowance($pr_code, $database, $schema);

                        // //update standard allowance based on the PR Code
                        // $this->updateStandardAllowance();

                    }
                    else
                    {
                        $message = "Please select a valid excel file.";
                        $msg_type = 'err_message';
                    }

                })->store('xls');
                
                $message = "File successfully uploaded.";
                $msg_type = 'message';
            }
            else
            {
                $message = "Please select a valid excel file.";
                $msg_type = 'err_message';
            }
        }
        else
        {
            $message = "Please select a valid excel file.";
            $msg_type = 'err_message';
        }

        # return view.
        return redirect(url('/partsrejectionrate'))
        ->with([$msg_type => $message, 
            'hasfile' => $filename]);
    }

    /**
    * Validate column names of the file.
    **/
    private function validateColumns($row)
    {
        $result = false;
        if(array_key_exists('salesno', $row) 
            && array_key_exists('salestype', $row) 
            && array_key_exists('salesorg', $row) 
            && array_key_exists('commercial', $row) 
            && array_key_exists('section', $row) 
            && array_key_exists('salesbranch', $row) 
            && array_key_exists('salesg', $row) 
            && array_key_exists('supplier', $row) 
            && array_key_exists('destination', $row) 
            && array_key_exists('payer', $row) 
            && array_key_exists('assistant', $row) 
            && array_key_exists('purchaseorderno', $row) 
            && array_key_exists('issuedate', $row) 
            && array_key_exists('flightneeddate', $row) 
            && array_key_exists('headertext', $row) 
            && array_key_exists('code', $row) 
            && array_key_exists('itemtext', $row) 
            && array_key_exists('orderquantity', $row) 
            && array_key_exists('unit', $row) 
            )
        {
            $result = true;
        }

        return $result;
    }

    /**
    * Save the file information of the uploaded file for future download.
    */
    private function saveUploadedFile($filepath, $token)
    {
        $result = false;
        try
        {
            if(is_numeric(strpos($filepath,"var/")) 
                || is_numeric(strpos($filepath,"xampp")))
            {
                $filepath = Config::get('constants.PROJECT_EXPORT_PATH') . $filepath;
            }
            else
            {
                $filepath = Config::get('constants.PROJECT_PATH') 
                . Config::get('constants.PROJECT_EXPORT_PATH') . $filepath;
            }

            if (!is_numeric(strpos($filepath,".xls")))
            {
                $filepath = $filepath . '.xls';
            }

            # insert intval , ckey, databse
            $result = DB::connection($this->mysql)->table('ypicsfileinfo')
            ->insert(
                ['filepath' => $filepath,
                'token' =>$token
                ]);
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
        return $result;
    }

    /**
    * Download uploaded file.
    */
    // public function exportPartsRejectionRate(Request $request_data)
    // {
    //     $message = '';
    //     $token = $request_data['_token'];
    //     $qty = $request_data['forgrpo'];
    //     $data = array();
    //     $updated_data = array();

    //     # retrieve uplaoded file.
        
    //     $order_data_report = DB::connection($this->mysql)->table('ypicsfileinfo')
    //     ->where('token',$token)
    //     ->get();
        
    //     if(count($order_data_report) <= 0 )
    //     {
    //         return redirect(url('/partsrejectionrate'))->with(['err_message' => 'Unable to generate GR PO. No uploaded file.']);
    //     }
    //     else
    //     {
    //         $this->deleteGeneratedYpics($token);

    //         $ctr=0;
    //         # convert the object result to array readable format.
    //         foreach ($order_data_report as $orderdatareport) 
    //         {
    //             $data[] = (array)$orderdatareport;
    //             #or first convert it and then change its properties using 
    //             #an array syntax, it's up to you
    //             Excel::load($data[0]['filepath'], function($reader) use ($token, $ctr, &$updated_data, $qty)
    //             {
    //                    // Getting all results
    //                     $results = $reader->get();
    //                     $results = $results->toArray();

    //                 foreach ($results as $key => $value) 
    //                 {
    //                     $recommended_qty = 0;

    //                     //get or extract PO
    //                     $po1 = substr($value['purchaseorderno'], strpos($value['purchaseorderno'],'-') + 1, strlen($value['purchaseorderno']));
    //                     $po = substr($po1, 0, strpos($po1,'-'));

    //                         //get PO recommended value
    //                         $standard_allowance = DB::connection($this->mysql)->table('temp_prrs_standard_allowance')
    //                         ->select(
    //                                 'code', 'name', 'bunr', 'bumo', 'unit_price', 'parts_type', 'note', 'allowance', 'order_qty'
    //                             )
    //                         ->get();

    //                         $addqty = 0;
    //                         if($value['orderquantity'] < 100)
    //                         {
    //                             $addqty = 1;
    //                         }

    //                         foreach ($standard_allowance as $key => $sa) 
    //                         {
    //                             $recommended_qty = 0;

    //                             if($value['code'] == $sa->code)
    //                             {
    //                                 $recommended_qty = round($value['orderquantity'] + ($value['orderquantity'] * $sa->allowance));
    //                                 break;
    //                             }
    //                         }

    //                         //update order quantity base on recommended value
    //                         //update order quantity for GR PO
    //                         if(strpos($value['purchaseorderno'],'GR'))
    //                         {
    //                             $value['orderquantity'] = $recommended_qty + $addqty + intval($qty);
    //                         }
    //                         else
    //                         {
    //                             $value['orderquantity'] = $recommended_qty + $addqty;
    //                         }
    //                         $updated_data[$ctr] = $value;
    //                         $ctr++;
    //                     }
    //             });

    //             if(!empty($updated_data))
    //             {
    //                 # Create and export excel by feeding the array result.
                    
    //                 $name = DB::connection($this->mysql)
    //                             ->table('prrs_filename')
    //                             ->select('filename')
    //                             ->orderBy('id','desc')
    //                             ->first();

    //                 $filename = str_replace('.xls', '', $name->filename);
    //                 Excel::create('Revised_'.$filename, function($excel) use($updated_data) 
    //                 {

    //                     $excel->sheet('GRPO', function($sheet) use($updated_data) 
    //                     {
    //                         $sheet->fromArray($updated_data);
    //                     });

    //                 })->export('xls');

    //             }

    //             break;
    //         }
    //     }
    // }

    public function exportPartsRejectionRate(Request $req)
    {
        $prrs_data = DB::connection($this->mysql)->table('tbl_prrs_data')->get();
        $gr_qty = 0;

        if ($this->checkIfExistObject($prrs_data) > 0) {
            foreach ($prrs_data as $key => $prrs) {
                if (strpos($prrs->purchaseorderno,'GR')) {

                    $gr_qty = $prrs->neworderqty + intval($req->forgrpo);

                    DB::connection($this->mysql)->table('tbl_prrs_data')
                        ->where('purchaseorderno',$prrs->purchaseorderno)
                        ->update([
                            'neworderqty' => $gr_qty
                        ]);
                }
            }

            $name = DB::connection($this->mysql)->table('prrs_filename')
                        ->select('filename')->orderBy('id','desc')
                        ->first();

            $filename = str_replace('.xls', '', $name->filename);
            Excel::create('Revised_'.$filename, function($excel)
            {
                $excel->sheet('GRPO', function($sheet)
                {
                    $sheet->cell('A1','salesno');
                    $sheet->cell('B1','salestype');
                    $sheet->cell('C1','salesorg');
                    $sheet->cell('D1','commercial');
                    $sheet->cell('E1','section');
                    $sheet->cell('F1','salesbranch');
                    $sheet->cell('G1','salesg');
                    $sheet->cell('H1','supplier');
                    $sheet->cell('I1','destination');
                    $sheet->cell('J1','payer');
                    $sheet->cell('K1','assistant');
                    $sheet->cell('L1','purchaseorderno');
                    $sheet->cell('M1','issuedate');
                    $sheet->cell('N1','flightneeddate');
                    $sheet->cell('O1','headertext');
                    $sheet->cell('P1','code');
                    $sheet->cell('Q1','itemtext');
                    $sheet->cell('R1','orderquantity');
                    $sheet->cell('S1','unit');

                    $data = DB::connection($this->mysql)->table('tbl_prrs_data')->get();
                    $row = 2;

                    foreach ($data as $key => $prrs) {
                        $sheet->cell('A'.$row,$prrs->salesno);
                        $sheet->cell('B'.$row,$prrs->salestype);
                        $sheet->cell('C'.$row,$prrs->salesorg);
                        $sheet->cell('D'.$row,$prrs->commercial);
                        $sheet->cell('E'.$row,$prrs->section);
                        $sheet->cell('F'.$row,$prrs->salesbranch);
                        $sheet->cell('G'.$row,$prrs->salesg);
                        $sheet->cell('H'.$row,$prrs->supplier);
                        $sheet->cell('I'.$row,$prrs->destination);
                        $sheet->cell('J'.$row,$prrs->payer);
                        $sheet->cell('K'.$row,$prrs->assistant);
                        $sheet->cell('L'.$row,$prrs->purchaseorderno);
                        $sheet->cell('M'.$row,$prrs->issuedate);
                        $sheet->cell('N'.$row,$prrs->flightneeddate);
                        $sheet->cell('O'.$row,$prrs->headertext);
                        $sheet->cell('P'.$row,$prrs->code);
                        $sheet->cell('Q'.$row,$prrs->itemtext);
                        $sheet->cell('R'.$row,$prrs->neworderqty);
                        $sheet->cell('S'.$row,"PC");
                        $row++;
                    }
                });

            })->export('xls');
        }
    }

    /**
    * Delete the information of the unused uploaded files from the DB.
    */
    private function deleteGeneratedYpics($token)
    {
        $result = false;
        try
        {
            # insert intval , ckey, databse
            $result = DB::connection($this->mysql)->table('ypicsfileinfo')
            ->where('token' , $token)
            ->delete();
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
        return $result;
    }

    /**
    * Delete the information of the unused uploaded files from the server physical path.
    */
    public function destroy($filepath, $token)
    {
        $prev_file = DB::connection($this->mysql)->table('ypicsfileinfo')
        ->where('token' , $token)
        ->get();
        # convert the object result to array readable format.
        foreach ($prev_file as $pfile) 
        {
            $data[] = (array)$pfile;
            #or first convert it and then change its properties using 
            #an array syntax, it's up to you
            if (!File::delete(trim(Config::get('constants.PROJECT_PATH')) . $filepath))
              {
                File::Delete($filepath);
                //Session::flash('flash_message', 'ERROR deleted the File!');
                //return Redirect::to('page name');
              }
            else
              {
                File::Delete($filepath);
            }
        }
    }

    /**
    * Get Db Connection
    * */
    private function getDbConnection($filename)
    {
        if (strpos($filename, 'BU2')) {
            $con = 'TS';
        } elseif(strpos($filename, 'BU1')) {
            $con = 'CN';
        } elseif (strpos($filename, 'CONNECTORS')) {
            $con = 'YF';
        } else {
            $con = 'TS';
        }
        return $con;
    }

    /**
    * Get standard allowance based on the uploaded PR Codes
    * */
    private function getStandardAllowance($pr_code, $database, $schema)
    {
        //get temp_prcode_allowance

        $temp_po = DB::connection($this->mssql)->table($schema . 'XITEM')
        ->select(
            'XITEM.CODE', 
            'XHEAD.NAME', 
            'XITEM.BUNR', 
            'XITEM.BUMO', 
            't_UnitPrice.UnitPrice', 
            't_UnitPrice.PartsType', 
            'XHEAD.NOTE')
        ->leftJoin('XHEAD', 'XITEM.CODE', '=', 'XHEAD.CODE')
        ->leftJoin( DB::raw("(SELECT XTANK.CODE AS PCode, 
            XHEAD.NAME AS PName, 
            XTANK.PRICE AS UnitPrice, 
            XITEM.BUNR AS PartsType 
            FROM (((SELECT XTANK.CODE, Max(XTANK.TID) AS MaximumTID FROM XTANK GROUP BY XTANK.CODE) as  a_03_UnitPrice_work INNER JOIN XTANK ON a_03_UnitPrice_work.MaximumTID = XTANK.TID) 
            INNER JOIN XHEAD ON a_03_UnitPrice_work.CODE = XHEAD.CODE) 
            INNER JOIN XITEM ON a_03_UnitPrice_work.CODE = XITEM.CODE
            ) t_UnitPrice"), 'XITEM.CODE', '=', 't_UnitPrice.PCode')
        ->where('XITEM.BUMO', '=', 'PURH100' )
        ->whereIn('XITEM.CODE', $pr_code)
        ->get();

        $standard_allowance = DB::connection($this->mysql)->table('prrs')
            ->select(DB::raw('(standard1 / 100) as standard1'))
            ->where('id', '=' , DB::raw('(SELECT MAX(id) FROM prrs)'))
            ->get();
        
        //Insert into temp table allowance.
        DB::connection($this->mysql)->table('temp_prrs_standard_allowance')->delete();
        foreach ($temp_po as $key => $allo) {
            DB::connection($this->mysql)->table('temp_prrs_standard_allowance')->insert([
                'code' => $allo->CODE,
                'name' => $allo->NAME,
                'bunr' => $allo->BUNR,
                'bumo' => $allo->BUMO,
                'unit_price' => $allo->UnitPrice,
                'parts_type' => $allo->PartsType,
                'note' => $allo->NOTE,
                'allowance' => $standard_allowance[0]->standard1
                // 'orderquantity' => $orderquantity
                ]);
        }

    //var_dump($temp_po);
    }

    /**
    * Update Standard Allowance based on the Uploaded PR Code/s
    * */
    private function updateStandardAllowance()
    {
        //get the current classification's percentage
        $classifications = DB::connection($this->mysql)->table('prrs_classification')
            ->select('classification', 'margin_qty', 'percentage_l', 'percentage_h')
            ->where('prrs_id', '=' , DB::raw('(SELECT MAX(id) FROM prrs)'))
            ->get();

        // var_dump($classifications);

        //update allowance based on the latest classification percentage
        foreach ($classifications as $key => $data) {

            $standard = $data->percentage_l / 100;
            DB::connection($this->mysql)->table('temp_prrs_standard_allowance')
            ->where(DB::raw('UCASE(bunr)'), '=', strtoupper($data->classification))
            ->update([
                'allowance' => $standard
                ]);
        // var_dump($standard);
        }        
    }


    // UPDATES
    private function getNewQTY($bunr,$orderQty)
    {
        $prrs_class = DB::connection($this->mysql)->table('prrs_classification')
                        ->where('classification',$bunr)->first();
        $rate = 0;
        $qty = 0;
        if ($this->checkIfExistObject($prrs_class) > 0) {
            if ($orderQty >= $prrs_class->margin_qty) {
                $rate = $prrs_class->percentage_h / 100;
            }

            if ($orderQty < $prrs_class->margin_qty) {
                $rate = $prrs_class->percentage_l / 100;
            }
        } else {
            $prrs_class = DB::connection($this->mysql)->table('prrs')->first();
            if ($bunr == 'CT With MOQ') {
                $rate = $prrs_class->standard2 / 100; //0
            } else {
                $rate = $prrs_class->standard1 / 100;
            }
        }

        $qty = $orderQty * $rate;

        return $qty;
    }

    private function getBUNR($code)
    {
        $db = DB::connection($this->mssql)->table('XITEM')
                ->select('BUNR')
                ->where('CODE',$code)
                ->first();
        return $db->BUNR;
    }

    private function checkIfExistObject($object)
    {
       return count( (array)$object);
    }

}