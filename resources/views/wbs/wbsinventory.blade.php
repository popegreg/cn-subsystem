<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: wbsinventory.blade.php
     MODULE NAME:  [3039] WBS Inventory
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2018.05.29
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2018.05.29     AK.DELAROSA      Initial Draft
*******************************************************************************/
?>

@extends('layouts.master')

@section('title')
	WBS Inventory | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_WBS_INV'))
			@if ($access->read_write == "2")
				<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach


	<div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                @include('includes.message-block')

                <div class="portlet box blue" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-navicon"></i>  WBS Inventory
                        </div>
                    </div>
                    <div class="portlet-body">

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped" id="tbl_inventory">
                                    <thead>
                                        <tr>
                                            <td>
                                            	<input type="checkbox" name="check_all" class="check_all" id="check_all">
                                            </td>
                                            <td>Receiving Control #</td>
                                            <td>Invoice No.</td>
                                            <td>Item Code</td>
                                            <td>Description</td>
                                            <td>Qty.</td>
                                            <td>Lot No.</td>
                                            <td>Location</td>
                                            <td>Supplier</td>
                                            <td>IQC Status</td>
                                            <td>Received By</td>
                                            <td>Received Date</td>
                                            <td>Updated By</td>
                                            <td>Last Update</td>
											<td></td>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_inventory_body"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                        	<div class="col-md-12 text-center">
                        		<button class="btn btn-sm blue" id="btn_open_search_modal">
                        			<i class="fa fa-search"></i> Search
                        		</button>

                        		<button class="btn btn-sm red" id="btn_delete" {{ $state }}>
                        			<i class="fa fa-trash"></i> Delete
                        		</button>

                                <a href="{{url('/wbs-inventory-excel')}}" class="btn btn-sm grey-gallery">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </a>
                        	</div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

	@include('includes.wbsinventory_modal')
	@include('includes.modals')


@endsection

@push('script')
	<script>
		var token = '{{ Session::token() }}';
        var inventoryListURL = "{{ url('/wbs-inventory-list') }}";
        var deleteselected = "{{ url('/wbs-inventory-delete') }}";
        var access_state = "{{ $pgaccess }}";
        var pcode = "{{ $pgcode }}";
	</script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/wbsinventory.js') }}" type="text/javascript"></script>
@endpush
