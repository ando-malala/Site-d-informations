<?php 

    include '../connect/Connect.php';

    function getImageByArticleId($articleId) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, article_id, image_url, alt_text, is_main FROM article_image WHERE article_id = ?");
        $stmt->bind_param("i", $articleId);
        $stmt->execute();
        $images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        closeConnection($conn);
        return $images;
    }

    function addImageToArticle($articleId, $imageUrl, $altText = null, $isMain = false) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO article_image (article_id, image_url, alt_text, is_main) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $articleId, $imageUrl, $altText, $isMain);
        $isAdded = $stmt->execute();
        closeConnection($conn);
        return $isAdded;
    }

    function updateImageById($id, $articleId, $imageUrl, $altText = null, $isMain = false) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE article_image SET article_id = ?, image_url = ?, alt_text = ?, is_main = ? WHERE id = ?");
        $stmt->bind_param("issii", $articleId, $imageUrl, $altText, $isMain, $id);
        $isUpdated = $stmt->execute();
        closeConnection($conn);
        return $isUpdated;
    }

    function deleteImageById($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM article_image WHERE id = ?");
        $stmt->bind_param("i", $id);
        $isDeleted = $stmt->execute();
        closeConnection($conn);
        return $isDeleted;
    }

    function deleteImageFromArticle($articleId, $imageUrl) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM article_image WHERE article_id = ? AND image_url = ?");
        $stmt->bind_param("is", $articleId, $imageUrl);
        $isDeleted = $stmt->execute();
        closeConnection($conn);
        return $isDeleted;
    }

    function getImgaeByArticleId($articleId) {
        return getImageByArticleId($articleId);
    }

    function deleteImageByArticleId($articleId, $imageUrl) {
        return deleteImageFromArticle($articleId, $imageUrl);
    }

?>