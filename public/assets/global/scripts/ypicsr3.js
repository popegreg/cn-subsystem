var dataColumn = [
		{ data: 'salesno', name: 'salesno' },
		{ data: 'salestype', name: 'salestype' },
		{ data: 'salesorg', name: 'salesorg' },
		{ data: 'commercial', name: 'commercial' },
		{ data: 'section', name: 'section' },
		{ data: 'salesbranch', name: 'salesbranch' },
		{ data: 'salesg', name: 'salesg' },
		{ data: 'supplier', name: 'supplier' },
		{ data: 'destination', name: 'destination' },
		{ data: 'payer', name: 'payer' },
		{ data: 'assistant', name: 'assistant' },
		{ data: 'purchaseorderno', name: 'purchaseorderno' },
		{ data: 'issuedate', name: 'issuedate' },
		{ data: 'flightneeddate', name: 'flightneeddate' },
		{ data: 'headertext', name: 'headertext' },
		{ data: 'code', name: 'code' },
		{ data: 'itemtext', name: 'itemtext' },
		{ data: 'orderquantity', name: 'orderquantity' },
		{ data: 'unit', name: 'unit' },
	];
$( function() {
	//getYPICSuserData(YpicsUserURL);
	getDatatable('tbl_ypicsr3',YpicsR3DataURL,dataColumn,[],0);

	// $('#btn_ypicsuser').on('click', function(event) {
	// 	getYPICSuserData(YpicsUserURL);
	// 	$('#ypicsUserModal').modal('show');
	// });

	$('#ddsupplier').on('change', function(event) {
		$('#selected_supplier').val($(this).val());
	});

	$('#ddproductline').on('change', function(event) {
		$('#selected_dbconnect').val($(this).val());
	});

	$('#ypicsr3Form').on('submit', function(event) {
		$('#loading').modal('show');
		event.preventDefault();
		var action_url = $(this).attr('action');
		var data = $(this).serialize();

		$.ajax({
			url : action_url,
			method: "POST",
			data : data,
		}).done(function(data, textStatus, jqXHR) {
			$('#loading').modal('hide');

			if (data.successModal == true) {
				var supplier = $('#ddsupplier').val();
				var productline = $('#ddproductline').val();
				$('#selected_dbconnect').val(productline);
				$('#selected_supplier').val(supplier);

				$('#success').modal('show');
				getDatatable('tbl_ypicsr3',YpicsR3DataURL,dataColumn,[],0);
			} else {
				msg(data.msg,data.status);
			}
		}).fail(function(jqXHR, textStatus, errorThrown) {
			$('#loading').modal('hide');
			msg("There was an error occurred while processing.",'error');
		});
	});

	$('#frm_print').on('submit', function() {
		var postData = $(this).serializeArray();
		var formURL = $(this).attr("action");
		//e.preventDefault(); //STOP default action
		$.ajax({
			url : formURL,
			method: "POST",
			data : postData,
		}).done(function(data, textStatus, jqXHR) {
			console.log(data);
		}).fail(function(jqXHR, textStatus, errorThrown) {
			//if fails
		});
	});
});

function getYPICSuserData(YpicsUserURL) {
	var tbl_ypicsuser_body = '';
	$('#tbl_ypicsuser_body').html('');
	$.ajax({
		url: YpicsUserURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: $('meta[name=csrf-token]').attr('content')},
	}).done(function(data,textStatus,jqXHR) {
		$.each(data, function(i, x) {
			tbl_ypicsuser_body = '<tr class="odd blue">'+
									'<td style="width: 25%">'+x.inputuser+'</td>'+
									'<td style="width: 25%">'+x.inputdate+'</td>'+
									'<td style="width: 25%" class="mrpuser-ckey">'+
										x.ckey+
										'<input type="hidden" name="ckey[]" value="'+x.ckey+'">'+
									'</td>'+
									'<td style="width: 25%" class="mrpuser-intval">'+
										x.intval+
										'<input type="hidden" name="intval[]" value="'+x.intval+'">'+
									'</td>'+
								'</tr>';
			$('#tbl_ypicsuser_body').append(tbl_ypicsuser_body);
		});
	}).fail(function(data,textStatus,jqXHR) {
		msg("There was an error occurred while processing.",'error');
	});
}

function actionStartStop(startstop)
{
	$('#loading').modal('show');

	var ckey = [];
	var intval = [];
	$("input[name='ckey[]']").each(function(index, el) {
		ckey.push($(this).val());
	});

	$("input[name='intval[]']").each(function(index, el) {
		intval.push($(this).val());
	});

	var dbconnection = $('#action').val();
	var data = {
		_token: $('meta[name=csrf-token]').attr('content'),
		action: startstop,
		dbconnect: dbconnection,
		ckey: JSON.stringify(ckey),
		intval: JSON.stringify(intval),
	}

	$.ajax({
		url : StartStopURL,
		method: "POST",
		data : data,
	}).done(function(data, textStatus, jqXHR) {
		$('#loading').modal('hide');
		//console.log(data);
		msg(data.msg,data.status);
	}).fail(function(jqXHR, textStatus, errorThrown) {
		$('#loading').modal('hide');
		msg("There was an error occurred while processing.",'error');
	});
}


document.onreadystatechange = function(e)
{
	if (document.readyState=="interactive")
	{
		var all = document.getElementsByName("salesorderdata");
		var totaTr = all.length;

		$('#itemcount').html("0 of " + totaTr);
		for (var i=0, max=all.length; i < max; i++)
		{
			set_ele(all[i]);
			$('#itemcount').html(i + " of " + totaTr );
		}
		$('#itemcount').html(i + " of " + totaTr );
	}
}

function check_element(ele)
{
	var all = document.getElementsByName("salesorderdata");
	var totalele=all.length;
	var per_inc=100/all.length;

	$('#percentage').html("Percentage : " + per_inc + "%");

	if($(ele).on())
	{
		var prog_width=per_inc+Number(document.getElementById("progress_width").value);

		$('#percentage').html("Percentage : " + Math.round(prog_width * 100) / 100 + "%");

		document.getElementById("progress_width").value=prog_width;

		$("#bar1").animate({width:prog_width+"%"},10,function(){
			if(document.getElementById("bar1").style.width=="100%")
			{
				$(".progress").fadeOut("slow");
			}
		});
	}

	else
	{
		set_ele(ele);
	}
}

function set_ele(set_element)
{
	check_element(set_element);
}
