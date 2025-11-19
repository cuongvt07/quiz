<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; }
        table th, table td { padding: 6px; vertical-align: top; }
    </style>
    <title>BÁO CÁO TỔNG HỢP THEO BÀI THI</title>
</head>
<body>
    <table>
        <tr>
            <td colspan="8" style="font-weight:bold; font-size:14pt; text-align:center;">CÔNG TY CỔ PHẦN ĐẦU TƯ VÀ PHÁT TRIỂN GIÁO DỤC HSA</td>
        </tr>
        <tr>
            <td colspan="8" style="text-align:center;">Hotline: 0988.371.194 - Email: hsaeducation.jsc@gmail.com</td>
        </tr>
        <tr><td colspan="8"></td></tr>
        <tr>
            <td colspan="8" style="font-weight:bold; font-size:12pt; text-align:center;">BÁO CÁO TỔNG HỢP THEO BÀI THI</td>
        </tr>
        @if(!empty($periodLabel))
            <tr>
                <td colspan="8" style="text-align:center;">Thời gian: {{ $periodLabel }}</td>
            </tr>
        @endif
        <tr><td colspan="8"></td></tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th>Loại bài thi</th>
                <th>Môn học</th>
                <th>ID bài thi</th>
                <th>Tên bài thi</th>
                <th>Số thí sinh</th>
                <th>Tổng lượt thi</th>
                <th>Điểm trung bình</th>
                <th>Điểm cao nhất</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['type_name'] ?? '' }}</td>
                    <td>{{ $row['subject_name'] ?? '' }}</td>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row['title'] }}</td>
                    <td>{{ $row['students_count'] }}</td>
                    <td>{{ $row['total_attempts'] }}</td>
                    <td>{{ $row['avg_score'] }}</td>
                    <td>{{ $row['max_score'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br/><br/>
    <table>
        <tr>
            <td colspan="6"></td>
            <td colspan="2" style="text-align:center;">Hà Nội, ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</td>
        </tr>
        <tr>
            <td colspan="6"></td>
            <td colspan="2" style="text-align:center;">Nhân viên</td>
        </tr>
        <tr><td colspan="6"></td><td colspan="2"></td></tr>
        <tr>
            <td colspan="6"></td>
            <td colspan="2" style="text-align:center;">Nguyễn Huỳnh Anh</td>
        </tr>
    </table>
</body>
</html>
