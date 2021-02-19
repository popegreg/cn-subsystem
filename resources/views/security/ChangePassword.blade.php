@extends('layouts.master')

@section('title')
	Change Password | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php ini_set('max_input_vars', 999999);?>
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_SEC'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach

	<div class="page-content">

		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
				@include('includes.message-block')
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-user"></i> Account Information
						</div>
					</div>
					<div class="portlet-body portlet-empty">

						<div class="row">
							<form class="form-horizontal" role="form" method="POST" action="{{ url('/changepasswordnow') }}">
								{!! csrf_field() !!}
								@foreach ($users as $user)
									<div class="col-md-6">
										<div class="form-group">
											<label for="inputuserid" class="col-md-4 control-label">*User ID</label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="user_id" id="userid" value="{{$user->user_id}}" disabled="disable">
											</div>
										</div>
										<div class="form-group">
											<label for="inputlname" class="col-md-4 control-label">Last Name</label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="lname" name="lastname" disabled="disable" value="{{$user->lastname}}">
											</div>
										</div>
										<div class="form-group">
											<label for="inputfname" class="col-md-4 control-label">First Name</label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="fname" name="firstname" disabled="disable" value="{{$user->firstname}}">
											</div>
										</div>
										<div class="form-group">
											<label for="inputmname" class="col-md-4 control-label">Middle Name</label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="mname" name="middlename" disabled="disable" value="{{$user->middlename}}">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="inputPassword" class="col-md-4 control-label">*Old Password</label>
											<div class="col-md-8">
												<input type="password" class="form-control" id="OldPass" placeholder="Old Password" name="OldPass" disabled="disable">
											</div>
										</div>
										<div class="form-group">
											<label for="inputPassword" class="col-md-4 control-label">*New Password</label>
											<div class="col-md-8">
												<input type="password" class="form-control" id="NewPass" placeholder="New Password" name="NewPass" disabled="disable">
											</div>
										</div>
										<div class="form-group">
											<label for="inputPassword" class="col-md-4 control-label">*Confirm Password</label>
											<div class="col-md-8">
												<input type="password" class="form-control" id="ConPass" placeholder="Confirm Password" name="ConPass" disabled="disable">
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12">
												<button type="submit" id="save" class="btn btn-sm green pull-right">
													<i class="fa fa-floppy-o"></i> Save
												</button>

												<a id="change" class="btn btn-sm yellow pull-right">
													<i class="fa fa-unlock"></i> Change
												</a>
											</div>
										</div>

									</div>
								@endforeach
							</form>
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
	$( document ).ready(function() {
		$('#change').on('click', function(){
			$('#userid').removeAttr("disabled");
			$('#NewPass').removeAttr("disabled");
			$('#OldPass').removeAttr("disabled");
			$('#ConPass').removeAttr("disabled");
		});
	});

</script>
