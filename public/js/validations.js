/**
 * CLINICA DENTAL - Validaciones JavaScript
 * Validaciones del lado del cliente
 */

// Patrones de validación
const PATTERNS = {
    dni: /^\d{8}$/,
    telefono: /^\d{9}$/,
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    nombres: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/,
    fecha: /^\d{4}-\d{2}-\d{2}$/,
    hora: /^\d{2}:\d{2}$/
};

// Mensajes de error
const MESSAGES = {
    required: 'Este campo es obligatorio',
    dni: 'DNI debe tener 8 dígitos',
    telefono: 'Teléfono debe tener 9 dígitos',
    email: 'Ingrese un email válido',
    nombres: 'Solo letras y espacios (2-50 caracteres)',
    fecha: 'Seleccione una fecha válida',
    hora: 'Seleccione una hora válida',
    fechaPasada: 'La fecha no puede ser anterior a hoy',
    select: 'Debe seleccionar una opción'
};

/**
 * Muestra mensaje de error en un campo
 */
function showError(input, message) {
    input.classList.remove('is-valid');
    input.classList.add('is-invalid');
    
    // Buscar o crear el div de feedback
    let feedback = input.nextElementSibling;
    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
        feedback = document.createElement('div');
        feedback.classList.add('invalid-feedback');
        input.parentNode.insertBefore(feedback, input.nextSibling);
    }
    feedback.textContent = message;
}

/**
 * Muestra éxito en un campo
 */
function showSuccess(input) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    
    // Remover mensaje de error si existe
    let feedback = input.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = '';
    }
}

/**
 * Limpia la validación de un campo
 */
function clearValidation(input) {
    input.classList.remove('is-invalid', 'is-valid');
}

/**
 * Valida campo requerido
 */
function validateRequired(input) {
    const value = input.value.trim();
    if (!value) {
        showError(input, MESSAGES.required);
        return false;
    }
    return true;
}

/**
 * Valida DNI
 */
function validateDNI(input) {
    const value = input.value.trim();
    if (!value) {
        showError(input, MESSAGES.required);
        return false;
    }
    if (!PATTERNS.dni.test(value)) {
        showError(input, MESSAGES.dni);
        return false;
    }
    showSuccess(input);
    return true;
}

/**
 * Valida teléfono
 */
function validateTelefono(input) {
    const value = input.value.trim();
    if (!value) {
        showError(input, MESSAGES.required);
        return false;
    }
    if (!PATTERNS.telefono.test(value)) {
        showError(input, MESSAGES.telefono);
        return false;
    }
    showSuccess(input);
    return true;
}

/**
 * Valida email
 */
function validateEmail(input) {
    const value = input.value.trim();
    if (!value) {
        showError(input, MESSAGES.required);
        return false;
    }
    if (!PATTERNS.email.test(value)) {
        showError(input, MESSAGES.email);
        return false;
    }
    showSuccess(input);
    return true;
}

/**
 * Valida nombres/apellidos
 */
function validateNombres(input) {
    const value = input.value.trim();
    if (!value) {
        showError(input, MESSAGES.required);
        return false;
    }
    if (!PATTERNS.nombres.test(value)) {
        showError(input, MESSAGES.nombres);
        return false;
    }
    showSuccess(input);
    return true;
}

/**
 * Valida fecha
 */
function validateFecha(input, allowPast = false) {
    const value = input.value;
    if (!value) {
        showError(input, MESSAGES.required);
        return false;
    }
    
    if (!allowPast) {
        const selectedDate = new Date(value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            showError(input, MESSAGES.fechaPasada);
            return false;
        }
    }
    
    showSuccess(input);
    return true;
}

/**
 * Valida hora
 */
function validateHora(input) {
    const value = input.value;
    if (!value) {
        showError(input, MESSAGES.required);
        return false;
    }
    showSuccess(input);
    return true;
}

/**
 * Valida select
 */
function validateSelect(input) {
    const value = input.value;
    if (!value) {
        showError(input, MESSAGES.select);
        return false;
    }
    showSuccess(input);
    return true;
}

/**
 * Valida formulario de paciente
 */
function validatePacienteForm(form) {
    let isValid = true;
    
    const nombres = form.querySelector('[name="nombres"]');
    const apellidos = form.querySelector('[name="apellidos"]');
    const dni = form.querySelector('[name="dni"]');
    const telefono = form.querySelector('[name="telefono"]');
    const email = form.querySelector('[name="email"]');
    
    if (!validateNombres(nombres)) isValid = false;
    if (!validateNombres(apellidos)) isValid = false;
    if (!validateDNI(dni)) isValid = false;
    if (!validateTelefono(telefono)) isValid = false;
    if (!validateEmail(email)) isValid = false;
    
    return isValid;
}

/**
 * Valida formulario de cita
 */
function validateCitaForm(form) {
    let isValid = true;
    
    const fecha = form.querySelector('[name="fecha"]');
    const hora = form.querySelector('[name="hora"]');
    const paciente = form.querySelector('[name="id_paciente"]');
    const odontologo = form.querySelector('[name="id_odontologo"]');
    
    if (!validateFecha(fecha)) isValid = false;
    if (!validateHora(hora)) isValid = false;
    if (!validateSelect(paciente)) isValid = false;
    if (!validateSelect(odontologo)) isValid = false;
    
    return isValid;
}

/**
 * Valida formulario de login
 */
function validateLoginForm(form) {
    let isValid = true;
    
    const usuario = form.querySelector('[name="usuario"]');
    const password = form.querySelector('[name="password"]');
    
    if (!validateRequired(usuario)) {
        showError(usuario, MESSAGES.required);
        isValid = false;
    } else {
        showSuccess(usuario);
    }
    
    if (!validateRequired(password)) {
        showError(password, MESSAGES.required);
        isValid = false;
    } else {
        showSuccess(password);
    }
    
    return isValid;
}

/**
 * Inicializa validaciones en tiempo real
 */
function initRealTimeValidation() {
    // DNI - solo números, máximo 8 dígitos
    document.querySelectorAll('[name="dni"]').forEach(input => {
        input.setAttribute('maxlength', '8');
        input.addEventListener('input', function() {
            // Solo permitir números
            this.value = this.value.replace(/[^0-9]/g, '');
            // Limitar a 8 caracteres
            if (this.value.length > 8) {
                this.value = this.value.slice(0, 8);
            }
        });
        input.addEventListener('blur', function() {
            if (this.value) validateDNI(this);
        });
    });
    
    // Teléfono - solo números, máximo 9 dígitos
    document.querySelectorAll('[name="telefono"]').forEach(input => {
        input.setAttribute('maxlength', '9');
        input.addEventListener('input', function() {
            // Solo permitir números
            this.value = this.value.replace(/[^0-9]/g, '');
            // Limitar a 9 caracteres
            if (this.value.length > 9) {
                this.value = this.value.slice(0, 9);
            }
        });
        input.addEventListener('blur', function() {
            if (this.value) validateTelefono(this);
        });
    });
    
    // Email
    document.querySelectorAll('[name="email"]').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) validateEmail(this);
        });
    });
    
    // Nombres y apellidos
    document.querySelectorAll('[name="nombres"], [name="apellidos"]').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) validateNombres(this);
        });
    });
    
    // Fecha
    document.querySelectorAll('[name="fecha"]').forEach(input => {
        // Establecer fecha mínima como hoy
        const today = new Date().toISOString().split('T')[0];
        input.setAttribute('min', today);
        
        input.addEventListener('change', function() {
            validateFecha(this);
        });
    });
}

/**
 * Muestra mensaje de confirmación antes de eliminar
 */
function confirmDelete(message = '¿Está seguro de eliminar este registro?') {
    return confirm(message);
}

/**
 * Muestra notificación toast
 */
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    toast.style.cssText = 'min-width: 300px; animation: slideIn 0.3s ease;';
    toast.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.getElementById('toast-container').appendChild(toast);
    
    // Auto cerrar después de 4 segundos
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initRealTimeValidation();
    
    // Agregar animación fade-in a elementos principales
    document.querySelectorAll('.card-custom, .table-custom').forEach(el => {
        el.classList.add('fade-in');
    });
});
