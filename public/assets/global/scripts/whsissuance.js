var issuance = [];

$( function() {
	getPendingRequest();
	getData();
	checkAllCheckboxesInTable('.check_all_details','.check_item_detail');

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		$($.fn.dataTable.tables(true)).DataTable() .columns.adjust();
	});

	$('#tbl_req_summary_body').on('click', '.btn_view_details', function() {
		viewReqDetails($(this).attr('data-transno'))
	});

	$('#tbl_req_details_body').on('change', '.check_item_detail', function() {
		isCheck($(this));
	});

	$('.check_all_details').on('change', function(e) {
		isCheck($('.check_item_detail'));
	});

	$('#btn_make_issuance').on('click', function() {
		var id = [];
		var transno = '';
		$(".check_item_detail:checked").each(function() {
			id.push($(this).val());
			transno = $(this).attr('data-transno');
		});

		$.ajax({
			url: getRequestDetails,
			type: 'GET',
			dataType: 'JSON',
			data: {
				_token: token,
				ids: id,
				transno: transno
			},
		}).done(function(data, textStatus, xhr) {
			switchToIssuance();
			clear();
			issuance = [];

			var details = data.details;
			var totals = data.totals;
			var served = data.served;
			var count = 1;

			$.each(details, function(i, x) {
				issuance.push({
					id: x.id,
					issuance_no: x.issuance_no,
					request_no: x.request_no,
					detail_id: count,
					item: x.item,
					item_desc: x.item_desc,
					pmr_detail_id: x.pmr_detail_id,
					request_qty: x.request_qty,
					issued_qty_o: x.issued_qty_o,
					issued_qty_t: x.issued_qty_t,
					servedqty: x.servedqty,
					lot_no: x.lot_no,
					location: x.location,
					approved_id:x.approved_id,
					remarks:x.remarks,
					reason_id:x.reason_id,
					inv_id: x.inv_id
				});

				count++;
			});

			$('#id').val(data.id);
			$('#issuance_no').val(details[0].issuance_no);
			$('#req_no').val(details[0].request_no);
			$('#status').val(data.status);
			$('#total_req_qty').val(totals.total_req_qty);

			var total_bal_qty = totals.total_req_qty - totals.total_served_qty;
			$('#total_bal_qty').val(total_bal_qty);

			makeIssuanceTable(issuance,served);
			addState();
		}).fail(function(xhr, textStatus, errorThrown) {
			msg(textStatus+': '+errorThrown,textStatus);
		});
	});

	$('#tbl_issuance_body').on('click', '.btn_edit_issuance_details', function() {
		getInventory($(this).attr('data-item'));

		$('#edit_detail_id').val($(this).attr('data-detail_id'));
		$('#edit_item').val($(this).attr('data-item'));
		$('#edit_item_desc').val($(this).attr('data-item_desc'));
		$('#edit_pmr_detail_id').val($(this).attr('data-pmr_detail_id'));
		$('#edit_request_qty').val($(this).attr('data-request_qty'));
		$('#edit_served_qty').val($(this).attr('data-servedqty'));
		$('#edit_lot_no').val($(this).attr('data-lot_no'));
		$('#iss_remarks').val($(this).attr('data-iss_remarks'));
 		$('#user').val($(this).attr('data-user'));
 		$('#id_reason').val($(this).attr('data-id_reason'));
		

		var issue_qty = parseFloat($(this).attr('data-request_qty')) - parseFloat($(this).attr('data-servedqty'));

		$('#edit_issued_qty').val(issue_qty);
		$('#edit_location').val($(this).attr('data-location'));

		$('#editIssuanceModal').modal('show');
	});

	$('#editIssuanceModal').on('shown.bs.modal', function(e){
		$($.fn.dataTable.tables(true)).DataTable()
		.columns.adjust();
	});

	$('#tbl_inventory_body').on('click', '.btn_select_item_inv', function() {
		var data = $('#tbl_inventory').DataTable().rows(0).data();
		var firstrow_id = data['0'].id;
		if($(this).attr('data-id') != firstrow_id){
			 $('#authorizedmodal').modal('show');
			 $('#f_lot_no').val($(this).attr('data-lot_no'));
			 $('#f_inv_id').val($(this).attr('data-id'));
			 $('#f_item').val($(this).attr('data-item'));
			 $('#f_item_desc').val($(this).attr('data-item_desc'));
			 $('#reason_id').val("");
			 $('#user_id').val("");
			 $('#password').val("");
			 $('#fiforeason').val("");

			 // <input type="text" name="f_item" id="f_item">
				// <input type="text" name="f_item_desc" id="f_item_desc">
				// <input type="text" name="f_issuanceno" id="f_issuanceno">
		}else{
			 $('#edit_lot_no').val($(this).attr('data-lot_no'));
			 $('#edit_inv_id').val($(this).attr('data-id'));
		}

	
	});

	$('#btn_fiforeason').on('click',function(){
		if ($('#reason_id').val() == '') {
		msg('Please specify your reason for using this Lot Number.','failed');	
		}
		else if($('#user_id').val() == "" || $('#password').val() == ""){

			msg('User and Password required!','failed');	
		}else{

				 AuthorizedUser();
		}
	})

	$('#btn_update_details').on('click', function() {
		var detail_id = parseFloat($('#edit_detail_id').val());
		var total_issued = parseFloat($('#edit_served_qty').val()) + parseFloat($('#edit_issued_qty').val());
		var remarks = $('#iss_remarks').val();
		var approved_id = $('#user').val();
		var reason_id = $('#id_reason').val();

		if (total_issued > $('#edit_request_qty').val()) {
			msg("Issue quantity is greater than request qty.",'failed');
		} else if ($('#edit_lot_no').val() == '') {
			msg("Please specify a Lot Number.",'failed');
		} else {
			for (var i = issuance.length - 1; i >= 0; --i) {
				if (issuance[i].detail_id == detail_id) {
					issuance[i].issued_qty_t = $('#edit_issued_qty').val();
					issuance[i].lot_no = $('#edit_lot_no').val();
					issuance[i].inv_id = $('#edit_inv_id').val();
					issuance[i].remarks = remarks;
					issuance[i].approved_id = approved_id;
					issuance[i].reason_id = reason_id;

				}
			}

			makeIssuanceTable(issuance,total_issued);
			$('#editIssuanceModal').modal('hide');
		}
	});

	$('#btn_save').on('click', function() {
		save();
	});

	$('#btn_edit').on('click', function() {
		ediState();
	});

	$('#btn_discard').on('click', function() {
		navigate('last');
	});

	$('#btn_cancel').on('click', function() {
		$('#cancel_issuance_no').val($('#issuance_no').val());
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
			getData(data.issuance_no);
			msg(data.msg,data.status);
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		}).always(function() {
			$('#loading').modal('hide');
			$('#ConfirmModal').modal('hide');
		});
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

	$('#issuance_no').on('change', function() {
		getData($(this).val());
	});

	$('#btn_search').on('click', function() {
		$('#searchModal').modal('show');
	});

	$('#frm_search').on('submit', function(e) {
		$('#loading').modal('show');
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			makeSearchTable(data)
		}).fail(function(xhr, textStatus, errorThrown) {
			msg(textStatus+': '+errorThrown,textStatus);
		}).always(function() {
			$('#loading').modal('hide');
		});
	});

	$('#tbl_search_body').on('click', '.btn_search_detail', function() {
		getData($(this).attr('data-issuance_no'));
		$('#searchModal').modal('hide');
	});

	$('#btn_report_excel').on('click', function() {
		window.location.href = excelPDF + '?issuance_no=' + $('#issuance_no').val();
	});

	$('#btn_report_pdf').on('click', function() {
		window.location.href = pdfURL + '?issuance_no=' + $('#issuance_no').val();
	});

	$('#tbl_issuance_body').on('click', '.btn_barcode', function() {
		// var arr_data = [];
		// arr_data.push({
		// 	_token: token,
		// 	request_no: $(this).attr('data-request_no'),
		// 	issuance_no: $(this).attr('data-issuance_no'),
		// 	detail_id: $(this).attr('data-detail_id'),
		// 	item: $(this).attr('data-item'),
		// 	item_desc: $(this).attr('data-item_desc'),
		// 	lot_no: $(this).attr('data-lot_no'),
		// 	pmr_detail_id: $(this).attr('data-pmr_detail_id'),
		// 	request_qty: $(this).attr('data-request_qty'),
		// 	issued_qty_t: $(this).attr('data-issued_qty_t'),
		// 	location: $(this).attr('data-location'),
		// 	servedqty: $(this).attr('data-servedqty'),
		// 	create_user: $(this).attr('data-create_user'),
		// 	created_at: $(this).attr('data-created_at')
		// });

		var arr_data = {
			_token: token,
			request_no: $(this).attr('data-request_no'),
			issuance_no: $(this).attr('data-issuance_no'),
			detail_id: $(this).attr('data-detail_id'),
			item: $(this).attr('data-item'),
			item_desc: $(this).attr('data-item_desc'),
			lot_no: $(this).attr('data-lot_no'),
			pmr_detail_id: $(this).attr('data-pmr_detail_id'),
			request_qty: $(this).attr('data-request_qty'),
			issued_qty_t: $(this).attr('data-issued_qty_t'),
			location: $(this).attr('data-location'),
			servedqty: $(this).attr('data-servedqty'),
			create_user: $(this).attr('data-create_user'),
			created_at: $(this).attr('data-created_at')
		};

		if (isOnMobile() == true) {
			printBRcode(arr_data);
		} else {
			printBRcode(arr_data);
			msg("Please use mobile device.",'failed');
		}
	});
});

function viewReqDetails(transno) {
	$.ajax({
		url: viewReqDetailsURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			transno: transno
		},
	}).done(function(data, textStatus, xhr) {
		makeViewDetailsTable(data);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(textStatus+': '+errorThrown,textStatus);
	}).always(function() {
		$('#loading').modal('hide');
	});
}

function getPendingRequest() {
	$('#tbl_req_summary').dataTable().fnClearTable();
    $('#tbl_req_summary').dataTable().fnDestroy();
    $('#tbl_req_summary').dataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        ajax: getPedingRequestURL,
        bLengthChange : false,
		scrollY: "250px",
		order: [[2,'desc']],
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'transno', name: 'transno', orderable: false },
            { data: 'created_at', name: 'created_at', orderable: false },
            { data: 'pono', name: 'pono', orderable: false },
            { data: 'destination', name: 'destination', orderable: false },
            { data: 'line', name: 'line', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'requestedby', name: 'requestedby', orderable: false },
            { data: 'lastservedby', name: 'lastservedby', orderable: false },
            { data: 'lastserveddate', name: 'lastserveddate', orderable: false }
        ]
    });
}

function makeViewDetailsTable(arr) {
	$('#tbl_req_details').dataTable().fnClearTable();
    $('#tbl_req_details').dataTable().fnDestroy();
    $('#tbl_req_details').dataTable({
        data: arr,
        bLengthChange : false,
        scrollY: "200px",
        searching: false,
		paging: false,
		order: [[1,'asc']],
        columns: [
            { data: function(x) {
            	var blocked = '';
            	if (x.requestqty == x.servedqty) {
            		blocked = 'disabled';
				}
				if (x.remarks.toUpperCase().replace(/\s/g,'') == "NONEEDREPLACEMENT") {
					blocked = 'disabled';
				}
                return "<input type='checkbox' class='check_item_detail' data-id='"+x.id+"' data-transno='"+x.transno+"' value='"+x.id+"' "+blocked+">";
            }, searchable: false, orderable: false },

            { data: 'detailid' },
			{ data: 'code' },
			{ data: 'name' },
			{ data: 'classification' },
			{ data: 'issuedqty' },
			{ data: 'requestqty' },
			{ data: 'servedqty' },
			{ data: 'remarks' },
			{ data: 'last_served_date' },
		],
		initComplete: function() {
			if (parseInt(access_state) !== 2) {
				$('.check_item_detail').prop('disabled', false);
			} else {
				$('.check_item_detail').prop('disabled', true);
			}
		}
    });
}

function makeIssuanceTable(arr,served_qty_per_item) {
	$('#tbl_issuance').dataTable().fnClearTable();
    $('#tbl_issuance').dataTable().fnDestroy();
    $('#tbl_issuance').dataTable({
        data: arr,
        bLengthChange : false,
        searching: false,
		paging: false,
		order: [[2,'asc']],
        columns: [
            { data: function(x) {
                return "<input type='checkbox' class='check_item_detail' "+
                		"data-request_no='"+x.request_no+"' data-issuance_no='"+x.issuance_no+"' value='"+x.id+"'>";
            }, searchable: false, orderable: false },

            { data: function(x) {
            	var user = x.approved_id ==null?"":x.approved_id;
            	var id_reason = x.reason_id ==null?"":x.reason_id;
            	var iss_remarks = x.remarks==null?"":x.remarks;
            	var servedqty = 0;
            	                // var mod = x.mod ==null?"":x.mod;

            	if ($.isArray(served_qty_per_item)) {
            		$.each(served_qty_per_item, function(i, xx) {
            			var serves = $.map(xx, function(ii, xxx) {
	            			return ii;
	            		});

	            		if (serves[0] == x.item) {
	            			servedqty = serves[1];
	            		}
	            	});
            	} else {
            		servedqty = (x.servedqty == undefined)? x.issued_qty_t : x.servedqty;
            	}

            	
            	// var servedqty = (x.servedqty == undefined)? x.issued_qty_t : total_served_qty; //x.issued_qty_t
                return "<button class='btn btn-sm blue btn_edit_issuance_details' data-id='"+x.id+"' "+
                			"data-detail_id='"+x.detail_id+"'"+
							"data-item='"+x.item+"'"+
							"data-item_desc='"+x.item_desc+"'"+
							"data-pmr_detail_id='"+x.pmr_detail_id+"'"+
							"data-request_qty='"+x.request_qty+"'"+
							"data-issued_qty_t='"+x.issued_qty_t+"'"+
							"data-location='"+x.location+"'"+
							"data-lot_no='"+x.lot_no+"'"+
							"data-iss_remarks='"+iss_remarks+"'"+
							"data-user='"+user+"'"+
							"data-id_reason='"+id_reason+"'"+
							"data-inv_id='" + x.inv_id + "'" +
							"data-servedqty='"+servedqty+"'>"+
                			"<i class='fa fa-edit'></i>"+
                		"</button>";
            }, searchable: false, orderable: false },

            { data: function(x) {
                return x.detail_id+'<input type="hidden" name="detail_id[]" value="'+x.detail_id+'">';
            } },
			{ data: function(x) {
                return x.item+'<input type="hidden" name="item[]" value="'+x.item+'">';
            } },
			{ data: function(x) {
                return x.item_desc+'<input type="hidden" name="item_desc[]" value="'+x.item_desc+'">';
            } },
			{ data: function(x) {
                return x.issued_qty_o+'<input type="hidden" name="issued_qty_o[]" value="'+x.issued_qty_o+'">';
            } },
			{ data: function(x) {
                return x.issued_qty_t+'<input type="hidden" name="issued_qty_t[]" value="'+x.issued_qty_t+'">';
            } },
			{ data: function(x) {
				var user = x.approved_id;
            	var id_reason = x.reason_id;
            	var iss_remarks = x.remarks;
                return x.lot_no+'<input type="hidden" name="lot_no[]" value="'+x.lot_no+'">'+
               					'<input type="hidden" name="user[]" value="'+user+'">'+
                				'<input type="hidden" name="iss_remarks[]" value="'+iss_remarks+'">'+
                				'<input type="hidden" name="id_reason[]" value="'+id_reason+'">';
            } },

			{ data: function(x) {
                return x.location+'<input type="hidden" name="location[]" value="'+x.location+'">'+
                				'<input type="hidden" name="inv_id[]" value="'+x.inv_id+'">'+
                				'<input type="hidden" name="pmr_detail_id[]" value="'+x.pmr_detail_id+'">'+
                				'<input type="hidden" name="request_qty[]" value="'+x.request_qty+'">';
            } },

            { data: function(x) {
            	var servedqty = 0;

            	if ($.isArray(served_qty_per_item)) {
            		$.each(served_qty_per_item, function(i, xx) {
            			var serves = $.map(xx, function(ii, xxx) {
	            			return ii;
	            		});

	            		if (serves[0] == x.item) {
	            			servedqty = serves[1];
	            		}
	            	});
            	} else {
            		servedqty = (x.servedqty == undefined)? x.issued_qty_t : x.servedqty;
            	}

            	//var servedqty = (x.servedqty == undefined)? x.issued_qty_t : total_served_qty;
                return "<button class='btn btn-sm grey-gallery btn_barcode' data-id='"+x.id+"' "+
                			"data-request_no='"+x.request_no+"'"+
                			"data-issuance_no='"+x.issuance_no+"'"+
                			"data-detail_id='"+x.detail_id+"'"+
							"data-item='"+x.item+"'"+
							"data-item_desc='"+x.item_desc+"'"+
							"data-lot_no='"+x.lot_no+"'"+
							"data-pmr_detail_id='"+x.pmr_detail_id+"'"+
							"data-request_qty='"+x.request_qty+"'"+
							"data-issued_qty_t='"+x.issued_qty_t+"'"+
							"data-location='"+x.location+"'"+
							"data-servedqty='"+servedqty+"'"+
							"data-create_user='"+x.create_user+"'"+
							"data-created_at='"+x.created_at+"'"+
							">"+
                			"<i class='fa fa-barcode'></i>"+
                		"</button>";
            }, searchable: false, orderable: false },

        ],
        columnDefs: [
        	{ "width": "4.11%", "targets": 0 },
        	{ "width": "5.11%", "targets": 1 },
        	{ "width": "4.11%", "targets": 2 },
        	{ "width": "11.11%", "targets": 3 },
        	{ "width": "25.11%", "targets": 4 },
        	{ "width": "11.11%", "targets": 5 },
        	{ "width": "11.11%", "targets": 6 },
        	{ "width": "11.11%", "targets": 7 },
        	{ "width": "17.11%", "targets": 8 },
        	{ "width": "17.11%", "targets": 9 }
        ]
    });
}

function makeInventoryTable(arr) {
	$('#tbl_inventory').dataTable().fnClearTable();
    $('#tbl_inventory').dataTable().fnDestroy();
    $('#tbl_inventory').dataTable({
        data: arr,
        bLengthChange : false,
        scrollY: "200px",
        searching: false,
	    paging: false,
	    order:[[5,'asc'],[4,'asc']],
        columns: [
            { data: function(x) {
                return '<button type="button" class="btn btn-sm blue btn_select_item_inv" data-id="'+x.id+'"'+
                			'data-qty="'+x.qty+'" data-item="'+x.item+'" data-item_desc="'+x.item_desc+'" data-lot_no="'+x.lot_no+'">'+
                			'<i class="fa fa-edit"></i>'+
                		'</button>';
            }, searchable: false, orderable: false },

            { data: 'item' },
			{ data: 'item_desc' },
			{ data: 'qty' },
			{ data: 'lot_no' },
			{ data: 'received_date' }
		],
		initComplete: function() {
			$('#loading').modal('hide');
		}
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
                return "<button type='button' class='btn btn-sm btn-primary btn_search_detail' "+
                				"data-issuance_no='"+x.issuance_no+"'>"+
                			"<i class='fa fa-eye'></i>"+
                		"</button>";
            }, searchable: false, orderable: false },

            { data: 'issuance_no' },
			{ data: 'request_no' },
			{ data: 'status' },
			{ data: 'create_user' },
			{ data: 'created_at' },
			{ data: 'update_user' },
			{ data: 'updated_at' },
        ]
    });
}

function isCheck(element) {
	if(element.is(':checked')) {
		$('#btn_make_issuance').prop('disabled',false);
	} else {
		$('#btn_make_issuance').prop('disabled',true);
	}
}

function switchToIssuance() {
	$('#summary_tab').removeClass('active');
	$('#summary_pane').removeClass('active');
	//
	$('#issuance_tab').addClass('active');
	$('#issuance_pane').addClass('active');

	$('#issuance_tab').addClass('active');
	$('#issuance_pane').addClass('active');


	$('#summary_tab_toggle').attr('data-toggle','');
}

function switchToSummary() {
	$('#issuance_tab').removeClass('active');
	$('#issuance_pane').removeClass('active');
	//
	$('#summary_tab').addClass('active');
	$('#summary_pane').addClass('active');
	$('#summary_tab_toggle').attr('data-toggle','tab');
}

function viewState() {
	if (parseInt(access_state) !== 2) {
		$('#btn_first').prop('disabled', false);
		$('#btn_prv').prop('disabled', false);
		$('#btn_nxt').prop('disabled', false);
		$('#btn_last').prop('disabled', false);

		$('#issuance_no').prop('readonly', false);
		$('#req_no').prop('readonly', true);
		$('#status').prop('readonly', true);
		$('#created_by').prop('readonly', true);
		$('#created_at').prop('readonly', true);
		$('#total_req_qty').prop('readonly', true);
		$('#updated_by').prop('readonly', true);
		$('#updated_at').prop('readonly', true);
		$('#total_bal_qty').prop('readonly', true);

		if ($('#status').val() == 'Serving') {
			$('#btn_edit').show();
		} else {
			$('#btn_edit').hide();
		}

		$('#btn_save').hide();
		$('#btn_cancel').hide();
		$('#btn_discard').hide();
		$('#btn_search').show();
		$('#btn_report_excel').show();
		$('#btn_report_pdf').show();

		$('.btn_edit_issuance_details').prop('disabled', true);
	} else {
		$('#btn_first').prop('disabled', false);
		$('#btn_prv').prop('disabled', false);
		$('#btn_nxt').prop('disabled', false);
		$('#btn_last').prop('disabled', false);

		$('#issuance_no').prop('readonly', false);
		$('#req_no').prop('readonly', true);
		$('#status').prop('readonly', true);
		$('#created_by').prop('readonly', true);
		$('#created_at').prop('readonly', true);
		$('#total_req_qty').prop('readonly', true);
		$('#updated_by').prop('readonly', true);
		$('#updated_at').prop('readonly', true);
		$('#total_bal_qty').prop('readonly', true);

		$('#btn_edit').hide();

		$('#btn_save').hide();
		$('#btn_cancel').hide();
		$('#btn_discard').hide();
		$('#btn_search').show();
		$('#btn_report_excel').show();
		$('#btn_report_pdf').show();

		$('.btn_edit_issuance_details').prop('disabled', true);
	}
}

function addState() {
	$('#btn_first').prop('disabled', true);
	$('#btn_prv').prop('disabled', true);
	$('#btn_nxt').prop('disabled', true);
	$('#btn_last').prop('disabled', true);

	$('#issuance_no').prop('readonly', true);
	$('#req_no').prop('readonly', true);
	$('#status').prop('readonly', true);
	$('#created_by').prop('readonly', true);
	$('#created_at').prop('readonly', true);
	$('#total_req_qty').prop('readonly', true);
	$('#updated_by').prop('readonly', true);
	$('#updated_at').prop('readonly', true);
	$('#total_bal_qty').prop('readonly', true);

	$('#btn_save').show();
	$('#btn_edit').hide();
	$('#btn_cancel').hide();
	$('#btn_discard').show();
	$('#btn_search').hide();
	$('#btn_report_excel').hide();
	$('#btn_report_pdf').hide();

	$('.btn_edit_issuance_details').prop('disabled', false);
}

function ediState() {
	$('#btn_first').prop('disabled', true);
	$('#btn_prv').prop('disabled', true);
	$('#btn_nxt').prop('disabled', true);
	$('#btn_last').prop('disabled', true);

	$('#issuance_no').prop('readonly', true);
	$('#req_no').prop('readonly', true);
	$('#status').prop('readonly', true);
	$('#created_by').prop('readonly', true);
	$('#created_at').prop('readonly', true);
	$('#total_req_qty').prop('readonly', true);
	$('#updated_by').prop('readonly', true);
	$('#updated_at').prop('readonly', true);
	$('#total_bal_qty').prop('readonly', true);

	$('#btn_save').show();
	$('#btn_edit').hide();
	$('#btn_cancel').show();
	$('#btn_discard').show();
	$('#btn_search').hide();
	$('#btn_report_excel').hide();
	$('#btn_report_pdf').hide();

	$('.btn_edit_issuance_details').prop('disabled', false);
}

function getInventory(item) {
	$('#loading').modal('show');
	$.ajax({
		url: getInventoryURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			item: item
		},
	}).done(function(data, textStatus, xhr) {
		console.log(data);
		makeInventoryTable(data);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(textStatus+': '+errorThrown,textStatus);
	}).always( function() {
		$('#loading').modal('hide');
	});
}

function navigate(to) {
	getData($('#issuance_no').val(),to);
}

function getData(issuance_no='',to = '') {
	$.ajax({
		url: getDataURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			issuance_no: issuance_no,
			to: to
		},
	}).done(function(data, textStatus, xhr) {
		$('#summary_tab_toggle').attr('data-toggle','tab');
		if ('status' in data) {
			msg(data.msg,data.status);
		} else {
			var sum = data.summary;

			$('#id').val(sum.id);
			$('#issuance_no').val(sum.issuance_no);
			$('#req_no').val(sum.request_no);
			$('#status').val(sum.status);
			$('#total_req_qty').val(sum.total_req_qty);
			$('#created_by').val(sum.create_user);
			$('#updated_by').val(sum.update_user);
			$('#created_at').val(sum.created_at);
			$('#updated_at').val(sum.updated_at);
			$('#total_bal_qty').val(data.total_bal_qty);
			issuance = data.details;

			makeIssuanceTable(issuance,data.served_qty_per_item);
		}
		viewState();
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(textStatus+': '+errorThrown,textStatus);
	}).always(function() {
		$('#loading').modal('hide');
	});
}

function clear() {
	$('.clear').val('');
}

function save() {
	$('#loading').modal('show');
	var data = {
		_token: token,
		id: $('#id').val(),
		issuance_no: $('#issuance_no').val(),
		req_no: $('#req_no').val(),
		total_req_qty: $('#total_req_qty').val(),
		user: $('input[name="user[]"]').map(function(){return $(this).val();}).get(),
		id_reason: $('input[name="id_reason[]"]').map(function(){return $(this).val();}).get(),
		iss_remarks: $('input[name="iss_remarks[]"]').map(function(){return $(this).val();}).get(),
		detail_id: $('input[name="detail_id[]"]').map(function(){return $(this).val();}).get(),
		item: $('input[name="item[]"]').map(function(){return $(this).val();}).get(),
		item_desc: $('input[name="item_desc[]"]').map(function(){return $(this).val();}).get(),
		issued_qty_o: $('input[name="issued_qty_o[]"]').map(function(){return $(this).val();}).get(),
		issued_qty_t: $('input[name="issued_qty_t[]"]').map(function(){return $(this).val();}).get(),
		lot_no: $('input[name="lot_no[]"]').map(function(){return $(this).val();}).get(),
		location: $('input[name="location[]"]').map(function(){return $(this).val();}).get(),
		inv_id: $('input[name="inv_id[]"]').map(function(){return $(this).val();}).get(),
		pmr_detail_id: $('input[name="pmr_detail_id[]"]').map(function(){return $(this).val();}).get(),
		request_qty: $('input[name="request_qty[]"]').map(function(){return $(this).val();}).get(),
	};

	$.ajax({
		url: saveURL,
		type: 'POST',
		dataType: 'JSON',
		data: data,
	}).done(function(data, textStatus, xhr) {
		getData(data.issuance_no);
		getPendingRequest();
		viewReqDetails([]);
		msg(data.msg,data.status);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(textStatus+': '+errorThrown,textStatus);
	}).always(function() {
		$('#loading').modal('hide');
	});
}

function printBRcode(arr_data) {
	$.ajax({
		url: printBarCodeURL,
		type: 'GET',
		dataType: 'json',
		data: arr_data,
	}).done(function(data,textStatus,xhr) {
		window.location.href = downloadBarCodeURL+"?filename="+data.filename;
	}).fail(function(xhr,textStatus,errorThrown) {
		msg(errorThrown,textStatus);
	}).always(function() {
		console.log("complete");
	});
	
	// window.location.href= printBarCodeURL + '?request_no='+arr_data.request_no+
	// 		'&&issuance_no='+arr_data.issuance_no+
	// 		'&&detail_id='+arr_data.detail_id+
	// 		'&&item='+arr_data.item+
	// 		'&&item_desc='+arr_data.item_desc+
	// 		'&&lot_no='+arr_data.lot_no+
	// 		'&&pmr_detail_id='+arr_data.pmr_detail_id+
	// 		'&&request_qty='+arr_data.request_qty+
	// 		'&&issued_qty_t='+arr_data.issued_qty_t+
	// 		'&&location='+arr_data.location+
	// 		'&&servedqty='+arr_data.servedqty+
	// 		'&&create_user='+arr_data.create_user+
	// 		'&&created_at='+arr_data.created_at;
}

function isOnMobile() {
	var isMobile = false; //initiate as false
	// device detection
	if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

	return isMobile;
}

function AuthorizedUser(){
	 var sample = {
	 	 _token: token,
	 	 lotno : $('#f_lot_no').val(),
		 id: $('#f_inv_id').val(),
		 user: $('user_id').val(),	
		 item : $('#f_item').val(),
		 item_desc : $('#f_item_desc').val(),
		 reason: $('#fiforeason').val(),
		 issuanceno: $('#issuance_no').val(),
		 reason_id : $('#reason_id').val(),
		 user: $('#user_id').val()

	 }
	var user_id  = $('#user_id').val();
	var actual_password = $('#password').val();
	$.ajax({
		url: AuthorizedURL,
		type: 'GET',
		dataType: 'JSON',
		data:
		 {
		 	user_id: user_id,
		 	actual_password :actual_password

		 },
	})
	.done(function(data, textStatus, xhr) {	  
		   if(data == null){
    			msg('Invalid user','failed');
   		   }
		   else if(data.length == 0){
				msg('User error','failed');
		   }	
		   else if(data["0"].update_user !== 'kurt_cn' || data["0"].update_user == "" ||  data["0"].update_user == null){
		 		msg('Unauthorizeduser','failed');
		   }else{
				msg('Authorizeduser','success');
			    saveFifoReason(sample);
			}
	})
	.fail(function(xhr, textStatus, errorThrown) {		
		console.log("error");
	})	
}

function saveFifoReason(params){
	$.ajax({
		url: SaveFifoURL,
		type: 'POST',
		dataType: 'JSON',
		data: params
	})
	.done(function(data, textStatus, xhr) {
		if(data.status == 'success'){
			var reason_id_text = $("#reason_id option:selected").text().trim();
			var otherreason = $("#fiforeason").val().trim();
			if(otherreason == ""){
			 $('#iss_remarks').val(reason_id_text+" "+otherreason);
			}else{
			$('#iss_remarks').val(reason_id_text+" - "+otherreason);
			}
			$('#edit_lot_no').val(params.lotno);
			$('#edit_inv_id').val(params.id);
			$('#user').val(params.user);
			$('#id_reason').val(params.reason_id);
			$('#authorizedmodal').modal('hide');
		}else{
			msg('Requesting Failed.','failed');	
		}

	})
	.fail(function(xhr, textStatus, errorThrown) {
		   msg("There was an error while processing.",'error');
	})
}
