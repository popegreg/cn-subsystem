@extends('layouts.master')

@section('title')
	SSS Delivery Warning | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php ini_set('max_input_vars', 999999);?>
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == "3008")  <!-- Please update "2001" depending on the corresponding program_code -->
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
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i> Scheduling Support System (sample DOUJI INPUT)
						</div>
					</div>
					<div class="portlet-body portlet-empty">
						<dv class="row">
							<div class="col-md-12">
								<h5>SAMPLE DOUJI INPUT</h5>
							</div>
						</dv>
						<br>
						<div class="row">
							<div class="portlet-body col-md-12">
								
								<div class="table-responsive scroller" data-rail-visible="1" style="height: 300px;">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<td>
													<b>ORDER DATE</b>
												</td>
												<td>
													<b>PO</b>
												</td>
												<td>
													<b>DCODE</b>
												</td>
												<td>
													<b>ORDERBAL</b>
												</td>
												<td>
													<b>ORDERQTY</b>
												</td>
												<td>
													<b>CUSTCODE</b>
												</td>
												<td>
													<b>CUSTOMER</b>
												</td>
												<td>
													<b>MCODE</b>
												</td>
												<td>
													<b>MNAME</b>
												</td>
												<td>
													<b>SUPPLIERCODE</b>
												</td>

												<td>
													<b>SUPPLIERNAME</b>
												</td>
												<td>
													<b>RE</b>
												</td>
												<td>
													<b>STATUS</b>
												</td>
												
											</tr>
										</thead>

										<tbody id="table" >
												

										</tbody>
									</table>

									<!-- <div class="row" id="loading" style="display: none">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<img src="assets/global/img/loading-spinner-blue.gif" class="img-responsive">
										</div>
									</div> -->
								</div>
							</div>
						</div>
						<span id="count"></span>
						
						<br/>
						<div class="row">
							<div class="col-md-12">
							

								<form method="post" enctype="multipart/form-data"  action="{{ url('/doujiexportexcel') }}" >
								{!! csrf_field() !!}
								<button class="btn blue btn-sm pull-right">
									<i class="fa fa-print"></i> Print
								</button>
								</form>

								
							</div>
						</div>


					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
			<div class="col-md-2"></div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
			

	<!-- AJAX LOADER -->
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


@endsection
@push('script')
<script type="text/javascript">
	$(function() {
		load();
	});
	
	function load(){
		var data = {
			_token: "{{ Session::token() }}"
		}
		//$('#loading').modal('show');  
		$.ajax({
	        url: '{{ url('/sampledoujiinputload') }}',
	        type: 'GET',
	        data: data,
	    }).done(function(data){
	    	//$('#loading').modal('hide');
	    	updateDeliveryWarningList(data);
	    	
	    }).fail(function() {
	    	alert("There was an error occurred.");
	    });

	}


	function updateDeliveryWarningList(data)
	{
		var html = "";
    	var count = 0;
    	$.each(data, function (i, item) {
    		
    		html = html + '<tr>'
			    				+ '<td>' + item['order_date'] + '</td>'
			    				+ '<td>' + item['po'] + '</td>'
			    				+ '<td>' + item['dcode'] + '</td>'
			    				+ '<td>' + item['order_bal'] + '</td>'
			    				+ '<td>' + item['order_qty'] + '</td>'
			    				+ '<td>' + item['cust_code'] + '</td>'
			    				+ '<td>' + item['cust_name'] + '</td>'
			    				+ '<td>' + item['mcode'] + '</td>'
			    				+ '<td>' + item['mname'] + '</td>'
			    				+ '<td>' + item['sup_code'] + '</td>'
			    				+ '<td>' + item['sup_name'] + '</td>'
			    				+ '<td>' + item['re'] + '</td>'
			    				+ '<td>' + item['status'] + '</td>'
	    				+ '</tr>';
	    				count++;
    	});

    	
    	$('#table').html(html);
    	if(count==1)
    		$('#count').html(count+" Item");
    	else
    		$('#count').html(count+" Items");
	}


		function arrangeDateToLong(date,val)
	{

		var returnvalue = "";
		if(val=="d")
			returnvalue = date.substring(3, 5);
		else if(val=="m")
			returnvalue = date.substring(0, 2);
		else if(val=="y")
			returnvalue = date.substring(6, 10);

		return returnvalue;
	}

	function arrangeLongToDate(date,val)
	{

		var returnvalue = "";
		if(val=="d")
			returnvalue = date.substring(6, 8);
		else if(val=="m")
			returnvalue = date.substring(4, 6);
		else if(val=="y")
			returnvalue = date.substring(0, 4);

		return returnvalue;
	}

	function isNumber(n) 
	{ 
		return /^-?[\d.]+(?:e-?\d+)?$/.test(n); 
	} 
</script>
@endpush