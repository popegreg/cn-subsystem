@extends('layouts.master')
@section('title')
	Destination & Classification Master | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_DESTI'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
							<i class="fa fa-truck"></i>  Destination & Classification
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
					        <div class="col-sm-8 col-sm-offset-2">     
					                <div class="form-group">
					                    <div class="col-sm-1">
					                    	<label class="control-label">Master: </label>
					                    </div>
					                    <div class="col-sm-6">
					                        <select class="form-control input-sm" name="master" id="master">
					                            <option value="Product Destination"  
						                            <?php 
						                            if(isset($selectedoption)){ 
						                            	if($selectedoption=='Product Destination') 
						                            		{								                  		
						                            		 echo 'selected="selected"'; 
						                            		}
						                            	}
						                            ?>>Product Destination
						                        </option>
					                            <option value="Line Destination" 
					                            	<?php 
					                            	if(isset($selectedoption)){
					                            	 	if($selectedoption=='Line Destination') 
					                            	 	{ 
					                            	 		echo 'selected="selected"'; 
					                            		}
					                            	 } 
					                            	 ?>>Line Destination
					                            </option>
					                            <option value="Classification"
						                             <?php 
						                             if(isset($selectedoption))
						                             	{ if($selectedoption=='Classification')
						                             	 	{ 
						                             	 	echo 'selected="selected"'; 
						                             	 	} 
						                             	 } 
						                            ?>>Classification
						                        </option>
					                        </select>
					                    </div>  
					                </div>							              
					        </div>
					    </div>

					    <div class="row">
					        <div class="col-sm-8 col-sm-offset-2 table-responsive">
				                <table class="table table-striped table-bordered table-hover" id="sample_3">
										<thead>
											<tr>
												<th class="table-checkbox" style="width: 5%">
													<input type="checkbox" class="group-checkable checkAllitems" data-set="#sample_3 .checkboxes"/>
												</th>
												<th></th>
												<th>Description</th>
											</tr>
										</thead>

										<tbody>
											@foreach ($tableData as $dest)
												<tr>	
													<td style="width: 5%">
							                           <input type="checkbox" class="form-control input-sm checkboxes" name="checkitem" id="checkitem" value="{{$dest->id}}"></input>
						            				</td>								   									
													<td style="width: 7%">
							                           
							                            <button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="{{$dest->id . '|' . $dest->description}}">
						                						<i class="fa fa-edit"></i> 
						            					</button>
						            				</td>
													<td>{{$dest->description}}</td>
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
					          	<a href="#" id="deleteAll" class="btn btn-danger btn-lg deleteAll-task">
					                <i class="fa fa-trash"></i> Delete
					            </a>
					        </div>
					    </div>
					
					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->

				<!-- Modal -->
				<div id="myModal" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm gray-gallery">

						<!-- Modal content-->
						<form class="form-horizontal" id="destinationform" role="form" method="POST">
							<div class="modal-content ">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title"></h4>
								</div>
								<div class="modal-body">
									<div class="row">
										
										{!! csrf_field() !!}
										<div class="col-sm-12">
											
											<div class="form-group">
												<label for="inputname" class="col-sm-4 control-label">*Description</label>
												<div class="col-sm-8">
													<input type="text" class="form-control input-sm" id="inputname" name="description" maxlength="40">
													<div id="er1"></div>
													<input type="hidden" value="" name="dbmaster" id="dbmaster" />
												</div>
												
												
											</div>
										</div>
										
									</div>
								</div>
								<div class="modal-footer">
									<input type="hidden" class="form-control input-sm" id="masterid" name="masterid" maxlength="40" >
									<input type="hidden" class="form-control input-sm" id="hdnaction" name="hdnaction" maxlength="40" value="ADD">
									<button type="button" onclick="javascript:Add_Records();" class="btn btn-success" id="modalsave" ><i class="fa fa-save"></i> Save</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!--delete all modal-->
				<div id="deleteAllModal" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm gray-gallery">

						<!-- Modal content-->
						<form class="form-horizontal" id="deleteAllform" role="form">
							<div class="modal-content ">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="deleteAll-title">Delete Destination</h4>
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

				<!---->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

@endsection

<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	$( document ).ready(function(e) {

		var prodlist = $('#prodDestinationlist');
        var linelist = $('#lineDestinationlist');
        var classlist = $('#classificationlist');
        prodlist.show();
        linelist.hide();
        classlist.hide();

        $('.deleteAll-task').addClass("disabled");
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
			var master = $('#master').val();
			e.preventDefault();
			$('.modal-title').html('');
			$('#myModal').modal('show');
			$('.modal-title').append(master);
			$('#dbmaster').val(master);
			$('#hdnaction').val('ADD');
			$('#inputname').val("");
			$('#er1').html("");
			
			$('#inputname').keyup(function(){
			   $('#er1').html(""); 
			});
		});

        $('.edit-task').on('click', function(e) {
        	var master = $('#master').val();   
        	var edittext = $(this).val().split('|');
        	var editid = edittext[0];
        	var editdesc = edittext[1];

        	$('#masterid').val(editid);   
			$('.modal-title').html('');
			$('#myModal').modal('show');
			$('.modal-title').append(master);
			$('#dbmaster').val(master);
			$('#inputname').val(editdesc);
			$('#hdnaction').val('EDIT');
			$('#er1').html("");

			$('#inputname').keyup(function(){
			   $('#er1').html(""); 
			});
		});


        $('#master').change(function(){
        	var master = $(this).find(':selected').val();       	
        	window.location.href = "{{ url('/destination?option=') }}" + master;       	
        });

        $('.delete-task').click(function(e){      
	    	var master = $('#master').find(':selected').val();
        	$.ajax({
				url: "{{ url('/delete-post') }}",
				method: 'get',
				data:  { masterid : $(this).val() },
				
			}).done( function(data, textStatus, jqXHR) {
				window.location.href = "{{ url('/destination?option=') }}" + master;
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown+'|'+textStatus);
			});
			
    	});

    
    	$('#deleteAll').click(function(){	
    		var master =$('#master').val();
    		deleteAllmaster = $('#deleteAllmaster').val(master);
    		
			$('.deleteAllmodal-title').html('Delete' + " " + deleteAllmaster);
			$('#deleteAllModal').modal('show');
			$('.deleteAllmodal-title').append(master);

    	});

    	

	});

	function update(){
		var formObj = $('#updateform');
		var formURL = formObj.attr("action");//{{ url('/readfile') }};//
		var formData = new FormData(fomObject);

		$.ajax({
			url: formURL,
			method: 'POST',
			data:  formData,
			
		}).done( function(data, textStatus, jqXHR) {
			console.log(data);
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(data);
		});

	}

	function deleteAllcheckeditems(){
		var master =$('#master').val();
		var tray = [];
		$(".checkboxes:checked").each(function () {
			tray.push($(this).val());
		});
		var traycount =tray.length;
		
		$.ajax({
			url: "{{ url('/deleteAll-post') }}",
			method: 'get',
			data:  { 
				tray : tray, 
				traycount : traycount,
				master : master
			},
			
		}).done( function(data, textStatus, jqXHR) {
			window.location.href = "{{ url('/destination?option=') }}" + master;   
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown+'|'+textStatus);

		});
	}

	function Add_Records(){
		var action = $('#hdnaction').val();
		var desc = $('input[name=description]').val();
		var dbmaster = $('input[name=dbmaster]').val();
		var masterid = $('input[name=masterid]').val();
		var myData = {'desc':desc,'dbmaster':dbmaster,'masterid':masterid};

		switch(desc){
			case '':
				$('#er1').html("Description field is blank"); 
	            $('#er1').css('color', 'red');
	            return false;       
			break;
		}

		if(action == 'ADD')
		{
			$.post("{{ url('/add-description') }}", 
			{
				_token : $('meta[name=csrf-token]').attr('content')
				, data : myData
			}).done(function(data, textStatus, jqXHR){
				/*console.log(data);*/				
				$('#myModal').modal('hide');
				$('#confirmModal').modal('show');
			
				$('#modalTitle').html('Success Message');
				$('#confirmMessage').html('Record succesfully saved');
					
				$('#confirmOk').click(function(){
					window.location.href= "{{ url('/destination?option=') }}" + dbmaster;
				});
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown+'|'+textStatus);
			});
		}
		else if(action == 'EDIT')
		{
			$.post("{{ url('/update-post') }}", 
			{
				_token : $('meta[name=csrf-token]').attr('content')
				, data : myData
			}).done(function(data, textStatus, jqXHR){
				/*console.log(data);*/
				$('#myModal').modal('hide');
				$('#confirmModal').modal('show');
			
				$('#modalTitle').html('Success Message');
				$('#confirmMessage').html('Record succesfully updated');
					
				$('#confirmOk').click(function(){
					window.location.href= "{{ url('/destination?option=') }}" + dbmaster;
				});
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown+'|'+textStatus);
			});
		}
	}

	/*function deleteAll(){
		var formObj = $('#deleteAllform');
		var formURL = formObj.attr("action");//{{ url('/readfile') }};//
		var formData = new FormData(fomObject);

		$.ajax({
			url: formURL,
			method: 'POST',
			data:  formData,
			
		}).done( function(data, textStatus, jqXHR) {
			console.log(data);
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(data);
		});

	}*/

	/*function save(){
		var formObj = $('#destinationform');
		var formURL = formObj.attr("action");//{{ url('/readfile') }};//
		var formData = new FormData(this);

		$.ajax({
			url: formURL,
			method: 'POST',
			data:  formData,
			
		}).done( function(data, textStatus, jqXHR) {
			console.log(data);
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(data);
		});

	}*/

	
	

	

</script>
