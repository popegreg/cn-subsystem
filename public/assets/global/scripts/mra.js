$(function() {
	// TableAdvanced.init();
	loadMRA();
	// $('.tr_click').on('click', function() {
	// 	$(this).toggleClass("highlight");
	// });
	$('#btn_generate').on('click', function() {
		$('#loading').modal('show');
		var tblMra = '';
        var data = {
            _token: token,
        };
        $.ajax({
            url: urlgeneratemra,
            type: "GET",
            data: data,
        }).done( function(data, textStatus, jqXHR) {
        	$('#loading').modal('hide');
            $('#tblMra').empty();
            var cnt = 0;
            var color = "";
            $.each(data, function(i, x) {
            	console.log(x);
            	cnt++;
            	// if (x.ForOrdering < 0) {
            	// 	color = "#cb5a5e";
            	// }
            	// if (x.ForOrdering == 0) {
            	// 	color = "#c49f47";
            	// }
            	// if (x.ForOrdering > 0) {
            	// 	color = "#1BA39C";
            	// }

                tblMra = '<tr class="tr_click">'+
							'<td>'+x.ItemCode+'</td>'+
							'<td>'+x.ItemName+'</td>'+
							'<td>'+x.ItemType+'</td>'+
							'<td>'+Number(x.TtlRequired).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.TtlCompleted).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.ReqToComplete).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.WHSE100).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.WHSE_NON).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.ASSY100).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.ASSY102).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.WHSESM).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.TotalOnHand).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.OrderBalance).toLocaleString('en')+'</td>'+
							'<td>'+Number(x.ForOrdering).toLocaleString('en')+'</td>'+
							'<td>'+x.MAINBUMO+'</td>'+
						'</tr>';
                $('#tblMra').append(tblMra);
            });
            $('#count').html('Total Items: '+cnt);
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-check"></i></strong> Success!')
            $('#err_msg').html("You've successfully generated the Material Requirements Analysis.");
        }).fail( function(data, textStatus, jqXHR) {
            $('#loading').modal('hide');
            $('#msg').modal('show');
            $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
            $('#err_msg').html("There's some error while processing.");
        });
	});
});

function loadMRA()
{
	var tblMra = '';
    var data = {
        _token: token,
    };
    $.ajax({
        url: urlmraload,
        type: "GET",
        data: data,
    }).done( function(data, textStatus, jqXHR) {
    	$('#loading').modal('hide');
        $('#tblMra').empty();
        var cnt = 0;
        var color = "";
        $.each(data, function(i, x) {
        	console.log(x);
        	cnt++;
        	// if (x.ForOrdering < 0) {
        	// 	color = "#cb5a5e";
        	// }
        	// if (x.ForOrdering == 0) {
        	// 	color = "#c49f47";
        	// }
        	// if (x.ForOrdering > 0) {
        	// 	color = "#1BA39C";
        	// }

            tblMra = '<tr>'+
						'<td>'+x.ItemCode+'</td>'+
						'<td>'+x.ItemName+'</td>'+
						'<td>'+x.ItemType+'</td>'+
						'<td>'+Number(x.TtlRequired).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.TtlCompleted).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.ReqToComplete).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.WHSE100).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.WHSE_NON).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.ASSY100).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.ASSY102).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.WHSESM).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.TotalOnHand).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.OrderBalance).toLocaleString('en')+'</td>'+
						'<td>'+Number(x.ForOrdering).toLocaleString('en')+'</td>'+
						'<td>'+x.MAINBUMO+'</td>'+
					'</tr>';
            $('#tblMra').append(tblMra);
        });
        $('#count').html('Total Items: '+cnt);
    }).fail( function(data, textStatus, jqXHR) {
        $('#loading').modal('hide');
        $('#msg').modal('show');
        $('#title').html('<strong><i class="fa fa-exclamation-triangle"></i></strong> Failed!')
        $('#err_msg').html("There's some error while processing.");
    });
}