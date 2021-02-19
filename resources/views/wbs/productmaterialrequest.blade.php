@extends('layouts.master')

@section('title')
	WBS | Pricon Microelectronics, Inc.
@endsection
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
		<div class="portlet box blue" >
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-navicon"></i>  WBS Production Material Request
				</div>
			</div>
			<div class="portlet-body">

				<div class="row">
            		<div class="form-horizontal" id="requestsummaryfrm">
            			<div class="col-md-5">
            				<div class="form-group row">
            					<label class="control-label col-md-3">Request No.</label>
            					<div class="col-md-9">

                                    <div class="input-group">
                						<input type="text" class="form-control clear input-sm" id="req_no" name="req_no">

                                        <span class="input-group-btn">
				   					 		<button type="button" id="btn_first" class="btn blue input-sm">
				   					 			<i class="fa fa-fast-backward"></i>
				   					 		</button>
                                            <button type="button" id="btn_prv" class="btn blue input-sm">
                                            	<i class="fa fa-backward"></i>
                                            </button>
                                            <button type="button" id="btn_nxt" class="btn blue input-sm">
                                            	<i class="fa fa-forward"></i>
                                            </button>
                                            <button type="button" id="btn_last" class="btn blue input-sm">
                                            	<i class="fa fa-fast-forward"></i>
                                            </button>
                                        </span>
                                    </div>
                                    
            					</div>
            				</div>
            				<div class="form-group row">
            					<label class="control-label col-md-3">PO No.</label>
            					<div class="col-md-7">
            						<form action="{{ url('/wbsprodmatrequest/search-po') }}" method="post" id="frm_search_po">
            							{{ csrf_field() }}
                					 	<div class="input-group">
                                            <input type="text" class="form-control clear input-sm" id="po" name="po" maxlength="15">

                                            <span class="input-group-btn">
                                                <button type="submit" class="btn green input-sm" id="btn_search_po">
                                                	<i class="fa fa-arrow-circle-down"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </form>

            					</div>
            				</div>
            				<div class="form-group row">
            					<label class="control-label col-md-3">Product Destination</label>
            					<div class="col-md-5">
                                    <select class="form-control clear input-sm" id="prod_destination" name="prod_destination"></select>
            					</div>
            				</div>
            				<div class="form-group row">
            					<label class="control-label col-md-3">Line Destination</label>
            					<div class="col-md-5">
                                    <select class="form-control clear input-sm" id="line_destination" name="line_destination" ></select>
            					</div>
            				</div>
            			</div>

            			<div class="col-md-3">
            				<div class="form-group row">
            					<label class="control-label col-md-4">Status</label>
            					<div class="col-md-8">
            						<input type="text" class="form-control clear input-sm" id="statuspmr" name="statuspmr" readonly>
            					</div>
            				</div>
            				<div class="form-group row">
            					<label class="control-label col-md-4">Remarks</label>
            					<div class="col-md-8">
            						<textarea class="form-control clear input-sm" style="resize:none;" id="remarkspmr"></textarea>
            					</div>
            				</div>
            			</div>

            			<div class="col-md-4">
            				<div class="form-group row">
            					<label class="control-label col-md-4">Created By</label>
            					<div class="col-md-8">
            						<input type="text" class="form-control clear input-sm" id="create_user" name="create_user" readonly>
            					</div>
            				</div>
            				<div class="form-group row">
            					<label class="control-label col-md-4">Created Date</label>
            					<div class="col-md-8">
            						<input class="form-control clear input-sm" type="text" name="created_at" id="created_at" readonly>
            					</div>
            				</div>
            				<div class="form-group row">
            					<label class="control-label col-md-4">Updated By</label>
            					<div class="col-md-8">
            						<input type="text" class="form-control clear input-sm" id="updated_by" name="updated_by" readonly>
            					</div>
            				</div>
            				<div class="form-group row">
            					<label class="control-label col-md-4">Updated Date</label>
            					<div class="col-md-8">
            						<input class="form-control clear input-sm" type="text" name="updated_at" id="updated_at" readonly>
            					</div>
            				</div>
            			</div>
					</div>
            	</div>


    			<div class="row">
					<div class="col-md-12">
						<table class="table table-striped table-bordered table-hover" style="font-size:10px" id="tbl_details">
            				 <thead>
            				 	<tr>
            						<td></td>
            						<td></td>
            						<td>Detail ID</td>
            						<td>Item/Part No.</td>
            						<td>Item Description</td>
            						<td>Classification</td>
            						<td>Issued Qty.(Kitting)</td>
            						<td>Request Qty.</td>
            						<td>Served Qty.</td>
            						<td>Requested By</td>
            						<td>Last Served By</td>
            						<td>Last Served Date</td>
            						<td>Remarks</td>
            						<td>Acknowledge By</td>
            					</tr>
            				</thead>
            				<tbody id="tbl_details_body"></tbody>
            			</table>

					</div>
				</div>

				<hr>

				<div class="row">
					<div class="col-md-12 text-center">
						<button type="button" id="btn_delete_details" class="btn btn-danger btn-sm">
							<i class="fa fa-trash"></i> Delete
						</button>
					</div>
				</div>

				<hr>


            	<div class="row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn green input-sm" id="btn_add_req" <?php echo($state); ?>>
                        	<i class="fa fa-plus"></i> Add New
                        </button>

                        <button type="button" class="btn blue-madison input-sm" id="btn_save_req" <?php echo($state); ?> >
                        	<i class="fa fa-pencil"></i> Save
                        </button>

                        <button type="button" class="btn blue-madison input-sm" id="btn_edit_req" <?php echo($state); ?> >
                        	<i class="fa fa-pencil"></i> Edit
                        </button>

                        <button type="button" class="btn red input-sm" id="btn_cancel_req" <?php echo($state); ?> >
                        	<i class="fa fa-trash"></i> Cancel
                        </button>

                        <button type="button" class="btn red-intense input-sm" id="btn_discard_req" <?php echo($state); ?> >
                        	<i class="fa fa-times"></i> Discard Changes
                        </button>

                        <button type="button" class="btn purple input-sm" id="btn_pdf_req">
                        	<i class="fa fa-file-pdf-o"></i> PDF
                        </button>

                        <button type="button" class="btn blue-steel input-sm" id="btn_search_req" >
                        	<i class="fa fa-search"></i> Search
                        </button>
                    </div>
            	</div>

			</div>
		</div>

		<input type="hidden" name="checkacknowledge" id="checkacknowledge">
	</div>

	@include('includes.productrequest-modal')
	@include('includes.modals')
@endsection
@push('script')
    <script type="text/javascript">
        var token = "{{ Session::token() }}";
        var SelectPODetailURL = "{{url('/wbsprodmatrequest/select-po-details')}}";
        var getSelectionsURL = "{{url('/wbsprodmatrequest/get-selections')}}";
        var user = "{{ Auth::user()->user_id }}";
        var saveURL = "{{url('/wbsprodmatrequest/save')}}";
        var getDataURL = "{{url('/wbsprodmatrequest/get-data')}}";
        var acknowledgeURL = "{{url('/wbsprodmatrequest/acknowledge')}}";
		var getPDFURL = "{{url('/wbsprodmatrequest/get-pdf')}}";
		var access_state = "{{ $pgaccess }}";
		var pcode = "{{ $pgcode }}";
    </script>
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/production_material_request.js') }}" type="text/javascript"></script>
@endpush