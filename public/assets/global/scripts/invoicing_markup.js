var dataColumn = [
	{ data: 'prod_line', name: 'prod_line' },
	{ data: 'mark_up', name: 'mark_up' },
	{ data: 'update_user', name: 'update_user' },
	{ data: 'updated_at', name: 'updated_at' },
	{ data: 'action', name: 'action', orderable: false, searchable: false },
];

$( function() {
	getDatatable('tbl_mark_up',showMarkUpURL,dataColumn,[],0);
	$('#btn_add_mark_up').on('click', function() {
		$('#formMarkUpModal').modal('show');
	});

	$('#frm_markup').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data,textStatus,jqXHR) {
			msg(data.msg,data.status);
			getDatatable('tbl_mark_up',showMarkUpURL,dataColumn,[],0);
		}).fail(function(data,textStatus,jqXHR) {
			var errors = data.responseJSON;
			MarkUpErrors(errors);
		});
	});

	$('.validate').on('change', function(e) {
		var no_error = $(this).attr('id');
		MarkUpNoErrors(no_error)
	});

	$('#tbl_mark_up_body').on('click', '.edit_markup', function(e) {
		e.preventDefault();
		$('#id').val($(this).attr('data-id'));
		$('#prod_line').val($(this).attr('data-prod_line'));
		$('#mark_up').val($(this).attr('data-mark_up'));

		$('#formMarkUpModal').modal('show');
	});

	$('#tbl_mark_up_body').on('click', '.delete_markup', function(e) {
		$('#delete_id').val($(this).attr('data-id'));
		delete_modal();
	});

	$('#btn_confirm_delete').on('click', function() {
		$.ajax({
			url: DeleteMarkUpURL,
			type: 'POST',
			dataType: 'JSON',
			data: {_token: token, id: $('#delete_id').val()},
		}).done(function(data,textStatus,jqXHR) {
			msg(data.msg,data.status);
			$('#delete_modal').modal('hide');
			getDatatable('tbl_mark_up',showMarkUpURL,dataColumn,[],0);
		}).fail(function(data,textStatus,jqXHR) {
			msg("There was an error while deleting data.",'error');
		});
	});
	
});

function MarkUpErrors(errors) {
	$.each(errors, function(i, x) {
		switch (i) {
			case 'item':
				$('#item_div').addClass('has-error');
				$('#item_msg').html(x);
				break;
			case 'item_desc':
				$('#item_desc_div').addClass('has-error');
				$('#item_desc_msg').html(x);
				break;
			case 'classification':
				$('#classification_div').addClass('has-error');
				$('#classification_msg').html(x);
				break;
		}
	});
}

function MarkUpNoErrors(no_error) {
	switch (no_error) {
		case 'item':
			$('#item_div').removeClass('has-error');
			$('#item_msg').html('');
			break;
		case 'item_desc':
			$('#item_desc_div').removeClass('has-error');
			$('#item_desc_msg').html('');
			break;
		case 'classification':
			$('#classification_div').removeClass('has-error');
			$('#classification_msg').html('');
			break;
	}
}