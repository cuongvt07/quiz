<x-admin.table :headers="['#', 'Tên', 'Email', 'Hành động']">
    @foreach ($admins as $i => $admin)
        <tr class="border-b hover:bg-gray-50" data-id="{{ $admin->id }}">
            <td class="px-3 py-2 text-gray-500">{{ $i + 1 }}</td>
            <td class="px-3 py-2">{{ $admin->name }}</td>
            <td class="px-3 py-2">{{ $admin->email }}</td>
            <td class="px-3 py-2 flex gap-2 justify-end">
                <button class="p-2 rounded hover:bg-blue-100 text-blue-600 btn-edit-admin" data-id="{{ $admin->id }}" data-name="{{ $admin->name }}" data-email="{{ $admin->email }}" title="Sửa">
                    <i data-feather="edit-2" style="width:20px;height:20px"></i>
                </button>
                <button class="p-2 rounded hover:bg-red-100 text-red-600 btn-delete-admin" data-id="{{ $admin->id }}" title="Xoá">
                    <i data-feather="trash-2" style="width:20px;height:20px"></i>
                </button>
            </td>
        </tr>
    @endforeach
</x-admin.table>
