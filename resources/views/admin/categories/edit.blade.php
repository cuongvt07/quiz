@extends('layouts.admin')

@section('content')
<h1 class="text-xl font-bold mb-4">Sửa danh mục câu hỏi</h1>
@include('admin.categories.form', [
    'action' => route('admin.categories.update', $category),
    'category' => $category,
    'method' => 'PUT',
])
@endsection
