<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PoPartsStatus.blade.php
     MODULE NAME:  [3008-1] PO Status : Parts Status
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.05.03
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.03     MESPINOSA       Initial Draft
*******************************************************************************/
?>
@extends('layouts.master')

@section('title')
	PO Status (Parts Status) | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_SSS'))
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
							<i class="fa fa-bar-chart-o"></i> PARTS REQUIREMENTS INFORMATION
						</div>
					</div>
					<div class="portlet-body portlet-empty">
						<form  class="form-horizontal" role="form" method="POST" action="{{ url('/print-popartstatus') }}">
						{!! csrf_field() !!}
						<div class="row">
							<div class="col-md-12">
								<a href="{{ url('/postatus?po=') }} <?php if(isset($po)) { if($po == 'X') { echo '';} else {echo $po;}} ?>" class="btn btn-sm yellow btn-xs pull-right"><i class="fa fa-mail-reply"></i> Back</a>
								<h4>PARTS REQUIREMENTS INFORMATION</h4>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label class="col-sm-3">
									<button class="btn btn-sm grey-gallery btn-xs" disabled>PART CODE:</button>
								</label>
								<div class="col-sm-9" style="font-size: 20px"><strong>
									@foreach($parts as $part)
										{{ $part->CODE }}
										<?php break;?>
									@endforeach
								</strong></div>
							</div>
							<div class="col-md-6">
								<label class="col-sm-3">
									<button class="btn btn-sm grey-gallery btn-xs" disabled>PART NAME:</button>
								</label>
								<div class="col-sm-9" style="font-size: 20px"><strong>
									<?php if(isset($part)){echo $part->NAME; } ?>
								</strong></div>
							</div>
						</div>
						<input type="hidden" class="form-control" name="has_part" value="<?php if(isset($has_part)) {echo $has_part;} ?>" >
						<input type="hidden" class="form-control" name="code" value="<?php if(isset($part)){echo $part->CODE; } ?>" >
						<div class="row">
							<div class="col-md-6">
								<div class="portlet box blue">
									<div class="portlet-body portlet-empty">
										<div class="col-md-12">
											<h4><strong>SUPPLIER</strong></h4>
										</div>
										<div class="row">
											<div class="col-md-1">
											</div>
											<div class="col-md-11">
												<h5>STATUS:</h5>
											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-5">
												ASSY100
											</div>
											<div class="col-md-1">
												<div class="pull-right"><?php if(isset($part)){echo $part->ASSY100; } ?></div>
											</div>
											<div class="col-md-3">
											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-5">
												WHS2100
											</div>
											<div class="col-md-1">
												<div class="pull-right"><?php if(isset($part)){echo $part->WHS100; } ?></div>
											</div>
											<div class="col-md-3">
											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-5">
												WHS2102
											</div>
											<div class="col-md-1">
												<div class="pull-right"><?php if(isset($part)){echo $part->WHS102; } ?></div>
											</div>
											<div class="col-md-3">
											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-5">
												<strong>TOTAL STOCK</strong>
											</div>
											<div class="col-md-1">
												<div class="pull-right"><?php if(isset($part)){echo $part->TOTAL; } ?></div>
											</div>
											<div class="col-md-3">
											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-5">
												GROSS REQUIREMENTS
											</div>
											<div class="col-md-1">
												<div class="pull-right"><?php if(isset($part)){echo $part->GROSS_REQ; } ?></div>
											</div>
											<div class="col-md-3">
											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-5">
												<strong>EXCESS/SHORTAGE</strong>
											</div>
											<div class="col-md-1">
												<div class="pull-right"><?php if(isset($part)){echo $part->EXCESS; } ?></div>
											</div>
											<div class="col-md-3">
											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-5">
												<div style="font-size: 10px">
													{PR BLANCE IS NOT INCLUDED}
												</div>
												PR BALANCE
											</div>
											<div class="col-md-1">
												<div class="pull-right"><?php if(isset($part)){echo $part->PR_BAL; } ?></div>
											</div>
											<div class="col-md-3">
											</div>
										</div>
										<br/>
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-5">
												<strong>AVAILABLE STOCK</strong>
											</div>
											<div class="col-md-1">
												<div class="pull-right"><?php if(isset($part)){echo $part->STOCK; } ?></div>
											</div>
											<div class="col-md-3">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="portlet box blue">
									<div class="portlet-body portlet-empty" >
										<div class="table-responsive scroller" style="overflow: scroll; height: 368px">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<td>
															<b>PR_Issued</b>
														</td>
														<td>
															<b>PR</b>
														</td>
														<td>
															<b>YEC_PU</b>
														</td>
														<td>
															<b>FI</b>
														</td>
														<td>
															<b>DeliQty</b>
														</td>
														<td>
															<b>DeliAccum</b>
														</td>															
													</tr>
												</thead>

												<tbody id="table" >
													@foreach($t1 as $t1detail)
													<tr>
														<td>{{ $t1detail->PR_ISSUED }}</td>
														<td>{{ $t1detail->PR }}</td>
														<td>{{ $t1detail->YEC_PU }}</td>
														<td>{{ $t1detail->FI }}</td>
														<td style="text-align: right">{{ $t1detail->DELIQTY }}</td>
														<td style="text-align: right">{{ $t1detail->DELIACCUM }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>

						</div>
						
						<br/>

						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive scroller" style="overflow: scroll; height: 280px">
									<table class="table table-striped table-bordered table-hover" ><!--id="sample_3"-->
										<thead>
											<tr>
												<td>
													<b>PO DATE</b>
												</td>
												<td>
													<b>PO</b>
												</td>
												<td>
													<b>CODE</b>
												</td>
												<td>
													<b>NAME</b>
												</td>
												<td>
													<b>PO BAL</b>
												</td>
												<td>
													<b>PO QTY</b>
												</td>
												<td>
													<b>DUE DATE</b>
												</td>
												<td>
													<b>PO REQ</b>
												</td>
												<td>
													<b>BAL REQ</b>
												</td>
												<td>
													<b>ALLOC</b>
												</td>
												<td>
													<b>ALLOCATIONCAL</b>
												</td>
												<td>
													<b>CUSTOMER NAME</b>
												</td>
											</tr>
										</thead>

										<tbody id="table" >
											<?php $cnt = 0; ?>
											@foreach($t2 as $t2detail)
											<tr>
												<td>{{ $t2detail->PODATE }}</td>
												<td>{{ $t2detail->PO }}</td>
												<td>{{ $t2detail->CODE }}</td>
												<td>{{ $t2detail->NAME }}</td>
												<td style="text-align: right">{{ $t2detail->POBAL }}</td>
												<td style="text-align: right">{{ $t2detail->POQTY }}</td>
												<td>{{ $t2detail->DUEDATE }}</td>
												<td style="text-align: right">{{ $t2detail->POREQ }}</td>
												<td style="text-align: right">{{ $t2detail->BALREQ }}</td>
												<td style="text-align: right">{{ $t2detail->ALLOC }}</td>
												<td style="text-align: right">{{ $t2detail->ALLOCAL }}</td>
												<td>{{ $t2detail->CUSTOMERNAME }}</td>
											</tr>
												<?php $cnt++; ?>
											@endforeach
										</tbody>
									</table>
								</div>
								<p>Count: <?php echo $cnt;?></p>
							</div>
						</div>


						<div class="row">
							<div class="col-md-12">
								<button type"button" class="btn btn-success btn-sm pull-right" <?php echo($state); ?> ><i class="fa fa-print" ></i> PRINT</button>
							</div>
						</div>
					</form>

					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
	@endsection