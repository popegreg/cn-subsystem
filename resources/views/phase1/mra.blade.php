@extends('layouts.master')

@section('title')
	MRA | Pricon Microelectronics, Inc.
@endsection

@section('content')
	
	<?php ini_set('max_input_vars', 999999);?>
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_MRA'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
							<i class="fa fa-bar-chart-o"></i> MATERIAL REQUIREMENTS ANALYSIS (MRA)
						</div>
					</div>
					<div class="portlet-body portlet-empty">
						<dv class="row">
							<div class="col-md-12">
								<a href="javascript:;" class="btn green" id="btn_generate">Generate Material Requirements</a>
								<span class="pull-right" style="color:#cb5a5e">Note: Data will load at least 3-10 minutes.</span >
							</div>
						</dv>
						<br>
						<div class="row">
							<div class="col-md-12">
								<div class="scroller" data-rail-visible="1" style="height: 500px">
									<table class="table table-striped table-bordered table-hover order-column tbl_scroll" style="font-size: 11px"><!-- id="sample_3"  -->
										<thead>
											<tr>
												<td>
													ITEM CODE
												</td>
												<td width="20%">
													ITEM NAME
												</td>
												<td>
													BUNR
												</td>
												<td>
													TOTAL REQUIRED
												</td>
												<td>
													TOTAL COMPLETED
												</td>
												<td>
													REQ TO COMPLETE
												</td>
												<td>
													WHS100
												</td>
												<td>
													WHSNON
												</td>
												<td>
													ASSY100
												</td>
												<td>
													ASSY102
												</td>
												<td>
													WHSSM
												</td>
												<td>
													TOTAL ON HAND
												</td>
												<td>
													ORDER BALANCE
												</td>
												<td>
													FOR ORDERING
												</td>
												<td>
													MAINBUMO
												</td>
											</tr>
										</thead>
										<tbody id="tblMra">
											
										</tbody>
									</table>
									
									{{-- <div class="row" id="loading" style="display: none">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<img src="{{ seet(Config::get('constants.PUBLIC_PATH').'assets/global/img/loading-spinner-blue.gif') }}" class="img-responsive">
										</div>
									</div> --}}

								</div>
								<span id="count"></span>
							</div>
						</div>
						
						<br/>
						<div class="row">
							<div class="col-md-12">
								<a href="{{ url('/mraPrint') }}" id="btn_excel" class="btn green input-sm pull-right">
									<i class="fa fa-file-excel-o"></i> Export To Excel
								</a>
							</div>
						</div>


					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>


	<!--msg-->
    <div id="msg" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 id="title" class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p id="err_msg"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
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


@endsection

@push('script')
	{{-- <script type="text/javascript" src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/admin/pages/scripts/table-datatables-scroller.js')}}"></script> --}}
	<script>
		var token = '{{ Session::token() }}';
		var urlgeneratemra = '{{ url('/generatemra') }}';
		var urlmraload = "{{url('/mraload')}}";

		
	</script>
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/mra.js') }}" type="text/javascript"></script>
@endpush

