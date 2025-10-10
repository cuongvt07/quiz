@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i data-feather="book"></i> Danh sách môn học
    </h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên môn học</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số câu hỏi</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($subjects as $subject)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">{{ $subject->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $subject->type === 'nang_luc' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $subject->type === 'nang_luc' ? 'Năng lực' : 'Tư duy' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-gray-900">{{ $subject->questions_count }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="{{ route('admin.questions.list', ['subject_id' => $subject->id]) }}" 
                        class="text-indigo-600 hover:text-indigo-900">
                        Xem câu hỏi
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="px-6 py-4 border-t">
        {{ $subjects->links() }}
    </div>
</div>
@endsection