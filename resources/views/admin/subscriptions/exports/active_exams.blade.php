<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; }
        table th, table td { padding: 6px; vertical-align: top; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="5" style="font-weight:bold; font-size:14pt; text-align:center;">CÔNG TY CỔ PHẦN ĐẦU TƯ VÀ PHÁT TRIỂN GIÁO DỤC HSA</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align:center;">Hotline: 0988.371.194 - Email: hsaeducation.jsc@gmail.com</td>
        </tr>
        <tr><td colspan="5"></td></tr>
        <tr>
            <td colspan="5" style="font-weight:bold; font-size:12pt; text-align:center;">DANH SÁCH BÀI THI ĐANG HOẠT ĐỘNG</td>
        </tr>
        @if(!empty($periodLabel))
            <tr>
                <td colspan="5" style="text-align:center;">Thời gian: {{ $periodLabel }}</td>
            </tr>
        @endif
        <tr><td colspan="5"></td></tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th>STT</th>
                <th>ID bài thi</th>
                <th>Tên bài thi</th>
                <th>Số câu hỏi</th>
                <th>Thời gian (phút)</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row['title'] }}</td>
                    <td>{{ $row['total_questions'] }}</td>
                    <td>{{ $row['duration_minutes'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br/><br/>
    <table>
        <tr>
            <td colspan="4"></td>
            <td style="text-align:center;">Hà Nội, ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</td>
        </tr>
        <tr><td colspan="4"></td><td></td></tr>
        <tr>
            <td colspan="4"></td>
            <td style="text-align:center;">Nhân viên</td>
        </tr>
        <tr><td colspan="4"></td><td></td></tr>
        <tr>
            <td colspan="4"></td>
            <td style="text-align:center;">Nguyễn Huỳnh Anh</td>
        </tr>
    </table>
</body>
</html>
