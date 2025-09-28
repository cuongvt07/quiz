@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
    <h1 class="text-2xl font-bold flex items-center gap-2"><i data-feather="shield"></i> Quản lý tài khoản admin</h1>
    <button id="btn-add-admin" class="flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition text-sm font-semibold"><i data-feather="plus"></i> Thêm mới</button>
</div>
<div id="admin-table-wrapper">
    @include('admin.admins.table', ['admins' => $admins])
</div>
@include('admin.admins.modal')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
if(window.feather) feather.replace();
</script>
<script src="{{ asset('js/admins.js') }}"></script>
@endsection
