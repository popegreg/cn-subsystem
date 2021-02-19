@extends('layouts.master')

@section('title')
	QC Database Molding | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_PCKNGMLD'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
                                                    <div class="col-sm-12">
                                                        <table class="table table-hover table-bordered table-striped" id="packingdatatable">
                                                            <thead>
                                                                <tr>
                                                                    <td class="table-checkbox" style="width: 5%">
                                                                        <input type="checkbox" class="group-checkable checkAllitems" />
                                                                    </td>
                                                                    <td></td>
                                                                    <td>Date Inspected</td>
                                                                    <td>Shipment Date</td>
                                                                    <td>Series Name</td>
                                                                    <td>P.O. #</td>
                                                                    <td>Packing Operator</td>
                                                                    <td>Inspector</td>
                                                                    <td>Packing Type</td>
                                                                    <td>Unit Condition</td>
                                                                    <td>Packing Code(per Series)</td>
                                                                    <td>Carton #</td>
                                                                    <td>Packing Code</td>
                                                                    <td>Qty</td>
                                                                    <td>Judgement</td>
                                                                    <td>Remarks</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tblforpacking">
                                                                  
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                      
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <a href="javascript:;" class="btn green" id="btn_add">
                                            <i class="fa fa-plus"></i> Add New
                                        </a>
                                        <a href="javascript:;" class="btn grey-gallery" id="btn_groupby">
                                            <i class="fa fa-group"></i> Group By
                                        </a>
                                        <button type="button" onclick="javascript:packingDelete();" class="btn red" id="btn_delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                        <a href="javascript:;" class="btn purple" id="btn_search">
                                            <i class="fa fa-search"></i> Search
                                        </a>
                                        <button type="button" class="btn yellow-gold" id="btn_pdf">
                                            <i class="fa fa-file-text-o"></i> Print to Pdf
                                        </button>
                                        <button type="button" class="btn green-jungle" id="btn_excel">
                                            <i class="fa fa-file-text-o"></i> Print to Excel
                                        </button>
                                        <input class="form-control input-sm" type="hidden" value="" name="hd_report_status" id="hd_report_status"/>
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


	<!-- ADD NEW MODAL -->
    <div id="AddNewModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog gray-gallery modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Packing Inspection Result</h4>
                </div>
                <form class=form-horizontal>
                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-4">P.O. No.</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="po_no" name="po_no">
                                        <input type="hidden" class="form-control input-sm" id="status" name="status">
                                        <input type="hidden" class="form-control input-sm" id="id" name="id">
                                        <div id="er_po_no"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Inspection Date</label>
                                    <div class="col-sm-8">
                                        <input class="form-control input-sm date-picker" type="text" value="" name="insp_date" id="insp_date"/>
                                        <div id="er_insp_date"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Shipment Date</label>
                                    <div class="col-sm-8">
                                        <input class="form-control input-sm date-picker" type="text" value="" name="ship_date" id="ship_date"/>
                                        <div id="er_ship_date"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Device Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="series_name" name="series_name" readonly />
                                        <div id="er_series_name"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Inspector</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="inspector" name="inspector">
                                        <div id="er_inspector"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Packing Type</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm  show-tick" name="packing_type" id="packing_type">
                                            <option value=""></option>
                                            @foreach($packingtypes as $packingtype)
                                            <option value="{{$packingtype->description}}">{{$packingtype->description}}</option>
                                            @endforeach
                                        </select>
                                        <div id="er_packing_type"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Unit Condition</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm  show-tick" name="unit_condition" id="unit_condition">
                                            <option value=""></option>
                                            @foreach($unitconditions as $unitcondition)
                                            <option value="{{$unitcondition->description}}">{{$unitcondition->description}}</option>
                                            @endforeach
                                        </select>
                                        <div id="er_unit_condition"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Packing Operator</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm  show-tick" name="packing_operator" id="packing_operator">
                                            <option value=""></option>
                                            @foreach($packingoperators as $packingoperator)
                                            <option value="{{$packingoperator->description}}">{{$packingoperator->description}}</option>
                                            @endforeach
                                        </select>
                                        <div id="er_packing_operator"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Remarks</label>
                                    <div class="col-sm-8">
                                        <textarea name="remarks" id="remarks" class="form-control input-sm" style="resize:none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Packing Code<small>(per Series)</small></label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm  show-tick" name="pack_code_per_series" id="pack_code_per_series">
                                            <option value=""></option>
                                            @foreach($packingcodeperseries as $packingcodeperserie)
                                            <option value="{{$packingcodeperserie->description}}">{{$packingcodeperserie->description}}</option>
                                            @endforeach
                                        </select>
                                        <div id="er_pack_code_per_series"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Carton No.</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="carton_no" name="carton_no">
                                        <div id="er_carton_no"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Packing Code.</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="pack_code" name="pack_code">
                                        <div id="er_pack_code"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Runcard</label>
                                    <div class="col-sm-8">
                                        <a href="javascript:;" class="btn btn-block green" id="btn_runcard"> Lot Number</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Judgement</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm  show-tick" name="judgement" id="judgement">
                                            <option value=""></option>
                                            <option value="Accept">Accept</option>
                                            <option value="Reject">Reject</option>   
                                        </select>
                                        <div id="er_judgement"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Total Qty</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" id="total_qty" name="total_qty" readonly>
                                        <div id="er_total_qty"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4" id="no_defects_label">No. of Defectives</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm clear" id="no_of_defects" name="no_of_defects" readonly>
                                       <!--  <div id="er_no_of_defects"></div> -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4" id="mode_defects_label">Mode of Defects</label>
                                    <div class="col-sm-8">
                                        <button type="button" onclick="javascript:display_packmod();" class="btn blue btn_mod_ins" id="btn_packmod">
                                            <i class="fa fa-plus-circle"></i> Add Mode of Defects
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" onclick="javascript:packingsave();" class="btn btn-success" id="btn_save">Save</button>
                            <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RUNCARD MODAL -->
    <div id="RuncardModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Runcard Specification</h4>
                </div>
                <form class="form-horizontal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Lot No.</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control input-sm" name="rc_no" id="rc_no">
                                        <input type="hidden" class="form-control input-sm" name="rc_id" id="rc_id">
                                        <input type="hidden" class="form-control input-sm" name="rc_packcode" id="rc_packcode">
                                        <div id="er_rc_no"></div>
                                    </div>
                                    <label class="control-label col-sm-1">Qty</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control input-sm" name="rc_qty" id="rc_qty">
                                        <input type="hidden" class="form-control input-sm" name="rc_status" id="rc_status">
                                        <div id="er_rc_qty"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Remarks</label>
                                    <div class="col-sm-7">
                                        <textarea name="rc_remarks" id="rc_remarks" class="form-control input-sm" style="resize:none"></textarea>
                                        <div id="er_rc_remarks"></div>
                                    </div>
                                
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" id="tableforruncard">
                                    <table class="table table-hover table-bordered table-striped" id="tbl_runcard">
                                        <thead>
                                            <tr>
                                                <td class="table-checkbox" style="width: 5%">
                                                    <input type="checkbox" class="group-checkable rccheckAllitems" />
                                                </td>
                                                <td></td>
                                                <td>P.O #</td>
                                                <td>Runcard #</td>
                                                <td>Qty</td>
                                                <td>Remarks</td>
                                            </tr>
                                        </thead>
                                        <tbody id="tblforruncard">
                                          
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="javascript:rcpackingDelete();" class="btn grey-gallery" id="btn_rc_delete">Delete Record</button>
                        <button type="button" onclick="javascript:rc_save();" class="btn btn-success" id="btn_save">Save</button>
                        <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_rc_close">Close</button>
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
                                    <label class="control-label col-sm-3">PO Number</label>
                                    <div class="col-sm-7">
                                        <input class="form-control input-sm" type="text" value="" name="search_pono" id="search_pono"/>
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
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="javascript:searchby();" class="btn btn-success" id="iqc_search">OK</button>
                        <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_search-close">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Empty FIELD SEARCH -->
    <div id="emptyModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog gray-gallery">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Warning!</h4>
                </div>
                <form class="form-horizontal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label col-sm-10">Please search record/s first before you print reports</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODE OF DEFECTS -->
    <div id="packmod_Modal" class="modal fade" role="dialog" data-backdrop="static">
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
                                        <select class="form-control input-sm show-tick" name="mod_inspection" id="mod_inspection">
                                            <option value="">select one</option>
                                            @foreach($mods as $mod)
                                            <option value="{{$mod->description}}">{{$mod->description}}</option>
                                            @endforeach
                                        </select>
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
                                        <button type="button" onclick="javascript:packmod_save();" id="" class="btn btn-sm green pull-right">Save</button>
                                        <button type="button" onclick="javascript:deleteAllcheckedinspection();" id="" class="btn btn-sm red pull-right">Delete</button>
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
                                                    <input type="checkbox" class="group-checkable checkAllitemsinspection" />
                                                </td>
                                                <td></td>
                                                <td>#</td>
                                                <td>Mode of Defects</td>
                                                <td>Quantity</td>
                                            </tr>
                                        </thead>
                                        <tbody id="tblformodinspection">
                                            <!-- table records here --> 
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-danger" id=packmod_close>Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- GROUP BY MODAL -->
    <div id="GroupByModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md gray-gallery">
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
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label col-sm-2">Group #1</label>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group1" id="group1">
                                        <option value=""></option>
                                        <option value="id">ID</option>
                                        <option value="date_inspected">Inspection Date</option>
                                        <option value="shipment_date">Shipment Date</option>
                                        <option value="device_name">Series Name</option>
                                        <option value="po_num">PO #</option>
                                        <option value="packing_operator">Packing Operator</option>  
                                        <option value="inspector">Inspector</option>
                                        <option value="packing_type">Packing Type</option>
                                        <option value="unit_condition">Unit Condition</option>
                                        <option value="packing_code_series">Packing Code(Per Series)</option>
                                        <option value="carton_num">Carton #</option>
                                        <option value="packing_code">Packing Code</option>
                                        <option value="total_qty">Qty</option>
                                        <option value="judgement">Judgement</option>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group1content" id="group1content">
                                    <!-- append here -->
                                    </select>
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
                                        <option value="id">ID</option>
                                        <option value="date_inspected">Inspection Date</option>
                                        <option value="shipment_date">Shipment Date</option>
                                        <option value="device_name">Series Name</option>
                                        <option value="po_num">PO #</option>
                                        <option value="packing_operator">Packing Operator</option>  
                                        <option value="inspector">Inspector</option>
                                        <option value="packing_type">Packing Type</option>
                                        <option value="unit_condition">Unit Condition</option>
                                        <option value="packing_code_series">Packing Code(Per Series)</option>
                                        <option value="carton_num">Carton #</option>
                                        <option value="packing_code">Packing Code</option>
                                        <option value="total_qty">Qty</option>
                                        <option value="judgement">Judgement</option>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group2content" id="group2content">
                                    <!-- append here -->
                                    <option value=""></option>  
                                    </select>
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
                                        <option value="id">ID</option>
                                        <option value="date_inspected">Inspection Date</option>
                                        <option value="shipment_date">Shipment Date</option>
                                        <option value="device_name">Series Name</option>
                                        <option value="po_num">PO #</option>
                                        <option value="packing_operator">Packing Operator</option>  
                                        <option value="inspector">Inspector</option>
                                        <option value="packing_type">Packing Type</option>
                                        <option value="unit_condition">Unit Condition</option>
                                        <option value="packing_code_series">Packing Code(Per Series)</option>
                                        <option value="carton_num">Carton #</option>
                                        <option value="packing_code">Packing Code</option>
                                        <option value="total_qty">Qty</option>
                                        <option value="judgement">Judgement</option>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control select2me input-sm show-tick" name="group3content" id="group3content">
                                    <!-- append here -->
                                    </select>
                                </div>
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
@endsection

@push('script')
<script>
$(function() {
    loadPackdata();
    lot_accepted();
    $('#hd_report_status').val("");
    $('#groupby_datefrom').datepicker();
    $('#groupby_dateto').datepicker();
    $('#groupby_datefrom').on('change',function(){
          $(this).datepicker('hide');
    });
    $('#groupby_dateto').on('change',function(){
          $(this).datepicker('hide');
    });

    $('#btn_pdf').click(function(){
        window.location.href="{{ url('/packingmolding') }}";
    });
    $('#btn_excel').click(function(){
        window.location.href="{{ url('/packingmolding') }}";
    });
    /*$('#packingdatatable').DataTable();*/

    $('#btn_groupby').on('click', function() {
        $('#GroupByModal').modal('show');
        $('#groupby_datefrom').val("");
        $('#groupby_dateto').val("");
        $('#group1').select2('val',"");
        $('#group1content').select2('val',"");
        $('#group2').select2('val',"");
        $('#group2content').select2('val',"");
        $('#group3').select2('val',"");
        $('#group3content').select2('val',"");
        $('#group4').select2('val',"");
        $('#group4content').select2('val',"");
        $('#group5').select2('val',"");
        $('#group5content').select2('val',"");

        //to classify group by when reporting----------
        $('#hd_report_status').val("GROUPBY");
        $('#hd_search_from').val("");
        $('#hd_search_to').val("");
        $('#hd_search_pono').val("");
    });
    $('#btn_search').on('click', function() {
        $('#SearchModal').modal('show');
        $('#search_pono').val("");
        $('#search_from').val("");
        $('#search_to').val("");
        $('#er_search_from').html(""); 
        $('#er_search_to').html(""); 

        //to classify group by when reporting----------
        $('#hd_report_status').val("SEARCH");
        $('#hd_search_from').val("");
        $('#hd_search_to').val("");
        $('#hd_groupfield').val("");
        $('#hd_value').val("");
    });

    $('#btn_add').on('click', function() {
        $('#AddNewModal').modal('show');
        $('#status').val("ADD");
        $('#rc_packcode').val();
        $('#po_no').val("");
        $('#insp_date').val("");
        $('#ship_date').val("");
        $('#series_name').val("");
        $('#packing_type').val("");
        $('#unit_condition').val("");
        $('#packing_operator').val("");
        $('#remarks').val("");
        $('#pack_code_per_series').val("");
        $('#carton_no').val("");
        $('#pack_code').val("");
        $('#judgement').val("");
        $('#total_qty').val("");
        $('#no_of_defects').val("");
    });

    $('#btn_groupby').on('click', function() {
        $('#GroupByModal').modal('show');
    });

    $('#btn_search').on('click', function() {
        $('#SearchModal').modal('show');
        $('#search_pono').val("");
        $('#search_from').val("");
        $('#search_to').val("");
        $('#er_search_from').html(""); 
        $('#er_search_to').html(""); 
    });

    $('#btn_runcard').on('click', function() {
        $('#RuncardModal').modal('show');
        $('#rc_status').val("ADD");
        $('#rc_packcode').val($('#po_no').val());
        $('#rc_no').val("");
        $('#rc_qty').val("");
        $('#rc_id').val("");
        $('#rc_remarks').val("");
        $('#er_rc_no').html("");
        $('#er_rc_qty').html("");
        $('#er_rc_remarks').html("");
        var pono = $('#po_no').val();
        var cartonno = $('#carton_no').val();
        var myData = {pono:pono,cartonno:cartonno};
        $('#tblforruncard').html("");
        $.ajax({
            url:"{{ url('/displayMOLDING_runcard') }}",
            method:'get',
            data:myData,
        }).done(function(data, textStatus, jqXHR){
            console.log(data);
            $.each(data,function(i,val){
                var tblrow = '<tr>'+
                                '<td style="width: 2%">'+
                                    '<input type="checkbox" class="form-control input-sm rccheckboxes" value="'+val.id+'" name="rccheckitem" id="rccheckitem"></input> '+
                                '</td> '+                       
                                '<td style="width: 5%"> '+          
                                    '<button type="button" name="rcedit-task" value="'+val.id+ '|' +val.po_no+ '|' +val.caton_no+'|'+val.runcard_no+'|'+val.runcard_qty+'|'+val.runcard_remarks+'" class="btn btn-sm btn-primary rcedit-task">'+
                                           '<i class="fa fa-edit"></i> '+
                                    '</button>'+
                                '</td>'+
                                '<td>'+val.po_no+'</td>'+
                                '<td>'+val.runcard_no+'</td>'+
                                '<td>'+val.runcard_qty+'</td>'+
                                '<td>'+val.runcard_remarks+'</td>'+
                            '</tr>';
                $('#tblforruncard').append(tblrow);
                $('#rc_no').val("");
                $('#rc_qty').val("");
                $('#rc_remarks').val("");
                $('#rc_status').val("ADD");

                $('.rcedit-task').on('click', function(e) {
                    $('#rc_status').val("EDIT");
                    var edittext = $(this).val().split('|');
                    var editid = edittext[0];
                    $.ajax({
                        url:"{{ url('/packingMOLDING_runcard_edit') }}",
                        method:'get',
                        data:{
                            id:editid
                        }
                    }).done(function(data, textStatus, jqXHR){
                        $('#rc_id').val(data[0]['id']);
                        $('#rc_packcode').val(data[0]['packing_code']);
                        $('#rc_no').val(data[0]['runcard_no']);
                        $('#rc_qty').val(data[0]['runcard_qty']);
                        $('#rc_remarks').val(data[0]['runcard_remarks']);
                    }).fail(function(jqXHR,textStatus,errorThrown){
                        console.log(errorThrown+'|'+textStatus)
                    });
                });
            });  
        }).fail(function(jqXHR, errorThrown, textStatus){
            console.log(errorThrown+'|'+textStatus);
        })
    });
    $('.checkAllitems').change(function(){
        if($('.checkAllitems').is(':checked')){
            $('.deleteAll-task').removeClass("disabled");
            $('#add').addClass("disabled");
            $('input[name=checkitem]').parents('span').addClass("checked");
            $('input[name=checkitem]').prop('checked',this.checked);
        }else{
            $('input[name=checkitem]').parents('span').removeClass("checked");
            $('input[name=checkitem]').prop('checked',this.checked);
            $('.deleteAll-task').addClass("disabled");
            $('#add').removeClass("disabled");
        }       
    });

    $('.checkboxes').change(function(){
        $('input[name=checkAllitem]').parents('span').removeClass("checked");
        $('input[name=checkAllitem]').prop('checked',false);
        if($('.checkboxes').is(':checked')){
            $('.deleteAll-task').removeClass("disabled");
            $('#add').addClass("disabled");
        }else{
            $('.deleteAll-task').addClass("disabled");
            $('#add').removeClass("disabled");
        }
    
    });
    $('.rccheckAllitems').change(function(){
        if($('.rccheckAllitems').is(':checked')){
            $('.deleteAll-task').removeClass("disabled");
            $('#add').addClass("disabled");
            $('input[name=rccheckitem]').parents('span').addClass("checked");
            $('input[name=rccheckitem]').prop('checked',this.checked);
        }else{
            $('input[name=rccheckitem]').parents('span').removeClass("checked");
            $('input[name=rccheckitem]').prop('checked',this.checked);
            $('.deleteAll-task').addClass("disabled");
            $('#add').removeClass("disabled");
        }       
    });

    $('.rccheckboxes').change(function(){
        $('input[name=rccheckAllitem]').parents('span').removeClass("checked");
        $('input[name=rccheckAllitem]').prop('checked',false);
        if($('.rccheckboxes').is(':checked')){
            $('.deleteAll-task').removeClass("disabled");
            $('#add').addClass("disabled");
        }else{
            $('.deleteAll-task').addClass("disabled");
            $('#add').removeClass("disabled");
        }
    
    });
    $('#tblforpacking').on('click','.edit-task', function(e) {
        $('#AddNewModal').modal('show');
        $('#status').val("EDIT");
        var edittext = $(this).val().split('|');
        var editid = edittext[0];
        $('#id').val(editid);
        $('#po_no').val(edittext[16]);
        $('#insp_date').val(edittext[1]);
        $('#ship_date').val(edittext[2]);
        $('#series_name').val(edittext[3]);
        $('#inspector').val(edittext[4]);
        $('#packing_type').val(edittext[5]);
        $('#unit_condition').val(edittext[6]);
        $('#packing_operator').val(edittext[7]);
        $('#remarks').val(edittext[8]);
        $('#pack_code_per_series').val(edittext[9]);
        $('#carton_no').val(edittext[10]);
        $('#pack_code').val(edittext[11]);
        $('#judgement').val(edittext[12]);
        $('#total_qty').val(edittext[13]);
        $('#no_of_defects').val(edittext[14]);
        $('#dbcon').val(edittext[15]);
     
    });
    $('.rcedit-task').on('click', function(e) {
        $('#rc_status').val("EDIT");
        var edittext = $(this).val().split('|');
        var editid = edittext[0];
        $.ajax({
            url:"{{ url('/rcpackingMOLDINGEdit') }}",
            method:'get',
            data:{
                id:editid
            }
        }).done(function(data, textStatus, jqXHR){
            $('#rc_id').val(data[0]['id']);
            $('#rc_packcode').val(data[0]['packing_code']);
            $('#rc_no').val(data[0]['runcard_no']);
            $('#rc_qty').val(data[0]['runcard_qty']);
            $('#rc_remarks').val(data[0]['runcard_remarks']);
        }).fail(function(jqXHR,textStatus,errorThrown){
            console.log(errorThrown+'|'+textStatus)
        });
    });

    $('#po_no').keyup(function(){
        $('#er_po_no').html(""); 
    });
    $('#insp_date').click(function(){
        $('#er_insp_date').html(""); 
    });
    $('#ship_date').click(function(){
        $('#er_ship_date').html(""); 
    });
    $('#series_name').keyup(function(){
        $('#er_series_name').html(""); 
    });
    $('#inspector').click(function(){
        $('#er_inspector').html(""); 
    });
    $('#packing_type').click(function(){
        $('#er_packing_type').html(""); 
    });
    $('#unit_condition').click(function(){
        $('#er_unit_condition').html(""); 
    });
    $('#packing_operator').click(function(){
        $('#er_packing_operator').html(""); 
    });
    $('#pack_code_per_series').click(function(){
        $('#er_pack_code_per_series').html(""); 
    });
    $('#carton_no').keyup(function(){
        $('#er_carton_no').html(""); 
    });
    $('#pack_code').click(function(){
        $('#er_pack_code').html(""); 
    });
    $('#judgement').click(function(){
        $('#er_judgement').html(""); 
    });
    $('#total_qty').keyup(function(){
        $('#er_total_qty').html(""); 
    });
    $('#no_of_defects').click(function(){
        $('#er_no_of_defects').html(""); 
    });
    $('#search_from').click(function(){
        $('#er_search_from').html(""); 
    });
    $('#search_to').click(function(){
        $('#er_search_to').html(""); 
    });
    $('#rc_no').keyup(function(){
        $('#er_rc_no').html(""); 
    });
    $('#rc_qty').keyup(function(){
        $('#er_rc_qty').html(""); 
    });
   
    $('#po_no').on('change',function(){
        var pono = $('#po_no').val();
        $.ajax({
            url:"{{ url('/getpackingMOLDINGYPICSrecords') }}",
            method:'get',
            data:{
                pono:pono
            },
        }).done(function(data, textStatus, jqXHR){ 
            $('#series_name').val(data[0]['DEVNAME']);
            if(pono == ""){
                $('#series_name').val("");
            }
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });

        $.ajax({
            url:"{{ url('/getTotalMOLDINGmod') }}",
            method:'get',
            data:{
                pono:pono
            },
        }).done(function(data, textStatus, jqXHR){ 
            console.log(data[0]['qty']);
            $('#no_of_defects').val(data[0]['qty']);
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });
    });

    $('#carton_no').focusout(function(){
        var pono = $('#po_no').val();
        var cartonno = $('#carton_no').val();
        $.ajax({
            url:"{{ url('/getTotalMOLDINGruncard') }}",
            method:'get',
            data:{
                pono:pono,
                cartonno:cartonno
            },
        }).done(function(data, textStatus, jqXHR){ 
            $('#total_qty').val(data[0]['qty']);
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });
    });

    $('#btn_pdf').on('click', function() {
        var searchpono = $('#search_pono').val();
        var datefrom = $('#search_from').val();
        var dateto = $('#search_to').val();
        var status = $('#hd_report_status').val();

        var tableData = {
            date_inspected: $('input[name^="hd_date_inspected[]"]').map(function(){return $(this).val();}).get(),
            shipment_date: $('input[name^="hd_shipment_date[]"]').map(function(){return $(this).val();}).get(),
            device_name: $('input[name^="hd_device_name[]"]').map(function(){return $(this).val();}).get(),
            po_num: $('input[name^="hd_po_num[]"]').map(function(){return $(this).val();}).get(),
            packing_operator: $('input[name^="hd_packing_operator[]"]').map(function(){return $(this).val();}).get(),
            inspector: $('input[name^="hd_inspector[]"]').map(function(){return $(this).val();}).get(),
            packing_type: $('input[name^="hd_packing_type[]"]').map(function(){return $(this).val();}).get(),
            unit_condition: $('input[name^="hd_unit_condition[]"]').map(function(){return $(this).val();}).get(),
            packing_code_series: $('input[name^="hd_packing_code_series[]"]').map(function(){return $(this).val();}).get(),
            carton_num: $('input[name^="hd_carton_num[]"]').map(function(){return $(this).val();}).get(),
            packing_code: $('input[name^="hd_packing_code[]"]').map(function(){return $(this).val();}).get(),
            total_qty: $('input[name^="hd_total_qty[]"]').map(function(){return $(this).val();}).get(),
            judgement: $('input[name^="hd_judgement[]"]').map(function(){return $(this).val();}).get(),
            remarks: $('input[name^="hd_remarks[]"]').map(function(){return $(this).val();}).get(),
            searchpono:searchpono,
            datefrom:datefrom,
            dateto:dateto,
            status:status,
        }
    
        var url = "{{ url('/packingMOLDINGprintreport?data=')  }}" + encodeURIComponent(JSON.stringify(tableData));
        window.location.href= url;
    });

    $('#btn_excel').on('click', function(){
        var searchpono = $('#search_pono').val();
        var datefrom = $('#search_from').val();
        var dateto = $('#search_to').val();
        var status = $('#hd_report_status').val();

        var tableData = {
            date_inspected: $('input[name^="hd_date_inspected[]"]').map(function(){return $(this).val();}).get(),
            shipment_date: $('input[name^="hd_shipment_date[]"]').map(function(){return $(this).val();}).get(),
            device_name: $('input[name^="hd_device_name[]"]').map(function(){return $(this).val();}).get(),
            po_num: $('input[name^="hd_po_num[]"]').map(function(){return $(this).val();}).get(),
            packing_operator: $('input[name^="hd_packing_operator[]"]').map(function(){return $(this).val();}).get(),
            inspector: $('input[name^="hd_inspector[]"]').map(function(){return $(this).val();}).get(),
            packing_type: $('input[name^="hd_packing_type[]"]').map(function(){return $(this).val();}).get(),
            unit_condition: $('input[name^="hd_unit_condition[]"]').map(function(){return $(this).val();}).get(),
            packing_code_series: $('input[name^="hd_packing_code_series[]"]').map(function(){return $(this).val();}).get(),
            carton_num: $('input[name^="hd_carton_num[]"]').map(function(){return $(this).val();}).get(),
            packing_code: $('input[name^="hd_packing_code[]"]').map(function(){return $(this).val();}).get(),
            total_qty: $('input[name^="hd_total_qty[]"]').map(function(){return $(this).val();}).get(),
            judgement: $('input[name^="hd_judgement[]"]').map(function(){return $(this).val();}).get(),
            remarks: $('input[name^="hd_remarks[]"]').map(function(){return $(this).val();}).get(),
            searchpono:searchpono,
            datefrom:datefrom,
            dateto:dateto,
            status:status,
        }

        var url = "{{ url('/packingMOLDINGprintreportexcel?data=')  }}" + encodeURIComponent(JSON.stringify(tableData));
        window.location.href= url;
    });

    $('#group1').on('change',function(){
        var g1 = $('select[name=group1]').val();
        var myData = {g1:g1};
        $('#group1content').html("");
        $('#tblforpacking').html("");
        $.post("{{ url('/packingMOLDINGselectgroupby1') }}",
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
                        var sup = '<option value="'+val.date_inspected+'">'+val.date_inspected+'</option>';
                        break;
                    case "shipment_date":
                        var sup = '<option value="'+val.shipment_date+'">'+val.shipment_date+'</option>';
                        break;
                    case "device_name":
                        var sup = '<option value="'+val.device_name+'">'+val.device_name+'</option>';
                        break;
                    case "po_num":
                        var sup = '<option value="'+val.po_num+'">'+val.po_num+'</option>';
                        break;
                    case "packing_operator":
                        var sup = '<option value="'+val.packing_operator+'">'+val.packing_operator+'</option>';
                        break;
                    case "inspector":
                        var sup = '<option value="'+val.inspector+'">'+val.inspector+'</option>';
                        break;
                    case "packing_type":
                        var sup = '<option value="'+val.packing_type+'">'+val.packing_type+'</option>';
                        break;
                    case "unit_condition":
                        var sup = '<option value="'+val.unit_condition+'">'+val.unit_condition+'</option>';
                        break;
                    case "packing_code_series":
                        var sup = '<option value="'+val.packing_code_series+'">'+val.packing_code_series+'</option>';
                        break;
                    case "carton_num":
                        var sup = '<option value="'+val.carton_num+'">'+val.carton_num+'</option>';
                        break;
                    case "packing_code":
                        var sup = '<option value="'+val.packing_code+'">'+val.packing_code+'</option>';
                        break;
                    case "total_qty":
                        var sup = '<option value="'+val.total_qty+'">'+val.total_qty+'</option>';
                        break;
                    case "judgement":
                        var sup = '<option value="'+val.judgement+'">'+val.judgement+'</option>';
                        break;
                    default:
                        var sup = '<option value=""></option>';
                }
                    
                //var option = '<option value="'+val.supplier'">'+val.supplier'</option>';
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
        $('#tblforpacking').html("");
        $.post("{{ url('/packingMOLDINGselectgroupby1') }}",
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
                        var sup = '<option value="'+val.date_inspected+'">'+val.date_inspected+'</option>';
                        break;
                    case "shipment_date":
                        var sup = '<option value="'+val.shipment_date+'">'+val.shipment_date+'</option>';
                        break;
                    case "device_name":
                        var sup = '<option value="'+val.device_name+'">'+val.device_name+'</option>';
                        break;
                    case "po_num":
                        var sup = '<option value="'+val.po_num+'">'+val.po_num+'</option>';
                        break;
                    case "packing_operator":
                        var sup = '<option value="'+val.packing_operator+'">'+val.packing_operator+'</option>';
                        break;
                    case "inspector":
                        var sup = '<option value="'+val.inspector+'">'+val.inspector+'</option>';
                        break;
                    case "packing_type":
                        var sup = '<option value="'+val.packing_type+'">'+val.packing_type+'</option>';
                        break;
                    case "unit_condition":
                        var sup = '<option value="'+val.unit_condition+'">'+val.unit_condition+'</option>';
                        break;
                    case "packing_code_series":
                        var sup = '<option value="'+val.packing_code_series+'">'+val.packing_code_series+'</option>';
                        break;
                    case "carton_num":
                        var sup = '<option value="'+val.carton_num+'">'+val.carton_num+'</option>';
                        break;
                    case "packing_code":
                        var sup = '<option value="'+val.packing_code+'">'+val.packing_code+'</option>';
                        break;
                    case "total_qty":
                        var sup = '<option value="'+val.total_qty+'">'+val.total_qty+'</option>';
                        break;
                    case "judgement":
                        var sup = '<option value="'+val.judgement+'">'+val.judgement+'</option>';
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
        $('#tblforpacking').html("");
        $.post("{{ url('/packingMOLDINGselectgroupby1') }}",
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
                        var sup = '<option value="'+val.date_inspected+'">'+val.date_inspected+'</option>';
                        break;
                    case "shipment_date":
                        var sup = '<option value="'+val.shipment_date+'">'+val.shipment_date+'</option>';
                        break;
                    case "device_name":
                        var sup = '<option value="'+val.device_name+'">'+val.device_name+'</option>';
                        break;
                    case "po_num":
                        var sup = '<option value="'+val.po_num+'">'+val.po_num+'</option>';
                        break;
                    case "packing_operator":
                        var sup = '<option value="'+val.packing_operator+'">'+val.packing_operator+'</option>';
                        break;
                    case "inspector":
                        var sup = '<option value="'+val.inspector+'">'+val.inspector+'</option>';
                        break;
                    case "packing_type":
                        var sup = '<option value="'+val.packing_type+'">'+val.packing_type+'</option>';
                        break;
                    case "unit_condition":
                        var sup = '<option value="'+val.unit_condition+'">'+val.unit_condition+'</option>';
                        break;
                    case "packing_code_series":
                        var sup = '<option value="'+val.packing_code_series+'">'+val.packing_code_series+'</option>';
                        break;
                    case "carton_num":
                        var sup = '<option value="'+val.carton_num+'">'+val.carton_num+'</option>';
                        break;
                    case "packing_code":
                        var sup = '<option value="'+val.packing_code+'">'+val.packing_code+'</option>';
                        break;
                    case "total_qty":
                        var sup = '<option value="'+val.total_qty+'">'+val.total_qty+'</option>';
                        break;
                    case "judgement":
                        var sup = '<option value="'+val.judgement+'">'+val.judgement+'</option>';
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

    $('#packmod_close').click(function(){
        $('#packmod_Modal').modal('hide');
        $('#AddNewModal').modal('show');
        totalmod();
    });
    $('#btn_rc_close').click(function(){
        totalquantity();
    });

    $('.edit-taskinspection').on('click',function(){
        var field = $(this).val().split('|');
        var id = field[0];
        $.ajax({
            url:"{{ url('/packMOLDINGmod_edit') }}",
            method:'get',
            data:{
                id:id
            },
        }).done(function(data, textStatus, jqXHR){
            console.log(data);
            $('#mod_inspection').val(data[0]['mod']);
            $('#qty_inspection').val(data[0]['qty']);
            $('#id_inspection').val(id);
            $('#status_inspection').val("EDIT");
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });
    });  

    $('input[name=inspector]').val("{{Auth::user()->user_id}}");
});//-----------------------------------------------------END OF SCRIPT-----------------------------------------


function totalquantity() {
    var pono = $('#po_no').val();
    var cartonno = $('#carton_no').val();
    $.ajax({
        url:"{{ url('/getMOLDINGlot') }}",
        method:'get',
        data:{
            pono:pono,
            cartonno:cartonno
        },
    }).done(function(data, textStatus, jqXHR){     
        console.log(data);
        $('#total_qty').val(data[0]['qty']);
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
}
function totalmod() {
    var pono = $('#po_no').val();
    $.ajax({
        url:"{{ url('/getMOLDINGmod') }}",
        method:'get',
        data:{
            pono:pono
        },
    }).done(function(data, textStatus, jqXHR){     
        console.log(data);
        $('#no_of_defects').val(data[0]['qty']);
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
}
function packingsave(){
    var pono = $('#po_no').val();
    var inspdate = $('#insp_date').val();
    var shipdate = $('#ship_date').val();
    var seriesname = $('#series_name').val();
    var inspector = $('#inspector').val();
    var packingtype = $('#packing_type').val();
    var unitcondition = $('#unit_condition').val();
    var packingoperator = $('#packing_operator').val();
    var remarks = $('#remarks').val();
    var packcodeperseries = $('#pack_code_per_series').val();
    var cartonno = $('#carton_no').val();
    var packcode = $('#pack_code').val();
    var judgement = $('#judgement').val();
    var totalqty = $('#total_qty').val();
    var mod = $('#no_of_defects').val();
    var dbcon = "{{Auth::user()->productline}}";
    var status = $('#status').val();
    var id = $('#id').val();
    var myData = {pono:pono,inspdate:inspdate,shipdate:shipdate,seriesname:seriesname,inspector:inspector,packingtype:packingtype,unitcondition:unitcondition,packingoperator:packingoperator,remarks:remarks,packcodeperseries:packcodeperseries,cartonno:cartonno,packcode:packcode,judgement:judgement,totalqty:totalqty,mod:mod,dbcon:dbcon,status:status,id:id};

    if(pono == ""){     
        $('#er_po_no').html("PO Number field is empty"); 
        $('#er_po_no').css('color', 'red');       
        return false;  
    }
    if(inspdate == ""){     
        $('#er_insp_date').html("Inspection Date field is empty"); 
        $('#er_insp_date').css('color', 'red');       
        return false;  
    }
    if(shipdate == ""){     
        $('#er_ship_date').html("Ship Date field is empty"); 
        $('#er_ship_date').css('color', 'red');       
        return false;  
    }
    if(seriesname == ""){     
        $('#er_series_name').html("Series Name field is empty"); 
        $('#er_series_name').css('color', 'red');       
        return false;  
    }
    if(inspector == ""){     
        $('#er_inspector').html("Inspector field is empty"); 
        $('#er_inspector').css('color', 'red');       
        return false;  
    }
    if(packingtype == ""){     
        $('#er_packing_type').html("Packing Type field is empty"); 
        $('#er_packing_type').css('color', 'red');       
        return false;  
    }
    if(unitcondition == ""){     
        $('#er_unit_condition').html("Unit Condition field is empty"); 
        $('#er_unit_condition').css('color', 'red');       
        return false;  
    }
    if(packingoperator == ""){     
        $('#er_packing_operator').html("Packing Operator Number field is empty"); 
        $('#er_packing_operator').css('color', 'red');       
        return false;  
    }
    if(packcodeperseries == ""){     
        $('#er_pack_code_per_series').html("Pack Code Per Series field is empty"); 
        $('#er_pack_code_per_series').css('color', 'red');       
        return false;  
    }
    if(cartonno == ""){     
        $('#er_carton_no').html("Carton Number field is empty"); 
        $('#er_carton_no').css('color', 'red');       
        return false;  
    }
    if(packcode == ""){     
        $('#er_pack_code').html("Pack Code field is empty"); 
        $('#er_pack_code').css('color', 'red');       
        return false;  
    }
    if(judgement == ""){     
        $('#er_judgement').html("Judgement field is empty"); 
        $('#er_judgement').css('color', 'red');       
        return false;  
    }
    if(totalqty == ""){     
        $('#er_total_qty').html("Total Quantity field is empty"); 
        $('#er_total_qty').css('color', 'red');       
        return false;  
    }
    if(mod == ""){     
        $('#er_no_of_defects').html("Mode of Defects field is empty"); 
        $('#er_no_of_defects').css('color', 'red');       
        return false;  
    }

    $.post("{{ url('/packingMOLDINGSave') }}",
    {
        _token:$('meta[name=csrf-token]').attr('content'),
        data:myData
    }).done(function(data, textStatus, jqXHR){
        /*console.log(data);*/
        window.location.href = "{{ url('/packingmolding') }}";
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
}
function rc_save(){
    var pono = $('#po_no').val();
    var carton_no= $('#carton_no').val();
    var rcid = $('#rc_id').val();
    var rcno = $('#rc_no').val();
    var rcqty = $('#rc_qty').val();
    var rcremarks = $('#rc_remarks').val();
    var rcstatus = $('#rc_status').val();
    if(rcno == ""){
        $('#er_rc_no').html("Runcard Number field is empty");
        $('#er_rc_no').css('color','red');
        return false;
    }
    if(rcqty == ""){
        $('#er_rc_qty').html("Quantity field is empty");
        $('#er_rc_qty').css('color','red');
        return false;
    }
 
    $('#tblforruncard').html("");
    var myData = {rcid:rcid,rcno:rcno,rcqty:rcqty,rcremarks:rcremarks,pono:pono,carton_no:carton_no,rcstatus:rcstatus};
    $.post("{{ url('/packingMOLDING_runcard_Save') }}",
    {
        _token:$('meta[name=csrf-token]').attr('content'),
        data:myData
    }).done(function(data, textStatus, jqXHR){
        console.log(data);
        $.each(data,function(i,val){
            var tblrow = '<tr>'+
                            '<td style="width: 2%">'+
                                '<input type="checkbox" class="form-control input-sm rccheckboxes" value="'+val.id+'" name="rccheckitem" id="rccheckitem"></input> '+
                            '</td> '+                       
                            '<td style="width: 5%"> '+          
                                '<button type="button" name="rcedit-task" value="'+val.id+ '|' +val.po_no+ '|' +val.caton_no+'|'+val.runcard_no+'|'+val.runcard_qty+'|'+val.runcard_remarks+'" class="btn btn-sm btn-primary rcedit-task">'+
                                       '<i class="fa fa-edit"></i> '+
                                '</button>'+
                            '</td>'+
                            '<td>'+val.po_no+'</td>'+
                            '<td>'+val.runcard_no+'</td>'+
                            '<td>'+val.runcard_qty+'</td>'+
                            '<td>'+val.runcard_remarks+'</td>'+
                        '</tr>';
            $('#tblforruncard').append(tblrow);
            $('#rc_no').val("");
            $('#rc_qty').val("");
            $('#rc_remarks').val("");
            $('#rc_status').val("ADD");

            $('.rcedit-task').on('click', function(e) {
                $('#rc_status').val("EDIT");
                var edittext = $(this).val().split('|');
                var editid = edittext[0];
                $.ajax({
                    url:"{{ url('/packingMOLDING_runcard_edit') }}",
                    method:'get',
                    data:{
                        id:editid
                    }
                }).done(function(data, textStatus, jqXHR){
                    $('#rc_id').val(data[0]['id']);
                    $('#rc_packcode').val(data[0]['packing_code']);
                    $('#rc_no').val(data[0]['runcard_no']);
                    $('#rc_qty').val(data[0]['runcard_qty']);
                    $('#rc_remarks').val(data[0]['runcard_remarks']);
                }).fail(function(jqXHR,textStatus,errorThrown){
                    console.log(errorThrown+'|'+textStatus)
                });
            });
        });  
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });

}

function packingDelete(){
    var tray = [];
    $('.checkboxes:checked').each(function(){
        tray.push($(this).val());
    });

    var traycount = tray.length;
    var myData = {tray:tray,traycount:traycount};
    $.ajax({
            url:"{{ url('/packingMOLDINGDelete') }}",
            method:'get',
            data:myData
                 
    }).done(function(data, textStatus, jqXHR){
        window.location.href="{{ url('/packingmolding') }}";
       /* alert(data);*/
    }).fail(function(jqXHR, textStatus,errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
}

function rcpackingDelete(){
    var tray = [];
    $('.rccheckboxes:checked').each(function(){
        tray.push($(this).val());
    });
    var pono = $('#po_no').val();
    var traycount = tray.length;
    var myData = {tray:tray,traycount:traycount,pono:pono};
    $('#tblforruncard').html("");
    $.ajax({
            url:"{{ url('/rcpackingMOLDINGDelete') }}",
            method:'get',
            data:myData            
    }).done(function(data, textStatus, jqXHR){
        console.log(data);
        $.each(data,function(i,val){
                var tblrow = '<tr>'+
                            '<td style="width: 2%">'+
                                '<input type="checkbox" class="form-control input-sm rccheckboxes" value="'+val.id+'" name="rccheckitem" id="rccheckitem"></input> '+
                            '</td> '+                       
                            '<td style="width: 5%"> '+          
                                '<button type="button" name="rcedit-task" value="'+val.id+ '|' +val.po_no+ '|' +val.caton_no+'|'+val.runcard_no+'|'+val.runcard_qty+'|'+val.runcard_remarks+'" class="btn btn-sm btn-primary rcedit-task">'+
                                       '<i class="fa fa-edit"></i> '+
                                '</button>'+
                            '</td>'+
                            '<td>'+val.po_no+'</td>'+
                            '<td>'+val.runcard_no+'</td>'+
                            '<td>'+val.runcard_qty+'</td>'+
                            '<td>'+val.runcard_remarks+'</td>'+
                        '</tr>';
                $('#tblforruncard').append(tblrow);
                $('#rc_no').val("");
                $('#rc_qty').val("");
                $('#rc_remarks').val("");
                $('#rc_status').val("ADD");

                $('.rcedit-task').on('click', function(e) {
                    $('#rc_status').val("EDIT");
                    var edittext = $(this).val().split('|');
                    var editid = edittext[0];
                    $.ajax({
                        url:"{{ url('/packingMOLDING_runcard_edit') }}",
                        method:'get',
                        data:{
                            id:editid
                        }
                    }).done(function(data, textStatus, jqXHR){
                        $('#rc_id').val(data[0]['id']);
                        $('#rc_packcode').val(data[0]['packing_code']);
                        $('#rc_no').val(data[0]['runcard_no']);
                        $('#rc_qty').val(data[0]['runcard_qty']);
                        $('#rc_remarks').val(data[0]['runcard_remarks']);
                    }).fail(function(jqXHR,textStatus,errorThrown){
                        console.log(errorThrown+'|'+textStatus)
                    });
                });
            });  
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
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
    alert(datefrom);
    alert(dateto);

    var myData = {g1:g1,g2:g2,g3:g3,g4:g4,g5:g5,g1content:g1content,g2content:g2content,g3content:g3content,g4content:g4content,g5content:g5content,datefrom:datefrom,dateto:dateto};
    $('#tblforpacking').html("");
    $.post("{{ url('/packingMOLDINGdbgroupby') }}",
    {
        _token:$('meta[name=csrf-token]').attr('content'),
        data:myData

    }).done(function(data, textStatus, jqXHR){
        console.log(data);
        PackmoldgetDataTable(data)
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
}
function searchby(){
    var tempdatefrom = $('#search_from').val().split('/');
    var tempdateto = $('#search_to').val().split('/');
    var pono = $('#search_pono').val();
    var datefrom = tempdatefrom[0]+'/'+tempdatefrom[1]+'/'+tempdatefrom[2];
    var dateto = tempdateto[0]+'/'+tempdateto[1]+'/'+tempdateto[2];
   /* if(tempdatefrom == ""){     
        $('#er_search_from').html("Date From field is empty"); 
        $('#er_search_from').css('color', 'red');       
        return false;  
    }
    if(tempdateto == ""){     
        $('#er_search_to').html("Search To field is empty"); 
        $('#er_search_to').css('color', 'red');       
        return false;  
    }*/
    $('#tblforpacking').html("");
    $.get("{{ url('/packingMOLDINGsearchby') }}",
    {
        _token:$('meta[name=csrf-token]').attr('content'),
        datefrom:datefrom,
        dateto:dateto,
        pono:pono
    }).done(function(data, textStatus, jqXHR){
        PackmoldgetDataTable(data);
    }).fail(function(jqXHR, errorThrown, textStatus){
        console.log(errorThrown+'|'+textStatus);
    });
}

function lot_accepted() {             
    $('#judgement').change(function(){
        if($(this).val() == 'Reject') {
            $('#no_of_defects').show();
            $('#btn_no_of_defectss').show();
            $('#no_defects_label').show();
            $('#mode_defects_label').show();
            $('#btn_packmod').show();
            totalmod();

        } 
        if($(this).val() == 'Accept') {
            $('#no_of_defects').show();
            $('#btn_no_of_defectss').hide();
            $('#no_defects_label').show();
            $('#mode_defects_label').hide();
            $('#btn_packmod').hide();
            $('#no_of_defects').val("NDF");
        }
    });
}

function display_packmod(){
    var pono = $('input[name=po_no]').val();
    $('#packmod_Modal').modal('show');
    $('#AddNewModal').modal('hide');
    $('#status_inspection').val("ADD")
    $('#tblformodinspection').html("");
    $.ajax({
        url:"{{ url('/displayMOLDINGpackmod') }}",
        method:'get',
        data:{
            pono:pono,
        },
    }).done(function(data, textStatus,jqXHR){
        console.log(data);
        $.each(data,function(i,val){
            var tblrow = '<tr>'+                   
                            '<td style="width: 2%">'+
                                '<input type="checkbox" class="form-control input-sm checkboxesinspection" value="'+val.id+'" name="checkiteminspection" id="checkiteminspection"></input> '+
                            '</td>'+                        
                            '<td style="width: 5%">'+           
                                '<button type="button" name="edit-taskinspection" class="btn btn-sm btn-primary edit-taskinspection" value="'+val.id+ '|' +val.mod+ '|' +val.qty+'">'+
                                       '<i class="fa fa-edit"></i> '+
                                '</button>'+
                            '</td>'+
                            '<td>'+val.id+'</td>'+
                            '<td>'+val.mod+'</td>'+
                            '<td>'+val.qty+'</td>'+
                        '</tr>';
            $('#tblformodinspection').append(tblrow);
            $('#mod_inspection').val("");
            $('#qty_inspection').val("");
            $('#id_inspection').val("");
            $('#status_inspection').val("ADD");
            $('.edit-taskinspection').on('click',function(){
            var field = $(this).val().split('|');
                var id = field[0];
                $.ajax({
                    url:"{{ url('/packMOLDINGmod_edit') }}",
                    method:'get',
                    data:{
                        id:id
                    },
                }).done(function(data, textStatus, jqXHR){
                    console.log(data);
                    $('#mod_inspection').val(data[0]['mod']);
                    $('#qty_inspection').val(data[0]['qty']);
                    $('#id_inspection').val(id);
                    $('#status_inspection').val("EDIT");
                }).fail(function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown+'|'+textStatus);
                });
            });     
        });
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
}

function packmod_save(){
    var mod = $('select[name=mod_inspection]').val();
    var qty = $('#qty_inspection').val();
    var status = $('#status_inspection').val();
    var id = $('#id_inspection').val();
    var pono = $('input[name=po_no]').val();
    if(mod == ""){
        $('#er_mod').html("Mode of Defect field is empty");
        $('#er_mod').css('color','red');
        return false;
    }
    if(qty == ""){ 
        $('#er_qty').html("Quantity field is empty");
        $('#er_qty').css('color','red');
        return false;
    }
    $('#tblformodinspection').html("");
    $.post("{{ url('/packMOLDINGmod_save')}}",
    {
        _token:$('meta[name=csrf-token]').attr('content'),
        mod:mod,
        qty:qty,
        status:status,
        id:id,
        pono:pono
    }).done(function(data, textStatus, jqXHR){
        console.log(data);
        $.each(data,function(i,val){
            var tblrow = '<tr>'+                   
                            '<td style="width: 2%">'+
                                '<input type="checkbox" class="form-control input-sm checkboxesinspection" value="'+val.id+'" name="checkiteminspection" id="checkiteminspection"></input> '+
                            '</td>'+                        
                            '<td style="width: 5%">'+           
                                '<button type="button" name="edit-taskinspection" class="btn btn-sm btn-primary edit-taskinspection" value="'+val.id+ '|' +val.mod+ '|' +val.qty+'">'+
                                       '<i class="fa fa-edit"></i> '+
                                '</button>'+
                            '</td>'+
                            '<td>'+val.id+'</td>'+
                            '<td>'+val.mod+'</td>'+
                            '<td>'+val.qty+'</td>'+
                        '</tr>';
            $('#tblformodinspection').append(tblrow);
            $('#mod_inspection').val("");
            $('#qty_inspection').val("");
            $('#id_inspection').val("");
            $('#status_inspection').val("ADD");
            $('.edit-taskinspection').on('click',function(){
            var field = $(this).val().split('|');
                var id = field[0];
                $.ajax({
                    url:"{{ url('/packMOLDINGmod_edit') }}",
                    method:'get',
                    data:{
                        id:id
                    },
                }).done(function(data, textStatus, jqXHR){
                    console.log(data);
                    $('#mod_inspection').val(data[0]['mod']);
                    $('#qty_inspection').val(data[0]['qty']);
                    $('#id_inspection').val(id);
                    $('#status_inspection').val("EDIT");
                }).fail(function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown+'|'+textStatus);
                });
            });     
        });
        lot_accepted();
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
}

function deleteAllcheckedinspection(){
    var pono = $('input[name=po_no]').val();
    var tray = [];
    $('.checkboxesinspection:checked').each(function(){
        tray.push($(this).val());
    });
    var traycount = tray.length;
    var myData = {tray:tray,traycount:traycount,pono:pono};
    $('#tblformodinspection').html("");
    $.ajax({
            url:"{{ url('/packMOLDINGmod_delete') }}",
            method:'get',
            data:myData            
    }).done(function(data, textStatus, jqXHR){
        console.log(data);
        $.each(data,function(i,val){
            var tblrow = '<tr>'+                   
                            '<td style="width: 2%">'+
                                '<input type="checkbox" class="form-control input-sm checkboxesinspection" value="'+val.id+'" name="checkiteminspection" id="checkiteminspection"></input> '+
                            '</td>'+                        
                            '<td style="width: 5%">'+           
                                '<button type="button" name="edit-taskinspection" class="btn btn-sm btn-primary edit-taskinspection" value="'+val.id+ '|' +val.mod+ '|' +val.qty+'">'+
                                       '<i class="fa fa-edit"></i> '+
                                '</button>'+
                            '</td>'+
                            '<td>'+val.id+'</td>'+
                            '<td>'+val.mod+'</td>'+
                            '<td>'+val.qty+'</td>'+
                        '</tr>';
            $('#tblformodinspection').append(tblrow);   
            $('.edit-taskinspection').on('click',function(){
                var field = $(this).val().split('|');
                var id = field[0];
                $.ajax({
                    url:"{{ url('/packMOLDINGmod_edit') }}",
                    method:'get',
                    data:{
                        id:id
                    },
                }).done(function(data, textStatus, jqXHR){
                    console.log(data);
                    $('#mod_inspection').val(data[0]['mod']);
                    $('#qty_inspection').val(data[0]['qty']);
                    $('#id_inspection').val(id);
                    $('#status_inspection').val("EDIT");
                }).fail(function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown+'|'+textStatus);
                });
            });      
        });
        lot_accepted();
    }).fail(function(jqXHR, textStatus,errorThrown){
        console.log(errorThrown+'|'+textStatus);
    });
}

function loadPackdata(){
    $.get("{{ url('/packmoldgetrows') }}",
    {
        _token:$('meta[name=csrf-token]').attr('content')
    }).done(function(data, textStatus, jqXHR){
        console.log(data);
        var cnt = 0;
        PackmoldgetDataTable(data);
    });
}

function PackmoldgetDataTable(data) {
    var cnt = 0;
    $.each(data,function(i,val){
        cnt++;
        var report_status = $('#hd_report_status').val();
        var qty = '';
        if(val.qty == null){
            qty = 0;
        }else{
            qty = val.qty;
        }
        var tblrow = '<tr>'+
                        '<td>'+
                            '<input type="checkbox" class="form-control input-sm checkboxes" value="'+val.id+'" name="checkitem" id="checkitem"></input> '+
                        '</td>'+                        
                        '<td style="width: 5%">'+           
                            '<button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="'+val.id+ '|' +val.date_inspected+ '|' +val.shipment_date+ '|' +val.device_name+ '|' +val.inspector+ '|' +val.packing_type+ '|' +val.unit_condition+ '|' +val.packing_operator+ '|' +val.remarks+ '|' +val.packing_code_series+ '|' +val.carton_num+ '|' +val.packing_code+ '|' +val.judgement+ '|' +val.total_qty+ '|' +val.mode_of_defect+ '|' +val.dbcon+ '|' +val.po_num+'">'+
                            
                                '   <i class="fa fa-edit"></i> '+
                            '</button>'+
                        '</td>'+
                        '<td>'+val.date_inspected+'<input type="hidden" id="hd_date_inspected" value="'+val.date_inspected+'" name="hd_date_inspected[]"></td>'+
                        '<td>'+val.shipment_date+'<input type="hidden" id="hd_shipment_date" value="'+val.shipment_date+'" name="hd_shipment_date[]"></td>'+
                        '<td>'+val.device_name+'<input type="hidden" id="hd_device_name" value="'+val.device_name+'" name="hd_device_name[]"></td>'+
                        '<td>'+val.po_num+'<input type="hidden" id="hd_po_num" value="'+val.po_num+'" name="hd_po_num[]"></td>'+
                        '<td>'+val.packing_operator+'<input type="hidden" id="hd_packing_operator" value="'+val.packing_operator+'" name="hd_packing_operator[]"></td>'+
                        '<td>'+val.inspector+'<input type="hidden" id="hd_inspector" value="'+val.inspector+'" name="hd_inspector[]"></td>'+
                        '<td>'+val.packing_type+'<input type="hidden" id="hd_packing_type" value="'+val.packing_type+'" name="hd_packing_type[]"></td>'+
                        '<td>'+val.unit_condition+'<input type="hidden" id="hd_unit_condition" value="'+val.unit_condition+'" name="hd_unit_condition[]"></td>'+
                        '<td>'+val.packing_code_series+'<input type="hidden" id="hd_packing_code_series" value="'+val.packing_code_series+'" name="hd_packing_code_series[]"></td>'+
                        '<td>'+val.carton_num+'<input type="hidden" id="hd_carton_num" value="'+val.carton_num+'" name="hd_carton_num[]"></td>'+
                        '<td>'+val.packing_code+'<input type="hidden" id="hd_packing_code" value="'+val.packing_code+'" name="hd_packing_code[]"></td>'+
                        '<td>'+val.total_qty+'<input type="hidden" id="hd_total_qty" value="'+val.total_qty+'" name="hd_total_qty[]"></td>'+
                        '<td>'+val.judgement+'<input type="hidden" id="hd_judgement" value="'+val.judgement+'" name="hd_judgement[]"></td>'+
                        '<td>'+val.remarks+'<input type="hidden" id="hd_remarks" value="'+val.remarks+'" name="hd_remarks[]"></td> '+       
                    '</tr>';    
        $('#tblforpacking').append(tblrow);
    });
}
</script>
@endpush
