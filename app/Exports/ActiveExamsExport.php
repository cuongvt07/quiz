<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ActiveExamsExport implements FromView, WithTitle, WithEvents
{
    protected $rows;
    protected $periodLabel;

    public function __construct(array $rows, string $periodLabel = '')
    {
        $this->rows = $rows;
        $this->periodLabel = $periodLabel;
    }

    public function view(): View
    {
        return view('admin.subscriptions.exports.active_exams', [
            'rows' => $this->rows,
            'periodLabel' => $this->periodLabel,
        ]);
    }

    public function title(): string
    {
        return 'Danh sách đề thi';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Cần kiểm tra view để xác định số dòng header chính xác
                // Nếu có periodLabel: company(1), hotline(1), blank(1), title(1), period(1), blank(1) = 6
                // Nếu không: company(1), hotline(1), blank(1), title(1), blank(1) = 5
                $headerRows = !empty($this->periodLabel) ? 6 : 5;
                $dataStartRow = $headerRows + 1;
                $lastRow = $dataStartRow + count($this->rows);
                
                // Apply AutoFilter (5 columns: A-E)
                $event->sheet->setAutoFilter("A{$dataStartRow}:E{$lastRow}");
            },
        ];
    }
}
