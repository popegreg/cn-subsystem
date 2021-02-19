<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\Http\Requests;
use Config;

class PackingListMoldingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getPackingListMolding()
    {
    	$common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_PLMOLDING'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('PackingListMolding',['userProgramAccess' => $userProgramAccess]);
        }
    }
}
