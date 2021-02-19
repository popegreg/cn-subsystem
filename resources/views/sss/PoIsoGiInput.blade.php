<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: PoIsoGiInput.blade.php
     MODULE NAME:  [3008-1] PO Status - ISO GI Input
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.05.03
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.05.03     MESPINOSA       Initial Draft
*******************************************************************************/
?>
@extends('layouts.master')

@section('title')
	PO Status(ISOGI Input) | Pricon Microelectronics, Inc.
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
							<i class="fa fa-pencil"></i> ISOGI INPUT
						</div>
					</div>
					<div class="portlet-body portlet-empty">
						<form class="form-horizontal" role="form" method="POST" action="{{ url('/post-poisogiinput') }}">
						{!! csrf_field() !!}
						<div class="row">
							<div class="col-md-12">
								<a href="{{ url('/postatus?po=') }} <?php if(isset($po)){ echo $po; } ?>" class="btn btn-sm yellow btn-xs pull-right"><i class="fa fa-mail-reply"></i> Back</a>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<label class="col-sm-2">
									<button class="btn btn-xs grey-gallery" disabled>PART NAME:</button>
								</label>
								<span class="col-sm-10" style="font-size: 16px" >  <strong><?php if(isset($code)){ echo $code; } ?> </strong></span>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="scroller" data-rail-visible="1" style="height: 500px">
									<table class="table table-striped table-bordered table-hover" id="sample_2">
										<thead>
											<tr>
												<!-- <th>
													PO DATE
												</th> -->
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
											@foreach($t1 as $t1_value)
											<tr>
												<!-- <td>{{ $t1_value->PODATE }}</td> -->
												<td>{{ $t1_value->PO}}</td>
												<td>{{ $t1_value->CODE }}</td>
												<td>{{ $t1_value->NAME }}</td>
												<td style="text-align: right">{{ $t1_value->POBAL }}</td>
												<td style="text-align: right">{{ $t1_value->POQTY }}</td>
												<td>{{ $t1_value->DUEDATE }}</td>
												<td style="text-align: right">{{ $t1_value->POREQ }}</td>
												<td style="text-align: right">{{ $t1_value->BALREQ }}</td>
												<td style="text-align: right">{{ $t1_value->ALLOC }}</td>
												<td style="text-align: right">{{ $t1_value->ALLOCAL }}</td>
												<td>{{ $t1_value->CUSTOMER }}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<br/>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="col-md-1">
										<h5><span class="label label-info">PICK UP DATE</span></h5>
									</div>
									<div class="col-md-4">
										<div class="input-group input-large date-picker input-daterange " data-date="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy">
											<input type="text" class="form-control" name="from" value="<?php if(isset($datefrom)){ echo $datefrom;} ?>">
												<span class="input-group-addon"> to </span>
											<input type="text" class="form-control" name="to" value="<?php if(isset($dateto)){ echo $dateto;} ?>">
										</div>
									</div>
									<div class="col-md-7">
										<button type="submit" class="btn btn-info btn-sm" id="btn_search"><i class="fa fa-search"></i> Search</button>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="scroller" data-rail-visible="1" style="height: 500px">
									<table class="table table-striped table-bordered table-hover" id="sample_3">
										<thead>
											<tr>
												<td>
													<b>PARTS PO</b>
												</td>
												<td>
													<b>CODE</b>
												</td>
												<td>
													<b>NAME</b>
												</td>
												<td>
													<b>PU QTY</b>
												</td>
												<td>
													<b>SUP CODE</b>
												</td>
												<td>
													<b>SUP NAME</b>
												</td>
												<td>
													<b>PICK UP DATE</b>
												</td>
												<td>
													<b>REMARKS</b>
												</td>
												<td>
													<b>PO</b>
												</td>
												<td>
													<b>PRODUCT NAME</b>
												</td>
												<td>
													<b>PR</b>
												</td>
											</tr>
										</thead>

										<tbody id="table" >
											@foreach($t2 as $t2_value)
											<tr>
												<td>{{ $t2_value->PO }}</td>
												<td>{{ $t2_value->CODE }}</td>
												<td>{{ $t2_value->NAME }}</td>
												<td style="text-align: right;">{{ $t2_value->PUQTY }}</td>
												<td style="text-align: center;">{{ $t2_value->SUPCODE }}</td>
												<td>{{ $t2_value->SUPNAME }}</td>
												<td style="text-align: center;">{{ $t2_value->PICKUPDATE }}</td>
												<td>{{ $t2_value->REMARKS }}</td>
												<td>{{ $t2_value->ISO_PO }}</td>
												<td>{{ $t2_value->PRODNAME }}</td>
												<td>{{ $t2_value->PR }}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
							<input class="form-control form-control-inline input-medium col-sm-3" type="hidden" name="po" value="<?php if(isset($po)){ echo $po; } ?>"/>
							<input class="form-control form-control-inline input-medium col-sm-3" type="hidden" name="code" value="<?php if(isset($code)){ echo $code; } ?>"/>
						</form>


						<form class="form-horizontal" role="form" method="POST" action="{{ url('/print-poisogiinput') }}">
						{!! csrf_field() !!}
							<input class="form-control form-control-inline input-medium col-sm-3" type="hidden" name="po" value="<?php if(isset($po)){ echo $po; } ?>"/>
							<input class="form-control form-control-inline input-medium col-sm-3" type="hidden" name="code" value="<?php if(isset($code)){ echo $code; } ?>"/>
							<div class="row">
								<div class="col-md-12">
									<button type"button" class="btn btn-success pull-right btn-sm" <?php echo($state); ?> ><i class="fa fa-print" ></i> PRINT</button>
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

