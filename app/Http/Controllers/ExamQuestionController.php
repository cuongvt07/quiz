<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\QuestionChoice;
use Illuminate\Http\Request;

class ExamQuestionController extends Controller
{
    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'question' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean'
        ]);

        $question = $exam->questions()->create([
            'question' => $request->question,
            'category_id' => $request->category_id,
            'is_active' => $request->boolean('is_active', true)
        ]);

        foreach ($request->answers as $index => $answer) {
            $question->questionChoices()->create([
                'name' => $answer['name'],
                'is_correct' => $index == $request->correct_answer,
                'index' => $index
            ]);
        }

        return redirect()->back()->with('success', 'Thêm câu hỏi thành công!');
    }

    public function update(Request $request, Exam $exam, ExamQuestion $question)
    {
        $request->validate([
            'question' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean'
        ]);

        $question->update([
            'question' => $request->question,
            'category_id' => $request->category_id,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->back()->with('success', 'Cập nhật câu hỏi thành công!');
    }

    public function destroy(Exam $exam, ExamQuestion $question)
    {
        $question->delete();
        return redirect()->back()->with('success', 'Xoá câu hỏi thành công!');
    }
}