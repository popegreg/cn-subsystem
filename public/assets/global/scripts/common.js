$(function() {
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('[autofocus]').focus();
    });
});
/**
 * Open Message Modal
 * @param  {String} msg [message content]
 * @param  {String} status [is it success or failed]
 */
function msg(msg,status) {
	$('#msg_content').html(msg);

	switch(status) {
	    case 'success':
	        $('#msg_status').css('color', '#1BA39C');
	        $('#msg_status').html('<strong><i class="fa fa-check"></i></strong> '+jsUcfirst(status)+"!");
	        break;
	    case 'failed':
	        $('#msg_status').css('color', '#F36A5A');
	        $('#msg_status').html('<strong><i class="fa fa-exclamation-circle"></i></strong> '+jsUcfirst(status)+"!");
	        break;
	    case 'error':
	        $('#msg_status').css('color', '#E7505A');
	        $('#msg_status').html('<strong><i class="fa fa-times"></i></strong> '+jsUcfirst(status)+"!");
	        break;
	    default:
	        $('#msg_status').css('color', '#1BA39C');
	}
	$('#msg_modal').modal('show');
}

/**
 * Uppercase first letter
 * @param  {[String]} word [word to uppercase first letter]
 * @return {[String]}      [word with uppercase first letter]
 */
function jsUcfirst(word) {
    return word.charAt(0).toUpperCase() + word.slice(1);
}

/**
 * Save data AJAX Request
 * @param  {[String]} url  [description]
 * @param  {[Object]} data [description]
 * @return {[msg]}      [description]
 */
function save(url,data) {
	// $('#loading').modal('show');
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'JSON',
		data: data,
	}).done(function(data,textStatus,jqXHR) {
		// $('#loading').modal('hide');
		console.log(data);
		msg(data.msg,data.status);
	}).fail(function(data,textStatus,jqXHR) {
		msg("There's an error occurred while processing.",'error');
	});
}

/**
 * Open Delete Modal
 * @return {[type]} [description]
 */
function delete_modal() {
	$('#delete_modal').modal('show');
}

function confirm_modal(question) {
    $('#confirm_question').html(question);
    $('#confirm_modal').modal('show');
}

/**
 * Delete data AJAX Request
 * @param  {[String]} url  [description]
 * @param  {[Object]} data [description]
 * @return {[msg]}      [description]
 */
function confirm_delete(url,data) {
    $('#confirm_modal').modal('hide');
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'JSON',
		data: data,
	}).done(function(data,textStatus,jqXHR) {
		msg(data.msg,data.status);
	}).fail(function(data,textStatus,jqXHR) {
		msg("There's an error occurred while processing.",'error');
	});
}

/**
 * Check all of checkeboxes
 * @param  {[String]} .checkAllClass  [description]
 * @param  {[String]} .checkItemClass [description]
 * @return {[type]}                [description]
 */
function checkAllCheckboxesInTable(checkAllClass,checkItemClass) {
	$(checkAllClass).on('change', function(e) {
		$('input:checkbox'+checkItemClass).not(this).prop('checked', this.checked);
	});
}

/**
 * Datatables
 * @param  {[String]} tbl_id       [description]
 * @param  {[String]} Url          [description]
 * @param  {[Array]} dataColumn   Data
 * @param  {Array}  aoColumnDefs  Define css styles per td
 * @param  {Number} inOrder       Define what column will in descending Order
 * @param  {Array}  unOrderable   Define what columns will not be orderable
 * @param  {Array}  unSearchable  Define what columns will not be searchable
 * @return {[Datable]}            [description]
 */
function getDatatable(tbl_id,Url,dataColumn,aoColumnDefs,inOrder,unOrderable,unSearchable) {
    var table = $('#'+tbl_id);

    table.dataTable().fnClearTable();
    table.dataTable().fnDestroy();
    table.dataTable({
       // "scrollY": "300px",
        // paging: false,
        processing: true,
        serverSide: true,
        ajax: Url,
        deferRender: true,
        columns: dataColumn,
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
        // bStateSave: true,
        aoColumnDefs: aoColumnDefs,
        lengthMenu: [
            [10, 20, 50, 100, 150, 200, 500, -1],
            [10, 20, 50, 100, 150, 200, 500, "All"]
        ],
        pageLength: 10,            
        pagingType: "bootstrap_full_number",
        columnDefs: [{
            orderable: false,
            targets: unOrderable
        }, {
        	searchable: false,
            targets: unSearchable
        }],
        order: [
            [inOrder, "desc"]
        ]
    });

    var tableWrapper = jQuery('#'+tbl_id+'_wrapper');

    table.find('.group-checkable').change(function () {
        var set = jQuery(this).attr("data-set");
        var checked = jQuery(this).is(":checked");
        jQuery(set).each(function () {
            if (checked) {
                $(this).prop("checked", true);
                $(this).parents('tr').addClass("active");
            } else {
                $(this).prop("checked", false);
                $(this).parents('tr').removeClass("active");
            }
        });
        jQuery.uniform.update(set);
    });

    table.on('change', 'tbody tr .checkboxes', function () {
        $(this).parents('tr').toggleClass("active");
    });
}

function openloading() {
	$('#loading').modal('show');
}

function closeloading() {
	$('#loading').modal('hide');
}

function formatDate(date) {
    var d = new Date(date);

    var month = d.getMonth()+1;
    var day = d.getDate();

    var newdate = ((''+month).length<2 ? '0' : '') + month + '/' +
        ((''+day).length<2 ? '0' : '') + day + '/' + d.getFullYear();
        
    return newdate;
}