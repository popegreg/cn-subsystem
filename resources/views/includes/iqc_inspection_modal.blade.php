<!-- IQC RESULT MODAL -->
<div id="IQCresultModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery modal-xl">
		<div class="modal-content ">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">IQC Inspection Result</h4>
			</div>
			<form class=form-horizontal id="frm_iqc_inspection">
				{{ csrf_field() }}
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							{{-- <div class="form-group">
								<label class="control-label col-sm-3">Classification</label>
								<div class="col-sm-9"> --}}
									<input type="hidden" id="classification" name="classification" value="Appearance Inspection">
									{{-- <select class="form-control input-sm clear" id="classification" name="classification">
										<option value=""></option>
										<option value="Visual Inspection">Visual Inspection (Temporary Invoice)</option>
										<option value="Pkg. & Raw Material">Packaging & Raw Material</option>
										<option value="Material Qualification">Material Qualification</option>
									</select> --}}
								{{-- </div>
							</div> --}}
							<div class="form-group has-feedback">
								<label class="control-label col-sm-3">Invoice No.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clear" id="invoice_no" name="invoice_no">
									<input type="hidden" class="form-control input-sm clear" id="iqc_result_id" name="iqc_result_id">
									{{-- <span class="fa fa-pulse fa-spinner form-control-feedback"></span> --}}
									{{-- <input type="hidden" class="form-control input-sm clear" id="classification" name="classification" value="Visual Inspection"> --}}
									<div id="er_invoice_no" style="color: #f24848; font-weight: 900"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Part Code</label>
								<div class="col-sm-9" id="partcodelbl_div">
									<input type="text" class="form-control input-sm clear" id="partcodelbl" name="partcodelbl" <?php echo($state);?>>
								</div>
								<div class="col-sm-9" id="partcode_div">
                                    <input type="text" id="partcode" name="partcode" class="form-control input-sm clear clearselect" <?php echo($state);?>>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Part Name</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="partname" name="partname">
									<div id="er_partname"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Family</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="family" name="family" >
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Supplier</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="supplier" name="supplier" >
									<div id="er_supplier"></div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
                                <label class="control-label col-sm-3"></label>
                                <div class="md-checkbox-inline">
                                    <div class="md-checkbox">
                                        <input type="checkbox" id="is_batching" class="md-check" name="is_batching">
                                        <label for="is_batching">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                        Check for Batching </label>
                                    </div>
                                </div>
                            </div>
							<div class="form-group">
								<label class="control-label col-sm-3">Application Date</label>
								<div class="col-sm-9">
									<input class="form-control input-sm clear" type="text" name="app_date" id="app_date" value="{{date('m/d/Y')}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Application Time</label>
								<div class="col-sm-9">
									<input type="text" data-format="h:m A" class="form-control input-sm clear" name="app_time" id="app_time" value="{{date('h:i A')}}">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Application Ctrl No.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clear" id="app_no" name="app_no">
									<div id="er_app_no"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot No.</label>
								<div class="col-sm-9">
									<input type="text" name="lot_no" id="lot_no" class="form-control required input-sm lot_no clear clearselect">
									<!-- </select> -->
									<div id="er_lot_no"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot Quantity</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="lot_qty" name="lot_qty">
									<div id="er_lot_qty"></div>
								</div>
							</div>
						</div>

					</div>

					<hr>

					<div class="row">
						<div class="col-sm-12">
							<strong>Sampling Plan</strong>
						</div>
					</div>


					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Type of Inspection</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clearselect show-tick actual" name="type_of_inspection" id="type_of_inspection">
									<div id="er_type_of_inspection"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Severity of Inspection</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clearselect show-tick actual" name="severity_of_inspection" id="severity_of_inspection">
									<div id="er_severity_of_inspection"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Inspection Level</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clearselect show-tick actual" name="inspection_lvl" id="inspection_lvl">
									<div id="er_inspection_lvl"></div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">AQL</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clearselect show-tick actual" name="aql" id="aql">
									<div id="er_aql"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Accept</label>
								<div class="col-sm-9">
									<input type="number" min="0" max="1" class="form-control input-sm clear actual" id="accept" name="accept">
									<div id="er_accept"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Reject</label>
								<div class="col-sm-9">
									<input type="number" min="0" max="1" class="form-control input-sm clear actual" id="reject" name="reject">
									<div id="er_reject"></div>
								</div>
							</div>
						</div>

					</div>

					<hr>

					<div class="row">
						<div class="col-sm-12">
							<strong>Visual Inspection</strong>
						</div>
					</div>


					<div class="row">

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Date Inspected</label>
								<div class="col-sm-9">
									<input class="form-control required input-sm clear date-picker actual" type="text" name="date_inspected" id="date_inspected" data-date-format='yyyy-mm-dd'/>
									<div id="er_date_ispected"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">WW#</label>
								<div class="col-sm-3">
									<input type="text" class="form-control input-sm clear actual" id="ww" name="ww">
									<div id="er_ww"></div>
								</div>
								<label class="control-label col-sm-3">FY#</label>
								<div class="col-sm-3">
									<input type="text" class="form-control input-sm clear actual" id="fy" name="fy">
									<div id="er_fy"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Time Inspected</label>
								<div class="col-sm-4">
									<input type="text" class="form-control required input-sm actual" name="time_ins_from" id="time_ins_from"/> {{-- timepicker timepicker-no-seconds --}}
									<div id="er_time_ins_from"></div>
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-4">
									<input type="text" class="form-control required input-sm actual" name="time_ins_to" id="time_ins_to"/> {{-- timepicker timepicker-no-seconds --}}
									<div id="er_time_ins_to"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Shift</label>
								<div class="col-sm-9">
									<select class="form-control required input-sm clearselect actual" name="shift" id="shift">
										<option value=""></option>
										<option value="Shift A">Shift A</option>
										<option value="Shift B">Shift B</option>
									</select>
									<div id="er_shift"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Inspector</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm actual" id="inspector" name="inspector" value="{{ Auth::user()->user_id }}">
									<div id="er_inspector"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Submission</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clearselect show-tick actual" name="submission" id="submission">
									<div id="er_submission"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Judgement</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="judgement" name="judgement" readonly>
									<div id="er_judgement"></div>
									<label class="text-success" id="msg_special_accept" style="margin-top:10px;" hidden>Special Accept</label>
								</div>
							</div>
						</div>

						<div class="col-md-6">

							<div class="form-group">
								<label class="control-label col-sm-3">Lot Inspected</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clear actual" id="lot_inspected" name="lot_inspected">
									<div id="er_lot_inspected"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot Accepted</label>
								<div class="col-sm-9">
									<input type="text" class="form-control required input-sm clear actual" id="lot_accepted" name="lot_accepted">
									<div id="er_lot_accepted"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Sample Size</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="sample_size" name="sample_size" readonly>
									<div id="er_sample_size"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" id="no_defects_label">No. of Defectives</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="no_of_defects" name="no_of_defects">
									<div id="er_no_of_defects"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Remarks</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="remarks" name="remarks">
									<input type="hidden" class="form-control input-sm clear" id="inspectionstatus" name="inspectionstatus">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" id="mode_defects_label">Mode of Defects</label>
								<div class="col-sm-4">
									<button type="button" class="btn blue btn_mod_ins" id="btn_mod_ins">
                                        <i class="fa fa-plus-circle"></i> Add Mode of Defects
                                    </button>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-3">
									<input type="hidden" name="save_status" id="save_status">
									<input type="hidden" name="batching" id="batching">
									<input type="hidden" name="inv_id" id="inv_id">
									<input type="hidden" name="mr_id" id="mr_id">
								</div>
							</div>
						</div>

					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary hidden" id="btn_special_accept">
						<i class="fa fa-check"></i> Special Accept
					</button>
					<button type="button" onclick="javascript:saveInspection();" class="btn btn-success" id="btn_save_inspection">
						<i class="fa fa-floppy-o"></i> Save
					</button>
					<button type="button" class="btn grey-gallery btn_clear" id="btn_clear_inspection">
						<i class="fa fa-eraser"></i> Clear
					</button>
					<a href="javascript:;" data-dismiss="modal"  class="btn btn-danger btn_backModal"><i class="fa fa-reply"></i>Back</a>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- IQC RESULT MODAL -->
<div id="ManualModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery modal-xl">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">IQC Inspection Result</h4>
			</div>
			<form class=form-horizontal>
				 <div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Classification</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="classification_man" name="classification_man" >
									{{-- <select class="form-control input-sm clear" id="classification_man" name="classification_man">
										<option value=""></option>
										<option value="Visual Inspection">Visual Inspection (Temporary Invoice)</option>
										<option value="Pkg. & Raw Material">Packaging & Raw Material</option>
										<option value="Material Qualification">Material Qualification</option>
									</select> --}}
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Invoice No.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="invoice_no_man" name="invoice_no_man">
									<input type="hidden" class="form-control input-sm clear" id="iqc_result_id_man" name="iqc_result_id_man">

									<div id="er_invoice_no_man" style="color: #f24848; font-weight: 900"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Part Code</label>
								<div class="col-sm-9">
									<!-- <input type="text" class="form-control input-sm clear" id="partcode" name="partcode"> -->
                                    <input type="text" id="partcode_man" name="partcode_man" class="form-control input-sm clear clearselect" <?php echo($state);?>>
									<!-- <select class="form-control select2me input-sm clear" id="partcode" name="partcode">
									</select> -->
									<div id="er_partcode"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Part Name</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="partname_man" name="partname_man">
									<div id="er_partname"></div>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-3">Family</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="family_man" name="family_man" >
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Supplier</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="supplier_man" name="supplier_man" >
									<div id="er_supplier"></div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Application Date</label>
								<div class="col-sm-9">
									<input class="form-control input-sm clear" type="text" name="app_date_man" id="app_date_man" value="{{date('m/d/Y')}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Application Time</label>
								<div class="col-sm-9">
									<input type="text" data-format="h:m A" class="form-control input-sm clear" name="app_time_man" id="app_time_man" value="{{date('H:i A')}}">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Application Ctrl No.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="app_no_man" name="app_no_man">
									<div id="er_app_no"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot No.</label>
								<div class="col-sm-9">
									<input type="text" name="lot_no_man" id="lot_no_man" class="form-control input-sm lot_no clear clearselect">
									<!-- </select> -->
									<div id="er_lot_no"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot Quantity</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="lot_qty_man" name="lot_qty_man">
									<div id="er_lot_qty"></div>
								</div>
							</div>
						</div>

					</div>

					<hr>

					<div class="row">
						<div class="col-sm-12">
							<strong>Sampling Plan</strong>
						</div>
					</div>


					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Type of Inspection</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clearselect show-tick actual" name="type_of_inspection_man" id="type_of_inspection_man">
									<div id="er_type_of_inspection"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Severity of Inspection</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clearselect show-tick actual" name="severity_of_inspection_man" id="severity_of_inspection_man">
									<div id="er_severity_of_inspection"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Inspection Level</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clearselect show-tick actual" name="inspection_lvl_man" id="inspection_lvl_man">
									<div id="er_inspection_lvl"></div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">AQL</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clearselect show-tick actual" name="aql_man" id="aql_man">
									<div id="er_aql"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Accept</label>
								<div class="col-sm-9">
									<input type="number" min="0" max="1" class="form-control input-sm clear actual" id="accept_man" name="accept_man">
									<div id="er_accept"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Reject</label>
								<div class="col-sm-9">
									<input type="number" min="0" max="1" class="form-control input-sm clear actual" id="reject_man" name="reject_man">
									<div id="er_reject"></div>
								</div>
							</div>
						</div>

					</div>

					<hr>

					<div class="row">
						<div class="col-sm-12">
							<strong>Visual Inspection</strong>
						</div>
					</div>


					<div class="row">

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Date Inspected</label>
								<div class="col-sm-9">
									<input class="form-control input-sm clear date-picker actual" type="text" name="date_inspected_man" id="date_inspected_man" data-date-format='yyyy-mm-dd'/>
									<div id="er_date_ispected"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">WW#</label>
								<div class="col-sm-3">
									<input type="text" class="form-control input-sm clear actual" id="ww_man" name="ww_man">
									<div id="er_ww"></div>
								</div>
								<label class="control-label col-sm-3">FY#</label>
								<div class="col-sm-3">
									<input type="text" class="form-control input-sm clear actual" id="fy_man" name="fy_man">
									<div id="er_fy"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Time Inspected</label>
								<div class="col-sm-4">
									<input type="text" data-format="hh:mm A" class="form-control input-sm actual" name="time_ins_from_man" id="time_ins_from_man" value="{{date('h:i A')}}"/> {{-- timepicker timepicker-no-seconds --}}
									<div id="er_time_ins_from"></div>
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-4">
									<input type="text" data-format="hh:mm A" class="form-control input-sm actual" name="time_ins_to_man" id="time_ins_to_man"  value="{{date('h:i A')}}"/> {{-- timepicker timepicker-no-seconds --}}
									<div id="er_time_ins_to"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Shift</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clearselect show-tick actual" name="shift_man" id="shift_man">
									<div id="er_shift"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Inspector</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm actual" id="inspector_man" name="inspector_man" value="{{ Auth::user()->user_id }}">
									<div id="er_inspector"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Submission</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clearselect show-tick actual" name="submission_man" id="submission_man">
									<div id="er_submission"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Judgement</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="judgement_man" name="judgement_man" readonly>
									<div id="er_judgement"></div>
								</div>
							</div>
						</div>

						<div class="col-md-6">

							<div class="form-group">
								<label class="control-label col-sm-3">Lot Inspected</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="lot_inspected_man" name="lot_inspected_man">
									<div id="er_lot_inspected"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot Accepted</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="lot_accepted_man" name="lot_accepted_man">
									<div id="er_lot_accepted"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Sample Size</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="sample_size_man" name="sample_size_man" readonly>
									<div id="er_sample_size"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" id="no_defects_label_man">No. of Defectives</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="no_of_defects_man" name="no_of_defects_man">
									<div id="er_no_of_defects"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Remarks</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear actual" id="remarks_man" name="remarks_man">
									<input type="hidden" class="form-control input-sm clear" id="inspectionstatus_man" name="inspectionstatus_man">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" id="mode_defects_label_man">Mode of Defects</label>
								<div class="col-sm-4">
									<button type="button" class="btn blue btn_mod_ins" id="btn_mod_ins_man">
                                        <i class="fa fa-plus-circle"></i> Add Mode of Defects
                                    </button>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-3">
									<input type="hidden" name="save_status" id="save_status_man">
								</div>
							</div>
						</div>

					</div>

				</div>
				<div class="modal-footer">
					<button type="button" onclick="javascript:saveInspection_man();" class="btn btn-success" id="btn_save_manual">
						<i class="fa fa-floppy-o"></i> Save
					</button>
					<button type="button" class="btn grey-gallery" id="btn_clear_manual">
						<i class="fa fa-eraser"></i> Clear
					</button>
					<a href="javascript:;" data-dismiss="modal"  class="btn btn-danger btn_backModal"><i class="fa fa-reply"></i>Back</a>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- REQUALI MODAL -->
<div id="ReQualiModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery modal-xl">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Re-qualification</h4>
			</div>
			<form class=form-horizontal>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="scroller" style="height:200px">
								<table class="table table-striped table-hover table-responsive table-bordered" id="tblrealification">
									<thead>
										<tr>
											<td class="table-checkbox" style="width: 2%">
                                                <input type="checkbox" class="group-checkable checkAllitemsrq" />
                                            </td>
											<td></td>
											<td>Ctrl No.</td>
											<td>Part Code</td>
											<td>Part Name</td>
											<td>Lot No.</td>
											<td>Application Date</td>
											<td>Application Time</td>
											<td>Application Ctrl No.</td>
										</tr>
									</thead>
									<tbody id="rq_inspection_body">
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row">

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Ctrl No.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm" id="ctrl_no_rq" name="ctrl_no_rq">
									<div id="er_ctrl_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Part Code</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm clear" id="partcode_rq" name="partcode_rq">
									<span id="er_partcode_rq" style="color:red"></span>
									<input type="hidden" class="form-control input-sm clear" id="id_rq" name="id_rq" readonly>
									<input type="hidden" class="form-control input-sm clear" id="save_status_rq" name="save_status_rq" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Part Name</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="partname_rq" name="partname_rq" readonly>
									<div id="er_partname_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Supplier</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="supplier_rq" name="supplier_rq" readonly>
									<div id="er_supplier_rq"></div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Application Ctrl No.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm clear" id="app_no_rq" name="app_no_rq">
									<span id="er_app_no_rq" style="color:red"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Application Date</label>
								<div class="col-sm-9">
									<input class="form-control input-sm clear date-picker" type="text" value="{{date('m/d/Y')}}" name="app_date_rq" id="app_date_rq" readonly />

								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Application Time</label>
								<div class="col-sm-9">
									<input type="text" data-format="hh:mm A" class="form-control input-sm clear clockface_1" value="{{date('h:i A')}}" name="app_time_rq" id="app_time_rq" readonly />

								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot No.</label>
								<div class="col-sm-9">
									<input type="text" name="lot_no_rq" id="lot_no_rq" class="form-control requiredRequali input-sm lot_no_rq">
									<div id="er_lot_no_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot Quantity</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="lot_qty_rq" name="lot_qty_rq" readonly>
									<div id="er_lot_qty_rq"></div>
								</div>
							</div>
						</div>

					</div>

					<hr>

					<div class="row">
						<div class="col-sm-12">
							<strong>Visual Inspection</strong>
						</div>
					</div>


					<div class="row">

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Date Inspected</label>
								<div class="col-sm-9">
									<input class="form-control requiredRequali input-sm clear date-picker" type="text" name="date_ispected_rq" id="date_ispected_rq"/>
									<div id="er_date_ispected_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">WW#</label>
								<div class="col-sm-3">
									<input type="text" class="form-control input-sm clear" id="ww_rq" name="ww_rq">
									<div id="er_ww_rq"></div>
								</div>
								<label class="control-label col-sm-3">FY#</label>
								<div class="col-sm-3">
									<input type="text" class="form-control input-sm clear" id="fy_rq" name="fy_rq">
									<div id="qr_fy_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Time Inspected</label>
								<div class="col-sm-4">
									<input type="text" data-format="hh:mm A" class="form-control requiredRequali input-sm clear clockface_1" name="time_ins_from_rq" id="time_ins_from_rq"/>
									<div id="er_time_ins_from_rq"></div>
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-4">
									<input type="text" data-format="hh:mm A" class="form-control requiredRequali input-sm clear clockface_1" name="time_ins_to_rq" id="time_ins_to_rq"/>
									<div id="er_time_ins_to_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Shift</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm clear show-tick" name="shift_rq" id="shift_rq">
									<div id="er_shift_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Inspector</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm" name="inspector_rq" id="inspector_rq"/>
									<div id="er_inspector_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Submission</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm clear show-tick" name="submission_rq" id="submission_rq">
									<div id="er_submission_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Judgement</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm clear" id="judgement_rq" name="judgement_rq">
									<div id="er_judgement_rq"></div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Lot Inspected</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm clear" id="lot_inspected_rq" name="lot_inspected_rq">
									<div id="er_lot_inspected_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot Accepted</label>
								<div class="col-sm-9">
									<input type="text" class="form-control requiredRequali input-sm clear" id="lot_accepted_rq" name="lot_accepted_rq">
									<div id="er_lot_accepted_rq"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" id="no_defects_label_rq">No. of Defectives</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="no_of_defects_rq" name="no_of_defects_rq">
									<div id="er_no_of_defects"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Remarks</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="remarks_rq" name="remarks_rq">
									<input type="hidden" class="form-control input-sm clear" id="status_rq" name="status_rq">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" id="mode_defects_label_rq">Mode of Defects</label>
								<div class="col-sm-4">
									<button type="button" class="btn blue btn_mod_rq" id="btn_mod_rq">
                                        <i class="fa fa-plus-circle"></i> Add Mode of Defects
                                    </button>
								</div>
							</div>
						</div>

					</div>


				</div>
				<div class="modal-footer">
					<button type="button" onclick="javascript:saveRequalification();" class="btn btn-success" id="btn_savemodal_rq"><i class="fa fa-floppy-disk-o"></i>Save</button>
					<button type="button" id="btn_deleteRequali" class="btn btn-success red"><i class="fa fa-trash"></i>Delete</button>
					<a href="javascript:;" class="btn grey-gallery btn_clearModal" id="btn_clearmodal_rq"><i class="fa fa-eraser"></i>Clear</a>
					<a href="javascript:;" data-dismiss="modal" id="btn_back_rq" class="btn btn-danger btn_backModal"><i class="fa fa-reply"></i>Back</a>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- MODE OF DEFECTS -->
<div id="mod_inspectionModal" class="modal fade" role="dialog" data-backdrop="static">
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
								<label class="control-label col-sm-3">Mode of Defect</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm show-tick" name="mod_inspection" id="mod_inspection">
									<div id="er_mod"></div>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-3">Quantity</label>
								<div class="col-sm-9">
									<input type="text" id="qty_inspection" name="qty_inspection" class="form-control input-sm">
									<input type="hidden" id="status_inspection" name="status_inspection" class="form-control input-sm">
									<input type="hidden" id="id_inspection" name="id_inspection" class="form-control input-sm">
									<div id="er_qty"></div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-sm-12">
									<button type="button" id="bt_save_modeofdefectsinspection" class="btn btn-sm green pull-right">Save</button>
									<button type="button" id="bt_delete_modeofdefectsinspection" class="btn btn-sm red pull-right">Delete</button>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-striped table-fixedheader" id="tbl_modeofdefect">
									<thead>
										<tr>
											<td class="table-checkbox" style="width: 8%">
                                                <input type="checkbox" class="group-checkable checkAllitemsinspection" />
                                            </td>
                                            <td style="width: 12%"></td>
											<td style="width: 5%">#</td>
											<td style="width: 55%">Mode of Defects</td>
											<td style="width: 20%">Quantity</td>
										</tr>
									</thead>
									<tbody id="tblformodinspection">
                                    	<!-- table records here -->
                                    </tbody>
								</table>
								<input type="hidden" name="mod_count" id="mod_count">
								<input type="hidden" name="mod_total_qty" id="mod_total_qty">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-danger" id=inspectionmod_close>Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- MODE OF DEFECTS CH3CKL3V3L -->
<div id="mod_checklevelModal" class="modal fade" role="dialog" data-backdrop="static">
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
								<label class="control-label col-sm-3">Mode of Defect</label>
								<div class="col-sm-9">
									<select class="form-control input-sm show-tick" name="mod_checklevel" id="mod_checklevel">
										<option value=""></option>
									</select>
									<div id="er_modcl"></div>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-3">Quantity</label>
								<div class="col-sm-9">
									<input type="text" id="qty_checklevel" name="qty_checklevel" class="form-control input-sm">
									<input type="hidden" id="status_checklevel" name="status_checklevel" class="form-control input-sm">
									<input type="hidden" id="id_checklevel" name="id_checklevel" class="form-control input-sm">
									<div id="er_qtycl"></div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-sm-12">
									<button type="button" onclick="javascript:checklevel_save();" id="btn_check_lvl" class="btn btn-sm green pull-right">Save</button>
									<button type="button" onclick="javascript:deleteAllcheckedchecklevel();" id="btn_del_check_lvl" class="btn btn-sm red pull-right">Delete</button>
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
											<td class="table-checkbox" style="width: 5%">
                                                <input type="checkbox" class="group-checkable checkAllitemschecklevel" />
                                            </td>
                                            <td></td>
											<td>#</td>
											<td>Mode of Defects</td>
											<td>Quantity</td>
										</tr>
									</thead>
									<tbody id="tblformodchecklevel">
                                    	<!-- table records here -->
                                    </tbody>
								</table>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" id=checklabelmod_close class="btn btn-danger">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- MODE OF DEFECTS REQUEALIFICATION -->
<div id="mod_requalificationModal" class="modal fade" role="dialog" data-backdrop="static">
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
								<label class="control-label col-sm-3">Mode of Defect</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm show-tick" name="mod_rq" id="mod_rq">
									<div id="er_modrq"></div>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-3">Quantity</label>
								<div class="col-sm-9">
									<input type="text" id="qty_rq" name="qty_rq" class="form-control input-sm">
									<input type="hidden" id="status_requalification" name="status_requalification" class="form-control input-sm">
									<input type="hidden" id="id_requalification" name="id_requalification" class="form-control input-sm">
									<div id="er_qtyrq"></div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-sm-12">
									<button type="button" onclick="javascript:saveModeOfDefectsRequali();" id="btn_add_mod" class="btn btn-sm green pull-right">Save</button>
									<button type="button" id="btn_deletemodrq" class="btn btn-sm red pull-right">Delete</button>
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
											<td class="table-checkbox" style="width: 5%">
                                                <input type="checkbox" class="group-checkable checkAllitemsrequalification" />
                                            </td>
                                            <td></td>
											<td>#</td>
											<td>Mode of Defects</td>
											<td>Quantity</td>
										</tr>
									</thead>
									<tbody id="tblformodrequalification">
                                    	<!-- table records here -->
                                    </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" id=rqmod_close class="btn btn-danger">Close</button>
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
            <form method="POST" action="{{ url('/iqc-calculate-dppm') }}" id="frm_DPPM">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Date from</span>
                                    <input type="text" class="form-control date-picker input-sm " id="gfrom" name="gfrom" autocomplete="false">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Date to</span>
                                    <input type="text" class="form-control date-picker input-sm " id="gto" name="gto" autocomplete="false">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Group By</span>
                                    <select class="form-control input-sm show-tick" name="field1" id="field1">
                                        <option value=""></option>
										<option value="app_no">Application Ctrl No</option>
										<option value="fy">FY</option>
										<option value="ww">WW</option>
										<option value="submission">Submission</option>
										<option value="partcode">Part Code</option>
										<option value="partname">Part Name</option>
										<option value="supplier">Supplier</option>
										<option value="aql">AQL</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">=</span>
                                    <select class="form-control input-sm show-tick" name="content1" id="content1">
                                        <!-- append here -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Group By</span>
                                    <select class="form-control input-sm show-tick" name="field2" id="field2">
                                        <option value=""></option>
										<option value="app_no">Application Ctrl No</option>
										<option value="fy">FY</option>
										<option value="ww">WW</option>
										<option value="submission">Submission</option>
										<option value="partcode">Part Code</option>
										<option value="partname">Part Name</option>
										<option value="supplier">Supplier</option>
										<option value="aql">AQL</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">=</span>
                                    <select class="form-control input-sm show-tick" name="content2" id="content2">
                                        <!-- append here -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Group By</span>
                                    <select class="form-control input-sm show-tick" name="field3" id="field3">
                                        <option value=""></option>
                                        <option value=""></option>
										<option value="app_no">Application Ctrl No</option>
										<option value="fy">FY</option>
										<option value="ww">WW</option>
										<option value="submission">Submission</option>
										<option value="partcode">Part Code</option>
										<option value="partname">Part Name</option>
										<option value="supplier">Supplier</option>
										<option value="aql">AQL</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">=</span>
                                    <select class="form-control input-sm show-tick" name="content3" id="content3">
                                        <!-- append here -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Calculate</button>
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
								<label class="control-label col-sm-3">Part Code</label>
								<div class="col-sm-7">
									<input type="text" class="form-control input-sm clear" id="search_partcode" name="search_partcode">
									<span id="search_partcode_error" style="color:red"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">From</label>
								<div class="col-sm-7">
									<input class="form-control input-sm date-picker" type="text" data-date-format='yyyy-mm-dd' name="search_from" id="search_from"/>
									<!-- <div id="er_search_from"></div> -->
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-3">To</label>
								<div class="col-sm-7">
									<input class="form-control input-sm date-picker" type="text" data-date-format='yyyy-mm-dd' name="search_to" id="search_to"/>
									<!-- <div id="er_search_to"></div> -->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="btn_searchnow" class="btn btn-success">OK</button>
					<button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_search-close">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- HISTORY MODAL -->
<div id="historyModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">History</h4>
			</div>
			<form class="form-horizontal">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">Part Code</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="hs_partcode" name="hs_partcode">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot No.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="hs_lotno" name="hs_lotno">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Judgement</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="hs_judgement" name="hs_judgement">
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label col-sm-3">From</label>
								<div class="col-sm-9">
									<input class="form-control input-sm date-picker" type="text" data-date-format='yyyy-mm-dd' name="hs_from" id="hs_from"/>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">To</label>
								<div class="col-sm-9">
									<input class="form-control input-sm date-picker" type="text" data-date-format='yyyy-mm-dd' name="hs_to" id="hs_to"/>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table table-bordered table-striped table-fixedheader" style="font-size: 10px;">
								<thead>
									<tr>
										<td style="width: 11.67%">Invoice No.</td>
										<td style="width: 11.67%">Part Code</td>
										<td style="width: 30.67%">Part Name</td>
										<td style="width: 16.67%">Lot No.</td>
										<td style="width: 12.67%">Lot Qty.</td>
										<td style="width: 16%">Jugdement</td>
									</tr>
								</thead>
								<tbody id="tblhistorybody"></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="btn_searchHistory" class="btn btn-success">OK</button>
					<button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_history-close">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Upload -->
<div id="uploadModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Data File</h4>
			</div>
			<form class="form-horizontal" method="POST" enctype="multipart/form-data" id="frm_upload" action="{{ url('/upload-iqc') }}">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label col-md-3">Inspection Data</label>
								<div class="col-md-9">
									<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
									<input type="file" class="filestyle" data-buttonName="btn-primary" name="inspection_data" id="inspection_data" {{$readonly}}>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-md-3">Mode of Defects</label>
								<div class="col-md-9">
									<input type="file" class="filestyle" data-buttonName="btn-primary" name="inspection_mod" id="inspection_mod" {{$readonly}}>
								</div>
							</div>

							<hr/>

							<div class="form-group">
								<label class="control-label col-md-3">Re-qualification Data</label>
								<div class="col-md-9">
									<input type="file" class="filestyle" data-buttonName="btn-primary" name="requali_data" id="requali_data" {{$readonly}}>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-md-3">Mode of Defects</label>
								<div class="col-md-9">
									<input type="file" class="filestyle" data-buttonName="btn-primary" name="requali_mod" id="requali_mod" {{$readonly}}>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="btn_uploadfile" class="btn btn-success">Upload</button>
					<button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_upload-close">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- MSG -->
<div id="confirmDeleteModal" class="modal fade" role="dialog" data-backdrop="static">
	 <div class="modal-dialog modal-sm gray-gallery">
		  <div class="modal-content ">
			   <div class="modal-header">
					<h4 class="modal-title">Delete</h4>
			   </div>
			   <div class="modal-body">
					<p>Are you sure do you want to delete?</p>
					<input type="hidden" name="delete_type" id="delete_type">
			   </div>
			   <div class="modal-footer">
			   		<button type="button" class="btn btn-primary" id="btn_deleteyes">Yes</button>
					<button type="button" data-dismiss="modal" class="btn btn-danger">No</button>
			   </div>
		  </div>
	 </div>
</div>