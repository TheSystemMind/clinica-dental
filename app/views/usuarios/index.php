<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Clinica Dental</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>

<?php require BASE_PATH . '/app/views/layouts/nav.php'; ?>

<div class="main-container">
    <div class="page-header">
        <h2 class="page-title">
            <i class="bi bi-shield-lock-fill"></i> Gestion de Usuarios
        </h2>
        <div class="d-flex gap-2">
            <!-- Dropdown de Reportes -->
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-file-earmark-bar-graph"></i> Reportes
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header"><i class="bi bi-download"></i> Descargar Reporte</h6></li>
                    <li>
                        <a class="dropdown-item" href="index.php?controller=usuario&action=exportarExcel">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel (CSV)
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="exportarPDF(); return false;">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> Reporte PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="imprimirTabla(); return false;">
                            <i class="bi bi-printer text-secondary"></i> Imprimir
                        </a>
                    </li>
                </ul>
            </div>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalNuevo">
                <i class="bi bi-plus-circle"></i> Nuevo Usuario
            </button>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success-custom alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <?php 
                $msgs = [
                    'created' => 'Usuario registrado correctamente',
                    'updated' => 'Usuario actualizado correctamente',
                    'deleted' => 'Usuario eliminado correctamente'
                ];
                echo $msgs[$_GET['msg']] ?? 'Operacion exitosa';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger-custom alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?php 
                $errors = [
                    'invalid_data' => 'Los datos ingresados no son validos',
                    'user_exists' => 'Ya existe un usuario con ese nombre',
                    'cannot_delete_self' => 'No puede eliminar su propio usuario'
                ];
                echo $errors[$_GET['error']] ?? 'Ocurrio un error';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card-custom fade-in">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th><i class="bi bi-hash"></i> ID</th>
                        <th><i class="bi bi-person"></i> Usuario</th>
                        <th><i class="bi bi-shield"></i> Rol</th>
                        <th><i class="bi bi-toggle-on"></i> Estado</th>
                        <th class="text-center"><i class="bi bi-gear"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No hay usuarios registrados</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($data as $u): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= $u['id_usuario'] ?></span></td>
                        <td>
                            <i class="bi bi-person-circle text-primary"></i>
                            <strong><?= htmlspecialchars($u['usuario']) ?></strong>
                            <?php if ($u['usuario'] === $_SESSION['usuario']): ?>
                                <span class="badge bg-info ms-1">Tu</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['rol'] === 'ADMIN'): ?>
                                <span class="badge bg-danger"><i class="bi bi-shield-fill"></i> Administrador</span>
                            <?php elseif ($u['rol'] === 'ODONTOLOGO'): ?>
                                <span class="badge bg-success"><i class="bi bi-heart-pulse"></i> Odontologo</span>
                            <?php elseif ($u['rol'] === 'RECEPCION'): ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-telephone"></i> Recepcion</span>
                            <?php else: ?>
                                <span class="badge bg-primary"><i class="bi bi-person"></i> Usuario</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['activo']): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning-custom btn-action"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar"
                                    onclick="cargarUsuario(
                                        '<?= $u['id_usuario'] ?>',
                                        '<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($u['rol'] ?? 'USER', ENT_QUOTES) ?>'
                                    )">
                                <i class="bi bi-pencil-fill text-white"></i>
                            </button>
                            <?php if ($u['usuario'] !== $_SESSION['usuario']): ?>
                            <a href="index.php?controller=usuario&action=eliminar&id=<?= $u['id_usuario'] ?>"
                               class="btn btn-danger-custom btn-action"
                               onclick="return confirm('¿Eliminar este usuario?')">
                                <i class="bi bi-trash-fill text-white"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 text-muted">
        <small><i class="bi bi-info-circle"></i> Total: <?= count($data) ?> usuario(s) registrado(s)</small>
    </div>
</div>

<!-- MODAL: NUEVO -->
<div class="modal fade modal-custom" id="modalNuevo" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="index.php?controller=usuario&action=guardar" class="modal-content" novalidate>
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill"></i> Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label-custom">Usuario *</label>
                    <input type="text" name="usuario" class="form-control form-control-custom" 
                           placeholder="Nombre de usuario" required maxlength="50">
                    <small class="form-text-helper">Sin espacios ni caracteres especiales</small>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Contrasena *</label>
                    <input type="password" name="password" class="form-control form-control-custom" 
                           placeholder="Contrasena" required minlength="4">
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Rol</label>
                    <select name="rol" class="form-select form-control-custom">
                        <option value="ODONTOLOGO">Odontologo</option>
                        <option value="RECEPCION">Recepcion</option>
                        <option value="ADMIN">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary-custom">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDITAR -->
<div class="modal fade modal-custom" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="index.php?controller=usuario&action=actualizar" class="modal-content" novalidate>
            <div class="modal-header" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_usuario" id="edit_id">
                <div class="mb-3">
                    <label class="form-label-custom">Usuario *</label>
                    <input type="text" name="usuario" id="edit_usuario" 
                           class="form-control form-control-custom" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Nueva Contrasena</label>
                    <input type="password" name="password" class="form-control form-control-custom" 
                           placeholder="Dejar vacio para mantener actual">
                    <small class="form-text-helper">Solo completar si desea cambiar la contrasena</small>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Rol</label>
                    <select name="rol" id="edit_rol" class="form-select form-control-custom">
                        <option value="ODONTOLOGO">Odontologo</option>
                        <option value="RECEPCION">Recepcion</option>
                        <option value="ADMIN">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning-custom">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Footer Copyright -->
<footer class="text-center py-3 mt-4" style="background: linear-gradient(135deg, #0077b6 0%, #00b4d8 100%); color: white;">
    <small>&copy; 2025 Peter A. Chirinos N. - Clinica Dental</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function cargarUsuario(id, usuario, rol) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_usuario').value = usuario;
    document.getElementById('edit_rol').value = rol;
}

// Exportar tabla a PDF
function exportarPDF() {
    const tabla = document.querySelector('.table-custom').cloneNode(true);
    tabla.querySelectorAll('th:last-child, td:last-child').forEach(el => el.remove());
    
    const contenido = document.createElement('div');
    contenido.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px; padding: 20px;">
            <h2 style="color: #0d6efd; margin: 0;">🦷 CLINICA DENTAL</h2>
            <h3 style="margin: 10px 0;">Reporte de Usuarios</h3>
            <p style="color: #6c757d; margin: 0;">Generado el: ${new Date().toLocaleString('es-PE')}</p>
            <hr style="margin: 15px 0;">
        </div>
    `;
    contenido.appendChild(tabla);
    
    tabla.style.width = '100%';
    tabla.style.fontSize = '11px';
    tabla.style.borderCollapse = 'collapse';
    tabla.querySelectorAll('th, td').forEach(cell => {
        cell.style.border = '1px solid #dee2e6';
        cell.style.padding = '8px';
    });
    tabla.querySelectorAll('th').forEach(th => {
        th.style.backgroundColor = '#f8f9fa';
        th.style.fontWeight = 'bold';
    });
    
    html2pdf().set({
        margin: 10,
        filename: 'Reporte_Usuarios_' + new Date().toISOString().slice(0,10) + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(contenido).save();
}

// Imprimir tabla
function imprimirTabla() {
    const tabla = document.querySelector('.table-custom').cloneNode(true);
    tabla.querySelectorAll('th:last-child, td:last-child').forEach(el => el.remove());
    
    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <html>
        <head>
            <title>Imprimir - Usuarios</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h2 { color: #0077b6; margin: 0; }
                .header p { color: #666; margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                th { background-color: #0077b6; color: white; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>🦷 CLINICA DENTAL</h2>
                <h3>Reporte de Usuarios</h3>
                <p>Fecha de impresion: ${new Date().toLocaleString('es-PE')}</p>
            </div>
            ${tabla.outerHTML}
        </body>
        </html>
    `);
    ventana.document.close();
    ventana.onload = function() { ventana.print(); };
}
</script>

</body>
</html>
