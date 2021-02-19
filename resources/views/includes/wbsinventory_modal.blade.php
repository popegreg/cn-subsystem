<div id="form_inventory_modal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">

		<div class="modal-content blue">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3 class="modal-title">ADD\EDIT Item</h3>
			</div>
			<form class="form-horizontal" role="form" method="POST" action="{{ url('/wbs-inventory-save') }}" id="frm_inventory">
				<div class="modal-body">
					{!! csrf_field() !!}
					<input type="hidden" id="id" name="id">

					<div class="form-group" id="item_code_div">
						<label for="inputcode" class="col-md-3 control-label">Item Code</label>
						<div class="col-md-9">
							<input type="text" class="form-control validate" id="item" name="item">
							<span class="help-block">
                                <strong id="item_msg"></strong>
                            </span>
						</div>
					</div>

					<div class="form-group" id="item_desc_div">
						<label for="inputcode" class="col-md-3 control-label">Description</label>
						<div class="col-md-9">
							<input type="text" class="form-control validate" id="item_desc" name="item_desc" autofocus>
							<span class="help-block">
                                <strong id="item_desc_msg"></strong>
                            </span>
						</div>
					</div>

					<div class="form-group" id="lot_no_div">
						<label for="inputcode" class="col-md-3 control-label">Lot No.</label>
						<div class="col-md-9">
							<input type="text" class="form-control validate" id="lot_no" name="lot_no">
							<span class="help-block">
                                <strong id="lot_no_msg"></strong>
                            </span>
						</div>
					</div>

					<div class="form-group" id="qty_div">
						<label for="inputcode" class="col-md-3 control-label">Qty.</label>
						<div class="col-md-9">
							<input type="text" class="form-control validate" id="qty" name="qty">
							<span class="help-block">
                                <strong id="qty_msg"></strong>
                            </span>
						</div>
					</div>

					<div class="form-group" id="location_div">
						<label for="inputcode" class="col-md-3 control-label">Location</label>
						<div class="col-md-9">
							<input type="text" class="form-control validate" id="location" name="location">
							<span class="help-block">
                                <strong id="location_msg"></strong>
                            </span>
						</div>
					</div>

					<div class="form-group" id="supplier_div">
						<label for="inputcode" class="col-md-3 control-label">Supplier</label>
						<div class="col-md-9">
							<input type="text" class="form-control validate" id="supplier" name="supplier">
							<span class="help-block">
                                <strong id="supplier_msg"></strong>
                            </span>
						</div>
					</div>

					<div class="form-group">
					    <label class="control-label col-sm-3"></label>
					    <div class="md-checkbox-inline">
					        <div class="md-checkbox">
					            <input type="checkbox" id="nr" name="nr" class="md-check" value="1">
					            <label for="nr">
					            <span></span>
					            <span class="check"></span>
					            <span class="box"></span>
					            Not for IQC </label>
					        </div>
					    </div>
					</div>

					{{-- <div class="form-group">
						<label for="inputcode" class="col-md-3 control-label"></label>
						<div class="col-md-9">
							<input type="checkbox" id="nr" name="nr"> Not for IQC
						</div>
					</div> --}}

					<div class="form-group" id="status_div">
						<label for="inputname" class="col-md-3 control-label">Status</label>
						<div class="col-md-9">
							<select class="form-control validate" id="status" name="status">
								<option value="0">Pending</option>
								<option value="1">Accept</option>
								<option value="2">Reject</option>
								<option value="3">On-going</option>
							</select>
							<span class="help-block">
                                <strong id="status_msg"></strong>
                            </span>
						</div>
					</div>
					
				</div>
				<div class="modal-footer">
						{{-- <button type="submit" class="btn btn-success" {{ $state }}><i class="fa fa-save"></i> Save</button> --}}
						<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
 						<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</form>
		</div>
			
	</div>
</div>

<div id="search_modal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content blue">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Search</h4>
				</div>
				<div class="modal-body">
					<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group">
                                <label for="saki_from" class="col-md-3 control-label">Received Date</label>
                                <div class="col-md-7">
                                    <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control input-sm reset" name="srch_from" id="srch_from"/>
                                        <span class="input-group-addon">to </span>
                                        <input type="text" class="form-control input-sm reset" name="srch_to" id="srch_to"/>
                                    </div>
                                </div>
                            </div>
							<div class="form-group">
								<label for="saki_pono" class="col-md-3 control-label">Invoice No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="srch_invoice" placeholder="Invoice No." name="srch_invoice" autofocus />
								</div>
							</div>
							<div class="form-group">
								<label for="saki_issno" class="col-md-3 control-label">Item Code</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="srch_item" placeholder="Item Code" name="srch_item" />
								</div>
							</div>

							<div class="form-group">
								<label for="saki_item" class="col-md-3 control-label">Lot No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm" id="srch_lot_no" placeholder="Lot No." name="srch_lot_no" />
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">

					<button type="button" id="btnSearch" class="btn btn-sm blue"><i class="fa fa-search"></i> Search</button>
					<button type="button" id="btnClearSearch" class="btn btn-sm" data-mode="wmi"><i class="fa fa-refresh"></i> Clear</button>
					<button type="button" style="font-size:12px" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</div>
		</div>
	</div>