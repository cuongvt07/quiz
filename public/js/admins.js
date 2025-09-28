$(function() {
    // Open modal for add
    $('#btn-add-admin').on('click', function() {
        $('#admin-form')[0].reset();
        $('#admin-id').val('');
        $('#password-note').show();
        $('#adminModalLabel').text('Thêm tài khoản admin');
        $('#adminModal').modal('show');
    });

    // Open modal for edit
    $(document).on('click', '.btn-edit-admin', function() {
        $('#admin-form')[0].reset();
        $('#admin-id').val($(this).data('id'));
        $('#admin-name').val($(this).data('name'));
        $('#admin-email').val($(this).data('email'));
        $('#password-note').show();
        $('#adminModalLabel').text('Sửa tài khoản admin');
        $('#adminModal').modal('show');
    });

    // Submit form (add/edit)
    $('#admin-form').on('submit', function(e) {
        e.preventDefault();
        var id = $('#admin-id').val();
        var url = id ? '/admin/admins/' + id : '/admin/admins';
        var method = id ? 'PUT' : 'POST';
        var data = $(this).serialize();
        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(res) {
                $('#adminModal').modal('hide');
                reloadAdminTable();
            },
            error: function(xhr) {
                alert('Lỗi: ' + (xhr.responseJSON?.message || 'Không xác định'));
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete-admin', function() {
        if(!confirm('Bạn có chắc chắn muốn xoá tài khoản này?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/admins/' + id,
            type: 'DELETE',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                reloadAdminTable();
            },
            error: function(xhr) {
                alert('Lỗi: ' + (xhr.responseJSON?.message || 'Không xác định'));
            }
        });
    });

    function reloadAdminTable() {
        $('#admin-table-wrapper').load(location.href + ' #admin-table-wrapper > *', function(){
            if(window.feather) feather.replace();
        });
    }
});
