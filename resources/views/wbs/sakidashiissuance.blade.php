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
        #hd_barcode{
        	position: absolute;
        	z-index: -1;
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
		<div class="row">
			<div class="col-md-12">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				@include('includes.message-block')

				<input type="text" id="hd_barcode" name="hd_barcode" />
				<div class="portlet box blue" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-navicon"></i>  WBS Sakidashi Issuance
						</div>
						<div class="actions">
							<div class="btn-group">
								<button type="button" class="btn yellow-gold input-sm" onclick="generateSiReport()" id="btn_print">
									<i class="fa fa-print"></i> Print Issuance Sheet
								</button>
								<button type="button" class="btn green-jungle input-sm" id="btn_print_excel">
									<i class="fa fa-print"></i> Export to Excel
								</button>
								<button type="button:;" id="btn_reasonlogs" class="btn btn-success input-sm">
				   					<i class="fa fa-file-o"></i> FIFO Reason Logs
				   				</button>
							</div>
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="row">
									<div class="form-horizontal">
										<div class="col-md-5">
											<div class="form-group">
												<label class="control-label col-md-3 input-sm">Issuance No.</label>
												<div class="col-md-9">
													<div class="input-group">
														<input type="text" class="form-control clear input-sm" id="issuancenosaki" name="issuancenosaki">

														<span class="input-group-btn">
															<button id="btn_min" onclick="nav('first')" class="btn blue input-sm"><i class="fa fa-fast-backward"></i></button>
															<button id="btn_prv" onclick="nav('prev')" class="btn blue input-sm"><i class="fa fa-backward"></i></button>
															<button id="btn_nxt" onclick="nav('nxt')" class="btn blue input-sm"><i class="fa fa-forward"></i></button>
															<button id="btn_max" onclick="nav('last')" class="btn blue input-sm"><i class="fa fa-fast-forward"></i></button>
														</span>
													</div>
													<input type="hidden" name="recid" id="recid">
												</div>
											</div>
											<form class="form-horizontal" method="POST" action="{{url('/sakidashi-issuance/searchpo')}}" id="searchPOform">
												<div class="form-group">
													{!! csrf_field() !!}
													<label class="control-label col-md-3 input-sm">PO No.</label>
													<div class="col-md-9">
														<div class="input-group">
															<input type="text" class="form-control clear input-sm" id="ponosaki" name="ponosaki">
															<span class="input-group-btn">
																<button type="submit" class="btn green input-sm" id="btn_ponosaki">
																	<i class="fa fa-arrow-circle-down"></i>
																</button>
															</span>
														</div>
														
													</div>
												</div>
											</form>
											<div class="form-group">
												<label class="control-label col-md-3 input-sm">Device Code</label>
												<div class="col-md-7">
													<input type="text" class="form-control clear input-sm" id="devicecodesaki" name="devicecodesaki" readonly>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-3 input-sm">Device Name</label>
												<div class="col-md-9">
													<input type="text" class="form-control clear input-sm" id="devicenamesaki" name="devicenamesaki" readonly>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-3 input-sm">PO Qty.</label>
												<div class="col-md-5">
													<input type="text" class="form-control clear input-sm" id="poqtysaki" name="poqtysaki" readonly>
												</div>
											</div>
										</div>

										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label col-md-4 input-sm">In-Charge</label>
												<div class="col-md-8">
													<input type="text" class="form-control clear input-sm" id="incharge" name="incharge">
												</div>
											</div>
											<div class="form-group" style="margin-bottom: 10px;">
												<label class="control-label col-md-4 input-sm">Remarks</label>
												<div class="col-md-8">
													<textarea class="form-control clear" style="resize:none;" id="remarks" name="remarks"></textarea>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-4 input-sm">Status</label>
												<div class="col-md-8">
													<input type="text" class="form-control clear input-sm" id="statussaki" name="statussaki" readonly>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-4 input-sm">Assessment:#</label>
												<div class="col-md-8">
													<input type="text" class="form-control clear input-sm" id="assessment" name="assessment" readonly>
												</div>
											</div>

										</div>

										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-4 input-sm">Created By</label>
												<div class="col-md-7">
													<input type="text" class="form-control clear input-sm" id="createdbysaki" name="createdbysaki" readonly>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-4 input-sm">Created Date</label>
												<div class="col-md-7">
													<input type="text" class="form-control clear input-sm" name="createddatesaki" id="createddatesaki" readonly/>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-4 input-sm">Updated By</label>
												<div class="col-md-7">
													<input type="text" class="form-control clear input-sm" id="updatedbysaki" name="updatedbysaki" readonly>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-4 input-sm">Updated Date</label>
												<div class="col-md-7">
													<input type="text" class="form-control clear input-sm" name="updateddatesaki" id="updateddatesaki" readonly/>
												</div>
											</div>
										</div>
									</div>
								</div>

								<hr>

								<div class="row">
									<div class="col-md-10 col-md-offset-1">
										
										<div class="row">
											<form class="form-horizontal">

												<div class="col-md-6">
													<div class="form-group">
														<label class="control-label col-md-3 input-sm">Part Code</label>
														<div class="col-md-9">
															<div class="input-group">
																<input type="text" class="form-control clear input-sm" id="partcode" name="partcode" readonly>
																<span class="input-group-btn">
																	<a href="javascript:;" id="btn_partcode" class="btn green input-sm">
																		<i class="fa fa-arrow-circle-down"></i>
																	</a>
																</span>
															</div>
														</div>
														<input type="hidden" class="form-control clear input-sm" id="hdnpartcode" name="hdnpartcode"/>
													</div>
													<div class="form-group">
														<label class="control-label col-md-3 input-sm">Part Name</label>
														<div class="col-md-9">
															<input type="text" class="form-control clear input-sm" id="partname" name="partname" readonly>
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-md-3 input-sm">Lot No.</label>
														<div class="col-md-9">
															<input type="text" class="form-control clear input-sm" id="lotno" name="lotno">
															<input type="hidden" class="clear" name="fifoid" id="fifoid">
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-md-3 input-sm">Pair No.</label>
														<div class="col-md-9">
															<input type="text" class="form-control clear input-sm" id="pairno" name="pairno">
															<textarea class="form-control iss_clear input-sm" id="iss_remarks" style="display:none;" name="iss_remarks"></textarea>
																<input type="hidden" class="form-control iss_clear input-sm" id="user" name="user">
																<input type="hidden" class="form-control iss_clear input-sm" id="id_reason" name="id_reason">
														</div>
													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group">
														<label class="control-label col-md-4 input-sm">Issue Qty</label>
														<div class="col-md-8">
															<input type="text" class="form-control clear input-sm" id="issueqty" name="issueqty">
															<input type="hidden" class="form-control clear input-sm" id="old_issueqty" name="old_issueqty">
															<input type="hidden" class="form-control clear input-sm" id="remaining_qty" name="remaining_qty">
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-md-4 input-sm">Required Qty.</label>
														<div class="col-md-8">
															<input type="text" class="form-control clear input-sm" id="reqqty" name="reqqty">
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-md-4 input-sm">Return Qty.</label>
														<div class="col-md-8">
															<input type="text" class="form-control clear input-sm" id="retqty" name="retqty" readonly>
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-md-4 input-sm">Sched Return Date</label>
														<div class="col-md-8">
															<input class="form-control clear date-picker input-sm" size="16" type="text" id="schedretdate" name="schedretdate" data-date-format="yyyy-mm-dd">
														</div>
													</div>
												</div>
											</form>
										</div>
										<div class="row">
											<div class="col-md-12">
												<table class="table table-striped table-bordered table-hover" style="font-size:10px" id="tbl_history">
													<thead>
														<tr>
															<td>Transaction No.</td>
															<td>Issued/Reel Qty.</td>
															<td>Required Qty.</td>
															<td>Returned Qty.</td>
															<td>Lot No.</td>
															<td>Pair No.</td>
															<td>Remarks</td>
															<td></td>
														</tr>
													</thead>
													<tbody id="tbl_history_body">
													</tbody>
												</table>
											</div>
										</div>

									</div>
								</div>


								<div class="row">
									<div class="col-md-12 text-center">
										<button type="button" class="btn green input-sm" onclick="setControl('ADD')" id="btn_add">
											<i class="fa fa-plus"></i> Add New
										</button>
										<button class="btn blue-madison input-sm" id="btn_save">
											<i class="fa fa-floppy-o"></i> Save
										</button>
										<button type="button" class="btn blue-madison input-sm" onclick="setControl('EDIT')" id="btn_edit">
											<i class="fa fa-pencil"></i> Edit
										</button>
										{{-- <button type="button" class="btn red input-sm" onclick="cancelTransaction()" id="btn_cancel">
											<i class="fa fa-trash"></i> Cancel Transaction
										</button> --}}

										<button type="button" class="btn red input-sm" onclick="cancelPO()" id="btn_cancel">
											<i class="fa fa-trash"></i> Cancel P.O.
										</button>

										<button type="button" onclick="javascript:setControl('DISCARD');" class="btn red-intense input-sm" id="btn_discard">
											<i class="fa fa-times"></i> Discard Changes
										</button>
										<button type="button" class="btn blue-steel input-sm" id="btn_search" >
											<i class="fa fa-search"></i> Search
										</button>
										
									</div>
								</div>
							</div>
						</div>

						<input type="hidden" name="brsense" id="brsense">


					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
	</div>


	@include('includes.sakidashi-modal')
	@include('includes.modals')
@endsection

@push('script')
	<script type="text/javascript">
		var saveIssuanceURL = "{{ url('/sakidashi-issuance/wbssisave') }}";
		var fifoReasonURL = "{{ url('/material-kitting/fiforeason') }}";
		var ExportToExcelURL = "{{ url('/sakidashi-issuance/export-to-excel')  }}";
		var issuanceSheetURL = "{{ url('/sakidashi-issuance/issuance-sheet') }}";
		var printBarcodeURL = "{{ url('/sakidashi-issuance/print-barcode') }}";
		var getTransCodeURL = "{{ url('/sakidashi-issuance/get-transcode') }}"
		var historyURL = "{{ url('/sakidashi-issuance/get-history') }}";
		var searchURL = "{{ url('/sakidashi-issuance/search') }}";
		var navigationURL = "{{ url('/sakidashi-issuance/navigate') }}";
		var cancelTransactionURL = "{{ url('/sakidashi-issuance/cancel-transaction') }}";
		var cancelPOURL = "{{ url('/sakidashi-issuance/cancel-po') }}";
		var fifoURL = "{{ url('/sakidashi-issuance/fifo') }}";
		var checkInPOURL = "{{ url('/sakidashi-issuance/checkinpo') }}";
		var checkInFifioURL = "{{ url('/sakidashi-issuance/checkinfifo') }}";
		var getLatestURL = "{{ url('/sakidashi-issuance/get-latest') }}";
		var getSakidashiDataURL = "{{ url('/sakidashi-issuance/get-sakidashi-data') }}";
		var token = "{{ Session::token() }}";
		var poDetailsURL = "{{ url('/sakidashi-issuance/get-po-details') }}";
		var CheckAuthorizedUserURL = "{{ url('/sakidashi-issuance/check-authorizeuser') }}";
		var access_state = "{{ $pgaccess }}";
		var pcode = "{{ $pgcode }}";
	</script>
	<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
	<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/sakidashiissuance.js') }}" type="text/javascript"></script>
@endpush