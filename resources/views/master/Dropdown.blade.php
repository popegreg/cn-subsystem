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
							<i class="fa fa-truck"></i>  Dropdown
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
							<div class="col-sm-8 col-sm-offset-2">
								<div class="form-group">
									<div class="col-sm-2">
										<label class="control-label">Dropdown Name: </label>
									</div>
									<div class="col-sm-6">
										<select class="form-control input-sm" name="master" id="master">
											@if(isset($category))
											@foreach ($category as $type)
											<option value="{{ $type->id }}" <?php if($selected_category==$type->id){ echo 'selected';} ?> >{{ $type->category }}</option>
											@endforeach
											@endif
										</select>
									</div>
									<div class="col-sm-3">
										<button type="button" name="add_category" class="btn btn-sm btn-primary add_category" <?php echo($state); ?> >
											<i class="fa fa-plus"></i>
										</button>
										<button type="button" name="edit_category" class="btn btn-sm blue edit_category" <?php echo($state); ?> >
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" name="del_category" data-toggle="modal" data-target="#deleteCatModal" class="btn btn-sm btn-danger del_category" <?php echo($state); ?> >
											<i class="fa fa-trash"></i>
										</button>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-8 col-sm-offset-2">
								<table class="table table-striped table-bordered table-hover" id="sample_3">
									<thead>
										<tr>
											<th class="table-checkbox" style="width: 5%">
												<input type="checkbox" class="group-checkable checkAllitems" data-set="#sample_3 .checkboxes"/>
											</th>
											<th></th>
											<th>Description</th>
										</tr>
									</thead>

									<tbody>
										@foreach ($dropdownlist as $data)
										<tr>
											<td style="width: 5%">
												<input type="checkbox" class="form-control input-sm checkboxes" name="checkitem" id="checkitem" value="{{$data->id}}"></input>
											</td>
											<td style="width: 7%">

												<button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="{{$data->id . '|' . $data->description}}" <?php echo($state); ?>>
													<i class="fa fa-edit"></i>
												</button>
											</td>
											<td>{{$data->description}}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-4 col-sm-offset-5" style="margin-top: 30px;">
										<button type="button" name="add" id="add" class="btn btn-success btn-sm" <?php echo($state); ?> >
											<i class="fa fa-plus-square-o"></i> Add
										</button>
										<button type="button" id="deleteAll" class="btn btn-danger btn-sm deleteAll-task" <?php echo($state); ?> >
											<i class="fa fa-trash"></i> Delete
										</button>
							</div>
						</div>

					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->

				<!-- Modal -->
				<div id="myModal" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm gray-gallery">
						<!-- Modal content-->
						<form class="form-horizontal" id="destinationform" role="form" method="POST" action="{{ url('/dropdown-save') }}">
							<div class="modal-content ">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title"></h4>
								</div>
								<div class="modal-body">
									<div class="row">

										{!! csrf_field() !!}
										<div class="col-sm-12">

											<div class="form-group">
												<label for="inputname" class="col-sm-4 control-label">*Description</label>
												<div class="col-sm-8">
													<input type="text" class="form-control input-sm" id="inputname" name="description" autofocus maxlength="40">
													<div id="er1"></div>
													<input type="hidden" value="" name="dbmaster" id="dbmaster" />
												</div>


											</div>
										</div>

									</div>
								</div>
								<div class="modal-footer">
									<input type="hidden" class="form-control input-sm" id="masterid" name="masterid" maxlength="40" >
									<input type="hidden" class="form-control input-sm" id="itemid" name="itemid" maxlength="40" >
									<input type="hidden" class="form-control input-sm" id="hdnaction" name="action" maxlength="40" value="ADD">
									<button type="submit" class="btn btn-success btn-sm" id="modalsave" ><i class="fa fa-save"></i> Save</button>
									<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!-- Modal -->
				<div id="myModalCategory" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm gray-gallery">
						<!-- Modal content-->
						<form class="form-horizontal" id="destinationform" role="form" method="POST" action="{{ url('/dropdown-cat-save') }}">
							<div class="modal-content ">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title"></h4>
								</div>
								<div class="modal-body">
									<div class="row">

										{!! csrf_field() !!}
										<div class="col-sm-12">

											<div class="form-group">
												<label for="inputname" class="col-sm-5 control-label input-sm">*Category Name</label>
												<div class="col-sm-7">
													<input type="text" class="form-control input-sm" id="inputcatname" name="category" autofocus maxlength="40">
													<div id="er1"></div>
													<input type="hidden" value="" name="dbmaster" id="dbmaster" />
												</div>


											</div>
										</div>

									</div>
								</div>
								<div class="modal-footer">
									<input type="hidden" class="form-control input-sm" id="catmasterid" name="masterid" maxlength="40" >
									<input type="hidden" class="form-control input-sm" id="hdncataction" name="action" maxlength="40" value="ADD">
									<button type="submit" class="btn btn-success btn-sm" id="modalsave" ><i class="fa fa-save"></i> Save</button>
									<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<!--delete all modal-->
				<div id="deleteAllModal" class="modal fade" role="dialog">
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

										{!! csrf_field() !!}
										<div class="col-sm-12">
											<label for="inputname" class="col-sm-12 control-label text-center">
												Are you sure you want to delete record/s?
											</label>
											<input type="hidden" value="" name="deleteAllmaster" id="deleteAllmaster" />
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<a href="#" class="btn btn-success btn-sm" id="modaldelete" ><i class="fa fa-save"></i> Yes</a>
									<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<!--delete Category-->
				<div id="deleteCatModal" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm gray-gallery">

						<!-- Modal content-->
						<form class="form-horizontal" id="deleteCatModal" role="form"  method="POST"  action="{{ url('/dropdown-cat-delete') }}">
							<div class="modal-content ">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="deleteAll-title">Delete Category</h4>
								</div>
								<div class="modal-body">
									<div class="row">

										{!! csrf_field() !!}
										<div class="col-sm-12">
											<label for="inputname" class="col-sm-12 control-label text-center">
												Are you sure you want to delete this Category?
											</label>
											<input type="hidden" value="<?php echo $selected_category;?>" name="catid" id="catid" />
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Yes</button>
									<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
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
									<a href="javascript:;" class="btn btn-success btn-sm" id="confirmOk" ><i class="fa fa-save"></i>OK</a>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!---->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
		
@endsection

@push('script')
	<script>
		var token = '{{ Session::token() }}';
		var SelectedUrl = "{{ url('/dropdown?option=') }}";
		var DeleteUrl = "{{ url('/dropdown-delete') }}";
	</script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/dropdown.js') }}" type="text/javascript"></script>
@endpush