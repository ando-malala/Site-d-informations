<?php
include_once '../controllers/Article.php';

function e($value) {
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$isEdit = $editItem !== null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CRUD Article</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
	<div class="container">
		<a class="navbar-brand" href="index.php">INFOFLASH</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav me-auto">
				<li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
				<li class="nav-item"><a class="nav-link active" href="Article.php">Article</a></li>
				<li class="nav-item"><a class="nav-link" href="ArticleImage.php">Article Image</a></li>
				<li class="nav-item"><a class="nav-link" href="CategoryArticle.php">CategoryArticle</a></li>
				<li class="nav-item"><a class="nav-link" href="Source.php">Source</a></li>
				<li class="nav-item"><a class="nav-link" href="TypeSource.php">TypeSource</a></li>
			</ul>
			<a href="../../frontoffice/index.php" class="btn btn-outline-light">Frontend</a>
		</div>
	</div>
</nav>

<div class="container py-4">
	<h2 class="mb-4">CRUD Article</h2>

	<?php if (!empty($message)): ?>
		<div class="alert alert-success"><?php echo e($message); ?></div>
	<?php endif; ?>
	<?php if (!empty($error)): ?>
		<div class="alert alert-danger"><?php echo e($error); ?></div>
	<?php endif; ?>

	<div class="card mb-4">
		<div class="card-header"><?php echo $isEdit ? 'Modifier' : 'Créer'; ?> un article</div>
		<div class="card-body">
			<form method="post">
				<input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
				<?php if ($isEdit): ?><input type="hidden" name="id" value="<?php echo e($editItem['id']); ?>"><?php endif; ?>

				<div class="row g-3">
					<div class="col-md-6"><input class="form-control" name="title" placeholder="Title" required value="<?php echo e($editItem['title'] ?? ''); ?>"></div>
					<div class="col-md-6"><input class="form-control" name="slug" placeholder="Slug" required value="<?php echo e($editItem['slug'] ?? ''); ?>"></div>
					<div class="col-md-12">
						<label for="summary-editor" class="form-label fw-semibold">Summary</label>
						<textarea class="form-control" id="summary-editor" name="summary" placeholder="Summary" rows="2"><?php echo e($editItem['summary'] ?? ''); ?></textarea>
					</div>
					<div class="col-md-12">
						<label for="content-editor" class="form-label fw-semibold">Content</label>
						<textarea class="form-control" id="content-editor" name="content" placeholder="Content" rows="5" required><?php echo e($editItem['content'] ?? ''); ?></textarea>
					</div>
					<div class="col-md-3">
						<select class="form-select" name="status" required>
							<?php $statusValue = $editItem['status'] ?? 'brouillon'; ?>
							<option value="brouillon" <?php echo $statusValue === 'brouillon' ? 'selected' : ''; ?>>brouillon</option>
							<option value="publie" <?php echo $statusValue === 'publie' ? 'selected' : ''; ?>>publie</option>
							<option value="archive" <?php echo $statusValue === 'archive' ? 'selected' : ''; ?>>archive</option>
						</select>
					</div>
					<div class="col-md-3"><input class="form-control" type="number" name="source_id" placeholder="Source ID" value="<?php echo e($editItem['source_id'] ?? ''); ?>"></div>
					<div class="col-md-3"><input class="form-control" type="number" name="category_id" placeholder="Category ID" value="<?php echo e($editItem['category_id'] ?? ''); ?>"></div>
					<div class="col-md-3"><input class="form-control" type="number" name="user_id" placeholder="User ID" value="<?php echo e($editItem['user_id'] ?? ''); ?>"></div>
				</div>

				<div class="mt-3 d-flex gap-2">
					<button class="btn btn-primary" type="submit"><?php echo $isEdit ? 'Mettre à jour' : 'Créer'; ?></button>
					<?php if ($isEdit): ?><a class="btn btn-secondary" href="Article.php">Annuler</a><?php endif; ?>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header">Liste des articles</div>
		<div class="table-responsive">
			<table class="table table-striped table-hover mb-0">
				<thead><tr><th>ID</th><th>Titre</th><th>Slug</th><th>Status</th><th>Source</th><th>Catégorie</th><th>Auteur</th><th>Actions</th></tr></thead>
				<tbody>
				<?php foreach ($items as $item): ?>
					<tr>
						<td><?php echo e($item['id']); ?></td>
						<td><?php echo e($item['title']); ?></td>
						<td><?php echo e($item['slug']); ?></td>
						<td><?php echo e($item['status']); ?></td>
						<td><?php echo e($item['source_id']); ?></td>
						<td><?php echo e($item['category_id']); ?></td>
						<td><?php echo e($item['user_id']); ?></td>
						<td class="d-flex gap-2">
							<a class="btn btn-sm btn-warning" href="?edit=<?php echo e($item['id']); ?>">Modifier</a>
							<form method="post" onsubmit="return confirm('Supprimer cet article ?');">
								<input type="hidden" name="action" value="delete">
								<input type="hidden" name="id" value="<?php echo e($item['id']); ?>">
								<button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
	tinymce.init({
		selector: '#summary-editor, #content-editor',
		height: 220,
		menubar: false,
		plugins: 'lists link code',
		toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
		branding: false
	});
</script>
</body>
</html>
