
//opening modal with user_id value
$('#editbtnprod').click(function(e){
	getValueUsingClass();

	function getValueUsingClass(){
		/* declare an checkbox array */
		var chkArray = [];
		
		/* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
		$(".checkboxes:checked").each(function() {
			chkArray.push($(this).val());
		});
		
		/* we join the array separated by the comma */
		var selected;
		selected = chkArray.join(',');
		
		/* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
		if(selected.length == 1){
			var id = selected;
			var code = $('input:checked').attr('data-code');
			var name = $('input:checked').attr('data-name');
			$(".modal-body #id").val( id );
			$('#editcode').val(code);
			$('#editname').val(name);
			$('#editModal').modal('show');
		}
		if(selected.length > 1){
			$.alert('Please select 1 product only.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		}
		if(selected.length < 1){
			$.alert('Please select 1 product.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		}
	}
});

$('#delbtnprod').click(function(e){
	/* Get the checkboxes values based on the class attached to each check box */

	getValueUsingClass();

	function getValueUsingClass(){
		/* declare an checkbox array */
		var chkArray = [];
		
		/* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
		$(".checkboxes:checked").each(function() {
			chkArray.push($(this).val());
		});
		
		/* we join the array separated by the comma */
		var selected;
		selected = chkArray.join(',');
		
		/* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
		if(selected.length == 1){
			var id = selected;
			$(".modal-body #id").val( id );
			$('#confirm').modal({ backdrop: 'static', keyboard: false })
		}
		if(selected.length > 1){
			$.alert('Please select 1 product only.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		}
		if(selected.length < 1){
			$.alert('Please select 1 product.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		}
	}
});