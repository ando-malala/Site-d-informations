<?php

if (!function_exists('getConnection')) {
    function getConnection() {
        $host = getenv('DB_HOST') ?: "127.0.0.1";
        $port = (int) (getenv('DB_PORT') ?: 3306);
        $dbname = getenv('DB_NAME') ?: "site_info";
        $username = getenv('DB_USER') ?: "root";
        $password = getenv('DB_PASS');
        if ($password === false) {
            $password = "";
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conn = new mysqli($host, $username, $password, $dbname, $port);
            $conn->set_charset("utf8mb4");
            return $conn;
        } catch (Exception $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
}

if (!function_exists('closeConnection')) {
    function closeConnection($conn) {
        $conn->close();
    }
}