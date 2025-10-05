<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{
    FromArray, WithTitle, WithHeadings, WithStyles, WithColumnFormatting, WithCustomStartCell
};
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\Category;

class ExamQuestionsSheet implements WithTitle, WithHeadings, WithStyles, FromArray, WithColumnFormatting, WithCustomStartCell
{
    protected $exam;
    protected $createTemplate;
    protected $categories;

    public function __construct($exam = null, $createTemplate = false)
    {
        $this->exam = $exam;
        $this->createTemplate = $createTemplate;
        $this->categories = Category::select('id', 'name')->get();
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function title(): string
    {
        return 'Câu hỏi';
    }

    public function headings(): array
    {
        return [[
            'danh_muc',
            'noi_dung_cau_hoi',
            'dap_an_a',
            'dap_an_b',
            'dap_an_c',
            'dap_an_d',
            'dap_an_dung_a',
            'dap_an_dung_b',
            'dap_an_dung_c',
            'dap_an_dung_d',
            'giai_thich_a',
            'giai_thich_b',
            'giai_thich_c',
            'giai_thich_d',
            'hien_thi_cau_hoi',
            'category_id',
        ]];
    }

    public function columnFormats(): array
    {
        $formats = [];
        for ($col = 'A'; $col <= 'P'; $col++) {
            $formats[$col] = NumberFormat::FORMAT_TEXT;
        }
        return $formats;
    }

    public function array(): array
    {
        if ($this->createTemplate) {
            return [
                [
                    'Dễ',
                    'Câu hỏi mẫu: 2 + 2 = ?',
                    '2', '3', '4', '5',
                    '0', '0', '1', '0',
                    'Sai', 'Sai', 'Đúng', '',
                    '1',
                    '=IF(A2="Dễ",1,IF(A2="Trung bình",2,IF(A2="Khá",3,IF(A2="Nâng cao",4,""))))',
                ],
                [
                    'Trung bình',
                    'Thủ đô Việt Nam là?',
                    'Hà Nội', 'TP.HCM', 'Huế', 'Đà Nẵng',
                    '1', '0', '0', '0',
                    'Đúng', 'Sai', 'Sai', 'Sai',
                    '1',
                    '=IF(A3="Dễ",1,IF(A3="Trung bình",2,IF(A3="Khá",3,IF(A3="Nâng cao",4,""))))',
                ]
            ];
        }

        // Nếu export từ exam
        if ($this->exam) {
            $rows = [];
            foreach ($this->exam->questions as $question) {
                $choices = $question->questionChoices->sortBy('id')->values();
                $category = $this->categories->firstWhere('id', $question->category_id);
                $categoryName = $category ? $category->name : '';

                $row = [
                    $categoryName,
                    $question->question,
                ];

                // Đáp án A-D
                for ($i = 0; $i < 4; $i++) {
                    $row[] = $choices[$i]->name ?? '';
                }
                // Đúng/Sai
                for ($i = 0; $i < 4; $i++) {
                    $row[] = isset($choices[$i]) && $choices[$i]->is_correct ? '1' : '0';
                }
                // Giải thích
                for ($i = 0; $i < 4; $i++) {
                    $row[] = $choices[$i]->explanation ?? '';
                }

                $row[] = $question->is_active ? '1' : '0';
                $row[] = $question->category_id ?? '';

                $rows[] = $row;
            }
            return $rows;
        }

        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = max($sheet->getHighestDataRow(), 10);

        // 🔽 Dropdown danh mục
        $categoryNames = $this->categories->pluck('name')->implode(',');
        for ($row = 2; $row <= $highestRow + 10; $row++) {
            $validation = $sheet->getCell("A{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $categoryNames . '"');
        }

        // 🔽 Dropdown 1/0 cho G-J, O
        foreach (['G', 'H', 'I', 'J', 'O'] as $col) {
            for ($row = 2; $row <= $highestRow + 10; $row++) {
                $validation = $sheet->getCell("{$col}{$row}")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"1,0"');
            }
        }

        // 🧮 Công thức tự động điền category_id
        for ($row = 2; $row <= $highestRow + 10; $row++) {
            $sheet->setCellValue("P{$row}", '=IF(A'.$row.'="Dễ",1,IF(A'.$row.'="Trung bình",2,IF(A'.$row.'="Khá",3,IF(A'.$row.'="Nâng cao",4,""))))');
        }

        // 🗂️ Bảng danh mục phụ ở bên cạnh (từ cột R)
        $sheet->setCellValue('R1', 'BẢNG DANH MỤC');
        $sheet->setCellValue('R2', 'Tên danh mục');
        $sheet->setCellValue('S2', 'ID');

        $rowIndex = 3;
        foreach ($this->categories as $cat) {
            $sheet->setCellValue("R{$rowIndex}", $cat->name);
            $sheet->setCellValue("S{$rowIndex}", $cat->id);
            $rowIndex++;
        }

        // Style tiêu đề
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D9EAD3']],
            ],
            'R1' => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '6FA8DC']],
            ],
            'R2' => ['font' => ['bold' => true]],
            'S2' => ['font' => ['bold' => true]],
        ];
    }
}
