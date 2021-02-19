// jQuery.support.cors = true;
$("#readfileform").on("submit",function(e){
	//e.preventDefault();
	$('#loading').modal('show');
	// var data = {
	// 	mlp01uf: $('#mlp01uf').val().split('\\').pop(),
	// 	mlp02uf: $('#mlp02uf').val().split('\\').pop()
	// }
	// console.log($('#readfileform').serializeArray());
	// $.ajaxSetup({
	//     headers:{
	//         'X-CSRF-Token': $('input[name="_token"]').val()
	//     }
	// });
	// $.ajax({
	//     url: actionUrl,
	//     method: 'POST',
	//     enctype: 'multipart/form-data',
	//     dataType:'json',
	//     data: data
	// }).done(function(response){
	// 	$('#loading').modal('hide');
	// 	console.log(response);
	// }).fail(function(response){
	// 	$('#loading').modal('hide');
	// 	console.log(response);
	// });
});

$('#processform').on('submit', function() {
	$('#processdone').modal('hide');
	$('#newproduct').modal('show')
});


