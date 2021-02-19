@extends('layouts.master')

@section('title')
	PR Change | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_PRCHANGE'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
							<i class="fa fa-file-o"></i>  PR CHANGE
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
							<div class="col-md-12">
								<div class="portlet box blue-hoki">
									<div class="portlet-body">
										<div class="row">
											<div class="col-md-12">
												<form method="POST" method="POST" action="{{ url('/uploadOrigPR') }}" accept-charset="UTF-8" enctype="multipart/form-data" class="form-horizontal" id="origPRform" >
													{{ csrf_field() }}

													<div class="form-group">
														<label class="control-label col-md-2">ORIGINAL PR</label>
														<div class="col-md-7">
															<input type="file" class="filestyle" data-buttonName="btn-primary" name="originalpr" id="originalpr" {{$readonly}}>
														</div>
														<div class="col-md-3">
															<button type="submit" id="origpr" class="btn btn-md btn-warning" {{$state}}>
																<i class="fa fa-upload"></i> Upload Original PR
															</button> <!-- type="submit" -->
														</div>
													</div>
												</form>
												<form method="POST" method="POST" action="{{ url('/uploadChangePR') }}" accept-charset="UTF-8" enctype="multipart/form-data" class="form-horizontal" id="changePRform" >
													{{ csrf_field() }}

													<div class="form-group">
														<label class="control-label col-md-2">CHANGE PR</label>
														<div class="col-md-7">
															<input type="file" class="filestyle" data-buttonName="btn-primary" name="changepr" id="changepr" {{$readonly}}>
														</div>
														<div class="col-md-3">
															<button type="submit" id="chpr" class="btn btn-md btn-warning" {{$state}}>
																<i class="fa fa-upload"></i> Upload Extract Change PR
															</button> <!-- type="submit" -->
														</div>
													</div>
												</form>
											</div>
										</div>

										@if (Session::has('download'))
											<div class="row">
												<div class="col-md-3 col-md-offset-4">
													<a href="{{ url('/download-pr-output') }}" class="btn btn-success">Download Output File</a>
												</div>
											</div>
										@endif
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

	<!-- ORIG PR -->

	<div id="opr" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-body">
					<p>{{Session::get('prorig_modal')}}</p>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
				</div>
			</div>
		</div>
	</div>
		
	<!-- CHANGE PR -->
	<div id="cpr" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-body">
					<p>{{Session::get('prchange_modal')}}</p>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
				</div>
			</div>
		</div>
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

@if (Session::has('prorig_modal'))
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('#opr').modal('show');
		});
	</script>
@endif

@if (Session::has('prchange_modal'))
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('#cpr').modal('show');
		});
	</script>
@endif

	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('#origPRform').on('submit', function(){
				$('#loading').modal('show');
			});
			$('#changePRform').on('submit', function(){
				$('#loading').modal('show');
			});
		});
	</script>
@endsection