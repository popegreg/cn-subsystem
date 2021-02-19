@extends('layouts.master')

@section('title')
YPICS | Pricon Microelectronics, Inc.
@endsection


@section('content')

    <?php $state = ""; $readonly = ""; ?>
    @foreach ($userProgramAccess as $access)
        @if ($access->program_code == Config::get('constants.MODULE_CODE_DISPATCH'))
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

                <div class="portlet box blue" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-navicon"></i>  YPICS Dispatch
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-8">
                                <form class="form-horizontal" method="post" files="true" enctype="multipart/form-data" action="{{ url('/dispatch-readfile') }}" id="dispatch_form">
                                    <div class="form-group">
                                        {{ csrf_field() }}
                                        <label class="control-label col-md-2">Dispatch File</label>
                                        <div class="col-md-7">
                                            <input type="file" class="filestyle" data-buttonName="btn-primary" name="dispatch_file" id="dispatch_file" {{$readonly}}>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-md green" {{$state}}>
                                                <i class="fa fa-upload"></i> Upload File
                                            </button> <!-- type="submit" -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-offset-5 col-md-2">
                                            <a href="{{ url('/dispatch-excel') }}" id="btn_download" class="btn blue">Download excel File</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                                
                        </div>

                        <div class="row" id="dispatch-table">
                            <div class="col-md-12">
                                <table class="table table-striped" id="tbl_dispatch">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>PORDER</th>
                                            <th>CODE</th>
                                            <th>MOTO</th>
                                            <th>HOKAN</th>
                                            <th>SEIBAN</th>
                                            <th>BEDA</th>
                                            <th>KVOL</th>
                                            <th>NEED</th>
                                            <th>PICKDATE</th>
                                            <th>LOTNAME</th>
                                            <th>TSLIP_NUM</th>
                                            <th>NOTE</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_dispatch_body"></tbody>
                                </table>
                            </div>
                        </div>      
                    </div>
                </div>

            </div>
        </div>
    </div>


    @include('includes.modals')

@endsection
@push('script')
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        var dataColumn = [
            { data: 'id', name: 'id' },
            { data: 'porder', name: 'porder' },
            { data: 'code', name: 'code' },
            { data: 'moto', name: 'moto' },
            { data: 'hokan', name: 'hokan' },
            { data: 'seiban', name: 'seiban' },
            { data: 'beda', name: 'beda' },
            { data: 'kvol', name: 'kvol' },
            { data: 'need', name: 'need' },
            { data: 'pickdate', name: 'pickdate' },
            { data: 'lotname', name: 'lotname'},
            { data: 'tslip_num', name: 'tslip_num' },
            { data: 'note', name: 'note'},
        ];
        $(function() {
            init();
            $('#dispatch_form').on('submit', function(e) {
                var dispatchURL = $(this).attr("action");
                var data = new FormData(this);
                var fileName = $("#dispatch_file").val();
                var ext = fileName.split('.').pop();
                e.preventDefault(); //Prevent Default action.
                
                $('#loading').modal('show');
                if ($("#dispatch_file").val() == '') {
                    msg("Please select a valid Excel file.",'failed');
                }
                if (fileName != ''){
                    if (ext == 'xls' || ext == 'xlsx' || ext == 'XLS' || ext == 'XLSX') {
                        
                        $.ajax({
                            url: dispatchURL,
                            method: 'POST',
                            data:  data,
                            mimeType:"multipart/form-data",
                            contentType: false,
                            cache: false,
                            processData:false,
                        }).done( function(data, textStatus, jqXHR) {
                            $('#loading').modal('hide');
                            var output = jQuery.parseJSON(data);
                            console.log(output);
                            msg(output.msg,output.status);
                            init();
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            $('#loading').modal('hide');
                            msg("There was an error while processing.",'error');
                        });
                    } else {
                        $('#loading').modal('hide');
                        msg("Please select a valid Excel file.",'failed');
                    }
                } 
            });
        });
        function init() {
            getDatatable('tbl_dispatch',"{{ url('/dispatch-datatable') }}",dataColumn,[],0);
            $('#btn_download').hide();
            var data = {
                _token: "{{ Session::token() }}",
            }
            $.ajax({
                url: "{{ url('/dispatch-checkdata') }}",
                type: 'GET',
                dataType: 'JSON',
                data: data,
            }).done(function(data,textStatus,jqXHR) {
                if (data.status == 'success') {
                    $('#btn_download').show();
                }
            }).fail(function(data,textStatus,jqXHR) {
                msg("There was an error while checking.",'error');
            });
        } 
    </script>
@endpush