@extends('layouts.master')

@section('title')
YPICS | Pricon Microelectronics, Inc.
@endsection


@section('content')

    <?php $state = ""; $readonly = ""; ?>
    @foreach ($userProgramAccess as $access)
        @if ($access->program_code == Config::get('constants.MODULE_CODE_XHIKI'))
            @if ($access->read_write == "2")
                <?php $state = "disabled"; $readonly = "readonly"; ?>
            @endif
        @endif
    @endforeach
    
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                @include('includes.message-block')

                <div class="portlet box blue" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-navicon"></i>  Withdrawal Detail
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form class="form-horizontal" method="post" files="true" enctype="multipart/form-data" action="{{ url('/xhiki-readfile') }}" id="xhiki_form">
                                    <div class="form-group">
                                        {{ csrf_field() }}
                                        <label class="control-label col-md-2">TXHIKI FILE</label>
                                        <div class="col-md-7">
                                            <input type="file" class="filestyle" data-buttonName="btn-primary" name="xhiki_file" id="xhiki_file" {{$readonly}}>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-md green" {{$state}}>
                                                <i class="fa fa-upload"></i> Upload File
                                            </button> <!-- type="submit" -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-offset-5 col-md-2">
                                            <a href="{{ url('/xhiki-excel') }}" id="btn_download" class="btn blue">Download excel File</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                                
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>


    <div id="loading" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-8">
                            <img src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/images/ajax-loader.gif') }}" class="img-responsive">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="msg" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 id="title" class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p id="err_msg"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')
    <script type="text/javascript">
        $(function() {
            init();
            $('#xhiki_form').on('submit', function(e) {
                var xhikiURL = $(this).attr("action");
                var data = new FormData(this);
                var fileName = $("#xhiki_file").val();
                var ext = fileName.split('.').pop();
                e.preventDefault(); //Prevent Default action.
                
                $('#loading').modal('show');
                if ($("#xhiki_file").val() == '') {
                    $.alert('Please select a valid Excel file.', {
                        position: ['center', [-0.42, 0]],
                        type: 'danger',
                        closeTime: 3000,
                        autoClose: true
                    });
                    $('#loading').modal('hide');
                }
                if (fileName != ''){
                    if (ext == 'xls' || ext == 'xlsx' || ext == 'XLS' || ext == 'XLSX') {
                        
                        $.ajax({
                            url: xhikiURL,
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
                            if (output.status == 'success') {
                                $('#title').html("SUCCESS");
                            } else {
                                $('#title').html("Withdrawal Detail");
                            }
                            $('#err_msg').html(output.msg);
                            $('#msg').modal('show');
                            init();
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            $('#loading').modal('hide');
                            $.alert('<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong> An Error Occur.', {
                                position: ['center', [-0.42, 0]],
                                type: 'danger',
                                closeTime: 3000,
                                autoClose: true
                            });
                        });
                    } else {
                        $.alert('<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong> Please select a valid Excel file.', {
                            position: ['center', [-0.42, 0]],
                            type: 'danger',
                            closeTime: 3000,
                            autoClose: true
                        });
                        $('#loading').modal('hide');
                    }
                } 
            });
        });
        function init() {
            $('#btn_download').hide();
            var data = {
                _token: "{{ Session::token() }}",
            }
            $.ajax({
                url: "{{ url('/xhiki-checkdata') }}",
                type: 'GET',
                dataType: 'JSON',
                data: data,
            }).done(function(data,textStatus,jqXHR) {
                if (data.status == 'success') {
                    $('#btn_download').show();
                }
            }).fail(function(data,textStatus,jqXHR) {
                $('#title').html("ERROR");
                $('#err_msg').html("There was an error while checking.");
                $('#msg').modal('show');
            });
        } 
    </script>
@endpush