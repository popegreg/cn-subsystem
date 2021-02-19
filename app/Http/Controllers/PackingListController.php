<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use Illuminate\Http\Response;
use Config;
use DB;

class PackingListController extends Controller
{
   
   public function getPackingList(Request $request)
    {
        $id = $request->id;
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PCKNGLIST'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $tableData = DB::table('tbl_packinglist')->get();
            return view('PackingList',['userProgramAccess' => $userProgramAccess, 'tableData' => $tableData]); 
            /*if($id){
                $count = DB::table('tbl_soldto')->count();
                $pagination = DB:: table('tbl_soldto')->where('id',$id)->paginate(5); 
                $searchedid = DB::table('tbl_soldto')->where('id',$id)->get();
                return view('SoldTo',['userProgramAccess' => $userProgramAccess,'tableData' => $searchedid, 'tableData' => $pagination]);
            } else {
                $count = DB::table('tbl_soldto')->count(); 
                $pagination = DB:: table('tbl_soldto')->paginate(5); 
                $tableData = DB::table('tbl_soldto')->get();
                return view('SoldTo',['userProgramAccess' => $userProgramAccess,'tableData' => $tableData, 'tableData' => $pagination]); 
            }*/
           
        }
    }

     public function addPackingList(Request $request)
    {
        $table = "tbl_packinglist";
        $field = $request->data;
        $exist = $field['soldto'];
        $dataexist = DB::table($table)->where('soldto',$exist)->get();
        if($dataexist){
            $msg = "Data Already Exist.";
            return redirect('/sold-to')->with(['err_message'=>$msg]);
        } else {
            $display ;
            $ok = DB::table($table)
                    ->insert([
                        'soldto' => $field['soldto'],
                        'carrier' => $field['carrier'],
                        'portofloading' => $field['portofloading'],   
                        'portofdestination' => $field['portofdestination'],
                        'destinationofgoods' => $field['destinationofgoods'],
                        'shipto' => $field['shipto'],                          
                    ]);

            if ($ok) {
                $msg = "Successfully saved.";
                return redirect('/packinglist')->with(['message'=>$msg]);
            } else {
                    $msg = "Saving Failed.";
                return redirect('/packinglist')->with(['err_message'=>$msg]);
            }
        }
       
    }

    public function updatePackingList(Request $request)
    {

        $table = "tbl_packinglist";
        $field = $request->data;
        $id = $field['masterid'];
        $soldto = $field['soldto'];
        $carrier = $field['carrier'];
        $portofloading = $field['portofloading'];
        $portofdestination = $field['portofdestination'];
        $destinationofgoods = $field['destinationofgoods'];
        $shipto = $field['shipto'];

        $ok = DB::table($table)
            ->where('id', $id)
            ->update(array('soldto'=>$soldto,'carrier'=>$carrier,'portofloading' =>$portofloading, 'portofdestination' =>$portofdestination, 'destinationofgoods' =>$destinationofgoods, 'shipto' =>$shipto));

        if ($ok) {
            $msg = "Successfully saved.";
            return redirect('/packinglist')->with(['message'=>$msg]);
        } else {
             $msg = "Saving Failed.";
            return redirect('/packinglist')->with(['err_message'=>$msg]);
        }
    }

     public function deleteAllPackingList(Request $request)
    {
       
        $tray = $request->tray;
        $traycount = $request->traycount;
       	/*return $traycount;*/
        if($traycount > 0){
            $ok = DB::table('tbl_packinglist')->wherein('id',$tray)->delete();
        
            if ($ok) {
                $msg = "Successfully deleted selected records.";
                return redirect('/packinglist')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/packinglist')->with(['err_message'=>$msg]);
            }
        } else {
             $ok = DB::table('tbl_soldto')->delete();
        
            if ($ok) {
                $msg = "Successfully deleted all records.";
                return redirect('/packinglist')->with(['message'=>$msg]);
            } else {
                $msg = "No Record Exists.";
                return redirect('/packinglist')->with(['err_message'=>$msg]);
            }
        }
    }

}
