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

	$('#modaldelete').on('click',function(){
		deleteAllcheckeditems();
	});

	$('#add').on('click', function(e) {
		var master = $('#master').val();
		e.preventDefault();
		$('.modal-title').html('Add Sold Master');
		$('#soldtoModal').modal('show');
		$('#hdnaction').val('ADD');

		$('#code').val("");
		$('#compname').val("");
		$('#description').val("");	
		$('#er1').html("");
		$('#er2').html("");
		$('#er3').html("");
		
		$('#code').keyup(function(){
		   $('#er1').html(""); 
		});
		$('#compname').keyup(function(){
		   $('#er2').html(""); 
		});
		$('#description').keyup(function(){
		   $('#er3').html(""); 
		});	
	});

	$('.edit-task').on('click', function(e) {
		

		var edittext = $(this).val().split('|');
    	var editid = edittext[0];
    	var code = edittext[1];
    	var compname = edittext[2];
    	var description = edittext[3];
    	$('#masterid').val(editid);
  		$('#hdnaction').val('EDIT');
		$('.modal-title').html('Update Sold Master');
		$('#soldtoModal').modal('show');

		$('#code').val(code);
		$('#compname').val(compname);
		$('#description').val(description);	
		$('#er1').html("");
		$('#er2').html("");
		$('#er3').html("");	

		$('#code').keyup(function(){
		   $('#er1').html(""); 
		});
		$('#compname').keyup(function(){
		   $('#er2').html(""); 
		});
		$('#description').keyup(function(){
		   $('#er3').html(""); 
		});	

	});

	$('.delete-task').click(function(e){      
	 	       
    	$.ajax({
			url: DeleteTaskUrl,
			method: 'get',
			data:  { masterid : $(this).val() },
			
		}).done( function(data, textStatus, jqXHR) {
			window.location.href = SoldToUrl;   
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
		deleteAll();
	});

});

function deleteAllcheckeditems(){
	var tray = [];
	$('.checkboxes:checked').each(function(){
		tray.push($(this).val());
	});

	var traycount = tray.length;

	$.ajax({
			url: DeleteAll,
			method:'get',
			data:{ 
				tray : tray,
				traycount : traycount
			},			
	}).done(function(data, textStatus, jqXHR){
		window.location.href= SoldToUrl;
	}).fail(function(jqXHR, textStatus,errorThrown){
		console.log(errorThrown+'|'+textStatus);
	});
}

function Add_Records(){
	var action = $('#hdnaction').val();
	var code = $('input[name=code]').val();
	var compname = $('input[name=compname]').val();
	var description = $('textarea#description').val();		
	var masterid = $('input[name=masterid]').val();
	var myData = {'code':code,'compname':compname,'description':description,'masterid':masterid};

	switch(code){
		case '':
			$('#er1').html("Code field is blank"); $('#er1').css('color', 'red'); return false;
		break;
	}
	switch(compname){
		case '':
			$('#er2').html("Company name field is blank"); $('#er2').css('color', 'red'); return false;
		break;
	}
	switch(description){
		case '':
			$('#er3').html("Description field is blank"); $('#er3').css('color', 'red'); return false;
		break;
	}


	if(action == 'ADD')
	{
		$.post(AddUrl, 
		{
			_token : token
			, data : myData
		}).done(function(data, textStatus, jqXHR){
			/*console.log(data);*/
			$('#soldtoModal').modal('hide');
			$('#confirmModal').modal('show');
		
			$('#modalTitle').html('Success Message');
			$('#confirmMessage').html('Record succesfully saved');
				
			$('#confirmOk').click(function(){
				window.location.href= SoldToUrl;
			});
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown+'|'+textStatus);
		});
	}
	else if(action == 'EDIT')
	{
		$.post(UpdateUrl, 
		{
			_token : token
			, data : myData
		}).done(function(data, textStatus, jqXHR){
			/*console.log(data);*/
			$('#soldtoModal').modal('hide');
			$('#confirmModal').modal('show');
		
			$('#modalTitle').html('Success Message');
			$('#confirmMessage').html('Record succesfully updated');
				
			$('#confirmOk').click(function(){
				window.location.href= SoldToUrl;
			});
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown+'|'+textStatus);
		});
	}
}