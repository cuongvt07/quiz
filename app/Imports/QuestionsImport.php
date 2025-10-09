<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\QuestionChoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation, WithStartRow, SkipsEmptyRows, SkipsOnFailure
{
    protected $exam;
    protected $replaceExisting;
    protected $importedCount = 0;

    public function __construct($exam, $replaceExisting = false)
    {
        $this->exam = $exam;
        $this->replaceExisting = $replaceExisting;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        // 🔹 Bỏ qua dòng không có nội dung câu hỏi hoặc loại câu hỏi
        if (empty($row['noi_dung_cau_hoi']) || empty($row['loai'])) {
            return null;
        }

        try {
            $loai = trim($row['loai']);

            // 🔹 Kiểm tra loại câu hỏi hợp lệ
            if (!in_array($loai, [
                Question::LOAI_NHAN_BIET,
                Question::LOAI_THONG_HIEU,
                Question::LOAI_VAN_DUNG,
                Question::LOAI_PHAN_TICH,
                Question::LOAI_TONG_HOP,
                Question::LOAI_DANH_GIA,
            ])) {
                Log::warning("Loại câu hỏi không hợp lệ, bỏ qua dòng này", ['loai' => $loai, 'row' => $row]);
                return null;
            }

            // 🔹 Dọn dữ liệu đúng/sai
            foreach (['a', 'b', 'c', 'd'] as $label) {
                $key = "dap_an_dung_$label";
                $row[$key] = isset($row[$key]) && (string) $row[$key] === '1' ? 1 : 0;
            }

            // 🔹 Nếu replaceExisting thì xóa câu hỏi cũ có cùng nội dung và loại
            if ($this->replaceExisting) {
                $existingQuestion = Question::where('question', $row['noi_dung_cau_hoi'])
                    ->where('loai', $loai)
                    ->first();
                    
                if ($existingQuestion) {
                    $existingQuestion->questionChoices()->delete();
                    $existingQuestion->delete();
                }
            }

            // 🔹 Tạo hoặc cập nhật câu hỏi
            if ($this->replaceExisting) {
                // Replace mode: updateOrCreate để cập nhật nếu đã tồn tại
                $question = Question::updateOrCreate(
                    [
                        'question' => $row['noi_dung_cau_hoi'],
                        'loai' => $loai,
                    ],
                    [
                        'is_active' => isset($row['hien_thi_cau_hoi']) && (string) $row['hien_thi_cau_hoi'] === '1',
                    ]
                );
            } else {
                // Append mode: create để luôn tạo mới
                $question = Question::create([
                    'question' => $row['noi_dung_cau_hoi'],
                    'loai' => $loai,
                    'is_active' => isset($row['hien_thi_cau_hoi']) && (string) $row['hien_thi_cau_hoi'] === '1',
                ]);
            }

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
            
            Log::info("Import thành công câu hỏi", [
                'id' => $question->id,
                'loai' => $loai,
                'question' => $row['noi_dung_cau_hoi']
            ]);

            return $question;
        } catch (\Throwable $e) {
            Log::error("Import error: " . $e->getMessage(), ['row' => $row]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'loai' => ['required', 'string', Rule::in([
                Question::LOAI_NHAN_BIET,
                Question::LOAI_THONG_HIEU,
                Question::LOAI_VAN_DUNG,
                Question::LOAI_PHAN_TICH,
                Question::LOAI_TONG_HOP,
                Question::LOAI_DANH_GIA,
            ])],
            'noi_dung_cau_hoi' => 'required|string',
            'dap_an_a' => 'required',
            'dap_an_b' => 'required',
            'dap_an_c' => 'required',
            'dap_an_d' => 'required',
            'dap_an_dung_a' => 'required|in:0,1',
            'dap_an_dung_b' => 'required|in:0,1',
            'dap_an_dung_c' => 'required|in:0,1',
            'dap_an_dung_d' => 'required|in:0,1',
            'giai_thich_a' => 'nullable|string',
            'giai_thich_b' => 'nullable|string',
            'giai_thich_c' => 'nullable|string',
            'giai_thich_d' => 'nullable|string',
            'hien_thi_cau_hoi' => 'required|in:0,1',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'loai.required' => 'Loại câu hỏi là bắt buộc',
            'loai.in' => 'Loại câu hỏi phải là một trong các giá trị: nhan_biet, thong_hieu, van_dung, phan_tich, tong_hop, danh_gia',
            'noi_dung_cau_hoi.required' => 'Nội dung câu hỏi là bắt buộc',
            'dap_an_a.required' => 'Đáp án A là bắt buộc',
            'dap_an_b.required' => 'Đáp án B là bắt buộc',
            'dap_an_c.required' => 'Đáp án C là bắt buộc',
            'dap_an_d.required' => 'Đáp án D là bắt buộc',
            'dap_an_dung_*.required' => 'Phải chọn đúng/sai cho tất cả đáp án',
            'dap_an_dung_*.in' => 'Giá trị đúng/sai phải là 0 hoặc 1',
            'hien_thi_cau_hoi.required' => 'Trạng thái hiển thị là bắt buộc',
            'hien_thi_cau_hoi.in' => 'Trạng thái hiển thị phải là 0 hoặc 1',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        // Xử lý các dòng lỗi validation - chỉ log, không throw
        foreach ($failures as $failure) {
            Log::warning('Import validation failed', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }
}