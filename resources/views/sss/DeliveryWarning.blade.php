@extends('layouts.master')

@section('title')
	SSS Delivery Warning | Pricon Microelectronics, Inc.
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
              height: 500px;

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
              height: 40px;
          }
     </style>
@endpush

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
			<div class=""></div>
			<div class="">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">

					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i> Scheduling Support System (Delivery Warning)
						</div>
					</div>
					<div class="portlet-body portlet-empty">
						<dv class="row">
							<div class="col-md-12">
								<h4>PARTS DELIVERY WARNING CHECK</h4>
							</div>
						</dv>
						<br>
						<br>
						<div class="row">
							<div class="col-md-12">
								<form action="#" class="form-horizontal form-bordered">
									<div class="form-body">
										<div class="form-group">
											<label class="control-label col-md-1">From:</label>
											<div class="col-md-3">
												<input class="form-control form-control-inline input-medium date-picker" size="16" type="text" value="" name="from" id="from"/>
											</div>

											<label class="control-label col-md-1">To:</label>
											<div class="col-md-3">
												<input class="form-control form-control-inline input-medium date-picker" size="16" type="text" value="" name="to" id ="to"  />
											</div>

											<div class="btn-group">
												<button type="button" onclick="loadDeliveryWarning(10);"  class="btn btn-sm btn-success " ><i class="fa fa-search"></i> Search</button>

											</div>

										</div>
									</div>
								</form>
								<div class="" data-rail-visible="1" >
									<div class="table-responsive">
										<table class="table table-striped table-bordered table-hover table-fixedheader" style="font-size: 9px;"><!--id = "sample_3" -->
											<thead>
												<tr>
													<td width="10%">ORDER DATE</td>
													<td width="10%">PO</td>
													<td width="10%">CODE</td>
													<td width="20%">NAME</td>
													<td width="5%">ORDER QTY</td>
													<td width="20%">CUSTOMER</td>
													<td width="5%">SCHED QTY</td>
													<td width="10%">PARTS COMPLETION</td>
													<td width="5%">YEC</td>
													<td width="5%">PMI</td>
												</tr>
											</thead>


											<tbody id="table"></tbody>
										</table>

									<!-- <div class="row" id="loading" style="display: none">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<img src="assets/global/img/loading-spinner-blue.gif" class="img-responsive">
										</div>
									</div> -->
									</div>
									<p>Count: <span id="count"></span></p>
								</div>
							</div>
						</div>
						

						<br/>
						<div class="row">
							<div class="col-md-12">
							<form style ="margin-top: 15px;" method="post" target="_blank" enctype="multipart/form-data"  action="{{ url('/postDeliveryWarningPDF') }}" >
							{!! csrf_field() !!}

								<input type="hidden" name="fd_pdf" id = "fd_pdf">
								<input type="hidden" name="td_pdf" id = "td_pdf">
								<button class="btn blue btn-sm pull-right">
									<i class="fa fa-print"></i> Print
								</button>
								</form>
								<!--<button type="button" id="excel" onclick="deliveryWarningExcel();" class="btn green btn-lg pull-right">
									<i class="fa fa-file-excel-o"></i> Excel
								</button> -->

								<form method="post" enctype="multipart/form-data"  action="{{ url('/postDeliveryWarningExcel') }}" >
								{!! csrf_field() !!}
								<input type="hidden" name="fd" id = "fd">
								<input type="hidden" name="td" id = "td">
								<button class="btn green btn-sm pull-right">
									<i class="fa fa-file-excel-o"></i> Excel
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
						<div class="col-sm-offset-2 col-sm-8">
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
	var from_date = "";
	var to_date = "";


	var load = function (){

		$('#imgs').html('');

	}

	var row = 15;

	getAllDeliveryWarning(row)

	$(function(){
		$('#table').scroll(function() {
	        if($(this).scrollTop() + $(this).height() >= $(this).height()) {
	            row = row+2;
	            getAllDeliveryWarning(row);
	        }
	    });
    });

    function getAllDeliveryWarning(row) {
    	var url = "{{ url('/getalldeliverywarning') }}";
    	var token = "{{ Session::token() }}";
    	var data = {
    		_token: token,
    		row: row
    	}
    	$.ajax({
    		url: url,
    		type: 'GET',
    		dataType: 'JSON',
    		data: data,
    	})
    	.done(function(data, textStatus, jqXHR) {
    		updateDeliveryWarningList(data)
    	})
    	.fail(function(data, textStatus, jqXHR) {
    		console.log("error");
    	});
    	
    }

	function loadDeliveryWarning(row)
	{

		var f = document.getElementById('from').value;
		var t = document.getElementById('to').value;

		from_date = arrangeDateToLong(f);
		to_date = arrangeDateToLong(t);

		document.getElementById("fd").value = from_date;
		document.getElementById("td").value = to_date;
		document.getElementById("fd_pdf").value = from_date;
		document.getElementById("td_pdf").value = to_date;

		if(validateDate(from_date,to_date))
		{
			$('#loading').modal('show');
			var ft = {
	            fd: from_date,
	            td: to_date,
	            row: row
	        }

			$.ajax({
				data: ft,
		        url: '{{ url('/loadDeliveryWarningWithDate') }}',
		        method: 'GET',
		    }).done(function(data){
		    	updateDeliveryWarningList(data);
		    });
		}
		else
		{
			alert('Invalid Date! "From" date must be greater than or equal to "To" date.');

			from_date = "";
			to_date = "";

			document.getElementById('from').value = "";
			document.getElementById('to').value = "";
			document.getElementById("fd").value = "";
			document.getElementById("td").value = "";
			document.getElementById("fd_pdf").value = "";
			document.getElementById("td_pdf").value = "";
		}

	}


	function updateDeliveryWarningList(data)
	{
		var html = "";
    	var count = 0;
    	$.each(data, function (i, item) {

    		html = html + '<tr>'
			    				+ '<td width="10%">' + arrangeLongToDate(item['order_date']) + '</td>'
			    				+ '<td width="10%">' + item['po'].substring(0, 10) + '</td>'
			    				+ '<td width="10%">' + item['code'] + '</td>'
			    				+ '<td width="20%">' + item['name'] + '</td>'
			    				+ '<td width="5%">' + item['order_qty'] + '</td>'
			    				+ '<td width="20%">' + item['customer'] + '</td>'
			    				+ '<td width="5%">' + item['sched_qty'] + '</td>'
			    				+ '<td width="10%"></td>'
			    				+ '<td width="5%"></td>'
			    				+ '<td width="5%"></td>'
	    				+ '</tr>';
	    				count++;
    	});




    	$('#loading').modal('hide');
    	$('#table').html(html);

    	if(count==1)
    		$('#count').html(count+" Item");
    	else
    		$('#count').html(count+" Items");
	}


	function validateDate(from_Date,to_Date)
	{
		if(from_Date.length == 9 && isNumber(from_Date) && to_Date.length == 9 && isNumber(to_Date) && from_Date <= to_Date)
			return true;
		else
			return false;
	}

	function arrangeDateToLong(date)
	{
		return date.substring(6, 10)+date.substring(0, 2)+date.substring(3, 5)+"1";
	}

	function arrangeLongToDate(date)
	{

		return date.substring(4, 6) + "/" + date.substring(6, 8) + "/" + date.substring(0, 4);
	}

	function isNumber(n)
	{
		return /^-?[\d.]+(?:e-?\d+)?$/.test(n);
	}
</script>
@endpush