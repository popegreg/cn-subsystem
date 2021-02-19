<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PartsStatus.blade.php
     MODULE NAME:  [3008-2] Parts Status
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.05.24
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.24     MESPINOSA       Initial Draft
*******************************************************************************/
?>
@extends('layouts.master')

@section('title')
	Parts Status | Pricon Microelectronics, Inc.
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
				@include('includes.message-block')
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i> Scheduling Support System (Parts Status)
						</div>
					</div>
					<div class="portlet-body">
						<form  class="form-horizontal" role="form" method="POST" action="{{ url('/postpartstatus') }}">
							<div class="row">
								<div class="col-md-6">
									<h3>PARTS REQUIREMENTS INFORMATION</h3>
								</div>
								<div class="col-md-6">
									{!! csrf_field() !!}
									<div class="portlet box blue">
										<div class="portlet-title" style="min-height: 20px;">
											<div class="caption" style="padding-top: 5px; padding-bottom: 5px;">
												SEARCH
											</div>
										</div>
										<div class="portlet-body">
											<div class="row">
												<div class="col-md-12" >
													<div class="form-group">
														<div class="col-md-1">
															<label for="input_po_name" class="control-label">Name</label>
														</div>
														<div class="col-md-10">
															<input type="text" class="form-control" id="inputPoName" placeholder="Name" name="name" value="<?php if(isset($name)){ if($name == 'X') { echo '';} else{ echo $name; }} ?>" >
														</div>
														<div class="col-md-1">
															<button type="submit" class="btn btn-info pull-right"><i class="fa fa-search"></i> SEARCH </button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<br/>
						
							
						<div class="row">
							<div class="col-md-12">
								<label class="col-sm-1">
									<button class="btn btn-sm grey-gallery" disabled>PART CODE:</button>
								</label>
								<div class="col-sm-11" style="font-size: 20px"><strong>
									@foreach($parts as $part)
										{{ $part->CODE }}
										<?php break;?>
									@endforeach
								</strong></div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<label class="col-sm-1">
									<button class="btn btn-sm grey-gallery" disabled>PART NAME:</button>
								</label>
								<div class="col-sm-11" style="font-size: 20px"><strong>
									<?php if(isset($part)){echo $part->NAME; } ?>
								</strong></div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="portlet box blue">
									<div class="portlet-body portlet-empty">
										<div class="col-md-12">
											<h4><strong>SUPPLIER</strong></h4>
										</div>
										<div class="col-md-1">
										</div>
										<div class="col-md-11">
											<h4>STATUS:</h4>
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
										<div class="table-responsive scroller" style="overflow: scroll; overflow-x: hidden; height: 350px">
										<table class="table table-striped table-bordered table-hover" style="font-size: 12px">
											<thead>
												<tr>
													<th>
														PR_Issued
													</th>
													<th>
														PR
													</th>
													<th>
														YEC_PU
													</th>
													<th>
														FI
													</th>
													<th>
														DeliQty
													</th>
													<th>
														DeliAccum
													</th>															
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
								<div class="table-responsive scroller" style="overflow: scroll; overflow-x: hidden; height: 320px">
									<table class="table table-striped table-bordered table-hover" ><!--id="sample_3"-->
										<thead>
											<tr>
												<th>
													PO DATE
												</th>
												<th>
													PO
												</th>
												<th>
													CODE
												</th>
												<th>
													NAME
												</th>
												<th>
													PO BAL
												</th>
												<th>
													PO QTY
												</th>
												<th>
													DUE DATE
												</th>
												<th>
													PO REQ
												</th>
												<th>
													BAL REQ
												</th>
												<th>
													ALLOC
												</th>
												<th>
													ALLOCATIONCAL
												</th>
												<th>
													CUSTOMER NAME
												</th>
											</tr>
										</thead>

										<tbody id="table" >
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
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</form>


					<form  class="form-horizontal" role="form" method="POST" action="{{ url('/print-partstatus') }}">
					{!! csrf_field() !!}
						 <input type="hidden" class="form-control" name="name" value="<?php if(isset($name)){ if($name == 'X') { echo '';} else{ echo $name; }} ?>" >
						<div class="row">
							<div class="col-md-12">
								<button type"button" class="btn btn-success pull-right" <?php echo($state); ?> ><i class="fa fa-print" ></i> PRINT</button>
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