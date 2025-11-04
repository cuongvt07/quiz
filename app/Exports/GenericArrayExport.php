<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericArrayExport implements FromArray, WithTitle, WithHeadings
{
    // Trả về header lấy từ keys của dòng đầu tiên (nếu có)
    public function headings(): array
    {
        if (!empty($this->data) && is_array($this->data[0])) {
            return array_keys($this->data[0]);
        }
        return [];
    }
    protected $data;
    protected $title;

    public function __construct(array $data, $title = 'Sheet1')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return $this->title;
    }
}
