<!-- add details -->
<div id="editIssuanceModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog gray-gallery modal-xl">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Add Details</h4>
            </div>
			<form class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-sm-12">
                                   <p>
                                       All fields are required.
                                   </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Detail ID.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_detail_id" name="edit_detail_id" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Item/Part No.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_item" name="edit_item" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Item Description</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_item_desc" name="edit_item_desc" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Request Detail ID</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_req_detail_id" name="edit_req_detail_id" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Request Qty.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_request_qty" name="edit_request_qty" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Served Qty.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_served_qty" name="edit_served_qty" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Issued Qty.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_issued_qty" name="edit_issued_qty">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Lot. No</label>
                                <div class="col-sm-9">
                                   <input type="text" class="form-control input-sm" id="edit_lot_no" name="edit_lot_no">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Location</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm" id="edit_location" name="edit_location" readonly>
                                    <input type="hidden" id="edit_inv_id" name="edit_inv_id">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">Reason</label>
                                <div class="col-sm-9">
                                    <textarea class="col-md-3 form-control" id="iss_remarks" name="iss_remarks"></textarea> 
                                 	<input type="hidden" class="form-control iss_clear input-sm" id="user" name="user">
								     <input type="hidden" class="form-control iss_clear input-sm" id="id_reason" name="id_reason">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                        	<div class="table-responsive">
								<table class="table table-bordered"  id="tbl_inventory" style="font-size:10px">
									<thead>
										<tr>
											<td></td>
											<td>Item Code</td>
											<td>Description</td>
											<td>Qty</td>
											<td>Lot</td>
											<td>Received Date</td>
										</tr>
									</thead>
									<tbody id="tbl_inventory_body"></tbody>
								</table>
							</div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_update_details" class="btn btn-success">Save</button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
			</form>
        </div>
    </div>
</div>

<!-- Search Modal -->
<div id="searchModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-full">
		<div class="modal-content blue">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Search</h4>
			</div>
			<form class="form-horizontal" method="post" action="{{ url('/whs-issuance/search-request') }}" id="frm_search">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-5">
							{{ csrf_field() }}
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
								<label for="inputname" class="col-md-3 control-label" style="font-size:12px">Issuance No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="srch_issuance_no" placeholder="Issuance No." name="srch_issuance_no" autofocus <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label for="inputname" class="col-md-3 control-label" style="font-size:12px">Request No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="srch_request_no" placeholder="Request No." name="srch_request_no" <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Status</label>
								<div class="md-checkbox-inline">
									<div class="md-checkbox">
										<input type="checkbox" id="srch_serving" class="md-check" name="srch_serving" value="Serving">
										<label for="srch_serving">
										<span></span>
										<span class="check"></span>
										<span class="box"></span>
										Serving </label>
									</div>
									<div class="md-checkbox">
										<input type="checkbox" id="srch_closed" class="md-check" name="srch_closed" value="Closed">
										<label for="srch_closed">
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
							<div class="table-responsive">
								<table class="table table-striped table-bordered" style="font-size:10px" id="tbl_search">
									<thead>
										<tr>
											<td style="width: 8.5%"></td>
											<td style="width: 14.5%">Issuance No.</td>
											<td style="width: 14.5%">Request No.</td>
											<td style="width: 12.5%">Status</td>
											<td style="width: 12.5%">Created By</td>
											<td style="width: 12.5%">Created Date</td>
											<td style="width: 12.5%">Updated By</td>
											<td style="width: 12.5%">Updated Date</td>
										</tr>
									</thead>
									<tbody id="tbl_search_body">
									</tbody>
								</table>
							</div>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn blue-madison btn-sm"><i class="glyphicon glyphicon-filter"></i> Filter</button>
					<button type="button" class="btn green btn-sm" ><i class="glyphicon glyphicon-repeat"></i> Reset</button>
					<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="ConfirmModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm blue">
		<form role="form" method="POST" action="{{url('/whs-issuance/cancel-issuance')}}" id="frm_cancel">
			<div class="modal-content ">
				<div class="modal-body">
					<p>Are you sure you want to cancel this Issuance?</p>
					{!! csrf_field() !!}
					<input type="hidden" name="cancel_issuance_no" id="cancel_issuance_no"/>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="btn_confirm_cancel">Yes</button>
					<button type="button" data-dismiss="modal" class="btn">Cancel</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!--AuthorizedUser -->
<div id="authorizedmodal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 id="tit" class="modal-title">FIFO Alert</h4>
			</div>
			<div class="modal-body">
				<p>FIFO is recommended, but you can specify your reason for using this Lot number.</p>
				<input type="hidden" name="f_lot_no" id="f_lot_no">
				<input type="hidden" name="f_inv_id" id="f_inv_id">
				<input type="hidden" name="f_item" id="f_item">
				<input type="hidden" name="f_item_desc" id="f_item_desc">
				{{-- <input type="text" name="f_issuanceno" id="f_issuanceno"> --}}
					<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group">
                                <label class="col-md-3 control-label">Reason: </label>
                                <div class="col-md-7">
									<select class="form-control input-sm" id="reason_id" name="reason_id">
										<option value=""></option>
						                @foreach($data as $fifo)
                                                <option value="{{$fifo->id}}">{{$fifo->dropdown_reason}}
                                                </option>
                                            @endforeach
              					  </select>
			
                                </div>
                            </div>
							<div class="form-group">
								<label class="col-md-3 control-label"><i class="fa fa-user"></i> Username:</label>
								<div class="col-md-7">
									<input type="text" class="form-control input-sm" id="user_id"name="user_id">
								</div>
							</div>
	
							<div class="form-group">
								<label class="col-md-3 control-label"><i class="fa fa-lock"></i> Password:</label>
								<div class="col-md-7">
									<input type="password" class="form-control input-sm" id="password" name="password">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><i class=""></i>Other Reason:</label> 
								<div class="col-md-7">
									 <textarea class="col-md-3 form-control" id="fiforeason"></textarea>  
								</div>
							</div>

						</form>
						
						
					</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" id="btn_fiforeason" class="btn btn-success">OK</a>
				<button type="button" data-dismiss="modal" class="btn btn-danger" id="err_msg_close">Close</button>
			</div>
		</div>
	</div>
</div>