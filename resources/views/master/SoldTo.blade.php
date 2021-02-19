@extends('layouts.master')
@section('title')
	Sold Master | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_SLDTO'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
							<i class="fa fa-dollar"></i>  Sold To Master
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-sm-8 col-sm-offset-2 table-responsive">										
								<table class="table table-striped table-bordered table-hover" id="sample_3" style="font-size: 12px;">
										<thead>
											<tr>
												<th class="table-checkbox" style="width: 5%">
													<input type="checkbox" class="group-checkable checkAllitems" data-set="#sample_3 .checkboxes"/>
												</th>
												<th></th>
												<th>Code</th>
												<th>Company Name</th>
												<th>Description</th>
											</tr>
										</thead>

										<tbody>
											@foreach ($tableData as $dest)
												<tr>
													
													<td style="width: 5%">
							                           <input type="checkbox" class="form-control input-sm checkboxes" name="checkitem" id="checkitem" value="{{$dest->id}}"></input>
						            				</td>
						   															
													<td style="width: 7%">
							                           
							                            <button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="{{$dest->id . '|' . $dest->code . '|' . $dest->companyname. '|' .$dest->description}}">
						                						<i class="fa fa-edit"></i> 
						            					</button>
						            				</td>
						            				<td>{{$dest->code}}</td>
													<td>{{$dest->companyname}}</td>
													<td><div style="white-space: pre-wrap;">{{$dest->description}}</div></td>
												</tr>
											@endforeach
										</tbody>
									</table>									
							</div>
						</div>

						<div class="row" style="margin-top: 30px;">
							<div class="col-sm-4 col-sm-offset-5">
								<a href="#" id="add" class="btn btn-success input-sm">
									<i class="fa fa-plus-square-o"></i> Add
								</a>
								<button type="submit" class="btn btn-danger input-sm deleteAll-task">
									<i class="fa fa-trash"></i> Delete 
								</button>
							</div>
						</div>


					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

	<!-- Add Modal -->
	<div id="soldtoModal" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog gray-gallery">
			<div class="modal-content ">
				<div class="modal-header">
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12">
							<form method="POST" class="form-horizontal" id="wbsfrmsml">
								{{ csrf_field() }}
								<div class="form-group">
									<div class="col-sm-7">
										<p>
											Value field is required.
										</p>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Code</label>
									<div class="col-sm-9">
										<input type="text" class="form-control input-sm" id="code" name="code">
										<div id="er1"></div>
									</div>
								</div>		
								<div class="form-group">
									<label class="control-label col-sm-3">Company Name</label>
									<div class="col-sm-9">
										<input type="text" class="form-control input-sm" id="compname" name="compname" maxlength="40">
										<div id="er2"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Description</label>
									<div class="col-sm-9">
										<textarea rows="5" cols="40" name="description" id="description" class="form-control" style="resize:none" ></textarea>
										<div id="er3"></div>
									</div>
								</div>		
								
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<input type="hidden" class="form-control input-sm" id="masterid" name="masterid" maxlength="40" >
					<input type="hidden" class="form-control input-sm" id="hdnaction" name="hdnaction" maxlength="40" value="ADD">
					<button type="button" onclick="javascript:Add_Records();" class="btn btn-success">Save</button>
					<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
				</div>
			</div>
			</form>
		</div>
	</div>
	<!-- End of Add Modal -->


	<!--delete all modal-->
	<div id="deleteAllModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm gray-gallery">

								<!-- Modal content-->
			<form class="form-horizontal" id="deleteAllform" role="form">
				<div class="modal-content ">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="deleteAll-title">Delete Sold Master Settings</h4>
					</div>
					<div class="modal-body">
						<div class="row">

							{!! csrf_field() !!}
							<div class="col-sm-12">
								<label for="inputname" class="col-sm-12 control-label text-center">
								Are you sure you want to delete all record/s?
								</label>
								<input type="hidden" value="" name="deleteAllmaster" id="deleteAllmaster" />
							</div>	
						</div>
					</div>
					<div class="modal-footer">
						<a href="#" class="btn btn-success" id="modaldelete" ><i class="fa fa-save"></i> Yes</a>
						<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- 	Success Message Modal -->
	<div id="confirmModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm gray-gallery">

								<!-- Modal content-->
			<form class="form-horizontal" id="confirmForm" role="form" method="POST">
				<div class="modal-content ">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="deleteAll-title" id="modalTitle"></h4>
					</div>
					<div class="modal-body">
						<div class="row">

							{!! csrf_field() !!}
							<div class="col-sm-12">
								<label for="confirmMessage" id="confirmMessage" class="col-sm-12 control-label text-center">
								
								</label>
							</div>	
						</div>
					</div>
					<div class="modal-footer">
						<a href="javascript:;" class="btn btn-success" id="confirmOk" ><i class="fa fa-save"></i>OK</a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- End of Add Modal -->
@endsection


@push('script')
	<script>
		var token = '{{ Session::token() }}';
		var SoldToUrl = "{{ url('/sold-to') }}";
		var DeleteTaskUrl = "{{ url('/delete-sold') }}";
		var DeleteAll = "{{ url('/deleteAll-sold') }}";
		var AddUrl = "{{ url('/add-sold') }}";
		var UpdateUrl = "{{ url('/update-sold') }}";
	</script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/soldto.js') }}" type="text/javascript"></script>
@endpush
