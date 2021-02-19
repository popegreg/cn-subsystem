<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
use Mail;
use DB;
use Excel;
use Carbon\Carbon;
use File;

class EmailExpiredItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will notify all the recipient for the WBS expiration of items.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->email('mysqlwbscn','sqlsrvcn','Items_to_Expire_CN');
        //$this->email('mysqlcn','Items_to_Expire_CN');
    }

    private function email($connection,$mssql,$filename)
    {
        $data = [];
        $dtnxtmonth = Carbon::now()->addMonths(1); // get date nxt month

        $now = Carbon::now(); //get date now

        $getdate = DB::connection($connection)->table('tbl_wbs_inventory')
                    ->select('item',
                            'item_desc',
                            'qty',
                            'lot_no',
                            'location',
                            'received_date')
                    ->where('qty','>', 0.0000)
                    ->where('deleted','<>',1)
                    ->orderBy('received_date','desc')
                    ->get();

        foreach ($getdate as $key => $inv) {
            if ($inv->received_date == null) {
                \Log::info('No expiring items: '.date('Y-m-d g:i:s a'));
            } else {
                $received = Carbon::createFromFormat('Y-m-d', $inv->received_date); // convert received date format

                $class = DB::connection($mssql)
                            ->table('XITEM')
                            ->select('BUNR as bunr')
                            ->where('CODE',$inv->item)
                            ->where('BUMO', 'LIKE', 'PURH%')
                            ->where('BUNR', '<>',  '')
                            ->orderBy('BUNR')
                            ->first();

                if ($this->checkIfExistObject($class) > 0) {
                    if ($class->bunr == 'CONTACT' || $class->bunr == 'CT With MOQ') {
                        $exp_date = $received->addMonths(6);
                        $expdate = substr($exp_date,0,10);
                        $nxtmonth = substr($dtnxtmonth,0,10);
                        $check = $now->diffInMonths($exp_date); //

                        if ($check == 1 && $expdate == $nxtmonth) { // 
                            $data[$key]['item'] = $inv->item;
                            $data[$key]['item_desc'] = $inv->item_desc;
                            $data[$key]['qty'] = $inv->qty;
                            $data[$key]['lot_no'] = $inv->lot_no;
                            $data[$key]['location'] = $inv->location;
                            $data[$key]['received_date'] = $inv->received_date;
                            $data[$key]['exp_date'] = $expdate;
                        }
                    } else {
                        $exp_date = $received->addMonths(24);
                        $expdate = substr($exp_date,0,10);
                        $nxtmonth = substr($dtnxtmonth,0,10);
                        $check = $now->diffInMonths($exp_date); //

                        if ($check == 1 && $expdate == $nxtmonth) { // && $expdate == $nxtmonth
                            $data[$key]['item'] = $inv->item;
                            $data[$key]['item_desc'] = $inv->item_desc;
                            $data[$key]['qty'] = $inv->qty;
                            $data[$key]['lot_no'] = $inv->lot_no;
                            $data[$key]['location'] = $inv->location;
                            $data[$key]['received_date'] = $inv->received_date;
                            $data[$key]['exp_date'] = $expdate;
                        }
                    }
                } else {
                    $exp_date = $received->addMonths(24);
                        $expdate = substr($exp_date,0,10);
                        $nxtmonth = substr($dtnxtmonth,0,10);
                        $check = $now->diffInMonths($exp_date); //

                        if ($check == 1 && $expdate == $nxtmonth) { // 
                            $data[$key]['item'] = $inv->item;
                            $data[$key]['item_desc'] = $inv->item_desc;
                            $data[$key]['qty'] = $inv->qty;
                            $data[$key]['lot_no'] = $inv->lot_no;
                            $data[$key]['location'] = $inv->location;
                            $data[$key]['received_date'] = $inv->received_date;
                            $data[$key]['exp_date'] = $expdate;
                        }
                }
            }
            
        }

        $countset = DB::connection($connection)
                    ->table('tbl_wbs_emailsettings')
                    ->count();

        if ($countset > 0) {
            $recipients = [];
            $emails = DB::connection($connection)
                    ->table('tbl_wbs_emailsettings')
                    ->get();

            foreach ($emails as $key => $email) {
                array_push($recipients, $email->sendto);
            }

            if (empty($data)) {
                \Log::info('No expiring items: '.date('Y-m-d g:i:s a'));
            } else {
                $dt = Carbon::now();
                $pathToFile = storage_path().'/email_attachements/'.$dt->format('Y-m-d').'/'.$filename.$dt->format('Y-m-d').'.txt';
                
                $this->generateTxtFile($filename,$data,$dt->format('Y-m-d'));

                Mail::send('email.mail', ['data'=>$data], function ($mail) use ($recipients,$pathToFile) {
                    $mail->to($recipients)
                        ->from('pmi.subsystem@gmail.com','WBS Subsystem')
                        ->subject('WBS: Items will expire in a month (CN)');
                    $mail->attach($pathToFile);
                });
                \Log::info('send at '.date('Y-m-d g:i:s a'));
            }
            
        } else {
            \Log::info('sending failed at '.date('Y-m-d g:i:s a'));
        }
    }

    private function generateTxtFile($filename,$data,$date)
    {
        $path = storage_path().'/email_attachements/'.$date;
                        File::makeDirectory($path, 0777, true, true);
                        if (!File::exists($path)) {
                            File::makeDirectory($path, 0777, true, true);
                        }
        $content = "item\titem_desc\tqty\tlot_no\tlocation\treceived_date\texp_date\r\n";

        foreach ($data as $key => $x) {
            $content .= $x['item']."\t".$data[$key]['item_desc']."\t".$data[$key]['qty']."\t".$data[$key]['lot_no']."\t".$data[$key]['location']."\t".$data[$key]['received_date']."\t".$data[$key]['exp_date']."\r\n";
        }

        $myfile = fopen($path."/".$filename.$date.".txt", "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);

    }

    private function checkIfExistObject($object)
    {
       return count( (array)$object);
    }
}
