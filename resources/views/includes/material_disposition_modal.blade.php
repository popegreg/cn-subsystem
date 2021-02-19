<div id="inventory_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-xl">

        <div class="modal-content blue">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Inventory Items</h5>
            </div>
            <form class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control input-sm" id="inv_id" name="inv_id"/>
                    <input type="hidden" class="form-control input-sm" id="key_id" name="key_id"/>
                    <input type="hidden" class="form-control input-sm" id="d_id" name="d_id"/>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Item Code</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="item" name="item">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Item Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="item_desc" name="item_desc" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Qty</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="qty" name="qty">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Lot No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear" id="lot_no" name="lot_no" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Exp Date</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm clear date-picker" id="exp_date" name="exp_date" data-date-format="yyyy-mm-dd">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Disposition</label>
                                <div class="col-sm-9">
                                    <select class="form-control input-sm clear" id="disposition_status" name="disposition_status">
                                        <option value=""></option>
                                        @foreach($dispositions as $status)
                                            <option value="{{ $status->description }}">{{ $status->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="control-label col-sm-3">Remarks</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control input-sm clear" id="item_remarks" name="item_remarks" style="resize: none"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <table class="table table-condensed table-bordered" id="tbl_inventory">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td>Item Code</td>
                                        <td>Description</td>
                                        <td>Qty</td>
                                        <td>Lot</td>
                                        <td>Location</td>
                                        <td>Received Date</td>
                                    </tr>
                                </thead>
                                <tbody id="tbl_inventory_body"></tbody>
                            </table>
                        </div>
                    </div>
                    

                </div>

                <div class="modal-footer">
                    {{-- <button type="submit" class="btn btn-success" {{ $state }}><i class="fa fa-save"></i> Save</button> --}}
                    <button type="button" class="btn btn-success btn-sm" id="btn_save_item">save</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn_close_modal" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                </div>
            </form>
                
        </div>

    </div>
</div>

<!-- Search Modal -->
<div id="modal_search" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-full">
        <!-- Modal content-->
        <div class="modal-content blue">
            <div class="modal-header">
               
                <h4 class="modal-title">Search</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label for="inputcode" class="col-md-3 control-label">Transaction Date</label>
                                <div class="col-md-9">
                                    <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control input-sm reset" name="srch_from" id="srch_from" autocomplete="off"/>
                                        <span class="input-group-addon">to </span>
                                        <input type="text" class="form-control input-sm reset" name="srch_to" id="srch_to" autocomplete="off"/>
                                    </div>
                                </div>
                            </div>
                                    
                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Transaction No.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm reset" id="srch_trans_no"  name="srch_trans_no"> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Item Code</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm reset" id="srch_item"  name="srch_item"> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputname" class="col-md-3 control-label">Lot No.</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm reset" id="srch_lot_no"  name="srch_lot_no"> 
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-striped table-bordered table-hover table-responsive" id="tbl_search">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td>Transaction No</td>
                                    <td>Item Code</td>
                                    <td>Description</td>
                                    <td>Qty</td>
                                    <td>Lot No.</td>
                                    <td>Created By</td>
                                    <td>Created Date</td>
                                </tr>
                            </thead>
                            <tbody id="tbl_search_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn blue-madison input-sm" id="btn_filter"><i class="glyphicon glyphicon-filter"></i> Filter</a>
                <a href="javascript:;" class="btn green input-sm" id="btn_reset"><i class="glyphicon glyphicon-repeat"></i> Reset</a>
                <a href="javascript:;" class="btn btn-danger input-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</a>
            </div>
        </div>
    </div>
</div>

<div id="export_data_modal" class="modal fade" role="dialog" data-backdrop="static">

    <div class="modal-dialog">

        <div class="modal-content blue">
            <div class="modal-header">
            
                <h5 class="modal-title">Export Data</h5>
            </div>
            <form class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control input-sm" id="lot_id" name="lot_id"/>

                    <div class="form-group">
                        <label for="" class="control-label col-sm-2">From:</label>
                        <div class="col-sm-10">
                            <input class="form-control input-sm " type="text" name="from" id="from">
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="" class="control-label col-sm-2 ">To:</label>
                        <div class="col-sm-10">
                             <input class="form-control input-sm " type="text" name="to" id="to">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    {{-- <button type="submit" class="btn btn-success" {{ $state }}><i class="fa fa-save"></i> Save</button> --}}
                   <button type="button" id="btn_excel" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </button>
                    <button type="button" id="btn_close" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                </div>
            </form>
                
        </div>

    </div>
</div>