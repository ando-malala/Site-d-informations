<?php 

    include '../connect/Connect.php';

    function createArticle($title, $slug, $content, $summary = null, $status = 'brouillon', $source_id = null, $category_id = null, $user_id = null) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO article (title, slug, summary, content, status, source_id, category_id, user_id) VALUES (?, ?, ?, ?, ?, NULLIF(?, 0), NULLIF(?, 0), NULLIF(?, 0))");
        $sourceId = (int) ($source_id ?? 0);
        $categoryId = (int) ($category_id ?? 0);
        $userId = (int) ($user_id ?? 0);
        $stmt->bind_param("sssssiii", $title, $slug, $summary, $content, $status, $sourceId, $categoryId, $userId);
        $isCreated = $stmt->execute();
        closeConnection($conn);
        return $isCreated;
    }

    function updateArticle($id, $title, $slug, $content, $summary = null, $status = 'brouillon', $source_id = null, $category_id = null, $user_id = null) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE article SET title = ?, slug = ?, summary = ?, content = ?, status = ?, source_id = NULLIF(?, 0), category_id = NULLIF(?, 0), user_id = NULLIF(?, 0) WHERE id = ?");
        $sourceId = (int) ($source_id ?? 0);
        $categoryId = (int) ($category_id ?? 0);
        $userId = (int) ($user_id ?? 0);
        $stmt->bind_param("sssssiiii", $title, $slug, $summary, $content, $status, $sourceId, $categoryId, $userId, $id);
        $isUpdated = $stmt->execute();
        closeConnection($conn);
        return $isUpdated;
    }

    function deleteArticle($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM article WHERE id = ?");
        $stmt->bind_param("i", $id);
        $isDeleted = $stmt->execute();
        closeConnection($conn);
        return $isDeleted;
    }


?>