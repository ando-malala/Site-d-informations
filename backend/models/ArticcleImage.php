<?php 

    include '../connect/Connect.php';

    function getImgaeByArticleId($articleId) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT image_url FROM article_image WHERE article_id = ?");
        $stmt->bind_param("i", $articleId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function addImageToArticle($articleId, $imageUrl) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO article_image (article_id, image_url) VALUES (?, ?)");
        $stmt->bind_param("is", $articleId, $imageUrl);
        $stmt->execute();
        closeConnection($conn);
    }

    function deleteImageByArticleId($articleId, $imageUrl) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM article_image WHERE article_id = ? AND image_url = ?");
        $stmt->bind_param("is", $articleId, $imageUrl);
        $stmt->execute();
        closeConnection($conn);
    }

?>