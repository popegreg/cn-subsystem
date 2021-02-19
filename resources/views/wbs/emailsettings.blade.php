
<?php
/*******************************************************************************
     Copyright (c) Company Nam All rights reserved.

     FILE NAME: emailsettings.blade.php
     MODULE NAME:  3021 : WBS - Email Notification Settings
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2017.03.29
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2017.03.29    AK.DELAROSA      Initial Draft
*******************************************************************************/
?>

@extends('layouts.master')

@section('title')
    WBS | Pricon Microelectronics, Inc.
@endsection


@section('content')

    <?php $state = ""; $readonly = ""; ?>
    @if ($pgaccess == "2")
        <?php $state = "disabled"; $readonly = "readonly"; ?>
    @endif
    
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                @include('includes.message-block')

                <div class="portlet box blue" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-navicon"></i>  WBS Email Notification Recipients
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-offset-1 col-md-10">
                                
                            </div>
                        </div>
                        <br>
						<div class="row">
                            <div class="col-md-offset-1 col-md-10">
                                <table id="inputtable" class="table table-bordered">
                                    <tbody id="inputbody"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-1 col-md-10">
                                <a href="javascript:;" id="add" class="btn input-sm btn-success pull-left" <?php echo $state; ?>>
                                    <i class="fa fa-plus"></i>
                                </a>
                                <a href="javascript:;" id="remove" class="btn input-sm btn-danger pull-left" <?php echo $state; ?>>
                                    <i class="fa fa-times"></i>
                                </a>
                                
                                <a href="javascript:;" id="btn_save" class="btn btn-primary input-sm pull-right" <?php echo $state; ?>>
                                    <i class="fa fa-floppy-o"></i> Save
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->

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

    <div id="deleteModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Recipients</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this recipient?</p>
                    <input type="hidden" name="delete_id" id="delete_id">
                </div>
                <div class="modal-footer">
                    <a href="javascript:deleteEmail();" class="btn btn-success">Yes</a>
                    <button type="button" data-dismiss="modal" class="btn btn-danger">No</button>
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
        var access_state = "{{ $pgaccess }}";
        var pcode = "{{ $pgcode }}";
        var token = "{{ Session::token() }}";

        $(document).ready(function() {
            loadSettings();

            $('#btn_clear').on('click', function() {
                clear();
                $('#sendto').tagsinput('removeAll');
            });

            $('#btn_save').on('click', function() {
                // var ok = checkValidation($('#sendto').val(),$('#subject').val(),$('#content').val());
                // if (ok) {
                    save();
                //}
            });

            $('#inputbody').on('click', '.delete_email', function() {
                $('#delete_id').val($(this).attr('data-id'));
                $('#deleteModal').modal('show');
            });

            $('#add').on('click', function() {
                
                $('#inputbody').append('<tr>'+
                                    '<td>Send To:</td>'+
                                    '<td>'+
                                        '<input type="email" class="form-control clear col-sm-12 input-sm" id="sendto" name="sendto[]" placeholder="Email Address">'+
                                    '</td>'+
                                    '<td>Full Name:</td>'+
                                    '<td>'+
                                        '<input type="text" class="form-control clear col-sm-12 input-sm" id="sendto_name" name="sendto_name[]">'+
                                    '</td>'+
                                    '<td>'+
                                        '<a href="javascript:;" class="delete_email btn input-sm btn-danger">'+
                                            '<i class="fa fa-trash"></i>'+
                                        '</a>'+
                                    '</td>'+
                                '</tr>');
            });

            $('#remove').on('click', function() {
                var table = document.getElementById('inputtable');
                var rowCount = table.rows.length;
                table.deleteRow(rowCount -1);
            });

        });

        function deleteEmail() {
            var token = "{{ Session::token() }}";
            var url = "{{ url('/wbsdemaildelete') }}";
            var data = {
                _token: token,
                id: $('#delete_id').val()
            }

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            }).done(function( data, textStatus, jqXHR) {
                $('#deleteModal').modal('hide');
                if(data.return_status == 'success') {
                    msg(data.msg,'success');
                    loadSettings();
                } else {
                    msg(data.msg,'failed');
                    loadSettings();
                }
            }).fail(function( data, textStatus, jqXHR) {
                msg("There's an error occurred while processing.",'failed');
            });
            
        }

        function loadSettings() {
            var data = {
                _token: token,
            }
            $('#inputbody').html('');
            $.ajax({
                url: "{{ url('/wbsemaildata') }}",
                type: 'get',
                dataType: 'JSON',
                data: data
            }).done( function(data, textStatus,jqXHR) {
                if (data.return_status == 'success') {
                    var x = data.details;
                    $.each(x, function(i, val) {
                        var disabled = 'disabled="true"'
                        if (parseInt(access_state) !== 2) {
                            disabled = '';
                        }
                        var inputs = '<tr class="hasvalue">'+
                                        '<td>Send To:</td>'+
                                        '<td>'+
                                            '<input type="hidden" id="id" name="id[]" value="'+val.id+'">'+
                                            '<input type="email" class="form-control clear col-sm-12 input-sm" id="sendto" name="sendto[]" placeholder="Email Address" value="'+val.sendto+'">'+
                                        '</td>'+
                                        '<td>Full Name:</td>'+
                                        '<td>'+
                                            '<input type="text" class="form-control clear col-sm-12 input-sm" id="sendto_name" name="sendto_name[]" value="'+val.sendto_name+'">'+
                                        '</td>'+
                                        '<td>'+
                                            '<a href="javascript:;" class="delete_email btn input-sm btn-danger" data-id="'+val.id+'"' +disabled+ '>'+
                                                '<i class="fa fa-trash"></i>'+
                                            '</a>'+
                                        '</td>'+
                                    '</tr>';
                        $('#inputbody').append(inputs);
                    });
                } else if (data.return_status == 'nodata') {
                    var inputs = '<tr>'+
                                    '<td>Send To:</td>'+
                                    '<td>'+
                                        '<input type="email" class="form-control clear col-sm-12 input-sm" id="sendto" name="sendto[]" placeholder="Email Address">'+
                                    '</td>'+
                                    '<td>Full Name:</td>'+
                                    '<td>'+
                                        '<input type="text" class="form-control clear col-sm-12 input-sm" id="sendto_name" name="sendto_name[]">'+
                                    '</td>'+
                                    '<td>'+
                                        '<a href="javascript:;" class="delete_email btn input-sm btn-danger">'+
                                            '<i class="fa fa-trash"></i>'+
                                        '</a>'+
                                    '</td>'+
                                '</tr>';
                    $('#inputbody').append(inputs);
                } else {
                    msg(data.msg,'failed');
                }
            }).fail( function(data, textStatus,jqXHR) {
                msg("There's an error occurred while processing.",'failed');
            });
        }

        function msg(msgs,status) {
            var title = '';
            if (status == 'failed') {
                title = '<i class="fa fa-exclamation-triangle"></i> Failed!';
            }

            if (status == 'warning') {
                title = '<i class="fa fa-exclamation-circle"></i> Warning!';
            }

            if (status == 'success') {
                title = '<i class="fa fa-check"></i> Success!';
            }

            $('#title').html(title);
            $('#err_msg').html(msgs);
            $('#msg').modal('show');
        }

        function save() {
            $('#loading').modal('show');
            var data = {
                _token: token,
                id: $('input[name="id[]"]').map(function(){return $(this).val();}).get(),
                sendto: $('input[name="sendto[]"]').map(function(){return $(this).val();}).get(),
                sendto_name: $('input[name="sendto_name[]"]').map(function(){return $(this).val();}).get()
            }

            $.ajax({
                url: "{{ url('/wbssaveemailsettings') }}",
                type: 'POST',
                dataType: 'JSON',
                data: data
            }).done( function(data,textStatus,jqXHR) {
                $('#loading').modal('hide');
                if (data.return_status == 'success') {
                    msg(data.msg,'success');
                    loadSettings();
                } else {
                    msg(data.msg,'failed');
                }
            }).fail( function(data,textStatus,jqXHR) {
                msg("There's an error occurred while processing.",'failed');
            });
            
        }

        function checkValidation(sendto,subject,content) {
            if (sendto == null || subject == null || content == null) {
                msg("Please fill out all input boxes.",'warning');
                return false;
            } else {
                return true;
            }
        }

        function clear() {
            $('.clear').val('');
        }
    </script>
@endpush
