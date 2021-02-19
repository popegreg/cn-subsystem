<div id="DetailsModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-full gray-gallery">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Details</h4>
            </div>
            <form method="POST" action="{{url('/editdetailpmr')}}" class="form-horizontal" id="editpodetailfrm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-3">Issuance No.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="issuance_no">
                                    <input type="hidden" class="form-control input-sm clear" id="detail_id">
                                    <input type="hidden" class="form-control input-sm clear" id="item_id">
                                    <input type="hidden" class="form-control input-sm clear" id="inv_id">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Item Code</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="item" name="item" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">Item Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="item_desc" name="item_desc" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">Issued Qty.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="issued_qty" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">Required Qty.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="required_qty" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">Return Qty.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="return_qty" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">Actual Returned Qty.</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control input-sm clear" id="actual_returned_qty">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Lot no</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="lot_no" name="lot_no" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Remarks</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control input-sm clear" id="detail_remarks" name="detail_remarks" cols="30" rows="5" style="resize:none;"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-condensed table-bordered" id="tbl_items">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td>Item Code</td>
                                        <td>Description</td>
                                        <td>Lot No</td>
                                        <td>Issued Qty</td>
                                        <td>Date Issued</td>
                                    </tr>
                                </thead>
                                <tbody id="tbl_items_body"></tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_save_details" class="btn btn-success">Save</button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="searchModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-full">

        <!-- Modal content-->
        <form class="form-horizontal" role="form" method="POST" action="{{url('/search-return')}}" id="frm_search">
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
                                <label for="inputsearch_pono" class="col-md-3 control-label" style="font-size:12px">Control No.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" id="srch_control_no" placeholder="Control No." name="srch_control_no" autofocus <?php echo($readonly); ?> />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputsearch_req_no" class="col-md-3 control-label" style="font-size:12px">P.O. No.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" id="srch_po" placeholder="P.O. No." name="srch_po" <?php echo($readonly); ?> />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputsearch_req_no" class="col-md-3 control-label" style="font-size:12px">Issuance No.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" id="srch_issuance" placeholder="Issuance No." name="srch_issuance" <?php echo($readonly); ?> />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputsearch_req_no" class="col-md-3 control-label" style="font-size:12px">Item Code.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" id="srch_item" placeholder="Item Code." name="srch_item" <?php echo($readonly); ?> />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <table class="table table-striped table-bordered table-hover" style="font-size:10px" id="tbl_search">
                                <thead>
                                    <tr>
                                        <td width="11.11%"></td>
                                        <td width="11.11%">Control No.</td>
                                        <td width="11.11%">PO No.</td>
                                        <td width="11.11%">Issuance No.</td>
                                        <td width="11.11%">Item Code</td>
                                        <td width="11.11%">Created By</td>
                                        <td width="11.11%">Created Date</td>
                                        <td width="11.11%">Updated By</td>
                                        <td width="11.11%">Updated Date</td>
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
                                    <input class="form-control input-sm date-picker" type="text" name="from" id="from"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">To</label>
                                <div class="col-sm-7">
                                    <input class="form-control input-sm date-picker" type="text" name="to" id="to"/>
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