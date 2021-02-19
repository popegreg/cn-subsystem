<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: OrderDataReport.blade.php
     MODULE NAME:  [3002] Order Data Report
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.18
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.18     MESPINOSA       Initial Draft
     100-00-02   2     2016.10.27     AKDELAROSA      FIX BUGS
*******************************************************************************/
?>

@extends('layouts.master')

@section('title')
	Order Data Report | Pricon Microelectronics, Inc.
@endsection

@push('css')
	<style type="text/css">
        table.table-fixedheader {
            width: 100%;
        }
        table.table-fixedheader, table.table-fixedheader>thead, table.table-fixedheader>tbody, table.table-fixedheader>thead>tr, table.table-fixedheader>tbody>tr, table.table-fixedheader>thead>tr>td, table.table-fixedheader>tbody>td {
            display: block;
        }
        table.table-fixedheader>thead>tr:after, table.table-fixedheader>tbody>tr:after {
            content:' ';
            display: block;
            visibility: hidden;
            clear: both;
        }
        table.table-fixedheader>tbody {
            overflow-y: scroll;
            height: 200px;
        }
        table.table-fixedheader>thead {
            overflow-y: scroll;
        }
        table.table-fixedheader>thead::-webkit-scrollbar {
            background-color: inherit;
        }

        table.table-fixedheader>thead>tr>td:after, table.table-fixedheader>tbody>tr>td:after {
            content:' ';
            display: table-cell;
            visibility: hidden;
            clear: both;
        }

        table.table-fixedheader>thead tr td, table.table-fixedheader>tbody tr td {
            float: left;
            word-wrap:break-word;
            height: 40px;
        }
    </style>
@endpush

@section('content')

	<?php $state = ""; $enabled=""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_YPICS'))
			@if ($access->read_write == "2")
				<?php $state = "disabled"; $enabled="enabled" ?>
			@endif
		@endif
	@endforeach

	<div class="page-content">
		<div class="row">
			<div class="col-md-12">
				@include('includes.message-block')
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-area-chart"></i>  YPICS R3 Order Data
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-md-offset-3 col-md-6">
								<form class="form-horizontal" role="form" method="POST" id="ypicsr3Form" action="{{ url('/ypicsr3/connect-orderdatareport') }}" >
									{!! csrf_field() !!}
									<!-- <div class="row">
										<div class="col-md-12">
											<button type="button" onclick="javascript: actionStartStop('START'); " class="btn btn-success btn-sm" <?php echo($state); ?> >
												<i class="fa fa-play"></i> START USING YPICS
											</button>
											<button type="button" onclick="javascript: actionStartStop('STOP'); " class="btn btn-danger btn-sm" <?php echo($state); ?> >
												<i class="fa fa-stop"></i> STOP USING YPICS
											</button>
											<button type="button" id="btn_ypicsuser" class="btn grey-gallery pull-right btn-sm">
												<i class="fa fa-users"></i> YPICS USER
											</button>
										</div>
									</div>
									<hr/> -->

									<?php
										if (Auth::user()->productline){
											$dbconnection = Auth::user()->productline;
										}
									?>
									<input type="hidden" id="ddproductline" name="productline" class="form-control input-sm" value="{{$dbconnection}}">

									<?php
										if (Session::has('selected_supplier')){
											$selected_supplier = Session::get('selected_supplier');
										}
									?>

									<div class="input-group input-group-sm">
										<div class="input-group-addon">
											<span class="input-group-text">Supplier</span>
										</div>
										<select id="ddsupplier" name="supplier" class="form-control input-sm">
											<option selected="selected">-- Select --</option>
												@foreach($suppliers as $supplier)
													<option value="{{$supplier->name}}"
													<?php if($selected_supplier == $supplier->name)
													{
														echo 'selected';
													}
													?>
													>{{ $supplier->name }}</option>
												@endforeach
													{{-- $supplier->code for value --}}
										</select>
										<span class="input-group-btn">
											<button type="submit" class="btn blue" >
												<i class="fa fa-refresh"></i> Generate Order Data Report
											</button>
										</span>
									</div>
								</form>
							</div>
						</div>

						<hr/>

						<div class="row">
							<div class="col-md-12">
								<table class="table table-striped table-bordered table-hover" id="tbl_ypicsr3" style="font-size:10px;">
									<thead>
										<tr style="color: #d6f5f3;background-color: #0ba8e2;">
											<td colspan="19">Summary List</td>
										</tr>
										<tr>
											<td style="width:2.2%">SALES NO</td>
											<td style="width:5.2%">SALES TYPE</td>
											<td style="width:5.2%">SALES ORG</td>
											<td style="width:5.2%">COMMERCIAL</td>
											<td style="width:5.2%">SECTION</td>
											<td style="width:5.2%">SALES BRAND</td>
											<td style="width:5.2%">SALESG</td>
											<td style="width:5.2%">SUPPLIER</td>
											<td style="width:5.2%">DESTINATION</td>
											<td style="width:5.2%">PAYER</td>
											<td style="width:5.2%">ASSISTANT</td>
											<td style="width:7.2%">PURCHASE ORDER NO</td>
											<td style="width:5.2%">ISSUEDATE</td>
											<td style="width:5.2%">FLIGHTDATE</td>
											<td style="width:3.2%">HEADERTEXT</td>
											<td style="width:5.2%">CODE</td>
											<td style="width:8.2%">ITEMTEXT</td>
											<td style="width:5.2%">ORDERQUANTITY</td>
											<td style="width:5.2%">UNIT</td>
										</tr>
									</thead>

									<tbody></tbody>
								</table>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>


	@include('includes.ypicsr3-modal')
	@include('includes.modals')

	@if (Session::has('msg') && Session::get('msg') == 0)
		<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
		<script type="text/javascript">
			$( document ).ready(function() {
				$('#loading').modal('hide');
				$('#msgbox').modal('show');
			});
		</script>
	@endif

	@if (Session::has('msg') && Session::get('msg') > 0)
		<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
		<script type="text/javascript">
			$( document ).ready(function() {
				$('#loading').modal('hide');
				$('#success').modal('show');
			});
		</script>
	@endif

@endsection

@push('script')
	<script type="text/javascript">
		var YpicsUserURL = "{{ url('/ypicsr3/ypics-user-data') }}";
		var StartStopURL = "{{ url('/ypicsr3/mrpusers-orderdatareport') }}";
		var YpicsR3DataURL = "{{ url('/ypicsr3/ypicsr3datatable') }}";
	</script>
	<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
	<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/ypicsr3.js') }}" type="text/javascript"></script>
@endpush
