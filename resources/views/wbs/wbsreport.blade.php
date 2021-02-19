@extends('layouts.master')

@section('title')
	WBS | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_WBS'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
				@include('includes.message-block')
				<div class="portlet box blue" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-navicon"></i>  WBS Reports
						</div>
					</div>
					<div class="portlet-body">
                        <div class="row">
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="padding-bottom: 10px">
								<a class="more showmodal" href="javascript:;" data-sub="MAT_KIT">
									<div class="dashboard-stat yellow-gold">
										<div class="visual">
											<i class="fa fa-clipboard"></i>
										</div>
										<div class="details">
											<div class="number">
												Material Kitting & Issuance
											</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<a class="more showmodal" href="javascript:;" data-sub="SAK_ISS">
									<div class="dashboard-stat yellow-casablanca">
										<div class="visual">
											<i class="glyphicon glyphicon-paste"></i>
										</div>
										<div class="details">
											<div class="number">
												Sakidashi Issuance
											</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<a class="more showmodal" href="javascript:;" data-sub="PHY_IN">
									<div class="dashboard-stat green">
										<div class="visual">
											<i class="glyphicon glyphicon-list-alt"></i>
										</div>
										<div class="details">
											<div class="number">
												Physical Inventory
											</div>
										</div>
									</div>
								</a>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<a class="more showmodal" href="javascript:;" data-sub="WMI">
									<div class="dashboard-stat red-flamingo">
										<div class="visual">
											<i class="glyphicon glyphicon glyphicon-th-large"></i>
										</div>
										<div class="details">
											<div class="number">
												Material Issuance Request
											</div>
										</div>
									</div>
								</a>
							</div>

						</div>

						<hr>

						<div class="row">
							@if(Auth::user()->user_id == 'kurt_cn')
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<a class="more showmodal" href="javascript:;" data-sub="WBS_INV">
										<div class="dashboard-stat grey-gallery">
											<div class="visual">
												<i class="glyphicon glyphicon glyphicon-th-large"></i>
											</div>
											<div class="details">
												<div class="number">
													WBS Inventory
												</div>
											</div>
										</div>
									</a>
								</div>
							@endif

							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<a class="more showmodal" href="javascript:;" data-sub="Fifo">
									<div class="dashboard-stat green">
										<div class="visual">
											<i class="glyphicon glyphicon glyphicon-save-file"></i>
										</div>
										<div class="details">
											<div class="number">
												Fifo Reason Logs
											</div>
										</div>
									</div>
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


	<!-- Material Kitting Modal -->
	<div id="matkitModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content blue">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Search</h4>
				</div>
				<div class="modal-body">
					<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group">
                                <label for="mkl_from" class="col-md-3 control-label">Issuance Date</label>
                                <div class="col-md-7">
                                    <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control input-sm reset" name="mkl_from" id="mkl_from"/>
                                        <span class="input-group-addon">to </span>
                                        <input type="text" class="form-control input-sm reset" name="mkl_to" id="mkl_to"/>
                                    </div>
                                </div>
                            </div>
							<div class="form-group">
								<label for="mkl_pono" class="col-md-3 control-label">Po No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="mkl_pono" placeholder="Po No" name="mkl_pono" autofocus <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label for="mkl_issno" class="col-md-3 control-label">Issuance No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="mkl_issno" placeholder="Issuance No" name="mkl_issno" <?php echo($readonly); ?> />
								</div>
							</div>

							<div class="form-group">
								<label for="mkl_device" class="col-md-3 control-label">Device Code</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="mkl_device" placeholder="Device Code" name="mkl_device" <?php echo($readonly); ?> />
								</div>
							</div>

							<div class="form-group">
								<label for="mkl_item" class="col-md-3 control-label">Part Code</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="mkl_item" placeholder="Part Code" name="mkl_item" <?php echo($readonly); ?> />
								</div>
							</div>
						</form>
						
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn blue-madison mkl_reports" data-mode="summary"><i class="fa fa-file-excel-o"></i> Material Kitting Summary List</button>
					<button type="button" class="btn green mkl_reports" data-mode="dispatch"><i class="fa fa-file-excel-o"></i> Generate Dispatch</button>
					<button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Sakidashi Modal -->
	<div id="sakidashiModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content blue">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Search</h4>
				</div>
				<div class="modal-body">
					<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group">
                                <label for="saki_from" class="col-md-3 control-label">Issuance Date</label>
                                <div class="col-md-7">
                                    <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control input-sm reset" name="saki_from" id="saki_from"/>
                                        <span class="input-group-addon">to </span>
                                        <input type="text" class="form-control input-sm reset" name="saki_to" id="saki_to"/>
                                    </div>
                                </div>
                            </div>
							<div class="form-group">
								<label for="saki_pono" class="col-md-3 control-label">Po No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="saki_pono" placeholder="Po No" name="saki_pono" autofocus <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label for="saki_issno" class="col-md-3 control-label">Issuance No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="saki_issno" placeholder="Issuance No" name="saki_issno" <?php echo($readonly); ?> />
								</div>
							</div>

							<div class="form-group">
								<label for="saki_device" class="col-md-3 control-label">Device Code</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="saki_device" placeholder="Device Code" name="saki_device" <?php echo($readonly); ?> />
								</div>
							</div>

							<div class="form-group">
								<label for="saki_item" class="col-md-3 control-label">Part Code</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="saki_item" placeholder="Part Code" name="saki_item" <?php echo($readonly); ?> />
								</div>
							</div>
						</form>
						
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn blue-madison saki_reports" data-mode="summary"><i class="fa fa-file-excel-o"></i> Sakidashi Issuance Summary</button>
					<button type="button" class="btn green saki_reports" data-mode="dispatch"><i class="fa fa-file-excel-o"></i> Generate Dispatch</button>
					<button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
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
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div id="msg_modal" class="modal fade" role="dialog" data-backdrop="static">
	    <div class="modal-dialog modal-sm gray-gallery">
	        <div class="modal-content ">
	            <div class="modal-header">
	                <h4 id="msg_status" class="modal-title"></h4>
	            </div>
	            <div class="modal-body">
	                <p id="msg_content"></p>
	            </div>
	            <div class="modal-footer">
	                <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
	            </div>
	        </div>
	    </div>
	</div>

	<div id="wmimsg" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-header">
					<h4 id="wmititle" class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<p id="wmi_err_msg"></p>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-danger" id="wmi_exportToExcel_close">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div id="physicalModal" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Search</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal">
						<div class="form-group">
	            			<label for="mkl_from" class="col-md-3 control-label">Date</label>
	                        <div class="col-md-7">
	                            <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
	                                <input type="text" class="form-control input-sm reset" name="phy_from" id="phy_from"/>
	                                <span class="input-group-addon">to </span>
	                                <input type="text" class="form-control input-sm reset" name="phy_to" id="phy_to"/>
	                            </div>
	                        </div>
	            		</div>
	            		<div class="form-group">
	            			<label for="" class="control-label col-sm-3">Location</label>
	            			<div class="col-sm-8">
	            				<input type="text" class="form-control input-sm" id="phy_location" name="phy_location">
	            			</div>
	            		</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" id="phy_exportToExcel" class="btn green"><i class="fa fa-file-excel-o"></i> Generate Excel</button>
					<button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</div>
		</div>
	</div>

	<div id="wmiModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content blue">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Search</h4>
				</div>
				<div class="modal-body">
					<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group">
                                <label for="saki_from" class="col-md-3 control-label">Date</label>
                                <div class="col-md-7">
                                    <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control input-sm reset" name="wmi_from" id="wmi_from"/>
                                        <span class="input-group-addon">to </span>
                                        <input type="text" class="form-control input-sm reset" name="wmi_to" id="wmi_to"/>
                                    </div>
                                </div>
                            </div>
							<div class="form-group">
								<label for="saki_pono" class="col-md-3 control-label">Po No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="wmi_pono" placeholder="Po No" name="wmi_pono" autofocus <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label for="saki_issno" class="col-md-3 control-label">Control No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="wmi_issno" placeholder="Issuance No" name="wmi_issno" <?php echo($readonly); ?> />
								</div>
							</div>

							<div class="form-group">
								<label for="saki_item" class="col-md-3 control-label">Part Code</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="wmi_item" placeholder="Part Code" name="wmi_item" <?php echo($readonly); ?> />
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">
					<!-- <button type="button" id="wmi_export_to_pdf" class="btn blue-madison" data-mode="pdf"><i class="fa fa-file-pdf-o"></i> Generate PDF</button> -->
					<button type="button" id="" class="btn green pmr_wmi_report" data-mode="pmr"><i class="fa fa-file-excel-o"></i> Production Request</button>

					<button type="button" id="" class="btn blue pmr_wmi_report" data-mode="wmi"><i class="fa fa-file-excel-o"></i> Warehouse Issuance</button>

					<button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</div>
		</div>
	</div>

<!-- Fifo Reason Logs -->
	<div id="fiforeasonlogs" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content blue">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Search</h4>
				</div>
				<div class="modal-body">
					<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group">
                                <label class="col-md-3 control-label">Issuance Date</label>
                                <div class="col-md-7">
                                    <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control input-sm reset" name="issueance_from" id="issueance_from"/>
                                        <span class="input-group-addon">to </span>
                                        <input type="text" class="form-control input-sm reset" name="issuance_to" id="issuance_to"/>
                                    </div>
                                </div>
                            </div>
						</form>
						
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn green" id="btn_fifo_logs" data-mode="dispatch"><i class="fa fa-file-excel-o"></i> Generate Fifo Logs</button>
					<button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</div>
		</div>
	</div>

	@include('includes.modals')
@endsection

@push('script')
	<script type="text/javascript">
		$('.showmodal').on('click', function() {
			var sub = $(this).attr('data-sub');
			if (sub == 'MAT_RCV') {}
			if (sub == 'IQC') {}
			if (sub == 'MAT_KIT') {
				$('#matkitModal').modal('show');
			}
			if (sub == 'SAK_ISS') {
				$('#sakidashiModal').modal('show');
			}
			if (sub == 'PHY_IN'){
				$('#physicalModal').modal('show');
			}
			if(sub == 'WMI'){
				$('#wmiModal').modal('show');
			}
			if (sub == 'WBS_INV') {
				window.location.href = '{{url("/wbsreportwbsinventory")}}'
			}
			if (sub == 'Fifo') {
				$('#fiforeasonlogs').modal('show');
			}
			if (sub == 'PART_REC') {}
			if (sub == 'PRD_MATREQ') {}
			if (sub == 'WHS_MATISS') {}
		});

		$('.mkl_reports').on('click', function() {
			var mode = $(this).attr('data-mode');
			var mkl_from = $('#mkl_from').val();
			var mkl_to = $('#mkl_to').val();
			var mkl_pono = $('#mkl_pono').val();
			var mkl_issno = $('#mkl_issno').val();
			var mkl_device = $('#mkl_device').val();
			var mkl_item = $('#mkl_item').val();
			var url = '{{url("/wbsreportmklreport")}}';

			if (mkl_from == '' || mkl_to == '') {
				failedMsg("Please fill out the dates.")
			} else {
				if (mode == 'dispatch') {
					mkl_saki_report(url,'dispatch',mkl_from,mkl_to,mkl_pono,mkl_issno,mkl_device,mkl_item);
				} else {
					mkl_saki_report(url,'Summary',mkl_from,mkl_to,mkl_pono,mkl_issno,mkl_device,mkl_item);
				}
			}
			
		});

		$('.saki_reports').on('click', function() {
			var mode = $(this).attr('data-mode');
			var saki_from = $('#saki_from').val();
			var saki_to = $('#saki_to').val();
			var saki_pono = $('#saki_pono').val();
			var saki_issno = $('#saki_issno').val();
			var saki_device = $('#saki_device').val();
			var saki_item = $('#saki_item').val();
			var url = '{{url("/wbsreportsakireport")}}';

			if (saki_from == '' || saki_to == '') {
				failedMsg("Please fill out the dates.")
			} else {
				if (mode == 'dispatch') {
					mkl_saki_report(url,'dispatch',saki_from,saki_to,saki_pono,saki_issno,saki_device,saki_item);
				} else {
					mkl_saki_report(url,'summary',saki_from,saki_to,saki_pono,saki_issno,saki_device,saki_item);
				}
			}
			
		});

		$('#phy_exportToExcel').on('click',function(){
			var datefrom = $('#phy_from').val();
			var dateto = $('#phy_to').val();
			var location = $('#phy_location').val();

			if(datefrom == '' || dateto == ''){
				$('#physicalModal').modal('hide');
				$('#msg').modal('show');
				$('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
				$('#err_msg').html("Date from and date to is required!");	
			}else{
				var url = "{{ url('/wbsreportphyreport?datefrom=')  }}" + datefrom + "&dateto=" + dateto+ "&location=" + location;
				window.location.href = url;	
			}	
		});

		$('#phy_exportToExcel_close').on('click',function(){
			$('#physicalModal').modal('show');
			$('#msg').modal('hide');
		});

		$('.pmr_wmi_report').on('click',function(){
			$('#loading').modal('show');
			var datefrom = $('#wmi_from').val();
			var dateto = $('#wmi_to').val();
			var pono = $('#wmi_pono').val();
			var issno = $('#wmi_issno').val();
			var item = $('#wmi_item').val();
			var mode = $(this).attr('data-mode');
			
			if (mode == 'wmi') {
				var url = "{{ url('/wbsreportwmireport?datefrom=')  }}" + datefrom + "&dateto=" + dateto+ "&pono=" + pono+ "&ctrlno=" + issno+ "&item=" + item+ "&mode="+mode;
			} else {
				var url = "{{ url('/wbsreportpmrreport?datefrom=')  }}" + datefrom + "&dateto=" + dateto+ "&pono=" + pono+ "&ctrlno=" + issno+ "&item=" + item+ "&mode="+mode;
			}
			
			window.location.href = url;

			$('#loading').modal('hide');
		});

		$('#wmi_exportToExcel_close').on('click',function(){
			$('#wmiModal').modal('show');
			$('#wmimsg').modal('hide');
		});

		$('#btn_fifo_logs').on('click',function(){
	    var datefrom = $('#issueance_from').val();
		var dateto  =   $('#issuance_to').val();
	
			if(datefrom == "" || dateto == "" ){
				fiforeportfailed('Date range required!');
				$('#fiforeasonlogs').modal('hide');
		 	 // failedMsg('Date range required!','failed');
			}else{
				 DownloadFifoReasonlog();
			}
		})

	
		function mkl_saki_report(route,mode,from,to,pono,issno,device,item) {
			var token = "{{ Session::token() }}";
            var ufrom = '';
            var uto = '';
            var upono = '';
            var uissno = '';
            var udevice = '';
            var uitem = '';
            if (from != '') {
            	ufrom = '&&from='+from;
            }
            if (to != '') {
            	uto = '&&to='+to;
            }
            if (pono != '') {
            	upono = '&&pono='+pono;
            }
            if (issno != '') {
            	uissno = '&&issno='+issno;
            }
            if (device != '') {
            	udevice = '&&device='+device;
            }
            if (item != '') {
            	uitem = '&&item='+item;
            }
	        var url = route+'?_token='+token+'&&mode='+mode+ufrom+uto+upono+uissno+udevice+uitem;

	        window.location.href= url;
		}

		function successMsg(msg) {
			$('#msg').modal('show');
			$('#title').html('<strong><i class="fa fa-check"></i></strong> Success!')
			$('#err_msg').html(msg);
		}

		function failedMsg(msg) {
			$('#msg').modal('show');
			$('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
			$('#err_msg').html(msg);
		}

		function fiforeportfailed(msg_modal) {
			$('#msg_modal').modal('show');
			$('#msg_status').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
			$('#msg_content').html(msg_modal);
		}

		function DownloadFifoReasonlog(){
			var url = '{{url("/wbsreportcheckdata")}}';
			var datefrom = $('#issueance_from').val();
			var dateto  =  $('#issuance_to').val();
			$.ajax({
					url:url,
					type: 'GET',
					dataType: 'JSON',
					data: {
						datefrom: datefrom,
						dateto:dateto
					},
					success:function(returnData){
						console.log(returnData.length);
						if (returnData.length > 0) {
							 window.location.href = "{{ url('/wbsfiforeport?datefrom=') }}" +datefrom+ "&dateto="+dateto;
							// alert('success');
						}else{
							fiforeportfailed('No data found!');
							$('#fiforeasonlogs').modal('hide');
						}
					}													
			}).fail(function(jqXHR, textStatus, errorThrown) {
					console.log("error");
				})	;				
		}
	</script>
@endpush
