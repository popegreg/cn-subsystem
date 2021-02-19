@extends('layouts.master')

@section('title')
	SSS Answer Input Management | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php ini_set('max_input_vars', 999999);?>
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_SSS'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i> Scheduling Support System (Answer Input Management)
						</div>
					</div>
					<div class="portlet-body portlet-empty">
						<div class="row">
							<div class="col-md-12">
								<h3>ANSWER INPUT MANAGEMENT</h3>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<label class="col-md-3 control-label">Order Date: </label>
								<div class="col-md-9">
									<input class="form-control form-control-inline input-medium date-picker col-sm-4" type="text"/>
								</div>
							</div>
							<div class="col-md-6">
								<label class="col-md-3 control-label">Order Date: </label>
								<label class="col-md-4 control-label">
									<input type="radio" class="form-control" /> This Date Only
								</label>
								<label class="col-md-5 control-label">
									<input type="radio" class="form-control" /> Include the Date Before
								</label>
							</div>
						</div>

						<br>

						<div class="row">
							<div class="col-md-6">
								<div class="portlet box blue">
									<div class="portlet-title">
										<div class="caption">
											Exceptions
										</div>
									</div>
									<div class="portlet-body portlet-empty">
										<div class="row">
											<div class="col-md-12">
												<table class="table table-striped table-bordered table-hover" style="font-size: 11px" ><!--id="sample_3"-->
													<thead>
														<tr>
															<td colspan="2">
																Product Name
															</td>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td width="50px"></td>
															<td></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<p>Regulations of answer input</p>
								<p>Should be input within 3 working days even though still 23:00.</p>
								<p>Should be input within 4 working days for the PO which answered as 23:00</p>
							</div>

						</div>

						<br/>

						<div class="row">
							<div class="col-md-12">
								<table class="table table-striped table-bordered table-hover" style="font-size: 10px" ><!--id="sample_3"-->
									<thead>
										<tr>
											<th>
												ORDER DATE
											</th>
											<th>
												PO
											</th>
											<th>
												PCODE
											</th>
											<th>
												PNAME
											</th>
											<th>
												QTY
											</th>
											<th>
												R3ANSWER
											</th>
											<th>
												TIME
											</th>
											<th>
												REMARKS
											</th>
											<th>
												CUSTCODE
											</th>
											<th>
												CUSTOMER
											</th>
										</tr>
									</thead>

									<tbody id="table" >
										<tr>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
										</tr>
									</tbody>
								</table>
							</div>
						</div>


						<div class="row">
							<div class="col-md-12">
								<a href="#" id="print" class="btn blue btn-sm pull-right ">
									<i class="fa fa-print"></i> Output
								</a>
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
