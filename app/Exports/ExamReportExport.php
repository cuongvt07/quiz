<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExamReportExport implements FromView, WithTitle
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function view(): View
    {
        return view('admin.reports.exams_report', [
            'rows' => $this->rows,
        ]);
    }

    public function title(): string
    {
        return 'Bao_cao_bai_thi'; // cố định → không bao giờ lỗi
    }
}
