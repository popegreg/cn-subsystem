@extends('layouts.master')

@section('title')
	Product Line Master | Pricon Microelectronics, Inc.
@endsection


@section('content')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_PRODUCT'))
			@if ($access->read_write == "2")
				<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach

	<div class="page-content">
		
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-offset-2 col-md-8">
				@include('includes.message-block')
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cart-plus"></i> PRODUCT LINE MASTER
						</div>
					</div>
					<div class="portlet-body">
						<br/>

						
						<div class="row">
							
							<div class="col-md-12">
								<table class="table table-striped table-bordered table-hover" id="sample_3">

									<thead>
										<tr>
											<th class="table-checkbox">
												<input type="checkbox" class="group-checkable" data-set="#sample_3 .checkboxes"/>
											</th>
											<th>
												CODE
											</th>
											<th>
												NAME
											</th>
										</tr>
									</thead>

									<tbody>
										@foreach($productlines as $productline)
											<tr class="odd gradeX" data-id="{{ $productline->id }}">
												<td>
													<input type="checkbox" class="checkboxes" id="check_id" name="check_id[]" value="{{ $productline->id }}" data-code="{{ $productline->code }}" data-name="{{ $productline->name }}"/>
													{!! csrf_field() !!}
												</td>
												
												<td>
													{{ $productline->code }}
												</td>
												<td>
													{{ $productline->name }}
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
								<div class="btn-group">
									<button type="button" data-toggle="modal" data-target="#myModal" class="btn btn-success input-sm" <?php echo($state); ?> ><i class="fa fa-plus-square-o"></i> ADD</button>
									<a href="#" id="editbtnprod"  class="btn btn-primary input-sm"><i class="fa fa-edit"></i> EDIT</a>
									<a href="#" id="delbtnprod" class="btn btn-danger input-sm" <?php echo($state); ?> ><i class="fa fa-trash-o"></i> DELETE</a><!--data-toggle="modal" data-target="#deleteModal"-->
									<!--data-toggle="modal" data-target="#myModal"-->
								</div>
							</div>
						</div>

						

					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->

				<!-- Modal -->
				<div id="myModal" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm gray-gallery">

						<!-- Modal content-->
						<form class="form-horizontal" role="form" method="POST" action="{{ url('/add-product') }}">
							<div class="modal-content ">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">ADD / EDIT PRODUCT LINE</h4>
								</div>
								<div class="modal-body">
									<div class="row">
										
										{!! csrf_field() !!}
										<div class="col-md-12">
											<div class="form-group">
												<label for="inputcode" class="col-md-4 control-label">*Code</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="inputcode" placeholder="Code" name="code" <?php echo($state); ?> >
												</div>
											</div>
											<div class="form-group">
												<label for="inputname" class="col-md-4 control-label">*Name</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="inputname" placeholder="Name" name="name" <?php echo($state); ?> >
												</div>
											</div>
										</div>
										
									</div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-success" id="modalsave" <?php echo($state); ?> ><i class="fa fa-save"></i> Save</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<div id="confirm" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm gray-gallery">
						<form role="form" method="POST" action="{{ url('/delete-product') }}">
							<div class="modal-content ">
								<div class="modal-body">
									<p>Are you sure you want to delete this Product Line?</p>
									{!! csrf_field() !!}
									<input type="hidden" name="id" id="id"/>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-primary" id="delete">Delete</button>
									<button type="button" data-dismiss="modal" class="btn">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<div id="editModal" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm gray-gallery">

						<!-- Modal content-->
						<form class="form-horizontal" role="form" method="POST" action="{{ url('/edit-product') }}">
							<div class="modal-content ">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">ADD / EDIT PRODUCT LINE</h4>
								</div>
								<div class="modal-body">
									<input type="hidden" name="id" id="id"/>
									<div class="row">
										
										{!! csrf_field() !!}
										<div class="col-md-12">
											<div class="form-group">
												<label for="inputcode" class="col-md-4 control-label">*Code</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="editcode" placeholder="Code" name="code" <?php echo($state); ?> >
												</div>
											</div>
											<div class="form-group">
												<label for="inputname" class="col-md-4 control-label">*Name</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="editname" placeholder="Name" name="name" <?php echo($state); ?> >
												</div>
											</div>
										</div>
										
									</div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-success" id="modalsave" <?php echo($state); ?> ><i class="fa fa-save"></i> Save</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

@endsection

@push('script')
	<script>
		var token = '{{ Session::token() }}';
		var url = '{{ url('/edit-user') }}';
	</script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/productline.js') }}" type="text/javascript"></script>
@endpush