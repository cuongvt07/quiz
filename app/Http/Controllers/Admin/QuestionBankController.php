<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function subjects(Request $request)
    {
        $subjects = Subject::withCount('questions')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.questions.subjects', compact('subjects'));
    }

    public function list(Request $request)
    {
        $query = Question::with(['questionChoices', 'exams.subject']);

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('question', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('questionChoices', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->whereHas('exams', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        // Filter by question type
        if ($request->filled('loai')) {
            $query->where('loai', $request->loai);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $questions = $query->orderByDesc('id')->paginate(15);
        $subjects = Subject::orderBy('name')->get();
        $questionTypes = Question::getDanhSachLoai();

        return view('admin.questions.list', compact('questions', 'subjects', 'questionTypes'));
    }
}