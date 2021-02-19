<!-- ADD NEW MODAL -->
<div id="AddNewModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md gray-gallery">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">OQC FGS</h4>
			</div>
			<form class="form-horizontal">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label col-sm-3">Date</label>
								<div class="col-sm-9">
									<input class="form-control input-sm date-picker clear" type="text" name="date" id="date" autocomplete="false" data-date-format="yyyy-mm-dd"/>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">P.O. Number</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="po_no" name="po_no" autocomplete="false">
									<div id="er_po_no"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Device Name</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="device_name" name="device_name" readonly autocomplete="false">
									<div id="er_device_name"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Quantity</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="quantity" name="quantity" autocomplete="false">
									<div id="er_quantity"></div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Total No. of Lots</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm clear" id="total_lots" name="total_lots" autocomplete="false">
									<input type="hidden" class="form-control input-sm clear" id="hd_status" name="hd_status">
									<input type="hidden" class="form-control input-sm clear" id="id" name="id">
									<div id="er_total_lots"></div>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn blue btn-sm" id="btn_clear"><i class="fa fa-eraser"></i> Clear</button>
					<button type="button" class="btn green btn-sm" id="btn_save"><i class="fa fa-floppy-o"></i> Save</button>
					<button type="button" data-dismiss="modal" class="btn red btn-sm"><i class="fa fa-times"></i> Close</button>
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
							<label class="control-label col-sm-2">Date From</label>
							<div class="col-sm-10">
									<input type="text" class="form-control date-picker input-sm " id="groupby_datefrom" name="groupby_datefrom" data-date-format="yyyy-mm-dd">
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label class="control-label col-sm-2">Date To</label>
							<div class="col-sm-10">
									<input type="text" class="form-control date-picker input-sm " id="groupby_dateto" name="groupby_dateto" data-date-format="yyyy-mm-dd">
							</div>
						</div>
					</div>
					
					<hr>
					<div class="row">
						<div class="col-sm-12">
							<label class="control-label col-sm-2">Group #1</label>
							<div class="col-sm-10">
								<select class="form-control input-sm show-tick group-field-select" name="group1" id="group1">
									<option value=""></option>
									<option value="date">Date Inspected</option>
									<option value="po_no">PO Number</option>
									<option value="device_name">Series Name</option>
									<option value="qty">Quantity</option>
									<option value="total_num_of_lots">Total No. of Lots</option>
								</select>
							</div>
						 
						</div>  
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label class="control-label col-sm-2">Group #2</label>
							<div class="col-sm-10">
								<select class="form-control input-sm show-tick group-field-select" name="group2" id="group2">
									<option value=""></option>
									<option value="date">Date Inspected</option>
									<option value="po_no">PO Number</option>
									<option value="device_name">Series Name</option>
									<option value="qty">Quantity</option>
									<option value="total_num_of_lots">Total No. of Lots</option>
								</select>
							</div>
						</div>  
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label class="control-label col-sm-2">Group #3</label>
							<div class="col-sm-10">
								<select class="form-control input-sm show-tick group-field-select" name="group3" id="group3">
									<option value=""></option>
									<option value="date">Date Inspected</option>
									<option value="po_no">PO Number</option>
									<option value="device_name">Series Name</option>
									<option value="qty">Quantity</option>
									<option value="total_num_of_lots">Total No. of Lots</option>
								</select>
							</div>
						</div>  
					</div>
					<br>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm yellow-gold" id="btn_group_by_pdf">
						<i class="fa fa-file-pdf-o"></i> PDF
					</button>
					<button type="button" class="btn btn-sm green-jungle" id="btn_group_by_excel">
						<i class="fa fa-file-excel-o"></i> Excel
					</button>
					<button type="button" class="btn btn-sm btn-primary" id="btn_group_by_generate">
						<i class="fa fa-search"></i> Search
					</button>
					<button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">
						<i class="fa fa-times"></i> Close
					</button>
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
									<input class="form-control input-sm date-picker" type="text" value="" name="search_from" id="search_from" data-date-format="yyyy-mm-dd"/>
									<div id="er_search_from"></div>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-3">To</label>
								<div class="col-sm-7">
									<input class="form-control input-sm date-picker" type="text" value="" name="search_to" id="search_to" data-date-format="yyyy-mm-dd"/>
									<div id="er_search_to"></div>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm yellow-gold" id="btn_search_pdf">
						<i class="fa fa-file-pdf-o"></i> PDF
					</button>
					<button type="button" class="btn btn-sm green-jungle" id="btn_search_excel">
						<i class="fa fa-file-excel-o"></i> Excel
					</button>
					<button type="button" class="btn btn-sm btn-primary" id="btn_search_generate">
						<i class="fa fa-search"></i> Search
					</button>
					<button type="button" data-dismiss="modal" class="btn btn-sm btn-danger" id="btn_search-close">
						<i class="fa fa-times"></i> Close
					</button>
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