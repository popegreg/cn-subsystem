@extends('layouts.master')
@section('title')
	Company Settings | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_COMSET'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
							<i class="fa fa-building"></i>  Company Settings
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
							<div class="col-md-6 col-md-offset-3">
								<div class="portlet box">
									<div class="portlet-body">
										<div class="row">
											<div class="col-sm-12">
												<?php
												if($count == 0){
													$name = "";
													$address = "";
													$tel1 = "";
													$tel2 = "";
												} else {
													foreach ($tableData as $dest)
													{
														$name = $dest->name;
														$address = $dest->address;
														$tel1 = $dest->tel1;
														$tel2 = $dest->tel2;
													}
												}															
												?>													
												<form method="POST" action="{{url('/update-companysetting')}}" class="form-horizontal" id="compfrm">
													{{ csrf_field() }}
													<div class="form-group">
														<label class="control-label col-sm-2">Name</label>
														<div class="col-sm-9">
															<input type="text" value="{{$name}}" class="form-control input-sm" id="name" name="name">
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-sm-2">Address</label>
														<div class="col-sm-9">
															<textarea name="address" id="address" class="form-control" style="resize:none" maxlength="100" required>{{$address}}</textarea>
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-sm-2">Tel. No. (1)</label>
														<div class="col-md-4">
															<input class="form-control mask_phone" type="text" name="tel1" value="{{$tel1}}" />
														</div>
													</div>
													<div class="form-group">
														<label class="control-label col-md-2">Tel. No. (2)</label>
														<div class="col-md-4">
															<input class="form-control mask_phone" type="text" name="tel2" value="{{$tel2}}" />
														</div>
													</div>
													<div class="form-group">
														<div class="col-sm-11">
															<button type="submit" class="btn btn-sm btn-success pull-right">
																<i class="fa fa-floppy-o"></i> Save
															</button>
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

@endsection

<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	$( document ).ready(function(e) {

		 

	});

	
</script>
