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

		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				@include('includes.message-block')
				<div class="portlet box blue" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-navicon"></i>  WBS
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<form action="" class="form-horizontal">
									<div class="col-md-6">
										<div class="form-group row">

											<label class="control-label col-md-3">Transaction No.</label>
											<div class="col-md-9">
												<input type="hidden" class="form-control input-sm" id="disposition_id" name="disposition_id"/>
												<div class="input-group">
													<input type="text" class="form-control input-sm add" id="transaction_no" name="transaction_no">

													<span class="input-group-btn">
														<button type="button" onClick="navigate('first');" id="btn_min" class="btn blue input-sm"><i class="fa fa-fast-backward"></i></button>
														<button type="button" onClick="navigate('prev');" id="btn_prv" class="btn blue input-sm"><i class="fa fa-backward"></i></button>
														<button type="button" onClick="navigate('next');" id="btn_nxt" class="btn blue input-sm"><i class="fa fa-forward"></i></button>
														<button type="button" onClick="navigate('last');" id="btn_max" class="btn blue input-sm"><i class="fa fa-fast-forward"></i></button>
													</span>
												</div>													
											</div>
										</div>

										<div class="form-group">
											<label for="" class="control-label col-sm-3">Remarks</label>
											<div class="col-sm-9">
												<textarea cols="30" rows="5" class="form-control input-sm" style="resize:none;" name="remarks" id="remarks" ></textarea>
												{{-- <input type="text" class="form-control input-sm clear" id="disposition" name="disposition"> --}}
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="" class="control-label col-sm-3">Created By</label>
											<div class="col-sm-9">
												<input type="text" class="form-control input-sm" id="create_user" name="create_user" readonly>
											</div>
										</div>
										<div class="form-group">
											<label for="" class="control-label col-sm-3">Created Date</label>
											<div class="col-sm-9">
												<input type="text" class="form-control input-sm" id="created_at" name="created_at" readonly>
											</div>
										</div>
										<div class="form-group">
											<label for="" class="control-label col-sm-3">Updated By</label>
											<div class="col-sm-9">
												<input type="text" class="form-control input-sm" id="update_user" name="update_user" readonly>
											</div>
										</div>
										<div class="form-group">
											<label for="" class="control-label col-sm-3">Updated Date</label>
											<div class="col-sm-9">
												<input type="text" class="form-control input-sm" id="updated_at" name="updated_at" readonly>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="portlet box">
									<div class="portlet-body">

										<div class="row">
											<div class="col-md-12">
												<table class="table table-bordered table-hover table-striped table-responsive" id="tbl_disposition" style="font-size: 11px;">
												<thead>
													<tr>
														<td width="5%">
															<input type="checkbox" class="check_all_disposition">
														</td>
														<td width="5%"></td>
														<td>Item Code</td>
														<td>Item Name</td>
														<td>Qty</td>
														<td>Lot No.</td>
														<td>Expiration</td>
														<td>Disposition</td>
														<td>Remarks</td>
													</tr>
												</thead>
												<tbody id="tbl_disposition_body"></tbody>
											</table>
											</div>
										</div>

										<div class="row">
											<div class="col-md-12 text-center">
												<button type="button" class="btn btn-sm green" id="btn_add_details">
													<i class="fa fa-plus"></i> Add Details
												</button>
												<button type="button" class="btn btn-sm red" id="btn_remove_details">
													<i class="fa fa-times"></i> Remove Details
												</button>
											</div>
										</div>
											
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 text-center">
								<button type="button" class="btn btn-sm green" id="btn_add">
									<i class="fa fa-plus"></i> Add
								</button>
								<button type="button" class="btn btn-sm blue" id="btn_edit">
									<i class="fa fa-edit"></i> Edit
								</button>
								<button type="button" class="btn btn-sm blue" id="btn_save">
									<i class="fa fa-floppy-o"></i> Save
								</button>
								<button type="button" class="btn btn-sm red" id="btn_disregard">
									<i class="fa fa-thumbs-o-down"></i> Disregard
								</button>
								<button type="button" class="btn btn-sm purple" id="btn_excel">
									<i class="fa fa-print"></i> Export to Excel
								</button>
								<button type="button" class="btn btn-sm btn-primary" id="btn_search">
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

	@include('includes.material_disposition_modal')
    @include('includes.modals')

@endsection

@push('script')
	<script type="text/javascript">
		var token = "{{ Session::token() }}";
		var access_state = "{{ $access->read_write }}";
		var getDataDispositionURL =  "{{ url('get-data-disposition')}}";
		var getInventoryURL =  "{{ url('get-disposition-inventory')}}";
		var saveDispositionURL =  "{{ url('save-disposition')}}";
		var searchDispositionURL =  "{{ url('search-disposition')}}";
		var access_state = "{{ $pgaccess }}";
		var pcode = "{{ $pgcode }}";
    </script>
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/materialdisposition.js') }}" type="text/javascript"></script>
@endpush