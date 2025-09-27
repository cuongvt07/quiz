<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionChoice;

class QuestionController extends Controller
{
    // Hiển thị danh sách câu hỏi
    public function index()
    {
        $questions = Question::with('choices')->get();
        return view('admin.questions.index', compact('questions'));
    }

    // Hiển thị chi tiết câu hỏi
    public function show($id)
    {
        $question = Question::with('choices')->findOrFail($id);
        return view('admin.questions.show', compact('question'));
    }
}
