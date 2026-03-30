<?php
include_once '../controllers/ArticleImage.php';

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
	<title>CRUD ArticleImage</title>
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
				<li class="nav-item"><a class="nav-link" href="Article.php">Article</a></li>
				<li class="nav-item"><a class="nav-link active" href="ArticleImage.php">Article Image</a></li>
				<li class="nav-item"><a class="nav-link" href="CategoryArticle.php">CategoryArticle</a></li>
				<li class="nav-item"><a class="nav-link" href="Source.php">Source</a></li>
				<li class="nav-item"><a class="nav-link" href="TypeSource.php">TypeSource</a></li>
			</ul>
			<a href="../../frontoffice/index.php" class="btn btn-outline-light">Frontend</a>
		</div>
	</div>
</nav>

<div class="container py-4">
	<h2 class="mb-4">CRUD ArticleImage</h2>

	<?php if (!empty($message)): ?><div class="alert alert-success"><?php echo e($message); ?></div><?php endif; ?>
	<?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>

	<div class="card mb-4">
		<div class="card-header"><?php echo $isEdit ? 'Modifier' : 'Créer'; ?> une image</div>
		<div class="card-body">
			<form method="post">
				<input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
				<?php if ($isEdit): ?><input type="hidden" name="id" value="<?php echo e($editItem['id']); ?>"><?php endif; ?>
				<div class="row g-3">
					<div class="col-md-2"><input class="form-control" type="number" name="article_id" placeholder="Article ID" required value="<?php echo e($editItem['article_id'] ?? ''); ?>"></div>
					<div class="col-md-6"><input class="form-control" name="image_url" placeholder="Image URL" required value="<?php echo e($editItem['image_url'] ?? ''); ?>"></div>
					<div class="col-md-4"><input class="form-control" name="alt_text" placeholder="Alt text" value="<?php echo e($editItem['alt_text'] ?? ''); ?>"></div>
					<div class="col-md-12 form-check mt-2 ms-2">
						<input class="form-check-input" type="checkbox" id="is_main" name="is_main" <?php echo !empty($editItem['is_main']) ? 'checked' : ''; ?>>
						<label class="form-check-label" for="is_main">Image principale</label>
					</div>
				</div>
				<div class="mt-3 d-flex gap-2">
					<button class="btn btn-primary" type="submit"><?php echo $isEdit ? 'Mettre à jour' : 'Créer'; ?></button>
					<?php if ($isEdit): ?><a class="btn btn-secondary" href="ArticleImage.php">Annuler</a><?php endif; ?>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header">Liste des images</div>
		<div class="table-responsive">
			<table class="table table-striped table-hover mb-0">
				<thead><tr><th>ID</th><th>Article ID</th><th>URL</th><th>Alt</th><th>Main</th><th>Actions</th></tr></thead>
				<tbody>
				<?php foreach ($items as $item): ?>
					<tr>
						<td><?php echo e($item['id']); ?></td>
						<td><?php echo e($item['article_id']); ?></td>
						<td><?php echo e($item['image_url']); ?></td>
						<td><?php echo e($item['alt_text']); ?></td>
						<td><?php echo !empty($item['is_main']) ? 'Oui' : 'Non'; ?></td>
						<td class="d-flex gap-2">
							<a class="btn btn-sm btn-warning" href="?edit=<?php echo e($item['id']); ?>">Modifier</a>
							<form method="post" onsubmit="return confirm('Supprimer cette image ?');">
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
</body>
</html>
