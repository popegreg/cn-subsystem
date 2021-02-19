<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: mrpCalculation.blade.php
     MODULE NAME:  [3007] MRP CALCULATION
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2016.05.17
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.17     AK.DELAROSA     Initial Draft
     100-00-02   1     2016.05.24     MESPINOSA       Continue the development.
     100-00-03   1     2016.10.12     AKDELAROSA      Debug whole module
     200-00-00   1     2016.11.22     AKDELAROSA      Recode Module
     200-00-01   1     2017.02.10     MESPINOSA       Update implementation based 
                                                        on the new requirements.
*******************************************************************************/
?>
@extends('layouts.master')

@section('title')
	MRP Calculation | Pricon Microelectronics, Inc.
@endsection


@section('content')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_MRP'))
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


				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-calculator"></i> MRP CALCULATION
						</div>
					</div>
					<div class="portlet-body">

						<form method="POST" action="{{url('/mrpreadfiles')}}" class="form-horizontal form-bordered" enctype="multipart/form-data" onsubmit="showLoading()">
							<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-4">Parts Answer Data(ZYPF0150):</label>
									<div class="col-md-5">
										<input type="file" class="filestyle" data-buttonName="btn-primary" name="partsdata">
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-md-4">PPS Answer Data:</label>
									<div class="col-md-5">
										<input type="file" class="filestyle" data-buttonName="btn-primary" name="ppsdata">
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-md-4">Invoice Data</label>
									<div class="col-md-5">
										<input type="file" class="filestyle" data-buttonName="btn-primary" name="invoicedata">
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-md-4 ">MRP Data Extract To:</label>
									<div class="col-md-5">
										<input type="text" class="form-control input-md" value="/var/www/html/pmi-subsystem/public/MRP_data_files_SSS/" disabled="disable"/>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-4"></div>
									<div class="col-md-5">
										<p>1. Set ZYPF0150 data.</p>
										<p>2. Set PPS answer Data and YEC Invoice Data (Optional)</p>
										<p>3. Click UPDATE.</p>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-4"></div>
									<div class="col-md-5">
										<p>* Invoice data is should be imported before calculation if invoice data is not loaded to YPICS.</p>
										<p>* System is asking about data loading of PPS data and Invoice data during calculation</p>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-4"></div>
									<div class="col-md-5">
										<button type="submit" class="btn btn-success pull-right" <?php echo($state); ?> >
											<i class="fa fa-edit"></i> Update
										</button>
									</div>
								</div>

							</div>
						</form>
						
					</div>
				</div>
				
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

	<!-- AJAX LOADER -->
	<div id="loading" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<img src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/images/ajax-loader.gif') }}" class="img-responsive">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- MSG -->
	 <div id="msg" class="modal fade" role="dialog" data-backdrop="static">
	      <div class="modal-dialog modal-sm gray-gallery">
	           <div class="modal-content ">
	                <div class="modal-header">
	                     <h4 id="title" class="modal-title"></h4>
	                </div>
	                <div class="modal-body">
	                     <p id="err_msg"></p>
	                     <p>Please download the MRP file.</p>
	                </div>
	                <div class="modal-footer">
	                	<a href="{{ url('/mrpexcel') }}" class="btn green">Download MRP File</a>
	                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
	                </div>
	           </div>
	      </div>
	 </div>
	
@endsection

@push('script')
	<script type="text/javascript">
		function showLoading()
		{
			$('#loading').modal('show');
		}
	</script>

	@if (Session::has('msg'))
		<script type="text/javascript">
			var title = "{{ Session::get('msg_type') }}";
			var msg = "{{ Session::get('msg') }}";

			$('#loading').modal('hide');

			$('#title').html(title+'!');
			$('#err_msg').html(msg);
			$('#msg').modal('show');
		</script>
	@endif
@endpush