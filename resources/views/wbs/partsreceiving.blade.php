@extends('layouts.master')

<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script language="javascript" type="text/javascript">

          /*
          * Material Receiving START
          */
          $( document ).ready(function(e)
          {
               $("#receivingno").keyup(function(event){
                    var mat = $('#receivingno').val();
                    if(event.keyCode == 13)
                    {
                         window.location.href= "{{ url('/wbspartsreceiving?page=') }}" + 'PR&id=' + mat;
                    }
               });

               var queryString = new Array;
               var query = location.search.substr(1);
               var result = {};

               query.split("&").forEach(function(part)
               {
                    var item = part.split("=");

                    if(decodeURIComponent(item[0])=='action' && (decodeURIComponent(item[1])=='ADD' || decodeURIComponent(item[1])=='EDIT'))
                    {
                         var rowCount = $('#sample_2 tr').length;
                         if(rowCount >= 2)
                         {
                              $("#validShipModal").modal("show");
                         }
                         else
                         {
                              $.alert('Please input valid and existing Ship No.',
                              {
                                   position  : ['center', [-0.40, 0]],
                                   type      : 'error',
                                   closeTime : 2000,
                                   autoClose : true,
                                   id        :'alert_suc'
                              });
                         }
                    }
               });

               $('#receivingdate').datepicker({
                       "setDate": new Date(),
                       "autoclose": true
               });

               $('#add_inputItemNo').on('change', function()
               {
                    var values = $(this).val().split('|');

                    if(values.length = 3)
                    {
                         $("#add_inputLocation").val(values[2]);
                    }
               });

               $('#edit_inputItemNo').on('change', function()
               {
                    var values = $(this).val().split('|');

                    if(values.length = 3)
                    {
                         $("#edit_inputLocation").val(values[2]);
                    }
               });
          });

          function getrecord(val)
          {
               var id = 0;
               switch(val)
               {
                 case ('MIN'):
                 id = 1;
                 break;
                 case ('PRV'):
                 id = parseInt($('#recid').val());
                 break;
                 case ('NXT'):
                 id = parseInt($('#recid').val());
                 break;
                 case ('MAX'):
                 id = -1;
                 break;
                 case ('INV'):
                 id = 0;
                 break;
                 default:
                 id = 1;
                 break;
            }
            window.location.href= "{{ url('/wbspartsreceiving?page=') }}" + val + '&id=' + id;
       }

       function saverecord()
       {
          var pr_arr = new Array;
          var obj_data = new Object;
          var itemno_arr = new Array;
          var itemdesc_arr = new Array;
          var qty_arr = new Array;
          var boxqty_arr = new Array;
          var lotno_arr = new Array;
          var location_arr = new Array;
          var cnt = 0;
          var ctr = 0;
          var is_valid = true;
          var action = $("#action").val();

          var detailsUpdateflag  = $("#detailsUpdateflag").val();
          var batchUpdateflag  = $("#batchUpdateflag").val();

          pr_arr[0] = $('#receivingno').val();
          pr_arr[1] = $("#receivingdate").val();
          pr_arr[2] = $("#shipno").val();
          pr_arr[3] = $("#palletno").val();
          pr_arr[4] = $("#totalqty").val();
          pr_arr[5] = $("#status").val();
          pr_arr[6] = $("#createdby").val();
          pr_arr[7] = $("#createddate").val();
          pr_arr[8] = $("#updatedby").val();
          pr_arr[9] = $("#updateddate").val();

          if($.trim(pr_arr[1]) == ''
               || $.trim(pr_arr[2]) == ''
               || $.trim(pr_arr[3]) == '')
          {
               is_valid = false;
          }

          if($("#shipno").val() != $("#hdnshipno").val())
          {
               is_valid = false;
          }

               cnt = 0;
               $(".inputItemNo").each(function()
               {
                    var id = $(this).attr('name');
                    obj_data[id] = $(this).text();
                    itemno_arr[cnt] = $.trim(obj_data[id]);
                    cnt++;
               });

               cnt = 0;
               $(".inputQty").each(function()
               {
                    var id = $(this).attr('name');
                    obj_data[id] = $(this).text();
                    if(parseFloat(obj_data[id]))
                    {
                         qty_arr[cnt] = parseFloat($.trim(obj_data[id]));
                    }
                    else
                    {
                         is_valid = false;
                    }

                    cnt++;
               });

               cnt = 0;
               $(".inputBoxQty").each(function()
               {
                    var id = $(this).attr('name');
                    obj_data[id] = $(this).text();
                    if(parseFloat(obj_data[id]))
                    {
                         boxqty_arr[cnt] = parseFloat($.trim(obj_data[id]));
                    }
                    else
                    {
                         is_valid = false;
                    }

                    cnt++;
               });

               cnt = 0;
               $(".inputLotNo").each(function()
               {
                    var id = $(this).attr('name');
                    obj_data[id] = $(this).text();
                    lotno_arr[cnt] = $.trim(obj_data[id]);
                    cnt++;
               });

               cnt = 0;
               $(".inputLocation").each(function()
               {
                    var id = $(this).attr('name');
                    obj_data[id] = $(this).text();
                    location_arr[cnt] = $.trim(obj_data[id]);
                    cnt++;
               });

               if(is_valid)
               {
                    $('#loading').modal('toggle');
                    switch(action)
                    {
                         case ('ADD'):
                              // alert('add');

                              $.post("{{ url('/wbspat-save') }}",
                              {
                                   _token              : $('meta[name=csrf-token]').attr('content')
                                   , pr_arr            : pr_arr
                                   , itemno_arr        : itemno_arr
                                   , itemdesc_arr      : itemdesc_arr
                                   , qty_arr           : qty_arr
                                   , boxqty_arr        : boxqty_arr
                                   , lotno_arr         : lotno_arr
                                   , location_arr      : location_arr
                                   , detailsUpdateflag : detailsUpdateflag
                                   , batchUpdateflag   : batchUpdateflag
                              })
                              .done(function(data)
                              {
                                   // alert(data);
                                   $('#loading').modal('toggle');
                                   $.alert('Parts Receiving Added Successfully.',
                                   {
                                        position  : ['center', [-0.40, 0]],
                                        type      : 'success',
                                        closeTime : 2000,
                                        autoClose : true,
                                        id        :'alert_suc'
                                   });
                                   window.location.href= "{{ url('/wbspartsreceiving?page=') }}" + 'MAX&id=-1';
                              })
                              .fail(function()
                              {
                                   $('#loading').modal('toggle');
                                   alert('fail');
                              });

                              break;

                              case('EDIT'):
                              $.post("{{ url('/wbspat-update') }}",
                              {
                                   _token              : $('meta[name=csrf-token]').attr('content')
                                   , pr_arr            : pr_arr
                                   , itemno_arr        : itemno_arr
                                   , itemdesc_arr      : itemdesc_arr
                                   , qty_arr           : qty_arr
                                   , boxqty_arr        : boxqty_arr
                                   , lotno_arr         : lotno_arr
                                   , location_arr      : location_arr
                                   , detailsUpdateflag : detailsUpdateflag
                                   , batchUpdateflag   : batchUpdateflag
                              })
                              .done(function(data)
                              {
                                   $('#loading').modal('toggle');
                                   // alert(data);
                                   $.alert('Parts Receiving Updated Successfully.',
                                   {
                                        position  : ['center', [-0.40, 0]],
                                        type      : 'success',
                                        closeTime : 2000,
                                        autoClose : true,
                                        id        :'alert_suc'
                                   });
                                   window.location.href= "{{ url('/wbspartsreceiving?page=') }}" + 'CUR&id=' + $('#recid').val();
                              })
                              .fail(function()
                              {
                                   $('#loading').modal('toggle');
                                   alert('fail');
                              });

                              break;

                              default:
                              // alert(action);

                              break;
                         }
                    }
                    else
                    {
                         $.alert('Something is wrong with the input data. All fields are required. Please check and try again.',
                         {
                          position  : ['center', [-0.40, 0]],
                          type      : 'error',
                          closeTime : 2000,
                          autoClose : true,
                          id        :'alert_suc'
                     });
                    }
               }

               function setcontrol(action, item)
               {
               // $('#loading').modal('toggle');
               switch(action)
               {
                    case ('ADD'):
                    $("#receivingno").prop("disabled", true);
                    $("#btn_min").prop("disabled", true);
                    $("#btn_prv").prop("disabled", true);
                    $("#btn_nxt").prop("disabled", true);
                    $("#btn_max").prop("disabled", true);

                    $("#btn_edit").hide();
                    $("#btn_add").hide();
                    $("#btn_search").hide();
                    $("#btn_cancel").hide();
                    $("#btn_barcode").hide();
                    $("#btn_print").hide();

                    $("#shipno").removeAttr('disabled');
                    $("#btn_checkinv").removeAttr('disabled');
                    $("#palletno").removeAttr('disabled');
                    $("#receivingdate").removeAttr('disabled');

                    $("#btn_save").show();
                    $("#btn_discard").show();
                    $("#btn_add_batch").show();
                    $("#btn_delete_batch").show();
                    $("#btn_update_batch").show();

                         // Set header values to empty.
                         $("#receivingno").val("");
                         $("#receivingdate").datepicker("setDate", new Date());
                         // $("#receivingdate").val("");
                         $("#shipno").val("");
                         $("#palletno").val("");
                         $("#totalqty").val("");
                         $("#status").val("");
                         $("#createdby").val("");
                         $("#createddate").val("");
                         $("#updatedby").val("");
                         $("#updateddate").val("");
                         $("#action").val("ADD");

                         var table = $('#sample_2').DataTable();
                         table
                         .clear()
                         .draw();
                         var table = $('#sample_3').DataTable();
                         table
                         .clear()
                         .draw();
                         var table = $('#tbl_batch').DataTable();
                         table
                         .clear()
                         .draw();

                      // document.getElementById('shipno').setAttribute("disabled","disabled");
                      break;

                      case ('EDIT'):

                      $("#receivingno").prop("disabled", true);
                      $("#btn_min").prop("disabled", true);
                      $("#btn_prv").prop("disabled", true);
                      $("#btn_nxt").prop("disabled", true);
                      $("#btn_max").prop("disabled", true);

                      $("#btn_edit").hide();
                      $("#btn_add").hide();
                      $("#btn_search").hide();
                      $("#btn_cancel").hide();
                      $("#btn_barcode").hide();
                      $("#btn_print").hide();

                      $("#shipno").removeAttr('disabled');
                      $("#btn_checkinv").removeAttr('disabled');
                      $("#palletno").removeAttr('disabled');
                      $("#receivingdate").removeAttr('disabled');

                      $("#btn_save").show();
                      $("#btn_discard").show();
                      $("#btn_add_batch").show();
                      $("#btn_delete_batch").show();
                      $("#btn_update_batch").show();

                      $("#action").val("EDIT");
                      break;
                      case ('CNL'):

                      if($("#status").val() == 'Cancelled')
                      {
                         $.alert('This transaction is already Cancelled.',
                         {
                              position  : ['center', [-0.40, 0]],
                              type      : 'error',
                              closeTime : 2000,
                              autoClose : true,
                              id        :'alert_suc'
                         });
                    }
                    else
                    {
                         $("#deleteModal").modal("show");
                         $('#delete_inputId').val($("#recid").val());
                    }
                    break;
                    case ('PRNT'):

                         var values = item.split('|');
                         var item = values[0];
                         var isprinted = values[1];
                         $("#barcodeModal").modal("show");
                         $('#barcode_inputId').val($("#recid").val());
                         $('#barcode_inputItemNo').val(item);

                         break;

                         case ('DIS'):
                         window.location.href= "{{ url('/wbspartsreceiving?page=') }}" + 'MIN&id=1';
                         //window.location.reload();
                         break;
                         default:
                         $("#btn_cancel").removeAttr('disabled');
                         $("#btn_barcode").removeAttr('disabled');
                         $("#btn_print").removeAttr('disabled');

                         $("#shipno").prop("disabled", true);
                         $("#btn_checkinv").prop("disabled", true);
                         $("#palletno").prop("disabled", true);
                         $("#receivingdate").prop("disabled", true);
                         $("#receivingno").prop("disabled", true);
                         $("#btn_save").prop("disabled", true);
                         $("#btn_discard").prop("disabled", true);
                         $("#btn_add_batch").prop("disabled", true);
                         $("#btn_delete_batch").prop("disabled", true);
                         // $("#action").val("VIEW");
                         // document.getElementById('shipno').removeAttribute("disabled");
                         break;
                    }
               // $('#loading').modal('toggle');
          }

          function batchdata(action)
          {
               var is_valid = true;

               if(action == "ADD")
               {
                    if($.trim($("#add_inputItemNo").val()) == '-1'
                         || $.trim($("#add_inputQty").val()) == ''
                         || $.trim($("#add_inputBoxQty").val()) == ''
                         || $.trim($("#add_inputLotNo").val()) == '')
                    {
                         is_valid = false;
                    }

                    if(parseFloat($("#add_inputQty").val()))
                    {
                         $("#add_inputQty").val(parseFloat($.trim($("#add_inputQty").val())));
                    }
                    else
                    {
                         is_valid = false;
                    }

                    if(parseFloat($("#add_inputBoxQty").val()))
                    {
                         $("#add_inputQty").val(parseFloat($.trim($("#add_inputQty").val())));
                    }
                    else
                    {
                         is_valid = false;
                    }

                    if(is_valid)
                    {
                         var values = $("#add_inputItemNo").val().split('|');
                         var item = values[0];
                         var desc = values[1];

                         var count = parseInt($('#item_count').val()) + 1;
                         var newItem = '<tr id="tr_batch_item' + count + '">'
                         +'<td><input type="checkbox" class="checkboxes batch_item'+count+'" value="'+count+'"/></td>'
                         +'<td class="batch_item'+count+' inputBatchId"><a href="#" onclick="editBatch('+count+')" value="sitem'+count+'"><i class="fa fa-edit"></i></a></td>'
                         +'<td>'+count+'</td>'
                         +'<td class="batch_item'+count+' inputItemNo" name="inputItemNo">'+ item +'</td>'
                         +'<td class="batch_item'+count+' inputItem" name="inputItem">'+ desc +'</td>'
                         +'<td class="batch_item'+count+' inputQty" name="inputQty">'+ $("#add_inputQty").val() +'</td>'
                         +'<td class="batch_item'+count+' inputBoxQty" name="inputBoxQty">'+ $("#add_inputBoxQty").val() +'</td>'
                         +'<td class="batch_item'+count+' inputLotNo" name="inputLotNo">'+ $("#add_inputLotNo").val() +'</td>'
                         +'<td class="batch_item'+count+' inputLocation" name="inputLocation">'+ $("#add_inputLocation").val() +'</td>'
                         +'<td class="batch_item'+count+' inputIsPrinted" name="inputIsPrinted"><input type="checkbox" class="isprinted" disabled /></td>'
                         +'<td><button type="button" class="btn grey-gallery input-sm" id="btn_sitemprint" disabled ><i class="fa fa-barcode"></i></button></td>'
                         +'</tr>';
                         $('#table_body').append(newItem);
                         $('#item_count').val(count);

                         $('#addbatchitem').modal('toggle');
                         $("#add_inputItemNo").val("");
                         $("#add_inputQty").val("");
                         $("#add_inputBoxQty").val("");
                         $("#add_inputLotNo").val("");
                         $("#add_inputLocation").val("");
                         $("#batchUpdateflag").val("1");
                    }
                    else
                    {
                         $('#addbatchitem').modal('toggle');
                         $("#invalidAddBatchModal").modal().shown();
                    }
               }
               else if (action == "DELETE")
               {
                    var obj_data = new Object;

                    $(".checkboxes").each(function()
                    {
                         var id = $(this).attr('name');
                         if($(this).is(':checked'))
                         {
                              obj_data[id] = $(this).val();
                              selecteditem = obj_data[id];
                              if( selecteditem.indexOf('x') >= 0)
                              {
                                   selecteditem = selecteditem.replace("x", "");
                              }
                              $('table#tbl_batch tr#'+'tr_batch_item' + selecteditem).remove();
                         }
                    });
                    $("#batchUpdateflag").val("1");
               }
               else if (action == "EDIT")
               {
                    if($.trim($("#edit_inputItemNo").val()) == '-1'
                         || $.trim($("#edit_inputQty").val()) == ''
                         || $.trim($("#edit_inputBoxQty").val()) == ''
                         || $.trim($("#edit_inputLotNo").val()) == '')
                    {
                         is_valid = false;
                    }

                    if(parseFloat($("#edit_inputQty").val()))
                    {
                         $("#edit_inputQty").val(parseFloat($.trim($("#edit_inputQty").val())));
                    }
                    else
                    {
                         is_valid = false;
                    }

                    if(parseFloat($("#edit_inputBoxQty").val()))
                    {
                         $("#edit_inputQty").val(parseFloat($.trim($("#edit_inputQty").val())));
                    }
                    else
                    {
                         is_valid = false;
                    }

                    if(is_valid)
                    {
                         var values = $("#edit_inputItemNo").val().split('|');
                         var item = values[0];
                         var desc = values[1];

                         $('#tr_batch_item'+$("#editId").val()+' td:nth-child(4)').html(item);
                         $('#tr_batch_item'+$("#editId").val()+' td:nth-child(5)').html(desc);
                         $('#tr_batch_item'+$("#editId").val()+' td:nth-child(6)').html($("#edit_inputQty").val());
                         $('#tr_batch_item'+$("#editId").val()+' td:nth-child(7)').html($("#edit_inputBoxQty").val());
                         $('#tr_batch_item'+$("#editId").val()+' td:nth-child(8)').html($("#edit_inputLotNo").val());
                         $('#tr_batch_item'+$("#editId").val()+' td:nth-child(9)').html($("#edit_inputLocation").val());
                         $('#editbatchitem').modal('toggle');
                         $("#batchUpdateflag").val("1");
                    }
                    else
                    {
                         $('#editbatchitem').modal('toggle');
                         $("#invalidEditBatchModal").modal().shown();
                    }
               }
          }

          function showAddBatch(action)
          {
               if(action=='ADD')
               {
                $('#invalidAddBatchModal').modal('toggle');
                $("#addbatchitem").modal().shown();
           }
           else
           {
                $('#invalidEditBatchModal').modal('toggle');
                $("#editbatchitem").modal().shown();
           }
      }

      function editBatch(count)
      {
          var obj_data = new Object;
          var selected_arr = new Array;
          var cnt = 0;

          $(".batch_item"+count).each(function()
          {
               var id = $(this).attr('name');
               if(cnt == 0)
               {
                    obj_data[id] = $(this).val();
               }
               else
               {
                    obj_data[id] = $(this).text();
               }
               selected_arr[cnt] = obj_data[id];
               cnt++;
          });

          $("#edit_inputBatchId").val($.trim(selected_arr[0]));
          $("#edit_inputQty").val($.trim(selected_arr[4]));
          $("#edit_inputBoxQty").val($.trim(selected_arr[5]));
          $("#edit_inputLotNo").val($.trim(selected_arr[6]));
          $("#edit_inputLocation").val($.trim(selected_arr[7]));

          $("#edit_inputItemNo option:selected").removeAttr("selected");
          $("#edit_inputItemNo option").each(function()
          {
               if($.trim($(this).text()) == $.trim(selected_arr[2]) + ' ' + $.trim(selected_arr[3]))
               {
                    var selecteditem = ' ' + $.trim(selected_arr[2]) + ' ' + $.trim(selected_arr[3]);
                    $(this).attr('selected', 'selected');
                    $(this).text(selecteditem);
               }
          });

              $("#editId").val(count);
              $('#editbatchitem').modal('show');
         }

         function searchData()
         {
           $("#searchModal").modal().shown();
      }

      function filterData(action)
      {
          var condition_arr = new Array;

          if(action == 'SRCH')
          {
               condition_arr[0] = $("#srch_from").val();
               condition_arr[1] = $("#srch_to").val();
               condition_arr[2] = $("#srch_shipno").val();
               condition_arr[3] = $("#srch_palletno").val();
          }
          else
          {
               $("#srch_from").val("")
               $("#srch_to").val("")
               $("#srch_shipno").val("")
               $("#srch_palletno").val("")

               condition_arr[0] = '';
               condition_arr[1] = '';
               condition_arr[2] = 'X';
               condition_arr[3] = 'X';
          }

          if($('#srch_open:checkbox:checked').length > 0)
          {
               condition_arr[4] ='1';
          }
          else
          {
               condition_arr[4] ='0';
          }

          if($('#srch_close:checkbox:checked').length > 0)
          {
               condition_arr[5] ='1';
          }
          else
          {
               condition_arr[5] ='0';
          }

          if($('#srch_cancelled:checkbox:checked').length > 0)
          {
               condition_arr[6] ='1';
          }
          else
          {
               condition_arr[6] ='0';
          }

          // alert(condition_arr);

          $.post("{{ url('/wbspat-search') }}",
          {
               _token         : $('meta[name=csrf-token]').attr('content')
               , condition_arr: condition_arr
          })
          .done(function(datatable)
          {
                    // alert(datatable);

                    var newcol = '';
                    var newItem = '';
                    var newcollink = '';

                    $('#srch_tbl_body').html('');

                    var arr = $.map(datatable, function(datarow)
                    {
                         newcol = '';
                         $.each( datarow, function( ckey, value )
                         {
                              if(ckey == 'id')
                              {
                                   newcollink = '<td><a href="#" class="btn btn-primary btn-sm" onclick="findEdit('+value+')" value="'+ value +'">Find</a></td>';
                              }
                              else
                              {
                                   newcol = newcol + '<td>'+value+'</td>'
                              }
                         });
                         newItem = '<tr>'
                         + newcollink
                         + newcol
                         + '</tr>';
                         $('#srch_tbl_body').append(newItem);
                    });


               })
          .fail(function()
          {
               alert('fail');
          });
     }

     function findEdit(id)
     {
          window.location.href= "{{ url('/wbspartsreceiving?page=') }}" + 'CUR&id=' + id;
     }

     function generateMrReport()
     {
          window.open("{{ url('/wbspat-report?') }}" + 'id=' + $("#recid").val(), '_blank');
     }

     function generateIqcReport()
     {
          window.open("{{ url('/wbspat-iqc-report?') }}" + 'id=' + $("#recid").val(), '_blank');
     }

          /*
          * Material Receiving END
          */
     </script>

@section('title')
	WBS | Pricon Microelectronics, Inc.
@endsection

@section('content')

	@include('includes.header')
	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_WBS'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach

	<div class="clearfix"></div>

	<!-- BEGIN CONTAINER -->
	<div class="page-container">
    @include('includes.sidebar')
		<!-- BEGIN CONTENT -->
		<div class="page-content-wrapper">
			<div class="page-content">

				<!-- BEGIN PAGE CONTENT-->
				<div class="row">
					<div class="col-md-12">
						<!-- BEGIN EXAMPLE TABLE PORTLET-->
						@include('includes.message-block')
						<div class="portlet box blue" >
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-navicon"></i>  WBS
								</div>
							</div>
							<div class="portlet-body">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="row">
                   <form>
                    {!! csrf_field() !!}
                    <div class="col-md-4">
                     <div class="form-group row">
                      @if(isset($pr_data))
                      @foreach($pr_data as $prdata)
                      @endforeach
                      @endif
                      <label class="control-label col-md-3" style="font-size:12px">Receiving No.</label>
                      <div class="col-md-4">
                       <input type="hidden" class="form-control input-sm" id="recid" name="recid" value="<?php if(isset($prdata)){echo $prdata->id; } ?>" />
                       <input type="hidden" class="form-control input-sm" id="action" name="action" value="<?php if(isset($action)){echo $action; } ?>" />
                       <input type="hidden" class="form-control input-sm" id="hdnreceivingno" name="hdnreceivingno" value="<?php if(isset($prdata)){echo $prdata->receive_no; } ?>" />
                       <input type="hidden" class="form-control input-sm" id="detailsUpdateflag" name="detailsUpdateflag" value="<?php if(isset($detailsUpdateFlag)){echo $detailsUpdateFlag; } ?>" />
                       <input type="hidden" class="form-control input-sm" id="batchUpdateflag" name="batchUpdateflag" value="<?php if(isset($batchUpdateFlag)){echo $batchUpdateFlag; } ?>" />
                       <input type="text" class="form-control input-sm" id="receivingno" name="receivingno" value="<?php if(isset($prdata)){echo $prdata->receive_no; } ?>" <?php if($action!='VIEW'){ echo "disabled"; } ?> />
                     </div>
                     <div class="col-md-4" >
                       <div class="btn-group btn-group-circle" style="width:200px;">
                        <button type="button" style="font-size:12px" onclick="javascript: getrecord('MIN'); " id="btn_min" class="btn blue input-sm" <?php if(isset($prdata)){if($prdata->id == 1){ echo 'disabled';} } ?> <?php if($action!='VIEW'){ echo "disabled"; } ?>><i class="fa fa-fast-backward"></i></button>
                        <button type="button" style="font-size:12px" onclick="javascript: getrecord('PRV'); " id="btn_prv" class="btn blue input-sm" <?php if(isset($prdata)){if($prdata->id == 1){ echo 'disabled';} } ?> <?php if($action!='VIEW'){ echo "disabled"; } ?>><i class="fa fa-backward"></i></button>
                        <button type="button" style="font-size:12px" onclick="javascript: getrecord('NXT'); " id="btn_nxt" class="btn blue input-sm" <?php if(isset($ismax)){if($ismax){ echo 'disabled';} } ?> <?php if($action!='VIEW'){ echo "disabled"; } ?>><i class="fa fa-forward"></i></button>
                        <button type="button" style="font-size:12px" onclick="javascript: getrecord('MAX'); " id="btn_max" class="btn blue input-sm" <?php if(isset($ismax)){if($ismax){ echo 'disabled';} } ?> <?php if($action!='VIEW'){ echo "disabled"; } ?>><i class="fa fa-fast-forward"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="control-label col-md-3" style="font-size:12px">Receiving Date.</label>
                    <div class="col-md-4">
                     <input class="form-control input-sm date-picker" size="16" type="text" name="receivingdate" id="receivingdate" value="<?php if(isset($prdata)){echo $prdata->receive_date; } ?>" <?php if($action=='VIEW'){ echo 'disabled'; } ?> />
                   </div>
                   <div class="col-md-5">
                     <!-- <button type="button" class="btn btn-default">Previous</button> -->
                   </div>
                 </div>
                 <div class="form-group row">
                  <label class="control-label col-md-3" style="font-size:12px">Ship No.</label>
                  <div class="col-md-4">
                   <input type="text" class="form-control input-sm" id="shipno" name="shipno" value="<?php if(isset($prdata)){ echo $prdata->ship_no; } ?>" <?php if($action=='VIEW'){ echo 'disabled'; } ?> />
                   <input type="hidden" class="form-control input-sm" id="hdnshipno" name="hdnshipno" value="<?php if(isset($prdata)){echo $prdata->ship_no; } ?>" />
                 </div>
                 <div class="col-md-5">
                   <button type="submit" class="btn btn-circle green input-sm" style="font-size:12px" id="btn_checkinv" <?php if($action=='VIEW'){ echo 'disabled'; } ?>><i class="fa fa-arrow-circle-down"></i></button>
                 </div>
               </div>
               <div class="form-group row">
                <label class="control-label col-md-3" style="font-size:12px">Pallet No.</label>
                <div class="col-md-4">
                 <input type="text" class="form-control input-sm" id="palletno" name="palletno" value="<?php if(isset($prdata)){ if($prdata->pallet_no != 'null') {echo $prdata->pallet_no; }} ?>" <?php if($action=='VIEW'){ echo 'disabled'; } ?> />
               </div>
             </div>
           </div>

           <div class="col-md-4">
             <div class="form-group row">
              <label class="control-label col-md-3" style="font-size:12px">Total Qty.</label>
              <div class="col-md-6">
               <input type="text" class="form-control input-sm" id="totalqty" name="totalqty" value="<?php if(isset($prdata)){echo $prdata->total_qty; } ?>" disabled="disable" />
             </div>
           </div>
           <div class="form-group row">
            <label class="control-label col-md-3" style="font-size:12px">Status</label>
            <div class="col-md-6">
             <input type="text" class="form-control input-sm" id="status" name="status" value="<?php if(isset($prdata)){echo $prdata->status; } ?>" disabled="disable" />
           </div>
         </div>
       </div>

       <div class="col-md-4">
         <div class="form-group row">
          <label class="control-label col-md-3" style="font-size:12px">Created By</label>
          <div class="col-md-6">
           <input type="text" class="form-control input-sm" id="createdby" name="createdby" value="<?php if(isset($prdata)){echo $prdata->create_user; } ?>" disabled="disable" />
         </div>
       </div>
       <div class="form-group row">
        <label class="control-label col-md-3" style="font-size:12px">Created Date.</label>
        <div class="col-md-6">
         <input class="form-control date-picker input-sm" size="50" type="text" name="createddate" id="createddate" value="<?php if(isset($prdata)){echo $prdata->created_at; } ?>" disabled="disable"/>
       </div>
     </div>
     <div class="form-group row">
      <label class="control-label col-md-3" style="font-size:12px">Updated By</label>
      <div class="col-md-6">
       <input type="text" class="form-control input-sm" id="updatedby" name="updatedby" value="<?php if(isset($prdata)){echo $prdata->update_user; } ?>" disabled="disable" />
     </div>
   </div>
   <div class="form-group row">
    <label class="control-label col-md-3" style="font-size:12px">Updated Date</label>
    <div class="col-md-6">
     <input class="form-control date-picker input-sm" size="50" type="text" name="updateddate" id="updateddate" value="<?php if(isset($prdata)){echo $prdata->updated_at; } ?>" disabled="disable"/>
   </div>
 </div>
</div>

</form>
</div>

<div class="row">
 <div class="col-md-12">
  <div class="tabbable-custom">
   <ul class="nav nav-tabs nav-tabs-lg" id="tabslist" role="tablist">
    <li class="active">
     <a href="#details" data-toggle="tab" data-toggle="tab" aria-expanded="true" style="font-size:12px">Details</a>
   </li>
   <li>
     <a href="#summary" data-toggle="tab" data-toggle="tab" aria-expanded="true" style="font-size:12px">Summary</a>
   </li>
   <li>
     <a href="#batch" data-toggle="tab" data-toggle="tab" aria-expanded="true" style="font-size:12px">Batch Details</a>
   </li>
 </ul>

 <!-- Details Tab -->
 <div class="tab-content" id="tab-subcontents">
  <div class="tab-pane fade in active" id="details">
   <div class="row">
    <div class="col-md-8 col-md-offset-2">
     <table class="table table-striped table-bordered table-hover table-responsive" id="sample_2" style="font-size:10px">
      <thead>
       <tr>
        <td width="20%">Item/Part No.</td>
        <td>Item Description</td>
        <td>Ship Qty</td>
        <td>Lot No.</td>
        <td>PO/PR No.</td>
        <td>Drawing No</td>
      </tr>
    </thead>
    <tbody>
     @if(isset($pr_details_data))
     @foreach($pr_details_data as $prddata)
     <tr>
      <td>{{ $prddata->item }}</td>
      <td>{{ $prddata->description }}</td>
      <td style="text-align: right">{{ $prddata->qty }}</td>
      <td>{{ $prddata->lot_no }}</td>
      <td>{{ $prddata->pr }}</td>
      <td>{{ $prddata->drawing_no }}</td>
    </tr>
    @endforeach
    @endif
  </tbody>
</table>
</div>
</div>
</div>
<!-- Summary Tab -->
<div class="tab-pane fade" id="summary">
 <div class="row">
  <div class="col-md-8 col-md-offset-2">
   <table class="table table-striped table-bordered table-hover table-responsive" id="sample_3">
    <thead>
     <tr>
      <th width="20%" style="font-size:10px">Item/Part No.</th>
      <th width="20%" style="font-size:10px">Item Description</th>
      <th style="font-size:10px">Quantity</th>
      <th style="font-size:10px">Received Qty.</th>
      <th style="font-size:10px">Variance</th>
    </tr>
  </thead>
  <tbody>
   <?php $iqc = 0?>
   @if(isset($pr_summary_data))
   @foreach($pr_summary_data as $prsdata)
   <tr class="odd gradeX" data-id="{{ $prsdata->id }}">
    <td style="font-size:10px">{{ $prsdata->item }}</td>
    <td style="font-size:10px">{{ $prsdata->description }}</td>
    <td style="text-align: right; font-size:10px">{{ $prsdata->qty }}</td>
    <td style="text-align: right; font-size:10px">{{ $prsdata->r_qty }}</td>
    <td style="text-align: right; font-size:10px">{{ $prsdata->variance }}</td>
  </tr>
  @endforeach
  @endif
</tbody>
</table>
</div>
</div>
</div>
<!-- Batch Details Tab -->
<div class="tab-pane fade" id="batch">
 <div class="row">
  <div class="col-md-8 col-md-offset-2">
   <table class="table table-striped table-bordered table-hover table-responsive" id="tbl_batch" style="font-size:10px">
    <thead id="th_batch">
     <tr>
      <th class="table-checkbox" style="font-size:10px">
       <!-- <input type="checkbox" class="group-checkable" data-set="#tbl_batch .checkboxes"/> -->
     </th>
     <th style="font-size:10px"></th>
     <th style="font-size:10px">Batch ID</th>
     <th style="font-size:10px">Item/Part No.</th>
     <th style="font-size:10px">Item Description</th>
     <th style="font-size:10px">Quantity</th>
     <th style="font-size:10px">Box Qty.</th>
     <th style="font-size:10px">Lot No.</th>
     <th style="font-size:10px">Location</th>
     <th style="font-size:10px">Printed</th>
     <th style="font-size:10px"></th>
   </tr>
 </thead>
 <tbody id="table_body" >
   <?php $ctr = 1; ?>
   @if(isset($pr_batch_data))
   @foreach($pr_batch_data as $prbdata)
   <tr id="tr_batch_item<?php echo $ctr; ?>">
    <td><input type="checkbox" class="checkboxes batch_item<?php echo $ctr; ?> input-sm" value="<?php echo $ctr; ?>"/></td>
    <td><a href="#" onclick="editBatch('<?php echo $ctr; ?>')" value="sitem<?php echo $ctr; ?>" <?php if($action=='VIEW'){ echo 'disabled'; } ?> ><i class="fa fa-edit"></i></a></td>
    <td class="batch_item<?php echo $ctr; ?> inputBatchId"><?php echo $ctr; ?></td>
    <td class="batch_item<?php echo $ctr; ?> inputItemNo" name="inputItemNo"> {{ $prbdata->item }} </td>
    <td class="batch_item<?php echo $ctr; ?> inputItem" name="inputItem"> {{ $prbdata->description}} </td>
    <td class="batch_item<?php echo $ctr; ?> inputQty" name="inputQty"> {{ $prbdata->qty }} </td>
    <td class="batch_item<?php echo $ctr; ?> inputBoxQty" name="inputBoxQty"> {{ $prbdata->box_qty }} </td>
    <td class="batch_item<?php echo $ctr; ?> inputLotNo" name="inputLotNo"> {{ $prbdata->lot_no }} </td>
    <td class="batch_item<?php echo $ctr; ?> inputLocation" name="inputLocation"> {{ $prbdata->location }} </td>
    <td class="batch_item<?php echo $ctr; ?> inputIsPrinted" name="inputIsPrinted"><input type="checkbox" class="isprinted<?php echo $ctr; ?>" disabled value="{{ $prbdata->is_printed}}" <?php if(isset($prbdata->is_printed)){if($prbdata->is_printed == 1){ echo 'checked';}} ?>/></td>
    <td>
     <button type="button" style="font-size:12px; <?php if(isset($prdata)){ if($prdata->status == 'Cancelled') { echo 'display:none;'; } } ?>" onclick="javascript: setcontrol('PRNT', '{{$prbdata->item}}|{{$prbdata->is_printed}}');" class="btn grey-gallery input-sm" <?php echo $state; ?> ><i class="fa fa-barcode"></i></button>
   </td>
 </tr>
 <?php $ctr ++; ?>
 @endforeach
 @endif
</tbody>
</table>
</div>
</div>
<div class="row">
  <div class="col-md-12 text-center">
   <button type="button" style="font-size:12px; <?php if($action=='VIEW'){ echo 'display:none;'; } ?>" data-toggle="modal" data-target="#addbatchitem" class="btn green input-sm" id="btn_add_batch">
    <i class="fa fa-plus"></i> Add
  </button>
  <button type="button" style="font-size:12px; <?php if($action=='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: batchdata('DELETE'); "  class="btn red input-sm" id="btn_delete_batch">
    <i class="fa fa-trash"></i> Delete
  </button>
  <input type="hidden" style="font-size:12px" class="form-control input-sm" id="item_count" placeholder="Lower Limit" name="item_count" value="<?php echo $ctr-1; ?>" />
</div>
</div>
</div>
</div>

</div>
</div>
</div>

<!-- Action Buttons -->
<form>
 {!! csrf_field() !!}
 <div class="row">
  <div class="col-md-12 text-center">
   <button type="button" style="font-size:12px; <?php if($action!='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: setcontrol('ADD'); " class="btn green input-sm" id="btn_add" <?php echo($state); ?> >
    <i class="fa fa-plus"></i> Add New
  </button>
  <button type="button" style="font-size:12px; <?php if($action=='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: saverecord(); " class="btn blue-madison input-sm" id="btn_save" <?php echo($state); ?> >
    <i class="fa fa-pencil"></i> Save
  </button>
  <button type="button" style="font-size:12px; <?php if(isset($prdata)){ if($prdata->status == 'Cancelled') { echo 'display:none;'; } } ?> <?php if($action!='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: setcontrol('EDIT'); " class="btn blue-madison input-sm" id="btn_edit"  <?php echo($state); ?> >
    <i class="fa fa-pencil"></i> Edit
  </button>
  <button type="button" style="font-size:12px; <?php if(isset($prdata)){ if($prdata->status == 'Cancelled') { echo 'display:none;'; } } ?> <?php if($action!='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: setcontrol('CNL'); " class="btn red input-sm" id="btn_cancel" <?php echo($state); ?> >
    <i class="fa fa-trash"></i> Cancel
  </button>
  <button type="button" style="font-size:12px; <?php if($action=='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: setcontrol('DIS'); " class="btn red-intense input-sm" id="btn_discard" <?php echo($state); ?> >
    <i class="fa fa-times"></i> Discard Changes
  </button>
  <button type="button" style="font-size:12px; <?php if($action!='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: searchData();" class="btn blue-steel input-sm" id="btn_search">
    <i class="fa fa-search"></i> Search
  </button>
  <button type="button" style="font-size:12px; <?php if(isset($prdata)){ if($prdata->status == 'Cancelled') { echo 'display:none;'; } } ?> <?php if($action!='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: setcontrol('PRNT', '|'); " class="btn grey-gallery input-sm" id="btn_barcode" <?php echo($state); ?>>
    <i class="fa fa-barcode"></i> Barcode
  </button>
  <button type="submit" style="font-size:12px; <?php if(isset($prdata)){ if($prdata->status == 'Cancelled') { echo 'display:none;'; } } ?> <?php if($action!='VIEW'){ echo 'display:none;'; } ?>" onclick="javascript: generateMrReport();" class="btn purple-plum input-sm" id="btn_print" <?php echo($state); ?>>
    <i class="fa fa-print"></i> Print
  </button>
</div>
</div>
</form>

<!-- AJAX LOADER -->
<div id="loading" class="modal fade" role="dialog" data-backdrop="static">
 <div class="modal-dialog modal-sm gray-gallery">
  <div class="modal-content ">
   <div class="modal-body">
    <div class="row">
     <div class="col-sm-2"></div>
     <div class="col-sm-8">
      <img src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/images/ajax-loader.gif') }}" class="img-responsive">
    </div>
    <div class="col-sm-2"></div>
  </div>
</div>
</div>
</div>
</div>

<!-- Successful Ship Load Pop-message-->
<div id="validShipModal" class="modal fade" role="dialog">
 <div class="modal-dialog modal-sm blue">
  <div class="modal-content ">
   <div class="modal-body">
    <p>Shipment Data Successfully Loaded.</p>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnok">OK</button>
  </div>
</div>
</div>
</div>

<!-- Add Batch Validation Pop-message -->
<div id="invalidAddBatchModal" class="modal fade" role="dialog">
 <div class="modal-dialog modal-sm blue">
  <div class="modal-content ">
   <div class="modal-body">
    <p>One or more fields contains invalid values.</p>
  </div>
  <div class="modal-footer">
    <button type="button" onclick="javascript: showAddBatch('ADD');" class="btn btn-primary" id="btnok">OK</button>
  </div>
</div>
</div>
</div>

<!-- Edit Batch Validation Pop-message -->
<div id="invalidEditBatchModal" class="modal fade" role="dialog">
 <div class="modal-dialog modal-sm blue">
  <div class="modal-content ">
   <div class="modal-body">
    <p>One or more fields contains invalid values.</p>
  </div>
  <div class="modal-footer">
    <button type="button" onclick="javascript: showAddBatch('EDIT');" class="btn btn-primary" id="btnok">OK</button>
  </div>
</div>
</div>
</div>

<!-- Cancel Confirmation Pop-message -->
<div id="deleteModal" class="modal fade" role="dialog">
 <div class="modal-dialog modal-sm blue">
  <form role="form" method="POST" action="{{ url('/wbspat-cancel') }}">
   <div class="modal-content ">
    <div class="modal-body">
     <p>Are you sure you want to cancel this transaction?</p>
     {!! csrf_field() !!}
     <input type="hidden" name="id" id="delete_inputId"/>
   </div>
   <div class="modal-footer">
     <button type="submit" class="btn btn-primary" id="delete">Yes</button>
     <button type="button" data-dismiss="modal" class="btn">Cancel</button>
   </div>
 </div>
</form>
</div>
</div>

<!-- Barcode Print Confirmation Pop-message -->
<div id="barcodeModal" class="modal fade" role="dialog">
 <div class="modal-dialog modal-sm blue">
  <form role="form" method="POST" action="{{ url('/wbspat-barcode') }}">
   <div class="modal-content ">
    <div class="modal-body">
     <p>Are you sure you want to print barcode/s?</p>
     {!! csrf_field() !!}
     <input type="hidden" name="id" id="barcode_inputId"/>
     <input type="hidden" name="item" id="barcode_inputItemNo"/>
   </div>
   <div class="modal-footer">
     <button type="submit" class="btn btn-primary" id="barcode">Yes</button>
     <button type="button" data-dismiss="modal" class="btn">Cancel</button>
   </div>
 </div>
</form>
</div>
</div>

<!-- Add Batch Modal -->
<div id="addbatchitem" class="modal fade" role="dialog">
 <div class="modal-dialog modal-sm">

  <!-- Modal content-->
  <div class="modal-content blue" style="width:500px;">
   <div class="modal-header">
    <button type="button" class="close" style="font-size:12px" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Add Batch</h4>
  </div>
  <div class="modal-body">
    <div class="row">
     <div class="col-md-6">
      All the fields are required.
    </div>
    <div class="col-md-12">
      <div class="form-group">
       <label for="inputcode" class="col-md-4 control-label" style="font-size:12px">*Batch ID</label>
       <div class="col-md-8">
        <input type="text" id="add_ship_no" name="id" hidden="true" />
        <input type="text" class="form-control input-sm" id="add_inputBatchId" placeholder="Batch ID" name="batchid" readonly />
      </div>
    </div>
    <div class="form-group">
     <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Item No</label>
     <div class="col-md-8">
      <select id="add_inputItemNo"  class="form-control select2me" name="itemno" <?php echo($state); ?> >
       <option value="-1">--Select--</option>
       @if(isset($items))
       @foreach($items as $value)
       <option value="{{$value->code . "|" . $value->name . "|" . $value->rackno}}"> {{ $value->code . ' ' . $value->name }}</option>
       @endforeach
       @endif
     </select>
     <!-- <input type="text" class="form-control" id="add_inputItemNo" placeholder="Item No" name="itemno" autofocus <?php echo($readonly); ?> /> -->
   </div>
 </div>
 <div class="form-group">
   <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Quantity</label>
   <div class="col-md-8">
    <input type="text" class="form-control input-sm" id="add_inputQty" placeholder="Quantity" name="qty" <?php echo($readonly); ?> />
  </div>
</div>
<div class="form-group">
 <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Box Qty</label>
 <div class="col-md-8">
  <input type="text" class="form-control input-sm" id="add_inputBoxQty" placeholder="Box Qty" name="boxqty" <?php echo($readonly); ?> />
</div>
</div>
<div class="form-group">
 <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Lot No</label>
 <div class="col-md-8">
  <input type="text" class="form-control input-sm" id="add_inputLotNo" placeholder="Lot No" name="lotno" <?php echo($readonly); ?> />
</div>
</div>
<div class="form-group">
 <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Location</label>
 <div class="col-md-8">
  <input type="text" class="form-control input-sm" id="add_inputLocation" placeholder="Location" name="location" disabled="disabled" <?php echo($readonly); ?> />
</div>
</div>
</div>
</div>
</div>
<div class="modal-footer">
  <button type="button" style="font-size:12px" onclick="javascript: batchdata('ADD'); " class="btn btn-success" <?php echo($state); ?> ><i class="fa fa-plus"></i> Add</button>
  <button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>
</div>
</div>
</div>

<!-- Edit Batch Modal -->
<div id="editbatchitem" class="modal fade" role="dialog">
 <div class="modal-dialog modal-sm">

  <!-- Modal content-->
  <div class="modal-content blue" style="width:500px;">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">EDIT Batch Details</h4>
  </div>
  <div class="modal-body">
    <div class="row">
     <div class="col-md-6">
      All the fields are required.
    </div>
    <div class="col-md-12">
      <div class="form-group">
       <label for="inputcode" class="col-md-4 control-label" style="font-size:12px">*Batch ID</label>
       <div class="col-md-8">
        <input type="text" class="form-control input-sm" id="edit_inputBatchId" placeholder="Batch ID" name="batchid" readonly />
      </div>
    </div>
    <div class="form-group">
     <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Item No</label>
     <div class="col-md-8" id="edit_itemno">
      <select id="edit_inputItemNo"  class="form-control select2me" name="edit_inputItemNo" <?php echo($state); ?> >
       <option value="-1">--Select--</option>
       @if(isset($items))
       @foreach($items as $value)
       <option value="{{$value->code . "|" . $value->name . "|" . $value->rackno}}"> {{ $value->code . ' ' . $value->name }}</option>
       @endforeach
       @endif
     </select>
     <!-- <input type="text" class="form-control" id="edit_inputItemNo" placeholder="Item No" name="itemno" autofocus <?php echo($readonly); ?> /> -->
   </div>
 </div>
 <div class="form-group">
   <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Quantity</label>
   <div class="col-md-8">
    <input type="text" class="form-control input-sm" id="edit_inputQty" placeholder="Quantity" name="qty" <?php echo($readonly); ?> />
  </div>
</div>
<div class="form-group">
 <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Box Qty</label>
 <div class="col-md-8">
  <input type="text" class="form-control input-sm" id="edit_inputBoxQty" placeholder="Box Qty" name="boxqty" <?php echo($readonly); ?> />
</div>
</div>
<div class="form-group">
 <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Lot No</label>
 <div class="col-md-8">
  <input type="text" class="form-control input-sm" id="edit_inputLotNo" placeholder="Lot No" name="lotno" <?php echo($readonly); ?> />
</div>
</div>
<div class="form-group">
 <label for="inputname" class="col-md-4 control-label" style="font-size:12px">*Location</label>
 <div class="col-md-8">
  <input type="text" class="form-control input-sm" id="edit_inputLocation" placeholder="Location" name="location" disabled="disabled" <?php echo($readonly); ?> />
</div>
</div>
</div>
</div>
</div>
<div class="modal-footer">
  <input type="hidden" class="form-control input-sm" id="editId" name="editId"/>
  <button type="button" style="font-size:12px" id="btn_update_batch" onclick="javascript: batchdata('EDIT'); " class="btn btn-success" <?php echo($state); ?> ><i class="fa fa-edit"></i> Update</button>
  <button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>
</div>
</div>
</div>


<!-- Search Modal -->
<div id="searchModal" class="modal fade" role="dialog">
 <div class="modal-dialog modal-lg">

  <!-- Modal content-->
  <form class="form-horizontal" role="form" method="POST" action="{{ url('/wbspartsreceiving') }}">
   {!! csrf_field() !!}
   <div class="modal-content blue">
    <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal">&times;</button>
     <h4 class="modal-title">Search</h4>
   </div>
   <div class="modal-body">
     <div class="row">
      <div class="col-md-12">
       <div class="form-group">
        <label for="inputcode" class="col-md-4 control-label" style="font-size:12px">Receive Date</label>
        <div class="col-md-8">
         <div class="input-group input-large date-picker input-daterange" data-date="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy">
          <input type="text" class="form-control input-sm" name="srch_from" id="srch_from"/>
          <span class="input-group-addon">to </span>
          <input type="text" class="form-control input-sm" name="srch_to" id="srch_to"/>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label for="inputname" class="col-md-4 control-label" style="font-size:12px">Ship No</label>
      <div class="col-md-4">
       <input type="text" class="form-control input-sm" id="srch_shipno" placeholder="Ship No" name="srch_shipno" autofocus <?php echo($readonly); ?> />
     </div>
   </div>
   <div class="form-group">
    <label for="inputname" class="col-md-4 control-label" style="font-size:12px">Pallet No</label>
    <div class="col-md-4">
     <input type="text" class="form-control input-sm" id="srch_palletno" placeholder="Pallet No" name="srch_palletno" <?php echo($readonly); ?> />
   </div>
 </div>
 <div class="form-group">
  <label for="inputname" class="col-md-4 control-label" style="font-size:12px">Status</label>
  <div class="col-md-8">
   <label><input type="checkbox" class="checkboxes" style="font-size:12px" value="Open" id="srch_open" name="Open" checked="true"/>Open</label>
   <label><input type="checkbox" class="checkboxes" style="font-size:12px" value="Close" id="srch_close" name="Close"/>Close</label>
   <label><input type="checkbox" class="checkboxes" style="font-size:12px" value="Cancelled" id="srch_cancelled" name="Cancelled"/>Cancelled</label>
 </div>
</div>
</div>
</div>
<div class="row" style="width:880px; height:500px; overflow:auto;">
  <div class="col-md-12">
   <table class="table table-striped table-bordered table-hover table-responsive" id="sample_3" style="font-size:10px">
    <thead>
     <tr>
      <td width="10%"></td>
      <td>Transaction No.</td>
      <td>Receive Date</td>
      <td>Ship No.</td>
      <td>Pallet No.</td>
      <td>Status</td>
      <td>Created By</td>
      <td>Created Date</td>
      <td>Updated By</td>
      <td>Updated Date</td>
    </tr>
  </thead>
  <tbody id="srch_tbl_body">
  </tbody>
</table>
</div>
</div>
</div>
<div class="modal-footer">
 <input type="hidden" class="form-control input-sm" id="editId" name="editId">
 <button type="button" style="font-size:12px" onclick="javascript: filterData('SRCH'); " class="btn blue-madison"><i class="glyphicon glyphicon-filter"></i> Filter</button>
 <button type="button" style="font-size:12px" onclick="javascript: filterData('CNCL'); " class="btn green" ><i class="glyphicon glyphicon-repeat"></i> Reset</button>
 <button type="button" style="font-size:12px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>
</div>
</form>
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
</div>
<!-- END CONTENT -->

</div>
<!-- END CONTAINER -->
@endsection
