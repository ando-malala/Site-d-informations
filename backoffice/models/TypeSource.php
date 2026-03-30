<?php 

    include '../connect/Connect.php';

    function createTypeSource($name) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO type_source (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $isCreated = $stmt->execute();
        closeConnection($conn);
        return $isCreated;
    }

    function updateTypeSource($id, $name) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE type_source SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $isUpdated = $stmt->execute();
        closeConnection($conn);
        return $isUpdated;
    }

    function deleteTypeSource($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM type_source WHERE id = ?");
        $stmt->bind_param("i", $id);
        $isDeleted = $stmt->execute();
        closeConnection($conn);
        return $isDeleted;
    }

?>