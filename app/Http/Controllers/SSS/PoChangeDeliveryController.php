<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PoChangeDeliveryController.php
     MODULE NAME:  [3008-1] PO Status : Change Delivery
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.05.03
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.03     MESPINOSA       Initial Draft
     100-00-02   1     2016.05.18     MESPINOSA       Retrieve data from MySQL.
*******************************************************************************/
?>
<?php
namespace App\Http\Controllers\SSS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use DB;
use Log;
// use App\PoChangeDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; #Auth facade
use Config;
use Excel;
use File;
use Mail;

/**
* PO ChangeDelivery Controller
*/
class  PoChangeDeliveryController extends Controller
{

    protected $mysql;
    protected $mssql;
    protected $common;

    public function __construct()
    {
        $this->middleware('auth');
        header("Content-Type: text/html; charset=SHIFT-JIS");
        $com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $com->userDBcon(Auth::user()->productline,'sss');
            $this->mssql = $com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    /**
    * Get All OrderDataReports.
    */
    public function getPoChangeDelivery(Request $request_data)
    {
        # checking of login user access rights
        $common = new CommonController;
        if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_SSS')
                                    , $userProgramAccess))
        {
            # redirect to home page if user has no access.
            return redirect('/home');
        }
        else
        {
            $po = trim($request_data['po']);
            $code = trim($request_data['code']);

            if(empty($po))
            {
                # for empty result.
                $po = Config::get('constants.EMPTY_FILTER_VALUE');
            }
            $po_details = $this->retrievePo($po, $code);
            $answers = $this->retrieveAnswer($po);
            $reasons = DB::connection($this->common)->table('mjustifications')->get();

            return view('sss.PoChangeDelivery', 
                    ['userProgramAccess' => $userProgramAccess
                    , 'po' => $po
                    , 'po_details' => $po_details
                    , 'answers' => $answers
                    , 'reasons' => $reasons]);
        }
    }

    /**
    * Get the PO details.
    **/
    private function retrievePo($po, &$code)
    {
        try
        {
            # retrieve PO details information data.
            $result = DB::connection($this->mysql)->table('temp_sss_mrplist')
            ->select(
                    DB::raw("SUBSTRING(po, 1, 10) AS PO")
                    , 'dcode as Code'
                    , 'dname as Name'
                    , DB::raw("CONCAT(custcode, ' ',custname) as Customer")
                    , DB::raw("CONCAT(balreq, ' / ', schdqty) as Qty")
                    , DB::raw("balreq as KVOL")
                    , DB::raw("(CASE orddate 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(orddate, '%m/%d/%y') 
                               END) AS PODate")
                    , DB::raw("(CASE duedate 
                                WHEN '0000-00-00' THEN NULL 
                                ELSE DATE_FORMAT(duedate, '%m/%d/%y') 
                               END) AS Demand")
                    , DB::raw(" '' as POTime ")
                    , 'supname as UpdatedBy'
                    , DB::raw(" '' as Remarks ")
                    )
            ->where('po', 'like', $po . '%')
            ->skip(0)->take(1) 
            ->get();

            foreach ($result as $key => $value) 
            {
                $value = get_object_vars($value);
                $code = $value['Code'];
                break;
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
        return $result;
    }

    /**
    * Get R3 Answer.
    **/
    private function retrieveAnswer($po)
    {
        try
        {
            # retreive R3Answer data.
            $result = DB::connection($this->mysql)->table('temp_sss_prdanswer')
            ->select('po'
                , DB::raw("(CASE r3answer 
                            WHEN '0000-00-00' THEN NULL 
                            ELSE DATE_FORMAT(r3answer, '%m/%d/%y') 
                           END) AS r3answer")
                , 'time'
                , 'qty' 
                , 're')
            ->where('po', $po)
            ->get();
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
            echo 'Message: ' .$e->getMessage();
        }
        return $result;
    }

    /**
    * Send the content of the page to Mail.
    **/
    public function sendMail(Request $request_data)
    {
        $msg_type = 'message';
        $msg = '';
        // return dd($request_data->all());

        $to = $request_data['to'];
        $cc = $request_data['cc'];
        $subject = $request_data['subject'];
        $po = $request_data['po'];
        $code = $request_data['code'];
        $data[0]['code'] = $request_data['code'];
        $data[0]['new1'] = $request_data['new1'];
        $data[0]['new2'] = $request_data['new2'];
        $data[0]['reason'] = $request_data['reason'];
        $data[0]['note'] = $request_data['note'];

        # mail recipient is mandatory.
        if(!$to == NULL)
        {
            $po_details = $this->retrievePo($po, $code);
            $answers = $this->retrieveAnswer($po);

            Mail::send('mail', ['data'=> $data
                                , 'po_details' => $po_details
                                , 'answers' => $answers], 
                function($message) use ($to, $cc, $subject)
            {
              $message->from('us@example.com', 'Pricon Microelectronic Inc.'); // please change this
              $message->to($to);
              if(!$cc === NULL)
              {
                $message->cc($cc);
              }
              $message->subject($subject);

            });
            $msg_type = 'message';
            $msg = 'Mail sent.';
        }
        else
        {
            $msg_type = 'err_message';
            $msg = 'Please input atleast one (1) mail recipient.';   
        }

       return redirect('/pochangedelivery' 
        . '?code='.$code.'&po=' . $po)->with([$msg_type => $msg]);
    }
}