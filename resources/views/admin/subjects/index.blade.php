@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Danh sách môn học</h1>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên môn học</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subjects as $subject)
                <tr>
                    <td>{{ $subject->id }}</td>
                    <td>{{ $subject->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection