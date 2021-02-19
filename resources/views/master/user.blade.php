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

						{{-- <div class="row">
							<div class="col-md-8">
								<h3 class="pull-left">SEARCH</h3>
							</div>
							<div class="col-md-4">
								<a href="{{ url('/getexcel') }}" class="btn btn-warning pull-right"><i class="fa fa-users"></i> MRP USER</a>
							</div>
						</div> --}}
						<div class="row">
							
							<div class="col-md-offset-1 col-md-10">
								<table class="table table-striped table-bordered table-hover" id="sample_3">

									<thead>
										<tr>
											<td class="table-checkbox">
												<input type="checkbox" class="group-checkable" data-set="#sample_3 .checkboxes"/>
											</td>
											<td>User ID</td>
											<td>Last Name</td>
											<td>First Name</td>
											<td>Middle Name</td>
											<td>Product Line</td>
											<td>Last Date Logged In</td>
											<td width="15%">Actions</td>
										</tr>
									</thead>

									<tbody>
										@foreach($users as $user)
											<tr class="odd gradeX" data-id="{{ $user->id }}">
												<td>
													<input type="checkbox" class="checkboxes" id="check_id" name="check_id[]" value="{{ $user->id }}" data-userid="{{ $user->user_id }}" data-lname="{{ $user->lastname }}" data-fname="{{ $user->firstname }}" data-mname="{{ $user->middlename }}" data-pword="{{ $user->actual_password }}" data-locked="{{ $user->locked }}"/>
													{!! csrf_field() !!}
												</td>
												
												<td>
													<a href="{{url('/usermaster/'.$user->id)}}">{{ $user->user_id }}</a>
													<input type="hidden" name="user_id[]" value="{{ $user->user_id }}" />
												</td>
												<td>
													{{ $user->lastname }}
													<input type="hidden" name="lastname[]" value="{{ $user->lastname }}" />
												</td>
												<td>
													{{ $user->firstname }}
													<input type="hidden" name="firstname[]" value="{{ $user->firstname }}" />
												</td>
												<td>
													{{ $user->middlename }}
													<input type="hidden" name="middlename[]" value="{{ $user->middlename }}" />
												</td>
												<td>
													{{ $user->productline }}
													<input type="hidden" name="productline[]" value="{{ $user->productline }}" />
												</td>
												<td>
													{{ $user->last_date_loggedin }}
												</td>
												<td>
													@if (Auth::user()->user_id != $user->user_id)
														<a href="{{ url('/usermaster/'.$user->id) }}" class="btn btn-sm blue" <?php echo($state);?>>
															<i class="fa fa-edit"></i>
														</a>
														<a href="javascript:;" class="btn btn-sm red btn_delete" data-id="{{$user->id}}" <?php echo($state);?>>
															<i class="fa fa-trash"></i>
														</a>
													@endif
												</td>
											</tr>
										@endforeach
										
									</tbody>
								</table>
							</div>
							
						</div>

						<br/>

						<div class="row">
							<div class="col-md-12 text-center">
								<a href="{{ url('usermaster/create') }}" class="btn btn-success btn-sm" <?php echo($state); ?> id="btn_add" ><i class="fa fa-plus-square-o"></i> ADD</a>
							</div>
						</div>

					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

	<div id="confirm" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm gray-gallery">
			<form role="form" action="" id="form_del" method="post">
				<div class="modal-content ">
					<div class="modal-body">
						<p>Are you sure you want to delete this user?</p>
						{!! csrf_field() !!}
						<input type="hidden" name="id"/>
					</div>
					<div class="modal-footer">
						<a href="javascript:;" class="btn btn-primary btn-sm" id="delete_now">Delete</a>
						<button type="button" data-dismiss="modal" class="btn red btn-sm">Cancel</button>
					</div>
				</div>
			</form>
		</div>
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
                    <a href="{{url('/usermaster')}}" class="btn btn-success" id="success_done">Done</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
	<script type="text/javascript">
		$('.btn_delete').on('click', function() {
			var id = $(this).attr('data-id');
			var action = '{{url("/destory")}}'+'/'+id;
			$('#confirm').modal('show');
			$('input[name="id"]').val(id);
			$('#form_del').attr('action', action);
		});

		$('#delete_now').on('click', function() {
			var id = $('input[name="id"]').val();
			var url = '{{url("/destory")}}'+'/'+id;
            var token = "{{ Session::token() }}";

            var data = {
                _token: token,
                id: id,
            };

        	$.ajax({
                url: url,
                type: "POST",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
            	if (data.status == 'success') {
            		$('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
					$('#success_msg').html(data.msg);
					$('#msg_success').modal('show');
            	} else {
            		$('#msg').modal('show');
	                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
	                $('#err_msg').html(data.msg);
	                $('#confirm').modal('hide');
            	}
				
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
		});
	</script>
@endpush