$( document ).ready(function(e) {

		$('.deleteAll-task').addClass("disabled");
		$('#add').removeClass("disabled");

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

		$('#master').change(function()
		{
			var master = $(this).find(':selected').val();
			$('#catid').val(master);
			window.location.href = SelectedUrl + master;
		});

		$('#add').on('click', function(e)
		{
			var master = $('#master').val();
			var mastertext = $('#master :selected').text();

			e.preventDefault();

			$('#masterid').val(master);
			$('#hdnaction').val('ADD');
			$('#inputname').val("");

			$('.modal-title').html('');
			$('#myModal').modal('show');
			$('.modal-title').append(mastertext);

			$('#er1').html("");

			$('#inputname').keyup(function(){
				$('#er1').html("");
			});
		});

		$('#deleteAll').click(function(){
			var master =$('#master').val();
			deleteAllmaster = $('#deleteAllmaster').val(master);

			$('.deleteAllmodal-title').html('Delete' + " " + deleteAllmaster);
			$('#deleteAllModal').modal('show');
			$('.deleteAllmodal-title').append(master);

		});

		$('#modaldelete').on('click', function() {
			deleteAllcheckeditems();
		});

		$('.edit-task').on('click', function(e)
		{
			var master = $('#master').val();
			var master_selected = $('#master :selected').text();
			var edittext = $(this).val().split('|');
			var editid = edittext[0];
			var editdesc = edittext[1];

			$('#itemid').val(editid);
			$('#masterid').val(master);
			$('#inputname').val(editdesc);
			$('#hdnaction').val('EDIT');

			$('.modal-title').html('');
			$('.modal-title').append(master_selected);
			$('#myModal').modal('show');

			$('#er1').html("");
			$('#inputname').keyup(function(){
				$('#er1').html("");
			});
		});

		$('.add_category').on('click', function(e)
		{
			e.preventDefault();

			$('#inputcatname').val("");
			$("#inputcatname").focus();

			$('.modal-title').html('');
			$('#myModalCategory').modal('show');
			$('.modal-title').append('Add New Category');

			$('#er1').html("");

			$('#inputname').keyup(function(){
				$('#er1').html("");
			});
		});

		$('.edit_category').on('click', function(e)
		{
			var master = $('#master').val();
			var master_selected = $('#master :selected').text();
			e.preventDefault();

			$('#catmasterid').val(master);
			$('#inputcatname').val(master_selected);
			$("#inputcatname").focus();
			$('#hdncataction').val('EDIT');

			$('.modal-title').html('');
			$('#myModalCategory').modal('show');
			$('.modal-title').append('Edit Category');

			$('#er1').html("");

			$('#inputname').keyup(function(){
				$('#er1').html("");
			});
		});

	});

	function deleteAllcheckeditems(){
		var master =$('#master :selected').val();
		var tray = [];
		$(".checkboxes:checked").each(function () {
			tray.push($(this).val());
		});
		var traycount =tray.length;

		$.post(DeleteUrl,
		{
			_token: token,
			tray : tray,
			traycount : traycount,
			master : master
		})
		.done(function(data)
		{
    				// alert(data);
    				$.alert('Selected item/s not deleted succesfully.', {
    					position: ['center', [-0.42, 0]],
    					type: 'info',
    					closeTime: 3000,
    					autoClose: true
    				});
    				window.location.href = SelectedUrl + master;
    			})
		.fail(function()
		{
			$.alert('Selected item was not deleted. Please try again.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		});
	}