<?php

class Database {

    // Host: usar variable de entorno o 'mysql' (nombre del servicio en Docker)
    private $host;
    private $db;
    private $user;
    private $pass;

    public function __construct() {
        // Configuración para Docker (usa variables de entorno) o valores por defecto
        $this->host = getenv('DB_HOST') ?: 'clinica-dental-db';  // Nombre del servicio en docker-compose
        $this->db   = getenv('DB_NAME') ?: 'clinica_dental';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->pass = getenv('DB_PASS') ?: 'root';
    }

    public function conectar()
    {
        $dsn = "mysql:host={$this->host};port=3306;dbname={$this->db};charset=utf8mb4";

        $pdo = new PDO(
            $dsn,
            $this->user,
            $this->pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        );

        return $pdo;
    }
}
