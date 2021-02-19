@extends('layouts.master')

@section('title')
    QC Permissions | Pricon Microelectronics, Inc.
@endsection

@section('content')

    <?php $state = ""; $readonly = ""; ?>
    @foreach ($userProgramAccess as $access)
        @if ($access->program_code == Config::get('constants.MODULE_CODE_QCPERM'))  <!-- Please update "2001" depending on the corresponding program_code -->
            @if ($access->read_write == "2")
            <?php $state = "disabled"; $readonly = "readonly"; ?>
            @endif
        @endif
    @endforeach

    <div class="page-content">

        <div class="row">
            <div class="col-md-12">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm" id="btn_addnew">
                        <i class="fa fa-plus"></i> Add New
                    </button>
                    <button type="button" class="btn red btn-sm" id="btn_delete">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        <hr>
        
        <div class="row">
            <div class="col-md-12" id="main_pane">

                <table class="table table-hover table-bordered table-striped table-condensed" id="tbl_qcpermission" style="font-size: 11px;">
                    <thead>
                        <tr>
                            <td></td>
                            <td>Last Name</td>
                            <td>First Name</td>
                            <td>Middle Name</td>
                            <td>User ID</td>
                            <td>Permission</td>
                            
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="col-md-12" id="group_by_pane"></div>
        </div>

    </div>

    @include('includes.qc_permission-modal')
    @include('includes.modals')

@endsection

@push('script')
<script type="text/javascript">
    var token = "{{ Session::token() }}";
    var author = "{{ Auth::user()->firstname }}";
    var UsersURL = "{{ url('/qc-permission-users') }}";
    var ProgramsURL = "{{ url('/qc-permission-programs') }}";
    var PermissionsURL = "{{ url('/qc-permission-list') }}";
    
</script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/qc_permission.js') }}" type="text/javascript"></script>
@endpush
