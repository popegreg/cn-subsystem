<!-- Select PO Details -->
<div id="SelectPODetailsModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog grey-gallery">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Select PO Details</h4>
            </div>
            <form class="form-horizontal" id="frm_select_po_details">
                {{ csrf_field() }}
                <div class="modal-body">
                    <table class="table table-bordered table-striped display nowrap" cellspacing="0" width="100%" style="font-size:10px" id="tbl_po_details">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" class="check_all_po_detail">
                                </th>
                                <th width="20%">Code</th>
                                <th width="35%">Description</th>
                                <th width="20%">Issued QTY</th>
                                <th width="20%">Lot No.</th>
                            </tr>
                        </thead>
                        <tbody id="tbl_po_details_body"></tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn_select_items">Select</button>
                    <a href="" data-dismiss="modal" class="btn btn-danger">Close</a>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit PO Details -->
<div id="EditPODetailsModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog grey-gallery">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Edit PO Details</h4>
            </div>
            <form class="form-horizontal" id="editpodetailfrm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label col-sm-3">Detail ID</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_detailid" readonly>
                                    <input type="hidden" name="item_id" id="edit_item_id">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Item/Part No.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_code" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Item Description</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_desc"readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Classification</label>
                                <div class="col-sm-9">
                                    <select class="form-control input-sm" id="edit_classification" name="edit_classification"></select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Issued Qty. (Kitting)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_issuedqty" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Request Qty.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_requestqty" name="edit_requestqty" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Requested By</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_requested_by" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3"></label>
                                <div class="col-sm-9">
                                    <input type="checkbox" id="cb_no_need_replacement" name="cb_no_need_replacement" />
                                    <label for="cb_no_need_replacement">NO NEED REPLACEMENT</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Remarks</label>
                                <div class="col-sm-9">
                                    <textarea id="edit_remarks" class="form-control input-sm" name="edit_remarks" style="resize:none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_update_req_item" class="btn btn-success" <?php if(isset($action)){if($action == 'VIEW'){ echo 'disabled';} } ?>>
                        Save
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Cancel Confirmation Pop-message -->
<div id="ConfirmModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm blue">
		<form role="form" method="POST" action="{{url('/wbsprodmatrequest/cancel-request')}}" id="frm_cancel">
			<div class="modal-content ">
				<div class="modal-body">
					<p>Are you sure you want to cancel this Request?</p>
					{!! csrf_field() !!}
					<input type="hidden" name="cancel_req_no" id="cancel_req_no"/>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="btn_confirm_cancel">Yes</button>
					<button type="button" data-dismiss="modal" class="btn">Cancel</button>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Search Modal -->
<div id="searchModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-full">

        <!-- Modal content-->
        <form class="form-horizontal" role="form" method="POST" action="{{url('/wbsprodmatrequest/search-request')}}" id="frm_search">
            {!! csrf_field() !!}
            <div class="modal-content blue">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                        	<div class="form-group">
                                <label for="srch_from" class="col-md-3 control-label">Date</label>
                                <div class="col-md-9">
                                    <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control input-sm reset" name="srch_from" id="srch_from"/>
                                        <span class="input-group-addon">to </span>
                                        <input type="text" class="form-control input-sm reset" name="srch_to" id="srch_to"/>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputsearch_pono" class="col-md-3 control-label" style="font-size:12px">PO No.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" id="srch_po" placeholder="PO No." name="srch_po" autofocus <?php echo($readonly); ?> />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputsearch_req_no" class="col-md-3 control-label" style="font-size:12px">Request No.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" id="srch_req_no" placeholder="Request No." name="srch_req_no" autofocus <?php echo($readonly); ?> />
                                </div>
                            </div>
                        
                            {{-- <div class="form-group">
                                <label for="inputsearch_proddes" class="col-md-3 control-label" style="font-size:12px">Product Destination</label>
                                <div class="col-md-9">
                                    <select class="form-control input-sm select2me" id="srch_prodes" name="srch_prodes"></select>
                                </div>
                           	</div>

                           	<div class="form-group">
	                            <label for="inputcode" class="col-md-3 control-label" style="font-size:12px">Line Destination</label>
                                <div class="col-md-9">
                                    <select class="form-control input-sm select2me" id="srch_linedes" name="srch_linedes"></select>
                                </div>
		                    </div> --}}

		                    <div class="form-group">
		                    	<label class="col-md-3 control-label">Status</label>
								<div class="md-checkbox-inline">
									<div class="md-checkbox">
										<input type="checkbox" id="srch_open" class="md-check" name="srch_open" value="Open">
										<label for="srch_open">
										<span></span>
										<span class="check"></span>
										<span class="box"></span>
										Alert </label>
									</div>
									<div class="md-checkbox">
										<input type="checkbox" id="srch_close" class="md-check" name="srch_close" value="Close">
										<label for="srch_close">
										<span></span>
										<span class="check"></span>
										<span class="box"></span>
										Close </label>
									</div>
									<div class="md-checkbox">
										<input type="checkbox" id="srch_cancelled" class="md-check" name="srch_cancelled" value="Cancelled">
										<label for="srch_cancelled">
										<span></span>
										<span class="check"></span>
										<span class="box"></span>
										Cancelled </label>
									</div>
								</div>
		                    </div>
		                </div>
		                <div class="col-md-7">
		                	<table class="table table-striped table-bordered table-hover tabl-fixedheader" style="font-size:10px" id="tbl_search">
		                        <thead>
		                            <tr>
		                                <td width="10%"></td>
		                                <td width="10%">Transaction No.</td>
		                                <td width="10%">PO No.</td>
		                                <td width="10%">Product Destination</td>
		                                <td width="10%">Line Destination</td>
		                                <td width="10%">Status</td>
		                                <td width="10%">Created By</td>
		                                <td width="10%">Created Date</td>
		                                <td width="10%">Updated By</td>
		                                <td width="10%">Updated Date</td>
		                            </tr>
		                        </thead>
		                        <tbody id="tbl_search_body">
		                        </tbody>
		                    </table>
		                </div>
		            </div>
                </div>
                <div class="modal-footer">
		            <button type="submit" class="btn btn-sm blue-madison"><i class="fa fa-search"></i> Search</button>
		            <button type="button" class="btn btn-sm green" ><i class="glyphicon glyphicon-repeat"></i> Reset</button>
		            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
		        </div>
            </div>
        </form>
    </div>
</div>


<!-- Message -->
<div id="msgModal" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-sm grey-gallery">
		<div class="modal-content ">
		   <div class="modal-body">
			  <p id="msg">{{Session::get('msg')}}</p>
		  </div>
		  <div class="modal-footer">
			  <button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
		  </div>
	  </div>
  </div>
</div>

<div id="errmsg" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm grey-gallery">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="errtitle"></h4>
			</div>
			<div class="modal-body">
				<p id="error"></p>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
			</div>
		</div>
	</div>
</div>