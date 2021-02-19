@extends('layouts.master')
@section('title')
	Transactions Settings | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_TRANSET'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
							<i class="fa fa-exchange"></i>  Transactions Settings
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-sm-12 table-responsive" >
								<table class="table table-striped table-bordered table-hover" id="sample_3">
										<thead>
											<tr>
												<th class="table-checkbox" style="width: 5%">
													<input type="checkbox" class="group-checkable checkAllitems" data-set="#sample_3 .checkboxes"/>
												</th>										
												<th></th>													
												<th>Code</th>
												<th>Description</th>
												<th>Prefix</th>
												<th >Prefix Format</th>
												<th >Next No.</th>
												<th>Next No. Length</th>
											</tr>
										</thead>

										<tbody>
											@foreach ($tableData as $dest)
												<tr>
													
													<td style="width: 5%">

							                           <input type="checkbox" class="form-control input-sm checkboxes" name="checkitem" id="checkitem'.{{$dest->id}}" value="{{$dest->id}}"></input>
						            				</td>
						   															
													<td style="width: 7%">
							                           
							                            <button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="{{$dest->id . '|' . $dest->code. '|' .$dest->description. '|' .$dest->prefix. '|' .$dest->prefixformat. '|' .$dest->nextno. '|' .$dest->nextnolength}}">
						                						<i class="fa fa-edit"></i> 
						            					</button>
						            				</td>
						            				<td>{{$dest->code}}</td>
													<td>{{$dest->description}}</td>
													<td>{{$dest->prefix}}</td>
													<td>{{$dest->prefixformat}}</td>
													<td>{{$dest->nextno}}</td>
													<td>{{$dest->nextnolength}}</td>
												</tr>
											@endforeach
										</tbody>
									</table>									
								
							</div>
						</div>

						<div class="row">
							<div class="col-sm-4 col-sm-offset-5" style="margin-top: 30px;">
								<a href="#" id="add" class="btn btn-success btn-lg">
									<i class="fa fa-plus-square-o"></i> Add
								</a>
								<button type="button" class="btn btn-danger btn-lg deleteAll-task">
									<i class="fa fa-trash"></i> Delete
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


	<div id="transetModal" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog gray-gallery">
			<div class="modal-content ">
				<form method="POST" class="form-horizontal" id="transfrmsml">
					<div class="modal-header">
						<h4 class="modal-title"></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								
									{{ csrf_field() }}
									<div class="form-group">
										<div class="col-sm-12">
											<p>
												Prefix, Next No. and Next No. Length fields are required.
											</p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Code</label>
										<div class="col-sm-9">
											<input type="text" class="form-control input-sm" id="code" name="code" maxlength="10" >
											<div id="er1"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Description</label>
										<div class="col-sm-9">
											<textarea name="desc" id="desc" class="form-control" style="resize:none"  ></textarea>
											<div id="er2"></div>
										</div>
									</div>						
									<div class="form-group">
										<label class="control-label col-sm-3">Prefix</label>
										<div class="col-sm-9">
											<input type="text" class="form-control input-sm" id="prefix" name="prefix" >
											<div id="er3"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Prefix Format</label>
										<div class="col-sm-9">
											<input type="text" class="form-control input-sm" id="prefixfm" name="prefixfm" >
											
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Next No.</label>
										<div class="col-sm-9">
											<input type="number" class="form-control input-sm" id="nextno" name="nextno" >
											<div id="er5"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Next No. Length</label>
										<div class="col-sm-9">
											<input type="number" class="form-control input-sm" id="nextnolength" max="10" name="nextnolength" >
											<div id="er6"></div>
										</div>
									</div>
									
									
														
							</div>
						</div>							
					</div>
					<div class="modal-footer">
						<input type="hidden" class="form-control input-sm" id="masterid" name="masterid" maxlength="40" >
						<input type="hidden" class="form-control input-sm" id="hdnaction" name="hdnaction" maxlength="40" value="ADD">
						<button type="button" onclick="javascript:Add_Records();" class="btn btn-success">Save</button>
						<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
					</div>
				</form>		
			</div>
		</div>
	</div>


	<!--delete all modal-->
	<div id="deleteAllModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm gray-gallery">
			<!-- Modal content-->
			<form class="form-horizontal" id="deleteAllform" action="{{url('/deleteAll-transaction')}}" role="form" method="POST">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="deleteAll-title">Delete Transaction Settings</h4>
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
					</div>
					<div class="modal-footer">
						<a href="#" class="btn btn-success" id="modaldelete" ><i class="fa fa-save"></i> Yes</a>
						<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- 	Success Message Modal -->
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
								<label for="confirmMessage" id="confirmMessage" class="col-sm-12 control-label text-center">
								
								</label>
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

@endsection

<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	$( document ).ready(function(e) {
		$('.deleteAll-task').addClass("disabled");
		$('.delete-task').addClass("disabled");
		$('#add').removeClass("disabled");

		$('.checkAllitems').change(function(){
			if($('.checkAllitems').is(':checked')){
				$('.deleteAll-task').removeClass("disabled");
				$('#add').addClass("disabled");
				$('input[name=checkitem]').parents('span').addClass("checked");
				$('input[name=checkitem]').prop('checked',this.checked);
			}else{
				$('input[name=checkitem]').parents('span').removeClass("checked");
				$('input[name=checkitem]').prop('checked',this.checked);
				$('.deleteAll-task').addClass("disabled");
				$('#add').removeClass("disabled");
			}		
		});

		$('.checkboxes').change(function(){
			$('input[name=checkAllitem]').parents('span').removeClass("checked");
			$('input[name=checkAllitem]').prop('checked',false);
			if($('.checkboxes').is(':checked')){
				$('.deleteAll-task').removeClass("disabled");
				$('#add').addClass("disabled");
			}else{
				$('.deleteAll-task').addClass("disabled");
				$('#add').removeClass("disabled");
			}
		
		});

		$('#modaldelete').on('click', function() {
			deleteAllcheckeditems();
		});

		$('#add').on('click', function(e) {
			$('#transetModal').modal('show');
			$('.modal-title').html('Add Transaction Settings');
			$('#hdnaction').val('ADD');
			$('#code').val("");
			$('#desc').val("");
			$('#prefix').val("");	
			$('#prefixfm').val("");
			$('#nextno').val("");
			$('#nextnolength').val("");
			$('#er1').html("");
			$('#er2').html("");
			$('#er3').html("");	
			$('#er5').html("");
			$('#er6').html("");	

			$('#code').keyup(function(){
			   $('#er1').html(""); 
			});
			$('#desc').keyup(function(){
			   $('#er2').html(""); 
			});
			$('#prefix').keyup(function(){
			   $('#er3').html(""); 
			});	
			$('#nextno').keyup(function(){
			   $('#er5').html(""); 
			});
			$('#nextnolength').keyup(function(){
			   $('#er6').html(""); 
			});	
			
		});

		$('.edit-task').on('click', function(e) {
			$('.modal-title').html('Update Transaction Setting');
			$('#transetModal').modal('show');

			var edittext = $(this).val().split('|');
        	var editid = edittext[0];
        	var code = edittext[1];
        	var desc = edittext[2];
        	var prefix = edittext[3];
        	var prefixfm = edittext[4];
        	var nextno = edittext[5];
        	var nextnolength = edittext[6];
        	$('#masterid').val(editid);
        	$('#hdnaction').val('EDIT');
        	$('#code').val(code);
			$('#desc').val(desc);
			$('#prefix').val(prefix);	
			$('#prefixfm').val(prefixfm);
			$('#nextno').val(nextno);
			$('#nextnolength').val(nextnolength);		
			$('#er1').html("");
			$('#er2').html("");
			$('#er3').html("");				
			$('#er5').html("");
			$('#er6').html("");	

			$('#code').keyup(function(){
			   $('#er1').html(""); 
			});
			$('#desc').keyup(function(){
			   $('#er2').html(""); 
			});
			$('#prefix').keyup(function(){
			   $('#er3').html(""); 
			});	
			$('#nextno').keyup(function(){
			   $('#er5').html(""); 
			});
			$('#nextnolength').keyup(function(){
			   $('#er6').html(""); 
			});	

		});

		$('.delete-task').click(function(e){      
	       
        	$.ajax({
				url: "{{ url('/delete-transaction') }}",
				method: 'get',
				data:  { masterid : $(this).val() },
				
			}).done( function(data, textStatus, jqXHR) {
				window.location.href = "{{ url('/transactionsetting') }}";   
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown+'|'+textStatus);
			});
			
    	});

    	$('.deleteAll-task').click(function(e){      		 	       
        	var master =$('#master').val();
    		deleteAllmaster = $('#deleteAllmaster').val(master);
    		
			$('.deleteAllmodal-title').html('Delete' + " " + deleteAllmaster);
			$('#deleteAllModal').modal('show');
			$('.deleteAllmodal-title').append(master);
			
    	});

	});

/*	function deleteAll(){
		var formObj = $('#deleteAllform');
		var formURL = formObj.attr("action");//{{ url('/readfile') }};//
		var forsmata = new Forsmata(fomObj);

		$.ajax({
			url: formURL,
			method: 'post',
			data:  forsmata,
			
		}).done( function(data, textStatus, jqXHR) {
			console.log(data);
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(data);
		});

	}*/

	function deleteAllcheckeditems(){
		var tray = [];
		$(".checkboxes:checked").each(function () {
			tray.push($(this).val());
		});
		var traycount = tray.length;

		$.ajax({
			url: "{{ url('/deleteAll-transaction') }}",
			method: 'get',
			data:  { 
				tray : tray, 
				traycount : traycount
			},
			
		}).done( function(data, textStatus, jqXHR) {
			/*console.log(data);*/
			window.location.href = "{{ url('/transactionsetting') }}";   
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown+'|'+textStatus);
		});
	}

	function Add_Records(){
		var action = $('#hdnaction').val();
		var code = $('input[name=code]').val();
		var desc = $('textarea#desc').val();
		var prefix = $('input[name=prefix]').val();
		var prefixfm = $('input[name=prefixfm]').val();
		var nextno = $('input[name=nextno]').val();
		var nextnolength = $('input[name=nextnolength]').val();
		var masterid = $('input[name=masterid]').val();
		var myData = {'code':code,'desc':desc,'prefix':prefix,'prefixfm':prefixfm,'nextno':nextno,'nextnolength':nextnolength,'masterid':masterid};

		switch (code) {
	        case '':                     
	            $('#er1').html("Code field is blank"); $('#er1').css('color', 'red'); return false;break;
        }
        switch (desc) {
	        case '':                     
	            $('#er2').html("Description field is blank"); $('#er2').css('color', 'red'); return false;break;
        }
        switch (prefix) {
	        case '':                     
	            $('#er3').html("Prefix field is blank"); $('#er3').css('color', 'red'); return false;break;
        }
        switch (nextno) {
	        case '':                     
	            $('#er5').html("Next number field is blank"); $('#er5').css('color', 'red'); return false;break;
        }
        switch (nextnolength) {
        	case '':
        		$('#er6').html("Next number length field is blank"); $('#er6').css('color', 'red'); return false;break;
        }
        if(nextnolength > 10){
        		$('#er6').html("Next number length field must below or equal to 10 only");
        		$('#er6').css('color', 'red');
        		 return false;
        }

		if(action == 'ADD')
		{
			$.post("{{ url('/add-transaction') }}", 
			{
				_token : $('meta[name=csrf-token]').attr('content')
				, data : myData
			}).done(function(data, textStatus, jqXHR){
				/*console.log(data);*/
				$('#transetModal').modal('hide');
				$('#confirmModal').modal('show');
			
				$('#modalTitle').html('Success Message');
				$('#confirmMessage').html('Record succesfully saved');
					
				$('#confirmOk').click(function(){
					window.location.href="{{ url('/transactionsetting') }}";
				});
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown+'|'+textStatus);
			});
		}
		else if(action == 'EDIT')
		{
			$.post("{{ url('/update-transaction') }}", 
			{
				_token : $('meta[name=csrf-token]').attr('content')
				, data : myData
			}).done(function(data, textStatus, jqXHR){
				/*console.log(data);*/
				$('#transetModal').modal('hide');
				$('#confirmModal').modal('show');

				$('#modalTitle').html('Success Message');
				$('#confirmMessage').html('Record succesfully updated');
					
				$('#confirmOk').click(function(){
					window.location.href="{{ url('/transactionsetting') }}";
				});
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown+'|'+textStatus);
			});
		}
	}
	
</script>
