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
					<div class="col-md-12" >
						<a href="{{ url('/inventoryquery') }}" class="btn btn-sm red pull-right">
							<i class="fa fa-mail-reply"></i> Back
						</a>
					</div>
				</div>

				<br>

				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cubes"></i>  TPICS STOCK QUERY BY PARTS
						</div>
					</div>
					<div class="portlet-body">

						<!-- <div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="portlet box blue-hoki">
									<div class="portlet-body">
										<div class="row">
											<div class="col-md-12">
												<form method="POST" action="{{url('/bypartsearchitem')}}" class="form-horizontal" id="searchfrm" >
													{{ csrf_field() }}

													<div class="form-group">
														<label class="control-label col-md-2">PART NAME:</label>
														<div class="col-md-9">
															<select class="form-control select2me" id="partname" name="partname">

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
								<!-- <div class="scroller" style="height: 100px"> -->
									<table class="table table-striped table-bordered table-hover" style="font-size:9px;" id="tblStockQuery">
										<thead>
											<tr>
												{{-- <td>ID</td> --}}
												<td>PART CODE</td>
												<td>PART NAME</td>
												<td>PRICE</td>
												<td>VENDOR</td>
												<td>WHSSM</td>
												<td>WHSNON</td>
												<td>WHS102</td>
												<td>WHS100</td>
												<td>ASSY100</td>
												<td>ASSY102</td>
												<td>StockTotal</td>
												<td>AvailableStock</td>
												<td>TotalRequired</td>
											</tr>
										</thead>
										{{-- <tbody>
											@foreach($parts as $part)
											<tr>
												<td>{{$part->id}}</td>
												<td>{{$part->code}}</td>
												<td>{{$part->name}}</td>
												<td>{{$part->price}}</td>
												<td>{{$part->vendor}}</td>
												<td>{{$part->whssm}}</td>
												<td>{{$part->whsnon}}</td>
												<td>{{$part->whs102}}</td>
												<td>{{$part->whs100}}</td>
												<td>{{$part->assy100}}</td>
												<td>{{$part->assy102}}</td>
												<td>{{$part->stocktotal}}</td>
												<td>{{$part->available}}</td>
												<td>{{$part->requirement}}</td>
											</tr>
											@endforeach
										</tbody> --}}
									</table>
								<!-- </div> -->
							</div>
						</div>

						<br>

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
				ajax: "{{url('/bypartsearchitem')}}",
				columns: [
					{ data: 'code', name: 'code' },
					{ data: 'name', name: 'name' },
					{ data: 'price', name: 'price' },
					{ data: 'vendor', name: 'vendor' },
					{ data: 'whssm', name: 'whssm' },
					{ data: 'whsnon', name: 'whsnon' },
					{ data: 'whs102', name: 'whs102' },
					{ data: 'whs100', name: 'whs100' },
					{ data: 'assy100', name: 'assy100' },
					{ data: 'assy102', name: 'assy102' },
					{ data: 'stocktotal', name: 'stocktotal' },
					{ data: 'available', name: 'available' },
					{ data: 'requirement', name: 'requirement' },

				]
			});
		});
	</script>
@endpush