<?php

function getConnection() {
    $host = "127.0.0.1";
    $port = 3306;
    $dbname = "site_info";
    $username = "root";
    $password = "";

    // Activer les erreurs mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli($host, $username, $password, $dbname, $port);

        // Définir le charset (important pour MySQL 8)
        $conn->set_charset("utf8mb4");

        return $conn;

    } catch (Exception $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

function closeConnection($conn) {
    $conn->close();
}

function getAllByTable($table) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM $table");
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    closeConnection($conn);
    return $data;
}