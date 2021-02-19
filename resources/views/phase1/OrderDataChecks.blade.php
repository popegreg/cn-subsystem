@extends('layouts.master')

@section('title')
	Order Data Check | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_CHECK'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
							<i class="fa fa-clipboard"></i>  ORDER DATA CHECK
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">

							<div class="col-md-7">
								<div class="row">
									<div class="col-md-12">

										<div class="portlet box blue-hoki">

											<div class="portlet-body">
												<div class="row">
													<div class="col-md-12">
														<form method="POST" enctype="multipart/form-data" action="{{ url('/readfiles') }}" class="form-horizontal" id="readfileform" >
															{{ csrf_field() }}

															<div class="form-group">
																<label class="control-label col-md-4">MLP01UF</label>
																<div class="col-md-5">
																	<input type="file" class="filestyle" data-buttonName="btn-primary" name="mlp01uf" id="mlp01uf" {{$readonly}}>
																</div>
															</div>

															<div class="form-group">
																<label class="control-label col-md-4">MLP02UF</label>
																<div class="col-md-5">
																	<input type="file" class="filestyle" data-buttonName="btn-primary" name="mlp02uf" id="mlp02uf" {{$readonly}}>
																</div>
															</div>

															<div class="form-group">
																<label class="control-label col-md-4">Output Directory</label>
																<div class="col-md-5">
																	<input type="text" class="form-control" value="/public/Order_Data_Check/" disabled="disable">
																</div>
															</div>

															<div class="form-group">
																<div class="col-md-9">
																	<button type="submit" class="btn btn-md btn-warning pull-right" {{$state}}>
																		<i class="fa fa-refresh"></i> Process
																	</button>
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>

										<div class="portlet box blue">
											<div class="portlet-title">
												<div class="caption">
													DETAIL SUMMARY
												</div>
											</div>
											<div class="portlet-body">
												<div class="row">
													<div class="col-md-6">
														<table class="table table-hover table-bordered">
															<thead>
																<tr style="color: #d6f5f3;background-color: #0ba8e2;">
																	<td colspan="3">
																		RECEIVED DATA DETAILS
																	</td>
																</tr>
															</thead>
															<tbody>
																@if (Auth::user()->productline == 'TS')
																	<tr>
																		<td width="100px">TS</td>
																		<td>TS PO:</td>
																		@if (Session::has('PO'))
																			<td style="font-weight: 900">
																				@if(Session::has('con') && Session::get('con') == 'TS')
																					{{Session::get('PO')}}
																				@endif
																			</td>
																		@else
																			<td style="font-weight: 900">0</td>
																		@endif
																	</tr>
																@endif
																@if (Auth::user()->productline == 'CN')
																	<tr>
																		<td>CN</td>
																		<td>CN PO:</td>
																		@if (Session::has('PO'))
																			<td style="font-weight: 900">
																				@if(Session::has('con') && Session::get('con') == 'CN')
																					{{Session::get('PO')}}
																				@endif
																			</td>
																		@else
																			<td style="font-weight: 900">0</td>
																		@endif
																	</tr>
																@endif
																@if (Auth::user()->productline == 'YF')
																	<tr>
																		<td>YF</td>
																		<td>YF PO:</td>
																		@if (Session::has('PO'))
																			<td style="font-weight: 900">
																				@if(Session::has('con') && Session::get('con') == 'YF')
																					{{Session::get('PO')}}
																				@endif
																			</td>
																		@else
																			<td style="font-weight: 900">0</td>
																		@endif
																	</tr>
																@endif
																<tr>
																	<td colspan="2">TOTAL:</td>
																	@if (Session::has('PO'))
																		<td style="font-weight: 900">
																			{{Session::get('PO')}}
																		</td>
																	@elseif (Session::has('PO'))
																		<td style="font-weight: 900">
																			{{Session::get('PO')}}
																		</td>
																	@else
																		<td style="font-weight: 900">0</td>
																	@endif
																</tr>
															</tbody>
														</table>
													</div>
													<div class="col-md-6">
														<table class="table table-hover table-bordered">
															<thead>
																<tr style="color: #d6f5f3;background-color: #0ba8e2;">
																	<td colspan="2">
																		RECEIVED DATA DETAILS
																	</td>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td width="200px">RS PO</td>
																	<td style="font-weight: 900">0</td>
																</tr>
																<tr>
																	<td>NORMAL PO</td>
																	@if (Session::has('NormalPO'))
																		<?php $NormalPO = Session::get('NormalPO'); ?>
																		<td style="font-weight: 900">
																			{{ $NormalPO }}
																		</td>
																	@else
																		<td style="font-weight: 900">0</td>
																	@endif
																</tr>
																<tr>
																	<td>NEW PRODUCT</td>
																	@if (Session::has('Products'))
																		<?php $Products = Session::get('Products'); ?>
																		<td style="font-weight: 900">{{ $Products['nonexist'] }}</td>
																	@else
																		<td style="font-weight: 900">0</td>
																	@endif
																</tr>
																<tr>
																	<td>RS GENERATED</td>
																	<td style="font-weight: 900">0</td>
																</tr>
																<tr>
																	<td>FOR ORDER ENTRY</td>
																	@if (Session::has('PO') && Session::has('Products') && Session::has('Products'))
																		<?php
																			$prodExist = Session::get('Products');
																			$prodNotExist = Session::get('Products');
																			$orderts = $prodExist['exist'] + $prodNotExist['nonexist'];
																		?>
																		<td style="font-weight: 900">
																			{{$orderts}}
																		</td>
																	@else
																		<td style="font-weight: 900">0</td>
																	@endif
																</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>

										<div class="portlet box blue">
											<div class="portlet-title">
												<div class="caption">
													NUMBER OF DATA LOADING TO TPICS
												</div>
											</div>
											<div class="portlet-body">
												<div class="table-responsive">
													<table class="table table-hover table-bordered table-condensed">
														<thead>
															<tr style="color: #d6f5f3;background-color: #0ba8e2;">
																<td></td>
																<td>ITEM NAME MASTER</td>
																<td>ITEM MASTER</td>
																<td>UNIT PRICE MASTER</td>
																<td>PRICE MASTER</td>
																<td>BOM MASTER</td>
																<td>ORDER ENTRY</td>
															</tr>
														</thead>
														<tbody>
															<tr align="right">
																<td>PART</td>
															@if (Session::has('ItemNamePartCount') && Session::has('ItemPartCount') && Session::has('UnitCount') && Session::has('BOMCount') && Session::has('Order'))
																<?php
																	$BOMCount = Session::get('BOMCount');
																	$UnitCount = Session::get('UnitCount');
																	$ItemNamePartCount = Session::get('ItemNamePartCount');
																	$ItemPartCount = Session::get('ItemPartCount');
																?>
																<td style="font-weight: 900">{{ $ItemNamePartCount }}</td>
																<td style="font-weight: 900">{{ $ItemPartCount }}</td>
																<td style="font-weight: 900">{{ $UnitCount }}</td>
																<td style="font-weight: 900"></td>
																<td style="font-weight: 900">{{ $BOMCount }}</td>
																<td style="font-weight: 900"></td>
															@else
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
															@endif

															</tr>
															<tr align="right">
																<td>PROD</td>
															@if (Session::has('ItemNameProdCount') && Session::has('ItemProdCount') && Session::has('PriceCount') && Session::has('Order'))
																<?php
																	$PriceCount = Session::get('PriceCount');
																	$ItemNameProdCount = Session::get('ItemNameProdCount');
																	$ItemProdCount = Session::get('ItemProdCount');
																	$prod_order = Session::get('Order');
																	$orderts = $prod_order['exist'] + $prod_order['non_exist'];
																?>
																<td style="font-weight: 900">{{ $ItemNameProdCount }}</td>
																<td style="font-weight: 900">{{ $ItemProdCount }}</td>
																<td style="font-weight: 900"></td>
																<td style="font-weight: 900">{{ $PriceCount }}</td>
																<td style="font-weight: 900"></td>
																<td style="font-weight: 900">{{ $orderts }}</td>
															@else
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
															@endif

															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>

									</div>
								</div>

								<div class="row">
									<div class="col-md-12 text-center">
										@if (Session::has('PO'))
											<a href="{{url('/momscheck')}}" class="btn btn-sm blue">MOMS Check</a>
										@endif
									</div>
								</div>
								<!--<div class="row"></div>
								<div class="row"></div>-->
							</div>





							<div class="col-md-5">
								<div class="row">
									<div class="col-md-12">

										<div class="portlet box blue">
											<div class="portlet-body">
											<!-- MLP01UF -->
												<table class="table table-hover table-bordered">
													<thead>
														<tr style="color: #d6f5f3;background-color: #0ba8e2;">
															<td colspan="2">
																MLP01UF
															</td>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td width="100px">
																START:
															</td>
															<td style="font-weight: 900">
																@if (Session::has('partStartPO'))
																	{{Session::get('partStartPO')}}
																@endif
															</td>
														</tr>
														<tr>
															<td>
																END:
															</td>
															<td style="font-weight: 900">
																@if (Session::has('partEndPO'))
																	{{Session::get('partEndPO')}}
																@endif
															</td>
														</tr>
													</tbody>
												</table>
											<!-- MLP02UF -->
												<table class="table table-hover table-bordered">
													<thead>
														<tr style="color: #d6f5f3;background-color: #0ba8e2;">
															<td colspan="2">
																MLP02UF
															</td>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td width="100px">
																START:
															</td>
															<td style="font-weight: 900">
																@if (Session::has('prodStartPO'))
																	{{Session::get('prodStartPO')}}
																@endif
															</td>
														</tr>
														<tr>
															<td>
																END:
															</td>
															<td style="font-weight: 900">
																@if (Session::has('prodEndPO'))
																	{{Session::get('prodEndPO')}}
																@endif
															</td>
														</tr>
													</tbody>
												</table>
											<!-- MLP DATA COMPARISON -->
												<table class="table table-hover table-bordered">
													<thead>
														<tr style="color: #d6f5f3;background-color: #0ba8e2;">
															<td colspan="2">
																MLP DATA COMPARISON
															</td>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td width="100px">
																START:
															</td>
															<td style="font-weight: 900">
															<?php
																if (Session::has('partStartPO') && Session::has('prodStartPO')) {
																	if (Session::has('partStartPO') == Session::has('prodStartPO')) {
																		echo "OK";
																	} else {
																		echo "NG";
																	}
																} else {

																}
															?>
															</td>
														</tr>
														<tr>
															<td>
																END:
															</td>
															<td style="font-weight: 900">
															<?php
																if (Session::has('partEndPO') && Session::has('prodEndPO')) {
																	if (Session::has('partEndPO') && Session::has('prodEndPO')) {
																		echo "OK";
																	} else {
																		echo "NG";
																	}
																} else {

																}
															?>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>

										<div class="portlet box blue">
											<div class="portlet-title">
												<div class="caption">
													DATA UNMATCH YPICS vs R3
												</div>
											</div>
											<div class="portlet-body">
												<div id="msg" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
												<table class="table table-hover table-bordered">
													<thead>
														<tr style="color: #d6f5f3;background-color: #0ba8e2;">
															<td>NAME</td>
															<td>QUANTITY</td>
														</tr>
													</thead>
													<tbody>
														<tr>
															@if (Session::has('Item') && Session::has('Unit') && Session::has('Price') && Session::has('BOM'))
																<?php
																	$BOM = Session::get('BOM');
																	$Price = Session::get('Price');
																	$Item = Session::get('Item');
																	$Unit = Session::get('Unit');
																?>
															@endif
															@if (Session::has('Price') && $Price['unmatch'] > 0)
																<td>SALES PRICE</td>
																<td style="font-weight: 900">
																	<?php $sales = Session::get('uSalescount'); ?>
																	<a href="{{url('/umSalesexcel')}}" class="btn btn-sm blue">{{ $sales }}</a>
																</td>
															@else
																<td>SALES PRICE</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
														<tr>
															@if (Session::has('uUnitcount') && Session::get('uUnitcount') > 0)
																<td>UNIT PRICE</td>
																<td style="font-weight: 900">
																	<?php $unit = Session::get('uUnitcount');?>
																	<a href="{{url('/umUnitexcel')}}" class="btn btn-sm blue">{{ $unit }}</a>
																</td>
															@else
																<td>UNIT PRICE</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
														<tr>
															@if (Session::has('BOM') && Session::get('uBOMcount') > 0)
																<td>BOM</td>
																<td style="font-weight: 900">
																	<?php $bomcount = Session::get('uBOMcount');?>
																	<a href="{{url('/umBOMexcel')}}" class="btn btn-sm blue">{{ $bomcount }}</a>
																</td>
															@else
																<td>BOM</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
														<tr>
															@if (Session::has('BOM') && Session::get('uUsagecount') > 0)
																<td>USAGE</td>
																<td style="font-weight: 900">
																	<?php $usagecount = Session::get('uUsagecount');?>
																	<a href="{{url('/umUsageexcel')}}" class="btn btn-sm blue">{{ $usagecount }}</a>
																</td>
															@else
																<td>USAGE</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
														<tr>
															@if (Session::has('uSuppcount') && Session::get('uSuppcount') > 0)
																<td>SUPPLIER</td>
																<td style="font-weight: 900">
																	<?php $supplier = Session::get('uSuppcount'); ?>
																	<a href="{{url('/umSuppexcel')}}" class="btn btn-sm blue">{{ $supplier }}</a>
																</td>
															@else
																<td>SUPPLIER</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
														<tr>
															@if (Session::has('Item') && Session::get('uPartNamecount') > 0)
																<td>PART NAME</td>
																<td style="font-weight: 900">
																	<?php $partnamecount = Session::get('uPartNamecount'); ?>
																	<a href="{{url('/umPartNameexcel')}}" class="btn btn-sm blue">{{ $partnamecount }}</a>
																</td>
															@else
																<td>PART NAME</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
														<tr>
															@if (Session::has('uProdNamecount') && Session::get('uProdNamecount') > 0)
																<td>PRODUCT NAME</td>
																<td style="font-weight: 900">
																	<?php $prodName = Session::get('uProdNamecount'); ?>
																	<a href="{{url('/umProdNameexcel')}}" class="btn btn-sm blue">{{ $prodName }}</a>
																</td>
															@else
																<td>PRODUCT NAME</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
														<tr>
															@if (Session::has('uProdDNcount') && Session::get('uProdDNcount') > 0)
																<td>PRODUCT DN</td>
																<td style="font-weight: 900">
																	<?php $prodDN = Session::get('uProdDNcount'); ?>
																	<a href="{{url('/umProdDNexcel')}}" class="btn btn-sm blue">{{ $prodDN }}</a>
																</td>
															@else
																<td>PRODUCT DN</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
														<tr>
															@if (Session::has('uPartDNcount') && Session::get('uPartDNcount') > 0)
																<td>PARTS DN</td>
																<td style="font-weight: 900">
																	<?php $partDN = Session::get('uPartDNcount');?>
																	<a href="{{url('/umPartDNexcel')}}" class="btn btn-sm blue">{{ $partDN }}</a>
																</td>
															@else
																<td>PARTS DN</td>
																<td style="font-weight: 900">0</td>
															@endif
														</tr>
													</tbody>
												</table>
											</div>
										</div>

									</div>
								</div>
								<!--<div class="row"></div>-->
							</div>

						</div>

					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

	<div id="processdone" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-body">
					<center><h3>Process successful!</h3></center>
				</div>
				<div class="modal-footer">
					<form method="POST" action="{{ url('/order_data_generate_report') }}" class="form-horizontal" target="_blank" id="processform">
						{{ csrf_field() }}
						@if (Session::has('partStartPO'))
							<input type="hidden" name="ml01start" value="{{Session::get('partStartPO')}}">
						@endif
						@if (Session::has('partEndPO'))
							<input type="hidden" name="ml01end" value="{{Session::get('partEndPO')}}">
						@endif
						@if (Session::has('prodStartPO'))
							<input type="hidden" name="ml02start" value="{{Session::get('prodStartPO')}}">
						@endif
						@if (Session::has('prodEndPO'))
							<input type="hidden" name="ml02end" value="{{Session::get('prodEndPO')}}">
						@endif
						<?php
							if (Session::has('partStartPO') && Session::has('prodStartPO')) {
								if (Session::get('partStartPO') == Session::get('prodStartPO')) {
						?>
									<input type="hidden" name="matchstart" value="OK">
						<?php
								} else {
						?>
									<input type="hidden" name="matchstart" value="NG">
						<?php
								}
							}
							if (Session::has('partEndPO') && Session::has('prodEndPO')) {
								if (Session::get('partEndPO') == Session::get('prodEndPO')) {
						?>
									<input type="hidden" name="matchend" value="OK">
						<?php
								} else {
						?>
									<input type="hidden" name="matchend" value="NG">
						<?php
								}
							}
						?>

						@if (Session::has('ItemNamePartCount') && Session::has('ItemPartCount') && Session::has('UnitCount') && Session::has('BOMCount') && Session::has('Order') && Session::has('NormalPO') && Session::has('Products') && Session::has('ItemNameProdCount') && Session::has('ItemProdCount') && Session::has('PriceCount'))
							<input type="hidden" name="po" value="{{ Session::get('PO') }}">
							<input type="hidden" name="normalpo" value="{{ Session::get('NormalPO') }}">
							<?php
								$PriceCount = Session::get('PriceCount');
								$ItemNameProdCount = Session::get('ItemNameProdCount');
								$ItemProdCount = Session::get('ItemProdCount');
								$BOMCount = Session::get('BOMCount');
								$UnitCount = Session::get('UnitCount');
								$ItemNamePartCount = Session::get('ItemNamePartCount');
								$ItemPartCount = Session::get('ItemPartCount');

								$Order = Session::get('Order');
								$prod_order = Session::get('Order');
								$Products = Session::get('Products');
								$orderts = $prod_order['exist'] + $prod_order['non_exist'];
								$dataentryts = $Products['exist'] + $Products['nonexist'];
							?>
							<input type="hidden" name="dataentryts" value="{{$dataentryts}}">
							<input type="hidden" name="newprod" value="{{ $Products['nonexist'] }}">
							<input type="hidden" name="itemnameparts" value="{{ $ItemNamePartCount }}">
							<input type="hidden" name="itemmasterparts" value="{{ $ItemPartCount }}">
							<input type="hidden" name="unitprice" value="{{ $UnitCount }}">
							<input type="hidden" name="itemnameprod" value="{{ $ItemNameProdCount }}">
							<input type="hidden" name="itemmasterprod" value="{{ $ItemProdCount }}">
							<input type="hidden" name="price" value="{{ $PriceCount }}">
							<input type="hidden" name="bom" value="{{ $BOMCount }}">
							<input type="hidden" name="orderts" value="{{ $orderts }}">
						@endif

						{{-- @if (Session::has('CNPO'))
							<input type="hidden" name="cnpo" value="{{Session::get('CNPO')}}">
						@endif --}}

						@if (Session::has('MLP01name'))
							<input type="hidden" name="mlp01name" value="{{Session::get('MLP01name')}}">
						@endif

						@if (Session::has('MLP02name'))
							<input type="hidden" name="mlp02name" value="{{Session::get('MLP02name')}}">
						@endif

						<input type="hidden" name="poforrs" value="">
						<input type="hidden" name="rsgen" value="">
						<input type="hidden" name="ordercn" value="">

						<button type="submit" class="btn btn-success">OK</button>
						<button type="button" data-dismiss="modal" class="btn btn-danger">Cancel</button>
					</form>

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

	<!-- NEW PRODUCT -->
	<div id="newproduct" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-sm gray-gallery">
			<div class="modal-content ">
				<div class="modal-body">
					@if (Session::has('Products'))
						<?php $Products = Session::get('Products'); ?>
						<p>Today's new product is {{ $Products['nonexist'] }}.</p>
					@else
						<p>Today's new product is 0.</p>
					@endif
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
				</div>
			</div>
		</div>
	</div>

	@if (Session::has('PO'))
		<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
		<script type="text/javascript">
			$( document ).ready(function() {
				$('#processdone').modal('show');

			});
		</script>
	@endif
@endsection

@push('script')
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/orderdatacheck.js') }}" type="text/javascript"></script>
@endpush
