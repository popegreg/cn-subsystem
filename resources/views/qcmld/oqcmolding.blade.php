@extends('layouts.master')

@section('title')
	QC Database Molding | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_OQCMLD'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
				<div class="portlet box grey-gallery" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-search"></i>  QC Database Molding
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-12">
            							<div class="portlet-body">
                                            <div class="row">
                                                <form class="form-forizontal">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-2">P.O. No.</label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control input-sm" id="posearch" name="posearch">
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <a href="javascript:;" class="btn blue input-sm" id="btn_posearch">
                                                                    View P.O.
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-2">Date From</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control input-sm date-picker" id="from" name="from">
                                                            </div>
                                                            <label class="control-label col-sm-2">Date to</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control input-sm date-picker" id="to" name="to">
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <a href="javascript:;" class="btn blue input-sm" id="btn_datesearch">
                                                                    Go
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-bordered table-striped tabla-responsive" id="oqcdatatable">
                                                            <thead>
                                                                <tr>
                                                                    <td style="width: 5%"></td>
                                                                    <td style="width: 5%"></td>
                                                                    <td>ID</td>
                                                                    <td>Date Inspected</td>
                                                                    <td>Shift</td>
                                                                    <td>From</td>
                                                                    <td>To</td>
                                                                    <td>Submission#</td>
                                                                    <td>Lot #</td>
                                                                    <td>Lot Size</td><!-- lot qty-->
                                                                    <td>Sample Size</td>
                                                                    <td>No of Defectives</td>
                                                                    <td>Mode of Defects</td>
                                                                    <td>Qty</td>
                                                                    <td>Judgement</td>
                                                                    <td>PTCP/TNR #</td>
                                                                    <td>Inspector</td>
                                                                    <td>Remarks</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="oqctable">
                                                                
                                                            </tbody>
                                                        </table>
                                                        <input type="hidden" id="record_count">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

								<div class="row">
                                    <div class="col-md-12 text-center">
                                        <a href="javascript:;" class="btn blue" id="btn_addnew">
                                            <i class="fa fa-plus"></i> Add New
                                        </a>
                                        <!-- <a href="javascript:;" class="btn grey-gallery" id="btn_groupby">
                                            <i class="fa fa-group"></i> Group By
                                        </a> -->
                                       <!--  <button type="button" class="btn grey-gallery" id="btn_groupby">
                                            <i class="fa fa-group"></i> Group By
                                        </button> -->
                                        {{-- <button type="button" class="btn red" id="btn_delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button> --}}
                                        <!-- <a href="javascript:;" class="btn purple" id="btn_search">
                                            <i class="fa fa-search"></i> Search
                                        </a> -->
										<a href="javascript:;" class="btn red" id="btn_pdf">
                                            <i class="fa fa-file-pdf-o"></i> Export to PDF
                                        </a>
                                        <a href="javascript:;" class="btn green" id="btn_excel">
                                            <i class="fa fa-file-excel-o"></i> Export to Excel
                                        </a>
                                    </div>
                                </div>
                                <input class="form-control input-sm" type="hidden" value="" name="hd_report_status" id="hd_report_status"/>

                                <input type="hidden" class="form-control input-sm clear" id="hdg1_selected" name="hdg1_selected" readonly>
                                <input type="hidden" class="form-control input-sm clear" id="hdg2_selected" name="hdg2_selected" readonly>
                                <input type="hidden" class="form-control input-sm clear" id="hdg3_selected" name="hdg3_selected" readonly>

                                <input type="hidden" class="form-control input-sm clear" id="count_lotrejected" name="count_lotrejected" readonly>
                                <input type="hidden" class="form-control input-sm clear" id="count_Totallotrejected" name="count_Totallotrejected" readonly>
                                <input type="hidden" class="form-control input-sm clear" id="count_lot" name="count_lot" readonly>
                                <input type="hidden" class="form-control input-sm clear" id="lot_accepted_changed" name="lot_accepted_changed" readonly>

							</div>
						</div>
					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>


    <!-- ADD NEW MODAL -->
    <div id="AddNewModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog gray-gallery modal-xl">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Application for OQC Inspection</h4>
                </div>
                <form class=form-horizontal>
                    <div class="modal-body">

                        <div class="row">
							<div class="col-md-12">
								<strong>Visual Inspection</strong>
							</div>
						</div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">P.O. No.</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="po_no" name="po_no" required>
                                        <input type="hidden" name="oqc_id" id="oqc_id">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Parts Code</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="part_code" name="part_code" readonly required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Parts Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="part_name" name="part_name" readonly required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Customer</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear show-tick" name="customer" id="customer" readonly required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Family</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm show-tick clear" name="family" id="family">
                                            <option value=""></option>  
                                            @foreach($families as $family)
                                                <option value="{{$family->description}}">{{$family->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Total Qty</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="total_qty" name="total_qty" readonly required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Die No.</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm clear show-tick" name="die_num" id="die_num" required>
                                            <option value="option"></option>
                                            @foreach($dienos as $dieno)
                                            <option value="{{$dieno->description}}">{{$dieno->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Quantity</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm show-tick clear" name="qty" id="qty">    
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Lot No.</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm show-tick clear" name="lot_no" id="lot_no">
                                        <!-- <a href="javascript:;" class="btn blue btn-sm" id="btn_lot" disabled="true">
                                            <i class="fa fa-plus-circle"></i> Add Lot Number
                                        </a> -->
                                    </div>
                                </div>
                            </div>

                        </div>

						<hr>

						<div class="row">
							<div class="col-md-12">
								<strong>Sampling Plan</strong>
							</div>
						</div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Type of Inspection</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm clear show-tick" name="type_of_inspection" id="type_of_inspection" required>
                                            <option ></option>
                                            @foreach($tofinspections as $tofinspection)
                                            <option value="{{$tofinspection->description}}">{{$tofinspection->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Severity of Inspection</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm clear show-tick" name="severity_of_inspection" id="severity_of_inspection" required>
                                            <option ></option>
                                            @foreach($sofinspections as $sofinspection)
                                            <option value="{{$sofinspection->description}}">{{$sofinspection->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Inspection Level</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm clear show-tick" name="inspection_lvl" id="inspection_lvl" required>
                                            <option></option>
                                            @foreach($inspectionlvls as $inspectionlvl)
                                            <option value="{{$inspectionlvl->description}}">{{$inspectionlvl->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">AQL</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm" id="aql" name="aql" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Accept</label>
                                    <div class="col-sm-9">
                                        <input type="number" min="0" max="1" class="form-control input-sm" id="accept" name="accept" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Reject</label>
                                    <div class="col-sm-9">
                                        <input type="number" min="0" max="1" class="form-control input-sm" id="reject" name="reject" required>
                                    </div>
                                </div>
                            </div>

                        </div>

						<hr>

						<div class="row">
							<div class="col-md-12">
								<strong>Visual Inspection Result</strong>
							</div>
						</div>

						<div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Date Inspected</label>
                                    <div class="col-sm-9">
                                        <input class="form-control input-sm date-picker" type="text" value="{{date('m/d/Y')}}" name="date_inspected" id="date_inspected" required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Time Inspected</label>
                                    <div class="col-sm-4">
                                        <input type="text" data-format="hh:mm A" class="form-control input-sm clear clockface_1 checkifEmpty" name="time_ins_from" id="time_ins_from"/>
                                        <div id="er_time_ins_from"></div>
                                    </div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4">
                                        <input type="text" data-format="hh:mm A" class="form-control input-sm clear clockface_1 checkifEmpty" name="time_ins_to" id="time_ins_to"/>
                                        <div id="er_time_ins_to"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Shift</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm clear show-tick" name="shift" id="shift" required>
                                            <option value="option"></option>
                                            @foreach($shifts as $shift)
                                            <option value="{{$shift->description}}">{{$shift->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Inspector</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm show-tick" name="inspector" id="inspector" value="{{Auth::user()->user_id}}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Submission</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm clear show-tick" name="submission" id="submission" required>
                                            <option value="option"></option>
                                            @foreach($submissions as $submission)
                                            <option value="{{$submission->description}}">{{$submission->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Visial Operator</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="visual_operator" name="visual_operator" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">WW#</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control input-sm clear" id="ww" name="ww" required>
                                    </div>
                                    <label class="control-label col-sm-3">FY#</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control input-sm clear" id="fy" name="fy" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Remarks</label>
                                    <div class="col-sm-9">
                                        <textarea name="remarks" id="remarks" class="form-control input-sm clear" style="resize:none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">PTCP / TNR#</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="ptcp_tnr" name="ptcp_tnr" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Lot Inspected</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm" id="lot_inspected" name="lot_inspected" value="1" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Lot Accepted</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="lot_accepted" name="lot_accepted" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Sample Size</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="sample_size" name="sample_size" required readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Judgement</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="judgement" name="judgement" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3" id="no_of_defects_label">No. of Defects</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="no_of_defects" name="no_of_defects" required readonly>
                                    </div>
                                </div>
								<div class="form-group">
									<label class="control-label col-sm-3" id="btn_mode_of_defects_label">Mode of Defects</label>
									<div class="col-sm-4">
										<a href="javascript:;" class="btn blue btn-sm" id="btn_mode_of_defects" disabled="true">
                                            <i class="fa fa-plus-circle"></i> Add Mode of Defects
                                        </a>
                                      <!--   <a href="javascript:;" class="btn blue btn-sm" id="btn_ndf" disabled="true">
                                            <i class="fa fa-plus-circle"></i> NDF
                                        </a> -->
									</div>
									<div class="col-sm-4">
                                        <div class="col-sm-9">
                                            <input type="hidden" class="form-control input-sm" id="oqc_status" name="oqc_status" value="insert">
                                            <input type="hidden" class="form-control input-sm" id="start_time" name="start_time">

                                        </div>
                                    </div>
								</div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn btn-success" id="btn_saveoqc">Save</a>
                        <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lot No -->
    <div id="LotNoModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Lot Numbers</h4>
                </div>
                <form class="form-horizontal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Lot No.</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm show-tick" name="lot_no" id="lot_no">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Quantity</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="lotqty" id="lotqty" class="form-control input-sm">
                                        <input type="hidden" name="state" id="state">
                                        <input type="hidden" name="lot_id" id="lot_id">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-striped" id="tbl_lotno">
                                        <thead>
                                            <tr>
                                                <td>Lot No.</td>
                                                <td>Quantity</td>
                                                <td width="10%">Option</td>
                                            </tr>
                                        </thead>
                                        <tbody id="tblforLot">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn btn-success input-sm" id="btn_lot_save">Save</a>
                        <button type="button" data-dismiss="modal" class="btn btn-danger input-sm">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODE OF DEFECTS -->
    <div id="ModeOfDefectsModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Mode of Defect</h4>
                </div>
                <form class="form-horizontal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Mode of Defects</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm show-tick def_clear" name="mode_of_def " id="mode_of_def">
                                            <option value="option"></option>
                                            @foreach($mods as $mod)
                                                <option value="{{$mod->description}}">{{$mod->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Quantity</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="mode_qty" id="mode_qty" class="form-control input-sm def_clear">
                                        <input type="hidden" name="mode_state" id="mode_state" class="form-control input-sm">
                                        <input type="hidden" name="mod_stat" id="mod_stat" class="form-control input-sm">
                                        <input type="hidden" name="mod_id" id="mod_id" class="form-control input-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-striped" id="tbl_modeofdefect">
                                        <thead>
                                            <tr>
                                                <td>Mode of Defects</td>
                                                <td>Quantity</td>
                                                <td>Option</td>
                                            </tr>
                                        </thead>
                                        <tbody id="tblformod">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn btn-success" id="btn_mod_save">Save</a>
                        <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

	<!-- SEARCH MODAL -->
	<div id="SearchModal" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog gray-gallery">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Search</h4>
				</div>
				<form class="form-horizontal">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label col-sm-3">From</label>
									<div class="col-sm-7">
										<input class="form-control input-sm date-picker" type="text" value="" name="search_from" id="search_from"/>
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-sm-3">To</label>
									<div class="col-sm-7">
										<input class="form-control input-sm date-picker" type="text" value="" name="search_to" id="search_to"/>
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<a href="javascript:;" class="btn btn-success" id="">OK</a>
						<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- GROUP BY MODAL -->
    <div id="GroupByModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg gray-gallery">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Group Items By:</h4>
                </div>
                <form class="form-horizontal">
                    <div class="modal-body">
                        {!! csrf_field() !!}
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label col-sm-2"></label>
                                <div class="col-sm-3">
                                    <!-- <input type="text" class="form-control datepicker input-sm " id="groupby_datefrom" name="groupby_datefrom">   -->   
                                </div>
                                <div class="col-sm-3">
                                        <!-- <input type="text" class="form-control datepicker input-sm " id="groupby_dateto" name="groupby_dateto"> -->
                                </div>
                            </div>
                        </div>
                        <br>
                
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label col-sm-2">Date From</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control datepicker input-sm " id="groupby_datefrom" name="groupby_datefrom">     
                                </div>
                                <div class="col-sm-5">
                                        <input type="text" class="form-control datepicker input-sm " id="groupby_dateto" name="groupby_dateto">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label col-sm-2">Group #1</label>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group1" id="group1">
                                        <option value=""></option>
                                        <option value="date_inspected">Date Inspected</option>
                                        <option value="submission">Submission</option>
                                        <option value="fy_no">FY#</option>
                                        <option value="ww_no">WW#</option>
                                        <option value="customer">Customer</option>
                                        <option value="partcode">Part Code</option>
                                        <option value="partname">Part Name</option>
                                        <option value="lot_qty">Lot Qty.</option>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group1content" id="group1content">
                                    <!-- append here -->
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label col-sm-1" id=""><!-- g1 --></label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label col-sm-1" id=""><!-- g1 --></label>
                                </div>
                            </div>  
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label col-sm-2">Group #2</label>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group2" id="group2">
                                        <option value=""></option>
                                        <option value="date_inspected">Date Inspected</option>
                                        <option value="submission">Submission</option>
                                        <option value="fy_no">FY#</option>
                                        <option value="ww_no">WW#</option>
                                        <option value="customer">Customer</option>
                                        <option value="partcode">Part Code</option>
                                        <option value="partname">Part Name</option>
                                        <option value="lot_qty">Lot Qty.</option>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group2content" id="group2content">
                                    <!-- append here -->
                                    <option value=""></option>  
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label col-sm-1" id=""><!-- g2 --></label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label col-sm-1" id=""><!-- g2 --></label>
                                </div>
                            </div>  
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label col-sm-2">Group #3</label>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group3" id="group3">
                                        <option value=""></option>
                                        <option value="date_inspected">Date Inspected</option>
                                        <option value="submission">Submission</option>
                                        <option value="fy_no">FY#</option>
                                        <option value="ww_no">WW#</option>
                                        <option value="customer">Customer</option>
                                        <option value="partcode">Part Code</option>
                                        <option value="partname">Part Name</option>
                                        <option value="lot_qty">Lot Qty.</option>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group3content" id="group3content">
                                    <!-- append here -->
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label col-sm-1" id=""><!-- g3 --></label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label col-sm-1" id=""><!-- g3 --></label>
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <td></td>
                                            <td>Total Inspected</td>
                                            <td>Total Accept</td>
                                            <td>Total Reject</td>
                                            <td>Total Sample Size</td>
                                            <td>Total NG</td>
                                            <td>Total LAR</td>
                                            <td>Total LRR</td>
                                            <td>Total DPPM</td>
                                        </tr>
                                    </thead>
                                    <tbody id="tblfortotallarlrrdppm">
                                        <!-- table records here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>   
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <td></td>
                                            <td>Inspected</td>
                                            <td>Accept</td>
                                            <td>Reject</td>
                                            <td>Sample Size</td>
                                            <td>NG</td>
                                            <td>LAR</td>
                                            <td>LRR</td>
                                            <td>DPPM</td>
                                        </tr>
                                    </thead>
                                    <tbody id="tblforlarlrrdppm">
                                        <!-- table records here -->
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="javascript:groupby();" class="btn btn-success" id="">OK</button>
                        <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--del lot msg-->
    <div id="delmsg" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Lot Number</h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to delete this lot number?</p>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-danger iput-sm" id="delete_lot">Delete</a>
                    <button type="button" data-dismiss="modal" class="btn btn-primary iput-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!--del mod msg-->
    <div id="delmodmsg" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Lot Number</h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to delete this Mode of defect?</p>
                    <input type="hidden" name="mod_desc" id="mod_desc" class="form-control input-sm">
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-danger iput-sm" id="delete_mod">Delete</a>
                    <button type="button" data-dismiss="modal" class="btn btn-primary iput-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!--del oqc msg-->
    <div id="deloqcmsg" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Lot Number</h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to delete this OQC Record?</p>
                    <input type="hidden" name="oqc_id" id="oqc_id" class="form-control input-sm">
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-danger iput-sm" id="delete_oqc">Delete</a>
                    <button type="button" data-dismiss="modal" class="btn btn-primary iput-sm">Cancel</button>
                </div>
            </div>
        </div>
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

    <!--msg_success-->
    <div id="msg_success" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 id="success_title" class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p id="success_msg"></p>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button> --}}
                    <a href="javascript: restart_lotno();" class="btn btn-success" id="success_done">Done</a>
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
<script>
    $(function() {
        init_mod();
        loadOQC();
        $('#hd_report_status').val("");
        $('#groupby_datefrom').datepicker();
        $('#groupby_dateto').datepicker();
        $('#groupby_datefrom').on('change',function(){
          $(this).datepicker('hide');
        });
        $('#groupby_dateto').on('change',function(){
              $(this).datepicker('hide');
        });
    	$('#btn_addnew').on('click', function (){
            getTime();
    		$('#AddNewModal').modal('show');
            $('#aql').val("0.65");
            $('#po_no').attr('disabled',false);
            $('#accept').val(0);
            $('#reject').val(1)
            $('#oqc_status').val("insert");
            $('.clear').val('');
    	});

    	$('#btn_mode_of_defects').on('click', function() {
    		$('#ModeOfDefectsModal').modal('show');
            $('#mode_state').val('insert');
            $('.def_clear').val("");
            $('#mod_stat').val("ADD");
    	});

    	$('#btn_groupby').on('click', function() {
    		$('#GroupByModal').modal('show');
            $('#hd_report_status').val("GROUPBY");
    	});

    	$('#btn_search').on('click', function() {
    		$('#SearchModal').modal('show');
    	});

        $('#btn_lot').on('click', function() {
            $('#btn_cancel_update_lot').hide();
            $('#LotNoModal').modal('show');
            $('#state').val('insert');
            $('#lot_no').val('');
            $('#lotqty').val('');
        });

        // execute the button of mode of defects
        $('#lot_accepted').on('change', function() {
            if ($(this).val() > 1) {
                $(this).val(1);
            }
            if ($(this).val() == 0) {
                $('#judgement').val('Rejected');
                init_mod(); 
            } else {
                $('#judgement').val('Accepted');
                init_mod();
            }
        });

        // generate details of po number
        $('#po_no').on('change', function() {
            var po = $(this).val();
            $.ajax({
                url:"{{ url('/getpooqcmolding') }}",
                method:'get',
                data:{
                    po:po
                },
            }).done( function(data, textStatus, jqXHR) {
                if (data == 0) {
                    $('#msg').modal('show');
                    $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#err_msg').html("That P.O. number is not available.");
                }else {
                    $.each(data, function(i,x) {
                        $('#part_code').val(x.partcode);
                        $('#part_name').val(x.partname);
                        $('#customer').val(x.customer);
                        $('#total_qty').val(parseFloat(x.qty));
                        getTableLotno();
                        getMODtable();
                        getTotalQty();
                        getMODTotalQty();
                    });
                    $('#btn_lot').removeAttr('disabled');
                    $('#btn_ndf').removeAttr('disabled');
                    $('#btn_mode_of_defects').removeAttr('disabled');
                }
                //date today
                var date = new Date();
                var month = date.getMonth()+1;
                var day = date.getDate();
                var output = (month<10 ? '0' : '') + month + '/' +(day<10 ? '0' : '') + day + '/' +  date.getFullYear();
                
                $('#date_inspected').val(output);
               
                var current_year = date.getFullYear();
                var newweek = new Date($('#date_inspected').val());
                var weektoday = newweek.getWeek();

                var adjustedweek = '';
                var adjustedyear = '';
                if(weektoday < 14){
                    adjustedweek = weektoday + 39;
                    adjustedyear = current_year - 1;
                }else{
                    adjustedweek = weektoday - 13;
                    adjustedyear = current_year;
                }
                $('#ww').val(adjustedweek);
                $('#fy').val(adjustedyear);
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
        });

        //saving / adding lot number
        $('#btn_lot_save').on('click', function() {
            var state = $('#state').val();
            var po = $('#po_no').val();
            var lot_no = $('#lot_no').val();
            var qty = $('#lotqty').val();
            var total_qty = $('#total_qty').val();
            var id = $('#lot_id').val();
            
            insert_lot(po,lot_no,qty,total_qty);
            
        });

        // initiate delete lot
        $('#tblforLot').on('click', '.btn_del_lot', function() {
            $('#lot_id').val($(this).attr('data-id'));
            $('#delmsg').modal('show');
        });

        // delete lot
        $('#delete_lot').on('click', function() {
            var id = $('#lot_id').val();
            $('#delmsg').modal('hide');
            delete_lot(id);
        });

        // initiate delete mod
        $('#tblformod').on('click', '.btn_del_MOD', function() {
            $('#mod_desc').val($(this).attr('data-mod'));
            $('#delmodmsg').modal('show');
        });

        // delete mod
        $('#delete_mod').on('click', function() {
            var mod = $('#mod_desc').val();
            var po = $('#po_no').val();
            var partcode = $('#part_code').val();
            $('#delmodmsg').modal('hide');
            delete_mod(po,partcode,mod);
        });

        // compute sampling plan
        
        $('#severity_of_inspection').on('change',function() {
            samplingplan();
        });
        $('#inspection_lvl').on('change',function() {
            samplingplan();
        });
        
        $('#aql').on('change', function() {
            samplingplan();
        });
        $('#quantity').on('change', function() {
            samplingplan();
        });
        $('#inspection_lvl').on('change', function() {
            samplingplan();
        });
        $('#severity_of_inspection').on('change', function() {
            samplingplan();
        });

        //mod save
        $('#btn_mod_save').on('click', function() {
            var mod = $('#mode_of_def').val();
            var qty = $('#mode_qty').val();
            var state = $('#mod_stat').val();
            var id = $('#mod_id').val();
            var po = $('#po_no').val();
            var partcode = $('#part_code').val();
            var submission = $('#submission').val();
            var lotno = $('#lot_no').val();

            save_mod(po,partcode,mod,qty,state,id,submission,lotno);
        });

        // save oqc
        $('#btn_saveoqc').on('click', function() {
            save_oqc();
            $('#clear').val('');
            $('#po_no').attr('disabled',false);
        });

        // update oqc
        $('#oqctable').on('click','.update-oqc', function() {
            var edittext =  $(this).val().split('|');
            var id = edittext[0];
            var po_no = edittext[1];
            var partcode = edittext[2];
            var partname = edittext[3];
            var customer = edittext[4];
            var family = edittext[5];
            var total_qty = edittext[9];
            var die_no = edittext[7];
            var qty = edittext[8];
            var lot_qty = edittext[9];
            var lot_no = edittext[10];
            var type_of_inspection = edittext[11];
            var severity_of_inspection = edittext[12];
            var inspection_lvl = edittext[13];
            var aql = edittext[14];
            var accept = edittext[15];
            var reject = edittext[16];
            var date_inspected = edittext[17];
            var shift = edittext[18];
            var inspector = edittext[19];
            var submission = edittext[20];
            var visual_operator = edittext[21];
            var fy_no = edittext[22];
            var ww_no = edittext[23];
            var remarks = edittext[24];
            var ptcp_tnr = edittext[25];
            var lot_inspected = edittext[26];
            var lot_accepted = edittext[27];
            var lot_rejected = edittext[28];
            var sample_size = edittext[29];
            var judgement = edittext[30];
            var from = edittext[31];
            var to = edittext[32];
            var num_of_defects = edittext[33];

            $('#oqc_status').val('update');
            $('#po_no').val(po_no);
            $('#part_code').val(partcode);
            $('#part_name').val(partname);
            $('#customer').val(customer);
            $('#total_qty').val(total_qty);
            $('#die_num').val(die_no);
            $('#qty').val(qty);
            $('#lot_no').val(lot_no);
            $('#type_of_inspection').val(type_of_inspection);
            $('#severity_of_inspection').val(severity_of_inspection);
            $('#inspection_lvl').val(inspection_lvl);
            $('#aql').val(aql);
            $('#accept').val(accept);
            $('#reject').val(reject);
            $('#date_inspected').val(date_inspected);
            $('#time_ins_from').val(from);
            $('#time_ins_to').val(to);
            $('#shift').val(shift);
            $('#inspector').val(inspector);
            $('#submission').val(submission);
            $('#visual_operator').val(visual_operator);
            $('#ww').val(ww_no);
            $('#fy').val(fy_no);
            $('#ptcp_tnr').val(ptcp_tnr);
            $('#lot_inspected').val(lot_inspected);
            $('#lot_accepted').val(lot_accepted);
            $('#sample_size').val(sample_size);
            $('#judgement').val(judgement);
            $('#no_of_defects').val(no_of_defects);
            $('#family').val(family);
            $('#remarks').val(remarks);
            $('#oqc_id').val(id);

            getTableLotno();
            getMODtable();
            getTotalQty();
            getMODTotalQty();
            init_mod();
            $('#btn_lot').removeAttr('disabled');
            $('#btn_mode_of_defects').removeAttr('disabled');
            $('#po_no').attr('disabled',true);
            $('#AddNewModal').modal('show');
        });

        //initiate delete
        $('#oqctable').on('click','.delete-oqc', function() {
            var id = $(this).attr('data-id');
            $('#oqc_id').val(id);
            $('#deloqcmsg').modal('show');
        })

        //delete oqc
        $('#delete_oqc').on('click', function() {
            $('#deloqcmsg').modal('hide');
            var id = $('#oqc_id').val();
            var url = "{{url('/deleteoqc')}}";
            var token = "{{ Session::token() }}";
            var data = {
                _token: token,
                id: id,
            };
            $.ajax({
                url: url,
                type: "POST",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                if (data.status == "success") {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                    $('#success_msg').html("OQC Record was successfully deleted.");
                    window.location.href="{{ url('/oqcmolding') }}";
                }
                if (data.status == "error") {
                    $('#msg').modal('show');
                    $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#err_msg').html("There's some error while processing.");
                }
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
        });
    
        $('#btn_posearch').on('click', function() {
            loadOQCSEARCH();
        });

        $('#btn_datesearch').on('click', function() {
            loadOQC();
        });

        $('#btn_pdf').on('click', function() {
            var status = $('#hd_report_status').val();
            var po = $('#posearch').val();
            var from = $('#from').val();
            var to = $('#to').val();
            var mod = [];
            var lot = [];

            $(".mod_val").each(function() {
                mod.push($(this).html());
            });

            $(".lot_val").each(function() {
                lot.push($(this).html());//push the array values to lot
            }); 
            var tableData  = ''; 
            if(status == "GROUPBY"){
                tableData = {
                    id:$('input[name^="hd_id[]').map(function(){return $(this).val();}).get(),
                    date_inspected:$('input[name^="hd_date_inspected[]').map(function(){return $(this).val();}).get(),
                    submission:$('input[name^="hd_submission[]').map(function(){return $(this).val();}).get(),
                    fy:$('input[name^="hd_fy[]').map(function(){return $(this).val();}).get(),
                    ww:$('input[name^="hd_ww[]').map(function(){return $(this).val();}).get(),
                    customer:$('input[name^="hd_customer[]').map(function(){return $(this).val();}).get(),
                    partcode:$('input[name^="hd_partcode[]').map(function(){return $(this).val();}).get(),
                    partname:$('input[name^="hd_partname[]').map(function(){return $(this).val();}).get(),
                    pono:$('input[name^="hd_pono[]').map(function(){return $(this).val();}).get(),
                    lotno:lot,
                    qty:$('input[name^="hd_qty[]').map(function(){return $(this).val();}).get(),
                    lotqty:$('input[name^="hd_lotqty[]').map(function(){return $(this).val();}).get(),
                    shift:$('input[name^="hd_shift[]').map(function(){return $(this).val();}).get(),
                    remarks:$('input[name^="hd_remarks[]').map(function(){return $(this).val();}).get(),
                    from:$('input[name^="hd_from[]').map(function(){return $(this).val();}).get(),
                    to:$('input[name^="hd_to[]').map(function(){return $(this).val();}).get(),
                    samplesize:$('input[name^="hd_sample_size[]').map(function(){return $(this).val();}).get(),
                    nod:$('input[name^="hd_nod[]').map(function(){return $(this).val();}).get(),
                    mod:mod,
                    ptcptnr:$('input[name^="hd_ptcp_tnr[]').map(function(){return $(this).val();}).get(),
                    judgement:$('input[name^="hd_judgement[]').map(function(){return $(this).val();}).get(),
                    inspector:$('input[name^="hd_inspector[]').map(function(){return $(this).val();}).get(),
                    searchpono:po,
                    datefrom:from,
                    dateto:to,
                    status:status
                }    
            } else {
                tableData = {
                    id:$('input[name^="hd_id[]').map(function(){return $(this).val();}).get(),
                    date_inspected:$('input[name^="hd_date_inspected[]').map(function(){return $(this).val();}).get(),
                    submission:$('input[name^="hd_submission[]').map(function(){return $(this).val();}).get(),
                    fy:$('input[name^="hd_fy[]').map(function(){return $(this).val();}).get(),
                    ww:$('input[name^="hd_ww[]').map(function(){return $(this).val();}).get(),
                    customer:$('input[name^="hd_customer[]').map(function(){return $(this).val();}).get(),
                    partcode:$('input[name^="hd_partcode[]').map(function(){return $(this).val();}).get(),
                    partname:$('input[name^="hd_partname[]').map(function(){return $(this).val();}).get(),
                    pono:$('input[name^="hd_pono[]').map(function(){return $(this).val();}).get(),
                    lotno:$('input[name^="hd_lotno[]').map(function(){return $(this).val();}).get(),
                    qty:$('input[name^="hd_qty[]').map(function(){return $(this).val();}).get(),
                    lotqty:$('input[name^="hd_lotqty[]').map(function(){return $(this).val();}).get(),
                    shift:$('input[name^="hd_shift[]').map(function(){return $(this).val();}).get(),
                    remarks:$('input[name^="hd_remarks[]').map(function(){return $(this).val();}).get(),
                    from:$('input[name^="hd_from[]').map(function(){return $(this).val();}).get(),
                    to:$('input[name^="hd_to[]').map(function(){return $(this).val();}).get(),
                    samplesize:$('input[name^="hd_sample_size[]').map(function(){return $(this).val();}).get(),
                    nod:$('input[name^="hd_nod[]').map(function(){return $(this).val();}).get(),
                    mod:mod,
                    ptcptnr:$('input[name^="hd_ptcp_tnr[]').map(function(){return $(this).val();}).get(),
                    judgement:$('input[name^="hd_judgement[]').map(function(){return $(this).val();}).get(),
                    inspector:$('input[name^="hd_inspector[]').map(function(){return $(this).val();}).get(),
                    searchpono:po,
                    datefrom:from,
                    dateto:to,
                    status:status
                }    
            }
            
            /*window.location.href = "{{url('/oqcprintreportpdf?po=')}}" + po + "&&from=" + from + "&&to=" + to;*/
            var url = "{{ url('/oqcprintreportpdf?data=') }}" + encodeURIComponent(JSON.stringify(tableData));
            window.location.href = url;
        });

        $('#btn_excel').on('click', function() {
            var status = $('#hd_report_status').val();
            var po = $('#posearch').val();
            var from = $('#from').val();
            var to = $('#to').val();
            var mod = [];
            var lot = [];

            $(".mod_val").each(function() {
                mod.push($(this).html());
            });
            $(".lot_val").each(function() {
                lot.push($(this).html());//push the array values to mod
            });

            var tableData = {
                id:$('input[name^="hd_id[]').map(function(){return $(this).val();}).get(),
                date_inspected:$('input[name^="hd_date_inspected[]').map(function(){return $(this).val();}).get(),
                submission:$('input[name^="hd_submission[]').map(function(){return $(this).val();}).get(),
                fy:$('input[name^="hd_fy[]').map(function(){return $(this).val();}).get(),
                ww:$('input[name^="hd_ww[]').map(function(){return $(this).val();}).get(),
                customer:$('input[name^="hd_customer[]').map(function(){return $(this).val();}).get(),
                partcode:$('input[name^="hd_partcode[]').map(function(){return $(this).val();}).get(),
                partname:$('input[name^="hd_partname[]').map(function(){return $(this).val();}).get(),
                pono:$('input[name^="hd_pono[]').map(function(){return $(this).val();}).get(),
                lotno:$('input[name^="hd_lotno[]').map(function(){return $(this).val();}).get(),
                qty:$('input[name^="hd_qty[]').map(function(){return $(this).val();}).get(),
                lotqty:$('input[name^="hd_lotqty[]').map(function(){return $(this).val();}).get(),
                shift:$('input[name^="hd_shift[]').map(function(){return $(this).val();}).get(),
                remarks:$('input[name^="hd_remarks[]').map(function(){return $(this).val();}).get(),
                from:$('input[name^="hd_from[]').map(function(){return $(this).val();}).get(),
                to:$('input[name^="hd_to[]').map(function(){return $(this).val();}).get(),
                samplesize:$('input[name^="hd_sample_size[]').map(function(){return $(this).val();}).get(),
                nod:$('input[name^="hd_nod[]').map(function(){return $(this).val();}).get(),
                mod:mod,
                ptcptnr:$('input[name^="hd_ptcp_tnr[]').map(function(){return $(this).val();}).get(),
                judgement:$('input[name^="hd_judgement[]').map(function(){return $(this).val();}).get(),
                inspector:$('input[name^="hd_inspector[]').map(function(){return $(this).val();}).get(),
                searchpono:po,
                datefrom:from,
                dateto:to,
                status:status
            }
            /*window.location.href = "{{url('/oqcprintreportpdf?po=')}}" + po + "&&from=" + from + "&&to=" + to;*/
            var url = "{{ url('/oqcprintreportexcel?data=') }}" + encodeURIComponent(JSON.stringify(tableData));
            window.location.href = url;
        });

        $('#group1').on('change',function(){
        var g1 = $('select[name=group1]').val();
        var myData = {g1:g1};
        $('#group1content').html("");
        $('#tblforoqcinspection').html("");
        $.post("{{ url('/oqcmoldselectgroupby1') }}",
        {
            _token:$('meta[name=csrf-token]').attr('content'),
            data:myData

        }).done(function(data, textStatus, jqXHR){
            console.log(data);
            /*$('#group1content').val(data);*/
            $.each(data,function(i,val){
                var sup = '';
                    switch(g1) {
                        case "date_inspected":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.date_inspected+'">'+val.date_inspected+'</option>';
                            break;
                        case "submission":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.submission+'">'+val.submission+'</option>';
                            break;
                        case "fy_no":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.fy_no+'">'+val.fy_no+'</option>';
                            break;
                        case "ww_no":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.ww_no+'">'+val.ww_no+'</option>';
                            break;
                        case "customer":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.customer+'">'+val.customer+'</option>';
                            break;
                        case "partcode":
                            var sup = '<option value=""></option>'+ 
                            '<option value="'+val.partcode+'">'+val.partcode+'</option>';
                            break;
                        case "partname":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.partname+'">'+val.partname+'</option>';
                            break;
                        case "lot_qty":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.lot_qty+'">'+val.lot_qty+'</option>';
                            break;
                        default:
                            var sup = '<option value=""></option>';
                    }   
                    
                    var option = sup;
                    $('#group1content').append(option);
                });
            
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });
        });

        $('#group2').on('change',function(){
            var g2 = $('select[name=group2]').val();
            var myData = {g2:g2};
            $('#group2content').html("");
            $('#tblforoqcinspection').html("");
            $.post("{{ url('/oqcmoldselectgroupby1') }}",
            {
                _token:$('meta[name=csrf-token]').attr('content'),
                data:myData

            }).done(function(data, textStatus, jqXHR){
                console.log(data);
                /*$('#group1content').val(data);*/
                $.each(data,function(i,val){
                    var sup = '';
                    switch(g2) {
                        case "date_inspected":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.date_inspected+'">'+val.date_inspected+'</option>';
                            break;
                        case "submission":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.submission+'">'+val.submission+'</option>';
                            break;
                        case "fy_no":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.fy_no+'">'+val.fy_no+'</option>';
                            break;
                        case "ww_no":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.ww_no+'">'+val.ww_no+'</option>';
                            break;
                        case "customer":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.customer+'">'+val.customer+'</option>';
                            break;
                        case "partcode":
                            var sup = '<option value=""></option>'+ 
                            '<option value="'+val.partcode+'">'+val.partcode+'</option>';
                            break;
                        case "partname":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.partname+'">'+val.partname+'</option>';
                            break;
                        case "lot_qty":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.lot_qty+'">'+val.lot_qty+'</option>';
                            break;
                        default:
                            var sup = '<option value=""></option>';
                    }
                        
                    //var option = '<option value="'+val.supplier'">'+val.supplier'</option>';
                    var option = sup;
                    $('#group2content').append(option);
                });
            
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });
        });

        $('#group3').on('change',function(){
            var g3 = $('select[name=group3]').val();
            var myData = {g3:g3};
            $('#group3content').html("");
            $('#tblforoqcinspection').html("");
            $.post("{{ url('/oqcmoldselectgroupby1') }}",
            {
                _token:$('meta[name=csrf-token]').attr('content'),
                data:myData

            }).done(function(data, textStatus, jqXHR){
                console.log(data);
                /*$('#group1content').val(data);*/
                $.each(data,function(i,val){
                    var sup = '';
                    switch(g3) {
                        case "date_inspected":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.date_inspected+'">'+val.date_inspected+'</option>';
                            break;
                        case "submission":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.submission+'">'+val.submission+'</option>';
                            break;
                        case "fy_no":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.fy_no+'">'+val.fy_no+'</option>';
                            break;
                        case "ww_no":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.ww_no+'">'+val.ww_no+'</option>';
                            break;
                        case "customer":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.customer+'">'+val.customer+'</option>';
                            break;
                        case "partcode":
                            var sup = '<option value=""></option>'+ 
                            '<option value="'+val.partcode+'">'+val.partcode+'</option>';
                            break;
                        case "partname":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.partname+'">'+val.partname+'</option>';
                            break;
                        case "lot_qty":
                            var sup = '<option value=""></option>'+
                            '<option value="'+val.lot_qty+'">'+val.lot_qty+'</option>';
                            break;
                        default:
                            var sup = '<option value=""></option>';
                    }
                        
                    //var option = '<option value="'+val.supplier'">'+val.supplier'</option>';
                    var option = sup;
                    $('#group3content').append(option);
                });
            
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });
        });

      
        $('#time_ins_to').focusout(function(){
            time_inspected();
        })

        /*editDefects(id,pono,partcode);*/

    });// end of script-----------------------------------------------
    
    // state of mode of defects
    function init_mod() {
        if ($('#lot_accepted').val() == '') {
            $('#no_of_defects_label').hide();
            $('#no_of_defects').hide();
            $('#btn_mode_of_defects_label').hide();
            $('#btn_mode_of_defects').hide();
            $('#btn_ndf').hide();
        }
        else if ($('#lot_accepted').val() > 0) {
            $('#no_of_defects_label').hide();
            $('#no_of_defects').hide();
          /*  $('#btn_mode_of_defects_label').show();
            $('#btn_mode_of_defects').hide();
            $('#btn_ndf').show();*/
        }
        else if ($('#lot_accepted').val() < 1) {
            $('#no_of_defects_label').show();
            $('#no_of_defects').show();
            $('#btn_mode_of_defects_label').show();
            $('#btn_ndf').hide();
            $('#btn_mode_of_defects').show();
        }
    }

    // getting the customer
    function getCust(custcode) {
        var url = "{{url('/getcustoqcmolding')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            custcode: custcode
        };
        $.ajax({
            url: url,
            type: "GET",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            $.each(data, function(i,x) {
                $('#customer').val(x.CNAME);
            });
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while processing.");
        });
    }

    // generating the table for lot number
    function getTableLotno() {
        var tblforLot = '';
        var po = $('#po_no').val();
        var url = "{{url('/getlotnoqcmolding')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            po: po
        };
        $.ajax({
            url: url,
            type: "GET",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            $('#tblforLot').empty();
            $.each(data, function(i, x) {
                tblforLot = '<tr>'+
                                '<td>'+x.lot_no+'</td>'+
                                '<td>'+x.qty+'</td>'+
                                '<td>'+
                                    '<a href="javascript:;" class="btn input-sm red btn_del_lot" data-id="'+x.id+'">'+
                                        '<i class="fa fa-trash"></i>'+
                                    '</a>'+
                                '</td>'+
                            '</tr>';
                $('#tblforLot').append(tblforLot);
            });
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while processing.");
        });
    }

    // get the total qty
    function getTotalQty() {
        var po = $('#po_no').val();
        var url = "{{url('/gettotalqty')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            po: po
        };
        $.ajax({
            url: url,
            type: "GET",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            $('#quantity').val(data);
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while calculating the total quantity.");
        });
    }

    // restart the process of lot number
    function restart_lotno() {
        $('#lot_no').empty();
        $('#lotqty').empty();
        $('#msg_success').modal('hide');
    }

    // insert lot number
    function insert_lot(po,lot_no,qty,total_qty) {
        if (!$.isNumeric(qty) || qty > parseInt(total_qty)) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("Quantity value is numeric only and must not be greater than the total quantity.");
        } else {
            var url = "{{url('/postlotnoqcmolding')}}";
            var token = "{{ Session::token() }}";
            var data = {
                _token: token,
                po: po,
                lot_no: lot_no,
                qty: qty,
                total_qty: total_qty
            };
            $.ajax({
                url: url,
                type: "POST",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                if (data.status == "success") {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                    $('#success_msg').html("Lot Number was successfully saved.");
                    console.log(data);
                    getTableLotno();
                    getTotalQty();
                    $('#lot_no').val('');
                    $('#lotqty').val('');
                }
                if (data.status == "existing") {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#success_msg').html("Lot number was already added.");
                    console.log(data);
                }
                if (data.status == "greater") {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#success_msg').html("You exceeded to the total P.O. quantity.");
                    console.log(data);
                }
                if (data.status == "error") {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#success_msg').html("There's some error while processing.");
                    console.log(data.total);
                }
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
        }
    }

    function delete_lot(id) {
        var url = "{{url('/deletelotnoqcmolding')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            id: id,
        };
        $.ajax({
            url: url,
            type: "POST",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            if (data.status == "success") {
                $('#msg_success').modal('show');
                $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                $('#success_msg').html("Lot Number was successfully deleted.");
                console.log(data);
                getTableLotno();
                getTotalQty();
                $('#lot_no').val('');
                $('#lotqty').val('');
            }
            if (data.status == "error") {
                $('#msg_success').modal('show');
                $('#success_title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#success_msg').html("There's some error while processing.");
                console.log(data.total);
            }
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while processing.");
        });
    }

    function samplingplan() {
        var qty = $('#qty').val();
        var soi = $('#severity_of_inspection').val();
        var ilvl = $('#inspection_lvl').val();
        var aql = parseFloat($('#aql').val());
 /*       var qty = parseInt($('#quantity').val());
*/
        if(qty >= 1 && qty <= 8 && soi == "Tightened"){
            $('#sample_size').val(qty);
        }
        if(qty >= 1 && qty <= 8 && soi == "Normal"){
            $('#sample_size').val(qty);
        }
        if(qty >= 1 && qty <= 8 && soi == "Reduced"){
            $('#sample_size').val(qty);
        }
        if(qty >= 9 && qty <= 15 && soi == "Tightened"){
            $('#sample_size').val(qty);
        }
        if(qty >= 9 && qty <= 15 && soi == "Normal"){
            $('#sample_size').val(qty);
        }
        if(qty >= 9 && qty <= 15 && soi == "Reduced"){
            $('#sample_size').val(8);
        }
        if(qty >= 16 && qty <= 25 && soi == "Tightened"){
            $('#sample_size').val(qty);
        }
        if(qty >= 16 && qty <= 25 && soi == "Normal"){
            $('#sample_size').val(20);
        }
        if(qty >= 16 && qty <= 25 && soi == "Reduced"){
            $('#sample_size').val(8);
        }
        if(qty >= 26 && qty <= 50 && soi == "Tightened"){
            $('#sample_size').val(32);
        }
        if(qty >= 26 && qty <= 50 && soi == "Normal"){
            $('#sample_size').val(20);
        }
        if(qty >= 26 && qty <= 50 && soi == "Reduced"){
            $('#sample_size').val(8);
        }

        if(qty >= 51 && qty <= 280 && soi == "Tightened"){
            $('#sample_size').val(32);
        }
        if(qty >= 51 && qty <= 280 && soi == "Normal"){
            $('#sample_size').val(20);
        }
        if(qty >= 51 && qty <= 280 && soi == "Reduced"){
            $('#sample_size').val(8);
        }

        if(qty >= 281 && qty <= 1200 && soi == "Tightened"){
            $('#sample_size').val("125");
        }
        if(qty >= 281 && qty <= 1200 && soi == "Normal"){
            $('#sample_size').val("80");
        }
        if(qty >= 281 && qty <= 1200 && soi == "Reduced"){
            $('#sample_size').val("32");
        }

        if(qty >= 1201 && qty <= 3200 && soi == "Tightened"){
            $('#sample_size').val(125);
        }
        if(qty >= 1201 && qty <= 3200 && soi == "Normal"){
            $('#sample_size').val(80);
        }
        if(qty >= 1201 && qty <= 3200 && soi == "Reduced"){
            $('#sample_size').val(50);
        }

        if(qty >= 3201 && qty <= 10000 && soi == "Tightened"){
            $('#sample_size').val(200);
        }
        if(qty >= 3201 && qty <= 10000 && soi == "Normal"){
            $('#sample_size').val(125);
        }
        if(qty >= 3201 && qty <= 10000 && soi == "Reduced"){
            $('#sample_size').val(80);
        }

        if(qty >= 10001 && qty <= 35000 && soi == "Tightened"){
            $('#sample_size').val(315);
        }
        if(qty >= 10001 && qty <= 35000 && soi == "Normal"){
            $('#sample_size').val(200);
        }
        if(qty >= 10001 && qty <= 35000 && soi == "Reduced"){
            $('#sample_size').val(125);
        }

        if(qty >= 35001 && qty <= 150000 && soi == "Tightened"){
            $('#sample_size').val(500);
        }
        if(qty >= 35001 && qty <= 150000 && soi == "Normal"){
            $('#sample_size').val(315);
        }
        if(qty >= 35001 && qty <= 150000 && soi == "Reduced"){
            $('#sample_size').val(200);
        }

        if(qty >= 150001 && soi == "Tightened"){
            $('#sample_size').val(800);
        }
        if(qty >= 150001 && soi == "Normal"){
            $('#sample_size').val(500);
        }
        if(qty >= 150001 && soi == "Reduced"){
            $('#sample_size').val(315);
        }

    }

    // save mode of defects
    function save_mod(po,partcode,mod,qty,state,id,submission,lotno) {
        if (!$.isNumeric(qty)) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("Quantity value is numeric only.");
        } else {
            var url = "{{url('/postmod')}}";
            var token = "{{ Session::token() }}";
            var data = {
                _token: token,
                po: po,
                mod: mod,
                qty: qty,
                partcode: partcode,
                state: state,
                id: id,
                submission:submission,
                lotno:lotno
            };
            $.ajax({
                url: url,
                type: "POST",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                if (data.status == "success") {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                    $('#success_msg').html("Mode of defects was successfully added.");
                    console.log(data);
                    getMODtable();
                    getMODTotalQty();
                    $('#mode_of_def').val('');
                    $('#mode_qty').val('');
                    $('#mod_stat').val("ADD");
                    $('#mod_id').val('');
                }
                if (data.status == "success_ndf") {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                    $('#success_msg').html("You assigned an NDF. It means that there are no defects for this Lot.");
                    console.log(data);
                    getMODtable();
                    getMODTotalQty();
                    $('#mode_of_def').val('');
                    $('#mode_qty').val('');
                    $('#btn_ndf').attr('disabled',true);
                    $('#mod_stat').val("ADD");
                    $('#mod_id').val('');
                }
                if (data.status == "error") {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#success_msg').html("There's some error while processing.");
                    console.log(data.total);
                }
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
        }
    }

    //delete mod
    function delete_mod(po,partcode,mod) {
        var url = "{{url('/deletemod')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            po: po,
            partcode: partcode,
            mod: mod
        };
        $.ajax({
            url: url,
            type: "POST",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            if (data.status == "success") {
                $('#msg_success').modal('show');
                $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                $('#success_msg').html("Mode of defect was successfully deleted.");
                console.log(data);
                getMODtable();
                getMODTotalQty();
                $('#mode_of_def').val('');
                $('#mode_qty').val('');
                $('#mod_stat').val("ADD");
                $('#mod_id').val('');
            }
            if (data.status == "error") {
                $('#msg_success').modal('show');
                $('#success_title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#success_msg').html("There's some error while processing.");
                console.log(data.total);
            }
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while processing.");
        });
    }

    function getMODtable() {
        var tblformod = '';
        var po = $('#po_no').val();
        var partcode = $('#part_code').val();
        var url = "{{url('/getmodtbl')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            po: po,
            partcode: partcode
        };
        $.ajax({
            url: url,
            type: "GET",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            $('#tblformod').empty();
            $.each(data, function(i, x) {
                tblformod = '<tr>'+
                                '<td>'+x.description+'</td>'+
                                '<td>'+x.qty+'</td>'+
                                '<td>'+
                                    '<a href="javascript:;" class="btn input-sm red btn_del_MOD" data-mod="'+x.description+'" data-id="'+x.id+'">'+
                                        '<i class="fa fa-trash"></i>'+
                                    '</a>'+
                                    '<button type="button" name="edit-taskmod" class="btn btn-sm btn-primary edit-taskmod" value="'+x.id+ '|' +x.po + '|' +x.partcode + '|' +x.description+ '|' +x.qty+'">'+
                                        '<i class="fa fa-edit"></i> '+
                                    '</button>'+
                                '</td>'+
                            '</tr>';
                $('#tblformod').append(tblformod);
                editDefects();
            });    
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while processing.");
        });
    }

    // get the total qty
    function getMODTotalQty() {
        var po = $('#po_no').val();
        var partcode = $('#part_code').val();
        var url = "{{url('/getmodtotalqty')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            po: po,
            partcode: partcode
        };
        $.ajax({
            url: url,
            type: "GET",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            $('#no_of_defects').val(data);
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while calculating the total quantity.");
        });
    }

    // save oqc record
    function save_oqc() {
        var oqc_status = $('#oqc_status').val();
        var po_no = $('#po_no').val();
        var part_code = $('#part_code').val();
        var part_name = $('#part_name').val();
        var customer = $('#customer').val();
        var lot_qty = $('#lot_qty').val();
        var lot_no = $('#lot_no').val();
        var total_qty = $('#total_qty').val();
        var die_num = $('#die_num').val();
        var quantity = $('#qty').val();
        var type_of_inspection = $('#type_of_inspection').val();
        var severity_of_inspection = $('#severity_of_inspection').val();
        var inspection_lvl = $('#inspection_lvl').val();
        var aql = $('#aql').val();
        var accept = $('#accept').val();
        var reject = $('#reject').val();
        var date_inspected = $('#date_inspected').val();
        var shift = $('#shift').val();
        var inspector = $('#inspector').val();
        var submission = $('#submission').val();
        var visual_operator = $('#visual_operator').val();
        var ww = $('#ww').val();
        var fy = $('#fy').val();
        var ptcp_tnr = $('#ptcp_tnr').val();
        var lot_inspected = $('#lot_inspected').val();
        var lot_accepted = $('#lot_accepted').val();
        var sample_size = $('#sample_size').val();
        var judgement = $('#judgement').val();
        var no_of_defects = $('#no_of_defects').val();
        var family = $('#family').val();
        var remarks = $('#remarks').val();
        var id = $('#oqc_id').val();
        var from = $('#start_time').val();


      /*  if(po_no == '' || part_code == '' || part_name == '' || customer == '' || lot_no == '' || total_qty == '' || die_num == '' || 
            quantity == '' || type_of_inspection == '' || severity_of_inspection == '' || inspection_lvl == '' || 
            aql == '' || accept == '' || reject == '' || date_inspected == '' || shift == '' || inspector == '' || 
            submission == '' || visual_operator == '' || ww == '' || fy == '' || ptcp_tnr == '' || lot_inspected == '' || 
            lot_accepted == '' || sample_size == '' || judgement == '') {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("Some input needed to fill out.");
        } else {*/
            var url = "{{url('/saveoqc')}}";
            var token = "{{ Session::token() }}";
            var data = {
                _token: token,
                id : id,
                po_no : po_no,
                part_code : part_code,
                part_name : part_name,
                customer : customer,
                lot_qty : lot_qty,
                lot_no : lot_no,
                total_qty : total_qty,
                die_num : die_num,
                quantity : quantity,
                type_of_inspection : type_of_inspection,
                severity_of_inspection : severity_of_inspection,
                inspection_lvl : inspection_lvl,
                aql : aql,
                accept : accept,
                reject : reject,
                date_inspected : date_inspected,
                shift : shift,
                inspector : inspector,
                submission : submission,
                visual_operator : visual_operator,
                ww : ww,
                fy : fy,
                ptcp_tnr : ptcp_tnr,
                lot_inspected : lot_inspected,
                lot_accepted : lot_accepted,
                sample_size : sample_size,
                judgement : judgement,
                no_of_defects : no_of_defects,
                family : family,
                remarks : remarks,
                status : oqc_status,
                from : from
            };
            $.ajax({
                url: url,
                type: "POST",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                if (data.status == 'success') {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                    $('#success_msg').html("Record was successfully saved.");
                    $('#oqc_status').val('insert');
                    $('.clear').val('');
                    $('#oqc_id').val('');
                    loadOQC();
                    getTime();
                    init_mod();
                    $('#btn_lot').attr('disabled',true);
                }

                if (data.status == 'update_success') {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                    $('#success_msg').html("Record was successfully updated.");
                    window.location.href = "{{ url('/oqcmolding') }}";
                    $('#oqcdatatable').DataTable();
                }

                if (data.status == 'error') {
                    $('#msg').modal('show');
                    $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#err_msg').html("There's some error while processing.");
                }
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
   /*     }*/
    }

    function getmodcounts(pono,lotno,subs,judgement,cnt) {
        var report_status = $('#hd_report_status').val();
        var datefrom = $('#hd_search_from').val();
        var dateto = $('#hd_search_to').val();
        $.ajax({
            url:"{{ url('/getmoldmodcounts') }}",
            method:'get',
            data:{

                pono: pono,
                lotno: lotno,
                subs: subs,
                report_status:report_status,
                datefrom:datefrom,
                dateto:dateto,
            },
        }).done(function(data,textStatus,jqXHR){
            console.log(data.mod);
            var x = 0;

            if(judgement == "Accepted"){
                $('#md_'+cnt).html("NDF");  
               /* $("#hd_mod_"+cnt).val("NDF");*/
                $("#gb_qty_"+cnt).html(0);
                $("#gb_nod_"+cnt).html(0);
                $("#gb_lot_"+cnt).html(""); 
                $("#hd_qty"+cnt).val(0); 
                $("#hd_num_of_defects"+cnt).val(0); 
            }

            $.each(data.mod, function(i,val) {
                x++;
                var mod = '';
                if(x == data.mod.length){
                    var mod = val + ' ';   

                }else{
                    var mod = val + ' , ';  
                }
           
                if(judgement == "Accepted"){
                    $('#md_'+cnt).html("NDF"); 
                } else {
                    $('#md_'+cnt).append(mod);  
                }
          
            });

            $.each(data.lotno, function(i,val) {
                x++;
                var lot = '';
                if(x == data.lotno.length){
                    var lot = val + ' ';   
                }else{
                    var lot = val + ' , ';   
                }
            
               /* var found = $.inArray('NDF', data.mod) > -1;*/
             
                if(judgement == "Accepted"){
                    $('#gb_lot_'+cnt).html("");   
                } else {
                    $('#gb_lot_'+cnt).append(lot);    
                }
          
            });
        }).fail(function(jqXHR,textStatus,errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });
    }

    // load table oqc
    function loadOQC() {
        $('#loading').modal('show');
        var oqctable = '';
        var po = $('#posearch').val();
        var oqcstat = $('#oqc_status').val();
        var from = $('#from').val();
        var to = $('#to').val();
        var url = "{{url('/getloadoqc')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            po: po,
            from: from,
            to: to,
            oqcstat:oqcstat
        };
        $.ajax({
            url: url,
            type: "GET",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            $('#loading').modal('hide');
            $('#oqctable').empty();
            $('#hd_report_status').val("");
            var cnt = 0;
            getDataTable(data);
            
            $('#record_count').val(cnt);
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while calculating the total quantity.");
        });
    }

    function loadOQCSEARCH(){
        $('#loading').modal('show');
        var oqctable = '';
        var po = $('#posearch').val();
        var oqcstat = $('#oqc_status').val();
        var from = $('#from').val();
        var to = $('#to').val();
        var url = "{{url('/getloadoqc')}}";
        var token = "{{ Session::token() }}";
        var data = {
            _token: token,
            po: po,
            from: from,
            to: to,
            oqcstat:oqcstat
        };
        $.ajax({
            url: url,
            type: "GET",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
            $('#loading').modal('hide');
            $('#oqctable').empty();
            $('#hd_report_status').val("SEARCH");
            var cnt = 0;
            getDataTable(data);
            
            $('#record_count').val(cnt);
        }).fail( function(data, textStatus, jqXHR) {
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while calculating the total quantity.");
        });
    }

    function getDataTable(data){
        var cnt = 0;
        $.each(data, function(i, x) {
            cnt++;
            var report_status = $('#hd_report_status').val();
            if(report_status == "GROUPBY"){
                var qty = '';
                if(x.qty == null){
                    qty = 0;
                }else{
                    qty = x.qty;
                }
                oqctable = '<tr>'+
                    '<td>'+
                        '<button type="button" class="btn btn-sm btn-primary update-oqc" value="'+x.id+'|'+x.po_no+'|'+x.partcode+'|'+x.partname+'|'+x.customer+'|'+x.family+'|'+x.total_qty+'|'+x.die_no+'|'+x.qty+'|'+x.lot_qty+'|'+x.lot_no+'|'+x.type_of_inspection+'|'+x.severity_of_inspection+'|'+x.inspection_lvl+'|'+x.aql+'|'+x.accept+'|'+x.reject+'|'+x.date_inspected+'|'+x.shift+'|'+x.inspector+'|'+x.submission+'|'+x.visual_operator+'|'+x.fy_no+'|'+x.ww_no+'|'+x.remarks+'|'+x.ptcp_tnr+'|'+x.lot_inspected+'|'+x.lot_accepted+'|'+x.lot_rejected+'|'+x.sample_size+'|'+x.judgement+'|'+x.from+'|'+x.to+'|'+x.num_of_defectives+'|'+x.dbcon+'">'+
                               '<i class="fa fa-edit"></i> '+
                        '</button>'+
                    '</td>'+
                    '<td>'+
                        '<a href="javascript:;" class="btn btn-sm btn-danger delete-oqc" data-id="'+x.id+'">'+
                               '<i class="fa fa-trash"></i>'+
                        '</a>'+
                    '</td>'+
                    '<td>'+x.id+'<input type="hidden" id="hd_id" name="hd_id[]" value="'+x.id+'"><input type="hidden" id="hd_pono" value="'+x.po_no+'" name="hd_pono[]"></td>'+
                    '<td>'+x.date_inspected+'<input type="hidden" id="hd_date_inspected" name="hd_date_inspected[]" value="'+x.date_inspected+'"></td>'+
                    '<td>'+x.shift+'<input type="hidden" id="hd_shift" name="hd_shift[]" value="'+x.shift+'"></td>'+
                    '<td>'+x.from+'<input type="hidden" id="hd_from" name="hd_from[]" value="'+x.from+'"></td>'+
                    '<td>'+x.to+'<input type="hidden" id="hd_to" name="hd_to[]" value="'+x.to+'"></td>'+
                    '<td>'+x.submission+'<input type="hidden" id="hd_submission" name="hd_submission[]" value="'+x.submission+'"></td>'+
                    '<td id="gb_lot_'+cnt+'" class="lot_val"></td>'+
                    '<td>'+x.lot_qty+'<input type="hidden" id="hd_lotqty" name="hd_lotqty[]" value="'+x.lot_qty+'"></td>'+
                    '<td>'+x.sample_size+'<input type="hidden" id="hd_sample_size" name="hd_sample_size[]" value="'+x.sample_size+'"><input type="hidden" id="hd_lotno" value="" name="hd_lotno[]"></td>'+
                    '<td id="gb_nod_'+cnt+'">'+qty+'</td>'+
                    '<td id="md_'+cnt+'" class="mod_val"></td>'+
                    '<td id="gb_qty_'+cnt+'">'+qty+'</td>'+
                    '<td>'+x.judgement+'<input type="hidden" id="hd_judgement" name="hd_judgement[]" value="'+x.judgement+'"></td>'+
                    '<td>'+x.ptcp_tnr+'<input type="hidden" id="hd_ptcp_tnr" name="hd_ptcp_tnr[]" value="'+x.ptcp_tnr+'"></td>'+
                    '<td>'+x.visual_operator+'<input type="hidden" id="hd_inspector" name="hd_inspector[]" value="'+x.visual_operator+'"></td>'+
                    '<td>'+x.remarks+'<input type="hidden" id="hd_remarks" name="hd_remarks[]" value="'+x.remarks+'"></td>'+
               '</tr>';
            }else{
                oqctable = '<tr>'+
                    '<td>'+
                        '<button type="button" class="btn btn-sm btn-primary update-oqc" value="'+x.id+'|'+x.po_no+'|'+x.partcode+'|'+x.partname+'|'+x.customer+'|'+x.family+'|'+x.total_qty+'|'+x.die_no+'|'+x.qty+'|'+x.lot_qty+'|'+x.lot_no+'|'+x.type_of_inspection+'|'+x.severity_of_inspection+'|'+x.inspection_lvl+'|'+x.aql+'|'+x.accept+'|'+x.reject+'|'+x.date_inspected+'|'+x.shift+'|'+x.inspector+'|'+x.submission+'|'+x.visual_operator+'|'+x.fy_no+'|'+x.ww_no+'|'+x.remarks+'|'+x.ptcp_tnr+'|'+x.lot_inspected+'|'+x.lot_accepted+'|'+x.lot_rejected+'|'+x.sample_size+'|'+x.judgement+'|'+x.from+'|'+x.to+'|'+x.num_of_defectives+'|'+x.dbcon+'">'+
                               '<i class="fa fa-edit"></i> '+
                        '</button>'+
                    '</td>'+
                    '<td>'+
                        '<a href="javascript:;" class="btn btn-sm btn-danger delete-oqc" data-id="'+x.id+'">'+
                               '<i class="fa fa-trash"></i>'+
                        '</a>'+
                    '</td>'+
                    '<td>'+x.id+'<input type="hidden" id="hd_id" name="hd_id[]" value="'+x.id+'"><input type="hidden" id="hd_pono" value="'+x.po_no+'" name="hd_pono[]"></td>'+
                    '<td>'+x.date_inspected+'<input type="hidden" id="hd_date_inspected" name="hd_date_inspected[]" value="'+x.date_inspected+'"></td>'+
                    '<td>'+x.shift+'<input type="hidden" id="hd_shift" name="hd_shift[]" value="'+x.shift+'"></td>'+
                    '<td>'+x.from+'<input type="hidden" id="hd_from" name="hd_from[]" value="'+x.from+'"></td>'+
                    '<td>'+x.to+'<input type="hidden" id="hd_to" name="hd_to[]" value="'+x.to+'"></td>'+
                    '<td>'+x.submission+'<input type="hidden" id="hd_submission" name="hd_submission[]" value="'+x.submission+'"></td>'+
                    '<td>'+x.lot_no+'<input type="hidden" id="hd_lotno" name="hd_lotno[]" value="'+x.lot_no+'"></td>'+
                    '<td>'+x.lot_qty+'<input type="hidden" id="hd_lotqty" name="hd_lotqty[]" value="'+x.lot_qty+'"></td>'+
                    '<td>'+x.sample_size+'<input type="hidden" id="hd_sample_size" name="hd_sample_size[]" value="'+x.sample_size+'"></td>'+
                    '<td>'+x.num_of_defectives+'<input type="hidden" id="hd_nod" name="hd_nod[]" value="'+x.num_of_defectives+'"></td>'+
                    '<td id="md_'+cnt+'" class="mod_val"></td>'+
                    '<td>'+x.qty+'<input type="hidden" id="hd_qty" name="hd_qty[]" value="'+x.qty+'"></td>'+
                    '<td>'+x.judgement+'<input type="hidden" id="hd_judgement" name="hd_judgement[]" value="'+x.judgement+'"></td>'+
                    '<td>'+x.ptcp_tnr+'<input type="hidden" id="hd_ptcp_tnr" name="hd_ptcp_tnr[]" value="'+x.ptcp_tnr+'"></td>'+
                    '<td>'+x.visual_operator+'<input type="hidden" id="hd_inspector" name="hd_inspector[]" value="'+x.visual_operator+'"></td>'+
                    '<td>'+x.remarks+'<input type="hidden" id="hd_remarks" name="hd_remarks[]" value="'+x.remarks+'"></td>'+
               '</tr>';    
            }
            getmodcounts(x.po_no,x.lot_no,x.submission,x.judgement,cnt)
            $('#oqctable').append(oqctable);
        });
    }

    function formatAMPM(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var sec = date.getSeconds();
        var ampm = hours >= 12 ? 'AM' : 'PM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ':' + sec + ' ' + ampm;
        return strTime;
    }

    function getTime() {
        var d = new Date();
        var time = formatAMPM(d);

        $('#start_time').val(time);
    }

    function groupby(){
        var datefrom= $('#groupby_datefrom').val();
        var dateto = $('#groupby_dateto').val();
  
        var g1 = $('select[name=group1]').val();
        var g2 = $('select[name=group2]').val();
        var g3 = $('select[name=group3]').val();
        var g4 = $('select[name=group4]').val();
        var g5 = $('select[name=group5]').val();
        var g1content = $('select[name=group1content]').val();
        var g2content = $('select[name=group2content]').val();
        var g3content = $('select[name=group3content]').val();
        var g4content = $('select[name=group4content]').val();
        var g5content = $('select[name=group5content]').val();

        var myData = {g1:g1,g2:g2,g3:g3,g4:g4,g5:g5,g1content:g1content,g2content:g2content,g3content:g3content,g4content:g4content,g5content:g5content,datefrom:datefrom,dateto:dateto};
        $('#oqctable').empty();
        $.post("{{ url('/oqcmoldgroupby') }}",
        {
                _token:$('meta[name=csrf-token]').attr('content'),
                data:myData
        }).done(function(data, textStatus, jqXHR){
            getDataTable(data);
            //get the LAR/LRR/DPPM-----------------
            $('#tblforlarlrrdppm').html("");
            $.ajax({
                url:"{{ url('/getoqcmoldlarlrrdppm') }}",
                method:'get',
                data:myData,
            }).done(function(data, textStatus, jqXHR){
            
                var x =  0;
                var y =  0;
                var z =  0;
                var selected =[];
                var selected2 =[];
                var selected3 =[];
            
                if(g1){
                    for(x;x<data.length;x++){
                        selected.push(data[x][g1]);
                    }   
                }
                if(g2){
                    for(y;y<data.length;y++){
                        selected2.push(data[y][g2]);
                    }   
                }
                if(g3){
                    for(z;z<data.length;z++){
                        selected3.push(data[z][g3]);
                    }   
                }
                
                var sample_size = [];
                var lot_qty = [];
                var no_of_defects = [];
                
                $('#hdg1_selected').val(selected);
                $('#hdg2_selected').val(selected2);
                $('#hdg3_selected').val(selected3);
                var la = 0;
                var lr = 0;
                $.each(data,function(i,val){
                    sample_size = val.sample_size;
                    lot_qty = val.lot_qty;
                    no_of_defects = val.num_of_defects;
                    no_of_lot_accepted = val.lot_accepted;
                    no_of_lot_rejected = val.lot_rejected;
                    no_of_lot_inspected = val.lot_inspected;
                    
                    //getting the lar value-------
                    var templar = no_of_lot_accepted / no_of_lot_inspected;
                    var lar = (templar * 100).toFixed(2);
                    //getting the lrr value-------
                    var templrr = no_of_lot_rejected / no_of_lot_inspected;
                    var lrr = (templrr * 100).toFixed(2);

                    //getting the dppm value-------
                    if(no_of_defects == 0 && sample_size == 0){
                        var noddivss = 0;   
                    }else if(sample_size == 0){
                        var noddivss = 0;
                    }else if(no_of_defects == 0){
                        var noddivss = 0;
                    }else{
                        var noddivss = no_of_defects/sample_size;   
                    }
                    var dppm = (noddivss * 1000000).toFixed(2);

                    //getting the lot_accepted
                    
                    var newselected = $('#hdg1_selected').val().split(',');
                    var newselected2 = $('#hdg2_selected').val().split(',');
                    var newselected3 = $('#hdg2_selected').val().split(',');
            
                    if(g1){
                        var finalselected = newselected[i];
                    }
                    if(g2){
                        var finalselected = newselected[i]+' - '+ newselected2[i];
                    }
                    if(g3){
                        var finalselected = newselected[i]+' - '+newselected2[i]+' - '+newselected3[i];
                    }
                    /*var reject = val.lot_inspected - val.lot_accepted;*/
                    var tblrow = '<tr>'+  
                                '<td id="groupbyselected">'+finalselected+'</td>'+ 
                                '<td>'+val.lot_inspected+'</td>'+
                                '<td>'+val.lot_accepted+'</td>'+
                                '<td>'+val.lot_rejected+'</td>'+  
                                '<td>'+val.sample_size+'</td>'+
                                '<td>'+val.num_of_defects+'</td>'+
                                '<td>'+lar+'</td>'+
                                '<td>'+lrr+'</td>'+
                                '<td>'+dppm+'</td>'+
                            '</tr>';
                    $('#tblforlarlrrdppm').append(tblrow);
                
                });    
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });

            $('#tblfortotallarlrrdppm').html("");
            $.ajax({
                url:"{{ url('/getoqcmoldtotallarlrrdppm') }}",
                method:'get',
                data:myData,
            }).done(function(data, textStatus, jqXHR){
                $.each(data,function(i,val){
                    sample_size = val.sample_size;
                    lot_qty = val.lot_qty;
                    no_of_defects = val.num_of_defects;
                    no_of_lot_accepted = val.lot_accepted;
                    no_of_lot_rejected = val.lot_rejected;
                    no_of_lot_inspected = val.lot_inspected;
                    
                    //getting the lar value-------
                    var templar = no_of_lot_accepted / no_of_lot_inspected;
                    var lar = (templar * 100).toFixed(2);
                    //getting the lrr value-------
                    var templrr = no_of_lot_rejected / no_of_lot_inspected;
                    var lrr = (templrr * 100).toFixed(2);

                    //getting the dppm value-------
                    if(no_of_defects == 0 && sample_size == 0){
                        var noddivss = 0;   
                    }else if(sample_size == 0){
                        var noddivss = 0;
                    }else if(no_of_defects == 0){
                        var noddivss = 0;
                    }else{
                        var noddivss = no_of_defects/sample_size;   
                    }
        
                    var dppm = (noddivss * 1000000).toFixed(2);
                    var tblrow = '<tr>'+  
                                '<td id="groupbyselected">'+val.submission+'</td>'+ 
                                '<td>'+val.lot_inspected+'</td>'+
                                '<td>'+val.lot_accepted+'</td>'+
                                '<td>'+val.lot_rejected+'</td>'+  
                                '<td>'+val.sample_size+'</td>'+
                                '<td>'+val.num_of_defects+'</td>'+
                                '<td>'+lar+'</td>'+
                                '<td>'+lrr+'</td>'+
                                '<td>'+dppm+'</td>'+
                            '</tr>';
                    $('#tblfortotallarlrrdppm').append(tblrow);
                });
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });

        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });
    }

    function time_inspected(){
        var timefrom = $('#time_ins_from').val();
        var timeto = $('#time_ins_to').val();
        var url = "{{ url('/moldtime') }}";
        $.ajax({
            url:url,
            method:'get',
            data:{
                timefrom:timefrom,
                timeto:timeto
            },
        }).done(function(data,textStatus,jqXHR){
            $('#shift').val(data);
        }).fail(function(jqXHR,textStatus,errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });
    }

    function editDefects(/*id,pono,partcode*/){
        $('.edit-taskmod').on('click',function(){
            var field = $(this).val().split('|');
            var id = field[0];
            var pono = field[1];
            var partcode = field[2];
            var description = field[3];
            $.ajax({
                url:"{{ url('/editDefects') }}",
                method:'get',
                data:{
                    id:id,
                    pono:pono,
                    partcode:partcode,
                    description:description
                },
            }).done(function(data, textStatus, jqXHR){
                console.log(data);
                $('#mode_of_def').val(data[0]['description']);
                $('#mode_qty').val(data[0]['qty']);
                $('#mod_stat').val("EDIT");
                $('#mod_id').val(data[0]['id']);
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });
        });
    }
</script>
@endpush
