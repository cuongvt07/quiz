@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Chi tiết câu hỏi</h1>
    <p><strong>Nội dung:</strong> {{ $question->content }}</p>
    <h3>Đáp án:</h3>
    <ul>
        @foreach ($question->choices as $choice)
            <li>{{ $choice->content }} @if($choice->is_correct) (Đúng) @endif</li>
        @endforeach
    </ul>
</div>
@endsection