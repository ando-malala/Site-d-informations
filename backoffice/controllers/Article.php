<?php

include_once __DIR__ . '/../connect/Connect.php';
include_once __DIR__ . '/../models/Article.php';

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
$editItem = null;

function parseNullableInt($value) {
	if ($value === null || $value === '') {
		return null;
	}

	if (!is_numeric($value)) {
		return null;
	}

	$intValue = (int) $value;
	return $intValue > 0 ? $intValue : null;
}

function redirectWithStatus($key, $value) {
	$redirectUrl = strtok($_SERVER['REQUEST_URI'], '?');
	header('Location: ' . $redirectUrl . '?' . $key . '=' . urlencode($value));
	exit;
}

function wrapTitleWithH1($title) {
	$cleanTitle = trim(strip_tags((string) $title));
	if ($cleanTitle === '') {
		return '';
	}
	return '<h1>' . $cleanTitle . '</h1>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'] ?? '';

	try {
		$title = trim($_POST['title'] ?? '');
		$slug = trim($_POST['slug'] ?? '');
		$contentHtml = $_POST['content'] ?? '';
		$summaryHtml = $_POST['summary'] ?? null;
		$status = trim($_POST['status'] ?? 'brouillon');

		if ($title === '' || $slug === '' || trim(strip_tags($contentHtml)) === '') {
			throw new Exception('Title, slug et content sont obligatoires.');
		}

		if ($action === 'create') {
			createArticle(
				wrapTitleWithH1($title),
				$slug,
				$contentHtml,
				$summaryHtml,
				$status,
				parseNullableInt($_POST['source_id'] ?? ''),
				parseNullableInt($_POST['category_id'] ?? ''),
				parseNullableInt($_POST['user_id'] ?? '')
			);
			redirectWithStatus('msg', 'Article créé avec succès');
		}

		if ($action === 'update') {
			updateArticle(
				(int) ($_POST['id'] ?? 0),
				$title,
				$slug,
				$contentHtml,
				$summaryHtml,
				$status,
				parseNullableInt($_POST['source_id'] ?? ''),
				parseNullableInt($_POST['category_id'] ?? ''),
				parseNullableInt($_POST['user_id'] ?? '')
			);
			redirectWithStatus('msg', 'Article modifié avec succès');
		}

		if ($action === 'delete') {
			deleteArticle((int) ($_POST['id'] ?? 0));
			redirectWithStatus('msg', 'Article supprimé avec succès');
		}
	} catch (Throwable $exception) {
		redirectWithStatus('err', $exception->getMessage());
	}
}

$items = [];
try {
	$conn = getConnection();
	$stmt = $conn->prepare("SELECT * FROM article");
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

