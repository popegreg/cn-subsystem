<?php
namespace App\Http\Controllers\Phase3;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth; #Auth facade

class AddnewYieldingPerformanceController extends Controller
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
                ->table('XHIKI as d')
                ->join('XHEAD as h','d.OYACODE','=','h.CODE')
                ->select(DB::raw("d.SEIBAN as PO")
                    , DB::raw("d.OYACODE as devicecode")
                    , DB::raw("h.NAME as devicename")
                    , DB::raw("SUM(d.KVOL) as POqty"))
                ->where('d.SEIBAN',$request->pono)
                ->groupBy('d.SEIBAN','d.OYACODE','h.NAME')
                ->get();
            $datenow = date('Y/m/d');
            $count = DB::connection($this->mysql)->table("tbl_yielding_pya")->select('yieldingno')->orderBy('id','desc')->first();

            $countpya = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->count(); 
            $countcmq = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->count(); 
            $family = DB::connection($this->mysql)->table("tbl_seriesregistration")->select('family')->distinct()->get();
            $modefect = $common->getDropdownByName('Mode of Defect - Yield Performance');
            /*$series = $common->getDropdownByName('series');*/
            $devreg = DB::connection($this->mysql)->table('tbl_deviceregistration')->get();
            $ys = $common->getDropdownByName('Yielding Station');

            return view('phase3.AddnewYieldingPerformance',['userProgramAccess' => $userProgramAccess,'family' => $family,'modefect' => $modefect,'yieldstation' => $ys,'yieldingno'=>$count,'devreg'=>$devreg, 'msrecords'=>$msrecords, 'count'=>$count,'countpya'=> $countpya,'countcmq'=> $countcmq]); 
        }
    }
    public function checkdetails(Request $request){
        $pono = $request->pono;
        $tablecount = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->where('pono','=',$pono)->count();

        return $tablecount;
    }

    //displaying the records for production date,yielding station and accumulated output
    private function displaypya($pono)
    {
        $table = "tbl_yielding_performance_backup";
        $dataexist = DB::connection($this->mysql)->table($table)
                    ->where('pono','=',$pono)
                    ->select('id','yieldingno','productiondate','yieldingstation','accumulatedoutput')
                    ->groupBy('yieldingno')
                    ->orderBY('productiondate','DESC')
                    ->get();

        return $dataexist; 
    }

    //display records for classification, mod and quantity
    private function displaycmq($pono)
    { 
        $table = "tbl_yielding_performance_backup";  
        $dataexist = DB::connection($this->mysql)->table($table)
                    ->where('pono','=',$pono)
                    ->select('id','pono','classification','mod','qty')
                    ->orderBY('productiondate','DESC')
                    ->get();
        return $dataexist; 
    }

    public function searchdisplaypya(Request $request)
    {
        $table = "tbl_yielding_performance_backup";
        $pono = $request->pono;  
        $dataexist = DB::connection($this->mysql)->table($table)
                    ->where('pono','=',$pono)
                    ->select('id','yieldingno','pono','productiondate','yieldingstation','accumulatedoutput')
                    ->groupBy('yieldingno')
                    ->orderBY('productiondate','DESC')
                    ->get();
                    
       return $dataexist;
    }
    public function getautovalue(Request $request){
        $pono = $request->pono;
        $table = DB::connection($this->mysql)->table('tbl_yielding_performance_backup as y')
                        ->join('tbl_yielding_pya as p','y.pono','=','p.pono')
                        ->join('tbl_yielding_cmq as c','y.pono','=','c.pono')
                        ->select(DB::raw("SUM(distinct(p.accumulatedoutput)) AS toutput"),DB::raw("SUM(distinct(c.qty)) AS treject"))
                        ->where('y.pono','=',$pono)
                        ->groupBy('y.pono')
                        ->orderBy('y.id','DESC')
                        ->get();
        return $table;
    }
    public function getpng(Request $request){
        $pono = $request->pono;
        $table = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                        ->select(DB::raw("SUM(tpng) AS tpng"))
                        ->where('pono','=',$pono)
                        ->where('classification','=','Production NG (PNG)')
                        ->get();
        return $table;
    }
    public function getmng(Request $request){
        $pono = $request->pono;
        $table = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                        ->select(DB::raw("SUM(tmng) AS tmng"))
                        ->where('pono','=',$pono)
                        ->where('classification','=','Material NG (MNG)')
                        ->get();
        return $table;
    }

    public function searchdisplaycmq(Request $request)
    {
        $table = "tbl_yielding_performance_backup";
        $pono = $request->pono;
        $dataexist = DB::connection($this->mysql)->table($table)
                    ->where('pono','=',$pono)
                    ->select('id','pono','yieldingno','classification','mod','qty')
                    ->orderBY('productiondate','DESC')
                    ->get();

        return $dataexist;
    }

    public function searchdisplaydetails(Request $request)
    {
        $table = "tbl_yielding_performance_backup";
        $pono = $request->pono;
        $dataexist = DB::connection($this->mysql)->table($table)
                    ->where('pono','=',$pono)
                    ->orderBy('productiondate','DESC')
                    ->get();

        return $dataexist; 
    }
    public function searchdisplaysummary(Request $request)
    {
        $table = "tbl_yielding_performance_backup";
        $pono = $request->pono;
        $dataexist = DB::connection($this->mysql)->table($table)
                    ->where('pono','=',$pono)
                    ->orderBy('productiondate','DESC')
                    ->get();

        return $dataexist; 
    }
    public function addYieldperformance(Request $request)
    {
        $dataField = $request->data;
        $status = $dataField['status'];
        $classification = $dataField['classification'];
        $x = 0;
        foreach ($dataField['newmod'] as $key => $rec) {
           /* if($dataField['classification']){
                $qty = $dataField['newqty'][$key];
                $classification = $dataField['newclassification'][$key];
                $mod = $rec;
            }else{
                $qty = "NDF";
                $classification = "NDF";
                $mod = "NDF";
            }*/
            DB::connection($this->mysql)->table('tbl_yielding_cmq')
                        ->insert([
                            'yieldingno' => $dataField['yieldingno'],
                            'pono' => $dataField['pono'],
                            'productiondate' => $dataField['productiondate'],
                            'mod' => $rec,
                            'classification' =>$dataField['newclassification'][$key],
                            'qty' => $dataField['newqty'][$key]
                            ]);                                                                                                                                                                                                                         
        }

        foreach ($dataField['newyieldingstation'] as $key => $rec) {
            DB::connection($this->mysql)->table('tbl_yielding_pya')
                        ->insert([
                            'yieldingno' => $dataField['yieldingno'],
                            'pono' => $dataField['pono'],
                            'productiondate' => $dataField['productiondate'],
                            'yieldingstation' => $rec,
                            'accumulatedoutput' => $dataField['accumulatedoutput']
                            ]);
                                                                                                                                                                                                                                         
        }
        
        if($status == "ADD"){
            foreach ($dataField['newmod'] as $key => $rec) {
               /* if($dataField['classification']){
                    $qty = $dataField['newqty'][$key];
                    $classification = $dataField['newclassification'][$key];
                    $mod = $rec;
                }else{
                    $qty = "NDF";
                    $classification = "NDF";
                    $mod = "NDF";
                }*/
                DB::connection($this->mysql)->table("tbl_yielding_performance_backup")
                        ->insert([
                            'yieldingno' => $dataField['yieldingno'],
                            'pono' => $dataField['pono'],
                            'poqty' => $dataField['poqty'],
                            'device' => $dataField['device'],
                            'family' => $dataField['family'],
                            'series' => $dataField['series'],
                            'prodtype' => $dataField['prodtype'],
                            'mod' => $rec,
                            'classification' => $dataField['newclassification'][$key],
                            'qty' => $dataField['newqty'][$key],
                            'productiondate' => $dataField['productiondate'],
                            'yieldingstation' => $dataField['yieldingstation'],
                            'accumulatedoutput' => $dataField['accumulatedoutput'],
                            'toutput' => $dataField['toutput'],
                            'treject' => $dataField['treject'],
                            'tmng' => $dataField['tmng'],
                            'tpng' => $dataField['tpng'],
                            'ywomng' => $dataField['ywomng'],
                            'twoyield' => $dataField['twoyield']
                            ]);                                         
                                                                                                                                                                                                                                         
            }
        } 

        if($status == "EDIT"){
            $table = "tbl_yielding_performance_backup";
            $dataField = $request->data;
            return $dataField['productiondate'];

            $ok = DB::connection($this->mysql)->table($table)
                ->where('id',$dataField['yieldingno'])
                ->update(array(
                    'yieldingno' => $dataField['yieldingno'],
                    'pono' => $dataField['pono'],
                    'poqty' => $dataField['poqty'],
                    'device' => $dataField['device'],
                    'family' => $dataField['family'],
                    'series' => $dataField['series'],
                    'prodtype' => $dataField['prodtype'],
                    'classification' => $dataField['classification'],
                    'mod' => implode(",", $dataField['mod']),
                    'qty' => $dataField['qty'],
                    'productiondate' => $dataField['productiondate'],
                    'yieldingstation' => $dataField['yieldingstation'],
                    'accumulatedoutput' => $dataField['accumulatedoutput'],
                    'toutput' => $dataField['toutput'],
                    'treject' => $dataField['treject'],
                    'tmng' => $dataField['tmng'],
                    'tpng' => $dataField['tpng'],
                    'ywomng' => $dataField['ywomng'],
                    'twoyield' => $dataField['twoyield']
            ));

           
            if ($ok) {
                $msg = "Successfully saved.";
                return redirect('/addnewYieldperformance')->with(['message'=>$msg]);
            } else {
                    $msg = "Saving Failed.";
                return redirect('/addnewYieldperformance')->with(['err_message'=>$msg]);
            }
              
        }
                                        
                
    } 

    public function searchPO(Request $request)
    {    
        $search = $request->data;
        $find = $search['pono'];
        
        $countrow = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
                    ->where('pono',$find)->count();
     
        $table = DB::connection($this->mysql)->table('tbl_deviceregistration as d')
                ->join('tbl_poregistration as p','d.pono','=','p.pono')
                ->where('d.pono',$find)
                ->select('d.pono as pono','d.devicename as devicename','d.family as family','d.series as series','d.ptype as ptype','p.poqty as poqty')
                ->get();    
      
        return $table;
    }

    public function search(Request $request)
    {    
        $search = $request->data;
        $find = $search['search'];
       /* return $find;*/
        
        $ok =DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
        ->where('pono', $find)
        ->orWhere('yieldingno',$find)
        ->get();

        return $ok;

        $dataexist = DB::connection($this->mysql)->table('tbl_yielding_performance_backup')
        ->where('pono',$find)
        ->orWhere('yieldingno',$find)
        ->count();
        /*return $dataField['treject'];*/

        if($dataexist == 0){
            return $dataexist;
        }
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
                return redirect('/addnewYieldperformance')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/addnewYieldperformance')->with(['err_message'=>$msg]);
            }
        }
    }

    public function deletecmq(Request $request)
    {      
        $tray = $request->tray;
        $traycount = $request->traycount;
        $yieldingno = $request->yieldingno;
        $count = DB::connection($this->mysql)->table('tbl_yielding_cmq')->where('yieldingno',$yieldingno)->count();
        
        if($traycount > 0){
            DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->wherein('id',$tray)->delete();
            DB::connection($this->mysql)->table('tbl_yielding_cmq')->wherein('id',$tray)->delete();
        }
        if($count == 1){
            DB::connection($this->mysql)->table('tbl_yielding_pya')->where('yieldingno',$yieldingno)->delete();
        }
        return $count;
    }

    public function deletepya(Request $request)
    {      
        $tray = $request->tray;
        $traycount = $request->traycount;
        $yieldingno = $request->yieldingno;
        $table = DB::connection($this->mysql)->table("tbl_yielding_pya")->select('yieldingno')->orderBy('id','desc')->first();

        if($traycount > 0){
            DB::connection($this->mysql)->table('tbl_yielding_performance_backup')->where('yieldingno',$yieldingno)->delete();
            DB::connection($this->mysql)->table('tbl_yielding_pya')->where('yieldingno',$yieldingno)->delete();
            DB::connection($this->mysql)->table('tbl_yielding_cmq')->where('yieldingno',$yieldingno)->delete();
        }
        return $table->yieldingno[0];
        
    }

    public function multiSearch(Request $request)
    {      
        $mSearch = $request->data;
        $mSearchtype1 = $request->data['mSearchtype1'];
        $mSearchval1 = $request->data['mSearchval1'];
        $fixedtbl =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->get();
        if($mSearchtype1 == 1){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('yieldingno')->get();
            $field = "yieldingno";
        }
        else if($mSearchtype1 == 2){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('pono')->get();
            $field = "pono";
        }
        else if($mSearchtype1 == 3){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('poqty')->get();
            $field = "poqty";
        }
        else if($mSearchtype1 == 4){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('device')->get();
            $field = "device";
        }
        else if($mSearchtype1 == 5){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('family')->get();
            $field = "family";
        }
        else if($mSearchtype1 == 6){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('series')->get();
            $field = "series";
        }
        else if($mSearchtype1 == 7){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('classification')->get();
            $field = "classification";
        }
        else if($mSearchtype1 == 8){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('mod')->get();
            $field = "mod";
        }
        else if($mSearchtype1 == 9){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('qty')->get();
            $field = "qty";
        }
        else if($mSearchtype1 == 10){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('productiondate')->get();
            $field = "productiondate";
        }
        else if($mSearchtype1 == 11){
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('yieldingstation')->get();
            $field = "yieldingstation";
        }
        else {
            $table =DB::connection($this->mysql)->table("tbl_yielding_performance_backup")->select('accumulatedoutput')->get();
            $field = "accumulatedoutput";
        }
   

        return $table;
     
    }

    public function multiSearchDisplay(Request $request){
        $search = $request->data;
        $columnfield = $search['mSearchtype1'];
        $columnvalue = $search['mSearchval1'];
      
        if($columnfield == 1){
            $field = 'yieldingno';
        }   else if($columnfield == 2){
            $field = 'pono';
        }   else if($columnfield == 3){
            $field = 'poqty';
        }    else if($columnfield == 4){
            $field = 'device';
        }    else if($columnfield == 5){
            $field = 'family';
        }    else if($columnfield == 6){
            $field = 'series';
        }    else if($columnfield == 7){
            $field = 'classification';
        }    else if($columnfield == 8){
            $field = 'mod';
        }    else if($columnfield == 9){
            $field = 'qty';
        }    else if($columnfield == 10){
            $field = 'productiondate';
        }    else if($columnfield == 11){
            $field = 'yieldingstation';
        }    else{
            $field = 'accumulatedoutput';
        }
       /* return $columnvalue;*/
        $ok = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")
            ->where($field, 'LIKE' ,$columnvalue)
            ->get();

        $count = DB::connection($this->mysql)->table("tbl_yielding_performance_backup")
            ->where($field,$columnvalue)
            ->count();

        return $ok;
        /*if(count($ok)>0) {
            $msg = "Record/s found";
            return redirect('/yieldperformance2')->with(['message'=>$msg,'records'=>$ok]);
        } else {
            $msg = "No Record/s found.";
            return redirect('/yieldperformance2')->with(['err_message'=>$msg]);
        }*/
    }

    public function getsummarylist()
    {
        $data = Summary::all();
        return Datatables::of($data)->make(true);
    }

    public function devreg_get_series(Request $request){
        $data = $request->data;
        $table = DB::connection($this->mysql)->table('tbl_dropdown_series')->select($data['family'])->get();
        return $table;
    }
    public function get_mod(Request $request){
        $prodtype = $request->prodtype;
        $table = DB::connection($this->mysql)->table('tbl_modregistration')->select('mod')->where('family',$prodtype)->get();
        return $table;
    }


    
}