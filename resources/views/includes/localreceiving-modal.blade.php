<!-- REPORT MODAL -->
<div id="reportModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog gray-gallery">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Summary Report</h4>
            </div>
            <form class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label col-sm-3">From</label>
                                <div class="col-sm-7">
                                    <input class="form-control input-sm date-picker" data-date-format="yyyy-mm-dd" type="text" name="from" id="from"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">To</label>
                                <div class="col-sm-7">
                                    <input class="form-control input-sm date-picker" data-date-format="yyyy-mm-dd" type="text" name="to" id="to"/>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="javascript:summaryReport();" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_search-close">Close</button>
                </div>
            </form>
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
                <h4 class="modal-title">Batch Items</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal">
                            <p>All the fields are required.</p>

                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">*Item No</label>
                                <div class="col-md-9">
                                    <input type="text" id="edt_item" class="form-control input-sm clearbatch" <?php echo($state);?> readonly>
                                    <input type="hidden" id="edt_id">
                                    <input type="hidden" id="batch_save_status">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">*Quantity</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm clearbatch" id="edt_qty" <?php echo($state);?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3" style="text-align: right;">
                                    <label for="inputname" class="control-label">*Package Category</label>
                                </div>
                                <div class="col-md-3">
                                    <select id="edt_box" class="form-control input-sm clearbatch" <?php echo($state);?>>
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-3" style="text-align: right;">
                                    <label for="inputname" class="control-label">*Package Qty</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control input-sm clearbatch" id="edt_box_qty" <?php echo($readonly); ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label" >*Lot No</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm clearbatch" id="edt_lot_no" <?php echo($readonly); ?>/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Location</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm clearbatch" id="edt_loc" <?php echo($readonly); ?>/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Supplier</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm clearbatch" id="edt_supplier" <?php echo($readonly); ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="edt_pressed_date" class="col-md-3 control-label">Pressed Date</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm clearbatch" placeholder="yyyy-mm-dd" id="edt_pressed_date" <?php echo($readonly); ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="edt_plating_date" class="col-md-3 control-label">Plating Date</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm clearbatch" placeholder="yyyy-mm-dd" id="edt_plating_date" <?php echo($readonly); ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label"></label>
                                <div class="col-md-9">
                                    <div class="md-checkbox-inline">
                                        <div class="md-checkbox">
                                            <input type="checkbox" id="nr_iqc" class="md-check" name="nr_iqc" value="0">
                                            <label for="nr_iqc">
                                                <span></span>
                                                <span class="check"></span>
                                                <span class="box"></span>
                                                Check if NOT required of IQC
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_edit_batch" class="btn btn-success" <?php echo($state); ?>><i class="fa fa-check"></i> Update</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<div id="searchModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-full">

        <!-- Modal content-->
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/local-receiving-search') }}" id="frm_search">
            {!! csrf_field() !!}
            <div class="modal-content blue">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputname" class="col-md-2 control-label" style="font-size:12px">Item Code</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control input-sm search_reset" id="srch_item" placeholder="Item Code" name="srch_item" autofocus />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-striped table-bordered table-hover table-responsive sortable" id="tbl_search">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td>Transaction No.</td>
                                        <td>Receive Date</td>
                                        <td>PPC Invoice No.</td>
                                        <td>Temp Invoice No.</td>
                                        <td>Code</td>
                                        <td>Lot No.</td>
                                        <td>Qty</td>
                                        <td>IQC status</td>
                                        <td>Created By</td>
                                        <td>Created Date</td>
                                        <td>Updated By</td>
                                        <td>Updated Date</td>
                                    </tr>
                                </thead>
                                <tbody id="tbl_search_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn blue-madison btn-sm" ><i class="glyphicon glyphicon-filter"></i> Filter</button>
                    <button type="button" class="btn green btn-sm" ><i class="glyphicon glyphicon-repeat"></i> Reset</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                </div>
            </div>
        </form>
    </div>
</div>