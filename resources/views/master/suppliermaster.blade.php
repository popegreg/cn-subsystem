<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: suppliermaster.blade.php
     MODULE NAME:  [2002] Supplier Master
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.13
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.13     MESPINOSA       Initial Draft
*******************************************************************************/
?>

@extends('layouts.master')



@section('title')
	Supplier Master | Pricon Microelectronics, Inc.
@endsection

@section('content')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_SUPPLIER'))
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
								<i class="fa fa-log-in"></i> SUPPLIER MASTER
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
														<input type="checkbox" class="group-checkable" data-set="#sample_3 .checkboxes" <?php echo($state); ?> />
													</th>
													<th>
														CODE
													</th>
													<th>
														NAME
													</th>
													<th>
														ADDRESS
													</th>
												</tr>
											</thead>

											<tbody>
												@foreach($suppliers as $supplier)
													<tr class="odd gradeX" data-id="{{ $supplier->id }}">
														<td>
															<input type="checkbox" class="checkboxes" name="check_id[]" value="{{ $supplier->id }}" />
															{!! csrf_field() !!}
														</td>
														<td>
															{{ $supplier->code }}
														</td>
														<td>
															{{ $supplier->name }}
														</td>
														<td>
															{{ $supplier->address }}
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
											<button type="button" data-toggle="modal" data-target="#addModal" class="btn btn-success input-sm" <?php echo($state); ?> >
											<i class="fa fa-plus-square-o"></i> ADD</button>
											<button type="button" onclick="javascript:action('EDIT');" class="btn btn-primary input-sm">
											<i class="fa fa-edit"></i> EDIT</button>
											<button type="button" onclick="javascript:action('DELETE');" class="btn btn-danger input-sm" <?php echo($state); ?> >
											<i class="fa fa-trash-o"></i> DELETE</button> 
											<!--data-toggle="modal" data-target="#deleteModal"-->
										</div>
									</div>
								</div>

							

						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->

					<!-- Add Modal -->
					<div id="addModal" class="modal fade" role="dialog">
						<div class="modal-dialog modal-lg">

							<!-- Modal content-->
							<form class="form-horizontal" role="form" method="POST" action="{{ url('/register-supplier') }}">
								<div class="modal-content blue">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">ADD\EDIT SUPPLIER</h4>
									</div>
									<div class="modal-body">
										<div class="row">
											
												{!! csrf_field() !!}
												<div class="col-md-6">
													<div class="form-group">
														<label for="inputcode" class="col-md-4 control-label">*Code</label>
														<div class="col-md-8">
															<input type="text" class="form-control" id="inputCode" placeholder="Code" name="code" autofocus>
														</div>
													</div>
													<div class="form-group">
														<label for="inputname" class="col-md-4 control-label">*Name</label>
														<div class="col-md-8">
															<input type="text" class="form-control" id="inputName" placeholder="Name" name="name">
														</div>
													</div>
													<div class="form-group">
														<label for="inputaddress" class="col-md-4 control-label">Address</label>
														<div class="col-md-8">
															<textarea rows="5" cols="40" class="form-control" id="inputAddress" placeholder="Address" name="address" style="resize: none;" ></textarea>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="inputtelno" class="col-md-4 control-label">Tel No</label>
														<div class="col-md-8">
															<input type="text" class="form-control" id="inputTelNo" placeholder="Tel No" name="telno">
															<span class="help-block">(999) 999-9999 </span>
														</div>
													</div>
													<div class="form-group">
														<label for="inputfaxno" class="col-md-4 control-label">Fax No</label>
														<div class="col-md-8">
															<input type="text" class="form-control" id="inputFaxNo" placeholder="Fax No" name="faxno">
															<span class="help-block">(999) 999-9999 </span>
														</div>
													</div>
													<div class="form-group">
														<label for="inputemailaddress" class="col-md-4 control-label">Email Address</label>
														<div class="col-md-8">
															<input type="email" class="form-control" id="inputEmailAddress" placeholder="Email Address" name="emailaddress">
														</div>
													</div>
												</div>
											
										</div>
									</div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-success" id="btnAdd"><i class="fa fa-save"></i> Save</button>
										<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
									</div>
								</div>
							</form>
						</div>
					</div>

					<!-- Edit Modal -->
					<div id="editModal" class="modal fade" role="dialog">
						<div class="modal-dialog modal-lg">

							<!-- Modal content-->
							<form class="form-horizontal" role="form" method="POST" action="{{ url('/update-supplier') }}">
								<div class="modal-content blue">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">ADD\EDIT SUPPLIER</h4>
									</div>
									<div class="modal-body">
										<div class="row">
											
												{!! csrf_field() !!}
												<div class="col-md-6">
													<div class="form-group">
														<label for="inputcode" class="col-md-4 control-label">*Code</label>
														<div class="col-md-8">
															<input type="text" id="edit_inputId" placeholder="Id" name="id" hidden="true">
															<input type="text" class="form-control" id="edit_inputCode" placeholder="Code" name="code" autofocus <?php echo($readonly); ?> >
														</div>
													</div>
													<div class="form-group">
														<label for="inputname" class="col-md-4 control-label">*Name</label>
														<div class="col-md-8">
															<input type="text" class="form-control" id="edit_inputName" placeholder="Name" name="name" <?php echo($readonly); ?> >
														</div>
													</div>
													<div class="form-group">
														<label for="inputaddress" class="col-md-4 control-label">Address</label>
														<div class="col-md-8">
															<textarea rows="5" cols="40" class="form-control" id="edit_inputAddress" placeholder="Address" name="address" style="resize: none;"> <?php echo($readonly); ?> ></textarea>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="inputtelno" class="col-md-4 control-label">Tel No</label>
														<div class="col-md-8">
															<input type="text" class="form-control" id="edit_inputTelNo" placeholder="Tel No" name="telno" <?php echo($readonly); ?> >
															<span class="help-block">(999) 999-9999 </span>
														</div>
													</div>
													<div class="form-group">
														<label for="inputfaxno" class="col-md-4 control-label">Fax No</label>
														<div class="col-md-8">
															<input type="text" class="form-control" id="edit_inputFaxNo" placeholder="Fax No" name="faxno" <?php echo($readonly); ?> >
															<span class="help-block">(999) 999-9999 </span>
														</div>
													</div>
													<div class="form-group">
														<label for="inputemailaddress" class="col-md-4 control-label">Email Address</label>
														<div class="col-md-8">
															<input type="email" class="form-control" id="edit_inputEmailAddress" placeholder="Email Address" name="emailaddress" <?php echo($readonly); ?> >
														</div>
													</div>
												</div>
											
										</div>
									</div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-success" id="btnUpdate" <?php echo($state); ?> ><i class="fa fa-save"></i> Save</button>
										<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div id="deleteModal" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm blue">
							<form role="form" method="POST" action="{{ url('/delete-supplier') }}">
								<div class="modal-content ">
									<div class="modal-body">
										<p>Are you sure you want to delete the selected supplier?</p>
										{!! csrf_field() !!}
										<input type="hidden" name="id" id="delete_inputId"/>
									</div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-primary" id="delete">Delete</button>
										<button type="button" data-dismiss="modal" class="btn">Cancel</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div id="incorrectSelection" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm blue">
							<div class="modal-content ">
								<div class="modal-body">
									<p></p>
									{!! csrf_field() !!}
								</div>
								<div class="modal-footer">
									<button type="button" data-dismiss="modal" class="btn">OK</button>
								</div>
							</div>
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
		var url = "{{ url('/edit-supplier') }}";
	</script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/supplier.js') }}" type="text/javascript"></script>
@endpush