<?php 

    include '../connect/Connect.php';

    function createArticle($title, $content,$source_id, $category_id, $created_at) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO article (title, content,source_id, category_id, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssii", $title, $content, $source_id, $category_id, $created_at);
        $stmt->execute();
        closeConnection($conn);
    }

    function updateArticle($id, $title, $content, $source_id, $category_id, $created_at) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE article SET title = ?, content = ?, source_id = ?, category_id = ?, created_at = ? WHERE id = ?");
        $stmt->bind_param("ssiii", $title, $content, $source_id, $category_id, $created_at, $id);
        $stmt->execute();
        closeConnection($conn);
    }

    function deleteArticle($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM article WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        closeConnection($conn);
    }


?>