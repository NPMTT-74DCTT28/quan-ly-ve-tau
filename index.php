<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/modules/thong-ke/ThongKe.php';

$thongKe = new ThongKe($db->getConnection());
$doanhThu7Ngay = $thongKe->getDoanhThu7Ngay();

// Chuẩn bị dữ liệu cho Chart.js
$labels = [];
$data = [];

foreach ($doanhThu7Ngay as $item) {
    // Format ngày sang dd/mm cho đẹp
    $labels[] = date('d/m', strtotime($item['ngay']));
    $data[] = $item['doanh_thu'];
}
?>

<div class="container">
    <h1>Trang chủ - Tổng quan hệ thống</h1>

    <?php if (isAdmin()): ?>
        <div class="chart-container" style="width: 80%; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; margin-bottom: 20px;">Xu hướng doanh thu 7 ngày gần nhất</h2>
            <canvas id="revenueChart"></canvas>
        </div>
    <?php else: ?>
        <p>Chào mừng bạn đến với hệ thống bán vé tàu.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    <?php if (isAdmin()): ?>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line', // Loại biểu đồ: đường
            data: {
                // Dữ liệu từ PHP in vào đây
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?php echo json_encode($data); ?>,
                    borderColor: '#4a6fa5',
                    backgroundColor: 'rgba(74, 111, 165, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3 // Làm mềm đường cong
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                // Format tiền tệ VNĐ
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });
    <?php endif; ?>
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>