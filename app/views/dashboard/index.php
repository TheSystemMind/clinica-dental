<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Clinica Dental</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .widget {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            height: 100%;
            overflow: hidden;
        }
        .widget-header {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            font-size: 0.9rem;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .widget-body {
            padding: 15px;
        }
        .stat-mini {
            text-align: center;
            padding: 10px 5px;
        }
        .stat-mini .number {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
        }
        .stat-mini .label {
            font-size: 0.7rem;
            color: #6c757d;
            margin-top: 4px;
        }
        .stat-row {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .stat-row:last-child {
            border-bottom: none;
        }
        .stat-row .icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 0.9rem;
        }
        .stat-row .info {
            flex: 1;
        }
        .stat-row .info .title {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .stat-row .info .value {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .chart-mini {
            height: 180px;
            position: relative;
        }
        .upcoming-item {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 6px;
            border-left: 3px solid #3498db;
        }
        .upcoming-item.confirmada { border-left-color: #27ae60; }
        .upcoming-item .time {
            font-weight: 600;
            font-size: 0.8rem;
            color: #2c3e50;
            min-width: 50px;
        }
        .upcoming-item .details {
            flex: 1;
            font-size: 0.75rem;
            color: #6c757d;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .quick-stat {
            background: linear-gradient(135deg, var(--bg-start), var(--bg-end));
            border-radius: 10px;
            padding: 15px 10px;
            text-align: center;
            color: white;
        }
        .quick-stat .icon { font-size: 1.5rem; opacity: 0.9; }
        .quick-stat .number { font-size: 1.8rem; font-weight: 700; }
        .quick-stat .label { font-size: 0.7rem; opacity: 0.9; }
        .progress-thin { height: 6px; border-radius: 3px; }
    </style>
</head>
<body>

<?php require BASE_PATH . '/app/views/layouts/nav.php'; ?>

<div class="main-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0"><i class="bi bi-speedometer2 text-primary"></i> Dashboard</h4>
            <small class="text-muted">Bienvenido, <?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuario') ?></small>
        </div>
        <span class="badge bg-light text-dark">
            <i class="bi bi-calendar3"></i> <?= date('d/m/Y') ?>
        </span>
    </div>

    <!-- Quick Stats Row -->
    <div class="quick-stats mb-3">
        <div class="quick-stat" style="--bg-start: #667eea; --bg-end: #764ba2;">
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <div class="number"><?= $totalPacientes ?></div>
            <div class="label">Pacientes</div>
        </div>
        <div class="quick-stat" style="--bg-start: #11998e; --bg-end: #38ef7d;">
            <div class="icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="number"><?= $totalOdontologos ?></div>
            <div class="label">Odontologos</div>
        </div>
        <div class="quick-stat" style="--bg-start: #fc4a1a; --bg-end: #f7b733;">
            <div class="icon"><i class="bi bi-calendar-check-fill"></i></div>
            <div class="number"><?= $estadisticasCitas['total'] ?? 0 ?></div>
            <div class="label">Citas</div>
        </div>
        <div class="quick-stat" style="--bg-start: #00c6ff; --bg-end: #0072ff;">
            <div class="icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="number"><?= $totalUsuarios ?></div>
            <div class="label">Usuarios</div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="row g-3">
        <!-- Estadisticas de Citas -->
        <div class="col-lg-4 col-md-6">
            <div class="widget">
                <div class="widget-header">
                    <i class="bi bi-clipboard-data text-primary"></i> Estado de Citas
                </div>
                <div class="widget-body p-2">
                    <div class="row g-0 text-center">
                        <div class="col-3 stat-mini">
                            <div class="number text-success"><?= $estadisticasCitas['hoy'] ?? 0 ?></div>
                            <div class="label">Hoy</div>
                        </div>
                        <div class="col-3 stat-mini">
                            <div class="number text-warning"><?= $estadisticasCitas['pendientes'] ?? 0 ?></div>
                            <div class="label">Pendientes</div>
                        </div>
                        <div class="col-3 stat-mini">
                            <div class="number text-info"><?= $estadisticasCitas['completadas'] ?? 0 ?></div>
                            <div class="label">Completadas</div>
                        </div>
                        <div class="col-3 stat-mini">
                            <div class="number text-danger"><?= $estadisticasCitas['canceladas'] ?? 0 ?></div>
                            <div class="label">Canceladas</div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="px-2">
                        <?php 
                        $total = $estadisticasCitas['total'] ?? 1;
                        $completadas = $estadisticasCitas['completadas'] ?? 0;
                        $porcent = $total > 0 ? round(($completadas / $total) * 100) : 0;
                        ?>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Tasa de completado</span>
                            <span class="fw-bold"><?= $porcent ?>%</span>
                        </div>
                        <div class="progress progress-thin">
                            <div class="progress-bar bg-success" style="width: <?= $porcent ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafico Estados -->
        <div class="col-lg-4 col-md-6">
            <div class="widget">
                <div class="widget-header">
                    <i class="bi bi-pie-chart-fill text-success"></i> Por Estado
                </div>
                <div class="widget-body p-2">
                    <div class="chart-mini">
                        <canvas id="chartEstados"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafico Odontologos -->
        <div class="col-lg-4 col-md-6">
            <div class="widget">
                <div class="widget-header">
                    <i class="bi bi-bar-chart-fill text-info"></i> Por Odontologo
                </div>
                <div class="widget-body p-2">
                    <div class="chart-mini">
                        <canvas id="chartOdontologos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proximas Citas -->
        <div class="col-lg-4 col-md-6">
            <div class="widget">
                <div class="widget-header">
                    <i class="bi bi-clock-fill text-warning"></i> Proximas Citas
                </div>
                <div class="widget-body p-2" style="max-height: 220px; overflow-y: auto;">
                    <?php if (!empty($proximasCitas)): ?>
                        <?php foreach ($proximasCitas as $cita): ?>
                        <div class="upcoming-item <?= strtolower($cita['estado']) ?>">
                            <span class="time"><?= date('H:i', strtotime($cita['hora'])) ?></span>
                            <span class="details">
                                <?= htmlspecialchars($cita['paciente']) ?> - 
                                <?= date('d/m', strtotime($cita['fecha'])) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                            <p class="small mb-0 mt-2">Sin citas proximas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tendencia Mensual -->
        <div class="col-lg-4 col-md-6">
            <div class="widget">
                <div class="widget-header">
                    <i class="bi bi-graph-up text-purple"></i> Tendencia Mensual
                </div>
                <div class="widget-body p-2">
                    <div class="chart-mini">
                        <canvas id="chartMensual"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Semanal -->
        <div class="col-lg-4 col-md-6">
            <div class="widget">
                <div class="widget-header">
                    <i class="bi bi-calendar-week text-danger"></i> Esta Semana / Mes
                </div>
                <div class="widget-body">
                    <div class="stat-row">
                        <div class="icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                        <div class="info">
                            <div class="title">Citas esta semana</div>
                            <div class="value text-primary"><?= $estadisticasCitas['semana'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="stat-row">
                        <div class="icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                        <div class="info">
                            <div class="title">Citas este mes</div>
                            <div class="value text-success"><?= $estadisticasCitas['mes'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="stat-row">
                        <div class="icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="info">
                            <div class="title">Citas hoy</div>
                            <div class="value text-warning"><?= $estadisticasCitas['hoy'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rapidos -->
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="widget">
                <div class="widget-header">
                    <i class="bi bi-lightning-fill text-warning"></i> Accesos Rapidos
                </div>
                <div class="widget-body">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <a href="index.php?controller=cita" class="btn btn-outline-primary w-100">
                                <i class="bi bi-calendar-plus"></i> Nueva Cita
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?controller=paciente" class="btn btn-outline-success w-100">
                                <i class="bi bi-person-plus"></i> Pacientes
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?controller=odontologo" class="btn btn-outline-info w-100">
                                <i class="bi bi-person-badge"></i> Odontologos
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?controller=cita" class="btn btn-outline-danger w-100">
                                <i class="bi bi-file-earmark-bar-graph"></i> Ver Citas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const datosEstados = <?= json_encode($citasPorEstado) ?>;
const datosOdontologos = <?= json_encode($citasPorOdontologo) ?>;
const datosMensuales = <?= json_encode($citasPorMes) ?>;

const colores = {
    'PROGRAMADA': '#f39c12',
    'CONFIRMADA': '#17a2b8',
    'COMPLETADA': '#28a745',
    'CANCELADA': '#dc3545'
};

// Grafico Estados (Doughnut)
new Chart(document.getElementById('chartEstados'), {
    type: 'doughnut',
    data: {
        labels: datosEstados.map(d => d.estado),
        datasets: [{
            data: datosEstados.map(d => d.total),
            backgroundColor: datosEstados.map(d => colores[d.estado] || '#6c757d'),
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: { display: false }
        }
    }
});

// Grafico Odontologos (Barras horizontales)
new Chart(document.getElementById('chartOdontologos'), {
    type: 'bar',
    data: {
        labels: datosOdontologos.map(d => d.odontologo.split(' ').slice(0,2).join(' ')),
        datasets: [{
            data: datosOdontologos.map(d => d.total),
            backgroundColor: ['#667eea', '#764ba2', '#11998e', '#38ef7d', '#fc4a1a'],
            borderRadius: 4
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { display: false } },
            y: { grid: { display: false } }
        }
    }
});

// Grafico Tendencia (Lineas)
new Chart(document.getElementById('chartMensual'), {
    type: 'line',
    data: {
        labels: datosMensuales.map(d => d.mes_nombre ? d.mes_nombre.substring(0,3) : d.mes),
        datasets: [{
            data: datosMensuales.map(d => d.total),
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointBackgroundColor: '#667eea'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0f0f0' } }
        }
    }
});
</script>

<!-- Footer Copyright -->
<footer class="text-center py-3 mt-4" style="background: linear-gradient(135deg, #0077b6 0%, #00b4d8 100%); color: white;">
    <small>&copy; 2025 Peter A. Chirinos N. - Clinica Dental</small>
</footer>

</body>
</html>
