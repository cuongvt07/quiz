<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class SubscriptionRevenueExport implements FromView, WithTitle
{
    protected $rows;
    protected $month;
    protected $year;

    public function __construct(array $rows, int $month, int $year)
    {
        $this->rows = $rows;
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        return view('admin.subscriptions.exports.revenue', [
            'rows' => $this->rows,
            'month' => $this->month,
            'year' => $this->year,
        ]);
    }

    public function title(): string
    {
        return 'Doanh thu';
    }
}
