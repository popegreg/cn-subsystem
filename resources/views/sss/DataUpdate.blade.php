@extends('layouts.master')

@section('title')
	SSS Data Update | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == "3008")
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
							<i class="fa fa-gift"></i> Scheduling Support System (Data Update)
						</div>
					</div>

					<div class="portlet-body">
						<div class="row">
							<div class="col-md-12">
								<form method="post" class="form-horizontal" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ url('/mrp_and_r3answer') }}" id="formReadfile">
									<div class="form-group">
										<input type="hidden" name="_token" value="{{Session::token()}}"></input>
										<label class="col-sm-4 control-label">Select MRP Data</label>
										<div class="col-sm-5">
											<input type="file" class="filestyle" data-buttonName="btn-primary" name="mrpdata" id="mrpdata" {{$readonly}} required>
											<span class="help-block">(TS_MRP_yy-mm-dd.xls)</span>
										</div>


										<label class="col-sm-4 control-label">Select R3 Answer Data</label>
										<div class="col-sm-5">
											<input type="file" class="filestyle" data-buttonName="btn-primary" name="r3answer" id="r3answer" {{$readonly}} required>
											<span class="help-block">(yymmdd_hhmm_TS_ZYPF0090.txt)</span>
											<button type="submit" class="btn yellow btn-sm pull-right"><i class="fa fa-edit"></i> Update</button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<hr/>
						<div class="row">
							<div class="col-md-12">
								<form method="post" class="form-horizontal" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ url('/partsanswer') }}" id="isogiform">
									<div class="form-group">
										{!! csrf_field() !!}
										<label class="col-sm-4 control-label">Select Parts Answer Data</label>
										<div class="col-sm-5">
											<input type="file" class="filestyle" data-buttonName="btn-primary" name="partsanswerfile" id="partsanswerfile" {{$readonly}} required>
											<span class="help-block">(yymmdd_hhmm_TS_ISOGI_ZYPF0120.txt)</span>
											<button onclick="showLoading()" type="submit" class="btn yellow btn-sm pull-right"><i class="fa fa-edit"></i> Update</button>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-4 control-label">File Date</label>
										<div class="col-sm-5">
											<input class="form-control form-control-inline input-medium " id = "dateFile" name = "dateFile" size="16" type="text" disabled />
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>


			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
	

	<div id="loading" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<img src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/images/ajax-loader.gif') }}" class="img-responsive">
						</div>
						<div class="col-sm-2"></div>
					</div>
				</div>
			</div>
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
@endsection

@push('script')
<script type="text/javascript">
    $(function() {
    	$('#formReadfile').on('submit', function(e){
			var formObj = $('#formReadfile');
			var formURL = formObj.attr("action");
			var formData = new FormData(this);
			var mrpdata = $("#mrpdata").val();
			var r3answer = $("#r3answer").val();
			var xls = mrpdata.split('.').pop();
			var txt = r3answer.split('.').pop();
			e.preventDefault(); //Prevent Default action.
			$('#loading').modal('show');
			if ($("#mrpdata").val() == '' || $("#r3answer").val() == '') {
				$('#loading').modal('hide');
				$('#msg').modal('show');
	             $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
	             $('#err_msg').html("Please select xls or txt files.");
			}
			if (txt != 'txt' || xls != 'xls') {
				$('#loading').modal('hide');
				$('#msg').modal('show');
	             $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
	             $('#err_msg').html("Please select valid files.");
			}
			if (mrpdata != '' || r3answer != ''){
				if (txt == 'txt' || xls == 'xls') {
					$.ajax({
						url: formURL,
						method: 'POST',
						data:  formData,
						dataType: 'json',
						mimeType:"multipart/form-data",
						contentType: false,
						cache: false,
						processData:false,
					}).done( function(data, textStatus, jqXHR) {
						$('#loading').modal('hide');
						$('#msg').modal('show');
			            $('#title').html('<strong><i class="fa fa-check"></i></strong> Success!')
			            $('#err_msg').html("Successfully Updated");
					}).fail(function(jqXHR, textStatus, errorThrown) {
						$.each(jqXHR, function(i,x) {
							console.log(x);
						})
						$('#loading').modal('hide');
						$('#msg').modal('show');
					    $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
					    $('#err_msg').html(jqXHR+";  "+textStatus+";   "+errorThrown);
					});
				}
			}
		});

		$('#isogiform').on('submit', function(e){
			var formObj = $('#isogiform');
			var formURL = formObj.attr("action");
			var formData = new FormData(this);
			var partsanswerfile = $("#partsanswerfile").val();
			var txt = partsanswerfile.split('.').pop();
			e.preventDefault(); //Prevent Default action.
			$('#loading').modal('show');
			if ($("#partsanswerfile").val() == '') {
				$('#loading').modal('hide');
				$('#msg').modal('show');
	             $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
	             $('#err_msg').html("Please select txt file.");
			}
			if (txt != 'txt') {
				$('#loading').modal('hide');
				$('#msg').modal('show');
	            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
	            $('#err_msg').html("Please select valid files.");
			}
			if (partsanswerfile != ''){
				if (txt == 'txt') {
					$.ajax({
						url: formURL,
						method: 'POST',
						data:  formData,
						dataType: 'json',
						mimeType:"multipart/form-data",
						contentType: false,
						cache: false,
						processData:false,
					}).done( function(data, textStatus, jqXHR) {
						$('#loading').modal('hide');
						$('#msg').modal('show');
			            $('#title').html('<strong><i class="fa fa-check"></i></strong> Success!')
			            $('#err_msg').html("Successfully Updated");
					}).fail(function(jqXHR, textStatus, errorThrown) {
						$.each(jqXHR, function(i,x) {
							console.log(x);
						})
						$('#loading').modal('hide');
						$('#msg').modal('show');
					    $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
					    $('#err_msg').html(jqXHR+";  "+textStatus+";   "+errorThrown);
					});
				}
			}
		});
	});
</script>
@endpush