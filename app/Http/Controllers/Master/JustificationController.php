<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: JustificationController.php
     MODULE NAME:  [2004] Reason Master
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.14
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.14     MESPINOSA       Initial Draft
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
use App\mJustification; #Justification Model for DB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade

/**
* Justification Controller
*/
class JustificationController extends Controller
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
    * Get All Justifications.
    */
    public function getJustificationmaster()
    {
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_REASON')
                                    , $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            $justifications = DB::connection($this->common)->table('mjustifications')->get();

            return view('master.justificationmaster', 
                       ['justifications' => $justifications,
                        'userProgramAccess' => $userProgramAccess]);
        }
    }

    /**
    * Create Justification Record.
    */
    public function postRegisterJustification(Request $request)
    {

        # initialize validation
        $this->validateInput($request);

        #set mJustification properties.
        $justification = new mJustification;
        $justification = $this->setMJustification($request, "ADD");

        try
        {
            $cnt = $this->isCodeExists($justification, "ADD");
            if($cnt > 0)
            {
                $message = "Selected reason was not updated. Code already exists.";
                $output = redirect(url('/justificationmaster'))
                        ->with(['err_message' => $message]);
            }
            else
            {
                #add to DB
                $result = DB::connection($this->common)->table('mjustifications')->insert(
                    ['code' => $justification->code, 
                     'name' => $justification->name, 
                     'create_pg' => $justification->create_pg,
                     'create_user' => $justification->create_user,
                     'created_at' => $justification->create_date,
                     'update_pg' => $justification->update_pg,
                     'update_user' => $justification->update_user,
                     'updated_at' => $justification->update_date
                    ]);

                if ($result) 
                {
                	$message = "New Reason was successfully added";
                	$output = redirect(url('/justificationmaster'))
                                     ->with(['message' => $message]);
                } 
                else 
                {
                	$message = "You entered wrong credentials";
                	$output = redirect()->route('justificationmaster')
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
        $justification_id = $request->input('selected_justification');

        $selected_justification = DB::connection($this->common)->table('mjustifications')
                                ->where('id', '=', $justification_id)->get();

        return $selected_justification;

    }

    /**
    * Check if code already exists.
    */
    private function isCodeExists($justification, $action)
    {
        if ($action == "EDIT")
        {
            $justification_cnt = DB::connection($this->common)->table('mjustifications')
                            ->where('id', '<>', $justification->id)
                            ->where('code', '=', $justification->code)
                            ->count();
        }
        else if ($action == "ADD")
        {
            $justification_cnt = DB::connection($this->common)->table('mjustifications')
                            ->where('code', '=', $justification->code)
                            ->count();   
        }
        return $justification_cnt;
    }

    /**
    * Update Justification Record
    */
    public function postUpdateJustification(Request $request)
    {
        # initialize validation
        $this->validateInput($request);

        #set mJustification properties.
        $justification = new mJustification;
        $justification = $this->setMJustification($request, "EDIT");

        #update justification to DB
        try
        {
            $cnt = $this->isCodeExists($justification, "EDIT");

            if($cnt > 0)
            {
                $message = "Selected reason was not updated. Code already exists.";
                $output = redirect(url('/justificationmaster'))
                        ->with(['err_message' => $message]);
            }
            else
            {
            $result = DB::connection($this->common)->table('mjustifications')
                            ->where('id', $justification->id)
                            ->update([
                                'code' => $justification->code, 
                                'name' => $justification->name, 
                                'update_pg' => $justification->update_pg,
                                'update_user' => $justification->update_user,
                                'updated_at' => $justification->update_date
                                ]);

            if($result)
            {
                $message = "Selected reason successfully updated.";
                $output = redirect(url('/justificationmaster'))
                        ->with(['message' => $message]);
            }
            else
            {
                Log::error($result);
                $message = "Selected reason was not updated. Please try again.";
                $output = redirect(url('/justificationmaster'))
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
    * Delete Justification Record
    */
    public function postDeleteJustification(Request $request)
    {
        #delete justification from DB
        try
        {
            $result = DB::connection($this->common)->table('mjustifications')->where('id', '=', $request['id'])->delete();

            if($result)
            {
                $message = "Selected reason successfully deleted.";
                $output = redirect(url('/justificationmaster'))
                        ->with(['message' => $message]);
            }
            else
            {
                Log::error($result);
                $message = "Selected reason was not deleted. Please try again.";
                $output = redirect(url('/justificationmaster'))
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
    private function setMJustification($request, $action)
    {
        #initiate othe variables
        $pg_code = "2004";
        $pg_name = "Justification Master";

        #instantiate Justification model
        $justification = new mJustification();

        #retrieve justification information
        if ($action == "EDIT")
        {
            $justification->id = $request['id'];
        }
        else if ($action == "ADD")
        {
            $justification->create_pg = $pg_code;
            $justification->create_user = Auth::user()->user_id;
            $justification->create_date = date("Y/m/d h:i:sa");
        }
        $justification->code = $request['code'];
        $justification->name = $request['name'];
        $justification->update_pg = $pg_code;
        $justification->update_user = Auth::user()->user_id;
        $justification->update_date = date("Y/m/d h:i:sa");

        return $justification;
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
            'faxno'        => 'min:0|max:50',
            'emailaddress' => 'min:0|max:50',
            'created_pg'   => 'min:0|max:50',
            'create_user' => 'min:0|max:20',
            'updated_pg'   => 'min:0|max:50',
            'update_user' => 'min:0|max:20'
        ]);
    }

}