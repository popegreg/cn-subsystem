@extends('layouts.master')

@section('title')
	User Master | Pricon Microelectronics, Inc.
@endsection


@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_USERS'))
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
							<i class="fa fa-users"></i> USER MASTER
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							
							<div class="col-md-offset-2 col-md-8">
								<form class="form-horizontal">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-sm-4">First Name</label>
												<div class="col-sm-8">
													<input class="form-control input-sm" type="text" name="fname" id="fname" value="{{$userdetails->firstname}}"/>
													<input type="hidden" name="id" value="{{$userdetails->id}}">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-4">Middle Name</label>
												<div class="col-sm-8">
													<input class="form-control input-sm" type="text" name="mname" id="mname" value="{{$userdetails->middlename}}"/>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-4">Last Name</label>
												<div class="col-sm-8">
													<input class="form-control input-sm" type="text" name="lname" id="lname" value="{{$userdetails->lastname}}"/>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-sm-4">User ID</label>
												<div class="col-sm-8">
													<input class="form-control input-sm" type="text" name="user_id" id="user_id" value="{{$userdetails->user_id}}"/>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-4">Password</label>
												<div class="col-sm-8">
													<input class="form-control input-sm" type="password" name="pword" id="pword" value="{{$userdetails->actual_password}}"/>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-4">
													<input class="checkboxes" type="checkbox" name="locked" id="locked" @if($userdetails->locked == 1) {{"checked"}}@endif/> Locked
												</label>
												<label class="control-label col-sm-3">Product Line</label>
												<div class="col-sm-5">
													<select name="productline" id="productline" class="form-control input-sm">
														<option value=""></option>
														<option value="TS" @if($userdetails->productline == 'TS') {{"selected"}}@endif>TS</option>

														<option value="CN" @if($userdetails->productline == 'CN') {{"selected"}}@endif>CN</option>

														<option value="YF" @if($userdetails->productline == 'YF') {{"selected"}}@endif>YF</option>
													</select>
													<input type="hidden" name="_method" value="POST">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-4">
													{{-- <input class="checkboxes" type="checkbox" name="locked" id="locked"/> Locked --}}
												</label>
												<label class="control-label col-sm-3">Authorization</label>
												<div class="col-sm-5">
													<select name="Authorization" id="Authorization" class="form-control input-sm">
														<option value="0"@if($userdetails->Authorization == '0') {{"selected"}}@endif>Unauthorized</option>
														<option value="1" @if($userdetails->Authorization == '1') {{"selected"}}@endif>Authorized</option>
													</select>
													{{-- <input type="hidden" name="_method" value="POST"> --}}
												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="panel-group accordion scrollable" id="subsystems">
												<div class="panel panel-success">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#subsystems" href="#masters">
															Master Management </a>
														</h4>
													</div>
													<div id="masters" class="panel-collapse in">
														<div class="panel-body">
															<table class="table table-striped table-hover table-bordered">
																<thead>
																	<tr>
																		<td>Subsystem</td>
																		<td>Read / Write</td>
																		<td>Read Only</td>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($masters as $ms)
																		<tr>
																			<td>{{$ms->program_name}}</td>
																			<td>
																				<input type="checkbox" class="checkboxes rw" name="rw[]" value="{{$ms->program_code}}" {{$state}} 
																					@if (isset($progs[$ms->program_code]))
																						@if ($progs[$ms->program_code] == 1)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes r" name="r[]" value="{{$ms->program_code}}" {{$state}} 
																					@if (isset($progs[$ms->program_code]))
																						@if ($progs[$ms->program_code] == 2)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="panel panel-primary">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#subsystems" href="#operation">
															Operational Management</a>
														</h4>
													</div>
													<div id="operation" class="panel-collapse collapse">
														<div class="panel-body">
															<table class="table table-striped table-hover table-bordered" id="tbl_operation">
																<thead>
																	<tr>
																		<td>Subsystem</td>
																		<td>Read / Write</td>
																		<td>Read Only</td>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($operations as $op)
																		<tr>
																			<td>
																				{{$op->program_name}}
																				<input type="hidden" name="prog_code[]" value="{{ $op->program_code }}">
																				<input type="hidden" name="prog_name[]" value="{{ $op->program_name }}">
																			</td>
																			<td>
																				<?php
																					$idrw = ''; $idr = '';
																					if($op->program_code == 'SSS'){
																						$idrw = 'SSSrw';
																						$idr = 'SSSr';
																					}

																					if($op->program_code == 'WBS'){
																						$idrw = 'WBSrw';
																						$idr = 'WBSr';
																					}

																					if($op->program_code == 'QCDB'){
																						$idrw = 'QCDBrw';
																						$idr = 'QCDBr';
																					}

																					if($op->program_code == 'QCMLD'){
																						$idrw = 'QCMLDrw';
																						$idr = 'QCMLDr';
																					}
																				?>
																				<input type="checkbox" class="checkboxes rw <?php echo $idrw;?>" name="rw[]" value="{{$op->program_code}}" {{$state}}
																					@if (isset($progs[$op->program_code]))
																						@if ($progs[$op->program_code] == 1)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes r <?php echo $idr;?>" name="r[]" value="{{$op->program_code}}" {{$state}}
																					@if (isset($progs[$op->program_code]))
																						@if ($progs[$op->program_code] == 2)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="panel panel-warning">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#subsystems" href="#sss">
															Scheduling Support Subsystem </a>
														</h4>
													</div>
													<div id="sss" class="panel-collapse collapse">
														<div class="panel-body">
															<table class="table table-striped table-hover table-bordered">
																<thead>
																	<tr>
																		<td>Subsystem</td>
																		<td>Read / Write</td>
																		<td>Read Only</td>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($sssprog as $sssprg)
																		<tr>
																			<td>
																				{{$sssprg->program_name}}
																				<input type="hidden" name="prog_code[]" value="{{ $sssprg->program_code }}">
																				<input type="hidden" name="prog_name[]" value="{{ $sssprg->program_name }}">
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes rw sssrw" name="rw[]" value="{{$sssprg->program_code}}" {{$state}}
																					@if (isset($progs[$sssprg->program_code]))
																						@if ($progs[$sssprg->program_code] == 1)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes r sssr" name="r[]" value="{{$sssprg->program_code}}" {{$state}}
																					@if (isset($progs[$sssprg->program_code]))
																						@if ($progs[$sssprg->program_code] == 2)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="panel panel-danger">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#subsystems" href="#wbs">
															WBS </a>
														</h4>
													</div>
													<div id="wbs" class="panel-collapse collapse">
														<div class="panel-body">
															<table class="table table-striped table-hover table-bordered">
																<thead>
																	<tr>
																		<td>Subsystem</td>
																		<td>Read / Write</td>
																		<td>Read Only</td>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($wbsprog as $wbsprg)
																		<tr>
																			<td>
																				{{$wbsprg->program_name}}
																				<input type="hidden" name="prog_code[]" value="{{ $wbsprg->program_code }}">
																				<input type="hidden" name="prog_name[]" value="{{ $wbsprg->program_name }}">
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes rw wbsrw" name="rw[]" value="{{$wbsprg->program_code}}" {{$state}}
																					@if (isset($progs[$wbsprg->program_code]))
																						@if ($progs[$wbsprg->program_code] == 1)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes r wbsr" name="r[]" value="{{$wbsprg->program_code}}" {{$state}}
																					@if (isset($progs[$wbsprg->program_code]))
																						@if ($progs[$wbsprg->program_code] == 2)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="panel panel-success">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#subsystems" href="#qc">
															QC Database </a>
														</h4>
													</div>
													<div id="qc" class="panel-collapse collapse">
														<div class="panel-body">
															<table class="table table-striped table-hover table-bordered">
																<thead>
																	<tr>
																		<td>Subsystem</td>
																		<td>Read / Write</td>
																		<td>Read Only</td>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($qcdbprog as $qcdbprg)
																		<tr>
																			<td>
																				{{$qcdbprg->program_name}}
																				<input type="hidden" name="prog_code[]" value="{{ $qcdbprg->program_code }}">
																				<input type="hidden" name="prog_name[]" value="{{ $qcdbprg->program_name }}">
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes rw qcdbrw" name="rw[]" value="{{$qcdbprg->program_code}}" {{$state}}
																					@if (isset($progs[$qcdbprg->program_code]))
																						@if ($progs[$qcdbprg->program_code] == 1)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes r qcdbr" name="r[]" value="{{$qcdbprg->program_code}}" {{$state}}
																					@if (isset($progs[$qcdbprg->program_code]))
																						@if ($progs[$qcdbprg->program_code] == 2)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="panel panel-primary">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#subsystems" href="#qcm">
															QC Database Molding </a>
														</h4>
													</div>
													<div id="qcm" class="panel-collapse collapse">
														<div class="panel-body">
															<table class="table table-striped table-hover table-bordered" id="tbl_qcmld">
																<thead>
																	<tr>
																		<td>Subsystem</td>
																		<td>Read / Write</td>
																		<td>Read Only</td>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($qcmldprog as $qcmldprg)
																		<tr>
																			<td>
																				{{$qcmldprg->program_name}}
																				<input type="hidden" name="prog_code[]" value="{{ $qcmldprg->program_code }}">
																				<input type="hidden" name="prog_name[]" value="{{ $qcmldprg->program_name }}">
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes rw qcmldrw" name="rw[]" value="{{$qcmldprg->program_code}}" {{$state}}
																					@if (isset($progs[$qcmldprg->program_code]))
																						@if ($progs[$qcmldprg->program_code] == 1)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes r qcmldr" name="r[]" value="{{$qcmldprg->program_code}}" {{$state}}
																					@if (isset($progs[$qcmldprg->program_code]))
																						@if ($progs[$qcmldprg->program_code] == 2)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="panel panel-warning">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#subsystems" href="#sec">
															Security Management </a>
														</h4>
													</div>
													<div id="sec" class="panel-collapse collapse">
														<div class="panel-body">
															<table class="table table-striped table-hover table-bordered" id="tbl_sec">
																<thead>
																	<tr>
																		<td>Subsystem</td>
																		<td>Read / Write</td>
																		<td>Read Only</td>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($security as $sec)
																		<tr>
																			<td>
																				{{$sec->program_name}}
																				<input type="hidden" name="prog_code[]" value="{{ $sec->program_code }}">
																				<input type="hidden" name="prog_name[]" value="{{ $sec->program_name }}">
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes rw secrw" name="rw[]" value="{{$sec->program_code}}" {{$state}}
																					@if (isset($progs[$sec->program_code]))
																						@if ($progs[$sec->program_code] == 1)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes r secr" name="r[]" value="{{$sec->program_code}}" {{$state}}
																					@if (isset($progs[$sec->program_code]))
																						@if ($progs[$sec->program_code] == 2)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="panel panel-primary">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#subsystems" href="#YPICS">
															YPICS </a>
														</h4>
													</div>
													<div id="YPICS" class="panel-collapse collapse">
														<div class="panel-body">
															<table class="table table-striped table-hover table-bordered" id="tbl_sec">
																<thead>
																	<tr>
																		<td>Subsystem</td>
																		<td>Read / Write</td>
																		<td>Read Only</td>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($ypics as $yp)
																		<tr>
																			<td>
																				{{$yp->program_name}}
																				<input type="hidden" name="prog_code[]" value="{{ $yp->program_code }}">
																				<input type="hidden" name="prog_name[]" value="{{ $yp->program_name }}">
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes rw yprw" name="rw[]" value="{{$yp->program_code}}" <?php echo($state); ?>
																					@if (isset($progs[$yp->program_code]))
																						@if ($progs[$yp->program_code] == 1)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																			<td>
																				<input type="checkbox" class="checkboxes r ypr" name="r[]" value="{{$yp->program_code}}" <?php echo($state); ?> 
																					@if (isset($progs[$yp->program_code]))
																						@if ($progs[$yp->program_code] == 2)
																							{{"checked"}}
																						@endif
																					@endif
																				/>
																			</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							
						</div>

						<br/>

						<div class="row">
							<div class="col-md-12 text-center">
								<a href="javascript:;" class="btn btn-success btn-sm" id="btn_save">Save</a>
								<a href="{{ url('/usermaster') }}" class="btn grey-gallery btn-sm" >Back</a>
							</div>
						</div>

					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

	<!--msg-->
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

    <!--msg_success-->
    <div id="msg_success" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 id="success_title" class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p id="success_msg"></p>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button> --}}
                    <a href="{{url('/usermaster').'/'.$userdetails->id}}" class="btn btn-success" id="success_done">Done</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
	<script type="text/javascript">
		$('#btn_save').on('click', function() {
			var id = $('input[name="id"]').val();
            var url = '{{url("/update")}}'+'/'+id;
            var token = "{{ Session::token() }}";
            var method = $("input[name='_method']").val();
            var rw = $("input[name='rw[]']:checked").map( function() { return $(this).val(); } ).get();
            var r = $("input[name='r[]']:checked").map( function() { return $(this).val(); } ).get();
			var prog_code = $('input[name="prog_code[]"]').map( function() { return $(this).val(); }).get();
			var prog_name = $('input[name="prog_name[]"]').map( function() { return $(this).val(); }).get();
			var fname = $('input[name="fname"]').val(), mname = $('input[name="mname"]').val(),
			lname = $('input[name="lname"]').val(), user_id = $('input[name="user_id"]').val(),
			pword = $('input[name="pword"]').val(), locked = 0, 
			Authorization = $( "#Authorization option:selected" ).val(),
			productline = $( "#productline option:selected" ).val();

			if ($('input[name="locked"]').is(':checked')) {
				locked = 1;
			}

            var data = {
                _token: token,
                _method: method,
                r: r,
                rw: rw,
				prog_code: prog_code,
				prog_name: prog_name,
				fname: fname,
				mname: mname,
				lname: lname,
				user_id: user_id,
				pword: pword,
				locked: locked,
				Authorization:Authorization,
				productline: productline,
            };

            if (checkRequired() == true) {
            	$.ajax({
	                url: url,
	                type: "POST",
	                data: data,
	            }).done( function(data, textStatus, jqXHR) {
					$('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
					$('#success_msg').html('This User was successfully updated');
					$('#msg_success').modal('show');					
	            }).fail( function(data, textStatus, jqXHR) {
	                $('#msg').modal('show');
	                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
	                $('#err_msg').html("There's some error while processing.");
	            });
            } else {
            	$('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("Fill out some of the required fields");
            }
		});

		function checkRequired() {
			var fname = $('#fname').val();
			var lname = $('#lname').val();
			var user_id = $('#user_id').val();
			var pword = $('#pword').val();
			var productline = $('#productline').val();

			if (fname == '' || lname == '' || user_id == '' || pword == '' || productline == '') {
				return false;
			} else {
				return true;
			}
		}
	</script>
@endpush

