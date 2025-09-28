<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query();
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }
        $subjects = $query->orderBy('id', 'desc')->paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        Subject::create($data);
        return redirect()->route('admin.subjects.index')->with('success', 'Tạo môn học thành công!');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $subject->update($data);
        return redirect()->route('admin.subjects.index')->with('success', 'Cập nhật môn học thành công!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Đã xoá môn học!');
    }
}
