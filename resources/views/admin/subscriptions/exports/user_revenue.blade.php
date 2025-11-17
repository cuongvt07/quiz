<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        /* Basic table styles for export */
        table { border-collapse: collapse; width: 100%; }
        table th, table td { padding: 6px; vertical-align: top; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="6" style="font-weight:bold; font-size:14pt; text-align:center;">CÔNG TY CỔ PHẦN ĐẦU TƯ VÀ PHÁT TRIỂN GIÁO DỤC HSA</td>
        </tr>
        <tr>
            <td colspan="6" style="text-align:center;">Hotline: 0988.371.194 - Email: hsaeducation.jsc@gmail.com</td>
        </tr>
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="font-weight:bold; font-size:12pt; text-align:center;">BÁO CÁO TỔNG HỢP DOANH THU THEO NGƯỜI DÙNG THÁNG {{ $month }}/{{ $year }}</td>
        </tr>
        <tr><td colspan="6"></td></tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th>STT</th>
                <th>ID học sinh</th>
                <th>Học sinh</th>
                <th>Số gói đăng ký</th>
                <th>Số lượt thi</th>
                <th>Doanh thu (VND)</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; $total = 0; @endphp
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $row['user_id'] }}</td>
                    <td>{{ $row['user_name'] }}</td>
                    <td>{{ $row['subscriptions_count'] }}</td>
                    <td>{{ $row['total_attempts'] }}</td>
                    <td>{{ number_format($row['revenue']) }}</td>
                </tr>
                @php $total += $row['revenue']; @endphp
            @endforeach
            <tr>
                <td colspan="5" style="font-weight:bold; text-align:left;">Tổng doanh thu</td>
                <td style="font-weight:bold;">{{ number_format($total) }}</td>
            </tr>
        </tbody>
    </table>

    <br/><br/>
    <table>
        <tr>
            <td colspan="5"></td>
            <td style="text-align:center;">Hà Nội, ngày {{ now()->format('d') }} tháng {{ $month }} năm {{ $year }}</td>
        </tr>
        <tr><td colspan="5"></td><td></td></tr>
        <tr>
            <td colspan="5"></td>
            <td style="text-align:center;">Nhân viên</td>
        </tr>
        <tr><td colspan="5"></td><td></td></tr>
        <tr>
            <td colspan="5"></td>
            <td style="text-align:center;">Nguyễn Huỳnh Anh</td>
        </tr>
    </table>
</body>
</html>
