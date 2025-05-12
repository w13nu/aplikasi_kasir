<?php include 'Sidebar.php'; ?>
<?php include_once 'helpers.php'; ?>
<?php include 'config.php'; ?>

<!-- Modern Dashboard -->
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">
            <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard
        </h1>
        <p class="dashboard-subtitle">Ringkasan performa bisnis Anda</p>
    </div>

    <!-- Stats Cards -->
    <div class="row stats-container g-4">
        <!-- Total Terjual -->
        <div class="col-md-6 col-lg-4">
            <div class="card stats-card shadow-sm">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-info">
                        <h6 class="stats-label">Total Terjual</h6>
                        <h3 class="stats-value">
                            <?php 
                            $result = mysqli_query($conn,"SELECT SUM(quantity) as jumlahterjual FROM tb_nota");
                            $row = mysqli_fetch_assoc($result);
                            echo number_format($row['jumlahterjual'] ?? 0, 0, ',', '.');
                            ?> 
                            <small>unit</small>
                        </h3>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Total seluruh periode</small>
                </div>
            </div>
        </div>

        <!-- Total Laba -->
        <div class="col-md-6 col-lg-4">
            <div class="card stats-card shadow-sm">
                <div class="card-body">
                    <div class="stats-icon bg-success text-white">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-info">
                        <h6 class="stats-label">Total Laba</h6>
                        <h3 class="stats-value">
                            <?php 
                            $query = mysqli_query($conn,"SELECT t.quantity, p.harga_modal, p.harga_jual FROM tb_nota t JOIN produk p ON t.idproduk=p.idproduk");
                            $laba = 0;
                            while ($r = mysqli_fetch_assoc($query)) {
                                $laba += ($r['harga_jual'] - $r['harga_modal']) * $r['quantity'];
                            }
                            echo 'Rp ' . ribuan($laba);
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Keuntungan bersih</small>
                </div>
            </div>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="col-md-6 col-lg-4">
            <div class="card stats-card shadow-sm">
                <div class="card-body">
                    <div class="stats-icon bg-warning text-dark">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="stats-info">
                        <h6 class="stats-label">Total Transaksi</h6>
                        <h3 class="stats-value">
                            <?php 
                            $result = mysqli_query($conn,"SELECT SUM(totalbeli) as jumlahtotal FROM laporan WHERE YEAR(tgl_sub) = YEAR(CURDATE())");
                            $row = mysqli_fetch_assoc($result);
                            echo 'Rp ' . ribuan($row['jumlahtotal'] ?? 0);
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Tahun <?= date('Y') ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="row mt-4 g-4">
        <!-- Grafik Penjualan per Bulan -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Grafik Penjualan dan Laba (<?= date('Y') ?>)</h5>
                        <div class="btn-group btn-group-sm chart-options" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-period="month">Bulanan</button>
                            <button type="button" class="btn btn-outline-primary" data-period="quarter">Kuartal</button>
                        </div>
                    </div>
                </div>
                <div class="card-body chart-container">
                    <canvas id="grafikPenjualan"></canvas>
                </div>
            </div>
        </div>

        <!-- Perbandingan Penjualan -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Perbandingan Bulan</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label for="bulan1" class="form-label">Bulan Pertama</label>
                            <select name="bulan1" id="bulan1" class="form-select form-select-sm" required>
                                <option value="">Pilih Bulan</option>
                                <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?= $i ?>" <?= (isset($_GET['bulan1']) && $_GET['bulan1'] == $i) ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="bulan2" class="form-label">Bulan Kedua</label>
                            <select name="bulan2" id="bulan2" class="form-select form-select-sm" required>
                                <option value="">Pilih Bulan</option>
                                <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?= $i ?>" <?= (isset($_GET['bulan2']) && $_GET['bulan2'] == $i) ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary btn-sm w-100" type="submit">
                                <i class="fas fa-chart-bar me-1"></i> Bandingkan
                            </button>
                        </div>
                    </form>

                    <div id="comparison-chart-container" class="<?= isset($_GET['bulan1']) && isset($_GET['bulan2']) ? '' : 'd-flex justify-content-center align-items-center' ?>" style="min-height: 250px;">
                        <?php if (!isset($_GET['bulan1']) || !isset($_GET['bulan2'])): ?>
                            <div class="text-center text-muted">
                                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                <p>Pilih dua bulan untuk membandingkan</p>
                            </div>
                        <?php else: ?>
                            <canvas id="perbandinganChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Table (Optional) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Produk Terlaris</h5>
                        <a href="produk.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Terjual</th>
                                    <th class="text-end">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = mysqli_query($conn, "SELECT p.nama_produk, SUM(t.quantity) as total_terjual, 
                                                            SUM(t.quantity * p.harga_jual) as total_nilai
                                                     FROM tb_nota t 
                                                     JOIN produk p ON t.idproduk = p.idproduk 
                                                     GROUP BY p.idproduk
                                                     ORDER BY total_terjual DESC
                                                     LIMIT 5");
                                                     
                                if (mysqli_num_rows($query) > 0) {
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        echo "<tr>";
                                        echo "<td>{$row['nama_produk']}</td>";
                                        echo "<td class='text-center'>{$row['total_terjual']}</td>";
                                        echo "<td class='text-end'>Rp " . ribuan($row['total_nilai']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center'>Belum ada data penjualan</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Include Custom CSS -->
<style>
    .dashboard-container {
        padding: 1.5rem;
    }
    
    .dashboard-header {
        margin-bottom: 1.5rem;
    }
    
    .dashboard-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .dashboard-subtitle {
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .stats-card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-card .card-body {
        display: flex;
        align-items: center;
        padding: 1.5rem;
    }
    
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #f0f5ff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
    }
    
    .stats-info {
        flex: 1;
    }
    
    .stats-label {
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0.3rem;
    }
    
    .stats-value {
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .stats-value small {
        font-size: 1rem;
        color: #6c757d;
    }
    
    .card-footer {
        background-color: transparent;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 0.75rem 1.5rem;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .chart-options .btn {
        font-size: 0.75rem;
    }
    
    @media (max-width: 767.98px) {
        .dashboard-container {
            padding: 1rem;
        }
        
        .stats-card .card-body {
            padding: 1rem;
        }
        
        .chart-container {
            height: 250px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Variables for charts
    let mainChart;
    let comparisonChart;
    
    // Initialize main chart
    initializeMainChart();
    
    // Initialize comparison chart if parameters exist
    <?php if (isset($_GET['bulan1']) && isset($_GET['bulan2'])): ?>
        initializeComparisonChart();
    <?php endif; ?>
    
    // Period toggle for main chart
    document.querySelectorAll('.chart-options button').forEach(button => {
        button.addEventListener('click', function() {
            const period = this.getAttribute('data-period');
            document.querySelectorAll('.chart-options button').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Update chart based on selected period
            updateChartPeriod(period);
        });
    });
    
    // Function to initialize main chart
    function initializeMainChart() {
        const ctx = document.getElementById('grafikPenjualan').getContext('2d');
        
        fetch('data_grafik_penjualan.php')
            .then(res => res.json())
            .then(data => {
                console.log("Data grafik penjualan:", data);
                
                // Destroy existing chart if it exists
                if (mainChart) {
                    mainChart.destroy();
                }
                
                mainChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.bulan,
                        datasets: [
                            {
                                label: 'Penjualan',
                                data: data.penjualan,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Laba',
                                data: data.laba,
                                backgroundColor: 'rgba(255, 206, 86, 0.6)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR',
                                                minimumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error("Error loading chart data:", error);
                document.getElementById('grafikPenjualan').parentElement.innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <p>Gagal memuat data grafik</p>
                    </div>
                `;
            });
    }
    
    // Function to update chart based on period
    function updateChartPeriod(period) {
        fetch(`data_grafik_penjualan.php?period=${period}`)
            .then(res => res.json())
            .then(data => {
                // Update chart data
                mainChart.data.labels = data.bulan;
                mainChart.data.datasets[0].data = data.penjualan;
                mainChart.data.datasets[1].data = data.laba;
                mainChart.update();
            })
            .catch(error => {
                console.error("Error updating chart period:", error);
            });
    }
    
    // Function to initialize comparison chart
    function initializeComparisonChart() {
        fetch('data_perbandingan_bulan.php?bulan1=<?= $_GET['bulan1'] ?? 0 ?>&bulan2=<?= $_GET['bulan2'] ?? 0 ?>')
            .then(res => res.json())
            .then(data => {
                console.log("Data perbandingan bulan:", data);
                const ctx2 = document.getElementById('perbandinganChart').getContext('2d');
                
                // Destroy existing chart if it exists
                if (comparisonChart) {
                    comparisonChart.destroy();
                }
                
                comparisonChart = new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: ['<?= date('F', mktime(0,0,0,$_GET['bulan1'] ?? 1,1)) ?>', '<?= date('F', mktime(0,0,0,$_GET['bulan2'] ?? 2,1)) ?>'],
                        datasets: [
                            {
                                label: 'Penjualan',
                                data: data.penjualan,
                                backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(54, 162, 235, 0.6)'],
                                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(54, 162, 235, 1)'],
                                borderWidth: 1
                            },
                            {
                                label: 'Laba',
                                data: data.laba,
                                backgroundColor: ['rgba(255, 206, 86, 0.6)', 'rgba(255, 206, 86, 0.6)'],
                                borderColor: ['rgba(255, 206, 86, 1)', 'rgba(255, 206, 86, 1)'],
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR',
                                                minimumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error("Error loading comparison chart data:", error);
                document.getElementById('perbandinganChart').parentElement.innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                        <p>Gagal memuat data perbandingan</p>
                    </div>
                `;
            });
    }
});
</script>

<?php include 'template/footer.php'; ?>