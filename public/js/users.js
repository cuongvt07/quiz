$(document).ready(function() {
    // Open modal for create
    $('#btnAddUser').on('click', function() {
        $('#userModalTitle').text('Thêm tài khoản');
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#userPassword').attr('required', true);
        $('#passwordNote').hide();
        $('#userModal').removeClass('hidden');
    });

    // Close modal
    $('#closeUserModal').on('click', function() {
        $('#userModal').addClass('hidden');
    });

    // Open modal for edit
    $(document).on('click', '.btnEditUser', function() {
        var id = $(this).data('id');
        $.get('/admin/users/' + id, function(user) {
            $('#userModalTitle').text('Sửa tài khoản');
            $('#userId').val(user.id);
            $('#userName').val(user.name);
            $('#userEmail').val(user.email);
            $('#userRole').val(user.role);
            $('#userPassword').val('');
            $('#userPassword').removeAttr('required');
            $('#passwordNote').show();
            $('#userModal').removeClass('hidden');
        });
    });

    // Submit form (create/update)
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#userId').val();
        var method = id ? 'PUT' : 'POST';
        var url = id ? '/admin/users/' + id : '/admin/users';
        var data = $(this).serialize();
        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(res) {
                location.reload();
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra!');
            }
        });
    });

    // Delete user
    $(document).on('click', '.btnDeleteUser', function() {
        if (!confirm('Bạn có chắc muốn xóa tài khoản này?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/users/' + id,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                location.reload();
            },
            error: function(xhr) {
                alert('Không thể xóa tài khoản!');
            }
        });
    });
});
