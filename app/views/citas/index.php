<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Citas - Clinica Dental</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>

<?php require BASE_PATH . '/app/views/layouts/nav.php'; ?>

<div class="main-container">
    <!-- Encabezado de pagina -->
    <div class="page-header">
        <h2 class="page-title">
            <i class="bi bi-calendar-check-fill"></i> Gestion de Citas
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
                        <a class="dropdown-item" href="index.php?controller=cita&action=exportarExcel">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel (CSV)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="index.php?controller=cita&action=exportarEstadisticas">
                            <i class="bi bi-file-earmark-spreadsheet text-primary"></i> Estadisticas Excel
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
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
                <i class="bi bi-plus-circle"></i> Nueva Cita
            </button>
        </div>
    </div>

    <!-- Mensajes de exito/error -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success-custom alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <?php 
                $msgs = [
                    'created' => 'Cita registrada correctamente',
                    'updated' => 'Cita actualizada correctamente',
                    'deleted' => 'Cita eliminada correctamente',
                    'status_changed' => 'Estado de cita actualizado'
                ];
                echo $msgs[$_GET['msg']] ?? 'Operacion exitosa';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tabla de Citas -->

    <!-- Tabla de citas -->
    <div class="card-custom fade-in">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th><i class="bi bi-hash"></i> ID</th>
                        <th><i class="bi bi-calendar-date"></i> Fecha</th>
                        <th><i class="bi bi-clock"></i> Hora</th>
                        <th><i class="bi bi-person"></i> Paciente</th>
                        <th><i class="bi bi-person-badge"></i> Odontologo</th>
                        <th><i class="bi bi-flag"></i> Estado</th>
                        <th class="text-center"><i class="bi bi-gear"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No hay citas registradas</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($data as $cita): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= $cita['id_cita'] ?></span></td>
                        <td>
                            <i class="bi bi-calendar3 text-primary"></i>
                            <?= date('d/m/Y', strtotime($cita['fecha'])) ?>
                        </td>
                        <td>
                            <i class="bi bi-clock text-info"></i>
                            <?= date('H:i', strtotime($cita['hora'])) ?>
                        </td>
                        <td><?= htmlspecialchars($cita['paciente_nombre']) ?></td>
                        <td>
                            <i class="bi bi-person-badge text-success"></i>
                            <?= htmlspecialchars($cita['odontologo_nombre']) ?>
                        </td>
                        <td>
                            <span class="badge-estado badge-<?= strtolower($cita['estado']) ?>">
                                <?php
                                    $iconos = [
                                        'PROGRAMADA' => 'calendar-event',
                                        'CONFIRMADA' => 'calendar-check',
                                        'COMPLETADA' => 'check-circle',
                                        'CANCELADA' => 'x-circle'
                                    ];
                                ?>
                                <i class="bi bi-<?= $iconos[$cita['estado']] ?? 'circle' ?>"></i>
                                <?= $cita['estado'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <!-- Boton Editar -->
                            <button class="btn btn-warning-custom btn-action" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditarCita"
                                    title="Editar"
                                    onclick="cargarCita(
                                        '<?= $cita['id_cita'] ?>',
                                        '<?= $cita['fecha'] ?>',
                                        '<?= $cita['hora'] ?>',
                                        '<?= $cita['estado'] ?>',
                                        '<?= $cita['id_paciente'] ?>',
                                        '<?= $cita['id_odontologo'] ?>'
                                    )">
                                <i class="bi bi-pencil-fill text-white"></i>
                            </button>
                            
                            <!-- Boton Eliminar -->
                            <a href="index.php?controller=cita&action=eliminar&id=<?= $cita['id_cita'] ?>" 
                               class="btn btn-danger-custom btn-action"
                               title="Eliminar"
                               onclick="return confirmDelete('¿Eliminar esta cita?')">
                                <i class="bi bi-trash-fill text-white"></i>
                            </a>
                            
                            <!-- Dropdown para cambiar estado -->
                            <div class="btn-group dropdown">
                                <button type="button" 
                                        class="btn btn-primary-custom btn-action" 
                                        data-bs-toggle="dropdown"
                                        data-bs-auto-close="true"
                                        aria-expanded="false">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                                    <li class="dropdown-header small text-muted">Cambiar estado a:</li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item py-2" href="index.php?controller=cita&action=cambiarEstado&id=<?= $cita['id_cita'] ?>&estado=PROGRAMADA">
                                            <i class="bi bi-calendar-event text-warning me-2"></i> Programada
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="index.php?controller=cita&action=cambiarEstado&id=<?= $cita['id_cita'] ?>&estado=CONFIRMADA">
                                            <i class="bi bi-calendar-check text-info me-2"></i> Confirmada
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="index.php?controller=cita&action=cambiarEstado&id=<?= $cita['id_cita'] ?>&estado=COMPLETADA">
                                            <i class="bi bi-check-circle text-success me-2"></i> Completada
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="index.php?controller=cita&action=cambiarEstado&id=<?= $cita['id_cita'] ?>&estado=CANCELADA">
                                            <i class="bi bi-x-circle text-danger me-2"></i> Cancelada
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Total de registros -->
    <div class="mt-3 text-muted">
        <small><i class="bi bi-info-circle"></i> Total: <?= count($data) ?> cita(s) registrada(s)</small>
    </div>
</div>

<!-- MODAL: NUEVA CITA -->
<div class="modal fade modal-custom" id="modalNuevaCita" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="index.php?controller=cita&action=guardar" 
              class="modal-content" id="formNuevaCita" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-plus-fill"></i> Nueva Cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">
                            <i class="bi bi-calendar-date"></i> Fecha *
                        </label>
                        <input type="date" name="fecha" class="form-control form-control-custom" required>
                        <div class="invalid-feedback"></div>
                        <small class="form-text-helper">Seleccione fecha de la cita</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">
                            <i class="bi bi-clock"></i> Hora *
                        </label>
                        <input type="time" name="hora" class="form-control form-control-custom" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">
                        <i class="bi bi-person"></i> Paciente *
                    </label>
                    <select name="id_paciente" class="form-select form-control-custom" required>
                        <option value="">-- Seleccionar Paciente --</option>
                        <?php foreach($pacientes as $p): ?>
                            <option value="<?= $p['id_paciente'] ?>">
                                <?= htmlspecialchars($p['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">
                        <i class="bi bi-person-badge"></i> Odontologo *
                    </label>
                    <select name="id_odontologo" class="form-select form-control-custom" required>
                        <option value="">-- Seleccionar Odontologo --</option>
                        <?php foreach($odontologos as $o): ?>
                            <option value="<?= $o['id_odontologo'] ?>">
                                <?= htmlspecialchars($o['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">
                        <i class="bi bi-flag"></i> Estado
                    </label>
                    <select name="estado" class="form-select form-control-custom">
                        <option value="PROGRAMADA" selected>Programada</option>
                        <option value="CONFIRMADA">Confirmada</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check-circle"></i> Guardar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDITAR CITA -->
<div class="modal fade modal-custom" id="modalEditarCita" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="index.php?controller=cita&action=actualizar" 
              class="modal-content" id="formEditarCita" novalidate>
            <div class="modal-header" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square"></i> Editar Cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_cita" id="edit_id_cita">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Fecha *</label>
                        <input type="date" name="fecha" id="edit_fecha" 
                               class="form-control form-control-custom" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Hora *</label>
                        <input type="time" name="hora" id="edit_hora" 
                               class="form-control form-control-custom" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Paciente *</label>
                    <select name="id_paciente" id="edit_id_paciente" 
                            class="form-select form-control-custom" required>
                        <?php foreach($pacientes as $p): ?>
                            <option value="<?= $p['id_paciente'] ?>">
                                <?= htmlspecialchars($p['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Odontologo *</label>
                    <select name="id_odontologo" id="edit_id_odontologo" 
                            class="form-select form-control-custom" required>
                        <?php foreach($odontologos as $o): ?>
                            <option value="<?= $o['id_odontologo'] ?>">
                                <?= htmlspecialchars($o['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Estado</label>
                    <select name="estado" id="edit_estado" class="form-select form-control-custom">
                        <option value="PROGRAMADA">Programada</option>
                        <option value="CONFIRMADA">Confirmada</option>
                        <option value="COMPLETADA">Completada</option>
                        <option value="CANCELADA">Cancelada</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-warning-custom">
                    <i class="bi bi-check-circle"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="js/validations.js"></script>
<script>
// Cargar datos en modal de edicion
function cargarCita(id, fecha, hora, estado, idPaciente, idOdontologo) {
    document.getElementById('edit_id_cita').value = id;
    document.getElementById('edit_fecha').value = fecha;
    document.getElementById('edit_hora').value = hora;
    document.getElementById('edit_estado').value = estado;
    document.getElementById('edit_id_paciente').value = idPaciente;
    document.getElementById('edit_id_odontologo').value = idOdontologo;
}

// Exportar tabla a PDF
function exportarPDF() {
    // Crear contenedor temporal para el PDF
    const tabla = document.querySelector('.table-custom').cloneNode(true);
    
    // Remover columna de acciones
    tabla.querySelectorAll('th:last-child, td:last-child').forEach(el => el.remove());
    
    const contenido = document.createElement('div');
    contenido.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px; padding: 20px;">
            <h2 style="color: #0d6efd; margin: 0;">🦷 CLINICA DENTAL</h2>
            <h3 style="margin: 10px 0;">Reporte de Citas</h3>
            <p style="color: #6c757d; margin: 0;">Generado el: ${new Date().toLocaleString('es-PE')}</p>
            <hr style="margin: 15px 0;">
        </div>
    `;
    contenido.appendChild(tabla);
    
    // Estilizar tabla para PDF
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
    
    const opt = {
        margin: 10,
        filename: 'Reporte_Citas_' + new Date().toISOString().slice(0,10) + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(contenido).save();
}

// Imprimir tabla
function imprimirTabla() {
    const tabla = document.querySelector('.table-custom').cloneNode(true);
    tabla.querySelectorAll('th:last-child, td:last-child').forEach(el => el.remove());
    
    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <html>
        <head>
            <title>Imprimir - Citas</title>
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
                <h3>Reporte de Citas</h3>
                <p>Fecha de impresion: ${new Date().toLocaleString('es-PE')}</p>
            </div>
            ${tabla.outerHTML}
        </body>
        </html>
    `);
    ventana.document.close();
    ventana.onload = function() { ventana.print(); };
}

// Validar formularios al enviar
document.getElementById('formNuevaCita').addEventListener('submit', function(e) {
    if (!validateCitaForm(this)) {
        e.preventDefault();
    }
});

document.getElementById('formEditarCita').addEventListener('submit', function(e) {
    if (!validateCitaForm(this)) {
        e.preventDefault();
    }
});

// Configurar dropdowns para mostrar correctamente
document.addEventListener('DOMContentLoaded', function() {
    var dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(function(dropdownToggle) {
        new bootstrap.Dropdown(dropdownToggle, {
            popperConfig: {
                strategy: 'fixed'
            }
        });
    });
});

// Limpiar formulario al cerrar modal
document.getElementById('modalNuevaCita').addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    this.querySelectorAll('.form-control, .form-select').forEach(input => clearValidation(input));
});
</script>

<!-- Footer Copyright -->
<footer class="text-center py-3 mt-4" style="background: linear-gradient(135deg, #0077b6 0%, #00b4d8 100%); color: white;">
    <small>&copy; 2025 Peter A. Chirinos N. - Clinica Dental</small>
</footer>

</body>
</html>