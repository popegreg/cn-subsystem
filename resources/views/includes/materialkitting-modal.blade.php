<style>
.dataTables_filter {
display: none;
}
.important,.important td{
 background-color:red !important;
}

</style>
<div id="kitqtyModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Add Kit Qty.</h4>
			</div>
			<form class="form-horizontal" id="kitqtyForm">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							{{ csrf_field() }}
							<div class="form-group">
								<label class="control-label col-sm-2">Kit Qty.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm mask_kitqty" id="getkitQty" name="kitqty" autofocus>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="updateKityQty">OK</button>
					<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="addIssuanceDetailsModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-full gray-gallery">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Add Issuance</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-5">
						<form class="form-horizontal" id="addIssuanceDetails_form">
							{{ csrf_field() }}
							<input type="hidden" name="fifoid" id="fifoid">
							<div class="form-group">
								<div class="col-sm-12">
								   <p>
									   Item/Part No., Issued Qty., and Location fields are required.
								   </p>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Item/Part No.</label>
								<div class="col-sm-8">
									<input type="text" class="form-control iss_clear input-sm" id="iss_item" name="iss_item" autofocus>
									<input type="hidden" class="form-control iss_clear input-sm" id="iss_item_desc" name="iss_item_desc">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Lot No</label>
								<div class="col-sm-8">
								   <input type="text" class="form-control iss_clear input-sm" id="iss_lotno" name="iss_lotno">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kit Qty.</label>
								<div class="col-sm-8">
									<input type="text" class="form-control iss_clear input-sm" id="iss_kitqty" name="iss_kitqty" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Issued Qty.</label>
								<div class="col-sm-8">
									<input type="text" class="form-control iss_clear input-sm" id="iss_qty" name="iss_qty">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Location</label>
								<div class="col-sm-8">
									<input type="text" class="form-control iss_clear input-sm" id="iss_location" name="iss_location">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Remarks</label>
								<div class="col-sm-8">
									{{-- <input type="text" class="form-control iss_clear input-sm" id="iss_qty" name="iss_qty"> --}}
									<textarea class="form-control iss_clear input-sm" id="iss_remarks" style="resize:none" name="iss_remarks"></textarea>
								</div>
							</div>
							<div class="form-group">
								{{-- <label class="control-label col-sm-3">Remarks</label> --}}
								<div class="col-sm-8">
									<input type="hidden" class="form-control iss_clear input-sm" id="user" name="user">
									<input type="hidden" class="form-control iss_clear input-sm" id="id_reason" name="id_reason">
									{{-- <textarea class="form-control iss_clear input-sm" id="iss_remarks" style="resize:none" name="iss_remarks"></textarea> --}}
								</div>
							</div>

		
							<input type="hidden" name="iss_save_status" id="iss_save_status">
							<input type="hidden" name="iss_detail_id" id="iss_detail_id">
							<input type="hidden" name="iss_id" id="iss_id">

						</form>
					</div>
					<style >
						.important,.important td{
 							background-color:#FFFF00 !important;
						}
						.selected {
                	  	    background-color: #d9534f !important;
                  		}

					/*	redClass*/
					</style>
					<div class="col-md-7">
						<table class="table table-bordered,dataTables_filter"  style="font-size:10px" id="tbl_fifo">
							<thead>
								<tr>
									<td></td>
									<td>Item Code</td>
									<td>Description</td>
									<td>Qty</td>
									<td>Lot</td>
									<td>Location</td>
									<td>Received Date</td>
								   {{-- <td>Created_at</td> --}}
								</tr>
							</thead>
							<tbody id="tbl_fifo_body"></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" class="btn btn-success" id="btn_add_issuance">OK</a>
				<button type="button" data-dismiss="modal" class="btn btn-danger iss_edit_close" id="iss_add_close">Close</button>
			</div>
		</div>
	</div>
</div>

<div id="fifoReasonModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 id="tit" class="modal-title">FIFO Alert</h4>
			</div>
			<div class="modal-body">
				<p>FIFO is recommended, but you can specify your reason for using this Lot number.</p>
				<input type="hidden" name="frid" id="frid">
				<input type="hidden" name="fritem" id="fritem">
				<input type="hidden" name="fritemdesc" id="fritemdesc">
				<input type="hidden" name="frqty" id="frqty">
				<input type="hidden" name="frlotno" id="frlotno">
				<input type="hidden" name="frlocation" id="frlocation">
				<input type="hidden" name="frkitqty" id="frkitqty">
									<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group">
                                <label class="col-md-3 control-label">Reason: </label>
                                <div class="col-md-7">
									<select class="form-control input-sm" id="reason_id" name="reason_id">
										<option value=""></option>
						                @foreach($data as $fifo)
                                                <option value="{{$fifo->id}}">{{$fifo->dropdown_reason}}</option>
                                               {{-- {{$fifo->dropdown_reason}} --}}

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
									 {{-- <input type="text" class="form-control input-sm" id="hidden_reason" name="password"> --}}
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

<div id="searchModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-full">

		<!-- Modal content-->
		<form class="form-horizontal" role="form" method="POST" action="{{ url('/material-kitting') }}">
			{!! csrf_field() !!}
			<div class="modal-content blue">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Search</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="inputname" class="col-md-3 control-label" style="font-size:12px">Po No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm search_reset" id="srch_pono" placeholder="Po No" name="srch_pono" autofocus />
								</div>
							</div>
							<div class="form-group">
								<label for="inputname" class="col-md-3 control-label" style="font-size:12px">Kit No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm search_reset" id="srch_kitno" placeholder="Kit No" name="srch_kitno" />
								</div>
							</div>
							<div class="form-group">
								<label for="inputname" class="col-md-3 control-label" style="font-size:12px">Prepared By</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm search_reset" id="srch_preparedby" placeholder="Prepared By" name="srch_preparedby" />
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="inputname" class="col-md-3 control-label" style="font-size:12px">Slip No</label>
								<div class="col-md-8">
									<input type="text" class="form-control input-sm search_reset" id="srch_slipno" placeholder="Slip No" name="srch_slipno" />
								</div>
							</div>
							<div class="form-group">
								<label for="inputname" class="col-md-3 control-label" style="font-size:12px">Status</label>
								<div class="col-md-8">
									<label><input type="checkbox" class="srch_status" style="font-size:12px" value="O" id="srch_open" name="srch_status[]"/>Open</label>
									<label><input type="checkbox" class="srch_status" style="font-size:12px" value="X" id="srch_close" name="srch_status[]"/>Close</label>
									<label><input type="checkbox" class="srch_status" style="font-size:12px" value="C" id="srch_cancelled" name="srch_status[]"/>Cancelled</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped table-bordered table-hover table-fixedheader" style="font-size:10px" id="tbl_search">
								<thead>
									<tr>
										<td width="8.3%"></td>
										<td width="8.3%">Transaction No.</td>
										<td width="8.3%">Po No.</td>
										<td width="8.3%">Device Code</td>
										<td width="8.3%">Device name</td>
										<td width="8.3%">Kit No.</td>
										<td width="8.3%">Prepared By</td>
										<td width="8.3%">Slip No.</td>
										<td width="8.3%">Created By</td>
										<td width="8.3%">Created Date</td>
										<td width="8.3%">Updated By</td>
										<td width="8.3%">Updated Date</td>
									</tr>
								</thead>
								<tbody id="tbl_search_body"></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" class="form-control input-sm" id="editId" name="editId">
					<button type="button" style="font-size:12px" onclick="javascript: filterSearch(); " class="btn blue-madison" ><i class="glyphicon glyphicon-filter"></i> Filter</button>
					<button type="button" style="font-size:12px" onclick="javascript: searchReset(); " class="btn green" ><i class="glyphicon glyphicon-repeat"></i> Reset</button>
					<button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="kittingListModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Search P.O. Number</h4>
			</div>
			<form class="form-horizontal">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							{{ csrf_field() }}
							<div class="form-group">
								<label class="control-label col-sm-2">P.O.</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm mask_kitqty" id="kittinglist_po" name="kittinglist_po">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Kit QTY</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm mask_kitqty" id="kittinglist_kitqty" name="kittinglist_kitqty">
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" id="btn_po_modal_search" class="btn btn-success" taget="_blank">Search</button>
					<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>