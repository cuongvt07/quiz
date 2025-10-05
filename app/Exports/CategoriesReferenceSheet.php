<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoriesReferenceSheet implements WithTitle, WithHeadings, FromCollection, WithStyles
{
    public function title(): string
    {
        return 'Danh mục';
    }

    public function headings(): array
    {
        return [
            ['DANH SÁCH DANH MỤC THAM KHẢO'],
            [''],
            ['Category ID', 'Tên danh mục', 'Mô tả'],
        ];
    }

    public function collection()
    {
        $categories = Category::select('id', 'name', 'description')->get();
        return $categories->map(function ($category) {
            return [
                $category->id,
                $category->name,
                $category->description ?? ''
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}