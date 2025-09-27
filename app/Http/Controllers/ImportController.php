<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;

class ImportController extends Controller
{
    // Hiển thị form import
    public function showImportForm()
    {
        return view('admin.import.form');
    }

    // Xử lý import file Excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new QuestionsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Import thành công!');
    }
}
