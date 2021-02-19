@extends('layouts.master')

@section('title')
	Packing List System | Pricon Microelectronics, Inc.
@endsection

@section('content')
	@include('includes.header')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_PLSYSTEM'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-bars"></i>  PACKING LIST SYSTEM
						</div>
					</div>
					<div class="portlet-body">

						{{-- <div class="row">
							<div class="col-md-12">
								<div class="portlet box">
									<div class="portlet-body">
										
									</div>
								</div>
							</div>
						</div> --}}

						<div class="row">
							<div class="col-md-12">
								<div class="portlet box">
									<div class="portlet-body">
										<!-- <form class="form-horizontal" role="form" method="POST" action="{{ url('/packinglistsystem-save') }}">
										{!! csrf_field() !!} -->
											<div class="row">
												<div class="col-md-12">
													<!-- <a href="#" id="lnk_masterreport" class="btn btn-success pull-right">
														<i class="fa fa-file-o"></i> Master File Report
													</a> -->
													<a href="#" onclick="javascript: addDetails();" id="lnk_addnew" class="btn btn-primary pull-right input-sm" <?php echo($state); ?> >
														<i class="fa fa-plus"></i> Add New
													</a>
												</div>
											</div>

											<div class="row">
												<!-- LEFT SIDE -->
												<div class="col-md-6">

													<div class="form-group">
														<div class="col-md-12">
															<h3>PACKING LIST</h3>
														</div>
													</div>
														<?php $id = "";?>
                                          				@if(isset($packinglist))
															@foreach($packinglist as $packingInfo)
																<?php if(isset($packinglist)){$id = $packingInfo->id; } ?>
                      											<input type="hidden" class="form-control" id="edittbl" name="edittbl" value="<?php if(count($packingdetails) > 0){ echo '1' ;} else{ echo '0';} ?>" />
															@endforeach
                                          				@endif
                                          				<input type="hidden" class="form-control" id="recid" name="recid" value="<?php echo $id; ?>" />
													<div class="form-group">
														<label class="col-md-12 input-sm">Sold To:</label>
														<div class="col-md-12">
																<select id="ddl_soldto" name="soldtoid" class="form-control input-sm" <?php echo($state); ?> >
																	<option selected="selected" value="N/A">-- Select --</option>
																	<?php
																	 $yec = '';
																	?>
                                          							@if(isset($soldto))
                                              							@if (isset($packingInfo))
                                              								@foreach($soldto as $value)
																				<option value="{{ $value->description . '|' .$value->code}}"
																					<?php if(isset($packingInfo)){ if($packingInfo->sold_to_id == $value->code){ echo 'selected';}}?> >
																					{{ $value->companyname }}
																				</option>
																			@endforeach
																		@else
																			@foreach($soldto as $value)
																				<option value="{{ $value->description . '|' .$value->code}}"
																					<?php if($value->code == '10011'){ echo 'selected'; $yec = $value->description;}?> >
																					{{ $value->companyname }}
																				</option>
																			@endforeach
                                              							@endif
																		
                                          							@endif
																</select>
														</div>
														<div class="col-md-12">
															<textarea id="txa_soldto" name="soldto" class="form-control input-sm" rows="6" maxlength="900" style="resize:none;"><?php if(isset($packinglist)){echo $packinglist[0]->sold_to; } else { echo $yec; } ?></textarea>
														</div>
													</div>

													<div class="form-group">
														<div class="row">
															<div class="col-md-6">
																<label class="col-md-12 input-sm">Carrier:</label>
																<div class="col-md-12">
																	<select id="ddl_carrier" class="form-control input-sm" name="carrier" <?php echo($state); ?> >
																		<option value="675">N/A</option>
                                              							@if(isset($carrier))
																			@foreach($carrier as $value)
																				<option value="{{$value->id}}"
																				<?php if(isset($packingInfo)){ if($packingInfo->carrier == $value->id){ echo 'selected';}}?> >
																				{{ $value->description }}
																				</option>
																			@endforeach
                                              							@endif
																	</select>
																</div>
															</div>
															<div class="col-md-6">
																<label class="col-md-12 input-sm">Date Ship:</label>
																<div class="col-md-12">
																	<input id="dtp_dateship" class="form-control date-picker" readonly="true" size="16" type="text" name="dateship" value="<?php if(isset($packinglist)){echo $packingInfo->date_ship; } ?>" <?php echo($state); ?> >
																</div>
															</div>
														</div>
													</div>

													<div class="form-group">
														<div class="row">
															<div class="col-md-6">
																<label class="col-md-12 input-sm">Port of Loading:</label>
																<div class="col-md-12">
																	<input id="txa_portloading" type="text" name="portloading" maxlength="900" class="form-control input-sm" value="<?php if(isset($packinglist)){echo $packingInfo->port_loading; } else {echo "MANILA, PHILIPPINES";}?>" <?php echo($readonly); ?> >
																</div>
															</div>
															<div class="col-md-6">
																<label class="col-md-12 input-sm">Port of Destination:</label>
																<div class="col-md-12">
																	<select id="ddl_portdes" class="form-control input-sm" name="portdes" <?php echo($state); ?> >
																		<option value="687">N/A</option>
                                              							@if(isset($portOfDestination))
																			@foreach($portOfDestination as $value)
																				<option value="{{$value->id}}"
																				<?php if(isset($packingInfo)){ if($packingInfo->port_destination == $value->id){ echo 'selected';}}?> >
																				{{ $value->description }}
																				</option>
																			@endforeach
                                              							@endif
																	</select>
																</div>
															</div>
														</div>
													</div>

													<div class="form-group">
														<div class="row">
															<div class="col-md-12">
																<label class="col-md-4 input-sm">Gross Weight (For Invoicing):</label>
																<div class="col-md-4">
																	<input id="tx_gweight" type="text" name="gweight" class="form-control input-sm" value="<?php if(isset($packinglist)){echo $packingInfo->grossweight_invoicing; } ?>" <?php echo($readonly); ?> >
																</div>
															</div>
														</div>
													</div>

												</div>

												<!-- RIGHT SIDE -->
												<div class="col-md-6">
													<div class="form-group">
														<div class="row">
															<div class="col-md-2">
															</div>
															<label class="col-md-10 input-sm">Ctrl #:</label>
															<div class="col-md-2">
															</div>
															<div class="col-md-10">
																<input id="txt_controlno" type="text" class="form-control input-sm" name="controlno" maxlength="900" value="<?php if(isset($packinglist)){echo $packingInfo->control_no; } ?>" <?php echo($readonly); ?> >
															</div>
															<label class="col-md-12 input-sm">No. and Date of Invoice #:</label>
															<div class="col-md-12">
																<input id="dtp_invoice" class="form-control date-picker input-sm" readonly="true" size="16" type="text" name="invoicedate" value="<?php if(isset($packinglist)){echo $packingInfo->invoice_date; }else{echo date('m/d/Y');} ?>" <?php echo($state); ?> >
															</div>
															<label class="col-md-12 input-sm">Remarks:</label>
															<div class="form-group">
																<div class="row">
																	<div class="col-md-1">
																	</div>
																	<div class="col-md-3">
																		<label class="col-md-12 input-sm">Time:</label>
																	</div>
																	<div class="col-md-6">
																		<input id="dtp_remarkstime" name="remarkstime" class="form-control input-sm" size="16" type="text" value="<?php if(isset($packinglist)){echo $packingInfo->remarks_time; } ?>" <?php echo($state); ?> >
																	</div>
																</div>
																<div class="row">
																	<div class="col-md-1">
																	</div>
																	<div class="col-md-3">
																		<label class="col-md-12 input-sm">Pick-Up Date:</label>
																	</div>
																	<div class="col-md-6">
																		<input id="dtp_remarkspickupdate" name="remarkspickupdate" class="form-control date-picker input-sm" size="16" type="text" data-date="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy" value="<?php if(isset($packinglist)){echo $packingInfo->remarks_pickupdate; } ?>" <?php echo($state); ?> >
																	</div>
																	<div class="col-md-2">
																	</div>
																</div>
																<div class="row">
																	<div class="col-md-1">
																	</div>
																	<div class="col-md-3">
																		<label class="col-md-12 input-sm">S / No.:</label>
																	</div>
																	<div class="col-md-6">
																		<input id="txt_s_no" type="text" class="form-control input-sm" name="sno" maxlength="900" value="<?php if(isset($packinglist)){echo $packingInfo->remarks_s_no; } ?>" <?php echo($readonly); ?> >
																	</div>
																	<div class="col-md-2">
																	</div>
																</div>
															</div>
															<label class="col-md-12 input-sm">Ship To:</label>
															<div class="col-md-12">
																<textarea id="txa_shipto" name="shipto" class="form-control input-sm" rows="6" maxlength="900" style="resize:none;" <?php echo($readonly); ?> ><?php 
																	if(isset($packinglist)){
																		echo $packinglist[0]->ship_to;
																	} else {
																		$ship = 'NITTSU NEC- Logistics., Co. Ltd.'."\n";
																		$ship .= '1-4-1 OSAKU, SAKURA-SHI'."\n";
																		$ship .= 'CHIBA 285-0802, JAPAN'."\n";

																		echo $ship;
																	}
																?></textarea>
															</div>
														</div>
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label class="col-md-12 input-sm">Description of Goods:</label>
														<div class="col-md-6">
															<select id="ddl_shipinstruction" name="shipinstruction" class="form-control input-sm" <?php echo($state); ?> >
																<option value="NA">NA</option>
                                          							@if(isset($descOfGoods))
                                          								@if (isset($packingInfo))
                                          									@foreach($descOfGoods as $value)
																				<option value="{{$value->id}}"
																					<?php if(isset($packingInfo)){ if($packingInfo->description_of_goods == $value->id){ echo 'selected';}}?> >
																					{{ $value->description }}
																				</option>
																			@endforeach
																		@else
																			@foreach($descOfGoods as $value)
																				<option value="{{$value->id}}"
																					<?php if($value->id == '122'){ echo 'selected';}?> >
																					{{ $value->description }}
																				</option>
																			@endforeach
                                          								@endif
																		
                                          							@endif
															</select>
														</div>
														<div class="col-md-1">
														</div>
														<label class="col-md-5 input-sm">Special Instruction / Shipping Instruction</label>
													</div>
												</div>
											</div>


											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label class="col-md-12 input-sm">Case Marks:</label>
														<div class="col-md-12">
															<textarea id="txa_casemarks" name="casemarks" class="form-control input-sm" maxlength="900" style="resize:none;" rows="6" <?php echo($readonly); ?> ><?php if(isset($packinglist)){echo $packinglist[0]->case_marks; } else {
																		$ship = 'NITTSU NEC- Logistics., Co. Ltd.'."\n";
																		$ship .= '1-4-1 OSAKU, SAKURA-SHI'."\n";
																		$ship .= 'CHIBA 285-0802, JAPAN'."\n";
																		$ship .= 'C/NO:';

																		echo $ship;
																	} ?></textarea>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label class="col-md-12 input-sm">Note / Highlight:</label>
														<div class="col-md-12">
															<textarea id="txa_note" name="note" class="form-control input-sm" maxlength="900" style="resize:none;" rows="6" <?php echo($readonly); ?> ><?php if(isset($packinglist)){echo $packinglist[0]->note; } ?></textarea>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<div class="row">
															<div class="col-md-3">
																<label class="col-md-12 input-sm">From:</label>
																<label class="col-md-12 input-sm">To:</label>
																<label class="col-md-12 input-sm">Freight:</label>
															</div>
															<div class="col-md-9">
																<label class="col-md-12 input-sm"><strong>PRICON</strong></label>
																<div class="col-md-12">
																	<input id="txt_to" type="text" class="form-control input-sm" name="sno" maxlength="900" value="<?php if(isset($packinglist)){echo $packingInfo->to; } else { echo "JAPAN";} ?>" <?php echo($readonly); ?> >
																	<input id="txt_freight" type="text" class="form-control input-sm" name="sno" value="<?php if(isset($packinglist)){echo $packingInfo->freight; }else { echo "COLLECT";} ?>" <?php echo($readonly); ?> >
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label class="col-md-12 input-sm">Prepared By:</label>
														<div class="col-md-12">
															<select id="preparedby" name="preparedby" class="form-control input-sm" <?php echo($state); ?> >
																<option value=""></option>
																@if(isset($preparedby))
																	@foreach($preparedby as $prep)
																		<option value="{{$prep->user}}"
																			<?php if(isset($packingInfo)){ if($packingInfo->preparedby == $prep->user){ echo 'selected';}}?> >
																			{{ $prep->user }}
																		</option>
																	@endforeach
																@endif
															</select>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label class="col-md-12 input-sm">Checked By:</label>
														<div class="col-md-12">
															<select id="checkedby" name="checkedby" class="form-control input-sm" <?php echo($state); ?> multiple="multiple">
																<option value=""></option>
																@if(isset($checkedby))
																	@foreach($checkedby as $checkd)
																		<option value="{{$checkd->user}}"
																			<?php if(isset($packingInfo)){
																				$chcks = explode(" / ",$packingInfo->checkedby);
																				foreach ($chcks as $key => $chck) {
																					if($chck == $checkd->user){ 
																						echo 'selected';
																						}
																					}
																				}
																			?> >
																			{{ $checkd->user }}
																		</option>
																	@endforeach
																@endif
															</select>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<a href="javascript:;" id="bu2" class="btn btn-primary pull-right input-sm" <?php echo($state); ?> >
														<i class="fa fa-cubes"></i>
														Packing List Details
													</a>
												</div>
											</div>

											<br/>

											<div class="row">
												<div class="col-md-12">
													<div class="scroller" style="height:300px">
														<div class="table-responsive">
															<table class="table table-striped table-bordered table-hover" id="tbl_viewtable" style="font-size:10px">
																<thead>
																	<tr>
																		<td>Box No</td>
																		<td>PO No.</td>
																		<td>Description / Model No.</td>
																		<td>Product Code</td>
																		<td>Price</td>
																		<td>Quantity</td>
																		<td>Unit of measurement</td>
																	</tr>
																</thead>
																<tbody id="view_tbl_body_row">
	                                                  				@if(isset($packingdetails))
																		@foreach($packingdetails as $details)
																		<tr>
																			<td>{{ $details->box_no }}</td>
																			<td>{{ $details->po }}</td>
																			<td>{{ $details->description }}</td>
																			<td>{{ $details->item_code }}</td>
																			<td>{{ number_format($details->price,4) }}</td>
																			<td>{{ $details->qty }}</td>
																			<td>{{ $details->gross_weight }}</td>
																		</tr>
																		@endforeach
																	@endif
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class=" text-center">
															<button type="submit" onclick="javascript: save();" id="btn_save" class="btn btn-primary input-sm" <?php echo($state); ?> ><i class="fa fa-save"></i>Save</button>
															<a href="javascript:;" class="btn purple input-sm" id="btn_printModal"><i class="fa fa-print"></i> Print Details</a>
													</div>
													<a href="{{url('/packinglistsystem')}}" class="btn grey-gallery pull-right input-sm"><i class="glyphicon glyphicon-chevron-left"></i>Back</a>
												</div>
											</div>
										<!-- </form> -->
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

	<!--Print Modal-->
	<div id="PrintModal" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Set Margin</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form-horizontal" role="form" method="POST" action="{{ url('/packinglistsystem-printpdf') }}" target="_blank" id="print_form">
								{!! csrf_field() !!}
								<input type="hidden" class="form-control input-sm" id="printid" name="id" value="<?php if(isset($packinglist)){echo $packingInfo->id; } ?>" />
								<div class="form-group">
									<label class="control-label col-sm-3">Top</label>
									<div class="col-sm-9">
										<input type="text" name="top" class="form-control" placeholder="Ex: 5 / 10 / 15 / etc." value="24">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Right</label>
									<div class="col-sm-9">
										<input type="text" name="right" class="form-control" placeholder="Ex: 5 / 10 / 15 / etc" value="5">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Bottom</label>
									<div class="col-sm-9">
										<input type="text" name="bottom" class="form-control" placeholder="Ex: 5 / 10 / 15 / etc" value="5">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Left</label>
									<div class="col-sm-9">
										<input type="text" name="left" class="form-control" placeholder="Ex: 5 / 10 / 15 / etc" value="5">
									</div>
								</div>
								<a href="javascript:;" formtarget="_blank" id="btn_print" class="btn btn-success input-sm pull-right" <?php echo($state); ?> ><i class="fa fa-print"></i>Print</a>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--MSG-->
	<div id="msg" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h5 class="modal-title" id="title"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<p id="errmsg">

							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button data-dismiss="modal" class="btn red pull-right input-sm"><i class="fa fa-close"></i>Close</button>
				</div>
			</div>
		</div>
	</div>

	<!-- MODAL -->
	<div id="bu2-orders" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-xl gray-gallery">
			<div class="modal-content ">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">YPICS <?php if(isset($dbconnection)){echo $dbconnection; } ?> - Orders</h4>
				</div>

				<div class="modal-body">
					<form class="form-horizontal" id="frm_orders" method="post">
					{!! csrf_field() !!}
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="col-md-10">
										<input id="txt_porder" type="text" class="form-control" name="search">
										<input id="txt_db" type="hidden" class="form-control" name="dbconnection" value="<?php if(isset($dbconnection)){echo $dbconnection; } ?>">
									</div>
									<div class="col-md-2">
										<button type="button" id="btn_search" onclick="javascript: getOrders(); " class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
									</div>
								</div>
							</div>
						</div>
					</form>

					<div class="row">
						<div class="col-md-12">
							<div class="scroller" style="height:200px;">
								<table class="table table-bordered table-responsive table-striped table-hover">
									<thead>
										<tr>
											<td>PO</td>
											<td>Description</td>
											<td>Product Code</td>
											<td>Price</td>
											<td visible="false">Qty</td>
										</tr>
									</thead>
									<tbody id="srch_tbl_body" >
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<br>
					<hr>
					<br>

					<div class="row"><!--style="width:1500px; height:500px; overflow:auto;"-->
						<div class="col-md-12">
							<div class="portlet box">
								<div class="portlet-body">
									<div class="table-responsive">
										<table class="table table-striped table-hover table-bordered" id="tbl_editable">
											<!-- to manipulate data edit this script assets/admin/pages/scripts/table-editable.js -->
											<thead id="edit_tbl_body">
												<tr>
													<td>Box No</td>
													<td>PO No.</td>
													<td>Description / Model No.</td>
													<td>Product Code</td>
													<td>Price</td>
													<td>Quantity</td>
													<td>Unit of Measurement</td>
													<!-- <td>Edit</td> -->
													<td>Remove</td>
												</tr>
											</thead>
											<tbody id="edit_tbl_body_row">
												<?php $ctr=0;?>
	                                            @if(isset($packingdetails))
													@foreach($packingdetails as $details)
													<tr id="tr<?php echo $ctr; ?>" >
														<td><input id="boxno" type="text" class="form-control" name="boxno" value="<?php echo $details->box_no ?>" <?php echo($readonly); ?> ></td>
														<td id="pono">{{ $details->po }}</td>
														<td id="desc">{{ $details->description }}</td>
														<td>{{$details->item_code}}<input id="prodcode" type="hidden" class="form-control" name="boxno" value="<?php echo $details->item_code ?>" <?php echo($readonly); ?> ></td>
														<td>{{number_format($details->price,4)}}<input id="price" type="hidden" class="form-control" name="boxno" value="<?php echo number_format($details->price,4) ?>" <?php echo($readonly); ?>></td>
														<td><input id="qty" type="text" class="form-control" name="boxno" value="<?php echo $details->qty ?>" <?php echo($readonly); ?>></td>
														<td><input id="gross" type="text" class="form-control" name="boxno" value="<?php echo $details->gross_weight ?>" <?php echo($readonly); ?> ></td>
														<!-- <td><a href="#" onclick="edit('+ trcnt +');" value="'+ trcnt +'">Edit</a></td> -->
														<td><a href="#" onclick="del('<?php echo $ctr; ?>');" value="<?php echo $ctr; ?>" <?php echo($state); ?> >Remove</a></td>
													</tr>
													<?php $ctr++; ?>
													@endforeach
												@endif
											</tbody>
										</table>
									</div>
                          				<input type="hidden" class="form-control" id="trcnt" name="trcnt" value="<?php echo $ctr;?>" />
								</div>
							</div>
						</div>
					</div>

				</div>

				<div class="modal-footer">
					<button data-dismiss="modal" class="btn grey-gallery pull-right"><i class="fa fa-close"></i>Cancel</button>
					<button onclick="javascript: reflect();" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add to Details</button>
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
		$( document ).ready(function() {
			$('#bu2').on('click',function(){
				$('#bu2-orders').modal('show');
			});

			$('#btn_printModal').on('click', function() {
				$('#PrintModal').modal('show');
			});

			$('#btn_print').on('click', function() {
				var top = $('input[name=top]').val();
				var right = $('input[name=right]').val();
				var bottom = $('input[name=bottom]').val();
				var left = $('input[name=left]').val();
				var errmsg = '';
				var title = '';

				if (top == '' || left == '' || bottom == '' || right == '') {
					title = '<i class="fa fa-exclamation-triangle"></i> Failed!';
					errmsg = "You need to fill out all margins";
					$('#title').html(title);
					$('#errmsg').html(errmsg);
					$('#msg').modal('show');
				} else {
					$('#print_form').submit();
					$('#PrintModal').modal('hide');
				}

			});

			$('#checkedby').select2({
				placeholder: "Select for checking..."
			});

			$('#ddl_soldto').on('change', function() {
				if($(this).val() == '-1')
				{
					$('#txa_soldto').val("");
				}
				else
				{
					var values = $(this).val().split('|');

	                if(values.length == 2)
	                {
	                    $("#txa_soldto").val(values[0]);
	                }
	                else
	                {
	                	$('#txa_soldto').val("");
	                }

					// $('#txa_soldto').val(sel.value);
				}
			});

			$('#frm_orders').on('submit', function(event) {
				event.preventDefault();
				getOrders();
			});
		});
		// DEAN 


		function save()
		{

			var details = new Array;
			var detailsListRow = new Array;
			var detailsList = new Array;
			var cnt = 0;
			var action = '';
			var is_valid = true;

			if($('#ddl_soldto').val() == '-1')
			{
				details[0] = '';
				is_valid = false;
			}
			else
			{

				var values = $('#ddl_soldto').val().split('|');

	            if(values.length == 2)
	            {
	                details[0] = values[1];
	            }
	            else
	            {
					details[0] = $("#ddl_soldto option:selected").text();
	            }

			}

			//details[2] = $('#ddl_carrier').val();

			if($('#ddl_carrier').val() == '-1')
			{
				details[2] = '';
				is_valid = false;
			}
			else
			{
				details[2] = $('#ddl_carrier').val();
			}


			if($('#ddl_portdes').val() == '-1')
			{
				details[5] = '';
				is_valid = false;
			}
			else
			{
				details[5] = $('#ddl_portdes').val();
			}

			if($('#ddl_shipinstruction').val() == 'NA')
			{
				details[12] = '';
				is_valid = false;
			}
			else
			{
				details[12] = $('#ddl_shipinstruction').val();
			}

			if($('#txa_soldto').val() == ''
				|| $('#dtp_dateship').val() == ''
				|| $('#txa_portloading').val() == ''
				|| $('#txt_controlno').val() == ''
				|| $('#dtp_invoice').val() == ''
				|| $('#txa_shipto').val() == ''
				|| $('#txa_casemarks').val() == ''
				|| $('#txt_to').val() == ''
				|| $('#txt_freight').val() == ''
				|| $('#preparedby').val() == ''
				|| $('#checkedby').val() == '')
			{
				is_valid = false;
			}//|| $('#txa_note').val() == ''

			//[0] ddl_soldto
			details[1] = $('#txa_soldto').val();
			//[2] ddl_carrier
			details[3] = $('#dtp_dateship').val();
			details[4] = $('#txa_portloading').val();
			//[5] ddl_portdes
			details[6] = $('#txt_controlno').val();
			details[7] = $('#dtp_invoice').val();
			details[8] = $('#dtp_remarkstime').val();
			details[9] = $('#dtp_remarkspickupdate').val();
			details[10] = $('#txt_s_no').val();
			details[11] = $('#txa_shipto').val();
			//[12] ddl_shipinstruction
			details[13] = $('#txa_casemarks').val();
			details[14] = $('#txa_note').val();
			details[15] = $('#txt_to').val();
			details[16] = $('#txt_freight').val();
			// details[17] = $('#recid').val();
			details[18] = $('#preparedby').val();
			details[19] = $('#checkedby').val();
			details[20] = $('#tx_gweight').val();

			if(is_valid)
			{
				if($('#recid').val() == '' || typeof $('#recid').val() === "undefined")
				{
					details[17] = '';
					action ='ADD';
				}
				else
				{
					details[17] = $('#recid').val();
					action ='UPD';
				}

				$('#tbl_viewtable tr').not(':first').each(function(i, row)
				{
					detailsListRow = new Array;
					detailsListRow[0] = $(this).find("td").eq(0).html();
					detailsListRow[1] = $(this).find("td").eq(1).html();
					detailsListRow[2] = $(this).find("td").eq(2).html();
					detailsListRow[3] = $(this).find("td").eq(3).html();
					detailsListRow[4] = parseFloat($(this).find("td").eq(4).html()).toFixed(4);
					detailsListRow[5] = $(this).find("td").eq(5).html();
					detailsListRow[6] = $(this).find("td").eq(6).html();
					detailsList[cnt] = detailsListRow;
					cnt++;
				});

				var data = {
					_token: $('meta[name=csrf-token]').attr('content'),
					details: details,
					detailsList : detailsList
				};

				$.ajax({
					url: "{{ url('/packinglistsystem-save') }}",
					type: "POST",
					dataType: 'JSON',
					data: data
				})
				.done(function(data)
				{
					// alert(data);
					// console.log(data);
					var msg = '';

					if(action == 'ADD')
					{
						msg = 'Packing List Successfully Added.';
					}
					else
					{
						msg = 'Packing List Successfully Updated.';
					}

					$.alert(msg,
					{
						position  : ['center', [-0.40, 0]],
						type      : 'success',
						closeTime : 2000,
						autoClose : true,
						id        :'alert_suc'
					});
					console.log(data);
					
					$('#recid').val(data);
					$('#printid').val(data);

					if(action =='ADD')
					{
						window.location.href= "{{ url('/packinglistdetails?selecteditem=') }}" + data + "&dbconnection=" + {{Auth::user()->productline}};
						$('#recid').val(data);
						$('#printid').val(data);
					}
				})
				.fail(function(data)
				{
					console.log(data);
					$.alert('Details contains invalid values. <br/> 1. Control No. must be unique. <br/> 2. All fields are required. <br/> Please check and try again.',
					{
						position  : ['center', [-0.40, 0]],
						type      : 'error',
						closeTime : 2000,
						autoClose : true,
						id        :'alert_suc'
					});
				});
				}
			else
			{
				$.alert('All fields are required.',
				{
					position  : ['center', [-0.40, 0]],
					type      : 'error',
					closeTime : 2000,
					autoClose : true,
					id        :'alert_suc'
				});
			}
		}

		function rowClick(ctr)
		{
			var obj_data = new Object;
			var cnt = 0;
			var item = new Array;
			var newrow = '';

			$(".srch_item"+ctr).each(function()
			{
				var id = $(this).attr('name');
				item[cnt] = $(this).text();

				cnt++;
			});

			if(item.length > 0)
			{
				var rowCount = $('#tbl_editable tr').length;
				var trcnt = parseInt($('#trcnt').val());

				if(rowCount > 25)
				{
					alert('Adding more than 25 items may have a problem during print. Please register a separate record instead.');
				}

				if(rowCount == 2 && $('#edittbl').val() == '0')
				{
					$('#edit_tbl_body_row').html('');
				}

				// var price = item[3];
				// if (item[3] === 0) {
				// 	price = item[4];
				// }
				var price = parseFloat(item[3]);
				newrow = '<tr id="tr'+ trcnt +'">'
							+'<td><input id="boxno" type="text" class="form-control" name="boxno" value=""></td>'
							+'<td id="pono">'+item[0]+'</td>'
							+'<td id="desc">'+item[1]+'</td>'
							+'<td>'+item[2]+'<input id="prodcode" type="hidden" class="form-control" name="boxno" value="'+item[2]+'"></td>'
							+'<td>'+price.toFixed(4)+'<input id="price" type="hidden" class="form-control" name="price" value="'+price.toFixed(4)+'"></td>'
							+'<td><input id="qty" type="text" class="form-control" name="qty" value=""></td>'
							+'<td><input id="gross" type="text" class="form-control" name="gross" value="pcs"></td>'
							// +'<td><a href="#" onclick="edit('+ trcnt +');" value="'+ trcnt +'">Edit</a></td>'
							+'<td><a href="#" onclick="del('+ trcnt +');" value="'+ trcnt +'">Remove</a></td>'
						+'</tr>';//
				$('#edit_tbl_body_row').append(newrow);
				$('#edittbl').val("1");

				$('#trcnt').val(trcnt+1);
			}
		}

		function edit(cnt)
		{
			alert(cnt);
		}

		function del(cnt)
		{
		    $('table#tbl_editable tr#'+'tr' + cnt).remove();
		}

		function reflect()
		{
		    $('#bu2-orders').modal('toggle');
			var newrow = '';
			var temp_val = '';
			var err = false;

			$('#tbl_editable tr').not(':first').each(function(i, row)
			{
				var boxno = '';
				var prodcode = '';
				var qty = '';
				var gross = '';
				var pono = '';
				var desc = '';
				var price = '';

				pono = '<td>' + $(this).find("td").eq(1).html() + '</td>';
				desc = '<td>' + $(this).find("td").eq(2).html() + '</td>';

			    $(this).find('input').each(function()
			    {
			    	if($(this).attr('id') == 'boxno')
			    	{
			    		if($(this).val().trim() == '')
			    		{
			    			boxno = '<td style="background-color: red">'+ $(this).val() +'</td>';
			    			err = true;
			    		}
			    		else
			    		{
			    			boxno = '<td>'+ $(this).val() +'</td>';
			    		}
			    	}
			    	if($(this).attr('id') == 'prodcode')
			    	{
			    		if($(this).val().trim() == '')
			    		{
			    			prodcode = '<td style="background-color: red">'+ $(this).val() +'</td>';
			    			err = true;
			    		}
			    		else
			    		{
			    			prodcode = '<td>'+ $(this).val() +'</td>';
			    		}
			    	}
			    	if($(this).attr('id') == 'price')
			    	{
			    		if($(this).val().trim() == '')
			    		{
			    			price = '<td style="background-color: red">'+ parseFloat($(this).val()).toFixed(4) +'</td>';
			    			err = true;
			    		}
			    		else
			    		{
			    			price = '<td>'+ parseFloat($(this).val()).toFixed(4) +'</td>';
			    		}
			    	}
			    	if($(this).attr('id') == 'qty')
			    	{
			    		if(parseInt($(this).val()))
			    		{
			    			qty = '<td>'+ parseInt($(this).val()) +'</td>';
			    		}
			    		else if($(this).val().trim() == '')
			    		{
			    			qty = '<td style="background-color: red">'+ $(this).val() +'</td>';
			    			err = true;
			    		}
			    		else
			    		{
			    			qty = '<td style="background-color: red">'+ $(this).val() +'</td>';
			    			err = true;
			    		}
			    	}
			    	if($(this).attr('id') == 'gross')
			    	{
			    		if($(this).val().trim() == '')
			    		{
			    			gross = '<td style="background-color: red">'+ $(this).val() +'</td>';
			    			err = true;
			    		}
			    		else
			    		{
			    			gross = '<td>'+ $(this).val() +'</td>';
			    		}
			    	}

			    })
			    newrow = newrow + '<tr>' + boxno + pono + desc + prodcode + price + qty + gross + '</tr>';
			});

			var view_trcontent = document.getElementById("view_tbl_body_row");
			view_trcontent.innerHTML = '';
			view_trcontent.innerHTML = newrow;

			if(err)
			{
		    	$.alert('Details contains invalid values. All fields are required.',
		    	{
					position: ['center', [-0.42, 0]],
					type: 'danger',
					closeTime: 3000,
					autoClose: true
				});
				$("#btn_save").prop("disabled", true);
				$("#btn_print").prop("disabled", true);
				$("#btn_printout").prop("disabled", true);
			}
			else
			{
				$("#btn_save").removeAttr('disabled');
				$("#btn_print").removeAttr('disabled');
				$("#btn_printout").removeAttr('disabled');
			}
		}

		function getOrders()
		{
			$('#loading').modal('show');
			var porder = $('#txt_porder').val();
			var dbcon = "{{ Auth::user()->productline }}";

			$.post("{{ url('/packinglistdetails-search') }}",
			{
				_token   : $('meta[name=csrf-token]').attr('content')
				, porder : porder
				, dbconnection : dbcon //$('#txt_db').val()
			})
			.done(function(datatable)
			{
			    console.log(datatable);
				$('#loading').modal('hide');
				var newcol = '';
				var newItem = '';

				$('#srch_tbl_body').html('');

				var ctr = 0;
				var arr = $.map(datatable, function(datarow)
				{
					newcol = '';

					console.log(datarow);

		    		$.each( datarow, function( ckey, value )
		    		{
		    			if(ckey == 'PORDER')
		    			{
		    			   newcol = newcol + '<td class="srch_item'+ ctr +'" name="srch_item'+ ctr +'">'+ value +'</td>';
		    			}	
		    			else if(ckey == 'PRICE')
		    			{
		    				// || ckey == 'NAME' || ckey == 'CODE' || ckey == 'KVOL' || ckey == 'PRICE'
		    				var val = parseFloat(value);
							newcol = newcol + '<td class="srch_item'+ ctr +'" name="srch_item'+ ctr +'">'+ val.toFixed(4) +'</td>';
		    			}
		    			else
		    			{
							newcol = newcol + '<td class="srch_item'+ ctr +'" name="srch_item'+ ctr +'">'+ value +'</td>';
		    			}
	    			});

	    			newItem = '<tr onclick="javascript: rowClick('+ ctr +');" style="cursor: pointer">'
							+ newcol
							+ '</tr>';
					$('#srch_tbl_body').append(newItem);
					ctr++;
				});
			})
			.fail(function()
			{
				$('#loading').modal('hide');
				alert('No available P.O.');
			});
		}

	    function addDetails()
	    {
	        if($('#dbconnection').val() == 0)
	       	{
				$.alert('Please select database connection.',
				{
					position: ['center', [-0.42, 0]],
					type: 'danger',
					closeTime: 3000,
					autoClose: true
				});
		    }
	        else
	       	{
				window.location.href= "{{ url('/packinglistdetails?dbconnection=') }}" + "{{ Auth::user()->productline }}"//$('#txt_db').val();
			}
	    }
	</script>
@endpush