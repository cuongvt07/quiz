<?php

namespace App\Services;

use App\Models\Exam;
use App\Imports\QuestionsImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class QuestionImportService
{
    protected $exam;
    protected $file;
    protected $importType;

    public function __construct(Exam $exam, $file, $importType)
    {
        $this->exam = $exam;
        $this->file = $file;
        $this->importType = $importType;
    }

    public function validateImport()
    {
        // Đọc số lượng câu hỏi từ file Excel (bỏ qua header và dòng trống)
        $excelQuestions = Excel::toCollection(new QuestionsImport($this->exam, false), $this->file)
            ->first()
            ->filter(function ($row) {
                return !empty($row['noi_dung_cau_hoi']);
            });

        $excelQuestionCount = $excelQuestions->count();
        $currentQuestionCount = $this->exam->questions()->count();
        $maxQuestions = $this->exam->total_questions;

        // Nếu là thêm mới, kiểm tra số lượng
        if ($this->importType === 'append') {
            $remainingSlots = $maxQuestions - $currentQuestionCount;
            
            if ($remainingSlots <= 0) {
                throw new \Exception("Đã đạt giới hạn số câu hỏi ({$maxQuestions} câu). Không thể thêm mới.");
            }

            if ($excelQuestionCount > $remainingSlots) {
                throw new \Exception(
                    "File chứa {$excelQuestionCount} câu hỏi nhưng chỉ còn trống {$remainingSlots} chỗ. " .
                    "Đang có {$currentQuestionCount}/{$maxQuestions} câu."
                );
            }
        }

        return [
            'currentCount' => $currentQuestionCount,
            'maxCount' => $maxQuestions,
            'excelCount' => $excelQuestionCount,
            'remainingSlots' => $maxQuestions - $currentQuestionCount
        ];
    }

    public function import()
    {
        // Validate trước khi import
        $stats = $this->validateImport();
        
        // Thực hiện import
        $import = new QuestionsImport($this->exam, $this->importType === 'replace');
        Excel::import($import, $this->file);

        return [
            'importedCount' => $import->getImportedCount(),
            'stats' => $stats
        ];
    }
}