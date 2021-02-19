<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: InvoiceDataCheck.blade.php
     MODULE NAME:  [3005] Invoice Data Check
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2016.05.03
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.03     AK.DELAROSA     Initial Draft
     100-00-02   1     2016.06.09     MESPINOSA       Fix for Issue #027,#028,#029.
*******************************************************************************/
?>
@extends('layouts.master')

@section('title')
	Invoice Data Check | Pricon Microelectronics, Inc.
@endsection


@section('content')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_INVOICE'))
			@if ($access->read_write == "2")
				<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach
		
	<div class="page-content">

		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-offset-2 col-md-8">
				@include('includes.message-block')


				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-file"></i> INVOICE DATA CHECK / GENERATE LOADING DATA
						</div>
					</div>
					<div class="portlet-body">

						<form method="POST" action="{{url('/readfile')}}" class="form-horizontal" enctype="multipart/form-data" id="formReadfile">
							<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-3">Imported Data</label>
									<div class="col-md-5">
										<input type="file" class="filestyle" data-buttonName="btn-primary" name="importedData" id="importedData" {{$readonly}}>
									</div>
									<div class="col-md-2">
										<button type="submit" class="btn green pull-right btn-md" {{$state}}>
											<i class="fa fa-refresh"></i> Generate
										</button>
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-md-3">INVOICE No.</label>
									<div class="col-md-5">
										<input type="text" class="form-control input-md" name="invoice" id="invoice" disabled />
									</div>
									<div class="col-md-2">
										<a href="javascript:;" id="calculate" class="btn yellow pull-right btn-md calculate" {{$state}}>
											<i class="fa fa-calculator"></i> Calculate
										</a>
									</div>
								</div>

								<!-- <div class="form-group">
									<label class="control-label col-md-4">Designated Directory</label>
									<div class="col-md-5">
										<input type="text" class="form-control input-md" value="/var/www/html/pmi-subsystem/public/Invoice_Data_Check/" disabled/>
									</div>
								</div> -->

								<div class="form-group">
									<div class="col-md-12">
										<button class="btn blue btn-sm" disabled>ORIGINAL INVOICE QTY:</button>
										<strong id="origqty"></strong>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12">
										<button class="btn blue btn-sm" disabled>DATA GENERATED QTY: </button>
										<strong id="genqty"></strong>
									</div>
								</div>

							</div>
						</form>

					</div>
				</div>

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

	<!-- OPEN MODAL VARIANCE -->

	<div id="openVarianceModal" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 id="title" class="modal-title">Variances</h4>
				</div>
				<div class="modal-body">
					<div class="col-md-12">

						<div class="row" style="padding:10px;">
							<div class="col-sm-12 table-responsive" style="height:500px; overflow: auto;">
								<table class="table table-hover table-bordered" style="font-size: 10px" id="tbl_variance">
									<thead>
										<tr>
											<td>InvoiceNo</td>
											<td>FltDate</td>
											<td>PR</td>
											<td>Code</td>
											<td>PartName</td>
											<td>UnitPrice</td>
											<td>OrderQty</td>
											<td>OrderBal</td>
											<td>DeliveredQty</td>
											<td>OverDelivery</td>
											<td>NewOrderQty</td>
											<td>OverAmount</td>
										</tr>
									</thead>
									<tbody id="tbl_variance_body">

									</tbody>
								</table>
							</div>
						</div>

					</div>
				</div>
				<br>
				<div class="modal-footer">
					<div class="col-md-7" style="color: red;"><h5 id="notif_msg">Sample notification message.</h5></div>
					<div class="col-md-5">
						<a href="javascript:;" id="btn_pdf_overdelivery" class="btn green pull-right btn-sm">Summary Report</a>
						<a href="javascript:;" id="btn_excel_unitcost" class="btn purple pull-right btn-sm">Unit Cost Difference</a>
						<a href="{{ url('/varianceexcel') }}" id="btn_excel_variance" class="btn blue pull-right btn-sm">Over Delivery</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- OPEN MODAL NON VARIANCE -->

	<div id="openNonVarianceModal" class="modal fade">
		<div class="modal-dialog modal-sm">
			<div class="modal-content ">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 id="title" class="modal-title">No Variances</h4>
				</div>
				<div class="modal-body">
					<h4>Success. There are no variances.</h4>
				</div>
				<div class="modal-footer">
					<a href="javascript:;" class="btn btn-sm blue pull-right calculate">Generate File</a>
				</div>
			</div>
		</div>
	</div>

@endsection

@push('script')
<script type="text/javascript">
	$( document ).ready(function(e) {
		$('#btn_csv_nonvariance').on('click', function(){
			window.location.href = "{{ url('/nonvariancecsv') }}";
		});

		$('.calculate').on('click', function(e){
			$('#openNonVarianceModal').modal('hide');
			$('#loading').modal('show');
			
			if ($('#invoice').val() != '') 
			{
				e.preventDefault();

				var data = {
						_token: "{{ Session::token() }}",
					}
				$.ajax({
					url: "{{ url('/nonvariancecsv') }}",
					type: 'GET',
					dataType: 'JSON',
					data: data
				}).done( function(data, textStatus, jqXHR) 
				{
					$('#loading').modal('hide');
					$('#origqty').html(data['origqty']);
					$('#genqty').html(data['dataqty']);
					$('#loading').modal('hide');
					window.location.href = "{{ url('/nonvarianceexcel') }}";

				}).fail(function(jqXHR, textStatus, errorThrown) 
				{
					$.alert('<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong> An Error Occur.', {
						position: ['center', [-0.42, 0]],
						type: 'danger',
						closeTime: 3000,
						autoClose: true
					});
				});
			} else {
				$.alert('<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong> No Invoice Number.', {
					position: ['center', [-0.42, 0]],
					type: 'danger',
					closeTime: 3000,
					autoClose: true
				});
			}
			$('#loading').modal('hide');
		});

		$('#formReadfile').on('submit', function(e){
			var tbl_variance = '';
			var tbl_nonvariance = '';
			var formObj = $('#formReadfile');
			var formURL = formObj.attr("action");
			var formData = new FormData(this);
			var fileName = $("#importedData").val();
			var ext = fileName.split('.').pop();
			e.preventDefault(); //Prevent Default action.
			
			$('#loading').modal('show');
			if ($("#importedData").val() == '') {
				$.alert('Please select a valid Text file.', {
					position: ['center', [-0.42, 0]],
					type: 'danger',
					closeTime: 3000,
					autoClose: true
				});
				$('#loading').modal('hide');
			}
			if (ext != 'txt') {
				$.alert('<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong> Please select a valid Text file. This module only accepts text file.', {
					position: ['center', [-0.42, 0]],
					type: 'danger',
					closeTime: 3000,
					autoClose: true
				});
				$('#loading').modal('hide');
			}
			if (fileName != ''){
				if (ext == 'txt') {
					
					$.ajax({
						url: formURL,
						method: 'POST',
						data:  formData,
						mimeType:"multipart/form-data",
						contentType: false,
						cache: false,
						processData:false,
					}).done( function(data, textStatus, jqXHR) {
						var output = jQuery.parseJSON(data);
						$('#loading').modal('hide');

						$('input[name=invoice]').val(output.invoice);
						$('#origqty').html(output.origqty);
						$('#genqty').html(output.dataqty);

						if(Object.keys(output.variance).length != 0 && Object.keys(output.costvariance).length != 0)
						{
							document.getElementById("notif_msg").innerHTML = "DETECTED PRICE DIFFERENCE AND OVER DELIVERY. PLEASE CHANGE THEM BEFORE DATA LOADING.";
						}
						else if(Object.keys(output.variance).length != 0 && Object.keys(output.costvariance).length == 0)
						{
							document.getElementById("notif_msg").innerHTML = "DETECTED OVER DELIVERY. PLEASE CHANGE THEM BEFORE DATA LOADING.";
						}
						else if(Object.keys(output.variance).length == 0 && Object.keys(output.costvariance).length != 0)
						{
							document.getElementById("notif_msg").innerHTML = "DETECTED PRICE DIFFERENCE. PLEASE CHANGE THEM BEFORE DATA LOADING.";
						}
						else
						{
							document.getElementById("notif_msg").innerHTML = "CHECKING IS DONE. YOU CAN PROCEED TO GENERATE UPLOAD DATA.";
						}


						if (Object.keys(output.variance).length != 0) {
							$.each(output.variance, function(i, x) {
								tbl_variance = '<tr>'+
													'<td>'+x.invoiceno+'</td>'+
													'<td>'+x.fdate+'</td>'+
													'<td>'+x.pr+'</td>'+
													'<td>'+x.code+'</td>'+
													'<td>'+x.partname+'</td>'+
													'<td>'+x.unitprice+'</td>'+
													'<td>'+x.orderqty+'</td>'+
													'<td>'+x.orderbal+'</td>'+
													'<td>'+x.deliveredqty+'</td>'+
													'<td>'+x.overdelivery+'</td>'+
													'<td>'+x.neworderqty+'</td>'+
													'<td>'+x.overamount+'</td>'+
												'</tr>';

								$('#tbl_variance_body').append(tbl_variance);
							});
							$('#openVarianceModal').modal('show');
						} else {
							$('#openNonVarianceModal').modal('show');
						}

						
						

					}).fail(function(jqXHR, textStatus, errorThrown) {
						$.alert('<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong> An Error Occur.', {
							position: ['center', [-0.42, 0]],
							type: 'danger',
							closeTime: 3000,
							autoClose: true
						});
					});
				}
			}
		});

		$('#btn_pdf_overdelivery').on('click', function() {
			window.location.href = "{{ url('/overdeliverypdf') }}";
		});

		$('#btn_excel_unitcost').on('click', function() {
			window.location.href = "{{ url('/unitcostexcel') }}";
		});
	});
</script>
@endpush