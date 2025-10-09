<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Exam;

class AdminController extends Controller
{

    // Hiển thị danh sách môn học
    public function indexSubjects()
    {
        $subjects = Subject::all();
        return view('admin.subjects.index', compact('subjects'));
    }
}
