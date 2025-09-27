@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Import Câu Hỏi</h1>
    <form action="{{ route('admin.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Chọn file Excel:</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
    </form>
</div>
@endsection