<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UserExamSummaryExport implements FromView, WithTitle, WithEvents
{
    protected $rows;
    protected $userName;
    protected $periodLabel;

    public function __construct(array $rows, string $userName, string $periodLabel = '')
    {
        $this->rows = $rows;
        $this->userName = $userName;
        $this->periodLabel = $periodLabel;
    }

    public function view(): View
    {
        return view('admin.users.exports.exam_summary', [
            'rows' => $this->rows,
            'userName' => $this->userName,
            'periodLabel' => $this->periodLabel,
        ]);
    }

    public function title(): string
    {
        return 'Tổng hợp kết quả thi';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Header: công ty (1), hotline (1), blank (1), tiêu đề (1), tên user (1), period (0-1), blank (1)
                $headerRows = !empty($this->periodLabel) ? 7 : 6;
                $dataStartRow = $headerRows + 1;
                $lastRow = $dataStartRow + count($this->rows);
                
                // Apply AutoFilter cho bảng dữ liệu (5 cột: STT, Bài thi, Điểm, Thời gian, Số lần)
                $event->sheet->setAutoFilter("A{$dataStartRow}:E{$lastRow}");
            },
        ];
    }
}
