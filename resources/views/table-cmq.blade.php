@extends('includes.tblyield') 
<table id="results2" class="table table-striped table-bordered table-hover" style="font-size:13px">
  <thead id="thead2">
       <tr>
            <td class="table-checkbox" style="width: 5%">
                 <input type="checkbox" class="group-checkable checkAllitemsCMQ" name="checkAllitemCMQ" data-set="#sample_3 .checkboxes"/>
            </td>
            <td>Purchase Order</td>
            <td>Classification</td>
            <td>Mode of Defects</td>
            <td>Quantity</td>
       </tr>
  </thead>
  <tbody id="tbody2">
       @foreach($fieldcmq as $rec)
       <tr>                      
            <td style="width: 3%"> 
                 <input type="checkbox" class="form-control input-sm checkboxesCMQ" value="{{$rec->id}}" name="checkitemCMQ" id="checkitemCMQ">
                 </input>
            </td>
            <td>{{$rec->pono}}<input type="hidden" value="{{$rec->id. '|' .$rec->pono. '|' .$rec->classification. '|' .$rec->mod. '|' .$rec->qty}}" class="form-control input-sm" id="hdcmq" name="hdcmq"  disabled="disabled" /></td>
            <td>{{$rec->classification}}</td>
            <td>{{$rec->mod}}</td>
            <td>{{$rec->qty}}</td>
       </tr>

       @endforeach

  </tbody>
</table>