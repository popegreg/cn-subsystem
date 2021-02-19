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
                         </div>
                         <div class="portlet-body">

                              <div class="row">
                                   <div class="col-sm-3">
                                        <div class="form-group row" style="margin-top:60px;;margin-left:20px;">
                                             <div class="col-sm-10" >
                                                  <button type="button" style="font-size:10px;" class="btn col-sm-8 btn-success" id="btnaddnew">
                                                       <i class="fa fa-plus"></i> New Transaction
                                                  </button>
                                             </div>
                                        </div>
                                        <div class="form-group row" style="margin-left:20px;">
                                             <div class="col-sm-10" >
                                                  <button type="button" style="font-size:10px;" class="btn col-sm-8 blue-soft" id="btn_poreg" >
                                                  <i class="fa fa-save"></i>PO Registration
                                                  </button>
                                             </div>
                                        </div>
                                        <div class="form-group row" style="margin-left:20px;">
                                             <div class="col-sm-10">
                                                  <button type="button" style="font-size:10px;" class="btn col-sm-8 blue-soft" id="btn_devicereg" ><i class="fa fa-desktop"></i>Device Registration</button>
                                             </div>
                                        </div>
                                        <div class="form-group row" style="margin-left:20px;">
                                             <div class="col-sm-10">
                                                  <button type="button" style="font-size:10px;" class="btn col-sm-8 blue-soft" id="btn_seriesreg" ><i class="fa fa-list-alt"></i>Series Registration</button>
                                             </div>
                                        </div>
                                        <div class="form-group row" style="margin-left:20px;">
                                             <div class="col-sm-10">
                                                  <button type="button" style="font-size:10px;" class="btn col-sm-8 blue-soft" id="btn_modreg" ><i class="fa fa-chain-broken"></i>Mode of Defect Registration</button>
                                             </div>
                                        </div>
                                        <div class="form-group row" style="margin-left:20px;">
                                             <div class="col-sm-10">
                                                  <button type="button" style="font-size:10px;" class="btn col-sm-8 blue-soft" id="btn_target" ><i class="fa fa-line-chart"></i>Target Yield</button>
                                             </div>
                                        </div>
                                        <div class="form-group row" style="margin-left:20px;">
                                             <div class="col-sm-10">
                                                  <button type="button" style="font-size:10px;" onclick="javascript:deleteAllcheckeditems();" class="btn  red col-sm-8 remove-task" id="btnremove_detail">
                                                       <i class="fa fa-trash remove-task"></i> Remove
                                                  </button>
                                             </div>
                                        </div>
                                   </div>

                                   <div class="col-xs-9 col-md-pull-1"">
                                        <table class="table table-striped table-bordered table-hover" id="sample_3" style="font-size:13px">
                                             <thead >
                                                  <tr>
                                                       <td class="table-checkbox" style="width: 5%">
                                                            <input type="checkbox" class="group-checkable checkAllitems" name="checkAllitem" data-set="#sample_3 .checkboxes"/>
                                                       </td>
                                                       <td>
                                                       </td>
                                                       <td>PO Number</td>
                                                       <td>PO Qty</td>
                                                       <td>Device Name</td>
                                                       <td>Series</td>
                                                       <td>Family</td>
                                                       <td>Total Output</td>
                                                       <td>Total Reject</td>
                                                       <td>Total Yield</td>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  @foreach($records as $rec)
                                                  <?php 
                                                  $x = $rec->accumulatedoutput + $rec->qty;
                                                  $y = $rec->accumulatedoutput / $x;
                                                  $twoyield = $y * 100.;
                                                  ?>
                                                  <tr>
                                                       <td style="width: 2%">
                                                            <input type="checkbox" class="form-control input-sm checkboxes" value="{{$rec->id}}" name="checkitem" id="checkitem"></input> 
                                                       </td>                        
                                                       <td style="width: 5%">           
                                                          <button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="{{$rec->id . '|' . $rec->pono . '|' .$rec->poqty. '|' .$rec->device. '|' . $rec->series . '|' .$rec->family. '|' .$rec->toutput. '|' . $rec->treject . '|' .$rec->twoyield}}" id="editTask{{$rec->id}}">
                                                                 <i class="fa fa-edit"></i> 
                                                            </button>
                                                       </td>
                                                       <td>{{$rec->pono}}</td>
                                                       <td>{{$rec->poqty}}</td>
                                                       <td>{{$rec->device}}</td>
                                                       <td>{{$rec->series}}</td>
                                                       <td>{{$rec->family}}</td>
                                                       <td>{{$rec->accumulatedoutput}}</td>
                                                       <td>{{$rec->qty}}</td>
                                                       <td>{{round($twoyield,2)}}</td>
                                                  </tr>
                                                  @endforeach
                                             </tbody>
                                        </table>
                                        <br>
                                        <div class="form-group pull-right">
                                             <label class="control-label col-sm-2">DPPM</label>
                                             <div class="col-sm-10">
                                                  <input type="text" class="form-control input-sm" id="dppm" name="dppm">
                                             </div> 
                                        </div>
                                        <div class="col-sm-2">
                                             <input type="text" class="form-control input-sm" id="datefroms" name="datefroms" > Date From
                                        </div>
                                         <div class="col-sm-2">
                                             <input type="text" class="form-control input-sm" id="datetos" name="datetos" > Date To
                                        </div>   
                                   </div>
                              </div>
                              <br>
                              <br>
                              <div class="row col-sm-offset-1 col-sm-10">
                                  <div id="chartContainer" style="height: 300px;"></div>
                              </div>
                              <div class="row">
                                   <div class="col-sm-12 text-center" style="margin-top:40px;">
                                        <a href="{{ url('/export-to-excel') }}" type="button" style="font-size:12px;" class="btn green-jungle input-sm" id="btnXexcel">
                                        <i class="fa fa-file-excel-o"></i> Export Summary to Excel
                                        </a>
                                        <a href="{{ url('/export-to-pdf') }}" type="button" style="font-size:12px;" class="btn yellow-gold input-sm" id="btnXpdf">
                                             <i class="fa fa-file-pdf-o"></i> Export Summary to Pdf
                                        </a>
                                        <button  type="button" style="font-size:12px;" class="btn blue-soft input-sm" id="btnxport">
                                             <i class="fa fa-share"></i>Export Files
                                        </button>
                                        <button  type="button" style="font-size:12px;" class="btn blue-soft input-sm" onclick="javascript:loadchart();" id="btnloadchart">
                                             <i class="fa fa-share"></i>Load Chart
                                        </button>
                                       
                                   </div>
                              </div>              
                         </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
               </div>
          </div>
          <!-- END PAGE CONTENT-->
     </div>


     <!-- Exports Modal -->
     <div id="Export-Modal" class="modal fade" role="dialog">
          <div class="modal-dialog modal-md">
               <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal">&times;</button>
                         <h4 class="mAndr-title">Export</h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              {!! csrf_field() !!}
                              <div class="form-group row">
                                   <div class="col-sm-12 col-sm-offset-3">
                                        <button type="button" class="btn col-sm-6 blue-hoki" id="btnxport-summaryrpt" ><i class="fa fa-list-ul"></i>Summary Report</button>
                                   </div>
                              </div>
                              <div class="form-group row">
                                   <div class="col-sm-12 col-sm-offset-3">
                                        <button type="button" class="btn col-sm-6 grey-cascade" id="btnxport-defectsummaryrpt" ><i class="fa fa-chain-broken"></i>Defect Summary</button>
                                   </div>
                              </div>
                              <div class="form-group row">
                                   <div class="col-sm-12 col-sm-offset-3">
                                        <button type="button" class="btn col-sm-6 red-sunglo" id="btnxport-yieldpsrpt" ><i class="fa fa-list-alt"></i>Yield Performance Summary</button>
                                   </div>
                              </div>
                              <div class="form-group row">
                                   <div class="col-sm-12 col-sm-offset-3">
                                        <button type="button" class="btn col-sm-6 purple-plum" id="btnxport-yieldsfrpt" ><i class="fa fa-align-justify"></i>Yield Summary Family</button>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_cancel"><i class="fa fa-times"></i>Cancel</button>
                    </div>
               </div>
          </div>
     </div>

     <!-- PO Registration -->
     <div id="poreg_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="poreg_modal-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-sm-12">
                                   <form class="form-horizontal">
                                        {!! csrf_field() !!}
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">PO Number</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="pono" name="pono">
                                                  <div id="er_pono"></div>
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Device</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="podevice" name="podevice" maxlength="40" >
                                                  <div id="er_podevice"></div>
                                             </div>
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">PO Quantity</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="poquantity" name="poquantity" maxlength="40" >
                                                  <input type="hidden" class="form-control input-sm" id="poregstatus" name="poregstatus" maxlength="40" >
                                                  <input type="hidden" class="form-control input-sm" id="poregid" name="poregid" maxlength="40">
                                                  <div id="er_poquantity"></div>
                                             </div>
                                        </div>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button"  onclick="javascript:poregistration();"  class="btn btn-success">Save</button> 
                                                  <button type="button" id='btnporegclear' class="btn btn-danger">Clear</button>
                                             </div>  
                                        </div>
                                   </form>   
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-sm-12">
                                   <div class="scroller" id="tableforporeg" style="height: 300px">
                                        <table id="poreg-table" class="table table-striped table-bordered table-hover"style="font-size:13px">
                                             <thead id="thead1">
                                                  <tr>
                                                      <td class="table-checkbox" style="width: 5%">
                                                            <input type="checkbox" class="group-checkable checkAllitemspo" name="checkAllitempo"/>      
                                                       </td>
                                                       <td></td>
                                                       <td>Purchase Order</td>
                                                       <td>Device</td>
                                                       <td>PO Quantity</td>    
                                                  </tr>
                                             </thead>
                                             <tbody id="tblforporeg">
                                             @foreach($tableporeg as $rec)
                                                  <tr>
                                                       <td style="width: 2%" >
                                                            <input type="checkbox" class="form-control input-sm checkboxespo" value="{{$rec->id}}" name="checkitempo" id="checkitempo"></input> 
                                                       </td> 
                                                       <td style="width: 2%">
                                                            <button type="button" name="edit-poreg" class="btn btn-sm btn-primary edit-poreg" value="{{$rec->id}}">
                                                                 <i class="fa fa-edit"></i> 
                                                            </button> 
                                                       </td>                  
                                                       <td><a id="edit-poreg" name="edit-poreg" value="{{$rec->id}}">{{$rec->pono}}</a>
                                                       </td>
                                                       <td>{{$rec->device}}</td>
                                                       <td>{{$rec->poqty}}</td>
                                                       
                                                  </tr>
                                             @endforeach
                                             </tbody>
                                        </table>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" id='modalsave' onclick="javascript:removeporeg();"  class="btn red">Remove</button>
                         <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_poclose">Close</button>
                    </div>
               </div>    
          </div>
     </div>

     <!-- Device Registration -->
     <div id="devicereg_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="devicereg_modal-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-sm-12">
                                   <form class="form-horizontal">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3">PO Number</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="devicepono" name="devicepono" tabindex="1">
                                                  <div id="er_devicepono"></div>
                                             </div>    
                                        </div>
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3">Device Name</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="devicename" name="devicename" tabindex="2">
                                                  <div id="er_devicename"></div>
                                             </div>    
                                        </div>
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3">Family</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control select2me input-sm" id="devicefamily" name="devicefamily" tabindex="3">
                                                       <option value=""></option>
                                                       @foreach($family as $ys)
                                                            <option value="{{$ys->family}}">{{$ys->family}}</option>
                                                       @endforeach    
                                                 </Select>
                                                  <div id="er_devicefamily"></div>
                                             </div>
                                        </div>
                                       
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3">Series</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control select2me input-sm" id="deviceseries" name="deviceseries" tabindex="4">
                                                       <!-- block of series here -->      
                                                 </Select>
                                                  <div id="er_deviceseries"></div>
                                             </div>
                                        </div> 
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3">Product Type</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control select2me input-sm" id="deviceptype" name="deviceptype" tabindex="5">
                                                     <option value=""></option>
                                                     <option value="Test Socket">Test Socket</option>
                                                     <option value="Burn In">Burn In</option>
                                                  </Select>
                                                  <input type="hidden" class="form-control input-sm" id="devregstatus" name="devregstatus" maxlength="40" >
                                                  <input type="hidden" class="form-control input-sm" id="devregid" name="devregid" maxlength="40">
                                                  <div id="er_deviceptype"></div>
                                             </div>
                                        </div>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button" id='modalsave' onclick="javascript:deviceregistration();"  class="btn btn-success">Save</button>
                                                  <button type="button" id='btndeviceregclear' class="btn btn-danger">Clear</button>
                                             </div>    
                                        </div> 
                                   </form>       
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-sm-12">
                                   <div class="scroller" style="height: 300px" id="tablefordevicereg">
                                        <table id="devicereg-table" class="table table-striped table-bordered table-hover"style="font-size:13px">
                                             <thead id="thead1">
                                                  <tr>
                                                       <td class="table-checkbox" style="width: 5%">
                                                            <input type="checkbox" class="group-checkable checkAllitemsdevice" name="checkAllitemdevice"/>
                                                       </td>
                                                       <td></td>
                                                       <td>PO Number</td>
                                                       <td>Device Name</td>
                                                       <td>Family</td>
                                                       <td>Series</td>
                                                       <td>Product Type</td>
                                                      
                                                  </tr>
                                             </thead>
                                             <tbody id="tblfordevreg">
                                             @foreach($tabledevicereg as $rec)
                                                  <tr>
                                                       <td style="width: 2%">
                                                            <input type="checkbox" class="form-control input-sm checkboxesdevice" value="{{$rec->id}}" name="checkitemdevice" id="checkitemdevice"></input> 
                                                       </td>
                                                       <td style="width: 2%">
                                                            <button type="button" name="edit-devicereg" class="btn btn-sm btn-primary edit-devicereg" value="{{$rec->id}}">
                                                                 <i class="fa fa-edit"></i> 
                                                            </button> 
                                                       </td>    
                                                       <td>{{$rec->pono}}</td>                           
                                                       <td>{{$rec->devicename}}</td>
                                                       <td>{{$rec->family}}</td>
                                                       <td>{{$rec->series}}</td>
                                                       <td>{{$rec->ptype}}</td>
                                                  </tr>
                                             @endforeach
                                             </tbody>
                                        </table>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" id='modalsave' onclick="javascript:removedevicereg();"  class="btn red">Remove</button>
                         <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_devclose">Close</button>
                    </div>
               </div>    
          </div>
     </div>
     
     <!-- Series Registration -->
     <div id="seriesreg_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="seriesreg_modal-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-sm-12">
                                   <form class="form-horizontal">
                                   {!! csrf_field() !!}
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Family</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control select2me input-sm" id="seriesfamily" name="seriesfamily">
                                                       <option value=""></option>
                                                       <option value="BGA">BGA</option>
                                                       <option value="BGA-FP">BGA-FP</option>
                                                       <option value="LGA">LGA</option>
                                                       <option value="PGA">PGA</option>
                                                       <option value="PGA-LGA">PGA-LGA</option>
                                                       <option value="Probe Pin">Probe Pin</option>
                                                       <option value="PUS">PUS</option>
                                                       <option value="QFN">QFN</option>
                                                       <option value="QFP1">QFP1</option>
                                                       <option value="QFP2">QFP2</option>
                                                       <option value="Socket No.2">Socket No.2</option>
                                                       <option value="SOJ">SOJ</option>
                                                       <option value="SON">SON</option>
                                                       <option value="TSOP">TSOP</option>
                                                 </Select>
                                                  <div id="er_seriesfamily"></div>
                                             </div>  
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Series</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="seriesname" name="seriesname" maxlength="40" >
                                                  <input type="hidden" class="form-control input-sm" id="seriesregstatus" name="seriesregstatus" maxlength="40" >
                                                  <input type="hidden" class="form-control input-sm" id="seriesregid" name="seriesregid" maxlength="40">
                                                  <div id="er_seriesname"></div>
                                             </div>
                                        </div>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button" id='modalsave' onclick="javascript:seriesregistration();"  class="btn btn-success">Save</button> 
                                                  <button type="button" id='btnseriesregclear' class="btn btn-danger">Clear</button>    
                                             </div>
                                        </div>
                                   <form>
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-sm-12">
                                   <div class="scroller" style="height: 300px" id="tableforseriesreg">
                                        <table id="seriesreg-table" class="table table-striped table-bordered table-hover"style="font-size:13px">
                                             <thead id="thead1">
                                                  <tr>
                                                       <td class="table-checkbox" style="width: 5%">
                                                            <input type="checkbox" class="group-checkable checkAllitemsseries" name="checkAllitemPYA" />
                                                       </td>
                                                       <td></td>
                                                       <td>Family</td>
                                                       <td>Series</td>
                                                  </tr>
                                             </thead> 
                                             <tbody id="tblforseries">
                                             @foreach($tableseriesreg as $rec)
                                                  <tr>
                                                       <td style="width: 2%">
                                                            <input type="checkbox" class="form-control input-sm checkboxesseries" value="{{$rec->id}}" name="checkitemseries" id="checkitemseries"></input> 
                                                       </td>  
                                                       <td style="width: 2%">
                                                            <button type="button" name="edit-seriesreg" class="btn btn-sm btn-primary edit-seriesreg" value="{{$rec->id}}">
                                                                 <i class="fa fa-edit"></i> 
                                                            </button> 
                                                       </td>                             
                                                       <td>{{$rec->family}}</td>
                                                       <td>{{$rec->series}}</td>
                                                  </tr>
                                             @endforeach
                                             </tbody>   
                                        </table>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" id='modalsave' onclick="javascript:removeseriesreg();"  class="btn red">Remove</button>
                         <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_seriesclose">Close</button>
                    </div>
               </div>    
          </div>
     </div>

     <!-- Mode of Defect Registration -->
     <div id="modreg_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="modreg_modal-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                               <div class="col-sm-12">
                                   <form class="form-horizontal">
                                   {!! csrf_field() !!}
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3">Product Type</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control select2me input-sm" id="modfamily" name="modfamily">
                                                     <option value=""></option>
                                                     <option value="Test Socket">Test Socket</option>
                                                     <option value="Burn In">Burn In</option>
                                                 </Select>
                                                  <input type="hidden" class="form-control input-sm" id="modregstatus" name="modregstatus" maxlength="40" >
                                                  <input type="hidden" class="form-control input-sm" id="modregid" name="modregid" maxlength="40">
                                                  <div id="er_modfamily"></div>
                                             </div>
                                        </div>
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3">Mode of Defect</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="mod" name="mod" maxlength="40">
                                                  <div id="er_mod" ></div>
                                             </div>     
                                        </div>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button" id='modalsave' onclick="javascript:modregistration();"  class="btn btn-success">Save</button>
                                                  <button type="button" id='btnmodregclear' class="btn btn-danger">Clear</button>
                                             </div>
                                        </div> 
                                   </form>       
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-sm-12">
                                   <div class="scroller" style="height: 300px" id="tableformodreg">
                                        <table id="modreg-table" class="table table-striped table-bordered table-hover"style="font-size:13px">
                                             <thead id="thead1">
                                                  <tr>
                                                       <td class="table-checkbox" style="width: 5%">
                                                            <input type="checkbox" class="group-checkable checkAllitemsmod" name="checkAllitemmod" data-set="#sample_3 .checkboxes"/>
                                                       </td>
                                                       <td></td>
                                                       <td>Product Type</td>
                                                       <td>Mod</td>    
                                                  </tr>
                                             </thead>
                                             <tbody id="tblformodreg">
                                             @foreach($tablemodreg as $rec)
                                                  <tr>
                                                       <td style="width: 2%">
                                                            <input type="checkbox" class="form-control input-sm checkboxesmod" value="{{$rec->id}}" name="checkitemmod" id="checkitemmod"></input> 
                                                       </td> 
                                                       <td style="width: 2%">
                                                            <button type="button" name="edit-modreg" class="btn btn-sm btn-primary edit-modreg" value="{{$rec->id}}">
                                                                 <i class="fa fa-edit"></i> 
                                                            </button> 
                                                       </td>                              
                                                       <td>{{$rec->family}}</td>     
                                                       <td>{{$rec->mod}}</td>
                                                  </tr>
                                             @endforeach
                                             </tbody>
                                        </table>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" id='modalsave' onclick="javascript:removemodreg();"  class="btn red">Remove</button>
                         <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_modclose">Close</button>
                    </div>
               </div>    
          </div>
     </div>

     <!-- Target Registration -->
     <div id="targetreg_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="targetreg_modal-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                               <div class="col-sm-12">
                                   <form class="form-horizontal">
                                        {!! csrf_field() !!}
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">From</label>
                                             <div class="col-sm-3">
                                                  <input type="text" class="form-control input-sm" name="target-datefrom" id="target-datefrom">
                                                  <div id="er_target-datefrom"></div>
                                             </div>
                                             <label class="control-label col-sm-2">To</label>
                                             <div class="col-sm-3">
                                                  <input type="text" class="form-control input-sm" name="target-dateto" id="target-dateto">
                                                  <div id="er_target-dateto"></div>
                                             </div>   
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Target Yield</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="targetyield" name="targetyield">
                                                  <div id="er_targetyield"></div>
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Target DPPM</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="targetdppm" name="targetdppm">
                                                  <input type="hidden" class="form-control input-sm" id="targetstatus" name="targetstatus" maxlength="40" >
                                                  <input type="hidden" class="form-control input-sm" id="targetid" name="targetid" maxlength="40">
                                                  <div id="er_targetdppm"></div>
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Product Type</label>
                                             <div class="col-sm-9">
                                                  <select class="form-control select2me input-sm" name="targetptype" id="targetptype">
                                                       <option value=""></option>
                                                       <option value="Test Socket">Test Socket</option>
                                                       <option value="Burn In">Burn In</option>     
                                                  </select>
                                                  <div id="er_targetyield"></div>
                                             </div>     
                                        </div>
                                        <br>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button" id='targetsave' onclick="javascript:targetregistration();"  class="btn btn-success">Save</button>
                                                  <button type="button" id='targetclear' class="btn btn-danger">Clear</button>
                                             </div>
                                        </div>      
                                   </form>       
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-sm-12">
                                   <div class="scroller" style="height: 300px" id="tablefortargetreg">
                                        <table id="modreg-table" class="table table-striped table-bordered table-hover"style="font-size:13px">
                                             <thead id="thead1">
                                                  <tr>
                                                       <td class="table-checkbox" style="width: 5%">
                                                            <input type="checkbox" class="group-checkable checkAllitemstarget" name="checkAllitemtarget"/>
                                                       </td>
                                                       <td></td>
                                                       <td>From</td>
                                                       <td>To</td>
                                                       <td>Target Yield</td>
                                                       <td>Target DPPM</td>
                                                       <td>Product Type</td>
                                                  </tr>
                                             </thead>
                                             <tbody id="tblfortarget">
                                             @foreach($target as $rec)
                                                  <tr>
                                                       <td style="width: 2%">
                                                            <input type="checkbox" class="form-control input-sm checkboxestarget" value="{{$rec->id}}" name="checkitemtarget" id="checkitemtarget"></input> 
                                                       </td> 
                                                       <td style="width: 2%">
                                                            <button type="button" name="edit-targetreg" class="btn btn-sm btn-primary edit-targetreg" value="{{$rec->id}}">
                                                                 <i class="fa fa-edit"></i> 
                                                            </button> 
                                                       </td>
                                                       <td>{{$rec->datefrom}}</td>
                                                       <td>{{$rec->dateto}}</td>
                                                       <td>{{$rec->yield}}</td>
                                                       <td>{{$rec->dppm}}</td>
                                                       <td>{{$rec->ptype}}</td>
                                                  </tr>
                                             @endforeach
                                             </tbody>
                                        </table>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" id='saveTarget' onclick="javascript:removetargetreg();"  class="btn red">Remove</button>
                         <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_targetclose">Close</button>
                    </div>
               </div>    
          </div>
     </div>
     
     <!-- Defect Summary Report -->
    <div id="defectsummaryrpt_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="defectsummaryrpt-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-sm-12">
                                   <form class="form-horizontal">
                                        {!! csrf_field() !!}
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">From</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="dsr-datefrom" id="dsr-datefrom">
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">To</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="dsr-dateto" id="dsr-dateto">
                                             </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Product Type</label>
                                             <div class="col-sm-9">
                                                  <select class="form-control input-sm" name="dsr-ptype" id="dsr-ptype">
                                                       <option value=""></option>
                                                       <option value="Test Socket">Test Socket</option>
                                                       <option value="Burn In">Burn In</option>     
                                                  </select>
                                             </div>
                                        </div>
                                       <br>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button" onclick="javascript:defectsummaryRpt();"  class="btn green-jungle input-sm">Export to Excel</button>
                                                  <!-- <button type="button" onclick="javascript:defectsummaryRptpdf();" class="btn yellow-gold input-sm" >Export to PDF</button>  -->
                                                   <button type="button" data-dismiss="modal" class="btn btn-danger input-sm">Close</button>
                                             </div>  
                                        </div>
                                   </form>   
                              </div>
                         </div>
                    </div>
               </div>    
          </div>
     </div>

     <!--Summary Report -->
    <div id="summaryrpt_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog " gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="summaryrpt-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-sm-12">
                                   <form class="form-horizontal">
                                        {!! csrf_field() !!}
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">From</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="srdatefrom" id="srdatefrom">
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">To</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="srdateto" id="srdateto">
                                             </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Production Type</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm" id="srprodtype" name="srprodtype">
                                                     <option value=""></option>
                                                     <option value="Test Socket">Test Socket</option>
                                                     <option value="Burn In">Burn In</option>
                                                 </Select>
                                             </div>     
                                        </div>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button" onclick="javascript:summaryREpt();"  class="btn green-jungle input-sm">Export to Excel</button>
                                                  <!-- <button type="button"  onclick="javascript:summaryREptpdf();" class="btn yellow-gold input-sm" >Export to PDF</button>  -->
                                                   <button type="button" data-dismiss="modal" class="btn btn-danger input-sm">Close</button>
                                             </div>  
                                        </div>
                                   </form>   
                              </div>
                         </div>
                    </div>
               </div>    
          </div>
     </div>

     <!--Yield Performance Summary Report -->
     <div id="yieldpsrpt_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="yieldpsrpt-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-sm-12">
                                   <form class="form-horizontal">
                                        {!! csrf_field() !!}
                                        
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">From</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="ypsr-datefrom" id="ypsr-datefrom">
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">To</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="ypsr-dateto" id="ypsr-dateto">
                                             </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">PO Number</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="ypsr-ponumber" id="ypsr-ponumber">
                                             </div>
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Product Type</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm " id="ypsr-prodtype" name="ypsr-prodtype">
                                                       <option value=""></option>
                                                       <option value="Test Socket">Test Socket</option>
                                                       <option value="Burn In">Burn In</option>
                                                 </Select>
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Family</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm " id="ypsr-family" name="ypsr-family">
                                                       <option></option>
                                                       @foreach($family as $fam)
                                                       <option value="{{$fam->family}}">{{$fam->family}}</option>
                                                       @endforeach
                                                 </Select>
                                             </div>
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Series Name</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm " id="ypsr-seriesname" name="ypsr-seriesname">
                                                       <option value=""></option>
                                                       <!-- yield performance series -->
                                                 </Select>
                                             </div>
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Device</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm " id="ypsr-device" name="ypsr-device">
                                                       <option value=""></option>
                                                       @foreach($record as $rec)
                                                       <option value="{{$rec->device}}">{{$rec->device}}</option>
                                                       @endforeach
                                                 </Select>
                                             </div>
                                        </div>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button" onclick="javascript:yieldsumRpt();"  class="btn green-jungle input-sm">Export to Excel</button>
                                                  <button type="button" onclick="javascript:yieldsumRptpdf();" class="btn yellow-gold input-sm" >Export to PDF</button> 
                                                  <button type="button" data-dismiss="modal" class="btn btn-danger input-sm">Close</button>
                                             </div>  
                                        </div>
                                   </form>   
                              </div>
                         </div>
                    </div>
               </div>    
          </div>
     </div>

     <!--Yield Summary Family Report -->
     <div id="yieldsfrpt_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header green">
                         <h4 class="yieldsfrpt-title"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-sm-12">
                                   <form class="form-horizontal">
                                        {!! csrf_field() !!}
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">From</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="ysf-datefrom" id="ysf-datefrom">
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">To</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" name="ysf-dateto" id="ysf-dateto">
                                             </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Yield Target</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm " id="ysf-yieldtarget" name="ysf-yieldtarget">
                                                       <option value=""></option>
                                                       @foreach ($targetyield as $rec)
                                                       <option value="{{$rec->yield}}">{{$rec->yield}}</option>
                                                       @endforeach
                                                  </Select>
                                                  <input type="hidden" class="form-control input-sm" id="chose" name="chose" disabled="disabled"/>
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">Product</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm " id="ysf-ptype" name="ysf-ptype">
                                                       <option value=""></option>
                                                       <option value="Test Socket">Test Socket</option>
                                                       <option value="Burn In">Burn In</option>
                                                  </Select>
                                                  <input type="hidden" class="form-control input-sm" id="chose" name="chose" disabled="disabled"/>
                                             </div>     
                                        </div>
                                        <hr>
                                        <div class="form-group pull-right">
                                             <div class="col-sm-12">
                                                  <button type="button" onclick="javascript:yieldsumfamRpt();"  class="btn green-jungle input-sm">Export to Excel</button>
                                                  <button type="button" onclick="javascript:yieldsumfamRptpdf();" class="btn yellow-gold input-sm" >Export to PDF</button> 
                                                   <button type="button" data-dismiss="modal" class="btn btn-danger input-sm">Close</button>
                                             </div>  
                                        </div>
                                   </form>   
                              </div>
                         </div>
                    </div>
               </div>    
          </div>
     </div>

     <div id="updateyield_Modal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog gray-gallery">
               <div class="modal-content ">
                    <div class="modal-header">
                         <h4 class="updatetitle"></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-sm-12">
                                   <form class="form-horizontal">
                                        {!! csrf_field() !!}
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">PO Number</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="pono2" name="pono2" disabled="disabled">
                                                  <input type="hidden" class="form-control input-sm" id="masterid" name="masterid">
                                             </div>     
                                        </div>
                                        <div class="form-group">
                                             <label class="control-label col-sm-3">PO Quantity</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="poqty2" name="poqty2" maxlength="40" disabled="disabled" >
                                                  <div id="er1"></div>
                                             </div>
                                        </div>
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3" style="font-size:12px">Device</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="device2" name="device2" value="@foreach($msrecords as $msrec){{$msrec->devicename}}@endforeach" disabled="disabled" />
                                                  
                                             </div>
                                        </div>
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3" style="font-size:12px">Series</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm" id="series2" name="series2">
                                                  @foreach ($series as $serie)
                                                  <option value="{{$serie->description}}">{{$serie->description}}
                                                  </option>
                                                  @endforeach
                                                  </Select>
                                             </div>
                                        </div>
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3" style="font-size:12px">Family</label>
                                             <div class="col-sm-9">
                                                  <Select class="form-control input-sm" id="family2" name="family2">
                                                 
                                                  @foreach ($family as $fam)
                                                  <option value="{{$fam->family}}">{{$fam->family}}</option>
                                                  @endforeach
                                                  </Select>
                                             </div>
                                        </div>
                                         <div class="form-group row">
                                             <label class="control-label col-sm-3" style="font-size:12px">Total Output</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="toutput2" name="toutput2"/>
                                                  <div id="erd2"></div>
                                             </div>
                                        </div>
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3" style="font-size:12px">Total Reject</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="treject2" name="treject2" />
                                                  <div id="erd3"></div>
                                             </div>
                                        </div>
                                        <div class="form-group row">
                                             <label class="control-label col-sm-3" style="font-size:12px">Total w/o Yield</label>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control input-sm" id="twoyield2" name="twoyield2" disabled="disabled"/>
                                                   
                                             </div>
                                        </div>    
                                   </form>   
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" id='modalsave' onclick="javascript:update();"  class="btn btn-success">Update</button> 
                         <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                    </div>
               </div>    
          </div>
     </div>

     <!-- Empty FIELD SEARCH -->
     <div id="messageModal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog modal-sm gray-gallery">
               <div class="modal-content">
                    <div class="modal-header">
                         <h4 class="modal-title">Warning!</h4>
                    </div>
                    <form class="form-horizontal">
                         <div class="modal-body">
                              <div class="row">
                                   <div class="col-sm-12">
                                        <label class="control-label col-sm-10" id="message"></label>
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
 
@endsection

@push('script')
<script type="text/javascript" src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/canvasjs.min.js') }}"></script>
<script>

$(document).ready(function(e) {
     DatePickers();
     ButtonsClicked();
     Checkboxes();
     EditButtons();
     ButtonsClear();
     ButtonClosed();
     $('#ysf-icsocket').change(function(){
          if($('#ysf-icsocket').is(':checked')){
               $('#chose').val("true");

          }else{
              $('#chose').val("false");
          }         
     });

     $('#pono').change(function(){
          var pono = $(this).val();
          $.ajax({
               url:"{{ url('/getponoreg') }}",
               method:'get',
               data: { 
                    pono : pono, 
               },
          }).done(function(data, textStatus, jqXHR){
               console.log(data);
            
               $('#pono').keyup(function(e){
                    if(!this.val()){
                         $('#pono').val("");
                         $('#podevice').val("");
                         $('#poquantity').val("");      
                    }   
               });
               $('#pono').val(data[0]['PO']);
               $('#podevice').val(data[0]['devicename']);
               $('#poquantity').val(data[0]['POqty']);

          }).fail(function(jqXHR,textStatus, errorThrown){
               console.log(errorThrown+'|'+textStatus);
          });   
     });

     $('#devicepono').change(function(){
          var pono = $(this).val();
          $.ajax({
               url:"{{ url('/getponoreg') }}",
               method:'get',
               data: { 
                    pono : pono, 
               },
          }).done(function(data, textStatus, jqXHR){
               console.log(data);
               $('#devicepono').keyup(function(e){
                    if(!this.val()){
                         $('#er_devicepono').html("Device field is empty"); 
                         $('#er_devicepono').css('color', 'red');   
                         return false;    
                         $('#devicepono').val("");
                         $('#devicename').val("");
                    }   

               });
               $('#devicepono').val(data[0]['PO']);
               $('#devicename').val(data[0]['devicename']);
          }).fail(function(jqXHR,textStatus, errorThrown){
               console.log(errorThrown+'|'+textStatus);
          });   
     });
     
     FieldsValidations();

     $('#devicefamily').on('change',function(){
          $('#deviceseries').select2('val',"");
          var family = $('select[name=devicefamily]').val();
          $('#deviceseries').html("");
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
                         case "QFP1":
                              var sup = '<option value="'+val.series+'">'+val.series+'</option>';
                              break;
                         case "QFP2":
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
                    $('#deviceseries').append(option);
               });
          
          }).fail(function(jqXHR, textStatus, errorThrown){
               console.log(errorThrown+'|'+textStatus);
          });
     });

     $('#ypsr-family').on('change',function(){
          $('#ypsr-seriesname').select2('val',"");
          var family = $('select[name=ypsr-family]').val();
          $('#ypsr-seriesname').html("");
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
                    $('#ypsr-seriesname').append(option);
               });
          });
     });

     
});//end of script-------------------------------------------------------------------------------------
var i =0;
function loadchart(){

     var datefroms = $('#datefroms').val();
     var datetos = $('#datetos').val();
   

     var token = "{{ Session::token() }}";
     var data = {_token: token, datefroms:datefroms, datetos:datetos};
     $.ajax({
          
          url:"{{ url('/loadchart') }}",
          method:'post',
          data:data
     }).done(function(data, textStatus, jqXHR){
         console.log(data);
     /*    alert(data[0]['toutput']);
         var treject =data[0]['treject'];*/

          var chart = new CanvasJS.Chart("chartContainer",
          {
               theme: "theme3",
                        animationEnabled: true,
               title:{
                    text: "Chart Summary",
                    fontSize: 30
               },
               toolTip: {
                    shared: true
               },             
               axisY: {
                    title: "Total Quantity"
               },
               
               data: [ 
               {
                    type: "column",     
                    name: "Total Outputs",
                    legendText: "Total Output",
                    showInLegend: true, 
                    dataPoints:
                    [

                    /*{label: data[0].family, y: parseInt(data[0]['toutputs'])},*/
                    
                    ]
               },
               {
                    type: "column",     
                    name: "Total Rejects",
                    legendText: "Total Rejects",
                    axisYType: "secondary",
                    showInLegend: true,
                    dataPoints:
                    [
                   
                   /*{label: data[0].family, y: parseInt(data[0]['treject'])},
                   */
                    
                    ]
               }
               
               ],
               legend:
               {
                    cursor:"pointer",
                    itemclick: function(e){
                    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                         e.dataSeries.visible = false;
                    }
                    else {
                         e.dataSeries.visible = true;
                    }
                         chart.render();
                    }
               },
          });


          for(var i = 0; i < data.length; i++)
          {
               var length = chart.options.data[0].dataPoints.length;
               chart.options.data[0].dataPoints.push({label: data[i].family, y: parseInt(data[i]['toutput'])});
               chart.render();
          }
          
          for(var i = 0; i < data.length; i++)
          {
               var length = chart.options.data[1].dataPoints.length;
               chart.options.data[1].dataPoints.push({label: data[i].family, y: parseInt(data[i]['qty'])});
               chart.render();
          }

    // });
     



     }).fail(function(jqXHR,textStatus,errorThrown){
          console.log(errorThrown+'|'+textStatus);
     });     
}
function poregistration(){
     var pono = $('#pono').val();
     var podevice = $('#podevice').val();
     var poquantity = $('#poquantity').val();
     var editsearch = $('#hdporegid').val();
     var status = $('input[name=poregstatus]').val();
     var id = $('input[name=poregid]').val();

     if(pono == ""){     
        $('#er_pono').html("PO number field is empty"); 
        $('#er_pono').css('color', 'red');       
        return false;  
     }
     
     if(podevice == ""){     
        $('#er_podevice').html("Device field is empty"); 
        $('#er_podevice').css('color', 'red');       
        return false;  
     } 
     if(poquantity == ""){     
        $('#er_poquantity').html("Quantity field is empty"); 
        $('#er_poquantity').css('color', 'red');       
        return false;  
     }
     $('#tblforporeg').html("");
     $.post("{{ url('/add-poregistration') }}",
     {
          _token:$('meta[name=csrf-token]').attr('content'),
          pono:pono,
          podevice:podevice,
          poquantity:poquantity,
          status:status,
          id:id
     }).done(function(data, textStatus, jqXHR){
          console.log(data);
          if(data >  0){
               $('#messageModal').modal('show');
               $('#message').html("Record exist please try another P.O number.");
               $.post("{{ url('/display-poregistration') }}",
               {
                    _token:$('meta[name=csrf-token]').attr('content'),
               }).done(function(data, textStatus, jqXHR){
                    console.log(data);
                    $.each(data,function(i,val){
                         var tblrow = '<tr>'+
                                        '<td style="width: 2%" >'+
                                             '<input type="checkbox" class="form-control input-sm checkboxespo" value="'+val.id+'" name="checkitempo" id="checkitempo"></input>'+ 
                                        '</td>'+ 
                                        '<td style="width: 2%">'+
                                             '<button type="button" name="edit-poreg" class="btn btn-sm btn-primary edit-poreg" value="'+val.id+'">'+
                                                  '<i class="fa fa-edit"></i>'+ 
                                             '</button> '+
                                        '</td>'+                  
                                        '<td>'+val.pono+'</td>'+
                                        '<td>'+val.device+'</td>'+
                                        '<td>'+val.poqty+'</td>'+    
                                   '</tr>';
                         $('#tblforporeg').append(tblrow);
                         $('#pono').val("");
                         $('#podevice').val("");
                         $('#poquantity').val("");
                         $('#poregstatus').val("ADD");         
                         $('.edit-poreg').click(function(){
                              $('#poregstatus').val("EDIT");
                              var editsearch = $(this).val();
                              $('#poregid').val(editsearch);
                              $.ajax({
                                   url: "{{ url('/editporeg') }}",
                                   method: 'get',
                                   data:  { 
                                        editsearch : editsearch, 
                                   },    
                              }).done(function(data, textStatus, jqXHR) {
                                   console.log(data);
                                   $('#pono').val(data[0]['pono']);
                                   $('#poquantity').val(data[0]['poqty']);
                                   $('#podevice').val(data[0]['device']); 
                              }).fail(function(jqXHR, textStatus, errorThrown) {
                                   console.log(errorThrown+'|'+textStatus);
                              });
                         });
                    });     
               }).fail(function(jqXHR,textStatus,erroThrown){
                    console.log(erroThrown+'|'+textStatus);
               });         
          }else{
               $.each(data,function(i,val){
                    var tblrow = '<tr>'+
                                   '<td style="width: 2%" >'+
                                        '<input type="checkbox" class="form-control input-sm checkboxespo" value="'+val.id+'" name="checkitempo" id="checkitempo"></input>'+ 
                                   '</td>'+ 
                                   '<td style="width: 2%">'+
                                        '<button type="button" name="edit-poreg" class="btn btn-sm btn-primary edit-poreg" value="'+val.id+'">'+
                                             '<i class="fa fa-edit"></i>'+ 
                                        '</button> '+
                                   '</td>'+                  
                                   '<td>'+val.pono+'</td>'+
                                   '<td>'+val.device+'</td>'+
                                   '<td>'+val.poqty+'</td>'+    
                              '</tr>';
                    $('#tblforporeg').append(tblrow);
                    $('#pono').val("");
                    $('#podevice').val("");
                    $('#poquantity').val("");
                    $('#poregstatus').val("ADD");         
                    $('.edit-poreg').click(function(){
                         $('#poregstatus').val("EDIT");
                         var editsearch = $(this).val();
                         $('#poregid').val(editsearch);
                         $.ajax({
                              url: "{{ url('/editporeg') }}",
                              method: 'get',
                              data:  { 
                                   editsearch : editsearch, 
                              },    
                         }).done(function(data, textStatus, jqXHR) {
                              console.log(data);
                              $('#pono').val(data[0]['pono']);
                              $('#poquantity').val(data[0]['poqty']);
                              $('#podevice').val(data[0]['device']); 
                         }).fail(function(jqXHR, textStatus, errorThrown) {
                              console.log(errorThrown+'|'+textStatus);
                         });
                    });
               });    
          }
          
     }).fail(function(jqXHR,textStatus,erroThrown){
          console.log(erroThrown+'|'+textStatus);
     });         
}
function deviceregistration(){
     var devicepono = $('#devicepono').val();
     var devicename = $('#devicename').val();
     var devicefamily = $('#devicefamily').val();
     var deviceseries = $('#deviceseries').val();
     var ptype = $('#deviceptype').val();
     var status = $('input[name=devregstatus]').val();
     var id = $('input[name=devregid]').val();
     if(devicepono == ""){     
        $('#er_devicepono').html("PO Number field is empty"); 
        $('#er_devicepono').css('color', 'red');       
        return false;  
     }
     if(devicename == ""){     
        $('#er_devicename').html("Device field is empty"); 
        $('#er_devicename').css('color', 'red');       
        return false;  
     }
     if(devicefamily == ""){     
        $('#er_devicefamily').html("Family field is empty"); 
        $('#er_devicefamily').css('color', 'red');       
        return false;  
     }
     if(deviceseries == ""){     
        $('#er_deviceseries').html("Series field is empty"); 
        $('#er_deviceseries').css('color', 'red');       
        return false;  
     }
     if(ptype == ""){     
        $('#er_deviceptype').html("Product Type field is empty"); 
        $('#er_deviceptype').css('color', 'red');       
        return false;  
     }

     $('#tblfordevreg').html("");
     $.post("{{ url('/add-deviceregistration') }}",
     {
          _token:$('meta[name=csrf-token]').attr('content'),
          devicename:devicename,
          family:devicefamily,
          series:deviceseries,
          pono:devicepono,
          ptype:ptype,
          status:status,
          id:id
     }).done(function(data, textStatus, jqXHR){
          if(data > 0){
               $('#messageModal').modal('show');
               $('#message').html("Record exist please try another P.O number.");
               $.post("{{ url('/display-deviceregistration') }}",
               {
                    _token:$('meta[name=csrf-token]').attr('content'),
               }).done(function(data, textStatus, jqXHR){
                    console.log(data); 
                    $.each(data,function(i,val){
                         var tblrow = '<tr>'+
                                        '<td style="width: 2%">'+
                                             '<input type="checkbox" class="form-control input-sm checkboxesdevice" value="'+val.id+'" name="checkitemdevice" id="checkitemdevice"></input> '+
                                        '</td>'+
                                        '<td style="width: 2%">'+
                                             '<button type="button" name="edit-devicereg" class="btn btn-sm btn-primary edit-devicereg" value="'+val.id+'">'+
                                                  '<i class="fa fa-edit"></i>'+ 
                                             '</button> '+
                                        '</td>'+    
                                        '<td>'+val.pono+'</td> '+                          
                                        '<td>'+val.devicename+'</td>'+
                                       ' <td>'+val.family+'</td>'+
                                        '<td>'+val.series+'</td>'+
                                        '<td>'+val.ptype+'</td>'+
                                   '</tr>';
                         $('#tblfordevreg').append(tblrow);
                         $('#devicepono').val("");
                         $('#devicename').val("");
                         $('#devicefamily').select2('val',"");
                         $('#deviceseries').select2('val',"");
                         $('#deviceptype').select2('val',"");
                         $('#devregstatus').val("ADD");         
                         $('.edit-devicereg').click(function(){
                              $('#devregstatus').val("EDIT");
                              var editsearch = $(this).val();
                              $('#devregid').val(editsearch);
                              $.ajax({
                                   url: "{{ url('/editdevicereg') }}",
                                   method: 'get',
                                   data:  { 
                                        editsearch : editsearch, 
                                   },
                                   
                              }).done(function(data, textStatus, jqXHR) {
                                   console.log(data);
                                   $('#devicepono').val(data[0]['pono']);
                                   $('#devicename').val(data[0]['devicename']);
                                   $('#devicefamily').select2('val',data[0]['family']);
                                   $('#deviceseries').select2('val',data[0]['series']); 
                                   $('#deviceptype').select2('val',data[0]['ptype']);
                              }).fail(function(jqXHR, textStatus, errorThrown) {
                                   console.log(errorThrown+'|'+textStatus);
                              });
                         });
                    });
               }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown+'|'+textStatus);
               });
          }else{
               $.each(data,function(i,val){
                    var tblrow = '<tr>'+
                                   '<td style="width: 2%">'+
                                        '<input type="checkbox" class="form-control input-sm checkboxesdevice" value="'+val.id+'" name="checkitemdevice" id="checkitemdevice"></input> '+
                                   '</td>'+
                                   '<td style="width: 2%">'+
                                        '<button type="button" name="edit-devicereg" class="btn btn-sm btn-primary edit-devicereg" value="'+val.id+'">'+
                                             '<i class="fa fa-edit"></i>'+ 
                                        '</button> '+
                                   '</td>'+    
                                   '<td>'+val.pono+'</td> '+                          
                                   '<td>'+val.devicename+'</td>'+
                                  ' <td>'+val.family+'</td>'+
                                   '<td>'+val.series+'</td>'+
                                   '<td>'+val.ptype+'</td>'+
                              '</tr>';
                    $('#tblfordevreg').append(tblrow);
                    $('#devicepono').val("");
                    $('#devicename').val("");
                    $('#devicefamily').select2('val',"");
                    $('#deviceseries').select2('val',"");
                    $('#deviceptype').select2('val',"");
                    $('#devregstatus').val("ADD");         
                    $('.edit-devicereg').click(function(){
                         $('#devregstatus').val("EDIT");
                         var editsearch = $(this).val();
                         $('#devregid').val(editsearch);
                         $.ajax({
                              url: "{{ url('/editdevicereg') }}",
                              method: 'get',
                              data:  { 
                                   editsearch : editsearch, 
                              },
                              
                         }).done(function(data, textStatus, jqXHR) {
                              console.log(data);
                              $('#devicepono').val(data[0]['pono']);
                              $('#devicename').val(data[0]['devicename']);
                              $('#devicefamily').select2('val',data[0]['family']);
                              $('#deviceseries').select2('val',data[0]['series']); 
                              $('#deviceptype').select2('val',data[0]['ptype']);
                         }).fail(function(jqXHR, textStatus, errorThrown) {
                              console.log(errorThrown+'|'+textStatus);
                         });
                    });
               });     
          }
          
     }).fail(function(jqXHR, textStatus, erroThrown){
          console.log(erroThrown+'|'+textStatus);
     });     
}
function seriesregistration(){
     var seriesfamily = $('#seriesfamily').val();
     var seriesname = $('#seriesname').val();
     var status = $('#seriesregstatus').val();
     var id = $('#seriesregid').val();
     if(seriesfamily == ""){     
        $('#er_seriesfamily').html("Family field is empty"); 
        $('#er_seriesfamily').css('color', 'red');       
        return false;  
     }
     if(seriesname == ""){     
        $('#er_seriesname').html("Series field is empty"); 
        $('#er_seriesname').css('color', 'red');       
        return false;  
     }

     $('#tblforseries').html("");
     $.post("{{ url('/add-seriesregistration') }}",
     {
          _token:$('meta[name=csrf-token]').attr('content'),
          family:seriesfamily,
          series:seriesname,
          status:status,
          id:id  

     }).done(function(data, textStatus, jqXHR){
          $.each(data,function(i,val){
               var tblrow = '<tr>'+
                              '<td style="width: 2%">'+
                                   '<input type="checkbox" class="form-control input-sm checkboxesseries" value="'+val.id+'" name="checkitemseries" id="checkitemseries"></input>'+ 
                              '</td>'+  
                              '<td style="width: 2%">'+
                                   '<button type="button" name="edit-seriesreg" class="btn btn-sm btn-primary edit-seriesreg" value="'+val.id+'">'+
                                        '<i class="fa fa-edit"></i>'+ 
                                   '</button>'+ 
                              '</td>'+                             
                              '<td>'+val.family+'</td>'+
                              '<td>'+val.series+'</td>'+
                         '</tr>';
               $('#tblforseries').append(tblrow);
               $('#seriesfamily').select2('val',"");
               $('#seriesname').val("");
               $('#seriesregstatus').val("ADD");  
               $('.edit-seriesreg').click(function(){
                    $('#seriesregstatus').val("EDIT");
                    var editsearch = $(this).val();
                    $('#seriesregid').val(editsearch);
                    $.ajax({
                         url: "{{ url('/editseriesreg') }}",
                         method: 'get',
                         data:  { 
                              editsearch : editsearch, 
                         },
                         
                    }).done(function(data, textStatus, jqXHR) {
                         console.log(data);
                         $('#seriesfamily').select2('val',data[0]['family']);
                         $('#seriesname').val(data[0]['series']);  
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                         console.log(errorThrown+'|'+textStatus);
                    });
               });
          });            
     }).fail(function(jqXHR, textStatus, erroThrown){
          console.log(erroThrown+'|'+textStatus);
     });
}
function modregistration(){
     var mod = $('#mod').val();
     var family = $('#modfamily').val();
     var status = $('#modregstatus').val();
     var id = $('#modregid').val();
     if(mod == ""){     
        $('#er_mod').html("Mod field is empty"); 
        $('#er_mod').css('color', 'red');       
        return false;  
     }
     if(family == ""){     
        $('#er_modfamily').html("Family field is empty"); 
        $('#er_modfamily').css('color', 'red');       
        return false;  
     }
     $('#tblformodreg').html("");
     $.post("{{ url('/add-modregistration') }}",
     {
          _token:$('meta[name=csrf-token]').attr('content'),
          mod:mod,
          family:family,
          status:status,
          id:id
     }).done(function(data, textStatus, jqXHR){
          $.each(data,function(i,val){
               var tblrow = '<tr>'+
                              '<td style="width: 2%">'+
                                   '<input type="checkbox" class="form-control input-sm checkboxesmod" value="'+val.id+'" name="checkitemmod" id="checkitemmod"></input>'+ 
                              '</td> '+
                              '<td style="width: 2%">'+
                                   '<button type="button" name="edit-modreg" class="btn btn-sm btn-primary edit-modreg" value="'+val.id+'">'+
                                        '<i class="fa fa-edit"></i> '+
                                   '</button>'+ 
                              '</td>'+                              
                              '<td>'+val.family+'</td>'+
                              '<td>'+val.mod+'</td>'+
                         '</tr>';
               $('#tblformodreg').append(tblrow);
               $('#mod').val("");
               $('#modfamily').select2('val',"");
               $('#modregstatus').val("ADD");  
               $('.edit-modreg').click(function(){
                    $('#modregstatus').val("EDIT");
                    var editsearch = $(this).val();
                    $('#modregid').val(editsearch);
                    $.ajax({
                         url: "{{ url('/editmodreg') }}",
                         method: 'get',
                         data:  { 
                              editsearch : editsearch, 
                         },
                         
                    }).done(function(data, textStatus, jqXHR) {
                         console.log(data);
                         $('#mod').val(data[0]['mod']);
                         $('#modfamily').select2('val',data[0]['family']);
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                         console.log(errorThrown+'|'+textStatus);
                    });
               });
          });
     }).fail(function(jqXHR, textStatus, erroThrown){
          console.log(erroThrown+'|'+textStatus);
     });   
}
function targetregistration(){
     var datefrom = $('#target-datefrom').val();
     var dateto = $('#target-dateto').val();
     var yielding = $('#targetyield').val();
     var dppm = $('#targetdppm').val();
     var ptype = $('#targetptype').val();
     var status = $('#targetstatus').val();
     var id = $('#targetid').val();
     if(srdatefrom == ""){     
        $('#er_target-datefrom').html("Date From field is empty"); 
        $('#er_target-datefrom').css('color', 'red');       
        return false;  
     }
     if(srdateto == ""){     
        $('#er_target-dateto').html("Date To field is empty"); 
        $('#er_target-dateto').css('color', 'red');       
        return false;  
     }
     if(yielding == ""){     
        $('#er_targetyield').html("Target Yield field is empty"); 
        $('#er_targetyield').css('color', 'red');       
        return false;  
     }
     if(dppm == ""){     
        $('#er_targetdppm').html("Target dppm field is empty"); 
        $('#er_targetdppm').css('color', 'red');       
        return false;  
     }
     if(ptype == ""){     
        $('#er_targetptype').html("Product Type field is empty"); 
        $('#er_targetptype').css('color', 'red');       
        return false;  
     }
     $('#tblfortarget').html("");
     $.post("{{ url('/add-targetreg') }}",
     {
          _token:$('meta[name=csrf-token]').attr('content'),
          datefrom:datefrom,
          dateto:dateto,
          yielding:yielding,
          dppm:dppm,
          ptype:ptype,
          status:status,
          id:id
     }).done(function(data, textStatus, jqXHR){
          $.each(data,function(i,val){
               var tblrow = '<tr>'+
                              '<td style="width: 2%">'+
                                   '<input type="checkbox" class="form-control input-sm checkboxestarget" value="'+val.id+'" name="checkitemtarget" id="checkitemtarget"></input>'+ 
                              '</td>'+ 
                              '<td style="width: 2%">'+
                                   '<button type="button" name="edit-targetreg" class="btn btn-sm btn-primary edit-targetreg" value="'+val.id+'">'+
                                        '<i class="fa fa-edit"></i>'+ 
                                   '</button>'+ 
                              '</td>'+
                              '<td>'+val.datefrom+'</td>'+
                              '<td>'+val.dateto+'</td>'+                              
                              '<td>'+val.yield+'</td>'+
                              '<td>'+val.dppm+'</td>'+
                              '<td>'+val.ptype+'</td>'+
                         '</tr>';
               $('#tblfortarget').append(tblrow);
               $('#target-datefrom').val("");
               $('#target-dateto').val("");
               $('#targetyield').val("");
               $('#targetdppm').val("");
               $('#targetptype').select2('val',"");
               $('#targetstatus').val("ADD");  
               $('.edit-targetreg').click(function(){
                    $('#targetstatus').val("EDIT");
                    var editsearch = $(this).val();
                    $('#targetid').val(editsearch);
                    $.ajax({
                         url: "{{ url('/edittargetreg') }}",
                         method: 'get',
                         data:  { 
                              editsearch : editsearch, 
                         },
                         
                    }).done(function(data, textStatus, jqXHR) {
                         console.log(data);
                         $('#target-datefrom').val(data[0]['datefrom']);
                         $('#target-dateto').val(data[0]['dateto']);
                         $('#targetyield').val(data[0]['yield']);
                         $('#targetdppm').val(data[0]['dppm']); 
                         $('#targetptype').select2('val',data[0]['ptype']); 
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                         console.log(errorThrown+'|'+textStatus);
                    });
               });    
          });
     }).fail(function(jqXHR, textStatus, erroThrown){
          console.log(erroThrown+'|'+textStatus);
     });      
}
function deleteAllcheckeditems(){
     var tray = [];
     $(".checkboxes:checked").each(function () {
          tray.push($(this).val());
     });
     var traycount =tray.length;

     $.ajax({
          url: "{{ url('/deleteAll-yieldperformance') }}",
          method: 'get',
          data:  { 
               tray : tray, 
               traycount : traycount
          },
          
     }).done( function(data, textStatus, jqXHR) {
          console.log(data);
          window.location.href = "{{ url('/yieldperformance') }}";   
     }).fail(function(jqXHR, textStatus, errorThrown) {
          console.log(errorThrown+'|'+textStatus);
     });
}
function removeporeg(){
     var tray = [];
     $(".checkboxespo:checked").each(function () {
          tray.push($(this).val());
     });
     var traycount =tray.length;
     $('#tblforporeg').html("");
     $.ajax({
          url: "{{ url('/deleteAllporeg') }}",
          method: 'get',
          data:  { 
               tray : tray, 
               traycount : traycount
          },
          
     }).done(function(data, textStatus, jqXHR) {
          console.log(data);
          $.each(data,function(i,val){
               var tblrow = '<tr>'+
                              '<td style="width: 2%" >'+
                                   '<input type="checkbox" class="form-control input-sm checkboxespo" value="'+val.id+'" name="checkitempo" id="checkitempo"></input>'+ 
                              '</td>'+ 
                              '<td style="width: 2%">'+
                                   '<button type="button" name="edit-poreg" class="btn btn-sm btn-primary edit-poreg" value="'+val.id+'">'+
                                        '<i class="fa fa-edit"></i>'+ 
                                   '</button> '+
                              '</td>'+                  
                              '<td>'+val.pono+'</td>'+
                              '<td>'+val.device+'</td>'+
                              '<td>'+val.poqty+'</td>'+    
                         '</tr>';
               $('#tblforporeg').append(tblrow);
               $('#pono').val("");
               $('#poquantity').val("");
               $('#podevice').val(""); 
               $('#poregid').val("");
               $('#poregstatus').val("ADD");
               $('.edit-poreg').click(function(){
                    $('#poregstatus').val("EDIT");
                    var editsearch = $(this).val();
                    $('#poregid').val(editsearch);
                    $.ajax({
                         url: "{{ url('/editporeg') }}",
                         method: 'get',
                         data:  { 
                              editsearch : editsearch, 
                         },
                         
                    }).done(function(data, textStatus, jqXHR) {
                         console.log(data);
                         $('#pono').val(data[0]['pono']);
                         $('#poquantity').val(data[0]['poqty']);
                         $('#podevice').val(data[0]['device']); 
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                         console.log(errorThrown+'|'+textStatus);
                    });
               });
          });
     }).fail(function(jqXHR, textStatus, errorThrown) {
          console.log(errorThrown+'|'+textStatus);
     });
}
function removedevicereg(){
     var tray = [];
     $(".checkboxesdevice:checked").each(function () {
          tray.push($(this).val());
     });
     var traycount =tray.length;
     $('#tblfordevreg').html("");
     $.ajax({
          url: "{{ url('/deleteAlldevicereg') }}",
          method: 'get',
          data:  { 
               tray : tray, 
               traycount : traycount
          },
          
     }).done(function(data, textStatus, jqXHR) {
          console.log(data);
          $.each(data,function(i,val){
               var tblrow = '<tr>'+
                              '<td style="width: 2%">'+
                                   '<input type="checkbox" class="form-control input-sm checkboxesdevice" value="'+val.id+'" name="checkitemdevice" id="checkitemdevice"></input> '+
                              '</td>'+
                              '<td style="width: 2%">'+
                                   '<button type="button" name="edit-devicereg" class="btn btn-sm btn-primary edit-devicereg" value="'+val.id+'">'+
                                        '<i class="fa fa-edit"></i>'+ 
                                   '</button> '+
                              '</td>'+    
                              '<td>'+val.pono+'</td> '+                          
                              '<td>'+val.devicename+'</td>'+
                             ' <td>'+val.family+'</td>'+
                              '<td>'+val.series+'</td>'+
                              '<td>'+val.ptype+'</td>'+
                         '</tr>';
               $('#tblfordevreg').append(tblrow);
               $('#devicepono').val("");
               $('#devicename').val("");
               $('#devicefamily').val("");
               $('#deviceseries').val(""); 
               $('#deviceptype').val("");
               $('#devregid').val("");
               $('#devregstatus').val("ADD");
               $('.edit-devicereg').click(function(){
                    $('#devregstatus').val("EDIT");
                    var editsearch = $(this).val();
                    $('#devregid').val(editsearch);
                    $.ajax({
                         url: "{{ url('/editdevicereg') }}",
                         method: 'get',
                         data:  { 
                              editsearch : editsearch, 
                         },   
                    }).done(function(data, textStatus, jqXHR) {
                         console.log(data);
                         $('#devicepono').val(data[0]['pono']);
                         $('#devicename').val(data[0]['devicename']);
                         $('#devicefamily').select2('val',data[0]['family']);
                         $('#deviceseries').select2('val',data[0]['series']); 
                         $('#deviceptype').select2('val',data[0]['ptype']);
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                         console.log(errorThrown+'|'+textStatus);
                    });
               });
          });
     }).fail(function(jqXHR, textStatus, errorThrown) {
          console.log(errorThrown+'|'+textStatus);
     });
}
function removeseriesreg(){
     var tray = [];
     $(".checkboxesseries:checked").each(function () {
          tray.push($(this).val());
     });
     var traycount =tray.length;
     $('#tblforseries').html("");
     $.ajax({
          url: "{{ url('/deleteAllseriesreg') }}",
          method: 'get',
          data:  { 
               tray : tray, 
               traycount : traycount
          },
          
     }).done(function(data, textStatus, jqXHR) {
          $.each(data,function(i,val){
               var tblrow = '<tr>'+
                              '<td style="width: 2%">'+
                                   '<input type="checkbox" class="form-control input-sm checkboxesseries" value="'+val.id+'" name="checkitemseries" id="checkitemseries"></input>'+ 
                              '</td>'+  
                              '<td style="width: 2%">'+
                                   '<button type="button" name="edit-seriesreg" class="btn btn-sm btn-primary edit-seriesreg" value="'+val.id+'">'+
                                        '<i class="fa fa-edit"></i>'+ 
                                   '</button>'+ 
                              '</td>'+                             
                              '<td>'+val.family+'</td>'+
                              '<td>'+val.series+'</td>'+
                         '</tr>';
               $('#tblforseries').append(tblrow);
               $('#seriesfamily').select2('val',"");
               $('#seriesname').select2('val',"");
               $('#seriesregid').val("");
               $('#seriesregstatus').val("ADD");  
               $('.edit-seriesreg').click(function(){
                    $('#seriesregstatus').val("EDIT");
                    var editsearch = $(this).val();
                    $('#seriesregid').val(editsearch);
                    $.ajax({
                         url: "{{ url('/editseriesreg') }}",
                         method: 'get',
                         data:  { 
                              editsearch : editsearch, 
                         },
                         
                    }).done(function(data, textStatus, jqXHR) {
                         console.log(data);
                         $('#seriesfamily').select2('val',data[0]['family']);
                         $('#seriesname').val(data[0]['series']);  
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                         console.log(errorThrown+'|'+textStatus);
                    });
               });
          });            
     }).fail(function(jqXHR, textStatus, errorThrown) {
          console.log(errorThrown+'|'+textStatus);
     });
}
function removemodreg(){
     var tray = [];
     $(".checkboxesmod:checked").each(function () {
          tray.push($(this).val());
     });
     var traycount =tray.length;
     $('#tblformodreg').html("");
     $.ajax({
          url: "{{ url('/deleteAllmodreg') }}",
          method: 'get',
          data:  { 
               tray : tray, 
               traycount : traycount
          },  
     }).done(function(data, textStatus, jqXHR) {
        $.each(data,function(i,val){
               var tblrow = '<tr>'+
                              '<td style="width: 2%">'+
                                   '<input type="checkbox" class="form-control input-sm checkboxesmod" value="'+val.id+'" name="checkitemmod" id="checkitemmod"></input>'+ 
                              '</td> '+
                              '<td style="width: 2%">'+
                                   '<button type="button" name="edit-modreg" class="btn btn-sm btn-primary edit-modreg" value="'+val.id+'">'+
                                        '<i class="fa fa-edit"></i> '+
                                   '</button>'+ 
                              '</td>'+                              
                              '<td>'+val.family+'</td>'+
                              '<td>'+val.mod+'</td>'+
                         '</tr>';
               $('#tblformodreg').append(tblrow);
               $('#mod').val("");
               $('#modfamily').select2('val',"");
               $('#modregstatus').val("ADD");  
               $('.edit-modreg').click(function(){
                    $('#modregstatus').val("EDIT");
                    var editsearch = $(this).val();
                    $('#modregid').val(editsearch);
                    $.ajax({
                         url: "{{ url('/editmodreg') }}",
                         method: 'get',
                         data:  { 
                              editsearch : editsearch, 
                         },
                         
                    }).done(function(data, textStatus, jqXHR) {
                         console.log(data);
                         $('#mod').val(data[0]['mod']);
                         $('#modfamily').select2('val',data[0]['family']);
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                         console.log(errorThrown+'|'+textStatus);
                    });
               });
          });
     }).fail(function(jqXHR, textStatus, errorThrown) {
          console.log(errorThrown+'|'+textStatus);
     });
}
function removetargetreg(){
     var tray = [];
     $(".checkboxestarget:checked").each(function () {
          tray.push($(this).val());
     });
     var traycount =tray.length;
     $('#tblfortarget').html("");
     $.ajax({
          url: "{{ url('/deleteAlltargetreg') }}",
          method: 'get',
          data:  { 
               tray : tray, 
               traycount : traycount
          },
          
     }).done(function(data, textStatus, jqXHR) {
          $.each(data,function(i,val){
               var tblrow = '<tr>'+
                              '<td style="width: 2%">'+
                                   '<input type="checkbox" class="form-control input-sm checkboxestarget" value="'+val.id+'" name="checkitemtarget" id="checkitemtarget"></input>'+ 
                              '</td>'+ 
                              '<td style="width: 2%">'+
                                   '<button type="button" name="edit-targetreg" class="btn btn-sm btn-primary edit-targetreg" value="'+val.id+'">'+
                                        '<i class="fa fa-edit"></i>'+ 
                                   '</button>'+ 
                              '</td>'+
                              '<td>'+val.datefrom+'</td>'+
                              '<td>'+val.dateto+'</td>'+                              
                              '<td>'+val.yield+'</td>'+
                              '<td>'+val.dppm+'</td>'+
                              '<td>'+val.ptype+'</td>'+
                         '</tr>';
               $('#tblfortarget').append(tblrow);
               $('#target-datefrom').val("");
               $('#target-dateto').val("");
               $('#targetyield').val("");
               $('#targetdppm').val("");
               $('#targetptype').select2('val',"");
               $('#targetstatus').val("ADD");  
               $('.edit-targetreg').click(function(){
                    $('#targetstatus').val("EDIT");
                    var editsearch = $(this).val();
                    $('#targetid').val(editsearch);
                    $.ajax({
                         url: "{{ url('/edittargetreg') }}",
                         method: 'get',
                         data:  { 
                              editsearch : editsearch, 
                         },
                         
                    }).done(function(data, textStatus, jqXHR) {
                         console.log(data);
                         $('#target-datefrom').val(data[0]['datefrom']);
                         $('#target-dateto').val(data[0]['dateto']);
                         $('#targetyield').val(data[0]['yield']);
                         $('#targetdppm').val(data[0]['dppm']); 
                         $('#targetptype').select2('val',data[0]['ptype']); 
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                         console.log(errorThrown+'|'+textStatus);
                    });
               });    
          });
     }).fail(function(jqXHR, textStatus, errorThrown) {
          console.log(errorThrown+'|'+textStatus);
     });
}
function update(){
     var yieldingno = $('input[name=yieldingno2]').val();
     var pono = $('input[name=pono2]').val();
     var poqty = $('input[name=poqty2]').val();
     var device = $('input[name=device2]').val();
     var family = $('#family2').val();
     var series = $('#series2').val();
     var toutput =  $('input[name=toutput2]').val();
     var treject =  $('input[name=treject2]').val();
     var twoyield =  $('input[name=twoyield2]').val();
     var masterid =  $('input[name=masterid]').val();

     var myData ={
                       'pono' : pono
                     ,'poqty' : poqty
                    ,'device' : device
                    ,'family' : family
                    ,'series' : series
                   ,'toutput' : toutput
                   ,'treject' : treject
                  ,'twoyield' : twoyield
                  ,'masterid' : masterid
               };

     $.post("{{ url('/update-yieldsummary') }}",
     { 
          _token: $('meta[name=csrf-token]').attr('content')
          , data: myData
     }).done(function(data, textStatus, jqXHR){
          /*console.log(data);*/
          window.location.href="{{ url('/yieldperformance') }}";
     }).fail(function(jqXHR, textStatus, errorThrown){
          console.log(errorThrown+'|'+textStatus);
     });
}
function summaryREpt(){
     var srdatefrom = $('#srdatefrom').val();
     var srdateto = $('#srdateto').val();
     var srprodtype = $('#srprodtype').val();
     var token = "{{ Session::token() }}";
     var paramfrom = srdatefrom.split("/");
     var paramto = srdateto.split("/");
     var datefrom = paramfrom[2]+'-'+paramfrom[0]+'-'+paramfrom[1];
     var dateto = paramto[2]+'-'+paramto[0]+'-'+paramto[1];
    
     window.location = "{{ url('/summaryREpt') }}" + "?_token=" + token + "&&datefrom=" + datefrom + "&&dateto=" + dateto + "&&srprodtype=" + srprodtype;
}

/*function summaryREptpdf(){
     var srdatefrom = $('#srdatefrom').val();
     var srdateto = $('#srdateto').val();
     var srprodtype = $('#srprodtype').val();
     var token = "{{ Session::token() }}";
     var paramfrom = srdatefrom.split("/");
     var paramto = srdateto.split("/");
     var datefrom = paramfrom[2]+'-'+paramfrom[0]+'-'+paramfrom[1];
     var dateto = paramto[2]+'-'+paramto[0]+'-'+paramto[1];
    
     window.location = "{{ url('/summaryREptpdf') }}" + "?_token=" + token + "&&datefrom=" + datefrom + "&&dateto=" + dateto + "&&srprodtype=" + srprodtype;
}
*/
function defectsummaryRpt(){
     var dsrdatefrom = $('#dsr-datefrom').val();
     var dsrdateto = $('#dsr-dateto').val();
     var ptype = $('#dsr-ptype').val();
     var icsocket = $('#dsr-icsocket').val();
     var fol = $('#dsr-fol').val();
     var option = "";
     if($('#dsr-icsocket').is(':checked')){
          option = icsocket;   
     }
     if($('#dsr-fol').is(':checked')){
          option = fol;
     }
    
     var token = "{{ Session::token() }}";
     var paramfrom = dsrdatefrom.split("/");
     var paramto = dsrdateto.split("/");
     var datefrom = paramfrom[2]+'-'+paramfrom[0]+'-'+paramfrom[1];
     var dateto = paramto[2]+'-'+paramto[0]+'-'+paramto[1];

     window.location = "{{ url('/defectsummaryRpt') }}" + "?_token=" + token + "&&datefrom=" + datefrom + "&&dateto=" + dateto + "&&ptype=" + ptype + "&&option=" + option;
}

/*function defectsummaryRptpdf(){
     var dsrdatefrom = $('#dsr-datefrom').val();
     var dsrdateto = $('#dsr-dateto').val();
     var ptype = $('#dsr-ptype').val();
     var icsocket = $('#dsr-icsocket').val();
     var fol = $('#dsr-fol').val();
     var option = "";
     if($('#dsr-icsocket').is(':checked')){
          option = icsocket;   
     }
     if($('#dsr-fol').is(':checked')){
          option = fol;
     }
    
     var token = "{{ Session::token() }}";
     var paramfrom = dsrdatefrom.split("/");
     var paramto = dsrdateto.split("/");
     var datefrom = paramfrom[2]+'-'+paramfrom[0]+'-'+paramfrom[1];
     var dateto = paramto[2]+'-'+paramto[0]+'-'+paramto[1];

     window.location = "{{ url('/defectsummaryRptpdf') }}" + "?_token=" + token + "&&datefrom=" + datefrom + "&&dateto=" + dateto + "&&ptype=" + ptype + "&&option=" + option;
}
*/
function yieldsumRpt(){
     var prodtype = $('#ypsr-prodtype').val();
     var family = $('#ypsr-family').val();
     var series = $('#ypsr-seriesname').val();
     var device = $('#ypsr-device').val();
     var pono = $('#ypsr-ponumber').val();
     var ypsrdatefrom = $('#ypsr-datefrom').val();
     var ypsrdateto = $('#ypsr-dateto').val();
     var token = "{{ Session::token() }}";
     var paramfrom = ypsrdatefrom.split("/");
     var paramto = ypsrdateto.split("/");
     var datefrom = paramfrom[2]+'-'+paramfrom[0]+'-'+paramfrom[1];
     var dateto = paramto[2]+'-'+paramto[0]+'-'+paramto[1];  

     window.location = "{{ url('/yieldsumRpt') }}" + "?_token=" + token + "&&datefrom=" + datefrom + "&&dateto=" + dateto + "&&prodtype=" + prodtype + "&&family=" + family + "&&series=" + series + "&&device=" + device + "&&pono=" + pono;    
}

function yieldsumRptpdf(){
     var prodtype = $('#ypsr-prodtype').val();
     var family = $('#ypsr-family').val();
     var series = $('#ypsr-seriesname').val();
     var device = $('#ypsr-device').val();
     var pono = $('#ypsr-ponumber').val();
     var ypsrdatefrom = $('#ypsr-datefrom').val();
     var ypsrdateto = $('#ypsr-dateto').val();
     var token = "{{ Session::token() }}";
     var paramfrom = ypsrdatefrom.split("/");
     var paramto = ypsrdateto.split("/");
     var datefrom = paramfrom[2]+'-'+paramfrom[0]+'-'+paramfrom[1];
     var dateto = paramto[2]+'-'+paramto[0]+'-'+paramto[1];  

     window.location = "{{ url('/yieldsumRptpdf') }}" + "?_token=" + token + "&&datefrom=" + datefrom + "&&dateto=" + dateto + "&&prodtype=" + prodtype + "&&family=" + family + "&&series=" + series + "&&device=" + device + "&&pono=" + pono;    
}


function yieldsumfamRpt(){
     
     var ysfdatefrom = $('#ysf-datefrom').val();
     var ysfdateto = $('#ysf-dateto').val();
     var yieldtarget = $('#ysf-yieldtarget').val();
     var checkboxicsocket = $('#ysf-icsocket').val();
     var ptype = $('#ysf-ptype').val();
    
     var checkboxfol = $('#ysf-fol').val();
     var token = "{{ Session::token() }}";
     var paramfrom = ysfdatefrom.split("/");
     var paramto = ysfdateto.split("/");
     var datefrom = paramfrom[2]+'-'+paramfrom[0]+'-'+paramfrom[1];
     var dateto = paramto[2]+'-'+paramto[0]+'-'+paramto[1];  
     var chosen = $('input[name=chose]').val();

     window.location = "{{ url('/yieldsumfamRpt') }}" + "?_token=" + token + "&&datefrom=" + datefrom + "&&dateto=" + dateto + "&&chosen=" + chose + "&&yieldtarget=" + yieldtarget + "&&ptype=" + ptype;

     // $.ajax({
     //      url: "{{ url('/yieldsumRptpdf') }}",
     //      method: 'get',
     //      data:  {
     //           yieldtarget:yieldtarget     
     //      },  
     // }).done(function(data, textStatus, jqXHR) {
     //      //alert(data);
     // }).fail(function(jqXHR, textStatus, errorThrown) {
     //      console.log(errorThrown+'|'+textStatus);
     // });
}

function yieldsumfamRptpdf(){
     
     var ysfdatefrom = $('#ysf-datefrom').val();
     var ysfdateto = $('#ysf-dateto').val();
     var yieldtarget = $('#ysf-yieldtarget').val();
     var checkboxicsocket = $('#ysf-icsocket').val();

    
     var checkboxfol = $('#ysf-fol').val();
     var token = "{{ Session::token() }}";
     var paramfrom = ysfdatefrom.split("/");
     var paramto = ysfdateto.split("/");
     var datefrom = paramfrom[2]+'-'+paramfrom[0]+'-'+paramfrom[1];
     var dateto = paramto[2]+'-'+paramto[0]+'-'+paramto[1];  
     var chosen = $('input[name=chose]').val();

     window.location = "{{ url('/yieldsumfamRptpdf') }}" + "?_token=" + token + "&&datefrom=" + datefrom + "&&dateto=" + dateto + "&&chosen=" + chose;

     // $.ajax({
     //      url: "{{ url('/yieldsumRptpdf') }}",
     //      method: 'get',
     //      data:  {
     //           yieldtarget:yieldtarget     
     //      },  
     // }).done(function(data, textStatus, jqXHR) {
     //      //alert(data);
     // }).fail(function(jqXHR, textStatus, errorThrown) {
     //      console.log(errorThrown+'|'+textStatus);
     // });
}

function DatePickers(){
     $( "#datefroms").datepicker();
     $( "#datetos").datepicker();
     $( "#dsr-datefrom" ).datepicker();
     $( "#dsr-dateto" ).datepicker();
     $( "#srdatefrom" ).datepicker();
     $( "#srdateto" ).datepicker();
     $( "#ypsr-datefrom" ).datepicker();
     $( "#ypsr-dateto" ).datepicker();
     $( "#ysf-datefrom" ).datepicker();
     $( "#ysf-dateto" ).datepicker();
     $('#target-datefrom').datepicker();
     $('#target-dateto').datepicker();

     $('#datefroms').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#datetos').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#dsr-datefrom').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#dsr-dateto').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#srdatefrom').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#srdateto').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#ypsr-datefrom').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#ypsr-dateto').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#ysf-datefrom').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#ysf-dateto').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#target-datefrom').on('change',function(){
          $(this).datepicker('hide');
     });
     $('#target-dateto').on('change',function(){
          $(this).datepicker('hide');
     });
}
function ButtonsClicked(){
     $('#btnaddnew').click(function(){
          window.location.href = "{{ url('/addnewYieldperformance') }}";
     });

     $('#btnmAndr').click(function(){
          $('#mAndr-Modal').modal('show');
     });

     $('#btn_poreg').click(function(){
          $('#poreg_Modal').modal('show');
          $('#mAndr-Modal').modal('hide');
          $('.poreg_modal-title').html("Purchase Order Registration");
          $('#poregstatus').val("ADD");
          $('#pono').val("");
          $('#podevice').val("");
          $('#poquantity').val("");
          $('#poregid').val("");

          $('#pono').keyup(function(){
               if(this.value == ''){
                    $('#poquantity').val(""); 
                    $('#podevice').val("");
               }
          });
     });
    

     $('#btn_devicereg').click(function(){
          $('#devicereg_Modal').modal('show');
          $('#mAndr-Modal').modal('hide');
          $('.devicereg_modal-title').html("Device Registration");
          $('#devregstatus').val("ADD");
          $('#devicepono').val("");
          $('#devicename').val("");
          $('#devicefamily').select2('val',"");
          $('#deviceseries').select2('val',"");
          $('#deviceptype').select2('val',""); 
          $('#devregid').val(""); 
     });
     $('#btn_seriesreg').click(function(){
          $('#seriesreg_Modal').modal('show');
          $('#mAndr-Modal').modal('hide');
          $('.seriesreg_modal-title').html("Series Registration");
          $('#seriesregstatus').val("ADD");
          $('#seriesfamily').val("");
          $('#seriesname').val("");
          $('#seriesregid').val("");
     });

     $('#btn_modreg').click(function(){
          $('#modreg_Modal').modal('show');
          $('#mAndr-Modal').modal('hide');
          $('.modreg_modal-title').html("Mode of Defect Registration");
          $('#modregstatus').val("ADD");
          $('#mod').val("");
          $('#modfamily').val("");
          $('#modregid').val("");
     });
     $('#btn_target').click(function(){
          $('#targetreg_Modal').modal('show');
          $('#mAndr-Modal').modal('hide');
          $('.targetreg_modal-title').html("Target Yield Registration");
          $('#targetstatus').val("ADD");
          $('#target-datefrom').val("");
          $('#target-dateto').val("");
          $('#targetyield').val("");
          $('#targetdppm').val("");
          $('#targetid').val("");
     });
  
     $('#btnxport-summaryrpt').click(function(){
          $('#summaryrpt_Modal').modal('show');
          $('#Export-Modal').modal('hide');
          $('.summaryrpt-title').html("Summary Report");
     });
     $('#btnxport-defectsummaryrpt').click(function(){
          $('#defectsummaryrpt_Modal').modal('show');
          $('#Export-Modal').modal('hide');
          $('.defectsummaryrpt-title').html("Defect Summary Report");
     });
     $('#btnxport-yieldpsrpt').click(function(){
          $('#yieldpsrpt_Modal').modal('show');
          $('#Export-Modal').modal('hide');
          $('.yieldpsrpt-title').html("Yield Performance Summary Report");
     });
     $('#btnxport-yieldsfrpt').click(function(){
          $('#yieldsfrpt_Modal').modal('show');
          $('#Export-Modal').modal('hide');
          $('.yieldsfrpt-title').html("Yield Summary Report");
     });
     $('#btnxport').click(function(){
          $('#Export-Modal').modal('show');
     });
     $('#btnxport-defectsummaryrpt').click(function(){
          var icsocket = $('#dsr-icsocket').val();
          var fol = $('#dsr-fol').val();
         
          $('#dsr-icsocket').change(function(){
               if($('#dsr-icsocket').is(':checked')){
                    $('input[name=dsr-fol]').parents('span').removeClass("checked");
                    $('input[name=dsr-fol]').prop('checked',false);    
               }
          });
          $('#dsr-fol').change(function(){
               if($('#dsr-fol').is(':checked')){
                    $('input[name=dsr-icsocket]').parents('span').removeClass("checked");
                    $('input[name=dsr-icsocket]').prop('checked',false);
               }
          });
         
     });
}
function FieldsValidations(){
    
     $('#pono').keyup(function(){
        $('#er_pono').html(""); 
     });
     $('#podevice').keyup(function(){
        $('#er_podevice').html(""); 
     });
     $('#poquantity').keyup(function(){
        $('#er_poquantity').html(""); 
     });
     //DEVICE Registration Inputs Validations-------------------
     $('#devicepono').keyup(function(){
        $('#er_devicepono').html(""); 
     });
     $('#devicename').keyup(function(){
        $('#er_devicename').html(""); 
     });
     $('#devicefamily').click(function(){
        $('#er_devicefamily').html(""); 
     });
     $('#deviceseries').click(function(){
        $('#er_deviceseries').html(""); 
     });
     $('#deviceptype').click(function(){
        $('#er_deviceptype').html(""); 
     });
     //SERIES Registration Inputs Validations-------------------
     $('#seriesfamily').click(function(){
        $('#er_seriesfamily').html(""); 
     });
     $('#seriesname').click(function(){
        $('#er_seriesname').html(""); 
     });
     //MODE OF DEFECTS Registration Inputs Validations-------------------
     $('#mod').click(function(){
        $('#er_mod').html(""); 
     });
     $('#modfamily').click(function(){
        $('#er_modfamily').html(""); 
     });
     //TARGET YIELD Registration Inputs Validations-------------------
     $('#target-datefrom').click(function(){
        $('#er_target-datefrom').html(""); 
     });
     $('#target-dateto').click(function(){
        $('#er_target-dateto').html(""); 
     });
     $('#targetyield').keyup(function(){
        $('#er_targetyield').html(""); 
     });
     $('#targetdppm').keyup(function(){
        $('#er_targetdppm').html(""); 
     });
}
function ButtonsClear(){
     $('#btnporegclear').click(function(){
          $('#hdporeg').val("ADD");
          $('#pono').val("");
          $('#podevice').val("");
          $('#poquantity').val("");
          $('#poregid').val("");
     });
     $('#btndeviceregclear').click(function(){
          $('#devregstatus').val("ADD");
          $('#devicepono').val("");
          $('#deviceptype').select2('val',"");
          $('#devicename').val("");
          $('#devicefamily').select2('val',"");
          $('#deviceseries').select2('val',"");
          $('#devregid').val("");
     });
     $('#btnseriesregclear').click(function(){
          $('#hdseriesreg').val("ADD");
          $('#seriesfamily').select2('val',"");
          $('#seriesname').val("");
          $('#seriesregid').val("");
     });
     $('#btnmodregclear').click(function(){
          $('#hdmodreg').val("ADD");
          $('#mod').val("");
          $('#modfamily').select2('val',"");
          $('#modregid').val("");
     });
     $('#btntargetregclear').click(function(){
          $('#hdmodreg').val("ADD");
          $('#target-datefrom').val("");
          $('#target-dateto').val("");
          $('#targetyield').val("");
          $('#targetdppm').val("");
          $('#targetid').val("");
     });
}
function Checkboxes(){
     $('.checkAllitems').change(function(){
          if($('.checkAllitems').is(':checked')){
               $('.deleteAll-task').removeClass("disabled");
               $('input[name=checkitem]').parents('span').addClass("checked");
               $('input[name=checkitem]').prop('checked',this.checked);
               $('.edit-task').addClass("disabled");
               
          }else{
               $('input[name=checkitem]').parents('span').removeClass("checked");
               $('input[name=checkitem]').prop('checked',this.checked);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
               $('.edit-task').addClass("disabled");
          }         
     });

     $('.checkboxes').change(function(){
          $('input[name=checkAllitem]').parents('span').removeClass("checked");
          $('input[name=checkAllitem]').prop('checked',false);
          var tray = [];
          $(".checkboxes:checked").each(function () {
               tray.push($(this).val());
               $('.checkAllitems').prop('checked',false)
          
          });
          
          if($('.checkboxes').is(':checked')){
               $('.deleteAll-task').removeClass("disabled");
               $('#add').addClass("disabled");
          }else{
               $('input[name=checkAllitem]').parents('span').removeClass("checked");
               $('input[name=checkAllitem]').prop('checked',false);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
          }
     
     });
     $('.checkAllitemspo').change(function(){
          if($('.checkAllitemspo').is(':checked')){
               $('input[name=checkitempo]').parents('span').addClass("checked");
               $('input[name=checkitempo]').prop('checked',this.checked);
               
          }else{
               $('input[name=checkitempo]').parents('span').removeClass("checked");
               $('input[name=checkitempo]').prop('checked',this.checked);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
               $('.edit-task').addClass("disabled");
          }         
     });

     $('.checkboxespo').change(function(){
          $('input[name=checkAllitempo]').parents('span').removeClass("checked");
          $('input[name=checkAllitempo]').prop('checked',false);
          var tray = [];
          $(".checkboxespo:checked").each(function () {
               tray.push($(this).val());
               $('.checkAllitemspo').prop('checked',false)
          });
          
          if($('.checkboxespo').is(':checked')){
               $('.deleteAll-task').removeClass("disabled");
               $('#add').addClass("disabled");
          }else{
               $('input[name=checkAllitempo]').parents('span').removeClass("checked");
               $('input[name=checkAllitempo]').prop('checked',false);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
          }
     
     });
     $('.checkAllitemsdevice').change(function(){
          if($('.checkAllitemsdevice').is(':checked')){
               $('input[name=checkitemdevice]').parents('span').addClass("checked");
               $('input[name=checkitemdevice]').prop('checked',this.checked);
               
          }else{
               $('input[name=checkitemdevice]').parents('span').removeClass("checked");
               $('input[name=checkitemdevice]').prop('checked',this.checked);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
               $('.edit-task').addClass("disabled");
          }         
     });

     $('.checkboxesdevice').change(function(){
          $('input[name=checkAllitemdevice]').parents('span').removeClass("checked");
          $('input[name=checkAllitemdevice]').prop('checked',false);
          var tray = [];
          $(".checkboxesdevice:checked").each(function () {
               tray.push($(this).val());
               $('.checkAllitemsdevice').prop('checked',false)
          
          });
          
          if($('.checkboxesdevice').is(':checked')){
               $('.deleteAll-task').removeClass("disabled");
               $('#add').addClass("disabled");
          }else{
               $('input[name=checkAllitemdevice]').parents('span').removeClass("checked");
               $('input[name=checkAllitemdevice]').prop('checked',false);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
          }    
     });

     $('.checkAllitemsseries').change(function(){
          if($('.checkAllitemsseries').is(':checked')){
               $('input[name=checkitemseries]').parents('span').addClass("checked");
               $('input[name=checkitemseries]').prop('checked',this.checked);
               
          }else{
               $('input[name=checkitemseries]').parents('span').removeClass("checked");
               $('input[name=checkitemseries]').prop('checked',this.checked);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
               $('.edit-task').addClass("disabled");
          }         
     });

     $('.checkboxesseries').change(function(){
          $('input[name=checkAllitemseries]').parents('span').removeClass("checked");
          $('input[name=checkAllitemseries]').prop('checked',false);
          var tray = [];
          $(".checkboxesseries:checked").each(function () {
               tray.push($(this).val());
               $('.checkAllitemsseries').prop('checked',false)
          
          });
          
          if($('.checkboxesseries').is(':checked')){
               $('.deleteAll-task').removeClass("disabled");
               $('#add').addClass("disabled");
          }else{
               $('input[name=checkAllitemseries]').parents('span').removeClass("checked");
               $('input[name=checkAllitemseries]').prop('checked',false);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
          }    
     });

     $('.checkAllitemsmod').change(function(){
          if($('.checkAllitemsmod').is(':checked')){
               $('input[name=checkitemmod]').parents('span').addClass("checked");
               $('input[name=checkitemmod]').prop('checked',this.checked);
               
          }else{
               $('input[name=checkitemmod]').parents('span').removeClass("checked");
               $('input[name=checkitemmod]').prop('checked',this.checked);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
               $('.edit-task').addClass("disabled");
          }         
     });

     $('.checkboxesmod').change(function(){
          $('input[name=checkAllitemmod]').parents('span').removeClass("checked");
          $('input[name=checkAllitemmod]').prop('checked',false);
          var tray = [];
          $(".checkboxesmod:checked").each(function () {
               tray.push($(this).val());
               $('.checkAllitemsmod').prop('checked',false)
          
          });
          
          if($('.checkboxesmod').is(':checked')){
               $('.deleteAll-task').removeClass("disabled");
               $('#add').addClass("disabled");
          }else{
               $('input[name=checkAllitemmod]').parents('span').removeClass("checked");
               $('input[name=checkAllitemmod]').prop('checked',false);
               $('.deleteAll-task').addClass("disabled");
               $('#add').removeClass("disabled");
          }    
     });
}
function EditButtons(){
     $('.edit-task').on('click', function(e) {
          var edittext = $(this).val().split('|');
          var editid = edittext[0];
          var pono = edittext[1];
          var poqty = edittext[2];
          var device = edittext[3];
          var series = edittext[4];
          var family = edittext[5];
          var toutput = edittext[6];
          var treject = edittext[7];
          var twoyield = edittext[8];

          $('#masterid').val(editid);
          $('.updatetitle').html('Update Yielding Summary');
          $('#updateyield_Modal').modal('show');
          $('#pono2').val(pono);
          $('#poqty2').val(poqty);
          $('#device2').val(device);
          $('#series2').val(series);
          $('#family2').val(family);
          $('#toutput2').val(toutput);
          $('#treject2').val(treject);
          $('#twoyield2').val(twoyield);
          $('#masterid').val(editid);        

          $('#name').keyup(function(){
             $('#er1').html(""); 
          });
          $('#desc').keyup(function(){
             $('#er2').html(""); 
          });
          $('#val').keyup(function(){
             $('#er3').html(""); 
          });
     });

     $('.edit-poreg').click(function(){
          $('#poregstatus').val("EDIT");
          var editsearch = $(this).val();
          $('#poregid').val(editsearch);
          $.ajax({
               url: "{{ url('/editporeg') }}",
               method: 'get',
               data:  { 
                    editsearch : editsearch, 
               },
               
          }).done(function(data, textStatus, jqXHR) {
               $('#pono').val(data[0]['pono']);
               $('#poquantity').val(data[0]['poqty']);
               $('#podevice').val(data[0]['device']); 
          }).fail(function(jqXHR, textStatus, errorThrown) {
               console.log(errorThrown+'|'+textStatus);
          });
     });

     $('.edit-devicereg').click(function(){
          $('#devregstatus').val("EDIT");
          var editsearch = $(this).val();
          $('input[name=devregid]').val(editsearch);
          $.ajax({
               url: "{{ url('/editdevicereg') }}",
               method: 'get',
               data:  { 
                    editsearch : editsearch, 
               },
               
          }).done(function(data, textStatus, jqXHR) {
               $('#devicepono').val(data[0]['pono']);
               $('#devicename').val(data[0]['devicename']);
               $('#devicefamily').select2('val',data[0]['family']);
               $('#deviceseries').select2('val',data[0]['series']); 
               $('#deviceptype').select2('val',data[0]['ptype']);
          }).fail(function(jqXHR, textStatus, errorThrown) {
               console.log(errorThrown+'|'+textStatus);
          });
     });

     $('.edit-seriesreg').click(function(){
          $('#seriesregstatus').val("EDIT");
          var editsearch = $(this).val();
          $('#seriesregid').val(editsearch);
          $.ajax({
               url: "{{ url('/editseriesreg') }}",
               method: 'get',
               data:  { 
                    editsearch : editsearch, 
               },
               
          }).done(function(data, textStatus, jqXHR) {
               $('#seriesfamily').select2('val',data[0]['family']);
               $('#seriesname').val(data[0]['series']);
              
          }).fail(function(jqXHR, textStatus, errorThrown) {
               console.log(errorThrown+'|'+textStatus);
          });
     });

     $('.edit-modreg').click(function(){
          $('#modregstatus').val("EDIT");
          var editsearch = $(this).val();
          $('#modregid').val(editsearch);
          $.ajax({
               url: "{{ url('/editmodreg') }}",
               method: 'get',
               data:  { 
                    editsearch : editsearch, 
               },
               
          }).done(function(data, textStatus, jqXHR) {
               $('#mod').val(data[0]['mod']);
               $('#modfamily').select2('val',data[0]['family']);
          }).fail(function(jqXHR, textStatus, errorThrown) {
               console.log(errorThrown+'|'+textStatus);
          });
     });

     $('.edit-targetreg').click(function(){
          $('#targetstatus').val("EDIT");
          var editsearch = $(this).val();
          $('#targetid').val(editsearch);
          $.ajax({
               url: "{{ url('/edittargetreg') }}",
               method: 'get',
               data:  { 
                    editsearch : editsearch, 
               },
               
          }).done(function(data, textStatus, jqXHR) {
               console.log(data);
               $('#target-datefrom').val(data[0]['datefrom']);
               $('#target-dateto').val(data[0]['dateto']);
               $('#targetyield').val(data[0]['yield']);
               $('#targetdppm').val(data[0]['dppm']);
               $('#targetptype').select2('val',data[0]['ptype']); 
          }).fail(function(jqXHR, textStatus, errorThrown) {
               console.log(errorThrown+'|'+textStatus);
          });
     });
}

function ButtonClosed(){
     $('#btn_poclose').click(function(){
          window.location.href = "{{ url('/yieldperformance') }}";
     });
     $('#btn_devclose').click(function(){
          window.location.href = "{{ url('/yieldperformance') }}";
     });
     $('#btn_seriesclose').click(function(){
          window.location.href = "{{ url('/yieldperformance') }}";
     });
     $('#btn_modclose').click(function(){
          window.location.href = "{{ url('/yieldperformance') }}";
     });
     $('#btn_targetclose').click(function(){
          window.location.href = "{{ url('/yieldperformance') }}";
     });
}
</script>
@endpush