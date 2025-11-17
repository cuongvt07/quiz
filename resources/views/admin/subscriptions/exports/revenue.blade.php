<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
            <td colspan="6" style="font-weight:bold; font-size:12pt; text-align:center;">BÁO CÁO TỔNG HỢP DOANH THU THEO GÓI DỊCH VỤ THÁNG {{ $month }}/{{ $year }}</td>
        </tr>
        <tr><td colspan="6"></td></tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th>STT</th>
                <th>ID gói dịch vụ</th>
                <th>Tên gói dịch vụ</th>
                <th>Giá bán (VND)</th>
                <th>Số lượng đăng ký</th>
                <th>Doanh thu (VND)</th>
            </tr>
        </thead>
        <tbody>
            @php $index = 1; $totalRevenue = 0; @endphp
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $index++ }}</td>
                    <td>{{ $row['plan_id'] }}</td>
                    <td>{{ $row['plan_name'] }}</td>
                    <td>{{ number_format($row['price']) }}</td>
                    <td>{{ $row['count'] }}</td>
                    <td>{{ number_format($row['revenue']) }}</td>
                </tr>
                @php $totalRevenue += $row['revenue']; @endphp
            @endforeach
            <tr>
                <td colspan="5" style="font-weight:bold; text-align:left;">Tổng doanh thu</td>
                <td style="font-weight:bold;">{{ number_format($totalRevenue) }}</td>
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
