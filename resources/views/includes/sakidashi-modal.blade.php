<!-- Item Modal -->
<div id="itemModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Select PO Part Details</h4>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-striped table-bordered table-hover" style="font-size:10px" id="item_tbl"> {{-- table-fixedheader --}}
						<thead>
							<tr>
								<td>Item Part/No.</td>
								<td>Item Description</td>
								<td>Sched Qty.</td>
								<td>Actual Total</td>
								<td></td>
							</tr>
						</thead>
						<tbody id="item_tbl_body">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="item_close"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<!-- FIFO Modal -->
<div id="fifoModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Select Lot Number</h4>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-fifo" id="tblfifo" style="font-size:10px">
							<thead>
								<tr>
									<td width="12.5%">Item Code</td>
									<td width="20.5%">Description</td>
									{{-- <td width="12.5%">Received Qty</td> --}}
									<td width="8.5%">Quantity</td>
									<td width="12.5%">Lot</td>
									<td width="10.5%">Location</td>
									<td width="14.5%">Received Date</td>
									<td width="8.5%"></td>
								</tr>
							</thead>
							<tbody id="tblfifoAdd"></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="fifo_close"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<!-- FIFO prompt -->
{{-- <div id="fifoReasonModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 id="tit" class="modal-title">FIFO Alert</h4>
			</div>
			<div class="modal-body">
				<p>FIFO is recommended, but you can specify your reason for using this Lot number.</p>
				<textarea class="form-control" id="fiforeason"></textarea>
				<input type="hidden" name="frlotno" id="frlotno">
				<input type="hidden" name="fritem" id="fritem">
				<input type="hidden" name="fritem_desc" id="fritem_desc">
				<input type="hidden" name="frqty" id="frqty">
				<input type="hidden" name="frfifo" id="frfifo">
			</div>
			<div class="modal-footer">
				<button type="button" id="btn_fiforeason" class="btn btn-success">OK</button>
				<button type="button" data-dismiss="modal" class="btn btn-danger" id="err_msg_close">Close</button>
			</div>
		</div>
	</div>
</div> --}}
<!--DEAN-->
<div id="fifoReasonModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 id="tit" class="modal-title">FIFO Alert</h4>
			</div>
			<div class="modal-body">
				<p>FIFO is recommended, but you can specify your reason for using this Lot number.</p>
				<input type="hidden" name="frlotno" id="frlotno">
				<input type="hidden" name="fritem" id="fritem">
				<input type="hidden" name="fritem_desc" id="fritem_desc">
				<input type="hidden" name="frqty" id="frqty">
				<input type="hidden" name="frfifo" id="frfifo">
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
								<label class="col-md-3 control-label"><i class="fa fa-user"></i> Password:</label>
								<div class="col-md-7">
									<input type="password" class="form-control input-sm" id="password"name="password">
								</div>
							</div>

							<div class="form-group">
							<label class="col-md-3 control-label"><i class=""></i>Other Reason:</label> 
								<div class="col-md-7">
									 <textarea class="col-md-3 form-control" id="fiforeason"></textarea>  
								</div>
							</div>

						</form>
						{{-- <textarea class="form-control" id="fiforeason"></textarea>  --}}
						
					</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" id="btn_fiforeason" class="btn btn-success">OK</a>
				<button type="button" data-dismiss="modal" class="btn btn-danger" id="err_msg_close">Close</button>
			</div>
		</div>
	</div>
</div>

<!--DEAN-->
<!-- Confirm -->
<div id="confirm" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm gray-gallery">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">WBS Sakidashi Issuance</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure?</p>
				<input type="hidden" name="confirm_status" id="confirm_status">
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" id="confirmyes" class="btn btn-success">Yes</button>
				<button type="button" data-dismiss="modal" id="confirmno" class="btn btn-danger">No</button>
			</div>
		</div>
	</div>
</div>

<!-- Search Modal -->
<div id="searchModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-full">
		<div class="modal-content blue">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Search</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-horizontal">
							<div class="form-group">
								<label for="inputname" class="col-md-4 control-label" style="font-size:12px">PO No</label>
								<div class="col-md-4">
									<input type="text" class="form-control input-sm clear_search" id="srch_pono" placeholder="PO No." name="srch_pono" autofocus <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label for="inputname" class="col-md-4 control-label" style="font-size:12px">Device Code</label>
								<div class="col-md-4">
									<input type="text" class="form-control input-sm clear_search" id="srch_devicecode" placeholder="Device Code" name="srch_devicecode" <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label for="inputname" class="col-md-4 control-label" style="font-size:12px">Item Part / No</label>
								<div class="col-md-4">
									<input type="text" class="form-control input-sm clear_search" id="srch_itemcode" placeholder="Item Part / No." name="srch_itemcode" <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label for="inputname" class="col-md-4 control-label" style="font-size:12px">Incharge</label>
								<div class="col-md-4">
									<input type="text" class="form-control input-sm clear_search" id="srch_incharge" placeholder="Incharge" name="srch_incharge" <?php echo($readonly); ?> />
								</div>
							</div>
							<div class="form-group">
								<label for="inputname" class="col-md-4 control-label" style="font-size:12px">Status</label>
								<div class="col-md-8">
									<label><input type="checkbox" class="checkboxes" style="font-size:12px" value="Open" id="srch_open" name="Open" checked="true"/>Open</label>
									<label><input type="checkbox" class="checkboxes" style="font-size:12px" value="Closed" id="srch_close" name="Close"/>Close</label>
									<label><input type="checkbox" class="checkboxes" style="font-size:12px" value="Cancelled" id="srch_cancelled" name="Cancelled"/>Cancelled</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover table-fixedheader" style="font-size:10px">
								<thead>
									<tr>
										<td style="width: 4.69%"></td>
										<td style="width: 7.69%">Transaction No.</td>
										<td style="width: 7.69%">PO No.</td>
										<td style="width: 7.69%">Device Code</td>
										<td style="width: 10.69%">Device Name</td>
										<td style="width: 7.69%">Incharge</td>
										<td style="width: 7.69%">Item Part/No.</td>
										<td style="width: 11.69%">Item Description</td>
										<td style="width: 5.69%">Status</td>
										<td style="width: 6.69%">Created By</td>
										<td style="width: 7.69%">Created Date</td>
										<td style="width: 6.69%">Updated By</td>
										<td style="width: 7.69%">Updated Date</td>
									</tr>
								</thead>
								<tbody id="srch_tbl_body"></tbody>
							</table>
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" style="font-size:12px" onclick="javascript: search(); " class="btn blue-madison"><i class="glyphicon glyphicon-filter"></i> Filter</button>
				<button type="button" style="font-size:12px" onclick="javascript: reset(); " class="btn green" ><i class="glyphicon glyphicon-repeat"></i> Reset</button>
				<button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>

