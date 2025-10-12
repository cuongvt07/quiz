<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Question;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;
use App\Services\QuestionImportService;

class ExamController extends Controller
{
    // Helper chung để ghi log lỗi
    protected function handleException(\Throwable $e, string $message, array $context = [])
    {
        Log::error($message . ': ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'context' => $context
        ]);
        return back()->with('error', $message);
    }

    // Đề thi Năng lực
    public function indexNangLuc(Request $request)
    {
        try {
            $subject_id = $request->get('subject_id');
            $subjects = Subject::where('type', Subject::TYPE_COMPETENCY)->orderBy('name')->get();

            $query = Exam::with('subject')->whereHas('subject', function ($q) {
                $q->where('type', Subject::TYPE_COMPETENCY);
            });

            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }

            $exams = $query->orderByDesc('id')->paginate(10);
            return view('admin.exams.index', compact('exams', 'subjects', 'subject_id'));
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Lỗi khi tải danh sách đề thi năng lực');
        }
    }

    // Đề thi Tư duy
    public function indexTuDuy(Request $request)
    {
        try {
            $subject_id = $request->get('subject_id');
            $subjects = Subject::where('type', Subject::TYPE_COGNITIVE)->orderBy('name')->get();

            $query = Exam::with('subject')->whereHas('subject', function ($q) {
                $q->where('type', Subject::TYPE_COGNITIVE);
            });

            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }

            $exams = $query->orderByDesc('id')->paginate(10);
            return view('admin.exams.index', compact('exams', 'subjects', 'subject_id'));
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Lỗi khi tải danh sách đề thi tư duy');
        }
    }

    // Tất cả đề thi
    public function index(Request $request)
    {
        try {
            $subject_id = $request->get('subject_id');
            $subjects = Subject::orderBy('name')->get();
            $query = Exam::with('subject');

            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }

            $exams = $query->orderByDesc('id')->paginate(10);
            return view('admin.exams.index', compact('exams', 'subjects', 'subject_id'));
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Lỗi khi tải danh sách đề thi');
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'title' => 'required|string|max:255',
                'duration_minutes' => 'required|integer|min:1',
                'total_questions' => 'required|integer|min:1',
            ]);

            $exam = Exam::create($data);
            $type = $exam->subject->type ?? null;

            $route = $type === 'nang_luc'
                ? 'admin.exams.nangluc'
                : ($type === 'tu_duy'
                    ? 'admin.exams.tuduy'
                    : 'admin.exams.index');

            return redirect()->route($route)->with('success', 'Thêm đề thi thành công!');
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Lỗi khi thêm đề thi', $request->all());
        }
    }

    public function show(Exam $exam)
    {
        try {
            $exam->load(['subject', 'questions']);
            $questionTypes = Question::getDanhSachLoai();
            return view('admin.exams.show', compact('exam', 'questionTypes'));
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Lỗi khi hiển thị chi tiết đề thi', ['exam_id' => $exam->id]);
        }
    }

    public function updateDuration(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'duration_minutes' => 'required|integer|min:1'
            ]);

            $exam->update([
                'duration_minutes' => $request->duration_minutes
            ]);

            return response()->json(['message' => 'Thời gian đã được cập nhật']);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi cập nhật thời gian: ' . $e->getMessage(), ['exam_id' => $exam->id]);
            return response()->json(['message' => 'Lỗi khi cập nhật thời gian'], 500);
        }
    }

    public function toggleQuestionStatus(Request $request, Exam $exam, $questionId)
    {
        try {
            $question = $exam->questions()->findOrFail($questionId);
            $question->update([
                'is_active' => !$question->is_active
            ]);

            return response()->json(['message' => 'Trạng thái câu hỏi đã được cập nhật']);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi đổi trạng thái câu hỏi: ' . $e->getMessage(), ['exam_id' => $exam->id, 'question_id' => $questionId]);
            return response()->json(['message' => 'Lỗi khi đổi trạng thái câu hỏi'], 500);
        }
    }

    public function saveQuestion(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'question' => 'required|string',
                'type' => 'required|in:multiple,text',
                'answers' => 'required|array|min:1',
                'answers.*.name' => 'required|string',
                'answers.*.is_correct' => 'required|boolean',
                'answers.*.explanation' => 'nullable|string'
            ]);

            $questionData = [
                'question' => $request->question,
                'is_active' => true
            ];

            if ($request->question_id) {
                $question = $exam->questions()->findOrFail($request->question_id);
                $question->update($questionData);
                $question->questionChoices()->delete();
            } else {
                $question = $exam->questions()->create($questionData);
            }

            foreach ($request->answers as $answer) {
                $question->questionChoices()->create([
                    'name' => $answer['name'],
                    'is_correct' => $answer['is_correct'],
                    'explanation' => $answer['explanation'] ?? null
                ]);
            }

            return response()->json([
                'message' => 'Câu hỏi đã được lưu',
                'question' => $question->load('questionChoices')
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi lưu câu hỏi: ' . $e->getMessage(), ['exam_id' => $exam->id, 'data' => $request->all()]);
            return response()->json(['message' => 'Lỗi khi lưu câu hỏi'], 500);
        }
    }

    public function update(Request $request, Exam $exam)
    {
        try {
            $data = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'title' => 'required|string|max:255',
                'duration_minutes' => 'required|integer|min:1',
                'total_questions' => 'required|integer|min:1',
            ]);

            $exam->update($data);
            $type = $exam->subject->type ?? null;

            $route = $type === 'nang_luc'
                ? 'admin.exams.nangluc'
                : ($type === 'tu_duy'
                    ? 'admin.exams.tuduy'
                    : 'admin.exams.index');

            return redirect()->route($route)->with('success', 'Cập nhật đề thi thành công!');
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Lỗi khi cập nhật đề thi', ['exam_id' => $exam->id, 'data' => $request->all()]);
        }
    }

    public function destroy(Exam $exam)
    {
        try {
            $type = $exam->subject->type ?? null;
            $exam->delete();

            $route = $type === 'nang_luc'
                ? 'admin.exams.nangluc'
                : ($type === 'tu_duy'
                    ? 'admin.exams.tuduy'
                    : 'admin.exams.index');

            return redirect()->route($route)->with('success', 'Xoá đề thi thành công!');
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Lỗi khi xoá đề thi', ['exam_id' => $exam->id]);
        }
    }

    public function batchUpdateQuestions(Request $request, Exam $exam)
    {
        try {
            $filtered = collect($request->input('questions', []))
                ->filter(fn($q) => !empty($q['question']))
                ->values()
                ->toArray();

            $request->merge(['questions' => $filtered]);

            $request->validate([
                'duration_minutes' => 'required|integer|min:1',
                'total_questions' => 'required|integer|min:1',
                'questions' => 'required|array|min:1',
                'questions.*.question' => 'required|string|max:1000',
                'questions.*.loai' => [
                    'required',
                    'string',
                    \Illuminate\Validation\Rule::in([
                        Question::LOAI_NHAN_BIET,
                        Question::LOAI_THONG_HIEU,
                        Question::LOAI_VAN_DUNG,
                        Question::LOAI_PHAN_TICH,
                        Question::LOAI_TONG_HOP,
                        Question::LOAI_DANH_GIA,
                    ]),
                ],
                'questions.*.answers' => 'required|array|min:1',
                'questions.*.answers.*.name' => 'required_with:questions.*.answers|string|max:500',
                'questions.*.answers.*.explanation' => 'nullable|string|max:1000',
                'questions.*.answers.*.is_correct' => 'boolean',
            ]);

            $exam->update([
                'duration_minutes' => $request->duration_minutes,
                'total_questions' => $request->total_questions
            ]);

            $questionIds = [];
            $createdCount = 0;

            foreach ($request->questions as $data) {
                $question = Question::updateOrCreate(
                    ['id' => $data['id'] ?? null],
                    [
                        'question' => $data['question'],
                        'loai' => $data['loai'],
                        'is_active' => $data['is_active'] ?? 1, 
                    ]
                );

                if (!$data['id']) {
                    $createdCount++; 
                }

                $questionIds[] = $question->id;

                if (isset($data['answers']) && is_array($data['answers']) && !empty($data['answers'])) {
                    $question->questionChoices()->delete();

                    foreach ($data['answers'] as $answerData) {
                        if (!empty($answerData['name'])) {
                            $question->questionChoices()->create([
                                'name' => $answerData['name'],
                                'explanation' => $answerData['explanation'] ?? '',
                                'is_correct' => $answerData['is_correct'] ?? false,
                            ]);
                        }
                    }
                } else {
                    $question->questionChoices()->delete();
                }
            }

            $exam->questions()->sync($questionIds);

            return response()->json([
                'message' => 'Đã lưu thay đổi thành công!',
                'created_count' => $createdCount,
                'redirect' => route('admin.exams.show', $exam)
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi batch update câu hỏi: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
                'input' => $request->all(),
            ]);
            return response()->json(['message' => 'Lỗi khi lưu thay đổi: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new \App\Exports\ExamQuestionsExport(null, true), 'template_cau_hoi.xlsx');
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Lỗi khi tải file mẫu');
        }
    }

    public function importQuestions(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
                'import_type' => 'required|in:append,replace'
            ]);

            $importService = new QuestionImportService(
                $exam,
                $request->file('file'),
                $request->import_type
            );

            // Validate số lượng trước khi import
            $importService->validateImport();

            // Thực hiện import
            $result = $importService->import();
            
            // Tạo thông báo chi tiết
            $stats = $result['stats'];
            $message = "Đã import thành công {$result['importedCount']} câu hỏi. ";
            
            if ($request->import_type === 'append') {
                $message .= "(Hiện có {$stats['currentCount']}/{$stats['maxCount']} câu, ";
                $message .= "còn trống {$stats['remainingSlots']} chỗ)";
            }

            return response()->json([
                'message' => $message,
                'redirect' => route('admin.exams.show', $exam)
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi import câu hỏi: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
                'file' => $request->file('file')?->getClientOriginalName()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
