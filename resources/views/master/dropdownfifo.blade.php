@extends('layouts.master')

@section('title')
Dropdown Master | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_DESTI'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
				<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach

	<div class="page-content">
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-sm-12">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				@include('includes.message-block')
				<div class="portlet box blue" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-list"></i>  Dropdown FiFo Master
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
						</div>
						<div class="row">
							<div class="col-sm-8 col-sm-offset-2">
								<table id="tbl_reason" class="table table-striped table-bordered" style="width:100%">
							        <thead>
							            <tr class="">
								            <th><input type="checkbox" class="checkall" id="check_all" /></th>	
							            	<th></th>
							                <th>ID</th>
							                <th>Reason</th>
							            </tr>
							        </thead>
							        <tbody>
							            <tr>
							            	<td></td>
							            	<td></td>
							                <td></td>
							                <td></td>
							            </tr>
							        </tbody>
							    </table>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-4 col-sm-offset-5" style="margin-top: 30px;">
										<button type="button" name="add_reason" id="add_reason" class="btn btn-success btn-sm" <?php echo($state); ?> >
											<i class="fa fa-plus-square-o"></i> Add
										</button>
										<button type="button" id="delete_button" class="btn btn-danger btn-sm deleteAll-task">
											<i class="fa fa-trash"></i> Delete
										</button>
							</div>
						</div>

					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->

				<!-- Modal -->
				<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
					<div class="modal-dialog modal-md gray-gallery">
							<div class="modal-content ">
								<div class="modal-header">
								
									<h4 class="modal-title">Add Reason</h4>
								</div>
								<div class="modal-body">
									<div class="row">

										{!! csrf_field() !!}
										<div class="col-sm-12">

											<div class="form-group">
												<label for="inputname" class="col-sm-2 control-label">*Reason</label>
												<div class="col-sm-8">
													<input type="text" class="form-control input-sm" pattern="(^$|^.*@.*\..*$)" required="	" autocomplete="off" id="new_reason" name="new_reason" autofocus maxlength="40">
													<input type="hidden" class="form-control input-sm" autocomplete="off" id="id" name="id" autofocus maxlength="40">
													<div id="er1"></div>
													
												</div>


											</div>
										</div>

									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-success btn-sm" id="bt_reason_save" ><i class="fa fa-save"></i> Save</button>
									<button type="button" id="btn_close_modal" name="btn_close_modal" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Close</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!-- Modal -->
				<div id="" class="modal fade" role="dialog">

					
				</div>
				<!--delete all modal-->
				<!--delete all modal-->
				<div id="deleteAllModal"  class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
					<div class="modal-dialog modal-sm gray-gallery">

						<!-- Modal content-->
						<form class="form-horizontal" id="deleteAllform" role="form">
							<div class="modal-content ">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="deleteAll-title">Delete Item</h4>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-sm-12">
											<label for="inputname" class="col-sm-12 control-label text-center">
												Are you sure you want to delete record/s?
											</label>
											<input type="hidden" value="" name="id_reason" id="id_reason" />
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" id="btn_delete" class="btn btn-success btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Yes</button>
									<button type="button" id="btn_close" class="btn btn-danger btn-sm" ><i class="fa fa-times"></i> No</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<!--delete Category-->
				<div id="deleteCatModal" class="modal fade" role="dialog">
					
				</div>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
	
@include('includes.modals')	
@endsection

@push('script')
	<script>
		var token = '{{ Session::token() }}';
		GetReasonURL = "{{ url('/get-reason') }}";
		AddURL = "{{ url('/add-reason') }}";
		UpdateURL = "{{url('/update-reason')}}";
		DeleteURL = "{{url('/delete-reason')}}";
		CheckReasonURL = "{{url('/check-reason')}}";
	</script>

  <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/dropdownfifo.js') }}" type="text/javascript"></script>

@endpush