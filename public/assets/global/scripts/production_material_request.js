var requests = [];
var select_po = [];
$( function() {
	getSelections();
	checkAllCheckboxesInTable('.check_all_po_detail','.check_item_po_detail');
	getData();

	$('#cb_no_need_replacement').on('change',function() {
		if($(this).is(":checked")) {
			$('#edit_remarks').val("NO NEED REPLACEMENT");
			$('#edit_remarks').attr('readonly','readonly');
		}else{
			$('#edit_remarks').val("");
			$('#edit_remarks').removeAttr('readonly');
		}       
	})

	$('#btn_add_req').on('click', function() {
		addState();
	});

	$('#btn_discard_req').on('click', function() {
		getData($('#req_no').val());
	});

	$('#req_no').on('change', function() {
		getData($(this).val());
	});

	$('#btn_edit_req').on('click', function() {
		editState();
	});

	$('#btn_cancel_req').on('click', function() {
		$('#cancel_req_no').val($('#req_no').val());
		$('#ConfirmModal').modal('show');
	});

	$('#frm_cancel').on('submit', function(e) {
		$('#loading').modal('show');
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			getData(data.req_no);
			msg(data.msg,data.status);
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		}).always(function() {
			$('#loading').modal('hide');
			$('#ConfirmModal').modal('hide');
		});
	});

	$('#btn_search_req').on('click', function() {
		$('#searchModal').modal('show');
	});

	$('#frm_search').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			makeSearchTable(data)
			//msg(data.msg,data.status);
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		}).always(function() {
			$('#loading').modal('hide');
			$('#ConfirmModal').modal('hide');
		});
	});

	$('#tbl_search_body').on('click', '.btn_search_detail', function() {
		getData($(this).attr('data-req_no'));
	});

	$('#SelectPODetailsModal').on('shown.bs.modal', function(e){
		$($.fn.dataTable.tables(true)).DataTable()
		.columns.adjust();
	});

	$('#frm_search_po').on('submit', function(e) {
		e.preventDefault();
		$('#loading').modal('show');
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			select_po = [];
			if (data.length > 0) {
				$.each(data, function(i, x) {
					select_po.push({
						id: x.id,
						code: x.code,
						name: x.name,
						issuedqty: x.issuedqty,
						lot_no: x.lot_no
					});
				});

				makeSelectPOtable(select_po);
				$('#SelectPODetailsModal').modal('show');
				editState();
			} else {
				msg('P.O. not found.','failed');
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		}).always(function() {
			$('#loading').modal('hide');
		});
	});

	$('#btn_select_items').on('click', function(e) {
		e.preventDefault();
		var chkArray = [];
		$(".check_item_po_detail:checked").each(function() {
			chkArray.push($(this).val());
		});

		var data = {
			_token: token,
			ids: chkArray
		};

		$.ajax({
			url: SelectPODetailURL,
			type: 'GET',
			dataType: 'JSON',
			data: data,
		}).done(function(data, textStatus, xhr) {
			requests = [];
			if (data.length > 0) {
				count = 1;
				$.each(data, function(i, x) {
					requests.push({
						id: x.id,
						detailid: count,
						po: x.po,
						code: x.code,
						name: x.name,
						lot_no: x.lot_no,
						classification: x.classification,
						issuedqty: x.issuedqty,
						requestqty: x.requestqty,
						servedqty: 0,
						requestedby: '',
						last_served_by: '',
						last_served_date: '',
						location: x.location,
						remarks:'',
						acknowledgeby:''

					});
					count++;
				});
				makeRequestDetailsTable(requests);
			} else {
				msg('No selected items.','failed');
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		}).always(function() {
			$('#SelectPODetailsModal').modal('hide');
		});
	});

	$('#tbl_details_body').on('click', '.btn_edit_req_item', function() {
		$('#edit_detailid').val($(this).attr('data-detailid'));
		$('#edit_code').val($(this).attr('data-code'));
		$('#edit_desc').val($(this).attr('data-name'));
		$('#edit_classification').val($(this).attr('data-classification'));
		$('#edit_issuedqty').val($(this).attr('data-issuedqty'));
		$('#edit_requestqty').val($(this).attr('data-requestqty'));

		if ($(this).attr('data-requestedby') == '') {
			$('#edit_requested_by').val(user);
		} else {
			$('#edit_requested_by').val($(this).attr('data-requestedby'));
		}
		
		$('#edit_remarks').val($(this).attr('data-remarks'));
		$('#edit_item_id').val($(this).attr('data-id'));

		if ($(this).attr('data-remarks') == "NO NEED REPLACEMENT") {
			$('#edit_remarks').attr('readonly','readonly');
			$('.checker span').addClass('checked');
		} else {
			$('#edit_remarks').removeAttr('readonly');
			$('.checker span').removeClass('checked');
		}

		$('#EditPODetailsModal').modal('show');

	});

	$('#tbl_details_body').on('click', '.btn_acknowledge_item', function() {
		var data = {
			_token: token,
			id:$(this).attr('data-id'),
			req_no: $('#req_no').val()
		}

		$.ajax({
			url: acknowledgeURL,
			type: 'POST',
			dataType: 'JSON',
			data: data,
		}).done(function(data, textStatus, xhr) {
			//getData(data.req_no);
			getData($('#req_no').val());
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		}).always(function() {
			$('#loading').modal('hide');
		});
	});

	$('#btn_update_req_item').on('click', function() {
		var detailid = parseFloat($('#edit_detailid').val());

		if ($('#edit_classification').val() == '' || $('#edit_requestqty').val() == '' || $('#edit_requestqty').val() < 1 || !$.isNumeric($('#edit_requestqty').val())) {
			msg("Please provide valid data for Request Quantity and Classification.",'failed');
		} else {
			for (var i = requests.length - 1; i >= 0; --i) {
				if (requests[i].detailid == detailid) {
					requests[i].classification = $('#edit_classification').val();
					requests[i].requestqty = $('#edit_requestqty').val();
					requests[i].requestedby = $('#edit_requested_by').val();
					requests[i].remarks = $('#edit_remarks').val();
				}
			}

			makeRequestDetailsTable(requests);
			$('#EditPODetailsModal').modal('hide');
		}
	});

	$('#btn_save_req').on('click', function() {
		var isValid = true;
        $('.reqd_details').each(function() {
            if ($.trim($(this).val()) == '') {
            	isValid = false;
            }
        });

        if ($('#prod_destination').val() == '' || $('#line_destination').val() == '') {
        	isValid = false;
        }

        if (isValid == false) {
			msg("Please comply all details before saving.",'failed');
		} else {
			save();
		}
	});

	$('#btn_first').on('click', function() {
		navigate('first');
	});

	$('#btn_prv').on('click', function() {
		navigate('prev');
	});

	$('#btn_nxt').on('click', function() {
		navigate('next');
	});

	$('#btn_last').on('click', function() {
		navigate('last');
	});

	$('#btn_delete_details').on('click', function() {
		var chkArray = [];
		$(".check_item_po_detail:checked").each(function() {
			chkArray.push($(this).attr('data-detailid'));
		});

		$.each(chkArray, function(i, x) {
			x--
			requests.splice(x,1);
		});

		var count = 1;

		$.each(requests, function(i, x) {
			requests[i].detailid = count;
		});

		makeRequestDetailsTable(requests);
	});

	$('#btn_pdf_req').on('click', function() {
		window.location.href = getPDFURL + "?req_no=" + $('#req_no').val();
	});
});

function clear() {
	$('.clear').val('');
}

function viewState() {
	if (parseInt(access_state) !== 2) {
		$('#btn_first').prop('disabled', false);
		$('#btn_prv').prop('disabled', false);
		$('#btn_nxt').prop('disabled', false);
		$('#btn_last').prop('disabled', false);
		$('#btn_search_po').prop('disabled', true);

		$('#btn_add_req').show();
		$('#btn_save_req').hide();

		if ($('#statuspmr').val() == 'Cancelled') {
			$('#btn_edit_req').hide();
		} else {
			$('#btn_edit_req').show();
		}
		
		$('#btn_cancel_req').hide();
		$('#btn_discard_req').hide();
		$('#btn_search_req').show();
		$('#btn_pdf_req').show();

		$('#btn_add_details').hide();
		$('#btn_delete_details').hide();

		$('#req_no').prop('readonly',false);
		$('#po').prop('readonly',true);
		$('#prod_destination').prop('disabled',true);
		$('#line_destination').prop('disabled',true);
		$('#remarkspmr').prop('readonly',true);

		$('.btn_edit_req_item').prop('disabled', true);
		$('.btn_acknowledge_item').prop('disabled', true);
	} else {
		$('#btn_first').prop('disabled', false);
		$('#btn_prv').prop('disabled', false);
		$('#btn_nxt').prop('disabled', false);
		$('#btn_last').prop('disabled', false);
		$('#btn_search_po').prop('disabled', true);

		$('#btn_add_req').hide();
		$('#btn_save_req').hide();

		$('#btn_edit_req').hide();

		$('#btn_cancel_req').hide();
		$('#btn_discard_req').hide();
		$('#btn_search_req').show();
		$('#btn_pdf_req').show();

		$('#btn_add_details').hide();
		$('#btn_delete_details').hide();

		$('#req_no').prop('readonly', false);
		$('#po').prop('readonly', true);
		$('#prod_destination').prop('disabled', true);
		$('#line_destination').prop('disabled', true);
		$('#remarkspmr').prop('readonly', true);

		$('.btn_edit_req_item').prop('disabled', true);
		$('.btn_acknowledge_item').prop('disabled', true);
	}
}

function addState() {
	$('#btn_first').prop('disabled', true);
	$('#btn_prv').prop('disabled', true);
	$('#btn_nxt').prop('disabled', true);
	$('#btn_last').prop('disabled', true);
	$('#btn_search_po').prop('disabled', false);

	$('#btn_add_req').hide();
	$('#btn_save_req').hide();
	$('#btn_edit_req').hide();
	$('#btn_cancel_req').hide();
	$('#btn_discard_req').show();
	$('#btn_search_req').hide();
	$('#btn_pdf_req').hide();

	$('#btn_add_details').hide();
	$('#btn_delete_details').hide();

	$('#req_no').prop('readonly',true);
	$('#po').prop('readonly',false);
	$('#prod_destination').prop('disabled',true);
	$('#line_destination').prop('disabled',true);
	$('#remarkspmr').prop('readonly',true);
	clear();
	makeRequestDetailsTable([]);
	$('.btn_edit_req_item').prop('disabled', false);
	$('.btn_acknowledge_item').prop('disabled', true);
}

function editState() {
	$('#btn_first').prop('disabled', true);
	$('#btn_prv').prop('disabled', true);
	$('#btn_nxt').prop('disabled', true);
	$('#btn_last').prop('disabled', true);
	$('#btn_search_po').prop('disabled', false);

	$('#btn_add_req').hide();
	$('#btn_save_req').show();
	$('#btn_edit_req').hide();
	$('#btn_cancel_req').show();
	$('#btn_discard_req').show();
	$('#btn_search_req').hide();
	$('#btn_pdf_req').hide();

	$('#btn_add_details').show();
	$('#btn_delete_details').show();

	$('#req_no').prop('readonly',true);
	$('#po').prop('readonly',true);
	$('#prod_destination').prop('disabled',false);
	$('#line_destination').prop('disabled',false);
	$('#remarkspmr').prop('readonly',false);

	$('.btn_edit_req_item').prop('disabled', false);
	$('.btn_acknowledge_item').prop('disabled', false);
}

function makeSelectPOtable(arr) {
	$('#tbl_po_details').dataTable().fnClearTable();
    $('#tbl_po_details').dataTable().fnDestroy();
    $('#tbl_po_details').dataTable({
        data: arr,
        bLengthChange : false,
        scrollY: "500px",
	    paging: false,
        columns: [
            { data: function(x) {
                return "<input type='checkbox' class='check_item_po_detail' data-id='"+x.id+"' value='"+x.id+"'>";
            }, searchable: false, orderable: false },

            { data: function(x) {
                return x.code+"<input type='hidden' name='code[]' value='"+x.code+"'>";
            }},

            { data: function(x) {
                return x.name+"<input type='hidden' name='name[]' value='"+x.name+"'>";
            }},

            { data: function(x) {
                return x.issuedqty+"<input type='hidden' name='issuedqty[]' value='"+x.issuedqty+"'>";
            }},

            { data: function(x) {
                return x.lot_no+"<input type='hidden' name='lot_no[]' value='"+x.lot_no+"'>";
            }},
        ],
        columnDefs: [
        	{ "width": "5%", "targets": 0 },
        	{ "width": "20%", "targets": 1 },
        	{ "width": "35%", "targets": 2 },
        	{ "width": "20%", "targets": 3 },
        	{ "width": "20%", "targets": 4 }
        ]
    });
}

function makeRequestDetailsTable(arr) {
	$('#tbl_details').dataTable().fnClearTable();
    $('#tbl_details').dataTable().fnDestroy();
    $('#tbl_details').dataTable({
        data: arr,
        bLengthChange : false,
        // scrollY: "250px",
	    paging: false,
	    searching: false,
        columns: [
            { data: function(x) {
                return "<input type='checkbox' class='check_item_po_detail' data-id='"+x.id+"' data-detailid='"+x.detailid+"' value='"+x.id+"'>";
            }, searchable: false, orderable: false },

            { data: function(x) {
                return "<button class='btn btn-sm btn-primary btn_edit_req_item' "+
                				"data-detailid='"+x.detailid+"'"+
								"data-code='"+x.code+"'"+
								"data-name='"+x.name+"'"+
								"data-classification='"+x.classification+"'"+
								"data-issuedqty='"+x.issuedqty+"'"+
								"data-requestqty='"+x.requestqty+"'"+
								"data-requestedby='"+x.requestedby+"'"+
								"data-remarks='"+x.remarks+"'"+
								"data-id='"+x.id+"'>"+
                			"<i class='fa fa-edit'></i>"+
                		"</button>";
            }, searchable: false, orderable: false },

            { data: function(x) {
                return x.detailid+"<input type='hidden' name='req_detailid[]' value='"+x.detailid+"'>";
            }},

            { data: function(x) {
                return x.code+"<input type='hidden' name='req_code[]' value='"+x.code+"'>";
            }},

            { data: function(x) {
                return x.name+"<input type='hidden' name='req_name[]' value='"+x.name+"'>";
            }},

            { data: function(x) {
                return x.classification+"<input type='hidden' name='req_classification[]' class='reqd_details' value='"+x.classification+"'>"+
                		"<input type='hidden' name='req_lot_no[]' value='"+x.lot_no+"'>";
            }},

            { data: function(x) {
                return x.issuedqty+"<input type='hidden' name='req_issuedqty[]' value='"+x.issuedqty+"'>";
            }},

            { data: function(x) {
                return x.requestqty+"<input type='hidden' name='req_requestqty[]' class='reqd_details' value='"+x.requestqty+"'>";
            }},

            { data: function(x) {
                return x.servedqty+"<input type='hidden' name='req_servedqty[]' value='"+x.servedqty+"'>";
            }},

            { data: function(x) {
                return x.requestedby+"<input type='hidden' name='req_requestedby[]' value='"+x.requestedby+"'>";
            }},

            { data: function(x) {
                return x.last_served_by+"<input type='hidden' name='req_last_served_by[]' value='"+x.last_served_by+"'>";
            }},

            { data: function(x) {
                return x.last_served_date+"<input type='hidden' name='req_last_served_date[]' value='"+x.last_served_date+"'>";
            }},

            { data: function(x) {
                return x.remarks+"<input type='hidden' name='req_remarks[]' value='"+x.remarks+"'>"+
                		"<input type='hidden' name='req_location[]' value='"+x.location+"'>";
            }},

            { data: function(x) {
            	if (x.servedqty > 0) {
            		if (x.acknowledgeby == null) {
	            		return "<button class='btn btn-sm btn-primary btn_acknowledge_item' "+
	                				"data-detailid='"+x.detailid+"'"+
									"data-code='"+x.code+"'"+
									"data-name='"+x.name+"'"+
									"data-classification='"+x.classification+"'"+
									"data-issuedqty='"+x.issuedqty+"'"+
									"data-requestqty='"+x.requestqty+"'"+
									"data-requestedby='"+x.requestedby+"'"+
									"data-remarks='"+x.remarks+"'"+
									"data-id='"+x.id+"'>"+
	                			"<i class='fa fa-thumbs-up'></i> Acknowledge"+
	                		"</button>";
	                } else {
	                	return x.acknowledgeby;
	                }
            	} else {
            		return "";
            	}
            	
            }, searchable: false, orderable: false },
        ]
    });
}

function makeSearchTable(arr) {
	$('#tbl_search').dataTable().fnClearTable();
    $('#tbl_search').dataTable().fnDestroy();
    $('#tbl_search').dataTable({
        data: arr,
        bLengthChange : false,
        scrollY: "250px",
        searching: false,
	    paging: false,
        columns: [
            { data: function(x) {
                return "<button class='btn btn-sm btn-primary btn_search_detail' "+
                				"data-req_no='"+x.transno+"'>"+
                			"<i class='fa fa-eye'></i>"+
                		"</button>";
            }, searchable: false, orderable: false },

            { data: 'transno'},
            { data: 'pono'},
            { data: 'destination'},
            { data: 'line'},
            { data: 'status'},
            { data: 'createdby'},
            { data: 'created_at'},
            { data: 'updatedby'},
            { data: 'updated_at'},
        ]
    });
}

function getSelections() {
	var edit_classification = '<option></option>';
	var prod_destination = '<option></option>';
	var line_destination = '<option></option>';
	$.ajax({
		url: getSelectionsURL,
		type: 'GET',
		dataType: 'JSON',
	}).done(function(data, textStatus, xhr) {
		$.each(data.class, function(i, x) {
			edit_classification += '<option value="'+x.description+'">'+x.description+'</option>';
			$('#edit_classification').html(edit_classification);
		});

		$.each(data.prod, function(i, x) {
			prod_destination += '<option value="'+x.description+'">'+x.description+'</option>';
			$('#prod_destination').html(prod_destination);
		});

		$.each(data.line, function(i, x) {
			line_destination += '<option value="'+x.description+'">'+x.description+'</option>';
			$('#line_destination').html(line_destination);
		});
	}).fail(function(xhr, textStatus, errorThrown) {
		console.log("error");
	});
}

function getData(req_no,to) {
	$.ajax({
		url: getDataURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			req_no: req_no,
			to: to
		},
	}).done(function(data, textStatus, xhr) {
		if ('status' in data) {
			msg(data.msg,data.status);
		} else {
			var sum = data.summary;
			$('#req_no').val(sum.transno);
			$('#po').val(sum.pono);
			$('#prod_destination').val(sum.destination);
			$('#line_destination').val(sum.line);
			$('#statuspmr').val(sum.status);
			$('#remarkspmr').val(sum.remarks);
			$('#create_user').val(sum.createdby);
			$('#created_at').val(sum.created_at);
			$('#updated_by').val(sum.updatedby);
			$('#updated_at').val(sum.updated_at);

			requests = data.details;

			makeRequestDetailsTable(requests);
		}
		viewState();
	}).fail(function(xhr, textStatus, errorThrown) {
		console.log("error");
	}).always(function() {
		$('#loading').modal('hide');
	});
}

function navigate(to) {
	getData($('#req_no').val(),to);
}

function save() {
	$('#loading').modal('show');
	var data = {
		_token: token,
		req_no: $('#req_no').val(),
		po: $('#po').val(),
		prod_destination: $('#prod_destination').val(),
		line_destination: $('#line_destination').val(),
		statuspmr: $('#statuspmr').val(),
		remarkspmr: $('#remarkspmr').val(),
		detailid: $('input[name="req_detailid[]"]').map(function(){return $(this).val();}).get(),
		code: $('input[name="req_code[]"]').map(function(){return $(this).val();}).get(),
		name: $('input[name="req_name[]"]').map(function(){return $(this).val();}).get(),
		lot_no: $('input[name="req_lot_no[]"]').map(function(){return $(this).val();}).get(),
		classification: $('input[name="req_classification[]"]').map(function(){return $(this).val();}).get(),
		issuedqty: $('input[name="req_issuedqty[]"]').map(function(){return $(this).val();}).get(),
		requestqty: $('input[name="req_requestqty[]"]').map(function(){return $(this).val();}).get(),
		servedqty: $('input[name="req_servedqty[]"]').map(function(){return $(this).val();}).get(),
		requestedby: $('input[name="req_requestedby[]"]').map(function(){return $(this).val();}).get(),
		last_served_by: $('input[name="req_last_served_by[]"]').map(function(){return $(this).val();}).get(),
		last_served_date: $('input[name="req_last_served_date[]"]').map(function(){return $(this).val();}).get(),
		remarks: $('input[name="req_remarks[]"]').map(function(){return $(this).val();}).get(),
		location: $('input[name="req_location[]"]').map(function(){return $(this).val();}).get(),
	}

	$.ajax({
		url: saveURL,
		type: 'POST',
		dataType: 'JSON',
		data: data,
	}).done(function(data, textStatus, xhr) {
		getData(data.req_no);
		msg(data.msg,data.status);
	}).fail(function(xhr, textStatus, errorThrown) {
		console.log("error");
	}).always(function() {
		$('#loading').modal('hide');
	});
}