@extends('layouts.master')

@section('title')
	QC Database | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_FGSDB'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach

	<div class="page-content">

		<div class="row">
			<div class="col-md-12">

				@include('includes.message-block')

				<div class="portlet box grey-gallery" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-line-chart"></i> FGS
						</div>
						<div class="actions">
							<div class="btn-group">
								<button type="button" class="btn green" id="btn_add">
									<i class="fa fa-plus"></i> Add New
								</button>
								<button type="button" class="btn blue" id="btn_groupby">
									<i class="fa fa-group"></i> Group By
								</button>
								<button type="button" class="btn red" id="btn_delete">
									<i class="fa fa-trash"></i> Delete
								</button>
								<button type="button" class="btn purple" id="btn_search">
									<i class="fa fa-search"></i> Search
								</button>
								{{-- <button type="button" class="btn yellow-gold" id="btn_pdf">
									<i class="fa fa-file-text-o"></i> Print to Pdf
								</button>
								<button type="button" class="btn green-jungle" id="btn_excel">
									<i class="fa fa-file-text-o"></i> Print to Excel
								</button> --}}
							</div>
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-hover table-bordered table-striped" id="tbl_fgs">
									<thead>
										<tr>
											<td width="4.28%" class="table-checkbox">
												<input type="checkbox" class="group-checkable checkAllitems" />
											</td>
											<td width="5.28%"></td>
											<td width="14.28%">Date Inspection</td>
											<td width="20.28%">P.O. #</td>
											<td width="27.28%">Series Name</td>
											<td width="14.28%">Quantity</td>
											<td width="14.28%">Total No. of Lots</td>

										</tr>
									</thead>
									<tbody id="tbl_fgs_body">
								
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<input class="form-control input-sm" type="hidden" value="" name="hd_report_status" id="hd_report_status"/>

					
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>


	@include('includes.fgs_modal')
	@include('includes.modals')
@endsection

@push('script')
<script type="text/javascript">
	var token = "{{ Session::token() }}";
	var FGSdataURL = "{{ url('/fgs-data') }}";
	var FGSdeleteURL = "{{ url('fgs-delete') }}";
	var FGSypicsURL = "{{ url('fgs-ypics-po') }}";
	var FGSsaveURL = "{{ url('fgs-save') }}";
	var FGSsearchURL = "{{ url('fgs-search') }}";
	var FGSgroupByURL = "{{ url('fgs-group-by') }}";
	var FGSreportDataCheckURL = "{{ url('fgs-report-data-check') }}";
	var FGSReportURL = "{{ url('fgs-report') }}";
</script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/fgs.js') }}" type="text/javascript"></script>
@endpush
