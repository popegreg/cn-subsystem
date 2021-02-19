
<?php
/*******************************************************************************
     Copyright (c) Company Nam All rights reserved.

     FILE NAME: iqc.blade.php
     MODULE NAME:  3006 : WBS - IQC Inspection
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2016.07.01
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.07.01    AK.DELAROSA      Initial Draft
     100-00-02   1     2016.07.27    MESPINOSA        IQC Inspection Implementation.
     200-00-01   1     2016.07.01    AK.DELAROSA      Version 2.0
*******************************************************************************/
?>

@extends('layouts.master')

@section('title')
WBS | Pricon Microelectronics, Inc.
@endsection


@section('content')

    <?php $state = ""; $readonly = ""; ?>
    @foreach ($userProgramAccess as $access)
        @if ($access->program_code == Config::get('constants.MODULE_CODE_IQCINS'))
            @if ($access->read_write == "2")
                <?php $state = "disabled"; $readonly = "readonly"; ?>
            @endif
        @endif
    @endforeach
    
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                @include('includes.message-block')

                <div class="portlet box blue" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-navicon"></i>  WBS IQC Inspection
                        </div>
                    </div>
                    <div class="portlet-body">
							<div class="row">
                                <div class="col-md-12">
                                	<div class="pull-right">
                                		<a href="javascript:;" id="searchbtn" class="btn btn-sm blue input-sm">
                                            <i class="fa fa-search"></i> Search
                                        </a>
                                		      <a href="javascript:;" id="statusbtn" class="btn btn-sm btn-success input-sm" <?php echo($state); ?> >
                                            <i class="fa fa-ellipsis-v"></i> Update Status Bulk
                                        </a>
                                	</div>
                                </div>
                            </div>
                                  <br/>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tbl_iqc" style="font-size:10px">
                                            <thead>
                                                <tr>
                                                    <td class="table-checkbox">
                                                        <input type="checkbox" id="chk_all" name="chk_all" class="group-checkable" />
                                                    </td>
                                                    <td>Item/Part No.</td>
                                                    <td>Item Description</td>
                                                    <td>Supplier</td>
                                                    <td>Quantity</td>
                                                    <td>Lot No.</td>
                                                    <td>Drawing No.</td>
                                                    <td>Receving No.</td>
                                                    <td>Invoice No.</td>
                                                    <td>Applied By</td>
                                                    <td>Date & Time Applied</td>
                                                    <td>Inspected By</td>
                                                    <td>Date & Time Inspected</td>
                                                    <td>Status</td>
                                                    <td>IQC Result</td>
                                                    <td>Edit Status</td>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_iqc_body"></tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->

            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>

    <div id="searchmodal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog gray-gallery">
            <div class="modal-content ">
            	<div class="modal-header">
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
	                    <div class="form-group">
	                    	<label class="control-label col-sm-3">From</label>
	                        <div class="col-sm-9">
	                            <input type="text" class="form-control input-sm datepicker" id="from" name="from" placeholder="From" data-date-format="yyyy-mm-dd" value="" autocomplete="off">
	                        </div>
	                    </div>
	                    <div class="form-group">
	                    	<label class="control-label col-sm-3">To</label>
	                        <div class="col-sm-9">
	                            <input type="text" class="form-control input-sm datepicker" id="to" name="to" placeholder="To" data-date-format="yyyy-mm-dd" value="" autocomplete="off">
	                        </div>
	                    </div>
	                    <div class="form-group">
	                        <label class="control-label col-sm-3">Receiving No.</label>
	                        <div class="col-sm-9">
	                            <input type="text" class="form-control input-sm" id="recno" name="recno">
	                        </div>
	                    </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Invoice No.</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" id="invoice_no" name="invoice_no">
                            </div>
                        </div>
	                    <div class="form-group">
	                        <label class="control-label col-sm-3">Status</label>
	                        <div class="col-sm-9">
	                            <select class="form-control input-sm" name="status" id="status">
                                    <option value=""></option>
	                                <option value="0">Pending</option>
	                                <option value="1">Accepted</option>
	                                <option value="2">Reject</option>
	                                <option value="3">On-going</option>
                                    <option value="4">Special Accept</option>
	                            </select>
	                        </div>
	                    </div>
	                    <div class="form-group">
	                        <label class="control-label col-sm-3">Item/Part No.</label>
	                        <div class="col-sm-9">
	                            <input type="text" class="form-control input-sm" id="itemno" name="itemno"/>
	                        </div>
	                    </div>
	                    <div class="form-group">
	                        <label class="control-label col-sm-3">Lot No.</label>
	                        <div class="col-sm-9">
	                            <input type="text" class="form-control input-sm" id="lotno" name="lotno">
	                        </div>
	                    </div>
                    </form>

                </div>
                <div class="modal-footer">
                	<a href="javascript:;" data-dismiss="modal" id="gobtn" class="btn btn-primary btn-sm">Go</a>
                	<button type="button" data-dismiss="modal" class="btn btn-danger btn-sm">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="statusModal" class="modal fade  " role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Update Status for IQC Inspection</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form  class="form-horizontal" id="statusmdl">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Status</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="statusup" id="statusup">
                                            <option value="1">Accepted</option>
                                            <option value="2">Reject</option>
                                            <option value="3" selected="selected">On-going</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Inspector</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="inspector" id="inspector" class="form-control input-sm" value="{{ Auth::user()->user_id }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Start Time</label>
                                    <div class="col-sm-8">
                                        <input type="text" data-format="hh:mm A" class="form-control required input-sm timepicker timepicker-no-seconds" name="start_time" id="start_time"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">IQC Result</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control input-sm" id="iqcresup" style="resize:none" name="iqcresup"  id="iqcresup" ></textarea>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="selectedid"/>
                    <a href="javascript:;" id="updateIQCstatusbtn" data-dismiss="modal" class="btn btn-success" <?php echo($state); ?>>OK</a>
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="bulkupdatemodal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Update Status for Bulk IQC Inspection</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form  class="form-horizontal">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Status</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="statusupbulk" id="statusupbulk">
                                            <option value="1">Accepted</option>
                                            <option value="2">Reject</option>
                                            <option value="3" selected="selected">On-going</option>
                                            <option value="4">Special Accept</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Inspector</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="inspectorbulk" id="inspectorbulk" class="form-control input-sm" value="{{ Auth::user()->user_id }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Start Time</label>
                                    <div class="col-sm-8">
                                        <input type="text" data-format="hh:mm A" class="form-control required input-sm timepicker timepicker-no-seconds" name="start_timebulk" id="start_timebulk"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">IQC Result</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control input-sm" id="iqcresupbulk" style="resize:none" name="iqcresupbulk" ></textarea>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <a href="javascript:;" id="updateIQCbulkbtn" data-dismiss="modal" class="btn btn-success" <?php echo($state); ?>>OK</a>
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-8">
                            <img src="{{ asset('public/assets/images/ajax-loader.gif') }}" class="img-responsive">
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
            loadforIQC("{{url('/getwbsiqc')}}"+"?status=0");
			$('.datepicker').datepicker({
				dateFormat: 'yy-mm-dd'
			});

			$('#searchbtn').on('click', function() {
				$('#searchmodal').modal('show');
			});

			$('#statusbtn').on('click', function() {
				$('#bulkupdatemodal').modal('show');
			});

			$('#tbl_iqc_body').on('click','.updatesinglebtn  <?php echo($state); ?>', function() {
				var id = $(this).attr('data-id');
				$('#selectedid').val(id);
				$('#statusModal').modal('show');
			});

			$('#gobtn').on('click', function() {
				searchstatus();
			});

			$('#updateIQCstatusbtn').on('click', function() {
				var url = '{{url("/postwbsiqcsingleupdate")}}';
	            var token = "{{ Session::token() }}";
	            var id = $('#selectedid').val();
	            var statusup = $('#statusup').val();
	            var iqcresup = $('#iqcresup').val();
                var inspector = $('#inspector').val();
                var start_time = $('#start_time').val();
				var data = {
	                _token: token,
	                id: id,
	                statusup: statusup,
                    inspector: inspector,
                    start_time: start_time,
	                iqcresup: iqcresup
	            };
	        	$.ajax({
	                url: url,
	                type: "POST",
	                data: data,
	            }).done( function(data, textStatus, jqXHR) {
                    console.log(data);
	            	loadforIQC("{{url('/getwbsiqc')}}"+"?status=0");
	            	isCheck($('#chk_all'))
	            }).fail( function(data, textStatus, jqXHR) {
	            	$('#loading').modal('hide');
	                $('#msg').modal('show');
	                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
	                $('#err_msg').html("There's some error while processing.");
	            });
			});

			$('.group-checkable').on('change', function(e) {
				$('input:checkbox.chk').not(this).prop('checked', this.checked);
			});

			$('#updateIQCbulkbtn').on('click', function() {
				var ids = getAllChecked();
				var url = '{{url("/postwbsiqcupdatebulk")}}';
	            var token = "{{ Session::token() }}";
	            var statusup = $('#statusupbulk').val();
	            var iqcresup = $('#iqcresupbulk').val();
                var inspector = $('#inspectorbulk').val();
                var start_time = $('#start_timebulk').val();
                var item = getAllCheckedItemCode();
                
				var data = {
	                _token: token,
	                id: ids,
	                statusup: statusup,
                    inspector: inspector,
                    start_time: start_time,
	                iqcresup: iqcresup,
                    item: item
	            };

	            if (ids.length > 0) {
	            	$.ajax({
		                url: url,
		                type: "POST",
		                data: data,
		            }).done( function(data, textStatus, jqXHR) {
                        console.log(data);
		            	loadforIQC("{{url('/getwbsiqc')}}"+"?status=0");
		            	isCheck($('#chk_all'))
		            }).fail( function(data, textStatus, jqXHR) {
		            	$('#loading').modal('hide');
		                $('#msg').modal('show');
		                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
		                $('#err_msg').html("There's some error while processing.");
		            });
	            } else {
	            	$('#loading').modal('hide');
	                $('#msg').modal('show');
	                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
	                $('#err_msg').html("Please check 2 or more checkboxes");
	            }
			});

		});

		function loadforIQC(url) {
			$('#tbl_iqc').dataTable().fnClearTable();
            $('#tbl_iqc').dataTable().fnDestroy();
            $('#tbl_iqc').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                ajax: url,
                lengthMenu: [
                    [5, 10, 20, 100, -1],
                    [5, 10, 20, 100,"All"]
                ],
                columns: [
                    {data: function(data){
                            return '<input type="checkbox" class="chk " value="'+data.id+'" data-id="'+data.id+'" data-code="'+data.item+'"/>';
                    },orderable: false, searchable:false, name:"id" },
                    { data: 'item', name: 'i.item' },
                    { data: 'item_desc', name: 'i.item_desc' },
                    { data: 'supplier', name: 'i.supplier' },
                    { data: 'qty', name: 'b.qty' },
                    { data: 'lot_no', name: 'i.lot_no' },
                    { data: 'drawing_num', name: 'i.drawing_num' },
                    { data: 'wbs_mr_id', name: 'i.wbs_mr_id' },
                    { data: 'invoice_no', name: 'i.invoice_no' },
                    { data: 'app_by', name: 'i.app_by' },
                    { data: 'app_date', name: 'i.app_date'},
                    { data: 'ins_by', name: 'i.ins_by' },
                    { data: 'ins_date', name: 'i.ins_date'},
                    { "data": function(data){
                        return data.iqc_status;
                    }, "name":"i.iqc_status" },
                    { data: 'iqc_result', name: 'i.iqc_result' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                aoColumnDefs: [
                    {
                        aTargets:[13], // You actual column with the string 'America'
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).css('font-weight', '700');
                            if(sData == "Accepted") {
                                $(nTd).css('background-color', '#c49f47');
                                $(nTd).css('color', '#fff');
                            }
                            if(sData == "Rejected") {
                                $(nTd).css('background-color', '#cb5a5e');
                                $(nTd).css('color', '#fff');
                            }
                            if(sData == "On-going") {
                                $(nTd).css('background-color', '#3598dc');
                                $(nTd).css('color', '#fff');
                            }

                            if(sData == "Special Accept") {
                                $(nTd).css('background-color', '#ff844c');
                                $(nTd).css('color', '#fff');
                            }
                        },      
                        defaultContent: '',
                        targets: '_all'
                    }
                ],
                order: [[0,'desc']]
            });
		}

		function searchstatus() {
            var token = "{{ Session::token() }}";
			loadforIQC('{{url("/getwbsiqcsearch")}}'+'?_token='+token+
                                                        '&&from='+$('#from').val()+
                                                        '&&to='+$('#to').val()+
                                                        '&&recno='+$('#recno').val()+
                                                        '&&status='+$('#status').val()+
                                                        '&&itemno='+$('#itemno').val()+
                                                        '&&lotno='+$('#lotno').val()+
                                                        '&&invoice_no='+$('#invoice_no').val());
		}

		function getAllChecked() {
			/* declare an checkbox array */
			var chkArray = [];

			/* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
			$(".chk:checked").each(function() {
				chkArray.push($(this).val());
			});

			return chkArray;
		}

        function getAllCheckedItemCode() {
            /* declare an checkbox array */
            var chkArray = [];

            /* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
            $(".chk:checked").each(function() {
                chkArray.push($(this).attr('data-code'));
            });

            return chkArray;
        }

		function isCheck(element) {
			if(element.is(':checked')) {
				element.parent('tr').removeAttr('checked');
				element.prop('checked',false)
			}
		}
	</script>
@endpush