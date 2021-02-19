@extends('layouts.master')

@section('title')
	PR Balance Difference Check| Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_PRBALANCE'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach
	
	<div class="page-content">

		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				@include('includes.message-block')
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-clipboard"></i>  PR BALANCE DIFFERENCE CHECK
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="portlet box blue-hoki">
									<div class="portlet-body">
										<div class="row">
											<div class="col-md-12">
												<form method="POST" action="{{ url('/prbfiles') }}" enctype="multipart/form-data" class="form-horizontal" id="prbfiles" >
													{{ csrf_field() }}

													<div class="form-group">
														<label class="control-label col-md-3">INPUT DATA</label>
														<div class="col-md-7">
															<input type="file" class="filestyle" data-buttonName="btn-primary" name="inputdata" id="inputdata" {{$readonly}}>
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-md-3">OUTPUT DATA</label>
														<div class="col-md-7">
															<input type="text" class="form-control" disabled="disable" value="/public/PRBalance/{{Auth::user()->user_id}}/">
														</div>
													</div>
													<div class="row">
														<div class="col-sm-12">

															<div class="portlet box blue-hoki">
																<div class="portlet-body">

																	<div class="row">
																		<div class="col-xs-7 col-xs-offset-1">
																			<h4>REFERENCE INVOICE DATA:</h4>
																		</div>
																		<div class="col-xs-3">
																			<div class="form-group">
																				<label for="inputlocked" class="control-label">
																					<input type="checkbox" class="checkboxes" name="invoicechk" id="invoicechk" value="1" checked="check" />
																					Disregard Invoice Data
																				</label>
																			</div>
																		</div>
																	</div>

																	<div class="row">
																		<div class="form-group">
																			<label class="control-label col-md-3">INVOICE: </label>
																			<div class="col-md-7">
																				<input type="file" class="filestyle" data-buttonName="btn-primary" name="invoice" id="invoice" {{$readonly}} disabled="true">
																				<span class="blue"></span>
																			</div>
																		</div>
																	</div>

																	<div class="row">
																		<div class="col-xs-7 col-xs-offset-1">
																			<span>If doesn't match the data,  please import Invoice data at "Over Deliver Checking.</span>
																		</div>
																		<div class="col-xs-3">
																			<div class="form-group">
																				<button type="submit" id="process" class="btn btn-md btn-warning" {{$state}}>
																					<i class="fa fa-refresh"></i> Process
																				</button>
																			</div>
																		</div>
																	</div>
																</div>
															</div>

														</div>
													</div>
												</form>
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
						<span id="btn-excel"></span>
						<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
					</div>
				</div>
			</div>
		</div>


@endsection
@push('script')
	<script type="text/javascript">
		$( document ).ready(function(e) {
			$('#invoicechk').on('change', function() {
				$('#invoice').prop('disabled', function(i, v) { return !v; });
			})
			$('#prbfiles').on('submit', function(e) {
				var formObj = $('#prbfiles');
				var formURL = formObj.attr("action");
				var formData = new FormData(this);
				var inputdata = $("#inputdata").val();
				var ext = inputdata.split('.').pop();
				var title = '';
				var msg = '';

				e.preventDefault(); //Prevent Default action.
				$('#loading').modal('show');

				if ($("#inputdata").val() == '') {
					$('#loading').modal('hide');
					$('#msg').modal('show');
					msg = 'Please select a valid Text file.'
					title = '<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong>';
					$('#title').html(title);
					$('#err_msg').html(msg);
					// $.alert('Please select a valid Text file.', {
					// 	position: ['center', [-0.42, 0]],
					// 	type: 'danger',
					// 	closeTime: 3000,
					// 	autoClose: true
					// });
				}
				if (ext != 'txt') {
					$('#inputdata').val("");
					$('#loading').modal('hide');
					$('#msg').modal('show');
					msg = 'Please select a valid Text file. This module only accepts text file.'
					title = '<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong>';
					$('#title').html(title);
					$('#err_msg').html(msg);
					// $.alert('<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong> Please select a valid Text file. This module only accepts text file.', {
					// 	position: ['center', [-0.42, 0]],
					// 	type: 'danger',
					// 	closeTime: 3000,
					// 	autoClose: true
					// });
				}
				if (inputdata != ''){
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
							$('#btn-excel').html('');
							$('#inputdata').val("");
							$('#loading').modal('hide');
							$('#msg').modal('show');
							msg = 'File Successfully uploaded.'
							title = '<strong><i class="fa fa-exclamation-circle"></i> Success!</strong>';
							$('#title').html(title);
							$('#err_msg').html(msg);
							$('#btn-excel').append('<a href="{{url("/prbexcel")}}" class="btn btn-success">Excel</a>');
							// $.alert('File Successfully uploaded.', {
							// 	position: ['center', [-0.42, 0]],
							// 	type: 'success',
							// 	closeTime: 3000,
							// 	autoClose: true
							// });

						}).fail(function(jqXHR, textStatus, errorThrown) {
							$('#inputdata').val("");
							$('#loading').modal('hide');
							$('#msg').modal('show');
							msg = 'There is an error while uploading.'
							title = '<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong>';
							$('#title').html(title);
							$('#err_msg').html(msg);
							// $.alert('There is an error while uploading.', {
							// 	position: ['center', [-0.42, 0]],
							// 	type: 'danger',
							// 	closeTime: 3000,
							// 	autoClose: true
							// });
						});
					}
				}
			});
		});
	</script>
@endpush

