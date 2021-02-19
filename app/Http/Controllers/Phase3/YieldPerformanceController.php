<?php
namespace App\Http\Controllers\Phase3;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use Datatables;
use App\Http\Requests;
use App\Poregistration;
use App\Deviceregistration;
use App\Seriesregistration;
use App\Modregistration;
use Carbon\Carbon;
use Config;
use DB;
use Dompdf\Dompdf;
use Excel;
use PDF;

class YieldPerformanceController extends Controller
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

    public function getYieldPerformance(Request $request)
    {
        $common = new CommonController;
         if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_YLDPRFMNCE'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        { 
        
            $msrecords = DB::connection($this->mssql)
                ->table('XRECE as d')
                ->join('XHEAD as h','d.CODE','=','h.CODE')
                ->select(DB::raw("d.SORDER as PO")
                    , DB::raw("d.CODE as devicecode")
                    , DB::raw("h.NAME as devicename")
                    , DB::raw("SUM(d.KVOL) as POqty"))
                ->where('d.SORDER',$request->pono)
                ->groupBy('d.SORDER','d.CODE','h.NAME')
                ->get();
            /*$msrecords = DB::connection($this->mssql)
                ->table('XHIKI as d')
                ->join('XHEAD as h','d.OYACODE','=','h.CODE')
                ->select(DB::raw("d.SEIBAN as PO")
                    , DB::raw("d.OYACODE as devicecode")
                    , DB::raw("h.NAME as devicename")
                    , DB::raw("SUM(d.KVOL) as POqty"))
                ->where('d.SEIBAN',$request->pono)
                ->groupBy('d.SEIBAN','d.OYACODE','h.NAME')
                ->get();*/
            $records = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")
                        ->select('id','pono','poqty','device','series','family','toutput','treject',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"),DB::raw("SUM(qty) as qty"),DB::raw("SUM(twoyield) as twoyield"))
                        ->groupBy('pono')
                        ->get();
            $record = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")
                        ->groupBy('pono')
                        ->get();
            $targetyield = DB::connection($this->mysql)->table("tbl_targetregistration")->distinct()->get();
           
            $count = DB::connection($this->mysql)->table("tbl_yielding_performance")->count() + 1;
            $tablepya = DB::connection($this->mysql)->table("tbl_yielding_pya")->get(); 
            $tablecmq = DB::connection($this->mysql)->table("tbl_yielding_cmq")->get(); 
            $classification = $common->getDropdownByName('Classification');
            $family = DB::connection($this->mysql)->table("tbl_seriesregistration")->select('family')->distinct()->get();
            $series = $common->getDropdownByName('series');
            $ys = $common->getDropdownByName('Yielding Station');
            $tableporeg = DB::connection($this->mysql)->table("tbl_poregistration")->orderBy('id','DESC')->get();
            $tabledevicereg = DB::connection($this->mysql)->table("tbl_deviceregistration")->orderBy('id','DESC')->get();
            $tableseriesreg = DB::connection($this->mysql)->table("tbl_seriesregistration")->orderBy('family','asc')->get();
            $tablemodreg = DB::connection($this->mysql)->table("tbl_modregistration")->orderBy('family','asc')->get();
            $target = DB::connection($this->mysql)->table("tbl_targetregistration")->orderBy('datefrom','asc')->get();
            $ptype = $common->getDropdownByName('Product Type');
            
            return view('phase3.YieldPerformance',['userProgramAccess' => $userProgramAccess,'category' => $classification,'family' => $family,'series' => $series,'yieldstation' => $ys,'records'=>$records,'record'=>$record, 'yieldingno'=>$count, 'msrecords'=>$msrecords, 'count'=>$count,'fieldpya'=>$tablepya,'fieldcmq'=>$tablecmq,'tableporeg'=>$tableporeg,'tabledevicereg'=>$tabledevicereg,'tableseriesreg'=>$tableseriesreg,'tablemodreg'=>$tablemodreg,
                'target' => $target,
                'ptype' => $ptype,
                'targetyield' => $targetyield]); 
        }
    }

    public function getponoreg(Request $request){
        $pono = $request->pono;
        /*$msrecords = DB::connection($this->mssql)
                ->table('XHIKI as d')
                ->join('XHEAD as h','d.OYACODE','=','h.CODE')
                ->select(DB::raw("d.SEIBAN as PO")
                    , DB::raw("d.OYACODE as devicecode")
                    , DB::raw("h.NAME as devicename")
                    , DB::raw("SUM(d.KVOL) as POqty"))
                ->where('d.SEIBAN',$request->pono)
                ->groupBy('d.SEIBAN','d.OYACODE','h.NAME')
                ->get();*/
        $msrecords = DB::connection($this->mssql)
                ->table('XRECE as d')
                ->join('XHEAD as h','d.CODE','=','h.CODE')
                ->select(DB::raw("d.SORDER as PO")
                    , DB::raw("d.CODE as devicecode")
                    , DB::raw("h.NAME as devicename")
                    , DB::raw("SUM(d.KVOL) as POqty"))
                ->where('d.SORDER',$request->pono)
                ->groupBy('d.SORDER','d.CODE','h.NAME')
                ->get();
        return $msrecords;
    }
   
    //displaying  tbl_poregistration records----------------

    private function displayporeg(){
        $table = DB::connection($this->mysql)->table('tbl_poregistration')->orderBy('id','DESC')->get();
        return $table;
    }
    public function displayporegistration(){
        $table = DB::connection($this->mysql)->table('tbl_poregistration')->orderBy('id','DESC')->get();
        return $table;
    }
    //Add and Update PO Registration------------------------
    public function poregistration(Request $request){
        $status = $request->status;
        $id = $request->id;
        $count = DB::connection($this->mysql)->table('tbl_poregistration')->where('pono',$request->pono)->count();
        if($count > 0){
            return $count;
        }else{
            if($status == "ADD"){
            DB::connection($this->mysql)->table('tbl_poregistration')
                ->insert([
                    'pono'=>$request->pono,
                    'device'=>$request->podevice,
                    'poqty'=>$request->poquantity
                ]);    
            }
            if($status == "EDIT"){
                DB::connection($this->mysql)->table('tbl_poregistration')
                    ->where('id','=',$id)
                    ->update(array(
                        'pono'=>$request->pono,
                        'device'=>$request->podevice,
                        'poqty'=>$request->poquantity,
                    ));
            }
            return $this->displayporeg();     
        }
        
    }
    //Edit records for tbl_poregistration------------------
    public function editporeg(Request $request)
    {    
        $search = $request->editsearch;
        $ok =DB::connection($this->mysql)->table('tbl_poregistration')
        ->where('id', $search)
        ->get();
        return $ok;
    }
    //Delete records for tbl_poregistration------------------
    public function deleteporeg(Request $request){  
        $tray = $request->tray;
        $traycount = $request->traycount;  
      /*  return $tray;  */
        if($traycount > 0){
            DB::connection($this->mysql)->table('tbl_poregistration')->wherein('id',$tray)->delete();  
        } 
        return $this->displayporeg();
    }
 
    //displaying  tbl_deviceregistration records----------------
    private function displaydevreg(){
        $table = DB::connection($this->mysql)->table('tbl_deviceregistration')->orderBy('id','DESC')->get();
        return $table;
    }
    public function displaydeviceregistration(){
        $table = DB::connection($this->mysql)->table('tbl_deviceregistration')->orderBy('id','DESC')->get();
        return $table;
    }
    //Add and Update Device Registration------------------------
    public function deviceregistration(Request $request){
        $status = $request->status;
        $id = $request->id;

        $count = DB::connection($this->mysql)->table('tbl_deviceregistration')->where('pono',$request->pono)->count();
        if($count > 0){
            return $count;
        }else{
            if($status == "ADD"){
               DB::connection($this->mysql)->table('tbl_deviceregistration')
                    ->insert([
                        'pono'=>$request->pono,
                        'devicename'=>$request->devicename,
                        'family'=>$request->family,
                        'series'=>$request->series,
                        'ptype'=>$request->ptype
                    ]);    
            }
            if($status == "EDIT"){
                DB::connection($this->mysql)->table('tbl_deviceregistration')
                    ->where('id','=',$id)
                    ->update(array(
                        'pono'=>$request->pono,
                        'devicename'=>$request->devicename,
                        'family'=>$request->family,
                        'series'=>$request->series,
                        'ptype'=>$request->ptype   
                    ));
            }
            return $this->displaydevreg();  
        }
    }
    //Edit records for tbl_deviceregistration------------------
    public function editdevicereg(Request $request)
    {    
        $search = $request->editsearch;   
        $ok =DB::connection($this->mysql)->table('tbl_deviceregistration')
        ->where('id', $search)
        ->get();
        return $ok;
    }
    //Delete records for tbl_deviceregistration------------------
    public function deletedevicereg(Request $request){  
        $tray = $request->tray;
        $traycount = $request->traycount;  
      /*  return $tray;  */
        if($traycount > 0){
            DB::connection($this->mysql)->table('tbl_deviceregistration')->wherein('id',$tray)->delete();  
        } 
        return $this->displaydevreg();
    }

    //displaying  tbl_Series Registration records----------------
    private function displayseriesreg(){
        $table = DB::connection($this->mysql)->table('tbl_seriesregistration')->get();
        return $table;
    }
    //Add and Update PO Registration------------------------
    public function seriesregistration(Request $request){
        $status = $request->status;
        $id = $request->id;

        if($status == "ADD"){
           DB::connection($this->mysql)->table('tbl_seriesregistration')
                ->insert([
                    'family'=>$request->family,
                    'series'=>$request->series
                 
                ]);    
        }
        if($status == "EDIT"){
            DB::connection($this->mysql)->table('tbl_seriesregistration')
                ->where('id','=',$id)
                ->update(array(
                    'family'=>$request->family,
                    'series'=>$request->series
                ));
        }
        return $this->displayseriesreg();  
    }
    //Edit records for tbl_seriesregistration------------------
    public function editseriesreg(Request $request)
    {    
        $search = $request->editsearch;
        $ok =DB::connection($this->mysql)->table('tbl_seriesregistration')
        ->where('id', $search)
        ->get();
        return $ok;
    }
    //Delete records for tbl_seriesregistration------------------
    public function deleteseriesreg(Request $request){  
        $tray = $request->tray;
        $traycount = $request->traycount;  

        if($traycount > 0){
            DB::connection($this->mysql)->table('tbl_seriesregistration')->wherein('id',$tray)->delete();  
        } 
        return $this->displayseriesreg();
    }
  
    //displaying  tbl_Mod Registration records----------------
    private function displaymodreg(){
        $table = DB::connection($this->mysql)->table('tbl_modregistration')->get();
        return $table;
    }
    //Add and Update PO Registration------------------------
    public function modregistration(Request $request){
        $status = $request->status;
        $id = $request->id;
        if($status == "ADD"){
           DB::connection($this->mysql)->table('tbl_modregistration')
                ->insert([
                    'mod'=>$request->mod,
                    'family'=>$request->family
                 
                ]);    
        }
        if($status == "EDIT"){
            DB::connection($this->mysql)->table('tbl_modregistration')
                ->where('id','=',$id)
                ->update(array(
                    'mod'=>$request->mod,
                    'family'=>$request->family
                ));
        }
        return $this->displaymodreg();  
    }
    //Edit records for tbl_modregistration------------------
    public function editmodreg(Request $request)
    {    
        $search = $request->editsearch;
        $ok =DB::connection($this->mysql)->table('tbl_modregistration')
        ->where('id', $search)
        ->get();
        return $ok;
    }
    //Delete records for tbl_modregistration------------------
    public function deletemodreg(Request $request){  
        $tray = $request->tray;
        $traycount = $request->traycount;  

        if($traycount > 0){
            DB::connection($this->mysql)->table('tbl_modregistration')->wherein('id',$tray)->delete();  
        } 
        return $this->displaymodreg();
    }
     //---------------------------------------------------------
    //----------------------------------------------------------
    //displaying  tbl_target Registration records----------------
    private function displaytargetreg(){
        $table = DB::connection($this->mysql)->table('tbl_targetregistration')->get();
        return $table;
    }
    //Add and Update PO Registration------------------------
    public function targetregistration(Request $request){
        $status = $request->status;
        $id = $request->id;
        if($status == "ADD"){
           DB::connection($this->mysql)->table('tbl_targetregistration')
                ->insert([
                    'datefrom'=>$request->datefrom,
                    'dateto'=>$request->dateto,
                    'yield'=>$request->yielding,
                    'dppm'=>$request->dppm,
                    'ptype'=>$request->ptype
                ]);    
        }
        if($status == "EDIT"){
            DB::connection($this->mysql)->table('tbl_targetregistration')
                ->where('id','=',$id)
                ->update(array(
                    'datefrom'=>$request->datefrom,
                    'dateto'=>$request->dateto,
                    'yield'=>$request->yielding,
                    'dppm'=>$request->dppm,
                    'ptype'=>$request->ptype
                ));
        }
        return $this->displaytargetreg();  
    }
    //Edit records for tbl_targetregistration------------------
    public function edittargetreg(Request $request)
    {    
        $search = $request->editsearch;
        $ok =DB::connection($this->mysql)->table('tbl_targetregistration')
        ->where('id', $search)
        ->get();
        return $ok;
    }
    //Delete records for tbl_targetregistration------------------
    public function deletetargetreg(Request $request){  
        $tray = $request->tray;
        $traycount = $request->traycount;  

        if($traycount > 0){
            DB::connection($this->mysql)->table('tbl_targetregistration')->wherein('id',$tray)->delete();  
        } 
        return $this->displaytargetreg();
    }


    public function deleteAllPost(Request $request)
    {      
        $tray = $request->tray;
        $traycount = $request->traycount;

       /* return $tray;*/
        if($traycount > 0){
            $ok = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->wherein('id',$tray)->delete();
        
            if ($ok) {
                $msg = "Successfully deleted selected records.";
                return redirect('/yieldperformance')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/yieldperformance')->with(['err_message'=>$msg]);
            }
        } else {
            $ok = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->delete();
        
            if ($ok) {
                $msg = "Successfully deleted all records.";
                return redirect('/yieldperformance')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/yieldperformance')->with(['err_message'=>$msg]);
            }
        }
       
    }

    public function deleteAll(Request $request)
    {
       
        $tray = $request->tray;
        $traycount = $request->traycount;
       /* return $tray;*/
        if($traycount > 0){
            $ok = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->wherein('id',$tray)->delete();
        
            if ($ok) {
                $msg = "Successfully deleted selected records.";
                return redirect('/yieldperformance')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/yieldperformance')->with(['err_message'=>$msg]);
            }
        } else {
             $ok = DB::connection($this->mysql)->table('tbl_wbssetting')->delete();
        
            if ($ok) {
                $msg = "Successfully deleted all records.";
                return redirect('/yieldperformance')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/yieldperformance')->with(['err_message'=>$msg]);
            }
        }
       
    } 

 
    public function udpateyieldsummary(Request $request)
    {
        $table = "tbl_yielding_performance_backup";
        $exist = $request->data;

  
        $ok = DB::connection($this->mysql)->table($table)
            ->where('id', $exist['masterid'])
            ->update(array('pono'=>$exist['pono'],'poqty' =>$exist['poqty'],'device' =>$exist['device'],'series' =>$exist['series'],'family' =>$exist['family'],'toutput' =>$exist['toutput'],'treject' =>$exist['treject'],'twoyield' =>$exist['twoyield'] ));
      
        if ($ok) {
            $msg = "Successfully saved.";
            return redirect('/yieldperformance')->with(['message'=>$msg]);
        } else {
             $msg = "Saving Failed.";
            return redirect('/yieldperformance')->with(['err_message'=>$msg]);
        }
    }

    function summarychart(){
        $ok =DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->get();
        return $ok;
    }

    public function exportToexcel(Request $request)
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';

            Excel::create('Summary_Records_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "PO NO");
                    $sheet->cell('B1', "PO QUANTITY");
                    $sheet->cell('C1', "DEVICE NAME");
                    $sheet->cell('D1', "SERIES");
                    $sheet->cell('E1', "FAMILY");
                    $sheet->cell('F1', "TOTAL OUTPUT");
                    $sheet->cell('G1', "TOTAL REJECT");
                    $sheet->cell('H1', "TOTAL YIELD");

                    $sheet->row(4, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(10);
                        $row->setAlignment('center');
                    });
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(10);
                        $row->setAlignment('center');
                    });
                    $sheet->row(3, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setFontSize(10);
                        $row->setAlignment('center');
                    });
                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  10,
                        )
                    ));
                    $row = 2;
                    $data = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->get();
                    foreach ($data as $key => $val) {
                        $sheet->cell('A'.$row, $val->pono);
                        $sheet->cell('B'.$row, $val->poqty);
                        $sheet->cell('C'.$row, $val->device);
                        $sheet->cell('D'.$row, $val->series);
                        $sheet->cell('E'.$row, $val->family);
                        $sheet->cell('F'.$row, $val->accumulatedoutput);
                        $sheet->cell('G'.$row, $val->qty);
                        $sheet->cell('H'.$row, $val->twoyield);
                        $row++;
                    }

                });

            })->download('xls');


        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
        }
    }

    public function exportTopdf(Request $request)
    {
        $field = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->get();
            $html1 = '<style>
                        #data {
                          border-collapse: collapse;
                          width: 100%;
                          font-size:10px
                        }

                        #data thead td {
                          border: 1px solid black;
                          text-align: center;
                        }

                        #data tbody td {
                          border-bottom: 1px solid black;
                          alignment:center;
                        }

                        #info {
                          width: 100%;
                          font-size:10px;
                        }

                        #info thead td {
                          text-align: center;
                        }
                        #date{
                            text-align:right;
                        }

                      </style>
                      <table id="info">
                        <thead>
                          <tr>
                            <td colspan="5">
                              <h2>YIELDING PERFORMANCE</h2>
                            </td>
                          </tr>
                          <tr>
                            <td colspan="5">
                              
                            </td>
                          </tr>
                          <tr>
                            <td id="date" colspan="6" style="margin-right:20%">
                              '.date("m/d/Y").'
                            </td>
                          </tr>
                        </thead>
                      </table>
                      <table id="data">
                        <thead>
                          <tr>
                            <td>P.O #</td>
                            <td>P.O QUANTITY</td>
                            <td>DEVICE NAME</td>
                            <td>SERIES</td>
                            <td>FAMILY</td>
                            <td>TOTAL OUTPUT</td>
                            <td>TOTAL REJECT</td>
                            <td>TOTAL YIELD</td>

                          </tr>
                        </thead>
                        <tbody>';

            $html3 = '</tbody>
                      </table>';
           
            $html2 = '';
            foreach ($field as $key => $v) {
               $html2 .= '<tr>
                    <td>'.$v->pono.'</td>
                    <td>'.$v->poqty.'</td>
                    <td>'.$v->device.'</td>
                    <td>'.$v->series.'</td>
                    <td>'.$v->family.'</td>
                    <td>'.$v->accumulatedoutput.'</td>
                    <td>'.$v->qty.'</td>
                    <td>'.$v->twoyield.'</td>
                   
                </tr>';
            }

            $html_final = $html1.$html2.$html3;
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html_final);
            $dompdf->setPaper('letter', 'landscape');
            $dompdf->render();
            $dompdf->stream('Yielding_Performance'.Carbon::now().'.pdf');    
    }

    public function summaryREpt(Request $request)
    {
        try
        { 
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';
            $datefrom = $request->datefrom;
            $dateto = $request->dateto;
            $prodtype = $request->srprodtype;
            $check = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->where('prodtype',$prodtype)->count();
            $check1 = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->count();

            if ($check > 0 || $check1 > 0) {
                Excel::create('Summary_Report_'.$date, function($excel) use($request)
                {
                    $excel->sheet('Sheet1', function($sheet) use($request)
                    {
                        $sheet->setAutoSize(true);
                        $datefrom = $request->datefrom;
                        $dateto = $request->dateto;
                         $prodtype = $request->srprodtype;
                        $family = $request->srfamily;
                        $sheet->setCellValue('A1', "$family Yield Summary");
                        $sheet->mergeCells('A1:B1');
                        $sheet->cell('A3',"Inclusive Date");
                        $date = date("Y-m-d");
                        $sheet->cell('A4',$date);
                        $sheet->cell('C3',"Date From");
                        $sheet->cell('D3',$datefrom);
                        $sheet->cell('C4',"Date To");
                        $sheet->cell('D4',$dateto);
                        $sheet->setHeight(1,30);

                        $sheet->row(1, function ($row) {
                            $row->setFontFamily('Calibri');
                            $row->setBackground('##ADD8E6');
                            $row->setFontSize(15);
                            $row->setAlignment('center');
                            $row->setFontWeight('bold');
                        });
                       
                         $sheet->setStyle(array(
                                'font' => array(
                                'name'      =>  'Calibri',
                                'size'      =>  10
                            )
                        ));

                         

                          $Outdatass = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                            ->select('family','device','yieldingno','pono','twoyield','poqty','mod','toutput','qty','twoyield','classification',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"))
                            ->groupBy('mod')

                             ->whereBetween('productiondate', [$datefrom, $dateto])
                             ->where('prodtype',$prodtype)
                             ->where('classification','<>','NDF')
                            ->get();

                            if($prodtype == ''){
                                        $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                                     ->select('family','device','yieldingno','pono','twoyield','poqty','mod','toutput','qty','twoyield',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"))
                                     ->groupBy('pono')
                                     ->whereBetween('productiondate', [$datefrom, $dateto])
                                     ->get();
                            }
                            else{
                                 $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                                     ->select('family','device','yieldingno','pono','twoyield','poqty','mod','toutput','qty','twoyield',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"))
                                     ->groupBy('pono')
                                     ->whereBetween('productiondate', [$datefrom, $dateto])
                                     ->where('prodtype',$prodtype)
                                     ->get();
                                }
                                $row = 6;
                                $ModHold = [];
                                $countMH = 12;
                                 foreach ($Outdatass as $key => $val) { //GET MOD
                                        $ModHold[$countMH] = $val->mod;
                                        $countMH++;
                                 }
                                 $ModHoldcount = count($ModHold);
                                 $modFixx = array_unique($ModHold);
                                 $ponoHold = [];
                                 $countpono = 3;
                                 foreach ($Outdata as $key => $val) { //GET PONO
                                        $ponoHold[$countpono] = $val->pono;
                                        $countpono = $countpono+2;
                                 }
                                 $ponocount = count($ponoHold);
                                 $ponoFixx = array_unique($ponoHold);
                          $arrayLetter = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ");
                          $con = count($arrayLetter)/3;
                          $r=1;
                          for($x=0;$x<$con;$x++){
                            
                           $sheet->setColumnFormat(array(
                            $arrayLetter[$r] => '0%'
                             ));
                           $r=$r+2;
                          }
                          $lete = 0;
                          $defe = 12; 
                          $rowMaintain = 12;
                            

                          $counter = 0;
                        foreach ($Outdata as $key => $val) {
                            $row = 6;
                            $sheet->cell($arrayLetter[$lete].$row, $val->device);
                            $ov1 = $arrayLetter[$lete].$row;
                            $l = $lete+1;
                            $ov2 = $arrayLetter[$l].$row;
                            $sheet->mergeCells("$ov1:$ov2");
                            $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $counter++;
                            $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $row++;
                            $sheet->cell($arrayLetter[$lete].$row, $val->pono);
                            $ov1 = $arrayLetter[$lete].$row;
                            $l = $lete+1;
                            $ov2 = $arrayLetter[$l].$row;
                            $sheet->mergeCells("$ov1:$ov2");
                            $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $row++;
                            // $sheet->cell($arrayLetter[$lete].$row, $val->poqty);
                            $ov1 = $arrayLetter[$lete].$row;
                            $l = $lete+1;
                            $ov2 = $arrayLetter[$l].$row;
                            $sheet->mergeCells("$ov1:$ov2");
                            $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                            $row++;
                            $sheet->cell($arrayLetter[$lete].$row, $val->accumulatedoutput);
                            $ov1 = $arrayLetter[$lete].$row;
                            $l = $lete+1;
                            $ov2 = $arrayLetter[$l].$row;
                            $sheet->mergeCells("$ov1:$ov2");
                            $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                            $row++;    
                            $sheet->cell($arrayLetter[$lete].$row, $val->twoyield/100);
                            $ov1 = $arrayLetter[$lete].$row;
                            $l = $lete+1;
                            $ov2 = $arrayLetter[$l].$row;
                            $sheet->mergeCells("$ov1:$ov2");
                            $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                            $sheet->setColumnFormat(array($arrayLetter[$lete].$row => '0%' ));
                            $row++;
                            $sheet->cell($arrayLetter[$lete].$row, "QTY");
                            $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $lete2 = $lete + 1;
                            $sheet->cell($arrayLetter[$lete2].$row, "Rate");
                            $sheet->getStyle($arrayLetter[$lete2].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                             $last = $lete;
                            $lete2 = $lete + 2;
                            $row++;
                            $lete = $lete+2;
                            $chester = $arrayLetter[$lete];
                        }
                        $lete++;
                        $row = 6;
                        $sheet->setColumnFormat(array($arrayLetter[$lete] => '0' ));
                        $sheet->cell($arrayLetter[$lete].$row, "OVERALL");
                        $ovc1 =$arrayLetter[$lete].$row;
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ovc2 = $arrayLetter[$l].$row;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                        $colorStart = $arrayLetter[$lete].$row;
                        $row = $row+2;
                        $Start = 'C'.$row;
                        $end = $arrayLetter[$last].$row;
                        $sheet->cell($arrayLetter[$lete].$row, "=SUM($Start:$end)");
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                        $row++;
                        $Start = 'C'.$row;
                        $end = $arrayLetter[$last].$row;
                        $sheet->cell($arrayLetter[$lete].$row, "=SUM($Start:$end)");
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                        $row++;
                        $Start = 'C'.$row;
                        $end = $arrayLetter[$last].$row;
                        $sheet->cell($arrayLetter[$lete].$row, "=SUM($Start:$end)/$counter");
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->setColumnFormat(array($arrayLetter[$lete].$row => '0%' ));
                        $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                        $row++;
                        $sheet->cell($arrayLetter[$lete].$row, "QTY");
                        $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $leter = $lete+1;
                        $sheet->cell($arrayLetter[$leter].$row, "Rates");
                        $sheet->cells($arrayLetter[$leter].$row, function($cells) {$cells->setFontWeight('bold'); });
                        $sheet->getStyle($arrayLetter[$leter].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $row++;
                       
                         
                     
                        //==================================================================DESIGN===================================================
                        
                       $Start = "A6";
                        $aa = 6;
                        $end = "$chester$aa";
                        $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#00CCFF'); });
                        $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });

                        $Start = "A7";
                        $aa = 7;
                        $end = "$chester$aa";
                        $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#00CCFF'); });
                        $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });

                        $Start = "B8";
                        $aa = 8;
                        $end = "$chester$aa";
                        $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#969696'); });

                        $Start = "B9";
                        $aa = 9;
                        $end = "$chester$aa";
                        $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#969696'); });

                        $Start = "B10";
                        $aa = 10;
                        $end = "$chester$aa";
                        $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#969696'); });
                        $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });

                        $Start = "A11";
                        $aa = 11;
                        $end = "$chester$aa";
                        $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });
                        $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#FFFF00'); });
                       
                        $sheet->cells("A8:A10", function($cells) {$cells->setBackground('#FFCC99'); });


                        //==================================================================DESIGN===================================================
                          $lete = 0;
                         $defe = 12; 
                        foreach ($Outdatass as $key => $val) {
                            $moDD = $val->mod;
                            $letes = $val->pono;
                            $roww = array_search($moDD, $ModHold);
                            $lete = array_search($letes, $ponoHold);
                            $lete = $lete - 3;
                            $sheet->cell($arrayLetter[$lete].$roww, $val->qty);
                            $sheet->getStyle($arrayLetter[$lete].$roww)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                            $lete2 = $lete + 1;
                            $rates = (($val->qty / $val->poqty)*100);
                             
                            $sheet->cell($arrayLetter[$lete2].$roww, $rates/1000);
                            $sheet->getStyle($arrayLetter[$lete2].$roww)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                          
                            $sheet->cell('A'.$defe, $ModHold[$defe]);
                            $defe++; 
                        }
                         //original end}
                         $row = $row+2;
                        
                          $sheet->cell('A'.$defe, "Total Defects");
                          $sheet->cells('A'.$defe, function($cells) {$cells->setFontWeight('bold'); });
                          $sheet->cells('A'.$defe, function($cells) {$cells->setBackground('#99CC00'); });
                          $lete = 0;
                          $row = $row-1;
                          $rowa = $row+1;
                          $rowMaintain =11;
                          $rowss = $rowMaintain + $ModHoldcount;
                          $rows1 = $rowss +1;
                          $ponocount = $ponocount*2;
                          
                          $sheet->cells('B'.$rows1, function($cells) {$cells->setBackground('#99CC00'); });
                          for($x=0;$x<$ponocount;$x++)
                          {
                          $Start = $arrayLetter[$lete].$rowMaintain;
                          $end = $arrayLetter[$lete].$rowss;
                          $sheet->cell($arrayLetter[$lete].$rows1, "=SUM($Start:$end)");
                          $sheet->cells($arrayLetter[$lete].$rows1, function($cells) {$cells->setBackground('#99CC00'); });
                        $sheet->getStyle($arrayLetter[$lete].$rows1)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->cells($arrayLetter[$lete].$rows1, function($cells) {$cells->setFontWeight('bold'); }); 
                        $lete++;
                          }
                        $sheet->cell('A6',"Device Name:");

                        $sheet->cell('A7',"PO Number:");
                        $sheet->cell('A8',"Total Input:");
                        $sheet->cell('A9',"Total Output");
                        $sheet->cell('A10',"Total Yield");
                        $sheet->cell('A11',"Defects:");

                        //=========================================OVERALL TOTAL==========================
                        $tempqty=0;
                        $temprate=0;
                        $s=0;
                        $s1 = 1;
                        $st = 12;
                       
                        for($y=0;$y<$ModHoldcount;$y++){
                            $temprate = 0;
                            $tempqty = 0;
                            $s = 0;
                            $s1 = 1;
                        for($x=0;$x<$counter;$x++)
                        {
                            $temprate += $sheet->getcell($arrayLetter[$s1].$st)->getCalculatedValue();
                            $tempqty += $sheet->getcell($arrayLetter[$s].$st)->getCalculatedValue();
                            $s = $s+2;
                            $s1 = $s1+2;
                        }
                        $s++;
                        $s1++;

                        $sheet->cell($arrayLetter[$s].$st, $tempqty);
                        $sheet->getStyle($arrayLetter[$s].$st)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->cells($arrayLetter[$s].$st, function($cells) {$cells->setFontWeight('bold'); }); 
                        $sheet->cell($arrayLetter[$s1].$st, $temprate);
                        $sheet->getStyle($arrayLetter[$s1].$st)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->cells($arrayLetter[$s1].$st, function($cells) {$cells->setFontWeight('bold'); }); 
                        $sheet->setColumnFormat(array($arrayLetter[$s1].$st => '0%'));
                        $st++;
                    }
                    $os = 11;
                    $minus = $st-1;
                    $Start = $arrayLetter[$s].$os;
                    $end = $arrayLetter[$s].$minus;
                    $Start2 = $arrayLetter[$s1].$os;
                    $end2 = $arrayLetter[$s1].$minus;
                    $st;  
                     $sheet->cell($arrayLetter[$s].$st, "=SUM($Start:$end)");
                     $sheet->cell($arrayLetter[$s1].$st, "=SUM($Start2:$end2)");
                     $l1 = $arrayLetter[$s].$st;
                     $l2 = $arrayLetter[$s1].$st;
                     $sheet->cells("$ovc1:$l1", function($cells) {$cells->setBackground('#99CC00'); });
                     $sheet->cells("$ovc2:$l2", function($cells) {$cells->setBackground('#99CC00'); });
                   

                     $sheet->setColumnFormat(array($arrayLetter[$s1].$st => '0%'));
                     $sheet->getStyle($arrayLetter[$s1].$st)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                     $sheet->cells($arrayLetter[$s1].$st, function($cells) {$cells->setFontWeight('bold'); }); 
                     $sheet->getStyle($arrayLetter[$s].$st)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                     $sheet->cells($arrayLetter[$s].$st, function($cells) {$cells->setFontWeight('bold'); }); 

                     $lete = 0;
                     $nine=9;
                     $twenty = 20;
                     $eight = 8;
                     for($x=0;$x<$ponocount/2;$x++){
                      $g = $sheet->getcell($arrayLetter[$lete].$nine)->getCalculatedValue();
                      $g2 = $sheet->getcell($arrayLetter[$lete].$twenty)->getCalculatedValue();
                     $sheet->cell($arrayLetter[$lete].$eight, $g+$g2);
                     $lete=$lete+2;
                     }
                    });
                })->download('xls');
            } else {
               //no data
            }
            
            
        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
         }
    }
     public function summaryREptpdf(Request $request)
     {
       try
        { 
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';
            $datefrom = $request->datefrom;
            $dateto = $request->dateto;
            $prodtype = $request->srprodtype;
            $check = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->where('prodtype',$prodtype)->count();
            if($check > 0){
            Excel::create('Summary_Report_'.$date, function($excel) use($request)
            {
                $excel->sheet('Sheet1', function($sheet) use($request)
                {
                    $sheet->setAutoSize(true);
                    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $prodtype = $request->srprodtype;
                    $family = $request->srfamily;
                    $sheet->setCellValue('A1', "$family Yield Summary");
                    $sheet->mergeCells('A1:B1');
                    $sheet->cell('A3',"Inclusive Date");
                    $date = date("Y-m-d");
                    $sheet->cell('A4',$date);
                    $sheet->cell('C3',"Date From");
                    $sheet->cell('D3',$datefrom);
                    $sheet->cell('C4',"Date To");
                    $sheet->cell('D4',$dateto);
                    $sheet->setHeight(1,30);

                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(15);
                        $row->setAlignment('center');
                        $row->setFontWeight('bold');
                    });
                   
                     $sheet->setStyle(array(
                            'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  10
                        )
                    ));

                     

                      $Outdatass = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                        ->select('family','device','yieldingno','pono','twoyield','poqty','mod','toutput','qty','twoyield',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"))
                        ->groupBy('mod')

                         ->whereBetween('productiondate', [$datefrom, $dateto])
                         ->where('prodtype',$prodtype)
                        ->get();

                     $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                        ->select('family','device','yieldingno','pono','twoyield','poqty','mod','toutput','qty','twoyield',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"))
                        ->groupBy('pono')

                         ->whereBetween('productiondate', [$datefrom, $dateto])
                         ->where('prodtype',$prodtype)
                        ->get();
                            $row = 6;
                            $ModHold = [];
                            $countMH = 12;
                             foreach ($Outdatass as $key => $val) { //GET MOD
                                    $ModHold[$countMH] = $val->mod;
                                    $countMH++;
                             }
                             $ModHoldcount = count($ModHold);
                             $modFixx = array_unique($ModHold);
                             $ponoHold = [];
                             $countpono = 3;
                             foreach ($Outdata as $key => $val) { //GET PONO
                                    $ponoHold[$countpono] = $val->pono;
                                    $countpono = $countpono+2;
                             }
                             $ponocount = count($ponoHold);
                             $ponoFixx = array_unique($ponoHold);
                      $arrayLetter = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ");
                      $con = count($arrayLetter)/3;
                      $r=1;
                      for($x=0;$x<$con;$x++){
                        
                       $sheet->setColumnFormat(array(
                        $arrayLetter[$r] => '0%'
                         ));
                       $r=$r+2;
                      }
                      $lete = 0;
                      $defe = 12; 
                      $rowMaintain = 12;
                        

                      $counter = 0;
                    foreach ($Outdata as $key => $val) {
                        $row = 6;
                        $sheet->cell($arrayLetter[$lete].$row, $val->device);
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $counter++;
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $row++;
                        $sheet->cell($arrayLetter[$lete].$row, $val->pono);
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $row++;
                        $sheet->cell($arrayLetter[$lete].$row, $val->poqty);
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                        $row++;
                        $sheet->cell($arrayLetter[$lete].$row, $val->accumulatedoutput);
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                        $row++;    
                        $sheet->cell($arrayLetter[$lete].$row, $val->twoyield/100);
                        $ov1 = $arrayLetter[$lete].$row;
                        $l = $lete+1;
                        $ov2 = $arrayLetter[$l].$row;
                        $sheet->mergeCells("$ov1:$ov2");
                        $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                        $sheet->setColumnFormat(array($arrayLetter[$lete].$row => '0%' ));
                        $row++;
                        $sheet->cell($arrayLetter[$lete].$row, "QTY");
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $lete2 = $lete + 1;
                        $sheet->cell($arrayLetter[$lete2].$row, "Rate");
                        $sheet->getStyle($arrayLetter[$lete2].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                         $last = $lete;
                        $lete2 = $lete + 2;
                        $row++;
                        $lete = $lete+2;
                        $chester = $arrayLetter[$lete];
                    }
                    $lete++;
                    $row = 6;
                    $sheet->setColumnFormat(array($arrayLetter[$lete] => '0' ));
                    $sheet->cell($arrayLetter[$lete].$row, "OVERALL");
                    $ovc1 =$arrayLetter[$lete].$row;
                    $ov1 = $arrayLetter[$lete].$row;
                    $l = $lete+1;
                    $ovc2 = $arrayLetter[$l].$row;
                    $ov2 = $arrayLetter[$l].$row;
                    $sheet->mergeCells("$ov1:$ov2");
                    $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                    $colorStart = $arrayLetter[$lete].$row;
                    $row = $row+2;
                    $Start = 'C'.$row;
                    $end = $arrayLetter[$last].$row;
                    $sheet->cell($arrayLetter[$lete].$row, "=SUM($Start:$end)");
                    $ov1 = $arrayLetter[$lete].$row;
                    $l = $lete+1;
                    $ov2 = $arrayLetter[$l].$row;
                    $sheet->mergeCells("$ov1:$ov2");
                    $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                    $row++;
                    $Start = 'C'.$row;
                    $end = $arrayLetter[$last].$row;
                    $sheet->cell($arrayLetter[$lete].$row, "=SUM($Start:$end)");
                    $ov1 = $arrayLetter[$lete].$row;
                    $l = $lete+1;
                    $ov2 = $arrayLetter[$l].$row;
                    $sheet->mergeCells("$ov1:$ov2");
                    $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                    $row++;
                    $Start = 'C'.$row;
                    $end = $arrayLetter[$last].$row;
                    $sheet->cell($arrayLetter[$lete].$row, "=SUM($Start:$end)/$counter");
                    $ov1 = $arrayLetter[$lete].$row;
                    $l = $lete+1;
                    $ov2 = $arrayLetter[$l].$row;
                    $sheet->mergeCells("$ov1:$ov2");
                    $sheet->getStyle("$ov1:$ov2")->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->setColumnFormat(array($arrayLetter[$lete].$row => '0%' ));
                    $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                    $row++;
                    $sheet->cell($arrayLetter[$lete].$row, "QTY");
                    $sheet->cells($arrayLetter[$lete].$row, function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $leter = $lete+1;
                    $sheet->cell($arrayLetter[$leter].$row, "Rates");
                    $sheet->cells($arrayLetter[$leter].$row, function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle($arrayLetter[$leter].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $row++;
                   
                     
                 
                    //==================================================================DESIGN===================================================
                    
                   $Start = "A6";
                    $aa = 6;
                    $end = "$chester$aa";
                    $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#00CCFF'); });
                    $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });

                    $Start = "A7";
                    $aa = 7;
                    $end = "$chester$aa";
                    $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#00CCFF'); });
                    $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });

                    $Start = "B8";
                    $aa = 8;
                    $end = "$chester$aa";
                    $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#969696'); });

                    $Start = "B9";
                    $aa = 9;
                    $end = "$chester$aa";
                    $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#969696'); });

                    $Start = "B10";
                    $aa = 10;
                    $end = "$chester$aa";
                    $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#969696'); });
                    $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });

                    $Start = "A11";
                    $aa = 11;
                    $end = "$chester$aa";
                    $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->cells("$Start:$end", function($cells) {$cells->setBackground('#FFFF00'); });
                   
                    $sheet->cells("A8:A10", function($cells) {$cells->setBackground('#FFCC99'); });


                    //==================================================================DESIGN===================================================
                      $lete = 0;
                     $defe = 12; 
                    foreach ($Outdatass as $key => $val) {
                        $moDD = $val->mod;
                        $letes = $val->pono;
                        $roww = array_search($moDD, $ModHold);
                        $lete = array_search($letes, $ponoHold);
                        $lete = $lete - 3;
                        $sheet->cell($arrayLetter[$lete].$roww, $val->qty);
                        $sheet->getStyle($arrayLetter[$lete].$roww)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                        $lete2 = $lete + 1;
                        $rates = (($val->qty / $val->poqty)*100);
                         
                        $sheet->cell($arrayLetter[$lete2].$roww, $rates/1000);
                        $sheet->getStyle($arrayLetter[$lete2].$roww)->getAlignment()->applyFromArray(array('horizontal' => 'center'));  
                      
                        $sheet->cell('A'.$defe, $ModHold[$defe]);
                        $defe++; 
                    }
                     //original end}
                     $row = $row+2;
                    
                      $sheet->cell('A'.$defe, "Total Defects");
                      $sheet->cells('A'.$defe, function($cells) {$cells->setFontWeight('bold'); });
                      $sheet->cells('A'.$defe, function($cells) {$cells->setBackground('#99CC00'); });
                      $lete = 0;
                      $row = $row-1;
                      $rowa = $row+1;
                      $rowMaintain =11;
                      $rowss = $rowMaintain + $ModHoldcount;
                      $rows1 = $rowss +1;
                      $ponocount = $ponocount*2;
                      
                      $sheet->cells('B'.$rows1, function($cells) {$cells->setBackground('#99CC00'); });
                      for($x=0;$x<$ponocount;$x++)
                      {
                      $Start = $arrayLetter[$lete].$rowMaintain;
                      $end = $arrayLetter[$lete].$rowss;
                      $sheet->cell($arrayLetter[$lete].$rows1, "=SUM($Start:$end)");
                      $sheet->cells($arrayLetter[$lete].$rows1, function($cells) {$cells->setBackground('#99CC00'); });
                    $sheet->getStyle($arrayLetter[$lete].$rows1)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->cells($arrayLetter[$lete].$rows1, function($cells) {$cells->setFontWeight('bold'); }); 
                    $lete++;
                      }
                    $sheet->cell('A6',"Device Name:");

                    $sheet->cell('A7',"PO Number:");
                    $sheet->cell('A8',"Total Input:");
                    $sheet->cell('A9',"Total Output");
                    $sheet->cell('A10',"Total Yield");
                    $sheet->cell('A11',"Defects:");

                    //=========================================OVERALL TOTAL==========================
                    $tempqty=0;
                    $temprate=0;
                    $s=0;
                    $s1 = 1;
                    $st = 12;
                   
                    for($y=0;$y<$ModHoldcount;$y++){
                        $temprate = 0;
                        $tempqty = 0;
                        $s = 0;
                        $s1 = 1;
                    for($x=0;$x<$counter;$x++)
                    {
                        $temprate += $sheet->getcell($arrayLetter[$s1].$st)->getCalculatedValue();
                        $tempqty += $sheet->getcell($arrayLetter[$s].$st)->getCalculatedValue();
                        $s = $s+2;
                        $s1 = $s1+2;
                    }
                    $s++;
                    $s1++;

                    $sheet->cell($arrayLetter[$s].$st, $tempqty);
                    $sheet->getStyle($arrayLetter[$s].$st)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->cells($arrayLetter[$s].$st, function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->cell($arrayLetter[$s1].$st, $temprate);
                    $sheet->getStyle($arrayLetter[$s1].$st)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->cells($arrayLetter[$s1].$st, function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->setColumnFormat(array($arrayLetter[$s1].$st => '0%'));
                    $st++;
                }
                $os = 11;
                $minus = $st-1;
                $Start = $arrayLetter[$s].$os;
                $end = $arrayLetter[$s].$minus;
                $Start2 = $arrayLetter[$s1].$os;
                $end2 = $arrayLetter[$s1].$minus;
                $st;  
                 $sheet->cell($arrayLetter[$s].$st, "=SUM($Start:$end)");
                 $sheet->cell($arrayLetter[$s1].$st, "=SUM($Start2:$end2)");
                 $l1 = $arrayLetter[$s].$st;
                 $l2 = $arrayLetter[$s1].$st;
                 $sheet->cells("$ovc1:$l1", function($cells) {$cells->setBackground('#99CC00'); });
                 $sheet->cells("$ovc2:$l2", function($cells) {$cells->setBackground('#99CC00'); });
               

                 $sheet->setColumnFormat(array($arrayLetter[$s1].$st => '0%'));
                 $sheet->getStyle($arrayLetter[$s1].$st)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                 $sheet->cells($arrayLetter[$s1].$st, function($cells) {$cells->setFontWeight('bold'); }); 
                 $sheet->getStyle($arrayLetter[$s].$st)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                 $sheet->cells($arrayLetter[$s].$st, function($cells) {$cells->setFontWeight('bold'); });  

                  
                    //=======================================//
                    $arrayLetterpdf =  array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                    $defe = 6;
                    
                    $countpono = 7 + $ModHoldcount;
                    $rowss = $countpono+1;
                    for($x=0;$x<=$countpono;$x++){
                        for($y=0;$y<=$rowss;$y++){
                    $rowww[$defe][$y] = $sheet->getcell($arrayLetterpdf[$y].$defe)->getCalculatedValue();
                         }
                         $defe++;
                    }
                        
                    //$sheet->setCellValue('H26', $rowww[6][0]);
                  
                    $html9 = "";
                    $deviceN = 6;

                     $a = count($rowww)-3;
                     $a = $a;
                     $startc = 12;
                     $ponocount = $ponocount+2;
                    for($x=0;$x<$countpono;$x++){
                        $html9 .= '<tr>';
                        for($y=0;$y<$a;$y++){
                            $html9 .= '<td>'.$rowww[$deviceN][$y].'</td>';
                        }
                        $deviceN++;
                        $html9 .= '</tr>';
                    }
                    //FOR TOTAL
                    $endC = $startc + $ModHoldcount;
                    $totalcontainer = [];
                    $total = 0;
                
                        $dt = Carbon::now();
                        $dompdf = new Dompdf();
                        $date = substr($dt->format('Ymd'), 2);
                        $html = '<div class="container-fluid" style="font-size:10">
                                                        <h2> YIELD SUMMARY</h2>
                                                        <p>Inclusive Date</p>
                                                        </br>
                                                        <p style="padding-left:6em;">Date From:'.$datefrom.'</p>
                                                        <p style="padding-left:6em;">Date From:'.$dateto.'</p>
                                                        <div style="width: 100%;margin-top: 3%;">
                                                            <table border="1" style="font-size:10">
                                                             '.$html9.'
                                                                </table>
                                                            </div>
                                                            <hr/>
                                                            <p align="right">
                                                                <strong>DATE CREATED:</strong> '.$dt->format('l jS \\of F Y h:i:s A').'
                                                            </p>
                                                        </div>';

         $dompdf->loadHtml($html);

        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
       return $dompdf->stream('Summary Report'.Carbon::now().'.pdf');
                });
            });
            }
            else{
                // no DATA
            }

        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
         }
    }

 


    public function defectsummaryRpt(Request $request)
    {

        try
        { 
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';
            $datefrom = $request->datefrom;
            $dateto = $request->dateto;
            $ptype = $request->ptype;
            $check = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->where('prodtype',$ptype)->count();
            $check1 = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->count();
            if($check > 0 || $check1 > 0){
            Excel::create('Defect_Summary_Report_'.$date, function($excel) use($request)
            {
                $excel->sheet('Sheet1', function($sheet) use($request)
              {

                    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $ptype = $request->ptype;
                    $option = $request->option;
                    $ptype = $request->ptype;
                   // $sheet->cell('Q4',$ptype);
                    $sheet->setAutoSize(true);
                    $sheet->setCellValue('A1', 'Defect Summary Per Family');
                    $sheet->mergeCells('A1:D1');
                    $sheet->cell('A3',"DATE");
                    $date = date("Y-m-d");
                    $sheet->cell('B3',$date);
                    $sheet->cell('E3',"Date Froms");
                    $sheet->cell('F3',$datefrom);
                    $sheet->cell('E4',"Date To");
                    $sheet->cell('F4',$dateto);
                    //$sheet->cell('A6',"PO No.");
                    
                  
                    $sheet->setHeight(1,30);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(15);
                        $row->setAlignment('left');
                    });

                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  10
                        )
                    ));
                   
                                                    $sheet->cell('B6',"Defectives");
                                                    $sheet->cells('B6', function($cells) {$cells->setFontWeight('bold'); });
                                                    $sheet->getStyle('B6')->getAlignment()->setTextRotation(90);
                                                        $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                                                        ->select('family','device','yieldingno','ywomng','pono','twoyield','poqty','mod',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"),DB::raw("COUNT(a.mod) as wew"),DB::raw("SUM(poqty) as sumpoqty"))
                                                        ->groupBy('mod')
                                                        ->orderBy('family')
                                                        ->whereBetween('productiondate', [$datefrom, $dateto])
                                                        ->get();

                                                        $Outdatass = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                                                        ->select('family')
                                                        ->groupBy('family')
                                                       // ->orderBy('family')
                                                        ->whereBetween('productiondate', [$datefrom, $dateto])
                                                        ->get();

                                                     $arrayLetter = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                 if($ptype == "Test Socket")
                   {
                                                      $lete = 0;
                                                      $deff = 0;
                                                      $defe = 0;
                                                     $modOfD = [];

                                                     $row = 2;
                                                     foreach ($Outdata as $key => $val) {
                                                        $modOfD[$row] = $val->mod;
                                                        $row++;
                                                     }
                                                    $Start = "B6";
                                                    $end = $arrayLetter[$row];
                                                    $a = 6;
                                                    $sheet->cells("$Start:$end$a", function($cells) {$cells->setFontWeight('bold'); });
                                                    $defe = 6;
                                                    $y = 2;
                                                    $countmod = count($modOfD);
                                                    for($x = 0 ; $x < $countmod; $x++){
                                                         $sheet->cell($arrayLetter[$x].$defe, $modOfD[$y]);
                                                         $sheet->getStyle($arrayLetter[$x].$defe)->getAlignment()->setTextRotation(90);
                                                         $y++;
                                                    }
                                                    $last = $y;
                                                    //FOR MOD

                                                    $defe = 7;
                                                    foreach ($Outdatass as $key => $val) {
                                                        $fams[$defe] = $val->family;
                                                        $defe++;
                                                     }
                                                    $defe = 7;
                                                   $countfam = count($fams);
                                                    for($x = 0 ; $x < $countfam; $x++){
                                                         $sheet->cell('B'.$defe, $fams[$defe]);
                                                        $defe++;
                                                    }
                                                    $Start = "B7";
                                                    $end = "B$defe";
                                                    $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });
                                                     $sheet->cell('B'.$defe, "TOTAL");
                                                     $l = $defe;
                                                    //FOR FAMILY

                                                     foreach ($Outdata as $key => $val) {
                                                        $famtemp = $val->family;
                                                        $modtemp = $val->mod;
                                                     $key = array_search($famtemp, $fams);
                                                     $key2 = array_search($modtemp, $modOfD);
                                                     $key2 = $key2 - 2;
                                                     $sheet->cell($arrayLetter[$key2].$key, $val->wew);
                                                 }
                                                 //FOR DATA
                                                 $defe = 7;

                                                   $last2 = $l-1;

                                                    for($x=0;$x<$countmod;$x++)
                                                    {
                                                       $start = $arrayLetter[$x].$defe;
                                                       $end = $arrayLetter[$x].$last2;
                                                       $sheet->setCellValue($arrayLetter[$x].$l, "=SUM($start:$end)");
                                                       $sheet->cells($arrayLetter[$x].$l, function($cells) {$cells->setFontWeight('bold'); });
                                                    }
                }
                else{
                     $sheet->cell('B6',"Defects");
                     $sheet->cell('B7',"PNG");
                     $sheet->cell('B8',"MNG");
                     $sheet->cell('B9',"TOTAL");
                     $sheet->cells('B6:B9', function($cells) {$cells->setFontWeight('bold'); });

                  
                   
                         $row = 0;
                         $modofD = [];
                         foreach ($Outdata as $key => $val) {
                            $modOfD[$row] = $val->mod;
                            $row++;
                         }
                        $countmod = count($modOfD);
                        $y=0;
                        $defe = 6;
                        for($x = 0 ; $x < $countmod; $x++){
                             $sheet->cell($arrayLetter[$x].$defe, $modOfD[$y]);
                             $sheet->getStyle($arrayLetter[$x].$defe)->getAlignment()->setTextRotation(90);
                             $sheet->cells($arrayLetter[$x].$defe, function($cells) {$cells->setFontWeight('bold'); });
                             $y++;
                        }
                         $sheet->getStyle('B6')->getAlignment()->setTextRotation(0);
                         $PNGC = [];
                         for($x=0;$x<$countmod;$x++)
                         {
                             $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                            ->select(DB::raw("COUNT(*) as wew"),'mod')
                            ->where('mod',$modOfD[$x])
                            ->where('classification','like','%Production%')
                            ->whereBetween('productiondate', [$datefrom, $dateto])
                            ->orderBy('family')
                            ->get();
                            foreach ($Outdatas as $key => $value) {
                               $PNGC[$x]  = $value->wew;
                            }
                        }
                        $MNGC = [];
                        for($x=0;$x<$countmod;$x++)
                         {
                             $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                            ->select(DB::raw("COUNT(*) as wew"),'mod')
                            ->where('mod',$modOfD[$x])
                            ->where('classification','like','%Material%')
                            ->whereBetween('productiondate', [$datefrom, $dateto])
                            ->orderBy('family')
                            ->get();
                            foreach ($Outdatas as $key => $value) {
                               $MNGC[$x]  = $value->wew;
                            }
                        }


                        $defe=7;
                        for($x = 0 ; $x < $countmod; $x++){
                             $sheet->cell($arrayLetter[$x].$defe, $PNGC[$x]);
                        }

                        $defe=8;
                        for($x = 0 ; $x < $countmod; $x++){
                             $sheet->cell($arrayLetter[$x].$defe, $MNGC[$x]);
                        }

                        $defe=9;
                        $seven = 7;
                        $eight = 8;
                        for($x = 0 ; $x < $countmod; $x++){
                            $start = $arrayLetter[$x].$seven;
                            $end = $arrayLetter[$x].$eight;
                         $sheet->setCellValue($arrayLetter[$x].$defe, "=SUM($start:$end)");
                        }


                    $sheet->cell('A12',"Total Input:");
                    $sheet->cell('A13',"Total Output");
                    $sheet->cell('A14',"Total PNG");
                    $sheet->cell('A15',"Total MNG");
                    $sheet->cell('A16',"Yield W/o MNG");
                    $sheet->cell('A17',"Total Yield");
                    $sheet->cells("A17", function($cells) {$cells->setFontColor('#FF0000'); });
                    $sheet->cells('A12:A17', function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->cells("A12:A17", function($cells) {$cells->setBackground('#CCFFCC'); });
                   foreach ($Outdata as $key => $val) {
                    
                        $sheet->cell('B12', $val->sumpoqty);
                        $sheet->cell('B13', $val->accumulatedoutput);
                        $start = $arrayLetter[0].$seven;
                        $end = $arrayLetter[$countmod].$seven;
                        $sheet->setCellValue('B14', "=SUM($start:$end)");
                        $start = $arrayLetter[0].$eight;
                        $end = $arrayLetter[$countmod].$eight;
                        $sheet->setCellValue('B15', "=SUM($start:$end)");
                        $sheet->cell('B16', $val->ywomng);
                        $sheet->cell('B17', $val->twoyield);
                    }
                    $sheet->getStyle('B12:B17')->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->cells('B12:B17', function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->setColumnFormat(array(
                        'B16' => '0%',
                        'B17' => '0%',
                         ));
                     $sheet->cells("B17", function($cells) {$cells->setFontColor('#FF0000'); });

                }

                 
                $a  = $sheet->getcell('B13')->getCalculatedValue();
                $a1 = $sheet->getcell('B14')->getCalculatedValue();
                $a2 = $sheet->getcell('B15')->getCalculatedValue();
                $sheet->cell('B12', $a+$a1+$a2);
               
           
                });

            })->download('xls');
            }
            else{
                //no DATA
            }



        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
        }
    }

 public function defectsummaryRptpdf(Request $request){
       
        try
        { 
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';
            $datefrom = $request->datefrom;
            $dateto = $request->dateto;
            $ptype = $request->ptype;
            $check = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->where('prodtype',$ptype)->count();
            if($check > 0)
            {
            Excel::create('Defect_Summary_Report_'.$date, function($excel) use($request)
            {
                $excel->sheet('Sheet1', function($sheet) use($request)
              {

                    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $ptype = $request->ptype;
                    $option = $request->option;
                    $ptype = $request->ptype;
                   // $sheet->cell('Q4',$ptype);
                    $sheet->setAutoSize(true);
                    $sheet->setCellValue('A1', 'Defect Summary Per Family');
                    $sheet->mergeCells('A1:D1');
                    $sheet->cell('A3',"DATE");
                    $date = date("Y-m-d");
                    $sheet->cell('B3',$date);
                    $sheet->cell('E3',"Date Froms");
                    $sheet->cell('F3',$datefrom);
                    $sheet->cell('E4',"Date To");
                    $sheet->cell('F4',$dateto);
                    //$sheet->cell('A6',"PO No.");
                    
                  
                    $sheet->setHeight(1,30);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(15);
                        $row->setAlignment('left');
                    });

                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  10
                        )
                    ));
                   
                                                    $sheet->cell('B6',"Defectives");
                                                    $sheet->cells('B6', function($cells) {$cells->setFontWeight('bold'); });
                                                    $sheet->getStyle('B6')->getAlignment()->setTextRotation(90);
                                                        $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                                                        ->select('family','device','yieldingno','ywomng','pono','twoyield','poqty','mod',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"),DB::raw("COUNT(a.mod) as wew"),DB::raw("SUM(poqty) as sumpoqty"))
                                                        ->groupBy('mod')
                                                        ->orderBy('family')
                                                        ->whereBetween('productiondate', [$datefrom, $dateto])
                                                        ->get();

                                                        $Outdatass = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                                                        ->select('family')
                                                        ->groupBy('family')
                                                       // ->orderBy('family')
                                                        ->whereBetween('productiondate', [$datefrom, $dateto])
                                                        ->get();

                                                     $arrayLetter = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                 if($ptype == "Test Socket")
                   {
                                                      $lete = 0;
                                                      $deff = 0;
                                                      $defe = 0;
                                                     $modOfD = [];

                                                     $row = 2;
                                                     foreach ($Outdata as $key => $val) {
                                                        $modOfD[$row] = $val->mod;
                                                        $row++;
                                                     }
                                                    $Start = "B6";
                                                    $end = $arrayLetter[$row];
                                                    $a = 6;
                                                    $sheet->cells("$Start:$end$a", function($cells) {$cells->setFontWeight('bold'); });
                                                    $defe = 6;
                                                    $y = 2;
                                                    $countmod = count($modOfD);
                                                    for($x = 0 ; $x < $countmod; $x++){
                                                         $sheet->cell($arrayLetter[$x].$defe, $modOfD[$y]);
                                                         $sheet->getStyle($arrayLetter[$x].$defe)->getAlignment()->setTextRotation(90);
                                                         $y++;
                                                    }
                                                    $last = $y;
                                                    //FOR MOD

                                                    $defe = 7;
                                                    foreach ($Outdatass as $key => $val) {
                                                        $fams[$defe] = $val->family;
                                                        $defe++;
                                                     }
                                                    $defe = 7;
                                                   $countfam = count($fams);
                                                    for($x = 0 ; $x < $countfam; $x++){
                                                         $sheet->cell('B'.$defe, $fams[$defe]);
                                                        $defe++;
                                                    }
                                                    $Start = "B7";
                                                    $end = "B$defe";
                                                    $sheet->cells("$Start:$end", function($cells) {$cells->setFontWeight('bold'); });
                                                     $sheet->cell('B'.$defe, "TOTAL");
                                                     $l = $defe;
                                                    //FOR FAMILY

                                                     foreach ($Outdata as $key => $val) {
                                                        $famtemp = $val->family;
                                                        $modtemp = $val->mod;
                                                     $key = array_search($famtemp, $fams);
                                                     $key2 = array_search($modtemp, $modOfD);
                                                     $key2 = $key2 - 2;
                                                     $sheet->cell($arrayLetter[$key2].$key, $val->wew);
                                                 }
                                                 //FOR DATA
                                                 $defe = 7;

                                                   $last2 = $l-1;

                                                    for($x=0;$x<$countmod;$x++)
                                                    {
                                                       $start = $arrayLetter[$x].$defe;
                                                       $end = $arrayLetter[$x].$last2;
                                                       $sheet->setCellValue($arrayLetter[$x].$l, "=SUM($start:$end)");
                                                    }
                                                }
                else{
                     $sheet->cell('B6',"Defects");
                     $sheet->cell('B7',"PNG");
                     $sheet->cell('B8',"MNG");
                     $sheet->cell('B9',"TOTAL");
                     $sheet->cells('B6:B9', function($cells) {$cells->setFontWeight('bold'); });

                  
                   
                         $row = 0;
                         $modofD = [];
                         foreach ($Outdata as $key => $val) {
                            $modOfD[$row] = $val->mod;
                            $row++;
                         }
                        $countmod = count($modOfD);
                        $y=0;
                        $defe = 6;
                        for($x = 0 ; $x < $countmod; $x++){
                             $sheet->cell($arrayLetter[$x].$defe, $modOfD[$y]);
                             $sheet->getStyle($arrayLetter[$x].$defe)->getAlignment()->setTextRotation(90);
                             $sheet->cells($arrayLetter[$x].$defe, function($cells) {$cells->setFontWeight('bold'); });
                             $y++;
                        }
                         $sheet->getStyle('B6')->getAlignment()->setTextRotation(0);
                         $PNGC = [];
                         for($x=0;$x<$countmod;$x++)
                         {
                             $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                            ->select(DB::raw("COUNT(*) as wew"),'mod')
                            ->where('mod',$modOfD[$x])
                            ->where('classification','like','%Production%')
                            ->whereBetween('productiondate', [$datefrom, $dateto])
                            ->orderBy('family')
                            ->get();
                            foreach ($Outdatas as $key => $value) {
                               $PNGC[$x]  = $value->wew;
                            }
                        }
                        $MNGC = [];
                        for($x=0;$x<$countmod;$x++)
                         {
                             $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                            ->select(DB::raw("COUNT(*) as wew"),'mod')
                            ->where('mod',$modOfD[$x])
                            ->where('classification','like','%Material%')
                            ->whereBetween('productiondate', [$datefrom, $dateto])
                            ->orderBy('family')
                            ->get();
                            foreach ($Outdatas as $key => $value) {
                               $MNGC[$x]  = $value->wew;
                            }
                        }


                        $defe=7;
                        for($x = 0 ; $x < $countmod; $x++){
                             $sheet->cell($arrayLetter[$x].$defe, $PNGC[$x]);
                        }

                        $defe=8;
                        for($x = 0 ; $x < $countmod; $x++){
                             $sheet->cell($arrayLetter[$x].$defe, $MNGC[$x]);
                        }

                        $defe=9;
                        $seven = 7;
                        $eight = 8;
                        for($x = 0 ; $x < $countmod; $x++){
                            $start = $arrayLetter[$x].$seven;
                            $end = $arrayLetter[$x].$eight;
                         $sheet->setCellValue($arrayLetter[$x].$defe, "=SUM($start:$end)");
                        }


                    $sheet->cell('A12',"Total Input:");
                    $sheet->cell('A13',"Total Output");
                    $sheet->cell('A14',"Total PNG");
                    $sheet->cell('A15',"Total MNG");
                    $sheet->cell('A16',"Yield W/o MNG");
                    $sheet->cell('A17',"Total Yield");
                    $sheet->cells("A17", function($cells) {$cells->setFontColor('#FF0000'); });
                    $sheet->cells('A12:A17', function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->cells("A12:A17", function($cells) {$cells->setBackground('#CCFFCC'); });
                   foreach ($Outdata as $key => $val) {
                    
                        $sheet->cell('B12', $val->sumpoqty);
                        $sheet->cell('B13', $val->accumulatedoutput);
                        $start = $arrayLetter[0].$seven;
                        $end = $arrayLetter[$countmod].$seven;
                        $sheet->setCellValue('B14', "=SUM($start:$end)");
                        $start = $arrayLetter[0].$eight;
                        $end = $arrayLetter[$countmod].$eight;
                        $sheet->setCellValue('B15', "=SUM($start:$end)");
                        $sheet->cell('B16', $val->ywomng);
                        $sheet->cell('B17', $val->twoyield);
                    }
                    $sheet->getStyle('B12:B17')->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $sheet->cells('B12:B17', function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->setColumnFormat(array(
                        'B16' => '0%',
                        'B17' => '0%',
                         ));
                     $sheet->cells("B17", function($cells) {$cells->setFontColor('#FF0000'); });
                }

                    //=========================
                    $arrayLetterpdf = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                    
                     if($ptype == "Test Socket"){
                    $html = "";
                    $html8 = "";
                    $html9 = "";
                    $html10 = "";
                    $defe = 6;

                    $countfam = $countfam+2;
                    $countmod = $countmod+2;
                    for($x=0;$x<$countfam;$x++){
                        for($y=0;$y<$countmod;$y++){
                    $rowww[$x][$y] = $sheet->getcell($arrayLetterpdf[$y].$defe)->getCalculatedValue();
                                 }
                                  $defe++;
                    }
                    
                    $html9 = "";
                    for($x=0;$x<$countfam;$x++){
                        $html9 .= '<tr>';
                        for($y=1;$y<$countmod;$y++){
                            $html9 .= '<td>'.$rowww[$x][$y].'</td>';
                        }
                        $defe++;
                        $html9 .= '</tr>';
                    }
                        
                        $dt = Carbon::now();
                        $dompdf = new Dompdf();
                        $date = substr($dt->format('Ymd'), 2);
                        $html = '<div class="container-fluid" style="font-size:10">
                                                        <h2> DEFECTS SUMMARY</h2>
                                                        <p>Inclusive Date</p>
                                                        </br>
                                                        <p style="padding-left:6em;">Date From:'.$datefrom.'</p>
                                                        <p style="padding-left:6em;">Date From:'.$dateto.'</p>
                                                        <div style="width: 100%;margin-top: 3%;">
                                                            <table border="1" style="font-size:10">
                                                            '.$html8.'
                                                               '.$html9.'
                                                                </table>
                                                            </div>
                                                            <hr/>
                                                            <p align="right">
                                                                <strong>DATE CREATED:</strong> '.$dt->format('l jS \\of F Y h:i:s A').'
                                                            </p>
                                                        </div>';

         $dompdf->loadHtml($html);

        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        return $dompdf->stream('Defects Summary Report'.Carbon::now().'.pdf');
    }
    else{

           $html = "";
                    $html8 = "";
                    $html9 = "";
                    $html10 = "";
                    $defe = 6;

                    $countfam = 12;
                    $countmod = 7;
                    for($x=0;$x<$countfam;$x++){
                        for($y=0;$y<$countmod;$y++){
                    $rowww[$x][$y] = $sheet->getcell($arrayLetterpdf[$y].$defe)->getCalculatedValue();
                                 }
                                  $defe++;
                    }
                    
                    $html9 = "";
                    for($x=0;$x<$countfam;$x++){
                        $html9 .= '<tr>';
                        for($y=0;$y<$countmod;$y++){
                            $html9 .= '<td>'.$rowww[$x][$y].'</td>';
                        }
                        $defe++;
                        $html9 .= '</tr>';
                    }
                        
                        $dt = Carbon::now();
                        $dompdf = new Dompdf();
                        $date = substr($dt->format('Ymd'), 2);
                        $html = '<div class="container-fluid" style="font-size:10">
                                                        <h2> DEFECTS SUMMARY</h2>
                                                        <p>Inclusive Date</p>
                                                        </br>
                                                        <p style="padding-left:6em;">Date From:'.$datefrom.'</p>
                                                        <p style="padding-left:6em;">Date From:'.$dateto.'</p>
                                                        <div style="width: 100%;margin-top: 3%;">
                                                            <table border="1" style="font-size:10">
                                                            '.$html8.'
                                                               '.$html9.'
                                                                </table>
                                                            </div>
                                                            <hr/>
                                                            <p align="right">
                                                                <strong>DATE CREATED:</strong> '.$dt->format('l jS \\of F Y h:i:s A').'
                                                            </p>
                                                        </div>';

         $dompdf->loadHtml($html);

        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
        return $dompdf->stream('Defects Summary Report'.Carbon::now().'.pdf');




    }
    });
  });//->download('xls');
}
else{
    //NO DATA
}
        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
        }

}

   public function yieldsumfamRpt(Request $request){
  try
        { 
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';
            $datefrom = $request->datefrom;
             $dateto = $request->dateto;
             $yieldtarget = $request->yieldtarget;
             $chosen = $request->chosen;
             $ptype = $request->ptype;

             $check = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->where('prodtype',$ptype)->count();
             $check1 = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->count();
              $check2 = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->where('yieldtarget',$yieldtarget)->count();
            if($check > 0 || $check1 > 0 || $check2){
            
            Excel::create('Yield_Summary_Family_Report_'.$date, function($excel) use($request)
            {
                $excel->sheet('Sheet1', function($sheet) use($request)
                {
                     $sheet->setAutoSize(true);
                     $datefrom = $request->datefrom;
                     $dateto = $request->dateto;
                     $yieldtarget = $request->yieldtarget;
                     $chosen = $request->chosen;
                     $ptype = $request->ptype;


                      $Outdata = DB::connection($this->mysql)->table('tbl_targetregistration')
                        ->select('dppm','yield')
                        ->where('yield',$yieldtarget)
                        ->get();
                         $tardppm = 0;
                        foreach ($Outdata as $key => $value) {
                            $tardppm = $value->dppm;
                        }
                
                    $sheet->setCellValue('A1', 'Yield Target Summary Per Family - '. $ptype);
                    $sheet->mergeCells('A1:E1');
                    $sheet->cell('A3',"DATE");
                    $date = date("Y-m-d");
                    $sheet->cell('B3',$date);
                    $sheet->cell('E3',"Date Froms");
                    $sheet->cell('F3',$datefrom);
                    $sheet->cell('E4',"Date To");
                    $sheet->cell('F4',$dateto);
                    $sheet->cell('A6',"Yield Target");
                        $sheet->cell('B6',$yieldtarget);
                    $sheet->cell('A7',"DPPM Target");
                        $sheet->cell('B7',$tardppm);
                    $sheet->getStyle('B6:B7')->getAlignment()->applyFromArray(array('horizontal' => 'center'));
                    $sheet->cells('B6:B7', function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->cell('A8',"Family");
                    $sheet->cell('A9',"Input");
                    $sheet->cell('A10',"Output");
                    $sheet->cell('A11',"Production NG");
                    $sheet->cell('A12',"Material NG");
                    $sheet->cell('A13',"Yield W/o MNG");
                    $sheet->cell('A14',"Total Yield (%)");
                    $sheet->cell('A15',"DPPM");
                    $sheet->setHeight(1,30);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(15);
                        $row->setAlignment('left');
                    });


                
                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  10
                        )
                    ));
                    $row = 2;
                    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $yieldtarget = $request->yieldtarget;
                    $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                        //->select('family',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"),DB::raw("SUM(toutput) as toutput"),'ywomng')
                        ->select('family',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"),'toutput','ywomng','tpng')
                        ->groupBy('family')
                        ->orderBy('family')
                         ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();

                     $arrayLetter = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                         $modOfFam = [];
                      $fff = 0;
                     foreach ($Outdata as $key => $val) {
                        $modOfFam[$fff] = $val->family;
                        $fff++;
                         
                     }
                    $Start = "A8";
                    $aa = 8;
                    $end = $arrayLetter[$fff];
                    $sheet->cells("$Start:$end$aa", function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->cells("$Start:$end$aa", function($cells) {$cells->setBackground('#3366FF'); });
                    $sheet->cells('A9:A15', function($cells) {$cells->setBackground('#FFFF00'); });
                    $sheet->cells('A9:A15', function($cells) {$cells->setFontWeight('bold'); });
                    $Fams = array_unique($modOfFam);
                    $newFams = array_values($Fams);
                    $countFam = count($Fams);

                    $defe = 7;
                    $lete = 0;
                    for($x=0;$x<$countFam;$x++)
                    {
                        $row = 8;
                        $sheet->cell($arrayLetter[$lete].$row, $newFams[$x]); //family row
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $lete++; 
                    }//FOR FAMILY
                    $nine = 9;
                    $fift = 15;
                    $start = $arrayLetter[$lete].$nine;
                    $end = $arrayLetter[$lete].$fift;
                    $sheet->cells("$start:$end", function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->cell($arrayLetter[$lete].$row, "TOTAL");
                    $TO = [];
                    $tpng = [];
                    $row = 9;
                    $lete = 0;
                     foreach ($Outdata as $key => $val) {
                        $sheet->cell($arrayLetter[$lete].$row,"0.0");
                        $sheet->cell($arrayLetter[$lete].$row, $val->toutput);
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $tpng[$lete] = $val->tpng;
                        $TO[$lete] = $val->toutput;
                        $lete++; 
                     }
                     $ACO = [];
                     $row++;
                     $lete = 0;
                      foreach ($Outdata as $key => $val) {

                        $sheet->cell($arrayLetter[$lete].$row, $val->accumulatedoutput);
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $ACO[$lete] = $val->accumulatedoutput;
                        $lete++; 
                     }
                     $row++;
                    //FOR PRODUCTIOM
                      $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select(DB::raw("COUNT(*) as classificationCount"),'family')
                        ->groupBy('family')
                        ->orderBy('family')
                        //->where('classification','like','%Production%')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                          // for($x=0;$x<$countFam;$x++)
                          // {
                          //   $sheet->cell($arrayLetter[$x].$row, '0.0');
                          //   $sheet->getStyle($arrayLetter[$x].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                          // }//zero filler
                         $lete = 0;
                        
                    foreach ($Outdatas as $key => $val) {
                       
                        if (in_array($val->family, $newFams)) {
                            $key = array_search($val->family, $newFams);
                                   $sheet->cell($arrayLetter[$key].$row, $val->classificationCount);
                                   $sheet->getStyle($arrayLetter[$key].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                                  
                                   //for DPPM
                                   $privateRow = $row + 4;
                                   $png = $val->classificationCount;
                                   $dppm = ($png/($TO[$lete]+$tpng[$lete]))*1000000;
                                   $percent = (round((float)$dppm));
                                   $sheet->cell($arrayLetter[$key].$privateRow, $percent);
                                   $sheet->getStyle($arrayLetter[$key].$privateRow)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                                   $sheet->setColumnFormat(array(
                        $arrayLetter[$key].$privateRow => '0%'
                         ));
                                }
                        $lete++; 
                    }
                    $row++;
                   
                    //FOR MATERIALS
                    $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select(DB::raw("COUNT(*) as classificationCount"),'family')
                        ->groupBy('family')
                        ->orderBy('family')
                        ->where('classification','like','%Material%')
                         ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                          for($x=0;$x<$countFam;$x++)
                          {
                            $sheet->cell($arrayLetter[$x].$row, '0.0');
                            $sheet->getStyle($arrayLetter[$x].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                          }//zero filler
                          $lete = 0;
                    foreach ($Outdatas as $key => $val) {
                        if (in_array($val->family, $newFams)) {
                            $indexs = array_search($val->family, $newFams);
                                   $sheet->cell($arrayLetter[$indexs].$row, $val->classificationCount);
                                   $sheet->getStyle($arrayLetter[$indexs].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                                }
                       
                        $lete++; 
                    }
                    $row++;
                    //TOTAL yield percentage
                    
                    $outputrow = 10;
                    $PNGrow = 11;
                    for($x=0;$x<$countFam;$x++)
                    {
                      
                        $out = $arrayLetter[$x].$outputrow;
                        $png = $arrayLetter[$x].$PNGrow;
                        $sheet->setCellValue($arrayLetter[$x].$row, "=(($out/($out + $png)))");
                        $x1 = $x+1;
                        $sheet->setColumnFormat(array($arrayLetter[$x].$row => '0%'));
                        $sheet->getStyle($arrayLetter[$x].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                       
                    }
                    $row++;
                    //FOR TOTAL YIELD %
                    for($x=0;$x<$countFam;$x++)
                    {

                        $Ypercent = (($TO[$x] / $ACO[$x]) * 100);
                        $percent = (round((float)$Ypercent))/100;
                        $sheet->cell($arrayLetter[$x].$row,$percent);
                         $sheet->setColumnFormat(array(
                        $arrayLetter[$x].$row => '0%'
                         ));
                         $sheet->getStyle($arrayLetter[$x].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    }
                    //TO TOTAL ALL
                    $row=9;
                    $last = $countFam-1;
                    $per = 13;
                    for($x=1;$x<=7;$x++)
                    {
                        $start = "B".$row;
                        $end = $arrayLetter[$last].$row;
                    $sheet->setCellValue($arrayLetter[$countFam].$row, "=SUM($start:$end)");
                     $sheet->getStyle($arrayLetter[$countFam].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    if($per < 15){
                     $sheet->setColumnFormat(array(
                        $arrayLetter[$countFam].$per => '0%'
                         ));

                    $per++;
                }
                    $row++;
                    }
                   
                 

                });
            })->download('xls');
            }
            else{
                //no data
            }
        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
        }
 }

  public function yieldsumfamRptpdf(Request $request){
  try
        { 
             $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';
         
            
            Excel::create('Yield_Summary_Family_Report_'.$date, function($excel) use($request)
            {
                $excel->sheet('Sheet1', function($sheet) use($request)
                {
                     $sheet->setAutoSize(true);
                     $datefrom = $request->datefrom;
                     $dateto = $request->dateto;
                     $yieldtarget = $request->yieldtarget;
                     $chosen = $request->chosen;
                     $ptype = $request->ptype;

                      $Outdata = DB::connection($this->mysql)->table('tbl_targetregistration')
                        ->select('dppm','yield')
                        ->where('yield',$yieldtarget)
                        ->get();
                         $tardppm = 0;
                        foreach ($Outdata as $key => $value) {
                            $tardppm = $value->dppm;
                        }
                    $wew1 = $yieldtarget;
                    $wew2 = $tardppm;

                
                    $sheet->setCellValue('A1', 'Yield Target Summary Per Family - '. $ptype);
                    $sheet->mergeCells('A1:E1');
                    $sheet->cell('A3',"DATE");
                    $date = date("Y-m-d");
                    $sheet->cell('B3',$date);
                    $sheet->cell('E3',"Date Froms");
                    $sheet->cell('F3',$datefrom);
                    $sheet->cell('E4',"Date To");
                    $sheet->cell('F4',$dateto);
                    $sheet->cell('A6',"Yield Target");
                        $sheet->cell('B6',$yieldtarget);
                    $sheet->cell('A7',"DPPM Target");
                        $sheet->cell('B7',$tardppm);
                    $sheet->getStyle('B6:B7')->getAlignment()->applyFromArray(array('horizontal' => 'center'));
                    $sheet->cells('B6:B7', function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->cell('A8',"Family");
                    $sheet->cell('A9',"Input");
                    $sheet->cell('A10',"Output");
                    $sheet->cell('A11',"Production NG");
                    $sheet->cell('A12',"Material NG");
                    $sheet->cell('A13',"Yield W/o MNG");
                    $sheet->cell('A14',"Total Yield (%)");
                    $sheet->cell('A15',"DPPM");
                    $sheet->setHeight(1,30);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(15);
                        $row->setAlignment('left');
                    });


                
                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  10
                        )
                    ));
                    $row = 2;
                    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $yieldtarget = $request->yieldtarget;
                    $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                        //->select('family',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"),DB::raw("SUM(toutput) as toutput"),'ywomng')
                        ->select('family',DB::raw("SUM(accumulatedoutput) as accumulatedoutput"),'toutput','ywomng','tpng')
                        ->groupBy('family')
                        ->orderBy('family')
                         ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();

                     $arrayLetter = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                         $modOfFam = [];
                      $fff = 0;
                     foreach ($Outdata as $key => $val) {
                        $modOfFam[$fff] = $val->family;
                        $fff++;
                         
                     }
                    $Start = "A8";
                    $aa = 8;
                    $end = $arrayLetter[$fff];
                    $sheet->cells("$Start:$end$aa", function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->cells("$Start:$end$aa", function($cells) {$cells->setBackground('#3366FF'); });
                    $sheet->cells('A9:A15', function($cells) {$cells->setBackground('#FFFF00'); });
                    $sheet->cells('A9:A15', function($cells) {$cells->setFontWeight('bold'); });
                    $Fams = array_unique($modOfFam);
                    $newFams = array_values($Fams);
                    $countFam = count($Fams);

                    $defe = 7;
                    $lete = 0;
                    for($x=0;$x<$countFam;$x++)
                    {
                        $row = 8;
                        $sheet->cell($arrayLetter[$lete].$row, $newFams[$x]); //family row
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $lete++; 
                    }//FOR FAMILY
                    $nine = 9;
                    $fift = 15;
                    $start = $arrayLetter[$lete].$nine;
                    $end = $arrayLetter[$lete].$fift;
                    $sheet->cells("$start:$end", function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->cell($arrayLetter[$lete].$row, "TOTAL");
                    $TO = [];
                    $tpng = [];
                    $row = 9;
                    $lete = 0;
                     foreach ($Outdata as $key => $val) {
                        $sheet->cell($arrayLetter[$lete].$row,"0.0");
                        $sheet->cell($arrayLetter[$lete].$row, $val->toutput);
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $tpng[$lete] = $val->tpng;
                        $TO[$lete] = $val->toutput;
                        $lete++; 
                     }
                     $ACO = [];
                     $row++;
                     $lete = 0;
                      foreach ($Outdata as $key => $val) {

                        $sheet->cell($arrayLetter[$lete].$row, $val->accumulatedoutput);
                        $sheet->getStyle($arrayLetter[$lete].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $ACO[$lete] = $val->accumulatedoutput;
                        $lete++; 
                     }
                     $row++;
                    //FOR PRODUCTIOM
                      $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select(DB::raw("COUNT(*) as classificationCount"),'family')
                        ->groupBy('family')
                        ->orderBy('family')
                        ->where('classification','like','%Production%')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                          for($x=0;$x<$countFam;$x++)
                          {
                            $sheet->cell($arrayLetter[$x].$row, '0.0');
                            $sheet->getStyle($arrayLetter[$x].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                          }//zero filler
                         $lete = 0;
                    foreach ($Outdatas as $key => $val) {
                        if (in_array($val->family, $newFams)) {
                            $key = array_search($val->family, $newFams);
                                   $sheet->cell($arrayLetter[$key].$row, $val->classificationCount);
                                   $sheet->getStyle($arrayLetter[$key].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 

                                   //for DPPM
                                   $privateRow = $row + 4;
                                   $png = $val->classificationCount;
                                   $dppm = ($png/($TO[$lete]+$tpng[$lete]))*1000000;
                                   $percent = (round((float)$dppm));
                                   $sheet->cell($arrayLetter[$key].$privateRow, $percent);
                                   $sheet->getStyle($arrayLetter[$key].$privateRow)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                                   $sheet->setColumnFormat(array(
                        $arrayLetter[$key].$privateRow => '0%'
                         ));
                                }
                        $lete++; 
                    }
                    $row++;
                    //FOR MATERIALS
                    $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select(DB::raw("COUNT(*) as classificationCount"),'family')
                        ->groupBy('family')
                        ->orderBy('family')
                        ->where('classification','like','%Material%')
                         ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                          for($x=0;$x<$countFam;$x++)
                          {
                            $sheet->cell($arrayLetter[$x].$row, '0.0');
                            $sheet->getStyle($arrayLetter[$x].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                          }//zero filler
                          $lete = 0;
                    foreach ($Outdatas as $key => $val) {
                        if (in_array($val->family, $newFams)) {
                            $indexs = array_search($val->family, $newFams);
                                   $sheet->cell($arrayLetter[$indexs].$row, $val->classificationCount);
                                   $sheet->getStyle($arrayLetter[$indexs].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                                }
                       
                        $lete++; 
                    }
                    $row++;
                    //TOTAL yield percentage
                    
                    $outputrow = 10;
                    $PNGrow = 11;
                    for($x=0;$x<$countFam;$x++)
                    {
                      
                        $out = $arrayLetter[$x].$outputrow;
                        $png = $arrayLetter[$x].$PNGrow;
                        $sheet->setCellValue($arrayLetter[$x].$row, "=(($out/($out + $png)))");
                        $x1 = $x+1;
                        $sheet->setColumnFormat(array($arrayLetter[$x].$row => '0%'));
                        $sheet->getStyle($arrayLetter[$x].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                       
                    }
                    $row++;
                    //FOR TOTAL YIELD %
                    for($x=0;$x<$countFam;$x++)
                    {

                        $Ypercent = (($TO[$x] / $ACO[$x]) * 100);
                        $percent = (round((float)$Ypercent))/100;
                        $sheet->cell($arrayLetter[$x].$row,$percent);
                         $sheet->setColumnFormat(array(
                        $arrayLetter[$x].$row => '0%'
                         ));
                         $sheet->getStyle($arrayLetter[$x].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    }
                    //TO TOTAL ALL
                    $row=9;
                    $last = $countFam-1;
                    $per = 13;
                    for($x=1;$x<=7;$x++)
                    {
                        $start = "B".$row;
                        $end = $arrayLetter[$last].$row;
                    $sheet->setCellValue($arrayLetter[$countFam].$row, "=SUM($start:$end)");
                     $sheet->getStyle($arrayLetter[$countFam].$row)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    if($per < 15){
                     $sheet->setColumnFormat(array(
                        $arrayLetter[$countFam].$per => '0%'
                         ));

                    $per++;
                }
                    $row++;
                    }


                    //==========================================================FOR PDF
                        $arrayLetterpdf =  array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                     
                         
                    $defe = 8;
                    $countFam = $countFam+3;
                    for($x=0;$x<8;$x++){
                        for($y=0;$y<$countFam;$y++){
                    $rowww[$x][$y] = $sheet->getcell($arrayLetterpdf[$y].$defe)->getCalculatedValue();
                                 }
                                  $defe++;
                    }
                    //$sheet->setCellValue('H20', $rowww[1][1]);
                    //$defe = 7;
                    $html9 = "";
                    for($x=0;$x<8;$x++){
                        $html9 .= '<tr>';
                        for($y=0;$y<$countFam;$y++){
                            $html9 .= '<td>'.$rowww[$x][$y].'</td>';
                        }
                        $defe++;
                        $html9 .= '</tr>';
                    }

                    $Outdata = DB::connection($this->mysql)->table('tbl_targetregistration')
                        ->select('dppm','yield')
                       
                        ->get();
                        foreach ($Outdata as $key => $value) {
                            $tardppm = $value->dppm;
                            $yieldtarget=$value->yield;
                        }

                    $dt = Carbon::now();
                        $dompdf = new Dompdf();
                        $date = substr($dt->format('Ymd'), 2);
                        $html = '<div class="container-fluid" style="font-size:10">
                                                        <h2> Yield Target Summary Per Family </h2>
                                                        <p>Inclusive Date</p>
                                                        </br>
                                                        <p style="padding-left:6em;">Date From:'.$datefrom.'</p>
                                                        <p style="padding-left:6em;">Date From:'.$dateto.'</p>
                                                        <div style="width: 100%;margin-top: 3%;">
                                                            <table border="1" style="font-size:10">
                                                            <tr><td>Yield Target: </td> <td>'.$yieldtarget.'</td></tr>
                                                            <tr><td>DPPM Target: </td><td>'.$tardppm.'</td></tr>
                                                             '.$html9.'
                                        
                                                                </table>
                                                            </div>
                                                            <hr/>
                                                            <p align="right">
                                                                <strong>DATE CREATED:</strong> '.$dt->format('l jS \\of F Y h:i:s A').'
                                                            </p>
                                                        </div>';

         $dompdf->loadHtml($html);

        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
       return $dompdf->stream('YIELD Summary Report'.Carbon::now().'.pdf');
                });
            });//->download('xls');

        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
        }
 }


   public function yieldsumRpt(Request $request)
   {
                    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $prodtype = $request->prodtype;
                    $family = $request->family;
                    $series = $request->series;
                    $device = $request->device;
                    $pono = $request->pono;



    try
        { 
            $dt = Carbon::now();    
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';
             //$check = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->where('pono',$pono)->count();
             $check = 1;
            if($check > 0){
            Excel::create('Yield_Summary_Report_'.$date, function($excel) use($request)
            {
                $excel->sheet('Sheet1', function($sheet) use($request)
                {
                    $sheet->setAutoSize(true);
                    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $prodtype = $request->prodtype;
                    $family = $request->family;
                    $series = $request->series;
                    $device = $request->device;
                    $pono = $request->pono;

                    $arrayLetter = array("B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                  
                   
                   

                    $sheet->setCellValue('A1', 'Yield Performance Report');
                    $sheet->mergeCells('A1:E1');
                    $sheet->cell('A3',"DATE");
                    $date = date("Y-m-d");
                    $sheet->cell('B3',$date);
                    $sheet->cell('E3',"Date Froms");
                    $sheet->cell('F3',$datefrom);
                    $sheet->cell('E4',"Date To");
                    $sheet->cell('F4',$dateto);
                    $sheet->setCellValue('A6',"PRODUCT TYPE:");
                    $sheet->setCellValue('B6',$prodtype);
                    $sheet->setCellValue('A7',"FAMILY: ");
                    $sheet->setCellValue('B7',$family);
                    $sheet->setCellValue('A8',"Series Name: ");
                    if($series == null){$series = '';}
                    $sheet->setCellValue('B8',$series);
                    $sheet->setCellValue('A9',"Device Name: ");
                    $sheet->setCellValue('B9',$device);
                    $sheet->setCellValue('A10',"PO NUMBER: ");
                    $sheet->setCellValue('B10',$pono);
                    $sheet->cells('B6:B10', function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle('B6:B10')->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    
                    $sheet->setHeight(1,30);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(15);
                        $row->setAlignment('center');
                        $row->setFontWeight('bold');

                    });
                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  10
                        )
                    ));

                   if($pono != '' && $prodtype != '' && $family != '' && $series != '' && $device != ''){
                    $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','productiondate')
                        ->groupBy('mod')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('device',$device)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != '' && $prodtype != '' && $family != '' && $series != ''){
                    $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','productiondate')
                        ->groupBy('mod')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != '' && $prodtype != '' && $family != ''){
                    $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','productiondate')
                        ->groupBy('mod')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != '' && $prodtype != ''){
                    $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','productiondate')
                        ->groupBy('mod')
                        ->where('prodtype',$prodtype)
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != ''){
                    $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','productiondate')
                        ->groupBy('mod')
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else{
                         $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','productiondate')
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    

                    $row=12;
                    $sheet->setCellValue('A12',"DEFECTS");
                    $sheet->cells('A12', function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->mergeCells('A12:A13');
                    

                        $modOfD = [];
                        $defe = 14;
                     foreach ($Outdata as $key => $val) {
                        $modOfD[$defe] = $val->mod;
                        $defe++;
                     }
                    $fff = 14;
                    $c = count($modOfD)+14;
                    for($x = 14 ; $x < $c; $x++){
                         $sheet->setCellValue('A'.$fff,$modOfD[$x]);
                         $fff++;
                    }

                    if($pono != '' && $prodtype != '' && $family != '' && $series != '' && $device != ''){
                      $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate')
                        ->orderBy('productiondate')
                        ->groupBy('productiondate')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('device',$device)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != '' && $prodtype != '' && $family != '' && $series != ''){
                      $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate')
                        ->orderBy('productiondate')
                        ->groupBy('productiondate')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                     else if($pono != '' && $prodtype != ''){
                      $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate')
                        ->orderBy('productiondate')
                        ->groupBy('productiondate')
                        ->where('prodtype',$prodtype)
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != ''){
                          $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate')
                        ->orderBy('productiondate')
                        ->groupBy('productiondate')
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else{
                          $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate')
                        ->orderBy('productiondate')
                        ->groupBy('productiondate')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();

                    }



                    $ches = $fff;
                        $twelve = 12;
                        $x=0;
                        $aa=0;
                        $dateholder = [];
                        $da = "";
                     foreach ($Outdata as $key => $val) {  
                         $d = $val->productiondate;
                         $datess = explode("-", $d);
                         switch ($datess[1]) {
                             case '01':
                                $da = "Jan-".$datess[2];
                             break;
                             case '02':
                                $da = "Feb-".$datess[2];
                             break;
                             case '03':
                                $da = "Mar-".$datess[2];
                             break;
                             case '04':
                                $da = "Apr-".$datess[2];
                             break;
                             case '05':
                                $da = "May-".$datess[2];
                             break;
                             case '06':
                                $da = "Jun-".$datess[2];
                             break;
                             case '07':
                                $da = "Jul-".$datess[2];
                             break;
                             case '08':
                                $da = "Aug-".$datess[2];
                             break;
                             case '09':
                                $da = "Sep-".$datess[2];
                             break;
                             case '10':
                                $da = "Oct-".$datess[2];
                             break;
                             case '11':
                                $da = "Nov-".$datess[2];
                             break;
                             case '12':
                                $da = "Dec-".$datess[2];
                             break;
                             
                             
                         }
                         //$sheet->setCellValue($arrayLetter[$x].$twelve,$val->productiondate);
                         $sheet->setCellValue($arrayLetter[$x].$twelve,$da);
                         $ot1 = $arrayLetter[$x].$twelve;
                         $y1=$x+1;
                         $ot2 = $arrayLetter[$y1].$twelve;
                         $sheet->mergeCells("$ot1:$ot2");
                         $sheet->getStyle($arrayLetter[$x].$twelve)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                         $sheet->cells($arrayLetter[$x].$twelve, function($cells) {$cells->setFontWeight('bold'); });
                         
                         $dateholder[$aa] = $val->productiondate;
                         $plus = $twelve+1;
                         $sheet->setCellValue($arrayLetter[$x].$plus,"PNG");
                         $sheet->getStyle($arrayLetter[$x].$plus)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                         $sheet->cells($arrayLetter[$x].$plus, function($cells) {$cells->setFontWeight('bold'); });
                         $s = $x+1;
                         $sheet->setCellValue($arrayLetter[$s].$plus,"MNG");
                         $sheet->getStyle($arrayLetter[$s].$plus)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                         $sheet->cells($arrayLetter[$s].$plus, function($cells) {$cells->setFontWeight('bold'); });
                         $x = $x+2;
                         $aa=$aa+2;
                     }
                     $twelve =12;
                     $endA = $arrayLetter[$x].$twelve;
                     $sheet->cells("A12:$endA", function($cells) {$cells->setBackground('#00FFFF'); });
                     $twelve =13;
                     $endA = $arrayLetter[$x].$twelve;
                     $sheet->cells("A12:$endA", function($cells) {$cells->setBackground('#00FFFF'); });


                    if($pono != '' && $prodtype != '' && $family != '' && $series != null && $device != ''){
                     $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('device',$device)
                        ->where('classification',"Production NG (PNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != '' && $prodtype != '' && $family != '' && $series != ''){
                     $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('classification',"Production NG (PNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != '' && $prodtype != ''){
                     $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('pono',$pono)
                        ->where('prodtype',$prodtype)
                        ->where('classification',"Production NG (PNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else if($pono != ''){
                         $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('pono',$pono)
                        ->where('classification',"Production NG (PNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                    else{
                         $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('classification',"Production NG (PNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    }
                  
                       
                       foreach ($Outdatas as $key => $value) {
                            $a = $value->productiondate;
                            $b = $value->mod;
                            $key1 = array_search($a, $dateholder);
                            $key2 = array_search($b, $modOfD);
                            $sheet->setCellValue($arrayLetter[$key1].$key2,$value->classificationCount);
                            $sheet->getStyle($arrayLetter[$key1].$key2)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        }

                        if($pono != '' && $prodtype != '' && $family != '' && $series != '' && $device != ''){
                        $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('device',$device)
                        ->where('classification',"Material NG (MNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                        }
                        else if($pono != '' && $prodtype != '' && $family != '' && $series != ''){
                        $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('classification',"Material NG (MNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                        }
                        else if($pono != '' && $prodtype != ''){
                        $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('pono',$pono)
                        ->where('prodtype',$prodtype)
                        ->where('classification',"Material NG (MNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                        }
                        else if($pono != ''){
                        $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('pono',$pono)
                        ->where('classification',"Material NG (MNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                        }
                        else{
                        $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('classification',"Material NG (MNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                        }
                      
                       

                       foreach ($Outdatas as $key => $value) {
                            $a = $value->productiondate;
                            $b = $value->mod;
                            $key1 = array_search($a, $dateholder);
                            $key2 = array_search($b, $modOfD);
                            $key1 = $key1+1;
                            $sheet->setCellValue($arrayLetter[$key1].$key2,$value->classificationCount);
                            $sheet->getStyle($arrayLetter[$key1].$key2)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        }

                        
                        $out = $ches+1;
                        $y=0;
                        for($x=0;$x<count($dateholder);$x++)
                        {
                            $ywmng = $ches + 4;
                            $twoyieldd = $ches + 5;

                        if($pono != '' && $prodtype != '' && $family != '' && $series != '' && $device != ''){
                         $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate','ywomng','twoyield')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('device',$device)
                        ->where('classification','like','%PNG%')
                        ->orwhere('classification','like','%MNG%')
                        ->where('productiondate', $dateholder[$y])
                        ->get();
                         }
                         else if($pono != '' && $prodtype != '' && $family != '' && $series != ''){
                        $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate','ywomng','twoyield')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('classification','like','%PNG%')
                        ->orwhere('classification','like','%MNG%')
                        ->where('productiondate', $dateholder[$y])
                        ->get();
                         }
                         else if($pono != '' && $prodtype != ''){
                        $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate','ywomng','twoyield')
                        ->where('prodtype',$prodtype)
                        ->where('pono',$pono)
                        ->where('classification','like','%PNG%')
                        ->orwhere('classification','like','%MNG%')
                        ->where('productiondate', $dateholder[$y])
                        ->get();
                         }
                         else if($pono != ''){
                        $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate','ywomng','twoyield')
                        ->where('pono',$pono)
                        ->where('classification','like','%PNG%')
                        ->orwhere('classification','like','%MNG%')
                        ->where('productiondate', $dateholder[$y])
                        ->get();
                         }
                        else{
                        $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate','ywomng','twoyield')
                        ->where('classification','like','%PNG%')
                        ->orwhere('classification','like','%MNG%')
                        ->where('productiondate', $dateholder[$y])
                        ->get();
                         }



                        foreach ($Outdata as $key => $value) {
                             $sheet->setCellValue($arrayLetter[$y].$out,$value->accumulatedoutput);
                             $ot1 = $arrayLetter[$y].$out;
                             $y1=$y+1;
                             $ot2 = $arrayLetter[$y1].$out;
                             $sheet->mergeCells("$ot1:$ot2");
                             $sheet->getStyle($arrayLetter[$y].$out)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                             $sheet->setCellValue($arrayLetter[$y].$ywmng,$value->ywomng/100);
                             $ot1 = $arrayLetter[$y].$ywmng;
                             $y1=$y+1;
                             $ot2 = $arrayLetter[$y1].$ywmng;
                             $sheet->mergeCells("$ot1:$ot2");
                             $sheet->getStyle($arrayLetter[$y].$ywmng)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                             $sheet->setColumnFormat(array($arrayLetter[$y].$ywmng => '0%' ));
                             $sheet->setCellValue($arrayLetter[$y].$twoyieldd,$value->twoyield/100);
                             $ot1 = $arrayLetter[$y].$twoyieldd;
                             $y1=$y+1;
                             $ot2 = $arrayLetter[$y1].$twoyieldd;
                             $sheet->mergeCells("$ot1:$ot2");
                             $sheet->getStyle($arrayLetter[$y].$twoyieldd)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                             $sheet->setColumnFormat(array($arrayLetter[$y].$twoyieldd => '0%' ));
                        }
                        $y=$y+2;
                        }
                        
                        $a=count($dateholder);
                        $first = $ches-3;
                        $last = $fff-1;
                        $PNG = $last + 3;
                        $skipPNG = 0;
                        for($x=0;$x<$a;$x++)
                        {
                        $start1 = $arrayLetter[$skipPNG].$first;
                        $end = $arrayLetter[$skipPNG].$last;
                        $sheet->cell($arrayLetter[$skipPNG].$PNG, "=SUM($start1:$end)"); 
                        $ot1 = $arrayLetter[$skipPNG].$PNG;
                        $y1=$skipPNG+1;
                        $ot2 = $arrayLetter[$y1].$PNG;
                        $sheet->mergeCells("$ot1:$ot2");  
                         //PNG
                        $sheet->getStyle($arrayLetter[$skipPNG].$PNG)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $skipPNG = $skipPNG+2;
                        }
                        $first = $ches-3;
                        $last = $fff-1;
                        $MNG = $last + 4;
                        $skipMNG = 1;
                        $skipPNG = 0;
                        for($x=0;$x<$a;$x++)
                        {
                        $start1 = $arrayLetter[$skipMNG].$first;
                        $end = $arrayLetter[$skipMNG].$last;
                        $sheet->cell($arrayLetter[$skipPNG].$MNG, "=SUM($start1:$end)");
                        $ot1 = $arrayLetter[$skipPNG].$MNG;
                        $y1=$skipPNG+1;
                        $ot2 = $arrayLetter[$y1].$MNG;
                        $sheet->mergeCells("$ot1:$ot2");    
                         //MNG
                        $sheet->getStyle($arrayLetter[$skipPNG].$MNG)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $skipMNG = $skipMNG+2;
                        $skipPNG = $skipPNG+2;
                        }
                        $skipper = 0;
                        $inp = $fff;
                       
                        for($x=0;$x<$a;$x++)
                        {
                            $c = $inp+1;
                            $start = $arrayLetter[$skipper].$c;
                            $b = $inp+3;
                            $end = $arrayLetter[$skipper].$b;
                           
                            $sheet->cell($arrayLetter[$skipper].$inp, "=SUM($start:$end)"); //INPUT
                            $ot1 = $arrayLetter[$skipper].$inp;
                            $y1=$skipper+1;
                            $ot2 = $arrayLetter[$y1].$inp;
                            $sheet->mergeCells("$ot1:$ot2"); 
                            $sheet->getStyle($arrayLetter[$skipper].$inp)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $skipper = $skipper+2;
                        }
                    
                   




                    $defe = $ches;
                    $start = $defe;
                    $sheet->cell('A'.$defe, "INPUT"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "OUTPUT"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "PRODUCTION-NG"); 
                    $defe++; 
                    $sheet->cell('A'.$defe, "MATERIAL-NG"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "Yield w/o MNG(%)"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "TOTAL Yield(%)"); 
                    $end = $defe;
                   
                    $s = "A$start";
                    $e = "A$end";
                 $sheet->cells("$s:$e", function($cells) {$cells->setBackground('#FFCC00'); });
                 $sheet->cells("$s:$e", function($cells) {$cells->setFontWeight('bold'); });

                 $defe = $defe + 5;
                     $start = $defe;
                    $sheet->cell('A'.$defe, "TOTAL INPUT"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "TOTAL OUTPUT"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "TOTAL PRODUCTION-NG"); 
                    $defe++; 
                    $sheet->cell('A'.$defe, "TOTAL MATERIAL-NG"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "Yield w/o MNG(%)"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "TOTAL Yield(%)"); 
                     $end = $defe;
                      $s = "A$start";
                    $e = "A$end";
                 $sheet->cells("$s:$e", function($cells) {$cells->setBackground('#00FF00'); });
                 $sheet->cells("$s:$e", function($cells) {$cells->setFontWeight('bold'); });

                  $totalInput = $fff+10;
                  $sumTI=0;
                  $skipper = 0;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumTI = $sumTI+$sheet->getcell($arrayLetter[$skipper].$fff)->getCalculatedValue();
                    $sheet->cell('B'.$totalInput, $sumTI); 
                    $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $skipper = $skipper+2;
                  }
                  //FOR TOTAL INPUT
                  

                  $totalInput = $fff+11;
                  $sumTO=0;
                  $skipper = 0;
                  $f1 = $fff+1;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumTO = $sumTO+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                    $sheet->cell('B'.$totalInput, $sumTO); 
                    $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $skipper = $skipper+2;
                  }
                  //FOR TOTAL OUTPUT
                  

                  $totalInput = $fff+12;
                  $sumPNG=0;
                  $skipper = 0;
                  $f1 = $fff+2;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumPNG = $sumPNG+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                    $sheet->cell('B'.$totalInput, $sumPNG);
                    $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $skipper = $skipper+2;
                  }
                  //FORPNG
                  

                  $totalInput = $fff+13;
                  $sumMNG=0;
                  $skipper = 0;
                  $f1 = $fff+3;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumMNG = $sumMNG+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                    $sheet->cell('B'.$totalInput, $sumMNG); 
                    $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $skipper = $skipper+2;
                  }
                  //FORMNG
                  
                  $totalInput = $fff+14;
                  $sumYWM=0;
                  $skipper = 0;
                  $f1 = $fff+4;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumYWM = $sumYWM+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                  //  $sheet->cell('B'.$totalInput, $sumYWM); 
                    $skipper = $skipper+2;
                  }
                  $a = count($dateholder);
                  if($a == 0)
                  {
                    $a = 1;
                  }
               
                     $sheet->cell('B'.$totalInput, ($sumYWM/$a)); 
                   $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                   $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                   $sheet->setColumnFormat(array('B'.$totalInput => '0%' ));
            
                     
                  //YWMNG
                  
                  $totalInput = $fff+15;
                  $sumTY=0;
                  $skipper = 0;
                  $f1 = $fff+5;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumTY = $sumTY+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                  //  $sheet->cell('B'.$totalInput, $sumYWM); 
                    $skipper = $skipper+2;
                  }
                   $sheet->cell('B'.$totalInput, ($sumTY/$a)); 
                   $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                   $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                   $sheet->setColumnFormat(array('B'.$totalInput => '0%' ));
                  //YWMNG

                });
            })->download('xls');
            }
            else{
                //no DATA
            }

        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
        }
    }

      public function yieldsumRptpdf(Request $request)
   {
    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $prodtype = $request->prodtype;
                    $family = $request->family;
                    $series = $request->series;
                    $device = $request->device;
                    $pono = $request->pono;

    try
        { 
            $dt = Carbon::now();    
            $date = substr($dt->format('Ymd'), 2);
            $path = public_path().'/Yielding_Performance_Data_Check/export';
             $check = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->whereBetween('productiondate', [$datefrom, $dateto])->where('prodtype',$prodtype)->where('family',$family)->where('series',$series)->where('pono',$pono)->where('device',$device)->count();
            if($check > 0){
      
            
            Excel::create('Yield_Summary_Report_'.$date, function($excel) use($request)
            {
                $excel->sheet('Sheet1', function($sheet) use($request)
                {
                    $sheet->setAutoSize(true);
                    $datefrom = $request->datefrom;
                    $dateto = $request->dateto;
                    $prodtype = $request->prodtype;
                    $family = $request->family;
                    $series = $request->series;
                    $device = $request->device;
                    $pono = $request->pono;

                    $arrayLetter = array("B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                  
                    

                    $sheet->setCellValue('A1', 'Yield Performance Report');
                    $sheet->mergeCells('A1:E1');
                    $sheet->cell('A3',"DATE");
                    $date = date("Y-m-d");
                    $sheet->cell('B3',$date);
                    $sheet->cell('E3',"Date Froms");
                    $sheet->cell('F3',$datefrom);
                    $sheet->cell('E4',"Date To");
                    $sheet->cell('F4',$dateto);
                    $sheet->setCellValue('A6',"PRODUCT TYPE:");
                    $sheet->setCellValue('B6',$prodtype);
                    $sheet->setCellValue('A7',"FAMILY: ");
                    $sheet->setCellValue('B7',$family);
                    $sheet->setCellValue('A8',"Series Name: ");
                    $sheet->setCellValue('B8',$series);
                    $sheet->setCellValue('A9',"Device Name: ");
                    $sheet->setCellValue('B9',$device);
                    $sheet->setCellValue('A10',"PO NUMBER: ");
                    $sheet->setCellValue('B10',$pono);
                    $sheet->cells('B6:B10', function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle('B6:B10')->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    
                    $sheet->setHeight(1,30);
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setBackground('##ADD8E6');
                        $row->setFontSize(15);
                        $row->setAlignment('center');
                        $row->setFontWeight('bold');

                    });
                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  10
                        )
                    ));
                    $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','productiondate')
                        ->groupBy('mod')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();

                    $row=12;
                    $sheet->setCellValue('A12',"DEFECTS");
                    $sheet->cells('A12', function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->mergeCells('A12:A13');
                    

                        $modOfD = [];
                        $defe = 14;
                     foreach ($Outdata as $key => $val) {
                        $modOfD[$defe] = $val->mod;
                        $defe++;
                     }
                    $fff = 14;
                    $c = count($modOfD)+14;
                    for($x = 14 ; $x < $c; $x++){
                         $sheet->setCellValue('A'.$fff,$modOfD[$x]);
                         $fff++;
                    }


                       $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate')
                        ->orderBy('productiondate')
                        ->groupBy('productiondate')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                    $ches = $fff;
                        $twelve = 12;
                        $x=0;
                        $aa=0;
                        $dateholder = [];
                        $da = "";
                     foreach ($Outdata as $key => $val) {  
                         $d = $val->productiondate;
                         $datess = explode("-", $d);
                         switch ($datess[1]) {
                             case '01':
                                $da = "Jan-".$datess[2];
                             break;
                             case '02':
                                $da = "Feb-".$datess[2];
                             break;
                             case '03':
                                $da = "Mar-".$datess[2];
                             break;
                             case '04':
                                $da = "Apr-".$datess[2];
                             break;
                             case '05':
                                $da = "May-".$datess[2];
                             break;
                             case '06':
                                $da = "Jun-".$datess[2];
                             break;
                             case '07':
                                $da = "Jul-".$datess[2];
                             break;
                             case '08':
                                $da = "Aug-".$datess[2];
                             break;
                             case '09':
                                $da = "Sep-".$datess[2];
                             break;
                             case '10':
                                $da = "Oct-".$datess[2];
                             break;
                             case '11':
                                $da = "Nov-".$datess[2];
                             break;
                             case '12':
                                $da = "Dec-".$datess[2];
                             break;
                             
                             
                         }
                         //$sheet->setCellValue($arrayLetter[$x].$twelve,$val->productiondate);
                         $sheet->setCellValue($arrayLetter[$x].$twelve,$da);
                         $ot1 = $arrayLetter[$x].$twelve;
                         $y1=$x+1;
                         $ot2 = $arrayLetter[$y1].$twelve;
                         $sheet->mergeCells("$ot1:$ot2");
                         $sheet->getStyle($arrayLetter[$x].$twelve)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                         $sheet->cells($arrayLetter[$x].$twelve, function($cells) {$cells->setFontWeight('bold'); });
                         
                         $dateholder[$aa] = $val->productiondate;
                         $plus = $twelve+1;
                         $sheet->setCellValue($arrayLetter[$x].$plus,"PNG");
                         $sheet->getStyle($arrayLetter[$x].$plus)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                         $sheet->cells($arrayLetter[$x].$plus, function($cells) {$cells->setFontWeight('bold'); });
                         $s = $x+1;
                         $sheet->setCellValue($arrayLetter[$s].$plus,"MNG");
                         $sheet->getStyle($arrayLetter[$s].$plus)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                         $sheet->cells($arrayLetter[$s].$plus, function($cells) {$cells->setFontWeight('bold'); });
                         $x = $x+2;
                         $aa=$aa+2;
                     }
                     $twelve =12;
                     $endA = $arrayLetter[$x].$twelve;
                     $sheet->cells("A12:$endA", function($cells) {$cells->setBackground('#00FFFF'); });
                     $twelve =13;
                     $endA = $arrayLetter[$x].$twelve;
                     $sheet->cells("A12:$endA", function($cells) {$cells->setBackground('#00FFFF'); });

                     $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('classification',"Production NG (PNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                       
                       foreach ($Outdatas as $key => $value) {
                            $a = $value->productiondate;
                            $b = $value->mod;
                            $key1 = array_search($a, $dateholder);
                            $key2 = array_search($b, $modOfD);
                            $sheet->setCellValue($arrayLetter[$key1].$key2,$value->classificationCount);
                            $sheet->getStyle($arrayLetter[$key1].$key2)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        }


                        $Outdatas = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate',DB::raw("COUNT(*) as classificationCount"))
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('classification',"Material NG (MNG)")
                        ->groupBy('mod')
                        ->whereBetween('productiondate', [$datefrom, $dateto])
                        ->get();
                       
                       foreach ($Outdatas as $key => $value) {
                            $a = $value->productiondate;
                            $b = $value->mod;
                            $key1 = array_search($a, $dateholder);
                            $key2 = array_search($b, $modOfD);
                            $key1 = $key1+1;
                            $sheet->setCellValue($arrayLetter[$key1].$key2,$value->classificationCount);
                            $sheet->getStyle($arrayLetter[$key1].$key2)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        }

                        
                        $out = $ches+1;
                        $y=0;
                        for($x=0;$x<count($dateholder);$x++)
                        {
                            $ywmng = $ches + 4;
                            $twoyieldd = $ches + 5;
                         $Outdata = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as a')
                        ->select('mod','family','prodtype','series','device','pono','toutput','accumulatedoutput','classification','productiondate','ywomng','twoyield')
                        ->where('family',$family)
                        ->where('prodtype',$prodtype)
                        ->where('series',$series)
                        ->where('pono',$pono)
                        ->where('classification','like','%PNG%')
                        ->orwhere('classification','like','%MNG%')
                        ->where('productiondate', $dateholder[$y])
                        ->get();
                        foreach ($Outdata as $key => $value) {
                             $sheet->setCellValue($arrayLetter[$y].$out,$value->accumulatedoutput);
                             $ot1 = $arrayLetter[$y].$out;
                             $y1=$y+1;
                             $ot2 = $arrayLetter[$y1].$out;
                             $sheet->mergeCells("$ot1:$ot2");
                             $sheet->getStyle($arrayLetter[$y].$out)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                             $sheet->setCellValue($arrayLetter[$y].$ywmng,$value->ywomng/100);
                             $ot1 = $arrayLetter[$y].$ywmng;
                             $y1=$y+1;
                             $ot2 = $arrayLetter[$y1].$ywmng;
                             $sheet->mergeCells("$ot1:$ot2");
                             $sheet->getStyle($arrayLetter[$y].$ywmng)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                             $sheet->setColumnFormat(array($arrayLetter[$y].$ywmng => '0%' ));
                             $sheet->setCellValue($arrayLetter[$y].$twoyieldd,$value->twoyield/100);
                             $ot1 = $arrayLetter[$y].$twoyieldd;
                             $y1=$y+1;
                             $ot2 = $arrayLetter[$y1].$twoyieldd;
                             $sheet->mergeCells("$ot1:$ot2");
                             $sheet->getStyle($arrayLetter[$y].$twoyieldd)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                             $sheet->setColumnFormat(array($arrayLetter[$y].$twoyieldd => '0%' ));
                        }
                        $y=$y+2;
                        }
                        
                        $a=count($dateholder);
                        $first = $ches-3;
                        $last = $fff-1;
                        $PNG = $last + 3;
                        $skipPNG = 0;
                        for($x=0;$x<$a;$x++)
                        {
                        $start1 = $arrayLetter[$skipPNG].$first;
                        $end = $arrayLetter[$skipPNG].$last;
                        $sheet->cell($arrayLetter[$skipPNG].$PNG, "=SUM($start1:$end)"); 
                        $ot1 = $arrayLetter[$skipPNG].$PNG;
                        $y1=$skipPNG+1;
                        $ot2 = $arrayLetter[$y1].$PNG;
                        $sheet->mergeCells("$ot1:$ot2");  
                         //PNG
                        $sheet->getStyle($arrayLetter[$skipPNG].$PNG)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $skipPNG = $skipPNG+2;
                        }
                        $first = $ches-3;
                        $last = $fff-1;
                        $MNG = $last + 4;
                        $skipMNG = 1;
                        $skipPNG = 0;
                        for($x=0;$x<$a;$x++)
                        {
                        $start1 = $arrayLetter[$skipMNG].$first;
                        $end = $arrayLetter[$skipMNG].$last;
                        $sheet->cell($arrayLetter[$skipPNG].$MNG, "=SUM($start1:$end)");
                        $ot1 = $arrayLetter[$skipPNG].$MNG;
                        $y1=$skipPNG+1;
                        $ot2 = $arrayLetter[$y1].$MNG;
                        $sheet->mergeCells("$ot1:$ot2");    
                         //MNG
                        $sheet->getStyle($arrayLetter[$skipPNG].$MNG)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                        $skipMNG = $skipMNG+2;
                        $skipPNG = $skipPNG+2;
                        }
                        $skipper = 0;
                        $inp = $fff;
                       
                        for($x=0;$x<$a;$x++)
                        {
                            $c = $inp+1;
                            $start = $arrayLetter[$skipper].$c;
                            $b = $inp+3;
                            $end = $arrayLetter[$skipper].$b;
                           
                            $sheet->cell($arrayLetter[$skipper].$inp, "=SUM($start:$end)"); //INPUT
                            $ot1 = $arrayLetter[$skipper].$inp;
                            $y1=$skipper+1;
                            $ot2 = $arrayLetter[$y1].$inp;
                            $sheet->mergeCells("$ot1:$ot2"); 
                            $sheet->getStyle($arrayLetter[$skipper].$inp)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                            $skipper = $skipper+2;
                        }
                    
                   




                    $defe = $ches;
                    $start = $defe;
                    $sheet->cell('A'.$defe, "INPUT"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "OUTPUT"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "PRODUCTION-NG"); 
                    $defe++; 
                    $sheet->cell('A'.$defe, "MATERIAL-NG"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "Yield w/o MNG(%)"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "TOTAL Yield(%)"); 
                    $end = $defe;
                   
                    $s = "A$start";
                    $e = "A$end";
                 $sheet->cells("$s:$e", function($cells) {$cells->setBackground('#FFCC00'); });
                 $sheet->cells("$s:$e", function($cells) {$cells->setFontWeight('bold'); });

                 $defe = $defe + 5;
                     $start = $defe;
                    $sheet->cell('A'.$defe, "TOTAL INPUT"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "TOTAL OUTPUT"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "TOTAL PRODUCTION-NG"); 
                    $defe++; 
                    $sheet->cell('A'.$defe, "TOTAL MATERIAL-NG"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "Yield w/o MNG(%)"); 
                    $defe++;
                    $sheet->cell('A'.$defe, "TOTAL Yield(%)"); 
                     $end = $defe;
                      $s = "A$start";
                    $e = "A$end";
                 $sheet->cells("$s:$e", function($cells) {$cells->setBackground('#00FF00'); });
                 $sheet->cells("$s:$e", function($cells) {$cells->setFontWeight('bold'); });

                  $totalInput = $fff+10;
                  $sumTI=0;
                  $skipper = 0;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumTI = $sumTI+$sheet->getcell($arrayLetter[$skipper].$fff)->getCalculatedValue();
                    $sheet->cell('B'.$totalInput, $sumTI); 
                    $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $skipper = $skipper+2;
                  }
                  //FOR TOTAL INPUT
                  

                  $totalInput = $fff+11;
                  $sumTO=0;
                  $skipper = 0;
                  $f1 = $fff+1;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumTO = $sumTO+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                    $sheet->cell('B'.$totalInput, $sumTO); 
                    $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $skipper = $skipper+2;
                  }
                  //FOR TOTAL OUTPUT
                  

                  $totalInput = $fff+12;
                  $sumPNG=0;
                  $skipper = 0;
                  $f1 = $fff+2;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumPNG = $sumPNG+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                    $sheet->cell('B'.$totalInput, $sumPNG);
                    $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); }); 
                    $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $skipper = $skipper+2;
                  }
                  //FORPNG
                  

                  $totalInput = $fff+13;
                  $sumMNG=0;
                  $skipper = 0;
                  $f1 = $fff+3;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumMNG = $sumMNG+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                    $sheet->cell('B'.$totalInput, $sumMNG); 
                    $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                    $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                    $skipper = $skipper+2;
                  }
                  //FORMNG
                  
                  $totalInput = $fff+14;
                  $sumYWM=0;
                  $skipper = 0;
                  $f1 = $fff+4;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumYWM = $sumYWM+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                  //  $sheet->cell('B'.$totalInput, $sumYWM); 
                    $skipper = $skipper+2;
                  }
                   $sheet->cell('B'.$totalInput, ($sumYWM/count($dateholder))); 
                   $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                   $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                   $sheet->setColumnFormat(array('B'.$totalInput => '0%' ));
                  //YWMNG
                  
                  $totalInput = $fff+15;
                  $sumTY=0;
                  $skipper = 0;
                  $f1 = $fff+5;
                  for($x=0;$x<$a;$x++)
                  {
                    $sumTY = $sumTY+$sheet->getcell($arrayLetter[$skipper].$f1)->getCalculatedValue();
                  //  $sheet->cell('B'.$totalInput, $sumYWM); 
                    $skipper = $skipper+2;
                  }
                   $sheet->cell('B'.$totalInput, ($sumTY/count($dateholder))); 
                   $sheet->cells('B'.$totalInput, function($cells) {$cells->setFontWeight('bold'); });
                   $sheet->getStyle('B'.$totalInput)->getAlignment()->applyFromArray(array('horizontal' => 'center')); 
                   $sheet->setColumnFormat(array('B'.$totalInput => '0%' ));
                  //YWMNG
                    //================================================FOR PDF
                 $arrayLetterpdf = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","AB","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP");
                    
                    $html = "";
                    $html8 = "";
                    $html9 = "";
                    $html10 = "";
                    $defe = 12;

                    $countrow = 6 + count($modOfD)+1+11;
                    $countcol = (count($dateholder)*2)+1;
                    for($x=0;$x<$countrow;$x++){
                        for($y=0;$y<$countcol;$y++){
                    $rowww[$x][$y] = $sheet->getcell($arrayLetterpdf[$y].$defe)->getCalculatedValue();
                                 }
                                  $defe++;
                    }
                    
                    $html9 = "";
                    for($x=0;$x<$countrow;$x++){
                        $html9 .= '<tr>';
                        for($y=0;$y<$countcol;$y++){
                            $html9 .= '<td>'.$rowww[$x][$y].'</td>';
                        }
                        $defe++;
                        $html9 .= '</tr>';
                    }
                        
                        $dt = Carbon::now();
                        $dompdf = new Dompdf();
                        $date = substr($dt->format('Ymd'), 2);
                        $html = '<div class="container-fluid" style="font-size:10">
                                                        <h2> DEFECTS SUMMARY</h2>
                                                        <p>Inclusive Date</p>
                                                        </br>
                                                        <p style="padding-left:6em;">Date From:'.$datefrom.'</p>
                                                        <p style="padding-left:6em;">Date From:'.$dateto.'</p>
                                                        <div style="width: 100%;margin-top: 3%;">
                                                            <table border="1" style="font-size:10">
                                                            '.$html8.'
                                                               '.$html9.'
                                                                </table>
                                                            </div>
                                                            <hr/>
                                                            <p align="right">
                                                                <strong>DATE CREATED:</strong> '.$dt->format('l jS \\of F Y h:i:s A').'
                                                            </p>
                                                        </div>';

         $dompdf->loadHtml($html);

        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();
       return $dompdf->stream('YIELD Summary Report'.Carbon::now().'.pdf');

                });

            });//->download('xls');
        }else{
            //no data
        }


        } catch (Exception $e) {
            return redirect(url('/yieldperformance'))->with(['err_message' => $e]);
        }
    }



 




    public function loadchart(Request $request){
        $df = $request->datefroms;
        $dt = $request->datetos;
        $pieces = explode("/", $df);
        $pieces2 = explode("/", $dt);
        $fixeddf = $pieces[2]."-".$pieces[0]."-".$pieces[1];
        $fixeddt = $pieces2[2]."-".$pieces2[0]."-".$pieces2[1];
       // var_dump($fixeddf);
        $table = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                    ->select('family',DB::raw("SUM(toutput) as toutput"),DB::raw("SUM(qty) as qty"))
                    ->groupBy('family')
                    ->orderBy('family')
                    ->whereBetween('productiondate', [$fixeddf, $fixeddt])
                    ->get();
       return $table;
    }

    public function devreg_get_series(Request $request){
        $family = $request->family;
        $table = DB::connection($this->mysql)->table('tbl_seriesregistration')->select('series')->where('family',$family)->get();
        return $table;
    }

}
