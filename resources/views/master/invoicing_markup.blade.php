<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: invoicing_markup.blade.php
     MODULE NAME:  [2007] Invoicing Mark Up Master
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2018.03.23
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2018.03.23     AK.DELAROSA      Initial Draft
*******************************************************************************/
?>

@extends('layouts.master')



@section('title')
	Invoicing Mark Up | Pricon Microelectronics, Inc.
@endsection

@section('content')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_SUPPLIER'))
			@if ($access->read_write == "2")
				<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach

	<div class="page-content">
		
		<div class="row">
			<div class="col-md-offset-3 col-md-6">
				@include('includes.message-block')

					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-log-in"></i> Invoicing Mark Up
							</div>
							<div class="tools">
								<button class="btn btn-sm btn-success" id="btn_add_mark_up">
									<i class="fa fa-plus"></i> Add
								</button>
							</div>
						</div>
						<div class="portlet-body">
							<table class="table table-striped table-bordered table-hover" style="font-size: 10px" id="tbl_mark_up">
								<thead>
									<tr>
										<td>Product Line</td>
										<td>Mark Up %</td>
										<td>Last Update By</td>
										<td>Last Update</td>
										<td>Action</td>
									</tr>
								</thead>
								<tbody id="tbl_mark_up_body"></tbody>
							</table>
						</div>
					</div>

			</div>
		</div>

	</div>

	@include('includes.modals')
	@include('includes.invoicing_markup_modal')
	
@endsection

@push('script')
	<script>
		var token = '{{ Session::token() }}';
		var showMarkUpURL = "{{ url('/invoicing-markup-show') }}";
		var DeleteMarkUpURL = "{{ url('/invoicing-markup-delete') }}";
	</script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/invoicing_markup.js') }}" type="text/javascript"></script>
@endpush