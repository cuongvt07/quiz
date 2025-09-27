@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Danh sách bài thi</h1>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên bài thi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($exams as $exam)
                <tr>
                    <td>{{ $exam->id }}</td>
                    <td>{{ $exam->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection