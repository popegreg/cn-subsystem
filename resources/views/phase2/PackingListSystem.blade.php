@extends('layouts.master')

@section('title')
	Packing List System | Pricon Microelectronics, Inc.
@endsection

@section('content')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_PLSYSTEM'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-bars"></i>  PACKING LIST SYSTEM
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
							<div class="col-md-12">

								<div class="row">
									<div class="col-sm-12">
										<form class="form-inline">
											{!! csrf_field() !!}
	                                        <div class="form-group">
												<label for="inputcode" class="col-sm-3 control-label">Invoice Date</label>
	                                            <div class="col-sm-3 col-xs-4">
	                                                <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy">
	                                                    <input type="text" class="form-control input-sm" name="srch_from" id="srch_from" value="<?php if(isset($srchfrom)){ echo $srchfrom;} ?>"/>
	                                                    <span class="input-group-addon input-sm">to </span>
	                                                    <input type="text" class="form-control input-sm" name="srch_to" id="srch_to" value="<?php if(isset($srchto)){ echo $srchto;} ?>"/>
	                                                </div>
	                                            </div>
	                                            
	                                        </div>

	                                        <div class="form-group">
	                                        	<div class="col-sm-1">
													<a href="javascript:search();" class="btn btn-primary"><i class="fa fa-filter"></i>view</a>
												</div>
	                                        </div>
										</form>
									</div>
								</div>


								<div class="row">
									<div class="col-sm-12">
										<table class="table table-striped table-bordered table-hover" id="tbl_packinglist" style="font-size:10px">
											<thead>
												<tr>
													<td width="1.69%"></td>
													<td width="7.69%">CTR #</td>
													<td width="3.69%">Invoice Date</td>
													<td width="10.69%">Remarks</td>
													<td width="10.69%">Sold To</td>
													<td width="12.69%">Ship To</td>
													<td width="7.69%">Carrier</td>
													<td width="4.69%">Date Ship</td>
													<td width="7.69%">Port of Loading</td>
													<td width="7.69%">Port of Destination</td>
													<td width="9.69%">Shipping Instruction</td>
													<td width="7.69%">Case Marks</td>
													<td width="7.69%">Note</td>
												</tr>
											</thead>
											<tbody id="tbl_packinglist_body"></tbody>
										</table>
									</div>
								</div>

								<div class="row">
									<div class="col-md-offset-4 col-sm-1" style="width:80px">
										<a href="#" onclick="javascript: addDetails();" class="btn green" id="addDetails" <?php echo($state); ?> >
											<i class="fa fa-plus"></i> Add
										</a>
									</div>

									<div class="col-sm-1" style="width:80px">
										<button type="button" onclick="javascript:actionRecords('EDIT');" class="btn blue-madison">
											<i class="fa fa-pencil"></i> Edit
										</button>
									</div>

									<div class="col-sm-1" style="width:95px">
										<button type="button" onclick="javascript:actionRecords('DEL');" class="btn btn-danger" <?php echo($state); ?> >
											<i class="fa fa-trash-o"></i> DELETE</button>
									</div>

									<div class="col-sm-1" style="width:90px">
											<form class="form-horizontal" role="form" method="POST" action="{{ url('/packinglistsystem-exportxls') }}">
												{!! csrf_field() !!}
												<input type="hidden" name="from" id="dateFromXls"/>
												<input type="hidden" name="to" id="dateToXls"/>
												<button type="submit" onclick="setDate();" class="btn purple-plum" <?php echo($state); ?> ><i class="fa fa-file-excel-o"></i> Excel</button>
											</form>
									</div>

									<div class="col-sm-1" style="width:80px">
										<form class="form-horizontal" role="form" method="POST" action="{{ url('/packinglistsystem-exportpdf') }}" target="_blank">
											{!! csrf_field() !!}
												<input type="hidden" name="from" id="dateFromPdf"/>
												<input type="hidden" name="to" id="dateToPdf"/>
											<button type="submit" onclick="setDate();" formtarget="_blank" class="btn purple-plum" <?php echo($state); ?> >
												<i class="fa fa-file-pdf-o"></i> PDF
											</button>
										</form>
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
	

	<div id="deleteModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm blue">
			<form role="form" method="POST" action="{{ url('/packinglistsystem-delete') }}">
				<div class="modal-content ">
					<div class="modal-body">
						<p>Are you sure you want to delete the selected record?</p>
						{!! csrf_field() !!}
						<input type="hidden" name="id" id="delete_inputId"/>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" id="delete">Delete</button>
						<button type="button" data-dismiss="modal" class="btn">Cancel</button>
					</div>
				</div>
			</form>
		</div>
	</div>


@endsection

@push('script')
	<script type="text/javascript">

		$(function() {
			var url = "{{url('/packinglistsystemtable')}}" + "?from=&&to=&&id=";
			pakinglistTable(url);
		});

		function actionRecords(action) {
	        var obj_data = new Object;
	        var cnt = 0;
	        var selecteditem = '';

	        $(".checkboxes").each(function()
	        {
	        	var id = $(this).attr('name');
	        	if($(this).is(':checked'))
	        	{
	        		if(cnt==0)
	        		{
						selecteditem = $(this).val();
	        		}
	        		else
	        		{
	        			selecteditem = selecteditem + ',' + $(this).val();
	        		}
	        		cnt++;
	        	}
	       	});


	        if (cnt == 0)
	        {
	    		$.alert('No selected record.',
	    		{
					position: ['center', [-0.42, 0]],
					type: 'danger',
					closeTime: 3000,
					autoClose: true
				});
	        }
	        else
	        {
	        	if(action =='DEL')
	        	{
		    		$("#deleteModal").modal("show");
					$('#delete_inputId').val(selecteditem);
	        	}
	        	else if(action == 'EDIT')
	        	{
	        		//error when more than 1 record is selected for edit.
	        		if(cnt > 1)
	        		{
			    		$.alert('Please select one (1) record only.',
			    		{
							position: ['center', [-0.42, 0]],
							type: 'danger',
							closeTime: 3000,
							autoClose: true
						});
	        		}
	        		else
	        		{
	        			var dbcon = "{{ Auth::user()->productline }}";
						window.location.href= "{{ url('/packinglistdetails?selecteditem=') }}" + selecteditem + "&&dbconnection=" + dbcon;
	        		}
	        	}
	        	else
	        	{
	        		//Unknown action.
	        	}
	        }
	    }

	    function addDetails() {
	    	var dbcon = "{{ Auth::user()->productline }}";
			window.location.href= "{{ url('/packinglistdetails?dbconnection=') }}" + dbcon;
	    }

	    function setDate() {
	    	$('#dateFromXls').val($('#srch_from').val());
	    	$('#dateToXls').val($('#srch_to').val());
	    	$('#dateFromPdf').val($('#srch_from').val());
	    	$('#dateToPdf').val($('#srch_to').val());
	    }

	    function pakinglistTable(url) {
	    	$('#tbl_packinglist').dataTable().fnClearTable();
            $('#tbl_packinglist').dataTable().fnDestroy();
            $('#tbl_packinglist').DataTable({
                processing: true,
                serverSide: true,
                ajax: url,
                columns: [
                    {data: function(data) {
                            return '<input type="checkbox" class="checkboxes input-sm" name="checkboxes" value="'+data.id+'" data-id="'+data.id+'"/>';
                    },orderable: false, searchable:false, name:"id" },
                    { data: 'control_no', name: 'control_no' },
					{ data: 'invoice_date', name: 'invoice_date' },
					{ data: function(data) {
						return '<strong>TIME:</strong> '+data.remarks_time+
						'<br><strong>PICKUP DATE:</strong> '+data.remarks_pickupdate+
						'<br><strong>NO:</strong> '+data.remarks_s_no;
					}, name: 'remarks_time' },
					{ data: 'sold_to', name: 'sold_to' },
					{ data: 'ship_to', name: 'ship_to' },
					{ data: function(data) {
						return data.carrier_name;
					}, name: 'carrier' },
					{ data: 'date_ship', name: 'date_ship' },
					{ data: 'port_loading', name: 'port_loading' },
					{ data: function(data) {
						return data.port_destination_name;
					}, name: 'port_destination' },
					{ data: function(data) {
						return '<strong>FROM:</strong> '+data.from+
						'<br><strong>TO:</strong> '+data.to+
						'<br><strong>FREIGHT:</strong> '+data.freight;
					}, name: 'from' },
					{ data: 'case_marks', name: 'case_marks' },
					{ data: 'note', name: 'note' },
                    //{ data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                aoColumnDefs: [
                    {
                        aTargets:[5],
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).css('white-space', 'pre-wrap');
                        },

                        aTargets:[6],
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).css('white-space', 'pre-wrap');
                        },
                    }
                ]
            });
	    }

	    function search() {
	    	var srch_from = $('#srch_from').val();
	    	var srch_to = $('#srch_to').val();
	    	var url = "{{url('/packinglistsystemtable')}}" + "?from="+srch_from+"=&&to="+srch_to+"&&id=";
	    	pakinglistTable(url);
	    }
    </script>
@endpush
