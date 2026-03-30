<?php 

    include '../connect/Connect.php';

    function createSource($name, $url) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO source (name, url) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $url);
        $stmt->execute();
        closeConnection($conn);
    }

    function updateSource($id, $name, $url) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE source SET name = ?, url = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $url, $id);
        $stmt->execute();
        closeConnection($conn);
    }

    function deleteSource($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM source WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        closeConnection($conn);
    }
?>