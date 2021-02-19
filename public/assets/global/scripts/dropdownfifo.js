var tbl_reason = ""

$(function(){
	GetFifoReason();
	checkAllCheckboxesInTable('.checkall','.check_lot');

$('#add_reason').on('click',function(){
 	$('#myModal').modal('show');
 	$('.modal-title').text('Add Reason');
 	clear();

})

$('#delete_button').on('click',function(){
	var arr_id = [];
	$('.check_lot:checked').each(function(index, el) {
		arr_id.push($(this).val());
	   			
	});
	if(arr_id.length > 0)
	{
		$('#deleteAllModal').modal('show');	


	}else{
		msg('Please check atleast 1 item to delete records!','failed');
	}
})

$('#btn_delete').on('click',function(){
	var delete_data = [];
	$('.check_lot:checked').each(function(index, el) {
		delete_data.push($(this).val());
		// alert(delete_data);   			
	});
	if (delete_data.length > 0 ) {
		Delete(delete_data);
	}else{

	}
});

$('#bt_reason_save').on('click',function(){
 var id = $('#id').val();
 var reason = $("#new_reason").val().trim();

 	if(reason == ""){
 		msg('Reason required','failed');
 	}else if(id !== ""){
		Update(id)
	}else{
	 	AddnewReason();
	}
})
 tbl_reason = $('#tbl_reason').DataTable();
$('#btn_close_modal').on('click',function(){
	$('#myModal').modal('hide');
	clear();	
})

$('#tbl_reason').on('click','.showreason',function(){

   $('#id').val($(this).attr('data-id'));
   $('#new_reason').val($(this).attr('data-dropdown_reason'));
   $('#myModal').modal('show');
   	$('.modal-title').text('Edit Reason');

})




$('#btn_close').on('click',function(){
	// alert('hello');
	$('#deleteAllModal').modal('hide');
})


})
function getFifoTable(data) {

  tbl_reason =	$('#tbl_reason').DataTable({
  		destroy: true,
		data:data,
		columns:[

		  		{
  			data:function(x){
  				return '<input type="checkbox" class="check_lot" value="' + x.id + '"' + 'data-detailid="'+x.dropdown_reason+'">';
  			}

  			},
			{data: function (x) 
				{
					return '<td>'+
								'<button class="btn btn-sm btn-primary showreason" data-id="'+x.id+'"data-dropdown_reason="'+x.dropdown_reason+'">'+
									'<i class="fa fa-edit"></i>'+
								'</button>'+
							'</td>'
				}
			},
			{
			data:'id',
			"visible": false,
            "searchable": false
			},

			{data:'dropdown_reason'}
		
		],
	})
}
function GetFifoReason(){
	$.ajax({
		url: GetReasonURL,
		type: 'GET',
		dataType: 'JSON',
		
	})
	.done(function(data,textStatus,jqXHR) {
		getFifoTable(data);
		
	})
	.fail(function(data,textStatus,jqXHR) {
		console.log("error");
	})	
}

function AddnewReason(){
    var dropdown_reason = $('#new_reason').val();
	$.ajax({
		url: CheckReasonURL,
		type: 'GET',
		dataType: 'JSON',
		data: 
		{
			_token: token,
			dropdown_reason: dropdown_reason
		},
	})
	.done(function(data,textStatus,jqXHR) {
		if(data.length >0){
			msg('Reason already exists!','failed')
		}else{
			$.ajax({
			url: AddURL,
			type: 'POST',
			dataType: 'JSON',
			data: {
			_token: token,
			dropdown_reason: dropdown_reason
		},
	})
		    .done(function(data,textStatus,jqXHR) {
			console.log("success");
			msg('Added new reason successfully saved!','success');
			$('#myModal').modal('hide');
			GetFifoReason();
		})
		.fail(function(data,textStatus,jqXHR) {
		console.log("error");
		})
	 }
		
	})
	.fail(function(data,textStatus,jqXHR) {
		console.log("error");
	})
	
}

function clear(){
	$('#id').val("");
	$('#new_reason').val("");
}

function Update(id){
	var dropdown_reason = $('#new_reason').val();
	$.ajax({
		url: UpdateURL,
		type: 'POST',
		dataType: 'JSON',
		data: {
			_token: token,
			dropdown_reason: dropdown_reason,
			id: id

		},
	})
	.done(function(data,textStatus,jqXHR) {
			msg('Reason successfully updated!','success');
			$('#myModal').modal('hide');
			GetFifoReason();
			// $('#delete_button').attr('disabled',true);
			clear();		
	})
	.fail(function(data,textStatus,jqXHR) {
		console.log("error");
	})	
}

function Delete(arr_id){
	$.ajax({
		url: DeleteURL,
		type: 'POST',
		dataType: 'JSON',
		data:
		 {
		 	_token: token,
			ids:arr_id,
		 },
	})
	.done(function(data,textStatus,jqXHR) {
		if(data > 0){
		msg('Successfully deleted!','success');
		$('#deleteAllModal').modal('hide');
		GetFifoReason();
		// $('#delete_button').attr('disabled',true);

		}else{
			msg('There is something error while deleteding!','failed');
		}
		console.log("success");	})
	.fail(function(data,textStatus,jqXHR) {
		console.log("error");
	})	
}