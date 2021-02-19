<?php
namespace App\Http\Controllers\Phase1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Illuminate\Support\Facades\Auth; #Auth facade
use App\mUserprogram;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use File;
use PDF;
use Illuminate\Support\Facades\Redirect;
use Config;
use App;
use Response;
use Zipper;
use Dompdf\Dompdf;

class MaterialListController extends Controller
{
	protected $mysql;
    protected $mssql;
    protected $common;
    //    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
		$common = new CommonController;
		if(!$common->getAccessRights(Config::get('constants.MODULE_CODE_MATERIAL'), $userProgramAccess))
		{
			return redirect('/home');
		}
		else
		{
			return view('phase1.materiallist',['userProgramAccess' => $userProgramAccess]);
		}
    }


    public function postGenerateMaterialList()
    {
        # declare variables
		$dt = Carbon::now();
        $date = $dt->format('Y-m-d');
        $time = $dt->format('his');
        $html = "";
        $popdfname = "";
        $order = "";
        $bom = "";
        $po = '';

        $date2 = substr($dt->format('Ymd'), 2);
        $filename = 'Material_List_For_Direct_Ordering_'.$date2.'.zip';
        $path = storage_path().'/Material_List_For_Direct_Ordering';

        # delete the existing folder
        if (File::exists($path)) {
            File::deleteDirectory($path, true);
        }

        //VALIDATE FILES
	    if($this->validateFileContentFormat('file1') && $this->validateFileContentFormat('file2')) {
	        
        	//GET THE CONTENT OF FILE
            $file1content = $this->getFileContent('file1');
            $counter = 0;

            //SPLIT THE CONTENT PER LINE
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $file1content) as $line) {
              	//SKIP THE HEADER OF FILE
              	if($counter != 0)
              	{
              		//SPLIT THE CONTENT
	                $data = explode("\t", $line);

	                $po = $data[0];
                    $poqty = preg_replace('/[,]/', '', (isset($data[4])? $data[4] : 0));
	                //CHECK PO
	                if($po!="") {
	                	//SELECT BOM CONTENT THAT WILL MATCH THE PO OF ORDER
	                	$bomcontent = $this->getBOMContent($po,"file2");

	                	if(!empty($bomcontent)) {
                			//generate html for pdf
                			// $html = $html.
                            $this->generateHTML($bomcontent,$data,$date,$path,$po,$date2);

                			if($order!="")
                				$order = $order."\r\n";
                			//generate order for text file
                			$order = $order.$this->generateOrderText($data);

                			if($bom!="")
                				$bom = $bom."\r\n";

                			//generate bom for text file
                			$bom = $bom.$this->generateBOMText($bomcontent,$poqty);
                            //return dd($bom);
                		}
	                $po = $data[0];
	               }

	            }
            	$counter++;

            }

            $db = Auth::user()->productline;
      		
      		# generate the files
  			$this->generateTxtFile("MLP02_DirectOrder",$order,$date2);
  			$this->generateTxtFile("MLP01_DirectOrder",$bom,$date2);

  			$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		    //$this->savePDF($html,$path,$po,$date2);

		    # download the zip file
		    return $this->downloadZip($path, $filename);
		    
	    } else {
	    	return Redirect::back()->with('err_message','Invalid File Format or Content !');
	    }
    }

    public function validateFileContentFormat($file)
    {

    	$validator1="";
    	$validator2="";
    	if($this->validateFile($file))
    	{
    		if(Input::hasFile($file))
		        {

		        	$content = $this->getFileContent($file);
		        	$counter = 0;

		            //SPLIT THE CONTENT PER LINE
		            foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line)
		              {

		              	if($counter==0)
		              	{
		              		$m = explode("\t", $line);
		              		$z = 0;
		              		foreach($m as $x)
		              		{
			                	if($z==0)
			                		$validator1 = preg_replace('/\s+/', '', $x);
			                	else if($z==1)
			                		$validator2 = preg_replace('/\s+/', '', $x);
			                	$z++;
			                }
		              	}

		              	$counter++;

		              }

		              if($validator1=="PO"&&$validator2=="Ref.No.")
		              	return true;
		              else
		              	return false;
		        }
		        else
		        	return false;

    	}else
    	return false;
    }

    public function getBOMContent($po,$file)
    {
    	$output = array();

    	if(Input::hasFile($file))
		        {

		            $filecontent = $this->getFileContent($file);

		            $counter = 0;
		            foreach(preg_split("/((\r?\n)|(\r\n?))/", $filecontent) as $line)
		              {
		              		$data = explode("\t", $line);
		              		$c = 0;
		              		if($counter != 0)
				                foreach($data as $d)
				                {
				                	if($c==0)
				                		if($po==$d)
				                		{
				                			$output[] = $line;
				                		}

				                	$c++;
				                }

				                $counter++;

		              }

		            
		        }
		  return $output;

    }

    private function downloadZip($path, $filename)
    {
    	# zip all the files
		$files = glob($path.'/*');
		Zipper::make($path.'/'.$filename)->add($files)->close();

		#download the zip file
		$headers = [
    					'Content-type'=>'text/plain', 
    					'Content-Disposition'=>sprintf('attachment; filename="%s"', $filename)
    				];
	
		return \Response::download($path.'/'.$filename, $filename, $headers);
    }

    private function savePDF($html,$path,$po,$date2)
    {
    	return PDF::loadHTML($html)
    			->setPaper('A4')
    			->setOrientation('landscape')
                ->setOption('margin-top', 10)
    			->setOption('margin-bottom', 20)
    			->save($path.'/MaterialList_'.$po.'_'.$date2.'.pdf');
    }
    

	public function getFileContent($file)
	{
			$f = Input::file($file);
            return File::get($f);	
	}


    public function validateFile($file)
    {

    	$rules = array(
        $file => 'required|mimes:txt',
        );

        $validator = Validator::make(Input::all(), $rules);
        if($validator->fails())
	        return false;
	    else
	    	return true;

    }

    public function generateOrderText($d)
    {
    	$i = '"';
    	$o = '";"';
        $fdate = Carbon::parse($d[6]);
        $date = $fdate->format('ymd');
    	return $i.trim(preg_replace('/\t+/','',$d[0].$d[1])).$o.trim(preg_replace('/\t+/','',$d[2])).$o.trim(preg_replace('/\t+/','',$d[3])).$o.trim(preg_replace('/\t+/','',$d[4])).$o.trim(preg_replace('/\t+/','',$date)).$o.trim(preg_replace('/\t+/','',$d[15])).$o.trim(preg_replace('/\t+/','',$d[9])).$o.trim(preg_replace('/\t+/','',$d[10])).$o.trim(preg_replace('/\t+/','',$d[16])).$o.trim(preg_replace('/\t+/','',$d[11])).$o.trim(preg_replace('/\t+/','',$d[20])).$o.Auth::user()->productline.$i;
    }

    public function generateTxtFile($filename,$content,$date)
    {    	
    	$path = storage_path().'/Material_List_For_Direct_Ordering';
			            if (!File::exists($path)) {
			                File::makeDirectory($path, 0777, true, true);
			            }

    	$myfile = fopen($path."/".$date."_".$filename.".txt", "w") or die("Unable to open file!");
		fwrite($myfile, $content);
		fclose($myfile);
    }

    private function getData($db,$select,$table,$where,$equal)
    {
    	try {
        $i = DB::connection($db)
                    ->table($table)
                    ->where($where,$equal)
                    ->get();

                    if(count($i) != 0)
                    	return $i[0]->$select;
                    else
                    	return "";
           } catch (Exception $e) {
	            return "";
	        }
    }

    public function generateBOMText($bomcontent,$poqty)
    {
    	$output = "";
        $tot_usage = 0;

    		foreach($bomcontent as $line)
    		{
    			$d = explode("\t", $line);

                $basic_qty = preg_replace('/[,]/', '', $d[14]);

                $basic_usage = preg_replace('/[,]/', '', $d[6]);

                $usage = $basic_usage / $basic_qty;

                $tot_usage = $usage*$poqty;

    						

                if($output!="")
                	$output = $output."\r\n";

                $i = '"';
		    	$o = '";"';
		    	 $output = $output.$i.trim(preg_replace('/\t+/','',$d[0].$d[1])).
                 $o.trim(preg_replace('/\t+/','',$d[3])).
                 $o.trim(preg_replace('/\t+/','',mb_convert_encoding($d[4],"UTF-8","SJIS"))).
                 $o.trim(preg_replace('/\t+/','',$d[2])).
                 $o.trim(preg_replace('/\t+/','',$d[5])).
                 $o.trim(preg_replace('/\t+/','',$usage)).
                 $o.trim(preg_replace('/\t+/','',$tot_usage)).
                 $o.$this->getData($this->mssql,'VENDOR',"XITEM","CODE",$d[5]).
                 $o.trim(preg_replace('/\t+/','','')).
                 $o.trim(preg_replace('/\t+/','',mb_convert_encoding($d[9],"UTF-8","SJIS"))).
                 $o.trim(preg_replace('/\t+/','','')).
                 $o.trim(preg_replace('/\t+/','','')).
                 $o.trim(preg_replace('/\t+/','','')).
                 $o.trim(preg_replace('/\t+/','',substr($d[12],2))).
                 $o.trim(preg_replace('/\t+/','',$this->getData($this->mssql,'PRICE','XTANK','CODE',$d[5]))).$i;
    			
    		}


    	return $output;
    }

    public function generateHTML($bomcontent,$d,$date,$path,$po,$date2)
    {
        $supplier = [];

        foreach($bomcontent as $line)
        {
            $detail = explode("\t", $line);
            $sup = $this->getData($this->mssql,"VENDOR","XITEM","CODE",$detail[5]);
            array_push($supplier, $sup);
        }

        $data = [
            'date' => $date,
            'time' => date("h:i:sa"),
            'bomcontent' => $bomcontent,
            'orderqty' => preg_replace('/[,]/', '', $d[4]),
            'supplier' => $supplier,
            'details' => $d,
        ];

        $header = '<!DOCTYPE html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <style>
                        .leftAlign {
                            text-align: left;
                            width:16.66%;
                        }
                        .page-break {
                            page-break-after: always;
                        }
                        body{
                            margin:0 auto;
                        }
                        td{
                            text-align: center;
                        }
                        .leftAlignColspan2 {
                            text-align: left;
                            width:33.33%;
                        }
                        .tg  {border-collapse:collapse;border-spacing:0;}
                        .tg td{font-family:Arial, sans-serif;font-size:15px;padding:10px 5px;border-style:solid;border-width:2px;overflow:hidden;word-break:normal;}
                        .tg th{font-family:Arial, sans-serif;font-size:15px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:2px;overflow:hidden;word-break:normal;}
                        .tg .tg-7uzy{vertical-align:top; font-weight: 900; height: 18px;}
                        .tg .tg-yw4l{vertical-align:top; font-weight: 900; height: 18px;}
                        .tg-wrap {width: 100%}
                    </style>
                </head>
                <body>
                    <p align="right" style="font-size: 14px;">Date: '.$date.' Time: '.date("h:i:sa").'</p>
                    <center>
                        <h1>
                            <p>MATERIAL LIST INFORMATION <span style="font-size: 13px;"> (DIRECT ORDERED) </span></p>
                        </h1>
                    </center>

                    <table style="width:100%;font-size: 15px;">
                        <tr>
                            <th style ="text-align: left;">&nbsp'.$d[12].' &nbsp &nbsp &nbsp &nbsp '.$d[13].'</th>
                            <th style ="text-align: right;">&nbspYAMAICHI ELECTRONICS CO,LTD.</th>
                        </tr>
                    </table>

                    <table class="tg" style="width:100%; font-size: 15px;" border="2" cellspacing="0"  cellpadding="0" >
                        <tr>
                            <th class="tg-yw4l leftAlign">&nbspPO No.</th>
                            <th class="tg-yw4l leftAlign">&nbspYEC SALES ORDER NO.</th>
                            <th colspan="2" class="tg-yw4l leftAlignColspan2">&nbspCUSTOMER &nbsp &nbsp &nbsp &nbsp&nbsp &nbsp&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp&nbsp &nbsp &nbsp &nbsp'.$d[9].'</th>
                            <th colspan="2" class="tg-yw4l leftAlignColspan2">&nbspORDER QTY &nbsp &nbsp&nbsp &nbsp &nbsp &nbsp &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  '.$d[4].'</th>
                        </tr>
                        <tr>
                            <th class="tg-yw4l leftAlign">&nbsp'.$d[0].' - '.$d[1].'</th>
                            <th class="tg-yw4l leftAlign">&nbsp'.$d[7].' - '.$d[8].'</th>
                            <th colspan="2" class="tg-yw4l leftAlignColspan2">&nbspCUSTOMER NAME &nbsp &nbsp &nbsp&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp '.$d[10].'</th>
                            <th class="tg-yw4l leftAlign" rowspan="2">&nbspDrawing No. <br>&nbsp'.$d[11].'&nbsp</th>
                            <th class="tg-yw4l leftAlign" rowspan="2">&nbspTRANSMIT ORDER NO. <br>&nbsp'.$d[14].'&nbsp</th>
                        </tr>
                        <tr>
                            <th colspan="2" class="tg-yw4l leftAlignColspan2">&nbspPRODUCT CODE  &nbsp &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp '.$d[3].'</th>
                            <th colspan="2" class="tg-yw4l leftAlignColspan2">&nbspPRODUCT NAME &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp '.$d[2].'</th>
                        </tr>
                        <tr>
                            <th class="tg-yw4l" colspan="6" style="text-align: left;">&nbspInstruction: </th>
                        </tr>
                    </table>
                </body>';

                



        return PDF::loadView('pdf.material_list', $data)
                ->setPaper('A4')
                ->setOrientation('landscape')
                ->setOption('header-html', $header)
                ->setOption('margin-top', 75)
                ->setOption('margin-bottom', 15)
                ->save($path.'/MaterialList_'.$po.'_'.$date2.'.pdf');
    }


    public function getMaterialListTable($bomcontent,$orderqty)
    {
    	$output = "";
        $color = '';
        $totalusage = '';

    		foreach($bomcontent as $line)
    		{
    			$d = explode("\t", $line);

                if ($d[2] == '2') {
                    $color = '#01a7e1';
                } else {
                    $color = '';
                }

                $totalusage = (float)$d[6] * $orderqty;
                $output = $output.'<tr class="line-height" style="background-color: '.$color.'">
									    <td>'.$d[2].'</td>
									    <td>'.$d[5].'</td>		
									    <td>'.mb_convert_encoding($d[4],"UTF-8","SJIS").'</td>
									    <td>'.mb_convert_encoding($d[9],"UTF-8","SJIS").'</td>
									    <td>'.$d[7].'</td>		
									    <td>'.number_format($d[6],4).'</td>
									    <td>'.$totalusage.'</td>
									    <td>'.$this->getData($this->mssql,"VENDOR","XITEM","CODE",$d[5]).'</td>		
									    <td></td>
									</tr>';
    			
    		}


    	return $output;
    }
}
