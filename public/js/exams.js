$(document).ready(function() {
    // Open modal for create
    $('#btnAddExam').on('click', function() {
        $('#examModalTitle').text('Thêm đề thi');
        $('#examForm')[0].reset();
        $('#examId').val('');
        $('#examModal').removeClass('hidden');
    });

    // Close modal
    $('#closeExamModal').on('click', function() {
        $('#examModal').addClass('hidden');
    });

    // Open modal for edit
    $(document).on('click', '.btnEditExam', function() {
        var id = $(this).data('id');
        $.get('/admin/exams/' + id, function(exam) {
            $('#examModalTitle').text('Sửa đề thi');
            $('#examId').val(exam.id);
            $('#examSubject').val(exam.subject_id);
            $('#examTitle').val(exam.title);
            $('#examDuration').val(exam.duration_minutes);
            $('#examTotalQuestions').val(exam.total_questions);
            $('#examModal').removeClass('hidden');
        });
    });

    // Submit form (create/update)
    $('#examForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#examId').val();
        var method = id ? 'PUT' : 'POST';
        var url = id ? '/admin/exams/' + id : '/admin/exams';
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

    // Delete exam
    $(document).on('click', '.btnDeleteExam', function() {
        if (!confirm('Bạn có chắc muốn xóa đề thi này?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/exams/' + id,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                location.reload();
            },
            error: function(xhr) {
                alert('Không thể xóa đề thi!');
            }
        });
    });
});
