@extends('layouts.master')

@section('title')
	WBS | Pricon Microelectronics, Inc.
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
        table.table-fifo>tbody {
            overflow-y: scroll;
            height: 375px;
        }
        .dataTables_scrollHeadInner {
          padding-left: 0px;
        }
    </style>
@endpush

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_WBS'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
				<div class="portlet box blue" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-navicon"></i>  WBS Warehouse Material Issuance
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">

							<?php
								$act = 0;
								if(isset($active_tab)){

									if($active_tab == '1'){ $act = 1;}
									if($active_tab == '0'){ $act = 0;}
								}
							?>
							<input type="hidden" name="name" value="{{$act}}" id="active">

							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="tabbable-custom">
                                            <ul class="nav nav-tabs nav-tabs-lg" id="tabslist" role="tablist">
                                                <li class="<?php if(isset($active_tab)){ if($active_tab == 0){ echo 'active';} }?>" id="summarytab">
                                                    <a href="#requestsummary" data-toggle="tab" aria-expanded="true" id="summarytabtoggle">Request Summary</a>
                                                </li>
                                                <li class="<?php if(isset($active_tab)){ if($active_tab == 1){ echo 'active';} }?>" id="issuancetab">
                                                    <a href="#issuance" data-toggle="tab" aria-expanded="true" >Issuance</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content" id="tab-subcontents">
                                                <div class="tab-pane fade in <?php if(isset($active_tab)){ if($active_tab == 0){ echo 'active';} }?>" id="requestsummary">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered display nowrap" cellspacing="0" width="100%" style="font-size:10px" id="tblSummary" >
                                                                    <thead>
                                                                        <tr>
                                                                            <td></td>
                                                                            <td>Transaction No.</td>
                                                                            <td>Date Created</td>
                                                                            <td>PO No.</td>
                                                                            <td>Destination</td>
                                                                            <td>Line</td>
                                                                            <td>Status</td>
                                                                            <td>Requested By</td>
                                                                            <td>Last Served By</td>
                                                                            <td>Last Served Date</td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody style="font-size:10px">

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-fixedheader table-striped" style="font-size:10px">
                                                                    <thead>
                                                                        <tr>
                                                                            <td style="width:5.1%;" class="text-center">
                                                                            	<input type="checkbox" class="details_check" value="">
                                                                            </td>
                                                                            <td style="width:11.1%;">Detail ID</td>
                                                                            <td style="width:11.1%;">Item/Part No.</td>
                                                                            <td style="width:17.1%;">Item Description</td>
																			<td style="width:11.1%;">Classification</td>
                                                                            <td style="width:11.1%;">Issued Qty.(Kitting)</td>
                                                                            <td style="width:11.1%;">Request Qty.</td>
                                                                            <td style="width:11.1%;">Served Qty.</td>
                                                                            <td style="width:11.1%;">Served Date</td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="tblViewDetail" style="font-size:10px">

                                                                    </tbody>
                                                                </table>
                                                            </div>

															<br>
															<div class="col-md-12 text-center">
																<a href="javascript:;" class="btn green btn-sm" id="btn_addToIssuance" disabled="true">
																	<i class="fa fa-plus"></i> Add
																</a>
																<input type="hidden" id="total_req_qty" name="total_req_qty">
																<input type="hidden" id="req_status" name="req_status">
															</div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="tab-pane fade in <?php if(isset($active_tab)){ if($active_tab == 1){ echo 'active';} }?>" id="issuance">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                       		 <form>

                                                       			 <div class="col-md-5">
                                                       				 <div class="form-group row">
                                                       					 <label class="control-label col-md-3">Issuance No.</label>
                                                       					 <div class="col-md-9">
                                                                            <div class="input-group">
                                                                                <input type="hidden" class="form-control input-sm" id="recid" name="recid"/>
                                                                                <input type="hidden" class="form-control input-sm" id="action" name="action" />
                                                                                <input type="text" class="form-control input-sm clear" id="issuancenowhs" name="issuancenowhs">

                                                                                <span class="input-group-btn">
                                                                                    <a href="javascript:navigate('first');" id="btn_min" class="btn blue input-sm"><i class="fa fa-fast-backward"></i></a>
                                                                                    <a href="javascript:navigate('prev');" id="btn_prv" class="btn blue input-sm"><i class="fa fa-backward"></i></a>
                                                                                    <a href="javascript:navigate('next');" id="btn_nxt" class="btn blue input-sm"><i class="fa fa-forward"></i></a>
                                                                                    <a href="javascript:navigate('last');" id="btn_max" class="btn blue input-sm"><i class="fa fa-fast-forward"></i></a>
                                                                                </span>
                                                                            </div>

                                                                            
                                                       					 </div>
                                                       				 </div>
                                                       				 <div class="form-group row">
                                                       					 <label class="control-label col-md-3">Request No.</label>
                                                       					 <div class="col-md-8">
                                                       						 <input type="text" class="form-control input-sm clear" id="reqno" name="reqno" readonly>
                                                       					 </div>
                                                       				 </div>
                                                       				 <div class="form-group row">
                                                       					 <label class="control-label col-md-3">Status</label>
                                                       					 <div class="col-md-8">
                                                       						 <input type="text" class="form-control input-sm clear" id="statuswhs" name="statuswhs" readonly>
                                                       					 </div>
                                                       				 </div>
                                                       			 </div>

                                                       			 <div class="col-md-3">
                                                                     <div class="form-group row">
                                                                         <label class="control-label col-md-5">Created By</label>
                                                                         <div class="col-md-7">
                                                                             <input type="text" class="form-control input-sm clear" id="createdbywhs" name="createdbywhs" readonly>
                                                                         </div>
                                                                     </div>
                                                                     <div class="form-group row">
                                                                         <label class="control-label col-md-5">Created Date</label>
                                                                         <div class="col-md-7">
                                                                             <input class="form-control input-sm clear date-picker" size="16" type="text" name="createddatewhs" id="createddatewhs" readonly/>
                                                                         </div>
                                                                     </div>
                                                                     <div class="form-group row">
                                                       					 <label class="control-label col-md-5">Total Request Qty</label>
                                                       					 <div class="col-md-7">
                                                       						 <input type="text" class="form-control input-sm clear" id="totreqqty" name="totreqqty" readonly>
                                                       					 </div>
                                                       				 </div>
                                                       			 </div>

                                                       			 <div class="col-md-4">

                                                       				 <div class="form-group row">
                                                       					 <label class="control-label col-md-4">Updated By</label>
                                                       					 <div class="col-md-8">
                                                       						 <input type="text" class="form-control input-sm clear" id="updatedbywhs" name="updatedbywhs" readonly>
                                                       					 </div>
                                                       				 </div>
                                                       				 <div class="form-group row">
                                                       					 <label class="control-label col-md-4">Updated Date</label>
                                                       					 <div class="col-md-8">
                                                       						<input class="form-control input-sm clear date-picker" size="16" type="text" name="updateddatewhs" id="updateddatewhs" readonly/>
                                                       						<input type="hidden" class="add" id="hd_barcode" name="hd_barcode" />
                                                       						<input type="hidden" class="add" id="hd_status" name="hd_status" />
                                                       					 </div>
                                                       				 </div>

                                                                     <div class="form-group row">
                                                                         <label class="control-label col-md-4">Total Balance Qty</label>
                                                                         <div class="col-md-8">
                                                                             <input type="text" class="form-control input-sm clear" id="totbalqty" name="totbalqty" readonly>
                                                                         </div>
                                                                     </div>
                                                       			 </div>

                                                       		 </form>
                                                       	 </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="portlet box">
                                                                        <div class="portlet-body">

                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <table class="table table-bordered table-fixedheader table-striped" style="font-size:10px">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <td style="width:100%;" class="text-center" colspan="9">Details</td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                {{-- <td width="10px;">
                                                                                                    <input type="checkbox" class="checkboxes" value="1"/>
                                                                                                </td> --}}
                                                                                                <td width="4.5%;"></td>
                                                                                                <td width="12.5%;">Detail ID</td>
                                                                                                <td width="12.5%;">Item/Part No.</td>
                                                                                                <td width="19.8%;">Item Description</td>
                                                                                                <td width="12.4%;">Issued Qty.(Others)</td>
                                                                                                <td width="12.5%;">Issued Qty.(This)</td>
                                                                                                <td width="12.5%;">Lot No.</td>
                                                                                                <td width="12.5%;">Location</td>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody id="tblIssuance">

                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>

                                                                            {{-- <div class="row">
                                                                                <div class="col-md-4 col-md-offset-5">
                                                                                    <button class="btn red input-sm" id="btn_deleteAll" style="font-size:12px;">
                                                                                        <i class="fa fa-trash"></i> Delete Detail
                                                                                    </button>
                                                                                </div>
                                                                            </div> --}}

                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>


                                                            <div class="row">
																<div class="col-md-12 text-center">
																  	<a href="javascript:;" style="font-size:12px;" class="btn blue-madison input-sm" id="btn_save" <?php echo($state); ?> >
																    	<i class="fa fa-floppy-o"></i> Save
																	</a>
																	<a href="javascript:;" style="font-size:12px;" class="btn blue-madison input-sm" id="btn_edit" <?php echo($state); ?> >
																	    <i class="fa fa-pencil"></i> Edit
																	</a>
																	<a href="javascript:;" style="font-size:12px;" class="btn red input-sm" id="btn_cancel" <?php echo($state); ?> >
																	    <i class="fa fa-trash"></i> Cancel
																	</a>
																	<a href="javascript:;" style="font-size:12px;" class="btn red-intense input-sm" id="btn_discard" <?php echo($state); ?> >
																	    <i class="fa fa-times"></i> Discard Changes
																	</a>
																  	<button type="button" style="font-size:12px;" class="btn blue-steel input-sm" id="btn_search" >
																    	<i class="fa fa-search"></i> Search
																  	</button>

																  	<button type="button" style="font-size:12px;" id="btn_report_excel" class="btn yellow-gold input-sm"
																    	<i class="fa fa-file-excel-o"></i> Export to Excel
																  	</button>

                                                                    <button type="button" style="font-size:12px;" id="btn_material_request" class="btn input-sm purple">
                                                                        <i class="fa fa-print"></i>
                                                                    </button>
																</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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

	@include('includes.materialissuance-modal')
    @include('includes.modals')
@endsection

@push('script')
<script type="text/javascript">
    var token = "{{Session::token()}}";
    var cu = "{{Auth::user()->user_id}}";
    var whsMaterialRequestPDF = "{{ url('/whs-material-request-pdf') }}";
    var whsServingURL = "{{ url('/wbswhsserving') }}";
    var getMatBcodeURL = "{{ url('/getmatbarcode') }}"
    var saveWhsIssuanceURL = "{{url('/savewhsissuance')}}";
    var whsIssuanceCancel = "{{url('/whsissuancecancel')}}";
    var whsMateIssuanceURL = "{{url('/wbswhsmatissuance')}}";
    var whsExcelReport = "{{ url('/wbsWhsReport_Excel')  }}";
    var getTotalBalanceURL = "{{url('/gettotalbalanceqty')}}";
    var getsearch_viewDetailsURL = "{{url('/getsearch_viewDetails')}}";
    var getmassalertURL = "{{url('/getmassalert')}}";
    var viewdetailsURL = "{{url('/viewdetails')}}";
    var wbswhscheckifnotcloseURL = "{{url('/wbswhscheckifnotclose')}}";
    var wbswhsissuancefifotblURL = "{{ url('/wbswhsissuancefifotbl') }}";
    var wbswhsissuancefifotblbcURL = "{{ url('/wbswhsissuancefifotblbc') }}";
    var whsissuancenavURL = "{{url('/whsissuancenav')}}";
    var whslatestissuanceURL =  "{{url('/whslatestissuance')}}";
    var whsSearchURL = "{{ url('/wbswmi-search') }}";
</script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
<script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/whsmaterialissuance.js') }}" type="text/javascript"></script>
@endpush
