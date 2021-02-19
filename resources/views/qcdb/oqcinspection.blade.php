@extends('layouts.master')

@section('title')
    QC Database | Pricon Microelectronics, Inc.
@endsection

@section('content')

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
                    <a href="javascript:;" class="btn green" id="btn_addnew" onclick="javascript:NewInspection();">
                        <i class="fa fa-plus"></i> Add New
                    </a>
                    <button type="button" class="btn blue" id="btn_groupby" onclick="javascript:GroupBy();">
                        <i class="fa fa-group"></i> Group By
                    </button>
                    <button type="button" class="btn red" id="btn_delete" onclick="javascript:DeleteInspection();">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                    <a href="javascript:;" class="btn purple" id="btn_search" onclick="javascript:Search();">
                        <i class="fa fa-search"></i> Search
                    </a>
                    {{-- <a href="javascript:;" class="btn yellow-gold" id="btn_report" onclick="javascript:Report();">
                        <i class="fa fa-file-text-o"></i> Reports
                    </a> --}}
                </div>
            </div>
        </div>
        <hr>
        
        <div class="row">
            <div class="col-md-12" id="main_pane">

                <table class="table table-hover table-bordered table-striped" id="tbl_oqc" style="font-size: 11px;">
                    <thead>
                        <tr>
                            <td class="table-checkbox">
                                <input type="checkbox" class="group-checkable" />
                            </td>
                            <td width="5%"></td>
                            <td>FY-WW</td>
                            <td>Date Inspected</td>
                            <td>Device Name</td>
                            <td>From</td>
                            <td>To</td>
                            <td># of Sub</td>
                            <td>Lot Size</td>
                            <td>Sample Size</td>
                            <td>No of Defective</td>
                            <td>Lot No</td>
                            <td>Mode of Defects</td>
                            <td>Qty</td>
                            <td>Judgement</td>
                            <td>Inspector</td>
                            <td>Remarks</td>
                            <td>Type</td>
                        </tr>
                    </thead>
                    <tbody id="tbl_oqc_body">

                    </tbody>
                </table>
            </div>

            <div class="col-md-12" id="group_by_pane"></div>
        </div>

    </div>

    @include('includes.oqc_inspection-modal')
    @include('includes.modals')

@endsection

@push('script')
<script type="text/javascript">
    var token = "{{ Session::token() }}";
    var author = "{{ Auth::user()->firstname }}";
    var loadSelectInputURL = "{{ url('/oqc-initiatedata') }}";
    var getWorkWeekURL = "{{ url('/oqc-workweek') }}";
    var getPOdetailsURL = "{{ url('/getpodetails') }}";
    var oqcDataTableURL = "{{ url('/oqc-datatable') }}";
    var DeleteInspectionURL = "{{ url('/oqc-delete-inspection') }}";
    var modDataTableURL = "{{ url('/oqc-mod-datatable') }}";
    var DeleteModeOfDefectsURL = "{{ url('/oqc-delete-mod') }}";
    var PDFReportURL = "{{ url('/oqc-pdf') }}";
    var ExcelReportURL = "{{ url('/oqc-excel') }}";
    var GroupByURL = "{{ url('/oqc-groupby-values') }}";
    var GetProbeProductURL = "{{ url('/getprobeproduct') }}";
    var SamplingPlanURL = "{{ url('/get-sampling-plan') }}";
    var getNumOfDefectivesURL  = "{{url('/oqc-num-of-defects')}}";
    var getShiftURL = "{{ url('/oqc-shift') }}";
    var PDFGroupByReportURL = "{{ url('/oqc-groupby-pdf') }}";
    var ExcelGroupByReportURL = "{{ url('/oqc-groupby-excel') }}";
    var GetSingleGroupByURL = "{{ url('/oqc-groupby-dppmgroup1') }}";
    var GetdoubleGroupByURL = "{{ url('/oqc-groupby-dppmgroup2') }}";
    var GettripleGroupByURL = "{{ url('/oqc-groupby-dppmgroup3') }}";
    var GetdoubleGroupByURLdetails = "{{ url('/oqc-groupby-dppmgroup2_Details') }}";
    var GettripleGroupByURLdetails = "{{ url('/oqc-groupby-dppmgroup3_Details') }}";

    
</script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/oqc_inspection.js') }}" type="text/javascript"></script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/oqc_inspection_groupby.js') }}" type="text/javascript"></script>
@endpush
