<?php
/**
 * CLINICA DENTAL - Clase Helper para Validaciones
 * Validaciones del lado del servidor (PHP)
 */
class Validator {
    
    private $errors = [];
    
    /**
     * Valida que un campo no este vacio
     */
    public function required($value, $fieldName) {
        if (empty(trim($value))) {
            $this->errors[$fieldName] = "El campo $fieldName es obligatorio";
            return false;
        }
        return true;
    }
    
    /**
     * Valida formato de DNI (8 digitos)
     */
    public function dni($value, $fieldName = 'DNI') {
        if (!preg_match('/^\d{8}$/', $value)) {
            $this->errors[$fieldName] = "El DNI debe tener 8 digitos";
            return false;
        }
        return true;
    }
    
    /**
     * Valida formato de telefono (9 digitos)
     */
    public function telefono($value, $fieldName = 'telefono') {
        if (!preg_match('/^\d{9}$/', $value)) {
            $this->errors[$fieldName] = "El telefono debe tener 9 digitos";
            return false;
        }
        return true;
    }
    
    /**
     * Valida formato de email
     */
    public function email($value, $fieldName = 'email') {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$fieldName] = "El email no tiene un formato valido";
            return false;
        }
        return true;
    }
    
    /**
     * Valida que solo contenga letras y espacios
     */
    public function lettersOnly($value, $fieldName, $minLength = 2, $maxLength = 50) {
        // Permite letras con acentos y espacios
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{' . $minLength . ',' . $maxLength . '}$/u', $value)) {
            $this->errors[$fieldName] = "El campo $fieldName solo debe contener letras ($minLength-$maxLength caracteres)";
            return false;
        }
        return true;
    }
    
    /**
     * Valida formato de fecha
     */
    public function date($value, $fieldName = 'fecha') {
        $date = DateTime::createFromFormat('Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            $this->errors[$fieldName] = "La fecha no tiene un formato valido";
            return false;
        }
        return true;
    }
    
    /**
     * Valida que la fecha no sea anterior a hoy
     */
    public function futureDate($value, $fieldName = 'fecha') {
        if (!$this->date($value, $fieldName)) {
            return false;
        }
        
        $date = new DateTime($value);
        $today = new DateTime('today');
        
        if ($date < $today) {
            $this->errors[$fieldName] = "La fecha no puede ser anterior a hoy";
            return false;
        }
        return true;
    }
    
    /**
     * Valida formato de hora
     */
    public function time($value, $fieldName = 'hora') {
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            $this->errors[$fieldName] = "La hora no tiene un formato valido";
            return false;
        }
        return true;
    }
    
    /**
     * Valida que sea un numero entero positivo
     */
    public function positiveInt($value, $fieldName) {
        if (!is_numeric($value) || intval($value) <= 0) {
            $this->errors[$fieldName] = "El campo $fieldName debe ser un numero positivo";
            return false;
        }
        return true;
    }
    
    /**
     * Valida longitud maxima
     */
    public function maxLength($value, $max, $fieldName) {
        if (strlen($value) > $max) {
            $this->errors[$fieldName] = "El campo $fieldName no puede exceder $max caracteres";
            return false;
        }
        return true;
    }
    
    /**
     * Valida un valor de un conjunto permitido (enum)
     */
    public function inArray($value, $allowed, $fieldName) {
        if (!in_array($value, $allowed)) {
            $this->errors[$fieldName] = "El valor de $fieldName no es valido";
            return false;
        }
        return true;
    }
    
    /**
     * Sanitiza una cadena de texto
     */
    public static function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitiza un entero
     */
    public static function sanitizeInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Verifica si hay errores
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Obtiene todos los errores
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Obtiene el primer error
     */
    public function getFirstError() {
        return reset($this->errors);
    }
    
    /**
     * Limpia los errores
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * Valida datos de paciente
     */
    public function validatePaciente($data) {
        $this->clearErrors();
        
        $this->required($data['nombres'] ?? '', 'nombres');
        $this->lettersOnly($data['nombres'] ?? '', 'nombres');
        
        $this->required($data['apellidos'] ?? '', 'apellidos');
        $this->lettersOnly($data['apellidos'] ?? '', 'apellidos');
        
        $this->required($data['dni'] ?? '', 'DNI');
        $this->dni($data['dni'] ?? '');
        
        $this->required($data['telefono'] ?? '', 'telefono');
        $this->telefono($data['telefono'] ?? '');
        
        $this->required($data['email'] ?? '', 'email');
        $this->email($data['email'] ?? '');
        
        return !$this->hasErrors();
    }
    
    /**
     * Valida datos de cita
     */
    public function validateCita($data) {
        $this->clearErrors();
        
        $this->required($data['fecha'] ?? '', 'fecha');
        $this->date($data['fecha'] ?? '');
        
        $this->required($data['hora'] ?? '', 'hora');
        $this->time($data['hora'] ?? '');
        
        $this->required($data['id_paciente'] ?? '', 'paciente');
        $this->positiveInt($data['id_paciente'] ?? '', 'paciente');
        
        $this->required($data['id_odontologo'] ?? '', 'odontologo');
        $this->positiveInt($data['id_odontologo'] ?? '', 'odontologo');
        
        if (isset($data['estado'])) {
            $this->inArray($data['estado'], ['PROGRAMADA', 'CONFIRMADA', 'COMPLETADA', 'CANCELADA'], 'estado');
        }
        
        return !$this->hasErrors();
    }
}
