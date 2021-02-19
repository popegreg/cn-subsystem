<?php

namespace App\Http\Controllers\QCDB;

use Illuminate\Http\Request;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use Excel;
use DB;
use Carbon\Carbon;
use Datatables;

class IQCMatrixController extends Controller
{
    protected $mysql;
    protected $mssql;
    protected $common;
    protected $com;

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

    public function index()
    {
        if(!$this->com->getAccessRights(Config::get('constants.MODULE_CODE_MATRIX'), $userProgramAccess))
        {
            return redirect('/home');
        }
        else
        {
            return view('qcdb.iqc_matrix',['userProgramAccess' => $userProgramAccess]);
        }
    }

    public function store(Request $req)
    {
        $data = [
            'msg' => 'Saving failed.',
            'status' => 'failed'
        ];

        $rules = [
            'item' => 'required',
            'item_desc' => 'required',
            'classification' => 'required',
        ];

        $msg = [
            'item.required' => 'Item Code is required.',
            'item_desc.required' => 'Item Description is required.',
            'classification.required' => 'Classification is required.',
        ];

        $this->validate($req,$rules,$msg);

        if (empty($req->id)) {
            $matrix = DB::connection($this->mysql)->table('tbl_iqc_matrix')
                        ->insertGetId([
                            'item' => $req->item,
                            'item_desc' => $req->item_desc,
                            'classification' => $req->classification,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'create_user' => Auth::user()->user_id,
                            'update_user' => Auth::user()->user_id
                        ]);
        } else {
            $matrix = DB::connection($this->mysql)->table('tbl_iqc_matrix')
                        ->where('id',$req->id)
                        ->update([
                            'item' => $req->item,
                            'item_desc' => $req->item_desc,
                            'classification' => $req->classification,
                            'updated_at' => Carbon::now(),
                            'update_user' => Auth::user()->user_id
                        ]);
        }

        if ($matrix) {
            $data = [
                'msg' => 'Successfully saved.',
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function show()
    {
        $matrix = DB::connection($this->mysql)->table('tbl_iqc_matrix')
                    ->select([
                        'id',
                        'item',
                        'item_desc',
                        'classification',
                        'update_user',
                        DB::raw("DATE_FORMAT(updated_at, '%m/%d/%Y %h:%i %p') as updated_at")
                    ]);
        return Datatables::of($matrix)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->addColumn('action', function($data) {
                            return '<button class="btn btn-sm blue btn_edit" data-id="'.$data->id.'"
                                        data-item="'.$data->item.'" data-item_desc="'.$data->item_desc.'"
                                        data-classification="'.$data->classification.'">
                                        <i class="fa fa-edit"></i>
                                    </button>';
                        })
                        ->make(true);
    }

    public function destroy(Request $req)
    {
        $data = [
            'msg' => "Deleting failed.",
            'status' => 'failed'
        ];

        $deleted = false;
        foreach ($req->ids as $key => $id) {
            $deleted = DB::connection($this->mysql)->table('tbl_iqc_matrix')
                        ->where('id',$id)
                        ->delete();
        }

        if ($deleted) {
            $data = [
                'msg' => "Items were successfully deleted.",
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function getDetails(Request $req)
    {
        $field = '';

        switch ($req->field) {
            case 'item':
                $field = 'i.CODE';
                break;
            case 'item_desc':
                $field = 'h.NAME';
                break;
            default:
                $field = 'i.CODE';
                break;
        }

        $query = DB::connection($this->mssql)->table('XITEM as i')
                    ->leftJoin('XHEAD as h','i.CODE','=','h.CODE')
                    ->where($field,$req->value)
                    ->select(
                        DB::raw("i.CODE as item"),
                        DB::raw("h.NAME as item_desc"),
                        DB::raw("i.BUNR as classification")
                    )->first();
        if (count((array)$query) > 0) {
            return response()->json($query);
        }
    }

    public function showClassification()
    {
        $query = DB::connection($this->mssql)->table('XITEM')
                    ->select(DB::raw("BUNR as classification"))
                    ->whereIn('BUNR',[
                        'CONTACT',
                        'PROBE',
                        'METAL',
                        'MOLD'
                    ])
                    ->distinct()
                    ->get();
        return $query;
    }

    public function ExtractExcelFile(Request $req)
    {
        $file = $req->file('matrix_file');
        $fields;

        $data = [
            'msg' => "Uploading Failed.",
            'status' => 'failed'
        ];

        Excel::load($file, function($reader) use(&$fields){
            $fields = $reader->toArray();
            $data = [
                'msg' => "Data was successfully saved.",
                'status' => 'success'
            ];
        });

        foreach ($fields as $key => $field) {
            $this->saveToMatrix($field);
            $data = [
                'msg' => "Data was successfully saved.",
                'status' => 'success'
            ];
        }

        //return dd($fields);

        return response()->json($data);
    }

    private function saveToMatrix($data)
    {
        unset($data[0]);
        if ($this->checkItemIfExist($data) < 1) {
            DB::connection($this->mysql)->table('tbl_iqc_matrix')
                ->insertGetId([
                    'item' => $this->getItemCode($data['item'],$data['item_desc']),
                    'item_desc' => $this->getItemName($data['item_desc'],$data['item']),
                    'classification' => $this->getItemClass($data['classification'],$data['item'],$data['item_desc']),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'create_user' => Auth::user()->user_id,
                    'update_user' => Auth::user()->user_id
                ]);
        }
    }

    private function getItemCode($item,$item_desc)
    {
        if ($item === '' || $item === '-') {
            $ypics = DB::connection($this->mssql)->table('XHEAD')
                        ->select('CODE')
                        ->where('NAME',$item_desc)
                        ->first();
            if (count((array)$ypics) > 0) {
                return $ypics->CODE;
            }
        } else {
            return $item;
        }
    }

    private function getItemName($item_desc,$item)
    {
        if ($item_desc === '' || $item_desc === '-') {
            $ypics = DB::connection($this->mssql)->table('XHEAD')
                        ->select('NAME')
                        ->where('CODE',$item)
                        ->first();
            if (count((array)$ypics) > 0) {
                return $ypics->NAME;
            }
        } else {
            return $item_desc;
        }
    }

    private function getItemClass($class,$item,$item_desc)
    {
        if ($item !== '' || $item !== '-') {
            $ypics = DB::connection($this->mssql)->table('XITEM')
                        ->select('BUNR')
                        ->where('CODE',$item)
                        ->first();
            if (count((array)$ypics) > 0) {
                return $ypics->BUNR;
            }
        }

        if ($item_desc !== '' || $item_desc !== '-') {
            $ypics = DB::connection($this->mssql)->table('XHEAD as h')
                        ->leftJoin('XITEM as i','i.CODE','=','h.CODE')
                        ->select('i.BUNR')
                        ->where('i.CODE',$item)
                        ->first();
            if (count((array)$ypics) > 0) {
                return $ypics->BUNR;
            }
        }

        if ($class !== '') {
            return $class;
        }
    }

    private function checkItemIfExist($data)
    {
        $db = DB::connection($this->mysql)->table('tbl_iqc_matrix')
                ->where('item',$data['item'])
                ->where('item_desc',$data['item_desc'])
                ->where('classification',$data['classification'])
                ->count();
        return $db;
    }

    public function getExcelReport()
    {
        Excel::create('IQC_Matrix_Summary', function($excel)
            {
                $excel->sheet('Sheet1', function($sheet)
                {
                    $sheet->cell('A1', "Item Code");
                    $sheet->cell('B1', "Item Description");
                    $sheet->cell('C1', "Classification");
                    $sheet->cell('D1', "Updated By");
                    $sheet->cell('E1', "Update Date");

                    $row = 2;
                    $matrix = DB::connection($this->mysql)->table('tbl_iqc_matrix')->get();
                    foreach ($matrix as $key => $data) {
                        $sheet->cell('A'.$row, $data->item);
                        $sheet->cell('B'.$row, $data->item_desc);
                        $sheet->cell('C'.$row, $data->classification);
                        $sheet->cell('D'.$row, $data->update_user);
                        $sheet->cell('E'.$row, $data->updated_at);
                        $row++;
                    }

                });

            })->download('xls');
    }
}
