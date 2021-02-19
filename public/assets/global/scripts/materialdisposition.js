var dispositionDetails = [];
var vState = '';

$(function () {
    viewState('');

    getDataDisposition('', '');

    $('#btn_add').on('click', function() {
        viewState('add');
    });

    $('#btn_edit').on('click', function () {
        viewState('edit');
    });

    $('#btn_disregard').on('click', function () {
        if ($('#transaction_no').val() !== "") {
            getDataDisposition('', $('#transaction_no').val());
        } else {
            getDataDisposition('', '');
        }
        viewState('');
    });

    $(document).on('shown.bs.modal', function () {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });

    $('#btn_add_details').on('click', function() {
        clearDetails();
        InventoryDataTable([]);

        $('#inventory_modal').modal('show');
    });

    $('#transaction_no').on('change', function() {
        getDataDisposition('', $(this).val());
    });

    $('#item').on('change', function() {
        getInventory($(this).val());
    });

    $('#tbl_inventory_body').on('click', '.btn_select_item_inv', function() {
        $('#inv_id').val($(this).attr('data-id'));
        $('#item').val($(this).attr('data-item'));
        $('#item_desc').val($(this).attr('data-item_desc'));
        $('#lot_no').val($(this).attr('data-lot_no'));
    });

    $('#btn_save_item').on('click', function() {
        if ($('#key_id').val() !== "") {
            dispositionDetails[$('#key_id').val()] = {
                id: $('#d_id').val(),
                key_id: $('#key_id').val(),
                inv_id: $('#inv_id').val(),
                item: $('#item').val(),
                item_desc: $('#item_desc').val(),
                qty: $('#qty').val(),
                lot_no: $('#lot_no').val(),
                expiration: $('#exp_date').val(),
                disposition: $('#disposition_status').val(),
                remarks: $('#item_remarks').val()
            };
        } else {
            var key_id = dispositionDetails.length;

            dispositionDetails.push({
                id: '',
                key_id: key_id,
                inv_id: $('#inv_id').val(),
                item: $('#item').val(),
                item_desc: $('#item_desc').val(),
                qty: $('#qty').val(),
                lot_no: $('#lot_no').val(),
                expiration: $('#exp_date').val(),
                disposition: $('#disposition_status').val(),
                remarks: $('#item_remarks').val()
            });
        }

        console.log(dispositionDetails);
        
        DispositionDataTable(dispositionDetails);

        $('#inventory_modal').modal('hide');
    });

    $('#tbl_disposition_body').on('click','.btn_edit_detail', function() {
        $('#d_id').val($(this).attr('data-id'));
        $('#key_id').val($(this).attr('data-key'));
        $('#inv_id').val($(this).attr('data-inv_id'));
        $('#item').val($(this).attr('data-item'));
        $('#item_desc').val($(this).attr('data-item_desc'));
        $('#lot_no').val($(this).attr('data-lot_no'));
        $('#qty').val($(this).attr('data-qty'));
        $('#exp_date').val($(this).attr('data-expiration'));
        $('#disposition_status').val($(this).attr('data-disposition'));
        $('#item_remarks').val($(this).attr('data-remarks'));

        getInventory($(this).attr('data-item'));

        $('#inventory_modal').modal('show');
    });

    $('#btn_save').on('click', function() {
        var param = {
            _token: token,
            disposition_id: $('#disposition_id').val(),
            transaction_no: $('#transaction_no').val(),
            remarks: $('#remarks').val(),
            detail_item: $('input[name="detail_item[]"]').map(function () { return $(this).val(); }).get(),
            detail_id: $('input[name="detail_id[]"]').map(function() { return $(this).val(); }).get(),
            detail_inv_id: $('input[name="detail_inv_id[]"]').map(function() { return $(this).val(); }).get(),
            detail_item_desc: $('input[name="detail_item_desc[]"]').map(function() { return $(this).val(); }).get(),
            detail_qty: $('input[name="detail_qty[]"]').map(function() { return $(this).val(); }).get(),
            detail_lot_no: $('input[name="detail_lot_no[]"]').map(function() { return $(this).val(); }).get(),
            detail_expiration: $('input[name="detail_expiration[]"]').map(function() { return $(this).val(); }).get(),
            detail_disposition: $('input[name="detail_disposition[]"]').map(function() { return $(this).val(); }).get(),
            detail_remarks: $('input[name="detail_remarks[]"]').map(function() { return $(this).val(); }).get()
        }

        $('#loading').modal('show');
        $.ajax({
            url: saveDispositionURL,
            type: 'POST',
            dataType: 'JSON',
            data: param,
        }).done(function (data, textStatus, jqXHR) {
            if (data.hasOwnProperty('trans_no')) {
                getDataDisposition('',data.trans_no);
            }
            msg(data.msg, data.status);
            viewState('');
        }).fail(function (jqXHR, textStatus, errorThrown) {
            $('#loading').modal('hide');
            console.log(jqXHR);
            msg("There was an error occurred while processing.", 'error');
        }).always( function() {
            $('#loading').modal('hide');
        });
    });

    $('#btn_search').on('click', function() {
        resetSearch();
        $('#modal_search').modal('show');
    });

    $('#btn_filter').on('click', function () {
        searchDisposition();
    });

    $('#btn_reset').on('click', function() {
        resetSearch();
    });

    $('#tbl_search_body').on('click', '.btn_select_item_srch', function() {
        getDataDisposition('', $(this).attr('data-transaction_no'));
        $('#modal_search').modal('hide');
    });
});/*End of Main function*/

function viewState(vstate) {
    switch (vstate) {
        case 'add':
            vState = 'add';
            $('#transaction_no').prop('readonly', true);
            $('#btn_min').prop('disabled', true);
            $('#btn_prv').prop('disabled', true);
            $('#btn_nxt').prop('disabled', true);
            $('#btn_max').prop('disabled', true);
            $('#remarks').prop('readonly', false);

            $('#btn_add_details').show();
            $('#btn_remove_details').show();

            $('#btn_add').hide();
            $('#btn_edit').hide();
            $('#btn_save').show();
            $('#btn_disregard').show();
            $('#btn_excel').hide();
            $('#btn_search').hide();

            dispositionDetails = [];
            DispositionDataTable(dispositionDetails);

            clear();
            clearDetails();
            break;
        
        case 'edit':
            vState = 'edit';
            $('#transaction_no').prop('readonly', true);
            $('#btn_min').prop('disabled', true);
            $('#btn_prv').prop('disabled', true);
            $('#btn_nxt').prop('disabled', true);
            $('#btn_max').prop('disabled', true);
            $('#remarks').prop('readonly', false);

            $('.check_all_disposition').prop('disabled', true);
            $('.btn_edit_detail').prop('disabled', false);

            $('#btn_add_details').show();
            $('#btn_remove_details').show();

            $('#btn_add').hide();
            $('#btn_edit').hide();
            $('#btn_save').show();
            $('#btn_disregard').show();
            $('#btn_excel').hide();
            $('#btn_search').hide();

            clearDetails();
            break;
    
        default:
            vState = 'view';
            $('#transaction_no').prop('readonly', false);
            $('#btn_min').prop('disabled', false);
            $('#btn_prv').prop('disabled', false);
            $('#btn_nxt').prop('disabled', false);
            $('#btn_max').prop('disabled', false);
            $('#remarks').prop('readonly', true);

            $('.check_all_disposition').prop('disabled', false);
            $('.btn_edit_detail').prop('disabled', false);
            $('#btn_search').show();

            if (parseInt(access_state) !== 2) {
                $('#btn_add_details').hide();
                $('#btn_remove_details').hide();
                $('#btn_add').show();
                $('#btn_edit').show();
                $('#btn_save').hide();
                $('#btn_disregard').hide();
                $('#btn_excel').show();
            } else {
                $('#btn_add_details').hide();
                $('#btn_remove_details').hide();
                $('#btn_add').hide();
                $('#btn_edit').hide();
                $('#btn_save').hide();
                $('#btn_disregard').hide();
                $('#btn_excel').hide();
            }
            break;
    }
}

function navigate(to) {
    getDataDisposition(to, $('#transaction_no').val());
}

function clear() {
    $('#disposition_id').val('');
    $('#transaction_no').val('');
    $('#remarks').val('');
    $('#create_user').val('');
    $('#created_at').val('');
    $('#update_user').val('');
    $('#updated_at').val('');
}

function clearDetails() {
    $('#inv_id').val('');
    $('#key_id').val('');
    $('#d_id').val('');
    $('#item').val('');
    $('#item_desc').val('');
    $('#qty').val('');
    $('#lot_no').val('');
    $('#exp_date').val('');
    $('#disposition_status').val('');
    $('#item_remarks').val('');
}

function resetSearch() {
    $('#srch_from').val('');
    $('#srch_to').val('');
    $('#srch_trans_no').val('');
    $('#srch_item').val('');
    $('#srch_lot_no').val('');

    SearchDataTable([]);
}

function getDataDisposition(to, transaction_no) {
    $('#loading').modal('show');

    $.ajax({
        url: getDataDispositionURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            to: to,
            transaction_no: transaction_no
        },
    }).done(function (data, textStatus, xhr) {
        if (data.hasOwnProperty('status')) {
            msg(data.msg,data.status);
        }

        dispositionDetails = [];

        $.each(data.details, function(i,x) {
            dispositionDetails.push({
                key_id: i,
                id: x.id,
                transaction_id: x.transaction_id,
                item: x.item,
                item_desc: x.item_desc,
                lot_no: x.lot_no,
                qty: x.qty,
                expiration: x.expiration,
                disposition: x.disposition,
                remarks: x.remarks,
                inv_id: x.inv_id
            });
        });
        
        fillTransactionInfo(data.transaction);
        DispositionDataTable(dispositionDetails);

    }).fail(function (xhr, textStatus, errorThrow) {
        msg(errorThrow, textStatus);
    }).always(function () {
        $('#loading').modal('hide');
    });
}

function DispositionDataTable(arr) {
    $('#tbl_disposition').dataTable().fnClearTable();
    $('#tbl_disposition').dataTable().fnDestroy();
    $('#tbl_disposition').dataTable({
        data: arr,
        bLengthChange: false,
        // scrollY: "250px",
        searching: false,
        order: [[2,'asc']],
        columns: [
            {
                data: function (x) {
                    if (x.id == '') {
                        return '';
                    } else {
                        return "<input type='checkbox' class='check_item_po_detail' data-id='" + x.id + "'>";
                    }
                    
                }, searchable: false, orderable: false
            },

            {
                data: function (x) {
                    return "<button type='button' class='btn btn-sm btn-primary btn_edit_detail' " +
                                "data-key='" + x.key_id +"' " +
                                "data-id='" + x.id + "'" +
                                "data-item='" + x.item + "'" +
                                "data-item_desc='" + x.item_desc + "'" +
                                "data-lot_no='" + x.lot_no + "'" +
                                "data-qty='" + x.qty + "'" +
                                "data-expiration='" + x.expiration + "'" +
                                "data-disposition='" + x.disposition + "'" +
                                "data-remarks='" + x.remarks + "'" +
                                "data-inv_id='" + x.inv_id + "'>" +
                                "<i class='fa fa-edit'></i>" +
                            "</button>";
                }, searchable: false, orderable: false
            },

            {
                data: function (x) {
                    return x.item + "<input type='hidden' name='detail_item[]' value='" + x.item + "'>"+
                        "<input type='hidden' name='detail_id[]' value='" + x.id + "'>"+
                        "<input type='hidden' name='detail_inv_id[]' value='" + x.inv_id + "'>";
                }
            },

            {
                data: function (x) {
                    return x.item_desc + "<input type='hidden' name='detail_item_desc[]' value='" + x.item_desc + "'>";
                }
            },

            {
                data: function (x) {
                    return x.qty + "<input type='hidden' name='detail_qty[]' value='" + x.qty + "'>";
                }
            },

            {
                data: function (x) {
                    return x.lot_no + "<input type='hidden' name='detail_lot_no[]' value='" + x.lot_no + "'>";
                }
            },

            {
                data: function (x) {
                    return x.expiration + "<input type='hidden' name='detail_expiration[]' value='" + x.expiration + "'>";
                }
            },

            {
                data: function (x) {
                    return x.disposition + "<input type='hidden' name='detail_disposition[]' value='" + x.disposition + "'>";
                }
            },

            {
                data: function (x) {
                    return x.remarks + "<input type='hidden' name='detail_remarks[]' value='" + x.remarks + "'>";
                }
            }
        ],
        initComplete: function() {
            switch (vState) {
                case 'add':
                    $('.check_all_disposition').prop('disabled', true);
                    $('.check_item_po_detail').prop('disabled', true);
                    $('.btn_edit_detail').prop('disabled', true);
                    break;
                
                case 'edit':
                    $('.check_all_disposition').prop('disabled', false);
                    $('.check_item_po_detail').prop('disabled', false);
                    $('.btn_edit_detail').prop('disabled', false);
                    break;
            
                default:
                    $('.check_all_disposition').prop('disabled', true);
                    $('.check_item_po_detail').prop('disabled', true);
                    $('.btn_edit_detail').prop('disabled', true);
                    break;
            }
            $('#loading').modal('hide');
        }
    });
}

function fillTransactionInfo(transaction) {
    $('#disposition_id').val(transaction.id);
    $('#transaction_no').val(transaction.transaction_no);
    $('#remarks').val(transaction.remarks);
    $('#create_user').val(transaction.create_user);
    $('#created_at').val(transaction.created_at);
    $('#update_user').val(transaction.update_user);
    $('#updated_at').val(transaction.updated_at);
}

function getInventory(item) {
    $('#loading').modal('show');

    $.ajax({
        url: getInventoryURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            item: item
        },
    }).done(function (data, textStatus, xhr) {
        if (data.hasOwnProperty('status')) {
            msg(data.msg, data.status);
        } else {
            InventoryDataTable(data);
        }
    }).fail(function (xhr, textStatus, errorThrow) {
        msg(errorThrow, textStatus);
    }).always(function () {
        $('#loading').modal('hide');
    });
}

function InventoryDataTable(arr) {
    $('#tbl_inventory').dataTable().fnClearTable();
    $('#tbl_inventory').dataTable().fnDestroy();
    $('#tbl_inventory').dataTable({
        data: arr,
        bLengthChange: false,
        scrollY: "200px",
        searching: false,
        paging: false,
        order: [[5, 'asc'], [4, 'asc']],
        columns: [
            {
                data: function (x) {
                    return '<button type="button" class="btn btn-sm blue btn_select_item_inv" data-id="' + x.id + '" data-location="' + x.location + '"' +
                        'data-qty="' + x.qty + '" data-item="' + x.item + '" data-item_desc="' + x.item_desc + '" data-lot_no="' + x.lot_no + '">' +
                        '<i class="fa fa-edit"></i>' +
                        '</button>';
                }, searchable: false, orderable: false
            },

            { data: 'item' },
            { data: 'item_desc' },
            { data: 'qty' },
            { data: 'lot_no' },
            { data: 'location' },
            { data: 'received_date' }
        ],
        initComplete: function () {
            $('#loading').modal('hide');
        }
    });
}

function searchDisposition() {
    $('#loading').modal('show');

    $.ajax({
        url: searchDispositionURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            srch_from: $('#srch_from').val(),
            srch_to: $('#srch_to').val(),
            srch_trans_no: $('#srch_trans_no').val(),
            srch_item: $('#srch_item').val(),
            srch_lot_no: $('#srch_lot_no').val()
        },
    }).done(function (data, textStatus, xhr) {
        if (data.hasOwnProperty('status')) {
            msg(data.msg, data.status);
        } else {
            SearchDataTable(data);
        }
    }).fail(function (xhr, textStatus, errorThrow) {
        msg(errorThrow, textStatus);
    }).always(function () {
        $('#loading').modal('hide');
    });
}

function SearchDataTable(arr) {
    $('#tbl_search').dataTable().fnClearTable();
    $('#tbl_search').dataTable().fnDestroy();
    $('#tbl_search').dataTable({
        data: arr,
        bLengthChange: false,
        searching: false,
        order: [[7, 'desc']],
        columns: [
            {
                data: function (x) {
                    return '<button type="button" class="btn btn-sm blue btn_select_item_srch" data-id="' + x.id + '" data-transaction_no="' + x.transaction_no + '"' +
                        'data-qty="' + x.qty + '" data-item="' + x.item + '" data-item_desc="' + x.item_desc + '" data-lot_no="' + x.lot_no + '"'+
                        'data-create_user="' + x.create_user + '" data-created_at="' + x.created_at + '" >' +
                        '<i class="fa fa-edit"></i>' +
                        '</button>';
                }, searchable: false, orderable: false
            },
            { data: 'transaction_no' },
            { data: 'item' },
            { data: 'item_desc' },
            { data: 'qty' },
            { data: 'lot_no' },
            { data: 'create_user' },
            { data: 'created_at' }
        ],
        initComplete: function () {
            $('#loading').modal('hide');
        }
    });
}