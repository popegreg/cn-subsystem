$( function() {
	init();

	$('#btn_addnew').on('click', function() {
		$('#qc-permission-modal').modal('show');
	});

	$('#qc-permission-modal').on('shown.bs.modal', function () {
	   var table = $('#tbl_users').DataTable();
	   table.columns.adjust();
   });

	$('#tbl_users_body').on('click', '.btnEdit', function(e) {
		$('.btnEdit').prop('disabled',true);
	});
});

function init() {
	PermissionList();
	ProgramsList();
	UsersList();
}

function GuiState(state) {
	switch (state) {
		case 'ADD':
			$('.btnEdit').prop('disabled', true);
			$('#btnSave').prop('disabled', false);
			$('#btnCancel').prop('disabled', false);
		break;

		case 'EDIT':
			$('.btnEdit').prop('disabled', true);
			$('#btnSave').prop('disabled', false);
			$('#btnCancel').prop('disabled', false);
		break;

		case 'DELETE':
			$('#btn_addnew').prop('disabled',true);
			$('#btn_delete').prop('disabled',false);
		break;

		default:
			$('.btnEdit').prop('disabled', true);
			$('#btnSave').prop('disabled', false);
			$('#btnCancel').prop('disabled', false);

			$('#btn_addnew').prop('disabled',false);
			$('#btn_delete').prop('disabled',true);
		break;
	}
}

function UsersList() {
	$('#loading').show();

	$.ajax({
		url: UsersURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token
		}
	}).done( function(data, textStatus, xhr) {
		UserListTable(data.lists);
	}).fail( function(xhr, textStatus, errorThrown) {
		console.log(xhr);
	}).always( function () {
		$('#loading').hide();
	});
}

function UserListTable(dataArr) {
	$('#tbl_users').dataTable().fnClearTable();
	$('#tbl_users').dataTable().fnDestroy();
	$('#tbl_users').dataTable({
		data: dataArr,
		bLengthChange : false,
		pageLength: 5,
		// paging: false,
		// searching: false,
		columns: [
			{ data: 'lastname', width: "30%" },
			{ data: 'firstname', width: "30%" },
			{ data: 'middlename', width: "15%" },
			{ data: 'user_id', width: "20%" },
			{ data: function(x) {
				if (x.id != '') {
					return '<button type="button" class="btn btn-link green btnEdit" data-id="'+x.id+'">'+
								'<i class="fa fa-edit"></i>'+
							'</button>';
				} else {
					return '';
				}
				
			}, orderable: false, width: "5%" },

		]
	});
}

function ProgramsList() {
	$('#loading').show();

	var options = '<option value="">-----</option>';
	$('#txtprogram').html(options);

	$.ajax({
		url: ProgramsURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token
		}
	}).done( function(data, textStatus, xhr) {
		$.each(data.lists, function(i,x) {
			options += '<option value="'+x.program_code+'">'+x.program_name+'</option>'
		});
		$('#txtprogram').html(options);
	}).fail( function(xhr, textStatus, errorThrown) {
		console.log(xhr);
	}).always( function () {
		$('#loading').hide();
	});
}

function PermissionList() {
	$('#loading').show();

	$.ajax({
		url: PermissionsURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token
		}
	}).done( function(data, textStatus, xhr) {
		PermissionListTable(data.lists);
	}).fail( function(xhr, textStatus, errorThrown) {
		console.log(xhr);
	}).always( function () {
		$('#loading').hide();
	});
}

function PermissionListTable(dataArr) {
	$('#tbl_qcpermission').dataTable().fnClearTable();
	$('#tbl_qcpermission').dataTable().fnDestroy();
	$('#tbl_qcpermission').dataTable({
		data: dataArr,
		bLengthChange : false,
		pageLength: 10,
		// paging: false,
		// searching: false,
		columns: [
			{ data: function(x) {
				if (x.id != '') {
					return '<button type="button" class="btn btn-link grey-gallery btnEdit" data-id="'+x.id+'">'+
								'<i class="fa fa-edit"></i>'+
							'</button>';
				} else {
					return '';
				}
				
			}, orderable: false, width: "3%" },
			{ data: 'lastname', width: "30%" },
			{ data: 'firstname', width: "22%" },
			{ data: 'middlename', width: "20%" },
			{ data: 'user_id', width: "15%" },
			{ data: function(x) {
				var return_data = '';

				if (x.create !== 0) {
					return_data += "Create";
				} 

				if (x.update !== 0) {
					if (return_data !== '') {
						return_data += ", Update";
					} else {
						return_data += "Update";
					}
					
				}

				if (x.delete !== 0) {
					if (return_data !== '') {
						return_data += ", Delete";
					} else {
						return_data += "Delete";
					}
					
				}
				
			}, orderable: false, width: "10%" }
		]
	});
}