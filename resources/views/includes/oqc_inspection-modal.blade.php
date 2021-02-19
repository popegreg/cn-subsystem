<div id="inspection_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog gray-gallery modal-xl">
        <div class="modal-content ">
            <form class=form-horizontal method="POST" action="{{ url('/oqc-save-inspection') }}" id="frm_inspection">
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th colspan="2" class="text-right">
                                {{ csrf_field() }}
                                <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                            </th>
                        </tr>
                        <tr>
                            <td width="50%">
                                <div class="form-group" id="assembly_line_div">
                                    <label class="control-label col-sm-3">Assembly Line</label>
                                    <div class="col-sm-9">
                                        <select class="form-control enter input-sm validate clear" name="assembly_line" id="assembly_line">
                                            <option value=""></option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="assembly_line_msg"></strong>
                                        </span>
                                        <input type="hidden" class="form-control enter input-sm validate clear" id="inspection_id" name="inspection_id">
                                    </div>
                                </div>
                                <div class="form-group" id="lot_no_div">
                                    <label class="control-label col-sm-3">Lot No.</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="lot_no" name="lot_no">
                                        <span class="help-block">
                                            <strong id="lot_no_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="app_date_div">
                                    <label class="control-label col-sm-3">Application Date</label>
                                    <div class="col-sm-9">
                                        <input class="form-control enter input-sm validate clear" type="date" name="app_date" id="app_date" />
                                        <span class="help-block">
                                            <strong id="app_date_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="app_time_div">
                                    <label class="control-label col-sm-3">Application Time</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" name="app_time" id="app_time"/>
                                        <span class="help-block">
                                            <strong id="app_time_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="prod_category_div">
                                    <label class="control-label col-sm-3">Product Category</label>
                                    <div class="col-sm-9">
                                        <select class="form-control enter input-sm validate clear" name="prod_category" id="prod_category">
                                            <option value=""></option>
                                            <option value="Automotive">Automotive</option>
                                            <option value="Non-Automotive">Non-Automotive</option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="prod_category_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td width="50%">
                                <div class="form-group">
                                    <label class="control-label col-sm-3"></label>
                                    <div class="md-checkbox-inline">
                                        <div class="md-checkbox">
                                            <input type="checkbox" id="is_probe" class="md-check" name="is_probe" value="0">
                                            <label for="is_probe">
                                            <span></span>
                                            <span class="check"></span>
                                            <span class="box"></span>
                                            Check if PROBE </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="po_no_div">
                                    <label class="control-label col-sm-3">P.O. No.</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control enter input-sm validate clear" id="po_no" name="po_no" maxlength="15">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn input-sm green" id="btn_getpodetails">
                                                    <i class="fa fa-arrow-circle-down"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <span class="help-block">
                                            <strong id="po_no_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="series_name_div">
                                    <label class="control-label col-sm-3">Device Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="series_name" name="series_name" readonly>
                                        <input type="hidden" class="form-control enter input-sm validate clear" id="series_code" name="series_code">
                                        <span class="help-block">
                                            <strong id="series_name_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="customer_div">
                                    <label class="control-label col-sm-3">Customer</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="customer" name="customer" readonly>
                                        <input type="hidden" class="form-control enter input-sm validate clear" id="customer_code" name="customer_code">
                                        <span class="help-block">
                                            <strong id="customer_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="po_qty_div">
                                    <label class="control-label col-sm-3">P.O. Qty</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="po_qty" name="po_qty">
                                        <span class="help-block">
                                            <strong id="po_qty_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="family_div">
                                    <label class="control-label col-sm-3">Family</label>
                                    <div class="col-sm-9">
                                        <select class=" form-control enter input-sm validate clear" name="family" id="family">
                                            <option value=""></option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="family_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th colspan="2">Sampling Plan</th>
                        </tr>
                        <tr>
                            <td width="50%">
                                <div class="form-group" id="type_of_inspection_div">
                                    <label class="control-label col-sm-3">Type of Inspection</label>
                                    <div class="col-sm-9">
                                        <select class=" form-control enter input-sm validate clear" name="type_of_inspection" id="type_of_inspection">
                                            <option value=""></option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="type_of_inspection_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="severity_of_inspection_div">
                                    <label class="control-label col-sm-3">Severity of Inspection</label>
                                    <div class="col-sm-9">
                                        <select class=" form-control enter input-sm validate clear" name="severity_of_inspection" id="severity_of_inspection">
                                            <option value=""></option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="severity_of_inspection_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="inspection_lvl_div">
                                    <label class="control-label col-sm-3">Inspection Level</label>
                                    <div class="col-sm-9">
                                        <select class=" form-control enter input-sm validate clear" name="inspection_lvl" id="inspection_lvl">
                                            <option value=""></option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="inspection_lvl_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="lot_qty_div">
                                    <label class="control-label col-sm-3">Lot Quantity</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="lot_qty" name="lot_qty">
                                        <span class="help-block">
                                            <strong id="lot_qty_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td width="50%">
                                <div class="form-group" id="aql_div">
                                    <label class="control-label col-sm-3">AQL</label>
                                    <div class="col-sm-9">
                                        <select class=" form-control enter input-sm validate clear" name="aql" id="aql">
                                            <option value=""></option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="aql_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="sample_size_div">
                                    <label class="control-label col-sm-3">Sample Size</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="sample_size" name="sample_size">
                                        <span class="help-block">
                                            <strong id="sample_size_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Accept</label>
                                    <div class="col-sm-9">
                                        <input type="number" min="0" max="1" class="form-control enter input-sm" id="accept" name="accept" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Reject</label>
                                    <div class="col-sm-9">
                                        <input type="number" min="0" max="1" class="form-control enter input-sm" id="reject" name="reject" readonly>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th colspan="2">Visual Inspection</th>
                        </tr>

                        <tr>
                            <td width="50%">
                                <div class="form-group" id="date_inspected_div">
                                    <label class="control-label col-sm-3">Date Inspected</label>
                                    <div class="col-sm-9">
                                        <input class="form-control enter input-sm validate clear" type="date" name="date_inspected" id="date_inspected" />
                                        <span class="help-block">
                                            <strong id="date_inspected_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">WW#</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control input-sm " id="ww" name="ww" readonly>
                                    </div>
                                    <label class="control-label col-sm-3">FY#</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control input-sm " id="fy" name="fy" readonly>
                                    </div>
                                </div>
                                <div class="form-group" id="time_ins_div">
                                    <label class="control-label col-sm-3">Time Inspected</label>
                                    <div class="col-sm-4">
                                        <input type="text" data-format="hh:mm A" class="form-control enter input-sm validate clear" name="time_ins_from" id="time_ins_from"/>
                                        <span class="help-block">
                                            <strong id="time_ins_msg"></strong>
                                        </span>
                                    </div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4">
                                        <input type="text" data-format="hh:mm A" class="form-control enter input-sm validate clear" name="time_ins_to" id="time_ins_to"/>
                                        <span class="help-block">
                                            <strong id="time_ins_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="shift_div">
                                    <label class="control-label col-sm-3">Shift</label>
                                    <div class="col-sm-9">
                                        <select class=" form-control enter input-sm validate clear" name="shift" id="shift">
                                            <option value=""></option>
                                            <option value="Shift A">Shift A</option>
                                            <option value="Shift B">Shift B</option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="shift_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Inspector</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="inspector" name="inspector" readonly value="{{ Auth::user()->firstname }}" />
                                    </div>
                                </div>
                                <div class="form-group" id="submission_div">
                                    <label class="control-label col-sm-3">Submission</label>
                                    <div class="col-sm-9">
                                        <select class=" form-control enter input-sm validate" name="submission" id="submission">
                                            <option value=""></option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="submission_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="coc_req_div">
                                    <label class="control-label col-sm-3">COC Requirements</label>
                                    <div class="col-sm-9">
                                        <select class=" form-control enter input-sm validate clear" name="coc_req" id="coc_req">
                                            <option value=""></option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                        <span class="help-block">
                                            <strong id="coc_req_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="judgement_div">
                                    <label class="control-label col-sm-3">Judgement</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="judgement" name="judgement" readonly>
                                        <span class="help-block">
                                            <strong id="judgement_div"></strong>
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td width="50%">
                                <div class="form-group" id="lot_inspected_div">
                                    <label class="control-label col-sm-3">Lot Inspected</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="lot_inspected" name="lot_inspected">
                                        <span class="help-block">
                                            <strong id="lot_inspected_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="lot_accepted_div">
                                    <label class="control-label col-sm-3">Lot Accepted</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="lot_accepted" name="lot_accepted">
                                        <span class="help-block">
                                            <strong id="lot_accepted_msg"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" id="no_of_defects_div">
                                    <label class="control-label col-sm-3">No. of Defectives</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm clear" id="no_of_defects" name="no_of_defects" readonly>
                                    </div>
                                </div>
                                <div class="form-group" id="remarks_div">
                                    <label class="control-label col-sm-3">Remarks</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control enter input-sm validate clear" id="remarks" name="remarks">
                                        <input type="hidden" id="inspection_save_status" name="inspection_save_status">
                                    </div>
                                </div>
                                <div class="form-group" id="mode_of_defects_div">
                                    <label class="control-label col-sm-3">Mode of Defects</label>
                                    <div class="col-sm-4">
                                        <button type="button" onclick="javascript:ModeOfDefects();" class="btn blue" id="btn_mode_of_defects">
                                            <i class="fa fa-plus-circle"></i> Add Mode of Defects
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2" class="text-right">
                                <button type="submit" class="btn btn-success" id="btn_savemodal">Save</button>
                                <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                            </th>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="mode_of_defects_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog gray-gallery">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Mode of Defect</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ url('/oqc-save-mod') }}" id="frm_mode_of_defects">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" id="mode_of_defects_name_div">
                                <label class="control-label col-sm-3">Mode of Defect</label>
                                <div class="col-sm-9">
                                    <select class="form-control input-sm validateModeOfDefects clear_mod" name="mode_of_defects_name" id="mode_of_defects_name">
                                       <option value=""></option>
                                    </select>
                                    <span class="help-block">
                                        <strong id="mode_of_defects_name_msg"></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" id="mod_qty_div">
                                <label class="control-label col-sm-3">Quantity</label>
                                <div class="col-sm-9">
                                    <input type="text" name="mod_qty" id="mod_qty" class="form-control input-sm validateModeOfDefects clear_mod">
                                    <input type="hidden" id="mode_save_status" name="mode_save_status" class="">
                                    <input type="hidden" id="mod_po" name="mod_po" class="">
                                    <input type="hidden" id="mod_device" name="mod_device" class="">
                                    <input type="hidden" id="mod_lotno" name="mod_lotno" class="">
                                    <input type="hidden" id="mod_submission" name="mod_submission" class="">
                                    <input type="hidden" id="mod_id" name="mod_id" class="">
                                    <input type="hidden" id="ins_id" name="ins_id" class="">
                                    <span class="help-block">
                                        <strong id="mod_qty_msg"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-offset-8 col-md-2">
                                    <button type="submit" class="btn btn-sm green">Save</button>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="javascript:DeleteModeOfDefects();" class="btn btn-sm red">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped" id="tbl_mode_of_defects">
                                <thead>
                                    <tr>
                                        <td class="table-checkbox" width="5%">
                                            <input type="checkbox" class="group-checkable-mod" />
                                        </td>
                                        <td width="8%"></td>
                                        <td>Mode of Defects</td>
                                        <td>Quantity</td>
                                    </tr>
                                </thead>
                                <tbody id="tbl_mode_of_defects_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="search_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog gray-gallery">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Search</h4>
            </div>
            <form class="form-horizontal" id="frm_oqc_search">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-sm-3">PO Number</label>
                        <div class="col-sm-7">
                            <input class="form-control input-sm" type="text" value="" name="search_po" id="search_po"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">From</label>
                        <div class="col-sm-7">
                            <input class="form-control input-sm date-picker" type="text" value="" name="search_from" id="search_from"/>
                            <div id="er_search_from"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">To</label>
                        <div class="col-sm-7">
                            <input class="form-control input-sm date-picker" type="text" value="" name="search_to" id="search_to"/>
                            <div id="er_search_to"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-success" onclick="javascript:searchInspection();">
                        <i class="fa fa-search"></i> Search
                    </button> --}}
                    <button type="button" id="btn_pdf" class="btn btn-primary">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </button>
                    <a href="javascript:ExcelReport();" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </a>
                    <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_search-close">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <div id="report_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog gray-gallery">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reports</h4>
            </div>
            <form class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-sm-3">PO Number</label>
                        <div class="col-sm-7">
                            <input class="form-control input-sm" type="text" name="rpt_po" id="rpt_po"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">From</label>
                        <div class="col-sm-7">
                            <input class="form-control input-sm date-picker" type="text" name="rpt_from" id="rpt_from"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">To</label>
                        <div class="col-sm-7">
                            <input class="form-control input-sm date-picker" type="text" name="rpt_to" id="rpt_to"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Submission</label>
                        <div class="col-sm-7">
                            <select class="form-control input-sm" name="rpt_sub" id="rpt_sub">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="javascript:PDFReport();" class="btn btn-primary">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </button>
                    <button type="button" onclick="javascript:ExcelReport();" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_search-close">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
 --}}
<div id="groupby_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg gray-gallery">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Group Items By:</h4>
            </div>
            <form method="POST" action="{{ url('/oqc-calculate-dppm') }}" id="frm_DPPM">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Date from</span>
                                    <input type="text" class="form-control date-picker input-sm grpfield " id="gfrom" name="gfrom">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Date to</span>
                                    <input type="text" class="form-control date-picker input-sm grpfield " id="gto" name="gto">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Group By</span>
                                    <select class="form-control input-sm grpfield show-tick" name="field1" id="field1">
                                        <option value=""></option>
                                        <option value="id">ID</option>
                                        <option value="date_inspected">Date Inspected</option>
                                        <option value="time_ins_from">Inspection Time</option>
                                        <option value="fy">FY#</option>
                                        <option value="ww">WW#</option>
                                        <option value="assembly_line">Assembly Line</option>
                                        <option value="submission">Submission</option>
                                        <option value="prod_category">Category</option>
                                        <option value="customer">Customer Name</option>
                                        <option value="family">Family</option>
                                        <option value="device_name">Device Name</option>
                                        <option value="po_no">P.O#</option>
                                        <option value="lot_no">Lot No</option>
                                        <option value="aql">AQL</option>
                                        <option value="lot_accepted">Lot Accepted</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">=</span>
                                    <select class="form-control input-sm grpfield show-tick" name="content1" id="content1">
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
                                    <select class="form-control input-sm grpfield show-tick" name="field2" id="field2">
                                        <option value=""></option>
                                        <option value="id">ID</option>
                                        <option value="date_inspected">Date Inspected</option>
                                        <option value="time_ins_from">Inspection Time</option>
                                        <option value="fy">FY#</option>
                                        <option value="ww">WW#</option>
                                        <option value="assembly_line">Assembly Line</option>
                                        <option value="submission">Submission</option>
                                        <option value="prod_category">Category</option>
                                        <option value="customer">Customer Name</option>
                                        <option value="family">Family</option>
                                        <option value="device_name">Device Name</option>
                                        <option value="po_no">P.O#</option>
                                        <option value="lot_no">Lot No</option>
                                        <option value="aql">AQL</option>
                                        <option value="lot_accepted">Lot Accepted</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">=</span>
                                    <select class="form-control input-sm grpfield show-tick" name="content2" id="content2">
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
                                    <select class="form-control input-sm grpfield show-tick" name="field3" id="field3">
                                        <option value=""></option>
                                        <option value="id">ID</option>
                                        <option value="date_inspected">Date Inspected</option>
                                        <option value="time_ins_from">Inspection Time</option>
                                        <option value="fy">FY#</option>
                                        <option value="ww">WW#</option>
                                        <option value="assembly_line">Assembly Line</option>
                                        <option value="submission">Submission</option>
                                        <option value="prod_category">Category</option>
                                        <option value="customer">Customer Name</option>
                                        <option value="family">Family</option>
                                        <option value="device_name">Device Name</option>
                                        <option value="po_no">P.O#</option>
                                        <option value="lot_no">Lot No</option>
                                        <option value="aql">AQL</option>
                                        <option value="lot_accepted">Lot Accepted</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">=</span>
                                    <select class="form-control input-sm grpfield show-tick" name="content3" id="content3">
                                        <!-- append here -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="calID">Calculate</button>
                    <button type="button" class="btn grey-gallery" id="btn_clear_grpby">Clear</button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="probe_item_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md gray-gallery">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Select Items</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="" class="control-label col-md-3">Series Name:</label>
                            <div class="col-md-8">
                                <select class="select2 form-control" name="item_probe" id="item_probe">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
            </div>
        </div>
    </div>
</div>
