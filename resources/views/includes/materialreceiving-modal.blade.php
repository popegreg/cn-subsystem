<!-- Add Batch Modal -->
<div id="batchItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content blue">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Batch</h4>
            </div>
            <div class="modal-body">
                <p>All the fields are required.</p>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputcode" class="col-md-3 control-label">*Batch ID</label>
                        <div class="col-md-9">
                            <input type="text" id="add_invoice_no" name="id" hidden="true" />
                            <input type="text" class="form-control input-sm clearbatch" id="add_inputBatchId"
                                placeholder="Batch ID" name="batchid" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="col-md-3 control-label">*Item No</label>
                        <div class="col-md-9">
                            <input type="text" id="add_inputItemNo" class="form-control input-sm clearbatch"
                                name="itemno" <?php echo ($state); ?>>
                            <input type="hidden" id="add_inputItemNoHidden" class="clearbatch">
                            <input type="hidden" id="add_inputItemDesc" class="clearbatch">
                            <input type="hidden" id="add_notForIqc" class="clearbatch">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="col-md-3 control-label">*Quantity</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="add_inputQty"
                                placeholder="Quantity" name="qty" <?php echo ($readonly); ?> />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3" style="text-align: right;">
                            <label for="inputname" class="control-label">*Package Category</label>
                        </div>
                        <div class="col-md-3">
                            <select type="text" id="add_inputBox" class="form-control input-sm clearbatch"
                                name="add_inputBox" <?php echo ($state); ?>>
                                <option value=""></option>
                                @foreach($packages as $pk)
                                <option value="{{ $pk->description }}">{{ $pk->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" style="text-align: right;">
                            <label for="inputname" class="control-label">*Package Qty</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control input-sm clearbatch" id="add_inputBoxQty"
                                placeholder="Box Qty" name="boxqty" <?php echo ($readonly); ?> />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="control-label col-md-3">*Lot No</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="add_inputLotNo"
                                placeholder="Lot No" name="lotno" <?php echo ($readonly); ?> />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="control-label col-md-3">Location</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="add_inputLocation"
                                placeholder="Location" name="location" disabled="disabled" <?php echo ($readonly); ?>
                                value="" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="control-label col-md-3">Supplier</label>
                        <div class="col-md-9">
                            <select class="form-control input-sm clearbatch" id="add_inputSupplier"
                                placeholder="Supplier" name="supplier" <?php echo ($readonly); ?>></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pressed_date" class="control-label col-md-3">Pressed Date</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="pressed_date"
                                placeholder="yyyy-mm-dd" name="pressed_date">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="plating_date" class="control-label col-md-3">Plating Date</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="plating_date"
                                placeholder="yyyy-mm-dd" name="plating_date">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_add_batch_modal" class="btn btn-success" <?php echo ($state); ?>><i
                        class="fa fa-plus"></i> Add</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
                    Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Batch Modal -->
<div id="EditbatchItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content blue">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Batch</h4>
            </div>
            <div class="modal-body">
                <p>All the fields are required.</p>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputcode" class="col-md-3 control-label">*Batch ID</label>
                        <div class="col-md-9">
                            <input type="hidden" id="edit_invoice_no" name="id" />
                            <input type="text" class="form-control input-sm clearbatch" id="edit_inputBatchId"
                                placeholder="Batch ID" name="batchid" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="col-md-3 control-label">*Item No</label>
                        <div class="col-md-9">
                            <input type="text" id="edit_inputItemNo" class="form-control input-sm clearbatch"
                                name="itemno" <?php echo ($state); ?>>
                            <input type="hidden" id="edit_inputItemNoHidden" class="clearbatch">
                            <input type="hidden" id="edit_inputItemDesc" class="clearbatch">
                            <input type="hidden" id="edit_notForIqc" class="clearbatch">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="col-md-3 control-label">*Quantity</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="edit_inputQty"
                                placeholder="Quantity" name="qty" />
                            <input type="hidden" name="edit_inputQtyHidden" id="edit_inputQtyHidden">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3" style="text-align: right;">
                            <label for="inputname" class="control-label">*Package Category</label>
                        </div>
                        <div class="col-md-3">
                            <select id="edit_inputBox" class="form-control input-sm clearbatch" name="itemno"
                                <?php echo ($state); ?>>
                                <option value=""></option>
                                @foreach($packages as $pk)
                                <option value="{{ $pk->description }}">{{ $pk->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" style="text-align: right;">
                            <label for="inputname" class="control-label">*Package Qty</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control input-sm clearbatch" id="edit_inputBoxQty"
                                placeholder="Box Qty" name="boxqty" <?php echo ($readonly); ?> />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="control-label col-md-3">*Lot No</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="edit_inputLotNo"
                                placeholder="Lot No" name="lotno" <?php echo ($readonly); ?> />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="control-label col-md-3">Location</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="edit_inputLocation"
                                placeholder="Location" name="location" disabled="disabled" <?php echo ($readonly); ?>
                                value="" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputname" class="control-label col-md-3">Supplier</label>
                        <div class="col-md-9">
                            <select class="form-control input-sm clearbatch" id="edit_inputSupplier"
                                placeholder="Supplier" name="supplier" <?php echo ($readonly); ?>></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_pressed_date" class="control-label col-md-3">Pressed Date</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="edit_pressed_date"
                                placeholder="yyyy-mm-dd" name="pressed_date">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_plating_date" class="control-label col-md-3">Plating Date</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-sm clearbatch" id="edit_plating_date"
                                placeholder="yyyy-mm-dd" name="plating_date">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_edit_batch_modal" class="btn btn-success" <?php echo ($state); ?>><i
                        class="fa fa-check"></i> Update</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
                    Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Search Modal -->
<div id="searchModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-full">
        <!-- Modal content-->
        <div class="modal-content blue">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Search</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="inputcode" class="col-md-3 control-label">Receive Date</label>
                                <div class="col-md-7">
                                    <div class="input-group input-large date-picker input-daterange"
                                        data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control input-sm reset" name="srch_from"
                                            id="srch_from" />
                                        <span class="input-group-addon">to </span>
                                        <input type="text" class="form-control input-sm reset" name="srch_to"
                                            id="srch_to" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Invoice No</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm reset" id="srch_invoiceno"
                                        placeholder="Invoice No" name="srch_invoiceno" autofocus
                                        <?php echo ($readonly); ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Item Code</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm reset" id="srch_item"
                                        placeholder="Item Code" name="srch_item" <?php echo ($readonly); ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Lot No.</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm reset" id="srch_lot"
                                        placeholder="Lot No." name="srch_lot" <?php echo ($readonly); ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Pallet No</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm reset" id="srch_palletno"
                                        placeholder="Pallet No" name="srch_palletno" <?php echo ($readonly); ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Status</label>
                                <div class="col-md-7">
                                    <label><input type="checkbox" class="checkboxes" value="Open" id="srch_open"
                                            name="Open" checked="checked" />Open</label>
                                    <label><input type="checkbox" class="checkboxes" value="Close" id="srch_close"
                                            name="Close" />Close</label>
                                    <label><input type="checkbox" class="checkboxes" value="Cancelled"
                                            id="srch_cancelled" name="Cancelled" />Cancelled</label>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-7">
                        <table class="table table-striped table-bordered table-hover table-responsive sortable"
                            id="tbl_search">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td>Transaction No.</td>
                                    <td>Receive Date</td>
                                    <td>Invoice No.</td>
                                    <td>Invoice Date</td>
                                    <td>Code</td>
                                    <td>Lot No.</td>
                                    <td>Qty</td>
                                    <td>Invoice Status</td>
                                    <td>IQC status</td>
                                    <td>Created By</td>
                                    <td>Created Date</td>
                                    <td>Updated By</td>
                                    <td>Updated Date</td>
                                </tr>
                            </thead>
                            <tbody id="tbl_search_body">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn blue-madison input-sm" id="btn_filter"><i
                        class="glyphicon glyphicon-filter"></i> Filter</a>
                <a href="javascript:;" class="btn green input-sm" id="btn_reset"><i
                        class="glyphicon glyphicon-repeat"></i> Reset</a>
                <a href="javascript:;" class="btn btn-danger input-sm" data-dismiss="modal"><i class="fa fa-times"></i>
                    Close</a>
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
                    <div class="col-sm-8 col-sm-offset-2">
                        <img src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/images/ajax-loader.gif') }}"
                            class="img-responsive">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MSG -->
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

<!-- Confirm -->
<div id="confirm" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm gray-gallery">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">WBS Material Receiving</h4>
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