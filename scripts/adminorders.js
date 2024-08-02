function generateActionButtons(row) {
    let buttons = '<div class="d-flex">';
    
    if (row.status === 'completed') {
        return '<div class="d-flex">No actions available</div>';
    }
    if (row.status === 'accepted') {
        buttons += '<form method="post" action="" class="mr-2">' +
                   '<input class="btn btn-success btn-sm" value="Complete" type="submit">' +
                   '<input type="hidden" value="' + row.order_id + '" name="order_id">' +
                   '<input type="hidden" value="completed" name="completed">' +
                   '</form>';
    }

    if (row.status !== 'accepted') {
        buttons += '<form method="post" action="" class="mr-2">' +
                   '<input class="btn btn-success btn-sm" value="Accept" type="submit">' +
                   '<input type="hidden" value="' + row.order_id + '" name="order_id">' +
                   '<input type="hidden" value="accepted" name="accepted">' +
                   '</form>';
    }

    if (row.status !== 'rejected') {
        buttons += '<form method="post" action="" class="mr-2">' +
                   '<input class="btn btn-danger btn-sm" value="Reject" type="submit">' +
                   '<input type="hidden" value="' + row.order_id + '" name="order_id">' +
                   '<input type="hidden" value="rejected" name="rejected">' +
                   '</form>';
    }

    if (row.status !== 'hold') {
        buttons += '<form method="post" action="">' +
                   '<input class="btn btn-secondary btn-sm" value="Hold" type="submit">' +
                   '<input type="hidden" value="' + row.order_id + '" name="order_id">' +
                   '<input type="hidden" value="hold" name="hold">' +
                   '</form>';
    }

    buttons += '</div>';
    return buttons;
}

function loadMoreOrders(status, tableBodyId, loadMoreButtonId) {
    let offset = 5;
    $('#' + loadMoreButtonId).on('click', function() {
        $.ajax({
            url: 'adminhandle.php',
            method: 'POST',
            data: { offset: offset, status: status },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    let rows = '';
                    $.each(data, function(index, row) {
                        let statusBadgeClass = row.status === 'completed' ? 'badge-success' : 'badge-warning';
                        rows += '<tr>' +
                                    '<td>' + row.order_id + '</td>' +
                                    '<td>' + row.name + '</td>' +
                                    '<td>' + row.order_date + '</td>' +
                                    '<td>$' + row.total + '</td>' +
                                    '<td><span class="badge ' + statusBadgeClass + '">' + row.status + '</span></td>' +
                                    '<td>' + generateActionButtons(row) + '</td>' +
                                    '<td>' +
                                        '<form method="post" action="adminordersinfo.php">' +
                                            '<input class="btn btn-info btn-sm" value="View Details" type="submit">' +
                                            '<input type="hidden" value="' + row.order_id + '" name="order_id">' +
                                        '</form>' +
                                    '</td>' +
                                '</tr>';
                    });
                    $('#' + tableBodyId).append(rows);
                    offset += 5;
                } else {
                    $('#' + loadMoreButtonId).prop('disabled', true).text('No More Orders');
                }
            },
            error: function() {
                alert('Error loading more orders.');
            }
        });
    });
}

$(document).ready(function() {
    loadMoreOrders('processing', 'orderTableBody1', 'loadMore1');
    loadMoreOrders('rejected', 'orderTableBody2', 'loadMore2');
    loadMoreOrders('accepted', 'orderTableBody3', 'loadMore3');
    loadMoreOrders('hold', 'orderTableBody4', 'loadMore4');
    loadMoreOrders('completed', 'orderTableBody5', 'loadMore5');
});


