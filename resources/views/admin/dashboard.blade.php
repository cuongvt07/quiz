@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-5 flex items-center gap-2"><i data-feather="bar-chart-2"></i> Thống kê hệ thống</h1>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-100 to-blue-200 p-4 rounded-xl shadow text-center flex flex-col items-center">
        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-500 mb-2"><i data-feather="users" class="text-white"></i></div>
        <div class="text-2xl font-bold text-blue-700 counter" data-count="{{ $users ?? 120 }}">0</div>
        <div class="text-gray-700 text-xs mt-1">Tổng số người dùng</div>
    </div>
    <div class="bg-gradient-to-br from-green-100 to-green-200 p-4 rounded-xl shadow text-center flex flex-col items-center">
        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-green-500 mb-2"><i data-feather="file-text" class="text-white"></i></div>
        <div class="text-2xl font-bold text-green-700 counter" data-count="{{ $exams ?? 15 }}">0</div>
        <div class="text-gray-700 text-xs mt-1">Tổng số đề thi</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 p-4 rounded-xl shadow text-center flex flex-col items-center">
        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-yellow-500 mb-2"><i data-feather="help-circle" class="text-white"></i></div>
        <div class="text-2xl font-bold text-yellow-700 counter" data-count="{{ $questions ?? 350 }}">0</div>
        <div class="text-gray-700 text-xs mt-1">Tổng số câu hỏi</div>
    </div>
    <div class="bg-gradient-to-br from-purple-100 to-purple-200 p-4 rounded-xl shadow text-center flex flex-col items-center">
        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-purple-500 mb-2"><i data-feather="activity" class="text-white"></i></div>
        <div class="text-2xl font-bold text-purple-700 counter" data-count="{{ $attempts ?? 200 }}">0</div>
        <div class="text-gray-700 text-xs mt-1">Lượt thi</div>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="text-base font-semibold mb-2 flex items-center gap-2"><i data-feather="calendar"></i> Lượt thi theo tháng</h2>
        <canvas id="attemptsByMonthChart" height="80"></canvas>
    </div>
    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="text-base font-semibold mb-2 flex items-center gap-2"><i data-feather="file-text"></i> Lượt thi theo đề thi</h2>
        <canvas id="attemptsByExamChart" height="80"></canvas>
    </div>
</div>
<div class="bg-white p-4 rounded-xl shadow mb-4">
    <h2 class="text-base font-semibold mb-2 flex items-center gap-2"><i data-feather="pie-chart"></i> Lượt thi theo loại (năng lực/tư duy)</h2>
    <canvas id="attemptsByTypeChart" height="80"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Counter animation
    document.querySelectorAll('.counter').forEach(function(el) {
        const count = +el.getAttribute('data-count');
        let n = 0;
        const step = Math.ceil(count / 40);
        const interval = setInterval(() => {
            n += step;
            if (n >= count) {
                el.textContent = count;
                clearInterval(interval);
            } else {
                el.textContent = n;
            }
        }, 20);
    });
    // Dữ liệu mẫu lượt thi theo tháng
    const attemptsByMonthChart = document.getElementById('attemptsByMonthChart').getContext('2d');
    new Chart(attemptsByMonthChart, {
        type: 'bar',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9'],
            datasets: [{
                label: 'Lượt thi',
                data: [12, 19, 8, 15, 22, 30, 25, 18, 20],
                backgroundColor: 'rgba(59, 130, 246, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
    // Dữ liệu mẫu lượt thi theo đề thi
    const attemptsByExamChart = document.getElementById('attemptsByExamChart').getContext('2d');
    new Chart(attemptsByExamChart, {
        type: 'bar',
        data: {
            labels: ['Đề 1', 'Đề 2', 'Đề 3', 'Đề 4', 'Đề 5'],
            datasets: [{
                label: 'Lượt thi',
                data: [40, 32, 25, 18, 10],
                backgroundColor: 'rgba(16, 185, 129, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
    // Dữ liệu mẫu lượt thi theo loại (năng lực/tư duy)
    const attemptsByTypeChart = document.getElementById('attemptsByTypeChart').getContext('2d');
    new Chart(attemptsByTypeChart, {
        type: 'pie',
        data: {
            labels: ['Năng lực', 'Tư duy'],
            datasets: [{
                data: [120, 80],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(245, 158, 11, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endpush