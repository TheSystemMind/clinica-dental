<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <?php 
        // Definir destino del logo según rol
        $rolUsuario = $_SESSION['rol'] ?? '';
        $logoDestino = ($rolUsuario === 'ADMIN') ? 'dashboard' : 'cita';
        ?>
        <a class="navbar-brand text-white" href="index.php?controller=<?= $logoDestino ?>">
            <span class="logo-icon">🦷</span>
            Clinica Dental
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php 
                $esAdmin = ($rolUsuario === 'ADMIN');
                $esOdontologo = ($rolUsuario === 'ODONTOLOGO');
                $esRecepcion = ($rolUsuario === 'RECEPCION');
                ?>
                
                <?php if ($esAdmin): ?>
                <!-- Dashboard: Solo ADMIN -->
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['controller']) && $_GET['controller'] == 'dashboard') ? 'active' : '' ?>" 
                       href="index.php?controller=dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($esAdmin || $esOdontologo || $esRecepcion): ?>
                <!-- Pacientes: ADMIN, ODONTOLOGO, RECEPCION -->
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['controller']) && $_GET['controller'] == 'paciente') ? 'active' : '' ?>" 
                       href="index.php?controller=paciente">
                        <i class="bi bi-people-fill"></i> Pacientes
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Citas: visible para todos -->
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['controller']) && $_GET['controller'] == 'cita') ? 'active' : '' ?>" 
                       href="index.php?controller=cita">
                        <i class="bi bi-calendar-check-fill"></i> Citas
                    </a>
                </li>
                
                <?php if ($esAdmin || $esRecepcion): ?>
                <!-- Odontologos: ADMIN y RECEPCION -->
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['controller']) && $_GET['controller'] == 'odontologo') ? 'active' : '' ?>" 
                       href="index.php?controller=odontologo">
                        <i class="bi bi-person-badge-fill"></i> Odontologos
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($esAdmin): ?>
                <!-- Usuarios: Solo ADMIN -->
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['controller']) && $_GET['controller'] == 'usuario') ? 'active' : '' ?>" 
                       href="index.php?controller=usuario">
                        <i class="bi bi-shield-lock-fill"></i> Usuarios
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="user-badge text-white">
                    <i class="bi bi-person-circle"></i>
                    <?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuario') ?>
                    <?php if ($esOdontologo): ?>
                    <span class="badge bg-success ms-1">Odontologo</span>
                    <?php elseif ($esRecepcion): ?>
                    <span class="badge bg-warning text-dark ms-1">Recepcion</span>
                    <?php elseif ($esAdmin): ?>
                    <span class="badge bg-danger ms-1">Admin</span>
                    <?php endif; ?>
                </span>
                <a href="index.php?controller=auth&action=logout" class="btn btn-logout btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </div>
</nav>
