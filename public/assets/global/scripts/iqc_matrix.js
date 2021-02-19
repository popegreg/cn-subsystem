var dataColumn = [
	{ data: function(data) {
        return '<input type="checkbox" class="check_item" value="'+data.id+'">';
    }, name: 'id', orderable: false, searchable: false },
	{ data: 'item', name: 'item' },
	{ data: 'item_desc', name: 'item_desc' },
	{ data: 'classification', name: 'classification' },
	{ data: 'update_user', name: 'update_user' },
	{ data: 'updated_at', name: 'updated_at' },
	{ data: 'action', name: 'action',orderable: false, searchable: false },
];

$( function() {
	showClassification();
	checkAllCheckboxesInTable('.check_all','.check_item');
	getDatatable('tbl_matrix',showMatrixURL,dataColumn,[],0);

	$('#btn_add').on('click', function() {
		$('#formMatrixModal').modal('show');
	})

	$('#tbl_matrix_body').on('click','.btn_edit', function() {
		$('#id').val($(this).attr('data-id'));
		$('#item').val($(this).attr('data-item'));
		$('#item_desc').val($(this).attr('data-item_desc'));
		$('#classification').val($(this).attr('data-classification'));
		$('#formMatrixModal').modal('show');
	});

	$('#frm_matrix').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data,textStatus,jqXHR) {
			msg(data.msg,data.status);
			getDatatable('tbl_matrix',showMatrixURL,dataColumn,[],0);
		}).fail(function(data,textStatus,jqXHR) {
			var errors = data.responseJSON;
			MatrixErrors(errors);
		});
	});

	$('.validate').on('change', function(e) {
		var no_error = $(this).attr('id');
		MatrixNoError(no_error)
	});

	$('#btn_delete').on('click', function() {
		delete_modal();
	});

	$('#btn_confirm_delete').on('click', function() {
		delete_items('.check_item',DeleteMatrixURL);
	});

	$('#item').on('change', function() {
		getDetails('item',$(this).val());
	});

	$('#item_desc').on('change', function() {
		getDetails('item_desc',$(this).val());
	});

	$('#frm_upload_matrix').on('submit', function(e) {
		$('#progress-close').prop('disabled', true);
		$('#progressbar').addClass('progress-striped active');
		$('#progressbar-color').addClass('progress-bar-success');
		$('#progressbar-color').removeClass('progress-bar-danger');
		$('#progress').modal('show');

		var formObj = $('#frm_upload_matrix');
		var formURL = formObj.attr("action");
		var formData = new FormData(this);
		var fileName = $("#matrix_file").val();
		var ext = fileName.split('.').pop();
		var tbl_batch = '';
		e.preventDefault(); //Prevent Default action.

		if ($("#matrix_file").val() == '') {
			$('#progress-close').prop('disabled', false);
			$('#progress-msg').html("Upload field is empty");
		} else {
			if (fileName != ''){
				if (ext == 'xls' || ext == 'xlsx' || ext == 'XLS' || ext == 'XLSX') {
					$('.myprogress').css('width', '0%');
					$('#progress-msg').html('Uploading in progress...');
					var percent = 0;

					$.ajax({
						url: formURL,
						type: 'POST',
						data:  formData,
						mimeType:"multipart/form-data",
						contentType: false,
						cache: false,
						processData:false,
						xhr: function() {
							var xhr = new window.XMLHttpRequest();
							if (xhr.upload) {
								xhr.upload.addEventListener("progress", function (evt) {

									var loaded = evt.loaded;
									var total = evt.total;

									if (evt.lengthComputable) {
										percent = Math.ceil(loaded / total * 100);
										// var percentComplete = evt.loaded / evt.total;
										// percentComplete = parseInt(percentComplete * 100);

										//if (percentComplete < 100) {
										// $('.myprogress').text(percent + '%');
										$('.myprogress').css('width', percent + '%');
										//}
										if (percent == 100) {
											$('.myprogress').css('width', percent + '%');
											$('#progress-msg').html('Finalizing upload...');
										}
									}
								}, false);
							}
							return xhr;
						}
					}).done(function(data) {
						$('#progressbar').removeClass('progress-striped active');
						var datas = JSON.parse(data);
						console.log(datas);

						if (datas.status == 'success') {
							getDatatable('tbl_matrix',showMatrixURL,dataColumn,[],0);
							$('#progress-close').prop('disabled', false);
							$('#progress-msg').html("Items were successfully uploaded.");
						} else {
							$('#progress-close').prop('disabled', false);
							$('#progressbar-color').removeClass('progress-bar-success');
							$('#progressbar-color').addClass('progress-bar-danger');
							$('#progress-msg').html(datas.msg);
						}
					}).fail(function(data) {
						$('#progress-close').prop('disabled', false);
						$('#progressbar').removeClass('progress-striped active');
						$('#progressbar-color').removeClass('progress-bar-success');
						$('#progressbar-color').addClass('progress-bar-danger');
						$('#progress-msg').html("There's some error while processing.");
					});
				} else {
					$('#progress-close').prop('disabled', false);
					$('#progress-msg').html("Please upload a valid excel file.");
				}
			}
		}
	});
});

function getDetails(id,value) {
	$.ajax({
		url: ItemDetailsURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			field: id,
			value: value
		},
	}).done(function(data, textStatus,jqXHR) {
		$('#item').val(data.item);
		$('#item_desc').val(data.item_desc);
		$('#classification').val(data.classification);
	}).fail(function(data, textStatus,jqXHR) {
		console.log("error");
	});
}

function showClassification(argument) {
	var classification = '<option></option>';
	$('#classification').html(classification);
	$.ajax({
		url: showClassificationURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus,jqXHR) {
		$.each(data, function(i, x) {
			classification = '<option value="'+x.classification+'">'+x.classification+'</option>';
			$('#classification').append(classification);
		});
	}).fail(function(data, textStatus,jqXHR) {
		console.log("error");
	});
}

function MatrixErrors(errors) {
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

function MatrixNoError(no_error) {
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

function delete_items(checkboxClass,url) {
	var chkArray = [];
	$(checkboxClass+":checked").each(function() {
		chkArray.push($(this).val());
	});

	if (chkArray.length > 0) {
		var data = {
			_token: token,
			ids: chkArray,
		}
		delete_now(url,data);
	} else {
		$('#delete_modal').modal('hide');
		msg("Please select at least 1 item.", 'failed');
	}
}

function delete_now(url,data) {
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'JSON',
		data: data,
	}).done(function(data,textStatus,jqXHR) {
		$('#delete_modal').modal('hide');
		msg(data.msg,data.status);
		getDatatable('tbl_matrix',showMatrixURL,dataColumn,[],0);
	}).fail(function(data,textStatus,jqXHR) {
		msg("There's an error occurred while deleting.",'error');
	});
}
