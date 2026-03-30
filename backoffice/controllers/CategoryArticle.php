<?php

include_once __DIR__ . '/../connect/Connect.php';
include_once __DIR__ . '/../models/CategoryArticle.php';

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
$editItem = null;

function redirectWithStatus($key, $value) {
    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $redirectUrl . '?' . $key . '=' . urlencode($value));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            createCategoryArticle(trim($_POST['name'] ?? ''), trim($_POST['slug'] ?? ''));
            redirectWithStatus('msg', 'Catégorie créée avec succès');
        }

        if ($action === 'update') {
            updateCategoryArticle((int) ($_POST['id'] ?? 0), trim($_POST['name'] ?? ''), trim($_POST['slug'] ?? ''));
            redirectWithStatus('msg', 'Catégorie modifiée avec succès');
        }

        if ($action === 'delete') {
            deleteCategoryArticle((int) ($_POST['id'] ?? 0));
            redirectWithStatus('msg', 'Catégorie supprimée avec succès');
        }
    } catch (Throwable $exception) {
        redirectWithStatus('err', $exception->getMessage());
    }
}

$items = [];
try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM category_article");
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    closeConnection($conn);
} catch (Throwable $exception) {
    if (empty($error)) {
        $error = $exception->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    foreach ($items as $item) {
        if ((int) $item['id'] === $id) {
            $editItem = $item;
            break;
        }
    }
}
