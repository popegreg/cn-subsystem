@extends('layouts.master')

@section('title')
	WBS | Pricon Microelectronics, Inc.
@endsection

@push('css')
    <style type="text/css">
        table.table-fixedheader {
            width: 100%;
        }
        table.table-fixedheader, table.table-fixedheader>thead, table.table-fixedheader>tbody, table.table-fixedheader>thead>tr, table.table-fixedheader>tbody>tr, table.table-fixedheader>thead>tr>td, table.table-fixedheader>tbody>td {
            display: block;
        }
        table.table-fixedheader>thead>tr:after, table.table-fixedheader>tbody>tr:after {
            content:' ';
            display: block;
            visibility: hidden;
            clear: both;
        }
        table.table-fixedheader>tbody {
            overflow-y: scroll;
            height: 200px;
        }
        table.table-fixedheader>thead {
            overflow-y: scroll;
        }
        table.table-fixedheader>thead::-webkit-scrollbar {
            background-color: inherit;
        }


        table.table-fixedheader>thead>tr>td:after, table.table-fixedheader>tbody>tr>td:after {
            content:' ';
            display: table-cell;
            visibility: hidden;
            clear: both;
        }

        table.table-fixedheader>thead tr td, table.table-fixedheader>tbody tr td {
            float: left;
            word-wrap:break-word;
            height: 40px;
        }
        .dataTables_scrollHeadInner{
            padding-left:0px!important;
        }
    </style>
@endpush

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_WBS'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach
    
	<div class="page-content">

		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				@include('includes.message-block')
				<div class="portlet box blue" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-navicon"></i>  WBS Local Material Receiving
						</div>
					</div>
        			<div class="portlet-body">
                        <div class="row">
                            <form action="" class="form-horizontal">
                            	<div class="col-md-4">
                            		<div class="form-group">
                            			<label class="control-label col-md-3">Control No.</label>
                                        <div class="col-md-9">
                                            <input type="hidden" class="form-control input-sm" id="loc_info_id" name="loc_info_id"/>
                                            <div class="input-group">
                                                <input type="text" class="form-control input-sm clear" id="controlno" name="controlno">

                                                <span class="input-group-btn">
                                                    <a href="javascript:navigate('first');" id="btn_min" class="btn blue input-sm"><i class="fa fa-fast-backward"></i></a>
                                                    <a href="javascript:navigate('prev');" id="btn_prv" class="btn blue input-sm"><i class="fa fa-backward"></i></a>
                                                    <a href="javascript:navigate('next');" id="btn_nxt" class="btn blue input-sm"><i class="fa fa-forward"></i></a>
                                                    <a href="javascript:navigate('last');" id="btn_max" class="btn blue input-sm"><i class="fa fa-fast-forward"></i></a>
                                                </span>
                                            </div>

                                            
                                        </div>
                            		</div>
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Invoice No.</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm clear" id="invoice_no" name="invoice_no">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Orig. Invoice</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm clear" id="orig_invoice" name="orig_invoice">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Invoice Date</label>
                                        <div class="col-md-9">
                                            <input class="form-control clear clearinv input-sm date-picker" data-date-format="yyyy-mm-dd" size="16" type="text" name="invoicedate" id="invoicedate" value="{{date('Y-m-d')}}"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3">Receive Date</label>
                                        <div class="col-md-9">
                                            <input class="form-control clear clearinv input-sm date-picker" data-date-format="yyyy-mm-dd" size="16" type="text" name="receivingdate" id="receivingdate" value="{{date('Y-m-d')}}"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Total Qty</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm clear" id="total" name="total" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                            		<div class="form-group">
                                        <label for="" class="control-label col-sm-3">Created By</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="clear form-control input-sm" id="create_user" name="create_user" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Created Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="clear form-control input-sm" id="created_at" name="created_at" readonly>
                                        </div>
                                    </div>
                            		<div class="form-group">
                            			<label for="" class="control-label col-sm-3">Updated By</label>
                            			<div class="col-sm-9">
                            				<input type="text" class="clear form-control input-sm" id="update_user" name="update_user" readonly>
                            			</div>
                            		</div>
                        			<div class="form-group">
                            			<label for="" class="control-label col-sm-3">Updated Date</label>
                            			<div class="col-sm-9">
                            				<input type="text" class="clear form-control input-sm" id="updated_at" name="updated_at" readonly>
                            			</div>
                            		</div>
                        		</div>
                            </form>
                        </div>

                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <form class="form-horizontal" method="POST" enctype="multipart/form-data" id="uploadbatchfiles" action="{{ url('/wbsuploadlocmat') }}">
                                   <div class="form-group">
                                        <label class="control-label col-sm-3">Upload Batch Items</label>
                                        <div class="col-sm-6">
                                            {{ csrf_field() }}
                                            <input type="file" class="filestyle" data-buttonName="btn-primary" name="localitems" id="localitems" {{$readonly}}>
                                            {{-- batchfiles --}}
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" id="btn_upload" class="btn btn-success" <?php echo($state); ?>>
                                                <i class="fa fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row">
                        	<div class="col-md-12">
                        		<table class="table table-hover table-bordered table-striped table-condensed no-footer" id="tbl_batch" style="font-size:10px;">
                                    <thead id="th_batch">
                                        <tr>
                                            <td class="table-checkbox" width="4.1%">
                                                <input type="checkbox" class="group-checkable"/>
                                            </td>
                                            <td width="5.1%"></td>
                                            <td width="7.1%">Code.</td>
                                            <td width="14.1%">Description</td>
                                            <td width="5.1%">Qty.</td>
                                            <td width="7.1%">Pckg. ctgry</td>
                                            <td width="7.1%">Pckg. Qty.</td>
                                            <td width="22.9%">Lot No.</td>
                                            <td width="7.1%">Location</td>
                                            <td width="7.1%">Supplier</td>
                                            <td width="6.1%">Not Reqd</td>
                                            <td width="4.1%">Printed</td>
                                            <td width="4.1%"></td>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_batch_body"></tbody>
                                </table>
                                <input type="hidden" name="save_type" id="save_type">
                        	</div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-sm green" id="btn_addDetails">
                                    <i class="fa fa-plus"></i> Add Batch Item
                                </button>
                                <button type="button" class="btn btn-sm red" id="btn_deleteDetails">
                                    <i class="fa fa-trash"></i> Delete Batch Item
                                </button>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                        	<div class="col-md-12 text-center">
                        		<button type="button" onclick="javascript:addstate();" class="btn btn-sm green" id="btn_add">
									<i class="fa fa-plus"></i> Add
								</button>
								<button type="button" onclick="javascript:editstate();" class="btn btn-sm blue" id="btn_edit">
									<i class="fa fa-pencil"></i> Edit
								</button>
								<button type="button" class="btn btn-sm green" id="btn_save">
									<i class="fa fa-floppy-o"></i> Save
								</button>
								<button type="button" onclick="javascript:getLocalMaterialData();" class="btn btn-sm red" id="btn_back">
									<i class="fa fa-times"></i> Back
								</button>
                                <button type="submit" class="btn grey-gallery input-sm" id="btn_print_iqc">
                                    <i class="fa fa-print"></i> Apply to IQC
                                </button>
								<button type="button" class="btn btn-sm green-jungle" id="btn_excel">
									<i class="fa fa-file-excel-o"></i> Export To Excel
								</button>

                                <button type="button" class="btn btn-sm blue" id="btn_search">
                                    <i class="fa fa-search"></i> Search
                                </button>

							</div>
                        </div>
        			</div>
                                
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

    @include('includes.localreceiving-modal')
    @include('includes.modals')
@endsection
@push('script')
    <script type="text/javascript">
        var savematlocURL = "{{ url('/savelocamat') }}";
        var user = "{{ Auth::user()->user_id }}";
        var datenow = "{{ date('Y-m-d') }}";
        var token = "{{ Session::token() }}";
        var LocalMaterialDataURL = "{{ url('/wbslocmatgetdata') }}";
        var LocalSummaryReport = "{{ url('/wbslocmatsummaryreport') }}";
        var localBarcodeURL = '{{url("/wbslocalprintbarcode")}}';
        var updateBatchItemURL = "{{ url('/wbslocupdatebatchitem') }}"
        var LocalIQCURL= "{{ url('/wbslociqc') }}";
        var getPackageCategoryURL= "{{ url('/wbslocpackagecategory') }}";
        var DeleteBatchItemURL = "{{ url('/wbslocaldeletebatchitem') }}";
        var getTotalURL = "{{ url('/wbslocgettotal') }}";
        var access_state = "{{ $pgaccess }}";
        var pcode = "{{ $pgcode }}";
    </script>
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/localreceiving.js') }}" type="text/javascript"></script>
@endpush