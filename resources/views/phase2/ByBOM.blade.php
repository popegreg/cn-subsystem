@extends('layouts.master')

@section('title')
	YPICS Inventory Query | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_INVQUERY'))  <!-- Please update "2001" depending on the corresponding program_code -->
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

				<div class="row">
					<div class="col-md-12">
						<a href="{{ url('/inventoryquery') }}" class="btn btn-sm red pull-right">
							<i class="fa fa-mail-reply"></i> Back
						</a>
					</div>
				</div>
				<br>

				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cubes"></i>  TPICS STOCK QUERY BY BOM
						</div>
					</div>
					<div class="portlet-body">

						<!-- <div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="portlet box blue-hoki">
									<div class="portlet-body">
										<div class="row">
											<div class="col-md-12">
												<form method="POST" action="{{url('/bybomsearchitems')}}" class="form-horizontal" id="searchfrm" >
													{{ csrf_field() }}

													<div class="form-group">
														<label class="control-label col-md-2">PRODUCT NAME:</label>
														<div class="col-md-9">
															<select class="form-control select2me" id="prodname" name="prodname">
															</select>
														</div>

													</div>

												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div> -->

						<div class="row">
							<div class="col-md-12">
								<div class="portlet box blue-hoki">
									<div class="portlet-body">

										<div class="row">
											<div class="col-md-12">
												<table class="table table-striped table-bordered table-hover table-responsive" style="font-size:9px;" id="tblStockQuery">
													<thead>
														<tr>
															<td>PART CODE</td>
															<td>PART NAME</td>
															<td>USAGE</td>
															<td>PRICE</td>
															<td>VENDOR</td>
															<td>ASSY100</td>
															<td>WHS100</td>
															<td>WHS102</td>
															<td>WHS-NON</td>
															<td>WHS-SM</td>
															<td>StockTotal</td>
															<td>TotalRequired</td>
															<td>AvailableStock</td>
															<td>PR_Balance</td>
															<td>PRODUCT</td>
															<td>PRODUCT NAME</td>
														</tr>
													</thead>
													{{-- <tbody>
														@foreach($boms as $bom)
															<tr>
																<td>{{$bom->code}}</td>
																<td>{{$bom->name}}</td>
																<td>{{$bom->usage}}</td>
																<td>{{$bom->price}}</td>
																<td>{{$bom->vendor}}</td>
																<td>{{$bom->assy100}}</td>
																<td>{{$bom->whs100}}</td>
																<td>{{$bom->whs102}}</td>
																<td>{{$bom->whsnon}}</td>
																<td>{{$bom->whssm}}</td>
																<td>{{$bom->stocktotal}}</td>
																<td>{{$bom->requirement}}</td>
																<td>{{$bom->available}}</td>
																<td>{{$bom->prbalance}}</td>
																<td>{{$bom->prodcode}}</td>
																<td>{{$bom->prodname}}</td>
															</tr>
														@endforeach
													</tbody> --}}
												</table>
											</div>
										</div>

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
			
@endsection
@push('script')
	<script type="text/javascript">
		$(function() {
			$('#tblStockQuery').DataTable({
				processing: true,
				serverSide: true,
				ajax: "{{url('/bybomitems')}}",
				columns: [
					{ data: 'code', name: 'code' },
					{ data: 'name', name: 'name' },
					{ data: 'usage', name: 'usage' },
					{ data: 'price', name: 'price' },
					{ data: 'vendor', name: 'vendor' },
					{ data: 'assy100', name: 'assy100' },
					{ data: 'whs100', name: 'whs100' },
					{ data: 'whs102', name: 'whs102' },
					{ data: 'whsnon', name: 'whsnon' },
					{ data: 'whssm', name: 'whssm' },
					{ data: 'stocktotal', name: 'stocktotal' },
					{ data: 'requirement', name: 'requirement' },
					{ data: 'available', name: 'available' },
					{ data: 'prbalance', name: 'prbalance' },
					{ data: 'prodcode', name: 'prodcode' },
					{ data: 'prodname', name: 'prodname' }

				]
			});

		});
	</script>
@endpush
