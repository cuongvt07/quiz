<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // Đề thi Năng lực
    public function indexNangLuc(Request $request)
    {
        $subject_id = $request->get('subject_id');
        $subjects = Subject::where('type', 'nang_luc')->orderBy('name')->get();
        $query = Exam::with('subject')->whereHas('subject', function($q){ $q->where('type', 'nang_luc'); });
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }
        $exams = $query->orderByDesc('id')->paginate(15);
        return view('admin.exams.index', compact('exams', 'subjects', 'subject_id'));
    }

    // Đề thi Tư duy
    public function indexTuDuy(Request $request)
    {
        $subject_id = $request->get('subject_id');
        $subjects = Subject::where('type', 'tu_duy')->orderBy('name')->get();
        $query = Exam::with('subject')->whereHas('subject', function($q){ $q->where('type', 'tu_duy'); });
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }
        $exams = $query->orderByDesc('id')->paginate(15);
        return view('admin.exams.index', compact('exams', 'subjects', 'subject_id'));
    }
    public function index(Request $request)
    {
        $subject_id = $request->get('subject_id');
        $subjects = Subject::orderBy('name')->get();
        $query = Exam::with('subject');
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }
        $exams = $query->orderByDesc('id')->paginate(15);
        return view('admin.exams.index', compact('exams', 'subjects', 'subject_id'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
        ]);
    $exam = Exam::create($data);
    $type = $exam->subject->type ?? null;
    $route = $type === 'nang_luc' ? 'admin.exams.nangluc' : ($type === 'tu_duy' ? 'admin.exams.tuduy' : 'admin.exams.index');
    return redirect()->route($route)->with('success', 'Thêm đề thi thành công!');
    }

    public function show(Exam $exam)
    {
    $exam->load(['subject', 'questions.questionChoices']);
    return view('admin.exams.show', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
        ]);
    $exam->update($data);
    $type = $exam->subject->type ?? null;
    $route = $type === 'nang_luc' ? 'admin.exams.nangluc' : ($type === 'tu_duy' ? 'admin.exams.tuduy' : 'admin.exams.index');
    return redirect()->route($route)->with('success', 'Cập nhật đề thi thành công!');
    }

    public function destroy(Exam $exam)
    {
    $type = $exam->subject->type ?? null;
    $exam->delete();
    $route = $type === 'nang_luc' ? 'admin.exams.nangluc' : ($type === 'tu_duy' ? 'admin.exams.tuduy' : 'admin.exams.index');
    return redirect()->route($route)->with('success', 'Xoá đề thi thành công!');
    }
}
