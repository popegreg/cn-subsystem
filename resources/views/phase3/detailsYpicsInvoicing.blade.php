@extends('layouts.master')

@section('title')
	YPICS Invoicing | Pricon Microelectronics, Inc.
@endsection

@push('css')
    <link href="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css')}}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_INVCING'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach

    <?php
        $ctrl_no = "";
    

        foreach ($packinginfo as $pkl) {
            $ctrl_no = $pkl->control_no;
        }
    ?>
    
	<div class="page-content">

		<!-- BEGIN PAGE CONTENT-->
        <!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				@include('includes.message-block')
				<div class="portlet box blue" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-file-text"></i>  YPICS Invoicing
						</div>
                        <div class="actions">
                            <a href="javascript:;" class="btn purple" id="btn_report">
                                <i class="fa fa-file-o"></i> Reports
                            </a>
                            <a href="javascript:;" class="btn green" id="btn_save">
                                <i class="fa fa-floppy-o"></i> Save
                            </a>
                            <a href="javascript:;" class="btn red" id="btn_clear">
                                <i class="fa fa-eraser"></i> clear
                            </a>
                            <a href="javascript:print();" class="btn blue-hoki" id="btn_print">
                                <i class="fa fa-print"></i> Print
                            </a>
                            <a href="{{url('/ypicsinvoicing')}}" class="btn grey-gallery pull-right">
                                <i class="fa fa-reply"></i> Back
                            </a>
                        </div>
					</div>
					<div class="portlet-body">

						<div class="row">

							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-11">
                                        <strong>From Packing List</strong>
                                    </div>

                                    <div class="col-md-1">
                                        <strong style="color: #1BA39C" id="invoice_status"></strong>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                <form class="form-horizontal">
                                                    {{-- <div class="form-group">
                                                        <label class="control-label col-sm-3">Item No.</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="itemno" name="itemno" readonly>
                                                        </div>
                                                    </div> --}}
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Packing List Ctrl</label>
                                                        <div class="col-sm-7">
                                                            {{-- <input type="text" id="packinglist_ctrl" name="packinglist_ctrl" class="form-control input-sm" value="{{$ctrl_no}}" readonly> --}}
                                                            <select id="packinglist_ctrl" name="packinglist_ctrl" class="form-control input-sm select2me">
                                                                @if (isset($packinglist))
                                                                    @foreach ($packinglist as $pk)
                                                                        <?php
                                                                            $selected = "";
                                                                            if ($ctrl_no == $pk->control_no){
                                                                                $selected = "selected";
                                                                            }
                                                                        ?>
                                                                        <option value="{{$pk->control_no}}" {{$selected}}>{{$pk->control_no}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <a href="javascript:;" class="btn input-sm blue" id="search_btn">
                                                                <i class="fa fa-search"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Products</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="description_of_goods" name="description_of_goods" class="form-control input-sm" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-12">Sold To Address</label>
                                                        <div class="col-sm-12">
                                                            <input type="hidden" name="sold_to_id" id="sold_to_id">
                                                            <textarea name="soldto_address" id="soldto_address" class="form-control input-sm" style="resize:none; height:175px" readonly></textarea>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-4">

                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                <form class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="col-sm-12">Ship To Address</label>
                                                        <div class="col-sm-12">
                                                            <textarea name="shipto_address" id="shipto_address" class="form-control input-sm" style="resize:none; height:175px" readonly></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Shipped From</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="shippedfrom" name="shippedfrom" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Ship To</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="shipto" name="shipto" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Carrier</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="carrier" name="carrier" readonly>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                <form class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Gross Weight</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="gross_weight" name="gross_weight" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Terms of Payment</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="terms_of_payment" name="terms_of_payment" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">P.O. No.</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="po_no" name="po_no" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Description</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="description" name="description" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Country Origin</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="country_origin" name="country_origin" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Quantity</label>
                                                        <div class="col-sm-4">
                                                            <input type="text" class="form-control input-sm" id="quantity" name="quantity" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Unit Price</label>
                                                        <div class="col-sm-4">
                                                            <input type="text" class="form-control input-sm" id="unitprice" name="unitprice" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Amount</label>
                                                        <div class="col-sm-4">
                                                            <input type="text" class="form-control input-sm" id="amount" name="amount" readonly>
                                                            <input type="hidden" class="form-control input-sm" id="ship_date" name="ship_date">
                                                            <input type="hidden" class="form-control input-sm" id="casemarks" name="casemarks">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <strong>For Input</strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                <form class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Revision No.</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm clear" id="revision_no" name="revision_no">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Transaction No.</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm clear" id="transaction_no" name="transaction_no">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">For BIR No.</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm clear" id="for_bir_no" name="for_bir_no">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Pick-up Date</label>
                                                        <div class="col-sm-9">
                                                            <input class="form-control input-sm date-picker" type="text" name="pickup_date" id="pickup_date"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Invoice Date</label>
                                                        <div class="col-sm-9">
                                                            <input class="form-control input-sm date-picker" type="text" name="invoice_date" id="invoice_date"/>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                <form class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Freight</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm" id="freight" name="freight" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Via</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm clear" id="via" name="via">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Sailing On</label>
                                                        <div class="col-sm-9">
                                                            <input class="form-control input-sm date-picker clear" type="text" value="" name="sailing_on" id="sailing_on"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">No# of Packaging</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm clear" id="no_of_packaging" name="no_of_packaging">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">AWB No.</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control input-sm clear" id="awb_no" name="awb_no">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                <form class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3">Prepared By</label>
                                                        <div class="col-sm-9">
                                                            <?php $preparedby = "";?>
                                                            @if (Auth::user()->productline == 'YF')
                                                                <?php $preparedby = "BHOY M. VOCES"; ?>
                                                            @else
                                                                <?php $preparedby = "JENNY M. RAMOS"; ?>
                                                            @endif
                                                            <input type="text" class="form-control input-sm clear" id="prepared_by" name="prepared_by" value="{{$preparedby}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-12">Remarks</label>
                                                        <div class="col-sm-12">
                                                            <textarea name="remarks" id="remarks" class="form-control input-sm clear" style="resize:none; height:50px"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-12">Note / Hightlight</label>
                                                        <div class="col-sm-12">
                                                            <textarea name="note_hightlight" id="note_hightlight" class="form-control input-sm" style="resize:none; height:50px" readonly></textarea>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 table-reponsive">
                                        <table class="table table-hover table-bordered table-striped" id="tblsummary">
                                            <thead>
                                                <tr>
                                                    <td></td>
                                                    <td>Item No.#</td>
                                                    <td>Control No.</td>
                                                    <td>P.O. No..</td>
                                                    <td>Quantity</td>
                                                    <td>Price</td>
                                                    <td>Amount</td>
                                                    <td>Draft Shipment</td>
                                                    <td>Description</td>
                                                    <td>Ship Date</td>
                                                    <td>Shipped From</td>
                                                    <td>Invoice Date</td>
                                                    {{-- <td>Products</td>
                                                    <td>Sold to Address</td>
                                                    <td>Ship To Address</td>
                                                    <td>Carrier</td>
                                                    <td>Gross Weight</td>
                                                    <td>Terms of Payment</td> --}}
                                                    
                                                    <td>Country Origin</td>
                                                    {{-- <td>Revision No.</td>
                                                    <td>Transaction No.</td>
                                                    <td>For BIR No.</td>
                                                    <td>Pick-up Date</td>
                                                    <td>Freight</td>
                                                    <td>Via</td>
                                                    <td>Sailing on</td>
                                                    <td>No. of Packaging</td>
                                                    <td>AWB No.</td>
                                                    <td>Prepared By</td>
                                                    <td>Remarks</td>
                                                    <td>Note/Highlight</td> --}}
                                                </tr>
                                            </thead>
                                            <tbody style="font-size:10px">
                                                <?php
                                                    $amount = 0;
                                                    foreach ($invoice as $inv) {
                                                        $amount = str_replace(',', '', $inv->unitprice) * $inv->quantity;
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <a href="javascript:;" class="btn input-sm blue btn_view" data-ctrl="{{$inv->packinglist_ctrl}}" data-box="{{$inv->item_no}}">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            </td>
                                                            <td>{{$inv->item_no}}</td>
                                                            <td>{{$inv->packinglist_ctrl}}</td>
                                                            <td>{{$inv->po_no}}</td>
                                                            <td>{{$inv->quantity}}</td>
                                                            <td>{{$inv->unitprice}}</td>
                                                            <td>{{number_format($amount,2)}}</td>
                                                            <td><a class="draft_shipment_update" data-name="draft_shipment" data-ctrl="{{$inv->packinglist_ctrl}}" data-pk="{{$inv->po_no}}">{{$inv->draft_shipment}}</a></td>
                                                            <td>{{$inv->description}}</td>
                                                            <td>{{$inv->ship_date}}</td>
                                                            <td>{{$inv->shippedfrom}}</td>
                                                            <td>{{$inv->invoice_date}}</td>
                                                            {{-- <td>{{$inv->products}}</td>
                                                            <td>{{$inv->soldto_address}}</td>
                                                            <td>{{$inv->shipto_address}}</td>
                                                            <td>{{$inv->carrier}}</td>
                                                            <td>{{$inv->gross_weight}}</td>
                                                            <td>{{$inv->terms_of_payment}}</td> --}}
                                                            
                                                            <td>{{$inv->country_origin}}</td>
                                                            {{-- <td>{{$inv->revision_no}}</td>
                                                            <td>{{$inv->transaction_no}}</td>
                                                            <td>{{$inv->for_bir_no}}</td>
                                                            <td>{{$inv->pickup_date}}</td>
                                                            <td>{{$inv->freight}}</td>
                                                            <td>{{$inv->via}}</td>
                                                            <td>{{$inv->sailing_on}}</td>
                                                            <td>{{$inv->no_of_packaging}}</td>
                                                            <td>{{$inv->awb_no}}</td>
                                                            <td>{{$inv->prepared_by}}</td>
                                                            <td>{{$inv->remarks}}</td>
                                                            <td>{{$inv->note_hightlight}}</td> --}}
                                                        </tr>
                                                <?php
                                                    }
                                                ?>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-md-12 text-center">
                                        <a href="javascript:;" class="btn blue" id="btn_save">
                                            <i class="fa fa-floppy-o"></i> Save
                                        </a>
                                        <a href="javascript:;" class="btn red" id="btn_clear">
                                            <i class="fa fa-eraser"></i> clear
                                        </a>
										<a href="javascript:;" class="btn blue-hoki">
                                            <i class="fa fa-print"></i> Print
                                        </a>
                                        <a href="{{url('/ypicsinvoicing')}}" class="btn grey-gallery pull-right">
                                            <i class="fa fa-reply"></i> Back
                                        </a>
                                    </div>
                                </div> --}}

                            </div>
						</div>



					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

    <!--msg-->
    <div id="modalReport" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">Choose Reports</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Date Covered:</label>
                            <div class="col-sm-4">
                                <input class="form-control input-sm date-picker" type="text" name="from_rep" id="from_rep"/>
                            </div>
                            <div class="col-sm-4">
                                <input class="form-control input-sm date-picker" type="text" name="to_rep" id="to_rep"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <a href="javascript:report('summary');" class="btn blue">
                                    <i class="fa fa-file-text-o"></i>Invoice Summary</a>
                                <a href="javascript:report('shipping');" class="btn purple">
                                    <i class="fa fa-file-text-o"></i>Shipping List</a>
                                <a href="javascript:report('sales');" class="btn green">
                                    <i class="fa fa-bar-chart-o"></i>Sales Report</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--msg-->
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

    <!--msg_success-->
    <div id="msg_success" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 id="success_title" class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p id="success_msg"></p>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button> --}}
                    <a href="{{url('/detailsypicsinvoicing/'.$ctrl_no)}}" class="btn btn-success" id="success_done">Done</a>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')
    <script type="text/javascript" src="{{asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js')}}"></script>
    {{-- <script type="text/javascript" src="{{asset(Config::get('constants.PUBLIC_PATH').'assets/admin/pages/scripts/form-editable.js')}}"></script> --}}
    <script type="text/javascript">
        $(function() {
            initView();

            // FormEditable.init();

            $('#tblsummary').dataTable({
                scrollY: 300,
                scrollX: true,
            });

            $.fn.editable.defaults.mode = 'popup';
            $.fn.editable.defaults.params = function (params) {
                params._token = $("meta[name=csrf-token]").attr("content");
                return params;
            };
            $('.draft_shipment_update').editable({
                validate: function(value) {
                    if($.trim(value) == '') 
                        return 'Value is required.';
                },
                type: 'text',
                url:"{{url('/edit-draftshipment')}}"+"?ctrl="+$('.draft_shipment_update').attr('data-ctrl'),
                title: 'Draft Shipment',
                placement: 'top', 
                send:'always',
                ajaxOptions: {
                    dataType: 'json'
                }
            });

            $('.btn_view').on('click', function() {
                var url = "{{url('/getdetails')}}";
                var token = "{{ Session::token() }}";
                var ctrl_no = $(this).attr('data-ctrl');
                var box_no = $(this).attr('data-box');
                var data = {
                    _token: token,
                    ctrl_no: ctrl_no,
                    box_no: box_no
                };

                $.ajax({
                    url: url,
                    type: "GET",
                    data: data,
                }).done( function(data, textStatus, jqXHR) {
                    $.each(data, function(i,pkl) {
                        console.log(pkl);
                        $('#itemno').val(pkl.item_no);
                        $('#packinglist_ctrl').val(ctrl_no);
                        $('#description_of_goods').val(pkl.description_of_goods);
                        $('#sold_to_id').val(data[0].sold_to_id);
                        $('#soldto_address').val(pkl.soldto_address);
                        $('#shipto_address').val(pkl.shipto_address);
                        $('#shippedfrom').val(pkl.shippedfrom);
                        $('#shipto').val(pkl.shipto);
                        $('#carrier').val(pkl.carrier);
                        $('#gross_weight').val(pkl.gross_weight);
                        $('#terms_of_payment').val(pkl.terms_of_payment);
                        $('#po_no').val(pkl.po_no);
                        $('#description').val(pkl.description);
                        $('#country_origin').val(pkl.country_origin);
                        $('#quantity').val(pkl.quantity);
                        $('#unitprice').val(pkl.unitprice.toFixed(4));
                        $('#amount').val(pkl.amount);
                        $('#freight').val(pkl.freight);
                        $('#note_hightlight').val(pkl.note_hightlight);
                        $('#ship_date').val(pkl.ship_date);
                        $('#casemarks').val(pkl.case_marks);
                        $('#revision_no').val(pkl.revision_no);
                        $('#transaction_no').val(pkl.transaction_no);
                        $('#for_bir_no').val(pkl.for_bir_no);
                        $('#via').val(pkl.via);
                        $('#sailing_on').val(pkl.sailing_on);
                        $('#no_of_packaging').val(pkl.no_of_packaging);
                        $('#awb_no').val(pkl.awb_no);
                        $('#prepared_by').val(pkl.prepared_by);
                        $('#remarks').val(pkl.remarks);
                        $("#pickup_date").text(pkl.remarks_pickupdate);
                        $("#invoice_date").val(pkl.invoice_date);
                        //$("#pickup_date").datepicker('setDate', new Date(pkl.remarks_pickupdate));
                        //getCarrier(pkl.carrier);
                    });
                }).fail( function(data, textStatus, jqXHR) {
                    $('#msg').modal('show');
                    $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#err_msg').html("There's some error while processing.");
                });
            });

            $('#btn_save').on('click', function() {
                save();
            });

            $('#btn_clear').on('click', function() {
                $('.clear').val('');
            });

            $('#btn_report').on('click', function() {
                $('#modalReport').modal('show');
            });

            $('#search_btn').on('click', function() {
                 window.location.href="{{url('/detailsypicsinvoicing')}}"+"/"+$('#packinglist_ctrl').val();
            });
        });

        function save() {
            var itemno = $('#itemno').val(), packinglist_ctrl = $('#packinglist_ctrl').val(), products = $('#products').val(),
                soldto_address = $('#soldto_address').val(), shipto_address = $('#shipto_address').val(), shippedfrom = $('#shippedfrom').val(),
                shipto = $('#shipto').val(), carrier = $('#carrier').val(), gross_weight = $('#gross_weight').val(),
                terms_of_payment = $('#terms_of_payment').val(), po_no = $('#po_no').val(), description = $('#description').val(),
                country_origin = $('#country_origin').val(), quantity = $('#quantity').val(), unitprice = $('#unitprice').val(),
                amount = $('#amount').val(), revision_no = $('#revision_no').val(), transaction_no = $('#transaction_no').val(),
                for_bir_no = $('#for_bir_no').val(), pickup_date = $('#pickup_date').val(), invoice_date = $('#invoice_date').val(),
                freight = $('#freight').val(), via = $('#via').val(), sailing_on = $('#sailing_on').val(),
                no_of_packaging = $('#no_of_packaging').val(), awb_no = $('#awb_no').val(), prepared_by = $('#prepared_by').val(),
                remarks = $('#remarks').val(), note_hightlight = $('#note_hightlight').val(), ship_date = $('#ship_date').val(),
                description_of_goods = $('#description_of_goods').val(), casemarks = $('#casemarks').val(); sold_to_id = $('#sold_to_id').val();

            var url = "{{url('/savedetails')}}";
            var token = "{{ Session::token() }}";
            var data = {
                _token: token,
                itemno : itemno,
                packinglist_ctrl : packinglist_ctrl,
                products : products,
                soldto_address : soldto_address,
                shipto_address : shipto_address,
                shippedfrom : shippedfrom,
                shipto : shipto,
                carrier : carrier,
                gross_weight : gross_weight,
                terms_of_payment : terms_of_payment,
                po_no : po_no,
                description : description,
                country_origin : country_origin,
                quantity : quantity,
                unitprice : unitprice,
                amount : amount,
                revision_no : revision_no,
                transaction_no : transaction_no,
                for_bir_no : for_bir_no,
                pickup_date : pickup_date,
                invoice_date : invoice_date,
                freight : freight,
                via : via,
                sailing_on : sailing_on,
                no_of_packaging : no_of_packaging,
                awb_no : awb_no,
                prepared_by : prepared_by,
                remarks : remarks,
                note_hightlight : note_hightlight,
                ship_date : ship_date,
                description_of_goods : description_of_goods,
                casemarks : casemarks,
                sold_to_id : sold_to_id,
            };

            if (checkRequired() == true) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: data,
                }).done( function(data, textStatus, jqXHR) {
                    $('#msg_success').modal('show');
                    $('#success_title').html('<strong><i class="fa fa-check"></i></strong> Success!')
                    $('#success_msg').html(data.msg);
                    console.log(data.msg);
                }).fail( function(data, textStatus, jqXHR) {
                    $('#msg').modal('show');
                    $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                    $('#err_msg').html("There's some error while processing.");
                });
            } else {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html('You need to fill out some more input in "For Input Section"');
            }
        }

        function getNCV(){
            var url = "{{url('/getncv')}}";
            var token = "{{ Session::token() }}";
            var ctrl_no = $('#packinglist_ctrl').val();
            var data = {
                _token: token,
                ctrl_no: ctrl_no,
            };

            $.ajax({
                url: url,
                type: "GET",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                console.log(data);
                $('#description_of_goods').val(data);
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing Description of Goods.");
            });
        }

        function initView() {
            var url = "{{url('/getinitdetails')}}";
            var token = "{{ Session::token() }}";
            var ctrl_no = $('#packinglist_ctrl').val();
            var data = {
                _token: token,
                ctrl_no: ctrl_no,
            };

            $.ajax({
                url: url,
                type: "GET",
                dataType: 'JSON',
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                console.log(data);
                //$.each(data, function(i,pkl) {
                    $('#packinglist_ctrl').val(ctrl_no);
                    getNCV();/*$('#description_of_goods').val(getNCV());*/
                    $('#sold_to_id').val(data[0].sold_to_id);
                    $('#soldto_address').val(data[0].soldto_address);
                    $('#shipto_address').val(data[0].shipto_address);
                    $('#shippedfrom').val(data[0].shippedfrom);
                    $('#shipto').val(data[0].shipto);
                    $('#carrier').val(data[0].carrier);
                    $('#gross_weight').val(data[0].gross_weight);
                    $('#terms_of_payment').val(data[0].terms_of_payment);
                    $('#po_no').val(data[0].po_no);
                    $('#description').val(data[0].description);
                    $('#country_origin').val(data[0].country_origin);
                    $('#quantity').val(data[0].quantity);
                    $('#unitprice').val(data[0].unitprice.toFixed(4));
                    $('#amount').val(data[0].amount);
                    $('#freight').val(data[0].freight);
                    $('#note_hightlight').val(data[0].note_hightlight);
                    $('#ship_date').val(data[0].ship_date);
                    $('#casemarks').val(data[0].case_marks);
                    $('#revision_no').val(data[0].revision_no);
                    $('#transaction_no').val(data[0].transaction_no);
                    $('#for_bir_no').val(data[0].for_bir_no);
                    $('#via').val(data[0].via);
                    $('#sailing_on').val(data[0].sailing_on);
                    $('#no_of_packaging').val(data[0].no_of_packaging);
                    $('#awb_no').val(data[0].awb_no);
                    // $('#prepared_by').val(data[0][0].prepared_by);
                    $('#remarks').val(data[0].remarks);
                    var date = new Date(data[0].pickup_date);
                    // $("#pickup_date").datepicker().val((date.getMonth() + 1) + '/' + date.getDate() + '/' +  date.getFullYear());
                    $("#pickup_date").val(data[0].pickup_date);
                    $("#carrier").val(data[0].carrier);
                    $("#invoice_date").val(data[0].invoice_date);
                    invoiceStatus();
                    //getCarrier(data[0].carrier);
                    //getDescOfGoods(pkl.description_of_goods);
                //});
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
        }

        function getCarrier(id) {
            var url = "{{url('/getcarrier')}}";
            var token = "{{ Session::token() }}";
            var data = {
                _token: token,
                id: id
            };
            $.ajax({
                url: url,
                type: "GET",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                $('#carrier').val(data);
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
        }

        function getDescOfGoods(id) {
            var url = "{{url('/getdescgoods')}}";
            var token = "{{ Session::token() }}";
            var data = {
                _token: token,
                id: id
            };
            $.ajax({
                url: url,
                type: "GET",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                $('#description_of_goods').val(data);
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
        }

        function formatDate(date) {
            return (date.getMonth() + 1) + '-' + date.getDate() + '-' +  date.getFullYear();
        }

        function checkRequired() {
            var revision_no = $('#revision_no').val();
            var transaction_no = $('#transaction_no').val();
            var for_bir_no = $('#for_bir_no').val();
            var via = $('#via').val();
            var sailing_on = $('#sailing_on').val();
            var no_of_packaging = $('#no_of_packaging').val();
            var awb_no = $('#awb_no').val();

            if (revision_no == '' || transaction_no == '' || for_bir_no == '' || via == '' || sailing_on == '' || no_of_packaging == '' || awb_no == '') {
                return false;
            } else {
                return true;
            }
        }

        function print() {
            var ctrl = $('#packinglist_ctrl').val();
            window.location = '{{url("/printinvoicing")}}'+'/'+ctrl;
        }

        function report(reps) {
            var from_rep = $('#from_rep').val(), to_rep = $('#to_rep').val();
            if (reps == 'summary') {
                window.location.href= "{{url('/invoicesummary?from_rep=')}}" + from_rep + "&to_rep=" + to_rep;
            }
            if (reps == 'shipping') {
                 window.location.href= "{{url('/shippinglist?from_rep=')}}" + from_rep + "&to_rep=" + to_rep;
            }
            if (reps == 'sales') {
                window.location.href= "{{url('/salesreport?from_rep=')}}" + from_rep + "&to_rep=" + to_rep;
            }
        }

        function invoiceStatus()
        {
            var url = "{{url('/invoicestatus')}}";
            var token = "{{ Session::token() }}";
            var ctrl_no = $('#packinglist_ctrl').val();
            var data = {
                _token: token,
                ctrl_no: ctrl_no,
            };

            $.ajax({
                url: url,
                type: "GET",
                data: data,
            }).done( function(data, textStatus, jqXHR) {
                console.log(data);
                $('#invoice_status').html(data);
            }).fail( function(data, textStatus, jqXHR) {
                $('#msg').modal('show');
                $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
                $('#err_msg').html("There's some error while processing.");
            });
        }


    </script>
@endpush
