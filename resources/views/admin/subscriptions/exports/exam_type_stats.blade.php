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
            <td colspan="3" style="font-weight:bold; font-size:14pt; text-align:center;">CÔNG TY CỔ PHẦN ĐẦU TƯ VÀ PHÁT TRIỂN GIÁO DỤC HSA</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:center;">Hotline: 0988.371.194 - Email: hsaeducation.jsc@gmail.com</td>
        </tr>
        <tr><td colspan="3"></td></tr>
        <tr>
            <td colspan="3" style="font-weight:bold; font-size:12pt; text-align:center;">BÁO CÁO THỐNG KÊ THEO LOẠI BÀI THI THÁNG {{ $month }}/{{ $year }}</td>
        </tr>
        <tr><td colspan="3"></td></tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th>STT</th>
                <th>Loại bài thi</th>
                <th>Số lượt thi</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; $total = 0; @endphp
            @foreach($rows as $row)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $row['type_name'] }}</td>
                    <td>{{ $row['count'] }}</td>
                </tr>
                @php $total += $row['count']; @endphp
            @endforeach
            <tr>
                <td colspan="2" style="font-weight:bold; text-align:left;">Tổng lượt thi</td>
                <td style="font-weight:bold;">{{ $total }}</td>
            </tr>
        </tbody>
    </table>

    <br/><br/>
    <table>
        <tr>
            <td colspan="2"></td>
            <td style="text-align:center;">Hà Nội, ngày {{ now()->format('d') }} tháng {{ $month }} năm {{ $year }}</td>
        </tr>
        <tr><td colspan="2"></td><td></td></tr>
        <tr>
            <td colspan="2"></td>
            <td style="text-align:center;">Nhân viên</td>
        </tr>
        <tr><td colspan="2"></td><td></td></tr>
        <tr>
            <td colspan="2"></td>
            <td style="text-align:center;">Nguyễn Huỳnh Anh</td>
        </tr>
    </table>
</body>
</html>
