@extends('layouts.master')

@section('title')
	WBS | Pricon Microelectronics, Inc.
@endsection

@push('css')
	<style type="text/css">
		.dataTables_scrollHeadInner {
			padding-left: 0px;
        }
	</style>
@endpush

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_WHSMATISS'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach
	
	<div class="page-content">
		@include('includes.message-block')
		<div class="portlet box blue" >
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-navicon"></i>  WBS Warehouse Material Issuance
				</div>
			</div>
			<div class="portlet-body">
				<div class="tabbable-custom">
					<ul class="nav nav-tabs nav-tabs-lg" id="tabslist" role="tablist">
						<li class="active" id="summary_tab">
							<a href="#summary_pane" data-toggle="tab" aria-expanded="true" id="summary_tab_toggle">Request Summary</a>
						</li>
						<li class="" id="issuance_tab">
							<a href="#issuance_pane" data-toggle="tab" aria-expanded="true" >Issuance</a>
						</li>
					</ul>

					<div class="tab-content" id="tab-subcontents">
						<div class="tab-pane fade in active" id="summary_pane">
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-bordered display nowrap" cellspacing="0" width="100%" style="font-size:10px" id="tbl_req_summary" >
											<thead>
												<tr>
													<td></td>
													<td>Transaction No.</td>
													<td>Date Created</td>
													<td>PO No.</td>
													<td>Product Destination</td>
													<td>Line Destination</td>
													<td>Status</td>
													<td>Requested By</td>
													<td>Last Served By</td>
													<td>Last Served Date</td>
												</tr>
											</thead>
											<tbody id="tbl_req_summary_body"></tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-bordered table-fixedheader table-striped" style="font-size:10px"  id="tbl_req_details">
											<thead>
												<tr>
													<td width="5%"></td>
													<td width="10%">Detail ID</td>
													<td width="10%">Item/Part No.</td>
													<td width="15%">Item Description</td>
													<td width="10%">Classification</td>
													<td width="10%">Issued Qty.(Kitting)</td>
													<td width="10%">Request Qty.</td>
													<td width="10%">Served Qty.</td>
													<td width="10%">Remarks</td>
													<td width="10%">Served Date</td>
												</tr>
											</thead>
											<tbody id="tbl_req_details_body"></tbody>
										</table>
									</div>

									<br>
									<div class="col-md-12 text-center">
										<button type="button" class="btn green btn-sm" id="btn_make_issuance" disabled="true">
											<i class="fa fa-plus"></i> Add
										</button>
									</div>
								</div>
							</div>

						</div>

						<div class="tab-pane" id="issuance_pane">
							<div class="row">
								<div class="col-md-5">
									<div class="form-group row">
										<label class="control-label col-md-3">Issuance No.</label>
										<div class="col-md-9">
											<div class="input-group">
												<input type="hidden" id="id" class="clear" name="id">
												<input type="text" class="form-control input-sm clear" id="issuance_no" name="issuance_no">

												<span class="input-group-btn">
													<button type="button" id="btn_first" class="btn blue input-sm"><i class="fa fa-fast-backward"></i></button>
													<button type="button" id="btn_prv" class="btn blue input-sm"><i class="fa fa-backward"></i></button>
													<button type="button" id="btn_nxt" class="btn blue input-sm"><i class="fa fa-forward"></i></button>
													<button type="button" id="btn_last" class="btn blue input-sm"><i class="fa fa-fast-forward"></i></button>
												</span>
											</div>

											
										</div>
									</div>
									<div class="form-group row">
										<label class="control-label col-md-3">Request No.</label>
										<div class="col-md-8">
											<input type="text" class="form-control input-sm clear" id="req_no" name="req_no">
										</div>
									</div>
									<div class="form-group row">
										<label class="control-label col-md-3">Status</label>
										<div class="col-md-8">
											<input type="text" class="form-control input-sm clear" id="status" name="status">
										</div>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group row">
										<label class="control-label col-md-5">Created By</label>
										<div class="col-md-7">
											<input type="text" class="form-control input-sm clear" id="created_by" name="created_by">
										</div>
									</div>
									<div class="form-group row">
										<label class="control-label col-md-5">Created Date</label>
										<div class="col-md-7">
											<input class="form-control input-sm clear"  type="text" name="created_at" id="created_at"/>
										</div>
									</div>
									<div class="form-group row">
										<label class="control-label col-md-5">Total Request Qty</label>
										<div class="col-md-7">
											<input type="text" class="form-control input-sm clear" id="total_req_qty" name="total_req_qty">
										</div>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group row">
										<label class="control-label col-md-4">Updated By</label>
										<div class="col-md-8">
											<input type="text" class="form-control input-sm clear" id="updated_by" name="updated_by">
										</div>
									</div>
									<div class="form-group row">
										<label class="control-label col-md-4">Updated Date</label>
										<div class="col-md-8">
											<input class="form-control input-sm clear" type="text" name="updated_at" id="updated_at"/>
										</div>
									</div>

									<div class="form-group row">
										<label class="control-label col-md-4">Total Balance Qty</label>
										<div class="col-md-8">
											<input type="text" class="form-control input-sm clear" id="total_bal_qty" name="total_bal_qty">
										</div>
									</div>
								</div>
							</div>

							<hr>

							<div class="row">
								<div class="col-md-12">
									<table class="table table-bordered table-striped" style="font-size:10px" id="tbl_issuance">
										<thead>
											<tr>
												<td></td>
												<td></td>
												<td>Detail ID</td>
												<td>Item/Part No.</td>
												<td>Item Description</td>
												<td>Issued Qty.(Others)</td>
												<td>Issued Qty.(This)</td>
												<td>Lot No.</td>
											{{-- 	<td>Remarks</td> --}}
												<td>Location</td>
												<td></td>
											</tr>
										</thead>
										<tbody id="tbl_issuance_body"></tbody>
									</table>
								</div>
							</div>


							<div class="row">
								<div class="col-md-12 text-center">
									<button type="button" class="btn blue-madison input-sm" id="btn_save" <?php echo($state); ?> >
										<i class="fa fa-floppy-o"></i> Save
									</button>
									<button type="button" class="btn blue-madison input-sm" id="btn_edit" <?php echo($state); ?> >
										<i class="fa fa-pencil"></i> Edit
									</button>
									<button type="button" class="btn red input-sm" id="btn_cancel" <?php echo($state); ?> >
										<i class="fa fa-trash"></i> Cancel
									</button>
									<button type="button" class="btn red-intense input-sm" id="btn_discard" <?php echo($state); ?> >
										<i class="fa fa-times"></i> Discard Changes
									</button>
									<button type="button" class="btn blue-steel input-sm" id="btn_search" >
										<i class="fa fa-search"></i> Search
									</button>

									<button type="button" id="btn_report_excel" class="btn green input-sm">
										<i class="fa fa-file-excel-o"></i> Excel
									</button>

									<button type="button" id="btn_report_pdf" class="btn input-sm purple">
										<i class="fa fa-file-pdf-o"></i> PDF
									</button>
								</div>
							</div>


						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	@include('includes.materialissuance-modal')
	@include('includes.modals')
@endsection

@push('script')
<script type="text/javascript">
	var token = "{{Session::token()}}";
	var getPedingRequestURL = "{{ url('/whs-issuance/pending-requests') }}";
	var viewReqDetailsURL = "{{ url('/whs-issuance/view-details') }}";
	var getRequestDetails = "{{ url('/whs-issuance/request-details') }}";
	var getInventoryURL = "{{ url('/whs-issuance/get-inventory') }}";
	var saveURL = "{{ url('/whs-issuance/save') }}";
	var getDataURL = "{{ url('/whs-issuance/get-data') }}";
	var excelPDF = "{{ url('/whs-issuance/get-excel') }}";
	var pdfURL = "{{ url('/whs-issuance/get-pdf') }}";
	var printBarCodeURL = "{{ url('/whs-issuance/print-barcode') }}";
	var downloadBarCodeURL = "{{ url('/whs-issuance/download-barcode') }}";
	var AuthorizedURL =  "{{ url('/whs-issuance/autorized-user') }}"; 
	var SaveFifoURL = "{{ url('/whs-issuance/save-fifo') }}";
	var access_state = "{{ $pgaccess }}";
    var pcode = "{{ $pgcode }}";
</script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/whsissuance.js') }}" type="text/javascript"></script>
@endpush
