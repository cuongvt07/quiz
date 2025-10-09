<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExamQuestionsExport implements WithMultipleSheets
{
    protected $exam;
    protected $createTemplate;

    public function __construct($exam = null, $createTemplate = false)
    {
        $this->exam = $exam;
        $this->createTemplate = $createTemplate;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new ExamQuestionsSheet($this->exam, $this->createTemplate);

        return $sheets;
    }
}