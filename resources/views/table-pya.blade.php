@extends('includes.tblyield') 
<table class="table table-striped table-bordered table-hover"style="font-size:13px">
  <thead id="thead1">
       <tr>
            <td class="table-checkbox" style="width: 5%">
                 <input type="checkbox" class="group-checkable checkAllitemsPYA" name="checkAllitemPYA" data-set="#sample_3 .checkboxes"/>
            </td>
            <td>Purchase Order</td>
            <td>Production Date</td>
            <td>Yielding Station</td>
            <td>Accumulated Output</td>
       </tr>
  </thead>
  <tbody id="tbody1">
       @foreach($fieldpya as $rec)
       <tr>                   
            <td style="width: 3%">
              <input type="checkbox" class="form-control input-sm checkboxesPYA" value="{{$rec->id}}" name="checkitemPYA" id="checkitemPYA">
              </input> 
              
            </td> 
            <td>{{$rec->pono}}<input type="hidden" value="{{$rec->id. '|' .$rec->pono. '|' .$rec->productiondate. '|' .$rec->yieldingstation. '|' .$rec->accumulatedoutput}}" class="form-control input-sm" id="hdpya" name="hdpya"/></td>
            <td>{{$rec->productiondate}}</td>
            <td>{{$rec->yieldingstation}}</td>
            <td>{{$rec->accumulatedoutput}}</td>
       </tr>
       @endforeach
  </tbody>
</table>
