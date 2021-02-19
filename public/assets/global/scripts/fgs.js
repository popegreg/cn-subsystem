$(function() {
	FGSdataTable(FGSdataURL,'');
	
	$('#hd_report_status').val("");

	$('.group-field-select').select2({
		placeholder: 'Select field to group',
		allowClear: true
	});

	$('#btn_add').on('click', function() {
		clear();
		$('#AddNewModal').modal('show');
		$('#hd_status').val("ADD");
	});

	$('#btn_save').on('click', function() {
		var error = 0;

		if ($('#date').val() == '') {
			error += 1;
		}

		if ($('#po_no').val() == '') {
			error += 1;
		}

		if ($('#device_name').val() == '') {
			error += 1;
		}

		if ($('#quantity').val() == '') {
			error += 1;
		}

		if ($('#total_lots').val() == '') {
			error += 1;
		}

		if (error > 0) {
			msg('Please complete all input fields.','failed');
		} else {
			$('#loading').modal('show');
			$.ajax({
				url: FGSsaveURL,
				type: 'POST',
				dataType: 'JSON',
				data: {
					_token: token,
					date: $('#date').val(),
					po_no: $('#po_no').val(),
					device_name: $('#device_name').val(),
					quantity: $('#quantity').val(),
					total_lots: $('#total_lots').val(),
					hd_status: $('#hd_status').val(),
					id: $('#id').val()
				},
			}).done(function(data, textStatus,xhr) {
				clear();
				$('#AddNewModal').modal('hide');
				if (data.status == 'success') {
					FGSdataTable(FGSdataURL + '?_token='+token+'&&mode=');
				}
				msg(data.msg,data.status);
			}).fail(function(xhr, textStatus,errorThrown) {
				msg(errorThrown,'error');
			}).always(function() {
				$('#loading').modal('hide');
			});
		}
	});

	$('#tbl_fgs_body').on('click','.btn_edit', function() {
		$('#hd_status').val("EDIT");
		$('#id').val($(this).attr('data-id'));
		$('#date').val($(this).attr('data-date'));
		$('#po_no').val($(this).attr('data-po_no'));
		$('#device_name').val($(this).attr('data-device_name'));
		$('#quantity').val($(this).attr('data-qty'));
		$('#total_lots').val($(this).attr('data-total_num_of_lots'));

		$('#AddNewModal').modal('show');
	});

	$('#btn_clear').on('click', function() {
		clear();
	});

	$('#btn_groupby').on('click', function() {
		$('#GroupByModal').modal('show');
		$('#groupby_datefrom').val("");
		$('#groupby_dateto').val("");
		$('#group1').select2('val',"");
		$('#group1content').select2('val',"");

		//to classify group by when reporting----------
		$('#hd_report_status').val("GROUPBY");
		$('#hd_search_from').val("");
		$('#hd_search_to').val("");
		$('#hd_search_pono').val("");
	});

	$('#btn_search').on('click', function() {
		$('#SearchModal').modal('show');
		$('#search_pono').val("");
		$('#search_from').val("");
		$('#search_to').val("");
		$('#er_search_from').html(""); 
		$('#er_search_to').html(""); 

		//to classify group by when reporting----------
		$('#hd_report_status').val("SEARCH");
		$('#hd_search_from').val("");
		$('#hd_search_to').val("");
		$('#hd_groupfield').val("");
		$('#hd_value').val("");
	});

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

	$('#po_no').on('change', function(){
		if ($(this).val() !== '') {
			$.ajax({
				url: FGSypicsURL,
				type: 'GET',
				dataType: 'JSON',
				data:{
					pono: $(this).val()
				},
			}).done(function(data, textStatus, jqXHR){
				$('#device_name').val(data[0]['DEVNAME']);
			}).fail(function(jqXHR, textStatus, errorThrown){
				msg(errorThrown,textStatus);
			});
		}
	});

	$('#btn_pdf').on('click', function() {
		var searchpono = $('#search_pono').val();
		var datefrom = $('#search_from').val();
		var dateto = $('#search_to').val();
		var status = $('#hd_report_status').val();

		var tableData = {
			date_inspected: $('input[name^="hd_date_inspected[]"]').map(function(){return $(this).val();}).get(),
			pono: $('input[name^="hd_pono[]"]').map(function(){return $(this).val();}).get(),
			device_name: $('input[name^="hd_device_name[]"]').map(function(){return $(this).val();}).get(),
			qty: $('input[name^="hd_qty[]"]').map(function(){return $(this).val();}).get(),
			total_lots: $('input[name^="hd_total_lots[]"]').map(function(){return $(this).val();}).get(),
			status:status,
			searchpono:searchpono,
			datefrom:datefrom,
			dateto:dateto
		};
		var url = "{{ url('/fgsprintreport?data=') }}" + encodeURIComponent(JSON.stringify(tableData));
		window.location.href= url;
	});

	$('#btn_excel').on('click', function() {
		var searchpono = $('#search_pono').val();
		var datefrom = $('#search_from').val();
		var dateto = $('#search_to').val();
		var status = $('#hd_report_status').val();

		var tableData = {
			date_inspected: $('input[name^="hd_date_inspected[]"]').map(function(){return $(this).val();}).get(),
			pono: $('input[name^="hd_pono[]"]').map(function(){return $(this).val();}).get(),
			device_name: $('input[name^="hd_device_name[]"]').map(function(){return $(this).val();}).get(),
			qty: $('input[name^="hd_qty[]"]').map(function(){return $(this).val();}).get(),
			total_lots: $('input[name^="hd_total_lots[]"]').map(function(){return $(this).val();}).get(),
			status:status,
			searchpono:searchpono,
			datefrom:datefrom,
			dateto:dateto
		};
	   
		var url = "{{ url('/fgsprintreportexcel?data=')  }}" + encodeURIComponent(JSON.stringify(tableData));
		window.location.href= url;
	});

	$('#btn_delete').on('click', function() {
		var ids = [];
		var table = $('#tbl_fgs').DataTable();

		for (var x = 0; x < table.context[0].aoData.length; x++) {
			var cells = table.context[0].aoData[x].anCells[0];

			if (cells !== null && cells.firstChild.checked == true) {
				ids.push(cells.firstChild.attributes['data-id'].value)
			}
		}

		if (ids.length > 0) {
			var del_msg = 'Are you sure to delete this data?';

			if (ids.length > 1) {
				del_msg = 'Are you sure to delete these data?';
			}

			bootbox.confirm({
				title: 'FGS Inspections',
				size: 'small',
				message: del_msg,
				buttons: {
					confirm: {
						label: 'OK',
						className: 'btn-sm btn-danger'
					},
					cancel: {
						label: 'Cancel',
						className: 'btn-sm btn-default'
					}
				},
				callback: function (result) {
					if (result) {
						$('#loading').modal('show');

						$.ajax({
							url: FGSdeleteURL,
							type: 'POST',
							dataType: 'JSON',
							data: {
								_token: token,
								ids: ids
							}
						}).done(function(data, textStatus, jqXHR){
							if (data.status == 'success') {
								FGSdataTable(FGSdataURL,'');
							}
							msg(data.msg,data.status);
						}).fail(function(jqXHR, textStatus,errorThrown){
							msg(errorThrown,textStatus);
						}).always( function() {
							$('#loading').modal('hide');
						});
					}
				}
			});
			
		} else {
			$('#loading').modal('hide');
			msg('Please check at least 1 FGS entry.', 'failed');
		}
	});

	$('#btn_search_generate').on('click', function() {
		var url = FGSsearchURL + '?_token='+ token +
					'&&pono='+$('#search_pono').val()+
					'&&datefrom='+$('#search_from').val()+
					'&&dateto='+$('#search_to').val();

		FGSdataTable(url,'search');
	});

	$('#btn_group_by_generate').on('click', function() {
		var url = FGSgroupByURL +
			'?_token='+ token+
			'&&mode=group'+
			'&&g1='+$('select[name=group1]').val()+
			'&&g2='+$('select[name=group2]').val()+
			'&&g3='+$('select[name=group3]').val()+
			'&&datefrom='+$('#groupby_datefrom').val()+
			'&&dateto='+$('#groupby_dateto').val();

		FGSdataTable(url,'groupby');
	});

	$('#btn_group_by_pdf').on('click', function() {
		var param = {
			_token: token,
			report_type: 'groupby',
			g1: $('select[name=group1]').val(),
			g2: $('select[name=group2]').val(),
			g3: $('select[name=group3]').val(),
			datefrom: $('#groupby_datefrom').val(),
			dateto: $('#groupby_dateto').val()

		}
		ReportDataCheck(param, function(output) {
			if (output > 0) {
				var url = FGSReportURL + '?_token='+ token+
								'&&report_type=groupby'+
								'&&mode=pdf'+
								'&&g1='+$('select[name=group1]').val()+
								'&&g2='+$('select[name=group2]').val()+
								'&&g3='+$('select[name=group3]').val()+
								'&&datefrom='+$('#groupby_datefrom').val()+
								'&&dateto='+$('#groupby_dateto').val();

				window.open(url,'_tab');
			} else {
				msg('No data was retrieved.','failed');
			}
		});
	});

	$('#btn_group_by_excel').on('click', function() {
		var param = {
			_token: token,
			report_type: 'groupby',
			g1: $('select[name=group1]').val(),
			g2: $('select[name=group2]').val(),
			g3: $('select[name=group3]').val(),
			datefrom: $('#groupby_datefrom').val(),
			dateto: $('#groupby_dateto').val()

		}
		ReportDataCheck(param, function(output) {
			if (output > 0) {
				var url = FGSReportURL + '?_token='+ token+
									'&&report_type=groupby'+
									'&&g1='+$('select[name=group1]').val()+
									'&&g2='+$('select[name=group2]').val()+
									'&&g3='+$('select[name=group3]').val()+
									'&&datefrom='+$('#groupby_datefrom').val()+
									'&&dateto='+$('#groupby_dateto').val();

				window.location.href = url;
			} else {
				msg('No data was retrieved.','failed');
			}
		});
	});

	$('#btn_search_pdf').on('click', function() {
		var param = {
			_token: token,
			report_type: 'search',
			pono: $('#search_pono').val(),
			datefrom: $('#search_from').val(),
			dateto: $('#search_to').val()

		}
		ReportDataCheck(param, function(output) {
			if (output > 0) {
				var url = FGSReportURL + '?_token='+ token+
									'&&report_type=search'+
									'&&mode=pdf'+
									'&&pono='+$('#search_pono').val()+
									'&&datefrom='+$('#search_from').val()+
									'&&dateto='+$('#search_to').val();

				window.open(url,'_tab');
			} else {
				msg('No data was retrieved.','failed');
			}
		});
	});

	$('#btn_search_excel').on('click', function() {
		var param = {
			_token: token,
			report_type: 'search',
			pono: $('#search_pono').val(),
			datefrom: $('#search_from').val(),
			dateto: $('#search_to').val()

		}

		ReportDataCheck(param, function(output) {
			if (output > 0) {
				var url = FGSReportURL + '?_token='+ token+
									'&&report_type=search'+
									'&&mode=excel'+
									'&&pono='+$('#search_pono').val()+
									'&&datefrom='+$('#search_from').val()+
									'&&dateto='+$('#search_to').val();

				window.location.href = url;
			} else {
				msg('No data was retrieved.','failed');
			}
		});
	});

});

function ReportDataCheck(param, handleData) {
	$.ajax({
		url: FGSreportDataCheckURL,
		type: 'get',
		dataType: 'JSON',
		data: param,
	}).done(function(data, textStatus, xhr) {
		handleData(data.DataCount);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg('Report check: '+errorThrown,textStatus);
	});
}

function FGSdataTable(url, mode){
	$('#loading').modal('show');
	var table = $('#tbl_fgs');

	table.dataTable().fnClearTable();
	table.dataTable().fnDestroy();
	table.DataTable({
		processing: true,
		serverSide: true,
		ajax: url,
		deferRender: true,
		//data: url,
		lengthMenu: [
            [10, 20, 50, 100, 150, 200, 500, -1],
            [10, 20, 50, 100, 150, 200, 500, "All"]
        ],
		pageLength: 10, 
		columns: [
			{ data: function(data) {
				return '<input type="checkbox" class="input-sm checkboxes" data-id="'+data.id+'" name="checkitem" id="checkitem" />';
			}, name: 'id', searchable: false, orderable: false },
			{ data: function(data) {
				if (mode !== 'groupby') {
					return '<button type="button" class="btn btn-sm btn-primary btn_edit" data-id="'+data.id+'" '+
							'data-po_no="'+data.po_no+'" data-date="'+data.date+'" data-device_name="'+data.device_name+' " '+
							'data-qty="'+data.qty+'" data-total_num_of_lots="'+data.total_num_of_lots+'">'+
								'<i class="fa fa-edit"></i>'+
							'</button>';
				}
			}, orderable: false, searchable: false , name: 'action'},
			{ data: 'date', name: 'date' },
			{ data: 'po_no', name: 'po_no' },
			{ data: 'device_name', name: 'device_name' },
			{ data: function(data) {
				var qty = '';
				if (data.qty == null) {
					qty = 0;
				} else {
					qty = data.qty;
				}
				return qty;
			}, name: 'qty' },
			{ data: 'total_num_of_lots', name: 'total_num_of_lots' }
		],
		initComplete: function () {
			$('#loading').modal('hide');
		},
	});
}

function clear() {
	$('.clear').val('');
}