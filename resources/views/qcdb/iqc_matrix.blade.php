<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: iqc_matrix.blade.php
     MODULE NAME:  [3037] IQC Matrix
     CREATED BY: AK.DELAROSA
     DATE CREATED: 2018.03.23
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2018.03.23     AK.DELAROSA      Initial Draft
*******************************************************************************/
?>

@extends('layouts.master')

@section('title')
	IQC Matrix | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_MATRIX'))
			@if ($access->read_write == "2")
				<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach


	<div class="page-content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                @include('includes.message-block')

                <div class="portlet box blue" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-navicon"></i>  IQC Matrix
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form class="form-horizontal" method="post" files="true" enctype="multipart/form-data" action="{{ url('/iqc-matrix-upload') }}" id="frm_upload_matrix">
                                    <div class="form-group">
                                        {{ csrf_field() }}
                                        <label class="control-label col-md-3">Not for IQC file</label>
                                        <div class="col-md-6">
                                            <input type="file" class="filestyle" data-buttonName="btn-primary" name="matrix_file" id="matrix_file" {{$readonly}}>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-md green" {{$state}}>
                                                <i class="fa fa-upload"></i> Upload File
                                            </button> <!-- type="submit" -->
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped" id="tbl_matrix">
                                    <thead>
                                        <tr>
                                            <td>
                                            	<input type="checkbox" name="check_all" class="check_all" id="check_all">
                                            </td>
                                            <td>Item Code</td>
                                            <td>Item Description</td>
                                            <td>Classification</td>
                                            <td>Updated By</td>
                                            <td>Last Update</td>
											<td></td>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_matrix_body"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                        	<div class="col-md-12 text-center">
                        		<button class="btn btn-sm green" id="btn_add">
                        			<i class="fa fa-plus"></i> Add
                        		</button>

                        		<button class="btn btn-sm red" id="btn_delete">
                        			<i class="fa fa-trash"></i> Delete
                        		</button>

                                <a href="{{url('/iqc-matrix-excel')}}" class="btn btn-sm grey-gallery">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </a>
                        	</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

	@include('includes.iqc_matrix_modal')
	@include('includes.modals')


@endsection

@push('script')
	<script>
		var token = '{{ Session::token() }}';
		var showMatrixURL = "{{ url('/iqc-matrix-show') }}";
		var DeleteMatrixURL = "{{ url('/iqc-matrix-delete') }}";
		var showClassificationURL = "{{ url('/iqc-matrix-classification') }}";
		var ItemDetailsURL = "{{ url('/iqc-matrix-details') }}";
	</script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/iqc_matrix.js') }}" type="text/javascript"></script>
@endpush
