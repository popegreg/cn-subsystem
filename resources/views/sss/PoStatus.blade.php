<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PoStatus.blade.php
     MODULE NAME:  [3008-1] PO Status
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.05.03
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.03     MESPINOSA       Initial Draft
*******************************************************************************/
?>
@extends('layouts.master')

	<script type="text/javascript">

		function redirect(btn)
		{
			var str = $('#lbl_inputpo').text();
			var po = str.replace(/\s+/g, '');
			//PO Change Delivery
			if(btn=='DEL')
			{
			    window.location.href= "{{ url('/pochangedelivery?code=') }}" + $('#input_code_d').text() + '&po=' + po;
			}
			//PO Parts Status
			else if(btn == 'PSTAT')
			{
			    window.location.href= "{{ url('/popartsstatus?code=') }}" + $('#input_code_d').text() + '&po=' + po + '&haspart=1';
			}
			//isogiinput
			else if(btn == 'ISO')
			{
			    window.location.href= "{{ url('/poisogiinput?code=') }}" + $('#input_code_d').text();
			}
			//Export to PDF
			else if(btn == 'PDF')
			{
				window.open("{{ url('/po_printing?po=') }}" + po, '_blank');
			}
		};

		function redirect_val(btn, val)
		{
			var str = $('#lbl_inputpo').text();
			var po = str.replace(/\s+/g, '');
			// PO Change Delivery
			if(btn=='DEL')
			{
			    window.location.href= "{{ url('/pochangedelivery?code=') }}" + val;
			}
			// PO Parts Status
			else if(btn == 'PSTAT')
			{
			    window.location.href= "{{ url('/popartsstatus?code=') }}" + val + '&po=' + po+ '&haspart=0';
			}
			//isogiinput
			else if(btn == 'ISO')
			{
			    window.location.href= "{{ url('/poisogiinput?code=') }}" + val + '&po=' + po;
			}
		};

		function exportHtmlToPdf()
		{
			var doc = new jsPDF('landscape','pt');
			// var table = tableToJson($('#sample_3').get(0))
		    var specialElementHandlers = {
		        '#editor': function (element, renderer) {
		            return true;
		        }
		    };

		    doc.fromHTML($('#sample_pdf').html(), 15, 15, {
		        'width': 1200,
	                'elementHandlers': specialElementHandlers
		    });
		    doc.setFontSize(6);
		    doc.save('sample-file.pdf');
		}

		function hideOrShow(action)
		{
			$(".details").each(function()
			{
				var id = $(this).attr('name');
				if(action =='HIDE')
				{
					$(this).hide();
				}
				else
				{
					$(this).show();	
				}
			});
		};

		function show_hide_column(col_no, do_show) 
		{
		   var tbl = document.getElementById('sample_3');
		   var col = tbl.getElementsByTagName('col')[col_no];
		   if (col) {
		     col.style.visibility=do_show?"":"collapse";
		   }
		};

	</script>

@section('title')
SSS (PO Status) | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_SSS'))
			@if ($access->read_write == "2")
				<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
		<?php $urlparam = $access->program_code;?>
	@endforeach
	
	<div class="page-content" style="padding-top:5px">
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
				@include('includes.message-block')
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-line-chart"></i>  Scheduling Support System (PO Status)
						</div>
					</div>
					<div class="portlet-body" style="padding-top: 0px; padding-bottom: 0px;">
						<div class="row">
							<div class="col-md-4">
								<h4>PO STATUS CONFIRMATION</h4>
							</div>
							<div class="col-md-8">
								<br/>
								<button type="button" onclick="javascript: redirect('DEL'); " id="btn_chgdelivery" class="btn btn-warning pull-right btn-xs"><i class="fa fa-truck"></i> CHANGE DELIVERY </button>
								<button type="button" onclick="javascript: redirect('PSTAT'); " class="btn btn-success pull-right btn-xs"><i class="fa fa-certificate"></i> PART STATUS </button>
								<button type="submit" formtarget="_blank" onclick="javascript: redirect('PDF'); " class="btn btn-info pull-right btn-xs" <?php echo($state); ?> ><i class="fa fa-file-pdf-o"></i> PRINT </button>
								<button type="button" data-toggle="modal" data-target="#myModal" class="btn btn-primary pull-right btn-xs" <?php echo($state); ?> ><i class="fa fa-file-archive-o"></i> EXTRACT </button>
								<input type="hidden" class="form-control" id="hdnPo" placeholder="PO" name="code" autofocus value="PO">
							</div>
						</div>
						<br/>
						<div class="row">
							<div class="col-md-12" >
								<form class="form-horizontal" role="form" method="POST" action="{{ url('/postatus') }}">
									{!! csrf_field() !!}
									<h4>SEARCH</h4>
									<div class="row">
										<div class="col-md-12" >
											<div class="form-group">
												<div class="col-md-1">
													<label for="input_po" class="control-label">PO</label>
												</div>
												<div class="col-md-4">
													<input type="text" class="form-control input-sm" id="inputPo" placeholder="PO" name="po" autofocus value="<?php if(isset($po_s)){ if($po_s == 'X') { echo '';} else{ echo $po_s; }} ?>" >
												</div>
												<div class="col-md-1">
													<label for="input_po_name" class="control-label">Product Name</label>
												</div>
												<div class="col-md-4">
													<input type="text" class="form-control input-sm" id="inputPoName" placeholder="Product Name" name="name" value="<?php if(isset($name)){ if($name == 'X') { echo '';} else{ echo $name; }} ?>" >
												</div>
												<div class="col-md-2">
													<button type="submit" class="btn btn-info btn-xs"><i class="fa fa-search"></i> SEARCH </button>
												</div>
											</div>
										</div>
									</div>

									{{-- <div class="portlet box blue" id="report_body" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px;">
										<div class="portlet-body" style="padding-top: 0px; padding-bottom: 0px;"> --}}
									




									<div class="row">
										<div class="col-md-6" >
											<div class="col-md-12" >
												<div class="col-md-2" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">PO :</span></h5>
												</div>
												<div class="col-md-5" style="padding-top: 0px; padding-bottom: 0px;">
													<b><label for="input_po" id="lbl_inputpo" class="control-label" style="font-size: 12px; font-weight: bold;"> 
													@foreach ($po as $value)
														{{ $value->PO }}
													@endforeach
													 </label></b>
												</div>
												<div class="col-md-2" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">PO DATE :</span></h5>
												</div>
												<div class="col-md-3" style="padding-top: 0px; padding-bottom: 0px;">
													<label for="input_podate" class="control-label" id="po_date"> 
													<?php if(isset($value)){echo $value->PODate; } ?>
													</label>
												</div>
											</div>
											<div class="col-md-12">
												<div class="col-md-2" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">CODE :</span></h5>
												</div>
												<div class="col-md-5" style="padding-top: 0px; padding-bottom: 0px;">
													<label for="input_code" id="input_code_d" class="control-label"> 
													<?php if(isset($value)){echo $value->Code; } ?>
													</label>
												</div>
												<div class="col-md-2" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">DEMAND :</span></h5>
												</div>
												<div class="col-md-3" style="padding-top: 0px; padding-bottom: 0px;">
													<label for="input_demand" class="control-label" id="demand"> 
													<?php if(isset($value)){echo $value->Demand; } ?>
													</label>
												</div>
											</div>
											<div class="col-md-12" >
												<div class="col-md-2" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">NAME :</span></h5>
												</div>
												<div class="col-md-5" style="padding-top: 0px; padding-bottom: 0px;">
													<label for="input_name" class="control-label pull-left" style="font-size: 12px; font-weight: bold; text-align: left;">
														<?php if(isset($value)){echo $value->Name; } ?>
													</label>
												</div>
												<div class="col-md-2" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">UPDATED BY :</span></h5>
												</div>
												<div class="col-md-3" style="padding-top: 0px; padding-bottom: 0px;">
													<label for="input_demand" class="control-label"> 
														<?php if(isset($value)){echo $value->UpdatedBy; } ?>
													</label>
												</div>
											</div>
											<div class="col-md-12" >
												<div class="col-md-2" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">CUSTOMER :</span></h5>
												</div>
												<div class="col-md-10" style="padding-top: 0px; padding-bottom: 0px;">
													<label for="input_customer" class="control-label">
														<?php if(isset($value)){echo $value->Customer; } ?>
													</label>
												</div>
											</div>
											<div class="col-md-12" >
												<div class="col-md-2" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">QUANTITY :</span></h5>
												</div>
												<div class="col-md-10" style="padding-top: 0px; padding-bottom: 0px;">
													<label for="input_qty" class="control-label"> 
													<?php if(isset($value)){echo $value->Qty; } ?>
													</label>
												</div>
											</div>
											<div class="col-md-12" >
												<div class="col-md-3" style="padding-top: 0px; padding-bottom: 0px;">
													<h5><span class="label label-info">MEMO/REMINDERS :</span></h5>
												</div>
												<div class="col-md-9" style="padding-top: 0px; padding-bottom: 0px;">
													<textarea rows="2" cols="40" class="form-control" id="edit_memo" placeholder="Memo / Reminders" name="memo" style="resize: none; padding-bottom: 0px;padding-top:0px" <?php echo($readonly); ?> ><?php if(isset($value)){ if(!empty($value->Remarks)){ echo trim($value->Remarks); }} ?></textarea>
												</div>
											</div>
											<div class="col-md-12" >
												<div class="col-md-12" style="padding-top: 5px; padding-bottom: 5px;">
													<button type="button" onclick="javascript:hideOrShow('SHOW');" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> SHOW DETAILS </button>
													<button type="button" onclick="javascript:hideOrShow('HIDE');" class="btn btn-warning btn-xs"><i class="fa fa-eye-slash"></i> HIDE DETAILS </button>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<h4>R3 ANSWER</h4>
											<div class="table-responsive scroller" style="height: 175px">
												<table class="table table-striped table-bordered table-hover" style="font-size: 9px;">
													<thead>
														<tr>
															<td>
																<b>PO</b>
															</td>
															<td>
																<b>R3Answer</b>
															</td>
															<td>
																<b>Time</b>
															</td>
															<td>
																<b>Qty</b>
															</td>
															<td>
																<b>Remarks</b>
															</td>
														</tr>
													</thead>
													<tbody id="tbl_body_r3answer">
														{{-- @foreach($answers as $answer)
														<tr class="odd gradeX" id="salesorderdata" name="salesorderdata">
															<td>
																{{ $answer->po}}
															</td>
															<td>
																{{ $answer->r3answer }}
															</td>
															<td style="text-align: right;">
																{{ $answer->time }}
															</td>
															<td style="text-align: right;">
																{{ $answer->qty }}
															</td>
															<td>
																{{ $answer->re }}
															</td>
														</tr>
														@endforeach --}}
													</tbody>
												</table>
											</div>
										</div>
									</div>

									<br>
									<div class="row">
										<div class="col-md-12">
											<div class="portlet box blue">
												<div class="portlet-title" style="min-height: 20px;">
													<div class="caption" style="padding-top: 5px; padding-bottom: 5px;">
														DETAILS &nbsp;&nbsp;&nbsp;
														<span class="label label-sm label-info"> FROM STOCK </span> &nbsp;&nbsp;&nbsp;
														<span class="label label-sm label-danger">ALLOCATION </span>
													</div>
													<div class="tools">
														<a href="javascript:;" class="collapse">
														</a>
													</div>
												</div>
												<div class="portlet-body">
													<div class="row">
														<div class="col-md-12 table-responsive" style="height: 300px" id="scroll_data">

															<table class="table table-striped table-bordered table-hover" style="font-size: 9px;"><!--id="sample_3"-->
																<thead>
																	<tr>
																		<td>
																			<b>CODE</b>
																		</td>
																		<td style="min-width: 250px">
																			<b>NAME</b>
																		</td>
																		<td>
																			<b>VI</b>
																		</td>
																		<td>
																			<b>PO Req</b>
																		</td>
																		<td>
																			<b>PO Balance</b>
																		</td>
																		<td>
																			<b>Gross Req</b>
																		</td>
																		<td class="details">
																			<b>ASSY100</b>
																		</td>
																		<td class="details">
																			<b>WHS100</b>
																		</td>
																		<td class="details">
																			<b>WHS102</b>
																		</td>
																		<td>
																			<b>TOTAL</b>
																		</td>
																		<td>
																			<b>INV RESR</b>
																		</td>
																		<td>
																			<b>PR BAL</b>
																		</td>
																		<td>
																			<b>MRP</b>
																		</td>
																		<td>
																			<b>PR_ISSUED</b>
																		</td>
																		<td>
																			<b>PR</b>
																		</td>
																		<td>
																			<b>PICK UP (GR)</b>
																		</td>
																		<td>
																			<b>YECPO</b>
																		</td>
																		<td>
																			<b>YEC P/U</b>
																		</td>
																		<td>
																			<b>P/U QTY</b>
																		</td>
																		<td>
																			<b>CHECK</b>
																		</td>
																		<td>
																			<b>DELIACCUM</b>
																		</td>
																		<td>
																			<b>IN CHARGE</b>
																		</td>
																		<td>
																			<b>RE</b>
																		</td>
																		<td>
																			<b>STATUS</b>
																		</td>
																		<td>
																			<b>ALLOCATIONCALC</b>
																		</td>
																	</tr>
																</thead>
																<tbody id="tbl_body_details_data">
																	{{-- @foreach($details_data as $detail)
																	<tr class="odd gradeX">
																		<td>
																			<!-- <a onclick="javascript: redirect_val('PSTAT', '{{ $detail->KCODE }}'); "> {{ $detail->KCODE }} </a> -->
																			<a onclick="javascript: redirect_val('ISO', '{{ urlencode($detail->PNAME) }}'); "> {{ $detail->KCODE }} </a>
																		</td>
																		<td>
																			<!-- <a onclick="javascript: redirect_val('ISO', '{{ urlencode($detail->PNAME) }}'); "> {{ $detail->PNAME }} </a> -->
																			<a onclick="javascript: redirect_val('PSTAT', '{{ $detail->KCODE }}'); "> {{ $detail->PNAME }} </a>
																		</td>
																		<td>
																			{{ $detail->VI }}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->PoReq }}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->PoBalance }}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->GrossReq }}
																		</td>
																		<td class="details" style="text-align: right">
																			{{ $detail->ASSY100 }}
																		</td>
																		<td class="details" style="text-align: right">
																			{{ $detail->WHS100}}
																		</td>
																		<td class="details" style="text-align: right">
																			{{ $detail->WHS102}}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->TOTAL }}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->InvResr }}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->PrBal }}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->MRP }}
																		</td>
																		<td>
																			{{ $detail->PR_Issued }}
																		</td>
																		<td>
																			{{ $detail->PR }}
																		</td>
																		<td>
																			{{ $detail->PickUpGR }}
																		</td>
																		<td>
																			{{ $detail->YecPo }}
																		</td>
																		<td>
																			{{ $detail->YecPu }}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->PuQty }}
																		</td>
																			@if ($detail->Check == 'FromStock')
																		<td>
																				<span class="label label-sm label-info">
																				FROM STOCK </span>
																		</td>
																			@elseif ($detail->Check == 'Allocation')
																		<td>
																				<span class="label label-sm label-danger">
																				ALLOCATION </span>
																		</td>
																			@else 
																		<td style="text-align: right">
																				{{ $detail->Check }}
																		</td>
																			@endif
																		<td style="text-align: right">
																			{{ $detail->Deliaccum }}
																		</td>
																		<td>
																			{{ $detail->Incharge }}
																		</td>
																		<td>
																			{{ $detail->RE }}
																		</td>
																		<td>
																			{{ $detail->Status }}
																		</td>
																		<td style="text-align: right">
																			{{ $detail->AllocationCalc }}
																		</td>
																	</tr>
																	@endforeach --}}
																</tbody>
															</table>

														</div>
													</div>
														
												</div>
											</div>
										</div>													
									</div>
										{{-- </div>
									</div> --}}

									<div id="editor"></div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>

	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">

			<!-- Modal content-->
			<form class="form-horizontal" role="form" method="POST" action="{{ url('/po_printing') }}" target="_blank">
				<div class="modal-content blue" style="width: 500px">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">PO PRINTING</h4>
					</div>
					<div class="modal-body">
						<div class="row">
								{!! csrf_field() !!}
								<div class="col-md-12">
									<div class="form-group">
										<div class="col-md-3">
											<h4><span class="label label-info">PO Date</span></h4>
										</div>
										<div class="col-md-9">
											<div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy">
												<input type="text" class="form-control" name="from">
												<span class="input-group-addon">
												to </span>
												<input type="text" class="form-control" name="to">
											</div>
											<span class="help-block"> Select date range </span>
										</div>
									</div>
								</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-info" id="btn_print" formtarget="_blank"><i class="fa fa-print"></i> Print</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
						<input type="hidden" class="form-control" id="hdnPo" name="po" autofocus value="<?php if(isset($value)){echo $value->PO; } ?>">
					</div>
				</div>
			</form>
		</div>
	</div>

@endsection

@push('script')
	<script type="text/javascript">
		var row = 10;
		getPOstatusData(row);
		
		$(function(){
			$('#scroll_data').scroll(function() {
                if($(this).scrollTop() + $(this).height() >= $(this).height()) {
                    row = row+2;
                    getPOstatusData(row);
                }
            });

			$('#tbl_body_details_data').on('click', '.parts_status_view', function(e) {
				e.preventDefault();
				redirect_val('ISO', $(this).attr('data-name'));
			});

			$('#tbl_body_details_data').on('click', '.parts_status_view_name', function(e) {
				e.preventDefault();
				redirect_val("PSTAT", $(this).attr('data-code'));
			});
		});

		function getPOstatusData(row) {
			var url = "{{ url('/postatusajax') }}";
			var token = "{{ Session::token() }}";
			var po = "<?php if(isset($po_s)){ if($po_s == 'X') { echo '';} else{ echo $po_s; }} ?>";
			var name = "<?php if(isset($name)){ if($name == 'X') { echo '';} else{ echo $name; }} ?>";
			var data = {
				_token: token,
				po: po,
				row: row,
				name: name
			}

			$.ajax({
				url: url,
				type: 'GET',
				//contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				dataType: 'JSON',
				data: data,
			}).done(function(data, textStatus, jqXHR) {
				console.log(data);
				mrp_details(data.details_data);
				r3answer(data.answers);
			}).fail(function(data, textStatus, jqXHR) {
				console.log("error");
			});
			
		}

		function mrp_details(data) {
			$('#tbl_body_details_data').html('');
			var tbl_body_details_data = '';
			$.each(data, function(i, x) {
				var color = '';
				if (x.Check == 'FromStock') {
					color = '#89C4F4;';
				} else if (x.Check == 'Allocation') {
					color = '#F3565D';
				}


				 tbl_body_details_data = '<tr class="odd gradeX">'+
							'<td>'+
								'<a href="javascript:;" class="parts_status_view" data-name="'+x.PNAME+'">'+x.KCODE+'</a>'+
							'</td>'+
							'<td>'+
								'<a href="javascript:;" class="parts_status_view_name"  data-code="'+x.KCODE+'">' + x.PNAME + '</a>'+
							'</td>'+
							'<td>'+
								//+ String(x.VI) +
								x.VI.toString() +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.PoReq +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.PoBalance +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.GrossReq +
							'</td>'+
							'<td class="details" style="text-align: right">'+
								+ x.ASSY100 +
							'</td>'+
							'<td class="details" style="text-align: right">'+
								+ x.WHS100+
							'</td>'+
							'<td class="details" style="text-align: right">'+
								+ x.WHS102+
							'</td>'+
							'<td style="text-align: right">'+
								+ x.TOTAL +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.InvResr +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.PrBal +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.MRP +
							'</td>'+
							'<td>'+
								//+ x.PR_Issued +
								x.PR_Issued +
							'</td>'+
							'<td>'+
								//+ x.PR +
								x.PR
								 +

							'</td>'+
							'<td>'+
								+ x.PickUpGR +
							'</td>'+
							'<td>'+
								+ x.YecPo +
							'</td>'+
							'<td>'+
								//+ x.YecPu +
								x.YecPu.toString() +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.PuQty +
							'</td>'+
							'<td style="background-color:'+color+'">'+
								//+ String(x.Check) +
								x.Check.toString() +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.Deliaccum +
							'</td>'+
							'<td>'+
								//+ x.Incharge.toString() +
								+ x.Incharge +
							'</td>'+
							'<td>'+
								+ x.RE +
							'</td>'+
							'<td>'+
								+ x.Status +
							'</td>'+
							'<td style="text-align: right">'+
								+ x.AllocationCalc +
							'</td>'+
						'</tr>';

				$('#tbl_body_details_data').append(tbl_body_details_data);
			});
		}

		function r3answer(data) {
			$('#tbl_body_r3answer').html('');
			var tbl_body_r3answer = '';
			$.each(data, function(i, x) {
				var r3ans = new Date(x.r3answer);

				 tbl_body_r3answer = '<tr class="odd gradeX" id="salesorderdata" name="salesorderdata">' +
										'<td>' +
											+ x.po +
										'</td>' +
										'<td>' +
											+ (r3ans.getMonth() + 1) + '/' + r3ans.getDate() + '/' + r3ans.getFullYear()  +
										'</td>' +
										'<td style="text-align: right;">' +
											+ x.time  +
										'</td>' +
										'<td style="text-align: right;">'
											+ x.qty +
										'</td>' +
										'<td>' +
											+ x.re +
										'</td>' +
									'</tr>';

				$('#tbl_body_r3answer').append(tbl_body_r3answer);
			});
		}
	</script>

@endpush