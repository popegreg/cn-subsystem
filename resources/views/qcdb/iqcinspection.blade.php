@extends('layouts.master')

@section('title')
	QC Database | Pricon Microelectronics, Inc.
@endsection
@push('css')
	<link href="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/css/table-fixedheader.css')}}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')

	@include('includes.header')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_QCDB'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach
	<div class="page-content">

		<div class="row">
			<div class="col-md-12">
				<div class="btn-group pull-right">
					<a href="javascript:;" class="btn green" id="btn_upload">
                        <i class="fa fa-upload"></i> Upload Data
                    </a>
                    <a href="javascript:;" class="btn grey-gallery" id="btn_groupby">
                        <i class="fa fa-group"></i> Group By
                    </a>
                    
                    <a href="javascript:;" class="btn purple" id="btn_search">
                        <i class="fa fa-search"></i> Search
                    </a>
					<a href="javascript:;" class="btn yellow-gold" id="btn_pdf">
                        <i class="fa fa-file-text-o"></i> Export to Pdf
                    </a>
                    <a href="javascript:;" class="btn green-jungle" id="btn_excel">
                        <i class="fa fa-file-text-o"></i> Export to Excel
                    </a>

                    <a href="javascript:;" class="btn blue" id="btn_history">
                        <i class="fa fa-book"></i> Item History
                    </a>
				</div>
			</div>
		</div>
		<hr>
		<div class="row col-sm-offset-3">
			<div class="col-sm-3">
				<a href="javascript:;" class="btn green btn-block" id="btn_iqcresult">
					<i class="fa fa-search"></i> Inspection
				</a>
			</div>

			<div class="col-sm-3">
				<a href="javascript:;" class="btn green btn-block" id="btn_iqcresult_man">
					<i class="fa fa-search"></i> Manual Input
				</a>
			</div>

			<div class="col-sm-3">
				<a href="javascript:;" class="btn green btn-block" id="btn_requali">
					<i class="fa fa-history"></i> Re-qualification
				</a>
			</div>
		</div>

		<div class="row">
            <div class="col-sm-12" id="main_pane">
            	<div class="tabbable-custom">
                	<ul class="nav nav-tabs" role="tablist">
                		<li role="presentation"  class="active"><a href="#on-going" aria-controls="on-going" role="tab" data-toggle="tab">On-Going</a></li>
                        <li role="presentation"><a href="#inspected" aria-controls="inspected" role="tab" data-toggle="tab">Inspected</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="on-going">
                        	<table class="table table-hover table-bordered table-striped" id="on-going-inspection" style="font-size: 10px;">
                                <thead>
                                    <tr>
                                    	<td class="table-checkbox">
                                            <input type="checkbox" class="group-checkable ongoing_checkall" />
                                        </td>
                                        <td></td>
                                        <td>Invoice No.</td>
                                        <td>Inspector</td>
                                        <td>Inspection Date</td>
                                        <td>Inspection Time</td>
                                        <td>Application Ctrl No.</td>
                                        <td>FY#</td>
                                        <td>WW#</td>
                                        <td>Sub</td>
                                        <td>Part Code</td>
                                        <td>Part Name</td>
                                        <td>Supplier</td>
                                        <td>Lot No.</td>
                                        <td>AQL</td>
										<td>Judgement</td>
                                    </tr>
                                </thead>
                                <tbody id="tblforongoing">
                                </tbody>
                            </table>
                            <div class="row">
                            	<div class="col-md-12 text-center">
                            		<button type="button" class="btn red" id="btn_delete_ongoing">
                            			<i class="fa fa-trash"></i> Delete
                            		</button>
                            	</div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane" id="inspected">
                        	<div>
                        		<table class="table table-hover table-bordered table-striped table-condensed" id="iqcdatatable" style="font-size: 10px;">
                                    <thead>
                                        <tr>
                                        	<td class="table-checkbox">
                                                <input type="checkbox" class="group-checkable iqc_checkall" />
                                            </td>
                                            <td></td>
											<td>Judgement</td>
                                            <td>Invoice No.</td>
							                <td>Inspector</td>
							                <td>App. No.</td>
											<td>App. Date</td>
											<td>App time</td>
											<td>Date Inspected</td>
											<td>Inspection Time</td>
											<td>FY</td>
											<td>WW</td>
											<td>Submission</td>
											<td>Part Code</td>
											<td>Part Name</td>
											<td>Supplier</td>
											<td>Lot No.</td>
											<td>AQL</td>
											<td>Lot Qty</td>
											<td>Type of Inspection</td>
											<td>Severity of Inspection</td>
											<td>Inspection Level</td>
											<td>Accept</td>
											<td>Reject</td>
											<td>Shift</td>
											<td>Lot Inspected</td>
											<td>Lot Accepted</td>
											<td>Sample Size</td>
											<td>No. of Defects</td>
											<td>Classification</td>
											<td>Family</td>
											<td>Remarks</td>
										</tr>
                                    </thead>
                                    <tbody id="tblforiqcinspection">
                                    </tbody>
                                </table>
                        	</div>
                            <div class="row">
                            	<div class="col-md-12 text-center">
                            		<button type="button" class="btn red" id="btn_delete_inspected">
                            			<i class="fa fa-trash"></i> Delete
                            		</button>
                            	</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12" id="group_by_pane"></div>
        </div>
	</div>

	@include('includes.iqc_inspection_modal')
	@include('includes.modals')
@endsection

@push('script')
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/iqcinspection.js') }}" type="text/javascript"></script>

<script type="text/javascript">
	var token = "{{ Session::token() }}";
	var GroupByURL = "{{ url('/iqc-groupby-values') }}";
	var GetSingleGroupByURL = "{{ url('/iqc-groupby-dppmgroup1') }}";
    var GetdoubleGroupByURL = "{{ url('/iqc-groupby-dppmgroup2') }}";
    var GettripleGroupByURL = "{{ url('/iqc-groupby-dppmgroup3') }}";
    var GetdoubleGroupByURLdetails = "{{ url('/iqc-groupby-dppmgroup2_Details') }}";
    var GettripleGroupByURLdetails = "{{ url('/iqc-groupby-dppmgroup3_Details') }}";
	var pdfURL = "{{ url('/iqcprintreport?') }}";
	var excelURL = "{{ url('/iqcprintreportexcel')}}";
	var getShiftURL = "{{ url('/iqc-get-shift') }}";
	var getIQCworkWeekURL = "{{url('/iqcgetworkweek')}}";
	var getIQCInspectionURL = "{{url('/iqcdbgetiqcdata')}}";
	var getHistoryURL = "{{url('/iqcdbgethistory')}}";
	var getOnGoingURL = "{{url('/iqcdbgetongoing')}}";
	var saveInspectionURL = "{{url('/iqcsaveinspection')}}";
	var samplingPlanURL = "{{url('/iqcsamplingplan')}}";
	var getDropdownsURL = "{{url('/iqcgetdropdowns')}}";
	var getItemsURL = "{{url('/iqcdbgetitems')}}";
	var getItemDetailsURL = "{{url('/iqcdbgetitemdetails')}}";
	var calculateLotQtyURL = "{{url('/iqccalculatelotqty')}}";
	var saveModeOfDefectsURL ="{{url('/iqcdbsavemodeofdefects')}}";
	var getModeOfDefectsInspectionURL = "{{url('/iqcdbgetmodeofdefectsinspection')}}";
	var getPartcodeSearchURL = "{{url('/iqcdbgetitemsearch')}}";
	var searchItemInspectionURL="{{url('/iqcdbsearchinspection')}}";
	var getItemsRequalificationURL = "{{url('/iqcdbgetitemrequali')}}";
	var getAppNoURL = "{{url('/iqcdbgetappnorequali')}}";
	var getDetailsRequalificationURL = "{{url('/iqcdbgetdetailsrequali')}}";
	var calculateLotQtyRequalificationURL = "{{url('/iqccalculatelotqtyrequali')}}";
	var getVisualInspectionRequalificationURL = "{{url('/iqcdbvisualinspectionrequali')}}";
	var getDropdownsRequaliURL ="{{url('/iqcgetdropdownsrequali')}}";
	var saveRequalificationURL ="{{url('/iqcsaverequali')}}";
	var getRequalidataTableURL = "{{url('/iqcdbgetrequalidata')}}";
	var	getModeOfDefectsRequali = "{{url('/iqcdbgetmodeofdefectsrequali')}}";
	var saveModeOfDefectsRQURL = "{{url('/iqcdbsavemodeofdefectsrq')}}";
	var getGroupbyContentsURL = "{{url('iqcdbgroupbygetcontent')}}";
	var getGroupByTable = "{{url('/iqcdbgroupbytable')}}";
	var inspectionByDateURL = "{{url('/iqcdbinspectionbydate')}}";
	var deleteInspectionURL ="{{url('/iqcdbdeleteinspection')}}";
	var deleteRequaliURL = "{{url('/iqcdbdeleterequali')}}";
	var deleteModRQURL = "{{url('/iqcdbdeletemodeofdefectsrequali')}}";
	var deleteModInsURL = "{{url('/iqcdbdeletemodeofdefects')}}";
	var deleteOnGoingURL = "{{url('/iqcdbdeleteongoing')}}";
	var iqcSpecialAcceptURL = "{{url('/iqcspecialaccept')}}";
	var ReportDataCheckURL = "{{url('/iqc-report-check')}}";

</script>

<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/iqc_inspection_groupby.js') }}" type="text/javascript"></script>
@endpush
