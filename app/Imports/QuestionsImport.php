<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Category;
use App\Models\QuestionChoice;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation, WithStartRow, SkipsEmptyRows
{
    protected $exam;
    protected $replaceExisting;
    protected $categories;
    protected $importedCount = 0;

    public function __construct($exam, $replaceExisting = false)
    {
        $this->exam = $exam;
        $this->replaceExisting = $replaceExisting;
        $this->categories = Category::pluck('id', 'name'); // key = tên, value = id
    }

    public function startRow(): int
    {
        return 2; // dòng đầu tiên chứa tiêu đề
    }

    protected function logDebug($message, $data = [])
    {
        Log::debug("Import Debug: " . $message, $data);
    }

    public function model(array $row)
    {
        if (empty($row['noi_dung_cau_hoi'])) {
            return null;
        }

        try {
            // 🔹 Xác định category_id
            $categoryId = null;
            if (!empty($row['category_id'])) {
                $categoryId = (int) $row['category_id'];
            } else {
                $name = trim($row['danh_muc'] ?? '');
                $categoryId = $this->categories[$name] ?? null;
                if (!$categoryId) {
                    throw new \Exception("Không tìm thấy danh mục: {$name}");
                }
            }

            // 🔹 Dọn dữ liệu đúng/sai
            foreach (['a', 'b', 'c', 'd'] as $label) {
                $key = "dap_an_dung_$label";
                $row[$key] = isset($row[$key]) && (string) $row[$key] === '1' ? 1 : 0;
            }

            // 🔹 Nếu replaceExisting thì xóa cũ
            if ($this->replaceExisting) {
                Question::where('question', $row['noi_dung_cau_hoi'])
                    ->where('category_id', $categoryId)
                    ->each(function ($q) {
                        $q->questionChoices()->delete();
                        $q->delete();
                    });
            }

            // 🔹 Tạo hoặc cập nhật câu hỏi
            $question = Question::updateOrCreate(
                [
                    'question' => $row['noi_dung_cau_hoi'],
                    'category_id' => $categoryId,
                ],
                [
                    'is_active' => (string) $row['hien_thi_cau_hoi'] === '1',
                ]
            );

            // 🔹 Xóa đáp án cũ
            $question->questionChoices()->delete();

            // 🔹 Tạo đáp án
            foreach (['a', 'b', 'c', 'd'] as $label) {
                $answerKey = "dap_an_$label";
                if (!empty($row[$answerKey])) {
                    QuestionChoice::create([
                        'question_id' => $question->id,
                        'name' => $row[$answerKey],
                        'is_correct' => $row["dap_an_dung_$label"] ?? 0,
                        'explanation' => $row["giai_thich_$label"] ?? null,
                    ]);
                }
            }

            // 🔹 Gắn vào đề thi
            $question->exams()->syncWithoutDetaching([$this->exam->id]);

            $this->importedCount++;
            return $question;
        } catch (\Throwable $e) {
            Log::error("Import error: " . $e->getMessage(), ['row' => $row]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'danh_muc' => 'required_without:category_id',
            'category_id' => 'required_without:danh_muc',
            'noi_dung_cau_hoi' => 'required',
            'dap_an_a' => 'required',
            'dap_an_dung_a' => 'nullable|numeric|in:0,1',
            'dap_an_dung_b' => 'nullable|numeric|in:0,1',
            'dap_an_dung_c' => 'nullable|numeric|in:0,1',
            'dap_an_dung_d' => 'nullable|numeric|in:0,1',
            'hien_thi_cau_hoi' => 'nullable|numeric|in:0,1',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'noi_dung_cau_hoi.required' => 'Câu hỏi là bắt buộc',
            'danh_muc.required_without' => 'Phải nhập danh mục hoặc Category ID',
            'category_id.required_without' => 'Phải nhập Category ID hoặc tên danh mục',
            'dap_an_a.required' => 'Phải có ít nhất một đáp án',
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }
}
