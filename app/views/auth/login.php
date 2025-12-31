<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Clinica Dental</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="login-page">

<div class="login-card fade-in">
    <div class="login-logo">
        <span class="logo-icon">🦷</span>
        <h2>Clinica Dental</h2>
        <p class="text-muted">Sistema de Gestion</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger-custom mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=auth&action=login" id="loginForm" novalidate>
        <div class="mb-3">
            <label class="form-label-custom">
                <i class="bi bi-person"></i> Usuario
            </label>
            <input type="text" 
                   name="usuario" 
                   class="form-control form-control-custom" 
                   placeholder="Ingrese su usuario"
                   value="admin"
                   required
                   autocomplete="username">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-4">
            <label class="form-label-custom">
                <i class="bi bi-lock"></i> Contrasena
            </label>
            <div class="input-group">
                <input type="password" 
                       name="password" 
                       id="password"
                       class="form-control form-control-custom" 
                       placeholder="Ingrese su contrasena"
                       value="admin123"
                       required
                       autocomplete="current-password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                    <i class="bi bi-eye" id="toggleIcon"></i>
                </button>
            </div>
            <div class="invalid-feedback"></div>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-2">
            <i class="bi bi-box-arrow-in-right"></i> Ingresar
        </button>
    </form>

    <div class="text-center mt-4">
        <small class="text-muted">
            <i class="bi bi-shield-check"></i> Acceso seguro al sistema
        </small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/validations.js"></script>
<script>
    // Mostrar/ocultar password
    function togglePassword() {
        const password = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // Validacion del formulario
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        if (!validateLoginForm(this)) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>
