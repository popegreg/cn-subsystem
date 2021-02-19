jQuery.support.cors = true;
jQuery(function($) {

    $('.rw').on("change",function() {
		disableMyText1();
		function disableMyText1(){
			var i;
			for (var i = 0; i < $('.rw').length; i++) {
				if(document.getElementById("check1"+i).checked == true){  
					document.getElementById("hidden1"+i).disabled = true;  
				}else{
					document.getElementById("hidden1"+i).disabled = false;
				}
			}
			  
		}
	});
	$('.r').on("change",function() {
		disableMyText2();
		function disableMyText2(){
			var i;
			for (var i = 0; i < $('.r').length; i++) {
				if(document.getElementById("check2"+i).checked == true){  
					document.getElementById("hidden2"+i).disabled = true;  
				}else{
					document.getElementById("hidden2"+i).disabled = false;
				}
			}
			  
		}
	});

	$('.rwedit').on("change",function() {
		disableMyText1();
		function disableMyText1(){
			var i;
			for (var i = 0; i < $('.rw').length; i++) {
				if(document.getElementById("checkedit1"+i).checked == true){  
					document.getElementById("hiddenedit1"+i).disabled = true;  
				}else{
					document.getElementById("hiddenedit1"+i).disabled = false;
				}
			}
			  
		}
	});

	$('.redit').on("change",function() {
		disableMyText2();
		function disableMyText2(){
			var i;
			for (var i = 0; i < $('.r').length; i++) {
				if(document.getElementById("checkedit2"+i).checked == true){  
					document.getElementById("hiddenedit2"+i).disabled = true;  
				}else{
					document.getElementById("hiddenedit2"+i).disabled = false;
				}
			}
			  
		}
	});

	$('.lockEdit').on("change",function() {
		disableMyLock();
		function disableMyLock(){
			if(document.getElementById("editlocked").checked == true){  
				document.getElementById("editlockedh").disabled = true;  
			}else{
				document.getElementById("editlockedh").disabled = false;
			}
		}
	});
});
	
//user id
var user_id = 0;
//var postBodyElement = null;
//

//opening modal with user_id value
$('#editbtn').on("click",function(e){
	
	getValueUsingClass(e);

	function getValueUsingClass(e){
		/* declare an checkbox array */
		var chkArray = [];
		
		/* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
		$(".checkboxes:checked").each(function() {
			chkArray.push($(this).val());
		});

		var isReadonly = document.getElementById("btn_add_user").disabled;
		var isDisabled = "";
		if(isReadonly)
		{
			isDisabled = "disabled";
		}

		
		/* we join the array separated by the comma */
		var selected;
		selected = chkArray.join(',');
		
		/* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
		if(selected.length == 1){
			//e.preventDefault();
			var id = selected; var lockCheck = ""; var ch = ""; var hid = "";
			var html = ""; var cnt = 0; var check1 = ""; var check2 = ""; var hid1 = ""; var hid2 = "";
			if (html != null) {
				html = "";
			}
			$(".modal-body #id").val( id );
			var uid = $('.checkboxes:checked').attr('data-userid');
			var lname = $('.checkboxes:checked').attr('data-lname');
			var fname = $('.checkboxes:checked').attr('data-fname');
			var mname = $('.checkboxes:checked').attr('data-mname');
			var pword = $('.checkboxes:checked').attr('data-pword');
			var locked = $('.checkboxes:checked').attr('data-locked');
			$("#usrid").val($('.checkboxes:checked').val());
			$('#editlname').val(lname);
			$('#editfname').val(fname);
			$('#editmname').val(mname);
			$('#edituserid').val(uid);
			$('#edituserid1').val(uid);
			$('#editPassword').val(pword);
			$('#editPassword').prop('disabled', true);
			
			if (locked == 1) {
				ch = "checked"; hid = "disabled";
			}else{
				ch = ""; hid = "";
			}
			lockCheck = '<label for="editlocked" class="col-md-5 control-label">'
						+	'*Locked   <input '+ isDisabled +' type="checkbox" class="lockEdit" id="editlocked" name="locked" value="1" '+ch+'/>'
						+'</label>'
						+'<input type="hidden" class="lock" id="editlockedh" name="locked" value="0" '+hid+'/>';

			$.ajax({
	            url: editUrl,
	            method: 'GET',
	            data: {
	            	_token: token,
	            	uid: uid,
	            	id: id
	            }
	        }).done(function(userprogs){

	        	
	        	$.each(userprogs, function (key, value) {//
	        		var rw = value['read_write'];
	        		if (uid == value['user_id']) {
	        			if (value['delete_flag'] == 0) {

	        				if (rw == 1) {
		        				check1 = "checked"; check2 = "";
		        				hid1 = "disabled"; hid2 = "";
		        			}else if (rw == 2) {
		        				check1 = ""; check2 = "checked";
		        				hid1 = ""; hid2 = "disabled";
		        			}else if (rw == 0) {
		        				check1 = ""; check2 = ""; hid1 = ""; hid2 = "";
		        			}

		        			html = html + '<tr class="odd gray-gallery">'
		        						+ 	'<td>'
		        						+		value['program_name']
		        						+		'<input type="hidden" name="id[]" value="'+value['id']+'">'
		        						+		'<input type="hidden" name="prog_code[]" value="'+value['program_code']+'">'
		        						+		'<input type="hidden" name="prog_name[]" value="'+value['program_name']+'">'
		        						+	'</td>'
		        						+ 	'<td>'
										+		'<input type="hidden" class="checkboxes1 rwh" id="hiddenedit1'+cnt+'" name="rw[]" value="0" '+hid1+'/>'
										+		'<input type="checkbox" class="checkboxes1 rwedit" id="checkedit1'+cnt+'" name="rw[]" value="1" '+check1+' ' + isDisabled + '/>'
										+	'</td>'
										+	'<td>'
										+		'<input type="hidden" class="checkboxes2 rh" id="hiddenedit2'+cnt+'" name="r[]" value="0" '+hid2+'/>'
										+		'<input type="checkbox" class="checkboxes2 redit" id="checkedit2'+cnt+'" name="r[]" value="2" '+check2+' ' + isDisabled + '/>'
										+	'</td>'
										+ '</tr>';
							cnt++;
							console.log(value['read_write']);
		        			
	        			}
	        		}
	        	});
	        	$('#editLockedDiv').html(lockCheck);
				$('#userprog').html(html);
	        	var modalObj = $('#editModal').modal(); // initialize
	            modalObj.modal('show');

	            $('.rwedit').on("change",function() {
					disableMyText1();
					function disableMyText1(){
						var i;
						for (var i = 0; i < $('.rw').length; i++) {
							if(document.getElementById("checkedit1"+i).checked == true){  
								document.getElementById("hiddenedit1"+i).disabled = true;  
							}else{
								document.getElementById("hiddenedit1"+i).disabled = false;
							}
						}
						  
					}
				});

				$('.redit').on("change",function() {
					disableMyText2();
					function disableMyText2(){
						var i;
						for (var i = 0; i < $('.r').length; i++) {
							if(document.getElementById("checkedit2"+i).checked == true){  
								document.getElementById("hiddenedit2"+i).disabled = true;  
							}else{
								document.getElementById("hiddenedit2"+i).disabled = false;
							}
						}
						  
					}
				});

				$('.lockEdit').on("change",function() {
					disableMyLock();
					function disableMyLock(){
						if(document.getElementById("editlocked").checked == true){  
							document.getElementById("editlockedh").disabled = true;  
						}else{
							document.getElementById("editlockedh").disabled = false;
						}
					}
				});
	        });
		}
		if(selected.length > 1){
			$.alert('Please select 1 user only.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		}
		if(selected.length < 1){
			$.alert('Please select 1 user.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		}
	}
});

$('#delbtn').click(function(e){
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
			var uid = $('.checkboxes:checked').attr('data-userid');
			$(".modal-body #id").val( id );
			$(".modal-body #user_id").val( uid );
			$('#confirm').modal({ backdrop: 'static', keyboard: false })
		}
		if(selected.length > 1){
			$.alert('Please select 1 user only.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		}
		if(selected.length < 1){
			$.alert('Please select 1 user.', {
				position: ['center', [-0.42, 0]],
				type: 'danger',
				closeTime: 3000,
				autoClose: true
			});
		}
	}
});


