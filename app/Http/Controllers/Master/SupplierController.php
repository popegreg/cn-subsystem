<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: SupplierController.php
     MODULE NAME:  [2002] Supplier Master
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.13
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.13     MESPINOSA       Initial Draft
     100-00-02   1     2016.04.28     MESPINOSA       1.Implement constants.
                                                      2.Fix direct url input.
*******************************************************************************/
?>
<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Log;
use Config;
use App\mSupplier; #Supplier Model for DB
use App\Programs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade

/**
* Supplier Controller
*/
class SupplierController extends Controller
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
    * Get All Suppliers.
    */
    public function getSuppliermaster()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_SUPPLIER')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $suppliers = DB::connection($this->common)->table('msuppliers')->get();
            
            return view('master.suppliermaster', 
             ['suppliers' => $suppliers,
             'userProgramAccess' => $userProgramAccess]);
        }
    }

    /**
    * Create Supplier Record.
    */
    public function postRegisterSupplier(Request $request)
    {

        # initialize validation
        $this->validateInput($request);

        #set mSupplier properties.
        $supplier = new mSupplier;
        $supplier = $this->setMSuplliers($request, "ADD");

        try
        {
            $cnt = $this->isCodeExists($supplier, "ADD");
            if($cnt > 0)
            {
                $message = "Selected supplier was not added. Code already exists.";
                $output = redirect(url('/suppliermaster'))
                        ->with(['err_message' => $message]);
            }
            else
            {
                #add to DB
                $result = DB::connection($this->common)->table('msuppliers')->insert(
                    ['code' => $supplier->code, 
                     'name' => $supplier->name, 
                     'address' => $supplier->address, 
                     'tel_no' => $supplier->telno, 
                     'fax_no' => $supplier->faxno, 
                     'email' => $supplier->emailaddress,
                     'create_pg' => $supplier->create_pg,
                     'create_user' => $supplier->create_user,
                     'created_at' => $supplier->create_date,
                     'update_pg' => $supplier->update_pg,
                     'update_user' => $supplier->update_user,
                     'updated_at' => $supplier->update_date
                    ]);

                if ($result) 
                {
                	$message = "New Supplier was successfully added";
                	$output = redirect(url('/suppliermaster'))
                                     ->with(['message' => $message]);
                } 
                else 
                {
                	$message = "You entered wrong credentials";
                	$output = redirect()->route('suppliermaster')
                                     ->with(['err_message' => $message]);
                }
            }
        } 
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }

        return $output;
    }
    
    /**
    * Set the values of the edit screen
    */
    public function postEditScreen(Request $request)
    {
        $supplier_id = $request->input('selected_supplier');

        $selected_supplier = DB::connection($this->common)->table('msuppliers')
                                ->where('id', '=', $supplier_id)
                                ->get();

        return $selected_supplier;

    }

    /**
    * Check if code already exists.
    */
    private function isCodeExists($supplier, $action)
    {
        if ($action == "EDIT")
        {
            $supplier_cnt = DB::connection($this->common)->table('msuppliers')
                            ->where('id', '<>', $supplier->id)
                            ->where('code', '=', $supplier->code)
                            ->count();
        }
        else if ($action == "ADD")
        {
            $supplier_cnt = DB::connection($this->common)->table('msuppliers')
                            ->where('code', '=', $supplier->code)
                            ->count();   
        }
        return $supplier_cnt;
    }

    /**
    * Update Supplier Record
    */
    public function postUpdateSupplier(Request $request)
    {
        # initialize validation
        $this->validateInput($request);

        #set mSupplier properties.
        $supplier = new mSupplier;
        $supplier = $this->setMSuplliers($request, "EDIT");

        #update supplier to DB
        try
        {
            $cnt = $this->isCodeExists($supplier, "EDIT");

            if($cnt > 0)
            {
                $message = "Selected supplier was not updated. Code already exists.";
                $output = redirect(url('/suppliermaster'))
                        ->with(['err_message' => $message]);
            }
            else
            {
            $result = DB::connection($this->common)->table('msuppliers')
                            ->where('id', $supplier->id)
                            ->update([
                                'code' => $supplier->code, 
                                'name' => $supplier->name, 
                                'address' => $supplier->address, 
                                'tel_no' => $supplier->telno, 
                                'fax_no' => $supplier->faxno, 
                                'email' => $supplier->emailaddress,
                                'update_pg' => $supplier->update_pg,
                                'update_user' => $supplier->update_user,
                                'updated_at' => $supplier->update_date
                                ]);

            if($result)
            {
                $message = "Selected supplier successfully updated.";
                $output = redirect(url('/suppliermaster'))
                        ->with(['message' => $message]);
            }
            else
            {
                Log::error($result);
                $message = "Selected supplier was not updated. Please try again.";
                $output = redirect(url('/suppliermaster'))
                        ->with(['err_message' => $message]);
            }   
            }
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
    return $output;
    }

    /**
    * Delete Supplier Record
    */
    public function postDeleteSupplier(Request $request)
    {
        #delete supplier from DB
        try
        {
            $result = DB::connection($this->common)->table('msuppliers')->where('id', '=', $request['id'])->delete();

            if($result)
            {
                $message = "Selected supplier successfully deleted.";
                $output = redirect(url('/suppliermaster'))
                        ->with(['message' => $message]);
            }
            else
            {
                Log::error($result);
                $message = "Selected supplier was not deleted. Please try again.";
                $output = redirect(url('/suppliermaster'))
                            ->with(['err_message' => $message]);
            }   
        }
        catch (Exception $e) 
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }            

        return $output;
    }

    /**
    * Set Model properties
    */
    private function setMSuplliers($request, $action)
    {
        #initiate othe variables
        $pg_code = "2002";
        $pg_name = "Supplier Master";

        #instantiate Supplier model
        $supplier = new mSupplier();

        #retrieve supplier information
        if ($action == "EDIT")
        {
            $supplier->id = $request['id'];
        }
        else if ($action == "ADD")
        {
            $supplier->create_pg = $pg_code;
            $supplier->create_user = Auth::user()->user_id;
            $supplier->create_date = date("Y/m/d h:i:sa");
        }
        $supplier->code = $request['code'];
        $supplier->name = $request['name'];
        $supplier->address = $request['address'];
        $supplier->telno = $request['telno'];
        $supplier->faxno = $request['faxno'];
        $supplier->emailaddress = $request['emailaddress'];
        $supplier->update_pg = $pg_code;
        $supplier->update_user = Auth::user()->user_id;
        $supplier->update_date = date("Y/m/d h:i:sa");

        return $supplier;
    }

    /**
    * Validate Input
    */
    private function validateInput($request)
    {
        # initialize validation
        $this->validate($request, [
            'code'         => 'required|min:1|max:20',
            'name'         => 'required|min:1|max:200',
            'address'      => 'min:0|max:500',
            'telno'        => 'min:0|max:50',
            'faxno'        => 'min:0',
            'emailaddress' => 'min:0|max:50',
            'created_pg'   => 'min:0|max:50',
            'create_user'  => 'min:0|max:20',
            'updated_pg'   => 'min:0|max:50',
            'update_user'  => 'min:0|max:20'
        ]);
    }
}