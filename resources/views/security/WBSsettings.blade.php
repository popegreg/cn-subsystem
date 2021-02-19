@extends('layouts.master')
@section('title')
	WBS Settings | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_WBSSET'))  <!-- Please update "2001" depending on the corresponding program_code -->
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
							<i class="fa fa-barcode"></i>  WBS Settings
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
												<th>
												</th>
											
												<th>
													Setting ID
												</th>
												<th>
													Name
												</th>
												<th>
													Description
												</th>
												<th>
													Description
												</th>
											</tr>
										</thead>

										<tbody>
											@foreach ($tableData as $dest)
												<tr>
													
													<td style="width: 5%">

							                           <input type="checkbox" class="form-control input-sm checkboxes" name="checkitem" id="checkitem'.{{$dest->id}}" value="{{$dest->id}}"></input>
							                           
						            				</td>
						   															
													<td style="width: 7%">
							                           
							                            <button type="button" name="edit-task" class="btn btn-sm btn-primary edit-task" value="{{$dest->id . '|' . $dest->name . '|' .$dest->description. '|' .$dest->value}}" id="editTask{{$dest->id}}">
						                						<i class="fa fa-edit"></i> 
						            					</button>
						            				</td>
						            				<td>{{$dest->id}}</td>
													<td>{{$dest->name}}</td>
													<td>{{$dest->description}}</td>
													<td>{{$dest->value}}</td>
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
								<a href="javascript:;" class="btn btn-danger btn-lg deleteAll-task" id="openModalDelete" name="deleteAll-task">
									<i class="fa fa-trash"></i> Delete
								</a>
							</div>
						</div>


					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
			

	<!-- Add Modal -->
	<div id="wbssetModal" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog gray-gallery">
			<div class="modal-content ">
				<div class="modal-header">
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12">
							<form method="POST" class="form-horizontal" id="wbsfrmsml">
								{{ csrf_field() }}
								<div class="form-group">
									<div class="col-sm-7">
										<p>
											Value field is required.
										</p>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Setting ID</label>
									<div class="col-sm-9">
										<input type="text" class="form-control input-sm" id="setid" name="setid" disabled="disable">

									</div>
									
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Name</label>
									<div class="col-sm-9">
										<input type="text" class="form-control input-sm" id="name" name="name" maxlength="40" >
										<div id="er1"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Description</label>
									<div class="col-sm-9">
										<textarea name="desc" id="desc" class="form-control" style="resize:none" maxlength="100" ></textarea>
										<div id="er2"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Value</label>
									<div class="col-sm-9">
										<input type="text" class="form-control input-sm" id="val" name="val" maxlength="40" >
										<div id="er3"></div>
									</div>
									
								</div>							
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<input type="hidden" class="form-control input-sm" id="masterid" name="masterid" maxlength="40" >
					<input type="hidden" class="form-control input-sm" id="hdnaction" name="hdnaction" maxlength="40" value="ADD">
					<button type="button" id='modalsave' onclick="javascript:Add_Records();"  class="btn btn-success">Save</button>
					<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
				</div>
			</div>
			</form>
		</div>
	</div>
	<!-- End of Add Modal -->


	<!--delete all modal-->
	<div id="deleteAllModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm gray-gallery">

								<!-- Modal content-->
			<form class="form-horizontal" id="deleteAllform" role="form" method="POST">
				<div class="modal-content ">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="deleteAll-title">Delete WBS Settings</h4>
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
						<a href="javascript:;" class="btn btn-success" id="modaldelete" ><i class="fa fa-save"></i> Yes</a>
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
		

		$('#modaldelete').on('click', function() {
			deleteAllcheckeditems();
		});

		$('#add').on('click', function(e) {			
			var master = $('#master').val();
			e.preventDefault();
			$('.modal-title').html('Add WBS Settings');
			$('#hdnaction').val('ADD');
			$('#wbssetModal').modal('show');
			/*$('#setid').val(editid);*/
			$('#setid').val("");
			$('#name').val("");
			$('#desc').val("");
			$('#val').val("");	
			$('#er1').html("");
			$('#er2').html("");
			$('#er3').html("");	

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

		$('.edit-task').on('click', function(e) {
			var edittext = $(this).val().split('|');
        	var editid = edittext[0];
        	var name = edittext[1];
        	var desc = edittext[2];
        	var value = edittext[3];
        	$('#masterid').val(editid);
			$('.modal-title').html('Update WBS Setting');
			$('#hdnaction').val('EDIT');
			$('#wbssetModal').modal('show');
			$('#setid').val(editid);
			$('#name').val(name);
			$('#desc').val(desc);
			$('#val').val(value);
			$('#er1').html("");
			$('#er2').html("");
			$('#er3').html("");			

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

		$('.deleteAll-task').click(function(e){
			var checked = $('')
			var master =$('#master').val();
			deleteAllmaster = $('#deleteAllmaster').val(master);
			
			$('.deleteAllmodal-title').html('Delete' + " " + deleteAllmaster);
			$('#deleteAllModal').modal('show');
			$('.deleteAllmodal-title').append(master);		 	
		});

		
		
	});//end of $(document).ready()------------------------------------

	/*function deleteAll(){
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
		var traycount =tray.length;

		$.ajax({
			url: "{{ url('/deleteAll-setting') }}",
			method: 'get',
			data:  { 
				tray : tray, 
				traycount : traycount
			},
			
		}).done( function(data, textStatus, jqXHR) {
			/*console.log(data);*/
			window.location.href = "{{ url('/wbssetiing') }}";   
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown+'|'+textStatus);
		});
	}

	function Add_Records(){
		var action = $('#hdnaction').val();
		var name = $('input[name=name]').val();
		var desc = $('textarea#desc').val();
		var val = $('input[name=val]').val();
		var masterid = $('input[name=masterid]').val();
		var myData = {'name':name,'desc':desc,'val':val,'masterid':masterid};
		if(name == ""){	
			$('#er1').html("Name field is blank"); 
            $('#er1').css('color', 'red');       
			return false;	
		} 
		if (desc == ""){
			$('#er2').html("Description field is blank"); 
            $('#er2').css('color', 'red');        
			return false;
		}
		if (val == ""){
			$('#er3').html("Value field is blank"); 
            $('#er3').css('color', 'red');
			return false;
		}

		if(action == 'ADD')
		{

			$.post("{{ url('/add-setting') }}", 
			{
				_token : $('meta[name=csrf-token]').attr('content')
				, data : myData
			}).done(function(data, textStatus, jqXHR){
				/*console.log(data);*/
				
				$('#wbssetModal').modal('hide');
				$('#confirmModal').modal('show');
			
				$('#modalTitle').html('Success Message');
				$('#confirmMessage').html('Record succesfully saved');
					
				$('#confirmOk').click(function(){
					window.location.href="{{ url('/wbssetiing') }}";
				});
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown+'|'+textStatus);
			});
		}
		else if(action == 'EDIT')
		{
			$.post("{{ url('/update-setting') }}", 
			{
				_token : $('meta[name=csrf-token]').attr('content')
				, data : myData
			}).done(function(data, textStatus, jqXHR){
				/*console.log(data);*/
				$('#wbssetModal').modal('hide');
				$('#confirmModal').modal('show');

				$('#modalTitle').html('Success Message');
				$('#confirmMessage').html('Record succesfully updated');
					
				$('#confirmOk').click(function(){
					window.location.href="{{ url('/wbssetiing') }}";
				});
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown+'|'+textStatus);
			});
		}


	}

</script>
