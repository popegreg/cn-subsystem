@extends('layouts.master')

@section('title')
	SSS Answer Input Management | Pricon Microelectronics, Inc.
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
							<i class="fa fa-bar-chart-o"></i> Scheduling Support System (Answer Input Management)
						</div>
					</div>
					<div class="portlet-body portlet-empty">
						<div class="row">
							<div class="col-md-12">
								<h4>ANSWER INPUT MANAGEMENT</h4>
							</div>
						</div>

						<div class="row">


							<div class="col-md-6">
								<label class="col-md-3 control-label">Order Date: </label>
								<div class="col-md-9">
									<input onchange="refreshRadioButton()" class="form-control form-control-inline input-sm date-picker" size="16" type="text" value="" name="to" id ="datepick"  />
								</div>
							</div>

							<div class="col-md-6">
								<label class="col-md-3 control-label">Date Options: </label>
								<div class="input-group">
									<div class="icheck-inline">
										<label id = "r1">
											<input type="radio" name="radio2" onclick="r1()">  This Date Only
										</label>
										<label id = "r2">
											<input type="radio" name="radio2" onclick="r2()"> Include the Date Before
										</label>
									</div>
								</div>
							</div>


						</div>


						<br>

						<div class="row">
							<div class="col-md-6">
								<div class="portlet box blue">
									<div class="portlet-title">
										<div class="caption">
											Exceptions
										</div>
										<div class="tools">
											<button id="btn_addproduct" class="btn green btn-xs" data-target="#stack1" onclick="clearText();" data-toggle="modal">
												Add <i class="fa fa-plus"></i>
											</button>
										</div>
									</div>
									<div class="portlet-body portlet-empty">
										<div class="row">
											<div class="col-sm-12">
												<p>Items: <span id="exceptionscount"></span></p>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-12">
												<div style="height: 16%;" class="table-responsive scroller">
													<table class="table table-striped table-bordered table-hover" style="font-size: 9px">
														<thead>
															<tr>
																<td colspan="3">
																	Product Name
																</td>
															</tr>
														</thead>
														<tbody id = "productbody">
															<tr>
																<!-- <td width="50px"></td>
																<td></td>
																<td width="50px"></td> -->
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<p>Regulations of answer input</p>
								<p>Should be input within 3 working days even though still 23:00.</p>
								<p>Should be input within 4 working days for the PO which answered as 23:00</p>
							</div>

						</div>

						<br>

						<div class="row">

							<div class="col-md-12">
								<div style="height: 35%; overflow-y: scroll;" class="scroller">
									<table class="table table-striped table-bordered table-hover" style="font-size: 9px;">
										<thead>
											<tr>
												<td>
													<b>ORDER DATE</b>
												</td>
												<td>
													<b>PO</b>
												</td>
												<td>
													<b>PCODE</b>
												</td>
												<td>
													<b>PNAME</b>
												</td>
												<td>
													<b>QTY</b>
												</td>
												<td>
													<b>R3ANSWER</b>
												</td>
												<td>
													<b>TIME</b>
												</td>
												<td>
													<b>REMARKS</b>
												</td>
												<td>
													<b>CUSTCODE</b>
												</td>
												<td>
													<b>CUSTOMER</b>
												</td>
											</tr>
										</thead>

										<tbody id="table" >
												<?php $cnt = 0;?>
											@foreach ($data as $d)
												<?php $cnt++; ?>
												<tr>
														<td> {{ $d->order_date }}</td>
														<td> {{ $d->code }}</td>
														<td> {{ $d->name }}</td>
														<td> {{ $d->po }}</td>
														<td> {{ $d->qty }}</td>
														<td> {{ $d->r3answer }}</td>
														<td> {{ $d->time }}</td>
														<td> {{ $d->remarks }}</td>
														<td> {{ $d->custcode }}</td>
														<td> {{ $d->customer }}</td>

												</tr>
											@endforeach												
										</tbody>
									</table>
								</div>
								<span id="count"></span>

								<div class="row">
									<div class="col-md-12">
										<form method="post" enctype="multipart/form-data"  action="{{ url('/postanswerinputmanagementexcel') }}" >
										{!! csrf_field() !!}
										<input type="hidden" name="hidorderdate" id = "hidorderdate">
										<input type="hidden" name="hidexceptions" id = "hidexceptions">
										<input type="hidden" name="hidradio" id = "hidradio">

											<button id="print" class="btn blue btn-sm pull-right">
												<i class="fa fa-print"></i> Output
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
			<div class="col-md-2"></div>
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

	<div id="stack1" class="modal fade" tabindex="-1" data-width="400">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Add Exception</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<h5>Product</h5>
							<p>
								<input type="text" id = "productvalue" class="col-md-12 form-control input-sm">
							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn green btn-sm" onclick="PushDataToJason();">Save</button>
					<button type="button" data-dismiss="modal" class="btn btn-sm red">Close</button>
				</div>
			</div>
		</div>
	</div>
@endsection
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript">

		// $(function() {
		// 	$('#datepick').datepicker({
		// 		autoclose: true,
		// 		todayHighlight: true,
		// 		format: 'yyyy/mm/dd' 
		// 	});
		// });

		var productJsonArray = [];
		var staticradio = "";

		function getData(r)
		{
			staticradio = r;
			var datepick = document.getElementById('datepick').value;
			var arangeDate = arangeDatee(datepick);


			console.log(arangeDate);
			if(datepick != "")
			{
				var data = {
				            orderdate: arangeDate,
				            exceptions: JSON.stringify(productJsonArray),
				            radio: r,
				        }
				$('#loading').modal('show');
				$.ajax({
					data: data,
			        url: '{{ url('/answerinputmanagementloadwithexceptions') }}',
			        method: 'GET',
			    }).done(function(data){
			    	updateAnswerInputList(data);
			    	console.log(data);
			    });
			    document.getElementById('hidradio').value = r;
			    document.getElementById('hidorderdate').value = arangeDate;
			}else
			{
				alert("Invalid Date!");
				$('#r1').html('<input type="radio" name="radio2" onchange="r1()">  This Date Only');
				$('#r2').html('<input type="radio" name="radio2" onchange="r2()"> Include the Date Before');
				document.getElementById('hidradio').value = "";
				staticradio = "";
				if(document.getElementById('datepick').value != "")
					loadAllData();
				document.getElementById('datepick').value = "";

			}

		}

		function PushDataToJason()
		{
			var product = document.getElementById('productvalue').value.trim();
			$('#stack1').modal('hide');
			if(checkIfExist(product) && product.trim() != "")
			{
				productJsonArray.push(product);
				loadProduct();
			}
			else
			{
				alert("Product Exist!");
			}

		}


		function loadProduct()
		{

			var html = "";
			var counter = 1;
			for(var i = 0; i < productJsonArray.length; i++)
			{
				html = html + '<tr><td width="50px">'+counter+'</td><td>'+productJsonArray[i]+'</td><td width="50px"><button class="btn default btn-xs black" id ='+productJsonArray[i]+' onclick="removeDataToJason(this.id);"><i class="fa fa-trash-o"></i> Delete </button></td></tr>';


				counter++;
			}
			if(counter<=2)
				$('#exceptionscount').html((counter-1));
			else
				$('#exceptionscount').html((counter-1));

				$("#productbody").html(html);
			document.getElementById('hidexceptions').value = JSON.stringify(productJsonArray);

			if(staticradio != "")
				getData(staticradio);
		}

		function removeDataToJason(x)
		{

			for(var i = 0; i < productJsonArray.length; i++)
			{
				if(productJsonArray[i] == x)
					productJsonArray.splice(i, 1);
			}
			loadProduct();
		}

		function checkIfExist(item)
		{
			var output = true;
			if(productJsonArray.length != 0)
			{
				for(var i = 0; i < productJsonArray.length; i++)
				{
					if(productJsonArray[i] == item)
						output = false;
				}
			}
			return output;
		}

			function clearText()
			{
				document.getElementById('productvalue').value = "";
			}

			function r1()
			{
				getData('=');
			}
			function r2()
			{
				getData('<=');
			}

			function refreshRadioButton()
			{
					if(staticradio != "")
						getData(staticradio);
			}

			var load = function (){
				loadAllData();
			}

			function loadAllData()
			{
				$('#loading').modal('show');
						$.ajax({
					        url: '{{ url('/answerinputmanagementload') }}',
					        method: 'GET',
					    }).done(function(data){
					    	updateAnswerInputList(data);
					    });
			}

			function updateAnswerInputList(data)
			{
			var html = "";
					    	var count = 0;
					    	$.each(data, function (i, item) {

					    		html = html + '<tr>'
								    				+ '<td>' + item['order_date']+ '</td>'
								    				+ '<td>' + item['po'] + '</td>'
								    				+ '<td>' + item['code'] + '</td>'
								    				+ '<td>' + item['name'] + '</td>'
								    				+ '<td>' + item['qty'] + '</td>'
								    				+ '<td>' + item['r3answer'] + '</td>'
								    				+ '<td>' + item['time'] + '</td>'
								    				+ '<td>' + item['remarks'] + '</td>'
								    				+ '<td>' + item['custcode'] + '</td>'
								    				+ '<td>' + item['customer'] + '</td>'
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


		function validateDate(date)
		{
			if(date.length == 9 && isNumber(date))
				return true;
			else
				return false;
		}

		function arangeDatee(date)
		{
			return date.substring(6, 10)+"/"+date.substring(0, 2)+"/"+date.substring(3, 5);
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
