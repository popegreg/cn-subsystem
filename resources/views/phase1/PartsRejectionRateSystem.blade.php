<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PartRejectionRateSystem.blade.php
     MODULE NAME:  [3004] PRRS
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.28
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.28     MESPINOSA       Initial Draft
     200-00-01   1     2017.02.09     MESPINOSA       Update implementation based 
                                                        on the new requirements.
*******************************************************************************/
?>
@extends('layouts.master')

@section('title')
	Parts Rejection Rate System | Pricon Microelectronics, Inc.
@endsection

@section('content')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_PRRS'))
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

				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-refresh"></i>  PARTS REJECTION RATE SYSTEM (PRRS)
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-md-12">
								<h4>Suggest purchase quantity for the Purchase Request.</h4>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="portlet box blue">

									<div class="portlet-title">
										<div class="caption">
											REJECTION RATE UPDATE
										</div>
									</div>

									<div class="portlet-body">
										<div class="form-group">
											<div class="row">
												<div class="col-md-4" >
													<h4><span class="label label-info"> Last Update : </span></h4>
												</div>
												<div class="col-md-8 pull-left">
													<label for="count" class="control-label" id="lbl_last_update">
														@if(isset($prrs)) 
															{{ $prrs->updated_at }}
														@endif
													</label>
												</div>
											</div>
											<div class="row">	
												<div class="col-md-4">
													<h4><span class="label label-info"> Period Covered : </span></h4>
												</div>
												<div class="col-md-2">
													<input type="text" class="form-control pull-center" style="width: 90px;" id="txt_period_covered" placeholder="months" name="period_covered"  <?php echo($readonly); ?> value="<?php if(isset($prrs)){echo $prrs->period_covered; } ?>">
												</div>
												<div class="col-md-6"> 
													<h5>months before form last months</h5>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
												</div>
												<div class="col-md-6">
													<span class="help-block"> <?php if(isset($prrs)){ echo $prrs->period; } ?> </span>
													<input type="text" id="prrs_id" placeholder="Id" name="hdn_prrs_id" hidden="true" value="<?php if(isset($prrs)){echo $prrs->id; } ?>">
												</div>
											</div>
										</div>
									</div>

								</div>
							</div>

							<div class="col-md-6">
								<div class="portlet box blue">

									<div class="portlet-title">
										<div class="caption">
											UPLOAD YPICS R3 FILE
										</div>
									</div>

									<div class="portlet-body">
										<form method="POST" enctype="multipart/form-data" action="{{ url('/prrs-uploadfile') }}" class="form-horizontal" id="readfileform" onsubmit="showLoading()">
											<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
											<div class="form-group">
												<label class="control-label col-md-4">Select a File</label>
												<div class="col-md-5">
													<input type="file" class="filestyle" data-buttonName="btn-primary" id="file_ypics" name="file_ypics">
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-9">
													<button type="submit" id="btn_prrs_upload" class="btn blue pull-right" <?php echo($state); ?> >
													<i class="fa fa-upload"></i> Upload </button>
												</div>
											</div>
										</form>
									</div>
									
								</div>
							</div>
						</div>

							<div class="row">
								<div class="col-md-6">
									<div class="portlet box blue">
										<div class="portlet-title">
											<div class="caption">
												ALLOWANCE SETUP
											</div>
										</div>
										<div class="portlet-body">

											<div class="row">
												<div class="col-md-4">
													<h4><span class="label label-info"> Standard : </span></h4>
												</div>
												<div class="col-md-4">
													<input type="text" class="form-control pull-right" id="txt_standard1" placeholder="Standard" name="standard1" <?php echo($readonly); ?> value="<?php if(isset($prrs)){ echo $prrs->standard1 . '%';}?>">
												</div>
											</div>

											<div class="row">
												<div class="col-md-8">
													<span class="help-block"> To reflect, this should be correctly input into "Classification" in Item Master</span>
												</div>
											</div>

											<div class="row">

												<div class="col-md-12">

													<div class="portlet box blue">
														<div class="portlet-title">
															<div class="caption">
																Classifications:
															</div>
														<!-- <div class="caption">
															<label for="count" class="control-label pull-left" id="lbl_count"> 
															&nbsp;&nbsp;&nbsp;1 item/s </label>
														</div> -->
													</div>
													<div class="portlet-body"> <!-- id="div_table" -->
														<div class="table-responsive scroller" style="max-height: 300px;height:180px; overflow: scroll; overflow-x: hidden;">
															<table class="table table-striped table-bordered table-hover" id="tbl">
																<thead>
																	<tr>
																		<th>
																			No.
																		</th>
																		<th>
																			Select
																		</th>
																		<th>
																			Qty. (margin)
																		</th>
																		<th>
																			Low %
																		</th>
																		<th>
																			High %
																		</th>
																	</tr>
																</thead>
																<tbody id="table_body" >
																	<?php $ctr = 1; ?>
																	@foreach($sclassifications as $sclassification)
																	<tr class="odd gradeX" id="item<?php echo $ctr;?>">
																		<td id="count">
																			<?php echo $ctr; ?>
																		</td>
																		<td>
																			<select name="select_classification" id="sitem<?php echo $ctr;?>" class="form-control form-filter input-sm select_classification" <?php echo($state); ?> >
																				<option value="0">Select...</option>
																				@foreach($classifications as $classification)
																				<option value="{{ $classification->bunr }}" 
																					<?php if($sclassification->classification == $classification->bunr)
																					{
																						echo 'selected';
																					}
																					?> 
																				> {{ $classification->bunr }}</option>
																				@endforeach
																			</select>
																		</td>
																		<td>
																			<input type="text" class="form-control text-right pull-right input-sm txt_qty" id="txt_qty" name="txt_qty" <?php echo($readonly); ?> value="<?php if(isset($prrs)){echo $sclassification->margin_qty;} ?>">
																		</td>
																		<td>
																			<input type="text" class="form-control text-right pull-right input-sm txt_hpercent" id="txt_hpercent" name="txt_hpercent" <?php echo($readonly); ?> value="<?php if(isset($prrs)){ echo $sclassification->percentage_l;} ?>%">
																		</td>
																		<td>
																			<input type="text" class="form-control text-right pull-right input-sm txt_percent" id="txt_percent" name="txt_percent" <?php echo($readonly); ?> value="<?php if(isset($prrs)){echo $sclassification->percentage_h;} ?>%">
																		</td>
																	</tr>
																	<?php $ctr ++; ?>
																	@endforeach
																	<?php if(count($sclassifications) == 0) { echo $select; $ctr = 2; } ?>
																</tbody>
															</table>
														</div>
													</div>

													<div style="margin-top: 5px;">
														<tr>
															<button type="button" onclick="javascript:action('ADD');" class="btn green input-sm pull-left" <?php echo($state); ?> ><i class="fa fa-plus"></i></button>
															<!-- <button class="btn blue input-sm pull-left" <?php echo($state); ?> ><i class="fa fa-edit"></i></button> -->
															<button type="button" onclick="javascript:action('DELETE');" class="btn red input-sm pull-left" <?php echo($state); ?> ><i class="fa fa-minus"></i></button>
															<label for="count" class="col-md-4 control-label pull-left" id="lbl_count"> <?php echo $ctr-1; ?> item/s </label>
														</tr>
													</div>
													<div class="col-md-12">
														<input type="hidden" class="form-control" id="item_count" placeholder="Lower Limit" name="item_count" value="<?php echo $ctr-1; ?>">
													</div>
												</div>
											</div>
										</div>
										<br/>

										<div class="row">
											<div class="col-md-6">
												<h4>For Higher Price Parts<h4>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<h4><span class="label label-info"> Lower Limit Price : </span></h4>
												</div>
												<div class="col-md-4">
													<input type="text" class="form-control pull-right" id="txt_lower_limit_price" placeholder="Lower Limit" name="lower_limit" <?php echo($readonly); ?> value="<?php if(isset($prrs)){ echo $prrs->lower_limit_price;} ?>">
												</div>
											</div>

											<div class="row">
												<div class="col-md-4">
													<h4><span class="label label-info"> Standard : </span></h4>
												</div>
												<div class="col-md-4">
													<input type="text" class="form-control pull-right" id="txt_standard2" placeholder="Standard" name="standard2" <?php echo($readonly); ?> value="<?php if(isset($prrs)){ echo $prrs->standard2 . '%';} ?> ">
												</div>
											</div>

											<div class="row">
												<div class="col-md-4 pull-right">
													<input type="hidden" id="lastday" name="lastday" value="<?php if(isset($prrs)){ echo $prrs->last_day;} ?> ">
													<button type="button" onclick="javascript:save();" class="btn btn-success" <?php echo($state); ?> 
													<?php if(isset($prrs)){ $date1=date('m/d/y'); if (strtotime($date1) <= strtotime($prrs->last_day)) {echo ''; }} ?>
													>REJECTION RATE UPDATE </button>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="portlet box blue">
										<div class="portlet-title">
											<div class="caption">
												GENERATE REVISED GR PO
											</div>
										</div>
										<div class="portlet-body">
											<div class="row">
												<div class="col-md-6">
													<h4>Additional qty consideration:</h4>
												</div>
											</div>
											<form class="form-horizontal" role="form" method="POST" action="{{ url('/prrs-export') }}"> <!--onsubmit="showLoading()"-->
												<div class="portlet-body">
													{!! csrf_field() !!}
													<div class="row">
														<div class="col-md-2">
															<h4><span class="label label-info"> For GR PO </span></h4>
														</div>
														<div class="col-md-6">
															<input type="text" class="form-control pull-right" id="txt_for_gr_po" placeholder="qty" name="forgrpo" <?php echo($readonly); ?> value="<?php if(isset($prrs)){ echo $prrs->for_gr_po;} ?> ">
														</div>
														<div class="col-md-2"> 
															<h4>PCS</h4>
														</div>
													</div>
													<div class="row">
														<div class="pull-right">
															<button type="submit" class="btn blue" <?php echo($state); ?> ><i class="fa fa-print"></i> GENERATE REVISED PR</button> &nbsp;&nbsp;&nbsp;&nbsp;
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>

							</div>

						</div>
					</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

	<div id="periodend" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-body">
					<p>Please update Period Covered.</p>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
				</div>
			</div>
		</div>
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
@endsection

@push('script')
	<script type="text/javascript">

		function showLoading()
		{
			$('#loading').modal('show');
		}

		Date.prototype.mmddyyyy = function() {
		   var yyyy = this.getFullYear().toString();
		   var mm = (this.getMonth()+1).toString();
		   var dd  = this.getDate().toString();
		   return (mm[1]?mm:"0"+mm[0]) + '/' + (dd[1]?dd:"0"+dd[0]) + '/' + yyyy;
		};

		$( document ).ready(function() 
		{
			d = new Date();

			if ($('#lastday').val() < d.mmddyyyy())
			{
		   		$('#periodend').modal('show');
			}
		});

		function action(action)
		{
			if(action == "ADD")
			{
				var count = parseInt($('#item_count').val()) + 1;
				var h = $("#div_table").height();
				var newItem = '<tr class="odd gradeX" id="item' + count + '"><td id="count" '+ count +'>' + count + '</td><td> <select name="select_classification" id="sitem' + count + '" class="form-control form-filter input-sm select_classification"></select></td><td><input type="text" class="form-control text-right pull-right input-sm txt_qty" id="txt_qty" name="txt_qty"></td><td><input type="text" class="form-control text-right pull-right input-sm txt_hpercent" id="txt_hpercent" name="txt_hpercent" value="0.0%"></td><td><input type="text" class="form-control text-right pull-right input-sm txt_percent" id="txt_percent" name="txt_percent" value="0.0%"></td></tr>";'
				$('#table_body').append(newItem);
				$('#item_count').val(count);
				$('#lbl_count').text(' ' + count + ' item/s');


				var select1 = document.getElementById("sitem1");
				var select2 = document.getElementById("sitem" + count);
				select2.innerHTML = select1.innerHTML;
				select2.value = 0;
			}
			else if (action == "DELETE")
			{
				var curcount = parseInt($('#item_count').val());
				var curitem = "#item"+ $('#item_count').val();

				if (curcount > 1)
				{
					$(curitem).remove();
					$('#item_count').val(curcount - 1);
					$('#lbl_count').text(' ' + curcount - 1 + ' item/s');
				}
			}
		}

		function save()
		{
			var prrs_arr = new Array;
			var obj_data = new Object;
			var cnt = 0;
			var select = new Array;
			var value1 = new Array;
			var value2 = new Array;
			var value3 = new Array;
			var is_valid = true;


			$(".select_classification").each(function()
			{
				var id = $(this).attr('name');
				obj_data[id] = $(this).val();
				select[cnt] = obj_data[id];
				cnt++;
			});

			cnt = 0;
			$(".txt_qty").each(function()
			{
				var id = $(this).attr('name');
				obj_data[id] = $(this).val();
				if(parseInt(obj_data[id]))
				{
					value1[cnt] = parseInt(obj_data[id]);
				}
				else
				{
					// if(parseInt(obj_data[id]))
					// {	
						is_valid = false;
					// }

				}
				cnt++;
			});
			cnt = 0;
			$(".txt_hpercent").each(function()
			{
				var id = $(this).attr('name');
				obj_data[id] = $(this).val();
				if(parseFloat(obj_data[id]))
				{
					value2[cnt] = parseFloat(obj_data[id]);
				}
				else
				{
					// if(parseInt(obj_data[id]) <> '0.0%' )
					// {	
					// 	is_valid = false;
					// }
				}
				cnt++;
			});
			cnt = 0;
			$(".txt_percent").each(function()
			{
				var id = $(this).attr('name');
				obj_data[id] = $(this).val();
				if(parseFloat(obj_data[id]))
				{
					value3[cnt] = parseFloat(obj_data[id]);
				}
				else
				{
					// if(parseInt(obj_data[id]) <> '0.0%' )
					// {	
					// 	is_valid = false;
					// }
				}

				cnt++;
			});

			if(is_valid)
			{

				// alert($('#prrs_id').val());
				prrs_arr[0] = $('#prrs_id').val();
				prrs_arr[1] = $('#txt_period_covered').val();
				prrs_arr[2] = $('#txt_standard1').val();
				prrs_arr[3] = $('#txt_lower_limit_price').val();
				prrs_arr[4] = $('#txt_standard2').val();
				prrs_arr[5] = $('#txt_for_gr_po').val();

				$.post("{{ url('/prrs-save') }}", 
				{
					_token: $('meta[name=csrf-token]').attr('content'),
					prrs_obj: prrs_arr,
					select_arr: select,
					value1_arr:value1,
					value2_arr:value2,
					value3_arr:value3
				})
				.done(function(data)
				{
		     		$.alert(data, 
		     		{
		     			position: ['center', [-0.40, 0]],
		     			type: 'success',
		     			closeTime: 2000,
		     			autoClose: true,
		     			id:'alert_suc'
		     		});
	     			window.location.reload();
		     	})
				.fail(function() 
				{
					$.alert('Unable to update PRRS at this moment. Please try again.', 
					{
						position: ['center', [-0.40, 0]],
						type: 'danger',
						closeTime: 2000,
						autoClose: true
					});
				});
			}
			else
			{
				$.alert('Unable to update PRRS at this moment. Please check the inputted values.', 
				{
					position: ['center', [-0.40, 0]],
					type: 'danger',
					closeTime: 2000,
					autoClose: true
				});
			}
		}

	</script>
@endpush