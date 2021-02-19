@extends('layouts.master')

@section('title')
	YPICS Inventory Query | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_INVQUERY'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
				<div class="row">
					<div class="col-md-12">
						<a href="{{ url('/inventoryquery') }}" class="btn btn-danger pull-right">
							<i class="fa fa-mail-reply"></i> Back
						</a>
					</div>
				</div>

				<br>

				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cubes"></i>  TPICS STOCK QUERY BY BOM REVERSE
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="portlet box blue-hoki">
									<div class="portlet-body">
										<div class="row">
											<div class="col-md-12">
												<form method="POST" action="{{url('/rebomitems')}}" class="form-horizontal" id="searchfrm">
													{{ csrf_field() }}

													<div class="form-group">
														<label class="control-label col-md-2">PART NAME:</label>
														<div class="col-md-8">
															<input type="text" class="form-control" id="partname" name="partname">
														</div>
														<div class="col-md-2">
															<a class="btn btn-sm btn-primary" id="btn_partname">
																<i class="fa fa-search"></i> Search
															</a>
														</div>
													</div>

												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="portlet box">

									<div class="portlet-body">

										<div class="row">
											<div class="col-md-12">
												<div class="scroller" style="height:200px">
													<table class="table table-striped table-bordered table-hover" style="font-size: 9px;">
														<thead>
															<tr>
																<td>VENDOR</td>
																<td>PRICE</td>
																<td>Stock Total</td>
																<td>ASSY100</td>
																<td>ASSY102</td>
																<td>WHS100</td>
																<td>WHS102</td>
																<td>WHS-NON</td>
																<td>WHS-SM</td>
																<td>Updated</td>
															</tr>
														</thead>
														<tbody id="tbl_bom"></tbody>
													</table>
												</div>

											</div>
										</div>

										<br/>

										<div class="row">
											<div class="col-md-8 col-md-offset-2">
												<div class="scroller" style="height:300px">
													<table class="table table-striped table-bordered table-hover" style="font-size: 10px;">
														<thead>
															<tr>
																<td>PRODUCT</td>
																<td>PRODUCT NAME</td>
																<td>USAGE</td>
															</tr>
														</thead>
														<tbody id="tbl_prod">
															<!-- @if(Session::has('prods'))
																@foreach(Session::get('prods') as $prod)

																@endforeach
															@endif -->
														</tbody>
													</table>
												</div>
											</div>
										</div>

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
			

	<!-- AJAX LOADER -->
	<div id="loading" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<img src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/images/ajax-loader.gif') }}" class="img-responsive">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- msg -->
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

<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	$( document ).ready(function(e) {
		//$('#searchfrm').submit();
		// selectValue(e);
		// function selectValue(e) {
		// 	var url = "{{url('/rebomitems')}}";
		// 	var token = "{{ Session::token() }}";
		// 	var data = {
		// 		_token : token,
		// 	};
		//
		// 	$.ajax({
		// 		url: url,
		// 		method: 'GET',
		// 		data:  data,
		// 	}).done( function(data, textStatus, jqXHR) {
		// 		$('#code option').remove();
		// 		$.each(data, function(i,item) {
		// 			$('#code').append(
		// 				$('<option></option>')
		// 				.text(item.name)
		// 				.val(item.code)
		// 			);
		// 		});
		// 	}).fail( function(data, textStatus, jqXHR) {
		// 		console.log(textStatus);
		// 	});
		// }

		$('#btn_partname').on('click', function(e) {
			var partname = $('#partname').val();
			bom(e,partname);
			prod(e,partname);
		});
		
		function bom(e,partname) {
			var formURL = "{{ url('/rebomsearchBOM') }}";
			var token = '{{ Session::token() }}';
			var formData = {
				_token: token,
				partname: partname,
			};
			var detailstbl = '';

			e.preventDefault(); //Prevent Default action.
			$('#loading').modal('show');

			$.ajax({
				url: formURL,
				method: 'POST',
				data:  formData,
			}).done( function(data, textStatus, jqXHR) {
				$('#loading').modal('hide');
				$('#tbl_bom').html('');
				console.log(data);
				$.each(data,function (index,bom) {
					var StockTotal = parseInt(bom.assy100) + parseInt(bom.assy102) + parseInt(bom.whs100) + parseInt(bom.whs102) + parseInt(bom.whsnon) + parseInt(bom.whssm);
					detailstbl = '<tr>'+
									'<td>'+bom.vendor+'</td>'+
									'<td>'+bom.price+'</td>'+
									'<td>'+StockTotal+'</td>'+
									'<td>'+bom.assy100+'</td>'+
									'<td>'+bom.assy102+'</td>'+
									'<td>'+bom.whs100+'</td>'+
									'<td>'+bom.whs102+'</td>'+
									'<td>'+bom.whsnon+'</td>'+
									'<td>'+bom.whssm+'</td>'+
									'<td></td>'
								'</tr>'
					$('#tbl_bom').append(detailstbl);
				});

			}).fail(function(data, jqXHR, textStatus, errorThrown) {
				$('#loading').modal('hide');
				console.log(data);
			});
		}

		function prod(e,partname) {
			var formURL = "{{ url('/rebomsearchprod') }}"
			var token = '{{ Session::token() }}';
			var formData = {
				_token: token,
				partname: partname,
			};
			var prodtbl = '';

			e.preventDefault(); //Prevent Default action.
			$('#loading').modal('show');

			$.ajax({
				url: formURL,
				method: 'POST',
				data:  formData,
			}).done( function(data, textStatus, jqXHR) {
				$('#loading').modal('hide');
				$('#tbl_prod').html('');
				console.log(data);
				$.each(data,function (index,prod) {
					prodtbl = '<tr>' +
								'<td>' + prod.prodcode + '</td>' + //prod.CODE
								'<td>' + prod.prodname + '</td>' + //prod.NAME
								'<td>' + prod.usage + '</td>' + //prod.SIYOU
							'</tr>';
					$('#tbl_prod').append(prodtbl);
				});

			}).fail(function(data, jqXHR, textStatus, errorThrown) {
				$('#loading').modal('hide');
				console.log(data);
			});
		}
	});
</script>
