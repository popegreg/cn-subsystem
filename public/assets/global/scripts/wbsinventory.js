$( function() {
	ViewState();

	checkAllCheckboxesInTable('.check_all','.check_item');
	inventoryDataTable(inventoryListURL);

	$('#btn_add').on('click', function() {
		$('#form_inventory_modal').modal('show');
	});

	$('#tbl_inventory').on('click', '.btn_edit', function() {
		$('#form_inventory_modal').modal('show');
	});

	$("#btn_delete").on('click', removeByID);

	$("#frm_inventory").on('submit', function(e){
		$('#loading').modal('show');
		var a = $(this).serialize();
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			msg("Modified Successful","success"); 
			inventoryDataTable(inventoryListURL);

		}).fail(function(xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			// showErrors(errors);
		}).always( function() {
			$('#loading').modal('hide');
		});
	});


	$('#tbl_inventory_body').on('click', '.btn_edit', function(e) {
		e.preventDefault();
		$('#id').val($(this).attr('data-id'));
		$('#item').val($(this).attr('data-item'));
		$('#item_desc').val($(this).attr('data-item_desc'));
		$('#lot_no').val($(this).attr('data-lot_no'));
		$('#qty').val($(this).attr('data-qty'));
		$('#location').val($(this).attr('data-location'));
		$('#supplier').val($(this).attr('data-supplier'));
		$('#status').val($(this).attr('data-iqc_status'));

		if ($(this).attr('data-not_for_iqc') == 1) {
			$('#nr').attr('checked',true);
		} else {
			$('#nr').attr('checked',false);
		}

		console.log($('#nr'));
		
	});

	$('#btn_open_search_modal').on('click', function() {
		$('#srch_from').val('');
		$('#srch_to').val('');
		$('#srch_invoice').val('');
		$('#srch_item').val('');
		$('#srch_lot_no').val('');
		
		$('#search_modal').modal('show');
	});

	$('#btnSearch').on('click', function() {
		var url = inventoryListURL + "?srch_from=" +$('#srch_from').val() +
									"&&srch_to=" +$('#srch_to').val() +
									"&&srch_invoice=" +$('#srch_invoice').val() +
									"&&srch_item=" +$('#srch_item').val() +
									"&&srch_lot_no=" +$('#srch_lot_no').val();
		inventoryDataTable(url);
	});
});

function ViewState() {
	if (parseInt(access_state) !== 2) {
		$('#check_all').prop('disabled', false);
		$('#btn_delete').show();

	} else {
		$('#check_all').prop('disabled', true);
		$('#btn_delete').hide();
	}
}


function removeByID(){
    var id = [];
    $(".check_item:checked").each(function () {
         id.push($(this).val());
    });

    var data = {
    	_token: token,
    	id: id
    };

    $.ajax({
    	url: deleteselected,
     	type: 'POST',
     	dataType: 'JSON',
     	data: data
    }).done(function(data, textStatus,xhr) {
     	msg(data.msg,data.status);
		inventoryDataTable(inventoryListURL);
    }).fail(function(xhr,textStatus) {
     	console.log("error");
    });
}

function inventoryDataTable(url) {
	$('#loading').modal('show');

	$('#tbl_inventory').dataTable().fnClearTable();
	$('#tbl_inventory').dataTable().fnDestroy();
	$('#tbl_inventory').dataTable({
		processing: true,
		serverSide: true,
		ajax: url,
		deferRender: true,
		processing: true,
		pageLength: 10,            
		pagingType: "bootstrap_full_number",
		columnDefs: [{
		    orderable: false,
		    targets: 0
		}, {
			searchable: false,
		    targets: 0
		}],
		order: [
		    [11, "desc"]
		],
		lengthMenu: [
		    [10, 20, 50, 100, 150, 200, 500, -1],
		    [10, 20, 50, 100, 150, 200, 500, "All"]
		],
		language: {
		    aria: {
		        sortAscending: ": activate to sort column ascending",
		        sortDescending: ": activate to sort column descending"
		    },
		    emptyTable: "No data available in table",
		    info: "Showing _START_ to _END_ of _TOTAL_ records",
		    infoEmpty: "No records found",
		    infoFiltered: "(filtered1 from _MAX_ total records)",
		    lengthMenu: "Show _MENU_",
		    search: "Search:",
		    zeroRecords: "No matching records found",
		    paginate: {
		        "previous":"Prev",
		        "next": "Next",
		        "last": "Last",
		        "first": "First"
		    }
		},
		columns: [
			{ data: function(data) {
				return '<input type="checkbox" class="check_item" value="'+data.id+'">';
			}, name: 'id', orderable: false, searchable: false },
			{ data: 'wbs_mr_id', name: 'wbs_mr_id' },
			{ data: 'invoice_no', name: 'invoice_no' },
			{ data: 'item', name: 'item' },
			{ data: 'item_desc', name: 'item_desc' },
			{ data: 'qty', name: 'qty' },
			{ data: 'lot_no', name: 'lot_no' },
			{ data: 'location', name: 'location' },
			{ data: 'supplier', name: 'supplier' },
			{ data: 'iqc_status', name: 'iqc_status' },
			{ data: 'create_user', name: 'create_user' },
			{ data: 'received_date', name: 'received_date' },
			{ data: 'update_user', name: 'update_user' },
			{ data: 'updated_at', name: 'updated_at' },
			{ data: 'action', name: 'action',orderable: false, searchable: false },
		],
		initComplete: function () {
			$('#loading').modal('hide');
			if (parseInt(access_state) !== 2) {
				$('.check_item').prop('disabled', false);
				$('.btn_edit').prop('disabled', false);
			} else {
				$('.check_item').prop('disabled', true);
				$('.btn_edit').prop('disabled', true);
			}
        },
	});
}
