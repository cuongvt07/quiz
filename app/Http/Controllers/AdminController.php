<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Exam;

class AdminController extends Controller
{
    // Hiển thị danh sách danh mục
    public function indexCategories(Request $request)
    {
        $query = Category::withCount('questions');
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        $categories = $query->orderByDesc('id')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    // Hiển thị danh sách môn học
    public function indexSubjects()
    {
        $subjects = Subject::all();
        return view('admin.subjects.index', compact('subjects'));
    }

    // Hiển thị danh sách bài thi
    public function indexExams()
    {
        $exams = Exam::all();
        return view('admin.exams.index', compact('exams'));
    }

    // Hiển thị form tạo đề thi
    public function createExam()
    {
        $subjects = Subject::all();
        return view('admin.exams.create', compact('subjects'));
    }

    // Lưu đề thi mới
    public function storeExam(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
        ]);

        Exam::create($request->all());

        return redirect()->route('admin.exams.index')->with('success', 'Đề thi đã được tạo thành công!');
    }

    // Danh mục câu hỏi CRUD
    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        Category::create($request->only('name', 'description'));
        return redirect()->route('admin.categories.index')->with('success', 'Tạo danh mục thành công!');
    }

    public function editCategory(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $category->update($request->only('name', 'description'));
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->questions()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xoá danh mục đang có câu hỏi liên quan!');
        }
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Xoá danh mục thành công!');
    }
}
