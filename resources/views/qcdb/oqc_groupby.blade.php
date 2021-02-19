@extends('layouts.master')

@section('title')
    QC Database | Pricon Microelectronics, Inc.
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
            /*height: 40px;*/
        }
    </style>
@endpush

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
                    <button type="button" class="btn blue" id="btn_groupby" onclick="javascript:GroupBy();">
                        <i class="fa fa-group"></i> Group By
                    </button>
                    <a href="javascript:;" class="btn purple" id="btn_search" onclick="javascript:Search();">
                        <i class="fa fa-search"></i> Search
                    </a>
                    <a href="javascript:;" class="btn yellow-gold" id="btn_report" onclick="javascript:Report();">
                        <i class="fa fa-file-text-o"></i> Reports
                    </a>
                    <a href="{{ url('/oqcinspection') }}" class="btn red" id="btn_back">
                        Back
                    </a>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12" id="main"></div>
        </div>

    </div>
    @include('includes.oqc_inspection-modal')
    @include('includes.modals')

@endsection
@push('script')
<script type="text/javascript">
    var token = "{{ Session::token() }}";
    var PDFReportURL = "{{ url('/oqc-pdf') }}";
    var ExcelReportURL = "{{ url('/oqc-excel') }}";
    var GroupByURL = "{{ url('/oqc-groupby-values') }}";
</script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/oqc_inspection_groupby.js') }}" type="text/javascript"></script>
@endpush
