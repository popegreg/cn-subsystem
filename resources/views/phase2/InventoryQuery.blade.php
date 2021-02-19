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
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cubes"></i>  YPICS STOCKS QUERY
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="portlet box blue-hoki">
									<div class="portlet-body">
										<div class="row">
											<div class="col-md-2 col-md-offset-1">
												<div class="form-group">
													<a href="{{url('/inventoryquerybyparts')}}" id="byparts" class="btn btn-md btn-warning btn-lg" {{$state}}>
														BY PARTS
													</a>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<a href="{{url('/inventoryquerybybom')}}" id="bybom" class="btn btn-md btn-primary btn-lg" {{$state}}>
														BY BOM
													</a>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<a href="{{url('/inventoryqueryrebom')}}" id="rebom" class="btn btn-md btn-success btn-lg" {{$state}}>
														REBOM
													</a>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<button id="btn_extract" class="btn btn-md btn-danger btn-lg" {{$state}}>
														EXTRACT
													</button>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<form class="" action="{{ url('/updatestock') }}" method="post" id="frm">
														{{ csrf_field() }}
														<a href="javascript:;" id="update" class="btn btn-md yellow btn-lg" {{$state}}>
															UPDATE
														</a>
													</form>

												</div>

											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-12" style="height:30px">
												<div class="progress">
													<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
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

@push('script')
<script type="text/javascript">
	$( document ).ready(function(e) {
		$('#update').on('click', function(e) {
			//$('#frm').submit();
			update(e);
			//$('#loading').modal('show');
		});

		$('#btn_extract').on('click', function(e) {
			e.preventDefault();
			var token = "{{ Session::token() }}";
			window.location.href= "{{ url('/stockqueryxls') }}"+ "?_token="+token;
		});

		function update(e) {
			var url = "{{ url('/updatestock') }}";
			var token = "{{ Session::token() }}";
			var data = {
				_token : token,
			};


			$('#loading').modal('show');
			$.ajax({
				url: url,
				method: 'POST',
				data:  data,
			}).done( function(data, textStatus, jqXHR) {
				console.log(data)
				$('#loading').modal('hide');
				$('#msg').modal('show');
				msg = 'Successfully updated.'
				title = '<strong><i class="fa fa-exclamation-circle"></i> Success!</strong>';
				$('#title').html(title);
				$('#err_msg').html(msg);
			}).fail( function(data, textStatus, jqXHR) {
				$('#loading').modal('hide');
				$('#msg').modal('show');
				msg = 'There is an error while updating.'
				title = '<strong><i class="fa fa-exclamation-circle"></i> Failed!</strong>';
				$('#title').html(title);
				$('#err_msg').html(msg);
			});
		}

	});
</script>
@endpush
