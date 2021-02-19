@extends('layouts.master')

@section('title')
    Material List for Direct Ordering | Pricon Microelectronics, Inc.
@endsection

@section('content')

    <?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == "3006")  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach
	
	<div class="page-content">
		<!--<div class="col-md-3"></div>-->
		<div class="col-md-offset-2 col-md-8">
			@include('includes.message-block')
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>Material List For Direct Ordering
					</div>
				</div>


				<div class="portlet box blue-hoki">
					<div class="portlet-body">
						<div class="row">

							<div class="col-md-12">
								<form method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank" action="{{ url('/material_list_pdf') }}" >
								{!! csrf_field() !!}
									<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
									<div class="form-group">
										<label class="control-label col-sm-3">Order:</label>
										<div class="col-sm-7">
											<input type="file" class="filestyle" name="file1" data-buttonName="btn-primary" id="mlp01uf" {{$readonly}} required>
											<!-- <div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="input-group input-large">
													<div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
														<i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
														</span>
													</div>
													<span class="input-group-addon btn default btn-file">
														<span class="fileinput-new">
															Select file
														</span>
														<span class="fileinput-exists">
															Change
														</span>
														<input type="file" name="file1" id="mlp01uf" {{$readonly}} required>
													</span>
													<a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
													Remove </a>
												</div>
											</div> -->
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">BOM:</label>
										<div class="col-sm-7">
											<input type="file" class="filestyle" name="file2" data-buttonName="btn-primary" id="mlp02uf" {{$readonly}} required>
											<!-- <div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="input-group input-large">
													<div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
														<i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
														</span>
													</div>
													<span class="input-group-addon btn default btn-file">
													<span class="fileinput-new">
													Select file </span>
													<span class="fileinput-exists">
													Change </span>
													<input type="file" name="file2" id="mlp02uf" {{$readonly}} required>
													</span>
													<a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
													Remove </a>
												</div>
											</div> -->
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12 text-center">
											<button type="submit"  class="btn btn-success btn-sm" ><i class="fa fa-print"></i> Generate</button>
										</div>
									</div>
								</form>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<!--<div class="col-md-3"></div>-->
	</div>
@endsection