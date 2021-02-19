<?php
namespace App\Http\Controllers\Phase1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; #Auth facade
use Illuminate\Http\Request;
use App\Http\Requests;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Schema\Blueprint;
use Storage;
use DB;
use App;
use PDF;
use Excel;
use PHPExcel_Style_Fill;
use Config;
use Schema;
use Zipper;

class OrderDataCheckController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;
    protected $com;
    private $_priceCount = '', $_BOMCount = '', $_UnitCount = '',
            $_ItemNameProdCount = '', $_ItemNamePartCount = '',
            $_ItemProdCount = '', $_ItemPartCount = '', $part_split = 0, $prod_split = 0;

    public function __construct()
    {
        $this->middleware('auth');
        $this->com = new CommonController;

        if (Auth::user() != null) {
            $this->mysql = $this->com->userDBcon(Auth::user()->productline,'mysql');
            $this->mssql = $this->com->userDBcon(Auth::user()->productline,'mssql');
            $this->common = $this->com->userDBcon(Auth::user()->productline,'common');
        } else {
            return redirect('/');
        }
    }

    public function getOrderDataCheck()
    {
        if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_CHECK'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {

            return view('phase1.OrderDataChecks',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function postReadFiles(Request $request)
    {
        // for getting extension getClientOriginalExtension()
        // for getting path getPathName();
        // get the mime type getMimeType()
        // get the max file size set from php.ini getMaxFilesize()
        // for TS check data to pmi_iscd
        // for CN check data to pmi_cn or pmi_yf
        try {
           $txt = array(
                      'part' => $request->file('mlp01uf'),
                      'prod' => $request->file('mlp02uf')
                      );
            //echo $txt['part']->getClientOriginalName().' '.$txt['prod']->getClientOriginalName();
            if (!empty($txt['part']) && !empty($txt['prod'])) {

                $file1 = $txt['part']->getClientOriginalName();
                $file2 = $txt['prod']->getClientOriginalName();
                $ext1 = $txt['part']->getClientOriginalExtension();
                $ext2 = $txt['prod']->getClientOriginalExtension();

                if ($ext1 != 'txt' || $ext2 != 'txt') {
                    $message = "Please select a txt extension file only.";
                    return redirect(url('/orderdatacheck'))->with(['err_message' => $message]);
                }
                if ((strpos($file1,"MLP01") || strlen(strstr($file1,"MLP01"))>0)
                    && (strpos($file2,"MLP02") || strlen(strstr($file2,"MLP02"))>0))
                {

                        $file1name = str_replace('.txt', '', $file1);
                        $inputdate = date('ymd');//substr($file1name, -6 , strlen($file1name));

                        $db = Auth::user()->productline;
                        $schema = '';

                        #check file if direct order
                        if(is_numeric(strpos(strtoupper($file1),"DirectOrder"))
                            && is_numeric(strpos(strtoupper($file2),"DirectOrder")))
                        {
                            $this->part_split = 15;
                            $this->prod_split = 16;
                        } else {
                            $this->part_split = 16;
                            $this->prod_split = 16;
                        }

                        header('content-type: text/html; charset=utf-8');
                        return $this->readUploadsTS($txt['part'],$txt['prod'],$db, $inputdate);
                }else {
                    $message = "Text files might mixed up.";
                    return redirect(url('/orderdatacheck'))->with(['err_message' => $message]);

                }
            } else {
                $message = "Please select a text file for each field.";
                return redirect(url('/orderdatacheck'))->with(['err_message' => $message]);
            }
        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }

    }

    private function readUploadsTS($txt1,$txt2,$db,$inputdate)
    {
        $data = array();
        $parts = array();
        $files1 = explode(PHP_EOL, file_get_contents($txt1)); // 16 arrays
        $files2 = explode(PHP_EOL, file_get_contents($txt2)); // 13 arrays

        $this->insertOrderDataReportDetails($db, $inputdate);

        $partsPO = $this->PartsPO($files1);
        $partsDivUsage = $this->PartsDivUsage($files1);
        $partsCode = $this->PartsItemCode($files1);
        $partsUsage = $this->PartsUsage($files1);
        $partsDnum = $this->PartsDrawingNum($files1);
        $partsName = $this->PartsItemName($files1);
        $partsUnit = $this->PartsUnit($files1);
        $partSE = $this->partStartEnd($files1);
        $jdate = $this->PartsJdate($files1);
        $partsQTY = $this->PartsQTY($files1);
        $partsVendor = $this->PartsVendor($files1);

        $ProdCode = $this->ProdItemCode($files2);
        $ProdDnum = $this->ProdDrawNum($files2);
        $ProdName = $this->ProdItemName($files2);
        $ProdPrice = $this->ProdPrice($files2);
        $ProdCust = $this->ProdCust($files2);
        $TSPO = count($this->ProdPO($files2));
        $prodSE = $this->prodStartEnd($files2);
        $prodPO = $this->ProdPO($files2);
        $custName = $this->ProdCustName($files2);
        $qty = $this->ProdQTY($files2);
        $date = $this->ProdCdate($files2);
        $buyers = $this->ProdBuyers($files2);
        $con = $this->con($files2);

        $this->InsertToTempTable1($partsPO,$partsName,$partsDivUsage,$partsCode,$partsUsage,$partsQTY,$partsVendor,$partsDnum,$jdate,$partsUnit);
        $this->InsertToTempTable2($prodPO,$ProdName,$ProdCode,$qty,$date,$ProdCust,$custName,$ProdPrice,$ProdDnum,$buyers,$con);

        $BOM = $this->BOM($db);
        $Usage = $this->Usage($db);
        $ItemName = $this->ItemName_Master($db);
        $Item = $this->Item_Master($db);
        $Unit = $this->Unit_Master($db);
        $Price = $this->Price_Master($db);
        $Order = $this->Order_Entry($db);
        $Products = $this->Products($db);

        $uSalesPrice = $this->getUnmatchSalesPrice($Price['code_unmatch'],$db);
        $uUnitPrice = $this->getUnmatchUnitPrice($Unit['code_unmatch'],$db);
        $uBOM = $this->getUnmatchBOM($BOM['code_unmatch'],$BOM['kcode_unmatch'],$BOM['usage_unmatch'],$db);

        $uUsage = $this->getUnmatchUsage($Usage['code_unmatch_usage'],$Usage['kcode_unmatch_usage'],$Usage['usage_unmatch_usage'],$db);

        //echo "<pre>",print_r($uBOM),"</pre>";
        $uSupplier = $this->getUnmatchSupplier($Item['unmatchSupplierCode'],$db);
        $uPartName = $this->getUnmatchPartName($Item['code_unmatch_partName'],$Item['name_unmatch_partName'],$db);
        $uProductName = $this->getUnmatchProductName($Item['code_unmatch_prodName'],$db);
        $uProductDN = $this->getUnmatchProductDN($Item['code_unmatch_prodDN'],$db);
        $uPartDN = $this->getUnmatchPartDN($Item['code_unmatch_partDN'],$db);

        $r3_dn = [];
        $r3_partdn = [];
        foreach ($Item['code_unmatch_prodDN'] as $key => $code) {
            $r3_dn[$code] = $Item['dn_unmatch_prodDN'][$key];
        }
        foreach ($Item['code_unmatch_partDN'] as $key => $code) {
            $r3_partdn[$code] = $Item['dn_unmatch_partDN'][$key];
        }

        //Truncate unmatch tables
            $this->truncateTable('unmatch_partdn');
            $this->truncateTable('unmatch_proddn');
            $this->truncateTable('unmatch_supts');
            $this->truncateTable('unmatch_prodname');
            $this->truncateTable('unmatch_partname');
            $this->truncateTable('unmatch_unitprice');
            $this->truncateTable('unmatch_salesprice');
            $this->truncateTable('unmatch_usgts');
            $this->truncateTable('unmatch_bom');

        //Insert into table unmatch
            $this->umatchPartdnInsertDB($uPartDN,$Item['dn_unmatch_partDN']);
            $this->umatchProddnInsertDB($uProductDN,$Item['dn_unmatch_prodDN']);
            $this->umatchSupplierInsertDB($uSupplier,$Item['unmatchSupplier']);
            $this->umatchProdNameInsertDB($uProductName,$Item['name_unmatch_prodName'],$r3_dn,$Item['con_unmatch_prodName']);
            $this->umatchPartNameInsertDB($uPartName,$Item['name_unmatch_partName']);
            $this->umatchBOMInsertDB($uBOM,$BOM['po_unmatch'],$BOM['code_unmatch'],$BOM['kcode_unmatch'],$BOM['name_unmatch'],$BOM['partsname_unmatch'],$BOM['supplier_unmatch'],$BOM['usage_unmatch'],$BOM['DivUsageUnmatch']);
            $this->umatchUnitInsertDB($uUnitPrice,$Unit['unit_unmatch'],$Unit['vendor_unmatch']);
            $this->umatchSalesInsertDB($uSalesPrice,$Price['price_unmatch']);
            $this->umatchUsageInsertDB($uUsage,$Usage['po_unmatch_usage'],$Usage['code_unmatch_usage'],$Usage['name_unmatch_usage'],$Usage['kcode_unmatch_usage'],$Usage['pname_unmatch_usage'],$Usage['vendor_unmatch_usage'],$Usage['usage_unmatch_usage'],$Usage['divusage_unmatch_usage']);


        //Retrieve unmatch
            $umPartDN = $this->getUnmatch('unmatch_partdn');
            $umProdDN = $this->getUnmatch('unmatch_proddn');
            $umSupplier = $this->getUnmatch('unmatch_supts');
            $umProdname = $this->getUnmatch('unmatch_prodname');
            $umPartname = $this->getUnmatch('unmatch_partname');
            $umUsage = $this->getUnmatch('unmatch_usgts');
            $umUnit = $this->getUnmatch('unmatch_unitprice');
            $umSales = $this->getUnmatch('unmatch_salesprice');
            $umBOM = $this->getUnmatch('unmatch_bom');

        //Count Unmatch
            $countUnmatchPartDN = $this->countUnmatch('unmatch_partdn','error');
            $countUnmatchProdDN = $this->countUnmatch('unmatch_proddn','error');
            $countUnmatchSupp = $this->countUnmatch('unmatch_supts','error');
            $countUnmatchProdName = $this->countUnmatch('unmatch_prodname','error');
            $countUnmatchPartName = $this->countUnmatch('unmatch_partname','error');
            $countUnmatchUnit = $this->countUnmatch('unmatch_unitprice','error');
            $countUnmatchSales = $this->countUnmatch('unmatch_salesprice','error');
            $countUnmatchUsage = $this->countUnmatch('unmatch_usgts','error');
            $countUnmatchBOM = DB::connection($this->mysql)->table('unmatch_bom')->where('error',1)->count();



        //Reports

            $path = storage_path().'/Order_Data_Check';

            if (File::exists($path)) {
                File::deleteDirectory($path, 0777, true, true);
            }

            if ($Products['nonexist'] > 0) {
                //echo "<pre>",print_r($this->getCustName($Products['cust'],$db)),"</pre>";
                $this->postNewProductPDF($Products['po'],$Products['code'],$Products['name'],$Products['drawing_num'],$Products['cust'],$Products['custname'],$Products['qty'],$db);
            }
            if ($BOM['non_exist'] > 0) {
                $this->generateTSBOM($BOM['code_nonexist'],$BOM['NameNonExist'],$BOM['kcode_nonexist'],$BOM['PartsNameNonExist'],$BOM['UsageNonExist'],$BOM['DivUsageNonExist']);
            }
            if ($Item['prod_nonexist'] > 0) {
                $this->generateItemProd($Item['CodeProdNonExist'],$Item['NameProdNonExist'],$Item['DnumProdNonExist'],$db);
            }
            if ($Item['part_nonexist'] > 0) {
                $this->generateItemParts($Item['CodePartNonExist'],$Item['NamePartNonExist'],$Item['DnumPartNonExist']);
            }
            if ($ItemName['prod_nonexist'] > 0) {
                $this->generateItemNameProd($ItemName['CodeProdNonExist'],$ItemName['NameProdNonExist'],$db);
            }
            if ($ItemName['part_nonexist'] > 0) {
                $this->generateItemNameParts($ItemName['CodePartNonExist'],$ItemName['NamePartNonExist']);
            }
            if ($Unit['non_exist'] > 0) {
                $this->generateUnitPrice($Unit['code_nonexist'],$Unit['VendorNonExist'],$Unit['UnitNonExist'],$Unit['NameNonExist'],$db);
            }

            $this->generateTSOrderEntry($prodPO,$ProdCode,$ProdCust,$qty,$buyers,$date,$jdate);
            $this->generateTSPriceMaster($prodPO,$ProdCode,$ProdName,$ProdCust,$custName,$ProdPrice,$Unit['code_nonexist'],$Unit['UnitNonExist'],$Unit['NameNonExist'],$db);

            $PriceCount = count(array_unique($ProdCode));
            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            //return dd($partSE['partStart']);


        return redirect(url('/orderdatacheck'))
                ->with([
                  'PO' => DB::connection($this->mysql)->table('tbl_orderdatacheck2')->count(),
                  'con' => Auth::user()->productline,
                  'NormalPO' => $Products['exist'],
                  'partStartPO' => $partSE['partStart'],
                  'partEndPO' => $partSE['partEnd'],
                  'prodStartPO' => $prodSE['prodStart'],
                  'prodEndPO' => $prodSE['prodEnd'],
                  'MLP01name' => $txt1->getClientOriginalName(),
                  'MLP02name' => $txt2->getClientOriginalName(),
                  'BOM' => $BOM,
                  'BOMCount' => $this->_BOMCount,
                  'Price' => $Price,
                  'PriceCount' => $this->_priceCount,//$PriceCount
                  'Unit' => $Unit,
                  'UnitCount' => $this->_UnitCount,
                  'ItemName' => $ItemName,
                  'ItemNameProdCount' => $this->_ItemNameProdCount,
                  'ItemNamePartCount' => $this->_ItemNamePartCount,
                  'Item' => $Item,
                  'ItemProdCount' => $this->_ItemProdCount,
                  'ItemPartCount' => $this->_ItemPartCount,
                  'Order' => $Order,
                  'Products' => $Products,
                  'uSalesPrice' => $umSales,
                  'uSalescount' => $countUnmatchSales,
                  'uUnitPrice' => $umUnit,
                  'uUnitcount' => $countUnmatchUnit,
                  'uSupplier' => $umSupplier,
                  'uSuppcount' => $countUnmatchSupp,
                  'uBOM' => $umBOM,
                  'uBOMcount' => $countUnmatchBOM,
                  'uUsage' => $umUsage,
                  'uUsagecount' => $countUnmatchUsage,
                  'uPartName' => $umPartname,
                  'uPartNamecount' => $countUnmatchPartName,
                  'uProductName' => $umProdname,
                  'uProdNamecount' => $countUnmatchProdName,
                  'uProductDN' => $umProdDN,
                  'uProdDNcount' => $countUnmatchProdDN,
                  'uPartDN' => $umPartDN,
                  'uPartDNcount' => $countUnmatchPartDN
                ]);

    }

    private function insertOrderDataReportDetails($db,$inputdate)
    {
        $result = false;
        $con = $this->com->userDBconFromStr($db);
        try
        {
            # check if the db already exists.
            $result_cnt = DB::connection($this->mysql)->table('tbl_order_data_report_details')
                        ->where('db', $this->mssql)
                        ->count();
            if($result_cnt >= 0)
            {
                # set inputdate, db for order data report details
                $result = DB::connection($this->mysql)->table('tbl_order_data_report_details')
                    ->insert([
                            'inputdate'  => $inputdate,
                            'db'         => $this->mssql,
                            'created_at' => date("Y/m/d h:i:sa"),
                            'updated_at' => date("Y/m/d h:i:sa")
                            ]);
            }
            else
            {
                # set inputdate, db for order data report details
                $result = DB::connection($this->mysql)->table('tbl_order_data_report_details')
                        ->where('db', $this->mssql)
                        ->update([
                            'inputdate'  => $inputdate,
                            'created_at' => date("Y/m/d h:i:sa"),
                            'updated_at' => date("Y/m/d h:i:sa")
                            ]);
            }
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result;
    }

    private function partStartEnd($files1)
    {
    	$start = str_split(reset($files1), 16);//this is an array
        $end = str_split(end($files1), 16);//this is an array

        $partStart = trim($start[0],'"');
        $partEnd = trim($end[0],'"');
        if ($partEnd == "") {
            $end = str_split(prev($files1),16);
            $partEnd = trim($end[0],'"');
        }else{
            $message = "Cannot read file.";
            return ['partStart' => $partStart, 'partEnd' => $partEnd];//redirect(url('/orderdatacheck'))->with(['err_message' => $message]);
        }

        return ['partStart' => $partStart, 'partEnd' => $partEnd];
    }

    private function prodStartEnd($files2)
    {
    	$start = str_split(reset($files2), 16);//this is an array
        $end = str_split(end($files2), 16);//this is an array
        $prodStart = trim($start[0],'"');
        $prodEnd = trim($end[0],'"');
        if ($prodEnd == "" || $files2 != null) {
            $end = str_split(prev($files2),16);
            $prodEnd = trim($end[0],'"');
        }else{
            $message = "Cannot read file.";
            return ['prodStart' => $prodStart, 'prodEnd' => $prodEnd];//redirect(url('/orderdatacheck'))->with(['err_message' => $message]);
        }

        return ['prodStart' => $prodStart, 'prodEnd' => $prodEnd];
    }

    /*PARTS FILES DATA*/
		private function PartsPO($files1)
		{
			$PO = [];
	    	$keys = array_keys($files1);
	        for ($i=0; $i < count($keys); $i++)
	        {
	            $key = $keys[$i];
	            $content = $files1[$key];
	            $data = array_filter(array_map("trim", explode(";", $content)));

	            if (isset($data[0]))
	            {
	            	$PO[] = (isset($data[0]) === TRUE ? trim($data[0],'"') : "");
	            }
	        }
	        return $PO;
		}

	    private function PartsItemName($files1)
	    {
	    	$ItemName = [];
	    	$keys = array_keys($files1);
	        for ($i=0; $i < count($keys); $i++)
	        {
	            $key = $keys[$i];
	            $content = $files1[$key];
	            $data = array_filter(array_map("trim", explode(";", $content)));

	            if (isset($data[0]))
	            {
	            	$ItemName[] = (isset($data[2]) === TRUE ? trim($data[2],'"') : "");
	            }
	        }
	        return $ItemName;
	    }

        private function PartsDivUsage($files1)
        {
            $divUsage = [];
            $keys = array_keys($files1);
            for ($i=0; $i < count($keys); $i++)
            {
                $key = $keys[$i];
                $content = $files1[$key];
                $data = array_filter(array_map("trim", explode(";", $content)));

                if (isset($data[0]))
                {
                    $divUsage[] = (isset($data[3]) === TRUE ? trim($data[3],'"') : "");
                }
            }
            return $divUsage;
        }

	    private function PartsItemCode($files1)
	    {
	    	$ItemCode = [];
	    	$keys = array_keys($files1);
	        for ($i=0; $i < count($keys); $i++)
	        {
	            $key = $keys[$i];
	            $content = $files1[$key];
	            $data = array_filter(array_map("trim", explode(";", $content)));

	            if (isset($data[0]))
	            {
	            	$ItemCode[] = (isset($data[4]) === TRUE ? trim($data[4],'"') : "");
	            }
	        }
	        return $ItemCode;
	    }

        private function PartsUsage($files1)
        {
            $usage = [];
            $keys = array_keys($files1);
            for ($i=0; $i < count($keys); $i++)
            {
                $key = $keys[$i];
                $content = $files1[$key];
                $data = array_filter(array_map("trim", explode(";", $content)));

                if (isset($data[0]))
                {
                    $usage[] = (isset($data[5]) === TRUE ? trim($data[5],'"') : "");
                }
            }
            return $usage;
        }

        private function PartsQTY($files1)
        {
            $qty = [];
            $keys = array_keys($files1);
            for ($i=0; $i < count($keys); $i++)
            {
                $key = $keys[$i];
                $content = $files1[$key];
                $data = array_filter(array_map("trim", explode(";", $content)));

                if (isset($data[0]))
                {
                    $qty[] = (isset($data[6]) === TRUE ? trim($data[6],'"') : "");
                }
            }
            return $qty;
        }

	    private function PartsVendor($files1)
	    {
	    	$vendor = [];
	    	$keys = array_keys($files1);
	        for ($i=0; $i < count($keys); $i++)
	        {
	            $key = $keys[$i];
	            $content = $files1[$key];
	            $data = array_filter(array_map("trim", explode(";", $content)));

	            if (isset($data[0]))
	            {
	            	$vendor[] = (isset($data[7]) === TRUE ? trim($data[7],'"') : "");
	            }
	        }
	        return $vendor;
	    }

	    private function PartsDrawingNum($files1)
	    {
	    	$drawNum = [];
	    	$keys = array_keys($files1);
	        for ($i=0; $i < count($keys); $i++)
	        {
	            $key = $keys[$i];
	            $content = $files1[$key];
	            $data = array_filter(array_map("trim", explode(";", $content)));

	            if (isset($data[0]))
	            {
	            	$drawNum[] = (isset($data[9]) === TRUE ? trim($data[9],'"') : "");
	            }
	        }
	        return $drawNum;
	    }

        private function PartsJdate($files1)
        {
            $jDate = [];
            $keys = array_keys($files1);
            for ($i=0; $i < count($keys); $i++)
            {
                $key = $keys[$i];
                $content = $files1[$key];
                $data = array_filter(array_map("trim", explode(";", $content)));

                if (isset($data[0]))
                {
                    $jDate[] = (isset($data[13]) === TRUE ? trim($data[13],'"') : "");
                }
            }
            return $jDate;
        }

	    private function PartsUnit($files1)
	    {
	    	$units = [];
	    	$keys = array_keys($files1);
	        for ($i=0; $i < count($keys); $i++)
	        {
	            $key = $keys[$i];
	            $content = $files1[$key];
	            $data = array_filter(array_map("trim", explode(";", $content)));

	            if (isset($data[0]))
	            {
	            	$units[] = (isset($data[14]) === TRUE ? trim($data[14],'"') : "");
	            }
	        }
	        return $units;
	    }

	/*PROD FILES DATA*/
		private function ProdPO($files2)
		{
			$PO = [];
	    	$keys = array_keys($files2);
	        for ($i=0; $i < count($keys); $i++)
	        {
	            $key = $keys[$i];
	            $content = $files2[$key];
	            $data = array_filter(array_map("trim", explode(";", $content)));

	            if (isset($data[0]))
	            {
	            	$PO[] = (isset($data[0]) === TRUE ? trim($data[0],'"') : "");
	            }
	        }
	        return $PO;
		}

		private function ProdItemName($files2)
	    {
	    	$ItemName = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $ItemName[] = (isset($data[1]) === TRUE ? trim($data[1],'"') : "");
		        }
		    }
		    return $ItemName;
	    }

	    private function ProdItemCode($files2)
	    {
	    	$ItemCode = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $ItemCode[] = (isset($data[2]) === TRUE ? trim($data[2],'"') : "");
		        }
		    }
		    return $ItemCode;
	    }

	    private function ProdQTY($files2)
	    {
	    	$qty = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $qty[] = (isset($data[3]) === TRUE ? trim($data[3],'"') : "");
		        }
		    }
		    return $qty;
	    }

	    private function ProdCdate($files2)
	    {
	    	$cdate = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $cdate[] = (isset($data[4]) === TRUE ? trim($data[4],'"') : "");
		        }
		    }
		    return $cdate;
	    }

	    private function ProdCust($files2)
	    {
	    	$cust = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $cust[] = (isset($data[6]) === TRUE ? trim($data[6],'"') : "");
		        }
		    }
		    return $cust;
	    }

	    private function ProdCustName($files2)
	    {
	    	$custname = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $custname[] = (isset($data[7]) === TRUE ? trim($data[7],'"') : "");
		        }
		    }
		    return $custname;
	    }

	    private function ProdPrice($files2)
	    {
	    	$price = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $price[] = (isset($data[8]) === TRUE ? trim($data[8],'"') : "");
		        }
		    }
		    return $price;
	    }

	    private function ProdDrawNum($files2)
	    {
	    	$drawNum = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $drawNum[] = (isset($data[9]) === TRUE ? trim($data[9],'"') : "");
		        }
		    }
		    return $drawNum;
	    }

	    private function ProdBuyers($files2)
	    {
	    	$buyers = [];
	    	$keys = array_keys($files2);
	    	for ($i=0; $i < count($keys); $i++) {
		        $key = $keys[$i];
		        $content = $files2[$key];
		        $data = array_filter(array_map("trim", explode(";", $content)));
		        if (isset($data[0])) {
		            $buyers[] = (isset($data[12]) === TRUE ? trim($data[12],'"') : "");
		        }
		    }
		    return $buyers;
	    }

        private function con($files2)
        {
            $con = [];
            $keys = array_keys($files2);
            for ($i=0; $i < count($keys); $i++) {
                $key = $keys[$i];
                $content = $files2[$key];
                $data = array_filter(array_map("trim", explode(";", $content)));
                if (isset($data[0])) {
                    $con[] = (isset($data[11]) === TRUE ? trim($data[11],'"') : "");
                }
            }
            return $con;
        }

	/*CHECK IF EXIST*/
		private function check_itemCode($code,$table,$db)
		{
			$count = DB::connection($this->mssql)->table($table)->where('CODE',$code)->distinct()->count();
        	return $count;
		}

	    private function check_kcode($code,$db)
		{
			$count = DB::connection($this->mssql)->table("XPRTS")->where('KCODE',$code)->distinct()->count();
        	return $count;
		}

        private function check_vcode($code,$db)
        {
            $count = DB::connection($this->mssql)->table("XTANK")->where('VCODE',$code)->where('CODE',$code)->distinct()->count();
            return $count;
        }

        private function check_BOM($code,$db)
        {
            $count = DB::connection($this->mssql)->table("XPRTS as b")
                        ->join('XHEAD as h','b.CODE','=','h.CODE')
                        ->select('b.CODE')
                        ->where('b.CODE',$code)
                        ->groupBy('b.CODE')
                        ->count();
            return $count;
        }


	private function getSupplier($cust,$db)
    {
        $count = DB::connection($this->mssql)
                    ->table('XCUST')
                    ->where('CUST',$cust)
                    ->count();
        return $count;
    }

    private function getSalesPrice($code,$price,$db)
    {
        $count = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XBAIK as s','i.CODE','=','s.CODE')
                    ->select('i.CODE','s.PRICE')
                    ->where('i.CODE',$code)
                    ->where('s.PRICE',$price)
                    ->count();
        return $count;
    }

    private function getUnitPrice($code,$price,$db)
    {
        $count = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XTANK as u','i.CODE','=','u.CODE')
                    ->select('i.CODE','u.PRICE')
                    ->where('i.CODE',$code)
                    ->where('u.PRICE',$price)
                    ->count();
        return $count;
    }

    private function getBOM($code,$kcode,$db)
    {
        $count = DB::connection($this->mssql)
                    ->table('XPRTS as b')
                    ->join('XHEAD as h','b.CODE','=','h.CODE')
                    ->where('b.CODE',$code)
                    ->where('b.KCODE',$kcode)
                    ->select('b.CODE','b.KCODE','h.NAME','b.SIYOU')
                    ->groupBy('b.CODE','b.KCODE','h.NAME','b.SIYOU')
                    ->count();
        return $count;
    }

    private function getUsage($code,$kcode,$usage,$db)
    {
        $count = DB::connection($this->mssql)
                    ->table('XPRTS')
                    ->select('CODE','KCODE','SIYOU')
                    ->where('CODE',$code)
                    ->where('KCODE',$kcode)
                    ->count();
        return $count;
    }

    private function getUnit($code,$unit,$db)
    {
        $count = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XHEAD as h','i.CODE','=','h.CODE')
                    ->join('XTANK as u','i.CODE','=','u.CODE')
                    ->select('u.CODE','u.VCODE','u.PRICE','h.NAME','i.VENDOR')
                    ->where('u.CODE',$code)
                    ->where('u.VCODE',$code)
                    ->where('u.PRICE',$unit)
                    ->count();
        return $count;
    }

    private function getName($code,$name,$db)
    {
        $count = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XHEAD as h','i.CODE','=','h.CODE')
                    ->select('i.CODE','h.NAME','i.DRAWING_NUM')
                    ->where('i.CODE',$code)
                    ->where('h.NAME',$name) //->where('h.NAME','like',$name.'%')
                    ->count();
        return $count;
    }

    private function getDN($code,$drawnum,$db)
    {
        $count = DB::connection($this->mssql)->table('XITEM')
                    ->select('DRAWING_NUM')
                    ->where('CODE',$code)
                    ->where('DRAWING_NUM',$drawnum)
                    ->count();
        return $count;
    }

    private function getPriceMaster($code)
    {
        $pricemaster = DB::connection($this->mssql)->table('XBAIK as P')
                        ->join('XCUST as C','C.CUST','=','P.CUST')
                        ->join('XHEAD as H','H.CODE','=','P.CODE')
                        ->where('P.CODE',$code)
                        //->where('P.CUST',$cust)
                        ->get();
        return $pricemaster;
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

    public function postOrderDataGenPDF(Request $request)
    {
        // Initiation
            if (empty($request->ml01start)) {
                $request->ml01start = 0;
            }
            if (empty($request->ml01end)) {
                $request->ml01end = 0;
            }
            if (empty($request->ml02start)) {
                $request->ml02start = 0;
            }
            if (empty($request->ml02end)) {
               $request->ml02end = 0;
            }
            if (empty($request->ts)) {
                $request->ts = 0;
            }
            if (empty($request->dataentryts)) {
                $request->dataentryts = 0;
            }
            if (empty($request->normalpo)) {
                $request->normalpo = 0;
            }
            if (empty($request->cnpo)) {
                $request->cnpo = 0;
            }
            if (empty($request->newprod)) {
                $request->newprod = 0;
            }
            if (empty($request->poforrs)) {
                $request->poforrs = 0;
            }
            if (empty($request->rsgen)) {
                $request->rsgen = 0;
            }
            if (empty($request->itemnameparts)) {
                $request->itemnameparts = 0;
            }
            if (empty($request->itemmasterparts)) {
                $request->itemmasterparts = 0;
            }
            if (empty($request->unitprice)) {
                $request->unitprice = 0;
            }
            if (empty($request->itemnameprod)) {
                $request->itemnameprod = 0;
            }
            if (empty($request->itemmasterprod)) {
                $request->itemmasterprod = 0;
            }
            if (empty($request->price)) {
                $request->price = 0;
            }
            if (empty($request->bom)) {
                $request->bom = 0;
            }
            if (empty($request->orderts)) {
                $request->orderts = 0;
            }
            if (empty($request->ordercn)) {
                $request->ordercn = 0;
            }

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);
            $ts = 0;
            $cn = 0;
            if ($con == 'TS') {
                $ts = $request->po;
            } else {
                $cn = $request->po;
            }

        $dt = Carbon::now();
        $date = $dt->format('Y-m-d');
        $time = $dt->format('his');
        $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/OrderDataGenerated/';
        $html1 = '<div class="container-fluid" style="font-size:10">
                    <h2>ORDER DATA GENERATE REPORT</h2>
                    <hr size="3"/>

                    <h4>ORIGINAL DATA</h4>
                        <div style="width: 100%">
                            <table style="font-size:10">
                                <tr>
                                    <td width="50px"></td>
                                    <td width="150px"><strong>MLP01UF</strong></td>
                                    <td width="160px">'.$request->mlp01name.'</td>
                                </tr>
                                <tr>
                                    <td width="50px"></td>
                                    <td width="150px"><strong>MLP02UF</strong></td>
                                    <td width="160px">'.$request->mlp02name.'</td>
                                </tr>
                            </table>
                        </div>
                    <hr/>

                    <h4>DATA RANGE</h4>
                        <table style="font-size:10">
                            <tr>
                                <td width="50px"></td>
                                <td width="120px"><strong>* MLP01UF</strong></td>
                                <td width="70px">START:</td>
                                <td width="150px">'.$request->ml01start.'</td>

                                <td width="50px"></td>
                                <td width="120px"><strong>* MLP02UF</strong></td>
                                <td width="70px">START:</td>
                                <td width="150px">'.$request->ml02start.'</td>

                                <td width="50px"></td>
                                <td width="70px">START:</td>
                                <td width="50px">OK</td>
                            </tr>

                            <tr>
                                <td></td>
                                <td><strong>(BOM DATA)</strong></td>
                                <td>END:</td>
                                <td>'.$request->ml01end.'</td>

                                <td></td>
                                <td><strong>(ORDER DATA)</strong></td>
                                <td>END:</td>
                                <td>'.$request->ml02end.'</td>

                                <td></td>
                                <td>END:</td>
                                <td>OK</td>
                            </tr>
                        </table>
                    <hr/>

                    <h4>RECEIVED DATA DETAILS</h4>
                        <div style="height:115px; padding:0px">
                            <div style="float:left; width:50%; height:inherit;">
                                <table style="font-size:10">
                                    <tr>
                                        <td width="50px"></td>
                                        <td width="100"><h4>TS PO:</h4></td>
                                        <td>'.$ts.'</td>
                                    </tr>
                                    <tr>
                                        <td width="50px"></td>
                                        <td><h4>CN PO:</h4></td>
                                        <td>'.$cn.'</td>
                                    </tr>
                                </table>
                            </div>

                            <div style="float:left; width:50%; height:inherit;">
                                <table style="font-size:10">
                                    <tr>
                                        <td width="200px"><strong>NORMAL PO</strong></td>
                                        <td>'.$request->normalpo.'</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PO FOR RS</strong></td>
                                        <td>'.$request->poforrs.'</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NEW PRODUCT</strong></td>
                                        <td>'.$request->newprod.'</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid">
                                        <td><strong>RS GENERATED</strong></td>
                                        <td>'.$request->rsgen.'</td>
                                    </tr>
                                    <tr>
                                        <td><strong>FOR ORDER ENTRY ('.Auth::user()->productline.')</strong></td>
                                        <td>'.$request->dataentryts.'</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <hr/>

                    <h4>LOADING DATA GENERATED</h4>
                        <div style="height:120px; padding:0px">
                            <div style="float:left; width:40%; height:inherit;">
                                <table style="font-size:10">
                                    <tr>
                                        <td width="50px"></td>
                                        <td width="250px"></td>
                                        <td width="100px" align="right"><strong>PARTS</strong></td>
                                        <td width="100px"></td>
                                        <td width="100px" align="right"><strong>PRODUCT</strong></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td ><strong>ITEM NAME MASTER</strong></td>
                                        <td align="right">'.$request->itemnameparts.'</td>
                                        <td></td>
                                        <td align="right">'.$request->itemnameprod.'</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><strong>ITEM MASTER</strong></td>
                                        <td align="right">'.$request->itemmasterparts.'</td>
                                        <td></td>
                                        <td align="right">'.$request->itemmasterprod.'</td>
                                    </tr>
                                </table>
                            </div>
                            <div style="float:left; width:10%; height:inherit;"></div>
                            <div style="float:left; width:50%; height:inherit;"">
                                <table style="font-size:10">
                                    <tr>
                                        <td width="230px"><strong>BOM MASTER</strong></td>
                                        <td align="right">'.$request->bom.'</td>
                                    </tr>
                                    <tr>
                                        <td><strong>UNIT PRICE MASTER</strong></td>
                                        <td align="right">'.$request->unitprice.'</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PRICE MASTER</strong></td>
                                        <td align="right">'.$request->price.'</td>
                                    </tr>';

                                    if (Auth::user()->productline == 'TS') {
                                         $html2 = '<tr>
                                            <td><strong>ORDER ENTRY TS</strong></td>
                                            <td align="right">'.$request->orderts.'</td>
                                        </tr>';
                                    }

                                    if (Auth::user()->productline == 'CN') {
                                         $html2 = '<tr>
                                            <td><strong>ORDER ENTRY CN</strong></td>
                                            <td align="right">'.$request->orderts.'</td>
                                        </tr>';
                                    }

                                    if (Auth::user()->productline == 'YF') {
                                         $html2 = '<tr>
                                            <td><strong>ORDER ENTRY YF</strong></td>
                                            <td align="right">0</td>
                                        </tr>';
                                    }

                                $html3 = '</table>
                            </div>
                        </div>
                    <hr/>

                    <p align="right">
                        <strong>DATE CREATED:</strong> '.$dt->format('l jS \\of F Y h:i:s A').'
                    </p>
                </div>';
        $html = $html1.$html2.$html3;

        PDF::loadHTML($html)->setPaper('A4')
            ->setOrientation('landscape')
            ->setOption('margin-bottom', 0)
            ->setOption('margin-top', 20)
            ->save($path.'/OrderDataGenerated_'.$date.'.pdf');

        $filename = 'Order_Data_Check_'.$date.'.zip';
        $zippath = storage_path().'/Order_Data_Check';

        # download the zip file
        return $this->downloadZip($zippath, $filename);
    }

    public function postNewProductPDF($newProd,$newPcode,$newDevName,$newAAdraw,$newCust,$newCustName,$newQTY,$db)
    {
        try {
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/NewProductVerification/';
            //$path = storage_path().'/images/article/imagegallery';
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $cnt = 1;
            foreach ($newProd as $key => $newProdval) {
                //$custname = mb_convert_encoding($newCustName[$key],'UTF-8','SJIS');
                $custs = $this->getCustName($newCust[$key],$db);
                $custname = (isset($custs[0]->CNAME) === TRUE ? $custs[0]->CNAME : "");
                //$pdf = App::make('snappy.pdf.wrapper');
                // $dompdf = new Dompdf();
                // $dompdf->loadHtml(
                $html = '<style type="text/css">
                                        .table {
                                            border-collapse: collapse;
                                            font-size:10px;
                                        }
                                        .table, .th, .td {
                                            border: 1px solid black;
                                        }
                                        .box{
                                            width: 25px;
                                            padding: 10px;
                                            border: 1px solid;
                                        }
                                        .b1 {
                                            height: 5px;
                                            margin-left:15px;
                                        }
                                        .b2 {
                                            height: 7px;
                                            margin-left:15px;
                                        }
                                    </style>

                                    <table width="730px" class="table">
                                        <tr>
                                            <td colspan="6"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" align="center" class="td">
                                                <h2>New Product Verification Checksheet (rev.1)</h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" align="center" class="td">
                                                <strong><small>PPC</small></strong>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td rowspan="2" class="td" width="90px" align="center"><strong>Device Name</strong></td>
                                            <td rowspan="2" class="td" width="200px">
                                                <small>'.$newPcode[$key].'</small><br>
                                                <strong>'.$newDevName[$key].'</strong>
                                            </td>
                                            <td style="font-size:9" width="90px" class="td" align="center"><small>Product Schedule</small></td>
                                            <td style="font-size:8" width="20px" class="td" align="center"><small>NewProduct</small></td>
                                            <td style="font-size:8" width="20px" class="td" align="center"><small>NewDevice</small></td>
                                            <td style="font-size:8" width="20px" class="td" align="center"><small>NewSeries</small></td>
                                        </tr>
                                        <tr>
                                            <td class="td"></td>
                                            <td class="td">
                                                <div class="box b1"></div>
                                            </td>
                                            <td class="td">
                                                <div class="box b1"></div>
                                            </td>
                                            <td class="td">
                                                <div class="box b1"></div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="td" width="90px" align="center"><strong>AA Drawing</strong></td>
                                            <td class="td" width="200px">
                                                <strong>'.$newAAdraw[$key].'</strong>
                                            </td>
                                            <td class="td" width="90px" align="center"><strong>Customer</strong></td>
                                            <td class="td" colspan="3" width="200px">
                                                <small>'.$newCust[$key].'</small><br>
                                                <strong>'.$custname.'</strong>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="td" width="90px" align="center"><strong>PO No.</strong></td>
                                            <td width="200px">
                                                <strong>'.$newProdval.'</strong>
                                            </td>
                                            <td class="td" width="90px" align="center"><strong>Data Accomplished</strong></td>
                                            <td class="td" colspan="3" width="200px"></td>
                                        </tr>

                                        <tr>
                                            <td class="td" width="90px" align="center"><strong>PO Quantity</strong></td>
                                            <td class="td" width="200px" align="center">
                                                <strong>'.floatval($newQTY[$key]).'</strong>
                                            </td>
                                            <td class="td" width="90px" align="center"><strong>Checked / Reviewd By</strong></td>
                                            <td class="td" colspan="3" width="200px"></td>
                                        </tr>
                                    </table>

                                    <table width="730px" class="table">
                                        <tr>
                                            <td colspan="4" align="center" class="td">
                                                <strong><small>MD - ENGINEERING</small></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="center" width="300px">Check Point</td>
                                            <td class="td" align="center" width="70px">YES</td>
                                            <td class="td" align="center" width="70px">NO</td>
                                            <td class="td" align="center" width="240px">Remarks</td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left">
                                                <strong>A.) Material List Checking</strong><br>
                                                <p>1.) Parts name talies with product drawing parts list</p>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>2.) Parts quality talies with product drawing parts list</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>3.) Qty per parts usage talies with product drawing parts list</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left">
                                                <strong>B.) Process Capability Check</strong><br>
                                                <p>1.) Does PMI has technical capability to perform the process</p>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>2.) Does PMI has necessary capability to perform the process</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left">
                                                <strong>C.) Assembly Documents</strong><br>
                                                <p>1.) Is Assembly drawing available?</p>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>2.) Is Assembly drawing (G-drwg) available?</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>3.) Is Inspection Guide (IG doc.) available?</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>4.) Is Packing Manual available?</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>5.) Any special Instruction received? TDI, etc.</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left">
                                                <strong>D.)Assemlby Tools and Jigs availability</strong><br>
                                                <p>1.) Insertion toll / Die set</p>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>2.) Ukedai / Pedestal</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>3.) Terminal Gauge</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>4.) Test Jig (if necessary)</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                        <tr>
                                            <td class="td" align="left"><p>5.) Others</p></td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center">
                                                <div class="box b2"></div>
                                            </td>
                                            <td class="td" align="center" ></td>
                                        </tr>
                                    </table>

                                    <table width="730px" class="table">
                                        <tr>
                                            <td colspan="3" class="td">
                                                <strong>RECOMMENDATION (Please incircle the recommended action)</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>1 - Can proceed eith mass production</td>
                                            <td colspan="2">2 - Request document / special instruction from YEC</td>
                                        </tr>
                                        <tr>
                                            <td>3 - Request Tools / Jigs from YEC</td>
                                            <td>4 - Acquire Tools / Jigs from Local Supplier</td>
                                            <td>5 - Other</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <em><strong>Comments for 2, 3, 4, 5 ...</strong></em>
                                            </td>
                                        </tr>
                                    </table>';
                                    header('content-type: text/html; charset=utf-8');
                //$dompdf->setPaper('letter', 'portrait');
                //$dompdf->render($path.'/NewProduct'.$cnt.'_'.$newProdval.'_'.$date.'-'.$time.'.pdf');
                //$dompdf->stream($path.'/NewProduct'.$cnt.'_'.$newProdval.'_'.$date.'-'.$time.'.pdf');
                PDF::loadHTML($html)->setPaper('letter')->setOrientation('portrait')->setOption('margin-bottom', 0)->save($path.'/NewProduct'.$cnt.'_'.$newProdval.'_'.$date.'-'.$time.'.pdf');
                $cnt++;
             }

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function generateTSOrderEntry($allProd,$allPcode,$allCust,$allQTY,$allBuyers,$allcDate,$jdate)
    {
        try {
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/';
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }


            Excel::create('TXRECEPLAN', function($excel) use($allProd,$allPcode,$allCust,$allQTY,$allBuyers,$allcDate,$jdate){
                $excel->sheet('LOAD_ORDER_ENTRY', function($sheet) use($allProd,$allPcode,$allCust,$allQTY,$allBuyers,$allcDate,$jdate){
                    $sheet->cell('A1', "SORDER");
                    $sheet->cell('B1', "EDA");
                    $sheet->cell('C1', "CODE");
                    $sheet->cell('D1', "CUST");
                    $sheet->cell('E1', "STATUS");
                    $sheet->cell('F1', "SLEVEL");
                    $sheet->cell('G1', "CDATE");
                    $sheet->cell('H1', "CVOL");
                    $sheet->cell('I1', "KVOL");
                    $sheet->cell('J1', "JDATE");
                    $sheet->cell('K1', "CONT");
                    $sheet->cell('L1', "BUYERS_CODE");

                    $row = 2;
                    //sort($allProd);
                    foreach ($allProd as $key => $allProdval) {
                        $sheet->cell('A'.$row, $allProdval);
                        $sheet->cell('B'.$row, "0.0");
                        $sheet->cell('C'.$row, $allPcode[$key]);
                        $sheet->cell('D'.$row, $allCust[$key]);
                        $sheet->cell('E'.$row, "0.0");
                        $sheet->cell('F'.$row, "0.0");
                        $sheet->cell('G'.$row, "20".$allcDate[$key]);
                        $sheet->cell('H'.$row, $allQTY[$key]);
                        $sheet->cell('I'.$row, $allQTY[$key]);//"0.0"
                        $sheet->cell('J'.$row, "20".$jdate[$key]);
                        $sheet->cell('K'.$row, "");
                        $sheet->cell('L'.$row, $allBuyers[$key]);
                        $row++;
                    }
                });
            })->store('xls',$path);

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }

    }

    public function generateTSPriceMaster($priceProd,$pricePcode,$priceDevName,$priceCust,$custName,$pricePrice,$UnitCode,$UnitPrice,$UnitName,$db)
    {
        try {
            $this->truncateTable('price_report');
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $tdate = date('Ymd');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/';
            //$path = storage_path().'\Order_Data_Check\TS_Price_Master_'.$date.'-'.$time;
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // foreach ($pricePcode as $key => $priceProdval) {
            //     $price = $this->getPriceMaster($priceProdval);
            // }

            Excel::create('TXBAIK', function($excel) use($priceProd,$pricePcode,$priceDevName,$priceCust,$custName,$pricePrice,$UnitCode,$UnitPrice,$UnitName,$tdate,$db) {
                $excel->sheet('LOAD_PRICE_MASTER', function($sheet) use($priceProd,$pricePcode,$priceDevName,$priceCust,$custName,$pricePrice,$UnitCode,$UnitPrice,$UnitName,$tdate,$db) {
                    // $sheet->cell('A1', "DataClass");
                    $sheet->cell('A1', "BID");
                    $sheet->cell('B1', "CODE");
                    $sheet->cell('C1', "NAME");
                    $sheet->cell('D1', "BUNR");
                    $sheet->cell('E1', "TANI");
                    $sheet->cell('F1', "CCODE");
                    $sheet->cell('G1', "CUST");
                    $sheet->cell('H1', "CUSTNAME");
                    $sheet->cell('I1', "TDATE");
                    $sheet->cell('J1', "EDATE");
                    $sheet->cell('K1', "TVOL");
                    $sheet->cell('L1', "PRICE");
                    $sheet->cell('M1', "APRICE");
                    $sheet->cell('N1', "CURRE");
                    $sheet->cell('O1', "SOUSUUOUT");
                    $sheet->cell('P1', "SOUOUTKINGAKU");
                    $sheet->cell('Q1', "FDATE");
                    $sheet->cell('R1', "HOKAN");
                    $sheet->cell('S1', "HOKANNAME");

                    foreach ($UnitCode as $key => $code) {
                        if ($this->check_itemCode($code,"XPRTS",$db) > 0) {
                            array_push($pricePcode,$code."part");
                            array_push($priceDevName,$UnitName[$key]);
                            array_push($pricePrice,$UnitPrice[$key]);
                        }
                    }

                    $row = 2;
                    $cnt = 0;
                    $cols = array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T');
                    //sort($pricePcode);
                    foreach ($pricePcode as $key => $priceProdval) {
                        $bunr = "";
                        $devname = (isset($priceDevName[$key]) === TRUE ? $priceDevName[$key] : "");
                        $cust = (isset($priceCust[$key]) === TRUE ? $priceCust[$key] : "");
                        $cstname = (isset($custName[$key]) === TRUE ? $custName[$key] : "");
                        $cname = mb_convert_encoding($cstname,"UTF-8","SJIS");
                        $price = (isset($pricePrice[$key]) === TRUE ? $pricePrice[$key] : "");



                        header( 'Content-Type: text/html; charset=utf-8' );
                        $checkItem = DB::connection($this->mssql)->table('XBAIK')
                                        ->where('CODE',$priceProdval)
                                        ->where('CUST',$cust)
                                        ->count();
                        if ($checkItem > 0) {
                            # code...
                        } else {
                            DB::connection($this->mysql)->table('price_report')->insert([
                                'code' => $priceProdval,
                                'name' => $devname,
                                'cust' => $cust,
                                'custname' => $cname,
                                'price' => $price
                            ]);
                        }
                    }

                    $datas = DB::connection($this->mysql)->table('price_report')
                                ->select('code','name','cust','custname','price')
                                ->distinct()
                                ->get();
                    $lastbid = DB::connection($this->mssql)->table('XBAIK')
                                    ->select('BID')
                                    ->orderBy('BID','DESC')
                                    ->first();

                    $BID = $lastbid->BID;
                    foreach ($datas as $key => $data) {
                        $BID++;
                        if (strpos($data->name, 'CT') !== FALSE) {
                            $bunr = "P PRODUCT";
                        } else {
                            $bunr = "TAB";
                        }

                        $sheet->cell('A'.$row, $BID);

                        if (strpos($data->code, 'part') !== false) {
                            foreach ($cols as $key => $col) {
                                $sheet->getStyle($col.$row)->applyFromArray(array(
                                    'fill' => array(
                                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'db4141')
                                    )
                                ));
                            }

                            $sheet->cell('B'.$row, substr($data->code,0,-4));
                        } else {
                            $sheet->cell('B'.$row, $data->code);

                        }

                        $sheet->cell('C'.$row, $data->name);
                        $sheet->cell('D'.$row, $bunr);
                        $sheet->cell('E'.$row, "pc.");
                        $sheet->cell('F'.$row, "");
                        $sheet->cell('G'.$row, $data->cust);
                        $sheet->cell('H'.$row, $data->custname);
                        $sheet->cell('I'.$row, $tdate);
                        $sheet->cell('J'.$row, "99999999");
                        $sheet->cell('K'.$row, "0.0");
                        $sheet->cell('L'.$row, $data->price);
                        $sheet->cell('M'.$row, $data->price);
                        $sheet->cell('N'.$row, "US$");
                        $sheet->cell('O'.$row, "0.0");
                        $sheet->cell('P'.$row, "0.0");
                        $sheet->cell('Q'.$row, "");
                        $sheet->cell('R'.$row, "WHS101");
                        $sheet->cell('S'.$row, "FGS");
                        $row++;
                        $cnt++;
                    }

                    $this->_priceCount = $cnt;
                });
            })->store('xls',$path);

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function generateTSBOM($code,$name,$kcode,$partsname,$usage,$div_usage)
    {
        try {
            $this->truncateTable('bom_report');
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/';
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }


            Excel::create('TXPRTS', function($excel) use($code,$name,$kcode,$partsname,$usage,$div_usage){
                $excel->sheet('LOAD_BOM', function($sheet) use($code,$name,$kcode,$partsname,$usage,$div_usage){
                    // $sheet->cell('A1', "DataClass");
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "OYANAME");
                    $sheet->cell('C1', "REV");
                    $sheet->cell('D1', "KCODE");
                    $sheet->cell('E1', "KONAME");
                    $sheet->cell('F1', "EDA");
                    // $sheet->cell('H1', "BUMO");
                    // $sheet->cell('I1', "BUMONAME");
                    $sheet->cell('G1', "OPT");
                    $sheet->cell('H1', "SIYOU");
                    $sheet->cell('I1', "SIYOUW");
                    $sheet->cell('J1', "SDATE");
                    $sheet->cell('K1', "EDATE");
                    $sheet->cell('L1', "NOKANRI");
                    $sheet->cell('M1', "CONT");
                    $sheet->cell('N1', "INPUTDATE");
                    $sheet->cell('O1', "INPUTUSER");


                    $row = 2;
                    $cnt = 0;
                    foreach ($code as $key => $icode) {
                        $pname = [];
                        $prodname = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('name','code')->where('code',$icode)->get();
                        foreach ($prodname as $key => $prd) {
                            $pname[$icode] = $prd->name;
                        }
                        header( 'Content-Type: text/html; charset=utf-8' );
                        DB::connection($this->mysql)->table('bom_report')->insert([
                            'code' => $icode,
                            'name' => $pname[$icode],
                            'kcode' => $kcode[$cnt],
                            'partsname' => $partsname[$cnt],
                            'usage' => $usage[$cnt],
                            'div_usage' => $div_usage[$cnt]
                        ]);
                        $cnt++;
                    }

                    $boms = DB::connection($this->mysql)->table('bom_report')
                                ->select('code','name','kcode','partsname','usage','div_usage')
                                ->distinct()
                                ->get();
                    $cnt = 0;
                    foreach ($boms as $key => $bom) {
                        // $sheet->cell('A'.$row, "");
                        $sheet->cell('A'.$row, $bom->code);
                        $sheet->cell('B'.$row, $bom->name);
                        $sheet->cell('C'.$row, "");
                        $sheet->cell('D'.$row, $bom->kcode);
                        $sheet->cell('E'.$row, $bom->partsname);
                        $sheet->cell('F'.$row, "");
                        // $sheet->cell('H'.$row, "");
                        // $sheet->cell('I'.$row, "");
                        $sheet->cell('G'.$row, "");
                        $sheet->cell('H'.$row, $bom->usage);
                        $sheet->cell('I'.$row, $bom->div_usage);
                        $sheet->cell('J'.$row, "");
                        $sheet->cell('K'.$row, "");
                        $sheet->cell('L'.$row, "");
                        $sheet->cell('M'.$row, "");
                        $sheet->cell('N'.$row, "");
                        $sheet->cell('O'.$row, "");
                        $row++;
                        $cnt++;
                    }
                    $this->_BOMCount = $cnt;

                });
            })->store('xls',$path);

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function generateItemNameProd($itemCode,$itemName,$db)
    {
        try {
            $this->truncateTable('itemnameprod_report');
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/';
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            Excel::create('TXHEAD_PRD', function($excel) use($itemCode,$itemName,$db){
                $excel->sheet('LOAD_ITEM_NAME_MASTER_PROT_', function($sheet) use($itemCode,$itemName,$db){
                    // $sheet->cell('A1', "DataClass");
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "MAINBUMO");
                    $sheet->cell('D1', "MAINBUMONAME");
                    $sheet->cell('E1', "DOFUKUSUU");
                    $sheet->cell('F1', "DOSEIBAN");
                    $sheet->cell('G1', "SEIBHKU");
                    $sheet->cell('H1', "MANUKHU");
                    $sheet->cell('I1', "DORIREKIOYA");
                    $sheet->cell('J1', "DORIREKIKO");
                    $sheet->cell('K1', "DOLOT");
                    $sheet->cell('L1', "FMRPGROUP");
                    $sheet->cell('M1', "DOFMRP");
                    $sheet->cell('N1', "PROJONLY");
                    $sheet->cell('O1', "TANI1");
                    $sheet->cell('P1', "OYAK");
                    $sheet->cell('Q1', "BIKOU");

                    $row = 2;
                    //sort($itemCode);
                    foreach ($itemCode as $key => $code) {
                        DB::connection($this->mysql)->table('itemnameprod_report')->insert([
                            'code' => $code,
                            'name' => $itemName[$key],
                            'bumoname' => $this->getBumoname($db,'ASSY100')[0]->BNAME
                        ]);
                    }

                    $itemnameprods = DB::connection($this->mysql)->table('itemnameprod_report')
                                        ->select('code','name','bumoname')
                                        ->distinct()
                                        ->get();
                    $cnt = 0;
                    foreach ($itemnameprods as $key => $inprod) {
                        // $sheet->cell('A'.$row, "");
                        $sheet->cell('A'.$row, $inprod->code);
                        $sheet->cell('B'.$row, $inprod->name);
                        $sheet->cell('C'.$row, "ASSY100");
                        $sheet->cell('D'.$row, $inprod->bumoname);
                        $sheet->cell('E'.$row, "0.0");
                        $sheet->cell('F'.$row, "2");
                        $sheet->cell('G'.$row, "1");
                        $sheet->cell('H'.$row, "1");
                        $sheet->cell('I'.$row, "0.0");
                        $sheet->cell('J'.$row, "0.0");
                        $sheet->cell('K'.$row, "0.0");
                        $sheet->cell('L'.$row, "");
                        $sheet->cell('M'.$row, "0.0");
                        $sheet->cell('N'.$row, "0.0");
                        $sheet->cell('O'.$row, "pc.");
                        $sheet->cell('P'.$row, "0.0");
                        $sheet->cell('Q'.$row, "");
                        $row++;
                        $cnt++;
                    }
                    $this->_ItemNameProdCount = $cnt;

                });
            })->store('xls',$path);

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function generateItemProd($itemCode,$itemName,$prodDrawnum,$db)
    {
        try {
            $this->truncateTable('itemprod_report');
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/';
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }


            // foreach ($itemCode as $key => $value) {
            //     $query = DB::connection($this->mssql)->table('XHEAD')->where('CODE','=',$value)->get();
            //     echo "<pre>",print_r($query),"</pre>";
            // }

            Excel::create('TXITEM_PRD', function($excel) use($itemCode,$itemName,$prodDrawnum,$db){
                $excel->sheet('LOAD_ITEM_MASTER_PROT_', function($sheet) use($itemCode,$itemName,$prodDrawnum,$db){
                    // $sheet->cell('A1', "DataClass");
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "BUNR");
                    $sheet->cell('D1', "BUMO");
                    $sheet->cell('E1', "BUMONAME");
                    $sheet->cell('F1', "VENDOR");
                    $sheet->cell('G1', "VENDORNAME");
                    $sheet->cell('H1', "FIXLEVEL");
                    $sheet->cell('I1', "DKAKU");
                    $sheet->cell('J1', "KAKU");
                    $sheet->cell('K1', "STZAIK");
                    $sheet->cell('L1', "LEAD");
                    $sheet->cell('M1', "KOUKI");
                    $sheet->cell('N1', "HOJYUU");
                    $sheet->cell('O1', "KURIAGE");
                    $sheet->cell('P1', "HOKAN");
                    $sheet->cell('Q1', "HOKANNAME");
                    $sheet->cell('R1', "PICKKU");
                    $sheet->cell('S1', "DRAWING_NUM");

                    $row = 2;
                    //sort($itemCode);
                    foreach ($itemCode as $key => $code) {
                        DB::connection($this->mysql)->table('itemprod_report')->insert([
                            'code' => $code,
                            'name' => $itemName[$key],
                            'bumoname' => $this->getBumoname($db,'ASSY100')[0]->BNAME,
                            'vendorname' => $this->getBumoname($db,'ASSY100')[0]->BNAME,
                            'dn_num' => $prodDrawnum[$key]
                        ]);
                    }

                    $itemprods = DB::connection($this->mysql)->table('itemprod_report')
                                    ->select('code','name','bumoname','vendorname','dn_num')
                                    ->distinct()
                                    ->get();
                    $cnt = 0;
                    foreach ($itemprods as $key => $iprod) {
                        // $sheet->cell('A'.$row, "");
                        $sheet->cell('A'.$row, $iprod->code);
                        $sheet->cell('B'.$row, $iprod->name);
                        $sheet->cell('C'.$row, "");
                        $sheet->cell('D'.$row, "ASSY100");
                        $sheet->cell('E'.$row, $iprod->bumoname);
                        $sheet->cell('F'.$row, "ASSY100");
                        $sheet->cell('G'.$row, $iprod->vendorname);
                        $sheet->cell('H'.$row, "1");
                        $sheet->cell('I'.$row, "2");
                        $sheet->cell('J'.$row, "2");
                        $sheet->cell('K'.$row, "0.0");
                        $sheet->cell('L'.$row, "1");
                        $sheet->cell('M'.$row, "0.0");
                        $sheet->cell('N'.$row, "1");
                        $sheet->cell('O'.$row, "1");
                        $sheet->cell('P'.$row, "WHS101");
                        $sheet->cell('Q'.$row, "FGS");
                        $sheet->cell('R'.$row, "1");
                        $sheet->cell('S'.$row, $iprod->dn_num);
                        $row++;
                        $cnt++;
                    }
                    $this->_ItemProdCount = $cnt;
                });
            })->store('xls',$path);

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function generateItemNameParts($itemCodeParts,$itemNameParts)
    {
        try {
            $this->truncateTable('itemnamepart_report');
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/';
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }


            Excel::create('TXHEAD_PRT', function($excel) use($itemCodeParts,$itemNameParts){
                $excel->sheet('LOAD_ITEM_NAME_MASTER_PARTS', function($sheet) use($itemCodeParts,$itemNameParts){
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "MAINBUMO");
                    $sheet->cell('D1', "MAINBUMONAME");
                    $sheet->cell('E1', "DOFUKUSUU");
                    $sheet->cell('F1', "DOSEIBAN");
                    $sheet->cell('G1', "SEIBHKU");
                    $sheet->cell('H1', "MANUKHU");
                    $sheet->cell('I1', "TANI1");
                    $sheet->cell('J1', "OYAK");
                    $sheet->cell('K1', "BIKOU");

                    $row = 2;
                    //sort($itemCodeParts);
                    foreach ($itemCodeParts as $key => $code) {
                        DB::connection($this->mysql)->table('itemnamepart_report')->insert([
                            'code' => $code,
                            'name' => $itemNameParts[$key]
                        ]);
                    }

                    $inameparts = DB::connection($this->mysql)->table('itemnamepart_report')
                                    ->select('code','name')
                                    ->distinct()
                                    ->get();
                    $cnt = 0;
                    foreach ($inameparts as $key => $impart) {
                        $sheet->cell('A'.$row, $impart->code);
                        $sheet->cell('B'.$row, $impart->name);
                        $sheet->cell('C'.$row, "PURH100");
                        $sheet->cell('D'.$row, "ISCD - PURCHASING");
                        $sheet->cell('E'.$row, "0.0");
                        $sheet->cell('F'.$row, "0.0");
                        $sheet->cell('G'.$row, "0.0");
                        $sheet->cell('H'.$row, "1");
                        $sheet->cell('I'.$row, "pc.");
                        $sheet->cell('J'.$row, "255");
                        $sheet->cell('K'.$row, "");
                        $row++;
                        $cnt++;
                    }
                    $this->_ItemNamePartCount = $cnt;


                });
            })->store('xls',$path);

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function generateItemParts($itemCodeParts,$itemNameParts,$partDrawnum)
    {
        try {
            $this->truncateTable('itempart_report');
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/';
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }



            Excel::create('TXITEM_PRT', function($excel) use($itemCodeParts,$itemNameParts,$partDrawnum){
                $excel->sheet('LOAD_ITEM_MASTER_PARTS', function($sheet) use($itemCodeParts,$itemNameParts,$partDrawnum){
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "BUNR");
                    $sheet->cell('D1', "BUMO");
                    $sheet->cell('E1', "VENDOR");
                    $sheet->cell('F1', "FIXLEVEL");
                    $sheet->cell('G1', "DKAKU");
                    $sheet->cell('H1', "KAKU");
                    $sheet->cell('I1', "LEAD");
                    $sheet->cell('J1', "HOKAN");
                    $sheet->cell('K1', "LOTH");
                    $sheet->cell('L1', "HIMOKI");
                    $sheet->cell('M1', "PICKKU");
                    $sheet->cell('N1', "DRAWING_NUM");

                    $row = 2;
                    //sort($itemCodeParts);
                    foreach ($itemCodeParts as $key => $code) {
                        // $bunr = "";
                        // if (preg_match('/CT/',$itemNameParts[$key])) {
                        //     $bunr = "PROBE";
                        // }
                        DB::connection($this->mysql)->table('itempart_report')->insert([
                            'code' => $code,
                            'name' => $itemNameParts[$key],
                            'vendor' => $this->getVendor($code)[0]->vendor,
                            'dn_num' => $partDrawnum[$key]
                        ]);
                    }

                    $iparts = DB::connection($this->mysql)->table('itempart_report')
                                ->select('code','name','vendor','dn_num')
                                ->distinct()
                                ->get();
                    $cnt = 0;
                    foreach ($iparts as $key => $ipart) {
                        $sheet->cell('A'.$row, $ipart->code);
                        $sheet->cell('B'.$row, $ipart->name);
                        $sheet->cell('C'.$row, "");
                        $sheet->cell('D'.$row, "PURH100");
                        $sheet->cell('E'.$row, $ipart->vendor);
                        $sheet->cell('F'.$row, "0.0");
                        $sheet->cell('G'.$row, "90");
                        $sheet->cell('H'.$row, "90");
                        $sheet->cell('I'.$row, "7");
                        $sheet->cell('J'.$row, "WHS100");
                        $sheet->cell('K'.$row, "0.0");
                        $sheet->cell('L'.$row, "10");
                        $sheet->cell('M'.$row, "2");
                        $sheet->cell('N'.$row, $ipart->dn_num);
                        $row++;
                        $cnt++;
                    }
                    $this->_ItemPartCount = $cnt;
                });
            })->store('xls',$path);

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function generateUnitPrice($UnitCode,$vendor,$UnitPrice,$name,$db)
    {
        try {
            $this->truncateTable('unitprice_report');
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');
            $time = $dt->format('his');

            $dbcon = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->select('con')->first();
            $con = $this->com->getr3Connection($dbcon->con);

            $path = storage_path().'/Order_Data_Check/'.$con.'/'.$date.'/';
            File::makeDirectory($path, 0777, true, true);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            //echo "<pre>",print_r($UnitPrice),"</pre>";
            Excel::create('TXTANK', function($excel) use($UnitCode,$vendor,$UnitPrice,$name,$date,$db){
                $excel->sheet('LOAD_UNIT_PRICE_MASTER_', function($sheet) use($UnitCode,$vendor,$UnitPrice,$name,$date,$db){
                    // $sheet->cell('A1', "DataClass");
                    $sheet->cell('A1', "TID");
                    $sheet->cell('B1', "CODE");
                    $sheet->cell('C1', "NAME");
                    $sheet->cell('D1', "TANI");
                    $sheet->cell('E1', "VCODE");
                    $sheet->cell('F1', "BUNR");
                    $sheet->cell('G1', "VENDOR");
                    $sheet->cell('H1', "VENDORNAME");
                    $sheet->cell('I1', "TDATE");
                    $sheet->cell('J1', "EDATE");
                    $sheet->cell('K1', "TVOL");
                    $sheet->cell('L1', "TEMA");
                    $sheet->cell('M1', "SPRICE");
                    $sheet->cell('N1', "PRICE");
                    $sheet->cell('O1', "CURRE");
                    $sheet->cell('P1', "SOUKINGAKU");
                    $sheet->cell('Q1', "SOUSUUIN");
                    $sheet->cell('R1', "APRICE");
                    $sheet->cell('S1', "HOKAN");
                    $sheet->cell('T1', "HOKANNAME");
                    $sheet->cell('U1', "INPUTDATE");
                    $sheet->cell('V1', "INPUTUSER");

                    $row = 2;
                    //sort($UnitCode);

                    foreach ($UnitCode as $key => $code) {
                        if ($this->check_itemCode($code,"XPRTS",$db) > 0) {

                        } else {
                            $price = DB::connection($this->mysql)->table('tbl_orderdatacheck1')
                                        ->select('kcode','unit')->where('kcode',$code)->get();
                            DB::connection($this->mysql)->table('unitprice_report')->insert([
                                'code' => $code,
                                'name' => $name[$key],
                                'vendor' => (isset($vendor[$key]) === TRUE ? $vendor[$key] : ""),
                                'sprice' => $price[0]->unit,
                                'aprice' => $price[0]->unit
                            ]);
                        }
                    }

                    $units = DB::connection($this->mysql)->table('unitprice_report')
                                ->select('code','name','vendor','sprice','aprice')
                                ->distinct()
                                ->get();

                    $txtank = DB::connection($this->mssql)->table('XTANK')
                                ->select('TID')
                                ->orderBy('TID','desc')
                                ->first();

                    $tid = $txtank->TID;
                    $cnt = 0;
                    foreach ($units as $key => $unit) {
                        $cnt++;
                        $tid++;
                        // $sheet->cell('A'.$row, "");
                        $sheet->cell('A'.$row, $tid);
                        $sheet->cell('B'.$row, $unit->code);
                        $sheet->cell('C'.$row, $unit->name);
                        $sheet->cell('D'.$row, "");
                        $sheet->cell('E'.$row, $unit->code);
                        $sheet->cell('F'.$row, $this->getBUNR($unit->code));
                        $sheet->cell('G'.$row, $unit->vendor);
                        $sheet->cell('H'.$row, "");
                        $sheet->cell('I'.$row, date('Ymd'));
                        $sheet->cell('J'.$row, "99999999");
                        $sheet->cell('K'.$row, "0.0");
                        $sheet->cell('L'.$row, "0.0");
                        $sheet->cell('M'.$row, $unit->sprice);
                        $sheet->cell('N'.$row, $unit->aprice);
                        $sheet->cell('O'.$row, "US$");
                        $sheet->cell('P'.$row, "0.0");
                        $sheet->cell('Q'.$row, "0.0");
                        $sheet->cell('R'.$row, "0.0");
                        $sheet->cell('S'.$row, "");
                        $sheet->cell('T'.$row, "");
                        $sheet->cell('U'.$row, "");
                        $sheet->cell('V'.$row, "");
                        $row++;
                    }
                    $this->_UnitCount = $cnt;

                });
            })->store('xls',$path);

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function getBUNR($code)
    {
        $db = DB::connection($this->mssql)->table('XITEM')
                    ->select('BUNR')
                    ->where('CODE',$code)
                    ->first();
        if (count((array)$db) > 0) {
            return $db->BUNR;
        } else {
            return "";
        }
    }

    private function InsertToTempTable1($partsPO,$partsName,$partsDivUsage,$partsCode,$partsUsage,$partsQTY,$partsVendor,$partsDnum,$jdate,$partsUnit)
    {
        DB::connection($this->mysql)->table('tbl_orderdatacheck1')->truncate();
        foreach ($partsPO as $key => $po) {
            // DB::statement('SET NAMES utf8');
            // header( 'Content-Type: text/html; charset=utf-8');
            DB::connection($this->mysql)->table('tbl_orderdatacheck1')->insertGetId([
                'po' => $po,
                'partsname' =>  mb_convert_encoding($partsName[$key],"UTF-8","SJIS"),
                'div_usage' => $partsDivUsage[$key],
                'kcode' => $partsCode[$key],
                'usage' => $partsUsage[$key],
                'qty' => $partsQTY[$key],
                'vendor' => $partsVendor[$key],
                'drawing_num' =>  mb_convert_encoding($partsDnum[$key],"UTF-8","SJIS"),
                'jdate' => $jdate[$key],
                'unit' => $partsUnit[$key]
            ]);
        }

    }

    private function InsertToTempTable2($ProdPO,$ProdName,$ProdCode,$qty,$date,$ProdCust,$custName,$ProdPrice,$ProdDnum,$buyers,$con)
    {
        DB::connection($this->mysql)->table('tbl_orderdatacheck2')->truncate();
        foreach ($ProdPO as $key => $po) {
            // DB::statement('SET NAMES utf8');
            // header( 'Content-Type: text/html; charset=utf-8' );
            DB::connection($this->mysql)->table('tbl_orderdatacheck2')->insertGetId([
                'po' => $po,
                'name' => mb_convert_encoding($ProdName[$key],"UTF-8","SJIS"),
                'code' => $ProdCode[$key],
                'qty' => $qty[$key],
                'cdate' => $date[$key],
                'cust' => $ProdCust[$key],
                'custname' => mb_convert_encoding($custName[$key],"UTF-8","SJIS"),
                'price' => $ProdPrice[$key],
                'drawing_num' => mb_convert_encoding($ProdDnum[$key],"UTF-8","SJIS"),
                'buyers' => $buyers[$key],
                'con' => $con[$key]
            ]);
        }
    }

    /////
    private function getCustName($code,$db)
    {
        $custname = DB::connection($this->mssql)
                    ->table('XCUST')
                    ->select('CNAME')
                    ->where('CUST',$code)
                    ->get();
        return $custname;
    }

    private function getBumoname($db,$bumo)
    {
        $bumoname = DB::connection($this->mssql)
                        ->table('XSECT')
                        ->select('BUMO','BNAME')
                        ->where('BUMO',$bumo)
                        ->get();
        return $bumoname;
    }

    private function getVendor($kcode)
    {
        $vendor = DB::connection($this->mysql)->table('tbl_orderdatacheck1')
                    ->select('kcode','vendor')
                    ->where('kcode',$kcode)
                    ->get();
        return $vendor;
    }

    private function ItemName_Master($db)
    {
        $prod = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->get();
        $part = DB::connection($this->mysql)->table('tbl_orderdatacheck1')->get();

        $existprod = 0;
        $nonexistprod = 0;
        $existpart = 0;
        $nonexistpart = 0;
        $CodeProdNonExist = [];
        $CodePartNonExist = [];
        $NameProdNonExist = [];
        $NamePartNonExist = [];
        foreach ($prod as $key => $itemprod) {
            if ($this->check_itemCode($itemprod->code,"XHEAD",$db) > 0) {
                $existprod++;
            } else {
                $nonexistprod++;
                $CodeProdNonExist[] = $itemprod->code;
                $NameProdNonExist[] = $itemprod->name;
            }
        }

        foreach ($part as $key => $itempart) {
            if ($this->check_itemCode($itempart->kcode,"XHEAD",$db) > 0) {
                $existpart++;
            } else {
                $nonexistpart++;
                $CodePartNonExist[] = $itempart->kcode;
                $NamePartNonExist[] = $itempart->partsname;
            }
        }

        return $item = [
            'prod_exist' => $existprod,
            'prod_nonexist' => $nonexistprod,
            'CodeProdNonExist' => $CodeProdNonExist,
            'NameProdNonExist' => $NameProdNonExist,
            'part_exist' => $existpart,
            'part_nonexist' => $nonexistpart,
            'CodePartNonExist' => $CodePartNonExist,
            'NamePartNonExist' => $NamePartNonExist
        ];
    }

    private function Item_Master($db)
    {
        $prod = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->get();
        $part = DB::connection($this->mysql)->table('tbl_orderdatacheck1')->get();

        $existprod = 0;
        $nonexistprod = 0;
        $existpart = 0;
        $nonexistpart = 0;
        $CodeProdExist = [];
        $CodeProdNonExist = [];
        $CodePartNonExist = [];

        $NameProdNonExist = [];
        $NamePartNonExist = [];

        $DnumProdNonExist = [];
        $DnumPartNonExist = [];

        $VendorPartNonExist = [];

        $matchProdDN = 0;
        $CodeProdUnmatchDN = [];
        $DNProdUnmatchDN = [];

        $matchPartDN = 0;
        $CodePartUnmatchDN = [];
        $DNPartUnmatchDN = [];

        $matchProdName = 0;
        $unmatchProdName = 0;
        $CodeProdUnmatchName = [];
        $NameProdUnmatchName = [];

        $matchPartName = 0;
        $unmatchPartName = 0;
        $CodePartUnmatchName = [];
        $NamePartUnmatchName = [];

        $matchCust = 0;
        $unmatchCust = 0;
        $UnmatchVendorItemCode = [];
        $UnmatchVendor = [];
        $unmatchCon = [];

        foreach ($prod as $key => $itemprod) {
            if ($this->check_itemCode($itemprod->code,"XITEM",$db) > 0) {
                $existprod++;
                $CodeProdExist[] = $itemprod->code;
                //$prod_dn = $replaced = preg_replace('/\s+/', ' ', $itemprod->drawing_num);
                if ($this->getDN($itemprod->code,$itemprod->drawing_num,$db) > 0) {
                    $matchProdDN++;
                } else {
                    $CodeProdUnmatchDN[] = $itemprod->code;
                    $DNProdUnmatchDN[] = $itemprod->drawing_num;
                }

                if ($this->getName($itemprod->code,$itemprod->name,$db) > 0) {
                    $matchProdName++;
                } else {
                    $unmatchProdName++;
                    $CodeProdUnmatchName[] = $itemprod->code;
                    $NameProdUnmatchName[] = $itemprod->name;
                    $unmatchCon[] = $itemprod->con;
                }
            } else {
                $nonexistprod++;
                $CodeProdNonExist[] = $itemprod->code;
                $NameProdNonExist[] = $itemprod->name;
                $DnumProdNonExist[] = $itemprod->drawing_num;
            }
        }

        foreach ($part as $key => $itempart) {
            if ($this->check_itemCode($itempart->kcode,"XITEM",$db) > 0) {
                $existpart++;
                // $dn = '';
                // if (strpos($itempart->drawing_num, "....") !== false) {
                //     $dn = substr($itempart->drawing_num, 0, -4 );
                // } elseif (strpos($itempart->drawing_num, "...") !== false) {
                //     $dn = substr($itempart->drawing_num, 0, -3 );
                // } else {
                //     $dn = $itempart->drawing_num;
                // }
                //
                // $part_dns = $replaced = preg_replace('/\s+/', ' ', $dn);

                if ($this->getDN($itempart->kcode,$itempart->drawing_num,$db) > 0) {
                    $matchPartDN++;
                } else {
                    $CodePartUnmatchDN[] = $itempart->kcode;
                    $DNPartUnmatchDN[] = $itempart->drawing_num;
                }

                if ($this->getName($itempart->kcode,$itempart->partsname,$db) > 0) {
                    $matchPartName++;
                } else {
                    $unmatchPartName++;
                    $CodePartUnmatchName[] = $itempart->kcode;
                    $NamePartUnmatchName[] = $itempart->partsname;
                }

                if ($this->getSupplier($itempart->kcode,$db) > 0) {
                    $matchCust++;
                } else {
                    $unmatchCust++;
                    $UnmatchVendorItemCode[] = $itempart->kcode;
                    $UnmatchVendor[] = $itempart->vendor;
                }

            } else {
                $nonexistpart++;
                $CodePartNonExist[] = $itempart->kcode;
                $NamePartNonExist[] = $itempart->partsname;
                $DnumPartNonExist[] = $itempart->drawing_num;
            }
        }

        return $item = [
            'CodeProdExist' => $CodeProdExist,
            'prod_exist' => $existprod,
            'prod_nonexist' => $nonexistprod,
            'code_unmatch_prodDN' => $CodeProdUnmatchDN,
            'dn_unmatch_prodDN' => $DNProdUnmatchDN,
            'part_exist' => $existpart,
            'part_nonexist' => $nonexistpart,
            'code_unmatch_partDN' => $CodePartUnmatchDN,
            'dn_unmatch_partDN' => $DNPartUnmatchDN,
            'unmatch_prodName' => $unmatchProdName,
            'code_unmatch_prodName' => $CodeProdUnmatchName,
            'name_unmatch_prodName' => $NameProdUnmatchName,
            'con_unmatch_prodName' => $unmatchCon,
            'unmatch_partName' => $unmatchPartName,
            'code_unmatch_partName' => $CodePartUnmatchName,
            'name_unmatch_partName' => $NamePartUnmatchName,
            'unmatch_supplier' => $unmatchCust,
            'unmatchSupplierCode' => $UnmatchVendorItemCode,
            'unmatchSupplier' => $UnmatchVendor,
            'CodeProdNonExist' => $CodeProdNonExist,
            'CodePartNonExist' => $CodePartNonExist,
            'NameProdNonExist' => $NameProdNonExist,
            'NamePartNonExist' => $NamePartNonExist,
            'DnumProdNonExist' => $DnumProdNonExist,
            'DnumPartNonExist' => $DnumPartNonExist,
            'VendorPartNonExist' => $VendorPartNonExist
        ];
    }

    private function Unit_Master($db)
    {
        $get = DB::connection($this->mysql)->table('tbl_orderdatacheck2 as prod')
                    ->join('tbl_orderdatacheck1 as part','prod.po','=','part.po')
                    ->select('part.unit','part.kcode','part.vendor','part.partsname')
                    ->get();
        $match = 0;
        $unmatch = 0;
        $exist = 0;
        $non_exist = 0;
        $unitcount = 0;
        $CodeExist = [];
        $CodeNonExist = [];
        $CodeUnmatch = [];
        $UnitUnmatch = [];
        $VcodeUnmatch = [];
        $VendorUnmatch = [];
        $CodeMatch = [];
        $UnitMatch = [];
        $VcodeMatch = [];
        $UnitNonExist = [];
        $NameNonExist = [];
        $VendorNonExist = [];

        foreach ($get as $key => $unit) {
            if ($this->check_itemCode($unit->kcode,"XTANK",$db) > 0) {
                $exist++;
                $CodeExist[] = $unit->kcode;

                if($check_unmatch = $this->getUnit($unit->kcode,$unit->unit,$db) > 0) {
                    $match++;
                    $CodeMatch[] = $unit->kcode;
                    $UnitMatch[] = $unit->unit;
                    $VcodeMatch[] = $unit->kcode;
                } else {
                    $unmatch++;
                    $CodeUnmatch[] = $unit->kcode;
                    $UnitUnmatch[] = $unit->unit;
                    $VcodeUnmatch[] = $unit->kcode;
                    $VendorUnmatch[] = $unit->vendor;
                }

            } else {
                $non_exist++;
                $CodeNonExist[] = $unit->kcode;
                $UnitNonExist[] = $unit->unit;
                $VendorNonExist[] = $unit->vendor;
                $NameNonExist[] = $unit->partsname;
            }
        }

        foreach ($CodeNonExist as $key => $value) {
            if ($this->check_itemCode($value,"XPRTS",$db) > 0) {
            } else {
                $unitcount++;
            }
        }

        return $UnitUnmatch = [
            'exist' => $exist,
            'code_exist' => $CodeExist,
            'non_exist' => $non_exist,
            'code_nonexist' => $CodeNonExist,
            'match' => $match,
            'code_match' => $CodeMatch,
            'unit_match' => $UnitMatch,
            'vcode_match' => $VcodeMatch,
            'unmatch' => $unmatch,
            'code_unmatch' => $CodeUnmatch,
            'unit_unmatch' => $UnitUnmatch,
            'vcode_unmatch' => $VcodeUnmatch,
            'vendor_unmatch' => $VendorUnmatch,
            'UnitNonExist' => $UnitNonExist,
            'VendorNonExist' => $VendorNonExist,
            'NameNonExist' => $NameNonExist,
            'unitcount' => $unitcount
        ];
    }

    private function BOM($db)
    {
        $get = DB::connection($this->mysql)->table('tbl_orderdatacheck2 as pd')
                    ->join('tbl_orderdatacheck1 as pt','pd.po','=','pt.po')
                    ->select('pd.po as po',
                            'pd.code as code',
                            'pd.name as name',
                            'pt.kcode as kcode',
                            'pt.partsname as partsname',
                            'pt.vendor as vendor',
                            'pt.usage as usage',
                            'pt.div_usage as div_usage')
                    ->distinct()
                    ->get();
        $match = 0;
        $unmatch = 0;
        $exist = 0;
        $non_exist = 0;
        $CodeExist = [];
        $CodeNonExist = [];
        $NameNonExist = [];
        $PartsNameNonExist = [];
        $UsageNonExist = [];
        $DivUsageNonExist = [];
        $KcodeExist = [];
        $KcodeNonExist = [];
        $POUnmatch = [];
        $CodeUnmatch = [];
        $KcodeUnmatch = [];
        $NameUnmatch = [];
        $PartsNameUnmatch = [];
        $SupplierUnmatch = [];
        $UsageUnmatch = [];
        $DivUsageUnmatch = [];



        foreach ($get as $key => $bom) {
            if ($this->check_BOM($bom->code,$db) > 0) {
                $exist++;
                $CodeExist[] = $bom->code;
                $KcodeExist[] = $bom->kcode;

                if($check_unmatch = $this->getBOM($bom->code,$bom->kcode,$db) > 0) {
                    $match++;
                } else {
                    $unmatch++;
                    $POUnmatch[] = $bom->po;
                    $CodeUnmatch[] = $bom->code;
                    $KcodeUnmatch[] = $bom->kcode;
                    $NameUnmatch[] = $bom->name;
                    $PartsNameUnmatch[] = $bom->partsname;
                    $SupplierUnmatch[] = $bom->vendor;
                    $UsageUnmatch[] = $bom->usage;
                    $DivUsageUnmatch[] = $bom->div_usage;
                }

            } else {
                $non_exist++;
                $CodeNonExist[] = $bom->code;
                $KcodeNonExist[] = $bom->kcode;
                $NameNonExist[] = $bom->name;
                $PartsNameNonExist[] = $bom->partsname;
                $UsageNonExist[] = $bom->usage;
                $DivUsageNonExist[] = $bom->div_usage;
            }
        }

        return $bomUnmatch = [
            'exist' => $exist,
            'code_exist' => $CodeExist,
            'kcode_exist' => $KcodeExist,
            'non_exist' => $non_exist,
            'code_nonexist' => $CodeNonExist,
            'kcode_nonexist' => $KcodeNonExist,
            'match' => $match,
            'unmatch' => $unmatch,
            'po_unmatch' => $POUnmatch,
            'code_unmatch' => $CodeUnmatch,
            'kcode_unmatch' => $KcodeUnmatch,
            'name_unmatch' => $NameUnmatch,
            'partsname_unmatch' => $PartsNameUnmatch,
            'supplier_unmatch' => $SupplierUnmatch,
            'usage_unmatch' => $UsageUnmatch,
            'NameNonExist' => $NameNonExist,
            'PartsNameNonExist' => $PartsNameNonExist,
            'UsageNonExist' => $UsageNonExist,
            'DivUsageNonExist' => $DivUsageNonExist,
            'DivUsageUnmatch' => $DivUsageUnmatch
        ];
    }

    private function Usage($db)
    {
        $get = DB::connection($this->mysql)->table('tbl_orderdatacheck2 as pd')
                    ->join('tbl_orderdatacheck1 as pt','pd.po','=','pt.po')
                    ->select('pd.po as po',
                            'pd.code as code',
                            'pd.name as name',
                            'pt.kcode as kcode',
                            'pt.partsname as partsname',
                            'pt.vendor as vendor',
                            DB::raw("SUM(pt.usage) as usages"),
                            'pt.div_usage as div_usage')
                    ->groupBy('pd.po','pd.code','pd.name','pt.kcode',
                            'pt.partsname','pt.vendor','pt.div_usage')
                    ->distinct()
                    ->get();

        $unmatch_usage = 0;
        $CodeUnmatchUsage = [];
        $UsageUnmatchUsage = [];
        $POUnmatchUsage = [];
        $KcodeUnmatchUsage = [];
        $NameUnmatchUsage = [];
        $VendorUnmatchUsage = [];
        $PnameUnmatchUsage = [];
        $DivUnmatchUsage = [];

        foreach ($get as $key => $bom) {
            if ($this->check_BOM($bom->code,$db) > 0) {

                if ($this->getUsage($bom->code,$bom->kcode,$bom->usages,$db)) {

                } else {
                    $unmatch_usage++;
                    $CodeUnmatchUsage[] = $bom->code;
                    $UsageUnmatchUsage[] = $bom->usages;
                    $KcodeUnmatchUsage[] = $bom->kcode;
                    $NameUnmatchUsage[] = $bom->name;
                    $PnameUnmatchUsage[] = $bom->partsname;
                    $POUnmatchUsage[] = $bom->po;
                    $VendorUnmatchUsage[] = $bom->vendor;
                    $DivUnmatchUsage[] = $bom->div_usage;
                }
            } else {

            }
        }

        return $usgUnmatch = [
            'unmatch_usage' => $unmatch_usage,
            'code_unmatch_usage' => $CodeUnmatchUsage,
            'usage_unmatch_usage' => $UsageUnmatchUsage,
            'kcode_unmatch_usage' => $KcodeUnmatchUsage,
            'name_unmatch_usage' => $NameUnmatchUsage,
            'pname_unmatch_usage' => $PnameUnmatchUsage,
            'po_unmatch_usage' => $POUnmatchUsage,
            'vendor_unmatch_usage' => $VendorUnmatchUsage,
            'divusage_unmatch_usage' => $DivUnmatchUsage
        ];
    }

    private function Price_Master($db)
    {
        $prod = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->get();

        $exist = 0;
        $nonexist = 0;
        $match = 0;
        $unmatch = 0;
        $CodeMatch = [];
        $PriceMatch = [];
        $CodeUnmatch = [];
        $PriceUnmatch = [];
        $code = []; $name = [];
        $cust = []; $price = [];

        foreach ($prod as $key => $pricem) {
            $code[] = $pricem->code; $name[] = $pricem->name;
            $cust[] = $pricem->cust; $price[] = $pricem->price;
            if ($this->check_itemCode($pricem->code,"XBAIK",$db) > 0) {
                $exist++;
                if ($this->getSalesPrice($pricem->code,$pricem->price,$db) > 0) {
                    $match++;
                    $CodeMatch[] = $pricem->code;
                    $PriceMatch[] = $pricem->price;
                } else {
                    $unmatch++;
                    $CodeUnmatch[] = $pricem->code;
                    $PriceUnmatch[] = $pricem->price;
                }
            } else {
                $nonexist++;
            }
        }

        return $data = [
                    'exist' => $exist,
                    'non_exist' => $nonexist,
                    'match' => $match,
                    'code_match' => $CodeMatch,
                    'price_match' => $PriceMatch,
                    'unmatch' => $unmatch,
                    'code_unmatch' => $CodeUnmatch,
                    'price_unmatch' => $PriceUnmatch,
                    'code' => $code,
                    'cust' => $cust,
                    'name' => $name,
                    'price' => $price
                ];
    }

    private function Order_Entry($db)
    {
        $prod = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->get();

        $exist = 0;
        $nonexist = 0;
        $code = []; $qty = []; $date = [];
        $cust = []; $buyers = [];

        foreach ($prod as $key => $order) {
            $code[] = $order->code; $qty[] = $order->qty; $date[] = $order->cdate;
            $cust[] = $order->cust; $buyers[] = $order->buyers;
            if ($this->check_itemCode($order->code,"XRECE",$db) > 0) {
                $exist++;
            } else {
                $nonexist++;
            }
        }

        return $data = [
                    'exist' => $exist,
                    'non_exist' => $nonexist,
                    'code' => $code,
                    'cust' => $cust,
                    'qty' => $qty,
                    'buyers' => $buyers,
                    'cdate' => $date,
                ];
    }

    private function Products($db)
    {
        $prod = DB::connection($this->mysql)->table('tbl_orderdatacheck2')->get();
        $PO = [];
        $code = [];
        $name = [];
        $drawing_num = [];
        $cust = [];
        $custname = [];
        $qty = [];
        $exist = 0;
        $nonexist = 0;
        foreach ($prod as $key => $item) {
            if ($this->check_itemCode($item->code,"XITEM",$db)) {
                $exist++;
            } else {
                $nonexist++;
                $PO[] = $item->po;
                $code[] = $item->code;
                $name[] = $item->name;
                $drawing_num[] = $item->drawing_num;
                $cust[] = $item->cust;
                $custname[] = $item->custname;
                $qty[] = $item->qty;
            }
        }

        return $data = [
                    'po' => $PO,
                    'code' => $code,
                    'name' => $name,
                    'drawing_num' => $drawing_num,
                    'cust' => $cust,
                    'custname' => $custname,
                    'qty' => $qty,
                    'exist' => $exist,
                    'nonexist' => $nonexist
                ];
    }

    private function getUnmatchSalesPrice($code,$db)
    {
        $unmatch = [];
        foreach ($code as $key => $value) {
            $ok = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XHEAD as h','i.CODE','=','h.CODE')
                    ->join('XBAIK as s','i.CODE','=','s.CODE')
                    ->select('i.CODE','s.PRICE','h.NAME')
                    ->where('i.CODE',$value)
                    ->count();


            if ($ok > 0) {
                $query = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XHEAD as h','i.CODE','=','h.CODE')
                    ->join('XBAIK as s','i.CODE','=','s.CODE')
                    ->select('i.CODE','s.PRICE','h.NAME')
                    ->where('i.CODE',$value)
                    ->get();

                $unmatch[] = $query[0];
            }


        }

        return $unmatch;
    }

    private function getUnmatchUnitPrice($code,$db)
    {
        $unmatch = [];
        foreach ($code as $key => $value) {
            $ok = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XHEAD as h','i.CODE','=','h.CODE')
                    ->join('XTANK as u','i.CODE','=','u.CODE')
                    ->select('i.CODE','u.PRICE','h.NAME','i.VENDOR','u.TID')
                    ->where('i.CODE',$value)
                    ->count();
            if ($ok > 0) {
                $query = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XHEAD as h','i.CODE','=','h.CODE')
                    ->join('XTANK as u','i.CODE','=','u.CODE')
                    ->select('i.CODE','u.PRICE','h.NAME','i.VENDOR','u.TID')
                    ->where('i.CODE',$value)
                    ->get();

                $unmatch[] = $query[0];
            }

        }
        return $unmatch;
    }

    private function getUnmatchBOM($code,$kcode,$usage_unmatch,$db)
    {
        $unmatch = [];
        $prodcode = [];
        $partcode = [];
        $usage = [];
        foreach ($kcode as $key => $value) {
            //$query = $code[$key];
            $ok = DB::connection($this->mssql)
                    ->table('XPRTS')
                    ->where('KCODE',$value)
                    ->where('CODE',$code[$key])
                    ->select('CODE','KCODE','SIYOU')
                    ->distinct()
                    ->count();

            if ($ok > 0) {
                $query = DB::connection($this->mssql)
                        ->table('XPRTS')
                        ->where('KCODE',$value)
                        ->where('CODE',$code[$key])//104578401
                        ->select('CODE','KCODE','SIYOU')
                        ->get();
                $prodcode[] = $query[0]->CODE;
                $partcode[] = $query[0]->KCODE;
                $usage[] = $query[0]->SIYOU;
            } else {
                $prodcode[] = $code[$key];
                $partcode[] = $value;
                $usage[] = $usage_unmatch[$key];
            }

            $unmatch = [
                'prodcode' => $prodcode,
                'partcode' => $partcode,
                'usage' => $usage
            ];
        }
        //$bom = call_user_func_array('array_merge', $uBOM);
        return $unmatch;
    }

    private function getUnmatchUsage($code,$kcode,$usage,$db)
    {
        $unmatch = [];
        foreach ($code as $key => $value) {
            $ok = DB::connection($this->mssql)
                    ->table('XPRTS')
                    ->select('CODE','KCODE','SIYOU')
                    ->where('CODE',$value)
                    ->where('KCODE',$kcode[$key])
                    ->count();
            if ($ok > 0) {
                $query = DB::connection($this->mssql)
                        ->table('XPRTS')
                        ->select('CODE','KCODE','SIYOU')
                        ->where('CODE',$value)
                        ->where('KCODE',$kcode[$key])
                        ->get();
                $unmatch[] = $query[0];
            }

        }
        return $unmatch;
    }

    private function getUnmatchSupplier($code,$db)
    {
        $unmatch = [];
        foreach ($code as $key => $itemCode) {
            $ok = DB::connection($this->mssql)
                        ->table('XITEM as i')
                        ->join('XHEAD as h','i.CODE','=','h.CODE')
                        ->select('i.CODE as CODE','i.VENDOR as VENDOR','h.NAME as NAME')
                        ->where('i.CODE',$itemCode)
                        ->count();
            if ($ok > 0) {
                $query = DB::connection($this->mssql)
                        ->table('XITEM as i')
                        ->join('XHEAD as h','i.CODE','=','h.CODE')
                        ->select('i.CODE as CODE','i.VENDOR as VENDOR','h.NAME as NAME')
                        ->where('i.CODE',$itemCode)
                        ->first();
                $unmatch[] = $query;
            }

        }
        return $unmatch;
    }

    private function getUnmatchPartName($code,$name,$db)
    {
        $unmatch = [];
        foreach ($code as $key => $value) {
            $ok = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->join('XHEAD as h','i.CODE','=','h.CODE')
                    ->select('i.CODE','h.NAME','i.DRAWING_NUM')
                    ->where('i.CODE',$value)
                    ->count();
            if ($ok > 0) {
                $query = DB::connection($this->mssql)
                        ->table('XITEM as i')
                        ->join('XHEAD as h','i.CODE','=','h.CODE')
                        ->select('i.CODE','h.NAME','i.DRAWING_NUM')
                        ->where('i.CODE',$value)
                        ->get();
                        // ->table('XHEAD')
                        // ->where('NAME',$value)
                        // ->select('CODE','NAME')
                        // ->get();
                $unmatch[] = $query;
            }

        }

        if (count($unmatch) > 0) {
            $flat = call_user_func_array('array_merge', $unmatch);
            return $flat;
        } else {
            return $unmatch;
        }

    }

    private function getUnmatchProductName($code,$db)
    {
        $unmatch = [];
        foreach ($code as $key => $value) {
            $ok = DB::connection($this->mssql)
                        ->table('XITEM as i')
                        ->join('XHEAD as h','i.CODE','=','h.CODE')
                        ->select('i.CODE','h.NAME','i.DRAWING_NUM')
                        ->where('i.CODE',$value)
                        ->count();
            if ($ok > 0) {
                $query = DB::connection($this->mssql)
                        ->table('XITEM as i')
                        ->join('XHEAD as h','i.CODE','=','h.CODE')
                        ->select('i.CODE','h.NAME','i.DRAWING_NUM')
                        ->where('i.CODE',$value)
                        ->get();
                $unmatch[] = $query[0];
            }

        }
        return $unmatch;
    }

    private function getUnmatchProductDN($code,$db)
    {
        $unmatch = [];
        foreach ($code as $key => $value) {
            $ok = DB::connection($this->mssql)->table('XITEM as i')
                        ->select('i.CODE','i.DRAWING_NUM','h.NAME')
                        ->join('XHEAD as h','i.CODE','=','h.CODE')
                        ->where('i.CODE',$value)
                        ->count();
            if ($ok > 0) {
                $query = DB::connection($this->mssql)->table('XITEM as i')
                            ->select('i.CODE','i.DRAWING_NUM','h.NAME')
                            ->join('XHEAD as h','i.CODE','=','h.CODE')
                            ->where('i.CODE',$value)
                            ->get();
                $unmatch[] = $query[0];
            }

        }
        return $unmatch;
    }

    private function getUnmatchPartDN($code,$db)
    {
        $unmatch = [];
        foreach ($code as $key => $value) {
            $ok = DB::connection($this->mssql)
                    ->table('XITEM as i')
                    ->select('i.CODE','i.DRAWING_NUM','h.NAME')
                    ->join('XHEAD as h','i.CODE','=','h.CODE')
                    ->where('i.CODE',$value)
                    ->count();
            if ($ok > 0) {
                $query = DB::connection($this->mssql)
                        ->table('XITEM as i')
                        ->select('i.CODE','i.DRAWING_NUM','h.NAME')
                        ->join('XHEAD as h','i.CODE','=','h.CODE')
                        ->where('i.CODE',$value)
                        ->get();
                $unmatch[] = $query[0];
            }

        }
        return $unmatch;
    }

    private function truncateTable($table)
    {
        return DB::connection($this->mysql)->table($table)->truncate();
    }

    private function umatchBOMInsertDB($uBOM,$po_unmatch,$code_unmatch,$kcode_unmatch,$name_unmatch,$partsname_unmatch,$supplier_unmatch,$usage_unmatch,$lvl)
    {
        foreach ($po_unmatch as $key => $po) {
            try {
                $error = 0;
                $err_usage = 0;


                if ($uBOM['prodcode'][$key] == $code_unmatch[$key] || $uBOM['partcode'][$key] == $kcode_unmatch[$key]) {
                    $error = 1;
                }

                if ($uBOM['usage'][$key] != $usage_unmatch[$key]) {
                    $err_usage = 1;
                }

                DB::connection($this->mysql)->table('unmatch_bom')
                    ->insert([
                        'po' => $po,
                        'prodcode' => $code_unmatch[$key],
                        'prodname' => $name_unmatch[$key],
                        'partcode' => $kcode_unmatch[$key],
                        'partname' => $partsname_unmatch[$key],
                        'supplier' => $supplier_unmatch[$key],
                        'ycode' => "",
                        'error' => $error,
                        'lv' => $lvl[$key],
                        'usage' => $uBOM['usage'][$key],
                        'r3usage' => $usage_unmatch[$key],
                        'errorflg' => $err_usage
                    ]);

            } catch (Exception $e) {

            }
        }

    }

    private function umatchPartdnInsertDB($uPartDN,$dn_unmatch_partDN)
    {
        //echo "<pre>",print_r(array_unique($dn_unmatch_partDN)),"</pre>";
        if ($uPartDN != null) {
            foreach (array_unique($dn_unmatch_partDN) as $key => $partDN) {
                try {
                    $error = 0;

                    if ($this->clean($uPartDN[$key]->DRAWING_NUM) !=  $this->clean(mb_convert_encoding($partDN,"UTF-8","SJIS")) ) {
                        $error = 1;
                    }

                    DB::connection($this->mysql)->table('unmatch_partdn')
                        ->insert([
                            'code' => $uPartDN[$key]->CODE,
                            'partname' => mb_convert_encoding($uPartDN[$key]->NAME,"UTF-8","SJIS"),
                            'drawing_num' => mb_convert_encoding($uPartDN[$key]->DRAWING_NUM,"UTF-8","SJIS"),
                            'r3_dn' => mb_convert_encoding($partDN,"UTF-8","SJIS"),
                            'error' => $error,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);

                    //$arr = array_unique(json_decode(json_encode($partDN),true));

                    // $db->code = $uPartDN[$key]->CODE;
                    // $db->partname = mb_convert_encoding($uPartDN[$key]->NAME,"UTF-8","SJIS");
                    // $db->drawing_num = mb_convert_encoding($uPartDN[$key]->DRAWING_NUM,"UTF-8","SJIS");
                    // $db->r3_dn = mb_convert_encoding($partDN,"UTF-8","SJIS");
                    // if ($this->clean($uPartDN[$key]->DRAWING_NUM) !=  $this->clean(mb_convert_encoding($partDN,"UTF-8","SJIS")) ) {
                    //     $error = 1;
                    // }
                    // $db->error = $error;
                    // $db->save();

                    DB::connection($this->mysql)->table('unmatch_partdn')->where('drawing_num', 'Std Product')->delete();
                } catch (Exception $e) {

                }
            }
        }

        //return dd($uPartDN[1]);
    }

    private function umatchProddnInsertDB($uProductDN,$dn_unmatch_prodDN)
    {
        if ($uProductDN != null) {
            foreach (array_unique($dn_unmatch_prodDN) as $key => $prodDN) {
                try {
                    $error = 0;

                    if ($this->clean($uProductDN[$key]->DRAWING_NUM) != $this->clean($prodDN)) {
                        $error = 1;
                    }

                    DB::connection($this->mysql)->table('unmatch_proddn')
                        ->insert([
                            'code' => $uProductDN[$key]->CODE,
                            'name' => $uProductDN[$key]->NAME,
                            'drawing_num' => $uProductDN[$key]->DRAWING_NUM,
                            'r3_dn' => $prodDN,
                            'error' => $error,
                        ]);
                } catch (Exception $e) {

                }
            }
        }

    }

    private function umatchSupplierInsertDB($uSupplier,$unmatchSupplier)
    {
        //echo "<pre>",print_r($uSupplier),"</pre>";
        //return dd($uSupplier);
        if ($uSupplier != null) {
            foreach ($uSupplier as $key => $supp) {
                if ($supp != null) {
                    try {
                        $error = 0;
                        if ($supp->VENDOR != $unmatchSupplier[$key]) {
                            $error = 1;
                        }

                        $check = DB::connection($this->mysql)->table('unmatch_supts')
                                ->where('partcode',$supp->CODE)
                                ->count();
                        if ($check > 0) {
                            # code...
                        } else {
                            DB::connection($this->mysql)->table('unmatch_supts')
                                ->insert([
                                    'partcode' => $supp->CODE,
                                    'partname' => $supp->NAME,
                                    'r3_sup' => $unmatchSupplier[$key],
                                    'vendor' => $supp->VENDOR,
                                    'error' => $error,
                                ]);
                        }
                    } catch (Exception $e) {

                    }

                }

            }
        }
    }

    private function umatchUnitInsertDB($uUnitPrice,$unit_unmatch,$vendor_unmatch)
    {
        if ($uUnitPrice != null) {
            foreach (array_unique($unit_unmatch) as $key => $unit) {
                try {
                    $error = 0;
                    if ($uUnitPrice[$key]->PRICE != $unit) {
                        $error = 1;
                    }

                    DB::connection($this->mysql)->table('unmatch_unitprice')
                        ->insert([
                            'tid' => $uUnitPrice[$key]->TID,
                            'lv' => 1,
                            'code' => $uUnitPrice[$key]->CODE,
                            'partname' => $uUnitPrice[$key]->NAME,
                            'vendor' => $vendor_unmatch[$key],
                            'price' => $uUnitPrice[$key]->PRICE,
                            'r3_price' => $unit,
                            'error' => $error,
                        ]);
                } catch (Exception $e) {

                }
            }
        }

    }

    private function umatchSalesInsertDB($uSalesPrice,$unit_unmatch)
    {
        // $data = [
        //     'salesprice' => $uSalesPrice,
        //     'unmatch' => $unit_unmatch
        // ];
        // return dd($data);
        if ($uSalesPrice != null) {
            foreach (array_unique($unit_unmatch) as $key => $sales) {
                try {
                    $error = 0;

                    if (isset($uSalesPrice[$key]->PRICE)) {
                        if ($uSalesPrice[$key]->PRICE != $sales) {
                            $error = 1;
                        }

                        DB::connection($this->mysql)->table('unmatch_salesprice')
                            ->insert([
                                'code' => $uSalesPrice[$key]->CODE,
                                'name' => $uSalesPrice[$key]->NAME,
                                'price' => $uSalesPrice[$key]->PRICE,
                                'r3_price' => $sales,
                                'error' => $error,
                            ]);
                    }
                        
                } catch (Exception $e) {

                }
            }
        }

    }

    private function umatchProdNameInsertDB($uProductName,$name_unmatch_prodName,$r3_dn,$con_unmatch_prodName)
    {
        if ($uProductName != null) {
            $cnt = 0;
            foreach (array_unique($name_unmatch_prodName) as $key => $prodName) {
                try {
                    $error = 0;

                    if ($uProductName[$key]->NAME != $prodName) {
                        $error = 1;
                    }

                    DB::connection($this->mysql)->table('unmatch_prodname')
                        ->insert([
                            'code' => $uProductName[$key]->CODE,
                            'name' => $uProductName[$key]->NAME,
                            'r3_name' => $prodName,
                            'error' => $error]);
                } catch (Exception $e) {

                }
            }
            $cnt++;
        }

    }

    private function umatchPartNameInsertDB($uPartName,$name_unmatch_partName)
    {
        //echo "<pre>",print_r($uPartName),"</pre>";
        if ($uPartName != null) {
            $cnt = 0;
            foreach (array_unique($name_unmatch_partName) as $key => $partName) {
                try {
                    $error = 0;
                    if ($uPartName[$key]->NAME != $partName) {
                        $error = 1;
                    }
                    DB::connection($this->mysql)->table('unmatch_partname')->insert([
                        'code' => $uPartName[$key]->CODE,
                        'partname' => $uPartName[$key]->NAME,
                        'r3_partname' => $partName,
                        'error' => $error
                    ]);
                } catch (Exception $e) {

                }
            }
            $cnt++;
        }
    }

    private function umatchUsageInsertDB($uUsage,$po_unmatch_usage,$code_unmatch_usage,$name_unmatch_usage,$kcode_unmatch_usage,$pname_unmatch_usage,$vendor_unmatch_usage,$usage_unmatch_usage,$divusage_unmatch_usage)
    {
        //echo "<pre>",print_r($code_unmatch_usage),"</pre>";
        if ($uUsage != null) {
            foreach (array_unique($code_unmatch_usage) as $key => $code) {
                try {
                    $usage = (isset($uUsage[$key]->SIYOU))? $uUsage[$key]->SIYOU : "";
                    $error = 0;
                    $error_usg = 0;

                    if ($kcode_unmatch_usage[$key] != $uUsage[$key]->KCODE) {
                        $error = 1;
                    }

                    if ($usage_unmatch_usage[$key] != $usage) {
                        $error_usg = 1;
                    }

                    DB::connection($this->mysql)->table('unmatch_usgts')
                        ->insert([
                            po => $po_unmatch_usage[$key],
                            productcode => $code,
                            productname => $name_unmatch_usage[$key],
                            partcode => $kcode_unmatch_usage[$key],
                            partname => $pname_unmatch_usage[$key],
                            supplier => $vendor_unmatch_usage[$key],
                            kcode => $uUsage[$key]->KCODE,
                            error => $error,
                            lv => $divusage_unmatch_usage[$key],
                            usg => $usage_unmatch_usage[$key],
                            siyou => $usage,
                            error_usg => $error_usg,
                        ]);
                } catch (Exception $e) {

                }
            }
        } else {
            foreach (array_unique($code_unmatch_usage) as $key => $code) {
                try {
                    $error = 0;
                    $error_usg = 0;

                    if ($kcode_unmatch_usage[$key] != "") {
                        $error = 1;
                    }

                    if ($usage_unmatch_usage[$key] != "") {
                        $error_usg = 1;
                    }

                    DB::connection($this->mysql)->table('unmatch_usgts')
                        ->insert([
                            'po' => $po_unmatch_usage[$key],
                            'productcode' => $code,
                            'productname' => $name_unmatch_usage[$key],
                            'partcode' => $kcode_unmatch_usage[$key],
                            'partname' => $pname_unmatch_usage[$key],
                            'supplier' => $vendor_unmatch_usage[$key],
                            'kcode' => "",

                            'error' => $error,
                            'lv' => $divusage_unmatch_usage[$key],
                            'usg' => $usage_unmatch_usage[$key],
                            'siyou' => "",
                            'error_usg' => $error_usg,
                        ]);


                } catch (Exception $e) {

                }
            }
        }

    }

    private function getUnmatch($table)
    {
        $get = DB::connection($this->mysql)->table($table)->where('error',1)->get();
        return $get;
    }

    private function countUnmatch($table,$field)
    {
        $unmatch = DB::connection($this->mysql)->table($table)
                    ->where($field,1)
                    ->count();
        return $unmatch;
    }

    public function UnmatchPartsDN()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch';

            Excel::create('unmatch_PartDN_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "PartName");
                    $sheet->cell('C1', "DRAWING_NUM");
                    $sheet->cell('D1', "R3_DN");
                    $sheet->cell('E1', "ErrorFLG");

                    $sheet->cell('G1', "ORIG_DRAWING_NUM");
                    $sheet->cell('H1', "ORIG_ErrorFLG");

                    $row = 2;
                    $data = DB::connection($this->mysql)->table('unmatch_partdn')->where('error',1)->distinct('code')->get();
                    $part = json_decode(json_encode($data), True);
                    foreach ($part as $key => $partdn) {
                        array_unique($partdn);
                        $sheet->cell('A'.$row, $partdn['code']);
                        $sheet->cell('B'.$row, $partdn['partname']);
                        $sheet->cell('C'.$row, $this->clean($partdn['drawing_num']));
                        $sheet->cell('D'.$row, $this->clean($partdn['r3_dn']));
                        $sheet->cell('E'.$row, $partdn['error']);

                        $sheet->cell('G'.$row, $partdn['drawing_num']);
                        $sheet->cell('H'.$row, $partdn['r3_dn']);

                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();

        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function UnmatchProdDN()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch';
            Excel::create('unmatch_ProductDN_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "DRAWING_NUM");
                    $sheet->cell('D1', "R3_DN");
                    $sheet->cell('E1', "ErrorFLG");

                    $sheet->cell('G1', "ORIG_DRAWING_NUM");
                    $sheet->cell('H1', "ORIG_ErrorFLG");

                    $row = 2;
                    $data = DB::connection($this->mysql)->table('unmatch_proddn')->where('error',1)->distinct('code')->get();
                    $prod = json_decode(json_encode($data), True);
                    foreach ($prod as $key => $proddn) {
                        array_unique($proddn);
                        $sheet->cell('A'.$row, $proddn['code']);
                        $sheet->cell('B'.$row, $proddn['name']);
                        $sheet->cell('C'.$row, $this->clean($proddn['drawing_num']));
                        $sheet->cell('D'.$row, $this->clean($proddn['r3_dn']));
                        $sheet->cell('E'.$row, $proddn['error']);

                        $sheet->cell('G'.$row, $proddn['drawing_num']);
                        $sheet->cell('H'.$row, $proddn['r3_dn']);
                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();


        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function UnmatchProdName()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch';
            Excel::create('unmatch_ProductName_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "R3_NAME");
                    $sheet->cell('D1', "ErrorFLG");
                    $sheet->cell('E1', "BU");

                    $row = 2;
                    $data = DB::connection($this->mysql)->table('unmatch_prodname')->where('error',1)->distinct('code')->get();
                    $prod = json_decode(json_encode($data), True);
                    foreach ($prod as $key => $prodname) {
                        array_unique($prodname);
                        $sheet->cell('A'.$row, $prodname['code']);
                        $sheet->cell('B'.$row, $prodname['name']);
                        $sheet->cell('C'.$row, $prodname['r3_name']);
                        $sheet->cell('D'.$row, $prodname['error']);
                        $sheet->cell('E'.$row, $prodname['bu']);
                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();


        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function UnmatchPartName()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch';
            Excel::create('unmatch_PartName_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "PartCode");
                    $sheet->cell('B1', "PartName");
                    $sheet->cell('C1', "R3_NAME");
                    $sheet->cell('D1', "ErrorFLG");

                    $row = 2;
                    $data = DB::connection($this->mysql)->table('unmatch_partname')->where('error',1)->distinct('code')->get();
                    $part = json_decode(json_encode($data), True);
                    foreach ($part as $key => $partname) {
                        array_unique($partname);
                        $sheet->cell('A'.$row, $partname['code']);
                        $sheet->cell('B'.$row, $partname['partname']);
                        $sheet->cell('C'.$row, $partname['r3_partname']);
                        $sheet->cell('D'.$row, $partname['error']);
                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();


        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function UnmatchSupplier()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch/';

            Excel::create('unmatch_Supplier_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "PartCode");
                    $sheet->cell('B1', "PartName");
                    $sheet->cell('C1', "R3_Supplier");
                    $sheet->cell('D1', "VENDOR");
                    $sheet->cell('E1', "ErrorFLG");

                    $row = 2;
                    $db = DB::connection($this->mysql)->table('unmatch_supts')
                            ->where('error',1)
                            ->select('partcode','partname','r3_sup','vendor','error')
                            ->distinct()
                            ->get();
                    $suppliers = json_decode(json_encode($db), True);
                    foreach ($suppliers as $key => $sup) {
                        array_unique($sup);
                        $sheet->cell('A'.$row, $sup['partcode']);
                        $sheet->cell('B'.$row, $sup['partname']);
                        $sheet->cell('C'.$row, $sup['r3_sup']);
                        $sheet->cell('D'.$row, $sup['vendor']);
                        $sheet->cell('E'.$row, $sup['error']);
                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();




        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function UnmatchBOM()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch/';

            Excel::create('unmatch_BOM_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "PO");
                    $sheet->cell('B1', "ProductCode");
                    $sheet->cell('C1', "ProductName");
                    $sheet->cell('D1', "PartCode");
                    $sheet->cell('E1', "PartName");
                    $sheet->cell('F1', "Supplier");
                    $sheet->cell('G1', "Y_CODE");
                    $sheet->cell('H1', "ErrorFLG");
                    $sheet->cell('I1', "Lvl");
                    $sheet->cell('J1', "USAGE");
                    $sheet->cell('K1', "R3_Usage");
                    $sheet->cell('L1', "ErrorFLG");

                    $row = 2;
                    $db = DB::connection($this->mysql)->table('unmatch_bom')->where('error',1)->distinct()->get();
                    $boms = json_decode(json_encode($db), True);
                    foreach ($boms as $key => $bom) {
                        array_unique($bom);
                        $sheet->cell('A'.$row, $bom['po']);
                        $sheet->cell('B'.$row, $bom['prodcode']);
                        $sheet->cell('C'.$row, $bom['prodname']);
                        $sheet->cell('D'.$row, $bom['partcode']);
                        $sheet->cell('E'.$row, $bom['partname']);
                        $sheet->cell('F'.$row, $bom['supplier']);
                        $sheet->cell('G'.$row, $bom['ycode']);
                        $sheet->cell('H'.$row, $bom['error']);
                        $sheet->cell('I'.$row, $bom['lv']);
                        $sheet->cell('J'.$row, $bom['usage']);
                        $sheet->cell('K'.$row, $bom['r3usage']);
                        $sheet->cell('L'.$row, $bom['errorflg']);
                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();




        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function UnmatchUnitPrice()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch';
            Excel::create('unmatch_UnitPrice_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "R3_SUPPLIER");
                    $sheet->cell('D1', "PRICE");
                    $sheet->cell('E1', "R3_PRICE");
                    $sheet->cell('F1', "ErrorFLG");

                    $row = 2;
                    $data = DB::connection($this->mysql)->table('unmatch_unitprice')->where('error',1)->distinct('code')->get();
                    $units = json_decode(json_encode($data), True);
                    foreach ($units as $key => $unit) {
                        array_unique($unit);
                        $sheet->cell('A'.$row, $unit['code']);
                        $sheet->cell('B'.$row, $unit['partname']);
                        $sheet->cell('C'.$row, $unit['vendor']);
                        $sheet->cell('D'.$row, $unit['price']);
                        $sheet->cell('E'.$row, $unit['r3_price']);
                        $sheet->cell('F'.$row, $unit['error']);
                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();


        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function UnmatchSalePrice()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch';
            Excel::create('unmatch_SalesPrice_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "CODE");
                    $sheet->cell('B1', "NAME");
                    $sheet->cell('C1', "PRICE");
                    $sheet->cell('D1', "R3_PRICE");
                    $sheet->cell('E1', "ErrorFLG");

                    $row = 2;
                    $data = DB::connection($this->mysql)->table('unmatch_salesprice')->where('error',1)->distinct('code')->get();
                    $sales = json_decode(json_encode($data), True);
                    foreach ($sales as $key => $sale) {
                        array_unique($sale);
                        $sheet->cell('A'.$row, $sale['code']);
                        $sheet->cell('B'.$row, $sale['name']);
                        $sheet->cell('C'.$row, $sale['price']);
                        $sheet->cell('D'.$row, $sale['r3_price']);
                        $sheet->cell('E'.$row, $sale['error']);
                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();


        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    public function UnmatchUsage()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch';
            Excel::create('unmatch_Usage_'.$date, function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "PO");
                    $sheet->cell('B1', "ProductCode");
                    $sheet->cell('C1', "ProductName");
                    $sheet->cell('D1', "PartCode");
                    $sheet->cell('E1', "PartName");
                    $sheet->cell('F1', "Supplier");
                    $sheet->cell('G1', "KCODE");
                    $sheet->cell('H1', "ErrorFLG");
                    $sheet->cell('I1', "LV");
                    $sheet->cell('J1', "Usg");
                    $sheet->cell('K1', "SIYOU");
                    $sheet->cell('L1', "ErrorFLG_Usg");

                    $row = 2;
                    $data = DB::connection($this->mysql)->table('unmatch_usgts')->where('error',1)->distinct('code')->get();
                    $usages = json_decode(json_encode($data), True);
                    foreach ($usages as $key => $usage) {
                        array_unique($usage);
                        $sheet->cell('A'.$row, $usage['PO']);
                        $sheet->cell('B'.$row, $usage['productcode']);
                        $sheet->cell('C'.$row, $usage['productname']);
                        $sheet->cell('D'.$row, $usage['partcode']);
                        $sheet->cell('E'.$row, $usage['partname']);
                        $sheet->cell('F'.$row, $usage['supplier']);
                        $sheet->cell('G'.$row, $usage['kcode']);
                        $sheet->cell('H'.$row, $usage['error']);
                        $sheet->cell('I'.$row, $usage['lv']);
                        $sheet->cell('J'.$row, $usage['usg']);
                        $sheet->cell('K'.$row, $usage['siyou']);
                        $sheet->cell('L'.$row, $usage['error_usg']);
                        $row++;
                    }

                });

            })->download('xls');
            // ->store('xls',$path)->export();




        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function getBOMUnmatch()
    {
        $get = DB::connection($this->mysql)->table('tbl_orderdatacheck2 as pd')
                    ->join('tbl_orderdatacheck1 as pt','pd.po','=','pt.po')
                    ->select('pd.po as po',
                            'pd.code as prodcode',
                            'pd.name as prodname',
                            'pt.kcode as partcode',
                            'pt.partsname as partname',
                            'pt.vendor as supplier',
                            'pt.usage as usages',
                            'pt.div_usage as lvl')
                    ->distinct()
                    ->get();
    }

    private function momsCheck()
    {
        DB::connection($this->mysql)->table('momscheck')->truncate();
        $po = DB::connection($this->mysql)->table('tbl_orderdatacheck2 as pd')
                    ->join('tbl_orderdatacheck1 as pt','pd.po','=','pt.po')
                    ->select('pd.po as po',
                            'pd.code as prodcode',
                            'pd.name as prodname',
                            'pt.kcode as partcode',
                            'pt.partsname as partsname',
                            'pt.vendor as vendor',
                            'pt.usage as usage',
                            'pt.div_usage as div_usage',
                            'pt.qty as qty',
                            'pd.con as db')
                    ->get();

        // $po = DB::connection($this->mysql)->table('tbl_orderdatacheck2')
        //         ->select('po')->get();



        foreach ($po as $key => $data) {
            $this->checkMomsCheck($key,$data);
            // echo "<pre>",print_r($data),"</pre>";
        }
    }

    private function momscheck2()
    {
        $xhikiArray = [];
        $txhiki = DB::connection($this->mssql)
                    ->select("SELECT k.CODE,
                                    k.OYACODE,
                                    k.BUMO,
                                    k.HOKAN,
                                    k.NEXTBUMO,
                                    k.IDATE,
                                    k.KVOL,
                                    k.TJITU,
                                    k.PSUMI,
                                    k.PORDER,
                                    k.PEDA,
                                    h.NAME as PARTNAME,
                                    ho.NAME as OYANAME,
                                    cb.BNAME as BUMONAME,
                                    ch.BNAME as HOKANNAME,
                                    cn.BNAME as NEXTBUMONAME,
                                    vtk.SEIBAN as PMISEIBAN,
                                    i.VENDOR,
                                    b.SIYOU as usage,
                                    b.SIYOUW as div_usage
                            FROM XHIKI k
                            left join XHEAD h on h.CODE=k.CODE
                            left join XHEAD ho on ho.CODE=k.OYACODE
                            left join XSECT cb on cb.BUMO=k.BUMO
                            left join XSECT ch on ch.BUMO=k.HOKAN
                            left join XSECT cn on cn.BUMO=k.NEXTBUMO
                            left join XSLIP sl on k.PORDER = sl.PORDER AND k.PEDA = sl.PEDA AND sl.KBAN=0
                            left join XSECT se on se.BUMO = sl.VENDOR
                            
                            LEFT JOIN VATOK vtk on k.PORDER = vtk.PORDER
                            LEFT JOIN XITEM i on k.CODE = i.CODE
                            LEFT JOIN XPRTS b on k.OYACODE = b.CODE AND k.CODE = b.KCODE
                            WHERE (( (k.IDATE=N'' OR k.IDATE is null)  OR (k.IDATE<>N'--Remote-' OR k.IDATE is null))
                            AND (k.KVOL>k.TJITU) AND ((k.PSUMI in ('P','T','M','W','H') AND ((k.PSUMI<>N'N' OR k.PSUMI is null))) OR se.GKU=1 ))
                            GROUP BY h.NAME,
                                    ho.NAME,
                                    cb.BNAME,
                                    ch.BNAME,
                                    cn.BNAME,
                                    vtk.SEIBAN,
                                    i.VENDOR,
                                    b.SIYOU,
                                    b.SIYOUW,
                                    k.CODE,
                                    k.OYACODE,
                                    k.BUMO,
                                    k.HOKAN,
                                    k.NEXTBUMO,
                                    k.IDATE,
                                    k.KVOL,
                                    k.TJITU,
                                    k.PSUMI,
                                    k.PORDER,
                                    k.PEDA
                            ORDER BY k.PORDER,k.PEDA,k.CODE");//left join XLNKP lnk on k.PORDER=lnk.PORDER
        //return dd($txhiki);

        foreach ($txhiki as $key => $xhiki) {
            $diff = 0;
            $momsfile = DB::connection($this->mysql)->table('tbl_orderdatacheck2 as pd')
                            ->join('tbl_orderdatacheck1 as pt','pd.po','=','pt.po')
                            ->select('pd.po as po',
                                    'pd.code as prodcode',
                                    'pd.name as prodname',
                                    'pt.kcode as partcode',
                                    'pt.partsname as partsname',
                                    'pt.vendor as vendor',
                                    'pt.usage as usage',
                                    'pt.div_usage as div_usage',
                                    'pt.qty as qty',
                                    'pd.con as db')
                            ->where('pd.po', $xhiki->PMISEIBAN)
                            ->where('pd.code', $xhiki->OYACODE)
                            ->where('pt.kcode', $xhiki->CODE)
                            ->first();

            if (count((array)$momsfile) > 0) {
                $diff = $xhiki->KVOL - $momsfile->qty;
                array_push($xhikiArray,[
                    'po' => $xhiki->PMISEIBAN, // ypics
                    'code' => $xhiki->OYACODE, // ypics
                    'prodname' =>$xhiki->PARTNAME, // ypics
                    'kcode' => $xhiki->CODE, // ypics
                    'lvl' => $momsfile->div_usage,
                    'vendor' => $xhiki->VENDOR, // ypics
                    'usage' => $momsfile->usage,
                    'qty' => $momsfile->qty,
                    'siyou' => $xhiki->usage, // ypics
                    'ypics_qty' => $xhiki->KVOL, // ypics
                    'diff1' => $diff,
                    'moms' => $momsfile->qty,
                    'withdrawal_qty' => $xhiki->KVOL, // ypics
                    'diff2' => $diff,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            array_push($xhikiArray,[
                'po' => $xhiki->PMISEIBAN, //ypics
                'code' => $xhiki->OYACODE, //ypics
                'prodname' =>$xhiki->PARTNAME, //ypics
                'kcode' => $xhiki->CODE, //ypics
                'lvl' => 'N/A',
                'vendor' => $xhiki->VENDOR, //ypics
                'usage' => 'N/A',
                'qty' => 'N/A',
                'siyou' => $xhiki->usage, //ypics
                'ypics_qty' => $xhiki->KVOL, //ypics
                'diff1' => 'N/A',
                'moms' => 'N/A',
                'withdrawal_qty' => $xhiki->KVOL, //ypics
                'diff2' => 'N/A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        DB::connection($this->mysql)->table('momscheck2')->truncate();

        $insertBatchs = array_chunk($xhikiArray, 2000);
        foreach ($insertBatchs as $batch) {
            DB::connection($this->mysql)->table('momscheck2')->insert($batch);
        }
    }

    private function checkMomsCheck($keymom,$data)
    {
        $arr = [];
        $checked = DB::connection($this->mssql)
                        ->table('XHIKI as h')
                        // ->join('XPRTS as b','h.CODE','=','b.KCODE'
                        ->join('XPRTS as b', function($join)
                            {
                                $join->on('h.CODE','=','b.KCODE');
                                $join->on('h.OYACODE','=','b.CODE');
                            })
                        ->join('XRECE as r','b.CODE','=','r.CODE')
                        ->join('XSLIP as s','h.PORDER','=','s.PORDER')
                        ->where('b.CODE',$data->prodcode)
                        ->where('b.KCODE',$data->partcode)
                        ->where('h.OYACODE',$data->prodcode)
                        ->where('h.CODE',$data->partcode)
                        ->where('r.CODE',$data->prodcode)
                        ->where('r.SORDER',$data->po)
                        ->where('s.SEIBAN',$data->po)
                        ->select('b.CODE as prodcode',
                                'b.KCODE as partcode',
                                'h.KVOL as qty',
                                'b.SIYOU as usage',
                                'h.INPUTDATE')
                        ->orderBy('h.INPUTDATE','desc')
                        ->distinct()
                        ->get();

        if (count((array)$checked) > 0) {
            $ypics = DB::connection($this->mssql)
                        ->table('XHIKI as h')
                        // ->join('XPRTS as b','h.CODE','=','b.KCODE'
                        ->join('XPRTS as b', function($join)
                            {
                                $join->on('h.CODE','=','b.KCODE');
                                $join->on('h.OYACODE','=','b.CODE');
                            })
                        ->join('XRECE as r','b.CODE','=','r.CODE')
                        ->join('XSLIP as s','h.PORDER','=','s.PORDER')
                        ->where('b.CODE',$data->prodcode)
                        ->where('b.KCODE',$data->partcode)
                        ->where('h.OYACODE',$data->prodcode)
                        ->where('h.CODE',$data->partcode)
                        ->where('r.CODE',$data->prodcode)
                        ->where('r.SORDER',$data->po)
                        ->where('s.SEIBAN',$data->po)
                        ->select('b.CODE as prodcode',
                                'b.KCODE as partcode',
                                'h.KVOL as qty',
                                'b.SIYOU as siyou',
                                'h.INPUTDATE')
                        ->orderBy('h.INPUTDATE','desc')
                        ->distinct()
                        ->first();
            //echo "<pre>",print_r($ypics),"</pre>";

            $this->insertMomsCheckReport($data->po,$data->partsname,$data->prodcode,$ypics->partcode,$data->div_usage,$data->vendor,$data->usage,$data->qty,$ypics->siyou,$ypics->qty);
        } else {
            $this->insertMomsCheckReport($data->po,$data->partsname,$data->prodcode,$data->partcode,$data->div_usage,$data->vendor,$data->usage,$data->qty,"N/A","N/A");
        }
    }

    private function insertMomsCheckReport($po,$partname,$prodcode,$kcode,$lvl,$vendor,$usage,$qty,$siyou,$ypics_qty)
    {
        // $diff2 = 0;
        // if ($this->checkIfSameData($po,$prodcode,$kcode,$lvl,$vendor)) {
        //     $moms = $this->sumQTY($po,$prodcode,$kcode,$lvl,$vendor);
        //     $diff2 = $ypics_qty - $moms;
        //     $this->updateQTY($po,$prodcode,$kcode,$lvl,$vendor,$usage,$qty,$moms,$diff2);
        // } else {
        //     $moms = $qty;
        // }

        if ($ypics_qty !== "N/A") {
            $diff = $ypics_qty - $qty;
        }

        if ($ypics_qty == "N/A") {
            $diff = "N/A";
        }

        $this->insertToMOMsCheck($po,$partname,$prodcode,$kcode,$lvl,$vendor,$usage,$qty,$siyou,$ypics_qty,$diff);

        $moms = $this->sumQTY($po,$prodcode,$kcode,$lvl,$vendor);

        if ($ypics_qty !== "N/A") {
            $diff2 = $ypics_qty - $moms;
        }

        if ($ypics_qty == "N/A") {
            $diff2 = "N/A";//0 - $moms;
        }

        $this->updateQTY($po,$prodcode,$kcode,$lvl,$vendor,$usage,$qty,$moms,$ypics_qty,$diff2);
    }

    private function insertToMOMsCheck($po,$partname,$prodcode,$kcode,$lvl,$vendor,$usage,$qty,$siyou,$ypics_qty,$diff)
    {
         DB::connection($this->mysql)->table('momscheck')
            ->insert([
                'po' => $po,
                'prodname' => mb_convert_encoding($partname,'UTF-8','SJIS'),
                'code' => $prodcode,
                'kcode' => $kcode,
                'lvl' => $lvl,
                'vendor' => $vendor,
                'usage' => $usage,
                'qty' => $qty,
                'siyou' => $siyou,
                'ypics_qty' => $ypics_qty,
                'diff1' => $diff,
                // 'moms' => $moms,
                // 'withdrawal_qty' => $ypics_qty,
                // 'diff2' => $diff2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    }

    private function checkIfSameData($po,$prodcode,$kcode,$lvl,$vendor)
    {
        $cnt = DB::connection($this->mysql)->table('momscheck')
                    ->where('po',$po)
                    ->where('code',$prodcode)
                    ->where('kcode',$kcode)
                    ->where('lvl',$lvl)
                    ->where('vendor',$vendor)
                    ->count();
        if ($cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function sumQTY($po,$prodcode,$kcode,$lvl,$vendor)
    {
        $data = DB::connection($this->mysql)->table('tbl_orderdatacheck2 as pd')
                    ->join('tbl_orderdatacheck1 as pt','pd.po','=','pt.po')
                    ->where('pd.po',$po)
                    ->where('pd.code',$prodcode)
                    ->where('pt.kcode',$kcode)
                    ->where('pt.vendor',$vendor)
                    ->where('pt.div_usage',$lvl)
                    ->select(DB::raw('SUM(pt.qty) as qty'))
                    ->groupBy('pd.po','pd.code','pt.kcode','pt.vendor','pt.div_usage')
                    ->first();
        if ($data != null) {
            return $data->qty;
        }
        return "0.0";
    }

    private function updateQTY($po,$prodcode,$kcode,$lvl,$vendor,$usage,$qty,$newqty,$ypics_qty,$diff)
    {
        DB::connection($this->mysql)->table('momscheck')
            ->where('po',$po)
            ->where('code',$prodcode)
            ->where('kcode',$kcode)
            ->where('lvl',$lvl)
            ->where('vendor',$vendor)
            ->where('usage',$usage)
            ->where('qty',$qty)
            ->update(['moms' => $newqty,'withdrawal_qty' => $ypics_qty,'diff2' => $diff]);
    }

    public function MomsCheckExcel()
    {
        try
        {
            $dt = Carbon::now();
            $date = substr($dt->format('Ymd'), 2);
            $path = storage_path().'/Order_Data_Check/unmatch';
            Excel::create('MOMS_Check_'.$date, function($excel)
            {
                $excel->sheet('MOMs', function($sheet)
                {
                    $sheet->cell('A1', "PO");
                    $sheet->cell('B1', "Code");
                    $sheet->cell('C1', "Part Code");
                    $sheet->cell('D1', "Part Name");
                    $sheet->cell('E1', "Lvl");
                    $sheet->cell('F1', "R3 Usage");
                    $sheet->cell('G1', "R3 Qty");
                    $sheet->cell('H1', "Supplier");
                    $sheet->cell('I1', "YPICS Usage");
                    $sheet->cell('J1', "YPICS Qty");
                    $sheet->cell('K1', "Difference");
                    $sheet->cell('L1', "");
                    $sheet->cell('M1', "MOMS");
                    $sheet->cell('N1', "Withdrawal Qty");
                    $sheet->cell('O1', "Qty. Difference");
                    $sheet->cell('P1', "Usage Difference");

                    $row = 2;
                    $data = DB::connection($this->mysql)->table('momscheck')->get();
                    $usage_diff = 'N/A';
                    foreach ($data as $key => $moms) {
                        $sheet->setHeight($row, 20);
                        $sheet->cell('A'.$row, $moms->po);
                        $sheet->cell('B'.$row, $moms->code);
                        $sheet->cell('C'.$row, $moms->kcode);
                        $sheet->cell('D'.$row, $moms->prodname);
                        $sheet->cell('E'.$row, $moms->lvl);
                        $sheet->cell('F'.$row, $moms->usage);
                        $sheet->cell('G'.$row, $moms->qty);
                        $sheet->cell('H'.$row, $moms->vendor);
                        $sheet->cell('I'.$row, $moms->siyou);
                        $sheet->cell('J'.$row, $moms->ypics_qty);
                        $sheet->cell('K'.$row, ($moms->diff1==0)?"0.0":$moms->diff1);
                        $sheet->cell('L'.$row, "");
                        $sheet->cell('M'.$row, $moms->moms);
                        $sheet->cell('N'.$row, $moms->withdrawal_qty);
                        $sheet->cell('O'.$row, ($moms->diff2==0)?"0.0":$moms->diff2);

                        if ($moms->siyou == 'N/A') {
                            $usage_diff = 'N/A';
                        } else {
                            $usage_diff = $moms->siyou - $moms->usage;
                        }

                        $sheet->cell('P'.$row, ($usage_diff == 0)? "0.0": $usage_diff);
                        $row++;
                    }

                });

                $excel->sheet('XHIKI', function($sheet)
                {
                    $sheet->cell('A1', "PO");
                    $sheet->cell('B1', "Code");
                    $sheet->cell('C1', "Part Code");
                    $sheet->cell('D1', "Part Name");
                    $sheet->cell('E1', "Lvl");
                    $sheet->cell('F1', "YPICS Usage");
                    $sheet->cell('G1', "YPICS Qty");
                    $sheet->cell('H1', "Supplier");
                    $sheet->cell('I1', "R3 Usage");
                    $sheet->cell('J1', "R3 Qty");
                    $sheet->cell('K1', "Difference");
                    $sheet->cell('L1', "");
                    $sheet->cell('M1', "MOMS");
                    $sheet->cell('N1', "Withdrawal Qty");
                    $sheet->cell('O1', "Difference");
                    $sheet->cell('P1', "Usage Difference");

                    $row = 2;
                    $check = DB::connection($this->mysql)->table('momscheck2')
                                ->select('po',
                                        'code',
                                        'kcode',
                                        'prodname',
                                        'siyou',
                                        'ypics_qty',
                                        'vendor')
                                ->distinct()->get();
                    
                    $usage_diff = 'N/A';
                    foreach ($check as $key => $moms) {
                        $data = DB::connection($this->mysql)->table('momscheck2')
                                    ->where('po',$moms->po)
                                    ->where('code',$moms->code)
                                    ->where('kcode',$moms->kcode)
                                    ->where('prodname',$moms->prodname)
                                    ->where('siyou',$moms->siyou)
                                    ->where('ypics_qty',$moms->ypics_qty)
                                    ->where('vendor',$moms->vendor)
                                    ->first();
                        $dif1 = '';

                        if ($data->diff1==0) {
                            $dif1 = '0.0';
                        } else {
                            $dif1 = $data->diff1;
                        }

                        if ($data->diff1 == 'N/A') {
                            $dif1 = 'N/A';
                        } else {
                            $dif1 = $data->diff1;
                        }

                        $dif2 = '';

                        if ($data->diff2==0) {
                            $dif2 = '0.0';
                        } else {
                            $dif2 = $data->diff2;
                        }

                        if ($data->diff2 == 'N/A') {
                            $dif2 = 'N/A';
                        } else {
                            $dif2 = $data->diff2;
                        }

                        $sheet->cell('A'.$row, $data->po); //ypics
                        $sheet->cell('B'.$row, $data->code); //ypics
                        $sheet->cell('C'.$row, $data->kcode); //ypics
                        $sheet->cell('D'.$row, $data->prodname); //ypics
                        $sheet->cell('E'.$row, $this->xhikiLVL($data->po)); //ypics
                        $sheet->cell('F'.$row, $data->siyou); //ypics
                        $sheet->cell('G'.$row, $data->ypics_qty); //ypics
                        $sheet->cell('H'.$row, $data->vendor); //r3
                        $sheet->cell('I'.$row, $data->usage); //r3
                        $sheet->cell('J'.$row, $data->qty); //r3
                        $sheet->cell('K'.$row, $dif1);
                        $sheet->cell('L'.$row, "");
                        $sheet->cell('M'.$row, $data->moms);
                        $sheet->cell('N'.$row, (
                            $this->withdrawalQty($data->po,$data->kcode) == '')?$data->withdrawal_qty : $this->withdrawalQty($data->po,$data->kcode)
                        );
                        $sheet->cell('O'.$row, $dif2);

                        if ($data->usage == 'N/A') {
                            $usage_diff = 'N/A';
                        } else {
                            $usage_diff = $data->usage - $data->siyou;
                        }
                        $sheet->cell('P'.$row, ($usage_diff == 0)?"0.0":$usage_diff);

                        $row++;
                    }
                });

            })->download('xls');
        } catch (Exception $e) {
            return redirect(url('/orderdatacheck'))->with(['err_message' => $e]);
        }
    }

    private function withdrawalQty($po,$item)
    {
        $db = DB::connection($this->mysql)->table('momscheck2')
                ->select(
                    DB::raw("po as po"),
                    DB::raw("kcode as kcode"),
                    DB::raw("sum(ypics_qty) as ypics_qty")
                )
                ->where('po',$po)
                ->where('kcode',$item)
                ->groupBy('po','kcode')
                ->HavingRaw("count(*) > 1")
                ->first();
        if (count((array)$db) > 0) {
            return $db->ypics_qty;
        } else {
            return '';
        }
    }

    private function clean($string)
    {
        $spaces = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $dashes = str_replace('-', '', $spaces); // Replaces all hypens with none.
        $dots = str_replace('.', '', $dashes); // Replaces all dots with none.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $dots); // Removes special chars.
    }

    private function xhikiLVL($xhikipo)
    {
        $db = DB::connection($this->mysql)->table('momscheck')
                ->select('lvl')
                ->where('po',$xhikipo)
                ->first();
        if (count((array)$db) > 0) {
            return $db->lvl;
        } else {
            return 'N/A';
        }
    }

    public function getMomsCheckExcel()
    {
        // MOMs Check Report
        $this->momsCheck();
        $this->momscheck2();

        return $this->MomsCheckExcel();
    }


}
