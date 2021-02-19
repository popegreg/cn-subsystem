<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use App\mProductline;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use App\mUserprogram;
use DB;
use Config;

class ProductlineController extends Controller
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
    public function getProductline()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PRODUCT'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
        	$productlines = mProductline::where('delete_flag','0')
                                        ->orderBy('id','asc')->get();

            return view('master.productlines', [
                                            'productlines' => $productlines,
                                            'userProgramAccess' => $userProgramAccess
                                            ]);
        }
    }

    public function postAddProduct(Request $request)
    {
    	$this->validate($request, [
            'code' => 'required|unique:mproductlines',
            'name' => 'required'
        ]);

        #initiate othe variables
        $pg_code = "2003";
        $pg_name = "Productline Master";

        #instantiate User model
        $prodlines = new mProductline();
        $prodlines->code = $request['code'];
        $prodlines->name = $request['name'];
        $prodlines->create_pg = $pg_code;
        $prodlines->create_user = Auth::user()->user_id;

        if ($prodlines->save()) {
        	$message = "New Product Line was successfully added";
            return redirect(url('/productlines'))->with(['message' => $message]);
        }
    }

    public function postDeleteProduct(Request $request)
    {
    	$id = $request['id'];
        
        $prodlines = mProductline::where('id',$request['id'])->delete();
                //->update(['delete_flag' => 1]);
        $message = "Product Line was successfully deleted.";
        return redirect(url('/productlines'))->with(['message' => $message]);
    }

    public function postEditProduct(Request $request)
    {
    	$prodlines = mProductline::where('id',$request->id)
                                    ->update([
                                             'code' => $request->code,
                                             'name' => $request->name,
                                             'update_pg' => '2003',
                                             'update_user' => Auth::user()->user_id
                                            ]);
        $message = "Product Line was successfully updated";
        return redirect(url('/productlines'))->with(['message' => $message]);
    }
}
