<?php

if (!function_exists('getConnection')) {
    function getConnection() {
        $host = "127.0.0.1";
        $port = 3306;
        $dbname = "site_info";
        $username = "root";
        $password = "";

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