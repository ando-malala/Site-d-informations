<?php 

    include '../connect/Connect.php';

    function createSource($name, $url, $logo_url = null, $type_id = null) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO source (name, url, logo_url, type_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $url, $logo_url, $type_id);
        $isCreated = $stmt->execute();
        closeConnection($conn);
        return $isCreated;
    }

    function updateSource($id, $name, $url, $logo_url = null, $type_id = null) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE source SET name = ?, url = ?, logo_url = ?, type_id = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $url, $logo_url, $type_id, $id);
        $isUpdated = $stmt->execute();
        closeConnection($conn);
        return $isUpdated;
    }

    function deleteSource($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM source WHERE id = ?");
        $stmt->bind_param("i", $id);
        $isDeleted = $stmt->execute();
        closeConnection($conn);
        return $isDeleted;
    }
?>