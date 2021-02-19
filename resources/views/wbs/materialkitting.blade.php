@extends('layouts.master')

@section('title')
	WBS | Pricon Microelectronics, Inc.
@endsection

@push('css')
	<style type="text/css">
        table.table-fixedheader {
            width: 100%;
        }
        table.table-fixedheader, table.table-fixedheader>thead, table.table-fixedheader>tbody, table.table-fixedheader>thead>tr, table.table-fixedheader>tbody>tr, table.table-fixedheader>thead>tr>td, table.table-fixedheader>tbody>td {
            display: block;
        }
        table.table-fixedheader>thead>tr:after, table.table-fixedheader>tbody>tr:after {
            content:' ';
            display: block;
            visibility: hidden;
            clear: both;
        }
        table.table-fixedheader>tbody {
            overflow-y: scroll;
            height: 200px;
        }
        table.table-fixedheader>thead {
            overflow-y: scroll;
        }
        table.table-fixedheader>thead::-webkit-scrollbar {
            background-color: inherit;
        }

        table.table-fixedheader>thead>tr>td:after, table.table-fixedheader>tbody>tr>td:after {
            content:' ';
            display: table-cell;
            visibility: hidden;
            clear: both;
        }

        table.table-fixedheader>thead tr td, table.table-fixedheader>tbody tr td {
            float: left;
            word-wrap:break-word;
            height: 40px;
        }

        table.table-fifo>tbody {
            overflow-y: scroll;
            height: 375px;
        }
       /* #hd_barcode {
        	position: absolute;
		    z-index: -1;
        }*/
    </style>
@endpush

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_WBS'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach
    
    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                @include('includes.message-block')
                <div class="portlet box blue" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-navicon"></i>  WBS Material Kitting & Issuance
                        </div>
                        <div class="actions">
                            <div class="btn-group">
                                <button class="btn grey-gallery input-sm" id="btn_kittinglist" <?php echo($state); ?>>
                                    <i class="fa fa-list"></i> Kitting List PDF
                                </button>
                                
                                <a target="_tab" class="btn purple-plum input-sm" id="btn_print" <?php echo($state); ?>>
                                    <i class="fa fa-print"></i> Material Issuance Sheet
                                </a>
                                <button id="btn_reasonlogs" class="btn btn-success input-sm" <?php echo($state); ?>>
                                    <i class="fa fa-file-o"></i> FIFO Reason Logs
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group row">

                                    <label class="control-label col-md-3">Issuance No.</label>
                                    <div class="col-md-9">
                                        <input type="hidden" class="form-control input-sm" id="kitinfo_id" name="kitinfo_id"/>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-sm add" id="issuanceno" name="issuanceno">

                                            <span class="input-group-btn">
                                                <a href="javascript:navigate('first');" id="btn_min" class="btn blue input-sm"><i class="fa fa-fast-backward"></i></a>
                                                <a href="javascript:navigate('prev');" id="btn_prv" class="btn blue input-sm"><i class="fa fa-backward"></i></a>
                                                <a href="javascript:navigate('next');" id="btn_nxt" class="btn blue input-sm"><i class="fa fa-forward"></i></a>
                                                <a href="javascript:navigate('last');" id="btn_max" class="btn blue input-sm"><i class="fa fa-fast-forward"></i></a>
                                            </span>
                                        </div>

                                        
                                    </div>
                                     
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-3">PO No.</label>
                                    <div class="col-md-6">
                                        <form method="POST" action="{{url('/material-kitting/searchpo')}}" id="searchpoForm">
                                            {!! csrf_field() !!}
                                            <div class="input-group">
                                                <input type="text" class="form-control input-sm add" id="searchpono" name="po" maxlength="15">
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn input-sm green" id="btn_searchpo"><i class="fa fa-arrow-circle-down"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-3">Device Code</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control input-sm add" id="devicecode" name="devicecode">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-3">Device Name</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control input-sm add" id="devicename" name="devicename">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-3">PO Qty.</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control input-sm add" id="poqty" name="poqty">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Kit Qty.</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control input-sm add" name="kitqty" id="kitqty">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn input-sm green" id="btn_kitqty"><i class="fa fa-arrow-circle-down"></i></button>
                                            </span>
                                        </div>
                                         
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Kit No.</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control input-sm add" id="kitno" name="kitno">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Prep. By</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control input-sm add" id="preparedby" name="preparedby">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Status</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control input-sm add" id="status" name="status" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Assessment:#</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control input-sm add" id="assessment" name="assessment" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Created By</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control input-sm add" id="createdby" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Created Date.</label>
                                    <div class="col-md-7">
                                        <input class="form-control input-sm add" type="text" id="createddate" readonly/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Updated By</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control input-sm add" id="updatedby" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-4">Updated Date</label>
                                    <div class="col-md-7">
                                        <input class="form-control input-sm add" type="text" id="updateddate" readonly>
                                    </div>
                                </div>
                                <input type="hidden" id="save_type" name="save_type">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tabbable-custom">
                                    <ul class="nav nav-tabs nav-tabs-lg" id="tabslist" role="tablist">
                                        <li class="active" id="kitdetailsli">
                                            <a href="#kitdetails" data-toggle="tab" data-toggle="tab" aria-expanded="true">Kit Details</a>
                                        </li>
                                        <li id="issuancedetailsli">
                                            <a href="#issuancedetails" data-toggle="tab" data-toggle="tab" aria-expanded="true">Issuance Details</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="tab-subcontents">
                                        <div class="tab-pane fade in active" id="kitdetails">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-bordered table-fixedheader table-striped" style="font-size:10px" id="tbl_kitdetails">
                                                        <thead>
                                                            <tr>
                                                                <td width="3.6%" class="table-checkbox">
                                                                    <input type="checkbox" class="tbl_kitdetails_group_checkable" id="group-checkable"/>
                                                                </td>
                                                                <td width="5.6%">Detail ID</td>
                                                                <td width="7.6%">Item/Part No.</td>
                                                                <td width="13.6%">Item Description</td>
                                                                <td width="4.6%">Usage</td>
                                                                <td width="5.6%">Rqd Qty.</td>
                                                                <td width="4.6%">Kit Qty.</td>
                                                                <td width="6.6%">Issued Qty.</td>
                                                                <td width="13.6%">Location</td>
                                                                <td width="10.6%">Drawing No.</td>
                                                                <td width="7.6%">Supplier</td>
                                                                <td width="7.6%">Whse100</td>
                                                                <td width="7.6%">Whse102</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbl_kitdetails_body"></tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <br>
                                                <div class="col-md-12 text-center">
                                                    <button type="button" class="btn red input-sm" id="btn_delete_kit_details">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="tab-pane fade" id="issuancedetails">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-striped table-bordered table-hover table-fixedheader" style="font-size:10px" id="tbl_issuance">
                                                        <thead>
                                                            <tr>
                                                                <td width="3.09%" class="table-checkbox">
                                                                    <input type="checkbox" class="tbl_issuance_group_checkable"/>
                                                                </td>
                                                                <td width="6.09%"></td>
                                                                <td width="6.09%">Detail ID</td>
                                                                <td width="12.09%">Item/Part No.</td>
                                                                <td width="18.09%">Item Description</td>
                                                                <td width="6.09%">Kit Qty.</td>
                                                                <td width="6.09%">Issued Qty.</td>
                                                                <td width="12.09%">Lot No.</td>
                                                                <td width="9.09%">Location</td>
                                                                <td width="15.09%">Remarks</td>
                                                                <td width="6.09%"></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbl_issuance_body"></tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <a href="javascript:;" class="btn btn-success input-sm" id="btn_add_issuance_details">
                                                        <i class="fa fa-plus"></i> Add Details
                                                    </a>

                                                    <a href="javascript:;" class="btn grey-gallery input-sm" id="btn_update_issuance_details" action="{{url('/material-kitting/savekitdetails')}}">
                                                        <i class="fa fa-floppy-o"></i> Update Details
                                                    </a>

                                                    <a href="javascript:;" class="btn btn-danger input-sm" id="btn_delete_issuance_details">
                                                        <i class="fa fa-trash"></i> Delete Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn green input-sm" id="btn_addPO" onclick="javascript:setControl('ADD');">
                                    <i class="fa fa-plus"></i> Add New
                                </button>
                                <button type="button" class="btn btn-primary input-sm" id="btn_save" action="{{url('/material-kitting/savekitdetails')}}">
                                    <i class="fa fa-floppy-o"></i> Save Issuance
                                </button>
                                {{-- <button type="button" class="btn btn-primary input-sm" id="btn_saveIssueance">
                                    <i class="fa fa-floppy-o"></i> Save Issuance
                                </button> --}}
                                <button type="button" id="btn_edit" class="btn blue-madison input-sm" onclick="javascript:setControl('EDIT');">
                                    <i class="fa fa-pencil"></i> Edit
                                </button>
                                <button type="button" id="btn_cancel" class="btn red input-sm" onclick="javascript:confirm_modal('Are you sure you want to cancel this P.O.?');">
                                    <i class="fa fa-trash"></i> Cancel Issuance No.
                                </button>
                                <button type="button" id="btn_discard" class="btn red-intense input-sm" onclick="javascript:setControl('DISCARD');">
                                    <i class="fa fa-times"></i> Back
                                </button>
                                <button type="button" class="btn blue-steel input-sm" id="btn_search"><i class="fa fa-search"></i> Search</button>
                            </div>
                            <input type="hidden" name="brsense" id="brsense">
                            <input type="hidden" id="hd_barcode" name="hd_barcode" />
                        </div>

                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>
        </div>
    </div>


    @include('includes.materialkitting-modal')
    @include('includes.modals')
@endsection
@push('script')
    <script type="text/javascript">
        var token = "{{ Session::token() }}";
        var currentUser = "{{ Auth::user()->user_id }}";
        var materialKittingDataURL = "{{ url('material-kitting/wbsmaterialkittingdata') }}";
        var kittingListURL = "{{ url('material-kitting/kitting-list') }}";
        var transferSlipURL = "{{ url('material-kitting/transfer-slip') }}";
        var DeleteIssDetailsURL = "{{ url('material-kitting/delete-issdetails') }}";
        var DeleteKitDetailsURL = "{{ url('material-kitting/delete-kitdetails') }}";
        var CancelPoURL = "{{ url('material-kitting/cancel-po') }}";
        var getItemAndLotnumFifoURL = "{{ url('material-kitting/item-lot-fifo') }}";
        var checkIssuedQtyURL = "{{ url('material-kitting/check-issued-qty') }}";
        var searchFilterURL = "{{ url('material-kitting/search-filter') }}";
        var printBarCodeURL = "{{ url('material-kitting/brprint') }}";
        var fifoReasonURL = "{{ url('material-kitting/fiforeason') }}";
        var reasonLogsURL = "{{ url('material-kitting/reasonlogs') }}";
        var KitDetailsURL = "{{ url('material-kitting/kitdata') }}";
        var CheckAuthorizedUserURL =  "{{ url('material-kitting/check-user')}}";
        var access_state = "{{ $pgaccess }}";
        var pcode = "{{ $pgcode }}";
    </script>
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/common.js') }}" type="text/javascript"></script>
    <script src="{{ asset(config('constants.PUBLIC_PATH').'assets/global/scripts/materialkitting.js') }}" type="text/javascript"></script>
@endpush