@extends('layouts.admin')

@section('content')
<h1 class="text-xl font-bold mb-4">Thêm danh mục câu hỏi</h1>
@include('admin.categories.form', [
    'action' => route('admin.categories.store'),
    'category' => null,
    'method' => 'POST',
])
@endsection
