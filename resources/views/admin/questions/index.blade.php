@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Danh sách câu hỏi</h1>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nội dung câu hỏi</th>
                <th>Đáp án</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($questions as $question)
                <tr>
                    <td>{{ $question->id }}</td>
                    <td>{{ $question->content }}</td>
                    <td>
                        <ul>
                            @foreach ($question->choices as $choice)
                                <li>{{ $choice->content }} @if($choice->is_correct) (Đúng) @endif</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection