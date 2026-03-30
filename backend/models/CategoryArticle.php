<?php 

    include '../connect/Connect.php';

    function createTypeSource($name) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO category_article (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        closeConnection($conn);
    }

    function updateTypeSource($id, $name) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE category_article SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        closeConnection($conn);
    }

    function deleteTypeSource($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM category_article WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        closeConnection($conn);
    }


?>