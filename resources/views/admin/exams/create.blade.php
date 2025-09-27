@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Tạo Đề Thi</h1>
    <form action="{{ route('admin.exams.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Tên Đề Thi:</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="subject_id">Môn Học:</label>
            <select name="subject_id" id="subject_id" class="form-control" required>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="duration_minutes">Thời Gian (phút):</label>
            <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="total_questions">Số Lượng Câu Hỏi:</label>
            <input type="number" name="total_questions" id="total_questions" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Tạo Đề Thi</button>
    </form>
</div>
@endsection