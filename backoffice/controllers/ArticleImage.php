<?php

include_once __DIR__ . '/../connect/Connect.php';
include_once __DIR__ . '/../models/ArticleImage.php';

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
			addImageToArticle(
				(int) ($_POST['article_id'] ?? 0),
				trim($_POST['image_url'] ?? ''),
				trim($_POST['alt_text'] ?? ''),
				isset($_POST['is_main']) ? 1 : 0
			);
			redirectWithStatus('msg', 'Image ajoutée avec succès');
		}

		if ($action === 'update') {
			updateImageById(
				(int) ($_POST['id'] ?? 0),
				(int) ($_POST['article_id'] ?? 0),
				trim($_POST['image_url'] ?? ''),
				trim($_POST['alt_text'] ?? ''),
				isset($_POST['is_main']) ? 1 : 0
			);
			redirectWithStatus('msg', 'Image modifiée avec succès');
		}

		if ($action === 'delete') {
			deleteImageById((int) ($_POST['id'] ?? 0));
			redirectWithStatus('msg', 'Image supprimée avec succès');
		}
	} catch (Throwable $exception) {
		redirectWithStatus('err', $exception->getMessage());
	}
}

$items = [];
try {
	$conn = getConnection();
	$stmt = $conn->prepare("SELECT * FROM article_image");
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

