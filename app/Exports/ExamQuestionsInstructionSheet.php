<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExamQuestionsInstructionSheet implements WithTitle, WithHeadings, WithStyles
{
    public function title(): string
    {
        return 'Hướng dẫn';
    }

    public function headings(): array
    {
        return [
            ['HƯỚNG DẪN IMPORT CÂU HỎI'],
            [''],
            ['1. File mẫu này gồm 3 sheet:'],
            ['   - Sheet "Hướng dẫn": Các thông tin hướng dẫn import'],
            ['   - Sheet "Danh mục": Danh sách ID và tên các danh mục để tham chiếu'],
            ['   - Sheet "Câu hỏi": Nơi nhập liệu các câu hỏi'],
            [''],
            ['2. Quy định nhập liệu:'],
            ['   - Cột "Category ID" và "Nội dung câu hỏi" là bắt buộc'],
            ['   - Nếu là câu hỏi 1 đáp án, chỉ cần điền "Đáp án A"'],
            ['   - Nếu là câu hỏi 4 đáp án, phải điền đủ từ A đến D'],
            ['   - Giải thích cho mỗi đáp án là không bắt buộc'],
            ['   - Cột "Trạng thái" nhận giá trị 1 (hiện) hoặc 0 (ẩn), mặc định là 1'],
            [''],
            ['3. Lưu ý:'],
            ['   - Không thay đổi cấu trúc của file mẫu'],
            ['   - Có thể xem sheet "Danh mục" để lấy Category ID chính xác'],
            ['   - Mỗi câu hỏi phải có ít nhất 1 đáp án đúng'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            'A3:A5' => ['font' => ['bold' => true]],
            'A8' => ['font' => ['bold' => true]],
            'A15' => ['font' => ['bold' => true]],
        ];
    }
}