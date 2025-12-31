<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odontologos - Clinica Dental</title>
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
            <i class="bi bi-person-badge-fill"></i> Gestion de Odontologos
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
                        <a class="dropdown-item" href="index.php?controller=odontologo&action=exportarExcel">
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
            <?php if (empty($soloLectura)): ?>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalNuevo">
                <i class="bi bi-plus-circle"></i> Nuevo Odontologo
            </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success-custom alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <?php 
                $msgs = [
                    'created' => 'Odontologo registrado correctamente',
                    'updated' => 'Odontologo actualizado correctamente',
                    'deleted' => 'Odontologo eliminado correctamente'
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
                    'access_denied' => 'No tiene permisos para realizar esta accion'
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
                        <th><i class="bi bi-person"></i> Nombres</th>
                        <th><i class="bi bi-award"></i> Especialidad</th>
                        <th><i class="bi bi-telephone"></i> Telefono</th>
                        <th><i class="bi bi-envelope"></i> Email</th>
                        <th class="text-center"><i class="bi bi-gear"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No hay odontologos registrados</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($data as $o): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= $o['id_odontologo'] ?></span></td>
                        <td>
                            <i class="bi bi-person-badge text-primary"></i>
                            <?= htmlspecialchars($o['nombres']) ?>
                        </td>
                        <td><span class="badge bg-info"><?= htmlspecialchars($o['especialidad']) ?></span></td>
                        <td>
                            <a href="tel:<?= htmlspecialchars($o['telefono']) ?>" class="text-decoration-none">
                                <i class="bi bi-telephone-fill text-success"></i>
                                <?= htmlspecialchars($o['telefono']) ?>
                            </a>
                        </td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($o['email']) ?>" class="text-decoration-none">
                                <?= htmlspecialchars($o['email']) ?>
                            </a>
                        </td>
                        <td class="text-center">
                            <?php if (empty($soloLectura)): ?>
                            <button class="btn btn-warning-custom btn-action"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar"
                                    onclick="cargarOdontologo(
                                        '<?= $o['id_odontologo'] ?>',
                                        '<?= htmlspecialchars($o['nombres'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($o['especialidad'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($o['telefono'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($o['email'], ENT_QUOTES) ?>'
                                    )">
                                <i class="bi bi-pencil-fill text-white"></i>
                            </button>
                            <a href="index.php?controller=odontologo&action=eliminar&id=<?= $o['id_odontologo'] ?>"
                               class="btn btn-danger-custom btn-action"
                               onclick="return confirm('¿Eliminar este odontologo?')">
                                <i class="bi bi-trash-fill text-white"></i>
                            </a>
                            <?php else: ?>
                            <span class="badge bg-secondary"><i class="bi bi-eye"></i> Solo lectura</span>
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
        <small><i class="bi bi-info-circle"></i> Total: <?= count($data) ?> odontologo(s) registrado(s)</small>
    </div>
</div>

<!-- MODAL: NUEVO -->
<div class="modal fade modal-custom" id="modalNuevo" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="index.php?controller=odontologo&action=guardar" class="modal-content" novalidate>
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill"></i> Nuevo Odontologo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label-custom">Nombres *</label>
                    <input type="text" name="nombres" class="form-control form-control-custom" 
                           placeholder="Ej: Dra. Maria Lopez" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Especialidad *</label>
                    <select name="especialidad" class="form-select form-control-custom" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="Odontologia General">Odontologia General</option>
                        <option value="Ortodoncia">Ortodoncia</option>
                        <option value="Endodoncia">Endodoncia</option>
                        <option value="Periodoncia">Periodoncia</option>
                        <option value="Cirugia Oral">Cirugia Oral</option>
                        <option value="Odontopediatria">Odontopediatria</option>
                        <option value="Estetica Dental">Estetica Dental</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Telefono</label>
                    <input type="text" name="telefono" class="form-control form-control-custom" 
                           placeholder="Ej: 999888777" maxlength="9">
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Email</label>
                    <input type="email" name="email" class="form-control form-control-custom" 
                           placeholder="Ej: doctor@clinica.com">
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
        <form method="POST" action="index.php?controller=odontologo&action=actualizar" class="modal-content" novalidate>
            <div class="modal-header" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Odontologo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_odontologo" id="edit_id">
                <div class="mb-3">
                    <label class="form-label-custom">Nombres *</label>
                    <input type="text" name="nombres" id="edit_nombres" 
                           class="form-control form-control-custom" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Especialidad *</label>
                    <select name="especialidad" id="edit_especialidad" class="form-select form-control-custom" required>
                        <option value="Odontologia General">Odontologia General</option>
                        <option value="Ortodoncia">Ortodoncia</option>
                        <option value="Endodoncia">Endodoncia</option>
                        <option value="Periodoncia">Periodoncia</option>
                        <option value="Cirugia Oral">Cirugia Oral</option>
                        <option value="Odontopediatria">Odontopediatria</option>
                        <option value="Estetica Dental">Estetica Dental</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Telefono</label>
                    <input type="text" name="telefono" id="edit_telefono" 
                           class="form-control form-control-custom" maxlength="9">
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control form-control-custom">
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
function cargarOdontologo(id, nombres, especialidad, telefono, email) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nombres').value = nombres;
    document.getElementById('edit_especialidad').value = especialidad;
    document.getElementById('edit_telefono').value = telefono;
    document.getElementById('edit_email').value = email;
}

// Exportar tabla a PDF
function exportarPDF() {
    const tabla = document.querySelector('.table-custom').cloneNode(true);
    tabla.querySelectorAll('th:last-child, td:last-child').forEach(el => el.remove());
    
    const contenido = document.createElement('div');
    contenido.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px; padding: 20px;">
            <h2 style="color: #0d6efd; margin: 0;">🦷 CLINICA DENTAL</h2>
            <h3 style="margin: 10px 0;">Reporte de Odontologos</h3>
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
        filename: 'Reporte_Odontologos_' + new Date().toISOString().slice(0,10) + '.pdf',
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
            <title>Imprimir - Odontologos</title>
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
                <h3>Reporte de Odontologos</h3>
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
