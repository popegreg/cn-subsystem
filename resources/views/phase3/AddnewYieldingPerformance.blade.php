@extends('layouts.master')    
@section('title')
     Yield Performance | Pricon Microelectronics, Inc.
@endsection

@section('content')

     <?php $state = ""; $readonly = ""; ?>
     @foreach ($userProgramAccess as $access)
          @if ($access->program_code == Config::get('constants.MODULE_CODE_YLDPRFMNCE'))  <!-- Please update "2001" depending on the corresponding program_code -->
               @if ($access->read_write == "2")
               <?php $state = "disabled"; $readonly = "readonly"; ?>
               @endif
          @endif
     @endforeach
     
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-sm-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                @include('includes.message-block')
                    <div class="portlet box blue" >
                        <div class="portlet-title">
                            <div class="caption">
                                 <i class="fa fa-navicon"></i>  Yield Performance
                            </div>
                            <div class="tools">
                                 <button onclick="javascript:back();" id="btnback" class="btn btn-sm red">Back</button>
                            </div>
                        </div>

                        <div class="portlet-body">
                           
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            
                                            <div class="portlet box">

                                                <div class="portlet-body">
                                                    <div class="row">
                                                        <div class="col-sm-12">  
                                                            <form class="form-horizontal">
                                                                {!! csrf_field() !!}
                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-3">Control No</label>
                                                                        <div class="col-sm-6">
                                                                            <input class="form-control input-sm" size="16" type="hidden" value="<?php if(isset($count)){
                                                                                    echo $count->yieldingno + 1;
                                                                                } else {
                                                                                    echo $count + 1;
                                                                                } ?>" name="hdyieldingno" id="hdyieldingno" />
                                                                            <input placeholder="Search by Control#/PO#" class="form-control input-sm" size="16" type="text"  name="yieldingno" id="yieldingno" />  
                                                                        </div>   
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-3">PO No.</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" value="@foreach($msrecords as $msrec){{$msrec->PO}}@endforeach" class="form-control input-sm" id="pono" name="pono"/>
                                                                            <div id="er1"></div>

                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <button type="button" onclick="javascript:searchpo();" name="search-task"  class="btn btn-circle input-sm green load-task"  id="btnload">
                                                                               <i class="fa fa-arrow-circle-down"></i> 
                                                                            </button>
                                                                        </div>                                                           
                                                                     </div>

                                                                     <div class="form-group">
                                                                        <label class="control-label col-sm-3">PO Qty</label>
                                                                        <div class="col-sm-6">
                                                                            <input class="form-control input-sm" size="16" type="text" name="poqty" value="@foreach($msrecords as $msrec){{$msrec->POqty}} @endforeach" id="poqty" disabled="disabled"/> 
                                                                            <div id="error1"></div>  
                                                                        </div>
                                                                    </div> 

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-3">Device</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="device" name="device" value="@foreach($msrecords as $msrec){{$msrec->devicename}}@endforeach" disabled="disabled"/>
                                                                            <div id="error2"></div>   
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-3">Family</label>
                                                                        <div class="col-sm-6">
                                                                            <Select class="form-control input-sm" id="family" name="family">
                                                                                <option value=""></option>
                                                                                @foreach ($family as $fam)
                                                                                    <option value="{{$fam->family}}">{{$fam->family}}</option>
                                                                                @endforeach
                                                                            </Select>
                                                                            <div id="er2"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-3">Series</label>
                                                                        <div class="col-sm-6">
                                                                            <Select class="form-control input-sm" id="series" name="series" required>
                                                                                <option value=""></option>
                                                                                @foreach ($devreg as $serie)
                                                                                   <option value="{{$serie->series}}">{{$serie->series}}
                                                                                   </option>
                                                                                @endforeach
                                                                            </Select>
                                                                            <div id="er3"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-3">Product Type</label>
                                                                        <div class="col-sm-6">
                                                                            <Select class="form-control input-sm" id="prodtype" name="prodtype" required>
                                                                                <option value=""></option>
                                                                                <option value="Test Socket">Test Socket</option>
                                                                                <option value="Burn In">Burn In</option>
                                                                            </Select>
                                                                            <div id="erprodtype"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Production Date</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control datepicker input-sm" id="productiondate" name="productiondate"/>
                                                                        </div>
                                                                          
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Yielding Station</label>
                                                                        <div class="col-sm-6">
                                                                            <Select class="form-control input-sm" id="yieldingstation" name="yieldingstation">
                                                                                <option value=""></option>
                                                                                @foreach($yieldstation as $ys)
                                                                                    <option value="{{$ys->description}}">{{$ys->description}}
                                                                                    </option>
                                                                                @endforeach
                                                                            </Select>
                                                                            <div id="er6"></div>
                                                                        </div>
                                                                    </div>

                                                                     <div class="form-group">
                                                                        <label class="control-label col-sm-4">Accumulated Output</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="accumulatedoutput" name="accumulatedoutput" />
                                                                            <div id="er7"></div>
                                                                        </div>
                                                                        <div class="col-sm-2">
                                                                            <button type="button" onclick="javascript:addpya();" name="search-task"  class="btn btn-circle input-sm green load-task"  id="btnloadpya">
                                                                            <i class="fa fa-plus"></i> 
                                                                            </button>
                                                                        </div>     
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Classification</label>
                                                                        <div class="col-sm-6">
                                                                            <Select class="form-control input-sm" id="classification" name="classification">
                                                                                <option value=""></option>
                                                                                <option value="NDF">NDF</option>
                                                                                <option value="Material NG (MNG)">Material NG (MNG)</option>
                                                                                <option value="Production NG (PNG)">Production NG (PNG)</option>   
                                                                            </Select>
                                                                            <div id="er4"></div>
                                                                        </div>   
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Mode of Defect</label>
                                                                        <div class="col-sm-6">
                                                                            <Select class="form-control input-sm mod" id="mod" name="mod">
                                                                                
                                                                            </Select>
                                                                            <div id="er5"></div>
                                                                        </div>   
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Qty</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="qty" name="qty" />
                                                                            <div id="er10"></div>
                                                                        </div>
                                                                        <div class="col-sm-2">
                                                                            <button type="button" onclick="javascript:addcmq();" name="search-task"  class="btn btn-circle input-sm green load-task"  id="btnloadcmq">
                                                                            <i class="fa fa-plus"></i> 
                                                                            </button>
                                                                        </div>       
                                                                     </div>
                                                                </div>

                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Total Output</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="toutput" name="toutput"  />
                                                                            <div id="er8"></div>
                                                                          </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Total Reject</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="treject" name="treject"/>
                                                                            <div id="er9"></div>
                                                                        </div>
                                                                     </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Total MNG</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="tmng" name="tmng"  disabled="disabled" />
                                                                        </div>
                                                                     </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Total PNG</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="tpng" name="tpng"  disabled="disabled" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">% Yield w/o MNG</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="ywomng" name="ywomng"  disabled="disabled" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4">Total Yield</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control input-sm" id="twoyield" name="twoyield"  disabled="disabled" />
                                                                            <input type="hidden" class="form-control input-sm" id="counter" name="counter"  disabled="disabled" />
                                                                        </div>
                                                                     </div>

                                                                </div>
                                                            </form>

                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="form-group pull-right">
                                                        <label class="control-label col-sm-2">DPPM</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control input-sm" id="dppm" name="dppm" disabled="disabled">
                                                            <input type="hidden" class="form-control input-sm " name="hdstatus" id="hdstatus"></input>
                                                        </div>    
                                                    </div>
                                                    <!-- Action Buttons -->
                                                    <br/>
                                                    <div class="row">
                                                        <div class="col-sm-12 text-center">
                                                            <button type="button" style="font-size:12px;" onclick="javascript:addnew();" class="btn green input-sm" id="btnadd">
                                                               <i class="fa fa-plus"></i> Add New
                                                            </button>
                                                            <button type="button" style="font-size:12px;" onclick="javascript: setcontrol('DIS'); " class="btn red-intense input-sm" id="btndiscard">
                                                               <i class="fa fa-pencil"></i> Discard Changes
                                                            </button>
                                                            <button type="button" style="font-size:12px;" onclick="javascript:save();" class="btn green input-sm" id="btnsave">
                                                               <i class="fa fa-save"></i> Save
                                                            </button>
                                                            <button type="button" style="font-size:12px;" class="btn blue-steel input-sm" id="btnsearch" onclick="javascript:searchrecord();">
                                                               <i class="fa fa-search"></i> Search
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                                                  
                                    <div class="row">
                                        <div class="col-sm-5 col-sm-offset-1">
                                            <label class="control-label col-sm-12">
                                                <h4>Table for (Production Date,Yielding Station and Accumulated Data)</h4>
                                            </label>
                                        </div>
                                        <div class="col-sm-5">
                                            <label class="control-label col-sm-12">
                                                <h4>Table for (Classification,Mode of Defect/s and Quantity)</h4>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5 col-sm-offset-1">
                                            <div class="scroller" style="height: 200px" id="tblforpya">
                                                <table id="results1" class="table table-striped table-bordered table-hover"style="font-size:13px">
                                                    <thead id="thead1">
                                                        <tr>
                                                            <td class="table-checkbox" style="width: 5%">
                                                                <input type="checkbox" class="group-checkable checkAllitemsPYA" name="checkAllitemPYA" data-set="#sample_3 .checkboxes"/>
                                                            </td>
                                                            <td>Production Date</td>
                                                            <td>Yielding Station</td>
                                                            <td>Accumulated Output</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody1">
                                                        <!-- tablebody1 row here! -->  
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button style="margin-top: 20px;" type="button" onclick="javascript:deletepya();" name="delete-taskPYA" class="btn btn-mg btn-danger delete-taskPYA" id="delete-taskPYA">Delete
                                                 <i class="fa fa-trash"></i> 
                                            
                                            </button>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="scroller" style="height: 200px" id="tblforcmq">
                                                <table id="results2" class="table table-striped table-bordered table-hover" style="font-size:13px">
                                                    <thead id="thead2">
                                                        <tr>
                                                            <td class="table-checkbox" style="width: 5%">
                                                                <input type="checkbox" class="group-checkable checkAllitemsCMQ" name="checkAllitemCMQ" data-set="#sample_3 .checkboxes"/>
                                                            </td>
                                                            <td>Classification</td>
                                                            <td>Mode of Defects</td>
                                                            <td>Quantity</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody2">
                                                        <!-- tablebody2 row here! -->  
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button style="margin-top:20px;" type="button" onclick="javascript:deletecmq();" name="delete-taskCMQ" class="btn btn-mg btn-danger delete-taskCMQ" id="delete-taskCMQ">Delete
                                                <i class="fa fa-trash"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="tabbable-custom">
                                                <ul class="nav nav-tabs nav-tabs-lg" id="tabslist" role="tablist">
                                                    <li class="active">
                                                        <a href="#details" data-toggle="tab" data-toggle="tab" aria-expanded="true">Details</a>
                                                    </li>
                                                    <li>
                                                        <a href="#summary" data-toggle="tab" data-toggle="tab" aria-expanded="true">Summary</a>
                                                    </li>   
                                                </ul>

                                                <!-- Details Tab -->
                                                <div class="tab-content" id="tab-subcontents">
                                                    <div class="tab-pane fade in active" id="details">
                                                        <div class="row">
                                                            <div class="col-sm-10 col-sm-offset-1">
                                                                <table class="table table-striped table-bordered table-hover" style="font-size:13px">
                                                                      <thead >
                                                                           <tr>
                                                                           <td class="table-checkbox" style="widtd: 5%">
                                                                                <input type="checkbox" class="group-checkable checkAllitems" name="checkAllitem" data-set="#sample_3 .checkboxes"/>
                                                                           </td>
                                                                           <td>Date</td>
                                                                           <td>Yield Station</td>
                                                                           <td>Output</td>
                                                                           <td>Classification</td>
                                                                           <td>MOD</td>
                                                                           <td>Qty</td>
                                                                           <td>PO No.</td>
                                                                           <td>PO Qty</td>
                                                                           <td>Device</td>
                                                                           <td>Family</td>
                                                                           <td>Series</td>
                                                                           </tr>
                                                                      </thead>
                                                                      <tbody id="tbldetails">
                                                                           
                                                                      </tbody>
                                                                </table>
                                                                </div>
                                                              <!--   <div class="col-sm-12 text-center">
                                                                     <button type="button" style="font-size:12px;" class="btn red input-sm remove-task" id="btnremove_detail">
                                                                          <i class="fa fa-trash remove-task"></i> Remove
                                                                     </button>
                                                                </div> -->
                                                            </div>
                                                        </div>
                                                        <!-- Summary Tab -->
                                                        <div class="tab-pane fade" id="summary">
                                                            <div class="row">
                                                                <div class="col-sm-10 col-sm-offset-1">
                                                                    <table class="table table-striped table-bordered table-hover summary" id="summary-table" style="font-size:13px">
                                                                        <thead>
                                                                            <tr>
                                                                                <td>PO No.</td>
                                                                                <td>PO Qty</td>
                                                                                <td>Device Name</td>
                                                                                <td>Series</td>
                                                                                <td>Family</td>
                                                                                <td>Total Output</td>
                                                                                <td>Total Reject</td>
                                                                                <td>Total Yield</td>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tblsummary">
                                                                       
                                                                        </tbody>   
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>


     <!-- Success Message Modal -->
    <div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm gray-gallery">
                <!-- Modal content-->
            <form class="form-horizontal" id="confirmForm" role="form" method="POST">
                <div class="modal-content ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="deleteAll-title" id="modalTitle"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {!! csrf_field() !!}
                            <div class="col-sm-12">
                                <label for="confirmMessage" id="confirmMessage" class="col-sm-12 control-label text-center"></label>
                            </div>    
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn btn-success" id="confirmOk" ><i class="fa fa-save"></i>OK</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

     <!--delete all modal-->
    <div id="deleteAllModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm gray-gallery">
        <!-- Modal content-->
            <form class="form-horizontal" id="deleteAllform" role="form" method="POST">
                <div class="modal-content ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="deleteAll-title">Delete Yield Performance Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {!! csrf_field() !!}
                            <div class="col-sm-12">
                                <label for="inputname" class="col-sm-12 control-label text-center">
                                Are you sure you want to delete record/s?
                                </label>
                                <input type="hidden" value="" name="deleteAllmaster" id="deleteAllmaster" />
                            </div>    
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success" id="modaldelete" ><i class="fa fa-save"></i> Yes</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

     <!-- Existing Invoice Load Pop-message-->
    <div id="multisearchModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="multisearch-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {!! csrf_field() !!}
                        <div class="form-group row">
                            <div class="col-sm-5 col-sm-offset-1">
                                <label class="control-label col-sm-4">Type</label>  
                            </div>
                            <div class="col-sm-5">
                                <label class="control-label col-sm-4">Values</label>  
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-5 col-sm-offset-1">
                                <Select class="form-control" id="mSearchtype1" name="mSearchtype1">
                                <option>Select One..</option>
                                <option value="1">Yielding No</option>
                                <option value="2">PO No.</option>
                                <option value="3">PO Qty.</option>
                                <option value="4">Device</option>
                                <option value="5">Family</option>
                                <option value="6">Series</option>
                                <option value="7">Classification</option>
                                <option value="8">Mode of Defect</option>
                                <option value="9">Quantity</option>
                                <option value="10">Production Date</option>
                                <option value="11">Yielding Station</option>
                                <option value="12">Accumulated Output</option>
                                </Select>
                            </div>
                            <div class="col-sm-5">
                                <Select class="form-control mSearchval1" id="mSearchval1" name="mSearchval1"></Select>  
                            </div>
                        </div>    
                    </div>
                </div>
                <div class="modal-footer">
                     <button type="button" onclick="javascript:multiSearchDisplay();" class="btn btn-success" id="btnmultiSearch" ><i class="fa fa-save"></i>Search</button>
                     <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>Cancel</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Existing Invoice Load Pop-message-->
    <div id="searchpoModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm blue">
            <div class="modal-content ">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="searchpo-title"></h4>
                </div>
                <div class="modal-body">
                    <h4 id="po-message"></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnok">OK</button>
                </div>
            </div>
        </div>
    </div>

@endsection
<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function(e) {
    $('#btnsave').addClass("disabled");
    $('#btnload').addClass("disabled");
    $('#btnloadpya').addClass("disabled");
    $('#btnloadcmq').addClass("disabled");
    $('#btndiscard').addClass("disabled");
    $('#productiondate').datepicker();
    $('#family').select2();
    $('#series').select2();
    $('#prodtype').select2();
    $('#yieldingstation').select2();
    $('#mod').select2();
    $('#classification').select2();
    
    $('#yieldingstation').change(function(){
        $('#hd_yieldingstation').val($(this).val());
        if($(this).val() == "Machine"){
            $('#hd_accumulatedoutput').val(0);
        }
        if($(this).val() == "First Visual Inspection"){
            $('#hd_accumulatedoutput').val(0);
        }
        if($(this).val() == "Final Visual Inspection"){
            $('#hd_accumulatedoutput').val("");
        }
    });
    $('#accumulatedoutput').keyup(function(){
        $('#hd_accumulatedoutput').val($(this).val());
    });
    
  
    $('input[name=pono]').attr('disabled',true);
    $('input[name=poqty]').attr('disabled',true);
    $('input[name=device]').attr('disabled',true);
    $('input[name=treject]').attr('disabled',true);
    $('input[name=toutput]').attr('disabled',true);
    $('#family').attr('disabled',true);
    $('#series').attr('disabled',true);
    $('#prodtype').attr('disabled',true);
    $('#classification').attr('disabled',true);
    $('#mod').attr('disabled',true);
    $('input[name=qty]').attr('disabled',true);
    $('input[name=productiondate]').attr('disabled',true);
    $('#yieldingstation').attr('disabled',true);
    $('input[name=accumulatedoutput]').attr('disabled',true);
    $('#yieldingno').val("");
    $('#btnremove_detail').addClass("disabled");
    $('.checkAllitemsPYA').attr('disabled',false);
   
    $('#hdstatus').val("");

  
//-------------------------------------------------------------------------------------multisearching---------------
    $('#mSearchtype1').change(function(e){
        var mSearchtype1 = $('#mSearchtype1').val();
        var mSearchval1 = $('#mSearchval1').val();
        var myData = {'mSearchtype1':mSearchtype1,'mSearchval1':mSearchval1};
        var fieldname = "";
        $.post("{{ url('/multisearch-yieldperformance') }}",
        { 
            _token: $('meta[name=csrf-token]').attr('content')
            , data: myData
        }).done(function(data, textStatus, jqXHR){  
            for(var i=0;i<data.length;i++){
                var field = data[i];
                var mSearchtype1 = $('#mSearchtype1').val();
                switch(mSearchtype1){
                    case '2':
                        $('#mSearchval1').append('<option value="'+field.pono+'">'+field.pono+'</option>'); 
                        break;
                    case '3':
                        $('#mSearchval1').append('<option value="'+field.poqty+'">'+field.poqty+'</option>'); 
                        break;
                    case '4':
                        $('#mSearchval1').append('<option value="'+field.device+'">'+field.device+'</option>'); 
                        break;
                    case '5':
                        $('#mSearchval1').append('<option value="'+field.family+'">'+field.family+'</option>'); 
                        break;
                    case '6':
                        $('#mSearchval1').append('<option value="'+field.series+'">'+field.series+'</option>'); 
                        break;
                    case '7':
                        $('#mSearchval1').append('<option value="'+field.classification+'">'+field.classification+'</option>'); 
                        break;
                    case '8':
                        $('#mSearchval1').append('<option value="'+field.mod+'">'+field.mod+'</option>'); 
                        break;
                    case '9':
                        $('#mSearchval1').append('<option value="'+field.qty+'">'+field.qty+'</option>'); 
                        break;
                    case '10':
                        $('#mSearchval1').append('<option value="'+field.pruductiondate+'">'+field.productiondate+'</option>'); 
                        break;
                    case '11':
                        $('#mSearchval1').append('<option value="'+field.yieldingstation+'">'+field.yieldingstation+'</option>'); 
                        break;
                    case '12':
                        $('#mSearchval1').append('<option value="'+field.accumulatedoutput+'">'+field.accumulatedoutput+'</option>'); 
                        break;
                    default:
                        $('#mSearchval1').append('<option value="'+field.yieldingno+'">'+field.yieldingno+'</option>'); 
                        break;
                }
            }  
            return false; 
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });  

    });

    //magbabago yung laman ng series field depende sa selected family-----------------------------------
    $('#family').on('change',function(){
          $('#series').select2('val',"");
          var family = $('select[name=family]').val();
          $('#series').html("");

          $.post("{{ url('/devreg_get_series') }}",
          {
               _token:$('meta[name=csrf-token]').attr('content'),
               family:family 
          }).done(function(data, textStatus, jqXHR){
               console.log(data);
               $.each(data,function(i,val){
                    var sup = '';
                    switch(family) {
                         case "BGA":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "BGA-FP":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "LGA":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "PGA":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "PGA-LGA":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "PUS":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "Probe Pin":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "QFN":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "Socket No.2":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "SOJ":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "TSOP":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                        default:
                              var sup = '<option value=""></option>';
                    }
                         
                    //var option = '<option value="'+val.supplier'">'+val.supplier'</option>';
                    var option = sup;
                    $('#series').append(option);
               });
          
          }).fail(function(jqXHR, textStatus, errorThrown){
               console.log(errorThrown+'|'+textStatus);
          });
     });

    //magbabago yung mga value ng mode of defects depende sa selected product type-------------------------
    $('#prodtype').on('change',function(){
        $('#mod').select2('val',"");
        var prodtype = $('select[name=prodtype]').val();
        $('#mod').html("");

        $.post("{{ url('/get_mod') }}",
        {
            _token:$('meta[name=csrf-token]').attr('content'),
            prodtype:prodtype 
        }).done(function(data, textStatus, jqXHR){
            console.log(data);
            $.each(data,function(i,val){
                var sup = '';
                switch(prodtype) {
                    case "Test Socket":
                        var sup = '<option value="'+val.mod+'">'+val.mod+'</option>';
                        break;
                    case "Burn In":
                        var sup = '<option value="'+val.mod+'">'+val.mod+'</option>';
                        break;
                    default:
                        var sup = '<option value="'+val.mod+'">'+val.mod+'</option>';
                        break;
                }       
                         
                var option = sup;
                $('#mod').append(option);
            });
          
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown+'|'+textStatus);
        });
     });
 
//---------------------------------------------------------------------------------
    $('#modaldelete').click(function() {
        deleteAllcheckeditems();
    });

//---------------------------------------------------------------------------------
    //delete all field value ---------------------------
    $('#btndiscard').click(function(){
        $('#btnsearch').removeClass("disabled");
        $('#btnload').addClass("disabled");
        $('#btnloadpya').addClass("disabled");
        $('#btnloadcmq').addClass("disabled");
        $('#btndiscard').addClass("disabled");
        $('#btnadd').removeClass("disabled");
        $('input[name=yieldingno]').attr('disabled',false);
        $('input[name=pono]').attr('disabled',true);
        $('input[name=poqty]').attr('disabled',true);
        $('input[name=device]').attr('disabled',true);
        $('#family').attr('disabled',true);
        $('#series').attr('disabled',true);
        $('#prodtype').attr('disabled',true);
        $('#classification').attr('disabled',true);
        $('#mod').attr('disabled',true);
        $('input[name=qty]').attr('disabled',true);
        $('input[name=productiondate]').attr('disabled',true);
        $('#yieldingstation').attr('disabled',true);
        $('input[name=accumulatedoutput]').attr('disabled',true);
        $('input[name=toutput]').attr('disabled',true);
        $('input[name=treject]').attr('disabled',true);

        $('input[name=yieldingno]').val("");
        $('input[name=pono]').val("");
        $('input[name=poqty]').val("");
        $('input[name=device]').val("");
        $('#family').select2('val',"");
        $('#series').select2('val',"");
        $('#prodtype').select2('val',"");
        $('#classification').val("");
        $('#mod').val("");
        $('input[name=qty]').val("");
        $('input[name=productiondate]').val("");
        $('input[name=accumulatedoutput]').val("");
        $('#yieldingstation').val("");
        $('input[name=toutput]').val(""); 
        $('input[name=treject]').val("");
        $('input[name=tmng]').val("");
        $('input[name=tpng]').val("");
        $('input[name=ywomng]').val("");
        $('input[name=twoyield]').val(""); 
        $('#dppm').val("");
        $('#hdstatus').val("");   
        $('#tbldetails').html("");
        $('#tblsummary').html("");
        $('#tbody1').html("");
        $('#tbody2').html("");

    });

    $('.edit-task').click(function(){
        $('#productiondate').attr("disabled",false);
        var getvalue = $(this).val().split('|');
        var yieldingno = getvalue[0];
        var  productiondate = getvalue[1];
        var  yieldingstation = getvalue[2];
        var  toutput = getvalue[3];
        var  classification = getvalue[4];
        var  mod = getvalue[5];
        var  qty = getvalue[6];
        var  pono = getvalue[7];
        var  poqty = getvalue[8];
        var  device = getvalue[9];
        var  family = getvalue[10];
        var  series = getvalue[11];
        var  Aoutput = getvalue[12];
        var  treject = getvalue[13];
        var  ywomng = getvalue[14];
        var  prodtype = getvalue[15];
        $('#hdstatus').val("EDIT");
     
        $('#yieldingno').val(yieldingno);
        $('#productiondate').val(productiondate.substring(0,10));
        $('#yieldingstation').select2('val',yieldingstation);
        $('#accumulatedoutput').val(Aoutput);
        $('#classification').select2('val',classification);
        $('#mod').select2('val',mod);
        $('#qty').val(qty);
        $('#pono').val(pono);
        $('#poqty').val(poqty);
        $('#device').val(device);
        $('#family').select2('val',family);
        $('#series').select2('val',series);
        $('#prodtype').select2('val',prodtype);
        $('#toutput').val(toutput);
        $('#treject').val(treject);  
        $('#ywomng').val(ywomng);

        $('input[name=yieldingno]').attr('disabled',true);
        $('input[name=pono]').attr('disabled',true);
        $('input[name=poqty]').attr('disabled',true);
        $('input[name=device]').attr('disabled',true);
        $('#family').attr('disabled',false);
        $('#series').attr('disabled',false);
        $('#prodtype').attr('disabled',false);
        $('#classification').attr('disabled',false);
        $('#mod').attr('disabled',false);
        $('input[name=qty]').attr('disabled',false);
        
        $('#yieldingstation').attr('disabled',false);
        $('input[name=accumulatedoutput]').attr('disabled',false);
        $('input[name=toutput]').attr('disabled',true);
        $('input[name=treject]').attr('disabled',true);
    });

//--------------------------------------------------------------------------------------
    //adding of records-------------------------------
    $('#btnadd').click(function(){
        $('#btnsearch').addClass("disabled");
        $('#btnloadpya').addClass("disabled");
        $('#btnloadcmq').addClass("disabled");
        $('#btndiscard').removeClass("disabled");
        $('#btnadd').addClass("disabled");
        $('#hdstatus').val("ADD");
        var hdyieldingno = $('#hdyieldingno').val();
        $('input[name=yieldingno]').val(hdyieldingno);
        $('input[name=pono]').attr('disabled',false);      
        $('#family').attr('disabled',true);
        $('#series').attr('disabled',true);
        $('#prodtype').attr('disabled',true);
        $('#classification').attr('disabled',true);
        $('#mod').attr('disabled',true);
        $('input[name=qty]').attr('disabled',true);
        $('select[name=yieldingstation]').attr('disabled',true);  
        $('input[name=accumulatedoutput]').attr('disabled',true);  
        $('input[name=toutput]').attr('disabled',true);  
        $('input[name=treject]').attr('disabled',true);  
        $('input[name=yieldingno]').attr('disabled',true); 

        $('input[name=pono]').val("");
        $('input[name=poqty]').val("");
        $('input[name=device]').val("");
        $('#family').val("");
        $('#series').val("");
        $('#prodtype').val("");
        $('#classification').val("");
        $('#mod').val("");
        $('input[name=qty]').val("");
        $('#yieldingstation').val("");
        $('input[name=toutput]').val(""); 
        $('input[name=treject]').val("");
        $('input[name=tmng]').val("");
        $('input[name=tpng]').val("");
        $('input[name=ywomng]').val("");
        $('input[name=twoyield]').val("");  
        $('input[name=accumulatedoutput]').val("");   
    });
//------------------------------------------------------------------------------------------------
    $('#btnload').click(function(){
     /*   $('#delete-taskCMQ').addClass("disabled");
        $('#delete-taskPYA').addClass("disabled");*/
        $('#btnloadpya').removeClass("disabled");
        $('#btnloadcmq').removeClass("disabled");
        $('#family').attr('disabled',false);
        $('#series').attr('disabled',false);
        $('#prodtype').attr('disabled',false);
        $('#classification').attr('disabled',false);
        $('#mod').attr('disabled',false);
        $('input[name=qty]').attr('disabled',false);
        $('select[name=yieldingstation]').attr('disabled',false);  
        $('input[name=accumulatedoutput]').attr('disabled',false);  
        if($('#pono').val() == ''){
            $('input[name=yieldingno]').attr('disabled',true);
            $('input[name=poqty]').attr('disabled',true);
            $('input[name=device]').attr('disabled',true);
            $('#family').attr('disabled',true);
            $('#series').attr('disabled',true);
            $('#prodtype').attr('disabled',true);
            $('#classification').attr('disabled',true);
            $('#mod').attr('disabled',true);
            $('input[name=qty]').attr('disabled',true);
            $('input[name=productiondate]').attr('disabled',true);
            $('#yieldingstation').attr('disabled',true);
            $('input[name=accumulatedoutput]').attr('disabled',true);
            $('input[name=toutput]').attr('disabled',true);
            $('input[name=treject]').attr('disabled',true);
        }
    });
//---------------------------------------------------------------------------------------------    
//Details table Checkboxes------------------------------------------------ 
    $('.checkAllitems').change(function(){
        if($('.checkAllitems').is(':checked')){           
            $('input[name=checkitem]').parents('span').addClass("checked");
            $('input[name=checkitem]').prop('checked',this.checked);
            $('.edit-task').addClass("disabled");                
        }else{
            $('input[name=checkitem]').parents('span').removeClass("checked");
            $('input[name=checkitem]').prop('checked',this.checked);
            $('.deleteAll-task').addClass("disabled"); 
            $('.edit-task').removeClass("disabled");                 
        }         
    });
//-----------------------------------------------------------------------------------------------
    $('.checkboxes').change(function(){
        $('input[name=checkAllitem]').parents('span').removeClass("checked");
        $('input[name=checkAllitem]').prop('checked',false);
        var tray = [];
        $(".checkboxes:checked").each(function () {
            tray.push($(this).val());
            $('.checkAllitems').prop('checked',false);  
        });
          
        if($('.checkboxes').is(':checked')){
            $('input[name=checkAllitem]').parents('span').removeClass("checked");
            $('input[name=checkAllitem]').prop('checked',false);

        } 
    });
//---------------------------------------------------------------------------------------------    
//production Date, Yielding Performance and Accumulated Output Checkboxes---------------------------------- 
    $('.checkAllitemsPYA').change(function(){
        if($('.checkAllitemsPYA').is(':checked')){           
            $('input[name=checkitemPYA]').parents('span').addClass("checked");
            $('input[name=checkitemPYA]').prop('checked',this.checked);       
        }else{
            $('input[name=checkitemPYA]').parents('span').removeClass("checked");
            $('input[name=checkitemPYA]').prop('checked',this.checked);
          
        }         
    });

    $('.checkboxesPYA').change(function(){
        $('input[name=checkAllitemPYA]').parents('span').removeClass("checked");
        $('input[name=checkAllitemPYA]').prop('checked',false);
        var tray = [];
        $(".checkboxesPYA:checked").each(function () {
            tray.push($(this).val());
            $('.checkAllitemsPYA').prop('checked',false);
            $('#btnremove_detail').removeClass("disabled");    
        });
      
        if($('.checkboxesPYA').is(':checked')){
            $('input[name=checkAllitemPYA]').parents('span').removeClass("checked");
            $('input[name=checkAllitemPYA]').prop('checked',false);
        } else {
            $('#btnremove_detail').addClass("disabled");
        }
    });
//---------------------------------------------------------------------------------------------     
    $('.checkAllitemsCMQ').change(function(){
        if($('.checkAllitemsCMQ').is(':checked')){           
            $('input[name=checkitemCMQ]').parents('span').addClass("checked");
            $('input[name=checkitemCMQ]').prop('checked',this.checked);             
        }else{
            $('input[name=checkitemCMQ]').parents('span').removeClass("checked");
            $('input[name=checkitemCMQ]').prop('checked',this.checked);      
        }         
    });
//-----------------------------------------------------------------------------------------------
//Classification,Mode of Defects and Quantity Checkboxes----------------------------------
    $('.checkboxesCMQ').change(function(){
        $('input[name=checkAllitemCMQ]').parents('span').removeClass("checked");
        $('input[name=checkAllitemCMQ]').prop('checked',false);
        var tray = [];
        $(".checkboxes:checked").each(function () {
            tray.push($(this).val());
            $('.checkAllitemsPYA').prop('checked',false);
            $('#delete-taskCMQ').removeClass("disabled");
          
        });
          
        if($('.checkboxesPYA').is(':checked')){
            $('input[name=checkAllitemCMQ]').parents('span').removeClass("checked");
            $('input[name=checkAllitemCMQ]').prop('checked',false);
        } 
    });

//---------------------------------------------------------------------------------------------------          
    $('.remove-task').on('click', function() {
        $('#deleteAllModal').modal('show');
    });

//----------------------------------------------------------------------------------------------
    //Accumulated Output computation based on Yielding Station--
    $('#yieldingstation').change(function(){
        if($(this).val() == "Machine"){
            $('#accumulatedoutput').val("0");
            $('#accumulatedoutput').attr('disabled',true);
        }else if($(this).val() == "First Visual Inspection"){
            $('#accumulatedoutput').val("0");
            $('#accumulatedoutput').attr('disabled',true);
        }else if($(this).val() == "Final Visual Inspection"){
            $('#accumulatedoutput').val("");
            $('#accumulatedoutput').attr('disabled',false);
        }else{
            $('#accumulatedoutput').val("");
            $('#accumulatedoutput').attr('disabled',true);
        }
    });

    $('#classification').change(function(){
        var classification = $('#classification').val();
        if(classification == "NDF"){
            $('#mod').append(option);
            var sup = '<option value="NDF">NDF</option>';   
            var option = sup;
            $('#mod').append(option);
            $('#qty').val("NDF");
        } else{
            $('#qty').val("");
        }   
    });
    

});//end of script-------------------------------------------------------------------------------------

// if Button Addnew is clicked-----------------------------------
function addnew(){
    $('#btnsave').removeClass("disabled");
    $('#btnload').removeClass("disabled");
    $('#treject').attr('disabled',false);
    
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    var output = (month<10 ? '0' : '') + month + '/' +(day<10 ? '0' : '') + day + '/' +  d.getFullYear()
   
    $('input[name=productiondate]').val(output);
    $('#pono').keyup(function(){
        $('#er1').html(""); 
         
        if(!this.value){
            $('#poqty').val("");
            $('#device').val("");
            $('#family').select2('val',"");
            $('#series').select2('val',"");
            $('#prodtype').select2('val',"");
            $('#classification').val("");
            $('#mod').val("");

            $('input[name=qty]').val("");
            $('select[name=yieldingstation]').val("");
            $('input[name=accumulatedoutput]').val("");
            $('input[name=toutput]').val("");
            $('input[name=treject]').val("");

            $('input[name=tmng]').val("");
            $('input[name=tpng]').val("");
            $('input[name=ywomng]').val("");
            $('input[name=twoyield]').val("");
            $('#dppm').val("");
            $('input[name=yieldingno]').attr('disabled',true);
            $('input[name=poqty]').attr('disabled',true);
            $('input[name=device]').attr('disabled',true);
            $('#family').attr('disabled',true);
            $('#series').attr('disabled',true);
            $('#prodtype').attr('disabled',true);
            $('#classification').attr('disabled',true);
            $('#mod').attr('disabled',true);
            $('input[name=qty]').attr('disabled',true);
            $('input[name=productiondate]').attr('disabled',true);
            $('#yieldingstation').attr('disabled',true);
            $('input[name=accumulatedoutput]').attr('disabled',true);
            $('input[name=toutput]').attr('disabled',true);
            $('input[name=treject]').attr('disabled',true);
            
            $('#tbody1').html("");
            $('#tbody2').html("");
            $('#tbldetails').html("");   
            $('#tblsummary').html("");   
        }
    });

    $('#pono').click(function(){
        $('#er1').html(""); 
        if(!this.value){
            $('#poqty').val("");
            $('#device').val("");
        }
    });
  
    $('#btnload').click(function(){
        $('#error1').html("");  
    });
    $('#family').click(function(){
        $('#er2').html(""); 
    });
    $('#series').click(function(){
        $('#er3').html(""); 
    });
    $('#prodtype').click(function(){
        $('#erprodtype').html(""); 
    });
    $('#classification').click(function(){
        $('#er4').html(""); 
    });
    $('#accumulatedoutput').keyup(function(){
        $('#er7').html(""); 
    });
    $('#mod').click(function(){
        $('#er5').html(""); 
    });
    $('#yieldingstation').click(function(){
        $('#er6').html(""); 
    });
    $('#toutput').keyup(function(){
        $('#er8').html(""); 
    });
    $('#treject').keyup(function(){
        $('#er9').html(""); 
    });
    $('#qty').keyup(function(){
        $('#er10').html(""); 
    });
}

//saving Yielding Performance Record------------------------------------
function save(){
    var hdstatus = $('#hdstatus').val();
    var yieldingno = $('input[name=yieldingno]').val();
    $('#hdyieldingno').val(parseInt(yieldingno) + 1);
    $('#yieldingno').val(parseInt(yieldingno) + 1);
    var pono = $('input[name=pono]').val();
    var poqty = $('input[name=poqty]').val();
    var device = $('input[name=device]').val();
    var family = $('#family').val();
    var series = $('#series').val();
    var prodtype = $('#prodtype').val();
    var classification = $('#classification').val();
   
    var mod = $('#mod').val();
    var qty = $('input[name=qty]').val();    
 
    var d =  $('input[name=productiondate]').val().split('/');
    var month = d[0];
    var day = d[1];
    var year = d[2];
    var productiondate = year+'-'+month+'-'+day;
  
    if(hdstatus == "EDIT"){
        var d =  $('input[name=productiondate]').val().split('-');
        var month = d[1];
        var day = d[2];
        var year = d[0];
        var productiondate = year+'-'+month+'-'+day;    
    }
   
    var yieldingstation =  $('select[name=yieldingstation]').val();
    var accumulatedoutput =  $('input[name=accumulatedoutput]').val();
    var toutput =  $('input[name=toutput]').val();
    var treject = $('input[name=treject]').val();
    var tmng =  $('input[name=tmng]').val();
    var tpng =  $('input[name=tpng]').val();
    var ywomng =  $('input[name=ywomng]').val();
    var twoyield =  $('input[name=twoyield]').val();
    
    var token = "{{ Session::token() }}";

    if(pono == ""){     
        $('#er1').html("PO number field is empty"); 
        $('#er1').css('color', 'red');       
        return false;  
    } 
    if(poqty == ""){     
        $('#error1').html("Please click the load button"); 
        $('#error1').css('color', 'red');       
        return false;  
    } 
     
    if (family == ""){
        $('#er2').html("Family field is empty"); 
        $('#er2').css('color', 'red');        
        return false;
    }
    if (series == ""){
        $('#er3').html("Series field is empty"); 
        $('#er3').css('color', 'red');
        return false;
    }
    if (prodtype == ""){
        $('#erprodtype').html("Series field is empty"); 
        $('#erprodtype').css('color', 'red');
        return false;
    }
    var traymod = [];
    $('#mod option:selected').each(function () {
        traymod.push($(this).val());
    });
       
    var myData ={
                      _token: token
                ,yieldingno : yieldingno
                      ,pono : pono
                     ,poqty : poqty
                    ,device : device
                    ,family : family
                    ,series : series
                  ,prodtype : prodtype
            ,classification : classification
                       ,mod : traymod
                       ,qty : qty
            ,productiondate : productiondate
           ,yieldingstation : yieldingstation
         ,accumulatedoutput : accumulatedoutput
                   ,toutput : toutput
                   ,treject : treject
                      ,tmng : tmng
                      ,tpng : tpng
                    ,ywomng : ywomng
                  ,twoyield : twoyield
                    ,status : hdstatus
                  ,newaccumulatedoutput:$('input[name^="pyaaccumulatedoutput"]').map(function(){return $(this).val();}).get()
                  ,newproductiondate:$('input[name^="pyaproductiondate"]').map(function(){return $(this).val();}).get()
                  ,newyieldingstation:$('input[name^="pyayieldingstation"]').map(function(){return $(this).val();}).get()
                  ,newclassification:$('input[name^="cmqclassification"]').map(function(){return $(this).val();}).get()
                  ,newmod:$('input[name^="cmqmod"]').map(function(){return $(this).val();}).get()
                  ,newqty:$('input[name^="cmqqty"]').map(function(){return $(this).val();}).get()
            };
    
    $.post("{{ url('/add-yieldperformance2') }}",
    { 
        _token: $('meta[name=csrf-token]').attr('content')
        , data: myData
    }).done(function(data, textStatus, jqXHR){
        console.log(data);
        if(data > 0){
            $('#searchpoModal').modal('show');
            $('.searchpo-title').html('Warning Message!');
            $('#po-message').html('Record Exist.');
            return false;
        }
    
        searchpo();
        
        $('#yieldingstation').select2('val',"");
        $('input[name=accumulatedoutput]').val("");
        $('#classification').select2('val',"");
        $('#mod').val("");
        $('#qty').val("");
        $('#btnloadpya').attr("disabled",false);
        $('#delete-taskCMQ').removeClass("disabled");
        $('#delete-taskPYA').removeClass("disabled");
        $('#btnsave').addClass("disabled");
        $('#btnsave').removeClass("disabled");
        $('#searchpoModal').modal('show');
        $('.searchpo-title').html('Success Message!');
        $('#po-message').html('Record successfully saved.');
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown+'|'+textStatus); 
    });  

}

// if Add button is clicked(Production Date, Yielding Station and AccumulatedOutput)-----------
var clickpya = 0;
function addpya(){
     $('#delete-taskPYA').addClass("disabled");
    $('#delete-taskCMQ').addClass("disabled");
    var poqty = $('#poqty').val();
    var pono =  $('input[name=pono]').val();
    var productiondate =  $('input[name=productiondate]').val();
    var yieldingstation =  $('#yieldingstation').val();
    var accumulatedoutput =  $('input[name=accumulatedoutput]').val();
    var toutput =  $('input[name=toutput]').val();
    var countpya = $('#countpya').val();
    $('#er8').html("");
    if(pono == ""){     
        $('#er1').html("PO number field is empty"); 
        $('#er1').css('color', 'red');       
          return false;  
    }  
    if(poqty == ""){     
        $('#error1').html("Please click the load button"); 
        $('#error1').css('color', 'red');       
        return false;  
    } 
    if (yieldingstation == ""){
        $('#er6').html("Yielding Station field is empty"); 
        $('#er6').css('color', 'red');
        return false;
    }
    if (accumulatedoutput == ""){
        $('#er7').html("Accumulated Output field is empty"); 
        $('#er7').css('color', 'red');
        return false;
    }
    if($('#yieldingstation').val() && $('input[name=accumulatedoutput]').val()){
        $('#btnloadpya').attr("disabled",true);
    }
  

    if($('#toutput').val() == ''){
        var toutput =$('#toutput').val();
        var accumulatedoutput = $('#accumulatedoutput').val();
        $('#toutput').val(toutput + parseInt(accumulatedoutput));    
    }else{
        var toutput =$('#toutput').val();
        var accumulatedoutput = $('#accumulatedoutput').val();
        $('#toutput').val(parseInt(toutput) + parseInt(accumulatedoutput));    
    }

    toutput = $('#toutput').val();
    clickpya++;
    $('#tbody1').html("");
    var tblrow = '<tr id="pya_row_'+clickpya+'" class="pyarow">'+                   
            '<td style="width: 3%">'+
                '<span>'+
                    '<input type="checkbox" class="form-control input-sm checkboxesPYA" value="'+clickpya+'" name="checkitemPYA" id="checkitemPYA">'+
                    '</input>'+
                '</span>'+   
            '</td>'+ 
            '<td>'+productiondate+'<input type="hidden" value="'+productiondate+'" class="form-control input-sm" id="pyaproductiondate'+clickpya+'" name="pyaproductiondate[]"/></td>'+
            '<td>'+yieldingstation+'<input type="hidden" value="'+yieldingstation+'" class="form-control input-sm" id="pyayieldingstation'+clickpya+'" name="pyayieldingstation[]"/></td>'+
            '<td>'+toutput+'<input type="hidden" value="'+toutput+'" class="form-control input-sm" id="pyaaccumulatedoutput'+clickpya+'" name="pyaaccumulatedoutput[]"/></td>'+
        '</tr>';
    $('#tbody1').append(tblrow);     
}

// if Add button is clicked(Classification, Mode of Defects and Quantity)-----------
var clickcmq = 0;
function addcmq(){
    var poqty = $('#poqty').val();
    var pono = $('#pono').val();
    var yieldingstation =  $('#yieldingstation').val();
    var accumulatedoutput =  $('input[name=accumulatedoutput]').val();
    var productiondate =  $('input[name=productiondate]').val();
    var classification = $('#classification').val();
    var mod = $('#mod').val();
    var qty = $('input[name=qty]').val();
    var toutput = $('input[name=toutput]').val();
    var countcmq = $('#countcmq').val();


    if(pono == ""){     
        $('#er1').html("PO number field is empty"); 
        $('#er1').css('color', 'red');       
        return false;  
    }
  
    if(poqty == ""){     
        $('#error1').html("Please click the load button"); 
        $('#error1').css('color', 'red');       
        return false;  
    }
    if (classification == ""){
        $('#er4').html("Classification field is empty"); 
        $('#er4').css('color', 'red');
        return false;
    }
    if (mod == ""){
        $('#er5').html("Please Select Mode of Defect field is empty"); 
        $('#er5').css('color', 'red');
        return false;
    }
    if (qty == ""){
        $('#er10').html("Quantity field is empty"); 
        $('#er10').css('color', 'red');
        return false;
    }
    if (toutput == ""){
        $('#er8').html("Please click the Accumulated Output Button"); 
        $('#er8').css('color', 'red');
        return false;
    }

    pyafieldcomputation();
    
    var rowcount = $('#counter').val();
    var tblrow = '';
    if(rowcount == 0){
        clickcmq++;
        $('#tbody2').html("");
        tblrow = '<tr id="cmq_row_'+clickcmq+'" class="cmqrow">'+                   
            '<td style="width: 3%">'+
                '<span>'+
                    '<input type="checkbox" class="form-control input-sm checkboxesCMQ" value="'+clickcmq+'" name="checkitemCMQ" id="checkitemCMQ">'+
                    '</input>'+
                '</span>'+   
            '</td>'+ 
            '<td>'+classification+'<input type="hidden"value="'+classification+'" class="form-control input-sm" id="cmqclassification'+clickcmq+'" name="cmqclassification[]"/></td>'+
            '<td>'+mod+'<input type="hidden" value="' +mod+ '" class="form-control input-sm" id="cmqmod'+clickcmq+'" name="cmqmod[]"/></td>'+
            '<td>'+qty+'<input type="hidden" value="' +qty+'" class="form-control input-sm" id="cmqqty'+clickcmq+'" name="cmqqty[]"/></td>'+
        '</tr>';
        $('#counter').val(parseInt(rowcount) + 1);
    } else {
        clickcmq++;
        tblrow = '<tr id="cmq_row_'+clickcmq+'" class="cmqrow">'+                   
            '<td style="width: 3%">'+
                '<span>'+
                    '<input type="checkbox" class="form-control input-sm checkboxesCMQ" value="'+clickcmq+'" name="checkitemCMQ" id="checkitemCMQ">'+
                    '</input>'+
                '</span>'+   
            '</td>'+ 
            '<td>'+classification+'<input type="hidden"value="'+classification+'" class="form-control input-sm" id="cmqclassification'+clickcmq+'" name="cmqclassification[]"/></td>'+
            '<td>'+mod+'<input type="hidden" value="' +mod+ '" class="form-control input-sm" id="cmqmod'+clickcmq+'" name="cmqmod[]"/></td>'+
            '<td>'+qty+'<input type="hidden" value="' +qty+'" class="form-control input-sm" id="cmqqty'+clickcmq+'" name="cmqqty[]"/></td>'+
        '</tr>';
    }
    $('#tbody2').append(tblrow);    
    var myData ={ 
                       'pono' : pono  
             ,'productiondate': productiondate
            ,'classification' : classification
                       ,'mod' : mod
                       ,'qty' : qty
               };

    
}

//Searching PO Number -------------------------------------------------- 
function searchpo(){
    var pono = $('#pono').val();
    $('#counter').val(0);
    var yieldingstation = $('#yieldingstation').val();
    $('input[name=productiondate]').attr('disabled',false);
    $('#btnremove_detail').removeClass("disabled");
    var myData = {'pono':pono,'yieldingstation':yieldingstation};
  
    if(pono == ""){
        $('#searchpoModal').modal('show');
        $('.searchpo-title').html('Warning Message!');
        $('#po-message').html('PO number field is empty.');
        $('#pono').val("");
        $('#poqty').val("");
        $('#device').val("");
        $('#family').select2('val',"");
        $('#series').select2('val',"");
        $('#prodtype').select2('val',"");
        $('input[name=yieldingno]').attr('disabled',true);
        $('input[name=poqty]').attr('disabled',true);
        $('input[name=device]').attr('disabled',true);
        $('#family').attr('disabled',true);
        $('#series').attr('disabled',true);
        $('#prodtype').attr('disabled',true);
        $('#classification').attr('disabled',true);
        $('#mod').attr('disabled',true);
        $('input[name=qty]').attr('disabled',true);
        $('#yieldingstation').attr('disabled',true);
        $('input[name=accumulatedoutput]').attr('disabled',true);
        $('input[name=toutput]').attr('disabled',true);
        $('input[name=treject]').attr('disabled',true);
    } else {
        $.post("{{ url('/search-pono2') }}",
        { 
            _token: $('meta[name=csrf-token]').attr('content')
            , data: myData
        }).done(function(data, textStatus, jqXHR){
            if(data == ""){
                $('#searchpoModal').modal('show');
                $('.searchpo-title').html('Warning Message!');
                $('#po-message').html('PO number not Match');
                $('input[name=yieldingno]').attr('disabled',true);
                $('input[name=poqty]').attr('disabled',true);
                $('input[name=device]').attr('disabled',true);
                $('#family').attr('disabled',true);
                $('#series').attr('disabled',true);
                $('#prodtype').attr('disabled',true);
                $('#classification').attr('disabled',true);
                $('#mod').attr('disabled',true);
                $('input[name=qty]').attr('disabled',true);
                $('#yieldingstation').attr('disabled',true);
                $('input[name=accumulatedoutput]').attr('disabled',true);
                $('input[name=toutput]').attr('disabled',true);
                $('input[name=treject]').attr('disabled',true);
            } 
            $('#accumulatedoutput').attr('disabled',true);
            $('input[name=yieldingno]').val();
            $('input[name=pono]').val(data[0]['pono']);
            $('input[name=poqty]').val(data[0]['poqty']);
            $('input[name=device]').val(data[0]['devicename']);
            $('#family').select2('val',data[0]['family']);
            $('#series').select2('val',data[0]['series']);
            $('#prodtype').select2('val',data[0]['ptype']);
            $('#tbody1').html("");
            $('#tbody2').html("");

            $('#mod').select2('val',"");
            var prodtype = $('select[name=prodtype]').val();
            var classification = $('select[name=classification]').val();
            $('#mod').html("");

            $.post("{{ url('/get_mod') }}",
            {
                _token:$('meta[name=csrf-token]').attr('content'),
                prodtype:prodtype 
            }).done(function(data, textStatus, jqXHR){
                console.log(data);
                $.each(data,function(i,val){
                    var sup = '';
                    switch(prodtype) {
                        case "Test Socket":
                            var sup = '<option value="'+val.mod+'">'+val.mod+'</option>';
                            break;
                        case "Burn In":
                            var sup = '<option value="'+val.mod+'">'+val.mod+'</option>';
                            break;
                        default:
                            var sup = '<option value="'+val.mod+'">'+val.mod+'</option>';
                            break;
                    } 
                    var option = sup;
                    $('#mod').append(option);
                });
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });

            //getting the value of Yield w/o MNG, DPPM and Total Yield-----------------
            $.ajax({
                url: "{{ url('/getautovalue') }}",
                method: 'get',
                data:{ 
                    pono : pono
                },      
            }).done(function(data, textStatus, jqXHR){
                $('input[name=treject]').val(data[0]['treject']);
                $('input[name=toutput]').val(data[0]['toutput']);
                var treject = parseInt(data[0]['treject']);
                var toutput = parseInt(data[0]['toutput']);
                $.ajax({
                    url: "{{ url('/getpng') }}",
                    method: 'get',
                    data:{ 
                        pono : pono
                    },      
                }).done(function(data, textStatus, jqXHR){
                    //dppm-------------------------------
                    $('#tpng').val(data[0]['tpng']);
                    var tpng = parseInt(data[0]['tpng']);
                    var sum = toutput + tpng;
                    var temp = tpng/sum;
                    $('#dppm').val((temp * 1000000).toFixed(2));  

                    if($('#tpng').val() == ''){
                        $('#dppm').val(0);    
                    }
                }).fail(function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown+'|'+textStatus);
                });
                $.ajax({
                    url: "{{ url('/getmng') }}",
                    method: 'get',
                    data:{ 
                        pono : pono
                    },      
                }).done(function(data, textStatus, jqXHR){
                    //yield without mng----------------------------------------
                    $('#tmng').val(data[0]['tmng']);
                    var toaddmng = toutput + parseInt(data[0]['tmng']);
                    var toaddtr = toutput + treject;
                    var quotient = toaddmng/toaddtr;
                    var ywomng = (quotient * 100).toFixed(2);
                    $('#ywomng').val(ywomng);

                    if($('#tmng').val() == ''){
                        var toaddmng = toutput + 0;
                        var toaddtr = toutput + treject;
                        var quotient = toaddmng/toaddtr;
                        var ywomng = (quotient * 100).toFixed(2);
                        $('#ywomng').val(ywomng);   
                    }

                }).fail(function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown+'|'+textStatus);
                });
               
            
                //total Yield-----------------------------------------------------------
                var toandtr = parseInt(data[0]['toutput']) + parseInt(data[0]['treject']);
                var todivtoandtr = parseInt(data[0]['toutput']) / toandtr;
                var twoyield = (todivtoandtr * 100).toFixed(2);
                $('#twoyield').val(twoyield);

            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });

            //Displaying the table of (Production Date, Yielding Statioin and Accumulated Output) when searching PO Number--------------
            $('#tbody1').html("");
            $.ajax({
                url: "{{ url('/searchdisplaypya') }}",
                method: 'get',
                data:  { 
                    pono : pono,
                    yieldingstation:yieldingstation       
                },      
            }).done(function(data, textStatus, jqXHR){
                $.each(data, function(i,val) {
                    var tblrow = '<tr id="pya_row_'+val.id+'" class="pyarow">'+                   
                                '<td style="width: 3%">'+
                                    '<span>'+
                                        '<input type="checkbox" class="form-control input-sm checkboxesPYA" value="'+val.id+'" name="checkitemPYA" id="checkitemPYA">'+
                                        '</input>'+
                                    '</span>'+   
                                '</td>'+ 
                                '<td>'+val.productiondate+'<input type="hidden" value="'+val.productiondate+'|'+val.yieldingno+'" class="form-control input-sm" id="pyaproductiondate'+val.id+'" name="pyaproductiondate[]"/></td>'+
                                '<td>'+val.yieldingstation+'<input type="hidden" value="'+val.yieldingstation+'" class="form-control input-sm" id="pyayieldingstation'+val.id+'" name="pyayieldingstation[]"/></td>'+
                                '<td>'+val.accumulatedoutput+'<input type="hidden" value="'+val.accumulatedoutput+'" class="form-control input-sm" id="pyaaccumulatedoutput'+val.id+'" name="pyaaccumulatedoutput[]"/></td>'+
                            '</tr>';
                    $('#tbody1').append(tblrow);   
                });  
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });

            //Displaying the table of(Classification, MOD and Qty) when searching PO Number-----------------
            $('#tbody2').html("");
            $.ajax({
                url: "{{ url('/searchdisplaycmq') }}",
                method: 'get',
                data:  { 
                    pono : pono, 
                    yieldingstation:yieldingstation      
                },      
            }).done(function(data, textStatus, jqXHR){
                $.each(data, function(i,val) {
                    var tblrow = '<tr id="cmq_row_'+val.id+'" class="cmqrow">'+                   
                            '<td style="width: 3%">'+
                                '<span>'+
                                    '<input type="checkbox" class="form-control input-sm checkboxesCMQ" value="'+val.id+'" name="checkitemCMQ" id="checkitemCMQ">'+
                                    '</input>'+
                                '</span>'+   
                            '</td>'+ 
                            '<td>'+val.classification+'<input type="hidden"value="'+val.classification+'" class="form-control input-sm" id="cmqclassification'+val.id+'" name="cmqclassification[]"/><input type="hidden"value="'+val.yieldingno+'" class="form-control input-sm" id="cmqyieldingno'+val.id+'" name="cmqyieldingno[]"/></td>'+
                            '<td>'+val.mod+'<input type="hidden" value="' +val.mod+ '" class="form-control input-sm" id="cmqmod'+val.id+'" name="cmqmod[]"/></td>'+
                            '<td>'+val.qty+'<input type="hidden" value="' +val.qty+'" class="form-control input-sm" id="cmqqty'+val.id+'" name="cmqqty[]"/></td>'+
                        '</tr>';
                    $('#tbody2').append(tblrow);
                });
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });

            //Displaying the Details Table when Searching records---------------------------------
            $('#tbldetails').html("");
            $.ajax({
                url: "{{ url('/searchdisplaydetails') }}",
                method: 'get',
                data:  { 
                    pono : pono,
                    yieldingstation:yieldingstation       
                },      
            }).done(function(data, textStatus, jqXHR){
                $.each(data, function(i,val) {
                console.log(val);
                var tblrow = '<tr>'+
                                '<td style="width: 2%">'+
                                '<input type="checkbox" class="form-control input-sm checkboxes" value="'+val.id+'" name="checkitem" id="checkitem"></input>'+   
                                '</td> '+                       
                              /*  '<td style="width: 3%">'+
                                    '<button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="'+val.id+ '|' +val.productiondate+ '|' +val.yieldingstation+ '|' +val.toutput+ '|' +val.classification+ '|' +val.mod+ '|' +val.qty+ '|' +val.pono+ '|' +val.poqty+ '|' +val.device+ '|' +val.family+ '|' +val.series+ '|' +val.accumulatedoutput+ '|' +val.treject+ '|' +val.twoyield+ '|' +val.prodtype+'">'+
                                        '<i class="fa fa-edit"></i> '+
                                    '</button>'+*/
                                '</td>'+
                                '<td>'+val.productiondate+'</td>'+
                                '<td>'+val.yieldingstation+'</td>'+
                                '<td>'+val.accumulatedoutput+'</td>'+
                                '<td>'+val.classification+'</td>'+
                                '<td>'+val.mod+'</td>'+
                                '<td>'+val.qty+'</td>'+
                                '<td>'+val.pono+'</td>'+
                                '<td>'+val.poqty+'</td>'+
                                '<td>'+val.device+'</td>'+
                                '<td>'+val.family+'</td>'+
                                '<td>'+val.series+'</td>'+
                            '</tr>';
                    $('#tbldetails').append(tblrow);
                });
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });

            $.ajax({
                url: "{{ url('/searchdisplaysummary') }}",
                method: 'get',
                data:  { 
                    pono : pono       
                },      
            }).done(function(data, textStatus, jqXHR){
                $.each(data, function(i,val) {
                console.log(val);
                var tblrow = '<tr>'+
                                '<td>'+val.pono+'</td>'+
                                '<td>'+val.poqty+'</td>'+
                                '<td>'+val.device+'</td>'+
                                '<td>'+val.series+'</td>'+
                                '<td>'+val.family+'</td>'+
                                '<td>'+val.toutput+'</td>'+
                                '<td>'+val.treject+'</td>'+
                                '<td>'+val.twoyield+'</td>'+
                            '</tr>';
                    $('#tblsummary').append(tblrow);
                });

                $('.edit-task').click(function(){
                    var getvalue = $(this).val().split('|');
                    var yieldingno = getvalue[0];
                    var  productiondate = getvalue[1];
                    var  yieldingstation = getvalue[2];
                    var  toutput = getvalue[3];
                    var  classification = getvalue[4];
                    var  mod = getvalue[5];
                    var  qty = getvalue[6];
                    var  pono = getvalue[7];
                    var  poqty = getvalue[8];
                    var  device = getvalue[9];
                    var  family = getvalue[10];
                    var  series = getvalue[11];
                    var  Aoutput = getvalue[12];
                    var  treject = getvalue[13];
                    var  ywomng = getvalue[14];
                    var  prodtype = getvalue[15];
                    $('#hdstatus').val("EDIT");
                 
                    $('#yieldingno').val(yieldingno);
                    $('#productiondate').val(productiondate.substring(0,10));
                    $('#yieldingstation').select2('val',yieldingstation);
                    $('#accumulatedoutput').val(Aoutput);
                    $('#classification').select2('val',classification);
                    $('#mod').select2('val',mod);
                    $('#qty').val(qty);
                    $('#pono').val(pono);
                    $('#poqty').val(poqty);
                    $('#device').val(device);
                    $('#family').select2('val',family);
                    $('#series').select2('val',series);
                    $('#prodtype').select2('val',prodtype);
                    $('#toutput').val(toutput);
                    $('#treject').val(treject);  
                    $('#ywomng').val(ywomng);

                    $('input[name=yieldingno]').attr('disabled',true);
                    $('input[name=pono]').attr('disabled',true);
                    $('input[name=poqty]').attr('disabled',true);
                    $('input[name=device]').attr('disabled',true);
                    $('#family').attr('disabled',false);
                    $('#series').attr('disabled',false);
                    $('#prodtype').attr('disabled',false);
                    $('#classification').attr('disabled',false);
                    $('#mod').attr('disabled',false);
                    $('input[name=qty]').attr('disabled',false);
                    $('#yieldingstation').attr('disabled',false);
                    $('input[name=accumulatedoutput]').attr('disabled',false);
                    $('input[name=toutput]').attr('disabled',true);
                    $('input[name=treject]').attr('disabled',true);
                });
            }).fail(function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown+'|'+textStatus);
            });    
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown+'|'+textStatus);
        }); 
    }

}

function deleteAllcheckeditems(){
    var tray = [];
    $(".checkboxes:checked").each(function () {
        tray.push($(this).val());
    });
    var traycount =tray.length;

    $.ajax({
        url: "{{ url('/deleteAll-pono2') }}",
        method: 'get',
        data:  { 
            tray : tray, 
            traycount : traycount
        },     
    }).done( function(data, textStatus, jqXHR) {
         /* console.log(data);*/
        window.location.href = "{{ url('/yieldperformance2') }}";   
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(errorThrown+'|'+textStatus);
    });
}

function searchrecord(){
    var search = $('#yieldingno').val();
    var myData = {'search':search};
    
    $.post("{{ url('/search-yieldperformance2') }}",
    { 
        _token: $('meta[name=csrf-token]').attr('content')
        , data: myData
    }).done(function(data, textStatus, jqXHR){
        console.log(data);
        $('#pono').val(data[0]['pono']);
        $('#poqty').val(data[0]['poqty']);
        $('#device').val(data[0]['device']);
        $('#family').select2('val',data[0]['family']);
        $('#series').select2('val',data[0]['series']);
        $('#classification').select2('val',data[0]['classification']);
        $('#mod').select2('val',data[0]['mod']);
        $('#qty').val(data[0]['qty']);
        $('#productiondate').val(data[0]['productiondate'].substring(0,10));
        $('#yieldingstation').select2('val',data[0]['yieldingstation']);
        $('#accumulatedoutput').val(data[0]['accumulatedoutput']);
        $('#toutput').val(data[0]['toutput']);
        $('#treject').val(data[0]['treject']);
        $('#tmng').val(data[0]['tmng']);
        $('#tpng').val(data[0]['tpng']);
        $('#ywomng').val(data[0]['ywomng']);
        $('#twoyield').val(data[0]['twoyield']);
    }).fail(function(jqXHR, textStatus, errorThrown){
           console.log(errorThrown+'|'+textStatus);
    });  
  
}

function deletepya(){
    var tray = [];
    $(".checkboxesPYA:checked").each(function () {
        tray.push($(this).val());
        var hdaccumulatedoutput = $('#pyaaccumulatedoutput'+tray).val();
      
        var hdqty = $('#cmqqty'+tray).val();
        var hdacc = $('#pyaaccumulatedoutput'+tray).val();
        var temp = $('#pyaproductiondate'+tray).val().split('|');
        var yieldingno = temp[1];
   
        var toutput = $('#toutput').val(); 
        $('#toutput').val(toutput - hdacc);
        $('#pya_row_'+tray).remove();

        var traycount = tray.length;
            $.ajax({
                url:"{{ url('/deletepya') }}",
                method:'get',
                data:  { 
                    tray : tray, 
                    traycount : traycount,
                    yieldingno:yieldingno,
                },
        }).done( function(data, textStatus, jqXHR) {
            console.log(data);
            searchpo();
            var x = $('#hdyieldingno').val() - 1;
            var a = $('#hdyieldingno').val();
            var b = a - 1;
            if(yieldingno == data){
                $('#hdyieldingno').val(b);
                $('#yieldingno').val(b);
            }
            if(b == 1){
                $('#toutput').val("");
                $('#treject').val("");
                $('#tmng').val("");
                $('#tpng').val("");
                $('#ywomng').val("");
                $('#twoyield').val("");
                $('#dppm').val("");
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown+'|'+textStatus);
        });
    });
    

    $('.checkAllitemsPYA:checked').each(function(){
        $("#tblforpya").find("tr:not(:first)").remove();
   });
}

function deletecmq(){
    var tray = [];
    $(".checkboxesCMQ:checked").each(function () {
        tray.push($(this).val());
        var hdqty = $('#cmqqty'+tray).val();
        var hdclassification = $('#cmqclassification'+tray).val();
        var treject = $('#treject').val();
        var tpng = $('#tpng').val();
        var tmng = $('#tmng').val();
        var yieldingno = $('#cmqyieldingno'+tray).val();
        $('#treject').val(treject - hdqty);
        if(hdclassification == "Material NG (MNG)"){
            $('#tmng').val(tmng - hdqty);    
        }
        if(hdclassification == "Production NG (PNG)"){
            $('#tpng').val(tpng - hdqty);   
        }

        var toutput = $('#toutput').val();
        var treject = $('#treject').val();
        var tpng = $('#tpng').val();
        if($('#tpng').val() == ''){
            var toaddtp = parseInt(toutput) + tpng;
            var dev = toutput/toaddtp * 100;
            var final = dev.toFixed(2);
            $('#ywomng').val(final);    
        } else {
            var toaddtp = parseInt(toutput) + parseInt(tpng);
            var dev = toutput/toaddtp * 100;
            var final = dev.toFixed(2);
            $('#ywomng').val(final);    
        }
        if($('#tmng').val() == ''){
            var toaddtr = parseInt(toutput) + treject;
            var temp = toutput/toaddtr;
            var final = temp.toFixed(2);
            $('#twoyield').val(final);    
        } else {
            var toaddtr = parseInt(toutput) + parseInt(treject);
            var temp = toutput/toaddtr;
            var final = temp.toFixed(2);
            $('#twoyield').val(final);    
        }

        var tempdppm = $('#ywomng').val() * 1000000;
        var finaldppm = tempdppm.toFixed(2);
        $('#dppm').val(finaldppm);
        $('#cmq_row_'+tray).remove();

        var traycount = tray.length;
            $.ajax({
                url:"{{ url('/deletecmq') }}",
                method:'get',
                data:  { 
                    tray : tray, 
                    traycount : traycount,
                    yieldingno : yieldingno
                },
        }).done( function(data, textStatus, jqXHR) {
            console.log(data);
            searchpo();
            if(data == 1){
                var x = $('#hdyieldingno').val() - 1;
                var a = $('#hdyieldingno').val();
                var b = a - 1;
                $('#hdyieldingno').val(b);
                $('#yieldingno').val(b);
            }

            if(b == 1){
                $('#toutput').val("");
                $('#treject').val("");
                $('#tmng').val("");
                $('#tpng').val("");
                $('#ywomng').val("");
                $('#twoyield').val("");
                $('#dppm').val("");
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown+'|'+textStatus);
        });
    });

    $('.checkAllitemsCMQ:checked').each(function(){
    
        $('#treject').val("");
        $('#tmng').val("");
        $('#tpng').val("");
        $('#ywomng').val("");
        $('#twoyield').val("");
        $('#dppm').val("");

        $("#tblforcmq").find("tr:not(:first)").remove();
   });
}

function back(){
    window.location.href="{{ url('/yieldperformance') }}";    
}

function pyafieldcomputation(){
    if($('#classification').val() == "Production NG (PNG)"){
        if($('#tpng').val() == ''){
            var qty = $('#qty').val();
            var tpng = $('#tpng').val();
            var treject =$('#treject').val(); 
            var sample=parseInt(treject);
            var sum = sample + parseInt(qty)
            $('#tpng').val(tpng+parseInt(qty));
            $('#treject').val();
        } else {
            var qty = $('#qty').val();
            var tpng = $('#tpng').val();
            var treject =$('#treject').val();
            var value=parseInt(treject);
            var sum = value + parseInt(qty);
            $('#tpng').val(parseInt(tpng)+parseInt(qty));
            $('#treject').val();
        }  
    }

    //computation for Total MNG and Total Reject
    if($('#classification').val() == "Material NG (MNG)"){
        if($('#tmng').val() == ''){
            var qty = $('#qty').val();
            var tmng = $('#tmng').val();
            var treject =$('#treject').val(); 
            $('#tmng').val(tmng+parseInt(qty));
            if($('#tpng').val()){
                var x = parseInt(treject)+parseInt(qty)
            } else {
                var x =treject+parseInt(qty);
            }
            $('#treject').val(x);
        } else {
            var qty = $('#qty').val();
            var tmng = $('#tmng').val();
            var treject =$('#treject').val();
            $('#tmng').val(parseInt(tmng)+parseInt(qty));
            $('#treject').val(parseInt(treject)+parseInt(qty));    
        }

    } else {
        if($('#treject').val() == ''){
            var treject =$('#treject').val();
            var qty = $('#qty').val();
            $('#treject').val(treject + parseInt(qty));      
        } else{
            var treject =$('#treject').val();
            var qty = $('#qty').val();
            $('#treject').val(parseInt(treject) + parseInt(qty));       
        }
    }

    //computation for DPPM,Yield without MNG and Total % Yield----------------
    var toutput = $('#toutput').val();
    var treject = $('#treject').val();
    var tmng = $('#tmng').val();

    if($('#tmng').val() == ""){
        var toaddtp = parseInt(toutput) + tmng;
        var toaddtr = parseInt(toutput) + treject;
        var dev = toaddtp/toaddtr * 100;
        var final = dev.toFixed(2);
        $('#ywomng').val(final);
    } else {

        var toaddtp = parseInt(toutput) + parseInt(tmng);
        var toaddtr = parseInt(toutput) + parseInt(treject);
        var dev = toaddtp/toaddtr * 100;
        var final = dev.toFixed(2);
        $('#ywomng').val(final);
    }

    if($('#toutput').val() == "0"){
        $('#ywomng').val("0");
    } 

    if($('#tmng').val() == ''){
        var toaddtr = parseInt(toutput) + treject;
        var temp = toutput/toaddtr * 100;
        var final = temp.toFixed(2);
        $('#twoyield').val(final);    
    } else {
        var toaddtr = parseInt(toutput) + parseInt(treject);
        var temp = toutput/toaddtr * 100;
        var final = temp.toFixed(2);
        $('#twoyield').val(final);    
    }

    if($('#tpng').val() == ''){    
        var toutput = $('#toutput').val();
        var treject = $('#treject').val();
        var tpng = $('#tpng').val();
        var toutputandtr = parseInt(toutput) + parseInt(treject);
        var tempdppm = tpng/toutputandtr; 
        $('#dppm').val((tempdppm * 1000000).toFixed(2));    
    }else{
        var toutput = $('#toutput').val();
        var treject = $('#treject').val();
        var tpng = $('#tpng').val();
        var toutputandtr = parseInt(toutput) + parseInt(treject);
        var tempdppm = tpng/toutputandtr; 
        $('#dppm').val((tempdppm * 1000000).toFixed(2));    
    }
}


</script>