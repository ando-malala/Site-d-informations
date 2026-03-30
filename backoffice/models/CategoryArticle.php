<?php 

    include '../connect/Connect.php';

    function createCategoryArticle($name, $slug = null) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO category_article (name, slug) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $slug);
        $isCreated = $stmt->execute();
        closeConnection($conn);
        return $isCreated;
    }

    function updateCategoryArticle($id, $name, $slug = null) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE category_article SET name = ?, slug = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $slug, $id);
        $isUpdated = $stmt->execute();
        closeConnection($conn);
        return $isUpdated;
    }

    function deleteCategoryArticle($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM category_article WHERE id = ?");
        $stmt->bind_param("i", $id);
        $isDeleted = $stmt->execute();
        closeConnection($conn);
        return $isDeleted;
    }

    function createTypeSource($name) {
        return createCategoryArticle($name);
    }

    function updateTypeSource($id, $name) {
        return updateCategoryArticle($id, $name);
    }

    function deleteTypeSource($id) {
        return deleteCategoryArticle($id);
    }


?>