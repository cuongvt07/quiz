<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{
    FromArray, WithTitle, WithHeadings, WithStyles, WithColumnFormatting, WithCustomStartCell
};
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\Question;

class ExamQuestionsSheet implements WithTitle, WithHeadings, WithStyles, FromArray, WithColumnFormatting, WithCustomStartCell
{
    protected $exam;
    protected $createTemplate;

    public function __construct($exam = null, $createTemplate = false)
    {
        $this->exam = $exam;
        $this->createTemplate = $createTemplate;
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
            'loai',
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
                    'nhan_biet', // Loại câu hỏi: nhận biết
                    'Câu hỏi mẫu: 2 + 2 = ?',
                    '2', '3', '4', '5',
                    '0', '0', '1', '0',
                    'Sai', 'Sai', 'Đúng', '',
                    '1'
                ],
                [
                    'thong_hieu', // Loại câu hỏi: thông hiểu
                    'Thủ đô Việt Nam là?',
                    'Hà Nội', 'TP.HCM', 'Huế', 'Đà Nẵng',
                    '1', '0', '0', '0',
                    'Đúng', 'Sai', 'Sai', 'Sai',
                    '1'
                ]
            ];
        }

        // Nếu export từ exam
        if ($this->exam) {
            $rows = [];
            foreach ($this->exam->questions as $question) {
                $choices = $question->questionChoices->sortBy('id')->values();

                $row = [
                    $question->loai,
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

                $rows[] = $row;
            }
            return $rows;
        }

        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = max($sheet->getHighestDataRow(), 10);

        // 🔽 Dropdown loại câu hỏi
        $loaiList = implode(',', array_keys(Question::getDanhSachLoai()));
        for ($row = 2; $row <= $highestRow + 10; $row++) {
            $validation = $sheet->getCell("A{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $loaiList . '"');
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

        // 🗂️ Bảng loại câu hỏi ở bên cạnh (từ cột R)
        $sheet->setCellValue('R1', 'LOẠI CÂU HỎI');
        $sheet->setCellValue('R2', 'Mã');
        $sheet->setCellValue('S2', 'Tên hiển thị');

        $rowIndex = 3;
        foreach (Question::getDanhSachLoai() as $key => $value) {
            $sheet->setCellValue("R{$rowIndex}", $key);
            $sheet->setCellValue("S{$rowIndex}", $value);
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
